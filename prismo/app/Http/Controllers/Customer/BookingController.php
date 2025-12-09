<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Review;

class BookingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Verify customer role
        if ($user->role !== 'customer') {
            abort(403, 'Unauthorized access');
        }
        
        // Current booking (status: cek_transaksi, menunggu, proses - exclude dibatalkan and selesai)
        $currentBooking = Booking::where('customer_id', $user->id)
            ->whereIn('status', ['cek_transaksi', 'menunggu', 'proses'])
            ->with(['mitra.mitraProfile'])
            ->orderBy('booking_date', 'desc')
            ->first();
        
        $currentBookingData = null;
        if ($currentBooking) {
            // Map status to display labels
            $statusMap = [
                'cek_transaksi' => 'Cek Transaksi',
                'menunggu' => 'Menunggu',
                'proses' => 'Dalam Proses',
                'selesai' => 'Selesai',
                'dibatalkan' => 'Dibatalkan',
            ];
            
            $currentBookingData = [
                'id' => $currentBooking->booking_code,
                'mitraId' => $currentBooking->mitra_id,
                'partner' => $currentBooking->mitra->mitraProfile->business_name ?? $currentBooking->mitra->name,
                'totalPrice' => $currentBooking->final_price,
                'location' => $currentBooking->mitra->mitraProfile->address ?? '-',
                'date' => $currentBooking->booking_date->format('Y-m-d'),
                'time' => substr($currentBooking->booking_time, 0, 5),
                'treatment' => $currentBooking->service_type,
                'tipe' => $currentBooking->vehicle_type,
                'nopol' => $currentBooking->vehicle_plate,
                'status' => $statusMap[$currentBooking->status] ?? ucfirst($currentBooking->status),
            ];
        }
        
        // Booking history (status: selesai, dibatalkan)
        $bookingHistory = Booking::where('customer_id', $user->id)
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->with(['mitra.mitraProfile', 'review'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($booking) {
                $review = Review::where('booking_id', $booking->id)->first();
                
                // Map status to display labels
                $statusMap = [
                    'cek_transaksi' => 'Cek Transaksi',
                    'menunggu' => 'Menunggu',
                    'proses' => 'Dalam Proses',
                    'selesai' => 'Selesai',
                    'dibatalkan' => 'Dibatalkan',
                ];
                
                // Get photos from review
                $photos = [];
                if ($review && $review->review_photos) {
                    $rawPhotos = is_array($review->review_photos) ? $review->review_photos : json_decode((string)$review->review_photos, true);
                    $photos = $rawPhotos ? $rawPhotos : [];
                }
                
                return [
                    'id' => $booking->booking_code,
                    'partner' => $booking->mitra->mitraProfile->business_name ?? $booking->mitra->name,
                    'service' => $booking->service_type,
                    'date' => $booking->booking_date->format('Y-m-d'),
                    'price' => $booking->final_price,
                    'rating' => $review ? $review->rating : 0,
                    'comment' => $review ? $review->comment : '',
                    'status' => $statusMap[$booking->status] ?? ucfirst($booking->status),
                    'hasReview' => $review ? true : false,
                    'photos' => $photos,
                    'mitraResponse' => $review && $review->mitra_reply ? $review->mitra_reply : null,
                ];
            });
        
        return view('customer.booking.Rbooking', compact('currentBookingData', 'bookingHistory'));
    }
    
    public function reschedule(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'booking_code' => 'required|string',
            'new_date' => 'required|date',
            'new_time' => 'required|string',
        ]);
        
        // Find booking
        $booking = Booking::where('booking_code', $validated['booking_code'])
            ->where('customer_id', $user->id)
            ->first();
        
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan'
            ], 404);
        }
        
        // Check if booking can be rescheduled (only cek_transaksi and menunggu status)
        if (!in_array($booking->status, ['cek_transaksi', 'menunggu'])) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak dapat direschedule karena sudah dalam proses atau selesai'
            ], 400);
        }
        
        // Update booking date and time
        $booking->booking_date = $validated['new_date'];
        $booking->booking_time = $validated['new_time'];
        $booking->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil direschedule',
            'data' => [
                'booking_code' => $booking->booking_code,
                'new_date' => $booking->booking_date->format('Y-m-d'),
                'new_time' => substr($booking->booking_time, 0, 5),
            ]
        ]);
    }
    
    public function submitReview(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'booking_code' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // Find booking
        $booking = Booking::where('booking_code', $validated['booking_code'])
            ->where('customer_id', $user->id)
            ->where('status', 'selesai')
            ->first();
        
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan atau belum selesai'
            ], 404);
        }
        
        // Check if review already exists
        $existingReview = Review::where('booking_id', $booking->id)->first();
        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Review sudah ada untuk booking ini. Gunakan endpoint update untuk mengubah review.'
            ], 400);
        }
        
        // Handle photo uploads
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('reviews', 'public');
                $photoPaths[] = $path;
            }
        }
        
        // Create review
        $review = Review::create([
            'booking_id' => $booking->id,
            'customer_id' => $user->id,
            'mitra_id' => $booking->mitra_id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'review_photos' => !empty($photoPaths) ? json_encode($photoPaths) : null,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Review berhasil disimpan',
            'data' => [
                'review_id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
            ]
        ]);
    }
    
    public function updateReview(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'booking_code' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // Find booking
        $booking = Booking::where('booking_code', $validated['booking_code'])
            ->where('customer_id', $user->id)
            ->first();
        
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan'
            ], 404);
        }
        
        // Find review
        $review = Review::where('booking_id', $booking->id)
            ->where('customer_id', $user->id)
            ->first();
        
        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review tidak ditemukan'
            ], 404);
        }
        
        // Handle photo uploads
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('reviews', 'public');
                $photoPaths[] = $path;
            }
        }
        
        // Update review
        $review->rating = $validated['rating'];
        $review->comment = $validated['comment'];
        if (!empty($photoPaths)) {
            $review->review_photos = json_encode($photoPaths);
        }
        $review->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Review berhasil diupdate',
            'data' => [
                'review_id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
            ]
        ]);
    }
}
