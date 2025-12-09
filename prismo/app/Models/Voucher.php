<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code', 'title', 'description', 'type', 'discount_percent', 'discount_fixed',
        'max_discount', 'min_transaction', 'start_date', 'end_date',
        'max_usage', 'current_usage', 'max_usage_per_user', 'registration_condition',
        'registration_days', 'color', 'terms', 'is_active'
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'discount_fixed' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'min_transaction' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'terms' => 'array',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_vouchers')
            ->withPivot('claimed_at', 'used_at', 'booking_id')
            ->withTimestamps();
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function userVouchers()
    {
        return $this->hasMany(UserVoucher::class);
    }
}
