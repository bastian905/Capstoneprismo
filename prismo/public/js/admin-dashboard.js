// ====== MODAL MANAGEMENT ======
class ModalManager {
    constructor() {
        this.modals = {
            logout: document.getElementById('logoutModal')
        };
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        // Logout modal
        const logoutBtn = document.getElementById('logoutBtn');
        const cancelLogout = document.getElementById('cancelLogout');
        const confirmLogout = document.getElementById('confirmLogout');
        
        if (logoutBtn) {
            logoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.showModal('logout');
            });
        }
        
        if (cancelLogout) {
            cancelLogout.addEventListener('click', () => {
                this.hideModal('logout');
            });
        }
        
        if (confirmLogout) {
            confirmLogout.addEventListener('click', () => {
                this.handleLogout();
            });
        }
        
        // Kelola Admin link
        const newAdminBtn = document.getElementById('newAdminBtn');
        if (newAdminBtn) {
            newAdminBtn.addEventListener('click', (e) => {
                e.preventDefault();
                window.location.href = '/admin/kelolaadmin/kelolaadmin';
            });
        }
        
        // Close modal when clicking outside
        Object.values(this.modals).forEach(modal => {
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        this.hideModal(modal.id.replace('Modal', ''));
                    }
                });
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.hideAllModals();
            }
        });
    }
    
    showModal(modalName) {
        const modal = this.modals[modalName];
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }
    
    hideModal(modalName) {
        const modal = this.modals[modalName];
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }
    
    hideAllModals() {
        Object.keys(this.modals).forEach(modalName => {
            this.hideModal(modalName);
        });
    }
    
    handleLogout() {
        console.log('Logging out...');
        
        const confirmBtn = document.getElementById('confirmLogout');
        const originalText = confirmBtn.textContent;
        confirmBtn.textContent = 'Logging out...';
        confirmBtn.disabled = true;
        
        this.hideModal('logout');
        
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
}

// ====== NAVIGATION MANAGEMENT ======
class NavigationManager {
    constructor() {
        this.navItems = document.querySelectorAll(".nav-item");
        this.init();
    }
    
    init() {
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        this.navItems.forEach((item) => {
            item.addEventListener("click", (e) => {
                // Hanya prevent default jika href adalah "#" atau kosong
                if (item.getAttribute('href') === '#' || !item.getAttribute('href')) {
                    e.preventDefault();
                }
                
                // Update state aktif
                this.setActiveItem(item);
                
                // Jika href adalah "#" atau kosong, tetap di halaman yang sama
                if (item.getAttribute('href') === '#' || !item.getAttribute('href')) {
                    console.log('Navigasi internal:', item.textContent);
                }
            });
        });
    }
    
    setActiveItem(activeItem) {
        this.navItems.forEach((item) => item.classList.remove("active"));
        activeItem.classList.add("active");
    }
}

// ====== USER PROFILE MANAGEMENT ======
class UserProfileManager {
    constructor() {
        this.userAvatar = document.querySelector('.user-avatar');
        this.dropdownMenu = document.querySelector('.dropdown-menu');
        this.init();
    }
    
    init() {
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        if (this.userAvatar && this.dropdownMenu) {
            this.userAvatar.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleDropdown();
            });
            
            // Tutup dropdown ketika klik di luar
            document.addEventListener('click', () => {
                this.hideDropdown();
            });
        }
    }
    
    toggleDropdown() {
        const isVisible = this.dropdownMenu.style.visibility === 'visible';
        
        if (isVisible) {
            this.hideDropdown();
        } else {
            this.showDropdown();
        }
    }
    
    showDropdown() {
        this.dropdownMenu.style.opacity = '1';
        this.dropdownMenu.style.visibility = 'visible';
        this.dropdownMenu.style.transform = 'translateY(0)';
    }
    
    hideDropdown() {
        this.dropdownMenu.style.opacity = '0';
        this.dropdownMenu.style.visibility = 'hidden';
        this.dropdownMenu.style.transform = 'translateY(-10px)';
    }
}

// ====== INITIALIZATION ======
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all managers
    const modalManager = new ModalManager();
    const navigationManager = new NavigationManager();
    const userProfileManager = new UserProfileManager();
    
    // Export earnings button
    const exportBtn = document.getElementById('exportEarningsBtn');
    const periodSelect = document.getElementById('periodSelect');
    
    if (exportBtn && periodSelect) {
        exportBtn.addEventListener('click', function() {
            const period = periodSelect.value;
            const date = new Date().toISOString().split('T')[0];
            
            // Show loading state
            exportBtn.disabled = true;
            exportBtn.innerHTML = '<span>Exporting...</span>';
            
            // Trigger download
            window.location.href = `/admin/reports/earnings-export?period=${period}&date=${date}`;
            
            // Reset button after delay
            setTimeout(() => {
                exportBtn.disabled = false;
                exportBtn.innerHTML = `
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                    </svg>
                    Export Excel
                `;
            }, 2000);
        });
    }
    
    console.log('Dashboard Admin initialized successfully');
});

// ====== UTILITY FUNCTIONS ======
// Fungsi untuk menampilkan notifikasi
function showNotification(message, type = 'info') {
    // Implementasi notifikasi bisa ditambahkan di sini
    console.log(`${type.toUpperCase()}: ${message}`);
}

// Fungsi untuk format angka
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

// Fungsi untuk format tanggal
function formatDate(date) {
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
}