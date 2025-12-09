<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'booking_code', 'customer_id', 'mitra_id', 'service_type', 'vehicle_type',
        'vehicle_plate', 'booking_date', 'booking_time', 'base_price', 'discount_amount',
        'final_price', 'voucher_id', 'payment_method', 'payment_proof', 'payment_status',
        'status', 'confirmed_at', 'started_at', 'completed_at', 'cancelled_at', 'cancellation_reason',
        'refund_completed_at'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'base_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_price' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refund_completed_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function mitra()
    {
        return $this->belongsTo(User::class, 'mitra_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
