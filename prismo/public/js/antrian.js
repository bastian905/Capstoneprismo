// ===== PRISMO ANTRIAN MANAGER =====
class PrismoAntrianManager {
    constructor() {
        this.isInitialized = false;
        this.isMobileMenuOpen = false;
        this.currentTab = 'today';
        this.selectedDate = '';
        this.waitingDates = [];
        this.currentMonth = new Date().getMonth();
        this.currentYear = new Date().getFullYear();
        this.dataVersion = 'v4'; // Update versi untuk force refresh data

        // Notifikasi data
        this.notifications = {
            antrian: 3,
            review: 5
        };

        // Cek versi data, jika berbeda hapus localStorage
        const savedVersion = localStorage.getItem('prismoAntrianVersion');
        if (savedVersion !== this.dataVersion) {
            localStorage.removeItem('prismoTodayBookings');
            localStorage.removeItem('prismoOtherBookings');
            localStorage.setItem('prismoAntrianVersion', this.dataVersion);
            console.log('🔄 Data version updated, localStorage cleared');
        }

        // Load real data from server - NO MORE MOCK DATA
        this.todayBookings = window.antrianData ? window.antrianData.filter(a => {
            const bookingDate = new Date(a.date);
            const today = new Date();
            return bookingDate.toDateString() === today.toDateString();
        }) : this.getInitialTodayBookings();
        this.otherBookings = window.antrianData ? window.antrianData.filter(a => {
            const bookingDate = new Date(a.date);
            const today = new Date();
            return bookingDate.toDateString() !== today.toDateString();
        }) : this.getInitialOtherBookings();
        
        this.debouncedSave = this.debounce(this.saveToStorage.bind(this), 1000);
    }

    getInitialTodayBookings() {
        const today = new Date();
        return [
            {
                id: 1,
                name: "Ahmad Rizki",
                car: "Honda Civic - B 1234 ABC",
                service: "Premium Steam",
                time: "09:00",
                date: this.formatDate(today),
                price: "Rp 55.000",
                status: "menunggu",
                currentStep: 0,
                lastUpdated: today.getTime(),
                avatar: "/images/profile.png"
            },
            {
                id: 2,
                name: "Ryan knalpot",
                car: "Toyota Avanza - B 5678 DEF",
                service: "Premium Steam",
                time: "10:00",
                date: this.formatDate(today),
                price: "Rp 55.000",
                status: "menunggu",
                currentStep: 0,
                lastUpdated: today.getTime(),
                avatar: "/images/profile.png"
            },
            {
                id: 3,
                name: "Yanto lokomotif",
                car: "Yamaha NMAX - B 9876 GHI",
                service: "Premium Steam",
                time: "11:00",
                date: this.formatDate(today),
                price: "Rp 55.000",
                status: "menunggu",
                currentStep: 1,
                lastUpdated: today.getTime(),
                avatar: "/images/profile.png"
            },
            {
                id: 4,
                name: "Lelaba sunda",
                car: "Yamaha NMAX - B 9876 GHI",
                service: "Premium Steam",
                time: "11:00",
                date: this.formatDate(today),
                price: "Rp 55.000",
                status: "menunggu",
                currentStep: 1,
                lastUpdated: today.getTime(),
                avatar: "/images/profile.png"
            },
        ];
    }

    getInitialOtherBookings() {
    const otherBookings = [];
    const today = new Date();
    
    // Tanggal yang akan datang dengan booking menunggu (Desember 2025)
    const waitingDates = [
        '2025-12-06', '2025-12-08', '2025-12-10', '2025-12-12', '2025-12-15',
        '2025-12-17', '2025-12-20', '2025-12-22', '2025-12-24', '2025-12-27',
        '2025-12-29', '2025-12-31'
    ];

    // Generate sample data untuk 90 hari (30 hari sebelum dan 60 hari setelah)
    for (let i = -30; i <= 60; i++) {
        if (i === 0) continue; // Skip hari ini
        
        const date = new Date();
        date.setDate(today.getDate() + i);
        const dateKey = this.formatDateForInput(date); // Format: 2025-12-06
        const dateString = this.formatDate(date); // Format: Jumat, 6 Des 2025
        const isFuture = i > 0;
        
        // Untuk Antrian Lainnya: Hanya ada status "menunggu", "selesai", atau "dibatalkan"
        // TIDAK ADA status "proses" di Antrian Lainnya
        let status;
        if (isFuture) {
            // Untuk tanggal mendatang: hanya "menunggu" (tidak ada "proses")
            status = 'menunggu';
        } else {
            // Untuk tanggal lampau: hanya "selesai" atau "dibatalkan"
            status = Math.random() > 0.3 ? 'selesai' : 'dibatalkan';
        }
        
        // Tentukan currentStep berdasarkan status (tanpa step 1 untuk proses)
        let currentStep;
        switch(status) {
            case 'menunggu':
                currentStep = 0;
                break;
            case 'selesai':
                currentStep = 2;
                break;
            case 'dibatalkan':
                currentStep = 3;
                break;
            default:
                currentStep = 0;
        }
        
        // Generate lebih banyak booking untuk tanggal yang ditentukan di waitingDates
        const bookingCount = waitingDates.includes(dateKey) ? 3 : Math.floor(Math.random() * 2) + 1;
        
        for (let j = 0; j < bookingCount; j++) {
            otherBookings.push({
                id: `other-${i}-${j}`,
                name: this.getRandomName(),
                car: this.getRandomCar(),
                service: "Premium Steam",
                time: this.getRandomTime(),
                date: dateKey, // SIMPAN DALAM FORMAT YYYY-MM-DD
                dateDisplay: dateString, // Untuk display
                price: "Rp 55.000",
                status: status,
                currentStep: currentStep,
                lastUpdated: date.getTime() + j * 3600000,
                avatar: this.getRandomAvatar(),
                isFuture: isFuture
            });
        }
    }

    console.log('📊 Sample data for Other Bookings generated:');
    console.log('🟡 Waiting dates (December 2025):', waitingDates);
    console.log('📈 Total bookings:', otherBookings.length);
    
    // Log status distribution
    const statusCount = otherBookings.reduce((acc, booking) => {
        acc[booking.status] = (acc[booking.status] || 0) + 1;
        return acc;
    }, {});
    console.log('📊 Status distribution:', statusCount);
    
    // Log beberapa sample booking
    console.log('📋 Sample future bookings:', otherBookings.filter(b => b.isFuture).slice(0, 5));

    return otherBookings;
}

    getRandomName() {
        const names = [
            "Rina Wijaya", "Andi Saputra", "Dewi Lestari", "Budi Santoso", "Sari Indah",
            "Joko Widodo", "Maya Sari", "Rizki Pratama", "Linda Wati", "Hendra Gunawan",
            "Fitri Amelia", "Agus Setiawan", "Nina Permata", "Eko Prasetyo", "Diana Putri"
        ];
        return names[Math.floor(Math.random() * names.length)];
    }

    getRandomCar() {
        const cars = [
            "Toyota Avanza - B 1234 ABC",
            "Honda Civic - B 5678 DEF", 
            "Yamaha NMAX - B 9012 GHI",
            "Mitsubishi Pajero - B 3456 JKL",
            "Suzuki Ertiga - B 7890 MNO",
            "Toyota Rush - B 2345 PQR",
            "Honda CR-V - B 6789 STU",
            "Daihatsu Terios - B 0123 VWX"
        ];
        return cars[Math.floor(Math.random() * cars.length)];
    }

    getRandomTime() {
        const hours = ['08', '09', '10', '11', '13', '14', '15', '16'];
        const minutes = ['00', '15', '30', '45'];
        return `${hours[Math.floor(Math.random() * hours.length)]}:${minutes[Math.floor(Math.random() * minutes.length)]}`;
    }

    getRandomAvatar() {
        return "/images/profile.png";
    }

    formatDate(date) {
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        const dayName = days[date.getDay()];
        const day = date.getDate();
        const month = months[date.getMonth()];
        const year = date.getFullYear();
        
        return `${dayName}, ${day} ${month} ${year}`;
    }

    formatDateForInput(dateString) {
        const date = new Date(dateString);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    isToday(date) {
        const today = new Date();
        const checkDate = new Date(date);
        return checkDate.toDateString() === today.toDateString();
    }

    // ===== HORIZONTAL SCROLL HANDLING =====
    setupHorizontalScroll() {
        const columnContents = document.querySelectorAll('.kanban-column__content');
        
        columnContents.forEach(column => {
            // Prevent vertical scroll and enable horizontal scroll on vertical wheel
            column.addEventListener('wheel', this.handleHorizontalScroll.bind(this), { passive: false });
            
            // Add drag to scroll functionality
            this.addDragToScroll(column);
            
            // Add touch support for mobile/touchpad
            this.addTouchToScroll(column);
        });
        
        // Ensure main content is scrollable
        const mainContent = document.getElementById('mainContent');
        if (mainContent) {
            mainContent.style.overflowY = 'auto';
            mainContent.style.overflowX = 'hidden';
        }
    }

    handleHorizontalScroll(event) {
        const columnContent = event.currentTarget;
        
        // Check if content is scrollable
        const isScrollable = columnContent.scrollWidth > columnContent.clientWidth;
        
        if (!isScrollable) {
            // If not scrollable, allow default scroll behavior on parent
            return;
        }
        
        // Only handle vertical wheel events
        if (Math.abs(event.deltaX) > Math.abs(event.deltaY)) {
            return; // Let horizontal wheel events work normally
        }
        
        event.preventDefault();
        event.stopPropagation();
        
        const scrollAmount = event.deltaY * 1.5; // Adjust scroll speed
        
        // Smooth scroll
        columnContent.scrollBy({
            left: scrollAmount,
            behavior: 'auto'
        });
    }

    addDragToScroll(column) {
        let isDragging = false;
        let startX;
        let scrollLeft;
        let velocity = 0;
        let lastX = 0;
        let lastTime = 0;
        let animationFrame;
        
        const updateVelocity = (currentX, currentTime) => {
            const timeDelta = currentTime - lastTime;
            if (timeDelta > 0) {
                velocity = (currentX - lastX) / timeDelta;
            }
            lastX = currentX;
            lastTime = currentTime;
        };
        
        const applyMomentum = () => {
            if (Math.abs(velocity) > 0.1) {
                column.scrollLeft -= velocity * 16; // Apply velocity
                velocity *= 0.95; // Friction
                animationFrame = requestAnimationFrame(applyMomentum);
            }
        };
        
        column.addEventListener('mousedown', (e) => {
            // Ignore if clicking on interactive elements
            if (e.target.closest('button, a, input, select, textarea')) {
                return;
            }
            
            if (e.button !== 0) return; // Only left click
            
            isDragging = true;
            column.classList.add('is-dragging');
            
            startX = e.pageX - column.offsetLeft;
            scrollLeft = column.scrollLeft;
            velocity = 0;
            lastX = e.pageX;
            lastTime = Date.now();
            
            if (animationFrame) {
                cancelAnimationFrame(animationFrame);
            }
            
            e.preventDefault();
        });
        
        column.addEventListener('mouseleave', () => {
            if (isDragging) {
                isDragging = false;
                column.classList.remove('is-dragging');
                applyMomentum();
            }
        });
        
        column.addEventListener('mouseup', (e) => {
            if (isDragging) {
                isDragging = false;
                column.classList.remove('is-dragging');
                applyMomentum();
            }
        });
        
        column.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            
            e.preventDefault();
            
            const x = e.pageX - column.offsetLeft;
            const walk = (x - startX) * 1.5; // Scroll speed multiplier
            column.scrollLeft = scrollLeft - walk;
            
            updateVelocity(e.pageX, Date.now());
        });
        
        // Prevent click events when dragging
        column.addEventListener('click', (e) => {
            if (Math.abs(velocity) > 1) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, true);
    }
    
    addTouchToScroll(column) {
        let startX;
        let scrollLeft;
        let isTouching = false;
        
        column.addEventListener('touchstart', (e) => {
            isTouching = true;
            startX = e.touches[0].pageX - column.offsetLeft;
            scrollLeft = column.scrollLeft;
        }, { passive: true });
        
        column.addEventListener('touchmove', (e) => {
            if (!isTouching) return;
            
            const x = e.touches[0].pageX - column.offsetLeft;
            const walk = (x - startX) * 1.5;
            column.scrollLeft = scrollLeft - walk;
        }, { passive: true });
        
        column.addEventListener('touchend', () => {
            isTouching = false;
        }, { passive: true });
    }

    // ===== CUSTOM DATE PICKER SYSTEM =====
    setupCustomDatePicker() {
        this.waitingDates = this.getDatesWithWaitingBookings();
        this.initializeDatePicker();
        this.renderCalendar();
        
        console.log('📅 Total waiting dates found:', this.waitingDates.length);
        console.log('📅 Waiting dates:', this.waitingDates);
        console.log('📊 Total other bookings:', this.otherBookings.length);
    }

    getDatesWithWaitingBookings() {
        const waitingDates = new Set();
        
        // Cari tanggal yang memiliki booking status 'menunggu' di otherBookings
        this.otherBookings.forEach(booking => {
            if (booking.status === 'menunggu') {
                // booking.date sudah dalam format YYYY-MM-DD
                waitingDates.add(booking.date);
            }
        });
        
        const datesArray = Array.from(waitingDates);
        console.log('🔍 Found waiting bookings on dates:', datesArray.length, 'dates');
        console.log('📅 Waiting dates:', datesArray);
        
        return datesArray;
    }

    initializeDatePicker() {
        const trigger = document.getElementById('datePickerTrigger');
        const dropdown = document.getElementById('datePickerDropdown');
        const overlay = this.createOverlay();
        const prevMonthBtn = document.getElementById('prevMonth');
        const nextMonthBtn = document.getElementById('nextMonth');
        const clearBtn = document.getElementById('clearDate');
        const applyBtn = document.getElementById('applyDate');

        if (!trigger || !dropdown) return;

        // Toggle dropdown
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('show');
            overlay.classList.toggle('show');
        });

        // Navigation
        prevMonthBtn.addEventListener('click', () => {
            this.navigateMonth(-1);
        });

        nextMonthBtn.addEventListener('click', () => {
            this.navigateMonth(1);
        });

        // Actions
        clearBtn.addEventListener('click', () => {
            this.clearSelectedDate();
            dropdown.classList.remove('show');
            overlay.classList.remove('show');
        });

        applyBtn.addEventListener('click', () => {
            dropdown.classList.remove('show');
            overlay.classList.remove('show');
            if (this.selectedDate) {
                this.displayOtherBookings();
            }
        });

        // Close on outside click
        overlay.addEventListener('click', () => {
            dropdown.classList.remove('show');
            overlay.classList.remove('show');
        });

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
                overlay.classList.remove('show');
            }
        });

        // Update date preview
        this.updateDatePreview();
    }

    createOverlay() {
        const overlay = document.createElement('div');
        overlay.className = 'custom-date-picker__overlay';
        document.body.appendChild(overlay);
        return overlay;
    }

    navigateMonth(direction) {
        this.currentMonth += direction;
        
        if (this.currentMonth > 11) {
            this.currentMonth = 0;
            this.currentYear++;
        } else if (this.currentMonth < 0) {
            this.currentMonth = 11;
            this.currentYear--;
        }
        
        this.renderCalendar();
    }

    renderCalendar() {
        const calendarDays = document.getElementById('calendarDays');
        const monthYear = document.getElementById('currentMonthYear');
        
        if (!calendarDays || !monthYear) return;

        // Update month year header
        const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                           'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        monthYear.textContent = `${monthNames[this.currentMonth]} ${this.currentYear}`;

        // Clear previous days
        calendarDays.innerHTML = '';

        // Get first day of month and total days
        const firstDay = new Date(this.currentYear, this.currentMonth, 1);
        const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
        const totalDays = lastDay.getDate();
        const startingDay = firstDay.getDay(); // 0 = Sunday, 1 = Monday, etc.

        // Adjust starting day for Indonesian calendar (Monday first)
        const adjustedStartingDay = startingDay === 0 ? 6 : startingDay - 1;

        // Add empty days for previous month
        for (let i = 0; i < adjustedStartingDay; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'custom-date-picker__day other-month';
            calendarDays.appendChild(emptyDay);
        }

        // Add days of current month
        const today = new Date();
        const todayFormatted = this.formatDateForInput(today);

        for (let day = 1; day <= totalDays; day++) {
            const dayElement = document.createElement('button');
            dayElement.className = 'custom-date-picker__day';
            dayElement.textContent = day;
            dayElement.type = 'button';
            
            const dateStr = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            
            // Check if today
            if (dateStr === todayFormatted) {
                dayElement.classList.add('today');
            }
            
            // Check if selected
            if (dateStr === this.selectedDate) {
                dayElement.classList.add('selected');
            }
            
            // Check if has waiting bookings (BACKGROUND KUNING)
            if (this.waitingDates.includes(dateStr)) {
                dayElement.classList.add('waiting-booking');
                console.log('✅ Adding yellow background to:', dateStr);
            }
            
            // Add click event
            dayElement.addEventListener('click', () => {
                this.selectDate(dateStr);
            });
            
            calendarDays.appendChild(dayElement);
        }

        // Calculate remaining empty days for next month
        const totalCells = 42; // 6 rows × 7 days
        const remainingCells = totalCells - (adjustedStartingDay + totalDays);
        
        for (let i = 0; i < remainingCells; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'custom-date-picker__day other-month';
            calendarDays.appendChild(emptyDay);
        }
    }

    selectDate(dateStr) {
        this.selectedDate = dateStr;
        
        // Update UI
        const selectedDateText = document.getElementById('selectedDateText');
        if (selectedDateText) {
            const date = new Date(dateStr);
            selectedDateText.textContent = this.formatDate(date);
        }
        
        // Update calendar display
        this.renderCalendar();
        
        // Check if selected date is today
        if (this.isToday(dateStr)) {
            this.showTodayAlertModal();
            this.clearSelectedDate();
            return;
        }
    }

    clearSelectedDate() {
        this.selectedDate = '';
        const selectedDateText = document.getElementById('selectedDateText');
        if (selectedDateText) {
            selectedDateText.textContent = 'Pilih tanggal';
        }
        this.renderCalendar();
        this.clearOtherBookingsDisplay();
    }

    updateDatePreview() {
        const datePreview = document.getElementById('datePreview');
        if (datePreview && this.waitingDates.length > 0) {
            datePreview.style.display = 'block';
        }
    }

    // ===== INITIALIZATION =====
    init() {
        if (this.isInitialized) return;

        try {
            // Reset notifications ke default setiap init
            this.notifications = {
                antrian: 3,
                review: 5
            };

            this.setupEventListeners();
            this.setupTabs();
            this.setupCustomDatePicker();
            this.setupDateFilter();
            this.setupMobileMenu();
            this.setupProfileNavigation();
            this.setupHorizontalScroll(); // ← Horizontal scroll setup
            this.updateAllNotifications();
            this.displayTodayBookings();
            this.isInitialized = true;

            console.log('✅ PRISMO Antrian Manager initialized');

        } catch (error) {
            console.error('❌ Failed to initialize PRISMO Antrian Manager:', error);
            this.showErrorAlert('Gagal memuat sistem antrian');
        }
    }

    setupEventListeners() {
        // Global event delegation
        document.addEventListener('click', this.handleGlobalClick.bind(this));

        // Keyboard navigation
        document.addEventListener('keydown', this.handleGlobalKeydown.bind(this));

        // Window events
        window.addEventListener('beforeunload', this.saveToStorage.bind(this));
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
        
        // Avatar now loaded from database via Blade template

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

    handleGlobalClick(event) {
        const target = event.target;

        // Tab buttons
        if (target.matches('.tabs__button')) {
            event.preventDefault();
            this.switchTab(target.dataset.tab);
            return;
        }

        // Action buttons in booking items
        if (target.matches('.btn[data-action]') || target.closest('.btn[data-action]')) {
            event.preventDefault();
            const button = target.matches('.btn[data-action]') ? target : target.closest('.btn[data-action]');
            this.handleActionButton(button);
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

        if (target.closest('[data-action="close-alert"]')) {
            event.preventDefault();
            event.stopPropagation();
            this.closeCurrentModal();
            return;
        }

        if (target.closest('[data-action="switch-to-today"]')) {
            event.preventDefault();
            event.stopPropagation();
            this.switchToTodayTab();
            return;
        }

        if (target.closest('[data-action="confirm"]')) {
            event.preventDefault();
            event.stopPropagation();
            this.handleCancelConfirm();
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
    }

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

    // ===== TAB MANAGEMENT =====
    setupTabs() {
        const activeTab = document.querySelector('.tabs__button--active');
        if (activeTab) {
            this.currentTab = activeTab.dataset.tab;
        }
    }

    switchTab(tabName) {
        if (this.currentTab === tabName) return;

        try {
            // Update tab buttons
            document.querySelectorAll('.tabs__button').forEach(btn => {
                btn.classList.toggle('tabs__button--active', btn.dataset.tab === tabName);
                btn.setAttribute('aria-selected', btn.dataset.tab === tabName);
            });

            // Update tab panels
            document.querySelectorAll('.tab-pane').forEach(pane => {
                const isActive = pane.id === `${tabName}-panel`;
                pane.classList.toggle('tab-pane--active', isActive);
                pane.hidden = !isActive;
            });

            this.currentTab = tabName;

            // Load data for the tab if needed
            if (tabName === 'other') {
                this.setupOtherTab();
            }

        } catch (error) {
            console.error('Error switching tab:', error);
            this.showErrorAlert('Gagal memuat data tab');
        }
    }

    switchToTodayTab() {
        this.closeCurrentModal();
        this.switchTab('today');
    }

    setupOtherTab() {
        this.clearOtherBookingsDisplay();
    }

    // ===== DATE FILTER =====
    setupDateFilter() {
        const searchButton = document.getElementById('searchButton');

        if (searchButton) {
            searchButton.addEventListener('click', () => {
                if (!this.selectedDate) {
                    this.showErrorAlert('Silakan pilih tanggal terlebih dahulu');
                    return;
                }
                
                // Check if selected date is today
                if (this.isToday(this.selectedDate)) {
                    this.showTodayAlertModal();
                    return;
                }
                
                this.displayOtherBookings();
            });
        }
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
        this.updateMobileNotifications();

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

    updateMobileNotifications() {
        // Update badge untuk antrian
        const antrianBadge = document.getElementById('mobile-antrian-badge');
        if (antrianBadge) {
            if (this.notifications.antrian > 0) {
                antrianBadge.textContent = this.notifications.antrian;
                antrianBadge.style.display = 'flex';
            } else {
                antrianBadge.style.display = 'none';
            }
        }

        // Update badge untuk review
        const reviewBadge = document.getElementById('mobile-review-badge');
        if (reviewBadge) {
            if (this.notifications.review > 0) {
                reviewBadge.textContent = this.notifications.review;
                reviewBadge.style.display = 'flex';
            } else {
                reviewBadge.style.display = 'none';
            }
        }
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

    // ===== NOTIFICATION SYSTEM =====
    updateAllNotifications() {
        this.updateNavBadges();
        this.updateMobileBadges();
    }

    updateNavBadges() {
        const badges = {
            antrian: document.getElementById('antrian-badge'),
            review: document.getElementById('review-badge')
        };

        Object.entries(badges).forEach(([key, badge]) => {
            if (badge) {
                const count = this.notifications[key];
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'flex';
                    badge.setAttribute('aria-label', `${count} notifikasi ${key}`);
                } else {
                    badge.style.display = 'none';
                }
            }
        });
    }

    updateMobileBadges() {
        const badges = {
            antrian: document.getElementById('mobile-antrian-badge'),
            review: document.getElementById('mobile-review-badge')
        };

        Object.entries(badges).forEach(([key, badge]) => {
            if (badge) {
                const count = this.notifications[key];
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'flex';
                    badge.setAttribute('aria-label', `${count} notifikasi ${key}`);
                } else {
                    badge.style.display = 'none';
                }
            }
        });
    }

    // ===== BOOKING MANAGEMENT =====
    displayTodayBookings() {
        try {
            const waitingBookings = this.todayBookings.filter(b => b.status === 'menunggu');
            const processBookings = this.todayBookings.filter(b => b.status === 'proses');
            const historyBookings = this.todayBookings.filter(b =>
                b.status === 'selesai' || b.status === 'dibatalkan'
            );

            // Sort bookings
            const sortedWaiting = this.sortBookingsByTime(waitingBookings);
            const sortedProcess = this.sortBookingsByTime(processBookings);
            const sortedHistory = this.sortBookingsByLastUpdated(historyBookings);

            // Update counts
            this.updateColumnCounts({
                waiting: waitingBookings.length,
                process: processBookings.length,
                history: historyBookings.length
            });

            // Render bookings
            this.renderBookings('waiting-bookings', sortedWaiting, true);
            this.renderBookings('process-bookings', sortedProcess, true);
            this.renderBookings('history-bookings', sortedHistory, true);

        } catch (error) {
            console.error('Error displaying today bookings:', error);
            this.showErrorAlert('Gagal memuat data antrian hari ini');
        }
    }

    displayOtherBookings() {
        try {
            if (!this.selectedDate) {
                this.showErrorAlert('Silakan pilih tanggal terlebih dahulu');
                return;
            }

            // Filter bookings by selected date
            const filteredBookings = this.otherBookings.filter(booking => {
                // booking.date sudah dalam format YYYY-MM-DD
                return booking.date === this.selectedDate;
            });

            const sortedBookings = this.sortBookingsByTime(filteredBookings);
            this.renderOtherBookings('other-bookings', sortedBookings);

            // Update date info
            this.updateDateInfo(filteredBookings.length);

        } catch (error) {
            console.error('Error displaying other bookings:', error);
            this.showErrorAlert('Gagal memuat data antrian untuk tanggal tersebut');
        }
    }

    clearOtherBookingsDisplay() {
        const container = document.getElementById('other-bookings');
        const dateInfo = document.getElementById('dateInfo');
        
        if (container) {
            container.innerHTML = `
                <div class="empty-state">
                    <p>Pilih tanggal untuk melihat antrian</p>
                    <p style="font-size: var(--font-size-sm); color: var(--gray-400); margin-top: var(--space-2);">
                        Pilih tanggal selain hari ini untuk melihat antrian sebelumnya atau mendatang
                    </p>
                </div>
            `;
        }

        if (dateInfo) {
            dateInfo.style.display = 'none';
        }
    }

    updateDateInfo(bookingCount) {
        const dateInfo = document.getElementById('dateInfo');
        if (dateInfo) {
            const selectedDate = new Date(this.selectedDate);
            const isFuture = selectedDate > new Date();
            const dateType = isFuture ? 'Mendatang' : 'Sebelumnya';
            
            // Cek apakah ada waiting bookings di tanggal ini
            const hasWaitingBookings = this.waitingDates.includes(this.selectedDate);
            const waitingBookings = this.otherBookings.filter(booking => 
                booking.date === this.selectedDate && 
                booking.status === 'menunggu'
            );
            const waitingCount = waitingBookings.length;
            
            let waitingInfo = '';
            if (hasWaitingBookings) {
                waitingInfo = `
                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 8px; padding: 8px; background: #fffbf0; border-radius: 8px; border-left: 4px solid var(--warning-500);">
                        <span style="color: var(--warning-500); font-weight: bold;">⚠</span>
                        <span style="color: var(--warning-700); font-size: 14px;">
                            Ada ${waitingCount} booking menunggu
                        </span>
                    </div>
                `;
                dateInfo.classList.add('has-waiting');
            } else {
                dateInfo.classList.remove('has-waiting');
            }
            
            dateInfo.innerHTML = `
                <h3>Antrian Tanggal ${this.formatDate(selectedDate)}</h3>
                <p>Menampilkan ${bookingCount} booking (${dateType})</p>
                ${waitingInfo}
            `;
            dateInfo.style.display = 'block';
        }
    }

    renderBookings(containerId, bookings, isToday = true) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.warn(`Container ${containerId} not found`);
            return;
        }

        // Clear container
        container.innerHTML = '';

        if (bookings.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <p>Tidak ada data booking</p>
                </div>
            `;
            return;
        }

        // Create booking items
        bookings.forEach(booking => {
            const bookingElement = this.createBookingElement(booking, isToday);
            container.appendChild(bookingElement);
        });
    }

    renderOtherBookings(containerId, bookings) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.warn(`Container ${containerId} not found`);
            return;
        }

        // Clear container
        container.innerHTML = '';

        if (bookings.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <p>Tidak ada data booking untuk tanggal ${this.formatDate(new Date(this.selectedDate))}</p>
                    <p style="font-size: var(--font-size-sm); color: var(--gray-400); margin-top: var(--space-2);">
                        Coba pilih tanggal lain
                    </p>
                </div>
            `;
            return;
        }

        // Create booking items
        bookings.forEach(booking => {
            const bookingElement = this.createBookingElement(booking, false);
            container.appendChild(bookingElement);
        });
    }

    createBookingElement(booking, isToday = true) {
    const template = document.createElement('div');
    
    // Determine status class based on booking status and date
    let statusClass = '';
    if (booking.status === 'dibatalkan') {
        statusClass = 'booking-item--cancelled';
    } else if (booking.status === 'selesai') {
        statusClass = 'booking-item--completed';
    } else if (booking.status === 'menunggu' && booking.isFuture) {
        statusClass = 'booking-item--waiting';
    }

    let actionButtons = '';
    if (isToday && (booking.status === 'menunggu' || booking.status === 'proses')) {
        // Hanya untuk Antrian Hari Ini - TIDAK BERUBAH
        const rightAction = booking.status === 'menunggu' ? 'proses' : 'selesai';

        actionButtons = `
            <div class="booking-item__actions">
                <button class="btn btn--success" 
                        data-action="${rightAction}" 
                        data-booking-id="${booking.id}"
                        aria-label="${booking.status === 'menunggu' ? 'Mulai Proses' : 'Selesai'} booking ${booking.name}">
                    ${booking.status === 'menunggu' ? 'MULAI PROSES' : 'SELESAI'}
                </button>
            </div>
        `;
    }

    template.innerHTML = `
        <div class="booking-item ${statusClass}" data-booking-id="${booking.id}">
            <div class="booking-item__header">
                <div class="booking-item__avatar">
                    <img src="${booking.avatar}" 
                         alt="${booking.name}" 
                         class="booking-item__avatar-image"
                         loading="lazy">
                </div>
                <div class="booking-item__info">
                    <h3 class="booking-item__name">${booking.name}</h3>
                    <div class="booking-item__car">${booking.car}</div>
                </div>
            </div>
            
            <div class="booking-item__details">
                <div class="booking-item__detail">
                    <span class="booking-item__label">Layanan:</span>
                    <span class="booking-item__value">${booking.service}</span>
                </div>
                <div class="booking-item__detail">
                    <span class="booking-item__label">Waktu:</span>
                    <span class="booking-item__value">
                        ${booking.time} • ${booking.dateDisplay || this.formatDate(new Date(booking.date))}
                    </span>
                </div>
                <div class="booking-item__detail">
                    <span class="booking-item__label">Harga:</span>
                    <span class="booking-item__value booking-item__value--price">${booking.price}</span>
                </div>
            </div>
            
            ${actionButtons}
        </div>
    `;

    return template.firstElementChild;
}

    // ===== ACTION HANDLING =====
    handleActionButton(button) {
        const action = button.dataset.action;
        const bookingId = button.dataset.bookingId;

        // Actions that don't require booking ID
        if (['close', 'cancel', 'close-alert'].includes(action)) {
            this.closeCurrentModal();
            return;
        }

        if (!bookingId) {
            console.error('No booking ID found for action:', action);
            return;
        }

        // Handle today bookings (numeric IDs)
        if (!isNaN(bookingId)) {
            const numericId = parseInt(bookingId);
            const booking = this.todayBookings.find(b => b.id === numericId);
            if (!booking) {
                console.error('Booking not found:', numericId);
                this.showErrorAlert('Data booking tidak ditemukan');
                return;
            }

            switch (action) {
                case 'proses':
                    this.processBooking(numericId);
                    break;
                case 'selesai':
                    this.completeBooking(numericId);
                    break;
                default:
                    console.warn('Unknown action:', action);
            }
        }
    }

    processBooking(bookingId) {
        const bookingIndex = this.todayBookings.findIndex(b => b.id === bookingId);
        if (bookingIndex === -1) return;

        const booking = this.todayBookings[bookingIndex];

        try {
            if (booking.status !== 'menunggu') {
                this.showErrorAlert('Hanya booking yang menunggu bisa diproses');
                return;
            }

            // Update to backend
            fetch('/mitra/booking/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    booking_id: bookingId,
                    status: 'proses'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    booking.currentStep = 1;
                    booking.status = 'proses';
                    booking.lastUpdated = Date.now();

                    this.displayTodayBookings();
                    this.debouncedSave();

                    this.showSuccessAlert('Booking berhasil diproses');
                } else {
                    this.showErrorAlert(data.message || 'Gagal memproses booking');
                }
            })
            .catch(error => {
                console.error('Error processing booking:', error);
                this.showErrorAlert('Gagal memproses booking');
            });

        } catch (error) {
            console.error('Error processing booking:', error);
            this.showErrorAlert('Gagal memproses booking');
        }
    }

    completeBooking(bookingId) {
        const bookingIndex = this.todayBookings.findIndex(b => b.id === bookingId);
        if (bookingIndex === -1) return;

        const booking = this.todayBookings[bookingIndex];

        try {
            if (booking.status !== 'proses') {
                this.showErrorAlert('Hanya booking yang sedang diproses bisa diselesaikan');
                return;
            }

            // Update to backend
            fetch('/mitra/booking/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    booking_id: bookingId,
                    status: 'selesai'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    booking.currentStep = 2;
                    booking.status = 'selesai';
                    booking.lastUpdated = Date.now();

                    this.displayTodayBookings();
                    this.debouncedSave();

                    this.showSuccessAlert('Booking berhasil diselesaikan');
                } else {
                    this.showErrorAlert(data.message || 'Gagal menyelesaikan booking');
                }
            })
            .catch(error => {
                console.error('Error completing booking:', error);
                this.showErrorAlert('Gagal menyelesaikan booking');
            });

        } catch (error) {
            console.error('Error completing booking:', error);
            this.showErrorAlert('Gagal menyelesaikan booking');
        }
    }

    // ===== MODALS =====
    showTodayAlertModal() {
        const template = document.getElementById('todayAlertModalTemplate');
        if (!template) {
            console.error('Today alert modal template not found');
            return;
        }

        const modal = template.content.cloneNode(true);
        document.body.appendChild(modal);

        const modalElement = document.querySelector('.modal-overlay');
        const closeBtn = modalElement.querySelector('[data-action="close-alert"]');
        const switchBtn = modalElement.querySelector('[data-action="switch-to-today"]');

        const closeModal = () => document.body.removeChild(modalElement);

        closeBtn.addEventListener('click', closeModal);
        switchBtn.addEventListener('click', () => {
            closeModal();
            this.switchToTodayTab();
        });

        this.setupModalFocusTrap(modalElement);
    }

    // ===== UTILITIES =====
    sortBookingsByTime(bookings) {
        return [...bookings].sort((a, b) => {
            const timeToMinutes = (time) => {
                const [hours, minutes] = time.split(':').map(Number);
                return hours * 60 + minutes;
            };
            return timeToMinutes(a.time) - timeToMinutes(b.time);
        });
    }

    sortBookingsByLastUpdated(bookings) {
        return [...bookings].sort((a, b) => b.lastUpdated - a.lastUpdated);
    }

    updateColumnCounts(counts) {
        const elements = {
            waiting: document.getElementById('waiting-count'),
            process: document.getElementById('process-count'),
            history: document.getElementById('history-count')
        };

        Object.entries(elements).forEach(([key, element]) => {
            if (element && element.textContent != counts[key]) {
                element.textContent = counts[key];
                element.classList.add('count-update');
                setTimeout(() => element.classList.remove('count-update'), 500);
            }
        });
    }

    // ===== MODAL MANAGEMENT =====
    setupModalFocusTrap(modal) {
        const focusableElements = modal.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );

        if (focusableElements.length === 0) return;

        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        modal.addEventListener('keydown', (event) => {
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
        });

        firstElement.focus();
    }

    closeModal(modal) {
        document.body.removeChild(modal);
    }

    closeCurrentModal() {
        const modal = document.querySelector('.modal-overlay');
        if (modal) {
            document.body.removeChild(modal);
        }
    }

    // ===== ALERTS =====
    showSuccessAlert(message) {
        const template = document.getElementById('successModalTemplate');
        if (!template) {
            alert(message);
            return;
        }

        const modal = template.content.cloneNode(true);
        const messageElement = modal.querySelector('#successMessage');

        if (messageElement) {
            messageElement.textContent = message;
        }

        document.body.appendChild(modal);

        const modalElement = document.querySelector('.modal-overlay');
        const closeBtn = modalElement.querySelector('[data-action="close"]');

        closeBtn.addEventListener('click', () => {
            document.body.removeChild(modalElement);
        });

        setTimeout(() => {
            if (document.body.contains(modalElement)) {
                document.body.removeChild(modalElement);
            }
        }, 3000);

        this.setupModalFocusTrap(modalElement);
    }

    showErrorAlert(message) {
        console.error('Error:', message);
        alert(`Error: ${message}`);
    }

    // ===== STORAGE =====
    saveToStorage() {
        try {
            localStorage.setItem('prismo_today_bookings', JSON.stringify(this.todayBookings));
        } catch (error) {
            console.error('Failed to save to storage:', error);
        }
    }

    loadFromStorage(key) {
        try {
            const data = localStorage.getItem(key);
            return data ? JSON.parse(data) : null;
        } catch (error) {
            console.error('Failed to load from storage:', error);
            return null;
        }
    }

    // ===== PERFORMANCE UTILITIES =====
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// ===== INITIALIZATION =====
document.addEventListener('DOMContentLoaded', () => {
    try {
        const antrianManager = new PrismoAntrianManager();
        antrianManager.init();
        
        window.prismoAntrian = antrianManager;
        
        console.log('🎉 PRISMO Antrian System loaded successfully');
        
    } catch (error) {
        console.error('❌ Failed to load PRISMO Antrian System:', error);
        
        const main = document.getElementById('mainContent');
        if (main) {
            main.innerHTML = `
                <div class="empty-state">
                    <p>Terjadi kesalahan saat memuat antrian. Silakan refresh halaman.</p>
                    <button onclick="location.reload()" class="btn btn--primary" style="margin-top: 1rem;">
                        Refresh Halaman
                    </button>
                </div>
            `;
        }
    }
});
