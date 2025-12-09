// ========== VOUCHER DATA MANAGEMENT ==========
let allVouchers = [];
let claimedVouchers = [];
let usedVouchers = [];
let currentVoucher = null;

// ========== INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', function() {
    loadUserProfile();
    loadVouchersFromStorage();
    initializeTabs();
    renderVouchers();
    initNotificationPermission();
    initializeMobileMenu();
    
    console.log('✅ Voucher page fully initialized');
});

// ========== MOBILE MENU ==========
function initializeMobileMenu() {
    const menuToggle = document.getElementById('menuToggle');
    const mainNav = document.getElementById('mainNav');
    
    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!menuToggle.contains(event.target) && !mainNav.contains(event.target)) {
                mainNav.classList.remove('active');
            }
        });
        
        // Close menu when clicking a nav link
        const navLinks = mainNav.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                mainNav.classList.remove('active');
            });
        });
        
        console.log('📱 Mobile menu initialized');
    }
}

// ========== NOTIFICATION PERMISSION ==========
function initNotificationPermission() {
    const notificationPopup = document.querySelector('.notification-popup');
    const btnBlokir = document.querySelector('.btn-blokir');
    const btnIzinkan = document.querySelector('.btn-izinkan');
    
    // Check if permission already set
    const savedPermission = localStorage.getItem('notificationPermission');
    if (savedPermission && notificationPopup) {
        notificationPopup.style.display = 'none';
    } else if (notificationPopup) {
        // Show notification popup
        notificationPopup.style.display = 'flex';
    }
    
    // Notification Popup (Permission Request)
    if (btnBlokir && notificationPopup) {
        btnBlokir.addEventListener('click', () => {
            notificationPopup.style.display = 'none';
            localStorage.setItem('notificationPermission', 'blocked');
            console.log('🚫 Notifications blocked');
        });
    }
    
    if (btnIzinkan && notificationPopup) {
        btnIzinkan.addEventListener('click', () => {
            notificationPopup.style.display = 'none';
            localStorage.setItem('notificationPermission', 'granted');
            if ('Notification' in window) {
                Notification.requestPermission().then(permission => {
                    console.log('✅ Notification permission:', permission);
                });
            }
        });
    }
    
    // Notification panel toggle is now handled by notification-system.js
    console.log('🔔 Using shared notification-system.js');
}

// ========== USER PROFILE LOADER ==========
function loadUserProfile() {
    // Avatar images loaded from database via Blade template
    // User name is already set in Blade template, no need to reload
    console.log('✅ Voucher page: User profile loaded from server');
}

// ========== STORAGE MANAGEMENT ==========
function loadVouchersFromStorage() {
    // Load real data from server - NO MORE MOCK DATA
    if (window.availableVouchers) {
        allVouchers = [...window.availableVouchers];
    } else {
        allVouchers = [];
    }
    
    if (window.myVouchers) {
        claimedVouchers = [...window.myVouchers];
    } else {
        claimedVouchers = [];
    }
    
    if (window.usedVouchers) {
        usedVouchers = [...window.usedVouchers];
    } else {
        usedVouchers = [];
    }
}

function saveVouchersToStorage() {
    localStorage.setItem('allVouchers', JSON.stringify(allVouchers));
    localStorage.setItem('claimedVouchers', JSON.stringify(claimedVouchers));
    localStorage.setItem('usedVouchers', JSON.stringify(usedVouchers));
}

// ========== TAB SYSTEM ==========
function initializeTabs() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabName = btn.dataset.tab;
            switchTab(tabName);
        });
    });
}

function switchTab(tabName) {
    // Update buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.tab === tabName) {
            btn.classList.add('active');
        }
    });
    
    // Update content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById(tabName).classList.add('active');
    
    // Render appropriate vouchers
    renderVouchers();
}

// ========== RENDER VOUCHERS ==========
function renderVouchers() {
    const activeTab = document.querySelector('.tab-btn.active').dataset.tab;
    
    if (activeTab === 'available') {
        renderAvailableVouchers();
    } else if (activeTab === 'claimed') {
        renderClaimedVouchers();
    } else if (activeTab === 'used') {
        renderUsedVouchers();
    }
}

function renderAvailableVouchers() {
    const container = document.getElementById('availableVouchers');
    const noData = document.getElementById('noAvailableVouchers');
    
    const availableVouchers = allVouchers.filter(v => 
        !v.claimed && !v.used && !isExpired(v.expiry)
    );
    
    if (availableVouchers.length === 0) {
        container.innerHTML = '';
        noData.style.display = 'block';
        return;
    }
    
    noData.style.display = 'none';
    container.innerHTML = availableVouchers.map(voucher => createVoucherCard(voucher, 'available')).join('');
    
    // Attach event listeners after rendering
    attachVoucherCardListeners();
}

function renderClaimedVouchers() {
    const container = document.getElementById('claimedVouchers');
    const noData = document.getElementById('noClaimedVouchers');
    
    if (claimedVouchers.length === 0) {
        container.innerHTML = '';
        noData.style.display = 'block';
        return;
    }
    
    noData.style.display = 'none';
    container.innerHTML = claimedVouchers.map(voucher => createVoucherCard(voucher, 'claimed')).join('');
    
    // Attach event listeners after rendering
    attachVoucherCardListeners();
}

function renderUsedVouchers() {
    const container = document.getElementById('usedVouchers');
    const noData = document.getElementById('noUsedVouchers');
    
    if (usedVouchers.length === 0) {
        container.innerHTML = '';
        noData.style.display = 'block';
        return;
    }
    
    noData.style.display = 'none';
    container.innerHTML = usedVouchers.map(voucher => createVoucherCard(voucher, 'used')).join('');
    
    // Attach event listeners after rendering
    attachVoucherCardListeners();
}

// Attach click event listeners to voucher cards
function attachVoucherCardListeners() {
    const voucherCards = document.querySelectorAll('.voucher-card:not(.used)');
    voucherCards.forEach(card => {
        const voucherId = card.getAttribute('data-voucher-id');
        if (voucherId) {
            card.style.cursor = 'pointer';
            card.onclick = function(e) {
                // Prevent if clicking on button
                if (e.target.closest('button')) {
                    return;
                }
                showVoucherDetail(voucherId);
            };
        }
    });
    
    console.log('✅ Event listeners attached to', voucherCards.length, 'voucher cards');
}

// ========== CREATE VOUCHER CARD ==========
function createVoucherCard(voucher, status) {
    const expired = isExpired(voucher.expiry);
    const cardClass = status === 'used' ? 'voucher-card used' : 
                     status === 'claimed' ? 'voucher-card claimed' : 'voucher-card';
    
    // Use voucher color or default
    const voucherColor = voucher.color || '#1c98f5';
    
    return `
        <div class="${cardClass}" data-voucher-id="${voucher.id}">
            ${status === 'claimed' ? '<div class="status-badge">Diklaim</div>' : ''}
            ${status === 'used' ? '<div class="status-badge used">Terpakai</div>' : ''}
            
            <div class="voucher-header" style="background: ${voucherColor};">
                <div class="voucher-title">${voucher.title}</div>
                <div class="voucher-discount">${voucher.discount}</div>
            </div>
            
            <div class="voucher-body">
                <div class="voucher-info">
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <span>Berlaku hingga ${formatDate(voucher.expiry)}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Min. Transaksi Rp ${formatNumber(voucher.minTransaction)}</span>
                    </div>
                </div>
                
                <div class="voucher-code">
                    <div class="code-label">Kode Voucher</div>
                    <div class="code-value">${voucher.code}</div>
                </div>
                
                <div class="voucher-footer">
                    ${getVoucherButton(voucher, status, expired)}
                </div>
            </div>
        </div>
    `;
}

function getVoucherButton(voucher, status, expired) {
    if (status === 'used') {
        return '';
    }
    
    if (expired) {
        return '<button class="btn-expired" disabled>Kadaluarsa</button>';
    }
    
    if (status === 'claimed') {
        return `
            <div class="voucher-info-text">
                <i class="fas fa-info-circle"></i>
                <span>Voucher sudah bisa dipakai, dan otomatis hangus saat digunakan</span>
            </div>
        `;
    }
    
    return `
        <button class="btn-claim" data-voucher-id="${voucher.id}">
            <i class="fas fa-gift"></i> Klaim
        </button>
    `;
}

function getVoucherTypeName(type) {
    const types = {
        'discount': 'Diskon',
        'cashback': 'Cashback',
        'special': 'Spesial'
    };
    return types[type] || 'Voucher';
}

// ========== VOUCHER DETAIL MODAL ==========
function showVoucherDetail(voucherId) {
    console.log('showVoucherDetail called with ID:', voucherId, typeof voucherId);
    console.log('Available arrays:', {
        allVouchers: allVouchers.length,
        claimedVouchers: claimedVouchers.length,
        usedVouchers: usedVouchers.length
    });
    
    // Search in all arrays (available, claimed, used)
    // Convert voucherId to number for comparison
    const id = typeof voucherId === 'string' ? parseInt(voucherId) : voucherId;
    currentVoucher = allVouchers.find(v => v.id == id) || 
                     claimedVouchers.find(v => v.id == id) || 
                     usedVouchers.find(v => v.id == id);
    
    console.log('Found voucher:', currentVoucher);
    
    if (!currentVoucher) {
        console.error('Voucher not found with ID:', id);
        return;
    }
    
    const modal = document.getElementById('voucherModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalDiscount = document.getElementById('modalDiscount');
    const modalExpiry = document.getElementById('modalExpiry');
    const modalMinTransaction = document.getElementById('modalMinTransaction');
    const modalCode = document.getElementById('modalCode');
    const modalClaimBtn = document.getElementById('modalClaimBtn');
    const btnCopy = modal.querySelector('.btn-copy');
    
    // Apply voucher color to modal elements
    const voucherColor = currentVoucher.color || '#1c98f5';
    modalDiscount.style.color = voucherColor;
    
    modalTitle.textContent = currentVoucher.title;
    modalDiscount.textContent = currentVoucher.discount;
    modalExpiry.textContent = formatDate(currentVoucher.expiry);
    modalMinTransaction.textContent = 'Rp ' + formatNumber(currentVoucher.minTransaction);
    modalCode.textContent = currentVoucher.code;
    
    // Determine if voucher is claimed or used
    // Voucher is claimed if it's in claimedVouchers or usedVouchers array
    const isClaimed = claimedVouchers.some(v => v.id === currentVoucher.id) || 
                      usedVouchers.some(v => v.id === currentVoucher.id);
    const isUsed = usedVouchers.some(v => v.id === currentVoucher.id);
    const expired = isExpired(currentVoucher.expiry);
    
    console.log('Debug Voucher:', {
        id: currentVoucher.id,
        title: currentVoucher.title,
        isClaimed: isClaimed,
        isUsed: isUsed,
        expired: expired,
        expiry: currentVoucher.expiry
    });
    
    // Show/hide copy button based on voucher status
    // Only show copy button for claimed vouchers (Voucher Saya)
    if (btnCopy) {
        if (isClaimed) {
            btnCopy.style.display = 'flex';
        } else {
            btnCopy.style.display = 'none';
        }
    }
    
    // Update button
    if (isUsed) {
        console.log('Button state: Used');
        modalClaimBtn.innerHTML = '<i class="fas fa-check"></i> Sudah Terpakai';
        modalClaimBtn.classList.add('btn-claimed');
        modalClaimBtn.onclick = null;
        modalClaimBtn.style.pointerEvents = 'none';
    } else if (isClaimed) {
        console.log('Button state: Claimed');
        modalClaimBtn.innerHTML = '<i class="fas fa-check"></i> Sudah Diklaim';
        modalClaimBtn.classList.add('btn-claimed');
        modalClaimBtn.onclick = null;
        modalClaimBtn.style.pointerEvents = 'none';
    } else if (expired) {
        console.log('Button state: Expired');
        modalClaimBtn.innerHTML = 'Kadaluarsa';
        modalClaimBtn.classList.add('btn-expired');
        modalClaimBtn.onclick = null;
        modalClaimBtn.style.pointerEvents = 'none';
    } else {
        console.log('Button state: Available - Setting onclick');
        modalClaimBtn.innerHTML = '<i class="fas fa-gift"></i> Klaim Voucher';
        modalClaimBtn.classList.remove('btn-claimed', 'btn-expired');
        modalClaimBtn.style.pointerEvents = 'auto';
        modalClaimBtn.onclick = claimVoucher;
    }
    
    modal.classList.add('active');
}

function closeVoucherModal() {
    document.getElementById('voucherModal').classList.remove('active');
    currentVoucher = null;
}

// ========== CLAIM VOUCHER ==========
async function claimVoucher() {
    if (!currentVoucher) return;
    
    try {
        const response = await fetch(`/api/vouchers/${currentVoucher.id}/claim`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            console.error('Claim error:', data);
            alert(data.message || 'Gagal mengklaim voucher');
            return;
        }
        
        // Close detail modal
        closeVoucherModal();
        
        // Show success modal
        showSuccessModal();
        
        // Reload page to refresh voucher lists
        setTimeout(() => {
            window.location.reload();
        }, 1500);
        
    } catch (error) {
        console.error('Error claiming voucher:', error);
        alert('Terjadi kesalahan saat mengklaim voucher');
    }
}

// ========== USE VOUCHER ==========
function useVoucher(voucherId) {
    const voucher = allVouchers.find(v => v.id === voucherId);
    if (!voucher) return;
    
    // Save to localStorage for booking page
    localStorage.setItem('activeVoucher', JSON.stringify(voucher));
    
    // Redirect to booking page
    window.location.href = '/customer/atur-booking/booking';
}

// ========== COPY VOUCHER CODE ==========
function copyVoucherCode() {
    const codeElement = document.getElementById('modalCode');
    const code = codeElement.textContent;
    
    // Fallback for older browsers
    const textArea = document.createElement('textarea');
    textArea.value = code;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    document.body.appendChild(textArea);
    textArea.select();
    
    try {
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        // Show success feedback
        const btn = event.target.closest('.btn-copy');
        const icon = btn.querySelector('i');
        
        icon.classList.remove('fa-copy');
        icon.classList.add('fa-check');
        btn.style.color = '#4CAF50';
        
        setTimeout(() => {
            icon.classList.remove('fa-check');
            icon.classList.add('fa-copy');
            btn.style.color = '';
        }, 2000);
    } catch (err) {
        document.body.removeChild(textArea);
        alert('Gagal menyalin kode voucher');
    }
}

// ========== SUCCESS MODAL ==========
function showSuccessModal() {
    document.getElementById('successModal').classList.add('active');
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.remove('active');
}

// ========== UTILITY FUNCTIONS ==========
function isExpired(expiryDate) {
    return new Date(expiryDate) < new Date();
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}

function formatNumber(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// ========== ADMIN FUNCTION (untuk menambah voucher baru) ==========
function addNewVoucher(voucherData) {
    const newVoucher = {
        id: 'VOUC' + (allVouchers.length + 1).toString().padStart(3, '0'),
        ...voucherData,
        claimed: false,
        used: false,
        status: 'available'
    };
    
    allVouchers.push(newVoucher);
    saveVouchersToStorage();
    renderVouchers();
    
    return newVoucher;
}

// Export untuk digunakan di halaman admin
window.voucherManager = {
    addNewVoucher,
    getAllVouchers: () => allVouchers,
    getClaimedVouchers: () => allVouchers.filter(v => v.claimed),
    getUsedVouchers: () => allVouchers.filter(v => v.used)
};

// Export global functions for HTML onclick handlers
window.showVoucherDetail = showVoucherDetail;
window.closeVoucherModal = closeVoucherModal;
window.claimVoucher = claimVoucher;
window.copyVoucherCode = copyVoucherCode;
window.closeSuccessModal = closeSuccessModal;

console.log('📦 Voucher manager and functions exported to window');
console.log('✅ All functions available globally');

