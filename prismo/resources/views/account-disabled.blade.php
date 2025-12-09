<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Dinonaktifkan - Prismo</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 15px;
            font-weight: 700;
        }

        p {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #ff6b6b;
            padding: 20px;
            border-radius: 8px;
            text-align: left;
            margin-bottom: 30px;
        }

        .info-box h3 {
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .info-box ul {
            list-style: none;
            padding-left: 0;
        }

        .info-box li {
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .info-box li:before {
            content: "•";
            color: #ff6b6b;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .btn {
            display: inline-block;
            padding: 14px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .contact-info {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e9ecef;
        }

        .contact-info p {
            font-size: 14px;
            color: #888;
            margin-bottom: 10px;
        }

        .contact-info a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .container {
                padding: 40px 25px;
            }

            h1 {
                font-size: 24px;
            }

            p {
                font-size: 14px;
            }

            .icon {
                width: 80px;
                height: 80px;
                font-size: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🚫</div>
        
        <h1>Akun Anda Dinonaktifkan</h1>
        
        <p>Maaf, akun Anda telah dinonaktifkan oleh administrator. Anda tidak dapat mengakses layanan Prismo saat ini.</p>
        
        <div class="info-box">
            <h3>Kemungkinan Alasan:</h3>
            <ul>
                <li>Pelanggaran ketentuan layanan</li>
                <li>Aktivitas mencurigakan terdeteksi</li>
                <li>Permintaan penonaktifan dari pihak berwenang</li>
                <li>Tindakan administratif lainnya</li>
            </ul>
        </div>
        
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn">Logout</button>
        </form>
        
        <div class="contact-info">
            <p>Butuh bantuan atau ingin mengajukan banding?</p>
            <p>Hubungi kami: <a href="mailto:support@prismo.com">support@prismo.com</a></p>
        </div>
    </div>
</body>
</html>
