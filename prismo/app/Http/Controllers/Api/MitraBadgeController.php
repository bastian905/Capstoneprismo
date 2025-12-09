<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Review;

class MitraBadgeController extends Controller
{
    public function getBadgeCounts(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'mitra') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $mitraId = auth()->id();
        
        // Count new bookings (menunggu status = antrian baru)
        $antrianBadgeCount = Booking::where('mitra_id', $mitraId)
            ->where('status', 'menunggu')
            ->count();
        
        // Count reviews without mitra reply (review yang belum dibalas)
        $reviewBadgeCount = Review::where('mitra_id', $mitraId)
            ->whereNull('mitra_reply')
            ->count();
        
        return response()->json([
            'antrian_badge' => $antrianBadgeCount,
            'review_badge' => $reviewBadgeCount
        ]);
    }
}
