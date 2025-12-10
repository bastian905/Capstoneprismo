<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Admin - Prismo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-role" content="admin">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}?v={{ time() }}">
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
                    <a href="{{ url('/admin/dashboard') }}" class="nav-item active">
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
                    <a href="{{ url('/admin/laporan/laporan') }}" class="nav-item">
                        <span class="icon"><img src="{{ asset('images/laporan.png') }}" alt="Laporan" width="16" height="16"></span>
                        Laporan
                    </a>
                </nav>
            </div>
        </section>

        <!-- Kartu Statistik -->
        <section class="stats-row">
            <div class="card">
                <div class="card-header">
                    <div class="badge-icon badge-blue">
                        <img src="{{ asset('images/totalmitra.png') }}" alt="Mitra" width="24" height="24">
                    </div>
                    <div class="card-content">
                        <span class="card-title">Total Mitra</span>
                        <div class="card-value">{{ $totalMitra }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="badge-icon badge-green">
                        <img src="{{ asset('images/totalcustomer.png') }}" alt="Customer" width="24" height="24">
                    </div>
                    <div class="card-content">
                        <span class="card-title">Total Customers</span>
                        <div class="card-value">{{ $totalCustomer }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="badge-icon badge-yellow">
                        <img src="{{ asset('images/totaltransaksi.png') }}" alt="Transaksi" width="24" height="24">
                    </div>
                    <div class="card-content">
                        <span class="card-title">Total Transaksi</span>
                        <div class="card-value">{{ $totalTransaksi }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="badge-icon badge-purple">
                        <img src="{{ asset('images/totalpendapatan.png') }}" alt="Pendapatan" width="24" height="24">
                    </div>
                    <div class="card-content">
                        <span class="card-title">Total Pendapatan</span>
                        <div class="card-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                        
                        <div style="margin-top: 12px; display: flex; gap: 8px; align-items: center;">
                            <select id="periodSelect" style="padding: 6px 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                                <option value="daily">Harian</option>
                                <option value="weekly">Mingguan</option>
                                <option value="monthly" selected>Bulanan</option>
                                <option value="yearly">Tahunan</option>
                            </select>
                            <button id="exportEarningsBtn" style="padding: 6px 12px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; display: flex; align-items: center; gap: 4px;">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                                </svg>
                                Export Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Permintaan Mitra & Penarikan -->
        <section class="requests-row">
            <div class="card">
                <div class="request-main">Permintaan Mitra Baru Mendaftar</div>
                <div class="request-number">{{ $mitraPending }}</div>
                <div class="request-desc">Menunggu Persetujuan</div>
                <div class="card-footer">
                    <a href="{{ url('/admin/kelolamitra/kelolamitra') }}" class="btn btn-primary">Lihat Detail</a>
                </div>
            </div>

            <div class="card">
                <div class="request-main">Permintaan Penarikan Saldo</div>
                <div class="request-number">{{ $pendingWithdrawals }}</div>
                <div class="request-desc">Menunggu Persetujuan</div>
                <div class="card-footer">
                    <a href="{{ url('/admin/dashboard/penarikan') }}" class="btn btn-warning">Lihat Detail</a>
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

    <script src="{{ asset('js/browser-notification.js') }}"></script>
    <script src="{{ asset('js/notification-system.js') }}"></script>
    <script src="{{ asset('js/prevent-back.js') }}"></script>
    <script src="{{ asset('js/admin-dashboard.js') }}?v={{ time() }}"></script>
</body>
</html>


