// Elements
const backBtn = document.getElementById('backBtn');
const logoutBtn = document.getElementById('logoutBtn');
const photoUpload = document.getElementById('photoUpload');
const avatarImg = document.getElementById('avatarImg');

// All profile data loaded from database via Blade template
// No localStorage needed

// Store selected file temporarily
let pendingAvatarFile = null;

// Photo upload handler
if (photoUpload) {
    photoUpload.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            // Validasi file
            if (!file.type.startsWith('image/')) {
                alert('File harus berupa gambar!');
                photoUpload.value = ''; // Reset input
                return;
            }
            
            // Validasi ukuran file (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file maksimal 5MB!');
                photoUpload.value = ''; // Reset input
                return;
            }
            
            // Preview image immediately
            const reader = new FileReader();
            reader.onload = function(e) {
                if (avatarImg) {
                    avatarImg.src = e.target.result;
                    console.log('👁️ Preview image loaded');
                }
            };
            reader.readAsDataURL(file);
            
            // Store file and show confirmation modal
            pendingAvatarFile = file;
            showAvatarConfirmModal();
        }
    });
}

// Show avatar confirmation modal
function showAvatarConfirmModal() {
    const modal = document.getElementById('avatarConfirmModal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

// Cancel avatar change
function cancelAvatarChange() {
    const modal = document.getElementById('avatarConfirmModal');
    if (modal) {
        modal.style.display = 'none';
    }
    
    // Restore original avatar
    if (avatarImg && avatarImg.dataset.originalSrc) {
        avatarImg.src = avatarImg.dataset.originalSrc;
    }
    
    pendingAvatarFile = null;
    photoUpload.value = ''; // Reset file input
}

// Confirm avatar change
function confirmAvatarChange() {
    if (!pendingAvatarFile) return;
    
    // Close modal
    const modal = document.getElementById('avatarConfirmModal');
    if (modal) {
        modal.style.display = 'none';
    }
    
    // Show loading state
    showNotification('Mengupload foto...', 'info');
    
    // Upload foto menggunakan API
    const formData = new FormData();
    formData.append('photo', pendingAvatarFile);
    
    // Get CSRF token from meta tag or cookie
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    
    fetch('/profile/photo/upload', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            console.log('✅ Profile photo updated and saved to storage');
            showNotification('Foto profil berhasil diperbarui!', 'success');
            
            // Update avatar di halaman ini langsung dengan timestamp baru
            const newAvatarUrl = data.avatar + '?v=' + data.cache_buster;
            const avatarImgs = document.querySelectorAll('#avatarImg, .avatar__image');
            avatarImgs.forEach(img => {
                img.src = newAvatarUrl;
                // Update data attribute with new original src
                img.dataset.originalSrc = newAvatarUrl;
            });
            
            // Broadcast ke tab lain untuk update avatar
            if (typeof BroadcastChannel !== 'undefined') {
                const channel = new BroadcastChannel('profile_update');
                channel.postMessage({
                    type: 'avatar_updated',
                    avatar: newAvatarUrl
                });
                channel.close();
            }
            
            console.log('🔄 Avatar updated to:', newAvatarUrl);
        } else {
            // If upload failed, restore original avatar
            if (avatarImg && avatarImg.dataset.originalSrc) {
                avatarImg.src = avatarImg.dataset.originalSrc;
            }
            showNotification('Gagal mengupload foto: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error uploading photo:', error);
        // Restore original avatar on error
        if (avatarImg && avatarImg.dataset.originalSrc) {
            avatarImg.src = avatarImg.dataset.originalSrc;
        }
        showNotification('Terjadi kesalahan saat mengupload foto. Silakan coba lagi.', 'error');
    })
    .finally(() => {
        // Clear pending file
        pendingAvatarFile = null;
        photoUpload.value = ''; // Reset file input
    });
}

// Logout modal functions
function showLogoutModal() {
    const modal = document.getElementById('logoutModalOverlay');
    if (modal) {
        modal.style.display = 'flex';
    }
}

function cancelLogout() {
    const modal = document.getElementById('logoutModalOverlay');
    if (modal) {
        modal.style.display = 'none';
    }
}

function confirmLogout() {
    // Logout via POST request
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/logout';
    
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken.getAttribute('content');
        form.appendChild(csrfInput);
    }
    
    document.body.appendChild(form);
    form.submit();
}

// Logout handler
if (logoutBtn) {
    logoutBtn.addEventListener('click', () => {
        showLogoutModal();
    });
}

// Notification system
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="ph ph-${getNotificationIcon(type)}"></i>
        <span>${message}</span>
    `;
    
    // Tambahkan styling inline dengan support untuk info type
    const colors = {
        success: { border: '#10B981', text: '#065F46' },
        error: { border: '#EF4444', text: '#7F1D1D' },
        info: { border: '#3B82F6', text: '#1E3A8A' }
    };
    
    const colorScheme = colors[type] || colors.success;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        z-index: 1001;
        animation: slideInRight 0.3s ease;
        border-left: 4px solid ${colorScheme.border};
        color: ${colorScheme.text};
    `;
    
    document.body.appendChild(notification);
    
    // Hapus setelah 3 detik
    setTimeout(() => {
        notification.style.animation = 'slideInRight 0.3s ease reverse';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

function getNotificationIcon(type) {
    switch(type) {
        case 'success': return 'check-circle';
        case 'error': return 'warning-circle';
        case 'info': return 'info';
        default: return 'check-circle';
    }
}

// All data loaded from database - no initialization needed
console.log('✅ User Profile Page Initialized');
