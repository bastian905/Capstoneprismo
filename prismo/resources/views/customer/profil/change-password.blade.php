<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ubah Password | PRISMO</title>
    <link rel="stylesheet" href="{{ asset('css/change-password.css') }}">
</head>
<body>
    <div class="container">
        <header>
            <button type="button" class="btn-back" onclick="goBack()">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M12.707 4.293a1 1 0 010 1.414L8.414 10l4.293 4.293a1 1 0 01-1.414 1.414l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 0z"/>
                </svg>
                Kembali
            </button>
            <h1>Ubah Password</h1>
            <p class="subtitle">Perbarui password Anda untuk keamanan akun</p>
        </header>

        @if(session('oauth_provider'))
        <!-- Notification for OAuth users -->
        <div class="oauth-notice">
            <div class="notice-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M13 16h-2v-6h2m0-4h-2V4h2m-1 16.5c-5.5 0-10-4.5-10-10s4.5-10 10-10 10 4.5 10 10-4.5 10-10 10z"/>
                </svg>
            </div>
            <div class="notice-content">
                <h3>Login dengan {{ ucfirst(session('oauth_provider')) }}</h3>
                <p>Anda login menggunakan akun {{ ucfirst(session('oauth_provider')) }}. Fitur ubah password tidak tersedia untuk akun OAuth. Silakan kelola password Anda melalui pengaturan {{ ucfirst(session('oauth_provider')) }}.</p>
            </div>
        </div>
        @else
        <!-- Password Change Form -->
        <form id="changePasswordForm" class="password-form">
            <div class="form-group">
                <label for="currentPassword" class="form-label">
                    Password Saat Ini <span class="required">*</span>
                </label>
                <div class="password-input-wrapper">
                    <input 
                        type="password" 
                        id="currentPassword" 
                        name="current_password"
                        class="form-input"
                        placeholder="Masukkan password saat ini"
                        required
                    >
                    <button type="button" class="toggle-password" data-target="currentPassword">
                        <svg class="eye-open" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 4.5c-4.142 0-7.5 3.358-7.5 7.5 0 .825.134 1.618.382 2.359a.75.75 0 001.4-.518A5.752 5.752 0 014.5 12c0-3.037 2.463-5.5 5.5-5.5s5.5 2.463 5.5 5.5c0 .482-.062.95-.179 1.395a.75.75 0 001.4.518c.248-.741.382-1.534.382-2.359 0-4.142-3.358-7.5-7.5-7.5z"/>
                            <path d="M10 9a1 1 0 100 2 1 1 0 000-2zm-3 1a3 3 0 116 0 3 3 0 01-6 0z"/>
                        </svg>
                        <svg class="eye-closed" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" style="display: none;">
                            <path d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"/>
                            <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>
                        </svg>
                    </button>
                </div>
                <span class="error-message" id="currentPasswordError"></span>
            </div>

            <div class="form-group">
                <label for="newPassword" class="form-label">
                    Password Baru <span class="required">*</span>
                </label>
                <div class="password-input-wrapper">
                    <input 
                        type="password" 
                        id="newPassword" 
                        name="new_password"
                        class="form-input"
                        placeholder="Masukkan password baru"
                        required
                    >
                    <button type="button" class="toggle-password" data-target="newPassword">
                        <svg class="eye-open" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 4.5c-4.142 0-7.5 3.358-7.5 7.5 0 .825.134 1.618.382 2.359a.75.75 0 001.4-.518A5.752 5.752 0 014.5 12c0-3.037 2.463-5.5 5.5-5.5s5.5 2.463 5.5 5.5c0 .482-.062.95-.179 1.395a.75.75 0 001.4.518c.248-.741.382-1.534.382-2.359 0-4.142-3.358-7.5-7.5-7.5z"/>
                            <path d="M10 9a1 1 0 100 2 1 1 0 000-2zm-3 1a3 3 0 116 0 3 3 0 01-6 0z"/>
                        </svg>
                        <svg class="eye-closed" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" style="display: none;">
                            <path d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"/>
                            <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Password Strength Indicator -->
                <div class="password-strength">
                    <div class="strength-bars">
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                    </div>
                    <span class="strength-text">Kekuatan password</span>
                </div>

                <!-- Password Requirements -->
                <div class="password-requirements">
                    <p class="requirements-title">Password harus memenuhi:</p>
                    <ul class="requirements-list">
                        <li class="requirement" data-requirement="length">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.5" fill="none"/>
                            </svg>
                            Minimal 8 karakter
                        </li>
                        <li class="requirement" data-requirement="uppercase">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.5" fill="none"/>
                            </svg>
                            Minimal 1 huruf besar (A-Z)
                        </li>
                        <li class="requirement" data-requirement="lowercase">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.5" fill="none"/>
                            </svg>
                            Minimal 1 huruf kecil (a-z)
                        </li>
                        <li class="requirement" data-requirement="number">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.5" fill="none"/>
                            </svg>
                            Minimal 1 angka (0-9)
                        </li>
                    </ul>
                </div>
            </div>

            <div class="form-group">
                <label for="confirmPassword" class="form-label">
                    Konfirmasi Password Baru <span class="required">*</span>
                </label>
                <div class="password-input-wrapper">
                    <input 
                        type="password" 
                        id="confirmPassword" 
                        name="confirm_password"
                        class="form-input"
                        placeholder="Konfirmasi password baru"
                        required
                    >
                    <button type="button" class="toggle-password" data-target="confirmPassword">
                        <svg class="eye-open" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 4.5c-4.142 0-7.5 3.358-7.5 7.5 0 .825.134 1.618.382 2.359a.75.75 0 001.4-.518A5.752 5.752 0 014.5 12c0-3.037 2.463-5.5 5.5-5.5s5.5 2.463 5.5 5.5c0 .482-.062.95-.179 1.395a.75.75 0 001.4.518c.248-.741.382-1.534.382-2.359 0-4.142-3.358-7.5-7.5-7.5z"/>
                            <path d="M10 9a1 1 0 100 2 1 1 0 000-2zm-3 1a3 3 0 116 0 3 3 0 01-6 0z"/>
                        </svg>
                        <svg class="eye-closed" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" style="display: none;">
                            <path d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"/>
                            <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>
                        </svg>
                    </button>
                </div>
                <span class="error-message" id="confirmPasswordError"></span>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="goBack()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Password</button>
            </div>
        </form>
        @endif
    </div>

    <!-- Success Modal -->
    <div class="modal-overlay" id="successModal" style="display: none;">
        <div class="modal">
            <div class="modal-icon success">
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                    <circle cx="24" cy="24" r="22" stroke="#10B981" stroke-width="4"/>
                    <path d="M14 24l8 8 12-16" stroke="#10B981" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h3 class="modal-title">Password Berhasil Diubah!</h3>
            <p class="modal-message">Password Anda telah berhasil diperbarui. Silakan login kembali dengan password baru Anda.</p>
            <button type="button" class="btn btn-primary" onclick="redirectToProfile()">Kembali ke Profil</button>
        </div>
    </div>

    <script src="{{ asset('js/change-password.js') }}"></script>
</body>
</html>
