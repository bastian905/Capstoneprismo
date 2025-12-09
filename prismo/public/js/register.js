const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');
const forgotPasswordBtn = document.getElementById('forgot-password');
const closeAfterSuccessBtn = document.getElementById('closeAfterSuccess');
const forgotPasswordModal = document.getElementById('forgotPasswordModal');
const forgotPasswordForm = document.getElementById('forgot-password-form');
const confirmationMessage = document.querySelector('.confirmation-message');
const customerTypeToggle = document.querySelector('.customer-type-toggle');
const submitResetPasswordBtn = document.querySelector('.forgot-password-form .main-btn');
const showTermsBtn = document.getElementById('showTerms');
const termsModal = document.getElementById('termsModal');
const closeTermsModal = document.getElementById('closeTermsModal');
const closeForgotPasswordModal = document.getElementById('closeForgotPasswordModal');
const signupForm = document.getElementById('signup-form');
const signinForm = document.getElementById('signin-form');
const notificationToast = document.getElementById('notificationToast');

// Variabel untuk menyimpan role
let selectedRole = 'customer';

// Handle signin form with API for token
if (signinForm) {
    signinForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const email = formData.get('email');
        const password = formData.get('password');
        
        // Validasi
        if (!email || !password) {
            showNotification('Email dan password harus diisi', 'error');
            return;
        }
        
        const submitBtn = e.target.querySelector('button[type="submit"]');
        showLoading(submitBtn);
        
        try {
            // Get CSRF token first
            await fetch('/sanctum/csrf-cookie', {
                credentials: 'same-origin'
            });
            
            // Login via API to get token
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin',
                body: JSON.stringify({ email, password })
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Login gagal');
            }
            
            // Save token to localStorage
            if (data.token) {
                localStorage.setItem('auth_token', data.token);
                console.log('✅ Token saved to localStorage');
            }
            
            // Save user data
            if (data.user) {
                localStorage.setItem('user_data', JSON.stringify(data.user));
                console.log('✅ User data saved:', data.user);
                console.log('📸 Avatar URL:', data.user.avatar);
            }

                // Set default Authorization header for future API requests
                window.apiFetch = function(url, options = {}) {
                    const token = localStorage.getItem('auth_token');
                    options.headers = options.headers || {};
                    if (token) {
                        options.headers['Authorization'] = 'Bearer ' + token;
                    }
                    options.headers['Accept'] = options.headers['Accept'] || 'application/json';
                    return fetch(url, options);
                };
            
            hideLoading(submitBtn);
            showNotification('Login berhasil! Mengarahkan...', 'success');
            
            // Redirect based on role
            setTimeout(() => {
                if (data.user.role === 'admin') {
                    window.location.href = '/admin/dashboard';
                } else if (data.user.role === 'mitra') {
                    if (!data.user.profile_completed || data.user.approval_status === 'pending' || data.user.approval_status === 'rejected') {
                        window.location.href = '/mitra/form-mitra';
                    } else {
                        window.location.href = '/dashboard-mitra';
                    }
                } else {
                    window.location.href = '/dashboard';
                }
            }, 1000);
            
        } catch (error) {
            console.error('Login error:', error);
            hideLoading(submitBtn);
            showNotification(error.message || 'Email atau password salah', 'error');
        }
    });
}

// Toggle animasi antar form - PERBAIKAN DI SINI
registerBtn.addEventListener('click', (e) => {
    e.preventDefault();
    document.body.classList.add("active");
    setTimeout(adjustCustomerTypeToggle, 100);
    
    // Update URL hash untuk register
    window.location.hash = 'register';
});

loginBtn.addEventListener('click', (e) => {
    e.preventDefault();
    document.body.classList.remove("active");
    
    // Update URL hash untuk login
    window.location.hash = 'login';
});

// Handle browser back/forward buttons and hash changes
window.addEventListener('hashchange', function() {
    const hash = window.location.hash;
    if (hash === '#register') {
        document.body.classList.add("active");
        setTimeout(adjustCustomerTypeToggle, 100);
    } else if (hash === '#login' || hash === '') {
        document.body.classList.remove("active");
    }
});

// Set initial state based on URL query parameter
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    
    if (tab === 'register') {
        document.body.classList.add("active");
        setTimeout(adjustCustomerTypeToggle, 100);
    } else if (tab === 'login') {
        document.body.classList.remove("active");
    } else {
        // Default behavior if no tab parameter
        const hash = window.location.hash;
        if (hash === '#register') {
            document.body.classList.add("active");
            setTimeout(adjustCustomerTypeToggle, 100);
        } else {
            document.body.classList.remove("active");
        }
    }
});

// Toggle ikon mata
document.querySelectorAll(".toggle-password").forEach(icon => {
    icon.addEventListener("click", () => {
        const input = icon.previousElementSibling;
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace("ph-eye", "ph-eye-slash");
        } else {
            input.type = "password";
            icon.classList.replace("ph-eye-slash", "ph-eye");
        }
    });
});

// Toggle Customer/Mitra
document.querySelectorAll('.type-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.type-btn').forEach(b => {
            b.classList.remove('active');
        });
        
        this.classList.add('active');
        
        selectedRole = this.getAttribute('data-type');
        console.log('Selected role:', selectedRole);
        
        // Update hidden role input
        const roleInput = document.getElementById('role-input');
        if (roleInput) {
            roleInput.value = selectedRole;
        }
    });
});

// Pindahkan toggle customer/mitra ke bawah judul "Daftar" di mobile
function adjustCustomerTypeToggle() {
    if (window.innerWidth <= 768) {
        const formTitle = document.querySelector('.sign-up .form-title');
        
        if (formTitle && customerTypeToggle) {
            formTitle.insertAdjacentElement('afterend', customerTypeToggle);
            customerTypeToggle.classList.add('mobile-position');
        }
    } else {
        const signUpForm = document.querySelector('.sign-up');
        if (signUpForm && customerTypeToggle) {
            // Kembalikan ke posisi semula (kanan atas)
            const existingToggle = signUpForm.querySelector('.customer-type-toggle');
            if (!existingToggle) {
                signUpForm.appendChild(customerTypeToggle);
            }
            customerTypeToggle.classList.remove('mobile-position');
        }
    }
}

// Panggil fungsi saat load dan resize
window.addEventListener('load', adjustCustomerTypeToggle);
window.addEventListener('resize', adjustCustomerTypeToggle);

// Modal lupa password
forgotPasswordBtn.addEventListener('click', () => {
    forgotPasswordModal.classList.add('active');
    resetForgotPasswordForm();
});

closeForgotPasswordModal.addEventListener('click', () => {
    forgotPasswordModal.classList.remove('active');
    resetForgotPasswordForm();
});

closeAfterSuccessBtn.addEventListener('click', () => {
    forgotPasswordModal.classList.remove('active');
    resetForgotPasswordForm();
});

// Tutup modal ketika klik di luar
forgotPasswordModal.addEventListener('click', (e) => {
    if (e.target === forgotPasswordModal) {
        forgotPasswordModal.classList.remove('active');
        resetForgotPasswordForm();
    }
});

// Modal Syarat & Ketentuan
showTermsBtn.addEventListener('click', (e) => {
    e.preventDefault();
    termsModal.classList.add('active');
});

closeTermsModal.addEventListener('click', () => {
    termsModal.classList.remove('active');
});

// Tutup modal terms ketika klik di luar
termsModal.addEventListener('click', (e) => {
    if (e.target === termsModal) {
        termsModal.classList.remove('active');
    }
});

// Handle form lupa password
forgotPasswordForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const emailInput = e.target.email;
    const email = emailInput.value;
    
    if (!isValidEmail(email)) {
        showError('Masukkan alamat email yang valid (contoh: nama@domain.com)');
        return;
    }
    
    showLoading(forgotPasswordForm.querySelector('.main-btn'));
    
    try {
        await sendResetPasswordEmail(email);
        hideLoading(forgotPasswordForm.querySelector('.main-btn'));
        showConfirmation();
    } catch (error) {
        console.error('Error:', error);
        hideLoading(forgotPasswordForm.querySelector('.main-btn'));
        showError('Terjadi kesalahan. Silakan coba lagi.');
    }
});

// Handle form sign up
signupForm.addEventListener('submit', (e) => {
    const formData = new FormData(e.target);
    const email = formData.get('email');
    const password = formData.get('password');
    const confirmPassword = formData.get('confirmPassword');
    const terms = formData.get('terms');
    
    // Validasi form - hanya prevent jika ada error
    if (!email || !password || !confirmPassword || !terms) {
        e.preventDefault();
        alert('Mohon lengkapi semua field');
        return;
    }
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Password dan Confirm Password tidak sama');
        return;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('Password minimal 6 karakter');
        return;
    }
    
    // Form valid, biarkan submit normal ke server
    showLoading(signupForm.querySelector('.main-btn'));
});

// Handle form sign in
signinForm.addEventListener('submit', (e) => {
    const formData = new FormData(e.target);
    const email = formData.get('email');
    const password = formData.get('password');
    
    // Validasi form - hanya prevent jika ada error
    if (!email || !password) {
        e.preventDefault();
        alert('Mohon lengkapi email dan password');
        return;
    }
    
    // Form valid, biarkan submit normal ke server
    showLoading(signinForm.querySelector('.main-btn'));
});

// Fungsi untuk menampilkan loading
function showLoading(button) {
    if (button) {
        button.classList.add('loading');
        button.disabled = true;
    }
}

// Fungsi untuk menyembunyikan loading
function hideLoading(button) {
    if (button) {
        button.classList.remove('loading');
        button.disabled = false;
    }
}

// Fungsi untuk menampilkan konfirmasi sukses
function showConfirmation() {
    forgotPasswordForm.style.display = 'none';
    confirmationMessage.style.display = 'block';
}

// Fungsi untuk reset form
function resetForgotPasswordForm() {
    forgotPasswordForm.reset();
    forgotPasswordForm.style.display = 'block';
    confirmationMessage.style.display = 'none';
    hideLoading(forgotPasswordForm.querySelector('.main-btn'));
}

// Validasi email
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Validasi password (hanya 8 karakter dan sama dengan confirm)
function validatePassword(password, confirmPassword) {
    if (password.length < 8) {
        return { isValid: false, message: 'Password harus minimal 8 karakter' };
    }
    
    if (password !== confirmPassword) {
        return { isValid: false, message: 'Password dan Confirm Password tidak sama' };
    }
    
    return { isValid: true, message: '' };
}

// Validasi form sign up
function validateSignUpForm(email, password, confirmPassword, terms) {
    // Validasi email
    if (!isValidEmail(email)) {
        showError('Format email tidak valid');
        return false;
    }
    
    // Validasi password
    const passwordValidation = validatePassword(password, confirmPassword);
    if (!passwordValidation.isValid) {
        showError(passwordValidation.message);
        return false;
    }
    
    // Validasi terms
    if (!terms) {
        showError('Anda harus menyetujui Syarat & Ketentuan');
        return false;
    }
    
    return true;
}

// Validasi form sign in
function validateSignInForm(email, password) {
    if (!isValidEmail(email)) {
        showError('Format email tidak valid');
        return false;
    }
    
    if (password.length === 0) {
        showError('Password harus diisi');
        return false;
    }
    
    return true;
}

// Fungsi untuk menampilkan pesan error
function showError(message) {
    let errorDiv = document.querySelector('.error-message');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        document.body.appendChild(errorDiv);
    }
    
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    
    setTimeout(() => {
        errorDiv.style.display = 'none';
    }, 5000);
}

// Fungsi untuk menampilkan notifikasi
function showNotification(message, type = 'success') {
    const toast = notificationToast;
    const toastContent = toast.querySelector('.toast-content');
    const toastIcon = toast.querySelector('.toast-icon');
    const toastMessage = toast.querySelector('.toast-message');
    
    // Set kelas berdasarkan type
    toastContent.className = 'toast-content';
    toastContent.classList.add(type);
    
    // Set icon berdasarkan type
    if (type === 'success') {
        toastIcon.innerHTML = '<i class="ph ph-check"></i>';
    } else {
        toastIcon.innerHTML = '<i class="ph ph-x"></i>';
    }
    
    // Set message
    toastMessage.textContent = message;
    
    // Tampilkan toast
    toast.classList.remove('hide');
    toast.classList.add('show');
    
    // Auto hide setelah 5 detik
    setTimeout(() => {
        hideNotification();
    }, 5000);
}

// Fungsi untuk menyembunyikan notifikasi
function hideNotification() {
    const toast = notificationToast;
    toast.classList.remove('show');
    toast.classList.add('hide');
    
    setTimeout(() => {
        toast.classList.remove('hide');
    }, 300);
}

// Event listener untuk close button toast
document.querySelector('.toast-close').addEventListener('click', hideNotification);

// Simulasi pengiriman email
function sendResetPasswordEmail(email) {
    return new Promise((resolve) => {
        setTimeout(() => {
            console.log('Reset password link sent to:', email);
            resolve({ success: true });
        }, 2000);
    });
}

// Simulasi registrasi user
function registerUser(userData) {
    return new Promise((resolve) => {
        setTimeout(() => {
            // Simulasi: 80% berhasil, 20% gagal
            const isSuccess = Math.random() > 0.2;
            
            if (isSuccess) {
                resolve({
                    success: true,
                    message: 'Registrasi berhasil'
                });
            } else {
                resolve({
                    success: false,
                    message: 'Email sudah terdaftar'
                });
            }
        }, 2000);
    });
}

// Simulasi login user
function loginUser(credentials) {
    return new Promise((resolve) => {
        setTimeout(() => {
            // Simulasi: 70% berhasil, 30% gagal
            const isSuccess = Math.random() > 0.3;
            
            if (isSuccess) {
                resolve({
                    success: true,
                    message: 'Login berhasil'
                });
            } else {
                resolve({
                    success: false,
                    message: 'Email atau password salah'
                });
            }
        }, 1500);
    });
}

// Real-time validation
document.addEventListener('DOMContentLoaded', function() {
    // Tambahkan CSS untuk error message
    const style = document.createElement('style');
    style.textContent = `
        .error-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #dc3545;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            max-width: 400px;
            white-space: pre-line;
            display: none;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .input-error {
            border-color: #dc3545 !important;
            background: #fff5f5 !important;
        }
    `;
    document.head.appendChild(style);
    
    // Validasi real-time untuk email
    setupEmailValidation('.sign-up input[type="email"]');
    setupEmailValidation('.sign-in input[type="email"]');
    
    // Validasi real-time untuk password
    const passwordInput = document.querySelector('.sign-up input[name="password"]');
    const confirmPasswordInput = document.querySelector('.sign-up input[name="confirmPassword"]');
    
    if (passwordInput && confirmPasswordInput) {
        setupPasswordValidation(passwordInput, confirmPasswordInput);
    }
});

function setupEmailValidation(selector) {
    const emailInput = document.querySelector(selector);
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const email = this.value;
            if (email.length > 0) {
                if (!isValidEmail(email)) {
                    this.classList.add('input-error');
                    this.classList.remove('input-success');
                } else {
                    this.classList.remove('input-error');
                    this.classList.add('input-success');
                }
            } else {
                this.classList.remove('input-error', 'input-success');
            }
        });
    }
}

function setupPasswordValidation(passwordInput, confirmPasswordInput) {
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        
        if (password.length > 0) {
            if (password.length < 8) {
                this.classList.add('input-error');
                this.classList.remove('input-success');
            } else {
                this.classList.remove('input-error');
                this.classList.add('input-success');
            }
        } else {
            this.classList.remove('input-error', 'input-success');
        }
        
        // Validasi confirm password
        if (confirmPasswordInput.value.length > 0) {
            validateConfirmPassword(passwordInput, confirmPasswordInput);
        }
    });
    
    confirmPasswordInput.addEventListener('input', function() {
        validateConfirmPassword(passwordInput, confirmPasswordInput);
    });
}

function validateConfirmPassword(passwordInput, confirmPasswordInput) {
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;
    
    if (confirmPassword.length > 0) {
        if (password === confirmPassword && password.length >= 8) {
            confirmPasswordInput.classList.remove('input-error');
            confirmPasswordInput.classList.add('input-success');
        } else {
            confirmPasswordInput.classList.add('input-error');
            confirmPasswordInput.classList.remove('input-success');
        }
    } else {
        confirmPasswordInput.classList.remove('input-error', 'input-success');
    }
}

// Inisialisasi role saat pertama kali load
document.addEventListener('DOMContentLoaded', function() {
    // Set role default
    selectedRole = 'customer';
    
    // Password strength checker for signup
    const signupPasswordInput = document.getElementById('signup-password');
    if (signupPasswordInput) {
        signupPasswordInput.addEventListener('input', function() {
            // Remove spaces
            this.value = this.value.replace(/\s/g, '');
            checkPasswordStrength(this.value);
        });
    }
    
    // Handle Google signup button - check terms first
    const googleSignupBtn = document.getElementById('google-signup-btn');
    const termsCheckbox = document.getElementById('terms');
    if (googleSignupBtn && termsCheckbox) {
        googleSignupBtn.addEventListener('click', function() {
            if (!termsCheckbox.checked) {
                showNotification('Harap centang Syarat & Ketentuan terlebih dahulu', 'error');
                // Highlight checkbox
                termsCheckbox.style.outline = '2px solid #EF4444';
                setTimeout(() => {
                    termsCheckbox.style.outline = '';
                }, 2000);
                return;
            }
            // Redirect to Google OAuth with action=register and role parameter
            window.location.href = '/auth/google?action=register&role=' + selectedRole;
        });
    }
});

// Password strength checker function
function checkPasswordStrength(password) {
    const passwordInput = document.getElementById('signup-password');
    
    if (!passwordInput) return;
    
    // Remove all classes first
    passwordInput.classList.remove('input-error', 'input-warning', 'input-success');
    
    if (!password) {
        return;
    }

    // Mandatory: >=8 characters
    const hasMandatory = password.length >= 8;
    
    // Optional criteria
    let optionalCount = 0;
    if (/[A-Z]/.test(password)) optionalCount++; // Huruf kapital
    if (/[a-z]/.test(password)) optionalCount++; // Huruf kecil
    if (/[0-9]/.test(password)) optionalCount++; // Angka
    if (/[^a-zA-Z0-9]/.test(password)) optionalCount++; // Symbol khusus

    // Determine strength level
    let level = 0;
    if (!hasMandatory) {
        level = 0; // Tidak valid
    } else if (optionalCount >= 4) {
        level = 4; // Sangat Kuat
    } else if (optionalCount >= 3) {
        level = 3; // Kuat
    } else if (optionalCount >= 2) {
        level = 2; // Menengah
    } else if (optionalCount >= 1) {
        level = 1; // Lemah
    } else {
        level = 0; // Tidak valid
    }

    // Apply border color based on level
    if (level === 0 || level === 1) {
        // Lemah = merah (input-error)
        passwordInput.classList.add('input-error');
        console.log('Password strength: LEMAH (level:', level, ')');
    } else if (level === 2) {
        // Menengah = kuning (input-warning)
        passwordInput.classList.add('input-warning');
        console.log('Password strength: MENENGAH (level:', level, ')');
    } else if (level === 3 || level === 4) {
        // Kuat & Sangat Kuat = hijau (input-success)
        passwordInput.classList.add('input-success');
        console.log('Password strength: KUAT/SANGAT KUAT (level:', level, ')');
    }
}