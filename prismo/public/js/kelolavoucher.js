// ====== MODAL MANAGEMENT ======
class ModalManager {
    constructor() {
        this.modals = {
            logout: document.getElementById('logoutModal'),
            voucherSuccess: document.getElementById('voucherSuccessModal'),
            delete: document.getElementById('deleteModal')
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
        
        // Voucher success modal
        document.getElementById('voucherOkBtn').addEventListener('click', () => {
            this.hideModal('voucherSuccess');
        });
        
        // Delete modal
        document.getElementById('cancelDelete').addEventListener('click', () => {
            this.hideModal('delete');
        });
        
        document.getElementById('confirmDelete').addEventListener('click', () => {
            this.handleDelete();
        });
        
        // Close modal when clicking outside
        Object.values(this.modals).forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    const modalName = modal.id.replace('Modal', '');
                    this.hideModal(modalName);
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
    
    handleDelete() {
        console.log('Deleting voucher...');
        this.hideModal('delete');
        
        // Implement delete logic here
        if (this.currentDeleteId) {
            voucherManager.deleteVoucher(this.currentDeleteId);
            this.currentDeleteId = null;
        }
    }
    
    setDeleteTarget(voucherId) {
        this.currentDeleteId = voucherId;
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

// ====== VOUCHER MANAGEMENT ======
class VoucherManager {
    constructor() {
        this.voucherForm = document.getElementById('voucherForm');
        this.resetBtn = document.getElementById('btnReset');
        this.tableBody = document.getElementById('voucherTableBody');
        
        this.vouchers = this.loadVouchers();
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.renderVouchers();
        this.setMinDate();
        this.setupColorPicker();
        this.updateRequiredStars();
        this.toggleHariTerdaftarInput();
    }
    
    setupEventListeners() {
        // Form submit
        this.voucherForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSubmit();
        });
        
        // Reset button
        this.resetBtn.addEventListener('click', () => {
            this.voucherForm.reset();
            this.setMinDate();
            document.getElementById('warnaVoucher').value = '#1c98f5';
            document.getElementById('warnaVoucherText').value = '#1c98f5';
            this.updateRequiredStars();
            this.clearCodeValidation();
            this.toggleHariTerdaftarInput();
        });
        
        // Listen to kondisi hari dropdown
        const kondisiHari = document.getElementById('kondisiHari');
        kondisiHari.addEventListener('change', () => {
            this.toggleHariTerdaftarInput();
        });
        
        // Listen to all required inputs
        const requiredInputs = this.voucherForm.querySelectorAll('input[required], select[required]');
        requiredInputs.forEach(input => {
            input.addEventListener('input', () => this.updateRequiredStars());
            input.addEventListener('change', () => this.updateRequiredStars());
        });
        
        // Real-time validation for voucher code
        const kodeInput = document.getElementById('kodeVoucher');
        kodeInput.addEventListener('input', (e) => {
            e.target.value = e.target.value.toUpperCase();
            this.validateVoucherCode(e.target.value);
        });
        
        kodeInput.addEventListener('blur', (e) => {
            this.validateVoucherCode(e.target.value);
        });
    }
    
    validateVoucherCode(code) {
        const kodeInput = document.getElementById('kodeVoucher');
        const formGroup = kodeInput.closest('.form-group');
        
        // Remove existing validation message
        const existingMsg = formGroup.querySelector('.validation-message');
        if (existingMsg) {
            existingMsg.remove();
        }
        
        // Remove validation classes
        formGroup.classList.remove('has-error', 'has-success');
        kodeInput.classList.remove('input-error', 'input-success');
        
        if (!code || code.trim() === '') {
            return;
        }
        
        if (this.isCodeExists(code.trim())) {
            formGroup.classList.add('has-error');
            kodeInput.classList.add('input-error');
            
            const errorMsg = document.createElement('small');
            errorMsg.className = 'validation-message error-message';
            errorMsg.textContent = 'Kode voucher sudah digunakan';
            formGroup.appendChild(errorMsg);
        } else {
            formGroup.classList.add('has-success');
            kodeInput.classList.add('input-success');
            
            const successMsg = document.createElement('small');
            successMsg.className = 'validation-message success-message';
            successMsg.textContent = 'Kode voucher tersedia';
            formGroup.appendChild(successMsg);
        }
    }
    
    clearCodeValidation() {
        const kodeInput = document.getElementById('kodeVoucher');
        const formGroup = kodeInput.closest('.form-group');
        
        const existingMsg = formGroup.querySelector('.validation-message');
        if (existingMsg) {
            existingMsg.remove();
        }
        
        formGroup.classList.remove('has-error', 'has-success');
        kodeInput.classList.remove('input-error', 'input-success');
    }
    
    updateRequiredStars() {
        const formGroups = this.voucherForm.querySelectorAll('.form-group');
        formGroups.forEach(group => {
            const requiredInput = group.querySelector('input[required], select[required]');
            if (requiredInput) {
                if (requiredInput.value.trim() !== '') {
                    group.classList.add('filled');
                } else {
                    group.classList.remove('filled');
                }
            }
        });
    }
    
    setupColorPicker() {
        const colorInput = document.getElementById('warnaVoucher');
        const colorText = document.getElementById('warnaVoucherText');
        
        // Update text when color picker changes
        colorInput.addEventListener('input', (e) => {
            colorText.value = e.target.value.toUpperCase();
        });
        
        // Update color picker when text changes
        colorText.addEventListener('input', (e) => {
            const value = e.target.value;
            if (/^#[0-9A-F]{6}$/i.test(value)) {
                colorInput.value = value;
            }
        });
        
        // Format text input
        colorText.addEventListener('blur', (e) => {
            let value = e.target.value.trim();
            if (!value.startsWith('#')) {
                value = '#' + value;
            }
            if (/^#[0-9A-F]{6}$/i.test(value)) {
                colorText.value = value.toUpperCase();
                colorInput.value = value;
            } else {
                colorText.value = colorInput.value.toUpperCase();
            }
        });
    }
    
    setMinDate() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('masaBerlaku').setAttribute('min', today);
    }
    
    toggleHariTerdaftarInput() {
        const kondisiHari = document.getElementById('kondisiHari');
        const hariTerdaftar = document.getElementById('hariTerdaftar');
        
        if (kondisiHari.value === 'none') {
            hariTerdaftar.disabled = true;
            hariTerdaftar.value = '';
            hariTerdaftar.style.opacity = '0.5';
        } else {
            hariTerdaftar.disabled = false;
            hariTerdaftar.style.opacity = '1';
        }
    }
    
    handleSubmit() {
        const kodeVoucher = document.getElementById('kodeVoucher').value.toUpperCase().trim();
        
        // Validate voucher code before submit
        if (this.isCodeExists(kodeVoucher)) {
            this.validateVoucherCode(kodeVoucher);
            document.getElementById('kodeVoucher').focus();
            return;
        }
        
        const kondisiHari = document.getElementById('kondisiHari').value;
        const hariTerdaftar = document.getElementById('hariTerdaftar').value;
        
        const formData = {
            code: kodeVoucher,
            title: document.getElementById('namaVoucher').value,
            end_date: document.getElementById('masaBerlaku').value,
            min_transaction: document.getElementById('minTransaksi').value || 0,
            discount_percent: document.getElementById('persentasePotongan').value || null,
            max_discount: document.getElementById('maksPotongan').value,
            discount_fixed: null,
            registration_condition: kondisiHari,
            registration_days: (kondisiHari !== 'none' && hariTerdaftar) ? hariTerdaftar : null,
            color: document.getElementById('warnaVoucher').value,
            type: 'discount',
            is_active: true
        };
        
        // Send to API
        fetch('/api/vouchers', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            // Show success modal
            modalManager.showModal('voucherSuccess');
            
            // Reload page to show new voucher
            setTimeout(() => {
                location.reload();
            }, 1500);
        })
        .catch(error => {
            console.error('Error saving voucher:', error);
            alert('Gagal menyimpan voucher. Silakan coba lagi.');
        });
    }
    
    isCodeExists(code, excludeId = null) {
        return this.vouchers.some(v => v.code === code && v.id !== excludeId);
    }
    
    deleteVoucher(id) {
        // Call API to delete voucher
        fetch(`/api/vouchers/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Voucher deleted:', data);
            // Reload page to refresh voucher list
            location.reload();
        })
        .catch(error => {
            console.error('Error deleting voucher:', error);
            alert('Gagal menghapus voucher. Silakan coba lagi.');
        });
    }
    
    renderVouchers() {
        if (this.vouchers.length === 0) {
            this.tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="ph ph-ticket"></i>
                        <p>Belum ada voucher yang dibuat</p>
                    </td>
                </tr>
            `;
            return;
        }
        
        this.tableBody.innerHTML = this.vouchers.map(voucher => {
            const status = this.getVoucherStatus(voucher.end_date);
            const potongan = this.formatPotongan(voucher);
            const minTransaksi = voucher.min_transaction ? this.formatRupiah(voucher.min_transaction) : '-';
            const masaBerlaku = this.formatDate(voucher.end_date);
            const warnaVoucher = voucher.color || '#1c98f5';
            
            return `
                <tr>
                    <td><span class="voucher-code">${voucher.code}</span></td>
                    <td>${voucher.name}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:30px;height:30px;border-radius:4px;background:${warnaVoucher};border:1px solid #ddd;"></div>
                            <span style="font-size:0.85rem;color:#666;">${warnaVoucher}</span>
                        </div>
                    </td>
                    <td>${potongan}</td>
                    <td>${minTransaksi}</td>
                    <td>${masaBerlaku}</td>
                    <td><span class="voucher-status ${status.class}">${status.text}</span></td>
                    <td>
                        <div class="voucher-actions">
                            <button class="btn btn-danger btn-icon" onclick="handleDeleteVoucher(${voucher.id})">
                                <i class="ph ph-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }
    
    formatPotongan(voucher) {
        if (voucher.discount_percent) {
            const maxDiscount = voucher.max_discount ? this.formatRupiah(voucher.max_discount) : 'Unlimited';
            return `${voucher.discount_percent}% (Max ${maxDiscount})`;
        }
        if (voucher.discount_fixed) {
            return this.formatRupiah(voucher.discount_fixed);
        }
        return '-';
    }
    
    formatRupiah(amount) {
        return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
    }
    
    formatDate(dateStr) {
        const date = new Date(dateStr);
        const options = { day: 'numeric', month: 'short', year: 'numeric' };
        return date.toLocaleDateString('id-ID', options);
    }
    
    getVoucherStatus(expiryDate) {
        const today = new Date();
        const expiry = new Date(expiryDate);
        
        if (expiry < today) {
            return { class: 'expired', text: 'Kadaluarsa' };
        }
        return { class: 'active', text: 'Aktif' };
    }
    
    loadVouchers() {
        // Use real data from server - NO MORE localStorage mock data
        return window.vouchersData || [];
    }
    
    saveVouchers() {
        // This would be handled by server-side (API call to save vouchers)
        console.warn('Voucher save should be handled via API');
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

// ====== GLOBAL FUNCTIONS ======
function handleDeleteVoucher(id) {
    modalManager.setDeleteTarget(id);
    modalManager.showModal('delete');
}

// ====== INITIALIZATION ======
let modalManager, navigationManager, voucherManager, userProfileManager;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all managers
    modalManager = new ModalManager();
    navigationManager = new NavigationManager();
    voucherManager = new VoucherManager();
    userProfileManager = new UserProfileManager();
    
    console.log('Kelola Voucher Page initialized successfully');
});
