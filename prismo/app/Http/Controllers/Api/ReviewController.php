<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\MitraProfile;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['customer', 'mitra', 'booking']);

        // Filter by mitra
        if ($request->has('mitra_id')) {
            $query->where('mitra_id', $request->mitra_id);
        }

        // Filter by customer
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by rating
        if ($request->has('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        $reviews = $query->orderBy('created_at', 'desc')->get();

        return response()->json($reviews);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
            'review_photos' => 'nullable|array',
            'review_photos.*' => 'image|max:2048',
        ]);

        $user = $request->user();
        $booking = Booking::findOrFail($validated['booking_id']);

        // Check if user is the customer of this booking
        if ($booking->customer_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check if booking is completed
        if ($booking->status !== 'selesai') {
            return response()->json(['message' => 'Booking belum selesai'], 400);
        }

        // Check if review already exists
        if (Review::where('booking_id', $booking->id)->exists()) {
            return response()->json(['message' => 'Review sudah ada untuk booking ini'], 400);
        }

        // Upload photos if any
        $photoPaths = [];
        if ($request->hasFile('review_photos')) {
            foreach ($request->file('review_photos') as $photo) {
                $photoPaths[] = $photo->store('review-photos', 'public');
            }
        }

        // Create review
        $review = Review::create([
            'booking_id' => $booking->id,
            'customer_id' => $user->id,
            'mitra_id' => $booking->mitra_id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'review_photos' => $photoPaths,
        ]);

        // Update mitra rating and review count
        try {
            $this->updateMitraRating($booking->mitra_id);
        } catch (\Exception $e) {
            \Log::error('Failed to update mitra rating: ' . $e->getMessage());
        }

        $review->load(['customer', 'mitra', 'booking']);

        return response()->json($review, 201);
    }

    public function show($id)
    {
        $review = Review::with(['customer', 'mitra', 'booking'])->findOrFail($id);
        return response()->json($review);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $review = Review::findOrFail($id);

        // Only customer who created the review or admin can delete
        if ($user->role !== 'admin' && $review->customer_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete photos
        if ($review->review_photos) {
            foreach ($review->review_photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        $mitraId = $review->mitra_id;
        $review->delete();

        // Update mitra rating
        $this->updateMitraRating($mitraId);

        return response()->json(['message' => 'Review berhasil dihapus']);
    }

    public function reply(Request $request, $id)
    {
        $user = $request->user();
        $review = Review::findOrFail($id);

        // Only the mitra who received the review can reply
        if ($user->role !== 'mitra' || $review->mitra_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'mitra_reply' => 'required|string',
        ]);

        $review->update([
            'mitra_reply' => $validated['mitra_reply'],
            'replied_at' => now(),
        ]);

        return response()->json($review);
    }

    private function updateMitraRating($mitraId)
    {
        $reviews = Review::where('mitra_id', $mitraId)->get();
        $reviewCount = $reviews->count();
        $averageRating = $reviewCount > 0 ? round($reviews->avg('rating'), 1) : 0;

        MitraProfile::where('user_id', $mitraId)->update([
            'rating' => $averageRating,
            'review_count' => $reviewCount,
        ]);
    }
}
