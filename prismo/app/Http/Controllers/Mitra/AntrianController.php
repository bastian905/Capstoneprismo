<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;

class AntrianController extends Controller
{
    public function index()
    {
        $mitra = auth()->user();
        
        // Get only confirmed bookings (not cek_transaksi) for this mitra
        $antrian = Booking::where('mitra_id', $mitra->id)
            ->whereIn('status', ['menunggu', 'proses', 'selesai', 'dibatalkan'])
            ->with(['customer'])
            ->orderByRaw("FIELD(status, 'menunggu', 'proses', 'selesai', 'dibatalkan')")
            ->orderBy('booking_date', 'asc')
            ->orderBy('booking_time', 'asc')
            ->get()
            ->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'name' => $booking->customer->name,
                    'customerName' => $booking->customer->name,
                    'customerPhone' => $booking->customer->phone ?? '-',
                    'car' => $booking->vehicle_type . ' - ' . $booking->vehicle_plate,
                    'service' => $booking->service_type,
                    'vehicleType' => $booking->vehicle_type,
                    'vehiclePlate' => $booking->vehicle_plate,
                    'date' => $booking->booking_date->format('Y-m-d'),
                    'time' => substr($booking->booking_time, 0, 5),
                    'price' => 'Rp ' . number_format($booking->final_price, 0, ',', '.'),
                    'priceRaw' => $booking->final_price,
                    'status' => $booking->status,
                    'currentStep' => $booking->status === 'menunggu' ? 0 : ($booking->status === 'proses' ? 1 : 2),
                    'paymentMethod' => $booking->payment_method,
                    'paymentProof' => $booking->payment_proof,
                    'avatar' => $booking->customer->avatar ?? '/images/profile.png',
                    'lastUpdated' => $booking->updated_at->timestamp * 1000,
                ];
            });
        
        return view('mitra.antrian.antrian', compact('antrian'));
    }
    
    public function updateStatus(Request $request)
    {
        $mitra = auth()->user();
        
        $validated = $request->validate([
            'booking_id' => 'required|integer',
            'status' => 'required|in:proses,selesai',
        ]);
        
        // Find booking
        $booking = Booking::where('id', $validated['booking_id'])
            ->where('mitra_id', $mitra->id)
            ->first();
        
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan'
            ], 404);
        }
        
        // Validate status transition
        if ($validated['status'] === 'proses' && $booking->status !== 'menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya booking yang menunggu bisa diproses'
            ], 400);
        }
        
        if ($validated['status'] === 'selesai' && $booking->status !== 'proses') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya booking yang sedang diproses bisa diselesaikan'
            ], 400);
        }
        
        // Update booking status
        $booking->status = $validated['status'];
        $booking->save();
        
        // Give 1 point to customer when booking is completed
        if ($validated['status'] === 'selesai') {
            $customer = User::find($booking->customer_id);
            if ($customer) {
                $customer->increment('points', 1);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Status booking berhasil diupdate',
            'data' => [
                'booking_id' => $booking->id,
                'status' => $booking->status,
            ]
        ]);
    }
}
