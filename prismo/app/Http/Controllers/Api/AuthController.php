<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Login user to create session (for web routes)
        Auth::login($user);
        $request->session()->regenerate();

        // Update last activity
        $user->last_activity_at = now();
        $user->save();

        // Create token (for API routes)
        $token = $user->createToken('api-token')->plainTextToken;

        // Prepare avatar URL - handle Google OAuth avatars
        $avatarUrl = $user->avatar;
        if ($avatarUrl && !str_starts_with($avatarUrl, 'http')) {
            $avatarUrl = asset($avatarUrl);
        }

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $avatarUrl,
                'approval_status' => $user->approval_status,
                'profile_completed' => $user->profile_completed,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        try {
            // Delete current access token
            if ($request->user()) {
                $request->user()->currentAccessToken()->delete();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
        } catch (\Exception $e) {
            // Even if token deletion fails, return success
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
        }
    }

    public function user(Request $request)
    {
        $user = $request->user();
        
        // Load relationships based on role
        if ($user->role === 'mitra') {
            $user->load('mitraProfile');
        } elseif ($user->role === 'customer') {
            $user->load('customerProfile');
        }

        return response()->json($user);
    }
}
