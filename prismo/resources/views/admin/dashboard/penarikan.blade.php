<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Persetujuan Penarikan Saldo - Prismo</title>
  <link rel="stylesheet" href="{{ asset('css/penarikan.css') }}" />
</head>
<body>
  <div class="container">
    <!-- HEADER -->
    <header class="header">
      <div class="header__content">
        <div class="header__left">
          <div class="header__brand">
            <img src="{{ asset('images/logo.png') }}" alt="PRISMO" class="logo" />
          </div>
        </div>

        <div class="user-menu">
          <button class="btn btn--back" onclick="goBack()" title="Kembali">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="margin-right:8px">
              <path d="M10.707 2.293a1 1 0 010 1.414L6.414 8l4.293 4.293a1 1 0 01-1.414 1.414l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 0z"/>
            </svg>
            Kembali
          </button>
        </div>
      </div>
    </header>

    <!-- MAIN -->
    <div class="wrap">
      <div class="panel" role="region" aria-label="Persetujuan Penarikan">
        <div class="panel-header">
          <h2>Persetujuan Penarikan Saldo</h2>
        </div>

        <div class="panel-body">
          <table class="approval-table" aria-describedby="list-payouts">
            <thead>
              <tr>
                <th class="col-no">No</th>
                <th class="col-name">Nama Tempat Cuci</th>
                <th class="col-owner">Pemilik</th>
                <th class="col-contact">Kontak</th>
                <th class="col-lokasi">Lokasi</th>
                <th class="col-jumlah">Jumlah</th>
                <th class="col-aksi">Aksi</th>
              </tr>
            </thead>
            <tbody id="payoutList">
              <!-- rows akan di-generate oleh JS -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL QRIS -->
  <div class="modal-overlay" id="qrisModal" aria-hidden="true">
    <div class="modal-card" role="dialog" aria-modal="true">
      <h3>QRIS Pembayaran</h3>
      <p>Scan QRIS berikut menggunakan aplikasi e-wallet / mobile banking.</p>
      <img id="qrisImage" class="qr-image" src="" alt="QRIS" />
      <div class="modal-actions">
        <button class="btn ghost" id="qrisClose">Tutup</button>
        <button class="btn green" id="qrisApprove">Selesai</button>
      </div>
    </div>
    </div>

    <script>
        // Inject real withdrawal data from server
        window.withdrawalsData = @json($withdrawals);
    </script>
    <script src="{{ asset('js/penarikan.js') }}"></script>
</body>
</html>