<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'booking_id' => $notification->booking_id,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans()
                ];
            });
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count()
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        
        $notification = Notification::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();
        
        $notification->markAsRead();
        
        return response()->json([
            'message' => 'Notification marked as read'
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        
        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        
        return response()->json([
            'message' => 'All notifications marked as read'
        ]);
    }

    public function getUnreadCount(Request $request)
    {
        $user = $request->user();
        
        $count = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        return response()->json([
            'unread_count' => $count
        ]);
    }

    public function delete(Request $request, $id)
    {
        $user = $request->user();
        
        $notification = Notification::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();
        
        $notification->delete();
        
        return response()->json([
            'message' => 'Notification deleted successfully'
        ]);
    }
}
