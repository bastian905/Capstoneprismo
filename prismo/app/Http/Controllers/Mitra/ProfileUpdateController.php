<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileUpdateController extends Controller
{
    /**
     * Update operational hours
     */
    public function updateOperationalHours(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'mitra') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'operational_hours' => 'required|array',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        $mitraProfile = $user->mitraProfile;
        
        if (!$mitraProfile) {
            return response()->json(['success' => false, 'message' => 'Profil mitra tidak ditemukan'], 404);
        }
        
        $mitraProfile->operational_hours = $request->operational_hours;
        $mitraProfile->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Jam operasional berhasil disimpan',
            'data' => $mitraProfile->operational_hours
        ]);
    }
    
    /**
     * Update service prices
     */
    public function updateServicePrices(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'mitra') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'basic_price' => 'nullable|numeric|min:0',
            'premium_price' => 'nullable|numeric|min:0',
            'complete_price' => 'nullable|numeric|min:0',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        $mitraProfile = $user->mitraProfile;
        
        if (!$mitraProfile) {
            return response()->json(['success' => false, 'message' => 'Profil mitra tidak ditemukan'], 404);
        }
        
        if ($request->has('basic_price')) {
            $mitraProfile->basic_price = $request->basic_price;
        }
        if ($request->has('premium_price')) {
            $mitraProfile->premium_price = $request->premium_price;
        }
        if ($request->has('complete_price')) {
            $mitraProfile->complete_price = $request->complete_price;
        }
        
        $mitraProfile->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Harga layanan berhasil disimpan',
            'data' => [
                'basic_price' => $mitraProfile->basic_price,
                'premium_price' => $mitraProfile->premium_price,
                'complete_price' => $mitraProfile->complete_price,
            ]
        ]);
    }
    
    /**
     * Update operational status (buka/tutup)
     */
    public function updateStatus(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'mitra') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'is_open' => 'required|boolean',
            'closing_time' => 'nullable|date_format:H:i',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        $mitraProfile = $user->mitraProfile;
        
        if (!$mitraProfile) {
            return response()->json(['success' => false, 'message' => 'Profil mitra tidak ditemukan'], 404);
        }
        
        $mitraProfile->is_open = $request->is_open;
        
        if ($request->has('closing_time')) {
            $mitraProfile->closing_time = $request->closing_time;
        }
        
        $mitraProfile->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Status operasional berhasil diupdate',
            'data' => [
                'is_open' => $mitraProfile->is_open,
                'closing_time' => $mitraProfile->closing_time,
            ]
        ]);
    }
    
    /**
     * Update custom services (add/edit/delete)
     */
    public function updateCustomServices(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'mitra') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'services' => 'required|array',
            'services.*.id' => 'required',
            'services.*.name' => 'required|string|max:255',
            'services.*.price' => 'required|numeric|min:0',
            'services.*.capacity' => 'required|integer|min:1|max:50',
            'services.*.description' => 'required|string',
            'services.*.max_slots' => 'nullable|integer|min:1|max:50',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        // Validate unique service names
        $serviceNames = array_column($request->services, 'name');
        $uniqueNames = array_unique($serviceNames);
        
        if (count($serviceNames) !== count($uniqueNames)) {
            return response()->json([
                'success' => false,
                'message' => 'Nama layanan harus unik. Ada duplikat nama layanan.'
            ], 422);
        }
        
        $mitraProfile = $user->mitraProfile;
        
        if (!$mitraProfile) {
            return response()->json(['success' => false, 'message' => 'Profil mitra tidak ditemukan'], 404);
        }
        
        // Ensure max_slots is set for each service (fallback to capacity if not set)
        $services = collect($request->services)->map(function($service) {
            if (!isset($service['max_slots']) || empty($service['max_slots'])) {
                $service['max_slots'] = $service['capacity'];
            }
            return $service;
        })->toArray();
        
        $mitraProfile->custom_services = $services;
        $mitraProfile->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil disimpan',
            'data' => $mitraProfile->custom_services
        ]);
    }
}
