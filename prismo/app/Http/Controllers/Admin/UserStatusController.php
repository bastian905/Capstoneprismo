<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserStatusController extends Controller
{
    public function toggleStatus(Request $request, $userId)
    {
        // Verify admin access
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        // Prevent admin from disabling themselves
        if (Auth::user()->id == $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menonaktifkan akun sendiri'
            ], 400);
        }
        
        $user = User::findOrFail($userId);
        
        // Toggle status
        $user->status = $user->status === 'Aktif' ? 'Nonaktif' : 'Aktif';
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => $user->status === 'Aktif' 
                ? 'User berhasil diaktifkan' 
                : 'User berhasil dinonaktifkan',
            'status' => $user->status
        ]);
    }
}
