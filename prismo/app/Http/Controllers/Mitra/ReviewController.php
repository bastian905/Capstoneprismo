<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Booking;

class ReviewController extends Controller
{
    public function index()
    {
        $mitra = auth()->user();
        
        // Get all reviews for this mitra
        $reviews = Review::where('mitra_id', $mitra->id)
            ->with(['customer', 'booking'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($review) {
                $photos = $review->review_photos ? (is_array($review->review_photos) ? $review->review_photos : json_decode((string)$review->review_photos, true)) : [];
                $images = array_map(function($photo) {
                    return asset('storage/' . $photo);
                }, $photos);
                
                return [
                    'id' => $review->id,
                    'bookingCode' => $review->booking->booking_code ?? '-',
                    'reviewer' => [
                        'name' => $review->customer->name,
                        'avatar' => $review->customer->avatar 
                            ? (filter_var($review->customer->avatar, FILTER_VALIDATE_URL) 
                                ? $review->customer->avatar 
                                : asset('storage/' . $review->customer->avatar))
                            : asset('images/default-avatar.png'),
                    ],
                    'service' => $review->booking->service_type ?? '-',
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'photos' => $photos,
                    'images' => $images,
                    'time' => $review->created_at->diffForHumans(),
                    'date' => $review->created_at->format('d M Y'),
                    'response' => $review->mitra_reply,
                    'mitraResponse' => $review->mitra_reply,
                    'hasResponse' => $review->mitra_reply ? true : false,
                    'hasReply' => $review->mitra_reply ? true : false,
                    'reply' => $review->mitra_reply ? [
                        'text' => $review->mitra_reply,
                        'time' => $review->updated_at->diffForHumans(),
                    ] : null,
                ];
            });
        
        // Calculate rating statistics
        $totalReviews = $reviews->count();
        $averageRating = $reviews->avg('rating');
        $ratingDistribution = [
            5 => $reviews->where('rating', 5)->count(),
            4 => $reviews->where('rating', 4)->count(),
            3 => $reviews->where('rating', 3)->count(),
            2 => $reviews->where('rating', 2)->count(),
            1 => $reviews->where('rating', 1)->count(),
        ];
        
        return view('mitra.review.review', compact('reviews', 'totalReviews', 'averageRating', 'ratingDistribution'));
    }
    
    public function reply(Request $request)
    {
        $mitra = auth()->user();
        
        $validated = $request->validate([
            'review_id' => 'required|integer',
            'reply' => 'required|string|max:500',
        ]);
        
        // Find review
        $review = Review::where('id', $validated['review_id'])
            ->where('mitra_id', $mitra->id)
            ->first();
        
        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review tidak ditemukan'
            ], 404);
        }
        
        // Update review with mitra reply
        $review->mitra_reply = $validated['reply'];
        $review->save();
        
        // TODO: Send notification to customer about mitra reply
        
        return response()->json([
            'success' => true,
            'message' => 'Respon berhasil dikirim',
            'data' => [
                'review_id' => $review->id,
                'reply' => $review->mitra_reply,
            ]
        ]);
    }
    
    public function updateReply(Request $request)
    {
        $mitra = auth()->user();
        
        $validated = $request->validate([
            'review_id' => 'required|integer',
            'reply' => 'required|string|max:500',
        ]);
        
        // Find review
        $review = Review::where('id', $validated['review_id'])
            ->where('mitra_id', $mitra->id)
            ->whereNotNull('mitra_reply')
            ->first();
        
        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review atau respon tidak ditemukan'
            ], 404);
        }
        
        // Update reply
        $review->mitra_reply = $validated['reply'];
        $review->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Respon berhasil diupdate',
            'data' => [
                'review_id' => $review->id,
                'reply' => $review->mitra_reply,
            ]
        ]);
    }
    
    public function deleteReply(Request $request)
    {
        $mitra = auth()->user();
        
        $validated = $request->validate([
            'review_id' => 'required|integer',
        ]);
        
        // Find review
        $review = Review::where('id', $validated['review_id'])
            ->where('mitra_id', $mitra->id)
            ->whereNotNull('mitra_reply')
            ->first();
        
        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review atau respon tidak ditemukan'
            ], 404);
        }
        
        // Delete reply
        $review->mitra_reply = null;
        $review->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Respon berhasil dihapus',
        ]);
    }
}
