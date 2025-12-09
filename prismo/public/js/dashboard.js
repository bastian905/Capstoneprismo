// ===== PRISMO DASHBOARD MANAGER =====
class PrismoDashboardManager {
    constructor() {
        this.isInitialized = false;
        this.isMobileMenuOpen = false;
        // Load initial status from database (default to 'open' if not set)
        this.storeStatus = (typeof window.mitraIsOpen !== 'undefined' && window.mitraIsOpen) ? 'open' : 'closed';
        this.selectedStatus = this.storeStatus;
        this.services = this.getInitialServices();
        this.operationalHours = this.getInitialOperationalHours();
        this.serviceToDelete = null;
        this.editingService = null;
        this.isProcessingDelete = false;
        
        // Drag functionality untuk WhatsApp button
        this.isDragging = false;
        this.dragStartX = 0;
        this.dragStartY = 0;
        this.initialX = 0;
        this.initialY = 0;
        
        // Resize handler reference
        this._resizeHandler = null;
        
        // Notifikasi data - DIKELOLA OLEH JAVASCRIPT
        this.notifications = {
            antrian: 3,
            review: 5
        };
        
        this.init();
    }

    getInitialServices() {
        // Priority 1: Load custom services dari backend (semua layanan sudah tersimpan di sini)
        if (window.initialCustomServices && window.initialCustomServices.length > 0) {
            console.log('Loading services from database:', window.initialCustomServices);
            return window.initialCustomServices;
        }
        
        // Priority 2: Load dari service packages backend (untuk backward compatibility)
        if (window.initialServicePackages && window.initialServicePackages.length > 0) {
            console.log('Loading from service packages:', window.initialServicePackages);
            return window.initialServicePackages.map((pkg, index) => ({
                id: index + 1,
                name: pkg.name,
                price: pkg.price,
                capacity: 5,
                description: pkg.features.join(', ')
            }));
        }
        
        // Priority 3: Default fallback (hanya jika tidak ada data sama sekali)
        console.log('Using default services (no data from backend)');
        return [
            {
                id: 1,
                name: "Basic Steam",
                price: 40000,
                capacity: 7,
                description: "Cuci eksterior mobil dengan sabun khusus dan air bersih"
            },
            {
                id: 2,
                name: "Premium Steam",
                price: 75000,
                capacity: 5,
                description: "Cuci eksterior dan interior dengan treatment khusus"
            },
            {
                id: 3,
                name: "Express Wash",
                price: 25000,
                capacity: 10,
                description: "Cuci cepat untuk eksterior mobil"
            }
        ];
    }

    saveCustomServices() {
        // Return a promise for async operations
        return new Promise((resolve, reject) => {
            // Get all services from this.services array
            const customServices = this.services.map(service => ({
                id: service.id,
                name: service.name,
                price: parseInt(service.price),
                capacity: parseInt(service.capacity),
                max_slots: parseInt(service.max_slots || service.capacity || 3),
                description: service.description
            }));

            // Send to backend
            fetch('/mitra/update-custom-services', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    services: customServices
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Custom services saved successfully:', data.services);
                    resolve(data);
                } else {
                    reject(new Error(data.message || 'Gagal menyimpan custom services'));
                }
            })
            .catch(error => {
                console.error('Error saving custom services:', error);
                reject(error);
            });
        });
    }

    getInitialOperationalHours() {
        // Cek apakah ada data dari backend
        if (window.initialOperationalHours && Object.keys(window.initialOperationalHours).length > 0) {
            return window.initialOperationalHours;
        }
        
        // Default fallback jika tidak ada data
        return {
            monday: { 
                enabled: true, 
                open: "08:00", 
                close: "17:00", 
                hasBreakSchedule: false,
                breakSchedules: []
            },
            tuesday: { 
                enabled: true, 
                open: "08:00", 
                close: "17:00", 
                hasBreakSchedule: false,
                breakSchedules: []
            },
            wednesday: { 
                enabled: true, 
                open: "08:00", 
                close: "17:00", 
                hasBreakSchedule: false,
                breakSchedules: []
            },
            thursday: { 
                enabled: true, 
                open: "08:00", 
                close: "17:00", 
                hasBreakSchedule: false,
                breakSchedules: []
            },
            friday: { 
                enabled: true, 
                open: "08:00", 
                close: "17:00", 
                hasBreakSchedule: true,
                breakSchedules: [
                    { open: "11:30", close: "13:00" }
                ]
            },
            saturday: { 
                enabled: true, 
                open: "08:00", 
                close: "17:00", 
                hasBreakSchedule: false,
                breakSchedules: []
            },
            sunday: { 
                enabled: false, 
                open: "08:00", 
                close: "17:00", 
                hasBreakSchedule: false,
                breakSchedules: []
            }
        };
    }

    init() {
        if (this.isInitialized) return;

        try {
            this.setupEventListeners();
            this.setupMobileMenu();
            this.setupFloatingWhatsApp();
            this.updateStatusDisplay();
            this.displayServices();
            this.displayOperationalSchedule();
            this.setupProfileNavigation();
            
            // 🔥 PASTIKAN INI DIPANGGIL - PERBAIKAN UTAMA
            this.updateAllBadges();
            
            // 🆕 TAMBAHKAN RESIZE LISTENER
            this.setupResizeListener();
            
            this.isInitialized = true;

            console.log('✅ PRISMO Dashboard Manager initialized');
            console.log('🔔 Initial notifications:', this.notifications);

        } catch (error) {
            console.error('❌ Failed to initialize PRISMO Dashboard Manager:', error);
        }
    }

    // 🆕 SETUP RESIZE LISTENER
    setupResizeListener() {
        let resizeTimeout;
        
        const handleResize = () => {
            // Debounce resize events
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                console.log('🔄 Window resized to:', window.innerWidth, 'x', window.innerHeight);
                this.resetFloatingButtonPosition();
            }, 250);
        };
        
        // Add event listener
        window.addEventListener('resize', handleResize);
        
        // Simpan reference untuk cleanup nanti
        this._resizeHandler = handleResize;
        
        console.log('📐 Resize listener setup completed');
    }

    setupEventListeners() {
        // Status buttons
        const openStatusBtn = document.getElementById('openStatusBtn');
        const closeStatusBtn = document.getElementById('closeStatusBtn');
        const saveStatusBtn = document.getElementById('saveStatusBtn');

        if (openStatusBtn) {
            openStatusBtn.addEventListener('click', () => {
                this.selectStatus('open');
            });
        }

        if (closeStatusBtn) {
            closeStatusBtn.addEventListener('click', () => {
                this.selectStatus('closed');
            });
        }

        if (saveStatusBtn) {
            saveStatusBtn.addEventListener('click', () => {
                this.showStatusConfirmation();
            });
        }

        // Save operational hours
        const saveHoursBtn = document.getElementById('saveOperationalHours');
        if (saveHoursBtn) {
            saveHoursBtn.addEventListener('click', () => {
                this.showSaveConfirmation();
            });
        }

        // Add service button
        const addServiceBtn = document.getElementById('addService');
        if (addServiceBtn) {
            addServiceBtn.addEventListener('click', () => {
                this.showAddServiceModal();
            });
        }

        // Day checkboxes
        const dayCheckboxes = document.querySelectorAll('input[name="operational-days"]');
        dayCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', (event) => {
                const day = event.target.value;
                const enabled = event.target.checked;
                this.toggleDaySchedule(day, enabled);
            });
        });

        // Global click handler
        document.addEventListener('click', (event) => {
            this.handleGlobalClick(event);
        });

        // Global keydown handler
        document.addEventListener('keydown', (event) => {
            this.handleGlobalKeydown(event);
        });
    }

    setupProfileNavigation() {
        // Setup user menu to navigate to profile (desktop)
        const userMenuToggle = document.querySelector('.user-menu__toggle');
        if (userMenuToggle) {
            userMenuToggle.addEventListener('click', (e) => {
                e.preventDefault();
                window.location.href = '/mitra/profil/profil';
            });
        }

        // Setup mobile user profile to navigate to profile
        const mobileUserProfile = document.getElementById('mobileUserProfile');
        if (mobileUserProfile) {
            mobileUserProfile.addEventListener('click', (e) => {
                e.preventDefault();
                this.closeMobileMenu();
                window.location.href = '/mitra/profil/profil';
            });
        }
    }

    // ===== NOTIFICATION BADGE SYSTEM - PERBAIKAN UTAMA =====
    updateAllBadges() {
        this.updateDesktopBadges();
        this.updateMobileBadges();
    }

    updateDesktopBadges() {
        console.log('🔄 Updating desktop badges...', this.notifications);
        
        // Update antrian badge
        const antrianBadge = document.getElementById('antrian-badge');
        if (antrianBadge) {
            const count = this.notifications.antrian;
            console.log('📱 Antrian badge:', count);
            if (count > 0) {
                antrianBadge.textContent = count;
                antrianBadge.style.display = 'flex';
                antrianBadge.setAttribute('aria-label', `${count} notifikasi antrian`);
            } else {
                antrianBadge.style.display = 'none';
            }
        }

        // Update review badge
        const reviewBadge = document.getElementById('review-badge');
        if (reviewBadge) {
            const count = this.notifications.review;
            console.log('⭐ Review badge:', count);
            if (count > 0) {
                reviewBadge.textContent = count;
                reviewBadge.style.display = 'flex';
                reviewBadge.setAttribute('aria-label', `${count} notifikasi review`);
            } else {
                reviewBadge.style.display = 'none';
            }
        }
    }

    updateMobileBadges() {
        console.log('📱 Updating mobile badges...', this.notifications);
        
        // Update mobile antrian badge
        const mobileAntrianBadge = document.getElementById('mobile-antrian-badge');
        if (mobileAntrianBadge) {
            const count = this.notifications.antrian;
            if (count > 0) {
                mobileAntrianBadge.textContent = count;
                mobileAntrianBadge.style.display = 'flex';
            } else {
                mobileAntrianBadge.style.display = 'none';
            }
        }

        // Update mobile review badge
        const mobileReviewBadge = document.getElementById('mobile-review-badge');
        if (mobileReviewBadge) {
            const count = this.notifications.review;
            if (count > 0) {
                mobileReviewBadge.textContent = count;
                mobileReviewBadge.style.display = 'flex';
            } else {
                mobileReviewBadge.style.display = 'none';
            }
        }
    }

    updateNotification(type, count) {
        if (this.notifications.hasOwnProperty(type)) {
            this.notifications[type] = count;
            this.updateAllBadges();
            
            // Trigger event untuk komponen lain jika perlu
            this.triggerNotificationUpdate(type, count);
        } else {
            console.warn(`❌ Notification type "${type}" not found`);
        }
    }

    incrementNotification(type, amount = 1) {
        if (this.notifications.hasOwnProperty(type)) {
            this.notifications[type] += amount;
            this.updateAllBadges();
            
            // Trigger event untuk komponen lain jika perlu
            this.triggerNotificationUpdate(type, this.notifications[type]);
        } else {
            console.warn(`❌ Notification type "${type}" not found`);
        }
    }

    decrementNotification(type, amount = 1) {
        if (this.notifications.hasOwnProperty(type)) {
            this.notifications[type] = Math.max(0, this.notifications[type] - amount);
            this.updateAllBadges();
            
            // Trigger event untuk komponen lain jika perlu
            this.triggerNotificationUpdate(type, this.notifications[type]);
        } else {
            console.warn(`❌ Notification type "${type}" not found`);
        }
    }

    resetNotification(type) {
        if (this.notifications.hasOwnProperty(type)) {
            this.notifications[type] = 0;
            this.updateAllBadges();
            
            // Trigger event untuk komponen lain jika perlu
            this.triggerNotificationUpdate(type, 0);
        } else {
            console.warn(`❌ Notification type "${type}" not found`);
        }
    }

    getTotalNotifications() {
        return Object.values(this.notifications).reduce((total, count) => total + count, 0);
    }

    triggerNotificationUpdate(type, count) {
        const event = new CustomEvent('notificationUpdate', {
            detail: {
                type: type,
                count: count,
                total: this.getTotalNotifications()
            }
        });
        document.dispatchEvent(event);
    }

    // ===== TEST FUNCTION UNTUK DEBUG =====
    testNotifications() {
        console.log('🧪 Testing notifications...');
        console.log('📊 Before test:', this.notifications);
        
        // Test increment
        this.incrementNotification('antrian');
        this.incrementNotification('review', 2);
        
        console.log('📊 After increment:', this.notifications);
        
        // Test decrement setelah 2 detik
        setTimeout(() => {
            this.decrementNotification('antrian');
            this.decrementNotification('review');
            console.log('📊 After decrement:', this.notifications);
        }, 2000);
        
        // Test reset setelah 4 detik
        setTimeout(() => {
            this.resetNotification('antrian');
            this.resetNotification('review');
            console.log('📊 After reset:', this.notifications);
        }, 4000);
        
        // Test restore setelah 6 detik
        setTimeout(() => {
            this.updateNotification('antrian', 3);
            this.updateNotification('review', 5);
            console.log('📊 After restore:', this.notifications);
        }, 6000);
    }

    // ===== FLOATING WHATSAPP DRAG FUNCTIONALITY =====
    setupFloatingWhatsApp() {
    const floatingElement = document.getElementById('floatingWhatsApp');
    const whatsappButton = document.getElementById('whatsappButton');

    if (!floatingElement || !whatsappButton) return;

    // Load saved position
    this.loadFloatingButtonPosition(floatingElement);

    // 🆕 Simple drag implementation dengan click preservation
    let isDragging = false;
    let startX, startY, initialX, initialY;

    const startDrag = (e) => {
        // Only start drag jika bukan klik pada link
        if (e.target.closest('a')) {
            return; // Biarkan link bekerja normal
        }

        isDragging = true;
        floatingElement.classList.add('dragging');
        
        e.preventDefault();
        e.stopPropagation();

        // Get initial position
        const rect = floatingElement.getBoundingClientRect();
        initialX = rect.left;
        initialY = rect.top;

        // Get mouse/touch position
        if (e.type === 'mousedown') {
            startX = e.clientX - initialX;
            startY = e.clientY - initialY;
        } else if (e.type === 'touchstart') {
            startX = e.touches[0].clientX - initialX;
            startY = e.touches[0].clientY - initialY;
        }

        // Add global event listeners
        document.addEventListener('mousemove', doDrag);
        document.addEventListener('touchmove', doDrag, { passive: false });
        document.addEventListener('mouseup', stopDrag);
        document.addEventListener('touchend', stopDrag);
    };

    const doDrag = (e) => {
        if (!isDragging) return;

        let clientX, clientY;

        if (e.type === 'mousemove') {
            clientX = e.clientX;
            clientY = e.clientY;
        } else if (e.type === 'touchmove') {
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
            e.preventDefault();
        }

        // Calculate new position
        const newX = clientX - startX;
        const newY = clientY - startY;

        // Apply boundaries
        const maxX = window.innerWidth - floatingElement.offsetWidth;
        const maxY = window.innerHeight - floatingElement.offsetHeight;

        const boundedX = Math.max(0, Math.min(newX, maxX));
        const boundedY = Math.max(0, Math.min(newY, maxY));

        // Apply position
        floatingElement.style.left = boundedX + 'px';
        floatingElement.style.top = boundedY + 'px';
        floatingElement.style.right = 'auto';
        floatingElement.style.bottom = 'auto';
    };

    const stopDrag = (e) => {
        if (!isDragging) return;

        isDragging = false;
        floatingElement.classList.remove('dragging');

        // Save position
        this.saveFloatingButtonPosition(floatingElement);

        // Remove global event listeners
        document.removeEventListener('mousemove', doDrag);
        document.removeEventListener('touchmove', doDrag);
        document.removeEventListener('mouseup', stopDrag);
        document.removeEventListener('touchend', stopDrag);
    };

    // 🆕 Add event listeners hanya untuk element, bukan link
    floatingElement.addEventListener('mousedown', startDrag);
    floatingElement.addEventListener('touchstart', startDrag, { passive: false });

    // 🆕 Biarkan link bekerja normal - HAPUS event listener untuk link
    // whatsappButton.addEventListener('click', ...) // ❌ HAPUS INI
}

    // 🆕 RESET FLOATING BUTTON POSITION
    resetFloatingButtonPosition() {
        const floatingElement = document.getElementById('floatingWhatsApp');
        if (!floatingElement) return;

        // Clear saved position untuk mobile devices
        if (window.innerWidth <= 768) {
            try {
                localStorage.removeItem('prismo_whatsapp_position');
                console.log('🔄 Cleared saved position for mobile device');
            } catch (error) {
                console.warn('⚠️ Could not clear localStorage:', error);
            }
        }

        // Set ke default position berdasarkan screen size
        this.setDefaultPosition(floatingElement);
        
        console.log('📱 Floating button position reset for current screen size');
    }

    // 🆕 SET DEFAULT POSITION
    setDefaultPosition(element) {
        if (!element) return;

        if (window.innerWidth <= 480) {
            // Mobile small
            element.style.left = 'auto';
            element.style.top = 'auto';
            element.style.right = '12px';
            element.style.bottom = '12px';
        } else if (window.innerWidth <= 768) {
            // Tablet
            element.style.left = 'auto';
            element.style.top = 'auto';
            element.style.right = '16px';
            element.style.bottom = '16px';
        } else {
            // Desktop
            element.style.left = 'auto';
            element.style.top = 'auto';
            element.style.right = '24px';
            element.style.bottom = '24px';
        }
        
        console.log('📍 Set default position for screen width:', window.innerWidth);
    }

    // 🆕 PERBAIKAN LOAD POSITION - SUPPORT UNTUK SEMUA DEVICE
    loadFloatingButtonPosition(element) {
    try {
        const saved = localStorage.getItem('prismo_whatsapp_position');
        
        if (saved) {
            const position = JSON.parse(saved);
            
            // Validate position is within viewport
            const maxX = window.innerWidth - element.offsetWidth;
            const maxY = window.innerHeight - element.offsetHeight;
            
            if (position.x <= maxX && position.y <= maxY) {
                element.style.left = position.x + 'px';
                element.style.top = position.y + 'px';
                element.style.right = 'auto';
                element.style.bottom = 'auto';
                return;
            }
        }
        
        // Default position
        this.setDefaultPosition(element);
        
    } catch (error) {
        console.warn('⚠️ Tidak bisa memuat posisi button:', error);
        this.setDefaultPosition(element);
    }
}

    selectStatus(status) {
        this.selectedStatus = status;
        this.updateStatusButtons();
    }

    updateStatusButtons() {
        const openStatusBtn = document.getElementById('openStatusBtn');
        const closeStatusBtn = document.getElementById('closeStatusBtn');

        if (openStatusBtn && closeStatusBtn) {
            // Remove selected class from all buttons
            openStatusBtn.classList.remove('status-btn--selected');
            closeStatusBtn.classList.remove('status-btn--selected');

            // Add selected class to active button
            if (this.selectedStatus === 'open') {
                openStatusBtn.classList.add('status-btn--selected');
            } else {
                closeStatusBtn.classList.add('status-btn--selected');
            }
        }
    }

    showStatusConfirmation() {
        const template = document.getElementById('statusConfirmModalTemplate');
        if (!template) {
            this.saveStatus();
            return;
        }

        const modal = template.content.cloneNode(true);
        const messageElement = modal.querySelector('#statusConfirmMessage');
        const statusText = this.selectedStatus === 'open' ? 'Buka' : 'Tutup';
        
        messageElement.textContent = `Apakah Anda yakin ingin ${statusText.toLowerCase()} steam?`;

        document.body.appendChild(modal);

        const modalElement = document.querySelector('.modal-overlay');
        const cancelButton = modalElement.querySelector('[data-action="cancel"]');
        const confirmButton = modalElement.querySelector('[data-action="confirm-status"]');

        const closeModal = () => {
            if (modalElement && document.body.contains(modalElement)) {
                document.body.removeChild(modalElement);
            }
        };

        cancelButton.addEventListener('click', closeModal);
        confirmButton.addEventListener('click', () => {
            closeModal();
            this.saveStatus();
        });

        this.setupModalFocusTrap(modalElement);
    }

    saveStatus() {
        this.storeStatus = this.selectedStatus;
        
        // Save to database via API
        const isOpen = this.storeStatus === 'open';
        
        fetch('/mitra/update-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify({
                is_open: isOpen
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateStatusDisplay();
                const statusText = this.storeStatus === 'open' ? 'Buka' : 'Tutup';
                this.showAlert('success', 'Berhasil!', `Status steam berhasil diubah menjadi ${statusText}`);
                console.log(`🔄 Store status changed to: ${this.storeStatus}`);
            } else {
                this.showAlert('error', 'Error', data.message || 'Gagal mengubah status');
            }
        })
        .catch(error => {
            console.error('Error saving status:', error);
            this.showAlert('error', 'Error', 'Terjadi kesalahan saat menyimpan status');
        });
    }

    updateStatusDisplay() {
        const statusBadge = document.getElementById('statusBadge');
        const statusText = this.storeStatus === 'open' ? 'Buka' : 'Tutup';

        if (statusBadge) {
            statusBadge.textContent = statusText;
            statusBadge.setAttribute('data-status', this.storeStatus);
        }

        // Update selected status to match current status
        this.selectedStatus = this.storeStatus;
        this.updateStatusButtons();
    }

    showCapacityInfo() {
        const template = document.getElementById('capacityInfoModalTemplate');
        if (!template) return;

        const modal = template.content.cloneNode(true);
        document.body.appendChild(modal);

        const modalElement = document.querySelector('.modal-overlay');
        const closeButton = modalElement.querySelector('[data-action="close"]');

        const closeModal = () => {
            if (modalElement && document.body.contains(modalElement)) {
                document.body.removeChild(modalElement);
            }
        };

        closeButton.addEventListener('click', closeModal);

        this.setupModalFocusTrap(modalElement);
    }

    showBreakInfoModal() {
        const template = document.getElementById('breakInfoModalTemplate');
        if (!template) {
            // Fallback jika template tidak ada
            this.showAlert('info', 'Informasi Sesi Istirahat', 
                'Dengan mengatur sesi istirahat, customer tidak akan bisa booking pada jam yang ditentukan');
            return;
        }

        const modal = template.content.cloneNode(true);
        document.body.appendChild(modal);

        const modalElement = document.querySelector('.modal-overlay');
        const closeButton = modalElement.querySelector('[data-action="close"]');

        const closeModal = () => {
            if (modalElement && document.body.contains(modalElement)) {
                document.body.removeChild(modalElement);
            }
        };

        closeButton.addEventListener('click', closeModal);
        this.setupModalFocusTrap(modalElement);
    }

    handleGlobalClick(event) {
        const target = event.target;

        // Service capacity info button
        if (target.closest('#serviceCapacityInfoBtn')) {
            event.preventDefault();
            event.stopPropagation();
            this.showCapacityInfo();
            return;
        }

        // Break schedule toggles
        if (target.closest('.break-toggle')) {
            event.preventDefault();
            const button = target.closest('.break-toggle');
            const day = button.dataset.day;
            this.toggleBreakSchedule(day);
            return;
        }

        // Break info buttons
        if (target.closest('.break-info-btn')) {
            event.preventDefault();
            event.stopPropagation();
            this.showBreakInfoModal();
            return;
        }

        // Add break schedule buttons
        if (target.closest('.add-break-btn')) {
            event.preventDefault();
            const button = target.closest('.add-break-btn');
            const day = button.dataset.day;
            this.addBreakSchedule(day);
            return;
        }

        // Remove break schedule buttons
        if (target.closest('.remove-break-btn')) {
            event.preventDefault();
            const button = target.closest('.remove-break-btn');
            const day = button.dataset.day;
            const index = parseInt(button.dataset.index);
            this.removeBreakSchedule(day, index);
            return;
        }

        // Edit service buttons
        if (target.closest('.btn--edit')) {
            event.preventDefault();
            event.stopPropagation();
            const button = target.closest('.btn--edit');
            const serviceId = parseInt(button.dataset.serviceId);
            this.showEditServiceModal(serviceId);
            return;
        }

        // Delete service buttons
        if (target.closest('.btn--delete')) {
            event.preventDefault();
            event.stopPropagation();
            
            if (this.isProcessingDelete) {
                return;
            }
            
            const button = target.closest('.btn--delete');
            const serviceId = parseInt(button.dataset.serviceId);
            
            if (!serviceId || isNaN(serviceId)) {
                this.showAlert('error', 'Error', 'ID layanan tidak valid');
                return;
            }
            
            this.isProcessingDelete = true;
            this.showDeleteConfirmation(serviceId);
            
            setTimeout(() => {
                this.isProcessingDelete = false;
            }, 1000);
            
            return;
        }

        // Time input changes
        if (target.classList.contains('time-input')) {
            this.handleTimeInputChange(target);
            return;
        }

        // Modal actions
        if (target.matches('.modal-overlay')) {
            this.closeModal(target);
            return;
        }

        if (target.closest('[data-action="cancel"]')) {
            event.preventDefault();
            event.stopPropagation();
            this.closeCurrentModal();
            return;
        }

        if (target.closest('[data-action="close"]')) {
            event.preventDefault();
            event.stopPropagation();
            this.closeCurrentModal();
            return;
        }

        if (target.closest('[data-action="confirm"]')) {
            event.preventDefault();
            event.stopPropagation();
            this.handleSaveConfirm();
            return;
        }

        if (target.closest('[data-action="confirm-status"]')) {
            event.preventDefault();
            event.stopPropagation();
            this.saveStatus();
            this.closeCurrentModal();
            return;
        }

        if (target.closest('[data-action="confirm-delete"]')) {
            event.preventDefault();
            event.stopPropagation();
            this.handleDeleteConfirm();
            return;
        }

        // Form submissions
        if (target.closest('#serviceForm') && target.type === 'submit') {
            event.preventDefault();
            this.handleServiceSubmit();
            return;
        }

        // Mobile menu toggle
        if (target.closest('.menu-toggle')) {
            this.toggleMobileMenu();
            return;
        }

        // Mobile menu close
        if (target.closest('.mobile-menu__close')) {
            this.closeMobileMenu();
            return;
        }

        // Prevent click during drag
        if (this.isDragging) {
            event.preventDefault();
            event.stopPropagation();
            return;
        }
    }

    // ===== TIME INPUT VALIDATION =====
    handleTimeInputChange(inputElement) {
        const timeValue = inputElement.value;
        const timeRegex = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/;
        
        if (!timeRegex.test(timeValue)) {
            this.showTimeValidationError(inputElement, 'Format waktu harus HH:MM (contoh: 08:00, 14:30)');
            return;
        }

        this.clearTimeValidationError(inputElement);
        this.validateTimePair(inputElement);
    }

    validateTimePair(changedInput) {
        const dayElement = changedInput.closest('.schedule-day');
        if (!dayElement) return;

        const day = dayElement.dataset.day;
        const openInput = document.getElementById(`open-${day}`);
        const closeInput = document.getElementById(`close-${day}`);

        if (!openInput || !closeInput) return;

        const openTime = openInput.value;
        const closeTime = closeInput.value;

        if (!openTime || !closeTime) return;

        const openMinutes = this.timeToMinutes(openTime);
        const closeMinutes = this.timeToMinutes(closeTime);

        if (openMinutes >= closeMinutes) {
            this.showTimeValidationError(openInput, 'Jam buka harus lebih awal dari jam tutup');
            this.showTimeValidationError(closeInput, 'Jam tutup harus lebih akhir dari jam buka');
        } else {
            this.clearTimeValidationError(openInput);
            this.clearTimeValidationError(closeInput);
        }

        this.validateBreakSchedules(day);
    }

    validateBreakSchedules(day) {
        const schedule = this.operationalHours[day];
        if (!schedule.hasBreakSchedule) return;

        const openInput = document.getElementById(`open-${day}`);
        const closeInput = document.getElementById(`close-${day}`);
        const openTime = openInput.value;
        const closeTime = closeInput.value;

        if (!openTime || !closeTime) return;

        schedule.breakSchedules.forEach((breakSchedule, index) => {
            const breakOpenInput = document.getElementById(`break-open-${day}-${index}`);
            const breakCloseInput = document.getElementById(`break-close-${day}-${index}`);

            if (!breakOpenInput || !breakCloseInput) return;

            const breakOpenTime = breakOpenInput.value;
            const breakCloseTime = breakCloseInput.value;

            if (!breakOpenTime || !breakCloseTime) return;

            const openMinutes = this.timeToMinutes(openTime);
            const closeMinutes = this.timeToMinutes(closeTime);
            const breakOpenMinutes = this.timeToMinutes(breakOpenTime);
            const breakCloseMinutes = this.timeToMinutes(breakCloseTime);

            let hasError = false;

            if (breakOpenMinutes < openMinutes || breakCloseMinutes > closeMinutes) {
                this.showTimeValidationError(breakOpenInput, 'Jam istirahat harus dalam jam operasional');
                this.showTimeValidationError(breakCloseInput, 'Jam istirahat harus dalam jam operasional');
                hasError = true;
            }

            if (breakOpenMinutes >= breakCloseMinutes) {
                this.showTimeValidationError(breakOpenInput, 'Jam mulai istirahat harus lebih awal dari jam selesai');
                this.showTimeValidationError(breakCloseInput, 'Jam selesai istirahat harus lebih akhir dari jam mulai');
                hasError = true;
            }

            if (!hasError) {
                this.clearTimeValidationError(breakOpenInput);
                this.clearTimeValidationError(breakCloseInput);
            }
        });
    }

    timeToMinutes(timeString) {
        const [hours, minutes] = timeString.split(':').map(Number);
        return hours * 60 + minutes;
    }

    showTimeValidationError(inputElement, message) {
        inputElement.classList.add('error');
        
        let validationElement = inputElement.nextElementSibling;
        if (!validationElement || !validationElement.classList.contains('time-validation-message')) {
            validationElement = document.createElement('div');
            validationElement.className = 'time-validation-message';
            inputElement.parentNode.appendChild(validationElement);
        }
        
        validationElement.textContent = message;
        validationElement.classList.add('show');
    }

    clearTimeValidationError(inputElement) {
        inputElement.classList.remove('error');
        
        const validationElement = inputElement.nextElementSibling;
        if (validationElement && validationElement.classList.contains('time-validation-message')) {
            validationElement.classList.remove('show');
        }
    }

    // ===== OPERATIONAL SCHEDULE MANAGEMENT =====
    displayOperationalSchedule() {
        const container = document.getElementById('timeSchedule');
        if (!container) return;

        container.innerHTML = '';

        const days = [
            { key: 'monday', name: 'Senin' },
            { key: 'tuesday', name: 'Selasa' },
            { key: 'wednesday', name: 'Rabu' },
            { key: 'thursday', name: 'Kamis' },
            { key: 'friday', name: 'Jumat' },
            { key: 'saturday', name: 'Sabtu' },
            { key: 'sunday', name: 'Minggu' }
        ];

        days.forEach(day => {
            const schedule = this.operationalHours[day.key];
            if (schedule.enabled) {
                const scheduleElement = this.createScheduleElement(day, schedule);
                container.appendChild(scheduleElement);
            }
        });

        this.updateDayCheckboxes();
    }

    createScheduleElement(day, schedule) {
        const element = document.createElement('div');
        element.className = 'schedule-day';
        element.setAttribute('data-day', day.key);

        element.innerHTML = `
            <div class="schedule-header">
                <span class="schedule-day-name">${day.name}</span>
                <div class="break-toggle-container">
                    <button type="button" class="break-toggle ${schedule.hasBreakSchedule ? 'break-toggle--active' : ''}" 
                            data-day="${day.key}">
                        ${schedule.hasBreakSchedule ? 'Hapus Sesi Istirahat' : 'Atur Istirahat'}
                    </button>
                    <button type="button" class="break-info-btn" data-day="${day.key}" 
                            aria-label="Informasi sesi istirahat">
                        <img src="/images/tanya.png" alt="Info" width="16" height="16">
                    </button>
                </div>
            </div>
            
            <div class="day-time-inputs">
                <div class="time-input-group">
                    <label for="open-${day.key}" class="time-label">Buka:</label>
                    <input type="text" id="open-${day.key}" class="time-input" 
                           value="${schedule.open}" placeholder="08:00" maxlength="5"
                           pattern="([0-1]?[0-9]|2[0-3]):[0-5][0-9]">
                    <div class="time-validation-message"></div>
                </div>
                <div class="time-separator">-</div>
                <div class="time-input-group">
                    <label for="close-${day.key}" class="time-label">Tutup:</label>
                    <input type="text" id="close-${day.key}" class="time-input" 
                           value="${schedule.close}" placeholder="17:00" maxlength="5"
                           pattern="([0-1]?[0-9]|2[0-3]):[0-5][0-9]">
                    <div class="time-validation-message"></div>
                </div>
            </div>

            ${schedule.hasBreakSchedule ? this.createBreakSchedules(day.key, schedule) : ''}
        `;

        const openInput = element.querySelector(`#open-${day.key}`);
        const closeInput = element.querySelector(`#close-${day.key}`);

        if (openInput && closeInput) {
            openInput.addEventListener('input', () => this.handleTimeInputChange(openInput));
            openInput.addEventListener('blur', () => this.handleTimeInputChange(openInput));
            closeInput.addEventListener('input', () => this.handleTimeInputChange(closeInput));
            closeInput.addEventListener('blur', () => this.handleTimeInputChange(closeInput));
        }

        // Add info button event listener
        const infoBtn = element.querySelector('.break-info-btn');
        if (infoBtn) {
            infoBtn.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.showBreakInfoModal();
            });
        }

        return element;
    }

    createBreakSchedules(dayKey, schedule) {
        const breakSchedules = schedule.breakSchedules;
        let breakSchedulesHTML = '';
        
        breakSchedules.forEach((breakSchedule, index) => {
            breakSchedulesHTML += `
                <div class="break-schedule-item">
                    <div class="break-schedule-header">
                        <span class="break-session-label">Sesi Istirahat ${index + 1}</span>
                        ${breakSchedules.length > 0 ? `
                            <button type="button" class="remove-break-btn btn btn--danger btn--small" 
                                    data-day="${dayKey}" data-index="${index}">
                                Hapus
                            </button>
                        ` : ''}
                    </div>
                    <div class="break-time-inputs">
                        <div class="time-input-group">
                            <label for="break-open-${dayKey}-${index}" class="time-label">Mulai:</label>
                            <input type="text" id="break-open-${dayKey}-${index}" class="time-input" 
                                   value="${breakSchedule.open}" placeholder="12:00" maxlength="5"
                                   pattern="([0-1]?[0-9]|2[0-3]):[0-5][0-9]">
                            <div class="time-validation-message"></div>
                        </div>
                        <div class="time-separator">-</div>
                        <div class="time-input-group">
                            <label for="break-close-${dayKey}-${index}" class="time-label">Selesai:</label>
                            <input type="text" id="break-close-${dayKey}-${index}" class="time-input" 
                                   value="${breakSchedule.close}" placeholder="13:00" maxlength="5"
                                   pattern="([0-1]?[0-9]|2[0-3]):[0-5][0-9]">
                            <div class="time-validation-message"></div>
                        </div>
                    </div>
                </div>
            `;
        });

        const breakSchedulesElement = document.createElement('div');
        breakSchedulesElement.className = 'break-schedule-options';
        breakSchedulesElement.innerHTML = `
            <div class="break-schedules-list">
                ${breakSchedulesHTML}
            </div>
            <button type="button" class="add-break-btn btn btn--secondary btn--small" 
                    data-day="${dayKey}">
                + Tambah Sesi Istirahat
            </button>
        `;

        breakSchedules.forEach((_, index) => {
            const breakOpenInput = breakSchedulesElement.querySelector(`#break-open-${dayKey}-${index}`);
            const breakCloseInput = breakSchedulesElement.querySelector(`#break-close-${dayKey}-${index}`);

            if (breakOpenInput && breakCloseInput) {
                breakOpenInput.addEventListener('input', () => this.handleTimeInputChange(breakOpenInput));
                breakOpenInput.addEventListener('blur', () => this.handleTimeInputChange(breakOpenInput));
                breakCloseInput.addEventListener('input', () => this.handleTimeInputChange(breakCloseInput));
                breakCloseInput.addEventListener('blur', () => this.handleTimeInputChange(breakCloseInput));
            }
        });

        return breakSchedulesElement.outerHTML;
    }

    getDayName(dayKey) {
        const dayNames = {
            monday: 'Senin',
            tuesday: 'Selasa',
            wednesday: 'Rabu',
            thursday: 'Kamis',
            friday: 'Jumat',
            saturday: 'Sabtu',
            sunday: 'Minggu'
        };
        return dayNames[dayKey];
    }

    toggleDaySchedule(day, enabled) {
        this.operationalHours[day].enabled = enabled;
        this.displayOperationalSchedule();
    }

    toggleBreakSchedule(day, enabled = null) {
        const schedule = this.operationalHours[day];
        
        if (enabled === null) {
            schedule.hasBreakSchedule = !schedule.hasBreakSchedule;
        } else {
            schedule.hasBreakSchedule = enabled;
        }

        if (schedule.hasBreakSchedule && schedule.breakSchedules.length === 0) {
            schedule.breakSchedules = [
                { open: "12:00", close: "13:00" }
            ];
        }

        this.displayOperationalSchedule();
    }

    addBreakSchedule(day) {
        const schedule = this.operationalHours[day];
        schedule.breakSchedules.push({ open: "14:00", close: "15:00" });
        this.displayOperationalSchedule();
    }

    removeBreakSchedule(day, index) {
        const schedule = this.operationalHours[day];
        schedule.breakSchedules.splice(index, 1);
        
        if (schedule.breakSchedules.length === 0) {
            schedule.hasBreakSchedule = false;
        }
        
        this.displayOperationalSchedule();
    }

    updateDayCheckboxes() {
        const checkboxes = document.querySelectorAll('input[name="operational-days"]');
        checkboxes.forEach(checkbox => {
            const day = checkbox.value;
            checkbox.checked = this.operationalHours[day].enabled;
        });
    }

    showSaveConfirmation() {
        if (!this.validateAllTimes()) {
            return;
        }

        const template = document.getElementById('confirmModalTemplate');
        if (!template) {
            this.saveOperationalHours();
            return;
        }

        const modal = template.content.cloneNode(true);
        const messageElement = modal.querySelector('#confirmMessage');
        messageElement.textContent = 'Apakah Anda yakin ingin menyimpan perubahan jam operasional?';

        document.body.appendChild(modal);

        const modalElement = document.querySelector('.modal-overlay');
        const cancelButton = modalElement.querySelector('[data-action="cancel"]');
        const confirmButton = modalElement.querySelector('[data-action="confirm"]');

        const closeModal = () => {
            if (modalElement && document.body.contains(modalElement)) {
                document.body.removeChild(modalElement);
            }
        };

        cancelButton.addEventListener('click', closeModal);
        confirmButton.addEventListener('click', () => {
            closeModal();
            this.saveOperationalHours();
        });

        this.setupModalFocusTrap(modalElement);
    }

    validateAllTimes() {
        let isValid = true;
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        days.forEach(day => {
            const schedule = this.operationalHours[day];
            if (schedule.enabled) {
                const openInput = document.getElementById(`open-${day}`);
                const closeInput = document.getElementById(`close-${day}`);

                if (openInput && closeInput) {
                    this.validateTimePair(openInput);
                    
                    if (openInput.classList.contains('error') || closeInput.classList.contains('error')) {
                        isValid = false;
                    }
                }
            }
        });

        if (!isValid) {
            this.showAlert('error', 'Error', 'Harap perbaiki kesalahan pada jam operasional sebelum menyimpan');
        }

        return isValid;
    }

    handleSaveConfirm() {
        this.saveOperationalHours();
        this.closeCurrentModal();
    }

    saveOperationalHours() {
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        let hasError = false;
        let errorMessage = '';

        days.forEach(day => {
            const schedule = this.operationalHours[day];
            
            if (schedule.enabled) {
                const openInput = document.getElementById(`open-${day}`);
                const closeInput = document.getElementById(`close-${day}`);

                const openTime = openInput?.value;
                const closeTime = closeInput?.value;

                if (!openTime || !closeTime) {
                    errorMessage = `Harap masukkan jam operasional untuk ${this.getDayName(day)}`;
                    hasError = true;
                    return;
                }

                const timeRegex = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/;
                if (!timeRegex.test(openTime) || !timeRegex.test(closeTime)) {
                    errorMessage = `Format waktu tidak valid untuk ${this.getDayName(day)}`;
                    hasError = true;
                    return;
                }

                if (openTime >= closeTime) {
                    errorMessage = `Jam tutup harus lebih besar dari jam buka untuk ${this.getDayName(day)}`;
                    hasError = true;
                    return;
                }

                schedule.open = openTime;
                schedule.close = closeTime;

                if (schedule.hasBreakSchedule) {
                    schedule.breakSchedules.forEach((breakSchedule, index) => {
                        const breakOpenInput = document.getElementById(`break-open-${day}-${index}`);
                        const breakCloseInput = document.getElementById(`break-close-${day}-${index}`);

                        const breakOpen = breakOpenInput?.value;
                        const breakClose = breakCloseInput?.value;

                        if (!breakOpen || !breakClose) {
                            errorMessage = `Harap lengkapi jam istirahat untuk ${this.getDayName(day)}`;
                            hasError = true;
                            return;
                        }

                        if (!timeRegex.test(breakOpen) || !timeRegex.test(breakClose)) {
                            errorMessage = `Format waktu istirahat tidak valid untuk ${this.getDayName(day)}`;
                            hasError = true;
                            return;
                        }

                        if (breakOpen >= breakClose) {
                            errorMessage = `Jam selesai istirahat harus lebih besar dari jam mulai untuk ${this.getDayName(day)}`;
                            hasError = true;
                            return;
                        }

                        if (breakOpen < openTime || breakClose > closeTime) {
                            errorMessage = `Jam istirahat harus berada dalam jam operasional untuk ${this.getDayName(day)}`;
                            hasError = true;
                            return;
                        }

                        breakSchedule.open = breakOpen;
                        breakSchedule.close = breakClose;
                    });
                }
            }
        });

        if (hasError) {
            this.showAlert('error', 'Error', errorMessage);
            return;
        }

        const enabledDays = days.filter(day => this.operationalHours[day].enabled);
        if (enabledDays.length === 0) {
            this.showAlert('error', 'Error', 'Harap pilih minimal satu hari operasional');
            return;
        }

        // Save to database via API
        fetch('/mitra/update-operational-hours', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                operational_hours: this.operationalHours
            })
        })
        .then(response => {
            if (response.status === 401 || response.status === 419) {
                console.log('🔒 Session expired, redirecting to login...');
                window.location.href = '/login';
                return;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;
            if (data.success) {
                this.showAlert('success', 'Berhasil!', 'Jam operasional berhasil disimpan!');
                console.log('📅 Operational hours saved:', this.operationalHours);
            } else {
                this.showAlert('error', 'Error', data.message || 'Gagal menyimpan jam operasional');
            }
        })
        .catch(error => {
            console.error('Error saving operational hours:', error);
            this.showAlert('error', 'Error', 'Terjadi kesalahan saat menyimpan');
        });
    }

    // ===== ALERT SYSTEM =====
    showAlert(type, title, message) {
        const existingAlert = document.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }

        const alert = document.createElement('div');
        alert.className = `alert alert--${type}`;
        
        const icon = type === 'success' ? '✓' : 
                    type === 'error' ? '✕' : 
                    type === 'warning' ? '⚠️' : 'ℹ️';
        
        alert.innerHTML = `
            <div class="alert__icon">${icon}</div>
            <div class="alert__content">
                <div class="alert__title">${title}</div>
                <div class="alert__message">${message}</div>
            </div>
            <button class="alert__close" aria-label="Tutup alert">✕</button>
        `;

        document.body.appendChild(alert);

        const closeBtn = alert.querySelector('.alert__close');
        closeBtn.addEventListener('click', () => {
            this.closeAlert(alert);
        });

        setTimeout(() => {
            if (document.body.contains(alert)) {
                this.closeAlert(alert);
            }
        }, 5000);
    }

    closeAlert(alert) {
        alert.classList.add('alert--closing');
        setTimeout(() => {
            if (document.body.contains(alert)) {
                alert.remove();
            }
        }, 300);
    }

    // ===== SERVICES MANAGEMENT =====
    displayServices() {
        const container = document.getElementById('servicesList');
        if (!container) return;

        container.innerHTML = '';

        if (this.services.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <p>Belum ada layanan. Tambah layanan pertama Anda!</p>
                </div>
            `;
            return;
        }

        this.services.forEach(service => {
            const serviceElement = this.createServiceElement(service);
            container.appendChild(serviceElement);
        });
    }

    createServiceElement(service) {
        const element = document.createElement('div');
        element.className = 'service-item';
        element.setAttribute('data-service-id', service.id);

        element.innerHTML = `
            <div class="service-header">
                <div class="service-info">
                    <h3 class="service-name">${this.escapeHtml(service.name)}</h3>
                    <div class="service-price">Rp ${service.price.toLocaleString('id-ID')}</div>
                    <div class="service-capacity">
                        <span class="service-capacity-label">Kapasitas Harian:</span>
                        <span class="service-capacity-value">${service.capacity} slot</span>
                    </div>
                    <div class="service-description">${this.escapeHtml(service.description)}</div>
                </div>
                <div class="service-actions">
                    <button class="btn--edit" 
                            data-service-id="${service.id}"
                            aria-label="Edit layanan ${this.escapeHtml(service.name)}">
                        Edit
                    </button>
                    <button class="btn--delete" 
                            data-service-id="${service.id}"
                            aria-label="Hapus layanan ${this.escapeHtml(service.name)}">
                        Hapus
                    </button>
                </div>
            </div>
        `;

        return element;
    }

    showAddServiceModal() {
        const template = document.getElementById('serviceModalTemplate');
        if (!template) {
            this.showAlert('error', 'Error', 'Template modal tidak ditemukan');
            return;
        }

        const modal = template.content.cloneNode(true);
        document.body.appendChild(modal);

        const modalElement = document.querySelector('.modal-overlay');
        const closeButton = modalElement.querySelector('.modal__close');
        const cancelButton = modalElement.querySelector('[data-action="cancel"]');
        const form = modalElement.querySelector('#serviceForm');
        const capacityInput = modalElement.querySelector('#serviceCapacity');
        const capacityInfoBtn = modalElement.querySelector('#serviceCapacityInfoBtn');

        // Setup capacity info button
        if (capacityInfoBtn) {
            capacityInfoBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.showCapacityInfo();
            });
        }

        // Setup capacity input validation
        if (capacityInput) {
            capacityInput.addEventListener('input', (event) => {
                const newCapacity = parseInt(event.target.value);
                if (newCapacity < 1) {
                    event.target.value = 1;
                } else if (newCapacity > 50) {
                    event.target.value = 50;
                }
            });

            capacityInput.addEventListener('blur', (event) => {
                const value = parseInt(event.target.value);
                if (isNaN(value) || value < 1) {
                    event.target.value = 7;
                } else if (value > 50) {
                    event.target.value = 50;
                }
            });
        }

        const closeModal = () => {
            if (modalElement && document.body.contains(modalElement)) {
                document.body.removeChild(modalElement);
            }
        };

        closeButton.addEventListener('click', closeModal);
        cancelButton.addEventListener('click', closeModal);

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            this.handleServiceSubmit();
        });

        const firstInput = modalElement.querySelector('#serviceName');
        if (firstInput) {
            setTimeout(() => {
                firstInput.focus();
            }, 100);
        }

        this.setupModalFocusTrap(modalElement);
    }

    showEditServiceModal(serviceId) {
        const service = this.services.find(s => s.id === serviceId);
        if (!service) {
            this.showAlert('error', 'Error', 'Layanan tidak ditemukan');
            return;
        }

        this.editingService = service;

        const template = document.getElementById('serviceModalTemplate');
        if (!template) {
            this.showAlert('error', 'Error', 'Template modal tidak ditemukan');
            return;
        }

        const modal = template.content.cloneNode(true);
        const modalTitle = modal.querySelector('#serviceModalTitle');
        const submitBtn = modal.querySelector('#serviceSubmitBtn');
        const serviceIdInput = modal.querySelector('#serviceId');
        const serviceNameInput = modal.querySelector('#serviceName');
        const servicePriceInput = modal.querySelector('#servicePrice');
        const serviceCapacityInput = modal.querySelector('#serviceCapacity');
        const serviceDescriptionInput = modal.querySelector('#serviceDescription');
        const capacityInfoBtn = modal.querySelector('#serviceCapacityInfoBtn');

        modalTitle.textContent = 'Edit Layanan';
        submitBtn.textContent = 'Update Layanan';
        serviceIdInput.value = service.id;
        serviceNameInput.value = service.name;
        servicePriceInput.value = service.price;
        serviceCapacityInput.value = service.capacity;
        serviceDescriptionInput.value = service.description;

        document.body.appendChild(modal);

        const modalElement = document.querySelector('.modal-overlay');
        const closeButton = modalElement.querySelector('.modal__close');
        const cancelButton = modalElement.querySelector('[data-action="cancel"]');
        const form = modalElement.querySelector('#serviceForm');

        // Setup capacity info button
        if (capacityInfoBtn) {
            capacityInfoBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.showCapacityInfo();
            });
        }

        // Setup capacity input validation
        if (serviceCapacityInput) {
            serviceCapacityInput.addEventListener('input', (event) => {
                const newCapacity = parseInt(event.target.value);
                if (newCapacity < 1) {
                    event.target.value = 1;
                } else if (newCapacity > 50) {
                    event.target.value = 50;
                }
            });

            serviceCapacityInput.addEventListener('blur', (event) => {
                const value = parseInt(event.target.value);
                if (isNaN(value) || value < 1) {
                    event.target.value = service.capacity;
                } else if (value > 50) {
                    event.target.value = 50;
                }
            });
        }

        const closeModal = () => {
            if (modalElement && document.body.contains(modalElement)) {
                document.body.removeChild(modalElement);
            }
            this.editingService = null;
        };

        closeButton.addEventListener('click', closeModal);
        cancelButton.addEventListener('click', closeModal);

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            this.handleServiceSubmit();
        });

        setTimeout(() => {
            serviceNameInput.focus();
        }, 100);

        this.setupModalFocusTrap(modalElement);
    }

    showDeleteConfirmation(serviceId) {
        const service = this.services.find(s => s.id === serviceId);
        
        if (!service) {
            this.showAlert('error', 'Error', 'Layanan tidak ditemukan');
            this.isProcessingDelete = false;
            return;
        }

        this.serviceToDelete = serviceId;

        const template = document.getElementById('deleteConfirmModalTemplate');
        if (!template) {
            if (confirm(`Apakah Anda yakin ingin menghapus layanan "${service.name}"?`)) {
                this.deleteService(serviceId);
            }
            this.isProcessingDelete = false;
            return;
        }

        const modal = template.content.cloneNode(true);
        const messageElement = modal.querySelector('#deleteConfirmMessage');
        messageElement.textContent = `Apakah Anda yakin ingin menghapus layanan "${service.name}"?`;

        document.body.appendChild(modal);

        const modalElement = document.querySelector('.modal-overlay');
        const cancelButton = modalElement.querySelector('[data-action="cancel"]');
        const confirmButton = modalElement.querySelector('[data-action="confirm-delete"]');

        modalElement._serviceId = serviceId;

        const closeModal = () => {
            if (modalElement && document.body.contains(modalElement)) {
                document.body.removeChild(modalElement);
            }
            this.isProcessingDelete = false;
        };

        cancelButton.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.serviceToDelete = null;
            closeModal();
        });

        confirmButton.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            closeModal();
            setTimeout(() => {
                this.handleDeleteConfirm();
            }, 100);
        });

        this.setupModalFocusTrap(modalElement);
    }

    handleDeleteConfirm() {
        let serviceIdToDelete = this.serviceToDelete;
        
        if (serviceIdToDelete === null || serviceIdToDelete === undefined) {
            const recentModal = document.querySelector('.modal-overlay[data-recent-delete]');
            if (recentModal && recentModal._serviceId) {
                serviceIdToDelete = recentModal._serviceId;
            }
        }
        
        if (serviceIdToDelete !== null && serviceIdToDelete !== undefined) {
            this.deleteService(serviceIdToDelete);
            this.serviceToDelete = null;
        } else {
            this.showAlert('error', 'Error', 'Tidak ada layanan yang dipilih untuk dihapus');
        }
        
        this.isProcessingDelete = false;
    }

    handleServiceSubmit() {
        const form = document.querySelector('#serviceForm');
        if (!form) {
            this.showAlert('error', 'Error', 'Form tidak ditemukan');
            return;
        }

        const serviceIdInput = form.querySelector('#serviceId');
        const nameInput = form.querySelector('#serviceName');
        const priceInput = form.querySelector('#servicePrice');
        const capacityInput = form.querySelector('#serviceCapacity');
        const descriptionInput = form.querySelector('#serviceDescription');

        const serviceId = serviceIdInput.value ? parseInt(serviceIdInput.value) : null;
        const name = nameInput.value.trim();
        const price = parseInt(priceInput.value);
        const capacity = parseInt(capacityInput.value);
        const description = descriptionInput.value.trim();

        if (!name) {
            this.showAlert('error', 'Error', 'Harap masukkan nama layanan');
            nameInput.focus();
            return;
        }

        if (!price || price < 0) {
            this.showAlert('error', 'Error', 'Harap masukkan harga yang valid');
            priceInput.focus();
            return;
        }

        if (!capacity || capacity < 1 || capacity > 50) {
            this.showAlert('error', 'Error', 'Harap masukkan kapasitas antara 1-50');
            capacityInput.focus();
            return;
        }

        if (!description) {
            this.showAlert('error', 'Error', 'Harap masukkan deskripsi layanan');
            descriptionInput.focus();
            return;
        }

        // Check if this is a default service package (Basic/Premium/Complete)
        const isDefaultPackage = name === 'Basic Steam' || name === 'Premium Steam' || name === 'Complete Steam';
        
        if (isDefaultPackage) {
            // Save to database for default packages
            const priceField = name === 'Basic Steam' ? 'basic_price' : 
                              name === 'Premium Steam' ? 'premium_price' : 'complete_price';
            
            const requestBody = {};
            requestBody[priceField] = price;
            
            fetch('/mitra/update-service-prices', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify(requestBody)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (serviceId) {
                        const serviceIndex = this.services.findIndex(s => s.id === serviceId);
                        if (serviceIndex !== -1) {
                            this.services[serviceIndex] = { id: serviceId, name, price, capacity, max_slots: capacity, description };
                        }
                    }
                    this.displayServices();
                    this.closeCurrentModal();
                    this.showAlert('success', 'Berhasil!', 'Harga layanan berhasil diupdate!');
                } else {
                    this.showAlert('error', 'Error', data.message || 'Gagal menyimpan harga layanan');
                }
            })
            .catch(error => {
                console.error('Error saving service price:', error);
                this.showAlert('error', 'Error', 'Terjadi kesalahan saat menyimpan');
            });
            return;
        }

        // Handle custom services (non-default packages)
        if (serviceId) {
            const serviceIndex = this.services.findIndex(s => s.id === serviceId);
            if (serviceIndex !== -1) {
                this.services[serviceIndex] = {
                    id: serviceId,
                    name,
                    price,
                    capacity,
                    max_slots: capacity,
                    description
                };
                
                // Save to database
                this.saveCustomServices()
                    .then(() => {
                        this.displayServices();
                        this.closeCurrentModal();
                        this.showAlert('success', 'Berhasil!', 'Layanan berhasil diupdate!');
                    })
                    .catch(error => {
                        console.error('Error saving custom service:', error);
                        const errorMessage = error.message || 'Gagal menyimpan layanan';
                        this.showAlert('error', 'Error', errorMessage);
                    });
            }
        } else {
            const newService = {
                id: Date.now(),
                name,
                price,
                capacity,
                max_slots: capacity,
                description
            };

            this.services.push(newService);
            
            // Save to database
            this.saveCustomServices()
                .then(() => {
                    this.displayServices();
                    this.closeCurrentModal();
                    this.showAlert('success', 'Berhasil!', 'Layanan berhasil ditambahkan!');
                })
                .catch(error => {
                    console.error('Error saving custom service:', error);
                    const errorMessage = error.message || 'Gagal menyimpan layanan';
                    this.showAlert('error', 'Error', errorMessage);
                    // Remove the service from array if save failed
                    this.services.pop();
                });
        }
    }

    deleteService(serviceId) {
        const serviceIndex = this.services.findIndex(s => s.id === serviceId);
        if (serviceIndex === -1) {
            this.showAlert('error', 'Error', 'Layanan tidak ditemukan');
            return;
        }

        const service = this.services[serviceIndex];
        
        this.services.splice(serviceIndex, 1);
        
        // Save to database after deletion
        this.saveCustomServices()
            .then(() => {
                this.displayServices();
                this.showAlert('success', 'Berhasil!', 'Layanan berhasil dihapus!');
            })
            .catch(error => {
                console.error('Error deleting custom service:', error);
                this.showAlert('error', 'Error', 'Gagal menghapus layanan');
                // Rollback: add the service back
                this.services.splice(serviceIndex, 0, service);
                this.displayServices();
            });
    }

    getCurrentState() {
        return {
            serviceToDelete: this.serviceToDelete,
            services: this.services.map(s => ({ id: s.id, name: s.name })),
            isProcessingDelete: this.isProcessingDelete
        };
    }

    // ===== UTILITY METHODS =====
    handleGlobalKeydown(event) {
        if (event.key === 'Escape') {
            const openModal = document.querySelector('.modal-overlay');
            if (openModal) {
                this.closeModal(openModal);
            }
            
            if (this.isMobileMenuOpen) {
                this.closeMobileMenu();
            }
        }
    }

    setupModalFocusTrap(modal) {
        const focusableElements = modal.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );

        if (focusableElements.length === 0) return;

        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        const handleTabKey = (event) => {
            if (event.key !== 'Tab') return;

            if (event.shiftKey) {
                if (document.activeElement === firstElement) {
                    event.preventDefault();
                    lastElement.focus();
                }
            } else {
                if (document.activeElement === lastElement) {
                    event.preventDefault();
                    firstElement.focus();
                }
            }
        };

        modal.addEventListener('keydown', handleTabKey);
        firstElement.focus();

        modal._focusTrapHandler = handleTabKey;
    }

    closeCurrentModal() {
        const modal = document.querySelector('.modal-overlay');
        if (modal) {
            if (modal._focusTrapHandler) {
                modal.removeEventListener('keydown', modal._focusTrapHandler);
            }
            if (document.body.contains(modal)) {
                document.body.removeChild(modal);
            }
        }
    }

    closeModal(modal) {
        if (modal._focusTrapHandler) {
            modal.removeEventListener('keydown', modal._focusTrapHandler);
        }
        if (document.body.contains(modal)) {
            document.body.removeChild(modal);
        }
    }

    escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // ===== MOBILE MENU =====
    setupMobileMenu() {
        const template = document.getElementById('mobileMenuTemplate');
        if (template) {
            const mobileMenu = template.content.cloneNode(true);
            document.body.appendChild(mobileMenu);
            this.setupMobileMenuEvents();
        }
    }

    setupMobileMenuEvents() {
        const mobileMenu = document.getElementById('mobileMenu');
        const closeButton = document.getElementById('mobileMenuClose');
        const menuItems = mobileMenu.querySelectorAll('.mobile-nav__item');

        if (!mobileMenu || !closeButton) return;

        closeButton.addEventListener('click', () => {
            this.closeMobileMenu();
        });

        // Update notifikasi badges di mobile menu
        this.updateMobileBadges();

        mobileMenu.addEventListener('click', (event) => {
            if (event.target === mobileMenu) {
                this.closeMobileMenu();
            }
        });

        mobileMenu.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                this.closeMobileMenu();
            }
        });
    }

    toggleMobileMenu() {
        if (this.isMobileMenuOpen) {
            this.closeMobileMenu();
        } else {
            this.openMobileMenu();
        }
    }

    openMobileMenu() {
        const mobileMenu = document.getElementById('mobileMenu');
        const menuToggle = document.getElementById('menuToggle');

        if (mobileMenu && menuToggle) {
            mobileMenu.classList.add('mobile-menu--open');
            menuToggle.setAttribute('aria-expanded', 'true');
            menuToggle.classList.add('menu-toggle--active');
            this.isMobileMenuOpen = true;
            document.body.style.overflow = 'hidden';

            this.setupMobileMenuFocusTrap();
        }
    }

    closeMobileMenu() {
        const mobileMenu = document.getElementById('mobileMenu');
        const menuToggle = document.getElementById('menuToggle');

        if (mobileMenu && menuToggle) {
            mobileMenu.classList.remove('mobile-menu--open');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.classList.remove('menu-toggle--active');
            this.isMobileMenuOpen = false;
            document.body.style.overflow = '';

            menuToggle.focus();
        }
    }

    setupMobileMenuFocusTrap() {
        const mobileMenu = document.getElementById('mobileMenu');
        if (!mobileMenu) return;

        const focusableElements = mobileMenu.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );

        if (focusableElements.length === 0) return;

        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        const handleTabKey = (event) => {
            if (event.key !== 'Tab') return;

            if (event.shiftKey) {
                if (document.activeElement === firstElement) {
                    event.preventDefault();
                    lastElement.focus();
                }
            } else {
                if (document.activeElement === lastElement) {
                    event.preventDefault();
                    firstElement.focus();
                }
            }
        };

        mobileMenu.addEventListener('keydown', handleTabKey);
        firstElement.focus();

        mobileMenu._focusTrapHandler = handleTabKey;
    }

    getServicesStats() {
        return {
            total: this.services.length,
            totalRevenue: this.services.reduce((sum, service) => sum + service.price, 0),
            averagePrice: this.services.length > 0 
                ? Math.round(this.services.reduce((sum, service) => sum + service.price, 0) / this.services.length)
                : 0,
            totalCapacity: this.services.reduce((sum, service) => sum + service.capacity, 0),
            averageCapacity: this.services.length > 0 
                ? Math.round(this.services.reduce((sum, service) => sum + service.capacity, 0) / this.services.length)
                : 0
        };
    }

    getOperationalSummary() {
        const enabledDays = Object.entries(this.operationalHours)
            .filter(([day, schedule]) => schedule.enabled)
            .map(([day, schedule]) => ({
                day,
                schedule
            }));

        return {
            enabledDays,
            hasBreakSchedule: Object.values(this.operationalHours).some(schedule => schedule.hasBreakSchedule),
            status: this.storeStatus
        };
    }

    // 🆕 CLEANUP METHOD
    destroy() {
        if (this._resizeHandler) {
            window.removeEventListener('resize', this._resizeHandler);
            console.log('🧹 Resize listener cleaned up');
        }
        console.log('🧹 Dashboard manager cleaned up');
    }
}

// ===== INITIALIZATION =====
// Avatar now loaded from database via Blade template - no localStorage needed

document.addEventListener('DOMContentLoaded', () => {
    try {
        const dashboardManager = new PrismoDashboardManager();
        window.prismoDashboard = dashboardManager;
        
        // Expose methods untuk debugging dan testing
        window.getServicesStats = () => dashboardManager.getServicesStats();
        window.getOperationalSummary = () => dashboardManager.getOperationalSummary();
        window.getCurrentState = () => dashboardManager.getCurrentState();
        
        // Expose notification methods
        window.updateNotification = (type, count) => dashboardManager.updateNotification(type, count);
        window.incrementNotification = (type, amount) => dashboardManager.incrementNotification(type, amount);
        window.decrementNotification = (type, amount) => dashboardManager.decrementNotification(type, amount);
        window.resetNotification = (type) => dashboardManager.resetNotification(type);
        window.getTotalNotifications = () => dashboardManager.getTotalNotifications();
        
        // Expose floating button methods
        window.resetFloatingButton = () => dashboardManager.resetFloatingButtonPosition();
        
        // Expose test function
        window.testDashboardNotifications = () => {
            if (window.prismoDashboard) {
                window.prismoDashboard.testNotifications();
            }
        };
        
        console.log('🎉 PRISMO Dashboard System loaded successfully');
        console.log('📱 Notifications:', dashboardManager.notifications);
        console.log('📱 Total notifications:', dashboardManager.getTotalNotifications());
        console.log('📊 Services stats:', dashboardManager.getServicesStats());
        
        // Listen untuk notification updates
        document.addEventListener('notificationUpdate', (event) => {
            console.log('🔔 Notification updated:', event.detail);
        });
        
        // Cleanup saat page unload
        window.addEventListener('beforeunload', () => {
            if (window.prismoDashboard) {
                window.prismoDashboard.destroy();
            }
        });
        
    } catch (error) {
        console.error('❌ Failed to load PRISMO Dashboard System:', error);
        
        const main = document.getElementById('mainContent');
        if (main) {
            main.innerHTML = `
                <div class="empty-state">
                    <p>Terjadi kesalahan saat memuat dashboard. Silakan refresh halaman.</p>
                    <button onclick="location.reload()" class="btn btn--primary" style="margin-top: 1rem;">
                        Refresh Halaman
                    </button>
                </div>
            `;
        }
    }
});

// Debug utilities untuk testing notifikasi
function testNotifications() {
    if (window.prismoDashboard) {
        console.log('🧪 Testing notifications...');
        
        // Test increment
        window.prismoDashboard.incrementNotification('antrian');
        window.prismoDashboard.incrementNotification('review', 2);
        
        // Test decrement setelah 2 detik
        setTimeout(() => {
            window.prismoDashboard.decrementNotification('antrian');
            window.prismoDashboard.decrementNotification('review');
        }, 2000);
        
        // Test reset setelah 4 detik
        setTimeout(() => {
            window.prismoDashboard.resetNotification('antrian');
            window.prismoDashboard.resetNotification('review');
        }, 4000);
        
        // Test restore setelah 6 detik
        setTimeout(() => {
            window.prismoDashboard.updateNotification('antrian', 3);
            window.prismoDashboard.updateNotification('review', 5);
        }, 6000);
    }
}

// Test floating button position
function testFloatingButton() {
    if (window.prismoDashboard) {
        console.log('🧪 Testing floating button...');
        window.prismoDashboard.resetFloatingButtonPosition();
    }
}

// Test services dengan kapasitas
function testServicesWithCapacity() {
    if (window.prismoDashboard) {
        console.log('🧪 Testing services with capacity...');
        console.log('📊 Services stats:', window.prismoDashboard.getServicesStats());
    }
}