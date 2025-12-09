<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - {{ $periodLabel }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #667eea;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 16px;
            color: #555;
            font-weight: normal;
        }
        .info-section {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        .info-value {
            flex: 1;
        }
        .summary {
            margin: 20px 0;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 5px;
            text-align: center;
        }
        .summary-row {
            display: flex;
            justify-content: space-around;
            margin-top: 10px;
        }
        .summary-item h3 {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .summary-item p {
            font-size: 18px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #667eea;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .amount {
            text-align: right;
            font-weight: bold;
            color: #28a745;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PRISMO</h1>
        <h2>Laporan Keuangan {{ ucfirst($period) }}</h2>
        <p>{{ $periodLabel }}</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Nama Mitra:</span>
            <span class="info-value">{{ $mitra->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Bisnis:</span>
            <span class="info-value">{{ $mitra->business_name ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal Cetak:</span>
            <span class="info-value">{{ now()->translatedFormat('d F Y H:i') }}</span>
        </div>
    </div>

    <div class="summary">
        <h3>Ringkasan</h3>
        <div class="summary-row">
            <div class="summary-item">
                <h3>Total Transaksi</h3>
                <p>{{ $totalTransactions }}</p>
            </div>
            <div class="summary-item">
                <h3>Total Pendapatan</h3>
                <p>Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                @if($period === 'daily')
                    <th>Jenis Layanan</th>
                    <th>Customer</th>
                    <th>Tanggal</th>
                    <th style="text-align: right;">Total</th>
                @else
                    <th>Periode</th>
                    <th>Total Transaksi</th>
                    <th style="text-align: right;">Total Pendapatan</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    @if($period === 'daily')
                        <td>{{ $transaction['service'] }}</td>
                        <td>{{ $transaction['customerName'] }}</td>
                        <td>{{ $transaction['date'] }}</td>
                        <td class="amount">Rp {{ number_format($transaction['amount'], 0, ',', '.') }}</td>
                    @else
                        <td>{{ $transaction['period'] }}</td>
                        <td>{{ $transaction['count'] }}</td>
                        <td class="amount">Rp {{ number_format($transaction['income'], 0, ',', '.') }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $period === 'daily' ? 4 : 3 }}" style="text-align: center; padding: 20px; color: #999;">
                        Tidak ada data untuk periode ini
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini digenerate otomatis oleh sistem PRISMO</p>
        <p>© {{ now()->year }} PRISMO - Platform Reservasi Pencucian Mobil</p>
    </div>
</body>
</html>
