<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Password | Prismo</title>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Montserrat", sans-serif;
        }

        body {
            background: #f4f6f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .reset-password-container {
            width: 100%;
            max-width: 450px;
        }

        .reset-password-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
        }

        .header h1 {
            font-size: clamp(24px, 4vw, 28px);
            font-weight: 700;
            color: #003049;
            margin-bottom: 30px;
            text-transform: uppercase;
        }

        .reset-password-form {
            width: 100%;
            margin-bottom: 30px;
        }

        .input-group {
            margin-bottom: 25px;
            text-align: left;
        }

        .input-label {
            display: block;
            color: #003049;
            font-size: clamp(13px, 1.5vw, 14px);
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-container {
            position: relative;
            width: 100%;
        }

        .input-container input {
            width: 100%;
            padding: clamp(14px, 3vw, 16px) clamp(40px, 4vw, 50px) clamp(14px, 3vw, 16px) 20px;
            font-size: clamp(14px, 2vw, 16px);
            border: 2px solid #E0E0E0;
            border-radius: 10px;
            outline: none;
            background: #FAFAFA;
            transition: all 0.3s ease;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .input-container input:focus {
            border-color: #007bff;
            background: #FFFFFF;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .input-container input.valid {
            border-color: #28a745 !important;
        }

        .input-container input.invalid {
            border-color: #dc3545 !important;
        }

        .password-strength {
            font-size: 12px;
            margin-top: 5px;
            font-weight: 500;
        }

        .password-strength.weak {
            color: #dc3545;
        }

        .password-strength.strong {
            color: #28a745;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #777;
            font-size: clamp(18px, 2vw, 20px);
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: #007bff;
        }

        .main-btn {
            background-color: #007bff;
            color: #fff;
            font-size: clamp(14px, 2vw, 16px);
            padding: clamp(14px, 3vw, 16px) 0;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }

        .main-btn:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }

        .main-btn:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .footer {
            border-top: 1px solid #E0E0E0;
            padding-top: 25px;
        }

        .back-to-login {
            background: none;
            border: none;
            color: #007bff;
            font-size: clamp(13px, 1.5vw, 14px);
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 0 auto;
            transition: color 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .back-to-login:hover {
            color: #0056b3;
        }

        .back-to-login i {
            font-size: clamp(14px, 1.5vw, 16px);
        }

        /* Custom Alert */
        .custom-alert {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .custom-alert.show {
            opacity: 1;
            visibility: visible;
        }

        .alert-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            transform: scale(0.7);
            transition: transform 0.3s ease;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .custom-alert.show .alert-content {
            transform: scale(1);
        }

        .alert-icon {
            font-size: 50px;
            color: #28a745;
            margin-bottom: 15px;
        }

        .alert-title {
            font-size: 20px;
            font-weight: 700;
            color: #003049;
            margin-bottom: 10px;
        }

        .alert-message {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .alert-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .alert-button:hover {
            background: #0056b3;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .reset-password-card {
                padding: 35px 30px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }
            
            .reset-password-card {
                padding: 30px 25px;
                border-radius: 15px;
            }
            
            .header h1 {
                margin-bottom: 25px;
            }
            
            .input-group {
                margin-bottom: 20px;
            }
            
            .footer {
                padding-top: 20px;
            }
            
            .alert-content {
                padding: 25px;
            }
        }

        @media (max-width: 375px) {
            .reset-password-card {
                padding: 25px 20px;
            }
            
            .header h1 {
                font-size: 22px;
            }
            
            .input-container input {
                padding: 12px 35px 12px 15px;
                font-size: 13px;
            }
            
            .toggle-password {
                right: 12px;
                font-size: 16px;
            }
            
            .main-btn {
                padding: 12px 0;
                font-size: 14px;
            }
            
            .back-to-login {
                font-size: 12px;
            }
        }

        @media (max-height: 600px) and (orientation: landscape) {
            body {
                padding: 10px;
                align-items: flex-start;
            }
            
            .reset-password-card {
                padding: 25px 30px;
                margin-top: 20px;
            }
            
            .header {
                margin-bottom: 20px;
            }
            
            .input-group {
                margin-bottom: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="reset-password-container">
        <div class="reset-password-card">
            <div class="header">
                <h1>Reset Password</h1>
            </div>

            <form class="reset-password-form">
                <div class="input-group">
                    <label class="input-label">Password baru</label>
                    <div class="input-container password">
                        <input type="password" class="password-input" id="newPassword" required placeholder="Masukkan password baru">
                        <i class="ph ph-eye toggle-password"></i>
                    </div>
                    <div class="password-strength" id="passwordStrength"></div>
                </div>

                <div class="input-group">
                    <label class="input-label">Konfirmasi password baru</label>
                    <div class="input-container password">
                        <input type="password" class="password-input" id="confirmPassword" required placeholder="Konfirmasi password baru">
                        <i class="ph ph-eye toggle-password"></i>
                    </div>
                </div>

                <button type="submit" class="main-btn" id="submitBtn">Konfirmasi</button>
            </form>

            <div class="footer">
                <button type="button" class="back-to-login" id="backToLogin">
                    <i class="ph ph-arrow-left"></i>
                    Kembali ke Login
                </button>
            </div>
        </div>
    </div>

    <!-- Custom Alert -->
    <div class="custom-alert" id="successAlert">
        <div class="alert-content">
            <div class="alert-icon">
                <i class="ph ph-check-circle"></i>
            </div>
            <h3 class="alert-title">Berhasil!</h3>
            <p class="alert-message">Password berhasil direset! Silakan login dengan password baru Anda.</p>
            <button class="alert-button" id="alertOkBtn">OK</button>
        </div>
    </div>

    <script>
        // Toggle visibility password
        document.querySelectorAll(".toggle-password").forEach(icon => {
            icon.addEventListener("click", function() {
                const input = this.previousElementSibling;
                if (input.type === "password") {
                    input.type = "text";
                    this.classList.replace("ph-eye", "ph-eye-slash");
                } else {
                    input.type = "password";
                    this.classList.replace("ph-eye-slash", "ph-eye");
                }
            });
        });

        // Back to login
        document.getElementById('backToLogin').addEventListener('click', function() {
            window.location.href = '/login';
        });

        // Validasi password strength
        const newPasswordInput = document.getElementById('newPassword');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const passwordStrength = document.getElementById('passwordStrength');

        newPasswordInput.addEventListener('input', function() {
            const password = this.value;
            
            // Reset classes
            this.classList.remove('valid', 'invalid');
            passwordStrength.className = 'password-strength';
            
            if (password.length === 0) {
                passwordStrength.textContent = '';
            } else if (password.length < 8) {
                this.classList.add('invalid');
                passwordStrength.textContent = 'Password terlalu pendek (minimal 8 karakter)';
                passwordStrength.classList.add('weak');
            } else {
                this.classList.add('valid');
                passwordStrength.classList.add('strong');
                validatePasswordMatch();
            }
        });

        // Validasi konfirmasi password
        confirmPasswordInput.addEventListener('input', validatePasswordMatch);

        function validatePasswordMatch() {
            const password = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            // Reset classes
            confirmPasswordInput.classList.remove('valid', 'invalid');
            
            if (confirmPassword.length === 0) {
                return;
            }
            
            // Hanya valid jika password sudah 8 karakter DAN cocok
            if (password.length >= 8 && password === confirmPassword) {
                confirmPasswordInput.classList.add('valid');
            } else if (confirmPassword.length > 0) {
                confirmPasswordInput.classList.add('invalid');
            }
        }

        // Form submission
        document.querySelector('.reset-password-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const password = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            // Validasi final
            if (password.length < 8) {
                alert('Password harus minimal 8 karakter');
                newPasswordInput.focus();
                return;
            }
            
            if (password !== confirmPassword) {
                alert('Password dan konfirmasi password tidak cocok');
                confirmPasswordInput.focus();
                return;
            }
            
            // Loading state
            submitBtn.disabled = true;
            submitBtn.textContent = 'Memproses...';
            
            // Simulasi proses reset password
            setTimeout(() => {
                // Show custom alert
                document.getElementById('successAlert').classList.add('show');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Konfirmasi';
            }, 1000);
        });

        // Custom alert handler
        document.getElementById('alertOkBtn').addEventListener('click', function() {
            document.getElementById('successAlert').classList.remove('show');
            window.location.href = '/login';
        });

        // Close alert when clicking outside
        document.getElementById('successAlert').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('show');
                window.location.href = '/login';
            }
        });
    </script>
</body>

</html>
