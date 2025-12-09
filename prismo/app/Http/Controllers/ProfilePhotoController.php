<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfilePhotoController extends Controller
{
    /**
     * Upload and update profile photo
     */
    public function upload(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        $user = Auth::user();

        try {
            // Determine folder based on role (case-insensitive)
            $folder = strtolower($user->role) === 'mitra' 
                ? 'profile-photos/mitra' 
                : 'profile-photos/customer';
            
            // Delete old photo if exists and not default
            if ($user->avatar && $user->avatar !== '/images/profile.png') {
                // Extract path from URL (remove /storage/ prefix)
                $oldPhotoPath = str_replace('/storage/', '', $user->avatar);
                if (Storage::disk('public')->exists($oldPhotoPath)) {
                    Storage::disk('public')->delete($oldPhotoPath);
                }
            }

            // Store new photo
            $photo = $request->file('photo');
            $filename = $user->id . '_' . time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs($folder, $filename, 'public');

            // Update user avatar
            $user->avatar = '/storage/' . $path;
            
            // Force update timestamp
            $user->touch();
            $user->save();
            
            // Force refresh the authenticated user in session
            Auth::setUser($user->fresh());

            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil diupdate',
                'photo_url' => asset('storage/' . $path),
                'avatar' => $user->avatar,
                'cache_buster' => time()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current profile photo URL
     */
    public function getPhoto()
    {
        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'photo_url' => $user->avatar ? asset($user->avatar) : asset('images/profile.png'),
            'avatar' => $user->avatar ?: '/images/profile.png',
            'is_oauth' => !empty($user->google_id),
        ]);
    }

    /**
     * Delete profile photo
     */
    public function delete()
    {
        $user = Auth::user();

        try {
            // Delete photo file if exists and not default
            if ($user->avatar && $user->avatar !== '/images/profile.png') {
                $photoPath = str_replace('/storage/', '', $user->avatar);
                if (Storage::disk('public')->exists($photoPath)) {
                    Storage::disk('public')->delete($photoPath);
                }
            }

            // Reset to default
            $user->avatar = '/images/profile.png';
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil dihapus',
                'photo_url' => asset('images/profile.png'),
                'avatar' => '/images/profile.png'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus foto: ' . $e->getMessage()
            ], 500);
        }
    }
}

