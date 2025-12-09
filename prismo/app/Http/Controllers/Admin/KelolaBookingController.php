<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class KelolaBookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['customer', 'mitra'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'booking_code' => $booking->booking_code ?? 'BK-' . str_pad($booking->id, 5, '0', STR_PAD_LEFT),
                    'customer_name' => $booking->customer->name ?? '-',
                    'customer_email' => $booking->customer->email ?? '-',
                    'mitra_name' => $booking->mitra->mitraProfile->business_name ?? $booking->mitra->name ?? '-',
                    'service_name' => $booking->service_type ?? '-',
                    'booking_date' => $booking->booking_date->format('Y-m-d'),
                    'booking_time' => substr($booking->booking_time, 0, 5),
                    'status' => $booking->status,
                    'payment_status' => $booking->payment_status,
                    'total_price' => $booking->final_price,
                    'payment_method' => $booking->payment_method ?? '-',
                    'payment_proof' => $booking->payment_proof ? asset('storage/' . $booking->payment_proof) : null,
                    'vehicle_type' => $booking->vehicle_type,
                    'vehicle_plate' => $booking->vehicle_plate,
                    'created_at' => $booking->created_at->format('Y-m-d H:i:s')
                ];
            });
        
        return view('admin.kelolabooking.kelolabooking', compact('bookings'));
    }
}
