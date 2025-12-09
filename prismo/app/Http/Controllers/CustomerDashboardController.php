<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Total booking user ini
        $totalBooking = Booking::where('customer_id', $user->id)->count();
        
        // Booking aktif (paid, processing)
        $activeBookings = Booking::where('customer_id', $user->id)
            ->whereIn('status', ['paid', 'processing'])
            ->count();
        
        // Booking completed
        $completedBookings = Booking::where('customer_id', $user->id)
            ->where('status', 'completed')
            ->count();
        
        // Points/vouchers (jika ada)
        $totalPoints = $user->points ?? 0;
        
        // Get all approved mitra for display
        $mitras = User::where('role', 'mitra')
            ->where('approval_status', 'approved')
            ->with('mitraProfile')
            ->get()
            ->map(function ($mitra) use ($totalBooking) {
                $profile = $mitra->mitraProfile;
                
                if (!$profile) {
                    return null;
                }
                
                // Count completed bookings for this mitra
                $completedBookingsCount = \App\Models\Booking::where('mitra_id', $mitra->id)
                    ->where('status', 'selesai')
                    ->count();
                
                // Get first facility photo or use default
                $facilityPhotos = $profile->facility_photos ? json_decode($profile->facility_photos, true) : [];
                $image = !empty($facilityPhotos) ? '/storage/' . $facilityPhotos[0] : '/images/logo.png';
                
                // Get custom services from database (already an array from cast)
                $customServices = is_array($profile->custom_services) ? $profile->custom_services : [];
                
                // Build prices object from custom services
                $prices = [];
                foreach ($customServices as $service) {
                    $prices[$service['name']] = (float) ($service['price'] ?? 0);
                }
                
                return [
                    'id' => $mitra->id,
                    'name' => $profile->business_name ?? 'Mitra',
                    'location' => $profile->address ?? '',
                    'kota' => $profile->city ?? '',
                    'provinsi' => $profile->province ?? '',
                    'rating' => (float) ($profile->rating ?? 0),
                    'reviews' => (int) ($profile->review_count ?? 0),
                    'completed_bookings' => $completedBookingsCount,
                    'status' => $profile->is_open ? 'open' : 'closed',
                    'image' => $image,
                    'services' => $customServices, // Full service data
                    'prices' => $prices // Service name => price mapping
                ];
            })
            ->filter(); // Remove null entries
        
        return view('customer.dashboard.dashU', compact(
            'totalBooking',
            'activeBookings',
            'completedBookings',
            'totalPoints',
            'mitras'
        ));
    }
}
