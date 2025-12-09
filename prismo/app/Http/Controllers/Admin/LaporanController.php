<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        // Statistik keseluruhan
        $totalRevenue = Booking::where('status', 'selesai')->sum('final_price');
        $totalBookings = Booking::count();
        $totalMitra = User::where('role', 'mitra')->where('approval_status', 'approved')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        
        // Top performing mitra
        $topMitras = Booking::select('mitra_id', DB::raw('COUNT(*) as bookings_count'), DB::raw('SUM(final_price) as total_revenue'))
            ->where('status', 'selesai')
            ->groupBy('mitra_id')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->with(['mitra.mitraProfile'])
            ->get()
            ->map(function($item) {
                return (object)[
                    'business_name' => $item->mitra->mitraProfile->business_name ?? $item->mitra->name ?? '-',
                    'bookings_count' => $item->bookings_count,
                    'total_revenue' => $item->total_revenue,
                    'rating' => $item->mitra->mitraProfile->rating ?? 0
                ];
            });
        
        // Monthly revenue trend
        $monthlyRevenue = Booking::select(
                DB::raw('DATE_FORMAT(booking_date, "%Y-%m") as month'),
                DB::raw('SUM(final_price) as revenue'),
                DB::raw('COUNT(*) as bookings')
            )
            ->where('status', 'selesai')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
        
        // Recent withdrawals
        $recentWithdrawals = Withdrawal::with('mitra')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($w) {
                return [
                    'id' => $w->id,
                    'mitra_name' => $w->mitra->name ?? '-',
                    'amount' => $w->amount,
                    'status' => $w->status,
                    'created_at' => $w->created_at->format('Y-m-d H:i:s')
                ];
            });
        
        return view('admin.laporan.laporan', compact(
            'totalRevenue',
            'totalBookings',
            'totalMitra',
            'totalCustomers',
            'topMitras',
            'monthlyRevenue',
            'recentWithdrawals'
        ));
    }
}
