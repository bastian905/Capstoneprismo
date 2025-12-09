<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peringatan Penghapusan Akun</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 30px;
            color: #333333;
            line-height: 1.6;
        }
        .email-body h2 {
            color: #667eea;
            margin-top: 0;
        }
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            opacity: 0.9;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666666;
            font-size: 12px;
        }
        .timeline {
            margin: 20px 0;
            padding-left: 20px;
        }
        .timeline li {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>⚠️ Peringatan Penghapusan Akun</h1>
        </div>
        
        <div class="email-body">
            <h2>Halo, {{ $userName }}</h2>
            
            <p>Kami mengirimkan email ini untuk memberitahukan bahwa akun <strong>{{ ucfirst($userRole) }}</strong> Anda di PRISMO akan dihapus secara otomatis dalam <strong>30 hari</strong>.</p>
            
            <div class="warning-box">
                <strong>⏰ Akun Anda tidak aktif selama lebih dari 2 tahun 11 bulan</strong>
                <p style="margin-bottom: 0;">Berdasarkan kebijakan kami, akun yang tidak aktif selama lebih dari 3 tahun akan dihapus secara otomatis untuk menjaga keamanan dan privasi data.</p>
            </div>
            
            <div class="info-box">
                <strong>📌 Apa yang akan terjadi jika akun dihapus?</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Semua data pribadi Anda akan dihapus secara permanen</li>
                    <li>Riwayat booking dan transaksi akan dihapus</li>
                    @if($userRole === 'mitra')
                    <li>Dokumen usaha dan legalitas akan dihapus</li>
                    <li>Data keuangan dan saldo akan dihapus</li>
                    @endif
                    @if($userRole === 'customer')
                    <li>Ulasan dan feedback Anda akan dihapus</li>
                    @endif
                    <li>Akun tidak dapat dipulihkan setelah dihapus</li>
                </ul>
            </div>
            
            <h3>🛡️ Cara Mencegah Penghapusan Akun</h3>
            <p>Untuk mempertahankan akun Anda, cukup login ke sistem kami:</p>
            
            <div style="text-align: center;">
                <a href="{{ url('/login') }}" class="button">Login Sekarang</a>
            </div>
            
            <p style="margin-top: 20px;">Atau salin link berikut ke browser Anda:<br>
            <a href="{{ url('/login') }}" style="color: #667eea; word-break: break-all;">{{ url('/login') }}</a></p>
            
            <div class="info-box" style="margin-top: 30px;">
                <strong>📧 Detail Akun Anda:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li><strong>Email:</strong> {{ $userEmail }}</li>
                    <li><strong>Tipe Akun:</strong> {{ ucfirst($userRole) }}</li>
                    <li><strong>Batas Waktu:</strong> 30 hari dari hari ini</li>
                </ul>
            </div>
            
            <p style="margin-top: 30px; color: #666;">Jika Anda memiliki pertanyaan atau memerlukan bantuan, silakan hubungi tim support kami.</p>
            
            <p style="margin-top: 20px;">Terima kasih,<br><strong>Tim PRISMO</strong></p>
        </div>
        
        <div class="email-footer">
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} PRISMO. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
