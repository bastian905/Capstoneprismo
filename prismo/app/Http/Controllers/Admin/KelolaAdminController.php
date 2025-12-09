<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class KelolaAdminController extends Controller
{
    public function index()
    {
        $admins = User::where('role', 'admin')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($admin) {
                return [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'created_at' => $admin->created_at->format('Y-m-d H:i:s')
                ];
            });
        
        return view('admin.kelolaadmin.kelolaadmin', compact('admins'));
    }
}
