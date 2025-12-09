<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CustomerProfileController;
use App\Http\Controllers\Api\MitraController;
use App\Http\Controllers\Api\MitraBadgeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\WithdrawalController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes - Support both Sanctum token and web session authentication
Route::middleware(['auth:sanctum,web'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Bookings
    Route::apiResource('bookings', BookingController::class);
    Route::get('/bookings/slots/{mitraId}', [BookingController::class, 'getBookedSlots']);
    Route::post('/bookings/{id}/payment-proof', [BookingController::class, 'uploadPaymentProof']);
    Route::post('/bookings/{id}/confirm', [BookingController::class, 'confirmBooking']);
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancelBooking']);
    Route::post('/bookings/{id}/refund', [BookingController::class, 'completeRefund']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'delete']);

    // Mitra Badge Counts
    Route::get('/mitra/badge-counts', [MitraBadgeController::class, 'getBadgeCounts']);

    // Customer Profile
    Route::post('/customer/profile', [CustomerProfileController::class, 'updateProfile']);

    // Mitra
    Route::get('/mitra', [MitraController::class, 'index']);
    Route::get('/mitra/{id}', [MitraController::class, 'show']);
    Route::put('/mitra/profile', [MitraController::class, 'updateProfile']);
    Route::post('/mitra/documents', [MitraController::class, 'uploadDocuments']);
    Route::post('/mitra/gallery', [MitraController::class, 'uploadGallery']);

    // Reviews
    Route::apiResource('reviews', ReviewController::class)->except(['update']);
    Route::post('/reviews/{id}/reply', [ReviewController::class, 'reply']);

    // Vouchers
    Route::get('/vouchers', [VoucherController::class, 'index']);
    Route::get('/vouchers/available', [VoucherController::class, 'available']);
    Route::post('/vouchers/{id}/claim', [VoucherController::class, 'claim']);
    Route::get('/vouchers/my-vouchers', [VoucherController::class, 'myVouchers']);

    // Withdrawals (Mitra only)
    Route::get('/withdrawals/balance-info', [WithdrawalController::class, 'getBalanceInfo']);
    Route::apiResource('withdrawals', WithdrawalController::class);
    
    // Admin routes
    Route::middleware('role:admin')->group(function () {
        // Voucher management
        Route::post('/vouchers', [VoucherController::class, 'store']);
        Route::put('/vouchers/{id}', [VoucherController::class, 'update']);
        Route::delete('/vouchers/{id}', [VoucherController::class, 'destroy']);

        // Withdrawal management
        Route::put('/withdrawals/{id}/approve', [WithdrawalController::class, 'approve']);
        Route::put('/withdrawals/{id}/reject', [WithdrawalController::class, 'reject']);
        Route::put('/withdrawals/{id}/complete', [WithdrawalController::class, 'complete']);
    });
});
