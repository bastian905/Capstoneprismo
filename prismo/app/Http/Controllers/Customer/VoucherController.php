<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\UserVoucher;

class VoucherController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get all available vouchers (not claimed yet) with registration condition check
        $availableVouchers = Voucher::where('is_active', true)
            ->where('end_date', '>=', now())
            ->whereDoesntHave('userVouchers', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get()
            ->filter(function($voucher) use ($user) {
                // Check registration condition
                if ($voucher->registration_condition && $voucher->registration_condition !== 'none' && $voucher->registration_days) {
                    $userRegistrationDays = now()->diffInDays($user->created_at);
                    
                    if ($voucher->registration_condition === 'less_than' && $userRegistrationDays >= $voucher->registration_days) {
                        return false;
                    }
                    
                    if ($voucher->registration_condition === 'greater_than' && $userRegistrationDays <= $voucher->registration_days) {
                        return false;
                    }
                }
                
                // Check max usage
                if ($voucher->max_usage && $voucher->current_usage >= $voucher->max_usage) {
                    return false;
                }
                
                return true;
            })
            ->map(function($voucher) use ($user) {
                // Determine discount type and value
                $discountType = $voucher->discount_percent ? 'percentage' : 'fixed';
                $discountValue = $voucher->discount_percent ?? $voucher->discount_fixed;
                $discountDisplay = $discountType === 'percentage' 
                    ? $discountValue . '%' 
                    : 'Rp ' . number_format((float)($discountValue ?? 0), 0, ',', '.');
                
                return [
                    'id' => $voucher->id,
                    'code' => $voucher->code,
                    'title' => $voucher->title,
                    'type' => $voucher->type,
                    'discount' => $discountDisplay,
                    'minTransaction' => $voucher->min_transaction,
                    'maxDiscount' => $voucher->max_discount,
                    'expiry' => $voucher->end_date->format('Y-m-d'),
                    'color' => $voucher->color ?? '#1c98f5',
                    'terms' => is_array($voucher->terms) ? $voucher->terms : json_decode((string)$voucher->terms, true),
                    'status' => 'available',
                    'claimed' => false,
                    'used' => false,
                ];
            })->values();
        
        // Get claimed vouchers (not used yet) - Tab "Voucher Saya"
        $myVouchers = UserVoucher::where('user_id', $user->id)
            ->whereNull('used_at')
            ->with('voucher')
            ->get()
            ->map(function($userVoucher) {
                $voucher = $userVoucher->voucher;
                
                // Determine discount type and value
                $discountType = $voucher->discount_percent ? 'percentage' : 'fixed';
                $discountValue = $voucher->discount_percent ?? $voucher->discount_fixed;
                $discountDisplay = $discountType === 'percentage' 
                    ? $discountValue . '%' 
                    : 'Rp ' . number_format((float)($discountValue ?? 0), 0, ',', '.');
                
                return [
                    'id' => $voucher->id,
                    'code' => $voucher->code,
                    'title' => $voucher->title,
                    'type' => $voucher->type,
                    'discount' => $discountDisplay,
                    'minTransaction' => $voucher->min_transaction,
                    'maxDiscount' => $voucher->max_discount,
                    'expiry' => $voucher->end_date->format('Y-m-d'),
                    'color' => $voucher->color ?? '#1c98f5',
                    'terms' => is_array($voucher->terms) ? $voucher->terms : json_decode((string)$voucher->terms, true),
                    'status' => 'claimed',
                    'claimed' => true,
                    'used' => false,
                    'claimed_at' => $userVoucher->claimed_at->format('Y-m-d H:i:s'),
                ];
            });
        
        // Get used vouchers - Tab "Terpakai"
        $usedVouchers = UserVoucher::where('user_id', $user->id)
            ->whereNotNull('used_at')
            ->with('voucher')
            ->get()
            ->map(function($userVoucher) {
                $voucher = $userVoucher->voucher;
                
                // Determine discount type and value
                $discountType = $voucher->discount_percent ? 'percentage' : 'fixed';
                $discountValue = $voucher->discount_percent ?? $voucher->discount_fixed;
                $discountDisplay = $discountType === 'percentage' 
                    ? $discountValue . '%' 
                    : 'Rp ' . number_format((float)($discountValue ?? 0), 0, ',', '.');
                
                return [
                    'id' => $voucher->id,
                    'code' => $voucher->code,
                    'title' => $voucher->title,
                    'type' => $voucher->type,
                    'discount' => $discountDisplay,
                    'minTransaction' => $voucher->min_transaction,
                    'maxDiscount' => $voucher->max_discount,
                    'expiry' => $voucher->end_date->format('Y-m-d'),
                    'color' => $voucher->color ?? '#1c98f5',
                    'terms' => is_array($voucher->terms) ? $voucher->terms : json_decode((string)$voucher->terms, true),
                    'status' => 'used',
                    'claimed' => true,
                    'used' => true,
                    'used_at' => $userVoucher->used_at->format('Y-m-d H:i:s'),
                ];
            });
        
        return view('customer.voucher.voucher', compact('availableVouchers', 'myVouchers', 'usedVouchers'));
    }
}
