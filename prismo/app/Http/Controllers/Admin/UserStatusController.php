<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserStatusController extends Controller
{
    public function toggleStatus(Request $request, $userId)
    {
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
