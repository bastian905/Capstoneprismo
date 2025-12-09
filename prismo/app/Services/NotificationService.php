<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create notification for user(s)
     */
    public static function create($userId, $type, $title, $message, $relatedId = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'booking_id' => $relatedId,
            'is_read' => false
        ]);
    }

    /**
     * Notify when booking is created
     */
    public static function bookingCreated($booking)
    {
        // Notify mitra
        self::create(
            $booking->mitra_id,
            'booking_new',
            'Pesanan Baru',
            "Pesanan baru dari {$booking->customer->name}. ID: #{$booking->id}",
            $booking->id
        );
    }

    /**
     * Notify when booking is confirmed by mitra
     */
    public static function bookingConfirmed($booking)
    {
        // Notify customer
        self::create(
            $booking->customer_id,
            'booking_confirmed',
            'Pesanan Dikonfirmasi',
            "Pesanan Anda #{$booking->id} telah dikonfirmasi oleh {$booking->mitra->name}",
            $booking->id
        );
    }

    /**
     * Notify when booking is started
     */
    public static function bookingStarted($booking)
    {
        // Notify customer
        self::create(
            $booking->customer_id,
            'booking_started',
            'Layanan Dimulai',
            "Layanan untuk pesanan #{$booking->id} telah dimulai",
            $booking->id
        );
    }

    /**
     * Notify when booking is completed
     */
    public static function bookingCompleted($booking)
    {
        // Notify customer
        self::create(
            $booking->customer_id,
            'booking_completed',
            'Layanan Selesai',
            "Pesanan #{$booking->id} telah selesai. Silakan berikan review!",
            $booking->id
        );
    }

    /**
     * Notify when booking is cancelled
     */
    public static function bookingCancelled($booking, $cancelledBy)
    {
        $targetUserId = ($cancelledBy === 'customer') ? $booking->mitra_id : $booking->customer_id;
        $actor = ($cancelledBy === 'customer') ? 'customer' : 'mitra';
        
        self::create(
            $targetUserId,
            'booking_cancelled',
            'Pesanan Dibatalkan',
            "Pesanan #{$booking->id} telah dibatalkan oleh {$actor}",
            $booking->id
        );
    }

    /**
     * Notify when withdrawal is approved
     */
    public static function withdrawalApproved($withdrawal)
    {
        self::create(
            $withdrawal->mitra_id,
            'withdrawal_approved',
            'Penarikan Disetujui',
            "Penarikan saldo sebesar Rp" . number_format($withdrawal->amount, 0, ',', '.') . " telah disetujui",
            null
        );
    }

    /**
     * Notify when withdrawal is completed
     */
    public static function withdrawalCompleted($withdrawal)
    {
        self::create(
            $withdrawal->mitra_id,
            'withdrawal_completed',
            'Penarikan Selesai',
            "Penarikan saldo sebesar Rp" . number_format($withdrawal->amount, 0, ',', '.') . " telah selesai diproses",
            null
        );
    }

    /**
     * Notify when withdrawal is rejected
     */
    public static function withdrawalRejected($withdrawal)
    {
        self::create(
            $withdrawal->mitra_id,
            'withdrawal_rejected',
            'Penarikan Ditolak',
            "Penarikan saldo sebesar Rp" . number_format($withdrawal->amount, 0, ',', '.') . " ditolak. Alasan: " . ($withdrawal->admin_note ?? 'Tidak ada keterangan'),
            null
        );
    }

    /**
     * Notify when mitra is approved
     */
    public static function mitraApproved($user)
    {
        self::create(
            $user->id,
            'mitra_approved',
            'Akun Disetujui',
            "Selamat! Akun mitra Anda telah disetujui dan dapat mulai menerima pesanan",
            null
        );
    }

    /**
     * Notify when mitra is rejected
     */
    public static function mitraRejected($user, $reason)
    {
        self::create(
            $user->id,
            'mitra_rejected',
            'Akun Ditolak',
            "Akun mitra Anda ditolak. Alasan: {$reason}",
            null
        );
    }

    /**
     * Notify when review is received
     */
    public static function reviewReceived($review)
    {
        self::create(
            $review->mitra_id,
            'review_new',
            'Review Baru',
            "Anda mendapat review {$review->rating} bintang dari {$review->customer->name}",
            $review->booking_id
        );
    }

    /**
     * Get unread count for user
     */
    public static function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark all as read for user
     */
    public static function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    /**
     * Notify all admins
     */
    private static function notifyAdmins($type, $title, $message, $relatedId = null)
    {
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            self::create($admin->id, $type, $title, $message, $relatedId);
        }
    }

    /**
     * Notify admin when new mitra registers
     */
    public static function newMitraRegistration($mitra)
    {
        self::notifyAdmins(
            'mitra_new',
            'Mitra Baru Mendaftar',
            "Mitra baru '{$mitra->name}' telah mendaftar dan menunggu persetujuan"
        );
    }

    /**
     * Notify admin when new withdrawal request
     */
    public static function newWithdrawalRequest($withdrawal)
    {
        self::notifyAdmins(
            'withdrawal_new',
            'Permintaan Penarikan Saldo',
            "Permintaan penarikan saldo sebesar Rp" . number_format($withdrawal->amount, 0, ',', '.') . " dari {$withdrawal->mitra->name}"
        );
    }

    /**
     * Notify admin when new booking
     */
    public static function newBookingForAdmin($booking)
    {
        self::notifyAdmins(
            'booking_new_admin',
            'Booking Baru',
            "Booking baru #{$booking->id} dari {$booking->customer->name} ke {$booking->mitra->name}",
            $booking->id
        );
    }
}
