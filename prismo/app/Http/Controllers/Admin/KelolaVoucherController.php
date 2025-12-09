<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class KelolaVoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::withCount('userVouchers')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($voucher) {
                // Determine discount type and value
                $discountType = $voucher->discount_percent ? 'percentage' : 'fixed';
                $discountValue = $voucher->discount_percent ?? $voucher->discount_fixed;
                
                return [
                    'id' => $voucher->id,
                    'code' => $voucher->code,
                    'name' => $voucher->title,
                    'description' => $voucher->description,
                    'type' => $voucher->type,
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                    'discount_percent' => $voucher->discount_percent,
                    'discount_fixed' => $voucher->discount_fixed,
                    'min_transaction' => $voucher->min_transaction,
                    'max_discount' => $voucher->max_discount,
                    'max_usage' => $voucher->max_usage,
                    'current_usage' => $voucher->current_usage,
                    'used_count' => $voucher->user_vouchers_count,
                    'start_date' => $voucher->start_date,
                    'end_date' => $voucher->end_date,
                    'registration_condition' => $voucher->registration_condition ?? 'none',
                    'registration_days' => $voucher->registration_days,
                    'color' => $voucher->color ?? '#1c98f5',
                    'is_active' => $voucher->is_active,
                    'created_at' => $voucher->created_at->format('Y-m-d H:i:s')
                ];
            });
        
        return view('admin.kelolavoucher.kelolavoucher', compact('vouchers'));
    }
}
