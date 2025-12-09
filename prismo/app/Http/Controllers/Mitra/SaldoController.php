<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\Booking;

class SaldoController extends Controller
{
    public function index()
    {
        $mitra = auth()->user();
        
        // Total earnings from completed bookings (saldo kotor)
        $totalEarnings = Booking::where('mitra_id', $mitra->id)
            ->where('status', 'selesai')
            ->sum('final_price');
        
        // Total withdrawn (completed withdrawals)
        $totalWithdrawn = Withdrawal::where('mitra_id', $mitra->id)
            ->where('status', 'completed')
            ->sum('amount');
        
        // Saldo tersedia = total earnings - total withdrawn
        $availableBalance = $totalEarnings - $totalWithdrawn;
        
        // Saldo bersih hari ini (dari booking selesai hari ini)
        // Use updated_at as fallback if completed_at is null
        $todayEarnings = Booking::where('mitra_id', $mitra->id)
            ->where('status', 'selesai')
            ->where(function($query) {
                $query->whereDate('completed_at', now()->toDateString())
                      ->orWhere(function($q) {
                          $q->whereNull('completed_at')
                            ->whereDate('updated_at', now()->toDateString());
                      });
            })
            ->sum('final_price');
        
        // Pending withdrawals (status pending atau approved)
        $pendingWithdrawals = Withdrawal::where('mitra_id', $mitra->id)
            ->whereIn('status', ['pending', 'approved'])
            ->sum('amount');
        
        // Cek apakah sudah ada penarikan hari ini
        $hasWithdrawnToday = Withdrawal::where('mitra_id', $mitra->id)
            ->whereDate('created_at', now()->toDateString())
            ->exists();
        
        // Cek apakah ada penarikan yang sedang diproses
        $hasProcessingWithdrawal = Withdrawal::where('mitra_id', $mitra->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();
        
        return view('mitra.saldo.saldo', compact(
            'availableBalance',
            'totalEarnings', 
            'totalWithdrawn', 
            'pendingWithdrawals',
            'todayEarnings',
            'hasWithdrawnToday',
            'hasProcessingWithdrawal'
        ));
    }
    
    public function history()
    {
        $mitra = auth()->user();
        
        // Get withdrawal history
        $withdrawalHistory = Withdrawal::where('mitra_id', $mitra->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($withdrawal) {
                return [
                    'id' => $withdrawal->id,
                    'amount' => $withdrawal->amount,
                    'status' => $withdrawal->status,
                    'requestDate' => $withdrawal->created_at->format('d M Y H:i'),
                    'processedDate' => $withdrawal->processed_at ? $withdrawal->processed_at->format('d M Y H:i') : null,
                    'note' => $withdrawal->note,
                ];
            });
        
        // Get earnings history from bookings
        $earningsHistory = Booking::where('mitra_id', $mitra->id)
            ->where('status', 'selesai')
            ->whereNotNull('completed_at')
            ->with('customer')
            ->orderBy('completed_at', 'desc')
            ->get()
            ->map(function($booking) {
                return [
                    'id' => $booking->booking_code,
                    'customerName' => $booking->customer->name,
                    'service' => $booking->service_type,
                    'amount' => $booking->final_price,
                    'date' => $booking->completed_at->format('d M Y H:i'),
                ];
            });
        
        return view('mitra.saldo.history', compact('withdrawalHistory', 'earningsHistory'));
    }
}
