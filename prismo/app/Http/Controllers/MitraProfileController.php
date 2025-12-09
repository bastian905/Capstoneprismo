<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MitraProfile;
use Illuminate\Support\Facades\Storage;

class MitraProfileController extends Controller
{
    public function showForm()
    {
        $user = Auth::user();
        
        // Jika bukan mitra, redirect ke dashboard sesuai role
        if ($user->role !== 'mitra') {
            return redirect('/dashboard');
        }
        
        // Jika sudah lengkapi profil dan approved, redirect ke dashboard
        if ($user->profile_completed && $user->approval_status === 'approved') {
            return redirect('/dashboard-mitra');
        }
        
        // Jika status rejected, pending, atau profile_completed tanpa approval, 
        // biarkan user mengisi ulang form dengan reset data lama
        if ($user->profile_completed || $user->approval_status === 'rejected' || $user->approval_status === 'pending') {
            // Hapus profil lama jika ada
            if ($user->mitraProfile) {
                // Hapus file lama dari storage
                if ($user->mitraProfile->facility_photos) {
                    $facilityPhotos = is_array($user->mitraProfile->facility_photos) 
                        ? $user->mitraProfile->facility_photos 
                        : json_decode((string)$user->mitraProfile->facility_photos, true);
                    if (is_array($facilityPhotos)) {
                        foreach ($facilityPhotos as $photo) {
                            Storage::disk('public')->delete($photo);
                        }
                    }
                }
                
                if ($user->mitraProfile->legal_doc) {
                    Storage::disk('public')->delete($user->mitraProfile->legal_doc);
                }
                
                if ($user->mitraProfile->ktp_photo) {
                    Storage::disk('public')->delete($user->mitraProfile->ktp_photo);
                }
                
                if ($user->mitraProfile->qris_photo) {
                    Storage::disk('public')->delete($user->mitraProfile->qris_photo);
                }
                
                // Hapus profil dari database
                $user->mitraProfile->delete();
            }
            
            // Reset user status agar bisa mengisi form dari awal
            $user->profile_completed = false;
            $user->approval_status = 'pending';
            $user->save();
        }
        
        return view('mitra.form-mitra');
    }
    
    public function submitForm(Request $request)
    {
        $validated = $request->validate([
            'businessName' => 'required|string|max:255',
            'establishmentYear' => 'required|integer|min:1900|max:2025',
            'address' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'postalCode' => 'required|string',
            'mapLocation' => 'required|url',
            'contactPerson' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'facilityPhotos' => 'required|array|min:1|max:5',
            'facilityPhotos.*' => 'required|string', // Base64 images
            'legalDoc' => 'required|string', // Base64 PDF
            'ktpPhoto' => 'required|string', // Base64 image
            'qrisPhoto' => 'required|string', // Base64 image
        ]);
        
        $user = Auth::user();
        
        try {
            // Upload facility photos
            $facilityPaths = [];
            foreach ($request->facilityPhotos as $index => $base64Image) {
                $image = str_replace('data:image/png;base64,', '', $base64Image);
                $image = str_replace('data:image/jpeg;base64,', '', $image);
                $image = str_replace('data:image/jpg;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = 'facility_' . $user->id . '_' . $index . '_' . time() . '.jpg';
                Storage::disk('public')->put('mitra/facilities/' . $imageName, base64_decode($image));
                $facilityPaths[] = 'mitra/facilities/' . $imageName;
            }
            
            // Upload legal document
            $legalDoc = str_replace('data:application/pdf;base64,', '', $request->legalDoc);
            $legalDoc = str_replace(' ', '+', $legalDoc);
            $legalDocName = 'legal_' . $user->id . '_' . time() . '.pdf';
            Storage::disk('public')->put('mitra/documents/' . $legalDocName, base64_decode($legalDoc));
            
            // Upload KTP photo
            $ktpPhoto = str_replace('data:image/png;base64,', '', $request->ktpPhoto);
            $ktpPhoto = str_replace('data:image/jpeg;base64,', '', $ktpPhoto);
            $ktpPhoto = str_replace('data:image/jpg;base64,', '', $ktpPhoto);
            $ktpPhoto = str_replace(' ', '+', $ktpPhoto);
            $ktpPhotoName = 'ktp_' . $user->id . '_' . time() . '.jpg';
            Storage::disk('public')->put('mitra/documents/' . $ktpPhotoName, base64_decode($ktpPhoto));
            
            // Upload QRIS photo
            $qrisPhoto = str_replace('data:image/png;base64,', '', $request->qrisPhoto);
            $qrisPhoto = str_replace('data:image/jpeg;base64,', '', $qrisPhoto);
            $qrisPhoto = str_replace('data:image/jpg;base64,', '', $qrisPhoto);
            $qrisPhoto = str_replace(' ', '+', $qrisPhoto);
            $qrisPhotoName = 'qris_' . $user->id . '_' . time() . '.jpg';
            Storage::disk('public')->put('mitra/documents/' . $qrisPhotoName, base64_decode($qrisPhoto));
            
            // Create or update mitra profile
            MitraProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'business_name' => $request->businessName,
                    'establishment_year' => $request->establishmentYear,
                    'contact_person' => $request->contactPerson,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'city' => $request->city,
                    'province' => $request->province,
                    'postal_code' => $request->postalCode,
                    'map_location' => $request->mapLocation,
                    'facility_photos' => json_encode($facilityPaths),
                    'legal_doc' => 'mitra/documents/' . $legalDocName,
                    'ktp_photo' => 'mitra/documents/' . $ktpPhotoName,
                    'qris_photo' => 'mitra/documents/' . $qrisPhotoName,
                ]
            );
            
            // Update user status
            $user->profile_completed = true;
            $user->approval_status = 'pending';
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil dilengkapi! Menunggu verifikasi admin.',
                'redirect' => '/mitra/form-mitra-pending'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}

