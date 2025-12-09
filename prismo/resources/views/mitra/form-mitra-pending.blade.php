<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Data</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: #f5f5f5;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 35px 28px 40px;
            border-radius: 18px;
            box-shadow: 0 16px 20px rgba(0,0,0,0.08);
            max-width: 420px;
            width: 100%;
            text-align: center;
            transition: all 0.3s ease;
        }

        .icon-container {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: #8BB7F0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            transition: all 0.3s ease;
        }

        .card img {
            display: block;
            width: 80px;
            height: auto;
            transition: all 0.3s ease;
        }

        h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #333;
            font-size: 22px;
            transition: all 0.3s ease;
        }

        p {
            color: #666;
            line-height: 1.5;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        button {
            margin-top: 25px;
            background: #ff6b6b;
            padding: 14px;
            border-radius: 10px;
            border: none;
            color: white;
            font-weight: bold;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
        }
        
        button:hover { 
            background: #ff5252; 
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 107, 107, 0.3);
        }

        /* Tablet Styles */
        @media (max-width: 768px) {
            .card {
                padding: 30px 24px 35px;
                max-width: 380px;
            }
            
            .icon-container {
                width: 120px;
                height: 120px;
            }
            
            .card img {
                width: 70px;
            }
            
            h3 {
                font-size: 20px;
            }
            
            p {
                font-size: 15px;
            }
        }

        /* Mobile Styles */
        @media (max-width: 480px) {
            body {
                padding: 15px;
            }
            
            .card {
                padding: 25px 20px 30px;
                max-width: 100%;
                border-radius: 14px;
            }
            
            .icon-container {
                width: 100px;
                height: 100px;
                margin-bottom: 15px;
            }
            
            .card img {
                width: 60px;
            }
            
            h3 {
                font-size: 18px;
                margin-bottom: 12px;
            }
            
            p {
                font-size: 14px;
                margin-bottom: 12px;
            }
            
            button {
                padding: 12px;
                font-size: 15px;
                margin-top: 20px;
            }
        }

        /* Small Mobile Styles */
        @media (max-width: 360px) {
            body {
                padding: 10px;
            }
            
            .card {
                padding: 20px 16px 25px;
            }
            
            .icon-container {
                width: 90px;
                height: 90px;
            }
            
            .card img {
                width: 50px;
            }
            
            h3 {
                font-size: 17px;
            }
            
            p {
                font-size: 13px;
            }
            
            button {
                padding: 11px;
                font-size: 14px;
            }
        }

        /* Large Screen Styles */
        @media (min-width: 1200px) {
            .card {
                max-width: 450px;
                padding: 40px 32px 45px;
            }
            
            .icon-container {
                width: 160px;
                height: 160px;
            }
            
            .card img {
                width: 90px;
            }
            
            h3 {
                font-size: 24px;
            }
            
            p {
                font-size: 17px;
            }
        }
    </style>
</head>
<body>

<div class="card">
    <div class="icon-container">
        <img src="{{ asset('images/jam-pasir.png') }}" alt="gambar jam pasir">
    </div>

    <h3>Data Toko Anda Sedang Diproses</h3>
    <p>Kami sedang memverifikasi data toko yang Anda kirim. Harap tunggu.</p>
    <p>Proses verifikasi membutuhkan waktu maksimal <b>1×24 jam</b>. Kami akan mengirimkan email jika selesai.</p>

    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
        @csrf
        <button type="submit">Logout</button>
    </form>
</div>

</body>
</html>
