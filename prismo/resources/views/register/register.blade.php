<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | Prismo</title>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>

<body>
    <!-- SIGN UP -->
    <div class="form-container sign-up">
        <!-- Pilihan Customer/Mitra di kanan atas -->
        <div class="customer-type-toggle">
            <div class="toggle-buttons">
                <button type="button" class="type-btn active" data-type="customer">Customer</button>
                <button type="button" class="type-btn" data-type="mitra">Mitra</button>
            </div>
        </div>

        <form id="signup-form">
            <div class="logo-container">
                <img src="{{ asset('images/logo.png') }}" alt="Prismo Logo" class="logo">
            </div>
            <h1 class="form-title">Daftar</h1>

            <div class="input-container email">
                <input type="email" name="email" placeholder="Email" autocomplete="email" required>
            </div>

            <div class="input-container password">
                <input type="password" id="signup-password" name="password" class="password-input" placeholder="Password" required>
                <i class="ph ph-eye toggle-password"></i>
            </div>

            <div class="input-container password">
                <input type="password" name="confirmPassword" class="password-input" placeholder="Confirm Password" required>
                <i class="ph ph-eye toggle-password"></i>
            </div>

            <div class="checkbox-container">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">Dengan membuat akun, Anda Menyetujui <a href="#" id="showTerms">Syarat & Ketentuan</a></label>
            </div>

            <button type="submit" class="main-btn">
                <span class="btn-text">Daftar</span>
                <div class="loading-spinner">
                    <i class="ph ph-circle-notch"></i>
                </div>
            </button>

            <div class="divider">
                <span>or</span>
            </div>

            <button type="button" class="google-btn" id="google-signup-btn">
                <img src="{{ asset('images/google.png') }}" alt="Google" class="google-icon">
                Lanjut dengan Google
            </button>

            <div class="login-link">
                <small>Sudah punya akun?</small>
                <a href="#" id="login">Masuk</a>
            </div>
        </form>
    </div>

    <!-- SIGN IN -->
    <div class="form-container sign-in">
        <form id="signin-form">
            <div class="logo-container">
                <img src="{{ asset('images/logo.png') }}" alt="Prismo Logo" class="logo">
            </div>
            <h1 class="form-title">Masuk</h1>

            <div class="input-container email">
                <input type="email" name="email" placeholder="Email" autocomplete="email" required>
            </div>

            <div class="input-container password">
                <input type="password" name="password" class="password-input" placeholder="Password" required>
                <i class="ph ph-eye toggle-password"></i>
            </div>

            <div class="forgot-password-container">
                <button type="button" id="forgot-password">Lupa Password?</button>
            </div>

            <button type="submit" class="main-btn">
                <span class="btn-text">Masuk</span>
                <div class="loading-spinner">
                    <i class="ph ph-circle-notch"></i>
                </div>
            </button>

            <div class="divider">
                <span>or</span>
            </div>

            <button type="button" class="google-btn" onclick="window.location.href='{{ url('/auth/google?action=login') }}'">
                <img src="{{ asset('images/google.png') }}" alt="Google" class="google-icon">
                Lanjut dengan Google
            </button>

            <div class="login-link">
                <small>Belum punya akun?</small>
                <a href="#" id="register">Buat Akun</a>
            </div>
        </form>
    </div>

    <!-- GAMBAR PANEL -->
    <div class="toggle">
        <div class="image-container"></div>
    </div>

    <!-- MODAL LUPA PASSWORD -->
    <div class="forgot-password-modal" id="forgotPasswordModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Lupa Password?</h2>
                <button type="button" class="close-modal" id="closeForgotPasswordModal">
                    <i class="ph ph-x"></i>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="modal-logo-container">
                    <img src="{{ asset('images/lupapassword.png') }}" alt="Prismo Logo" class="modal-logo">
                </div>
                
                <p class="modal-description">Masukkan email Anda dan kami akan mengirimkan link untuk reset password</p>

                <form class="forgot-password-form" id="forgot-password-form">
                    <div class="input-container">
                        <input type="email" name="email" placeholder="Email" autocomplete="email" required>
                    </div>

                    <button type="submit" class="main-btn">
                        <span class="btn-text">Kirim link reset password</span>
                        <div class="loading-spinner">
                            <i class="ph ph-circle-notch"></i>
                        </div>
                    </button>
                </form>

                <div class="confirmation-message">
                    <div class="success-icon">
                        <i class="ph ph-check-circle"></i>
                    </div>
                    <h3>Email Terkirim!</h3>
                    <p>Kami telah mengirimkan link reset password ke email Anda. Silakan cek inbox atau folder spam Anda.</p>
                    <button type="button" class="main-btn" id="closeAfterSuccess">Tutup</button>
                </div>
            </div>

            <div class="modal-footer">
                <div class="support-links">
                    <p>Butuh Bantuan?</p>
                    <div class="support-buttons">
                        <a href="/support">Hubungi Support</a>
                        <span>|</span>
                        <a href="/faq">FAQ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL SYARAT & KETENTUAN -->
    <div class="terms-modal" id="termsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Syarat & Ketentuan</h2>
                <button type="button" class="close-modal" id="closeTermsModal">
                    <i class="ph ph-x"></i>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="terms-content">
                    <p>Dengan menggunakan layanan kami, Anda menyetujui syarat dan ketentuan berikut:</p>
                    <p>1</p>
                    <p>2</p>
                </div>
            </div>
        </div>
    </div>

    <!-- NOTIFICATION TOAST -->
    <div class="notification-toast" id="notificationToast">
        <div class="toast-content">
            <div class="toast-icon"></div>
            <div class="toast-message"></div>
            <button class="toast-close">
                <i class="ph ph-x"></i>
            </button>
        </div>
    </div>

    <script src="{{ asset('js/register.js') }}"></script>
</body>

</html>
