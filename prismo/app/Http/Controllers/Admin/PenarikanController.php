<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class PenarikanController extends Controller
{
    public function index()
    {
        // Only show pending withdrawals in approval page
        $withdrawals = Withdrawal::with(['mitra.mitraProfile'])
            ->where('status', 'pending')  // Only pending withdrawals
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($w) {
                $profile = $w->mitra->mitraProfile ?? null;
                
                return [
                    'id' => $w->id,
                    'withdrawal_code' => 'WD-' . str_pad($w->id, 5, '0', STR_PAD_LEFT),
                    'mitra_id' => $w->mitra_id,
                    'mitra_name' => $profile->business_name ?? $w->mitra->name ?? '-',
                    'mitra_email' => $w->mitra->email ?? '-',
                    'amount' => $w->amount,
                    'bank_name' => $w->bank_name,
                    'account_number' => $w->account_number,
                    'account_name' => $w->account_name,
                    'qris_image' => $profile->qris_photo ?? null,  // Gunakan QRIS dari form-mitra
                    'status' => $w->status,
                    'admin_note' => $w->admin_note,
                    'created_at' => $w->created_at->format('Y-m-d H:i:s'),
                    'processed_at' => $w->processed_at ? $w->processed_at->format('Y-m-d H:i:s') : null,
                    'mitra' => [
                        'name' => $profile->business_name ?? $w->mitra->name ?? '-',
                        'owner' => $w->mitra->name ?? '-',
                        'email' => $w->mitra->email ?? '-',
                        'phone' => $profile->phone ?? '-',
                        'address' => $profile->city ?? $profile->address ?? '-'
                    ]
                ];
            });
        
        return view('admin.dashboard.penarikan', compact('withdrawals'));
    }
}
