<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MitraProfile;
use App\Models\Review;

class DetailMitraController extends Controller
{
    public function show($id)
    {
        $mitra = User::where('id', $id)
            ->where('role', 'mitra')
            ->with('mitraProfile')
            ->firstOrFail();
        
        $profile = $mitra->mitraProfile;
        
        // Count completed bookings
        $completedBookingsCount = \App\Models\Booking::where('mitra_id', $mitra->id)
            ->where('status', 'selesai')
            ->count();
        
        $business = [
            'mitraId' => $mitra->id,
            'name' => $profile->business_name,
            'rating' => $profile->rating,
            'reviewCount' => $profile->review_count,
            'completedBookings' => $completedBookingsCount,
            'description' => $profile->description,
            'location' => $profile->city . ', ' . $profile->province,
            'address' => $profile->address,
            'phone' => $profile->phone,
            'mapLocation' => $profile->map_location,
            'operationalHours' => $profile->operational_hours,
        ];
        
        $galleryImages = $profile->facility_photos ?? [];
        if (is_string($galleryImages)) {
            $galleryImages = json_decode($galleryImages, true) ?? [];
        }
        
        // Convert paths to full URLs with /storage/ prefix
        $galleryImages = array_map(function($path) {
            // If path already starts with http, return as is
            if (strpos($path, 'http') === 0) {
                return $path;
            }
            // If path starts with storage/, use asset()
            if (strpos($path, 'storage/') === 0) {
                return asset($path);
            }
            // Otherwise prepend storage/
            return asset('storage/' . $path);
        }, $galleryImages);
        
        // Get custom services from database
        $customServices = $profile->custom_services ?? [];
        if (is_string($customServices)) {
            $customServices = json_decode($customServices, true) ?? [];
        }
        
        // Use custom services if available, otherwise use default packages
        if (!empty($customServices)) {
            $services = $customServices;
        } else {
            // Default packages from prices
            $services = [
                ['id' => 1, 'name' => 'Basic Steam', 'description' => 'Cuci eksterior mobil dengan sabun khusus', 'price' => $profile->basic_price ?? 35000],
                ['id' => 2, 'name' => 'Premium Steam', 'description' => 'Pembersihan interior lengkap', 'price' => $profile->premium_price ?? 55000],
                ['id' => 3, 'name' => 'Complete Steam', 'description' => 'Paket lengkap cuci exterior + interior', 'price' => $profile->complete_price ?? 85000],
            ];
        }
        
        // Reviews from database - last 9 reviews
        $reviews = Review::where('mitra_id', $mitra->id)
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->take(9)
            ->get()
            ->map(function($review) {
                // Parse review photos
                $photos = $review->review_photos ? (is_array($review->review_photos) ? $review->review_photos : json_decode((string)$review->review_photos, true)) : [];
                
                // Convert photo paths to full URLs
                $images = array_map(function($path) {
                    if (strpos($path, 'http') === 0) {
                        return $path;
                    }
                    return asset('storage/' . $path);
                }, $photos);
                
                // Get customer avatar
                $avatar = $review->customer->avatar;
                if ($avatar) {
                    // Check if it's a full URL (Google OAuth)
                    if (filter_var($avatar, FILTER_VALIDATE_URL)) {
                        $avatarUrl = $avatar;
                    } elseif (strpos($avatar, '/storage/') === 0 || strpos($avatar, 'storage/') === 0) {
                        // Avatar already has storage path
                        $avatarUrl = asset($avatar);
                    } else {
                        // Relative path, add storage prefix
                        $avatarUrl = asset('storage/' . $avatar);
                    }
                } else {
                    $avatarUrl = asset('images/default-avatar.png');
                }
                
                return [
                    'id' => $review->id,
                    'customerName' => $review->customer->name,
                    'avatar' => $avatarUrl,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'date' => $review->created_at->format('d M Y'),
                    'time' => $review->created_at->format('H:i'),
                    'images' => $images,
                    'mitraResponse' => $review->mitra_reply,
                ];
            });
        
        return view('customer.detail-mitra.minipro', compact('business', 'galleryImages', 'services', 'reviews'));
    }
}
