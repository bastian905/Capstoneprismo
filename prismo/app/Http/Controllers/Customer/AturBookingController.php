<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\MitraProfile;

class AturBookingController extends Controller
{
    public function show($mitraId)
    {
        // Validate input to prevent SQL injection
        if (!is_numeric($mitraId) || $mitraId < 1) {
            abort(404);
        }
        
        // Verify customer role
        if (!Auth::check() || Auth::user()->role !== 'customer') {
            abort(403, 'Unauthorized access');
        }
        
        $mitra = User::where('id', (int)$mitraId)
            ->where('role', 'mitra')
            ->where('approval_status', 'approved')
            ->with('mitraProfile')
            ->firstOrFail();
        
        $profile = $mitra->mitraProfile;
        
        // Get operational hours from database
        $operationalHours = $profile->operational_hours ?? [];
        
        // Convert operational hours to the format expected by booking.js
        $dailyHours = [];
        $dayMapping = [
            'sunday' => 0,
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            // Indonesian day names
            'minggu' => 0,
            'senin' => 1,
            'selasa' => 2,
            'rabu' => 3,
            'kamis' => 4,
            'jumat' => 5,
            'sabtu' => 6
        ];
        
        $operationalDays = [];
        
        foreach ($operationalHours as $day => $hours) {
            $dayIndex = $dayMapping[strtolower($day)] ?? null;
            
            if ($dayIndex === null) continue;
            
            // Handle object format (new format)
            if (is_array($hours) && isset($hours['enabled'])) {
                if ($hours['enabled']) {
                    $dailyHours[$dayIndex] = [
                        'open' => $hours['open'],
                        'close' => $hours['close']
                    ];
                    $operationalDays[] = $dayIndex;
                } else {
                    $dailyHours[$dayIndex] = null;
                }
            }
            // Handle string format (old format like "08:00-17:00" or "Tutup")
            else if (is_string($hours)) {
                if (strtolower($hours) !== 'tutup' && strpos($hours, '-') !== false) {
                    list($open, $close) = explode('-', $hours);
                    $dailyHours[$dayIndex] = [
                        'open' => trim($open),
                        'close' => trim($close)
                    ];
                    $operationalDays[] = $dayIndex;
                } else {
                    $dailyHours[$dayIndex] = null;
                }
            }
        }
        
        // Build break schedules
        $breakSchedules = [];
        
        foreach ($operationalHours as $day => $hours) {
            if (is_array($hours) && isset($hours['hasBreakSchedule']) && $hours['hasBreakSchedule']) {
                if (isset($hours['breakSchedules']) && is_array($hours['breakSchedules'])) {
                    $dayIndex = $dayMapping[strtolower($day)] ?? null;
                    if ($dayIndex !== null) {
                        foreach ($hours['breakSchedules'] as $breakSchedule) {
                            if (!isset($breakSchedules[$dayIndex])) {
                                $breakSchedules[$dayIndex] = [];
                            }
                            $breakSchedules[$dayIndex][] = [
                                'start' => $breakSchedule['open'],
                                'end' => $breakSchedule['close']
                            ];
                        }
                    }
                }
            }
        }
        
        // Get customer points
        $customer = Auth::user();
        $customerPoints = $customer->points ?? 0;
        
        $businessData = [
            'mitraId' => $mitra->id,
            'businessName' => $profile->business_name,
            'city' => $profile->city,
            'province' => $profile->province,
            'address' => $profile->address,
            'phone' => $mitra->phone,
            'dailyHours' => $dailyHours,
            'operationalDays' => $operationalDays,
            'breakSchedules' => $breakSchedules,
            'isOpen' => $profile->is_open ?? true,
            'services' => $profile->custom_services ?? [],
            'customerPoints' => $customerPoints
        ];
        
        return view('customer.atur-booking.booking', compact('businessData'));
    }
}
