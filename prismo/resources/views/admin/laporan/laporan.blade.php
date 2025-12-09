<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Laporan - Prismo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-role" content="admin">
    <link rel="stylesheet" href="{{ asset('css/laporan.css') }}" />
</head>
<body>
    <!-- ====== MODAL KONFIRMASI ====== -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Konfirmasi Logout</h3>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin logout dari sistem?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="cancelLogout">Batal</button>
                <button class="btn btn-danger" id="confirmLogout">Ya, Logout</button>
            </div>
        </div>
    </div>

    <!-- ====== TOPBAR ====== -->
    <header class="topbar">
        <div class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="Prismo Logo">
        </div>

        <div class="user-area">
            <div class="user-info">
                <span class="user-name">{{ auth()->user()->name }}</span>
                <span class="user-role">Admin</span>
            </div>
            <!-- Notification Bell -->
            <button id="notifBtn" class="notification-btn" aria-label="Notifications" style="margin-right: 16px; background: none; border: none; cursor: pointer; position: relative;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                <span id="notifBadge" class="notification-badge" style="display: none; position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 11px; display: flex; align-items: center; justify-content: center;">0</span>
            </button>
            <div class="user-dropdown">
                <div class="user-avatar">
                    @if(auth()->user()->avatar && str_starts_with(auth()->user()->avatar, 'http'))
                        <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" onerror="this.src='{{ asset('images/profile.png') }}'">
                    @elseif(auth()->user()->avatar)
                        <img src="{{ asset(auth()->user()->avatar) }}?v={{ auth()->user()->updated_at->timestamp }}" alt="{{ auth()->user()->name }}" onerror="this.src='{{ asset('images/profile.png') }}'">
                    @else
                        <img src="{{ asset('images/profile.png') }}" alt="{{ auth()->user()->name }}">
                    @endif
                </div>
                <div class="dropdown-menu">
                    <a href="{{ url('/admin/kelolaadmin/kelolaadmin') }}" class="dropdown-item new-admin" id="newAdminBtn">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="margin-right: 8px;">
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                        Kelola Admin
                    </a>
                    <a href="#" class="dropdown-item logout" id="logoutBtn">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="margin-right: 8px;">
                            <path d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0v2z"/>
                            <path d="M10.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L9.293 7.5H2.5a.5.5 0 0 0 0 1h6.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- ====== ISI HALAMAN ====== -->
    <main class="page">
        <section class="nav-row">
            <div class="card">
                <nav class="nav" id="mainNav">
                    <a href="{{ url('/admin/dashboard') }}" class="nav-item">
                        <span class="icon"><img src="{{ asset('images/dashboard.png') }}" alt="Dashboard" width="16" height="16"></span>
                        Dashboard
                    </a>
                    <a href="{{ url('/admin/kelolamitra/kelolamitra') }}" class="nav-item">
                        <span class="icon"><img src="{{ asset('images/kelolamitra.png') }}" alt="Kelola Mitra" width="16" height="16"></span>
                        Kelola Mitra
                    </a>
                    <a href="{{ url('/admin/kelolacustomer/kelolacustomer') }}" class="nav-item">
                        <span class="icon"><img src="{{ asset('images/kelolacustomer.png') }}" alt="Kelola Customer" width="16" height="16"></span>
                        Kelola Customer
                    </a>
                    <a href="{{ url('/admin/kelolavoucher/kelolavoucher') }}" class="nav-item">
                        <span class="icon"><img src="{{ asset('images/voucher.png') }}" alt="Kelola Voucher" width="16" height="16"></span>
                        Kelola Voucher
                    </a>
                    <a href="{{ url('/admin/kelolabooking/kelolabooking') }}" class="nav-item">
                        <span class="icon"><img src="{{ asset('images/kelolabooking.png') }}" alt="Kelola Booking" width="16" height="16"></span>
                        Kelola Booking
                    </a>
                    <a href="{{ url('/admin/laporan/laporan') }}" class="nav-item active">
                        <span class="icon"><img src="{{ asset('images/laporan.png') }}" alt="Laporan" width="16" height="16"></span>
                        Laporan
                    </a>
                </nav>
            </div>
        </section>

        <!-- LAPORAN PENDAPATAN -->
        <section class="card report-card">
            <div class="report-header">
                <h1 class="report-title">Laporan Pendapatan</h1>
                <div class="export-dropdown">
                    <button class="btn btn-primary export-btn" id="exportBtn">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="margin-right: 6px;">
                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                            <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                        </svg>
                        Export
                    </button>
                    <div class="export-menu" id="exportMenu">
                        <a href="#" class="export-item" data-type="hari-ini">
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" style="margin-right: 8px;">
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2z"/>
                                <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5V4zM11 7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm-2 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z"/>
                            </svg>
                            Pendapatan Hari Ini
                        </a>
                        <a href="#" class="export-item" data-type="minggu-ini">
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" style="margin-right: 8px;">
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2z"/>
                                <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5V4zM11 7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm-2 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z"/>
                            </svg>
                            Pendapatan Minggu Ini
                        </a>
                        <a href="#" class="export-item" data-type="bulan-ini">
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" style="margin-right: 8px;">
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2z"/>
                                <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5V4zM11 7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm-2 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z"/>
                            </svg>
                            Pendapatan Bulan Ini
                        </a>
                    </div>
                </div>
            </div>

            <!-- SUMMARY -->
            <div class="summary-row">
                <div class="summary-card summary-blue">
                    <div class="summary-value">{{ 'Rp ' . number_format($totalRevenue, 0, ',', '.') }}</div>
                    <div class="summary-label">Total Pendapatan</div>
                </div>

                <div class="summary-card summary-green">
                    <div class="summary-value">{{ number_format($totalBookings) }}</div>
                    <div class="summary-label">Total Booking Bulan Ini</div>
                </div>

                <div class="summary-card summary-purple">
                    <div class="summary-value">{{ number_format($totalMitra) }}</div>
                    <div class="summary-label">Total Mitra Aktif</div>
                </div>
            </div>
        </section>

        <!-- STATISTIK & TOP MITRA -->
        <section class="bottom-row">
            <!-- Statistik Booking -->
            <div class="card stats-card">
                <div class="card-title-lg">Statistik Booking</div>

                <div class="stats-list">
                    <div class="stats-item">
                        <span class="stats-label">Total Booking Bulan Ini</span>
                        <span class="stats-value">{{ number_format($totalBookings) }}</span>
                    </div>
                    <div class="stats-item">
                        <span class="stats-label">Total Mitra Aktif</span>
                        <span class="stats-value stats-positive">{{ number_format($totalMitra) }}</span>
                    </div>
                    <div class="stats-item">
                        <span class="stats-label">Total Customer</span>
                        <span class="stats-value stats-positive">{{ number_format($totalCustomers) }}</span>
                    </div>
                    <div class="stats-item">
                        <span class="stats-label">Total Pendapatan</span>
                        <span class="stats-value stats-positive">{{ 'Rp ' . number_format($totalRevenue, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Top Performing Mitra -->
            <div class="card top-mitra-card">
                <div class="card-title-lg">Top Performing Mitra</div>

                <div class="mitra-list">
                    @forelse($topMitras as $mitra)
                    <div class="mitra-item">
                        <div class="mitra-main">
                            <div class="mitra-name">{{ $mitra->business_name }}</div>
                            <div class="mitra-sub">{{ $mitra->bookings_count }} booking</div>
                        </div>
                        <div class="mitra-meta">
                            <div class="mitra-income">Rp {{ number_format($mitra->total_revenue ?? 0, 0, ',', '.') }}</div>
                            <div class="mitra-rating">
                                ★ <span>{{ number_format($mitra->rating ?? 0, 1) }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="mitra-item">
                        <div class="mitra-main">
                            <div class="mitra-name">Belum ada data mitra</div>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

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
        // Inject real report data from server
        window.reportData = {
            totalRevenue: @json($totalRevenue),
            totalBookings: @json($totalBookings),
            totalMitra: @json($totalMitra),
            totalCustomers: @json($totalCustomers),
            topMitras: @json($topMitras),
            monthlyRevenue: @json($monthlyRevenue),
            recentWithdrawals: @json($recentWithdrawals)
        };
    </script>
    <script src="{{ asset('js/browser-notification.js') }}"></script>
    <script src="{{ asset('js/notification-system.js') }}"></script>
    <script src="{{ asset('js/admin-laporan.js') }}?v={{ time() }}"></script>
</body>
</html>


