// ========== GLOBAL VARIABLES ==========
let currentTab = 1;
let currentBooking = null;
let bookingHistory = [];
let rescheduleData = {
    date: null,
    time: null
};
let cancelData = {
    refundMethod: 'shopeepay',
    accountNumber: ''
};

// Review modal variables
let currentReviewData = {
    rating: 0,
    comment: '',
    bookingId: null,
    isEditing: false,
    photos: []
};

// Status phases configuration
const BOOKING_STATUS = {
    CHECK_TRANSACTION: 1,  // Cek Transaksi
    WAITING: 2,            // Menunggu
    IN_PROGRESS: 3,        // Dalam Proses
    COMPLETED: 4           // Selesai
};

const STATUS_LABELS = {
    1: 'Cek Transaksi',
    2: 'Menunggu',
    3: 'Dalam Proses',
    4: 'Selesai'
};

// Helper function to get status value from label
function getStatusValueFromLabel(label) {
    for (const [key, value] of Object.entries(STATUS_LABELS)) {
        if (value === label) return parseInt(key);
    }
    return BOOKING_STATUS.CHECK_TRANSACTION;
}

// Rating descriptions
const RATING_DESCRIPTIONS = {
    1: 'Sangat Buruk',
    2: 'Buruk',
    3: 'Cukup',
    4: 'Baik',
    5: 'Sangat Baik'
};

// ========== USER PROFILE LOADER ==========
function loadUserProfile() {
    // Avatar images loaded from database via Blade template
    
    // Load user name
    // User name loaded from Blade template
    const userNameElement = document.getElementById('bookingUserName');
    const mobileUserNameElement = document.getElementById('mobileBookingUserName');
    
    if (userName) {
        if (userNameElement) {
            userNameElement.textContent = userName;
        }
        if (mobileUserNameElement) {
            mobileUserNameElement.textContent = userName;
        }
        console.log('✅ Booking page: User name loaded');
    }
}

// ========== INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Booking Status System Initialized');
    
    // Initialize all systems
    initializeNavigation();
    // Notification system handled by notification-system.js
    initializeStatusTabs();
    initializeBookingActions();
    initializeRescheduleModal();
    initializeCancelModal();
    initializeReviewModal();
    initializeMobileMenu();
    initializeSmoothScroll();
    initializeServiceCardEffects();
    loadBookingData();
    renderBookingHistory();
    loadUserProfile();
    
    // Check for booking success parameter
    checkBookingSuccess();
});

// Note: loadUserProfile() defined above (line ~115)

// ========== REVIEW MODAL SYSTEM ==========
function initializeReviewModal() {
    const stars = document.querySelectorAll('#starRating .star');
    const commentTextarea = document.getElementById('reviewComment');
    const uploadPhotoBtn = document.getElementById('uploadPhotoBtn');
    const photoInput = document.getElementById('photoInput');
    const sendReviewBtn = document.getElementById('sendReviewBtn');
    
    // Initialize star rating
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            setStarRating(rating);
        });
        
        star.addEventListener('mouseover', function() {
            const rating = parseInt(this.dataset.rating);
            highlightStars(rating);
        });
    });
    
    // Reset stars when mouse leaves
    document.getElementById('starRating').addEventListener('mouseleave', function() {
        if (currentReviewData.rating > 0) {
            highlightStars(currentReviewData.rating);
        } else {
            resetStars();
        }
    });
    
    // Character count for comment
    if (commentTextarea) {
        commentTextarea.addEventListener('input', function() {
            updateCharCount(this.value.length);
            currentReviewData.comment = this.value;
        });
    }
    
    // Photo upload button
    if (uploadPhotoBtn && photoInput) {
        uploadPhotoBtn.addEventListener('click', function() {
            photoInput.click();
        });
        
        photoInput.addEventListener('change', function(e) {
            handlePhotoUpload(e.target.files);
        });
    }
    
    // Send review button
    if (sendReviewBtn) {
        sendReviewBtn.addEventListener('click', function() {
            submitReview();
        });
    }
    
    console.log('⭐ Review modal initialized');
}

function setStarRating(rating) {
    currentReviewData.rating = rating;
    highlightStars(rating);
    
    // Update rating text
    const ratingText = document.getElementById('ratingText');
    if (ratingText) {
        ratingText.textContent = RATING_DESCRIPTIONS[rating] || `Rating: ${rating} bintang`;
    }
}

function highlightStars(rating) {
    const stars = document.querySelectorAll('#starRating .star');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.textContent = '★';
            star.classList.add('active');
        } else {
            star.textContent = '☆';
            star.classList.remove('active');
        }
    });
}

function resetStars() {
    const stars = document.querySelectorAll('#starRating .star');
    stars.forEach(star => {
        star.textContent = '☆';
        star.classList.remove('active');
    });
}

function updateCharCount(count) {
    const charCount = document.getElementById('charCount');
    const charCountElement = document.querySelector('.char-count');
    
    if (charCount) {
        charCount.textContent = count;
        
        // Update color based on count
        charCountElement.classList.remove('warning', 'error');
        if (count > 400) {
            charCountElement.classList.add('warning');
        }
        if (count > 500) {
            charCountElement.classList.add('error');
        }
    }
}

function handlePhotoUpload(files) {
    if (!files || files.length === 0) return;
    
    const maxPhotos = 5;
    const maxFileSize = 5 * 1024 * 1024; // 5MB
    
    // Check total photos limit
    if (currentReviewData.photos.length + files.length > maxPhotos) {
        alert(`Maksimal ${maxPhotos} foto dapat diunggah!`);
        return;
    }
    
    const previewContainer = document.getElementById('uploadedPreviews');
    const previewImages = document.getElementById('previewImages');
    
    Array.from(files).forEach(file => {
        // Validate file size
        if (file.size > maxFileSize) {
            alert(`File ${file.name} terlalu besar! Maksimal 5MB per foto.`);
            return;
        }
        
        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert(`File ${file.name} bukan gambar!`);
            return;
        }
        
        // Create preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const photoData = {
                file: file,
                dataUrl: e.target.result,
                name: file.name
            };
            
            currentReviewData.photos.push(photoData);
            
            // Create preview element
            const previewDiv = document.createElement('div');
            previewDiv.className = 'preview-item';
            previewDiv.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <button class="remove-preview" onclick="removePhoto(${currentReviewData.photos.length - 1})">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            previewImages.appendChild(previewDiv);
            previewContainer.style.display = 'block';
            
            console.log('📷 Photo added:', file.name);
        };
        reader.readAsDataURL(file);
    });
    
    // Reset input
    document.getElementById('photoInput').value = '';
}

function removePhoto(index) {
    currentReviewData.photos.splice(index, 1);
    
    // Re-render previews
    const previewImages = document.getElementById('previewImages');
    const previewContainer = document.getElementById('uploadedPreviews');
    
    previewImages.innerHTML = '';
    
    if (currentReviewData.photos.length === 0) {
        previewContainer.style.display = 'none';
    } else {
        currentReviewData.photos.forEach((photo, idx) => {
            const previewDiv = document.createElement('div');
            previewDiv.className = 'preview-item';
            previewDiv.innerHTML = `
                <img src="${photo.dataUrl}" alt="Preview">
                <button class="remove-preview" onclick="removePhoto(${idx})">
                    <i class="fas fa-times"></i>
                </button>
            `;
            previewImages.appendChild(previewDiv);
        });
    }
    
    console.log('🗑️ Photo removed, remaining:', currentReviewData.photos.length);
}

function openReviewModal(booking, isEditing = false) {
    if (!booking) return;
    
    currentReviewData = {
        rating: booking.rating || 0,
        comment: booking.comment || '',
        bookingId: booking.id,
        isEditing: isEditing,
        photos: booking.photos || []
    };
    
    const modal = document.getElementById('reviewModalOverlay');
    const partnerName = document.getElementById('reviewPartnerName');
    const serviceType = document.getElementById('reviewServiceType');
    const modalTitle = document.getElementById('reviewModalTitle');
    const currentReviewSection = document.getElementById('currentReviewSection');
    
    if (partnerName) partnerName.textContent = booking.partner;
    if (serviceType) serviceType.textContent = booking.service;
    
    // Set modal title based on mode
    if (modalTitle) {
        modalTitle.textContent = isEditing ? 'Edit Rating & Review' : 'Beri Rating & Review';
    }
    
    // Set current values
    if (currentReviewData.rating > 0) {
        setStarRating(currentReviewData.rating);
    } else {
        resetStars();
        document.getElementById('ratingText').textContent = 'Pilih rating (1-5 bintang)';
    }
    
    const commentTextarea = document.getElementById('reviewComment');
    if (commentTextarea) {
        commentTextarea.value = currentReviewData.comment;
        updateCharCount(currentReviewData.comment.length);
    }
    
    // Reset upload previews
    const previewContainer = document.getElementById('uploadedPreviews');
    const previewImages = document.getElementById('previewImages');
    
    if (previewContainer) previewContainer.style.display = 'none';
    if (previewImages) previewImages.innerHTML = '';
    
    // Load existing photos if editing
    if (isEditing && booking.photos && booking.photos.length > 0) {
        booking.photos.forEach((photo, idx) => {
            const previewDiv = document.createElement('div');
            previewDiv.className = 'preview-item';
            previewDiv.innerHTML = `
                <img src="${photo.dataUrl}" alt="Preview">
                <button class="remove-preview" onclick="removePhoto(${idx})">
                    <i class="fas fa-times"></i>
                </button>
            `;
            previewImages.appendChild(previewDiv);
        });
        previewContainer.style.display = 'block';
    }
    
    // Show/hide current review section for editing
    if (currentReviewSection) {
        if (isEditing && booking.comment) {
            currentReviewSection.style.display = 'block';
            document.getElementById('currentRatingDisplay').innerHTML = generateStars(booking.rating);
            document.getElementById('currentCommentDisplay').textContent = booking.comment;
        } else {
            currentReviewSection.style.display = 'none';
        }
    }
    
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        console.log(`⭐ Review modal opened for ${booking.partner} (${isEditing ? 'editing' : 'new'})`);
    }
}

function closeReviewModal() {
    const modal = document.getElementById('reviewModalOverlay');
    
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        console.log('✖ Review modal closed');
    }
}

function submitReview() {
    if (currentReviewData.rating === 0) {
        alert('Mohon berikan rating terlebih dahulu!');
        return;
    }
    
    if (currentReviewData.comment.length > 500) {
        alert('Komentar terlalu panjang! Maksimal 500 karakter.');
        return;
    }
    
    // Find the booking in history
    const bookingIndex = bookingHistory.findIndex(b => b.id === currentReviewData.bookingId);
    if (bookingIndex === -1) {
        alert('Booking tidak ditemukan!');
        return;
    }
    
    console.log('📤 Submitting review to backend:', {
        bookingId: currentReviewData.bookingId,
        rating: currentReviewData.rating,
        comment: currentReviewData.comment,
        photos: currentReviewData.photos.length,
        isEditing: currentReviewData.isEditing
    });
    
    // Prepare form data for file upload
    const formData = new FormData();
    formData.append('booking_code', currentReviewData.bookingId);
    formData.append('rating', currentReviewData.rating);
    formData.append('comment', currentReviewData.comment);
    
    // Add photos if any
    if (currentReviewData.photos && currentReviewData.photos.length > 0) {
        currentReviewData.photos.forEach((photo, index) => {
            // If photo is an object with file property (from file input)
            if (photo && photo.file && photo.file instanceof File) {
                formData.append('photos[]', photo.file);
            }
            // If photo is a base64 string, convert to blob
            else if (typeof photo === 'string' && photo.startsWith('data:image')) {
                const blob = dataURLtoBlob(photo);
                formData.append('photos[]', blob, `review-${index}.jpg`);
            }
            // If photo is a direct base64 dataUrl
            else if (photo && photo.dataUrl && photo.dataUrl.startsWith('data:image')) {
                const blob = dataURLtoBlob(photo.dataUrl);
                formData.append('photos[]', blob, photo.name || `review-${index}.jpg`);
            }
        });
    }
    
    // Send to backend
    const endpoint = currentReviewData.isEditing ? '/customer/review/update' : '/customer/review/submit';
    
    fetch(endpoint, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update booking data
            bookingHistory[bookingIndex].rating = currentReviewData.rating;
            bookingHistory[bookingIndex].comment = currentReviewData.comment;
            bookingHistory[bookingIndex].photos = currentReviewData.photos;
            bookingHistory[bookingIndex].hasReview = true;
            
            console.log('✅ Review submitted successfully:', data);
            
            // Close modal
            closeReviewModal();
            
            // Re-render history to show updated review
            renderBookingHistory();
            
            // Show success message
            const photoText = currentReviewData.photos.length > 0 ? `\n📷 ${currentReviewData.photos.length} foto diunggah` : '';
            alert(
                currentReviewData.isEditing ? 
                `✅ Review berhasil diperbarui! 🌟${photoText}` : 
                `✅ Terima kasih atas review Anda! 🌟${photoText}`
            );
        } else {
            alert(data.message || 'Gagal menyimpan review');
        }
    })
    .catch(error => {
        console.error('❌ Error submitting review:', error);
        alert('Terjadi kesalahan saat menyimpan review');
    });
}

// Helper function to convert base64 to blob
function dataURLtoBlob(dataurl) {
    const arr = dataurl.split(',');
    const mime = arr[0].match(/:(.*?);/)[1];
    const bstr = atob(arr[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);
    while(n--){
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new Blob([u8arr], {type:mime});
}

// ========== NAVIGATION SYSTEM ==========
function initializeNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    const currentPage = window.location.pathname;
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        
        if (currentPage === href || 
            currentPage.includes('/customer/booking') && href.includes('/booking') ||
            currentPage === '/dashboard' && href.includes('/dashboard')) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
        
        link.addEventListener('click', function(e) {
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    console.log('🔗 Navigation initialized');
}

// Mobile Menu Initialization
function initializeMobileMenu() {
    const menuToggle = document.getElementById('menuToggle');
    const mainNav = document.getElementById('mainNav');
    
    if (!menuToggle || !mainNav) {
        console.log('Mobile menu elements not found, skipping mobile menu initialization');
        return;
    }
    
    console.log('Menu Toggle:', menuToggle);
    console.log('Main Nav:', mainNav);
    
    // Ensure correct initial state
    function resetMenuState() {
        if (window.innerWidth > 768) {
            // Desktop mode
            mainNav.classList.remove('active');
            menuToggle.innerHTML = '☰';
        } else {
            // Mobile mode
            if (!mainNav.classList.contains('active')) {
                menuToggle.innerHTML = '☰';
            }
        }
    }
    
    // Initial state
    resetMenuState();
    
    // Toggle menu
    menuToggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        mainNav.classList.toggle('active');
        
        // Update icon
        if (mainNav.classList.contains('active')) {
            this.innerHTML = '✕';
            this.setAttribute('aria-expanded', 'true');
            console.log('Menu opened');
        } else {
            this.innerHTML = '☰';
            this.setAttribute('aria-expanded', 'false');
            console.log('Menu closed');
        }
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 600 && 
            !event.target.closest('.header-content') && 
            mainNav.classList.contains('active')) {
            mainNav.classList.remove('active');
            menuToggle.innerHTML = '☰';
            menuToggle.setAttribute('aria-expanded', 'false');
            console.log('Menu closed by outside click');
        }
    });
    
    // Close menu when clicking nav links
    const navLinks = mainNav.querySelectorAll('a');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (window.innerWidth <= 600) {
                mainNav.classList.remove('active');
                menuToggle.innerHTML = '☰';
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });
    });
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            resetMenuState();
            console.log('Window resized to:', window.innerWidth);
        }, 250);
    });
    
    console.log('📱 Mobile menu initialized');
}

// ========== STATUS TABS SYSTEM ==========
function initializeStatusTabs() {
    const tabs = document.querySelectorAll('.tab');
    const statusTabsContainer = document.querySelector('.status-tabs');
    
    if (!tabs.length) {
        console.warn('⚠️ Status tabs not found');
        return;
    }
    
    tabs.forEach((tab, index) => {
        tab.addEventListener('click', function() {
            selectTab(index + 1, this);
        });
    });
    
    if (statusTabsContainer) {
        statusTabsContainer.setAttribute('data-progress', currentTab);
    }
    
    console.log('📊 Status tabs initialized');
}

function selectTab(tabNumber, element) {
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => tab.classList.remove('active'));
    
    element.classList.add('active');
    
    const statusTabsContainer = document.querySelector('.status-tabs');
    if (statusTabsContainer) {
        statusTabsContainer.setAttribute('data-progress', tabNumber);
    }
    
    currentTab = tabNumber;
    console.log(`✅ Tab ${tabNumber} selected`);
}

// ========== BOOKING DATA MANAGEMENT ==========
function loadBookingData() {
    // Load real data from server - NO MORE MOCK DATA
    currentBooking = window.currentBookingData || null;
    bookingHistory = window.bookingHistory || [];
    
    // Update current booking display or hide section if no active booking
    if (currentBooking) {
        updateCurrentBookingDisplay();
        
        // Update status tabs based on current booking status
        const statusValue = getStatusValueFromLabel(currentBooking.status);
        updateStatusProgress(statusValue);
    } else {
        // Hide current booking section if no active booking
        hideCurrentBookingSection();
    }
    
    console.log('📦 Booking data loaded from server', { currentBooking, historyCount: bookingHistory.length });
}

function hideCurrentBookingSection() {
    // Hide the entire booking card
    const bookingCard = document.querySelector('.booking-card');
    if (bookingCard) {
        bookingCard.style.display = 'none';
    }
    
    // Add empty state to booking-current section
    const bookingCurrent = document.querySelector('.booking-current');
    if (bookingCurrent) {
        // Check if empty state already exists
        if (!bookingCurrent.querySelector('.empty-booking-state')) {
            const emptyState = document.createElement('div');
            emptyState.className = 'empty-booking-state';
            emptyState.style.cssText = 'text-align: center; padding: 60px 20px; color: #666; background: #f8f9fa; border-radius: 12px; margin-top: 20px;';
            emptyState.innerHTML = `
                <i class="fas fa-calendar-times" style="font-size: 64px; color: #ddd; margin-bottom: 20px; display: block;"></i>
                <h3 style="font-size: 20px; margin: 0 0 12px 0; color: #333;">Tidak ada booking aktif saat ini</h3>
                <p style="font-size: 14px; margin: 0; color: #999;">Silakan buat booking baru untuk menggunakan layanan</p>
            `;
            bookingCurrent.appendChild(emptyState);
        }
    }
}

function updateCurrentBookingDisplay() {
    if (!currentBooking) {
        console.log('⚠️ No current booking to display');
        return;
    }
    
    document.getElementById('currentPartner').textContent = currentBooking.partner;
    document.getElementById('currentPrice').textContent = `Total: ${formatCurrency(currentBooking.totalPrice)}`;
    document.getElementById('currentLocation').textContent = currentBooking.location;
    document.getElementById('currentDate').textContent = currentBooking.date;
    document.getElementById('currentTime').textContent = currentBooking.time;
    document.getElementById('currentTreatment').textContent = currentBooking.treatment;
    document.getElementById('currentType').textContent = currentBooking.tipe;
    document.getElementById('currentNopol').textContent = currentBooking.nopol;
    
    // Get current status from booking
    const statusValue = getStatusValueFromLabel(currentBooking.status);
    
    // Disable cancel and reschedule buttons if status is IN_PROGRESS or COMPLETED
    const cancelBtn = document.querySelector('.btn-cancel');
    const rescheduleBtn = document.querySelector('.btn-reschedule');
    
    if (statusValue >= BOOKING_STATUS.IN_PROGRESS) {
        if (cancelBtn) {
            cancelBtn.disabled = true;
            cancelBtn.style.opacity = '0.5';
            cancelBtn.style.cursor = 'not-allowed';
        }
        if (rescheduleBtn) {
            rescheduleBtn.disabled = true;
            rescheduleBtn.style.opacity = '0.5';
            rescheduleBtn.style.cursor = 'not-allowed';
        }
    } else {
        if (cancelBtn) {
            cancelBtn.disabled = false;
            cancelBtn.style.opacity = '1';
            cancelBtn.style.cursor = 'pointer';
        }
        if (rescheduleBtn) {
            rescheduleBtn.disabled = false;
            rescheduleBtn.style.opacity = '1';
            rescheduleBtn.style.cursor = 'pointer';
        }
    }
}

function renderBookingHistory() {
    const historyContainer = document.getElementById('historyContainer');
    if (!historyContainer) return;
    
    historyContainer.innerHTML = '';
    
    bookingHistory.forEach(booking => {
        const historyCard = createHistoryCard(booking);
        historyContainer.appendChild(historyCard);
    });
    
    console.log('📋 Booking history rendered');
}

function createHistoryCard(booking) {
    const card = document.createElement('div');
    card.className = 'history-card';
    card.dataset.bookingId = booking.id;
    
    const hasRating = booking.rating > 0;
    const hasComment = booking.comment && booking.comment.trim() !== '';
    const starsHTML = hasRating ? generateStars(booking.rating) : '';
    const isCancelled = booking.status === 'Dibatalkan';
    
    // Badge class based on status
    const badgeClass = isCancelled ? 'badge-cancelled' : 'badge-success';
    
    card.innerHTML = `
        <div class="history-header">
            <h4>${booking.partner}</h4>
            <span class="${badgeClass}">${booking.status}</span>
        </div>
        
        <p class="service-type">${booking.service}</p>
        
        <div class="history-details">
            <div class="detail-item">
                <span class="icon">📅</span>
                <span>${booking.date}</span>
            </div>
            <div class="detail-item">
                <span class="icon">💰</span>
                <span>${formatCurrency(booking.price)}</span>
            </div>
        </div>
        
        ${!isCancelled ? `
            <div class="history-actions">
                ${!hasRating ? 
                    `<button class="btn-review">Beri Rating</button>` : 
                    `<button class="btn-edit-review">Edit Review</button>`
                }
                <button class="btn-book-again">Booking Lagi</button>
            </div>
            
            ${hasRating ? `
                <div class="rating-with-edit">
                    <div class="rating-stars">
                        ${starsHTML}
                    </div>
                </div>
            ` : ''}
            
            ${hasComment ? `
                <div class="review-comment">
                    <p>"${booking.comment}"</p>
                </div>
            ` : ''}
            
            ${booking.mitraResponse ? `
                <div class="mitra-response">
                    <div class="response-header">
                        <span class="response-icon">💬</span>
                        <span class="response-label">Respon dari ${booking.partner}</span>
                    </div>
                    <p class="response-text">${booking.mitraResponse}</p>
                </div>
            ` : ''}
            
            ${booking.photos && booking.photos.length > 0 ? `
                <div class="review-photos">
                    <div class="photos-grid">
                        ${booking.photos.map(photo => {
                            // Handle both dataUrl (uploaded in session) and storage path (from database)
                            const photoSrc = photo.dataUrl || (typeof photo === 'string' ? `/storage/${photo}` : photo);
                            return `
                                <div class="photo-thumbnail">
                                    <img src="${photoSrc}" alt="Review photo" onclick="openPhotoModal('${photoSrc}')">
                                </div>
                            `;
                        }).join('')}
                    </div>
                </div>
            ` : ''}
        ` : `
            <div class="history-actions">
                <button class="btn-book-again">Booking Lagi</button>
            </div>
        `}
    `;
    
    // Add event listeners
    const reviewBtn = card.querySelector('.btn-review');
    const editBtn = card.querySelector('.btn-edit-review');
    const bookAgainBtn = card.querySelector('.btn-book-again');
    
    if (reviewBtn && !hasRating) {
        reviewBtn.addEventListener('click', function(e) {
            openReviewModal(booking, false);
        });
    }
    
    if (editBtn && hasRating) {
        editBtn.addEventListener('click', function(e) {
            openReviewModal(booking, true);
        });
    }
    
    if (bookAgainBtn) {
        bookAgainBtn.addEventListener('click', function(e) {
            handleBookAgain(booking);
        });
    }
    
    return card;
}

function generateStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<span class="star filled">★</span>';
        } else {
            stars += '<span class="star">☆</span>';
        }
    }
    return stars;
}

// ========== BOOKING ACTIONS ==========
function initializeBookingActions() {
    const cancelBtn = document.querySelector('.btn-cancel');
    const rescheduleBtn = document.querySelector('.btn-reschedule');
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', openCancelModal);
    }
    
    if (rescheduleBtn) {
        rescheduleBtn.addEventListener('click', openRescheduleModal);
    }
    
    console.log('🎬 Booking actions initialized');
}

function handleBookAgain(booking) {
    if (!booking) return;
    
    console.log('🔄 Booking again:', { 
        partner: booking.partner, 
        service: booking.service, 
        price: booking.price 
    });
    
    // Prepare URL parameters with all booking details
    const params = new URLSearchParams({
        service: booking.service,
        price: booking.price
    });
    
    // Navigate to booking page with service and price parameters
    window.location.href = `/customer/atur-booking/booking?${params.toString()}`;
}

// ========== CANCEL MODAL SYSTEM ==========
function initializeCancelModal() {
    const refundRadios = document.querySelectorAll('input[name="refundMethod"]');
    const accountInput = document.getElementById('cancelAccountNumber');
    
    if (refundRadios.length > 0) {
        refundRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                cancelData.refundMethod = this.value;
                console.log('💳 Refund method selected:', this.value);
            });
        });
    }
    
    if (accountInput) {
        accountInput.addEventListener('input', function() {
            cancelData.accountNumber = this.value;
        });
    }
    
    console.log('🚫 Cancel modal initialized');
}

function openCancelModal() {
    if (!currentBooking) {
        alert('Tidak ada booking yang aktif');
        return;
    }
    
    // Check if booking can be cancelled
    const statusValue = getStatusValueFromLabel(currentBooking.status);
    if (statusValue >= BOOKING_STATUS.IN_PROGRESS) {
        alert('Booking tidak dapat dibatalkan karena sudah dalam proses atau selesai');
        return;
    }
    
    const modal = document.getElementById('cancelModalOverlay');
    const partnerName = document.getElementById('cancelPartnerName');
    const serviceType = document.getElementById('cancelServiceType');
    const bookingDate = document.getElementById('cancelBookingDate');
    const bookingTime = document.getElementById('cancelBookingTime');
    
    if (!currentBooking) {
        console.error('No current booking found');
        return;
    }
    
    if (partnerName) partnerName.textContent = currentBooking.partner;
    if (serviceType) serviceType.textContent = currentBooking.treatment;
    if (bookingDate) bookingDate.textContent = currentBooking.date;
    if (bookingTime) bookingTime.textContent = currentBooking.time;
    
    // Reset form
    const accountInput = document.getElementById('cancelAccountNumber');
    if (accountInput) {
        accountInput.value = '';
    }
    
    // Set default refund method
    const defaultRadio = document.querySelector('input[name="refundMethod"][value="shopeepay"]');
    if (defaultRadio) {
        defaultRadio.checked = true;
        cancelData.refundMethod = 'shopeepay';
    }
    
    cancelData = { refundMethod: 'shopeepay', accountNumber: '' };
    
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        console.log('🚫 Cancel modal opened');
    }
}

function closeCancelModal() {
    const modal = document.getElementById('cancelModalOverlay');
    
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        console.log('✖ Cancel modal closed');
    }
}

function confirmCancelBooking() {
    // Validate account number
    if (!cancelData.accountNumber || cancelData.accountNumber.trim() === '') {
        alert('Mohon masukkan nomor rekening/e-wallet!');
        return;
    }
    
    // Validate account number format (must be numeric and at least 8 digits)
    if (!/^\d{8,}$/.test(cancelData.accountNumber)) {
        alert('Nomor rekening/e-wallet harus berisi minimal 8 digit angka!');
        return;
    }
    
    console.log('✅ Cancel booking confirmed:', {
        bookingId: currentBooking.id,
        refundMethod: cancelData.refundMethod,
        accountNumber: cancelData.accountNumber
    });
    
    if (!currentBooking) {
        console.error('No current booking found');
        return;
    }
    
    // Close cancel modal
    closeCancelModal();
    
    // Show processing
    const cancelBtn = document.querySelector('.btn-cancel');
    if (cancelBtn) {
        cancelBtn.disabled = true;
        cancelBtn.textContent = 'Memproses...';
    }
    
    // Simulate API call
    setTimeout(() => {
        console.log('🚫 Booking cancelled successfully');
        
        // Add cancelled booking to history
        const cancelledBooking = {
            id: currentBooking.id,
            partner: currentBooking.partner,
            service: currentBooking.treatment,
            date: currentBooking.date,
            price: currentBooking.totalPrice,
            rating: 0,
            comment: '',
            status: 'Dibatalkan',
            hasReview: false,
            photos: []
        };
        
        // Add to beginning of history
        bookingHistory.unshift(cancelledBooking);
        
        // Show success modal
        const successModal = document.getElementById('successModalOverlay');
        const successModalContent = successModal.querySelector('.success-modal');
        
        if (successModalContent) {
            successModalContent.innerHTML = `
                <div class="success-icon">✓</div>
                <h3>Booking berhasil dibatalkan!</h3>
                <p style="margin: 15px 0; color: #666; font-size: 14px;">
                    Dana Rp ${formatCurrency(currentBooking.totalPrice - 2500).replace('Rp ', '')} akan dikembalikan ke ${getRefundMethodName(cancelData.refundMethod)}<br>
                    Nomor: ${cancelData.accountNumber}<br><br>
                    <strong>Estimasi: 1-3 hari kerja</strong>
                </p>
                <button class="btn-primary" onclick="closeSuccessModalAndRefresh()">Tutup</button>
            `;
        }
        
        if (successModal) {
            successModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }, 1500);
}

function getRefundMethodName(method) {
    const methods = {
        'ovo': 'OVO',
        'dana': 'Dana',
        'gopay': 'Gopay',
        'shopeepay': 'ShopeePay'
    };
    return methods[method] || method;
}

// ========== RESCHEDULE MODAL SYSTEM ==========
let mitraBusinessData = null;
let bookedSlotsData = {};

function initializeRescheduleModal() {
    const rescheduleDate = document.getElementById('rescheduleDate');
    
    if (rescheduleDate) {
        const today = new Date().toISOString().split('T')[0];
        rescheduleDate.setAttribute('min', today);
        
        rescheduleDate.addEventListener('change', async function() {
            rescheduleData.date = this.value;
            await loadTimeSlots(this.value);
        });
    }
    
    console.log('📅 Reschedule modal initialized');
}

async function loadTimeSlots(selectedDate) {
    if (!currentBooking) {
        console.error('❌ No current booking data');
        return;
    }
    
    if (!currentBooking.mitraId) {
        console.error('❌ No mitraId in current booking');
        alert('Data mitra tidak ditemukan. Silakan refresh halaman.');
        return;
    }
    
    console.log('🔄 Loading time slots for:', selectedDate, 'Mitra ID:', currentBooking.mitraId);
    
    // Show loading state
    const timeSelect = document.getElementById('rescheduleTime');
    if (timeSelect) {
        timeSelect.innerHTML = '<option value="">Memuat data...</option>';
    }
    
    // Load mitra business data if not loaded
    if (!mitraBusinessData) {
        try {
            console.log('📥 Fetching mitra data...');
            const response = await fetch(`/api/mitra/${currentBooking.mitraId}`);
            if (response.ok) {
                const data = await response.json();
                mitraBusinessData = data;
                console.log('✅ Mitra data loaded:', mitraBusinessData);
            } else {
                console.error('❌ Failed to load mitra data:', response.status);
                if (timeSelect) {
                    timeSelect.innerHTML = '<option value="">Gagal memuat data mitra</option>';
                }
                return;
            }
        } catch (error) {
            console.error('❌ Error loading mitra data:', error);
            if (timeSelect) {
                timeSelect.innerHTML = '<option value="">Gagal memuat data mitra</option>';
            }
            return;
        }
    }
    
    // Load booked slots if not loaded
    if (!bookedSlotsData[selectedDate]) {
        try {
            console.log('📥 Fetching booked slots...');
            const startDate = selectedDate;
            const endDate = selectedDate;
            const response = await fetch(`/api/bookings/slots/${currentBooking.mitraId}?start_date=${startDate}&end_date=${endDate}`);
            if (response.ok) {
                const data = await response.json();
                bookedSlotsData = { ...bookedSlotsData, ...data.booked_slots };
                console.log('✅ Booked slots loaded:', bookedSlotsData);
            } else {
                console.error('❌ Failed to load booked slots:', response.status);
            }
        } catch (error) {
            console.error('❌ Error loading booked slots:', error);
        }
    }
    
    // Generate time slots only after data is loaded
    generateRescheduleTimeSlots(selectedDate);
}

function generateRescheduleTimeSlots(selectedDate) {
    const timeSelect = document.getElementById('rescheduleTime');
    if (!timeSelect) return;
    
    // Clear existing options
    timeSelect.innerHTML = '<option value="">Pilih Waktu</option>';
    
    // Get day of week
    const date = new Date(selectedDate + 'T00:00:00');
    const dayOfWeek = date.getDay();
    
    console.log('📅 Generating slots for:', selectedDate, 'Day:', dayOfWeek);
    console.log('🔍 mitraBusinessData:', mitraBusinessData);
    console.log('🔍 mitraProfile:', mitraBusinessData?.mitraProfile);
    console.log('🔍 mitra_profile:', mitraBusinessData?.mitra_profile);
    
    // Check if mitra data is loaded
    if (!mitraBusinessData) {
        console.error('❌ Mitra data is null');
        timeSelect.innerHTML = '<option value="">Memuat data...</option>';
        return;
    }
    
    // Check both camelCase and snake_case
    const mitraProfile = mitraBusinessData.mitraProfile || mitraBusinessData.mitra_profile;
    if (!mitraProfile) {
        console.error('❌ Mitra profile not found in data');
        timeSelect.innerHTML = '<option value="">Data mitra tidak lengkap</option>';
        return;
    }
    
    // Get operational hours for this day
    const dailyHours = mitraProfile.operational_hours || {};
    const dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    const dayName = dayNames[dayOfWeek];
    const hours = dailyHours[dayName];
    
    console.log('🕐 Hours for', dayName, ':', hours);
    
    if (!hours || !hours.enabled) {
        timeSelect.innerHTML = '<option value="">Tutup pada hari ini</option>';
        console.log('⚠️ Mitra tutup pada hari', dayName);
        return;
    }
    
    // Parse hours
    const [openHour, openMinute] = hours.open.split(':').map(Number);
    const [closeHour, closeMinute] = hours.close.split(':').map(Number);
    
    let currentMinutes = openHour * 60 + openMinute;
    const endMinutes = closeHour * 60 + closeMinute;
    
    // Check if today
    const now = new Date();
    const isToday = date.getDate() === now.getDate() &&
                    date.getMonth() === now.getMonth() &&
                    date.getFullYear() === now.getFullYear();
    const nowMinutes = now.getHours() * 60 + now.getMinutes();
    
    console.log('⏰ Is today:', isToday, 'Current minutes:', nowMinutes);
    
    // Get max slots for this service from mitraProfile.custom_services
    let maxSlots = 3; // Default
    
    if (mitraProfile && mitraProfile.custom_services && Array.isArray(mitraProfile.custom_services)) {
        console.log('📋 All services:', mitraProfile.custom_services);
        
        // Try to match service by name or service_name
        for (let service of mitraProfile.custom_services) {
            console.log('🔍 Checking service:', service);
            
            if (service.name === currentBooking.treatment || service.service_name === currentBooking.treatment) {
                maxSlots = parseInt(service.max_slots) || 3;
                console.log('✅ Found matching service, max_slots:', maxSlots);
                break;
            }
        }
        
        if (maxSlots === 3) {
            console.warn('⚠️ No matching service found, using default 3 slots');
        }
    } else {
        console.warn('⚠️ No custom_services in mitraProfile');
    }
    
    console.log('🎯 Service:', currentBooking.treatment, 'Max slots:', maxSlots);
    
    let optionCount = 0;
    
    // Generate time slots
    while (currentMinutes < endMinutes) {
        const hour = Math.floor(currentMinutes / 60);
        const minute = currentMinutes % 60;
        const timeString = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
        
        // Skip if time has passed today
        if (isToday && currentMinutes <= nowMinutes) {
            currentMinutes += 30;
            continue;
        }
        
        // Check if during break time
        const breaks = hours.breakSchedules || [];
        const isDuringBreak = breaks.some(breakPeriod => {
            const [breakStartHour, breakStartMinute] = breakPeriod.open.split(':').map(Number);
            const [breakEndHour, breakEndMinute] = breakPeriod.close.split(':').map(Number);
            const breakStartMinutes = breakStartHour * 60 + breakStartMinute;
            const breakEndMinutes = breakEndHour * 60 + breakEndMinute;
            return currentMinutes >= breakStartMinutes && currentMinutes < breakEndMinutes;
        });
        
        if (isDuringBreak) {
            currentMinutes += 30;
            continue;
        }
        
        // Check if slot is fully booked
        const bookedCount = bookedSlotsData[selectedDate]?.filter(t => t === timeString).length || 0;
        const isFullyBooked = bookedCount >= maxSlots;
        
        if (!isFullyBooked) {
            const availableSlots = maxSlots - bookedCount;
            const option = document.createElement('option');
            option.value = timeString;
            option.textContent = `${timeString} (${availableSlots} slot tersedia)`;
            timeSelect.appendChild(option);
            optionCount++;
        }
        
        currentMinutes += 30;
    }
    
    console.log('✅ Generated', optionCount, 'time slots');
    
    if (optionCount === 0) {
        timeSelect.innerHTML = '<option value="">Tidak ada slot tersedia</option>';
    }
    
    // Add change listener for time select
    timeSelect.onchange = function() {
        rescheduleData.time = this.value;
    };
}

async function openRescheduleModal() {
    if (!currentBooking) {
        alert('Tidak ada booking yang aktif');
        return;
    }
    
    // Check if mitraId exists
    if (!currentBooking.mitraId) {
        console.error('❌ No mitraId found in currentBooking:', currentBooking);
        alert('Data mitra tidak ditemukan. Silakan refresh halaman.');
        return;
    }
    
    // Check if booking can be rescheduled
    const statusValue = getStatusValueFromLabel(currentBooking.status);
    if (statusValue >= BOOKING_STATUS.IN_PROGRESS) {
        alert('Booking tidak dapat direschedule karena sudah dalam proses atau selesai');
        return;
    }
    
    const modal = document.getElementById('rescheduleModalOverlay');
    const partnerName = document.getElementById('reschedulePartnerName');
    const serviceType = document.getElementById('rescheduleServiceType');
    
    if (partnerName) partnerName.textContent = currentBooking.partner;
    if (serviceType) serviceType.textContent = currentBooking.treatment;
    
    const dateInput = document.getElementById('rescheduleDate');
    const timeInput = document.getElementById('rescheduleTime');
    
    if (dateInput) dateInput.value = '';
    if (timeInput) {
        timeInput.innerHTML = '<option value="">Pilih tanggal terlebih dahulu</option>';
        timeInput.value = '';
    }
    
    rescheduleData = { date: null, time: null };
    
    // Preload mitra business data before opening modal
    if (!mitraBusinessData) {
        try {
            console.log('📥 Preloading mitra data...');
            const response = await fetch(`/api/mitra/${currentBooking.mitraId}`);
            if (response.ok) {
                const data = await response.json();
                mitraBusinessData = data;
                console.log('✅ Mitra data preloaded:', mitraBusinessData);
            } else {
                console.error('❌ Failed to preload mitra data:', response.status);
                alert('Gagal memuat data mitra. Silakan coba lagi.');
                return;
            }
        } catch (error) {
            console.error('❌ Error preloading mitra data:', error);
            alert('Gagal memuat data mitra. Silakan coba lagi.');
            return;
        }
    }
    
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        console.log('📅 Reschedule modal opened for mitra:', currentBooking.mitraId);
    }
}

function closeRescheduleModal() {
    const modal = document.getElementById('rescheduleModalOverlay');
    
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        console.log('✖ Reschedule modal closed');
    }
}

function confirmReschedule() {
    if (!rescheduleData.date || !rescheduleData.time) {
        alert('Mohon lengkapi tanggal dan waktu baru!');
        return;
    }
    
    if (!currentBooking || !currentBooking.id) {
        alert('Data booking tidak ditemukan!');
        return;
    }
    
    console.log('✅ Reschedule confirmed:', rescheduleData);
    console.log('📤 Sending reschedule request for booking:', currentBooking.id);
    
    // Send reschedule request to backend
    fetch('/customer/booking/reschedule', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify({
            booking_code: currentBooking.id,
            new_date: rescheduleData.date,
            new_time: rescheduleData.time
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('✅ Reschedule successful:', data);
            
            // Update current booking display
            if (currentBooking) {
                currentBooking.date = formatDateToIndonesian(rescheduleData.date);
                currentBooking.time = rescheduleData.time;
                updateCurrentBookingDisplay();
            }
            
            closeRescheduleModal();
            
            setTimeout(() => {
                openSuccessModal();
            }, 300);
        } else {
            alert(data.message || 'Gagal melakukan reschedule');
        }
    })
    .catch(error => {
        console.error('❌ Reschedule error:', error);
        alert('Terjadi kesalahan saat reschedule. Silakan coba lagi.');
    });
}

function openSuccessModal() {
    const modal = document.getElementById('successModalOverlay');
    
    if (modal) {
        modal.classList.add('active');
        console.log('✅ Success modal opened');
    }
}

function closeSuccessModal() {
    const modal = document.getElementById('successModalOverlay');
    
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        console.log('✖ Success modal closed');
    }
}

function closeSuccessModalAndRefresh() {
    closeSuccessModal();
    
    // Hide booking section
    const bookingSection = document.querySelector('.booking-current');
    if (bookingSection) {
        bookingSection.style.display = 'none';
    }
    
    // Reset current booking
    currentBooking = null;
    
    // Refresh history display
    renderBookingHistory();
    
    console.log('🔄 Page refreshed after cancel');
}

// ========== UTILITY FUNCTIONS ==========
function formatCurrency(amount) {
    return 'Rp ' + amount.toLocaleString('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
}

function formatDateToIndonesian(dateString) {
    const date = new Date(dateString);
    const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear();
    
    return `${day} ${month} ${year}`;
}

// ========== SMOOTH SCROLL & EFFECTS ==========
function initializeSmoothScroll() {
    // Smooth scroll for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    console.log('🔄 Smooth scroll initialized');
}

function initializeServiceCardEffects() {
    const serviceCards = document.querySelectorAll('.service-card');
    
    serviceCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-5px)';
            card.style.transition = 'transform 0.3s ease';
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
        });
    });
    
    console.log('🎴 Service card effects initialized');
}

// ========== BOOKING SUCCESS CHECK ==========
function checkBookingSuccess() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('bookingSuccess') === 'true') {
        console.log('✅ New booking created successfully');
        setTimeout(() => {
            alert('🎉 Booking berhasil dibuat!\n\nPembayaran Anda sedang diverifikasi.');
        }, 500);
        updateNotificationBadge(5);
    }
}

// ========== PHOTO MODAL ==========
function openPhotoModal(imageUrl) {
    const modal = document.createElement('div');
    modal.className = 'photo-modal-overlay';
    modal.innerHTML = `
        <div class="photo-modal-content">
            <button class="photo-modal-close" onclick="closePhotoModal()">&times;</button>
            <img src="${imageUrl}" alt="Review photo">
        </div>
    `;
    modal.onclick = function(e) {
        if (e.target === modal) closePhotoModal();
    };
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
    setTimeout(() => modal.classList.add('active'), 10);
}

function closePhotoModal() {
    const modal = document.querySelector('.photo-modal-overlay');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => {
            modal.remove();
            document.body.style.overflow = '';
        }, 300);
    }
}

// ========== KEYBOARD SHORTCUTS ==========
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeNotificationPanel();
        closeRescheduleModal();
        closeSuccessModal();
        closeCancelModal();
        closeReviewModal();
        closePhotoModal();
    }
});

// ========== ERROR HANDLING ==========
window.addEventListener('error', function(e) {
    console.error('✖ JavaScript Error:', e.message);
});

// ========== STATUS TABS ENHANCEMENT ==========
function initializeStatusTabs() {
    const tabs = document.querySelectorAll('.status-tabs .tab');
    const progressFill = document.getElementById('progressFill');
    
    console.log('📊 Status tabs initialized');
}

function updateStatusProgress(status) {
    const tabs = document.querySelectorAll('.status-tabs .tab');
    const progressFill = document.getElementById('progressFill');
    const statusInfoTitle = document.getElementById('statusInfoTitle');
    const statusInfoText = document.getElementById('statusInfoText');
    
    // Update active tab
    tabs.forEach((tab, index) => {
        const step = parseInt(tab.dataset.step);
        
        // Remove all status classes
        tab.classList.remove('active', 'completed');
        
        // Add appropriate class
        if (step < status) {
            tab.classList.add('completed');
            // Add timestamp for completed steps
            updateStepTimestamp(step);
        } else if (step === status) {
            tab.classList.add('active');
            // If status is completed (step 4), also add completed class
            if (step === 4) {
                tab.classList.add('completed');
                updateStepTimestamp(step);
            }
        }
    });
    
    // Update progress bar
    if (progressFill) {
        const progressWidth = ((status - 1) / 3) * 100; // 0%, 33%, 66%, 100%
        progressFill.style.width = progressWidth + '%';
    }
    
    // Update status info card
    const statusInfo = getStatusInfo(status);
    if (statusInfoTitle) statusInfoTitle.textContent = statusInfo.title;
    if (statusInfoText) statusInfoText.textContent = statusInfo.text;
    
    console.log('✅ Status updated to:', STATUS_LABELS[status]);
    
    // Check if status is COMPLETED and show completion modal
    if (status === BOOKING_STATUS.COMPLETED && currentBooking) {
        setTimeout(() => {
            showCompletionModal();
        }, 500);
    }
}

function updateStepTimestamp(step) {
    const timeElement = document.getElementById(`time-step-${step}`);
    if (timeElement) {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        timeElement.textContent = timeString;
    }
}

function showCompletionModal() {
    if (!currentBooking) return;
    
    console.log('🎉 Booking completed');
    
    // Move completed booking to history
    const completedBooking = {
        id: currentBooking.id,
        partner: currentBooking.partner,
        service: currentBooking.treatment,
        date: currentBooking.date,
        price: currentBooking.totalPrice,
        rating: 0,
        comment: '',
        status: 'Selesai',
        hasReview: false,
        photos: []
    };
    
    // Add to beginning of history
    bookingHistory.unshift(completedBooking);
    
    // Show completion modal
    const successModal = document.getElementById('successModalOverlay');
    const successTitle = successModal?.querySelector('h3');
    
    if (successTitle) {
        successTitle.textContent = 'Booking telah selesai';
    }
    
    if (successModal) {
        successModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    // Update closeSuccessModal to handle completion
    window.closeSuccessModal = function() {
        if (successModal) {
            successModal.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        // Hide current booking section
        const bookingSection = document.querySelector('.booking-current');
        if (bookingSection) {
            bookingSection.style.display = 'none';
        }
        
        // Refresh history
        renderBookingHistory();
        
        // Scroll to history section
        const historySection = document.querySelector('.booking-history');
        if (historySection) {
            historySection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        
        // Clear current booking
        currentBooking = null;
        
        console.log('✅ Completion modal closed and booking moved to history');
    };
}

function getStatusInfo(status) {
    const statusMessages = {
        1: {
            title: 'Transaksi Sedang Dicek oleh Admin',
            text: 'Admin sedang memverifikasi pembayaran dan detail booking Anda. Proses ini biasanya memakan waktu beberapa menit.'
        },
        2: {
            title: 'Booking Dikonfirmasi - Harap Tiba Tepat Waktu',
            text: 'Booking Anda telah dikonfirmasi. Pastikan Anda tiba di lokasi sesuai jadwal yang telah ditentukan.'
        },
        3: {
            title: 'Kendaraan Sedang Dalam Proses Pembersihan',
            text: 'Mitra sedang membersihkan kendaraan Anda. Mohon tunggu hingga proses selesai.'
        },
        4: {
            title: 'Kendaraan Telah Selesai Dibersihkan',
            text: 'Kendaraan Anda sudah selesai dibersihkan dan siap diambil. Jangan lupa berikan review untuk mitra kami!'
        }
    };
    
    return statusMessages[status] || statusMessages[1];
}

// Simulate status progression (for demo purposes)
function simulateStatusProgress() {
    let currentStep = 1;
    const interval = setInterval(() => {
        if (currentStep < 4) {
            currentStep++;
            updateStatusProgress(currentStep);
        } else {
            clearInterval(interval);
            console.log('🏁 Status simulation completed');
        }
    }, 3000); // Change status every 3 seconds
}

// Uncomment to auto-simulate status progression
// setTimeout(() => simulateStatusProgress(), 2000);

console.log('✅ All JavaScript systems loaded successfully');