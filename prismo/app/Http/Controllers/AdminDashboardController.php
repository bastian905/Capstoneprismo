<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Total Mitra (semua status)
        $totalMitra = User::where('role', 'mitra')->count();
        
        // Total Customer
        $totalCustomer = User::where('role', 'customer')->count();
        
        // Total Transaksi (Bookings)
        $totalTransaksi = Booking::count();
        
        // Total Pendapatan (sum dari semua booking yang selesai)
        $totalPendapatan = Booking::where('status', 'selesai')
            ->sum('final_price');
        
        // Mitra Pending Approval
        $mitraPending = User::where('role', 'mitra')
            ->where('approval_status', 'pending')
            ->count();
        
        // Mitra Approved
        $mitraApproved = User::where('role', 'mitra')
            ->where('approval_status', 'approved')
            ->count();
        
        // Mitra Rejected
        $mitraRejected = User::where('role', 'mitra')
            ->where('approval_status', 'rejected')
            ->count();
        
        // Pending Withdrawal Requests
        $pendingWithdrawals = Withdrawal::where('status', 'pending')->count();
        
        // Top performing mitra (top 5 by revenue)
        $topMitras = Booking::select('mitra_id', DB::raw('COUNT(*) as bookings_count'), DB::raw('SUM(final_price) as total_revenue'))
            ->where('status', 'selesai')
            ->groupBy('mitra_id')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
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
        
        // Recent Activities (last 10 activities)
        $recentActivities = $this->getRecentActivities();
        
        return view('admin.dashboard.dashboard', compact(
            'totalMitra',
            'totalCustomer',
            'totalTransaksi',
            'totalPendapatan',
            'mitraPending',
            'mitraApproved',
            'mitraRejected',
            'pendingWithdrawals',
            'topMitras',
            'recentActivities'
        ));
    }
    
    private function getRecentActivities()
    {
        // Gabungkan berbagai aktivitas (registrasi user, booking, dll)
        $activities = [];
        
        // User registrations
        $recentUsers = User::whereIn('role', ['customer', 'mitra'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($user) {
                return [
                    'type' => $user->role === 'mitra' ? 'mitra_registration' : 'customer_registration',
                    'description' => $user->role === 'mitra' 
                        ? "Mitra baru mendaftar: {$user->name}"
                        : "Customer baru mendaftar: {$user->name}",
                    'time' => $user->created_at->diffForHumans(),
                    'created_at' => $user->created_at
                ];
            });
        
        // Recent bookings
        $recentBookings = Booking::with('customer')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($booking) {
                return [
                    'type' => 'booking',
                    'description' => "Booking baru dari {$booking->customer->name}",
                    'time' => $booking->created_at->diffForHumans(),
                    'created_at' => $booking->created_at
                ];
            });
        
        // Merge and sort
        $activities = $recentUsers->concat($recentBookings)
            ->sortByDesc('created_at')
            ->take(10)
            ->values();
        
        return $activities;
    }
}
