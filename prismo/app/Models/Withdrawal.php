<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = [
        'mitra_id', 'amount', 'bank_name', 'account_number', 'account_name',
        'qris_image', 'status', 'admin_note', 'processed_at', 'processed_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function mitra()
    {
        return $this->belongsTo(User::class, 'mitra_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
