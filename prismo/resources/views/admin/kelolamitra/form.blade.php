<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Formulir Steam Mitra - Prismo</title>
  <link rel="stylesheet" href="{{ asset('css/form.css') }}">
</head>
<body>
  <header class="top">
    <div class="top-inner">
      <button type="button" class="back-btn" onclick="window.location.href='{{ url('/admin/kelolamitra/kelolamitra') }}'">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12.5 15L7.5 10L12.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Kembali
      </button>
      <div class="title">
        {{ $mitra->approval_status === 'pending' ? 'Formulir Steam Mitra - Persetujuan' : 'Detail Informasi Mitra' }}
      </div>
    </div>
  </header>

  <main class="container">
    <section class="columns">
      <!-- LEFT -->
      <div class="col col-left">
        <h3 class="section-h">Informasi Bisnis</h3>

        <div class="field">
          <div class="label">Nama Bisnis</div>
          <div class="value">{{ $mitra->mitraProfile->business_name ?? '-' }}</div>
        </div>

        <div class="field">
          <div class="label">Tahun Berdiri</div>
          <div class="value">{{ $mitra->mitraProfile->establishment_year ?? '-' }}</div>
        </div>

        <h4 class="sub-h">Lokasi Bisnis</h4>

        <div class="field">
          <div class="label">Alamat Lengkap</div>
          <div class="value multi">{{ $mitra->mitraProfile->address ?? '-' }}</div>
        </div>

        <div class="inline-row">
          <div class="field small">
            <div class="label">Provinsi</div>
            <div class="value">{{ $mitra->mitraProfile->province ?? '-' }}</div>
          </div>

          <div class="field small">
            <div class="label">Kota / Kabupaten</div>
            <div class="value">{{ $mitra->mitraProfile->city ?? '-' }}</div>
          </div>

          <div class="field small">
            <div class="label">Kode Pos</div>
            <div class="value">{{ $mitra->mitraProfile->postal_code ?? '-' }}</div>
          </div>
        </div>

        <div class="box file-list">
          <div class="box-title">Fasilitas Usaha</div>
          <div class="box-sub">Foto Fasilitas Steam Mitra</div>

          <ul>
            @if($mitra->mitraProfile && $mitra->mitraProfile->facility_photos)
              @foreach(json_decode($mitra->mitraProfile->facility_photos) as $index => $photo)
                <li>
                  <div class="file-left">{{ basename($photo) }}</div>
                  <div class="file-right">
                    <button class="icon-eye" data-type="image" data-src="{{ asset('storage/' . $photo) }}" title="Lihat">👁</button>
                  </div>
                </li>
              @endforeach
            @else
              <li>
                <div class="file-left">Tidak ada foto</div>
              </li>
            @endif
          </ul>
        </div>
      </div>

      <!-- RIGHT -->
      <div class="col col-right">
        <h3 class="section-h">Informasi Kontak</h3>

        <div class="field">
          <div class="label">Nama Penanggung Jawab</div>
          <div class="value">{{ $mitra->mitraProfile->contact_person ?? '-' }}</div>
        </div>

        <div class="field">
          <div class="label">Email</div>
          <div class="value">{{ $mitra->email ?? '-' }}</div>
        </div>

        <div class="field">
          <div class="label">Nomor WhatsApp/Telepon</div>
          <div class="value">{{ $mitra->mitraProfile->phone ?? '-' }}</div>
        </div>

        <div class="field">
          <div class="label">Lokasi di Peta</div>
          <div class="value">{{ $mitra->mitraProfile->map_location ?? '-' }}</div>
        </div>

        <h4 class="sub-h">Operasional Usaha</h4>

        <div class="box doc-list">
          @if($mitra->mitraProfile && $mitra->mitraProfile->legal_doc)
          <div class="doc-item">
            <div class="doc-left">
              <div class="pdf-ico">PDF</div>
            </div>
            <div class="doc-mid">
              <div class="doc-title">SIUP/TDP/NIB</div>
              <div class="doc-sub">{{ basename($mitra->mitraProfile->legal_doc) }}</div>
            </div>
            <div class="doc-right">
              <button class="icon-eye" data-type="pdf" data-src="{{ asset('storage/' . $mitra->mitraProfile->legal_doc) }}" title="Lihat">👁</button>
            </div>
          </div>
          @endif

          @if($mitra->mitraProfile && $mitra->mitraProfile->ktp_photo)
          <div class="doc-item">
            <div class="doc-left">
              <div class="img-ico">IMG</div>
            </div>
            <div class="doc-mid">
              <div class="doc-title">KTP Penanggung Jawab</div>
              <div class="doc-sub">{{ basename($mitra->mitraProfile->ktp_photo) }}</div>
            </div>
            <div class="doc-right">
              <button class="icon-eye" data-type="image" data-src="{{ asset('storage/' . $mitra->mitraProfile->ktp_photo) }}" title="Lihat">👁</button>
            </div>
          </div>
          @endif

          @if($mitra->mitraProfile && $mitra->mitraProfile->qris_photo)
          <div class="doc-item">
            <div class="doc-left">
              <div class="img-ico">IMG</div>
            </div>
            <div class="doc-mid">
              <div class="doc-title">QRIS</div>
              <div class="doc-sub">{{ basename($mitra->mitraProfile->qris_photo) }}</div>
            </div>
            <div class="doc-right">
              <button class="icon-eye" data-type="image" data-src="{{ asset('storage/' . $mitra->mitraProfile->qris_photo) }}" title="Lihat">👁</button>
            </div>
          </div>
          @endif
        </div>

      </div>
    </section>

    <hr class="divider">

    @if($mitra->approval_status === 'pending')
    <section class="action-row">
      <button id="btnApprove" class="btn-approve" data-id="{{ $mitra->id }}">Setujui</button>
      <button id="btnReject" class="btn-reject" data-id="{{ $mitra->id }}">Tolak</button>
    </section>
    @else
    <section class="action-row">
      <div style="text-align: center; padding: 20px; background: #f5f5f5; border-radius: 8px; width: 100%;">
        <p style="margin: 0; color: #666; font-size: 14px;">
          Status: 
          @if($mitra->approval_status === 'approved')
            <span style="color: #28a745; font-weight: 600;">✓ Disetujui</span>
          @elseif($mitra->approval_status === 'rejected')
            <span style="color: #dc3545; font-weight: 600;">✗ Ditolak</span>
          @endif
        </p>
        @if($mitra->approval_status === 'rejected' && $mitra->mitraProfile && $mitra->mitraProfile->reject_reason)
          <p style="margin-top: 8px; font-size: 13px; color: #666;">
            Alasan: {{ $mitra->mitraProfile->reject_reason }}
          </p>
        @endif
      </div>
    </section>
    @endif
  </main>

  <!-- MODAL VIEWER DOKUMEN/GAMBAR -->
  <div id="modalViewer" class="modal-overlay">
    <div class="modal-card viewer-card">
      <div class="viewer-header">
        <h3 class="viewer-title" id="viewerTitle">Preview Dokumen</h3>
        <button class="viewer-close" id="viewerClose">&times;</button>
      </div>
      <div class="viewer-content">
        <div id="pdfViewer" class="pdf-container">
          <iframe id="pdfFrame" class="pdf-frame" frameborder="0"></iframe>
          <div class="pdf-actions">
            <button class="btn btn-primary" onclick="downloadFile()">Download PDF</button>
          </div>
        </div>
        <div id="imageViewer" class="image-container">
          <img id="imagePreview" class="image-preview" alt="Preview Gambar">
          <div class="image-actions">
            <button class="btn btn-primary" onclick="downloadFile()">Download Gambar</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL KONFIRMASI TOLAK -->
  <div id="modalReject" class="modal-overlay">
    <div class="modal-card">
      <div class="modal-icon warn">!</div>
      <h3 class="modal-title">Yakin ingin menolak?</h3>
      <p class="modal-text">Jika ditolak, mitra akan menerima email notifikasi dan dapat mengisi formulir ulang.</p>
      <div class="form-group" style="margin: 20px 0; text-align: left;">
        <label for="rejectReason" style="display: block; margin-bottom: 8px; font-weight: 600;">Alasan Penolakan <span style="color: red;">*</span></label>
        <textarea id="rejectReason" rows="4" placeholder="Masukkan alasan penolakan (minimal 10 karakter)" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; resize: vertical;"></textarea>
        <small id="rejectReasonError" style="color: red; display: none; margin-top: 4px;">Alasan penolakan wajib diisi minimal 10 karakter</small>
      </div>
      <div class="modal-actions">
        <button id="confirmReject" class="btn btn-danger">Ya, Tolak</button>
        <button id="cancelReject" class="btn btn-ghost">Batal</button>
      </div>
    </div>
  </div>

  <!-- MODAL SUCCESS -->
  <div id="modalSuccess" class="modal-overlay">
    <div class="modal-card">
      <div class="modal-icon success">✓</div>
      <h3 class="modal-title">Berhasil</h3>
      <p class="modal-text" id="successText">Mitra telah disetujui.</p>
      <div class="modal-actions">
        <button id="okSuccess" class="btn btn-primary">OK</button>
      </div>
    </div>
  </div>

  <script src="{{ asset('js/form.js') }}"></script>
</body>
</html>
