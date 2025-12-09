<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class FinancialReportController extends Controller
{
    public function getReportData(Request $request)
    {
        $mitra = Auth::user();
        $period = $request->get('period', 'daily'); // daily, weekly, monthly, yearly
        $date = $request->get('date', now()->toDateString());
        
        $data = $this->calculateReportData($mitra, $period, $date);
        
        return response()->json($data);
    }
    
    public function exportPDF(Request $request)
    {
        $mitra = Auth::user();
        $period = $request->get('period', 'daily');
        $date = $request->get('date', now()->toDateString());
        
        $data = $this->calculateReportData($mitra, $period, $date);
        $data['mitra'] = $mitra;
        $data['period'] = $period;
        $data['date'] = $date;
        $data['periodLabel'] = $this->getPeriodLabel($period, $date);
        
        $pdf = PDF::loadView('mitra.reports.pdf', $data);
        $filename = "laporan-keuangan-{$period}-" . Carbon::parse($date)->format('Y-m-d') . ".pdf";
        
        return $pdf->download($filename);
    }
    
    public function exportExcel(Request $request)
    {
        $mitra = Auth::user();
        $period = $request->get('period', 'daily');
        $date = $request->get('date', now()->toDateString());
        
        $data = $this->calculateReportData($mitra, $period, $date);
        
        // Generate CSV (simpler than Excel for now)
        $filename = "laporan-keuangan-{$period}-" . Carbon::parse($date)->format('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($data, $period) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            if ($period === 'daily') {
                fputcsv($file, ['Jenis Layanan', 'Customer', 'Tanggal', 'Total']);
                foreach ($data['transactions'] as $transaction) {
                    fputcsv($file, [
                        $transaction['service'],
                        $transaction['customerName'],
                        $transaction['date'],
                        $transaction['amount']
                    ]);
                }
            } else {
                fputcsv($file, ['Periode', 'Total Transaksi', 'Total Pendapatan']);
                foreach ($data['transactions'] as $transaction) {
                    fputcsv($file, [
                        $transaction['period'],
                        $transaction['count'],
                        $transaction['income']
                    ]);
                }
            }
            
            // Summary
            fputcsv($file, []);
            fputcsv($file, ['Total Transaksi', $data['totalTransactions']]);
            fputcsv($file, ['Total Pendapatan', 'Rp ' . number_format($data['totalIncome'], 0, ',', '.')]);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    private function calculateReportData($mitra, $period, $date)
    {
        $baseQuery = Booking::where('mitra_id', $mitra->id)
            ->where('status', 'selesai')
            ->whereNotNull('completed_at')
            ->with('customer');
        
        switch ($period) {
            case 'daily':
                $targetDate = Carbon::parse($date);
                $bookings = $baseQuery
                    ->whereDate('completed_at', $targetDate)
                    ->orderBy('completed_at', 'desc')
                    ->get();
                
                $transactions = $bookings->map(function($booking) {
                    return [
                        'id' => $booking->booking_code,
                        'service' => $booking->service_type,
                        'customerName' => $booking->customer->name ?? 'Unknown',
                        'date' => $booking->completed_at->format('d M Y H:i'),
                        'amount' => $booking->final_price
                    ];
                })->toArray();
                
                break;
                
            case 'weekly':
                $targetDate = Carbon::parse($date);
                $startOfWeek = $targetDate->copy()->startOfWeek();
                
                // Get last 4 weeks
                $transactions = [];
                for ($i = 0; $i < 4; $i++) {
                    $weekStart = $startOfWeek->copy()->subWeeks($i);
                    $weekEnd = $weekStart->copy()->endOfWeek();
                    
                    $weekBookings = Booking::where('mitra_id', $mitra->id)
                        ->where('status', 'selesai')
                        ->whereNotNull('completed_at')
                        ->whereBetween('completed_at', [$weekStart, $weekEnd])
                        ->get();
                    
                    $transactions[] = [
                        'period' => 'Minggu ' . $weekStart->weekOfMonth . ' ' . $weekStart->format('M Y'),
                        'count' => $weekBookings->count(),
                        'income' => $weekBookings->sum('final_price')
                    ];
                }
                
                break;
                
            case 'monthly':
                $targetDate = Carbon::parse($date);
                
                // Get last 3 months
                $transactions = [];
                for ($i = 0; $i < 3; $i++) {
                    $monthStart = $targetDate->copy()->subMonths($i)->startOfMonth();
                    $monthEnd = $monthStart->copy()->endOfMonth();
                    
                    $monthBookings = Booking::where('mitra_id', $mitra->id)
                        ->where('status', 'selesai')
                        ->whereNotNull('completed_at')
                        ->whereBetween('completed_at', [$monthStart, $monthEnd])
                        ->get();
                    
                    $transactions[] = [
                        'period' => $monthStart->translatedFormat('F Y'),
                        'count' => $monthBookings->count(),
                        'income' => $monthBookings->sum('final_price')
                    ];
                }
                
                break;
                
            case 'yearly':
                $targetDate = Carbon::parse($date);
                
                // Get last 3 years
                $transactions = [];
                for ($i = 0; $i < 3; $i++) {
                    $yearStart = $targetDate->copy()->subYears($i)->startOfYear();
                    $yearEnd = $yearStart->copy()->endOfYear();
                    
                    $yearBookings = Booking::where('mitra_id', $mitra->id)
                        ->where('status', 'selesai')
                        ->whereNotNull('completed_at')
                        ->whereBetween('completed_at', [$yearStart, $yearEnd])
                        ->get();
                    
                    $transactions[] = [
                        'period' => $yearStart->format('Y'),
                        'count' => $yearBookings->count(),
                        'income' => $yearBookings->sum('final_price')
                    ];
                }
                
                break;
        }
        
        // Calculate totals based on period
        if ($period === 'daily') {
            $totalTransactions = count($transactions);
            $totalIncome = collect($transactions)->sum('amount');
        } else {
            $totalTransactions = collect($transactions)->sum('count');
            $totalIncome = collect($transactions)->sum('income');
        }
        
        return [
            'transactions' => $transactions,
            'totalTransactions' => $totalTransactions,
            'totalIncome' => $totalIncome
        ];
    }
    
    private function getPeriodLabel($period, $date)
    {
        $targetDate = Carbon::parse($date);
        
        switch ($period) {
            case 'daily':
                return $targetDate->translatedFormat('d F Y');
            case 'weekly':
                return 'Minggu ' . $targetDate->weekOfMonth . ' ' . $targetDate->translatedFormat('F Y');
            case 'monthly':
                return $targetDate->translatedFormat('F Y');
            case 'yearly':
                return $targetDate->format('Y');
            default:
                return $date;
        }
    }
}
