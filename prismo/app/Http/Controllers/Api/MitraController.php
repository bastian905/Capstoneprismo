<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MitraProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MitraController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'mitra')
            ->where('approval_status', 'approved')
            ->with('mitraProfile');

        // Filter by city
        if ($request->has('city')) {
            $query->whereHas('mitraProfile', function ($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        // Filter by rating
        if ($request->has('min_rating')) {
            $query->whereHas('mitraProfile', function ($q) use ($request) {
                $q->where('rating', '>=', $request->min_rating);
            });
        }

        $mitra = $query->get();

        return response()->json($mitra);
    }

    public function show($id)
    {
        $mitra = User::where('role', 'mitra')
            ->with(['mitraProfile', 'mitraReviews' => function ($query) {
                $query->with('customer')->latest()->take(10);
            }])
            ->findOrFail($id);

        return response()->json($mitra);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'mitra') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'business_name' => 'sometimes|string|max:255',
            'contact_person' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string|max:100',
            'province' => 'sometimes|string|max:100',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'description' => 'sometimes|string',
            'operational_hours' => 'sometimes|array',
            'break_times' => 'sometimes|array',
        ]);

        $profile = MitraProfile::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return response()->json($profile);
    }

    public function uploadDocuments(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'mitra') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'ktp_photo' => 'sometimes|image|max:2048',
            'qris_photo' => 'sometimes|image|max:2048',
            'legal_doc' => 'sometimes|file|max:5120',
        ]);

        $profile = MitraProfile::firstOrCreate(['user_id' => $user->id]);
        $updates = [];

        if ($request->hasFile('ktp_photo')) {
            if ($profile->ktp_photo) {
                Storage::disk('public')->delete($profile->ktp_photo);
            }
            $updates['ktp_photo'] = $request->file('ktp_photo')->store('mitra-documents/ktp', 'public');
        }

        if ($request->hasFile('qris_photo')) {
            if ($profile->qris_photo) {
                Storage::disk('public')->delete($profile->qris_photo);
            }
            $updates['qris_photo'] = $request->file('qris_photo')->store('mitra-documents/qris', 'public');
        }

        if ($request->hasFile('legal_doc')) {
            if ($profile->legal_doc) {
                Storage::disk('public')->delete($profile->legal_doc);
            }
            $updates['legal_doc'] = $request->file('legal_doc')->store('mitra-documents/legal', 'public');
        }

        $profile->update($updates);

        return response()->json([
            'message' => 'Documents uploaded successfully',
            'documents' => $updates
        ]);
    }

    public function uploadGallery(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'mitra') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|max:2048',
        ]);

        $profile = MitraProfile::firstOrCreate(['user_id' => $user->id]);
        $existingPhotos = $profile->facility_photos ?? [];
        $newPhotos = [];

        foreach ($request->file('photos') as $photo) {
            $newPhotos[] = $photo->store('mitra-documents/fasilitas', 'public');
        }

        $allPhotos = array_merge($existingPhotos, $newPhotos);
        $profile->update(['facility_photos' => $allPhotos]);

        return response()->json([
            'message' => 'Gallery photos uploaded successfully',
            'photos' => $allPhotos
        ]);
    }
}
