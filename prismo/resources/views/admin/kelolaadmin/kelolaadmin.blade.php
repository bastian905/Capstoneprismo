<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Kelola Admin - Prismo</title>

  <!-- Sambungkan CSS -->
  <link rel="stylesheet" href="{{ asset('css/kelolaadmin.css') }}">
</head>
<body>
  <div class="container">
    <!-- HEADER (pakai logo lokal dari history) -->
    <header class="header">
      <div class="header__content">
        <div class="header__left">
          <div class="header__brand">
            <!-- path lokal dari upload (akan diubah menjadi URL oleh pipeline) -->
            <img src="{{ asset('images/logo.png') }}" alt="PRISMO" class="logo" />
          </div>
        </div>

        <div class="user-menu">
          <button class="btn btn--back" onclick="history.back()" title="Kembali">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="margin-right:8px">
              <path d="M10.707 2.293a1 1 0 010 1.414L6.414 8l4.293 4.293a1 1 0 01-1.414 1.414l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 0z"/>
            </svg>
            Kembali
          </button>
        </div>
      </div>
    </header>

    <main class="wrap">
      <section class="panel">
        <div class="panel-header">
          <h1>Kelola Admin</h1>
          <button id="btnAddAdmin" class="btn add-btn">Tambah Admin</button>
        </div>

        <div class="panel-body">
          <table class="admin-table" aria-describedby="table-admins">
            <thead>
              <tr>
                <th style="width:80px">No</th>
                <th>Nama Admin</th>
                <th>Email</th>
                <th style="width:140px; text-align:center">Aksi</th>
              </tr>
            </thead>
            <tbody id="adminList">
              <!-- diisi oleh JS -->
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>

  <!-- Modal: Tambah Admin -->
<div class="modal-overlay" id="modalAdd">
  <div class="modal-card">
    <div class="modal-header">
      <h2>Tambahkan Admin</h2>
    </div>

    <div class="modal-body">
      <label class="field-label">Nama lengkap</label>
      <input id="inputName" class="input-field" type="text" placeholder="Nama lengkap" />

      <label class="field-label">Email</label>
      <input id="inputEmail" class="input-field" type="email" placeholder="email@domain.com" />

      <label class="field-label">Password</label>
      <input id="inputPassword" class="input-field" type="password" placeholder="" />

      <label class="field-label">Role</label>
      <input id="inputRole" class="input-field" type="text" value="Admin" readonly />
    </div>

    <div class="modal-actions">
      <button class="btn btn-danger" id="btnCancelAdd">Cancel</button>
      <button class="btn btn-primary" id="btnSaveAdmin">Selesai</button>
    </div>
  </div>
</div>


  <!-- Modal: Konfirmasi Hapus -->
  <div class="modal-overlay" id="modalDelete">
    <div class="modal-card small">
      <div class="modal-icon">!</div>
      <h3>Yakin Ingin Hapus?</h3>
      <p>Admin ini akan dihapus dari data admin.</p>
      <div class="modal-actions">
        <button class="btn btn-primary" id="confirmDeleteYes">Ya, hapus!</button>
        <button class="btn btn-danger" id="confirmDeleteNo">Cancel</button>
      </div>
    </div>
  </div>

  <!-- Sambungkan JS -->
  <script>
    // Inject real admin data from server
    window.adminsData = @json($admins);
  </script>
  <script src="{{ asset('js/kelolaadmin.js') }}"></script>
</body>
</html>
