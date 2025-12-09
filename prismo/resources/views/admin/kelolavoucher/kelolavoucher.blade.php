<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-role" content="admin">
    <title>Kelola Voucher - Prismo</title>
    <link rel="stylesheet" href="{{ asset('css/admin-kelolavoucher.css') }}?v={{ time() }}">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
    <!-- MODAL LOGOUT -->
    <div class="modal" id="logoutModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Konfirmasi Logout</h3>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin keluar dari akun ini?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="cancelLogout">Batal</button>
                <button class="btn btn-warning" id="confirmLogout">Ya, Logout</button>
            </div>
        </div>
    </div>

    <!-- MODAL SUCCESS VOUCHER -->
    <div class="modal" id="voucherSuccessModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Berhasil</h3>
            </div>
            <div class="modal-body">
                <p>Voucher berhasil disimpan!</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="voucherOkBtn">OK</button>
            </div>
        </div>
    </div>

    <!-- MODAL DELETE CONFIRMATION -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Konfirmasi Hapus</h3>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus voucher ini?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="cancelDelete">Batal</button>
                <button class="btn btn-danger" id="confirmDelete">Hapus</button>
            </div>
        </div>
    </div>

    <!-- TOPBAR -->
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

    <!-- NAV ROW (TAB MENU) -->
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
                    <a href="{{ url('/admin/kelolavoucher/kelolavoucher') }}" class="nav-item active">
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

        <!-- HEADER KONTEN -->
        <section class="content-header">
            <h1 class="page-title">Kelola Voucher</h1>
            <p class="page-subtitle">Buat dan kelola voucher untuk customer</p>
        </section>

        <!-- FORM VOUCHER -->
        <section class="card voucher-form-section">
            <h2 class="section-title">Buat Voucher Baru</h2>
            
            <form id="voucherForm" class="voucher-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="namaVoucher">Nama Voucher <span class="required">*</span></label>
                        <input type="text" id="namaVoucher" name="namaVoucher" class="form-control" placeholder="Contoh: Voucher Akhir Tahun" required>
                    </div>

                    <div class="form-group">
                        <label for="kodeVoucher">Kode Voucher <span class="required">*</span></label>
                        <input type="text" id="kodeVoucher" name="kodeVoucher" class="form-control" placeholder="Contoh: PRISMO2024" required maxlength="20">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="masaBerlaku">Masa Berlaku <span class="required">*</span></label>
                        <input type="date" id="masaBerlaku" name="masaBerlaku" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="minTransaksi">Minimal Transaksi (Opsional)</label>
                        <div class="input-group">
                            <span class="input-addon">Rp</span>
                            <input type="number" id="minTransaksi" name="minTransaksi" class="form-control" placeholder="50000" min="1">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="persentasePotongan">Persentase Potongan (Opsional)</label>
                        <div class="input-group">
                            <input type="number" id="persentasePotongan" name="persentasePotongan" class="form-control" placeholder="20" min="1" max="100">
                            <span class="input-addon">%</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="maksPotongan">Maksimal Potongan <span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-addon">Rp</span>
                            <input type="number" id="maksPotongan" name="maksPotongan" class="form-control" placeholder="100000" min="1" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="hariTerdaftar">Pengguna (Opsional)</label>
                        <div class="input-group">
                            <select id="kondisiHari" name="kondisiHari" class="form-control" style="max-width: 120px; border-right: 2px solid #e8e8e8;">
                                <option value="none">Semua</option>
                                <option value="less_than">&lt;</option>
                                <option value="greater_than">&gt;</option>
                            </select>
                            <input type="number" id="hariTerdaftar" name="hariTerdaftar" class="form-control" placeholder="30" min="1" style="border-left: none;">
                            <span class="input-addon">hari terdaftar</span>
                        </div>
                        <small class="form-help">Pilih kondisi untuk membatasi berdasarkan lama pengguna terdaftar</small>
                    </div>

                    <div class="form-group">
                        <label for="warnaVoucher">Warna Voucher <span class="required">*</span></label>
                        <div class="color-picker-group">
                            <input type="color" id="warnaVoucher" name="warnaVoucher" class="color-input" value="#1c98f5" required>
                            <input type="text" id="warnaVoucherText" class="form-control color-text" value="#1c98f5" placeholder="#1c98f5" maxlength="7" required>
                        </div>
                        <small class="form-help">Pilih warna untuk tampilan voucher di aplikasi customer</small>
                    </div>
                </div>

                <div class="form-footer">
                    <button type="button" class="btn btn-secondary" id="btnReset">Reset</button>
                    <button type="submit" class="btn btn-primary">Simpan Voucher</button>
                </div>
            </form>
        </section>

        <!-- DAFTAR VOUCHER -->
        <section class="card voucher-list-section">
            <div class="section-header">
                <h2 class="section-title">Daftar Voucher</h2>
            </div>
            
            <div class="voucher-table-wrapper">
                <table class="voucher-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Voucher</th>
                            <th>Warna</th>
                            <th>Potongan</th>
                            <th>Min. Transaksi</th>
                            <th>Masa Berlaku</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="voucherTableBody">
                        <!-- Data voucher akan dimuat di sini -->
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Notification Panel -->
    <div id="notifPanel" class="notification-panel">
        <div class="notification-panel-header">
            <button id="markAllReadBtn" class="mark-all-read-btn">Tandai Semua Dibaca</button>
        </div>
        <div id="notificationList" class="notification-list">
            <div class="notification-loading">Memuat notifikasi...</div>
        </div>
    </div>
    <div id="notifOverlay" class="notification-overlay"></div>

    <script>
        // Inject real voucher data from server
        window.vouchersData = @json($vouchers);
    </script>
    <script src="{{ asset('js/browser-notification.js') }}"></script>
    <script src="{{ asset('js/notification-system.js') }}"></script>
    <script src="{{ asset('js/kelolavoucher.js') }}"></script>
</body>
</html>

