<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;

class CustomerProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get customer statistics
        $totalBooking = Booking::where('customer_id', $user->id)->count();
        $totalPoints = $user->points ?? 0;
        
        return view('customer.profil.uprofil', compact('user', 'totalBooking', 'totalPoints'));
    }
}
