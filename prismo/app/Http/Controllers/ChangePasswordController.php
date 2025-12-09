<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function showChangePasswordForm(Request $request)
    {
        // Check if user logged in via OAuth
        $oauthProvider = null;
        if (Auth::user()->google_id) {
            $oauthProvider = 'google';
        }
        
        // Determine view based on route
        $view = $request->is('customer/*') 
            ? 'customer.profil.change-password' 
            : 'mitra.profil.change-password';
        
        return view($view, [
            'oauth_provider' => $oauthProvider
        ]);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();
        
        // Prevent password change for OAuth users
        if ($user->google_id) {
            return response()->json([
                'success' => false,
                'message' => 'Password tidak dapat diubah untuk akun OAuth.'
            ], 403);
        }

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'current_password' => ['Password saat ini tidak sesuai.']
                ]
            ], 422);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.'
        ]);
    }
}
