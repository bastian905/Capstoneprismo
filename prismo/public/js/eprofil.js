// Elements
const backBtn = document.getElementById('backBtn');
const editForm = document.getElementById('editForm');
const submitBtn = document.getElementById('submitBtn');
const successModal = document.getElementById('successModal');
const okBtn = document.getElementById('okBtn');

// Form inputs
const fullNameInput = document.getElementById('fullName');
const phoneInput = document.getElementById('phone');

// Back button - kembali ke halaman profil
backBtn.addEventListener('click', () => {
    window.location.href = '/customer/profil/uprofil';
});

// Handle form submission
editForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Validasi input
    const fullName = fullNameInput.value.trim();
    const phone = phoneInput.value.trim();
    
    if (!fullName) {
        showNotification('Nama lengkap harus diisi!', 'error');
        return;
    }
    
    // Validasi phone format (jika diisi)
    if (phone && !isValidPhone(phone)) {
        showNotification('Format nomor telepon tidak valid!', 'error');
        return;
    }
    
    // Show loading
    submitBtn.classList.add('loading');
    
    try {
        // Update profile ke database
        await updateUserProfile({
            name: fullName,
            phone: phone
        });
        
        // Hide loading
        submitBtn.classList.remove('loading');
        
        // Tampilkan modal success
        successModal.classList.add('active');
        
    } catch (error) {
        console.error('Error:', error);
        submitBtn.classList.remove('loading');
        showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
    }
});

// OK button di modal - redirect ke /customer/profil/uprofil
okBtn.addEventListener('click', () => {
    successModal.classList.remove('active');
    window.location.href = '/customer/profil/uprofil';
});

// Close modal jika klik di luar modal content
successModal.addEventListener('click', (e) => {
    if (e.target === successModal) {
        successModal.classList.remove('active');
        window.location.href = '/customer/profil/uprofil';
    }
});

// Fungsi API untuk update profile
function updateUserProfile(data) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    
    // Gunakan FormData untuk menghindari CSRF issue di API routes
    const formData = new FormData();
    formData.append('name', data.name);
    if (data.phone) {
        formData.append('phone', data.phone);
    }
    
    const headers = {};
    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken.content;
    }
    
    return fetch('/api/customer/profile', {
        method: 'POST',
        headers: headers,
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to update profile');
        }
        return response.json();
    })
    .then(result => {
        console.log('Profile updated:', result);
        return { success: true, data: result.data };
    })
    .catch(error => {
        console.error('Profile update error:', error);
        throw error;
    });
}

// Validasi phone
function isValidPhone(phone) {
    if (!phone) return true; // Allow empty phone
    // Format: +62 xxx-xxxx-xxxx atau 08xxxxxxxxxx atau +628xxxxxxxxxx
    const phoneRegex = /^(\+62|62|0)[0-9]{9,13}$/;
    const cleanPhone = phone.replace(/[\s-]/g, ''); // Hapus spasi dan dash
    return phoneRegex.test(cleanPhone);
}

// Notification system
function showNotification(message, type = 'success') {
    // Buat elemen notifikasi
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="ph ph-${getNotificationIcon(type)}"></i>
        <span>${message}</span>
    `;
    
    // Tambahkan ke body
    document.body.appendChild(notification);
    
    // Hapus setelah 3 detik
    setTimeout(() => {
        notification.style.animation = 'slideInRight 0.3s ease reverse';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Helper function untuk icon notifikasi berdasarkan type
function getNotificationIcon(type) {
    switch(type) {
        case 'success': return 'check-circle';
        case 'error': return 'warning-circle';
        case 'info': return 'info';
        default: return 'check-circle';
    }
}

// Prevent form submission dengan Enter di tengah input (optional)
editForm.addEventListener('keypress', (e) => {
    if (e.key === 'Enter' && e.target.tagName !== 'BUTTON') {
        e.preventDefault();
    }
});

// Auto focus ke input pertama saat halaman dimuat
window.addEventListener('load', () => {
    fullNameInput.focus();
});

// Tambahkan CSS untuk modal guest (jika belum ada)
const additionalStyles = `
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .modal.active {
        opacity: 1;
        visibility: visible;
    }
    
    .modal-content {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        text-align: center;
    }
    
    .modal-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    
    .modal-icon.success {
        color: #10B981;
    }
    
    .modal-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
        justify-content: center;
    }
    
    .notification {
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
        border-left: 4px solid;
    }
    
    .notification.success {
        border-left-color: #10B981;
        color: #065F46;
    }
    
    .notification.error {
        border-left-color: #EF4444;
        color: #7F1D1D;
    }
    
    .notification.info {
        border-left-color: #3B82F6;
        color: #1E3A8A;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .btn-primary {
        background: #3B82F6;
        color: white;
    }
    
    .btn-primary:hover {
        background: #2563EB;
    }
    
    .btn-secondary {
        background: #6B7280;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #4B5563;
    }
`;

// Inject additional styles jika belum ada
if (!document.querySelector('#additional-styles')) {
    const styleSheet = document.createElement('style');
    styleSheet.id = 'additional-styles';
    styleSheet.textContent = additionalStyles;
    document.head.appendChild(styleSheet);
}
