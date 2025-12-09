<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MitraProfile extends Model
{
    protected $fillable = [
        'user_id', 'business_name', 'establishment_year', 'contact_person', 'phone', 'address', 
        'city', 'province', 'postal_code', 'map_location', 'description', 'operational_hours', 'break_times',
        'ktp_photo', 'qris_photo', 'legal_doc', 'facility_photos', 'reject_reason',
        'rating', 'review_count', 'total_bookings', 'balance',
        'basic_price', 'premium_price', 'complete_price', 'is_open', 'closing_time', 'custom_services'
    ];

    protected $casts = [
        'operational_hours' => 'array',
        'break_times' => 'array',
        'facility_photos' => 'array',
        'custom_services' => 'array',
        'rating' => 'decimal:1',
        'balance' => 'decimal:2',
        'basic_price' => 'decimal:2',
        'premium_price' => 'decimal:2',
        'complete_price' => 'decimal:2',
        'is_open' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'mitra_id', 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'mitra_id', 'user_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'mitra_id', 'user_id');
    }
}
