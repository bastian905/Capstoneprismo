<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class KelolaMitraController extends Controller
{
    public function index()
    {
        $mitras = User::where('role', 'mitra')
            ->with('mitraProfile')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($mitra) {
                return [
                    'id' => $mitra->id,
                    'name' => $mitra->name,
                    'email' => $mitra->email,
                    'phone' => optional($mitra->mitraProfile)->phone ?? '-',  // Ambil dari mitraProfile
                    'business_name' => optional($mitra->mitraProfile)->business_name ?? '-',
                    'address' => optional($mitra->mitraProfile)->address ?? '-',
                    'city' => optional($mitra->mitraProfile)->city ?? '-',
                    'approval_status' => $mitra->approval_status,
                    'rating' => optional($mitra->mitraProfile)->rating ?? 0,
                    'created_at' => $mitra->created_at->format('Y-m-d')
                ];
            })
            ->values() // Convert to array with sequential keys
            ->toArray(); // Convert Collection to array
        
        \Log::info('Kelola Mitra - Total mitras: ' . count($mitras));
        \Log::info('Kelola Mitra - Data: ' . json_encode($mitras));
        
        return view('admin.kelolamitra.kelolamitra', compact('mitras'));
    }

    public function show($id)
    {
        $mitra = User::where('role', 'mitra')
            ->with('mitraProfile')
            ->findOrFail($id);
        
        return view('admin.kelolamitra.form', compact('mitra'));
    }

    public function approve($id)
    {
        $mitra = User::findOrFail($id);
        $mitra->approval_status = 'approved';
        $mitra->save();

        // Send notification to mitra
        NotificationService::mitraApproved($mitra);

        return response()->json(['success' => true, 'message' => 'Mitra berhasil disetujui']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'required|string|min:10'
        ]);
        
        $mitra = User::findOrFail($id);
        $mitra->approval_status = 'rejected';
        $mitra->save();
        
        // Simpan alasan reject ke mitra profile atau tabel terpisah
        if ($mitra->mitraProfile) {
            $mitra->mitraProfile->reject_reason = $request->reject_reason;
            $mitra->mitraProfile->save();
        }
        
        // Send in-app notification
        NotificationService::mitraRejected($mitra, $request->reject_reason);
        
        // Kirim email notifikasi
        try {
            \Mail::to($mitra->email)->send(new \App\Mail\MitraRejectedMail($mitra, $request->reject_reason));
        } catch (\Exception $e) {
            \Log::error('Failed to send rejection email: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Mitra berhasil ditolak dan email notifikasi telah dikirim']);
    }
}
