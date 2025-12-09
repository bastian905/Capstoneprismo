<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserVoucher;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::orderBy('created_at', 'desc')->get();
        return response()->json($vouchers);
    }

    public function available(Request $request)
    {
        $user = $request->user();
        
        $vouchers = Voucher::where('is_active', true)
            ->where('end_date', '>=', now())
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('max_usage')
                    ->orWhereRaw('current_usage < max_usage');
            })
            ->get()
            ->filter(function ($voucher) use ($user) {
                // Check user claim limit
                $userClaimCount = UserVoucher::where('user_id', $user->id)
                    ->where('voucher_id', $voucher->id)
                    ->count();
                
                if ($userClaimCount >= $voucher->max_usage_per_user) {
                    return false;
                }
                
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
                
                return true;
            })
            ->values();

        return response()->json($vouchers);
    }

    public function myVouchers(Request $request)
    {
        $user = $request->user();
        
        $vouchers = $user->vouchers()
            ->withPivot('claimed_at', 'used_at', 'booking_id')
            ->orderBy('user_vouchers.claimed_at', 'desc')
            ->get();

        return response()->json($vouchers);
    }

    public function claim(Request $request, $id)
    {
        $user = $request->user();
        $voucher = Voucher::findOrFail($id);

        // Check if voucher is active and valid
        if (!$voucher->is_active) {
            return response()->json(['message' => 'Voucher tidak aktif'], 400);
        }

        if ($voucher->end_date < now()) {
            return response()->json(['message' => 'Voucher sudah kadaluarsa'], 400);
        }

        if ($voucher->start_date && $voucher->start_date > now()) {
            return response()->json(['message' => 'Voucher belum dapat diklaim'], 400);
        }

        // Check max usage
        if ($voucher->max_usage && $voucher->current_usage >= $voucher->max_usage) {
            return response()->json(['message' => 'Voucher sudah habis'], 400);
        }

        // Check user claim limit
        $userClaimCount = UserVoucher::where('user_id', $user->id)
            ->where('voucher_id', $voucher->id)
            ->count();

        if ($userClaimCount >= $voucher->max_usage_per_user) {
            return response()->json(['message' => 'Anda sudah mencapai batas klaim voucher ini'], 400);
        }

        // Check if already claimed
        $existingClaim = UserVoucher::where('user_id', $user->id)
            ->where('voucher_id', $voucher->id)
            ->whereNull('used_at')
            ->first();

        if ($existingClaim) {
            return response()->json(['message' => 'Anda sudah memiliki voucher ini'], 400);
        }

        // Check registration condition
        if ($voucher->registration_condition && $voucher->registration_condition !== 'none' && $voucher->registration_days) {
            $userRegistrationDays = now()->diffInDays($user->created_at);
            
            if ($voucher->registration_condition === 'less_than' && $userRegistrationDays >= $voucher->registration_days) {
                return response()->json(['message' => 'Voucher ini hanya untuk pengguna yang terdaftar kurang dari ' . $voucher->registration_days . ' hari'], 400);
            }
            
            if ($voucher->registration_condition === 'greater_than' && $userRegistrationDays <= $voucher->registration_days) {
                return response()->json(['message' => 'Voucher ini hanya untuk pengguna yang terdaftar lebih dari ' . $voucher->registration_days . ' hari'], 400);
            }
        }

        // Create user voucher
        UserVoucher::create([
            'user_id' => $user->id,
            'voucher_id' => $voucher->id,
            'claimed_at' => now(),
        ]);

        // Increment usage count
        $voucher->increment('current_usage');

        return response()->json(['message' => 'Voucher berhasil diklaim']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:vouchers,code',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|in:discount,cashback,free_service',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_fixed' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'min_transaction' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'required|date',
            'max_usage' => 'nullable|integer|min:1',
            'max_usage_per_user' => 'nullable|integer|min:1',
            'registration_condition' => 'nullable|in:none,less_than,greater_than',
            'registration_days' => 'nullable|integer|min:1',
            'color' => 'nullable|string',
            'terms' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $validated['current_usage'] = 0;
        $validated['max_usage_per_user'] = $validated['max_usage_per_user'] ?? 1;
        $validated['registration_condition'] = $validated['registration_condition'] ?? 'none';

        $voucher = Voucher::create($validated);

        return response()->json($voucher, 201);
    }

    public function update(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:discount,cashback,free_service',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_fixed' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'min_transaction' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'sometimes|date',
            'max_usage' => 'nullable|integer|min:1',
            'max_usage_per_user' => 'nullable|integer|min:1',
            'registration_condition' => 'nullable|in:none,less_than,greater_than',
            'registration_days' => 'nullable|integer|min:1',
            'color' => 'nullable|string',
            'terms' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $voucher->update($validated);

        return response()->json($voucher);
    }

    public function destroy($id)
    {
        $voucher = Voucher::findOrFail($id);
        
        // Check if voucher is used
        $usedCount = UserVoucher::where('voucher_id', $voucher->id)
            ->whereNotNull('used_at')
            ->count();

        if ($usedCount > 0) {
            return response()->json(['message' => 'Tidak dapat menghapus voucher yang sudah digunakan'], 400);
        }

        $voucher->delete();

        return response()->json(['message' => 'Voucher berhasil dihapus']);
    }
}
