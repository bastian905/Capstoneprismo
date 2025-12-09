<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MitraProfile;
use App\Models\Withdrawal;
use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function getBalanceInfo(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'mitra') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Total earnings from completed bookings
        $totalEarnings = Booking::where('mitra_id', $user->id)
            ->where('status', 'selesai')
            ->sum('final_price');

        // Total withdrawn
        $totalWithdrawn = Withdrawal::where('mitra_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        // Available balance
        $availableBalance = $totalEarnings - $totalWithdrawn;

        // Today's earnings
        $todayEarnings = Booking::where('mitra_id', $user->id)
            ->where('status', 'selesai')
            ->whereDate('completed_at', now()->toDateString())
            ->sum('final_price');

        // Check withdrawal eligibility
        $currentHour = now()->format('H:i');
        $isOperationalHours = $currentHour >= '08:00' && $currentHour < '23:00';

        $hasWithdrawnToday = Withdrawal::where('mitra_id', $user->id)
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        $hasProcessingWithdrawal = Withdrawal::where('mitra_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        $canWithdraw = $isOperationalHours && 
                       !$hasWithdrawnToday && 
                       !$hasProcessingWithdrawal && 
                       $availableBalance >= 50000;

        $withdrawalMessage = '';
        if (!$isOperationalHours) {
            $withdrawalMessage = 'Penarikan hanya dapat dilakukan pada jam 08:00 - 23:00';
        } elseif ($hasWithdrawnToday) {
            $withdrawalMessage = 'Anda sudah melakukan penarikan hari ini';
        } elseif ($hasProcessingWithdrawal) {
            $withdrawalMessage = 'Anda masih memiliki penarikan yang sedang diproses';
        } elseif ($availableBalance < 50000) {
            $withdrawalMessage = 'Saldo minimal untuk penarikan adalah Rp 50.000';
        }

        return response()->json([
            'availableBalance' => $availableBalance,
            'totalEarnings' => $totalEarnings,
            'totalWithdrawn' => $totalWithdrawn,
            'todayEarnings' => $todayEarnings,
            'canWithdraw' => $canWithdraw,
            'withdrawalMessage' => $withdrawalMessage,
            'minWithdrawal' => 50000,
            'operationalHours' => '08:00 - 23:00',
        ]);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $query = Withdrawal::with(['mitra', 'processedBy']);

        if ($user->role === 'mitra') {
            $query->where('mitra_id', $user->id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->orderBy('created_at', 'desc')->get();

        return response()->json($withdrawals);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'mitra') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validasi jam operasional: 08:00 - 23:00
        $currentHour = now()->format('H:i');
        if ($currentHour < '08:00' || $currentHour >= '23:00') {
            return response()->json([
                'message' => 'Penarikan saldo hanya dapat dilakukan pada jam 08:00 - 23:00'
            ], 400);
        }

        // Validasi maksimal 1x penarikan per hari
        $todayWithdrawal = Withdrawal::where('mitra_id', $user->id)
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if ($todayWithdrawal) {
            return response()->json([
                'message' => 'Anda sudah melakukan penarikan hari ini. Silakan coba lagi besok setelah jam 00:00'
            ], 400);
        }

        // Validasi jika ada penarikan yang masih diproses
        $pendingWithdrawal = Withdrawal::where('mitra_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($pendingWithdrawal) {
            return response()->json([
                'message' => 'Anda masih memiliki penarikan yang sedang diproses. Harap tunggu hingga selesai.'
            ], 400);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:50000',
            'bank_name' => 'required_without:qris_image|string',
            'account_number' => 'required_without:qris_image|string',
            'account_name' => 'required_without:qris_image|string',
            'qris_image' => 'required_without:bank_name|image|max:2048',
        ], [
            'amount.min' => 'Minimal penarikan adalah Rp 50.000'
        ]);

        // Hitung saldo tersedia dari booking selesai dikurangi total penarikan completed
        $totalEarnings = \App\Models\Booking::where('mitra_id', $user->id)
            ->where('status', 'selesai')
            ->sum('final_price');

        $totalWithdrawn = Withdrawal::where('mitra_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        $availableBalance = $totalEarnings - $totalWithdrawn;

        // Validasi saldo tersedia tidak boleh minus
        if ($availableBalance < $validated['amount']) {
            return response()->json([
                'message' => 'Saldo tidak mencukupi. Saldo tersedia: Rp ' . number_format($availableBalance, 0, ',', '.')
            ], 400);
        }

        // Upload QRIS if provided
        if ($request->hasFile('qris_image')) {
            $validated['qris_image'] = $request->file('qris_image')->store('withdrawal-qris', 'public');
        }

        $validated['mitra_id'] = $user->id;
        $validated['status'] = 'pending';

        $withdrawal = Withdrawal::create($validated);
        $withdrawal->load(['mitra']);

        // Notify admins about new withdrawal request
        NotificationService::newWithdrawalRequest($withdrawal);

        return response()->json([
            'message' => 'Penarikan berhasil diajukan. Waktu proses: 1-3 hari kerja',
            'data' => $withdrawal
        ], 201);
    }

    public function show($id)
    {
        $withdrawal = Withdrawal::with(['mitra', 'processedBy'])->findOrFail($id);
        return response()->json($withdrawal);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $withdrawal = Withdrawal::findOrFail($id);

        // Only mitra who owns the withdrawal can update if status is pending
        if ($user->role === 'mitra') {
            if ($withdrawal->mitra_id !== $user->id || $withdrawal->status !== 'pending') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'bank_name' => 'sometimes|string',
                'account_number' => 'sometimes|string',
                'account_name' => 'sometimes|string',
            ]);

            $withdrawal->update($validated);
        }

        return response()->json($withdrawal);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $withdrawal = Withdrawal::findOrFail($id);

        // Only mitra can delete their own pending withdrawal
        if ($user->role !== 'mitra' || $withdrawal->mitra_id !== $user->id || $withdrawal->status !== 'pending') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $withdrawal->delete();

        return response()->json(['message' => 'Penarikan berhasil dibatalkan']);
    }

    public function approve(Request $request, $id)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return response()->json(['message' => 'Penarikan sudah diproses'], 400);
        }

        $validated = $request->validate([
            'admin_note' => 'nullable|string',
        ]);

        $withdrawal->update([
            'status' => 'approved',
            'admin_note' => $validated['admin_note'] ?? null,
            'processed_at' => now(),
            'processed_by' => $request->user()->id,
        ]);

        // Send notification to mitra
        NotificationService::withdrawalApproved($withdrawal);

        return response()->json($withdrawal);
    }

    public function reject(Request $request, $id)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return response()->json(['message' => 'Penarikan sudah diproses'], 400);
        }

        $validated = $request->validate([
            'admin_note' => 'required|string',
        ]);

        $withdrawal->update([
            'status' => 'rejected',
            'admin_note' => $validated['admin_note'],
            'processed_at' => now(),
            'processed_by' => $request->user()->id,
        ]);

        // Send notification to mitra
        NotificationService::withdrawalRejected($withdrawal);

        return response()->json($withdrawal);
    }

    public function complete(Request $request, $id)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        // Allow completing from pending or approved status
        if (!in_array($withdrawal->status, ['pending', 'approved'])) {
            return response()->json(['message' => 'Penarikan sudah diproses atau ditolak'], 400);
        }

        // Hitung saldo tersedia
        $totalEarnings = \App\Models\Booking::where('mitra_id', $withdrawal->mitra_id)
            ->where('status', 'selesai')
            ->sum('final_price');

        $totalWithdrawn = Withdrawal::where('mitra_id', $withdrawal->mitra_id)
            ->where('status', 'completed')
            ->sum('amount');

        $availableBalance = $totalEarnings - $totalWithdrawn;

        // Validasi saldo tidak boleh minus
        if ($availableBalance < $withdrawal->amount) {
            return response()->json([
                'message' => 'Saldo mitra tidak mencukupi. Saldo tersedia: Rp ' . number_format($availableBalance, 0, ',', '.')
            ], 400);
        }

        $withdrawal->update([
            'status' => 'completed',
            'processed_at' => now(),
            'processed_by' => $request->user()->id ?? null,
        ]);

        // Send notification to mitra
        NotificationService::withdrawalCompleted($withdrawal);

        return response()->json([
            'message' => 'Penarikan berhasil diselesaikan',
            'data' => $withdrawal
        ]);
    }
}
