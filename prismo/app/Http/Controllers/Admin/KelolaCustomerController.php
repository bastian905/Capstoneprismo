<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;

class KelolaCustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')
            ->withCount('bookings')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone ?? '-',
                    'joinDate' => $customer->created_at->format('Y-m-d'),
                    'totalBooking' => $customer->bookings_count,
                    'points' => $customer->points ?? 0,
                    'status' => $customer->is_active ? 'Active' : 'Inactive'
                ];
            });
        
        return view('admin.kelolacustomer.kelolacustomer', compact('customers'));
    }
}
