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
        document.getElementById('logoutBtn').addEventListener('click', (e) => {
            e.preventDefault();
            this.showModal('logout');
        });
        
        document.getElementById('cancelLogout').addEventListener('click', () => {
            this.hideModal('logout');
        });
        
        document.getElementById('confirmLogout').addEventListener('click', () => {
            this.handleLogout();
        });
        
        // Kelola Admin link
        document.getElementById('newAdminBtn').addEventListener('click', (e) => {
            e.preventDefault();
            window.location.href = '/admin/kelolaadmin/kelolaadmin';
        });
        
        // Close modal when clicking outside
        Object.values(this.modals).forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.hideModal(modal.id.replace('Modal', ''));
                }
            });
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

// ====== EXPORT MANAGEMENT ======
class ExportManager {
    constructor() {
        this.exportBtn = document.getElementById('exportBtn');
        this.exportMenu = document.getElementById('exportMenu');
        this.exportDropdown = document.querySelector('.export-dropdown');
        this.exportItems = document.querySelectorAll('.export-item');
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        // Toggle export dropdown
        this.exportBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleExportMenu();
        });
        
        // Handle export item clicks
        this.exportItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const exportType = item.getAttribute('data-type');
                this.handleExport(exportType);
                this.hideExportMenu();
            });
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', () => {
            this.hideExportMenu();
        });
        
        // Close dropdown with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.hideExportMenu();
            }
        });
    }
    
    toggleExportMenu() {
        const isVisible = this.exportDropdown.classList.contains('show');
        
        if (isVisible) {
            this.hideExportMenu();
        } else {
            this.showExportMenu();
        }
    }
    
    showExportMenu() {
        this.exportDropdown.classList.add('show');
    }
    
    hideExportMenu() {
        this.exportDropdown.classList.remove('show');
    }
    
    handleExport(exportType) {
        console.log(`Exporting: ${exportType}`);
        
        // Simulasi proses export
        const originalText = this.exportBtn.innerHTML;
        
        // Tampilkan loading state
        this.exportBtn.innerHTML = `
            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="margin-right: 6px;">
                <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0zM7.5 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1zm2 0a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1zm-4 3a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zm0 2a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zm0 2a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5z"/>
            </svg>
            Memproses...
        `;
        this.exportBtn.disabled = true;
        
        // Simulasi delay export
        setTimeout(() => {
            // Kembalikan ke state semula
            this.exportBtn.innerHTML = originalText;
            this.exportBtn.disabled = false;
            
            // Tampilkan notifikasi sukses
            this.showExportSuccess(exportType);
        }, 1500);
    }
    
    showExportSuccess(exportType) {
        // Buat notifikasi sederhana
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #19a86b;
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 1000;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        `;
        
        let typeText = '';
        switch(exportType) {
            case 'hari-ini':
                typeText = 'Pendapatan Hari Ini';
                break;
            case 'minggu-ini':
                typeText = 'Pendapatan Minggu Ini';
                break;
            case 'bulan-ini':
                typeText = 'Pendapatan Bulan Ini';
                break;
        }
        
        notification.innerHTML = `
            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
            </svg>
            ${typeText} berhasil di-export
        `;
        
        document.body.appendChild(notification);
        
        // Hapus notifikasi setelah 3 detik
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// ====== LAPORAN DATA MANAGEMENT ======
class LaporanManager {
    constructor() {
        // Load data from server (injected by LaporanController via Blade)
        const reportData = window.reportData || {};
        
        this.totalRevenue = reportData.totalRevenue || 0;
        this.totalBookings = reportData.totalBookings || 0;
        this.totalMitra = reportData.totalMitra || 0;
        this.totalCustomers = reportData.totalCustomers || 0;
        this.topMitras = reportData.topMitras || [];
        this.monthlyRevenue = reportData.monthlyRevenue || [];
        this.recentWithdrawals = reportData.recentWithdrawals || [];
        
        this.init();
    }
    
    init() {
        this.renderData();
        console.log('Laporan Manager initialized with server data');
        console.log('Total Revenue:', this.totalRevenue);
        console.log('Total Bookings:', this.totalBookings);
        console.log('Total Mitra:', this.totalMitra);
        console.log('Total Customers:', this.totalCustomers);
    }
    
    renderData() {
        // Data is rendered server-side via Blade template
        // This can be used for dynamic updates if needed
        console.log('Using server-rendered data...');
    }
    
    updateSummaryData(newData) {
        // Implementasi update UI jika diperlukan
        console.log('Updating summary data:', newData);
    }
}

// ====== INITIALIZATION ======
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all managers
    const modalManager = new ModalManager();
    const navigationManager = new NavigationManager();
    const userProfileManager = new UserProfileManager();
    const exportManager = new ExportManager();
    const laporanManager = new LaporanManager();
    
    console.log('Laporan Page initialized successfully');
});

// ====== UTILITY FUNCTIONS ======
// Fungsi untuk format angka
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

// Fungsi untuk format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

// Fungsi untuk format persentase
function formatPercentage(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'percent',
        minimumFractionDigits: 1,
        maximumFractionDigits: 1
    }).format(value / 100);
}