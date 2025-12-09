<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EarningsReportController extends Controller
{
    public function exportEarnings(Request $request)
    {
        $period = $request->get('period', 'monthly'); // daily, weekly, monthly, yearly
        $date = $request->get('date', now()->toDateString());
        
        $data = $this->calculateEarningsData($period, $date);
        
        // Generate CSV
        $filename = "laporan-pendapatan-mitra-{$period}-" . Carbon::parse($date)->format('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($data, $period) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, ['LAPORAN PENDAPATAN MITRA - ' . strtoupper($period)]);
            fputcsv($file, ['Tanggal Export: ' . now()->translatedFormat('d F Y H:i')]);
            fputcsv($file, []);
            
            // Column headers
            fputcsv($file, ['Nama Mitra', 'Total Pendapatan', 'Total Booking', 'Rating', 'Status']);
            
            // Data rows
            foreach ($data['mitras'] as $mitra) {
                fputcsv($file, [
                    $mitra['name'],
                    'Rp ' . number_format($mitra['total_earnings'], 0, ',', '.'),
                    $mitra['total_bookings'],
                    number_format($mitra['rating'], 1),
                    $mitra['status']
                ]);
            }
            
            // Summary
            fputcsv($file, []);
            fputcsv($file, ['RINGKASAN']);
            fputcsv($file, ['Total Mitra', count($data['mitras'])]);
            fputcsv($file, ['Total Pendapatan Keseluruhan', 'Rp ' . number_format($data['total_all_earnings'], 0, ',', '.')]);
            fputcsv($file, ['Total Booking Keseluruhan', $data['total_all_bookings']]);
            fputcsv($file, ['Rata-rata Rating', number_format($data['average_rating'], 2)]);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    private function calculateEarningsData($period, $date)
    {
        $targetDate = Carbon::parse($date);
        
        // Determine date range based on period
        switch ($period) {
            case 'daily':
                $startDate = $targetDate->copy()->startOfDay();
                $endDate = $targetDate->copy()->endOfDay();
                break;
                
            case 'weekly':
                $startDate = $targetDate->copy()->startOfWeek();
                $endDate = $targetDate->copy()->endOfWeek();
                break;
                
            case 'monthly':
                $startDate = $targetDate->copy()->startOfMonth();
                $endDate = $targetDate->copy()->endOfMonth();
                break;
                
            case 'yearly':
                $startDate = $targetDate->copy()->startOfYear();
                $endDate = $targetDate->copy()->endOfYear();
                break;
                
            default:
                $startDate = $targetDate->copy()->startOfMonth();
                $endDate = $targetDate->copy()->endOfMonth();
        }
        
        // Get all mitras with their earnings
        $mitras = User::where('role', 'mitra')
            ->where('approval_status', 'approved')
            ->with('mitraProfile')
            ->get()
            ->map(function($mitra) use ($startDate, $endDate) {
                $bookings = Booking::where('mitra_id', $mitra->id)
                    ->where('status', 'selesai')
                    ->whereNotNull('completed_at')
                    ->whereBetween('completed_at', [$startDate, $endDate])
                    ->get();
                
                return [
                    'name' => optional($mitra->mitraProfile)->business_name ?: $mitra->name,
                    'total_earnings' => $bookings->sum('final_price'),
                    'total_bookings' => $bookings->count(),
                    'rating' => floatval(optional($mitra->mitraProfile)->rating) ?: 0,
                    'status' => optional($mitra->mitraProfile)->status === 'buka' ? 'Aktif' : 'Nonaktif'
                ];
            })
            ->sortByDesc('total_earnings')
            ->values()
            ->toArray();
        
        $totalAllEarnings = collect($mitras)->sum('total_earnings');
        $totalAllBookings = collect($mitras)->sum('total_bookings');
        $averageRating = collect($mitras)->where('rating', '>', 0)->avg('rating') ?: 0;
        
        return [
            'mitras' => $mitras,
            'total_all_earnings' => $totalAllEarnings,
            'total_all_bookings' => $totalAllBookings,
            'average_rating' => $averageRating,
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
    }
}
