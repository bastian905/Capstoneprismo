// Password visibility toggle
document.addEventListener('DOMContentLoaded', function() {
    const toggleButtons = document.querySelectorAll('.toggle-password');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const eyeOpen = this.querySelector('.eye-open');
            const eyeClosed = this.querySelector('.eye-closed');
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.style.display = 'none';
                eyeClosed.style.display = 'block';
            } else {
                input.type = 'password';
                eyeOpen.style.display = 'block';
                eyeClosed.style.display = 'none';
            }
        });
    });

    // Dynamic label for current password
    const currentPasswordInput = document.getElementById('currentPassword');
    const currentPasswordLabel = document.querySelector('label[for="currentPassword"]');
    if (currentPasswordInput && currentPasswordLabel) {
        currentPasswordInput.addEventListener('input', function() {
            // Remove spaces
            this.value = this.value.replace(/\s/g, '');
            
            if (this.value) {
                currentPasswordLabel.innerHTML = 'Password Saat Ini';
            } else {
                currentPasswordLabel.innerHTML = 'Password Saat Ini <span class="required">*</span>';
            }
        });
    }

    // Dynamic label for new password
    const newPasswordInput = document.getElementById('newPassword');
    const newPasswordLabel = document.querySelector('label[for="newPassword"]');
    if (newPasswordInput && newPasswordLabel) {
        newPasswordInput.addEventListener('input', function() {
            // Remove spaces
            this.value = this.value.replace(/\s/g, '');
            
            if (this.value) {
                newPasswordLabel.innerHTML = 'Password Baru';
            } else {
                newPasswordLabel.innerHTML = 'Password Baru <span class="required">*</span>';
            }
            checkPasswordStrength(this.value);
        });
    }

    // Dynamic label for confirm password
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const confirmPasswordLabel = document.querySelector('label[for="confirmPassword"]');
    if (confirmPasswordInput && confirmPasswordLabel) {
        confirmPasswordInput.addEventListener('input', function() {
            // Remove spaces
            this.value = this.value.replace(/\s/g, '');
            
            if (this.value) {
                confirmPasswordLabel.innerHTML = 'Konfirmasi Password Baru';
            } else {
                confirmPasswordLabel.innerHTML = 'Konfirmasi Password Baru <span class="required">*</span>';
            }
        });
    }

    // Form submission
    const form = document.getElementById('changePasswordForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }

    // Real-time validation for confirm password (already handled above with dynamic label)
    if (confirmPasswordInput && newPasswordInput) {
        // Additional real-time matching validation
        const originalConfirmInput = confirmPasswordInput.addEventListener;
        confirmPasswordInput.addEventListener('input', function() {
            if (this.value && this.value !== newPasswordInput.value) {
                this.classList.add('error');
                this.classList.remove('success');
            } else if (this.value === newPasswordInput.value && this.value !== '') {
                hideError('confirmPasswordError');
                this.classList.remove('error');
                this.classList.add('success');
            }
        });
    }
});

function checkPasswordStrength(password) {
    const strengthBars = document.querySelectorAll('.strength-bar');
    const strengthText = document.querySelector('.strength-text');
    
    if (!password) {
        strengthBars.forEach(bar => {
            bar.classList.remove('active', 'weak', 'medium', 'strong', 'very-strong');
        });
        strengthText.textContent = 'Kekuatan password';
        strengthText.className = 'strength-text';
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
        // Tidak memenuhi wajib (<8 karakter)
        level = 0; // Tidak valid
    } else if (optionalCount >= 4) {
        // Wajib + 4 opsional = Sangat Kuat
        level = 4;
    } else if (optionalCount >= 3) {
        // Wajib + 3 opsional = Kuat
        level = 3;
    } else if (optionalCount >= 2) {
        // Wajib + 2 opsional = Menengah
        level = 2;
    } else if (optionalCount >= 1) {
        // Wajib + 1 opsional = Lemah
        level = 1;
    } else {
        // Hanya wajib, tanpa opsional = Tidak valid
        level = 0;
    }

    // Update bars
    strengthBars.forEach((bar, index) => {
        bar.classList.remove('active', 'weak', 'medium', 'strong', 'very-strong');
        if (index < level) {
            bar.classList.add('active');
            if (level === 1) {
                bar.classList.add('weak');
            } else if (level === 2) {
                bar.classList.add('medium');
            } else if (level === 3) {
                bar.classList.add('strong');
            } else if (level === 4) {
                bar.classList.add('very-strong');
            }
        }
    });

    // Update text
    if (!hasMandatory) {
        strengthText.textContent = 'Minimal 8 karakter';
        strengthText.className = 'strength-text weak';
    } else if (level === 0) {
        strengthText.textContent = 'Terlalu lemah';
        strengthText.className = 'strength-text weak';
    } else if (level === 1) {
        strengthText.textContent = 'Lemah';
        strengthText.className = 'strength-text weak';
    } else if (level === 2) {
        strengthText.textContent = 'Menengah';
        strengthText.className = 'strength-text medium';
    } else if (level === 3) {
        strengthText.textContent = 'Kuat';
        strengthText.className = 'strength-text strong';
    } else if (level === 4) {
        strengthText.textContent = 'Sangat Kuat';
        strengthText.className = 'strength-text very-strong';
    }
}



function validateForm() {
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    let isValid = true;

    // Clear previous errors
    clearAllErrors();

    // Validate current password
    if (!currentPassword) {
        showError('currentPasswordError', 'Password saat ini harus diisi');
        document.getElementById('currentPassword').classList.add('error');
        isValid = false;
    }

    // Validate new password
    if (!newPassword) {
        document.getElementById('newPassword').classList.add('error');
        isValid = false;
    } else if (newPassword.length < 8) {
        // Must be >=8 characters
        document.getElementById('newPassword').classList.add('error');
        isValid = false;
    } else {
        // Check optional criteria
        let optionalCount = 0;
        if (/[A-Z]/.test(newPassword)) optionalCount++;
        if (/[a-z]/.test(newPassword)) optionalCount++;
        if (/[0-9]/.test(newPassword)) optionalCount++;
        if (/[^a-zA-Z0-9]/.test(newPassword)) optionalCount++;
        
        if (optionalCount < 1) {
            // Must have at least 1 optional criteria
            document.getElementById('newPassword').classList.add('error');
            isValid = false;
        } else if (newPassword === currentPassword) {
            document.getElementById('newPassword').classList.add('error');
            isValid = false;
        }
    }

    // Validate confirm password
    if (!confirmPassword) {
        document.getElementById('confirmPassword').classList.add('error');
        isValid = false;
    } else if (confirmPassword !== newPassword) {
        showError('confirmPasswordError', 'Password tidak cocok');
        document.getElementById('confirmPassword').classList.add('error');
        isValid = false;
    }

    return isValid;
}

async function handleFormSubmit(e) {
    e.preventDefault();

    if (!validateForm()) {
        return;
    }

    const submitButton = e.target.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    
    try {
        // Show loading state
        submitButton.classList.add('loading');
        submitButton.disabled = true;

        const formData = new FormData(e.target);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        const response = await fetch('/profile/change-password', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await response.json();

        if (response.ok) {
            // Show success modal
            document.getElementById('successModal').style.display = 'flex';
            
            // Reset form
            e.target.reset();
            clearAllErrors();
            
            // Reset password strength indicator
            const strengthBars = document.querySelectorAll('.strength-bar');
            strengthBars.forEach(bar => {
                bar.classList.remove('active', 'weak', 'medium', 'strong');
            });
            document.querySelector('.strength-text').textContent = 'Kekuatan password';
            document.querySelector('.strength-text').className = 'strength-text';
            
            // Reset requirements
            document.querySelectorAll('.requirement').forEach(req => {
                req.classList.remove('met');
            });
        } else {
            // Handle validation errors
            if (data.errors) {
                if (data.errors.current_password) {
                    showError('currentPasswordError', data.errors.current_password[0]);
                    document.getElementById('currentPassword').classList.add('error');
                }
                if (data.errors.new_password) {
                    showError('newPasswordError', data.errors.new_password[0]);
                    document.getElementById('newPassword').classList.add('error');
                }
                if (data.errors.confirm_password) {
                    showError('confirmPasswordError', data.errors.confirm_password[0]);
                    document.getElementById('confirmPassword').classList.add('error');
                }
            } else {
                showError('currentPasswordError', data.message || 'Terjadi kesalahan. Silakan coba lagi.');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        showError('currentPasswordError', 'Terjadi kesalahan koneksi. Silakan coba lagi.');
    } finally {
        // Remove loading state
        submitButton.classList.remove('loading');
        submitButton.disabled = false;
    }
}

function showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.textContent = message;
    }
}

function hideError(elementId) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.textContent = '';
    }
}

function clearAllErrors() {
    const errorElements = document.querySelectorAll('.error-message');
    errorElements.forEach(el => el.textContent = '');
    
    const inputs = document.querySelectorAll('.form-input');
    inputs.forEach(input => {
        input.classList.remove('error', 'success');
    });
}

function goBack() {
    window.history.back();
}

function redirectToProfile() {
    // Determine if customer or mitra based on current URL
    const isCustomer = window.location.pathname.includes('/customer/');
    if (isCustomer) {
        window.location.href = '/customer/profil/uprofil';
    } else {
        window.location.href = '/mitra/profil/profil';
    }
}
