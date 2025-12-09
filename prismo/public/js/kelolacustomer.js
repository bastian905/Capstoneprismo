// Kelola Customer - JavaScript Functionality dengan Modal System

// Pagination state
let currentPage = 1;
const itemsPerPage = 30;

// ===== DATA FROM SERVER =====
const mockData = {
    customers: window.customersData || []
};

// ===== MODAL MANAGEMENT =====
class ModalManager {
    constructor() {
        this.modals = {
            logout: document.getElementById('logoutModal'),
            ban: document.getElementById('banModal'),
            unban: document.getElementById('unbanModal')
        };
        
        this.currentCustomer = null;
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
        
        // Kelola Admin link
        document.getElementById('newAdminBtn').addEventListener('click', (e) => {
            e.preventDefault();
            window.location.href = '/admin/kelolaadmin/kelolaadmin';
        });
        
        document.getElementById('cancelLogout').addEventListener('click', () => {
            this.hideModal('logout');
        });
        
        document.getElementById('confirmLogout').addEventListener('click', () => {
            this.handleLogout();
        });
        
        // Ban modal
        document.getElementById('cancelBan').addEventListener('click', () => {
            this.hideModal('ban');
        });
        
        document.getElementById('confirmBan').addEventListener('click', () => {
            this.handleBan();
        });
        
        // Unban modal
        document.getElementById('cancelUnban').addEventListener('click', () => {
            this.hideModal('unban');
        });
        
        document.getElementById('confirmUnban').addEventListener('click', () => {
            this.handleUnban();
        });
        
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
    
    showModal(modalName, customer = null) {
        const modal = this.modals[modalName];
        if (modal) {
            this.currentCustomer = customer;
            
            if (customer) {
                this.populateModal(modalName, customer);
            }
            
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }
    
    hideModal(modalName) {
        const modal = this.modals[modalName];
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
            this.currentCustomer = null;
        }
    }
    
    hideAllModals() {
        Object.keys(this.modals).forEach(modalName => {
            this.hideModal(modalName);
        });
    }
    
    populateModal(modalName, customer) {
        switch(modalName) {
            case 'ban':
                document.getElementById('banCustomerName').textContent = customer.nama;
                break;
            case 'unban':
                document.getElementById('unbanCustomerName').textContent = customer.nama;
                break;
        }
    }
    
    handleLogout() {
        console.log('Logging out...');
        
        // Tampilkan loading state
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
    
    handleBan() {
        if (!this.currentCustomer) return;
        
        const customer = this.currentCustomer;
        
        // Tampilkan loading state
        const confirmBtn = document.getElementById('confirmBan');
        const originalText = confirmBtn.textContent;
        confirmBtn.textContent = 'Memproses...';
        confirmBtn.disabled = true;
        
        // Simulasi delay server
        setTimeout(() => {
            // Update banned status
            customer.banned = true;
            
            showNotification(`Customer "${customer.nama}" berhasil dinonaktifkan`, 'success');
            
            // Re-render untuk konsistensi UI
            renderTableData();
            
            // Reset button dan sembunyikan modal
            confirmBtn.textContent = originalText;
            confirmBtn.disabled = false;
            this.hideModal('ban');
        }, 1500);
    }
    
    handleUnban() {
        if (!this.currentCustomer) return;
        
        const customer = this.currentCustomer;
        
        // Tampilkan loading state
        const confirmBtn = document.getElementById('confirmUnban');
        const originalText = confirmBtn.textContent;
        confirmBtn.textContent = 'Memproses...';
        confirmBtn.disabled = true;
        
        // Simulasi delay server
        setTimeout(() => {
            // Update banned status
            customer.banned = false;
            
            showNotification(`Customer "${customer.nama}" berhasil diaktifkan kembali`, 'success');
            
            // Re-render untuk konsistensi UI
            renderTableData();
            
            // Reset button dan sembunyikan modal
            confirmBtn.textContent = originalText;
            confirmBtn.disabled = false;
            this.hideModal('unban');
        }, 1500);
    }
}

// ===== INITIALIZATION =====
let modalManager;

document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi modal manager pertama
    modalManager = new ModalManager();
    
    // Kemudian inisialisasi lainnya
    initNavigation();
    initUserDropdown();
    loadMockData();
    initSearch();
    initTableActions();
    initResponsiveHandlers();
    
    console.log('Kelola Customer - System initialized dengan modal support');
});

// ===== NAVIGATION & USER DROPDOWN =====
function initNavigation() {
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach((item) => {
        item.addEventListener('click', function(e) {
            // Hanya prevent default jika href adalah "#" atau kosong
            if (item.getAttribute('href') === '#' || !item.getAttribute('href')) {
                e.preventDefault();
            }
            
            // Hapus class active dari semua item
            navItems.forEach((i) => i.classList.remove('active'));
            
            // Tambah class active ke item yang diklik
            this.classList.add('active');
        });
    });
}

function initUserDropdown() {
    const userAvatar = document.querySelector('.user-avatar');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    if (userAvatar && dropdownMenu) {
        userAvatar.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.style.opacity = dropdownMenu.style.opacity === '1' ? '0' : '1';
            dropdownMenu.style.visibility = dropdownMenu.style.visibility === 'visible' ? 'hidden' : 'visible';
            dropdownMenu.style.transform = dropdownMenu.style.transform === 'translateY(0px)' ? 'translateY(-10px)' : 'translateY(0)';
        });
        
        // Tutup dropdown ketika klik di luar
        document.addEventListener('click', function() {
            dropdownMenu.style.opacity = '0';
            dropdownMenu.style.visibility = 'hidden';
            dropdownMenu.style.transform = 'translateY(-10px)';
        });
    }
}

// ===== DATA MANAGEMENT =====
function loadMockData() {
    // Data already loaded from window.customersData
    renderTableData();
}

function renderTableData() {
    const tbody = document.getElementById('customerTableBody');
    
    if (!tbody) return;
    
    // Clear table body
    tbody.innerHTML = '';
    
    if (mockData.customers.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="empty-state">
                    <div class="icon">👥</div>
                    <p>Tidak ada data customer</p>
                    <small>Customer akan muncul di sini setelah mendaftar</small>
                </td>
            </tr>
        `;
        return;
    }
    
    // Calculate pagination
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedData = mockData.customers.slice(startIndex, endIndex);
    
    // Render each table row
    paginatedData.forEach((customer, index) => {
        const globalIndex = startIndex + index;
        const row = document.createElement('tr');
        row.className = 'fade-in';
        row.setAttribute('data-id', customer.id);
        row.style.animationDelay = `${index * 0.1}s`;
        
        // Status badge - default Active
        const statusBadgeClass = customer.status === 'Nonaktif' ? 'status-badge inactive' : 'status-badge active';
        const statusBadgeText = customer.status || 'Aktif';
        
        row.innerHTML = `
            <td>${globalIndex + 1}</td>
            <td>
                <div class="cell-main">
                    <div>${customer.name}</div>
                    <small>${customer.email}</small>
                </div>
            </td>
            <td>${customer.phone}</td>
            <td>${formatDate(customer.joinDate)}</td>
            <td>${customer.totalBooking}</td>
            <td><span class="${statusBadgeClass}">${statusBadgeText}</span></td>
            <td>
                <button class="btn ${customer.status === 'Nonaktif' ? 'btn-success' : 'btn-danger'} btn-sm ban-btn" data-id="${customer.id}">
                    ${customer.status === 'Nonaktif' ? 'Aktifkan' : 'Nonaktifkan'}
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
    });
    
    // Render pagination
    renderPagination();
}

function renderPagination() {
    const totalPages = Math.ceil(mockData.customers.length / itemsPerPage);
    const paginationContainer = document.getElementById('paginationControls');
    
    if (!paginationContainer) return;
    
    if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }
    
    let paginationHTML = '<div class="pagination">';
    
    // Previous button
    if (currentPage > 1) {
        paginationHTML += `<button class="pagination-btn" onclick="changePage(${currentPage - 1})">&laquo; Prev</button>`;
    } else {
        paginationHTML += `<button class="pagination-btn" disabled>&laquo; Prev</button>`;
    }
    
    // Page info
    paginationHTML += `<span class="pagination-info">Halaman ${currentPage} dari ${totalPages}</span>`;
    
    // Next button
    if (currentPage < totalPages) {
        paginationHTML += `<button class="pagination-btn" onclick="changePage(${currentPage + 1})">Next &raquo;</button>`;
    } else {
        paginationHTML += `<button class="pagination-btn" disabled>Next &raquo;</button>`;
    }
    
    paginationHTML += '</div>';
    paginationContainer.innerHTML = paginationHTML;
}

function changePage(page) {
    const totalPages = Math.ceil(mockData.customers.length / itemsPerPage);
    if (page < 1 || page > totalPages) return;
    
    currentPage = page;
    renderTableData();
    // Scroll to top of table
    document.querySelector('.table-wrapper')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ===== SEARCH FUNCTIONALITY =====
function initSearch() {
    const searchInput = document.getElementById('searchCustomer');
    
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function() {
        const keyword = this.value.toLowerCase().trim();
        filterTableData(keyword);
    });
    
    // Clear search dengan Escape key
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            this.dispatchEvent(new Event('input'));
            this.blur();
        }
    });
    
    // Focus search input dengan Ctrl+K
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
        }
    });
}

function filterTableData(keyword) {
    const tbody = document.getElementById('customerTableBody');
    const rows = tbody.querySelectorAll('tr');
    
    let hasVisibleRows = false;
    
    rows.forEach((row) => {
        const text = row.textContent.toLowerCase();
        const isVisible = text.includes(keyword);
        
        row.style.display = isVisible ? '' : 'none';
        
        if (isVisible) {
            hasVisibleRows = true;
        }
    });
    
    showNoResultsMessage(!hasVisibleRows && keyword !== '');
}

function showNoResultsMessage(show) {
    let message = document.getElementById('noResultsMessage');
    const tbody = document.getElementById('customerTableBody');
    
    if (show && !message) {
        message = document.createElement('tr');
        message.id = 'noResultsMessage';
        message.innerHTML = `
            <td colspan="7" class="empty-state">
                <div class="icon">🔍</div>
                <p>Tidak ada customer yang sesuai dengan pencarian Anda</p>
                <small>Coba gunakan kata kunci yang berbeda</small>
            </td>
        `;
        tbody.appendChild(message);
    } else if (!show && message) {
        message.remove();
    }
}

// ===== TABLE ACTIONS =====
function initTableActions() {
    // Remove existing listener if any
    const tableBody = document.getElementById('customerTableBody');
    if (!tableBody) {
        console.error('Table body not found');
        return;
    }
    
    // Use event delegation on table body
    tableBody.addEventListener('click', async function(e) {
        // Check if clicked element is ban button
        if (!e.target.classList.contains('ban-btn')) return;
        
        e.preventDefault();
        console.log('Ban button clicked');
        
        const customerId = e.target.getAttribute('data-id');
        console.log('Customer ID:', customerId);
        
        const customer = mockData.customers.find(c => c.id == customerId);
        
        if (!customer) {
            console.error('Customer not found:', customerId);
            return;
        }
        
        console.log('Customer found:', customer);
        
        // Konfirmasi action
        const action = customer.status === 'Nonaktif' ? 'mengaktifkan' : 'menonaktifkan';
        const confirmation = confirm(`Apakah Anda yakin ingin ${action} user ${customer.name}?`);
        
        if (!confirmation) return;
        
        // Disable button selama proses
        const button = e.target;
        const originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Memproses...';
        
        try {
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            console.log('Sending request to:', `/admin/user/${customerId}/toggle-status`);
            
            // Call API
            const response = await fetch(`/admin/user/${customerId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    _token: csrfToken
                })
            });
            
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Response:', result);
            
            if (result.success) {
                // Update customer status di mockData
                customer.status = result.status;
                
                // Re-render table
                renderTableData();
                
                // Show success notification
                showNotification(result.message, 'success');
            } else {
                throw new Error(result.message || 'Gagal mengubah status user');
            }
        } catch (error) {
            console.error('Error toggling user status:', error);
            showNotification(error.message || 'Terjadi kesalahan saat mengubah status user', 'error');
            
            // Re-enable button
            button.disabled = false;
            button.textContent = originalText;
        }
    });
}

// ===== RESPONSIVE HANDLERS =====
function initResponsiveHandlers() {
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(handleResize, 250);
    });
    
    handleResize();
}

function handleResize() {
    const width = window.innerWidth;
    
    if (width <= 768) {
        document.body.classList.add('mobile-view');
    } else {
        document.body.classList.remove('mobile-view');
    }
}

// ===== NOTIFICATION SYSTEM =====
function showNotification(message, type = 'info') {
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-message">${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    const autoHide = setTimeout(() => {
        hideNotification(notification);
    }, 5000);
    
    notification.querySelector('.notification-close').addEventListener('click', () => {
        clearTimeout(autoHide);
        hideNotification(notification);
    });
}

function hideNotification(notification) {
    notification.style.animation = 'slideOut 0.3s ease';
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 300);
}

// ===== UTILITY FUNCTIONS =====
function formatDate(dateString) {
    const options = { 
        day: 'numeric', 
        month: 'long', 
        year: 'numeric' 
    };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}

// Export functions untuk penggunaan global
window.CustomerManager = {
    mockData,
    formatDate,
    showNotification,
    refreshData: function() {
        renderTableData();
        showNotification('Data customer berhasil diperbarui', 'success');
    },
    addMockCustomer: function() {
        const newCustomer = {
            id: 'CUST' + (mockData.customers.length + 1).toString().padStart(3, '0'),
            nama: 'Customer Baru ' + (mockData.customers.length + 1),
            email: 'newcustomer@email.com',
            kontak: '+62 81-0000-0000',
            bergabung: new Date().toISOString().split('T')[0],
            totalBooking: 0,
            terakhirTransaksi: new Date().toISOString().split('T')[0],
            status: 'active',
            banned: false
        };
        
        mockData.customers.push(newCustomer);
        renderTableData();
        showNotification('Customer baru ditambahkan', 'success');
    }
};

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === '/') {
        e.preventDefault();
        alert(`Keyboard Shortcuts:\n\nCtrl+K - Focus search\nEscape - Clear search\nCtrl+/ - Show this help`);
    }
    
    // Demo shortcut untuk testing
    if (e.ctrlKey && e.shiftKey && e.key === 'A') {
        e.preventDefault();
        window.CustomerManager.addMockCustomer();
    }
    
    if (e.ctrlKey && e.shiftKey && e.key === 'R') {
        e.preventDefault();
        window.CustomerManager.refreshData();
    }
});

console.log('Kelola Customer JS loaded dengan modal system');