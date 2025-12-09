<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bantuan & FAQ | PRISMO</title>
    <link rel="stylesheet" href="{{ asset('css/help-faq.css') }}">
</head>
<body>
    <div class="container">
        <header>
            <button type="button" class="btn-back" onclick="goBack()">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M12.707 4.293a1 1 0 010 1.414L8.414 10l4.293 4.293a1 1 0 01-1.414 1.414l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 0z"/>
                </svg>
                Kembali
            </button>
            <h1>Bantuan & FAQ</h1>
            <p class="subtitle">Temukan jawaban untuk pertanyaan umum Anda</p>
        </header>

        <!-- Search Box -->
        <div class="search-container">
            <div class="search-box">
                <svg class="search-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                </svg>
                <input type="text" id="searchInput" placeholder="Cari pertanyaan atau topik..." class="search-input">
                <button type="button" id="clearSearch" class="clear-search" style="display: none;">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M4.646 4.646a.5.5 0 01.708 0L8 7.293l2.646-2.647a.5.5 0 01.708.708L8.707 8l2.647 2.646a.5.5 0 01-.708.708L8 8.707l-2.646 2.647a.5.5 0 01-.708-.708L7.293 8 4.646 5.354a.5.5 0 010-.708z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Category Tabs -->
        <div class="category-tabs">
            <button type="button" class="category-tab active" data-category="all">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                </svg>
                Semua
            </button>
            <button type="button" class="category-tab" data-category="account">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
                Akun
            </button>
            <button type="button" class="category-tab" data-category="booking">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                Booking
            </button>
            <button type="button" class="category-tab" data-category="payment">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                </svg>
                Pembayaran
            </button>
            <button type="button" class="category-tab" data-category="other">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                </svg>
                Lainnya
            </button>
        </div>

        <!-- FAQ List -->
        <div class="faq-container">
            <div id="noResults" class="no-results" style="display: none;">
                <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                    <circle cx="32" cy="32" r="30" stroke="#E5E7EB" stroke-width="4"/>
                    <path d="M32 20v16M32 44v.01" stroke="#9CA3AF" stroke-width="4" stroke-linecap="round"/>
                </svg>
                <p>Tidak ada hasil ditemukan</p>
                <span>Coba gunakan kata kunci lain</span>
            </div>

            <!-- Account Category -->
            <div class="faq-item" data-category="account">
                <button type="button" class="faq-question">
                    <span>Bagaimana cara mendaftar sebagai mitra di PRISMO?</span>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Untuk mendaftar sebagai mitra PRISMO:</p>
                    <ol>
                        <li>Klik tombol "Daftar Sebagai Mitra" di halaman utama</li>
                        <li>Isi formulir pendaftaran dengan data lengkap usaha Anda</li>
                        <li>Upload dokumen yang diperlukan (KTP, NPWP, foto usaha)</li>
                        <li>Tunggu proses verifikasi dari tim kami (1-3 hari kerja)</li>
                        <li>Setelah disetujui, Anda dapat mulai menerima pesanan</li>
                    </ol>
                </div>
            </div>

            <div class="faq-item" data-category="account">
                <button type="button" class="faq-question">
                    <span>Bagaimana cara mengubah password akun saya?</span>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Untuk mengubah password:</p>
                    <ol>
                        <li>Masuk ke halaman Profil</li>
                        <li>Pilih menu "Ubah Password"</li>
                        <li>Masukkan password lama dan password baru</li>
                        <li>Klik "Simpan Password"</li>
                    </ol>
                    <p><strong>Catatan:</strong> Jika Anda login menggunakan Google, fitur ubah password tidak tersedia. Kelola password melalui akun Google Anda.</p>
                </div>
            </div>

            <div class="faq-item" data-category="account">
                <button type="button" class="faq-question">
                    <span>Bagaimana cara memperbarui informasi profil mitra?</span>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Anda dapat memperbarui informasi profil melalui:</p>
                    <ol>
                        <li>Buka menu "Profil" di dashboard</li>
                        <li>Klik tombol "Edit Profil"</li>
                        <li>Ubah informasi yang ingin diperbarui</li>
                        <li>Klik "Simpan Perubahan"</li>
                    </ol>
                    <p>Informasi yang dapat diubah meliputi: nama usaha, alamat, nomor telepon, jam operasional, dan foto profil.</p>
                </div>
            </div>

            <!-- Booking Category -->
            <div class="faq-item" data-category="booking">
                <button type="button" class="faq-question">
                    <span>Bagaimana cara menerima pesanan booking?</span>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Proses penerimaan pesanan:</p>
                    <ol>
                        <li>Notifikasi akan muncul saat ada pesanan baru</li>
                        <li>Buka menu "Antrian" untuk melihat detail pesanan</li>
                        <li>Review informasi customer dan layanan yang dipesan</li>
                        <li>Klik "Terima" untuk menerima atau "Tolak" untuk menolak</li>
                        <li>Jika diterima, customer akan mendapat konfirmasi</li>
                    </ol>
                </div>
            </div>

            <div class="faq-item" data-category="booking">
                <button type="button" class="faq-question">
                    <span>Apa yang harus dilakukan jika customer tidak datang?</span>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Jika customer tidak datang sesuai jadwal:</p>
                    <ol>
                        <li>Tunggu hingga 15 menit setelah waktu booking</li>
                        <li>Hubungi customer melalui nomor yang tertera</li>
                        <li>Jika tidak ada respon, ubah status booking menjadi "No Show"</li>
                        <li>Laporkan ke admin melalui fitur "Laporan"</li>
                        <li>Kompensasi akan diproses sesuai kebijakan</li>
                    </ol>
                </div>
            </div>

            <div class="faq-item" data-category="booking">
                <button type="button" class="faq-question">
                    <span>Bagaimana cara membatalkan pesanan yang sudah diterima?</span>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Untuk membatalkan pesanan:</p>
                    <ol>
                        <li>Buka detail pesanan di menu "Antrian"</li>
                        <li>Klik tombol "Batalkan Pesanan"</li>
                        <li>Pilih alasan pembatalan</li>
                        <li>Konfirmasi pembatalan</li>
                    </ol>
                    <p><strong>Perhatian:</strong> Pembatalan berulang dapat mempengaruhi rating mitra Anda. Pastikan hanya membatalkan jika benar-benar diperlukan.</p>
                </div>
            </div>

            <!-- Payment Category -->
            <div class="faq-item" data-category="payment">
                <button type="button" class="faq-question">
                    <span>Kapan saya bisa menarik saldo dari PRISMO?</span>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Penarikan saldo dapat dilakukan dengan ketentuan:</p>
                    <ul>
                        <li>Minimal saldo untuk penarikan: Rp 50.000</li>
                        <li>Penarikan dapat dilakukan setiap hari</li>
                        <li>Proses transfer 1-2 hari kerja</li>
                        <li>Pastikan rekening bank sudah terdaftar dan terverifikasi</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item" data-category="payment">
                <button type="button" class="faq-question">
                    <span>Bagaimana cara menambahkan rekening bank untuk pencairan?</span>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Untuk menambahkan rekening bank:</p>
                    <ol>
                        <li>Buka menu "Saldo"</li>
                        <li>Klik "Tambah Rekening"</li>
                        <li>Masukkan informasi rekening (nama bank, nomor rekening, nama pemilik)</li>
                        <li>Upload bukti kepemilikan rekening (buku tabungan/m-banking)</li>
                        <li>Tunggu verifikasi dari tim (maksimal 1 hari kerja)</li>
                    </ol>
                </div>
            </div>

            <div class="faq-item" data-category="payment">
                <button type="button" class="faq-question">
                    <span>Berapa biaya admin atau komisi yang dikenakan PRISMO?</span>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Struktur biaya PRISMO:</p>
                    <ul>
                        <li>Komisi per transaksi: 15% dari total pembayaran</li>
                        <li>Biaya penarikan saldo: Gratis</li>
                        <li>Biaya pendaftaran: Gratis</li>
                    </ul>
                    <p>Contoh: Jika layanan Rp 100.000, mitra menerima Rp 85.000 dan PRISMO mendapat Rp 15.000.</p>
                </div>
            </div>

            <!-- Other Category -->
            <div class="faq-item" data-category="other">
                <button type="button" class="faq-question">
                    <span>Bagaimana cara meningkatkan rating mitra saya?</span>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Tips meningkatkan rating:</p>
                    <ul>
                        <li>Berikan layanan terbaik dan profesional</li>
                        <li>Pastikan kualitas hasil sesuai ekspektasi customer</li>
                        <li>Tepat waktu dalam melayani pesanan</li>
                        <li>Responsif terhadap pertanyaan customer</li>
                        <li>Jaga kebersihan dan kerapihan tempat usaha</li>
                        <li>Minimalkan pembatalan pesanan</li>
                        <li>Minta feedback untuk perbaikan</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item" data-category="other">
                <button type="button" class="faq-question">
                    <span>Bagaimana cara merespon review dari customer?</span>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Untuk merespon review customer:</p>
                    <ol>
                        <li>Buka menu "Review"</li>
                        <li>Pilih review yang ingin direspon</li>
                        <li>Klik tombol "Balas"</li>
                        <li>Tulis respon dengan sopan dan profesional</li>
                        <li>Klik "Kirim Respon"</li>
                    </ol>
                    <p><strong>Tips:</strong> Selalu ucapkan terima kasih, tanggapi kritik dengan baik, dan tunjukkan komitmen untuk perbaikan.</p>
                </div>
            </div>

            <div class="faq-item" data-category="other">
                <button type="button" class="faq-question">
                    <span>Bagaimana cara menghubungi customer service PRISMO?</span>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Anda dapat menghubungi kami melalui:</p>
                    <ul>
                        <li><strong>Email:</strong> support@prismo.id</li>
                        <li><strong>WhatsApp:</strong> +62 812-3456-7890</li>
                        <li><strong>Telepon:</strong> (021) 1234-5678</li>
                        <li><strong>Jam operasional:</strong> Senin - Jumat, 09:00 - 18:00 WIB</li>
                    </ul>
                    <p>Tim kami siap membantu Anda dengan senang hati!</p>
                </div>
            </div>
        </div>

        <!-- Contact Support Section -->
        <div class="contact-support">
            <div class="support-icon">
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                    <circle cx="24" cy="24" r="22" stroke="#FF6B35" stroke-width="4"/>
                    <path d="M24 14v16M24 34v.01" stroke="#FF6B35" stroke-width="4" stroke-linecap="round"/>
                </svg>
            </div>
            <h3>Tidak menemukan jawaban?</h3>
            <p>Hubungi tim support kami untuk bantuan lebih lanjut</p>
            <button type="button" class="btn-contact" onclick="contactSupport()">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                </svg>
                Hubungi Support
            </button>
        </div>
    </div>

    <script src="{{ asset('js/help-faq.js') }}"></script>
</body>
</html>
