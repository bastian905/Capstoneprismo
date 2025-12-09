<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MitraDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role !== 'mitra') {
            return redirect('/dashboard');
        }
        
        if (!$user->profile_completed) {
            return redirect('/mitra/form-mitra')->with('info', 'Silakan lengkapi profil Anda terlebih dahulu.');
        }
        
        if ($user->approval_status === 'rejected') {
            return redirect('/mitra/form-mitra')->with('info', 'Pendaftaran Anda ditolak. Silakan perbaiki dan kirim ulang formulir.');
        }
        
        if ($user->approval_status === 'pending') {
            return redirect('/mitra/form-mitra-pending');
        }
        
        if ($user->approval_status !== 'approved') {
            return redirect('/mitra/form-mitra-pending');
        }
        
        // Total bookings untuk mitra ini
        $totalBookings = Booking::where('mitra_id', $user->id)->count();
        
        // Bookings hari ini
        $todayBookings = Booking::where('mitra_id', $user->id)
            ->whereDate('booking_date', today())
            ->whereIn('status', ['paid', 'processing'])
            ->count();
        
        // Total pendapatan
        $totalEarnings = Booking::where('mitra_id', $user->id)
            ->where('status', 'completed')
            ->sum('final_price');
        
        // Saldo tersedia (dari withdrawal atau balance table)
        $balance = $user->balance ?? $totalEarnings;
        
        // Pending reviews (belum dibalas) - temporary disabled until mitra_response column added
        $pendingReviews = 0; // Review::where('mitra_id', $user->id)->whereNull('mitra_response')->count();
        
        // Average rating
        $averageRating = Review::where('mitra_id', $user->id)
            ->avg('rating') ?? 0;
        
        // Get mitra profile untuk operational hours dan service packages
        $mitraProfile = $user->mitraProfile;
        
        // Operational hours dari database (JSON)
        $operationalHours = $mitraProfile->operational_hours ?? [];
        if (is_string($operationalHours)) {
            $operationalHours = json_decode($operationalHours, true) ?? [];
        }
        
        // Service packages (basic, premium, complete prices)
        $servicePackages = [
            [
                'id' => 'basic',
                'name' => 'Basic Steam',
                'price' => $mitraProfile->basic_price ?? 0,
                'features' => ['Cuci Eksterior', 'Vacuum Interior', 'Lap Dashboard']
            ],
            [
                'id' => 'premium',
                'name' => 'Premium Steam',
                'price' => $mitraProfile->premium_price ?? 0,
                'features' => ['Cuci Eksterior', 'Vacuum Interior', 'Lap Dashboard', 'Poles Ban', 'Wax']
            ],
            [
                'id' => 'complete',
                'name' => 'Complete Steam',
                'price' => $mitraProfile->complete_price ?? 0,
                'features' => ['Cuci Eksterior', 'Vacuum Interior', 'Lap Dashboard', 'Poles Ban', 'Wax', 'Coating', 'Engine Wash']
            ]
        ];
        
        // Custom services dari database
        $customServices = $mitraProfile->custom_services ?? [];
        if (is_string($customServices)) {
            $customServices = json_decode($customServices, true) ?? [];
        }
        
        return view('mitra.dashboard.dashboard', compact(
            'totalBookings',
            'todayBookings',
            'totalEarnings',
            'balance',
            'pendingReviews',
            'averageRating',
            'operationalHours',
            'servicePackages',
            'customServices',
            'mitraProfile'
        ));
    }
}
