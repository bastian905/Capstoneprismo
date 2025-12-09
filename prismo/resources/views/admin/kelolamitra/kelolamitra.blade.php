<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Kelola Mitra - Prismo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="user-role" content="admin">
  <link rel="stylesheet" href="{{ asset('css/kelolamitra.css') }}" />
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

  <div id="banModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Konfirmasi Nonaktifkan Mitra</h3>
      </div>
      <div class="modal-body">
        <p>Apakah Anda yakin ingin menonaktifkan mitra <strong id="banMitraName"></strong>?</p>
        <small style="color: #666; margin-top: 8px; display: block;">Mitra yang dinonaktifkan tidak dapat login ke sistem.</small>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" id="cancelBan">Batal</button>
        <button class="btn btn-danger" id="confirmBan">Ya, Nonaktifkan</button>
      </div>
    </div>
  </div>

  <div id="unbanModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Konfirmasi Aktifkan Mitra</h3>
      </div>
      <div class="modal-body">
        <p>Apakah Anda yakin ingin mengaktifkan kembali mitra <strong id="unbanMitraName"></strong>?</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" id="cancelUnban">Batal</button>
        <button class="btn btn-success" id="confirmUnban">Ya, Aktifkan</button>
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
          <a href="{{ url('/admin/kelolaadmin/kelolaadmin') }}" class="dropdown-item" id="newAdminBtn">
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
    <!-- NAVBAR DALAM CARD - SAMA PERSIS DASHBOARD -->
    <section class="nav-row">
      <div class="card">
        <nav class="nav" id="mainNav">
          <a href="{{ url('/admin/dashboard') }}" class="nav-item">
            <span class="icon"><img src="{{ asset('images/dashboard.png') }}" alt="Dashboard" width="16" height="16"></span>
            Dashboard
          </a>
          <a href="#" class="nav-item active">
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

    <!-- Header + search -->
    <section class="mitra-section">
      <!-- Kartu Persetujuan Mitra -->
      <div class="card approval-card">
        <div class="approval-bar">
          <span>Persetujuan Mitra</span>
          <span class="approval-count" id="approvalCount">(5)</span>
        </div>

        <div class="approval-body">
          <div class="approval-container" id="approvalContainer">
            <!-- Data akan diisi oleh JavaScript -->
          </div>
        </div>
      </div>

      <!-- Tabel Mitra -->
      <div class="card table-card">
        <div class="mitra-header">
          <h1 class="mitra-title">Kelola Data Mitra</h1>

          <div class="search-wrapper">
            <input
              type="text"
              id="searchMitra"
              class="search-input"
              placeholder="Cari Mitra..."
            />
          </div>
        </div>
        
        <div class="table-wrapper">
          <table class="mitra-table" id="mitraTable">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Tempat Cuci</th>
                <th>Pemilik</th>
                <th>Kontak</th>
                <th>Lokasi</th>
                <th>Rating</th>
                <th>Saldo</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="mitraTableBody">
              <!-- Data akan diisi oleh JavaScript -->
            </tbody>
          </table>
          <div id="paginationControls" class="pagination"></div>
        </div>
      </div>
    </section>
  </main>

  <!-- Notification Toast -->
  <div id="notification" class="notification">
    <div class="notification-content">
      <span class="notification-message" id="notificationMessage"></span>
      <button class="notification-close">&times;</button>
    </div>
    </div>

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
        // Inject real mitra data from server
        window.mitrasData = @json($mitras);
        console.log('=== KELOLA MITRA DEBUG ===');
        console.log('mitrasData injected:', window.mitrasData);
        console.log('mitrasData type:', typeof window.mitrasData);
        console.log('mitrasData is array:', Array.isArray(window.mitrasData));
        console.log('mitrasData length:', window.mitrasData ? window.mitrasData.length : 'null');
    </script>
    <script src="{{ asset('js/browser-notification.js') }}"></script>
    <script src="{{ asset('js/notification-system.js') }}"></script>
    <script src="{{ asset('js/kelolamitra.js') }}"></script>
</body>
</html>