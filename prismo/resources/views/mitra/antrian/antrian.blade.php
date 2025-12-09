<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-role" content="mitra">
    <title>PRISMO - Antrian</title>
    <link rel="stylesheet" href="{{ asset('css/antrian.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preload" href="/images/logo.png" as="image">
</head>

<body>
    <div class="container">
        <header class="header">
            <div class="header__content">
                <div class="header__left">
                    <div class="header__brand">
                        <img src="{{ asset('images/logo.png') }}" alt="PRISMO" class="logo" width="200" height="200">
                    </div>

                    <nav class="nav nav--main" aria-label="Navigasi utama">
                        <a href="{{ url('/dashboard-mitra') }}" class="nav__item" data-page="dashboard">
                            Dashboard
                        </a>
                        <a href="{{ url('/mitra/saldo/saldo') }}" class="nav__item" data-page="saldo">
                            Saldo
                        </a>
                        <a href="#" class="nav__item nav__item--active" data-page="antrian">
                            Antrian
                        </a>
                        <a href="{{ url('/mitra/review/review') }}" class="nav__item" data-page="review">
                            Review
                        </a>
                    </nav>
                </div>

                <button class="notification-btn" id="notifBtn" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">0</span>
                </button>

                <div class="user-menu">
                    <button class="user-menu__toggle" aria-expanded="false" aria-label="Menu pengguna">
                        <div class="avatar">
                            <img src="{{ asset(auth()->user()->avatar ?? 'images/profile.png') }}?v={{ auth()->user()->updated_at->timestamp }}"
                                alt="Avatar Mitra PRISMO" class="avatar__image" width="40" height="40">
                        </div>
                        <div class="user-info">
                            <span class="user-info__name">{{ auth()->user()->name }}</span>
                            <span class="user-info__role">Mitra</span>
                        </div>
                    </button>
                </div>

                <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu mobile" aria-expanded="false">
                    <span class="menu-toggle__bar"></span>
                    <span class="menu-toggle__bar"></span>
                    <span class="menu-toggle__bar"></span>
                </button>
            </div>
        </header>

        <main class="main" id="mainContent">
            <div class="tabs" role="tablist" aria-label="Jenis antrian">
                <button class="tabs__button tabs__button--active" role="tab" aria-selected="true"
                    aria-controls="today-panel" data-tab="today">
                    Antrian Hari Ini
                </button>
                <button class="tabs__button" role="tab" aria-selected="false" aria-controls="other-panel"
                    data-tab="other">
                    Antrian Lainnya
                </button>
            </div>

            <div class="tab-content">
                <!-- Tab Antrian Hari Ini -->
                <div id="today-panel" class="tab-pane tab-pane--active" role="tabpanel" aria-labelledby="today-tab">
                    <div class="kanban-board-wrapper">
                        <div class="kanban-board">
                            <div class="kanban-column kanban-column--waiting">
                                <div class="kanban-column__header">
                                    <h2 class="kanban-column__title">
                                        <span class="kanban-column__icon">🕐</span>
                                        Menunggu
                                    </h2>
                                    <span class="kanban-column__count" id="waiting-count">0</span>
                                </div>
                                <div class="kanban-column__content" id="waiting-bookings">
                                    <!-- Booking items akan diisi oleh JavaScript -->
                                </div>
                            </div>

                            <div class="kanban-column kanban-column--process">
                                <div class="kanban-column__header">
                                    <h2 class="kanban-column__title">
                                        <span class="kanban-column__icon">⚙️</span>
                                        Proses
                                    </h2>
                                    <span class="kanban-column__count" id="process-count">0</span>
                                </div>
                                <div class="kanban-column__content" id="process-bookings">
                                    <!-- Booking items akan diisi oleh JavaScript -->
                                </div>
                            </div>

                            <div class="kanban-column kanban-column--history">
                                <div class="kanban-column__header">
                                    <h2 class="kanban-column__title">
                                        <span class="kanban-column__icon">📋</span>
                                        Riwayat
                                    </h2>
                                    <span class="kanban-column__count" id="history-count">0</span>
                                </div>
                                <div class="kanban-column__content" id="history-bookings">
                                    <!-- Booking items akan diisi oleh JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Antrian Lainnya -->
                <div id="other-panel" class="tab-pane" role="tabpanel" aria-labelledby="other-tab" hidden>
                    <div class="filter-bar">
                        <label for="dateFilter" class="filter-bar__label">Pilih Tanggal:</label>
                        <div class="date-filter-wrapper">
                            <div class="custom-date-picker">
    <button type="button" class="custom-date-picker__trigger" id="datePickerTrigger">
        <span id="selectedDateText">Pilih tanggal</span>
        <span class="custom-date-picker__icon">
            <img src="{{ asset('images/tanggal.png') }}" alt="Pilih tanggal" class="date-icon" width="16" height="16">
        </span>
    </button>
    <div class="custom-date-picker__dropdown" id="datePickerDropdown">
        <div class="custom-date-picker__header">
            <button type="button" class="custom-date-picker__nav" id="prevMonth">←</button>
            <h4 id="currentMonthYear">November 2025</h4>
            <button type="button" class="custom-date-picker__nav" id="nextMonth">→</button>
        </div>
        <div class="custom-date-picker__calendar">
            <div class="custom-date-picker__weekdays">
                <div>Sen</div>
                <div>Sel</div>
                <div>Rab</div>
                <div>Kam</div>
                <div>Jum</div>
                <div>Sab</div>
                <div>Min</div>
            </div>
            <div class="custom-date-picker__days" id="calendarDays">
                <!-- Days will be populated by JavaScript -->
            </div>
        </div>
        <div class="custom-date-picker__actions">
            <button type="button" class="btn btn--secondary" id="clearDate">Hapus</button>
            <button type="button" class="btn btn--primary" id="applyDate">Terapkan</button>
        </div>
    </div>
</div>
                        </div>
                        <button class="btn btn--primary" id="searchButton">
                            Filter
                        </button>
                    </div>

                    <div class="other-bookings-grid" id="other-bookings" aria-live="polite">
                        <!-- Booking items akan diisi oleh JavaScript -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Templates -->
    <template id="cancelModalTemplate">
        <div class="modal-overlay" role="dialog" aria-labelledby="cancelModalTitle" aria-modal="true">
            <div class="modal">
                <div class="modal__header">
                    <h3 id="cancelModalTitle" class="modal__title">Konfirmasi Pembatalan</h3>
                </div>
                <div class="modal__content">
                    <p>Apakah Anda yakin ingin membatalkan booking berikut?</p>
                    <div class="booking-summary" id="cancelBookingSummary">
                        <!-- Diisi oleh JavaScript -->
                    </div>
                    <p class="warning-text">⚠️ Tindakan ini tidak dapat dibatalkan!</p>
                </div>
                <div class="modal__actions">
                    <button type="button" class="btn btn--secondary" data-action="cancel">Batal</button>
                    <button type="button" class="btn btn--danger" data-action="confirm">Ya, Batalkan</button>
                </div>
            </div>
        </div>
    </template>

    <template id="successModalTemplate">
        <div class="modal-overlay" role="dialog" aria-labelledby="successModalTitle" aria-modal="true">
            <div class="modal modal--success">
                <div class="modal__content modal__content--centered">
                    <div class="success-icon" aria-hidden="true">✓</div>
                    <h3 id="successModalTitle" class="modal__title">Berhasil!</h3>
                    <p class="modal__message" id="successMessage"></p>
                    <button type="button" class="btn btn--success" data-action="close">Tutup</button>
                </div>
            </div>
        </div>
    </template>

    <template id="todayAlertModalTemplate">
        <div class="modal-overlay" role="dialog" aria-labelledby="todayAlertModalTitle" aria-modal="true">
            <div class="modal modal--success">
                <div class="modal__content modal__content--centered">
                    <div class="info-icon">ⓘ</div>
                    <h3 id="todayAlertModalTitle" class="modal__title">Hari Ini</h3>
                    <p class="modal__message">Untuk antrian hari ini, silakan gunakan tab "Antrian Hari Ini"</p>
                    <div class="modal__actions modal__actions--centered">
                        <button type="button" class="btn btn--primary" data-action="switch-to-today">
                            Buka Antrian Hari Ini
                        </button>
                        <button type="button" class="btn btn--secondary" data-action="close-alert">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Mobile Menu Template -->
    <template id="mobileMenuTemplate">
        <div class="mobile-menu" id="mobileMenu">
            <div class="mobile-menu__header">
                <img src="{{ asset('images/logo.png') }}" alt="PRISMO" class="logo" width="120" height="40">
                <button class="mobile-menu__close" id="mobileMenuClose" aria-label="Tutup menu">
                    ✕
                </button>
            </div>

            <div class="mobile-user-profile" id="mobileUserProfile">
                <div class="avatar">
                    <img src="{{ asset(auth()->user()->avatar ?? 'images/profile.png') }}?v={{ auth()->user()->updated_at->timestamp }}"
                        alt="Avatar Mitra PRISMO" class="avatar__image" width="50" height="50">
                </div>
                <div class="mobile-user-profile__info">
                    <span class="user-info__name">{{ auth()->user()->name }}</span>
                    <span class="user-info__role">Mitra</span>
                </div>
            </div>

            <nav class="mobile-nav" aria-label="Navigasi mobile">
                <a href="{{ url('/dashboard-mitra') }}" class="mobile-nav__item" data-page="dashboard">
                    <div class="mobile-nav__item-content">
                        Dashboard
                    </div>
                </a>
                <a href="{{ url('/mitra/saldo/saldo') }}" class="mobile-nav__item" data-page="saldo">
                    <div class="mobile-nav__item-content">
                        Saldo
                    </div>
                </a>
                <a href="#" class="mobile-nav__item mobile-nav__item--active" data-page="antrian">
                    <div class="mobile-nav__item-content">
                        Antrian
                    </div>
                </a>
                <a href="{{ url('/mitra/review/review') }}" class="mobile-nav__item" data-page="review">
                    <div class="mobile-nav__item-content">
                        Review
                    </div>
                </a>
            </nav>
        </div>
    </template>

    <!-- Notification Panel -->
    <div id="notifPanel" class="notification-panel">
        <div class="notification-panel-header">
            <h3>Notifikasi</h3>
            <button id="markAllReadBtn" class="mark-all-read-btn">Tandai Semua Dibaca</button>
        </div>
        <div id="notificationList" class="notification-list">
            <div class="notification-loading">Memuat notifikasi...</div>
        </div>
    </div>
    <div id="notifOverlay" class="notification-overlay"></div>

    <script>
        // Inject real data from server - NO MORE MOCK DATA
        window.antrianData = @json($antrian);
    </script>
    <script src="{{ asset('js/browser-notification.js') }}"></script>
    <script src="{{ asset('js/notification-system.js') }}"></script>
    <script src="{{ asset('js/mitra-badge-manager.js') }}"></script>
    <script src="{{ asset('js/antrian.js') }}"></script>
    <script>
        // Listen untuk update avatar dari halaman profil
        if (typeof BroadcastChannel !== 'undefined') {
            const channel = new BroadcastChannel('profile_update');
            channel.onmessage = (event) => {
                if (event.data.type === 'avatar_updated') {
                    document.querySelectorAll('.avatar__image').forEach(img => {
                        img.src = event.data.avatar;
                    });
                    console.log('🔄 Avatar synced from other tab');
                }
            };
        }
    </script>
</body>

</html>
