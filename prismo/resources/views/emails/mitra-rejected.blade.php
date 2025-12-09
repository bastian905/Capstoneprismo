<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Mitra Ditolak</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f6f8;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
        }
        .logo {
            font-size: 32px;
            font-weight: 700;
            color: #2ea0ff;
            margin-bottom: 10px;
        }
        .icon-warning {
            font-size: 48px;
            color: #ff9800;
            margin: 20px 0;
        }
        h1 {
            color: #222;
            font-size: 24px;
            margin: 20px 0 10px;
        }
        .reason-box {
            background-color: #fff3cd;
            border-left: 4px solid #ff9800;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
        }
        .reason-label {
            font-weight: 700;
            color: #856404;
            margin-bottom: 8px;
        }
        .reason-text {
            color: #856404;
            line-height: 1.6;
        }
        .info-text {
            color: #666;
            margin: 20px 0;
            line-height: 1.8;
        }
        .btn-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 14px 32px;
            background-color: #2ea0ff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #1e90ff;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
            color: #999;
            font-size: 14px;
        }
        .footer a {
            color: #2ea0ff;
            text-decoration: none;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .email-container {
                padding: 20px;
            }
            h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">PRISMO</div>
            <div class="icon-warning">⚠️</div>
            <h1>Pendaftaran Mitra Ditolak</h1>
        </div>

        <p class="info-text">
            Halo <strong>{{ $mitra->name }}</strong>,
        </p>

        <p class="info-text">
            Terima kasih atas minat Anda untuk menjadi mitra Prismo. Setelah kami meninjau formulir pendaftaran Anda, dengan berat hati kami informasikan bahwa pendaftaran Anda <strong>belum dapat disetujui</strong> saat ini.
        </p>

        <div class="reason-box">
            <div class="reason-label">Alasan Penolakan:</div>
            <div class="reason-text">{{ $rejectReason }}</div>
        </div>

        <p class="info-text">
            Anda dapat <strong>mengisi formulir pendaftaran ulang</strong> dengan memperbaiki informasi sesuai dengan catatan di atas. Kami berharap dapat bekerja sama dengan Anda di masa mendatang.
        </p>

        <div class="btn-container">
            <a href="{{ url('/mitra/form-mitra') }}" class="btn">Isi Formulir Ulang</a>
        </div>

        <p class="info-text" style="font-size: 14px; color: #999;">
            Jika Anda memiliki pertanyaan atau memerlukan bantuan, silakan hubungi tim support kami.
        </p>

        <div class="footer">
            <p>
                Email ini dikirim otomatis oleh sistem Prismo.<br>
                Untuk informasi lebih lanjut, kunjungi <a href="{{ url('/') }}">website kami</a>.
            </p>
            <p style="margin-top: 10px;">
                &copy; {{ date('Y') }} Prismo. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
