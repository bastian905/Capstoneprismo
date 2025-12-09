// ========== GLOBAL VARIABLES ==========
let selectedFile = null;
let appliedVoucher = null;
let bookingData = {
    date: null,
    dateObj: null,
    time: null,
    service: null,
    price: null,
    tipe: null,
    nopol: null,
    merek: null,
    catatan: null,
    voucherCode: null,
    voucherDiscount: 0,
    pointsUsed: 0
};

// Get service type and price from URL parameters or default
const urlParams = new URLSearchParams(window.location.search);
const serviceType = urlParams.get('service') || 'Basic Steam';
const servicePrice = parseInt(urlParams.get('price') || '35000');

// Get mitra business data from backend or use defaults
const mitraData = window.mitraBusinessData || {};

// Get max slots for selected service
function getMaxSlotsForService() {
    if (!mitraData.services || !Array.isArray(mitraData.services)) {
        return 3; // Default max slots
    }
    
    const service = mitraData.services.find(s => s.name === serviceType);
    return service && service.max_slots ? parseInt(service.max_slots) : 3;
}

// Carwash Business Hours Configuration
const BUSINESS_HOURS = {
    open: '09:00',
    close: '18:00',
    slotDuration: 30, // minutes
    maxSlotsPerTime: getMaxSlotsForService(), // Get from service data
    operationalDays: mitraData.operationalDays || [1, 2, 3, 4, 5], // Use real operational days from backend
    dailyHours: mitraData.dailyHours || {
        0: null, // Minggu - Tutup
        1: { open: '07:00', close: '17:00' }, // Senin
        2: { open: '09:00', close: '18:00' }, // Selasa
        3: { open: '08:00', close: '17:00' }, // Rabu
        4: { open: '09:00', close: '18:00' }, // Kamis
        5: { open: '09:00', close: '18:00' }, // Jumat
        6: { open: '08:00', close: '14:00' }  // Sabtu
    }
};

// Break schedules per day - now loaded from backend
const BREAK_SCHEDULES = mitraData.breakSchedules || {};

// Booked slots - will be loaded from API
let BOOKED_SLOTS = {};

// ========== INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Booking System Initialized');
    initializeBookingSystem();
    initializeModalHandlers();
    initializeFormListeners();
    initializeTimePeriodSelector();
    loadBookedSlots(); // Load booked slots from API
});

// ========== LOAD BOOKED SLOTS FROM API ==========
async function loadBookedSlots() {
    try {
        const mitraId = mitraData.mitraId;
        if (!mitraId) {
            console.warn('⚠️ No mitra ID found, using mock data');
            return;
        }

        const response = await fetch(`/api/bookings/slots/${mitraId}?start_date=${new Date().toISOString().split('T')[0]}&end_date=${new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]}`);
        
        if (!response.ok) {
            console.warn('⚠️ Failed to load booked slots, using mock data');
            return;
        }

        const data = await response.json();
        BOOKED_SLOTS = data.booked_slots || {};
        console.log('✅ Booked slots loaded from API:', BOOKED_SLOTS);
        
        // Regenerate time slots if date is already selected
        if (bookingData.dateObj) {
            generateTimeSlots();
        }
    } catch (error) {
        console.error('❌ Error loading booked slots:', error);
    }
}

// ========== BUSINESS HOURS FUNCTIONS ==========
function getBusinessHours(date = null) {
    // Get day of week and retrieve hours and breaks for that specific day
    let dayOfWeek = 0;
    let breaks = [];
    let open = '08:00';
    let close = '21:00';
    
    if (date) {
        dayOfWeek = date.getDay(); // 0=Sunday, 1=Monday, etc.
        
        // Get open/close hours for this day
        if (BUSINESS_HOURS.dailyHours && BUSINESS_HOURS.dailyHours[dayOfWeek]) {
            open = BUSINESS_HOURS.dailyHours[dayOfWeek].open || open;
            close = BUSINESS_HOURS.dailyHours[dayOfWeek].close || close;
        }
        
        // Get breaks for this day
        if (BREAK_SCHEDULES[dayOfWeek]) {
            breaks = BREAK_SCHEDULES[dayOfWeek];
        }
    }
    
    return {
        open: open,
        close: close,
        breaks: breaks
    };
}

function generateTimeSlots() {
    const timeGrid = document.getElementById('timeGrid');
    if (!timeGrid) return;
    
    timeGrid.innerHTML = '';
    
    // Get business hours with breaks for selected date
    const hours = getBusinessHours(bookingData.dateObj);
    const startHour = parseInt(hours.open.split(':')[0]);
    const startMinute = parseInt(hours.open.split(':')[1]);
    const endHour = parseInt(hours.close.split(':')[0]);
    const endMinute = parseInt(hours.close.split(':')[1]);
    
    console.log(`🕐 Generating time slots: ${hours.open} - ${hours.close}`);
    
    // Get current time
    const now = new Date();
    const isToday = bookingData.dateObj && 
                    bookingData.dateObj.getDate() === now.getDate() &&
                    bookingData.dateObj.getMonth() === now.getMonth() &&
                    bookingData.dateObj.getFullYear() === now.getFullYear();
    
    // Convert to minutes for easier calculation
    let currentMinutes = startHour * 60 + startMinute;
    const endMinutes = endHour * 60 + endMinute;
    const nowMinutes = now.getHours() * 60 + now.getMinutes();
    
    while (currentMinutes < endMinutes) {
        const hour = Math.floor(currentMinutes / 60);
        const minute = currentMinutes % 60;
        const timeString = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
        
        const timeSlot = document.createElement('div');
        
        // Check if time has passed today
        if (isToday && currentMinutes <= nowMinutes) {
            timeSlot.className = 'time-slot past-time';
            timeSlot.textContent = timeString;
            timeSlot.title = 'Jam sudah terlewat';
        }
        // Check if this time is during any break
        else if (isBreakTime(timeString)) {
            timeSlot.className = 'time-slot break-time';
            timeSlot.textContent = '⏸️';
            timeSlot.title = getBreakReason(timeString);
        } 
        // Check if this slot is fully booked
        else if (isSlotBooked(timeString)) {
            timeSlot.className = 'time-slot booked';
            timeSlot.textContent = timeString;
            timeSlot.title = 'Penuh - Sudah dipesan';
        } 
        // Available slot
        else {
            const bookedCount = getBookedCount(timeString);
            const availableSlots = BUSINESS_HOURS.maxSlotsPerTime - bookedCount;
            
            timeSlot.className = 'time-slot available';
            timeSlot.textContent = timeString;
            timeSlot.dataset.time = timeString;
            timeSlot.title = `Tersedia ${availableSlots} slot - ${timeString}`;
            timeSlot.addEventListener('click', () => selectTime(timeString, timeSlot));
        }
        
        timeGrid.appendChild(timeSlot);
        
        // Move to next slot (30 minutes)
        currentMinutes += BUSINESS_HOURS.slotDuration;
    }
    
    console.log(`⏰ Generated time slots: ${hours.open} - ${hours.close}`);
}

function isSlotBooked(timeString) {
    const bookedCount = getBookedCount(timeString);
    const maxSlots = BUSINESS_HOURS.maxSlotsPerTime;
    const isBooked = bookedCount >= maxSlots;
    
    // Debug logging - check all time slots
    console.log(`🔍 ${timeString} - bookedCount: ${bookedCount}, maxSlots: ${maxSlots}, isBooked: ${isBooked}`);
    
    return isBooked;
}

function getBookedCount(timeString) {
    if (!bookingData.date) return 0;
    
    // Format date as YYYY-MM-DD for consistency
    const selectedDate = formatDateForAPI(bookingData.date);
    const bookedForDate = BOOKED_SLOTS[selectedDate];
    
    if (!bookedForDate) return 0;
    
    // Count how many bookings for this specific time
    const count = bookedForDate.filter(time => time === timeString).length;
    
    // Debug logging
    if (timeString === '10:00') {
        console.log(`🔍 DEBUG getBookedCount - date: ${selectedDate}, bookedForDate:`, bookedForDate, `count for ${timeString}: ${count}`);
    }
    
    return count;
}

function formatDateForAPI(dateString) {
    if (!dateString) return null;
    
    const parts = dateString.split(' ');
    if (parts.length !== 3) return null;
    
    const day = parts[0];
    const monthNames = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    const month = monthNames.indexOf(parts[1]);
    const year = parts[2];
    
    if (month === -1) return null;
    
    return `${year}-${String(month + 1).padStart(2, '0')}-${day}`;
}

function initializeTimePeriodSelector() {
    // Generate all time slots
    generateTimeSlots();
}

function clearTimeSelection() {
    bookingData.time = null;
    const summaryTimeEl = document.getElementById('summaryTime');
    if (summaryTimeEl) {
        summaryTimeEl.textContent = '-';
    }
    
    // Remove selected class from all time slots
    const allTimeSlots = document.querySelectorAll('.time-slot');
    allTimeSlots.forEach(slot => slot.classList.remove('selected'));
}

function updateBusinessHoursDisplay(dateStr) {
    const breakTimesEl = document.getElementById('breakTimes');
    if (!breakTimesEl) {
        console.warn('⚠️ breakTimes element not found!');
        return;
    }
    
    // Use bookingData.dateObj which is already a proper Date object
    const dateObj = bookingData.dateObj;
    if (!dateObj) {
        console.warn('⚠️ bookingData.dateObj not set!');
        return;
    }
    
    const dayOfWeek = dateObj.getDay();
    
    console.log(`🔍 updateBusinessHoursDisplay - dateStr: ${dateStr}, dayOfWeek: ${dayOfWeek}`);
    console.log(`🔍 BREAK_SCHEDULES[${dayOfWeek}]:`, BREAK_SCHEDULES[dayOfWeek]);
    
    const breaks = getBusinessHours(dateObj).breaks;
    console.log(`🔍 Final breaks for ${dateStr}:`, breaks);
    
    if (breaks.length === 0) {
        breakTimesEl.textContent = 'Tidak ada waktu istirahat';
    } else {
        const breakTexts = breaks.map(b => `${b.start} - ${b.end}`);
        breakTimesEl.textContent = breakTexts.join(', ');
    }
    
    console.log(`☕ Break times updated to: ${breakTimesEl.textContent}`);
}

// ========== TIME SLOTS FUNCTIONS ==========
function selectTime(time, element) {
    // Check if slot is available
    if (element.classList.contains('booked')) {
        showAlert('Slot Sudah Dipesan', 'Maaf, slot ini sudah dipesan oleh customer lain. Silakan pilih slot lainnya.');
        return;
    }
    
    // Check if selected time is during any break
    if (element.classList.contains('break-time')) {
        showBreakTimeAlert(time);
        return;
    }
    
    // Check if selected time is outside business hours
    if (!isWithinBusinessHours(time)) {
        const hours = getBusinessHours(bookingData.dateObj);
        showAlert('Di Luar Jam Operasional', `Maaf, jam ${time} berada di luar jam operasional (${hours.open} - ${hours.close}).`);
        return;
    }
    
    // Remove previous selection
    const allTimeSlots = document.querySelectorAll('.time-slot');
    allTimeSlots.forEach(slot => slot.classList.remove('selected'));
    
    // Add selection to clicked time
    element.classList.add('selected');
    
    // Update summary
    const summaryTimeEl = document.getElementById('summaryTime');
    if (summaryTimeEl) {
        summaryTimeEl.textContent = time;
    }
    
    // Store time in booking data
    bookingData.time = time;
    
    console.log(`⏰ Time selected: ${time}`);
}

function isBreakTime(time) {
    const breaks = getBusinessHours(bookingData.dateObj).breaks;
    const [selectedHour, selectedMinute] = time.split(':').map(Number);
    const selectedTimeInMinutes = selectedHour * 60 + selectedMinute;
    
    return breaks.some(breakPeriod => {
        const [breakStartHour, breakStartMinute] = breakPeriod.start.split(':').map(Number);
        const [breakEndHour, breakEndMinute] = breakPeriod.end.split(':').map(Number);
        
        const breakStartInMinutes = breakStartHour * 60 + breakStartMinute;
        const breakEndInMinutes = breakEndHour * 60 + breakEndMinute;
        
        return selectedTimeInMinutes >= breakStartInMinutes && selectedTimeInMinutes < breakEndInMinutes;
    });
}

function isWithinBusinessHours(time) {
    const hours = getBusinessHours(bookingData.dateObj);
    const [selectedHour, selectedMinute] = time.split(':').map(Number);
    const [openHour, openMinute] = hours.open.split(':').map(Number);
    const [closeHour, closeMinute] = hours.close.split(':').map(Number);
    
    const selectedTimeInMinutes = selectedHour * 60 + selectedMinute;
    const openTimeInMinutes = openHour * 60 + openMinute;
    const closeTimeInMinutes = closeHour * 60 + closeMinute;
    
    return selectedTimeInMinutes >= openTimeInMinutes && selectedTimeInMinutes < closeTimeInMinutes;
}

function getBreakReason(time) {
    const breaks = getBusinessHours(bookingData.dateObj).breaks;
    const [selectedHour, selectedMinute] = time.split(':').map(Number);
    const selectedTimeInMinutes = selectedHour * 60 + selectedMinute;
    
    const breakPeriod = breaks.find(breakTime => {
        const [breakStartHour, breakStartMinute] = breakTime.start.split(':').map(Number);
        const [breakEndHour, breakEndMinute] = breakTime.end.split(':').map(Number);
        
        const breakStartInMinutes = breakStartHour * 60 + breakStartMinute;
        const breakEndInMinutes = breakEndHour * 60 + breakEndMinute;
        
        return selectedTimeInMinutes >= breakStartInMinutes && selectedTimeInMinutes < breakEndInMinutes;
    });
    
    return breakPeriod ? `Istirahat (${breakPeriod.start} - ${breakPeriod.end})` : 'Waktu Istirahat';
}

function showBreakTimeAlert(time) {
    const breakReason = getBreakReason(time);
    const breakInfo = breakReason.replace('Istirahat: ', '');
    
    showAlert(
        'Jam Istirahat', 
        `Maaf, jam ${time} adalah waktu ${breakInfo.toLowerCase()}.<br>Silakan pilih jam lain yang tersedia.`
    );
}

function showAlert(title, message, type = 'warning') {
    // Create alert overlay if it doesn't exist
    let alertOverlay = document.getElementById('customAlert');
    
    if (!alertOverlay) {
        alertOverlay = document.createElement('div');
        alertOverlay.id = 'customAlert';
        alertOverlay.className = 'alert-overlay';
        document.body.appendChild(alertOverlay);
    }
    
    // Choose icon based on type
    const icons = {
        'warning': '⚠️',
        'info': 'ℹ️',
        'success': '✅',
        'error': '❌'
    };
    const icon = icons[type] || icons['warning'];
    
    alertOverlay.innerHTML = `
        <div class="alert-box">
            <div class="alert-icon">${icon}</div>
            <div class="alert-title">${title}</div>
            <div class="alert-message">${message}</div>
            <button class="alert-btn" onclick="closeAlert()">Mengerti</button>
        </div>
    `;
    
    alertOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeAlert() {
    const alertOverlay = document.getElementById('customAlert');
    if (alertOverlay) {
        alertOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function showPointInfo() {
    showAlert(
        'Info Point Prismo', 
        'Setiap kali booking kamu mendapatkan 1 point.<br>1 point = Rp1.000<br><br>Tukarkan 10 point untuk mendapatkan diskon Rp10.000!',
        'info'
    );
}

// ========== CALENDAR FUNCTIONS ==========
function generateCalendar() {
    const calendarGrid = document.querySelector('.calendar-grid');
    if (!calendarGrid) return;
    
    // Keep headers, remove only day cells
    const existingDays = calendarGrid.querySelectorAll('.calendar-day');
    existingDays.forEach(day => day.remove());
    
    // Get current date
    const now = new Date();
    const year = now.getFullYear();
    const month = now.getMonth();
    const today = now.getDate();
    const todayDay = now.getDay(); // 0 = Sunday, 1 = Monday, etc.
    
    // Get first day of month and total days
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    
    // Adjust for Monday start (0 = Sunday, we want Monday = 0)
    const offset = firstDay === 0 ? 6 : firstDay - 1;
    
    // Add empty cells for days before the 1st
    for (let i = 0; i < offset; i++) {
        const emptyDay = document.createElement('div');
        emptyDay.className = 'calendar-day empty';
        calendarGrid.appendChild(emptyDay);
    }
    
    // Add day cells
    for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        dayElement.textContent = String(day).padStart(2, '0');
        
        // Check if it's today
        if (day === today) {
            dayElement.classList.add('today');
        }
        
        // Check day of week
        const dayOfWeek = new Date(year, month, day).getDay();
        
        // Check if it's operational day
        const isOperationalDay = BUSINESS_HOURS.operationalDays.includes(dayOfWeek);
        
        // Check if mitra is open (from backend data)
        const isMitraOpen = mitraData.isOpen !== false;
        
        // Mark non-operational days or when mitra is closed
        if (!isOperationalDay || !isMitraOpen) {
            dayElement.classList.add('closed');
        }
        
        // Check if it's weekend (Saturday = 6, Sunday = 0)
        if (dayOfWeek === 0 || dayOfWeek === 6) {
            dayElement.classList.add('weekend');
        }
        
        // Disable past dates, non-operational days, or when mitra is closed
        if (day < today || !isOperationalDay || !isMitraOpen) {
            dayElement.classList.add('disabled');
        } else {
            dayElement.addEventListener('click', () => selectDate(day, month, year, dayElement));
        }
        
        calendarGrid.appendChild(dayElement);
    }
    
    console.log(`📅 Calendar generated: ${daysInMonth} days`);
}

function selectDate(day, month, year, element) {
    // Remove previous selection
    const allDays = document.querySelectorAll('.calendar-day');
    allDays.forEach(d => d.classList.remove('selected'));
    
    // Add selection to clicked day
    element.classList.add('selected');
    
    // Create Date object for this selection
    const dateObj = new Date(year, month, day);
    
    // Format date
    const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    const dateStr = `${String(day).padStart(2, '0')} ${months[month]} ${year}`;
    
    // Update summary
    const summaryDateEl = document.getElementById('summaryDate');
    if (summaryDateEl) {
        summaryDateEl.textContent = dateStr;
    }
    
    // Store both date string (for display/API) and Date object (for calculations)
    bookingData.date = dateStr;
    bookingData.dateObj = dateObj;
    
    console.log(`📅 Date selected: ${dateStr}`);
    console.log(`🔍 DEBUG - formatDateForAPI result: ${formatDateForAPI(dateStr)}`);
    console.log(`🔍 DEBUG - BOOKED_SLOTS for this date:`, BOOKED_SLOTS[formatDateForAPI(dateStr)]);
    console.log(`🔍 DEBUG - BREAK_SCHEDULES for this date:`, getBusinessHours(dateObj).breaks);
    
    // Update business hours display with breaks for this date
    updateBusinessHoursDisplay(dateStr);
    
    // Clear time selection when date changes
    clearTimeSelection();
    
    // Regenerate time slots to reflect booked slots for new date
    console.log(`🔄 Regenerating time slots for ${dateStr}...`);
    generateTimeSlots();
}

// ========== BOOKING SYSTEM INITIALIZATION ==========
function initializeBookingSystem() {
    // Set service type in summary
    const summaryServiceEl = document.getElementById('summaryService');
    if (summaryServiceEl) {
        summaryServiceEl.textContent = serviceType;
        bookingData.service = serviceType;
    }
    
    // Generate calendar
    const calendarGrid = document.querySelector('.calendar-grid');
    if (calendarGrid) {
        generateCalendar();
    } else {
        console.warn('⚠️ Calendar grid not found');
    }
    
    // Initialize summary
    updateSummary();
    
    console.log('🏪 Business Hours:', BUSINESS_HOURS);
    console.log('⏸️ Break Schedules:', BREAK_SCHEDULES);
}

// ========== FORM LISTENERS ==========
function initializeFormListeners() {
    // Input listeners for real-time update
    const merekEl = document.getElementById('merek');
    const tipeEl = document.getElementById('tipe');
    const nopolEl = document.getElementById('nopol');
    const catatanEl = document.getElementById('catatan');
    const usePointsEl = document.getElementById('usePoints');
    
    // Function to toggle asterisk based on input value
    function toggleAsterisk(input) {
        const label = input.previousElementSibling;
        if (label && label.classList.contains('required')) {
            if (input.value.trim() !== '') {
                label.classList.add('filled');
            } else {
                label.classList.remove('filled');
            }
        }
    }
    
    if (merekEl) {
        merekEl.addEventListener('input', function() {
            updateSummary();
            toggleAsterisk(this);
        });
    }
    
    if (tipeEl) {
        tipeEl.addEventListener('input', function() {
            updateSummary();
            toggleAsterisk(this);
        });
    }
    
    if (nopolEl) {
        nopolEl.addEventListener('input', function() {
            updateSummary();
            toggleAsterisk(this);
        });
    }
    
    if (catatanEl) {
        catatanEl.addEventListener('input', updateSummary);
    }
    
    if (usePointsEl) {
        usePointsEl.addEventListener('change', updateSummary);
    }
    
    // Checkbox listener
    const agreeCheckbox = document.getElementById('agreeCheckbox');
    if (agreeCheckbox) {
        agreeCheckbox.addEventListener('change', toggleConfirmButton);
    }
}

// ========== MODAL HANDLERS INITIALIZATION ==========
function initializeModalHandlers() {
    const uploadArea = document.getElementById('uploadArea');
    const modalOverlay = document.getElementById('modalOverlay');
    const fileInput = document.getElementById('fileInput');
    
    if (!uploadArea) {
        console.warn('⚠️ Upload area not found');
        return;
    }

    // Drag and drop handlers
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    });

    // File input change handler
    if (fileInput) {
        fileInput.addEventListener('change', handleFileSelect);
    }

    // Close modal when clicking outside
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                closeModal();
            }
        });
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modalOverlay && modalOverlay.classList.contains('active')) {
            closeModal();
        }
    });
}

// ========== SUMMARY UPDATE FUNCTIONS ==========
function updateSummary() {
    // Update merek
    const merekEl = document.getElementById('merek');
    const summaryMerekEl = document.getElementById('summaryMerek');
    if (merekEl && summaryMerekEl) {
        const merek = merekEl.value || '-';
        summaryMerekEl.textContent = merek;
        bookingData.merek = merek;
    }
    
    // Update tipe
    const tipeEl = document.getElementById('tipe');
    const summaryTipeEl = document.getElementById('summaryTipe');
    if (tipeEl && summaryTipeEl) {
        const tipe = tipeEl.value || '-';
        summaryTipeEl.textContent = tipe;
        bookingData.tipe = tipe;
    }
    
    // Update nopol
    const nopolEl = document.getElementById('nopol');
    const summaryNopolEl = document.getElementById('summaryNopol');
    if (nopolEl && summaryNopolEl) {
        const nopol = nopolEl.value || '-';
        summaryNopolEl.textContent = nopol;
        bookingData.nopol = nopol;
    }
    
    // Update catatan
    const catatanEl = document.getElementById('catatan');
    const summaryCatatanEl = document.getElementById('summaryCatatan');
    if (catatanEl && summaryCatatanEl) {
        const catatan = catatanEl.value || '-';
        summaryCatatanEl.textContent = catatan;
        bookingData.catatan = catatan;
    }
    
    // Calculate total
    calculateTotal();
}

function calculateTotal() {
    const adminFee = 1000;
    const usePointsEl = document.getElementById('usePoints');
    const usePoints = usePointsEl ? usePointsEl.checked : false;
    
    // Calculate subtotal before point discount
    const subtotalBeforePoint = servicePrice + adminFee;
    
    // Get user's available points from backend
    const availablePoints = window.mitraBusinessData?.customerPoints || 0;
    
    // Each point = Rp 1,000
    const pointValue = 1000;
    
    // Get voucher discount first
    const voucherDiscount = appliedVoucher ? appliedVoucher.discount : 0;
    
    // Calculate subtotal after voucher
    const subtotalAfterVoucher = subtotalBeforePoint - voucherDiscount;
    
    // Calculate points to use: use floor to only use complete thousands
    // If subtotal is 2002, we use 2 points (2000), leaving 2 to pay
    // If subtotal is 1002, we use 1 point (1000), leaving 2 to pay
    const pointsToUse = usePoints ? Math.min(Math.floor(subtotalAfterVoucher / pointValue), availablePoints) : 0;
    const pointDiscount = pointsToUse * pointValue;
    
    // Show/hide point section with actual points used
    const pointSection = document.getElementById('pointSection');
    const pointDiscountEl = document.getElementById('pointDiscount');
    if (pointSection && pointDiscountEl) {
        pointSection.style.display = usePoints ? 'flex' : 'none';
        pointDiscountEl.textContent = `-Rp ${pointDiscount.toLocaleString('id-ID')} (${pointsToUse} Point)`;
    }
    
    // Show/hide voucher section
    const voucherSection = document.getElementById('voucherSection');
    const voucherDiscountEl = document.getElementById('voucherDiscount');
    if (voucherSection && voucherDiscountEl) {
        voucherSection.style.display = voucherDiscount > 0 ? 'flex' : 'none';
        voucherDiscountEl.textContent = '-Rp ' + voucherDiscount.toLocaleString('id-ID');
    }
    
    // Calculate total
    const total = Math.max(0, servicePrice + adminFee - pointDiscount - voucherDiscount);
    
    // Format and display total
    const summaryTotalEl = document.getElementById('summaryTotal');
    if (summaryTotalEl) {
        summaryTotalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }
    
    // Store in booking data
    bookingData.basePrice = servicePrice + adminFee; // Store base price with admin fee
    bookingData.finalPrice = total; // Store final price after discounts
    bookingData.pointsUsed = pointsToUse;
    bookingData.voucherDiscount = voucherDiscount;
}

// ========== CHECKBOX & BUTTON TOGGLE ==========
function toggleConfirmButton() {
    const checkbox = document.getElementById('agreeCheckbox');
    const confirmBtn = document.getElementById('confirmBtn');
    
    if (!checkbox || !confirmBtn) {
        console.warn('⚠️ Checkbox or button not found');
        return;
    }
    
    confirmBtn.disabled = !checkbox.checked;
}

// ========== VALIDATION ==========
function validateBookingForm() {
    const errors = [];
    
    // Check date
    if (!bookingData.date || bookingData.date === '-') {
        errors.push('Pilih tanggal pencucian');
    }
    
    // Check time
    if (!bookingData.time || bookingData.time === '-') {
        errors.push('Pilih jam pencucian');
    }
    
    // Check merek
    const merekEl = document.getElementById('merek');
    if (!merekEl || !merekEl.value.trim()) {
        errors.push('Isi merek kendaraan');
    }
    
    // Check tipe
    const tipeEl = document.getElementById('tipe');
    if (!tipeEl || !tipeEl.value.trim()) {
        errors.push('Isi tipe kendaraan');
    }
    
    // Check nopol
    const nopolEl = document.getElementById('nopol');
    if (!nopolEl || !nopolEl.value.trim()) {
        errors.push('Isi nomor polisi kendaraan');
    }
    
    return errors;
}

// ========== MODAL FUNCTIONS ==========
function confirmBooking() {
    console.log('🔘 Confirm Booking button clicked');
    
    // Validate form
    const errors = validateBookingForm();
    
    if (errors.length > 0) {
        const errorMessage = 'Mohon lengkapi data berikut:\n• ' + errors.join('\n• ');
        console.warn('⚠️ Validation errors:', errors);
        showAlert('Data Belum Lengkap', errorMessage.replace(/\n/g, '<br>'));
        return;
    }
    
    console.log('✅ Booking data:', bookingData);
    
    // Open payment modal
    const modalOverlay = document.getElementById('modalOverlay');
    if (modalOverlay) {
        modalOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        console.log('💳 Payment modal opened');
    } else {
        console.error('❌ Modal overlay not found!');
    }
}

function closeModal() {
    const modalOverlay = document.getElementById('modalOverlay');
    if (modalOverlay) {
        modalOverlay.classList.remove('active');
        document.body.style.overflow = '';
        resetModalForm();
        console.log('❌ Payment modal closed');
    }
}

function downloadQR() {
    const qrImage = document.querySelector('.qr-code');
    
    if (qrImage) {
        // Create download link
        const link = document.createElement('a');
        link.href = qrImage.src;
        link.download = `QRIS_Payment_${Date.now()}.svg`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        console.log('📥 QR Code downloaded');
    } else {
        alert('QR Code tidak ditemukan');
    }
}

// ========== FILE UPLOAD FUNCTIONS ==========
function handleFileSelect(event) {
    const file = event.target.files[0];
    if (file) {
        handleFile(file);
    }
}

function handleFile(file) {
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    const maxSize = 5 * 1024 * 1024; // 5MB

    // Validate file type
    if (!validTypes.includes(file.type)) {
        alert('❌ Format file tidak valid!\nGunakan JPG, PNG, atau JPEG');
        return;
    }

    // Validate file size
    if (file.size > maxSize) {
        const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
        alert(`❌ Ukuran file terlalu besar! (${sizeMB}MB)\nMaksimal 5MB`);
        return;
    }

    selectedFile = file;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const previewImage = document.getElementById('previewImage');
        const fileName = document.getElementById('fileName');
        const previewContainer = document.getElementById('previewContainer');
        const submitBtn = document.getElementById('submitBtn');
        
        if (previewImage) {
            previewImage.src = e.target.result;
        }
        if (fileName) {
            fileName.textContent = file.name;
        }
        if (previewContainer) {
            previewContainer.style.display = 'block';
            previewContainer.classList.add('active');
        }
        if (submitBtn) {
            submitBtn.disabled = false;
        }
        
        console.log(`📤 File uploaded: ${file.name} (${(file.size / 1024).toFixed(2)}KB)`);
    };
    
    reader.onerror = function() {
        alert('❌ Gagal membaca file. Silakan coba lagi.');
        console.error('File read error');
    };
    
    reader.readAsDataURL(file);
}

function removeFile() {
    selectedFile = null;
    
    const fileInput = document.getElementById('fileInput');
    const previewContainer = document.getElementById('previewContainer');
    const previewImage = document.getElementById('previewImage');
    const submitBtn = document.getElementById('submitBtn');
    
    if (fileInput) {
        fileInput.value = '';
    }
    if (previewImage) {
        previewImage.src = '';
    }
    if (previewContainer) {
        previewContainer.style.display = 'none';
        previewContainer.classList.remove('active');
    }
    if (submitBtn) {
        submitBtn.disabled = true;
    }
    
    console.log('🗑️ File removed');
}

async function submitPayment() {
    if (!selectedFile) {
        showAlert('Bukti Pembayaran Diperlukan', '❌ Silakan upload bukti pembayaran terlebih dahulu!');
        return;
    }

    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn ? submitBtn.textContent : '';
    
    if (submitBtn) {
        submitBtn.textContent = 'Memproses...';
        submitBtn.disabled = true;
    }
    
    console.log('⏳ Processing payment...');
    console.log('📋 Current booking data:', bookingData);

    try {
        // Prepare booking data for API
        const bookingPayload = {
            mitra_id: mitraData.mitraId || window.location.pathname.split('/').pop(),
            service_type: bookingData.service || serviceType,
            vehicle_type: bookingData.tipe || '-',
            vehicle_plate: bookingData.nopol || '-',
            booking_date: formatDateForAPI(bookingData.date),
            booking_time: bookingData.time + ':00',
            base_price: bookingData.basePrice || (servicePrice + 1000),
            discount_amount: bookingData.voucherDiscount + (bookingData.pointsUsed * 1000),
            final_price: bookingData.finalPrice || 0,
            voucher_code: bookingData.voucherCode || null,
            payment_method: 'qris'
        };

        console.log('📤 Sending booking to API:', bookingPayload);

        // Get CSRF cookie first (for Sanctum)
        await fetch('/sanctum/csrf-cookie', {
            credentials: 'same-origin'
        });

        // Create booking
        const bookingResponse = await fetch('/api/bookings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify(bookingPayload)
        });

        if (!bookingResponse.ok) {
            const errorData = await bookingResponse.json();
            throw new Error(errorData.message || 'Gagal membuat booking');
        }

        const bookingResult = await bookingResponse.json();
        console.log('✅ Booking created:', bookingResult);

        // Upload payment proof
        const formData = new FormData();
        formData.append('payment_proof', selectedFile);

        const uploadResponse = await fetch(`/api/bookings/${bookingResult.id}/payment-proof`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: formData
        });

        if (!uploadResponse.ok) {
            throw new Error('Gagal mengupload bukti pembayaran');
        }

        console.log('✅ Payment proof uploaded');

        // Show success message
        const paymentContent = document.getElementById('paymentContent');
        const successMessage = document.getElementById('successMessage');
        
        if (paymentContent) {
            paymentContent.style.display = 'none';
        }
        if (successMessage) {
            successMessage.style.display = 'block';
            successMessage.classList.add('active');
            
            const successSubtext = successMessage.querySelector('.success-subtext');
            if (successSubtext) {
                successSubtext.textContent = 'Pembayaran Anda sedang diverifikasi';
            }
        }

        // Prepare invoice data
        const partnerNameEl = document.querySelector('.partner-name');
        const phoneEl = document.querySelector('.phone');
        
        const invoiceDetails = {
            partnerName: mitraData.businessName || (partnerNameEl ? partnerNameEl.textContent : 'Mitra'),
            partnerLocation: `${mitraData.city || 'Jakarta'}, ${mitraData.province || 'DKI Jakarta'}`,
            partnerAddress: mitraData.address || 'Alamat tidak tersedia',
            partnerPhone: mitraData.phone || (phoneEl ? phoneEl.textContent : '-'),
            bookingDate: bookingData.date,
            bookingTime: bookingData.time,
            serviceName: bookingData.service || serviceType,
            carBrand: bookingData.merek || '-',
            carType: bookingData.tipe || '-',
            licensePlate: bookingData.nopol || '-',
            totalAmount: calculateFinalPrice(),
            bookingId: bookingResult.booking_code || 'BK-XXXXX',
            notes: bookingData.catatan || '-'
        };
        
        console.log('📄 Invoice details prepared:', invoiceDetails);
        
        // Close payment modal and show invoice
        setTimeout(() => {
            const modalOverlay = document.getElementById('modalOverlay');
            if (modalOverlay) {
                modalOverlay.classList.remove('active');
            }
            showInvoice(invoiceDetails);
        }, 2000);

    } catch (error) {
        console.error('❌ Booking error:', error);
        showAlert('Gagal Membuat Booking', error.message || 'Terjadi kesalahan. Silakan coba lagi.');
        
        if (submitBtn) {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    }
}

function calculateFinalPrice() {
    // Return the final price that was already calculated in calculateTotal()
    return bookingData.finalPrice || 0;
}

function resetModalForm() {
    setTimeout(() => {
        // Reset file upload
        removeFile();
        
        // Reset modal display
        const paymentContent = document.getElementById('paymentContent');
        const successMessage = document.getElementById('successMessage');
        const submitBtn = document.getElementById('submitBtn');
        
        if (paymentContent) {
            paymentContent.style.display = 'block';
        }
        if (successMessage) {
            successMessage.style.display = 'none';
            successMessage.classList.remove('active');
            
            // Reset success message to default
            const successSubtext = successMessage.querySelector('.success-subtext');
            if (successSubtext) {
                successSubtext.textContent = 'Terima kasih atas pembayaran Anda';
            }
        }
        if (submitBtn) {
            submitBtn.textContent = 'Konfirmasi Pembayaran';
            submitBtn.disabled = true;
        }
        
        console.log('🔄 Modal form reset');
    }, 300);
}

// ========== INVOICE SYSTEM ==========
let invoiceData = {
    partnerName: 'Autospa Steam',
    partnerLocation: 'Jakarta Pusat, DKI Jakarta',
    partnerAddress: 'Jl. Sudirman No. 45',
    partnerPhone: '0897654721',
    serviceName: 'Premium',
    carType: 'A - Class',
    licensePlate: 'B 2346 NT',
    totalAmount: 150000,
    bookingId: 'BOOK001',
    date: new Date().toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    })
};

// Show Invoice Modal
function showInvoice(customData = null) {
    const modal = document.getElementById('invoiceModal');
    
    // Update data jika ada custom data
    if (customData) {
        invoiceData = { ...invoiceData, ...customData };
    }
    
    // Update UI dengan data
    updateInvoiceUI();
    
    // Show modal
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    console.log('✅ Invoice displayed:', invoiceData);
}

// Close Invoice Modal
function closeInvoice() {
    const modal = document.getElementById('invoiceModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
    
    console.log('❌ Invoice closed - Redirecting to booking page');
    
    // Redirect to booking page after closing invoice
    setTimeout(() => {
        window.location.href = '/customer/booking/Rbooking';
    }, 300);
}

// Update Invoice UI
function updateInvoiceUI() {
    document.getElementById('partnerName').textContent = invoiceData.partnerName;
    
    const locationEl = document.getElementById('partnerLocation');
    if (locationEl && invoiceData.partnerLocation) {
        locationEl.textContent = invoiceData.partnerLocation;
    }
    
    document.getElementById('partnerAddress').textContent = invoiceData.partnerAddress;
    document.getElementById('partnerPhone').textContent = invoiceData.partnerPhone;
    
    // Update invoice date and time
    if (invoiceData.bookingDate && invoiceData.bookingDate !== '-') {
        document.getElementById('invoiceDate').textContent = invoiceData.bookingDate;
    }
    if (invoiceData.bookingTime && invoiceData.bookingTime !== '-') {
        document.getElementById('invoiceTime').textContent = invoiceData.bookingTime;
    }
    
    // Update service details
    document.getElementById('serviceName').textContent = invoiceData.serviceName;
    
    // Update car brand
    const carBrandEl = document.getElementById('carBrand');
    if (carBrandEl && invoiceData.carBrand) {
        carBrandEl.textContent = invoiceData.carBrand;
    }
    
    document.getElementById('carType').textContent = invoiceData.carType;
    document.getElementById('licensePlate').textContent = invoiceData.licensePlate;
    
    // Update notes if available
    const notesRow = document.getElementById('invoiceNotesRow');
    const notesEl = document.getElementById('invoiceNotes');
    if (invoiceData.notes && invoiceData.notes !== '-' && notesRow && notesEl) {
        notesEl.textContent = invoiceData.notes;
        notesRow.style.display = 'flex';
    }
    
    document.getElementById('totalAmount').textContent = formatCurrency(invoiceData.totalAmount);
}

// Download Invoice
function downloadInvoice() {
    console.log('📥 Downloading invoice...');
    
    const invoiceContent = document.querySelector('.invoice-content');
    
    if (invoiceContent) {
        // Load html2canvas from CDN if not already loaded
        if (typeof html2canvas === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
            script.onload = function() {
                captureAndDownload();
            };
            document.head.appendChild(script);
        } else {
            captureAndDownload();
        }
        
        function captureAndDownload() {
            html2canvas(invoiceContent, {
                scale: 2,
                backgroundColor: '#ffffff',
                logging: false
            }).then(canvas => {
                // Convert canvas to image and download
                const link = document.createElement('a');
                link.download = `Invoice_${invoiceData.bookingId}_${new Date().getTime()}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
                
                console.log('✅ Invoice downloaded successfully');
                
                // Tutup invoice setelah 1 detik dan redirect
                setTimeout(() => {
                    closeInvoice();
                }, 1000);
            }).catch(error => {
                console.error('❌ Error capturing invoice:', error);
                alert('Gagal mendownload invoice. Silakan coba lagi.');
            });
        }
    } else {
        alert('❌ Invoice tidak dapat didownload saat ini');
    }
}

// ========== INTEGRASI DENGAN SISTEM BOOKING ==========
function onPaymentConfirmed(bookingDetails) {
    console.log('💳 Payment confirmed for booking:', bookingDetails.bookingId);
    
    // Update invoice data dari booking details
    const invoiceDetails = {
        partnerName: bookingDetails.partnerName || 'Autospa Steam',
        partnerAddress: bookingDetails.partnerAddress || 'Jl. Sudirman No. 45, Jakarta Pusat',
        partnerPhone: bookingDetails.partnerPhone || '0897654721',
        serviceName: bookingDetails.serviceName || 'Premium',
        carType: bookingDetails.carType || 'A - Class',
        licensePlate: bookingDetails.licensePlate || 'B 2346 NT',
        totalAmount: bookingDetails.totalAmount || 150000,
        bookingId: bookingDetails.bookingId || 'BOOK001'
    };
    
    // Tampilkan invoice
    showInvoice(invoiceDetails);
}

window.showInvoiceAfterPayment = onPaymentConfirmed;

// ========== UTILITY FUNCTIONS ==========
function formatCurrency(amount) {
    return 'Rp ' + amount.toLocaleString('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
}

function formatDate(date) {
    const options = { day: '2-digit', month: 'long', year: 'numeric' };
    return new Date(date).toLocaleDateString('id-ID', options);
}

// ========== VOUCHER MANAGEMENT ==========
// Daftar voucher yang tersedia (dalam implementasi nyata, ini dari backend)
const AVAILABLE_VOUCHERS = {
    'PRISMO10': {
        code: 'PRISMO10',
        discount: 10000,
        type: 'fixed',
        description: 'Diskon Rp 10.000',
        minPurchase: 0,
        expiryDate: '2025-12-31'
    },
    'CUCI20': {
        code: 'CUCI20',
        discount: 20,
        type: 'percentage',
        description: 'Diskon 20%',
        minPurchase: 50000,
        maxDiscount: 50000,
        expiryDate: '2025-12-31'
    },
    'WELCOME15': {
        code: 'WELCOME15',
        discount: 15000,
        type: 'fixed',
        description: 'Diskon Rp 15.000 untuk pelanggan baru',
        minPurchase: 30000,
        expiryDate: '2025-12-31'
    }
};

function applyVoucher() {
    const voucherInput = document.getElementById('voucherCode');
    const voucherMessage = document.getElementById('voucherMessage');
    const applyBtn = document.getElementById('applyVoucherBtn');
    
    if (!voucherInput || !voucherMessage) return;
    
    const code = voucherInput.value.trim().toUpperCase();
    
    // Reset message
    voucherMessage.style.display = 'none';
    voucherMessage.className = 'voucher-message';
    
    // Validasi input
    if (!code) {
        showVoucherMessage('Masukkan kode voucher terlebih dahulu', 'error');
        return;
    }
    
    // Cek voucher
    const voucher = AVAILABLE_VOUCHERS[code];
    
    if (!voucher) {
        showVoucherMessage('Kode voucher tidak valid', 'error');
        appliedVoucher = null;
        updateSummary();
        return;
    }
    
    // Cek tanggal kadaluarsa
    if (new Date(voucher.expiryDate) < new Date()) {
        showVoucherMessage('Voucher sudah kadaluarsa', 'error');
        appliedVoucher = null;
        updateSummary();
        return;
    }
    
    // Cek minimum pembelian
    if (voucher.minPurchase && servicePrice < voucher.minPurchase) {
        showVoucherMessage(`Minimum pembelian Rp ${voucher.minPurchase.toLocaleString('id-ID')} untuk menggunakan voucher ini`, 'error');
        appliedVoucher = null;
        updateSummary();
        return;
    }
    
    // Hitung diskon
    let discount = 0;
    if (voucher.type === 'fixed') {
        discount = voucher.discount;
    } else if (voucher.type === 'percentage') {
        discount = Math.floor(servicePrice * voucher.discount / 100);
        if (voucher.maxDiscount) {
            discount = Math.min(discount, voucher.maxDiscount);
        }
    }
    
    // Simpan voucher yang diterapkan
    appliedVoucher = {
        code: voucher.code,
        discount: discount,
        description: voucher.description
    };
    
    // Update UI
    showVoucherMessage(`Voucher berhasil diterapkan! ${voucher.description}`, 'success');
    voucherInput.disabled = true;
    applyBtn.textContent = 'Hapus';
    applyBtn.onclick = removeVoucher;
    
    // Update booking data
    bookingData.voucherCode = voucher.code;
    
    // Update total
    updateSummary();
    
    console.log('✅ Voucher applied:', appliedVoucher);
}

function removeVoucher() {
    const voucherInput = document.getElementById('voucherCode');
    const voucherMessage = document.getElementById('voucherMessage');
    const applyBtn = document.getElementById('applyVoucherBtn');
    
    // Reset voucher
    appliedVoucher = null;
    bookingData.voucherCode = null;
    bookingData.voucherDiscount = 0;
    
    // Reset UI
    if (voucherInput) {
        voucherInput.value = '';
        voucherInput.disabled = false;
    }
    
    if (voucherMessage) {
        voucherMessage.style.display = 'none';
    }
    
    if (applyBtn) {
        applyBtn.textContent = 'Gunakan';
        applyBtn.onclick = applyVoucher;
    }
    
    // Update total
    updateSummary();
    
    console.log('🗑️ Voucher removed');
}

function showVoucherMessage(message, type) {
    const voucherMessage = document.getElementById('voucherMessage');
    if (!voucherMessage) return;
    
    voucherMessage.textContent = message;
    voucherMessage.className = `voucher-message ${type}`;
    voucherMessage.style.display = 'flex';
}

// ========== DEBUG MODE (Remove in production) ==========
if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    console.log('🔧 Debug Mode Active');
    console.log('Service Type:', serviceType);
    console.log('Service Price:', formatCurrency(servicePrice));
    console.log('Business Hours:', BUSINESS_HOURS);
    console.log('Break Sessions:', BUSINESS_HOURS.breaks);
    console.log('Booked Slots Sample:', BOOKED_SLOTS);
    console.log('Available Vouchers:', Object.keys(AVAILABLE_VOUCHERS));
}

// ========== EXPORT FUNCTIONS FOR GLOBAL USE ==========
window.selectTime = selectTime;
window.selectDate = selectDate;
window.confirmBooking = confirmBooking;
window.closeModal = closeModal;
window.downloadQR = downloadQR;
window.removeFile = removeFile;
window.submitPayment = submitPayment;
window.closeAlert = closeAlert;
window.showPointInfo = showPointInfo;
window.showInvoice = showInvoice;
window.closeInvoice = closeInvoice;
window.downloadInvoice = downloadInvoice;
window.toggleConfirmButton = toggleConfirmButton;
window.applyVoucher = applyVoucher;
window.removeVoucher = removeVoucher;
window.updateSummary = updateSummary;