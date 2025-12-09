<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Booking;
use App\Models\Review;

class MitraBadgeComposer
{
    public function compose(View $view)
    {
        if (auth()->check() && auth()->user()->role === 'mitra') {
            $mitraId = auth()->id();
            
            // Count new bookings (menunggu status = antrian baru)
            $antrianBadgeCount = Booking::where('mitra_id', $mitraId)
                ->where('status', 'menunggu')
                ->count();
            
            // Count reviews without mitra reply (review yang belum dibalas)
            $reviewBadgeCount = Review::where('mitra_id', $mitraId)
                ->whereNull('mitra_reply')
                ->count();
            
            $view->with([
                'antrianBadgeCount' => $antrianBadgeCount,
                'reviewBadgeCount' => $reviewBadgeCount
            ]);
        }
    }
}
