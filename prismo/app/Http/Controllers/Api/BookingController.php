<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Voucher;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Booking::query();

        if ($user->role === 'customer') {
            $query->where('customer_id', $user->id);
        } elseif ($user->role === 'mitra') {
            $query->where('mitra_id', $user->id);
        }

        // For admin, only show cek_transaksi and dibatalkan (pending refund) bookings
        if ($user->role === 'admin') {
            $query->where(function($q) {
                // Show cek_transaksi bookings
                $q->where('status', 'cek_transaksi')
                  // OR show dibatalkan bookings that haven't been refunded yet
                  ->orWhere(function($q2) {
                      $q2->where('status', 'dibatalkan')
                         ->whereNull('refund_completed_at');
                  });
            });
        }

        // Filter by status if provided
        if ($request->has('status')) {
            $statusFilter = $request->status;
            $query->where('status', $statusFilter);
            
            // For dibatalkan status, exclude bookings with completed refunds
            if ($statusFilter === 'dibatalkan') {
                $query->whereNull('refund_completed_at');
            }
        }

        $bookings = $query->with(['customer', 'mitra', 'voucher'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        // Verify customer role
        if (!$request->user() || $request->user()->role !== 'customer') {
            return response()->json([
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        $validated = $request->validate([
            'mitra_id' => 'required|integer|exists:users,id',
            'service_type' => 'required|string|max:100',
            'vehicle_type' => 'required|string|in:Sedan,SUV,MPV,Truk,Motor',
            'vehicle_plate' => 'required|string|max:20|regex:/^[A-Z0-9\s-]+$/i',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'base_price' => 'required|numeric|min:0|max:10000000',
            'discount_amount' => 'nullable|numeric|min:0',
            'final_price' => 'required|numeric|min:0|max:10000000',
            'voucher_code' => 'nullable|string|max:50|alpha_num',
            'payment_method' => 'required|string|in:Dana,Gopay,OVO,ShopeePay,QRIS',
        ]);

        // Check if customer has active booking (not selesai or dibatalkan)
        $activeBooking = Booking::where('customer_id', $request->user()->id)
            ->whereIn('status', ['cek_transaksi', 'menunggu', 'proses'])
            ->first();

        if ($activeBooking) {
            return response()->json([
                'message' => 'Anda masih memiliki booking yang sedang berlangsung. Selesaikan atau batalkan booking sebelumnya terlebih dahulu.',
                'active_booking' => [
                    'booking_code' => $activeBooking->booking_code,
                    'status' => $activeBooking->status,
                    'booking_date' => $activeBooking->booking_date->format('Y-m-d'),
                ]
            ], 422);
        }

        $validated['customer_id'] = $request->user()->id;
        $validated['booking_code'] = 'BK-' . strtoupper(Str::random(8));
        $validated['discount_amount'] = $validated['discount_amount'] ?? 0;
        $validated['status'] = 'cek_transaksi';
        $validated['payment_status'] = 'pending';

        // Check voucher if provided
        if ($request->voucher_code) {
            $voucher = Voucher::where('code', $request->voucher_code)
                ->where('is_active', true)
                ->where('end_date', '>=', now())
                ->first();

            if ($voucher) {
                $validated['voucher_id'] = $voucher->id;
            }
        }

        $booking = Booking::create($validated);
        $booking->load(['customer', 'mitra', 'voucher']);

        // Award 1 point to customer for each booking
        $customer = $request->user();
        $customer->increment('points', 1);
        
        // Create notification for mitra
        Notification::create([
            'user_id' => $validated['mitra_id'],
            'type' => 'booking_baru',
            'title' => 'Booking Baru',
            'message' => 'Booking baru dari ' . $customer->name . ' untuk ' . $validated['service_type'],
            'data' => json_encode([
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'customer_name' => $customer->name,
                'service_type' => $validated['service_type'],
                'booking_date' => $validated['booking_date'],
                'booking_time' => $validated['booking_time'],
            ]),
            'is_read' => false,
        ]);

        return response()->json($booking, 201);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $booking = Booking::with(['customer', 'mitra', 'voucher', 'review'])->findOrFail($id);

        // Check authorization
        if ($user->role !== 'admin' && $booking->customer_id !== $user->id && $booking->mitra_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($booking);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $booking = Booking::findOrFail($id);

        // Authorization check
        if ($user->role === 'customer' && $booking->customer_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($user->role === 'mitra' && $booking->mitra_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:cek_transaksi,menunggu,proses,selesai,dibatalkan',
            'payment_status' => 'sometimes|in:pending,confirmed,failed',
            'cancellation_reason' => 'nullable|string',
        ]);

        // Update timestamps based on status
        if (isset($validated['status'])) {
            if ($validated['status'] === 'proses') {
                $validated['started_at'] = now();
            } elseif ($validated['status'] === 'selesai') {
                $validated['completed_at'] = now();
            } elseif ($validated['status'] === 'dibatalkan') {
                $validated['cancelled_at'] = now();
            }
        }

        $booking->update($validated);
        $booking->load(['customer', 'mitra', 'voucher']);

        return response()->json($booking);
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        
        // Only allow deletion if status is 'menunggu' or 'dibatalkan'
        if (!in_array($booking->status, ['menunggu', 'dibatalkan'])) {
            return response()->json(['message' => 'Cannot delete active booking'], 403);
        }

        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully']);
    }

    public function uploadPaymentProof(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->customer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'payment_proof' => 'required|image|max:2048'
        ]);

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');
        $booking->payment_proof = $path;
        $booking->save();

        return response()->json([
            'message' => 'Payment proof uploaded successfully',
            'payment_proof' => $path
        ]);
    }

    public function confirmBooking(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking = Booking::findOrFail($id);
        
        // Only allow confirming bookings in cek_transaksi status
        if ($booking->status !== 'cek_transaksi') {
            return response()->json(['message' => 'Booking already processed'], 400);
        }

        $booking->status = 'menunggu';
        $booking->payment_status = 'confirmed';
        $booking->confirmed_at = now();
        $booking->save();

        // Create notification for customer
        \App\Models\Notification::create([
            'user_id' => $booking->customer_id,
            'type' => 'booking_confirmed',
            'title' => 'Booking Dikonfirmasi',
            'message' => 'Booking Anda telah dikonfirmasi. Silakan tunggu antrian dari mitra.',
            'booking_id' => $booking->id,
            'is_read' => false
        ]);

        return response()->json([
            'message' => 'Booking confirmed successfully',
            'booking' => $booking->load(['customer', 'mitra'])
        ]);
    }

    public function cancelBooking(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'cancellation_reason' => 'required|string'
        ]);

        $booking = Booking::findOrFail($id);
        
        // Allow canceling bookings in cek_transaksi or menunggu status
        if (!in_array($booking->status, ['cek_transaksi', 'menunggu'])) {
            return response()->json(['message' => 'Booking already processed'], 400);
        }

        $booking->status = 'dibatalkan';
        $booking->payment_status = 'failed';
        $booking->cancelled_at = now();
        $booking->cancellation_reason = $request->cancellation_reason;
        $booking->save();

        // Create notification for customer
        \App\Models\Notification::create([
            'user_id' => $booking->customer_id,
            'type' => 'booking_cancelled',
            'title' => 'Booking Dibatalkan',
            'message' => 'Booking Anda telah dibatalkan oleh admin. Alasan: ' . $request->cancellation_reason,
            'booking_id' => $booking->id,
            'is_read' => false
        ]);

        // TODO: Implement refund logic here

        return response()->json([
            'message' => 'Booking cancelled successfully',
            'booking' => $booking->load(['customer', 'mitra'])
        ]);
    }

    public function completeRefund(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking = Booking::findOrFail($id);
        
        // Only allow completing refund for cancelled bookings
        if ($booking->status !== 'dibatalkan') {
            return response()->json(['message' => 'Only cancelled bookings can be refunded'], 400);
        }

        // Check if refund already completed
        if ($booking->refund_completed_at) {
            return response()->json(['message' => 'Refund already completed'], 400);
        }

        // Mark refund as completed
        $booking->refund_completed_at = now();
        $booking->payment_status = 'failed';
        $booking->save();

        return response()->json([
            'message' => 'Refund completed successfully',
            'booking' => $booking->load(['customer', 'mitra'])
        ]);
    }

    public function getBookedSlots(Request $request, $mitraId)
    {
        // Get date range - default to 30 days from now
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->addDays(30)->format('Y-m-d'));

        // Get all bookings for this mitra within date range
        // Only count bookings that are NOT cancelled
        $bookings = Booking::where('mitra_id', $mitraId)
            ->whereIn('status', ['cek_transaksi', 'menunggu', 'proses', 'selesai'])
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->get(['booking_date', 'booking_time', 'service_type']);

        // Group bookings by date and time
        $bookedSlots = [];
        foreach ($bookings as $booking) {
            $date = $booking->booking_date->format('Y-m-d');
            $time = $booking->booking_time;
            
            if (!isset($bookedSlots[$date])) {
                $bookedSlots[$date] = [];
            }
            
            $bookedSlots[$date][] = $time;
        }

        return response()->json([
            'booked_slots' => $bookedSlots
        ]);
    }
}
