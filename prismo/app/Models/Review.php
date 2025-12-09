<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'booking_id', 'customer_id', 'mitra_id', 'rating', 'comment',
        'review_photos', 'mitra_reply', 'replied_at'
    ];

    protected $casts = [
        'review_photos' => 'array',
        'replied_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function mitra()
    {
        return $this->belongsTo(User::class, 'mitra_id');
    }
}
