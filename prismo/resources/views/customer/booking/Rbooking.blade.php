<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-role" content="customer">
    <title>Status Pengerjaan & Booking</title>
    <link rel="stylesheet" href="{{ asset('css/Rbooking.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Header Status Pengerjaan -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <div class="logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Prismo Logo">
                </div>
                <ul class="nav-menu" id="mainNav">
                    <li class="mobile-profile-menu">
                        <div class="user-profile mobile-user-profile" onclick="window.location.href='{{ url('/customer/profil/uprofil') }}'" style="cursor: pointer;">
                            @php
                                $avatar = auth()->user()->avatar;
                                if (!$avatar) {
                                    $avatarUrl = asset('images/profile.png');
                                } elseif (Str::startsWith($avatar, 'http')) {
                                    $avatarUrl = $avatar;
                                } elseif (Str::startsWith($avatar, 'storage/')) {
                                    $avatarUrl = asset($avatar);
                                } elseif (Str::startsWith($avatar, '/storage/')) {
                                    $avatarUrl = url($avatar);
                                } else {
                                    $avatarUrl = asset('storage/' . $avatar);
                                }
                            @endphp
                            <img src="{{ $avatarUrl }}?v={{ auth()->user()->updated_at->timestamp }}" alt="User" class="user-icon-img" id="mobileBookingProfileImg">
                            <div class="user-info">
                                <span class="user-name" id="mobileBookingUserName">{{ auth()->user()->name }}</span>
                                <span class="user-role">User</span>
                            </div>
                        </div>
                    </li>
                    <li><a href="{{ url('/customer/dashboard/dashU') }}" class="nav-link">Beranda</a></li>
                    <li><a href="{{ url('/customer/booking/Rbooking') }}" class="nav-link active">Booking</a></li>
                    <li><a href="{{ url('/customer/voucher/voucher') }}" class="nav-link">Voucher</a></li>
                </ul>
                <div class="nav-right">
                    <button class="notification-btn" id="notifBtn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">5</span>
                    </button>
                    <div class="user-profile desktop-user-profile" onclick="window.location.href='{{ url('/customer/profil/uprofil') }}'" style="cursor: pointer;">
                        @php
                            $avatar = auth()->user()->avatar;
                            if (!$avatar) {
                                $avatarUrl = asset('images/profile.png');
                            } elseif (Str::startsWith($avatar, 'http')) {
                                $avatarUrl = $avatar;
                            } elseif (Str::startsWith($avatar, 'storage/')) {
                                $avatarUrl = asset($avatar);
                            } elseif (Str::startsWith($avatar, '/storage/')) {
                                $avatarUrl = url($avatar);
                            } else {
                                $avatarUrl = asset('storage/' . $avatar);
                            }
                        @endphp
                        <img src="{{ $avatarUrl }}?v={{ auth()->user()->updated_at->timestamp }}" alt="User" class="user-icon-img" id="bookingProfileImg">
                        <div class="user-info">
                            <span class="user-name" id="bookingUserName">{{ auth()->user()->name }}</span>
                            <span class="user-role">User</span>
                        </div>
                    </div>
                    <button class="mobile-menu-toggle" id="menuToggle" aria-label="Toggle Menu">
                        ☰
                    </button>
                </div>
            </nav>
        </div>
    </header>

    <!-- Overlay -->
    <div class="notification-overlay" id="notifOverlay"></div>

    <!-- Notification Panel -->
    <div class="notification-panel" id="notifPanel">
        <div class="panel-header">
            <h2>Notifikasi</h2>
            <button class="mark-all-read-btn" id="markAllReadBtn" style="display: none; font-size: 12px; padding: 4px 8px; background: #1c98f5; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Tandai Semua Dibaca
            </button>
        </div>

        <div id="notificationList">
            <!-- Notifications will be loaded here dynamically -->
            <div class="loading-state" style="text-align: center; padding: 40px; color: #999;">
                <i class="fas fa-spinner fa-spin" style="font-size: 24px; margin-bottom: 12px;"></i>
                <p>Memuat notifikasi...</p>
            </div>
        </div>
    </div>

    <!-- Overlay -->
    <div class="sort-overlay" id="sortOverlay"></div>

    <!-- Booking Sections -->
    <div class="booking-sections">
        <!-- Booking Saat Ini -->
        <section class="booking-current">
            <h3>Booking Saat ini</h3>
            
            <div class="booking-card">
            <!-- Status Pengerjaan -->
            <div class="status-section">
                <div class="status-header">
                    <h2>Status Pengerjaan</h2>
                </div>
                
                <!-- Progress Bar -->
                <div class="progress-wrapper">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                </div>
                
                <div class="status-tabs" style="pointer-events: none;">
                    <div class="tab active" data-step="1">
                        <div class="tab-connector"></div>
                        <div class="icon-wrapper">
                            <div class="icon-circle">
                                <img src="{{ asset('images/imenunggu.png') }}" alt="Cek Transaksi" class="status-icon">
                            </div>
                            <div class="pulse-ring"></div>
                            <div class="tab-badge">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <div class="tab-content">
                            <span class="label">Cek Transaksi</span>
                            <span class="description">Admin melakukan pengecekan</span>
                            <span class="status-time" id="time-step-1"></span>
                        </div>
                    </div>
                    
                    <div class="tab" data-step="2">
                        <div class="tab-connector"></div>
                        <div class="icon-wrapper">
                            <div class="icon-circle">
                                <img src="{{ asset('images/imulai.png') }}" alt="Menunggu" class="status-icon">
                            </div>
                            <div class="pulse-ring"></div>
                            <div class="tab-badge">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <div class="tab-content">
                            <span class="label">Menunggu</span>
                            <span class="description">Pastikan tiba tepat waktu</span>
                            <span class="status-time" id="time-step-2"></span>
                        </div>
                    </div>
                    
                    <div class="tab" data-step="3">
                        <div class="tab-connector"></div>
                        <div class="icon-wrapper">
                            <div class="icon-circle">
                                <img src="{{ asset('images/iproses.png') }}" alt="Dalam Proses" class="status-icon">
                            </div>
                            <div class="pulse-ring"></div>
                            <div class="tab-badge">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <div class="tab-content">
                            <span class="label">Dalam Proses</span>
                            <span class="description">Kendaraan sedang dibersihkan</span>
                            <span class="status-time" id="time-step-3"></span>
                        </div>
                    </div>
                    
                    <div class="tab" data-step="4">
                        <div class="tab-connector"></div>
                        <div class="icon-wrapper">
                            <div class="icon-circle">
                                <img src="{{ asset('images/iselesai.png') }}" alt="Selesai" class="status-icon">
                            </div>
                            <div class="pulse-ring"></div>
                            <div class="tab-badge">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <div class="tab-content">
                            <span class="label">Selesai</span>
                            <span class="description">Kendaraan telah dibersihkan</span>
                            <span class="status-time" id="time-step-4"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Booking Details -->
            <div class="booking-header">
                <h4 id="currentPartner">Prismo Pro</h4>
                <span class="price" id="currentPrice">Total: Rp 150.000</span>
            </div>
            
            <p class="location" id="currentLocation">Jl. Sudirman No. 45, Jakarta Pusat</p>
            
            <div class="booking-details">
                <div class="detail-row">
                    <span class="label">Tanggal:</span>
                    <span class="value" id="currentDate">12 September 2025</span>
                </div>
                <div class="detail-row">
                    <span class="label">Jam:</span>
                    <span class="value" id="currentTime">14:00</span>
                </div>
                <div class="detail-row">
                    <span class="label">Layanan:</span>
                    <span class="value" id="currentTreatment">Cuci Mobil + Salon</span>
                </div>
                <div class="detail-row">
                    <span class="label">Tipe:</span>
                    <span class="value" id="currentType">4-Class</span>
                </div>
                <div class="detail-row">
                    <span class="label">Nomor Polisi:</span>
                    <span class="value" id="currentNopol">B 2348 NT</span>
                </div>
            </div>
            
            <div class="booking-actions">
                <button class="btn-cancel">Cancel</button>
                <button class="btn-reschedule">Reschedule</button>
            </div>
        </div>
    </section>

    <!-- Booking Terakhir -->
    <section class="booking-history">
        <h3>Booking Terakhir</h3>
        
        <div id="historyContainer">
            <!-- History cards will be dynamically generated here -->
        </div>
    </section>
    </div>

    <!-- Cancel Modal -->
    <div class="modal-overlay" id="cancelModalOverlay">
        <div class="modal-container cancel-modal">
            <div class="modal-header">
                <h3>Cancel Booking</h3>
                <button class="modal-close" onclick="closeCancelModal()">×</button>
            </div>
            
            <div class="modal-body">
                <div class="booking-info">
                    <h4 id="cancelPartnerName">Prismo Pro</h4>
                    <p id="cancelServiceType">Cuci Mobil + Salon</p>
                </div>
                
                <div class="booking-details-info">
                    <div class="detail-item">
                        <span class="label">Tanggal</span>
                        <span class="value" id="cancelBookingDate">12 September 2025</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Waktu</span>
                        <span class="value" id="cancelBookingTime">14:00</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Pilih E-Wallet Tujuan Pengembalian Dana :</label>
                    
                    <div class="radio-group">
                        <!-- OVO -->
                        <label class="radio-option">
                            <img src="{{ asset('images/ovo-logo.png') }}" alt="OVO" class="ewallet-icon">
                            <span class="radio-label">OVO</span>
                            <input type="radio" name="refundMethod" value="ovo">
                        </label>
                        
                        <!-- Dana -->
                        <label class="radio-option">
                            <img src="{{ asset('images/dana-logo.png') }}" alt="Dana" class="ewallet-icon">
                            <span class="radio-label">Dana</span>
                            <input type="radio" name="refundMethod" value="dana">
                        </label>
                        
                        <!-- Gopay -->
                        <label class="radio-option">
                            <img src="{{ asset('images/gopay-logo.png') }}" alt="Gopay" class="ewallet-icon">
                            <span class="radio-label">Gopay</span>
                            <input type="radio" name="refundMethod" value="gopay">
                        </label>
                        
                        <!-- Shopeepay (Default Selected) -->
                        <label class="radio-option">
                            <img src="{{ asset('images/shopee-logo.png') }}" alt="Shopeepay" class="ewallet-icon">
                            <span class="radio-label">Shopeepay</span>
                            <input type="radio" name="refundMethod" value="shopeepay" checked>
                        </label>
                    </div>
                    
                    <input 
                        type="text" 
                        id="cancelAccountNumber" 
                        class="form-input account-input" 
                        placeholder="089734653793"
                        maxlength="15"
                    >
                </div>
                
                <div class="warning-box">
                    <i class="fas fa-exclamation-circle"></i>
                    <div class="warning-text">
                        <strong>Perhatian!</strong>
                        <p>Pengembalian booking akan dikenakan biaya admin Rp 2.500 yang dipotong dari pengembalian dana.</p>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeCancelModal()">Batal</button>
                <button class="btn-danger" onclick="confirmCancelBooking()">Konfirmasi Cancel</button>
            </div>
        </div>
    </div>

    <!-- Reschedule Modal -->
    <div class="modal-overlay" id="rescheduleModalOverlay">
        <div class="modal-container reschedule-modal">
            <div class="modal-header">
                <h3>Reschedule Booking</h3>
                <button class="modal-close" onclick="closeRescheduleModal()">×</button>
            </div>
            
            <div class="modal-body">
                <div class="booking-info">
                    <h4 id="reschedulePartnerName">Prismo Pro</h4>
                    <p id="rescheduleServiceType">Cuci Mobil + Salon</p>
                </div>
                
                <div class="form-group">
                    <label>Masukkan Tanggal Baru</label>
                    <input type="date" id="rescheduleDate" class="form-input">
                </div>
                
                <div class="form-group">
                    <label>Masukkan Waktu Baru</label>
                    <select id="rescheduleTime" class="form-input">
                        <option value="">Pilih Waktu</option>
                        <option value="06:00">06:00</option>
                        <option value="06:30">06:30</option>
                        <option value="07:00">07:00</option>
                        <option value="07:30">07:30</option>
                        <option value="08:00">08:00</option>
                        <option value="08:30">08:30</option>
                        <option value="09:00">09:00</option>
                        <option value="09:30">09:30</option>
                        <option value="10:00">10:00</option>
                        <option value="10:30">10:30</option>
                        <option value="11:00">11:00</option>
                        <option value="11:30">11:30</option>
                        <option value="12:00">12:00</option>
                        <option value="12:30">12:30</option>
                        <option value="13:00">13:00</option>
                        <option value="13:30">13:30</option>
                        <option value="14:00">14:00</option>
                        <option value="14:30">14:30</option>
                        <option value="15:00">15:00</option>
                        <option value="15:30">15:30</option>
                        <option value="16:00">16:00</option>
                    </select>
                </div>
            </div>
            
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeRescheduleModal()">Batal</button>
                <button class="btn-primary" onclick="confirmReschedule()">Konfirmasi Reschedule</button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal-overlay" id="successModalOverlay">
        <div class="modal-container success-modal">
            <div class="success-icon">✓</div>
            <h3>Jadwal telah berhasil diubah</h3>
            <button class="btn-primary" onclick="closeSuccessModal()">Ya</button>
        </div>
    </div>

    <!-- Review Modal -->
    <div class="modal-overlay" id="reviewModalOverlay">
        <div class="modal-container review-modal">
            <div class="modal-header">
                <h3 id="reviewModalTitle">Beri Rating & Review</h3>
                <button class="modal-close" onclick="closeReviewModal()">×</button>
            </div>
            
            <div class="modal-body">
                <div class="booking-info">
                    <h4 id="reviewPartnerName">Prismo Pro</h4>
                    <p id="reviewServiceType">Cuci Mobil + Salon</p>
                </div>
                
                <div class="form-group">
                    <label class="modal-section-title">Bagikan Pengalaman Anda!</label>
                    <div class="star-rating">
                        <div class="stars" id="starRating">
                            <span class="star" data-rating="1">☆</span>
                            <span class="star" data-rating="2">☆</span>
                            <span class="star" data-rating="3">☆</span>
                            <span class="star" data-rating="4">☆</span>
                            <span class="star" data-rating="5">☆</span>
                        </div>
                        <div class="rating-text" id="ratingText">Pilih rating (1-5 bintang)</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="review-input-container">
                        <div class="input-actions-left">
                            <button type="button" class="input-action-btn" id="uploadPhotoBtn" title="Tambah foto">
                                <i class="fas fa-camera"></i>
                            </button>
                            <input type="file" id="photoInput" accept="image/*" multiple hidden>
                        </div>
                        <textarea 
                            id="reviewComment" 
                            class="form-input review-textarea" 
                            placeholder="Bagaimana pengalaman Anda menggunakan layanan ini?"
                            rows="3"
                        ></textarea>
                        <button type="button" class="send-review-btn" id="sendReviewBtn" title="Kirim review">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <div class="char-count">
                        <span id="charCount">0</span>/500 karakter
                    </div>
                    
                    <!-- Preview uploaded images -->
                    <div class="uploaded-previews" id="uploadedPreviews" style="display: none;">
                        <div class="preview-label">Foto yang diunggah:</div>
                        <div class="preview-images" id="previewImages"></div>
                    </div>
                </div>
                
                <div class="current-review" id="currentReviewSection" style="display: none;">
                    <div class="review-display">
                        <h4>Review Saat Ini:</h4>
                        <div class="current-rating" id="currentRatingDisplay"></div>
                        <p class="current-comment" id="currentCommentDisplay"></p>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeReviewModal()">Batal</button>
            </div>
        </div>
    </div>

    <script>
        // Inject real data from server - NO MORE MOCK DATA
        window.currentBookingData = @json($currentBookingData);
        window.bookingHistory = @json($bookingHistory);
    </script>
    <script src="{{ asset('js/Rbooking.js') }}"></script>
    <script>
        // Listen untuk update avatar dari halaman profil
        if (typeof BroadcastChannel !== 'undefined') {
            const channel = new BroadcastChannel('profile_update');
            channel.onmessage = (event) => {
                if (event.data.type === 'avatar_updated') {
                    document.querySelectorAll('.user-icon-img, .avatar__image').forEach(img => {
                        img.src = event.data.avatar;
                    });
                    console.log('🔄 Avatar synced from other tab');
                }
            };
        }
    </script>
    <script src="{{ asset('js/browser-notification.js') }}"></script>
    <script src="{{ asset('js/notification-system.js') }}"></script>
</body>
</html>
