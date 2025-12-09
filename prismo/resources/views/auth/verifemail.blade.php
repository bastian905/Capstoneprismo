<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email | Prismo</title>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            padding: 40px;
            text-align: center;
            border: 1px solid #f0f0f0;
        }

        .icon-wrapper {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .icon-wrapper i {
            font-size: 40px;
            color: white;
        }

        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .email {
            color: #667eea;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .message {
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 15px;
        }

        .timer-container {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .timer-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }

        .timer {
            font-size: 36px;
            font-weight: 700;
            color: #667eea;
            font-family: 'Courier New', monospace;
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-outline {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-outline:hover {
            background: #667eea;
            color: white;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            margin-left: 8px;
            vertical-align: middle;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: left;
        }

        .info-box p {
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
        }

        .info-box p:last-child {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-wrapper">
            <i class="ph ph-envelope"></i>
        </div>

        <h1>Verifikasi Email Anda</h1>
        <p class="email">{{ Auth::user()->email }}</p>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <p class="message">
            Kami telah mengirimkan link verifikasi ke email Anda. 
            Silakan cek inbox atau folder spam Anda.
        </p>

        <div class="timer-container">
            <div class="timer-label">Kirim ulang dalam:</div>
            <div class="timer" id="countdown">05:00</div>
        </div>

        <form method="POST" action="{{ route('verification.resend') }}" id="resendForm">
            @csrf
            <button type="submit" class="btn btn-primary" id="resendBtn" disabled>
                <span id="btnText">Kirim Ulang Email</span>
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline">
                Logout
            </button>
        </form>

        <div class="info-box">
            <p><strong>Tips:</strong></p>
            <p>• Link verifikasi berlaku selama 5 menit</p>
            <p>• Cek folder spam jika tidak menemukan email</p>
            <p>• Klik link verifikasi hanya sekali</p>
        </div>
    </div>

    <script>
        // Get expiry time from server (Unix timestamp in seconds)
        const expiryTime = {{ $expiryTime ?? now()->addMinutes(5)->timestamp }};
        const currentTime = Math.floor(Date.now() / 1000);
        let countdown = Math.max(0, expiryTime - currentTime);
        
        const timerElement = document.getElementById('countdown');
        const resendBtn = document.getElementById('resendBtn');
        const btnText = document.getElementById('btnText');
        const resendForm = document.getElementById('resendForm');

        function updateTimer() {
            const minutes = Math.floor(countdown / 60);
            const seconds = countdown % 60;
            timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

            if (countdown <= 0) {
                resendBtn.disabled = false;
                timerElement.textContent = '00:00';
                timerElement.style.color = '#dc3545';
            } else {
                countdown--;
                setTimeout(updateTimer, 1000);
            }
        }

        updateTimer();

        resendForm.addEventListener('submit', function(e) {
            resendBtn.disabled = true;
            btnText.innerHTML = 'Mengirim... <span class="loading"></span>';
        });
    </script>
</body>
</html>
