<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-role" content="mitra">
    <title>PRISMO - Saldo & Laporan</title>
    <link rel="stylesheet" href="{{ asset('css/saldo.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preload" href="/images/logo.png" as="image">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="header__content">
                <div class="header__left">
                    <div class="header__brand">
                        <img src="{{ asset('images/logo.png') }}" alt="PRISMO" class="logo" width="120" height="40">
                    </div>

                    <nav class="nav nav--main" aria-label="Navigasi utama">
                        <a href="{{ url('/dashboard-mitra') }}" class="nav__item" data-page="dashboard">
                            Dashboard
                        </a>
                        <a href="#" class="nav__item nav__item--active" data-page="saldo">
                            Saldo
                        </a>
                        <a href="{{ url('/mitra/antrian/antrian') }}" class="nav__item" data-page="antrian">
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
            <!-- Section Saldo & Penarikan -->
            <section class="saldo-section">
                <div class="saldo-card">
                    <h1 class="saldo-title">Saldo & Penarikan</h1>
                    <div class="saldo-amount">
                        <span class="saldo-number">Rp{{ number_format($availableBalance, 0, ',', '.') }}</span>
                        <span class="saldo-label">Saldo Tersedia</span>
                    </div>
                    
                    <div class="pendapatan-hari-ini">
                        <h3>Pendapatan Hari ini</h3>
                        <div class="pendapatan-details">
                            <div class="pendapatan-item">
                                <span>Komisi Platform</span>
                                <span class="komisi-platform">Rp0</span>
                            </div>
                            <div class="pendapatan-item">
                                <span>Saldo Bersih</span>
                                <span class="saldo-bersih">Rp{{ number_format($todayEarnings, 0, ',', '.') }}</span>
                            </div>
                            <div class="pendapatan-item total-pendapatan">
                                <span>Total Pendapatan</span>
                                <span class="total-pendapatan-amount">Rp{{ number_format($todayEarnings, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="withdrawal-info">
                        <div class="info-box" id="withdrawalInfoBox">
                            <div class="info-box__icon">ℹ️</div>
                            <div class="info-box__content">
                                <strong>Informasi Penarikan Saldo:</strong>
                                <ul>
                                    <li>Jam operasional: <strong>08:00 - 23:00</strong></li>
                                    <li>Maksimal <strong>1x penarikan per hari</strong></li>
                                    <li>Reset penarikan: <strong>Setiap hari jam 00:00</strong></li>
                                    <li>Minimal penarikan: <strong>Rp50.000</strong></li>
                                    <li>Jika ada penarikan yang <strong>masih diproses</strong>, tidak bisa menarik saldo lagi</li>
                                    <li>Waktu proses: <strong>1-3 hari kerja</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="saldo-actions">
                        <a href="{{ url('/mitra/saldo/history') }}" class="btn btn--secondary">
                            Laporan Keuangan
                        </a>
                        <button class="btn btn--primary btn--withdraw" id="tarikSaldoBtn">
                            Tarik Saldo
                        </button>
                    </div>
                </div>

                <!-- Riwayat Penarikan -->
                <div class="riwayat-container">
        <div class="riwayat-header">
            <h2>Riwayat Penarikan</h2>
        </div>
        <div class="riwayat-section">
            <div class="riwayat-cards">
                <!-- Cards akan diisi oleh JavaScript -->
            </div>
        </div>
    </div>
            </section>
        </main>
    </div>

    <!-- Modal Tarik Saldo -->
    <template id="withdrawModalTemplate">
        <div class="modal-overlay" role="dialog" aria-labelledby="withdrawModalTitle" aria-modal="true">
            <div class="modal modal--withdraw">
                <div class="modal__header">
                    <h3 id="withdrawModalTitle">Tarik Saldo</h3>
                    <button class="modal__close" aria-label="Tutup modal">✕</button>
                </div>
                <div class="modal__content">
                    <div class="saldo-info">
                        <div class="saldo-available">
                            <span class="saldo-amount">Rp450.000</span>
                            <span class="saldo-label">Saldo Tersedia</span>
                        </div>
                    </div>
                    
                    <div class="withdraw-form">
                        <label for="withdrawAmount" class="form-label">Jumlah Penarikan</label>
                        <div class="amount-input-group">
                            <span class="currency-symbol">Rp</span>
                            <input type="text" id="withdrawAmount" class="amount-input" placeholder="50000">
                        </div>
                        <p class="amount-info">Minimum penarikan Rp50.000</p>
                    </div>
                </div>
                <div class="modal__actions">
                    <button type="button" class="btn btn--secondary" data-action="cancel">Batal</button>
                    <button type="button" class="btn btn--primary" data-action="submit" disabled>Ajukan Penarikan</button>
                </div>
            </div>
        </div>
    </template>

    <!-- Modal Konfirmasi Penarikan -->
    <template id="confirmWithdrawModalTemplate">
        <div class="modal-overlay" role="dialog" aria-labelledby="confirmWithdrawModalTitle" aria-modal="true">
            <div class="modal modal--success">
                <div class="modal__content modal__content--centered">
                    <div class="success-icon">✓</div>
                    <h3 id="confirmWithdrawModalTitle">Penarikan Diproses</h3>
                    <p class="modal__message">
                        Saldo sebesar <strong>Rp447.500</strong> akan di transfer ke rekening bank kamu
                    </p>
                    <button type="button" class="btn btn--success" data-action="close">Oke, Mengerti</button>
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
                <a href="#" class="mobile-nav__item mobile-nav__item--active" data-page="saldo">
                    <div class="mobile-nav__item-content">
                        Saldo
                    </div>
                </a>
                <a href="{{ url('/mitra/antrian/antrian') }}" class="mobile-nav__item" data-page="antrian">
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
        window.availableBalance = @json($availableBalance);
        window.totalEarnings = @json($totalEarnings);
        window.totalWithdrawn = @json($totalWithdrawn);
        window.pendingWithdrawals = @json($pendingWithdrawals);
        window.todayEarnings = @json($todayEarnings);
        window.hasWithdrawnToday = @json($hasWithdrawnToday);
        window.hasProcessingWithdrawal = @json($hasProcessingWithdrawal);
    </script>
    <script src="{{ asset('js/browser-notification.js') }}"></script>
    <script src="{{ asset('js/notification-system.js') }}"></script>
    <script src="{{ asset('js/mitra-badge-manager.js') }}"></script>
    <script src="{{ asset('js/saldo.js') }}"></script>
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
