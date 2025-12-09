# Real-Time System Implementation Guide

## Overview
Implementasi sistem real-time menggunakan Laravel Broadcasting dengan Pusher atau Laravel Reverb untuk notifikasi dan update otomatis tanpa refresh.

## Prerequisites
```bash
composer require pusher/pusher-php-server
npm install --save-dev laravel-echo pusher-js
```

## Step 1: Configuration

### 1.1 Update `.env`
```env
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=ap1
PUSHER_SCHEME=https
PUSHER_HOST=
PUSHER_PORT=443

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 1.2 Update `config/broadcasting.php`
File ini sudah ada, pastikan konfigurasi Pusher sudah benar.

## Step 2: Create Broadcast Events

### 2.1 Booking Status Updated Event
```bash
php artisan make:event BookingStatusUpdated
```

File: `app/Events/BookingStatusUpdated.php`
```php
<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('user.' . $this->booking->customer_id),
            new PrivateChannel('user.' . $this->booking->mitra_id)
        ];
    }

    public function broadcastWith()
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'status' => $this->booking->status,
            'message' => 'Status booking telah diperbarui menjadi ' . $this->booking->status
        ];
    }
}
```

### 2.2 New Notification Event
```bash
php artisan make:event NewNotification
```

File: `app/Events/NewNotification.php`
```php
<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->notification->user_id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->notification->id,
            'type' => $this->notification->type,
            'title' => $this->notification->title,
            'message' => $this->notification->message,
            'created_at' => $this->notification->created_at->toISOString()
        ];
    }
}
```

### 2.3 Withdrawal Status Updated Event
```bash
php artisan make:event WithdrawalStatusUpdated
```

File: `app/Events/WithdrawalStatusUpdated.php`
```php
<?php

namespace App\Events;

use App\Models\Withdrawal;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WithdrawalStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $withdrawal;

    public function __construct(Withdrawal $withdrawal)
    {
        $this->withdrawal = $withdrawal;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->withdrawal->mitra_id);
    }

    public function broadcastWith()
    {
        return [
            'withdrawal_id' => $this->withdrawal->id,
            'amount' => $this->withdrawal->amount,
            'status' => $this->withdrawal->status,
            'message' => 'Status penarikan saldo telah diperbarui'
        ];
    }
}
```

## Step 3: Update Controllers to Broadcast Events

### 3.1 Update AntrianController
```php
use App\Events\BookingStatusUpdated;

public function updateStatus(Request $request)
{
    // ... existing code ...
    
    $booking->save();
    
    // Broadcast event
    broadcast(new BookingStatusUpdated($booking))->toOthers();
    
    // ... rest of code ...
}
```

### 3.2 Update NotificationService
```php
use App\Events\NewNotification;

public function createNotification($userId, $type, $title, $message, $relatedId = null)
{
    $notification = Notification::create([
        'user_id' => $userId,
        'type' => $type,
        'title' => $title,
        'message' => $message,
        'related_id' => $relatedId,
        'is_read' => false,
    ]);
    
    // Broadcast event
    broadcast(new NewNotification($notification))->toOthers();
    
    return $notification;
}
```

## Step 4: Setup Broadcast Authentication

Update `routes/channels.php`:
```php
<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
```

## Step 5: Frontend Integration

### 5.1 Update `resources/js/bootstrap.js`
```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    },
});
```

### 5.2 Listen to Events in Frontend

#### For Customer Dashboard:
```javascript
// Listen for booking status updates
Echo.private(`user.${userId}`)
    .listen('BookingStatusUpdated', (e) => {
        console.log('Booking status updated:', e);
        // Update UI
        showNotification(`Booking ${e.booking_code} status: ${e.status}`);
        refreshBookingList();
    })
    .listen('NewNotification', (e) => {
        console.log('New notification:', e);
        // Update notification badge
        updateNotificationBadge();
        showToast(e.title, e.message);
    });
```

#### For Mitra Dashboard:
```javascript
// Listen for new bookings and updates
Echo.private(`user.${userId}`)
    .listen('BookingStatusUpdated', (e) => {
        refreshBookingQueue();
    })
    .listen('NewNotification', (e) => {
        updateNotificationBadge();
        showToast(e.title, e.message);
    })
    .listen('WithdrawalStatusUpdated', (e) => {
        refreshSaldoInfo();
        showNotification(e.message);
    });
```

## Step 6: Build Assets
```bash
npm run build
```

## Testing
1. Open browser console
2. Check for Echo connection: `window.Echo`
3. Trigger events (update booking status, create notification)
4. Verify real-time updates without page refresh

## Alternative: Laravel Reverb (Free Option)

If you want to use Laravel's own WebSocket server instead of Pusher:

```bash
composer require laravel/reverb
php artisan reverb:install
```

Update `.env`:
```env
BROADCAST_DRIVER=reverb

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

Start Reverb server:
```bash
php artisan reverb:start
```

## Features Implemented
✅ Real-time booking status updates
✅ Real-time notifications
✅ Real-time withdrawal status updates
✅ Auto-refresh without manual reload
✅ Private channels per user
✅ Authentication for broadcast channels

## Notes
- Pusher free tier: 100 concurrent connections, 200k messages/day
- Laravel Reverb: Unlimited, self-hosted
- Make sure queue worker is running: `php artisan queue:work`
- For production, use supervisor to keep Reverb running
