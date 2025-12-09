// kelolamitra.js - Sistem Kelola Mitra Prismo

// ===== KONFIGURASI DAN DATA =====

// Pagination state
let currentPage = 1;
const itemsPerPage = 30;

// Use real mitra data from server if available
const serverMitras = window.mitrasData || [];

console.log('Server Mitras:', serverMitras);
console.log('Total mitras from server:', serverMitras.length);

// Separate mitras by approval status
const mockData = {
    approvalMitra: serverMitras.filter(m => m.approval_status === 'pending').map(m => ({
        id: m.id,
        namaTempat: m.business_name || '-',
        pemilik: m.name,
        email: m.email,
        kontak: m.phone || '-',
        lokasi: m.city || '-',
        bergabung: m.created_at,
        status: m.approval_status,
        rating: parseFloat(m.rating) || 0
    })),
    
    activeMitra: serverMitras.filter(m => m.approval_status === 'approved').map(m => ({
        id: m.id,
        namaTempat: m.business_name || '-',
        pemilik: m.name,
        email: m.email,
        kontak: m.phone || '-',
        lokasi: m.city || '-',
        bergabung: m.created_at,
        rating: parseFloat(m.rating) || 0,
        saldo: 0, // Will be fetched from mitra profile if needed
        status: m.status || 'Aktif' // Use status from database
    }))
};

console.log('Approval Mitra (pending):', mockData.approvalMitra);
console.log('Active Mitra (approved):', mockData.activeMitra);
console.log('Total pending:', mockData.approvalMitra.length);
console.log('Total approved:', mockData.activeMitra.length);

// ===== MODAL MANAGEMENT =====
class ModalManager {
    constructor() {
        this.modals = {
            logout: document.getElementById('logoutModal'),
            ban: document.getElementById('banModal'),
            unban: document.getElementById('unbanModal')
        };
        
        this.currentMitra = null;
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
    
    showModal(modalName, mitra = null) {
        const modal = this.modals[modalName];
        if (modal) {
            this.currentMitra = mitra;
            
            if (mitra) {
                this.populateModal(modalName, mitra);
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
            this.currentMitra = null;
        }
    }
    
    hideAllModals() {
        Object.keys(this.modals).forEach(modalName => {
            this.hideModal(modalName);
        });
    }
    
    populateModal(modalName, mitra) {
        switch(modalName) {
            case 'ban':
                document.getElementById('banMitraName').textContent = mitra.namaTempat;
                break;
            case 'unban':
                document.getElementById('unbanMitraName').textContent = mitra.namaTempat;
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
        if (!this.currentMitra) return;
        
        const mitra = this.currentMitra;
        
        // Tampilkan loading state
        const confirmBtn = document.getElementById('confirmBan');
        const originalText = confirmBtn.textContent;
        confirmBtn.textContent = 'Memproses...';
        confirmBtn.disabled = true;
        
        // Simulasi delay server
        setTimeout(() => {
            // Update banned status
            mitra.banned = true;
            
            showNotification(`Mitra "${mitra.namaTempat}" berhasil dinonaktifkan`, 'success');
            
            // Re-render untuk konsistensi UI
            renderTableData();
            
            // Reset button dan sembunyikan modal
            confirmBtn.textContent = originalText;
            confirmBtn.disabled = false;
            this.hideModal('ban');
        }, 1500);
    }
    
    handleUnban() {
        if (!this.currentMitra) return;
        
        const mitra = this.currentMitra;
        
        // Tampilkan loading state
        const confirmBtn = document.getElementById('confirmUnban');
        const originalText = confirmBtn.textContent;
        confirmBtn.textContent = 'Memproses...';
        confirmBtn.disabled = true;
        
        // Simulasi delay server
        setTimeout(() => {
            // Update banned status
            mitra.banned = false;
            
            showNotification(`Mitra "${mitra.namaTempat}" berhasil diaktifkan kembali`, 'success');
            
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
    initApprovalScroll();
    initResponsiveHandlers();
    
    console.log('Kelola Mitra - System initialized dengan modal support');
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
    console.log('=== loadMockData called ===');
    renderApprovalData();
    renderTableData();
}

function renderApprovalData() {
    console.log('=== renderApprovalData called ===');
    const container = document.getElementById('approvalContainer');
    const countElement = document.getElementById('approvalCount');
    
    console.log('approvalContainer:', container);
    console.log('approvalCount:', countElement);
    
    if (!container) {
        console.error('approvalContainer element not found!');
        return;
    }
    
    // Update count
    if (countElement) {
        countElement.textContent = `(${mockData.approvalMitra.length})`;
    }
    
    // Clear container
    container.innerHTML = '';
    
    if (mockData.approvalMitra.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="icon">✅</div>
                <p>Tidak ada persetujuan mitra yang tertunda</p>
                <small>Semua mitra telah diproses</small>
            </div>
        `;
        return;
    }
    
    // Render each approval row
    mockData.approvalMitra.forEach((mitra, index) => {
        const row = document.createElement('div');
        row.className = 'approval-row fade-in';
        row.style.animationDelay = `${index * 0.1}s`;
        
        row.innerHTML = `
            <div class="approval-col">
                <div class="label">Nama Tempat Cuci</div>
                <div class="value">${mitra.namaTempat}</div>
                <div class="sub">Bergabung: ${formatDate(mitra.bergabung)}</div>
            </div>
            <div class="approval-col">
                <div class="label">Pemilik</div>
                <div class="value">${mitra.pemilik}</div>
                <div class="sub">${mitra.email}</div>
            </div>
            <div class="approval-col">
                <div class="label">Kontak</div>
                <div class="value">${mitra.kontak}</div>
            </div>
            <div class="approval-col">
                <div class="label">Lokasi</div>
                <div class="value">${mitra.lokasi}</div>
            </div>
            <div class="approval-col approval-status">
                <div class="label">Status</div>
                <div class="tag waiting">Menunggu</div>
            </div>
            <div class="approval-col approval-rating">
                <div class="label">Rating</div>
                <div class="rating">
                    <span class="star">★</span>
                    <span>-</span>
                </div>
            </div>
            <div class="approval-col approval-action">
                <a href="/admin/kelolamitra/${mitra.id}/form" class="btn btn-primary btn-sm">Detail</a>
            </div>
        `;
        
        container.appendChild(row);
    });
}

function renderTableData() {
    console.log('=== renderTableData called ===');
    const tbody = document.getElementById('mitraTableBody');
    console.log('tbody element:', tbody);
    
    if (!tbody) {
        console.error('mitraTableBody element not found!');
        return;
    }
    
    // Clear table body
    tbody.innerHTML = '';
    
    console.log('mockData.activeMitra:', mockData.activeMitra);
    console.log('activeMitra length:', mockData.activeMitra.length);
    
    // Calculate pagination
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedData = mockData.activeMitra.slice(startIndex, endIndex);
    
    console.log('paginatedData:', paginatedData);
    console.log('paginatedData length:', paginatedData.length);
    
    if (paginatedData.length === 0) {
        console.warn('No data to display in table');
        tbody.innerHTML = '<tr><td colspan="9" style="text-align: center; padding: 20px;">Tidak ada data mitra</td></tr>';
        return;
    }
    
    // Render each table row
    paginatedData.forEach((mitra, index) => {
        const globalIndex = startIndex + index;
        const row = document.createElement('tr');
        row.className = 'fade-in';
        row.setAttribute('data-id', mitra.id);
        row.style.animationDelay = `${index * 0.1}s`;
        
        // Format rating dengan 1 desimal - convert to number first
        const rating = parseFloat(mitra.rating) || 0;
        const formattedRating = rating % 1 === 0 ? rating.toFixed(1) : rating.toFixed(1);
        
        // Status badge berdasarkan status field
        const statusBadgeClass = mitra.status === 'Nonaktif' ? 'status-badge inactive' : 'status-badge active';
        const statusBadgeText = mitra.status || 'Aktif';
        
        row.innerHTML = `
            <td>${globalIndex + 1}</td>
            <td>
                <div class="cell-main">
                    <div>${mitra.namaTempat}</div>
                    <small>Bergabung: ${formatDate(mitra.bergabung)}</small>
                </div>
            </td>
            <td>
                <div class="cell-main">
                    <div>${mitra.pemilik}</div>
                    <small>${mitra.email}</small>
                </div>
            </td>
            <td>
                <div class="cell-main">
                    <div>${mitra.kontak}</div>
                </div>
            </td>
            <td>${mitra.lokasi}</td>
            <td>
                <div class="rating">
                    <span class="star">★</span>
                    <span>${formattedRating}</span>
                </div>
            </td>
            <td>
                <div class="saldo">
                    <span>Rp ${formatCurrency(mitra.saldo)}</span>
                </div>
            </td>
            <td>
                <span class="${statusBadgeClass}">${statusBadgeText}</span>
            </td>
            <td>
                <button class="btn btn-info btn-sm" onclick="viewMitraDetail(${mitra.id})" style="margin-right: 8px;">
                    Detail
                </button>
                <button class="btn ${mitra.status === 'Nonaktif' ? 'btn-success' : 'btn-danger'} btn-sm ban-btn" data-id="${mitra.id}">
                    ${mitra.status === 'Nonaktif' ? 'Aktifkan' : 'Nonaktifkan'}
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
    });
    
    // Render pagination
    renderPagination();
}

function renderPagination() {
    const totalPages = Math.ceil(mockData.activeMitra.length / itemsPerPage);
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
    const totalPages = Math.ceil(mockData.activeMitra.length / itemsPerPage);
    if (page < 1 || page > totalPages) return;
    
    currentPage = page;
    renderTableData();
    // Scroll to top of table
    document.querySelector('.table-wrapper')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ===== SEARCH FUNCTIONALITY =====
function initSearch() {
    const searchInput = document.getElementById('searchMitra');
    
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function() {
        const keyword = this.value.toLowerCase().trim();
        filterTableData(keyword);
        filterApprovalData(keyword);
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
    const tbody = document.getElementById('mitraTableBody');
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
    
    showNoResultsMessage(!hasVisibleRows && keyword !== '', 'table');
}

function filterApprovalData(keyword) {
    const container = document.getElementById('approvalContainer');
    const rows = container.querySelectorAll('.approval-row');
    
    let hasVisibleRows = false;
    
    rows.forEach((row) => {
        const text = row.textContent.toLowerCase();
        const isVisible = text.includes(keyword);
        
        row.style.display = isVisible ? '' : 'none';
        
        if (isVisible) {
            hasVisibleRows = true;
        }
    });
    
    showNoResultsMessage(!hasVisibleRows && keyword !== '', 'approval');
}

function showNoResultsMessage(show, type) {
    let message = document.getElementById(`noResultsMessage-${type}`);
    let container;
    
    if (type === 'table') {
        container = document.querySelector('#mitraTable tbody');
    } else {
        container = document.getElementById('approvalContainer');
    }
    
    if (show && !message) {
        message = document.createElement('div');
        message.id = `noResultsMessage-${type}`;
        message.className = 'empty-state';
        message.innerHTML = `
            <div class="icon">🔍</div>
            <p>Tidak ada ${type === 'table' ? 'mitra' : 'persetujuan'} yang sesuai dengan pencarian Anda</p>
            <small>Coba gunakan kata kunci yang berbeda</small>
        `;
        
        if (type === 'table') {
            container.parentNode.insertBefore(message, container.nextSibling);
        } else {
            container.appendChild(message);
        }
    } else if (!show && message) {
        message.remove();
    }
}

// ===== TABLE ACTIONS =====
function initTableActions() {
    // Remove existing listener if any
    const tableBody = document.getElementById('mitraTableBody');
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
        
        const mitraId = e.target.getAttribute('data-id');
        console.log('Mitra ID:', mitraId);
        
        const mitra = mockData.activeMitra.find(m => m.id == mitraId);
        
        if (!mitra) {
            console.error('Mitra not found:', mitraId);
            return;
        }
        
        console.log('Mitra found:', mitra);
        
        // Konfirmasi action
        const action = mitra.status === 'Nonaktif' ? 'mengaktifkan' : 'menonaktifkan';
        const confirmation = confirm(`Apakah Anda yakin ingin ${action} mitra ${mitra.pemilik}?`);
        
        if (!confirmation) return;
        
        // Disable button selama proses
        const button = e.target;
        const originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Memproses...';
        
        try {
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            console.log('Sending request to:', `/admin/user/${mitraId}/toggle-status`);
            
            // Call API
            const response = await fetch(`/admin/user/${mitraId}/toggle-status`, {
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
                // Update mitra status di mockData
                mitra.status = result.status;
                
                // Re-render table
                renderTableData();
                
                // Show success notification
                showNotification(result.message, 'success');
            } else {
                throw new Error(result.message || 'Gagal mengubah status mitra');
            }
        } catch (error) {
            console.error('Error toggling mitra status:', error);
            showNotification(error.message || 'Terjadi kesalahan saat mengubah status mitra', 'error');
            
            // Re-enable button
            button.disabled = false;
            button.textContent = originalText;
        }
    });
}

// ===== APPROVAL BODY SCROLL HANDLING =====
function initApprovalScroll() {
    const approvalBody = document.querySelector('.approval-body');
    const approvalContainer = document.getElementById('approvalContainer');
    
    if (!approvalBody || !approvalContainer) return;
    
    function updateScrollVisibility() {
        const containerWidth = approvalContainer.scrollWidth;
        const bodyWidth = approvalBody.clientWidth;
        const containerHeight = approvalContainer.scrollHeight;
        const bodyHeight = approvalBody.clientHeight;
        
        if (containerWidth > bodyWidth) {
            approvalBody.classList.add('has-horizontal-scroll');
        } else {
            approvalBody.classList.remove('has-horizontal-scroll');
        }
        
        if (containerHeight > bodyHeight) {
            approvalBody.classList.add('has-vertical-scroll');
        } else {
            approvalBody.classList.remove('has-vertical-scroll');
        }
    }
    
    // Initial check
    updateScrollVisibility();
    
    // Check on resize dengan debounce
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(updateScrollVisibility, 100);
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
    const existingNotification = document.getElementById('notification');
    if (existingNotification) {
        existingNotification.classList.remove('show');
        setTimeout(() => {
            if (existingNotification.parentNode) {
                existingNotification.parentNode.removeChild(existingNotification);
            }
        }, 300);
    }
    
    const notification = document.getElementById('notification');
    const messageElement = document.getElementById('notificationMessage');
    
    if (!notification || !messageElement) return;
    
    messageElement.textContent = message;
    notification.className = `notification notification-${type}`;
    notification.classList.add('show');
    
    setTimeout(() => {
        notification.classList.remove('show');
    }, 5000);
    
    const closeBtn = notification.querySelector('.notification-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            notification.classList.remove('show');
        });
    }
}

// ===== UTILITY FUNCTIONS =====
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

function formatDate(dateString) {
    const options = { 
        day: 'numeric', 
        month: 'long', 
        year: 'numeric' 
    };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}

function getStatusText(status) {
    const statusMap = {
        'waiting': 'Menunggu',
        'open': 'Buka',
        'closed': 'Tutup'
    };
    return statusMap[status] || status;
}

// Export functions untuk penggunaan global
window.MitraManager = {
    mockData,
    formatCurrency,
    formatDate,
    showNotification,
    refreshData: function() {
        renderApprovalData();
        renderTableData();
        showNotification('Data berhasil diperbarui', 'success');
    },
    addMockApproval: function() {
        const newMitra = {
            id: 'AP' + (mockData.approvalMitra.length + 1).toString().padStart(3, '0'),
            namaTempat: 'New Car Wash ' + (mockData.approvalMitra.length + 1),
            pemilik: 'Pemilik Baru',
            email: 'new@carwash.com',
            kontak: '+62 81-0000-0000',
            lokasi: 'Lokasi Baru',
            bergabung: new Date().toISOString().split('T')[0],
            status: 'waiting',
            rating: null
        };
        
        mockData.approvalMitra.push(newMitra);
        renderApprovalData();
        showNotification('Mitra approval baru ditambahkan', 'success');
    }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing Kelola Mitra...');
    loadMockData();
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === '/') {
        e.preventDefault();
        alert(`Keyboard Shortcuts:\n\nCtrl+K - Focus search\nEscape - Clear search\nCtrl+/ - Show this help`);
    }
    
    // Demo shortcut untuk testing
    if (e.ctrlKey && e.shiftKey && e.key === 'A') {
        e.preventDefault();
        window.MitraManager.addMockApproval();
    }
    
    if (e.ctrlKey && e.shiftKey && e.key === 'R') {
        e.preventDefault();
        window.MitraManager.refreshData();
    }
});

// Function to view mitra detail
function viewMitraDetail(mitraId) {
    // Redirect to detail page using the existing route
    window.location.href = `/admin/kelolamitra/${mitraId}/form`;
}

console.log('Kelola Mitra JS loaded dengan modal system dan rating 4.0');