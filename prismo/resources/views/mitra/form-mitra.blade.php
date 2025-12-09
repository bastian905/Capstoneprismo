<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pendaftaran Mitra Steam</title>
    <link rel="stylesheet" href="{{ asset('css/form-mitra.css') }}">
</head>

<body>
    <div class="container">
        <header>
            <h1>Pendaftaran Mitra Steam</h1>
            <p class="subtitle">Bergabunglah dengan platform terpercaya dan kembangkan bisnis cuci steam Anda</p>
        </header>

        <form id="registrationForm">
            <div class="form-container">
                <!-- Kolom Kiri -->
                <div class="form-column left-column">
                    <!-- Informasi Bisnis -->
                    <section class="form-section">
                        <h2>Informasi Bisnis</h2>

                        <div class="form-group">
                            <label for="businessName" class="required">Nama Bisnis</label>
                            <input type="text" id="businessName" name="businessName" placeholder="Prismo" required>
                        </div>

                        <div class="form-group">
                            <label for="establishmentYear" class="required">Tahun Berdiri</label>
                            <input type="number" id="establishmentYear" name="establishmentYear" min="1900" max="2025"
                                placeholder="2025" required>
                        </div>

                        <div class="form-group">
                            <label for="address" class="required">Alamat Lengkap</label>
                            <input type="text" id="address" name="address"
                                placeholder="Jl. Cikeas No. 123, Bogor Timur" required>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="province" class="required" id="provinceLabel">Provinsi</label>
                                    <select id="province" name="province" class="form-control" required>
                                        <option value="">Pilih Provinsi</option>
                                        <option value="DKI Jakarta">DKI Jakarta</option>
                                        <option value="Jawa Barat">Jawa Barat</option>
                                        <option value="Jawa Tengah">Jawa Tengah</option>
                                        <option value="Jawa Timur">Jawa Timur</option>
                                        <option value="Banten">Banten</option>
                                        <option value="Yogyakarta">Yogyakarta</option>
                                        <option value="Bali">Bali</option>
                                        <option value="Aceh">Aceh</option>
                                        <option value="Sumatera Utara">Sumatera Utara</option>
                                        <option value="Sumatera Barat">Sumatera Barat</option>
                                        <option value="Sumatera Selatan">Sumatera Selatan</option>
                                        <option value="Riau">Riau</option>
                                        <option value="Kepulauan Riau">Kepulauan Riau</option>
                                        <option value="Jambi">Jambi</option>
                                        <option value="Lampung">Lampung</option>
                                        <option value="Bengkulu">Bengkulu</option>
                                        <option value="Bangka Belitung">Bangka Belitung</option>
                                        <option value="Kalimantan Barat">Kalimantan Barat</option>
                                        <option value="Kalimantan Tengah">Kalimantan Tengah</option>
                                        <option value="Kalimantan Selatan">Kalimantan Selatan</option>
                                        <option value="Kalimantan Timur">Kalimantan Timur</option>
                                        <option value="Kalimantan Utara">Kalimantan Utara</option>
                                        <option value="Sulawesi Utara">Sulawesi Utara</option>
                                        <option value="Sulawesi Tengah">Sulawesi Tengah</option>
                                        <option value="Sulawesi Selatan">Sulawesi Selatan</option>
                                        <option value="Sulawesi Tenggara">Sulawesi Tenggara</option>
                                        <option value="Sulawesi Barat">Sulawesi Barat</option>
                                        <option value="Gorontalo">Gorontalo</option>
                                        <option value="Maluku">Maluku</option>
                                        <option value="Maluku Utara">Maluku Utara</option>
                                        <option value="Papua">Papua</option>
                                        <option value="Papua Barat">Papua Barat</option>
                                        <option value="Papua Tengah">Papua Tengah</option>
                                        <option value="Papua Pegunungan">Papua Pegunungan</option>
                                        <option value="Papua Selatan">Papua Selatan</option>
                                        <option value="Papua Barat Daya">Papua Barat Daya</option>
                                        <option value="Nusa Tenggara Barat">Nusa Tenggara Barat</option>
                                        <option value="Nusa Tenggara Timur">Nusa Tenggara Timur</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="city" class="required" id="cityLabel">Kota/Kabupaten</label>
                                    <select id="city" name="city" class="form-control" required disabled>
                                        <option value="">Pilih Provinsi Terlebih Dahulu</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="postalCode" class="required" id="postalCodeLabel">Kode Pos</label>
                                    <select id="postalCode" name="postalCode" class="form-control" required disabled>
                                        <option value="">Pilih Kota Terlebih Dahulu</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="mapLocation" class="required">Lokasi di Peta</label>
                                    <input type="url" id="mapLocation" name="mapLocation"
                                        placeholder="https://www.google.com/maps/place/" required>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Informasi Kontak -->
                    <section class="form-section">
                        <h2>Informasi Kontak</h2>

                        <div class="form-group">
                            <label for="contactPerson" class="required">Nama Penanggung Jawab</label>
                            <input type="text" id="contactPerson" name="contactPerson" placeholder="Sari Dewi" required>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="email" class="required">Email</label>
                                    <input type="email" id="email" name="email" placeholder="sari@quickclean.com" value="{{ Auth::user()->email }}" readonly style="background-color: #f5f5f5; cursor: not-allowed;" required>
                                    <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Email tidak dapat diubah</small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="phone" class="required">Nomor WhatsApp/Telepon</label>
                                    <input type="tel" id="phone" name="phone" placeholder="08123456789" required>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Kolom Kanan -->
                <div class="form-column right-column">
                    <!-- Fasilitas & Dokumentasi -->
                    <section class="form-section">
                        <h2>Fasilitas & Dokumentasi</h2>

                        <!-- Foto Fasilitas -->
                        <div class="form-group" id="facilityGroup">
                            <label class="required">Foto Fasilitas (Maksimal 5 foto)</label>
                            <div class="upload-card" id="facilityUpload">
                                <div class="upload-content">
                                    <div class="upload-icon">
                                        <img src="{{ asset('images/fasilitas.png') }}" alt="Fasilitas">
                                    </div>
                                    <div class="upload-text">
                                        <p class="upload-title">Klik untuk upload foto fasilitas</p>
                                        <p class="upload-subtitle">JPG atau PNG maksimal 5MB per foto</p>
                                    </div>
                                </div>
                            </div>
                            <div id="facilityPreview" class="upload-preview"></div>
                        </div>

                        <!-- SIUP/TDP/NIB -->
                        <div class="form-group">
                            <label class="required">SIUP/TDP/NIB</label>
                            <div class="upload-card" id="legalDocUpload">
                                <div class="upload-content">
                                    <div class="upload-icon">
                                        <img src="{{ asset('images/dokumen.png') }}" alt="Dokumen">
                                    </div>
                                    <div class="upload-text">
                                        <p class="upload-title">Upload dokumen legalitas usaha</p>
                                        <p class="upload-subtitle">PDF maksimal 5MB</p>
                                    </div>
                                </div>
                            </div>
                            <div id="legalDocPreview" class="upload-preview"></div>
                        </div>

                        <!-- KTP Penanggung Jawab -->
                        <div class="form-group">
                            <label class="required">KTP Penanggung Jawab</label>
                            <div class="upload-card" id="ktpUpload">
                                <div class="upload-content">
                                    <div class="upload-icon">
                                        <img src="{{ asset('images/ktp.png') }}" alt="KTP">
                                    </div>
                                    <div class="upload-text">
                                        <p class="upload-title">Upload KTP yang masih berlaku</p>
                                        <p class="upload-subtitle">JPG atau PNG maksimal 5MB</p>
                                    </div>
                                </div>
                            </div>
                            <div id="ktpPreview" class="upload-preview"></div>
                        </div>

                        <!-- QRIS -->
                        <div class="form-group">
                            <label class="required">QRIS</label>
                            <div class="upload-card" id="qrisUpload">
                                <div class="upload-content">
                                    <div class="upload-icon">
                                        <img src="{{ asset('images/qris.png') }}" alt="QRIS">
                                    </div>
                                    <div class="upload-text">
                                        <p class="upload-title">Upload barcode QRIS saja (tanpa background)</p>
                                        <p class="upload-subtitle">JPG atau PNG maksimal 5MB</p>
                                    </div>
                                </div>
                            </div>
                            <div id="qrisPreview" class="upload-preview"></div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Proses Verifikasi & Syarat Ketentuan (Full Width) -->
            <section class="form-section full-width verification-process-section">
                <h1>Proses Verifikasi & Syarat Ketentuan</h1>

                <div class="two-column-layout">
                    <!-- Kolom Kiri - Tahapan Verifikasi -->
                    <div class="column left-column">
                        <div class="verification-section">
                            <h2>Tahapan Verifikasi</h2>
                            <ul class="verification-list">
                                <li>Verifikasi dokumen legalitas <span class="time-info">(1-2 hari kerja setelah
                                        pengajuan)</span></li>
                                <li>Survey lokasi oleh tim kami <span class="time-info">(2-3 hari kerja setelah dokumen
                                        disetujui)</span></li>
                                <li>Pelatihan sistem dan SOP <span class="time-info">(3 hari kerja setelah
                                        survey)</span></li>
                                <li>Aktivasi akun mitra <span class="time-info">(1 hari kerja setelah pelatihan
                                        selesai)</span></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Kolom Kanan - Keuntungan Mitra -->
                    <div class="column right-column">
                        <div class="benefits-section">
                            <h2>Keuntungan Mitra</h2>
                            <ul class="benefits-list">
                                <li>Komisi 95% dari total seluruh transaksi</li>
                                <li>Marketing digital gratis</li>
                                <li>Sistem pembayaran terintegrasi</li>
                                <li>Dukungan customer service 24/7</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Bagian Persetujuan -->
            <div class="agreement-section">
                <div class="terms-agreement">
                    <label class="checkbox-simple">
                        <input type="checkbox" id="agreeTerms" name="agreeTerms">
                        <span class="checkmark-simple"></span>
                        <span class="terms-text">
                            Saya menyetujui Syarat & Ketentuan dan Kebijakan Privasi Steam. Saya memahami bahwa
                            informasi yang diberikan akan diverifikasi dan digunakan untuk proses kemitraan.
                        </span>
                    </label>
                </div>
            </div>

            <!-- Bagian Tombol Logout dan Daftar -->
            <div class="button-section">
                <button type="button" class="btn-logout" onclick="handleLogout()">Logout</button>
                <button type="submit" class="btn-submit">Daftar Sekarang</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data provinsi ke kota/kabupaten
            const cityData = {
            'Aceh': ['Banda Aceh', 'Langsa', 'Lhokseumawe', 'Sabang', 'Subulussalam', 'Aceh Barat', 'Aceh Barat Daya', 'Aceh Besar', 'Aceh Jaya', 'Aceh Selatan', 'Aceh Singkil', 'Aceh Tamiang', 'Aceh Tengah', 'Aceh Tenggara', 'Aceh Timur', 'Aceh Utara', 'Bener Meriah', 'Bireuen', 'Gayo Lues', 'Nagan Raya', 'Pidie', 'Pidie Jaya', 'Simeulue'],
            'Bali': ['Denpasar', 'Badung', 'Bangli', 'Buleleng', 'Gianyar', 'Jembrana', 'Karangasem', 'Klungkung', 'Tabanan'],
            'Banten': ['Cilegon', 'Serang', 'Tangerang', 'Tangerang Selatan', 'Lebak', 'Pandeglang', 'Kabupaten Serang', 'Kabupaten Tangerang'],
            'Bengkulu': ['Bengkulu', 'Bengkulu Selatan', 'Bengkulu Tengah', 'Bengkulu Utara', 'Kaur', 'Kepahiang', 'Lebong', 'Mukomuko', 'Rejang Lebong', 'Seluma'],
            'Yogyakarta': ['Yogyakarta', 'Bantul', 'Gunung Kidul', 'Kulon Progo', 'Sleman'],
            'DKI Jakarta': ['Jakarta Barat', 'Jakarta Pusat', 'Jakarta Selatan', 'Jakarta Timur', 'Jakarta Utara', 'Kepulauan Seribu'],
            'Gorontalo': ['Gorontalo', 'Boalemo', 'Bone Bolango', 'Gorontalo Utara', 'Pohuwato'],
            'Jambi': ['Jambi', 'Sungai Penuh', 'Batang Hari', 'Bungo', 'Kerinci', 'Merangin', 'Muaro Jambi', 'Sarolangun', 'Tanjung Jabung Barat', 'Tanjung Jabung Timur', 'Tebo'],
            'Jawa Barat': ['Bandung', 'Banjar', 'Bekasi', 'Bogor', 'Cimahi', 'Cirebon', 'Depok', 'Sukabumi', 'Tasikmalaya', 'Bandung Barat', 'Ciamis', 'Cianjur', 'Garut', 'Indramayu', 'Karawang', 'Kuningan', 'Majalengka', 'Pangandaran', 'Purwakarta', 'Subang', 'Sumedang', 'Kabupaten Bandung', 'Kabupaten Bekasi', 'Kabupaten Bogor', 'Kabupaten Cirebon', 'Kabupaten Sukabumi', 'Kabupaten Tasikmalaya'],
            'Jawa Tengah': ['Magelang', 'Pekalongan', 'Salatiga', 'Semarang', 'Surakarta', 'Tegal', 'Banjarnegara', 'Banyumas', 'Batang', 'Blora', 'Boyolali', 'Brebes', 'Cilacap', 'Demak', 'Grobogan', 'Jepara', 'Karanganyar', 'Kebumen', 'Kendal', 'Klaten', 'Kudus', 'Pati', 'Pemalang', 'Purbalingga', 'Purworejo', 'Rembang', 'Sragen', 'Sukoharjo', 'Temanggung', 'Wonogiri', 'Wonosobo', 'Kabupaten Magelang', 'Kabupaten Pekalongan', 'Kabupaten Semarang', 'Kabupaten Tegal'],
            'Jawa Timur': ['Batu', 'Blitar', 'Kediri', 'Madiun', 'Malang', 'Mojokerto', 'Pasuruan', 'Probolinggo', 'Surabaya', 'Bangkalan', 'Banyuwangi', 'Bojonegoro', 'Bondowoso', 'Gresik', 'Jember', 'Jombang', 'Lamongan', 'Lumajang', 'Magetan', 'Nganjuk', 'Ngawi', 'Pacitan', 'Pamekasan', 'Ponorogo', 'Sampang', 'Sidoarjo', 'Situbondo', 'Sumenep', 'Trenggalek', 'Tuban', 'Tulungagung', 'Kabupaten Blitar', 'Kabupaten Kediri', 'Kabupaten Madiun', 'Kabupaten Malang', 'Kabupaten Mojokerto', 'Kabupaten Pasuruan', 'Kabupaten Probolinggo'],
            'Kalimantan Barat': ['Pontianak', 'Singkawang', 'Bengkayang', 'Kapuas Hulu', 'Kayong Utara', 'Ketapang', 'Kubu Raya', 'Landak', 'Melawi', 'Mempawah', 'Sambas', 'Sanggau', 'Sekadau', 'Sintang'],
            'Kalimantan Selatan': ['Banjarbaru', 'Banjarmasin', 'Balangan', 'Banjar', 'Barito Kuala', 'Hulu Sungai Selatan', 'Hulu Sungai Tengah', 'Hulu Sungai Utara', 'Kotabaru', 'Tabalong', 'Tanah Bumbu', 'Tanah Laut', 'Tapin'],
            'Kalimantan Tengah': ['Palangka Raya', 'Barito Selatan', 'Barito Timur', 'Barito Utara', 'Gunung Mas', 'Kapuas', 'Katingan', 'Kotawaringin Barat', 'Kotawaringin Timur', 'Lamandau', 'Murung Raya', 'Pulang Pisau', 'Seruyan', 'Sukamara'],
            'Kalimantan Timur': ['Balikpapan', 'Bontang', 'Samarinda', 'Berau', 'Kutai Barat', 'Kutai Kartanegara', 'Kutai Timur', 'Mahakam Ulu', 'Paser', 'Penajam Paser Utara'],
            'Kalimantan Utara': ['Tarakan', 'Bulungan', 'Malinau', 'Nunukan', 'Tana Tidung'],
            'Bangka Belitung': ['Pangkal Pinang', 'Bangka', 'Bangka Barat', 'Bangka Selatan', 'Bangka Tengah', 'Belitung', 'Belitung Timur'],
            'Kepulauan Riau': ['Batam', 'Tanjung Pinang', 'Bintan', 'Karimun', 'Kepulauan Anambas', 'Lingga', 'Natuna'],
            'Lampung': ['Bandar Lampung', 'Metro', 'Lampung Barat', 'Lampung Selatan', 'Lampung Tengah', 'Lampung Timur', 'Lampung Utara', 'Mesuji', 'Pesawaran', 'Pesisir Barat', 'Pringsewu', 'Tanggamus', 'Tulang Bawang', 'Tulang Bawang Barat', 'Way Kanan'],
            'Maluku': ['Ambon', 'Tual', 'Buru', 'Buru Selatan', 'Kepulauan Aru', 'Maluku Barat Daya', 'Maluku Tengah', 'Maluku Tenggara', 'Maluku Tenggara Barat', 'Seram Bagian Barat', 'Seram Bagian Timur'],
            'Maluku Utara': ['Ternate', 'Tidore Kepulauan', 'Halmahera Barat', 'Halmahera Selatan', 'Halmahera Tengah', 'Halmahera Timur', 'Halmahera Utara', 'Kepulauan Sula', 'Pulau Morotai', 'Pulau Taliabu'],
            'Nusa Tenggara Barat': ['Bima', 'Mataram', 'Dompu', 'Lombok Barat', 'Lombok Tengah', 'Lombok Timur', 'Lombok Utara', 'Sumbawa', 'Sumbawa Barat'],
            'Nusa Tenggara Timur': ['Kupang', 'Alor', 'Belu', 'Ende', 'Flores Timur', 'Lembata', 'Manggarai', 'Manggarai Barat', 'Manggarai Timur', 'Nagekeo', 'Ngada', 'Rote Ndao', 'Sabu Raijua', 'Sikka', 'Sumba Barat', 'Sumba Barat Daya', 'Sumba Tengah', 'Sumba Timur', 'Timor Tengah Selatan', 'Timor Tengah Utara'],
            'Papua': ['Jayapura', 'Asmat', 'Biak Numfor', 'Boven Digoel', 'Deiyai', 'Dogiyai', 'Intan Jaya', 'Jayawijaya', 'Keerom', 'Kepulauan Yapen', 'Lanny Jaya', 'Mamberamo Raya', 'Mamberamo Tengah', 'Mappi', 'Merauke', 'Mimika', 'Nabire', 'Nduga', 'Paniai', 'Pegunungan Bintang', 'Puncak', 'Puncak Jaya', 'Sarmi', 'Supiori', 'Tolikara', 'Waropen', 'Yahukimo', 'Yalimo'],
            'Papua Barat': ['Sorong', 'Fakfak', 'Kaimana', 'Manokwari', 'Manokwari Selatan', 'Maybrat', 'Pegunungan Arfak', 'Raja Ampat', 'Sorong Selatan', 'Tambrauw', 'Teluk Bintuni', 'Teluk Wondama'],
            'Riau': ['Dumai', 'Pekanbaru', 'Bengkalis', 'Indragiri Hilir', 'Indragiri Hulu', 'Kampar', 'Kepulauan Meranti', 'Kuantan Singingi', 'Pelalawan', 'Rokan Hilir', 'Rokan Hulu', 'Siak'],
            'Sulawesi Barat': ['Majene', 'Mamasa', 'Mamuju', 'Mamuju Tengah', 'Mamuju Utara', 'Polewali Mandar'],
            'Sulawesi Selatan': ['Makassar', 'Palopo', 'Parepare', 'Bantaeng', 'Barru', 'Bone', 'Bulukumba', 'Enrekang', 'Gowa', 'Jeneponto', 'Kepulauan Selayar', 'Luwu', 'Luwu Timur', 'Luwu Utara', 'Maros', 'Pangkajene dan Kepulauan', 'Pinrang', 'Sidenreng Rappang', 'Sinjai', 'Soppeng', 'Takalar', 'Tana Toraja', 'Toraja Utara', 'Wajo'],
            'Sulawesi Tengah': ['Palu', 'Banggai', 'Banggai Kepulauan', 'Banggai Laut', 'Buol', 'Donggala', 'Morowali', 'Morowali Utara', 'Parigi Moutong', 'Poso', 'Sigi', 'Tojo Una-Una', 'Toli-Toli'],
            'Sulawesi Tenggara': ['Bau-Bau', 'Kendari', 'Bombana', 'Buton', 'Buton Selatan', 'Buton Tengah', 'Buton Utara', 'Kolaka', 'Kolaka Timur', 'Kolaka Utara', 'Konawe', 'Konawe Kepulauan', 'Konawe Selatan', 'Konawe Utara', 'Muna', 'Muna Barat', 'Wakatobi'],
            'Sulawesi Utara': ['Bitung', 'Kotamobagu', 'Manado', 'Tomohon', 'Bolaang Mongondow', 'Bolaang Mongondow Selatan', 'Bolaang Mongondow Timur', 'Bolaang Mongondow Utara', 'Kepulauan Sangihe', 'Kepulauan Siau Tagulandang Biaro', 'Kepulauan Talaud', 'Minahasa', 'Minahasa Selatan', 'Minahasa Tenggara', 'Minahasa Utara'],
            'Sumatera Barat': ['Bukittinggi', 'Padang', 'Padang Panjang', 'Pariaman', 'Payakumbuh', 'Sawahlunto', 'Solok', 'Agam', 'Dharmasraya', 'Kepulauan Mentawai', 'Lima Puluh Kota', 'Padang Pariaman', 'Pasaman', 'Pasaman Barat', 'Pesisir Selatan', 'Sijunjung', 'Solok Selatan', 'Tanah Datar'],
            'Sumatera Selatan': ['Lubuklinggau', 'Pagar Alam', 'Palembang', 'Prabumulih', 'Banyuasin', 'Empat Lawang', 'Lahat', 'Muara Enim', 'Musi Banyuasin', 'Musi Rawas', 'Musi Rawas Utara', 'Ogan Ilir', 'Ogan Komering Ilir', 'Ogan Komering Ulu', 'Ogan Komering Ulu Selatan', 'Ogan Komering Ulu Timur', 'Penukal Abab Lematang Ilir'],
            'Sumatera Utara': ['Binjai', 'Gunungsitoli', 'Medan', 'Padang Sidempuan', 'Pematang Siantar', 'Sibolga', 'Tanjung Balai', 'Tebing Tinggi', 'Asahan', 'Batubara', 'Dairi', 'Deli Serdang', 'Humbang Hasundutan', 'Karo', 'Labuhanbatu', 'Labuhanbatu Selatan', 'Labuhanbatu Utara', 'Langkat', 'Mandailing Natal', 'Nias', 'Nias Barat', 'Nias Selatan', 'Nias Utara', 'Padang Lawas', 'Padang Lawas Utara', 'Pakpak Bharat', 'Samosir', 'Serdang Bedagai', 'Simalungun', 'Tapanuli Selatan', 'Tapanuli Tengah', 'Tapanuli Utara', 'Toba Samosir']
        };

        // Data kota ke kode pos (contoh, bisa diperluas sesuai kebutuhan)
        const postalCodeData = {
            'Banda Aceh': ['23111', '23115', '23116', '23117', '23118', '23119', '23121', '23122', '23123', '23124', '23125', '23126', '23127', '23128', '23129', '23131', '23132', '23133', '23141', '23142', '23143', '23144', '23145', '23146', '23147', '23148', '23149', '23151', '23152', '23153', '23154', '23155', '23156', '23157', '23158', '23159', '23231', '23232', '23233', '23234', '23235', '23236', '23237', '23238', '23239', '23241', '23242', '23243', '23244', '23245', '23246', '23247', '23248', '23249'],
            'Denpasar': ['80111', '80112', '80113', '80114', '80115', '80116', '80117', '80118', '80119', '80225', '80226', '80227', '80228', '80229', '80231', '80232', '80233', '80234', '80235', '80236', '80237', '80238', '80239', '80361', '80362', '80363', '80364', '80365', '80366', '80367', '80368', '80369', '80571', '80572', '80573', '80574', '80575', '80576', '80577', '80578', '80579'],
            'Yogyakarta': ['55111', '55112', '55113', '55114', '55115', '55116', '55117', '55141', '55142', '55143', '55144', '55151', '55152', '55153', '55161', '55162', '55163', '55164', '55165', '55166', '55167', '55168', '55181', '55182', '55183', '55184', '55191', '55192', '55193', '55194', '55221', '55222', '55223', '55224', '55225', '55226', '55227', '55228', '55229', '55231', '55232', '55233', '55234', '55241', '55242', '55243', '55244', '55251', '55252', '55253', '55254', '55261', '55262', '55263', '55264', '55271', '55272', '55273', '55274', '55281', '55282', '55283', '55284', '55291', '55292', '55293', '55294'],
            'Jakarta Barat': ['11110', '11120', '11130', '11140', '11150', '11160', '11170', '11180', '11210', '11220', '11230', '11240', '11250', '11260', '11270', '11310', '11320', '11330', '11340', '11350', '11360', '11410', '11420', '11430', '11440', '11450', '11460', '11470', '11480', '11510', '11520', '11530', '11540', '11550', '11560', '11610', '11620', '11630', '11640', '11650', '11710', '11720', '11730', '11740', '11750', '11810', '11820', '11830', '11840', '11850'],
            'Jakarta Pusat': ['10110', '10120', '10130', '10140', '10150', '10160', '10210', '10220', '10230', '10240', '10250', '10260', '10270', '10310', '10320', '10330', '10340', '10350', '10410', '10420', '10430', '10440', '10450', '10460', '10470', '10510', '10520', '10530', '10540', '10550', '10560', '10570', '10610', '10620', '10630', '10640', '10650', '10660', '10710', '10720', '10730', '10740', '10750', '10760', '10770'],
            'Jakarta Selatan': ['12110', '12120', '12130', '12140', '12150', '12160', '12170', '12180', '12190', '12210', '12220', '12230', '12240', '12250', '12260', '12270', '12310', '12320', '12330', '12340', '12350', '12360', '12410', '12420', '12430', '12440', '12450', '12460', '12510', '12520', '12530', '12540', '12550', '12560', '12610', '12620', '12630', '12640', '12650', '12710', '12720', '12730', '12740', '12750', '12760', '12770', '12780', '12810', '12820', '12830', '12840', '12850', '12860', '12870', '12910', '12920', '12930', '12940', '12950', '12960', '12970', '12980'],
            'Jakarta Timur': ['13110', '13120', '13130', '13140', '13150', '13160', '13210', '13220', '13230', '13240', '13250', '13260', '13270', '13310', '13320', '13330', '13340', '13350', '13410', '13420', '13430', '13440', '13450', '13460', '13470', '13510', '13520', '13530', '13540', '13550', '13560', '13570', '13610', '13620', '13630', '13640', '13650', '13660', '13710', '13720', '13730', '13740', '13750', '13760', '13770', '13810', '13820', '13830', '13840', '13850', '13860', '13870', '13910', '13920', '13930', '13940', '13950', '13960'],
            'Jakarta Utara': ['14110', '14120', '14130', '14140', '14150', '14160', '14210', '14220', '14230', '14240', '14250', '14260', '14270', '14310', '14320', '14330', '14340', '14350', '14410', '14420', '14430', '14440', '14450', '14460', '14470', '14510', '14520', '14530', '14540', '14550'],
            'Bandung': ['40111', '40112', '40113', '40114', '40115', '40116', '40117', '40121', '40122', '40123', '40124', '40131', '40132', '40133', '40141', '40142', '40143', '40151', '40152', '40153', '40161', '40162', '40163', '40164', '40171', '40172', '40173', '40174', '40181', '40182', '40183', '40184', '40191', '40192', '40193', '40194', '40211', '40212', '40213', '40214', '40215', '40216', '40221', '40222', '40223', '40224', '40225', '40226', '40231', '40232', '40233', '40234', '40241', '40242', '40243', '40251', '40252', '40253', '40254', '40261', '40262', '40263', '40264', '40271', '40272', '40281', '40282', '40283', '40284', '40285', '40286', '40287', '40288', '40289', '40291', '40292', '40293', '40294', '40295'],
            'Semarang': ['50111', '50112', '50113', '50114', '50115', '50116', '50117', '50118', '50119', '50121', '50122', '50123', '50124', '50125', '50126', '50127', '50128', '50131', '50132', '50133', '50134', '50135', '50136', '50137', '50138', '50139', '50141', '50142', '50143', '50144', '50145', '50146', '50147', '50148', '50149', '50151', '50161', '50162', '50163', '50164', '50171', '50172', '50173', '50174', '50175', '50176', '50177', '50178', '50179', '50181', '50182', '50183', '50184', '50185', '50186', '50187', '50188', '50189', '50191', '50192', '50193', '50194', '50195', '50196', '50197', '50198', '50199', '50211', '50212', '50213', '50214', '50229', '50241', '50242', '50243', '50244', '50245', '50246', '50247', '50248', '50249', '50251', '50252', '50253', '50254', '50255', '50256', '50257', '50258', '50259', '50261', '50262', '50263', '50264', '50265', '50271', '50272', '50273', '50274', '50275'],
            'Surabaya': ['60111', '60112', '60113', '60114', '60115', '60116', '60117', '60118', '60119', '60121', '60122', '60123', '60124', '60125', '60131', '60132', '60133', '60134', '60135', '60136', '60137', '60138', '60139', '60141', '60142', '60143', '60144', '60145', '60146', '60147', '60148', '60151', '60152', '60153', '60154', '60155', '60156', '60157', '60161', '60162', '60171', '60172', '60173', '60174', '60175', '60176', '60177', '60178', '60179', '60181', '60182', '60183', '60184', '60185', '60186', '60187', '60188', '60189', '60191', '60192', '60193', '60194', '60195', '60196', '60197', '60198', '60199', '60211', '60212', '60213', '60214', '60215', '60216', '60217', '60218', '60219', '60221', '60222', '60223', '60224', '60225', '60226', '60227', '60228', '60229', '60231', '60232', '60233', '60234', '60235', '60236', '60237', '60238', '60239', '60241', '60242', '60243', '60244', '60245', '60246', '60247', '60248', '60249', '60251', '60252', '60253', '60254', '60255', '60256', '60257', '60258', '60259', '60261', '60262', '60263', '60264', '60265', '60266', '60267', '60268', '60269', '60271', '60272', '60273', '60274', '60275', '60276', '60277', '60278', '60281', '60282', '60283', '60284', '60285', '60286', '60287', '60288', '60289', '60291', '60292', '60293', '60294', '60295', '60296', '60297', '60298', '60299'],
            'Medan': ['20111', '20112', '20113', '20114', '20115', '20116', '20117', '20118', '20119', '20121', '20122', '20123', '20124', '20125', '20126', '20127', '20128', '20129', '20131', '20132', '20133', '20134', '20135', '20136', '20137', '20138', '20139', '20141', '20142', '20143', '20144', '20145', '20146', '20147', '20148', '20149', '20151', '20152', '20153', '20154', '20155', '20156', '20157', '20158', '20211', '20212', '20213', '20214', '20215', '20216', '20217', '20218', '20219', '20221', '20222', '20223', '20224', '20225', '20226', '20227', '20228', '20229', '20231', '20232', '20233', '20234', '20235', '20236', '20237', '20238', '20239', '20241', '20242', '20243', '20244', '20245', '20371', '20372'],
            'Makassar': ['90111', '90112', '90113', '90114', '90115', '90116', '90117', '90118', '90119', '90121', '90122', '90123', '90124', '90125', '90126', '90127', '90128', '90129', '90131', '90132', '90133', '90134', '90135', '90136', '90137', '90138', '90139', '90141', '90142', '90143', '90144', '90145', '90146', '90147', '90148', '90149', '90151', '90152', '90153', '90154', '90155', '90156', '90157', '90158', '90159', '90161', '90162', '90163', '90164', '90165', '90166', '90167', '90168', '90169', '90171', '90172', '90173', '90174', '90175', '90176', '90177', '90178', '90179', '90181', '90182', '90183', '90184', '90185', '90186', '90187', '90188', '90189', '90191', '90192', '90193', '90194', '90195', '90196', '90197', '90198', '90199', '90211', '90212', '90213', '90214', '90215', '90216', '90217', '90218', '90221', '90222', '90223', '90224', '90225', '90226', '90227', '90228', '90229', '90231', '90232', '90233', '90234', '90235', '90236', '90237', '90241', '90242', '90243', '90244', '90245', '90251', '90252', '90253', '90254', '90255'],
            'Palembang': ['30111', '30112', '30113', '30114', '30115', '30116', '30117', '30118', '30119', '30121', '30122', '30123', '30124', '30125', '30126', '30127', '30128', '30129', '30131', '30132', '30133', '30134', '30135', '30136', '30137', '30138', '30139', '30141', '30142', '30143', '30144', '30145', '30146', '30147', '30148', '30149', '30151', '30152', '30153', '30154', '30155', '30156', '30157', '30158', '30159', '30161', '30162', '30163', '30164', '30165', '30166', '30167', '30168', '30169', '30171', '30172', '30173', '30174', '30175', '30176', '30177', '30178', '30179', '30181', '30182', '30183', '30184', '30185', '30186', '30187', '30188', '30189', '30191', '30192', '30193', '30194', '30195', '30196', '30197', '30198', '30199', '30211', '30212', '30213', '30214', '30215', '30216', '30217', '30218', '30219', '30221', '30222', '30223', '30224', '30225', '30226', '30227', '30228', '30229', '30231', '30232', '30233', '30234', '30235', '30236', '30237', '30238', '30239', '30241', '30242', '30243', '30244', '30245', '30246', '30247', '30248', '30249', '30251', '30252', '30253', '30254', '30255', '30256', '30257', '30258', '30259', '30961', '30962', '30963', '30964', '30965', '30966', '30967', '30968', '30969'],
            'Tangerang': ['15111', '15112', '15113', '15114', '15115', '15116', '15117', '15118', '15119', '15121', '15122', '15123', '15124', '15125', '15126', '15127', '15128', '15129', '15131', '15132', '15133', '15134', '15135', '15136', '15137', '15138', '15139', '15141', '15142', '15143', '15144', '15145', '15146', '15147', '15148', '15149', '15151', '15152', '15153', '15154', '15155', '15156', '15157', '15158', '15159', '15161', '15162', '15163', '15164', '15165', '15166', '15167', '15168', '15169', '15171', '15172', '15173', '15174', '15175', '15176', '15177', '15178', '15179', '15181', '15182', '15183', '15184', '15185', '15186', '15187', '15188', '15189', '15191', '15192', '15193', '15194', '15195', '15196', '15197', '15198', '15199', '15211', '15212', '15213', '15214', '15215', '15216', '15217', '15218', '15219', '15221', '15222', '15223', '15224', '15225', '15226', '15227', '15228', '15229', '15310', '15311', '15312', '15313', '15314', '15315', '15316', '15317', '15318', '15319', '15321', '15322', '15323', '15324', '15325', '15326', '15327', '15328', '15329', '15331', '15332', '15333', '15334', '15335', '15336', '15337', '15338', '15339', '15341', '15342', '15343', '15344', '15345', '15346', '15347', '15348', '15349', '15410', '15411', '15412', '15413', '15414', '15415', '15416', '15417', '15418', '15419', '15421', '15422', '15423', '15424', '15425', '15426', '15427', '15428', '15429', '15510', '15511', '15512', '15513', '15514', '15515', '15516', '15517', '15518', '15519', '15521', '15522', '15523', '15524', '15525', '15526', '15527', '15528', '15529', '15531', '15532', '15533', '15534', '15535', '15536', '15537', '15538', '15539', '15541', '15542', '15543', '15544', '15545', '15546', '15547', '15548', '15549', '15561', '15562', '15563', '15564', '15565', '15566', '15567', '15568', '15569', '15610', '15611', '15612', '15613', '15614', '15615', '15616', '15617', '15618', '15619', '15710', '15711', '15712', '15713', '15714', '15715', '15716', '15717', '15718', '15719', '15810', '15811', '15812', '15813', '15814', '15815', '15816', '15817', '15818', '15819', '15820', '15821', '15822', '15823', '15824', '15825', '15826', '15827', '15828', '15829'],
            'Bekasi': ['17111', '17112', '17113', '17114', '17115', '17116', '17117', '17118', '17119', '17121', '17122', '17123', '17124', '17125', '17126', '17127', '17128', '17129', '17131', '17132', '17133', '17134', '17135', '17136', '17137', '17138', '17139', '17141', '17142', '17143', '17144', '17145', '17146', '17147', '17148', '17149', '17151', '17152', '17153', '17154', '17155', '17156', '17157', '17158', '17159', '17211', '17212', '17213', '17214', '17215', '17216', '17217', '17218', '17219', '17221', '17222', '17223', '17224', '17225', '17226', '17227', '17228', '17229', '17231', '17232', '17233', '17234', '17235', '17236', '17237', '17238', '17239', '17411', '17412', '17413', '17414', '17415', '17416', '17417', '17418', '17419', '17421', '17422', '17423', '17424', '17425', '17426', '17427', '17428', '17429', '17431', '17432', '17433', '17434', '17435', '17436', '17437', '17438', '17439', '17510', '17511', '17512', '17513', '17514', '17515', '17516', '17517', '17518', '17519', '17520', '17521', '17522', '17523', '17524', '17525', '17526', '17527', '17528', '17529', '17530', '17531', '17532', '17533', '17534', '17535', '17536', '17537', '17538', '17539', '17540', '17541', '17542', '17543', '17544', '17545', '17546', '17547', '17548', '17549', '17550', '17551', '17552', '17553', '17554', '17555', '17556', '17557', '17558', '17559', '17610', '17611', '17612', '17613', '17614', '17615', '17616', '17617', '17618', '17619', '17620', '17621', '17622', '17623', '17624', '17625', '17626', '17627', '17628', '17629', '17630', '17631', '17632', '17633', '17634', '17635', '17636', '17637', '17638', '17639', '17640', '17641', '17642', '17643', '17644', '17645', '17646', '17647', '17648', '17649', '17710', '17711', '17712', '17713', '17714', '17715', '17716', '17717', '17718', '17719', '17720', '17721', '17722', '17723', '17724', '17725', '17726', '17727', '17728', '17729', '17730', '17731', '17732', '17733', '17734', '17735', '17736', '17737', '17738', '17739', '17740', '17741', '17742', '17743', '17744', '17745', '17746', '17747', '17748', '17749', '17820', '17821', '17822', '17823', '17824', '17825', '17826', '17827', '17828', '17829', '17830', '17831', '17832', '17833', '17834', '17835', '17836', '17837', '17838', '17839', '17910', '17911', '17912', '17913', '17914', '17915', '17916', '17917', '17918', '17919', '17920', '17921', '17922', '17923', '17924', '17925', '17926', '17927', '17928', '17929'],
            'Depok': ['16411', '16412', '16413', '16414', '16415', '16416', '16417', '16418', '16419', '16421', '16422', '16423', '16424', '16425', '16426', '16427', '16428', '16429', '16431', '16432', '16433', '16434', '16435', '16436', '16437', '16438', '16439', '16441', '16442', '16443', '16444', '16445', '16446', '16447', '16448', '16449', '16451', '16452', '16453', '16454', '16455', '16456', '16457', '16458', '16459', '16511', '16512', '16513', '16514', '16515', '16516', '16517', '16518', '16519', '16521', '16522', '16523', '16524', '16525', '16526', '16527', '16528', '16529', '16531', '16532', '16533', '16534', '16535', '16536', '16537', '16538', '16539', '16911', '16912', '16913', '16914', '16915', '16916', '16917', '16918', '16919', '16921', '16922', '16923', '16924', '16925', '16926', '16927', '16928', '16929'],
            'Bogor': ['16111', '16112', '16113', '16114', '16115', '16116', '16117', '16118', '16119', '16121', '16122', '16123', '16124', '16125', '16126', '16127', '16128', '16129', '16131', '16132', '16133', '16134', '16135', '16136', '16137', '16138', '16139', '16141', '16142', '16143', '16144', '16145', '16146', '16147', '16148', '16149', '16151', '16152', '16153', '16154', '16155', '16156', '16157', '16158', '16159', '16161', '16162', '16163', '16164', '16165', '16166', '16167', '16168', '16169', '16320', '16321', '16322', '16323', '16324', '16325', '16326', '16327', '16328', '16329', '16330', '16331', '16332', '16333', '16334', '16335', '16336', '16337', '16338', '16339', '16340', '16341', '16342', '16343', '16344', '16345', '16346', '16347', '16348', '16349', '16350', '16351', '16352', '16353', '16354', '16355', '16356', '16357', '16358', '16359', '16610', '16611', '16612', '16613', '16614', '16615', '16616', '16617', '16618', '16619', '16620', '16621', '16622', '16623', '16624', '16625', '16626', '16627', '16628', '16629', '16630', '16631', '16632', '16633', '16634', '16635', '16636', '16637', '16638', '16639', '16710', '16711', '16712', '16713', '16714', '16715', '16716', '16717', '16718', '16719', '16720', '16721', '16722', '16723', '16724', '16725', '16726', '16727', '16728', '16729', '16730', '16731', '16732', '16733', '16734', '16735', '16736', '16737', '16738', '16739', '16740', '16741', '16742', '16743', '16744', '16745', '16746', '16747', '16748', '16749', '16810', '16811', '16812', '16813', '16814', '16815', '16816', '16817', '16818', '16819', '16820', '16821', '16822', '16823', '16824', '16825', '16826', '16827', '16828', '16829'],
            'Batam': ['29111', '29112', '29113', '29114', '29115', '29116', '29117', '29118', '29119', '29121', '29122', '29123', '29124', '29125', '29126', '29127', '29128', '29129', '29131', '29132', '29133', '29134', '29135', '29136', '29137', '29138', '29139', '29141', '29142', '29143', '29144', '29145', '29146', '29147', '29148', '29149', '29151', '29152', '29153', '29154', '29155', '29156', '29157', '29158', '29159', '29424', '29425', '29426', '29427', '29428', '29429', '29432', '29433', '29434', '29435', '29436', '29437', '29438', '29439', '29442', '29443', '29444', '29445', '29446', '29447', '29448', '29449', '29452', '29453', '29454', '29455', '29456', '29457', '29458', '29459', '29461', '29462', '29463', '29464', '29465', '29466', '29467', '29468', '29469'],
            'Pekanbaru': ['28111', '28112', '28113', '28114', '28115', '28116', '28117', '28118', '28119', '28121', '28122', '28123', '28124', '28125', '28126', '28127', '28128', '28129', '28131', '28132', '28133', '28134', '28135', '28136', '28137', '28138', '28139', '28141', '28142', '28143', '28144', '28145', '28146', '28147', '28148', '28149', '28151', '28152', '28153', '28154', '28155', '28156', '28157', '28158', '28159', '28161', '28162', '28163', '28164', '28165', '28166', '28167', '28168', '28169', '28281', '28282', '28283', '28284', '28285', '28286', '28287', '28288', '28289', '28291', '28292', '28293', '28294', '28295', '28296', '28297', '28298', '28299'],
            'Bandar Lampung': ['35111', '35112', '35113', '35114', '35115', '35116', '35117', '35118', '35119', '35121', '35122', '35123', '35124', '35125', '35126', '35127', '35128', '35129', '35131', '35132', '35133', '35134', '35135', '35136', '35137', '35138', '35139', '35141', '35142', '35143', '35144', '35145', '35146', '35147', '35148', '35149', '35151', '35152', '35153', '35154', '35155', '35156', '35157', '35158', '35159', '35211', '35212', '35213', '35214', '35215', '35216', '35217', '35218', '35219', '35221', '35222', '35223', '35224', '35225', '35226', '35227', '35228', '35229', '35231', '35232', '35233', '35234', '35235', '35236', '35237', '35238', '35239', '35241', '35242', '35243', '35244', '35245', '35246', '35247', '35248', '35249', '35251', '35252', '35253', '35254', '35255', '35256', '35257', '35258', '35259', '35361', '35362', '35363', '35364', '35365', '35366', '35367', '35368', '35369', '35371', '35372', '35373', '35374', '35375', '35376', '35377', '35378', '35379', '35381', '35382', '35383', '35384', '35385', '35386', '35387', '35388', '35389'],
            'Padang': ['25111', '25112', '25113', '25114', '25115', '25116', '25117', '25118', '25119', '25121', '25122', '25123', '25124', '25125', '25126', '25127', '25128', '25129', '25131', '25132', '25133', '25134', '25135', '25136', '25137', '25138', '25139', '25141', '25142', '25143', '25144', '25145', '25146', '25147', '25148', '25149', '25151', '25152', '25153', '25154', '25155', '25156', '25157', '25158', '25159', '25161', '25162', '25163', '25164', '25165', '25166', '25167', '25168', '25169', '25171', '25172', '25173', '25174', '25175', '25176', '25177', '25178', '25179', '25211', '25212', '25213', '25214', '25215', '25216', '25217', '25218', '25219', '25221', '25222', '25223', '25224', '25225', '25226', '25227', '25228', '25229'],
            'Malang': ['65111', '65112', '65113', '65114', '65115', '65116', '65117', '65118', '65119', '65121', '65122', '65123', '65124', '65125', '65126', '65127', '65128', '65129', '65131', '65132', '65133', '65134', '65135', '65136', '65137', '65138', '65139', '65141', '65142', '65143', '65144', '65145', '65146', '65147', '65148', '65149', '65151', '65152', '65153', '65154', '65155', '65156', '65157', '65158', '65159', '65161', '65162', '65163', '65164', '65165', '65166', '65167', '65168', '65169', '65311', '65312', '65313', '65314', '65315', '65316', '65317', '65318', '65319', '65321', '65322', '65323', '65324', '65325', '65326', '65327', '65328', '65329'],
            'Samarinda': ['75111', '75112', '75113', '75114', '75115', '75116', '75117', '75118', '75119', '75121', '75122', '75123', '75124', '75125', '75126', '75127', '75128', '75129', '75131', '75132', '75133', '75134', '75135', '75136', '75137', '75138', '75139', '75141', '75142', '75143', '75144', '75145', '75146', '75147', '75148', '75149', '75251', '75252', '75253', '75254', '75255', '75256', '75257', '75258', '75259', '75261', '75262', '75263', '75264', '75265', '75266', '75267', '75268', '75269', '75271', '75272', '75273', '75274', '75275', '75276', '75277', '75278', '75279', '75281', '75282', '75283', '75284', '75285', '75286', '75287', '75288', '75289'],
            'Balikpapan': ['76111', '76112', '76113', '76114', '76115', '76116', '76117', '76118', '76119', '76121', '76122', '76123', '76124', '76125', '76126', '76127', '76128', '76129', '76131', '76132', '76133', '76134', '76135', '76136', '76137', '76138', '76139', '76141', '76142', '76143', '76144', '76145', '76146', '76147', '76148', '76149', '76211', '76212', '76213', '76214', '76215', '76216', '76217', '76218', '76219', '76221', '76222', '76223', '76224', '76225', '76226', '76227', '76228', '76229'],
            'Pontianak': ['78111', '78112', '78113', '78114', '78115', '78116', '78117', '78118', '78119', '78121', '78122', '78123', '78124', '78125', '78126', '78127', '78128', '78129', '78131', '78132', '78133', '78134', '78135', '78136', '78137', '78138', '78139', '78211', '78212', '78213', '78214', '78215', '78216', '78217', '78218', '78219', '78221', '78222', '78223', '78224', '78225', '78226', '78227', '78228', '78229', '78231', '78232', '78233', '78234', '78235', '78236', '78237', '78238', '78239', '78241', '78242', '78243', '78244', '78245', '78246', '78247', '78248', '78249'],
            'Manado': ['95111', '95112', '95113', '95114', '95115', '95116', '95117', '95118', '95119', '95121', '95122', '95123', '95124', '95125', '95126', '95127', '95128', '95129', '95131', '95132', '95133', '95134', '95135', '95136', '95137', '95138', '95139', '95211', '95212', '95213', '95214', '95215', '95216', '95217', '95218', '95219', '95221', '95222', '95223', '95224', '95225', '95226', '95227', '95228', '95229', '95231', '95232', '95233', '95234', '95235', '95236', '95237', '95238', '95239', '95241', '95242', '95243', '95244', '95245', '95246', '95247', '95248', '95249', '95251', '95252', '95253', '95254', '95255', '95256', '95257', '95258', '95259'],
            'Bantul': ['55711', '55712', '55713', '55714', '55715', '55716', '55717', '55718', '55719', '55751', '55752', '55753', '55754', '55761', '55762', '55763', '55764', '55771', '55772', '55773', '55774', '55781', '55782', '55783', '55784', '55791', '55792', '55793', '55794'],
            'Sleman': ['55511', '55512', '55513', '55514', '55515', '55551', '55552', '55553', '55554', '55561', '55562', '55563', '55564', '55571', '55572', '55573', '55574', '55581', '55582', '55583', '55584', '55591', '55592', '55593', '55594'],
            'Gunung Kidul': ['55811', '55812', '55813', '55814', '55815', '55816', '55817', '55818', '55819', '55821', '55822', '55823', '55831', '55832', '55833', '55841', '55842', '55843', '55851', '55852', '55853', '55861', '55862', '55863', '55871', '55872', '55873', '55881', '55882', '55883', '55891', '55892', '55893'],
            'Kulon Progo': ['55611', '55612', '55613', '55614', '55615', '55616', '55617', '55618', '55619', '55621', '55622', '55623', '55624', '55631', '55632', '55633', '55634', '55641', '55642', '55643', '55644', '55651', '55652', '55653', '55654', '55661', '55662', '55663', '55664', '55671', '55672', '55673', '55681', '55682', '55683', '55691', '55692', '55693'],
            'Cilegon': ['42411', '42412', '42413', '42414', '42415', '42416', '42417', '42418', '42419', '42421', '42422', '42423', '42424', '42425', '42426', '42427', '42428', '42429', '42431', '42432', '42433', '42434', '42435', '42436', '42437', '42438', '42439', '42441', '42442', '42443', '42444', '42445', '42446', '42447', '42448', '42449'],
            'Serang': ['42111', '42112', '42113', '42114', '42115', '42116', '42117', '42118', '42119', '42121', '42122', '42123', '42124', '42125', '42126', '42127', '42128', '42129', '42131', '42132', '42133', '42134', '42135', '42136', '42137', '42138', '42139', '42141', '42142', '42143', '42144', '42145', '42146', '42147', '42148', '42149', '42151', '42152', '42153', '42154', '42155', '42156', '42157', '42158', '42159', '42161', '42162', '42163', '42164', '42165', '42166', '42167', '42168', '42169'],
            'Tangerang Selatan': ['15411', '15412', '15413', '15414', '15415', '15416', '15417', '15418', '15419', '15421', '15422', '15423', '15424', '15425', '15426', '15427', '15428', '15429', '15431', '15432', '15433', '15434', '15435', '15436', '15437', '15438', '15439', '15441', '15442', '15443', '15444', '15445', '15446', '15447', '15448', '15449', '15451', '15452', '15453', '15454', '15455', '15456', '15457', '15458', '15459', '15461', '15462', '15463', '15464', '15465', '15466', '15467', '15468', '15469'],
            'Cimahi': ['40511', '40512', '40513', '40514', '40515', '40516', '40517', '40518', '40519', '40521', '40522', '40523', '40524', '40525', '40526', '40527', '40528', '40529', '40531', '40532', '40533', '40534', '40535', '40536', '40537', '40538', '40539'],
            'Cirebon': ['45111', '45112', '45113', '45114', '45115', '45116', '45117', '45118', '45119', '45121', '45122', '45123', '45124', '45125', '45126', '45127', '45128', '45129', '45131', '45132', '45133', '45134', '45135', '45136', '45137', '45138', '45139', '45141', '45142', '45143', '45144', '45145', '45146', '45147', '45148', '45149'],
            'Sukabumi': ['43111', '43112', '43113', '43114', '43115', '43116', '43117', '43118', '43119', '43121', '43122', '43123', '43124', '43125', '43126', '43127', '43128', '43129', '43131', '43132', '43133', '43134', '43135', '43136', '43137', '43138', '43139'],
            'Tasikmalaya': ['46111', '46112', '46113', '46114', '46115', '46116', '46117', '46118', '46119', '46121', '46122', '46123', '46124', '46125', '46126', '46127', '46128', '46129', '46131', '46132', '46133', '46134', '46135', '46136', '46137', '46138', '46139', '46141', '46142', '46143', '46144', '46145', '46146', '46147', '46148', '46149'],
            'Magelang': ['56111', '56112', '56113', '56114', '56115', '56116', '56117', '56118', '56119', '56121', '56122', '56123', '56124', '56125', '56126', '56127', '56128', '56129', '56131', '56132', '56133', '56134', '56135', '56136', '56137', '56138', '56139'],
            'Salatiga': ['50711', '50712', '50713', '50714', '50715', '50716', '50717', '50718', '50719', '50721', '50722', '50723', '50724', '50725', '50726', '50727', '50728', '50729'],
            'Surakarta': ['57111', '57112', '57113', '57114', '57115', '57116', '57117', '57118', '57119', '57121', '57122', '57123', '57124', '57125', '57126', '57127', '57128', '57129', '57131', '57132', '57133', '57134', '57135', '57136', '57137', '57138', '57139', '57141', '57142', '57143', '57144', '57145', '57146', '57147', '57148', '57149', '57151', '57152', '57153', '57154', '57155', '57156', '57157', '57158', '57159'],
            'Tegal': ['52111', '52112', '52113', '52114', '52115', '52116', '52117', '52118', '52119', '52121', '52122', '52123', '52124', '52125', '52126', '52127', '52128', '52129', '52131', '52132', '52133', '52134', '52135', '52136', '52137', '52138', '52139'],
            'Pekalongan': ['51111', '51112', '51113', '51114', '51115', '51116', '51117', '51118', '51119', '51121', '51122', '51123', '51124', '51125', '51126', '51127', '51128', '51129', '51131', '51132', '51133', '51134', '51135', '51136', '51137', '51138', '51139'],
            'Batu': ['65311', '65312', '65313', '65314', '65315', '65316', '65317', '65318', '65319', '65321', '65322', '65323', '65324', '65325', '65326', '65327', '65328', '65329'],
            'Blitar': ['66111', '66112', '66113', '66114', '66115', '66116', '66117', '66118', '66119', '66121', '66122', '66123', '66124', '66125', '66126', '66127', '66128', '66129', '66131', '66132', '66133', '66134', '66135', '66136', '66137', '66138', '66139'],
            'Kediri': ['64111', '64112', '64113', '64114', '64115', '64116', '64117', '64118', '64119', '64121', '64122', '64123', '64124', '64125', '64126', '64127', '64128', '64129', '64131', '64132', '64133', '64134', '64135', '64136', '64137', '64138', '64139'],
            'Madiun': ['63111', '63112', '63113', '63114', '63115', '63116', '63117', '63118', '63119', '63121', '63122', '63123', '63124', '63125', '63126', '63127', '63128', '63129', '63131', '63132', '63133', '63134', '63135', '63136', '63137', '63138', '63139'],
            'Mojokerto': ['61311', '61312', '61313', '61314', '61315', '61316', '61317', '61318', '61319', '61321', '61322', '61323', '61324', '61325', '61326', '61327', '61328', '61329'],
            'Pasuruan': ['67111', '67112', '67113', '67114', '67115', '67116', '67117', '67118', '67119', '67121', '67122', '67123', '67124', '67125', '67126', '67127', '67128', '67129', '67131', '67132', '67133', '67134', '67135', '67136', '67137', '67138', '67139'],
            'Probolinggo': ['67211', '67212', '67213', '67214', '67215', '67216', '67217', '67218', '67219', '67221', '67222', '67223', '67224', '67225', '67226', '67227', '67228', '67229'],
            'Sidoarjo': ['61211', '61212', '61213', '61214', '61215', '61216', '61217', '61218', '61219', '61221', '61222', '61223', '61224', '61225', '61226', '61227', '61228', '61229', '61231', '61232', '61233', '61234', '61235', '61236', '61237', '61238', '61239', '61241', '61242', '61243', '61244', '61245', '61246', '61247', '61248', '61249', '61251', '61252', '61253', '61254', '61255', '61256', '61257', '61258', '61259', '61261', '61262', '61263', '61264', '61265', '61266', '61267', '61268', '61269', '61271', '61272', '61273', '61274', '61275', '61276', '61277', '61278', '61279'],
            'Gresik': ['61111', '61112', '61113', '61114', '61115', '61116', '61117', '61118', '61119', '61121', '61122', '61123', '61124', '61125', '61126', '61127', '61128', '61129', '61131', '61132', '61133', '61134', '61135', '61136', '61137', '61138', '61139', '61141', '61142', '61143', '61144', '61145', '61146', '61147', '61148', '61149', '61151', '61152', '61153', '61154', '61155', '61156', '61157', '61158', '61159', '61161', '61162', '61163', '61164', '61165', '61166', '61167', '61168', '61169', '61171', '61172', '61173', '61174', '61175', '61176', '61177', '61178', '61179'],
            // Kode Pos Kabupaten Jawa Barat
            'Kabupaten Bogor': ['16310', '16320', '16330', '16340', '16350', '16360', '16370', '16610', '16620', '16630', '16640', '16650', '16660', '16710', '16720', '16730', '16740', '16750', '16810', '16820', '16830', '16840', '16850'],
            'Kabupaten Bandung': ['40311', '40312', '40313', '40314', '40315', '40316', '40317', '40318', '40319', '40391', '40392', '40393', '40394', '40395', '40396', '40397', '40398', '40399', '40551', '40552', '40553', '40554', '40555', '40556', '40557', '40558', '40559', '40561', '40562', '40563', '40564', '40565', '40566', '40567', '40568', '40569', '40911', '40912', '40913', '40914', '40915', '40916', '40917', '40918', '40919'],
            'Kabupaten Bekasi': ['17510', '17520', '17530', '17540', '17550', '17560', '17610', '17620', '17630', '17640', '17650', '17710', '17720', '17730', '17740', '17750', '17820', '17830', '17840', '17850', '17910', '17920', '17930', '17940', '17950'],
            'Kabupaten Cirebon': ['45153', '45154', '45155', '45156', '45157', '45158', '45159', '45161', '45162', '45163', '45164', '45165', '45166', '45167', '45168', '45169', '45171', '45172', '45173', '45174', '45175', '45176', '45177', '45178', '45179', '45181', '45182', '45183', '45184', '45185', '45186', '45187', '45188', '45189'],
            'Kabupaten Sukabumi': ['43151', '43152', '43153', '43154', '43155', '43156', '43157', '43158', '43159', '43161', '43162', '43163', '43164', '43165', '43166', '43167', '43168', '43169', '43171', '43172', '43173', '43174', '43175', '43176', '43177', '43178', '43179', '43181', '43182', '43183', '43184', '43185', '43186', '43187', '43188', '43189'],
            'Kabupaten Tasikmalaya': ['46151', '46152', '46153', '46154', '46155', '46156', '46157', '46158', '46159', '46171', '46172', '46173', '46174', '46175', '46176', '46177', '46178', '46179', '46181', '46182', '46183', '46184', '46185', '46186', '46187', '46188', '46189', '46191', '46192', '46193', '46194', '46195', '46196', '46197', '46198', '46199'],
            'Bandung Barat': ['40721', '40722', '40723', '40724', '40725', '40726', '40727', '40728', '40729', '40751', '40752', '40753', '40754', '40755', '40756', '40757', '40758', '40759', '40761', '76762', '40763', '40764', '40765', '40766', '40767', '40768', '40769'],
            'Ciamis': ['46211', '46212', '46213', '46214', '46215', '46216', '46217', '46218', '46219', '46251', '46252', '46253', '46254', '46255', '46256', '46257', '46258', '46259', '46261', '46262', '46263', '46264', '46265', '46266', '46267', '46268', '46269'],
            'Cianjur': ['43211', '43212', '43213', '43214', '43215', '43216', '43217', '43218', '43219', '43251', '43252', '43253', '43254', '43255', '43256', '43257', '43258', '43259', '43261', '43262', '43263', '43264', '43265', '43266', '43267', '43268', '43269'],
            'Garut': ['44111', '44112', '44113', '44114', '44115', '44116', '44117', '44118', '44119', '44151', '44152', '44153', '44154', '44155', '44156', '44157', '44158', '44159', '44161', '44162', '44163', '44164', '44165', '44166', '44167', '44168', '44169'],
            'Indramayu': ['45211', '45212', '45213', '45214', '45215', '45216', '45217', '45218', '45219', '45251', '45252', '45253', '45254', '45255', '45256', '45257', '45258', '45259', '45261', '45262', '45263', '45264', '45265', '45266', '45267', '45268', '45269'],
            'Karawang': ['41311', '41312', '41313', '41314', '41315', '41316', '41317', '41318', '41319', '41351', '41352', '41353', '41354', '41355', '41356', '41357', '41358', '41359', '41361', '41362', '41363', '41364', '41365', '41366', '41367', '41368', '41369'],
            'Kuningan': ['45511', '45512', '45513', '45514', '45515', '45516', '45517', '45518', '45519', '45551', '45552', '45553', '45554', '45555', '45556', '45557', '45558', '45559', '45561', '45562', '45563', '45564', '45565', '45566', '45567', '45568', '45569'],
            'Majalengka': ['45411', '45412', '45413', '45414', '45415', '45416', '45417', '45418', '45419', '45451', '45452', '45453', '45454', '45455', '45456', '45457', '45458', '45459', '45461', '45462', '45463', '45464', '45465', '45466', '45467', '45468', '45469'],
            'Pangandaran': ['46351', '46352', '46353', '46354', '46355', '46356', '46357', '46358', '46359', '46361', '46362', '46363', '46364', '46365', '46366', '46367', '46368', '46369'],
            'Purwakarta': ['41111', '41112', '41113', '41114', '41115', '41116', '41117', '41118', '41119', '41151', '41152', '41153', '41154', '41155', '41156', '41157', '41158', '41159', '41161', '41162', '41163', '41164', '41165', '41166', '41167', '41168', '41169'],
            'Subang': ['41211', '41212', '41213', '41214', '41215', '41216', '41217', '41218', '41219', '41251', '41252', '41253', '41254', '41255', '41256', '41257', '41258', '41259', '41261', '41262', '41263', '41264', '41265', '41266', '41267', '41268', '41269'],
            'Sumedang': ['45311', '45312', '45313', '45314', '45315', '45316', '45317', '45318', '45319', '45351', '45352', '45353', '45354', '45355', '45356', '45357', '45358', '45359', '45361', '45362', '45363', '45364', '45365', '45366', '45367', '45368', '45369'],
            'Banjar': ['46311', '46312', '46313', '46314', '46315', '46316', '46317', '46318', '46319', '46321', '46322', '46323', '46324', '46325', '46326', '46327', '46328', '46329'],
            // Kode Pos Kabupaten Banten
            'Kabupaten Serang': ['42182', '42183', '42184', '42185', '42186', '42187', '42188', '42189', '42191', '42192', '42193', '42194', '42195', '42196', '42197', '42198', '42199', '42211', '42212', '42213', '42214', '42215', '42216', '42217', '42218', '42219'],
            'Kabupaten Tangerang': ['15311', '15312', '15313', '15314', '15315', '15316', '15317', '15318', '15319', '15331', '15332', '15333', '15334', '15335', '15336', '15337', '15338', '15339', '15610', '15620', '15630', '15640', '15650', '15710', '15720', '15730'],
            'Lebak': ['42311', '42312', '42313', '42314', '42315', '42316', '42317', '42318', '42319', '42351', '42352', '42353', '42354', '42355', '42356', '42357', '42358', '42359', '42361', '42362', '42363', '42364', '42365', '42366', '42367', '42368', '42369'],
            'Pandeglang': ['42211', '42212', '42213', '42214', '42215', '42216', '42217', '42218', '42219', '42251', '42252', '42253', '42254', '42255', '42256', '42257', '42258', '42259', '42261', '42262', '42263', '42264', '42265', '42266', '42267', '42268', '42269'],
            // Kode Pos Kabupaten Jawa Tengah
            'Kabupaten Magelang': ['56111', '56112', '56113', '56114', '56115', '56116', '56117', '56118', '56119', '56151', '56152', '56153', '56154', '56155', '56156', '56157', '56158', '56159', '56161', '56162', '56163', '56164', '56165', '56166', '56167', '56168', '56169'],
            'Kabupaten Pekalongan': ['51161', '51162', '51163', '51164', '51165', '51166', '51167', '51168', '51169', '51171', '51172', '51173', '51174', '51175', '51176', '51177', '51178', '51179', '51181', '51182', '51183', '51184', '51185', '51186', '51187', '51188', '51189'],
            'Kabupaten Semarang': ['50511', '50512', '50513', '50514', '50515', '50516', '50517', '50518', '50519', '50551', '50552', '50553', '50554', '50555', '50556', '50557', '50558', '50559', '50561', '50562', '50563', '50564', '50565', '50566', '50567', '50568', '50569'],
            'Kabupaten Tegal': ['52411', '52412', '52413', '52414', '52415', '52416', '52417', '52418', '52419', '52451', '52452', '52453', '52454', '52455', '52456', '52457', '52458', '52459', '52461', '52462', '52463', '52464', '52465', '52466', '52467', '52468', '52469'],
            'Banjarnegara': ['53411', '53412', '53413', '53414', '53415', '53416', '53417', '53418', '53419', '53451', '53452', '53453', '53454', '53455', '53456', '53457', '53458', '53459', '53461', '53462', '53463', '53464', '53465', '53466', '53467', '53468', '53469'],
            'Banyumas': ['53111', '53112', '53113', '53114', '53115', '53116', '53117', '53118', '53119', '53151', '53152', '53153', '53154', '53155', '53156', '53157', '53158', '53159', '53161', '53162', '53163', '53164', '53165', '53166', '53167', '53168', '53169'],
            'Batang': ['51211', '51212', '51213', '51214', '51215', '51216', '51217', '51218', '51219', '51251', '51252', '51253', '51254', '51255', '51256', '51257', '51258', '51259', '51261', '51262', '51263', '51264', '51265', '51266', '51267', '51268', '51269'],
            'Blora': ['58211', '58212', '58213', '58214', '58215', '58216', '58217', '58218', '58219', '58251', '58252', '58253', '58254', '58255', '58256', '58257', '58258', '58259', '58261', '58262', '58263', '58264', '58265', '58266', '58267', '58268', '58269'],
            'Boyolali': ['57311', '57312', '57313', '57314', '57315', '57316', '57317', '57318', '57319', '57351', '57352', '57353', '57354', '57355', '57356', '57357', '57358', '57359', '57361', '57362', '57363', '57364', '57365', '57366', '57367', '57368', '57369'],
            'Brebes': ['52211', '52212', '52213', '52214', '52215', '52216', '52217', '52218', '52219', '52251', '52252', '52253', '52254', '52255', '52256', '52257', '52258', '52259', '52261', '52262', '52263', '52264', '52265', '52266', '52267', '52268', '52269'],
            'Cilacap': ['53211', '53212', '53213', '53214', '53215', '53216', '53217', '53218', '53219', '53251', '53252', '53253', '53254', '53255', '53256', '53257', '53258', '53259', '53261', '53262', '53263', '53264', '53265', '53266', '53267', '53268', '53269'],
            'Demak': ['59511', '59512', '59513', '59514', '59515', '59516', '59517', '59518', '59519', '59551', '59552', '59553', '59554', '59555', '59556', '59557', '59558', '59559', '59561', '59562', '59563', '59564', '59565', '59566', '59567', '59568', '59569'],
            'Grobogan': ['58111', '58112', '58113', '58114', '58115', '58116', '58117', '58118', '58119', '58151', '58152', '58153', '58154', '58155', '58156', '58157', '58158', '58159', '58161', '58162', '58163', '58164', '58165', '58166', '58167', '58168', '58169'],
            'Jepara': ['59411', '59412', '59413', '59414', '59415', '59416', '59417', '59418', '59419', '59451', '59452', '59453', '59454', '59455', '59456', '59457', '59458', '59459', '59461', '59462', '59463', '59464', '59465', '59466', '59467', '59468', '59469'],
            'Karanganyar': ['57711', '57712', '57713', '57714', '57715', '57716', '57717', '57718', '57719', '57751', '57752', '57753', '57754', '57755', '57756', '57757', '57758', '57759', '57761', '57762', '57763', '57764', '57765', '57766', '57767', '57768', '57769'],
            'Kebumen': ['54311', '54312', '54313', '54314', '54315', '54316', '54317', '54318', '54319', '54351', '54352', '54353', '54354', '54355', '54356', '54357', '54358', '54359', '54361', '54362', '54363', '54364', '54365', '54366', '54367', '54368', '54369'],
            'Kendal': ['51311', '51312', '51313', '51314', '51315', '51316', '51317', '51318', '51319', '51351', '51352', '51353', '51354', '51355', '51356', '51357', '51358', '51359', '51361', '51362', '51363', '51364', '51365', '51366', '51367', '51368', '51369'],
            'Klaten': ['57411', '57412', '57413', '57414', '57415', '57416', '57417', '57418', '57419', '57451', '57452', '57453', '57454', '57455', '57456', '57457', '57458', '57459', '57461', '57462', '57463', '57464', '57465', '57466', '57467', '57468', '57469'],
            'Kudus': ['59311', '59312', '59313', '59314', '59315', '59316', '59317', '59318', '59319', '59351', '59352', '59353', '59354', '59355', '59356', '59357', '59358', '59359', '59361', '59362', '59363', '59364', '59365', '59366', '59367', '59368', '59369'],
            'Pati': ['59111', '59112', '59113', '59114', '59115', '59116', '59117', '59118', '59119', '59151', '59152', '59153', '59154', '59155', '59156', '59157', '59158', '59159', '59161', '59162', '59163', '59164', '59165', '59166', '59167', '59168', '59169'],
            'Pemalang': ['52311', '52312', '52313', '52314', '52315', '52316', '52317', '52318', '52319', '52351', '52352', '52353', '52354', '52355', '52356', '52357', '52358', '52359', '52361', '52362', '52363', '52364', '52365', '52366', '52367', '52368', '52369'],
            'Purbalingga': ['53311', '53312', '53313', '53314', '53315', '53316', '53317', '53318', '53319', '53351', '53352', '53353', '53354', '53355', '53356', '53357', '53358', '53359', '53361', '53362', '53363', '53364', '53365', '53366', '53367', '53368', '53369'],
            'Purworejo': ['54111', '54112', '54113', '54114', '54115', '54116', '54117', '54118', '54119', '54151', '54152', '54153', '54154', '54155', '54156', '54157', '54158', '54159', '54161', '54162', '54163', '54164', '54165', '54166', '54167', '54168', '54169'],
            'Rembang': ['59211', '59212', '59213', '59214', '59215', '59216', '59217', '59218', '59219', '59251', '59252', '59253', '59254', '59255', '59256', '59257', '59258', '59259', '59261', '59262', '59263', '59264', '59265', '59266', '59267', '59268', '59269'],
            'Sragen': ['57211', '57212', '57213', '57214', '57215', '57216', '57217', '57218', '57219', '57251', '57252', '57253', '57254', '57255', '57256', '57257', '57258', '57259', '57261', '57262', '57263', '57264', '57265', '57266', '57267', '57268', '57269'],
            'Sukoharjo': ['57511', '57512', '57513', '57514', '57515', '57516', '57517', '57518', '57519', '57551', '57552', '57553', '57554', '57555', '57556', '57557', '57558', '57559', '57561', '57562', '57563', '57564', '57565', '57566', '57567', '57568', '57569'],
            'Temanggung': ['56211', '56212', '56213', '56214', '56215', '56216', '56217', '56218', '56219', '56251', '56252', '56253', '56254', '56255', '56256', '56257', '56258', '56259', '56261', '56262', '56263', '56264', '56265', '56266', '56267', '56268', '56269'],
            'Wonogiri': ['57611', '57612', '57613', '57614', '57615', '57616', '57617', '57618', '57619', '57651', '57652', '57653', '57654', '57655', '57656', '57657', '57658', '57659', '57661', '57662', '57663', '57664', '57665', '57666', '57667', '57668', '57669'],
            'Wonosobo': ['56311', '56312', '56313', '56314', '56315', '56316', '56317', '56318', '56319', '56351', '56352', '56353', '56354', '56355', '56356', '56357', '56358', '56359', '56361', '56362', '56363', '56364', '56365', '56366', '56367', '56368', '56369'],
            // Kode Pos Kabupaten Jawa Timur
            'Kabupaten Blitar': ['66111', '66112', '66113', '66114', '66115', '66116', '66117', '66118', '66119', '66151', '66152', '66153', '66154', '66155', '66156', '66157', '66158', '66159', '66161', '66162', '66163', '66164', '66165', '66166', '66167', '66168', '66169'],
            'Kabupaten Kediri': ['64111', '64112', '64113', '64114', '64115', '64116', '64117', '64118', '64119', '64151', '64152', '64153', '64154', '64155', '64156', '64157', '64158', '64159', '64161', '64162', '64163', '64164', '64165', '64166', '64167', '64168', '64169'],
            'Kabupaten Madiun': ['63111', '63112', '63113', '63114', '63115', '63116', '63117', '63118', '63119', '63151', '63152', '63153', '63154', '63155', '63156', '63157', '63158', '63159', '63161', '63162', '63163', '63164', '63165', '63166', '63167', '63168', '63169'],
            'Kabupaten Malang': ['65111', '65112', '65113', '65114', '65115', '65116', '65117', '65118', '65119', '65151', '65152', '65153', '65154', '65155', '65156', '65157', '65158', '65159', '65161', '65162', '65163', '65164', '65165', '65166', '65167', '65168', '65169'],
            'Kabupaten Mojokerto': ['61311', '61312', '61313', '61314', '61315', '61316', '61317', '61318', '61319', '61351', '61352', '61353', '61354', '61355', '61356', '61357', '61358', '61359', '61361', '61362', '61363', '61364', '61365', '61366', '61367', '61368', '61369'],
            'Kabupaten Pasuruan': ['67111', '67112', '67113', '67114', '67115', '67116', '67117', '67118', '67119', '67151', '67152', '67153', '67154', '67155', '67156', '67157', '67158', '67159', '67161', '67162', '67163', '67164', '67165', '67166', '67167', '67168', '67169'],
            'Kabupaten Probolinggo': ['67211', '67212', '67213', '67214', '67215', '67216', '67217', '67218', '67219', '67251', '67252', '67253', '67254', '67255', '67256', '67257', '67258', '67259', '67261', '67262', '67263', '67264', '67265', '67266', '67267', '67268', '67269'],
            'Bangkalan': ['69111', '69112', '69113', '69114', '69115', '69116', '69117', '69118', '69119', '69151', '69152', '69153', '69154', '69155', '69156', '69157', '69158', '69159', '69161', '69162', '69163', '69164', '69165', '69166', '69167', '69168', '69169'],
            'Banyuwangi': ['68411', '68412', '68413', '68414', '68415', '68416', '68417', '68418', '68419', '68451', '68452', '68453', '68454', '68455', '68456', '68457', '68458', '68459', '68461', '68462', '68463', '68464', '68465', '68466', '68467', '68468', '68469'],
            'Bojonegoro': ['62111', '62112', '62113', '62114', '62115', '62116', '62117', '62118', '62119', '62151', '62152', '62153', '62154', '62155', '62156', '62157', '62158', '62159', '62161', '62162', '62163', '62164', '62165', '62166', '62167', '62168', '62169'],
            'Bondowoso': ['68211', '68212', '68213', '68214', '68215', '68216', '68217', '68218', '68219', '68251', '68252', '68253', '68254', '68255', '68256', '68257', '68258', '68259', '68261', '68262', '68263', '68264', '68265', '68266', '68267', '68268', '68269'],
            'Jember': ['68111', '68112', '68113', '68114', '68115', '68116', '68117', '68118', '68119', '68151', '68152', '68153', '68154', '68155', '68156', '68157', '68158', '68159', '68161', '68162', '68163', '68164', '68165', '68166', '68167', '68168', '68169'],
            'Jombang': ['61411', '61412', '61413', '61414', '61415', '61416', '61417', '61418', '61419', '61451', '61452', '61453', '61454', '61455', '61456', '61457', '61458', '61459', '61461', '61462', '61463', '61464', '61465', '61466', '61467', '61468', '61469'],
            'Lamongan': ['62211', '62212', '62213', '62214', '62215', '62216', '62217', '62218', '62219', '62251', '62252', '62253', '62254', '62255', '62256', '62257', '62258', '62259', '62261', '62262', '62263', '62264', '62265', '62266', '62267', '62268', '62269'],
            'Lumajang': ['67311', '67312', '67313', '67314', '67315', '67316', '67317', '67318', '67319', '67351', '67352', '67353', '67354', '67355', '67356', '67357', '67358', '67359', '67361', '67362', '67363', '67364', '67365', '67366', '67367', '67368', '67369'],
            'Magetan': ['63311', '63312', '63313', '63314', '63315', '63316', '63317', '63318', '63319', '63351', '63352', '63353', '63354', '63355', '63356', '63357', '63358', '63359', '63361', '63362', '63363', '63364', '63365', '63366', '63367', '63368', '63369'],
            'Nganjuk': ['64411', '64412', '64413', '64414', '64415', '64416', '64417', '64418', '64419', '64451', '64452', '64453', '64454', '64455', '64456', '64457', '64458', '64459', '64461', '64462', '64463', '64464', '64465', '64466', '64467', '64468', '64469'],
            'Ngawi': ['63211', '63212', '63213', '63214', '63215', '63216', '63217', '63218', '63219', '63251', '63252', '63253', '63254', '63255', '63256', '63257', '63258', '63259', '63261', '63262', '63263', '63264', '63265', '63266', '63267', '63268', '63269'],
            'Pacitan': ['63511', '63512', '63513', '63514', '63515', '63516', '63517', '63518', '63519', '63551', '63552', '63553', '63554', '63555', '63556', '63557', '63558', '63559', '63561', '63562', '63563', '63564', '63565', '63566', '63567', '63568', '63569'],
            'Pamekasan': ['69311', '69312', '69313', '69314', '69315', '69316', '69317', '69318', '69319', '69351', '69352', '69353', '69354', '69355', '69356', '69357', '69358', '69359', '69361', '69362', '69363', '69364', '69365', '69366', '69367', '69368', '69369'],
            'Ponorogo': ['63411', '63412', '63413', '63414', '63415', '63416', '63417', '63418', '63419', '63451', '63452', '63453', '63454', '63455', '63456', '63457', '63458', '63459', '63461', '63462', '63463', '63464', '63465', '63466', '63467', '63468', '63469'],
            'Sampang': ['69211', '69212', '69213', '69214', '69215', '69216', '69217', '69218', '69219', '69251', '69252', '69253', '69254', '69255', '69256', '69257', '69258', '69259', '69261', '69262', '69263', '69264', '69265', '69266', '69267', '69268', '69269'],
            'Situbondo': ['68311', '68312', '68313', '68314', '68315', '68316', '68317', '68318', '68319', '68351', '68352', '68353', '68354', '68355', '68356', '68357', '68358', '68359', '68361', '68362', '68363', '68364', '68365', '68366', '68367', '68368', '68369'],
            'Sumenep': ['69411', '69412', '69413', '69414', '69415', '69416', '69417', '69418', '69419', '69451', '69452', '69453', '69454', '69455', '69456', '69457', '69458', '69459', '69461', '69462', '69463', '69464', '69465', '69466', '69467', '69468', '69469'],
            'Trenggalek': ['66311', '66312', '66313', '66314', '66315', '66316', '66317', '66318', '66319', '66351', '66352', '66353', '66354', '66355', '66356', '66357', '66358', '66359', '66361', '66362', '66363', '66364', '66365', '66366', '66367', '66368', '66369'],
            'Tuban': ['62311', '62312', '62313', '62314', '62315', '62316', '62317', '62318', '62319', '62351', '62352', '62353', '62354', '62355', '62356', '62357', '62358', '62359', '62361', '62362', '62363', '62364', '62365', '62366', '62367', '62368', '62369'],
            'Tulungagung': ['66211', '66212', '66213', '66214', '66215', '66216', '66217', '66218', '66219', '66251', '66252', '66253', '66254', '66255', '66256', '66257', '66258', '66259', '66261', '66262', '66263', '66264', '66265', '66266', '66267', '66268', '66269'],
            'Kepulauan Seribu': ['14530', '14540']
        };

        // Fungsi untuk toggle class "required" pada label
        function toggleRequiredClass(labelId, selectElement) {
            const label = document.getElementById(labelId);
            if (label) {
                if (selectElement.value) {
                    label.classList.remove('required');
                } else {
                    label.classList.add('required');
                }
            }
        }

        // Event listener untuk provinsi
        document.getElementById('province').addEventListener('change', function() {
            const provinceValue = this.value;
            const citySelect = document.getElementById('city');
            const postalCodeSelect = document.getElementById('postalCode');

            // Toggle asterisk pada provinsi
            toggleRequiredClass('provinceLabel', this);

            // Reset dan disable city dan postal code
            citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
            citySelect.disabled = true;
            postalCodeSelect.innerHTML = '<option value="">Pilih Kode Pos</option>';
            postalCodeSelect.disabled = true;
            
            // Restore asterisk pada city dan postal code
            document.getElementById('cityLabel').classList.add('required');
            document.getElementById('postalCodeLabel').classList.add('required');

            // Populate city jika provinsi dipilih
            if (provinceValue && cityData[provinceValue]) {
                const cities = cityData[provinceValue];
                cities.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city;
                    option.textContent = city;
                    citySelect.appendChild(option);
                });
                citySelect.disabled = false;
            }
        });

        // Event listener untuk city
        document.getElementById('city').addEventListener('change', function() {
            const cityValue = this.value;
            const postalCodeSelect = document.getElementById('postalCode');

            // Toggle asterisk pada city
            toggleRequiredClass('cityLabel', this);

            // Reset postal code
            postalCodeSelect.innerHTML = '<option value="">Pilih Kode Pos</option>';
            postalCodeSelect.disabled = true;
            document.getElementById('postalCodeLabel').classList.add('required');

            // Populate postal code jika city dipilih dan data tersedia
            if (cityValue && postalCodeData[cityValue]) {
                const postalCodes = postalCodeData[cityValue];
                postalCodes.forEach(code => {
                    const option = document.createElement('option');
                    option.value = code;
                    option.textContent = code;
                    postalCodeSelect.appendChild(option);
                });
                postalCodeSelect.disabled = false;
            } else if (cityValue) {
                // Jika tidak ada data kode pos, allow manual input atau enable dengan default
                postalCodeSelect.disabled = false;
            }
        });

        // Event listener untuk postal code
        document.getElementById('postalCode').addEventListener('change', function() {
            toggleRequiredClass('postalCodeLabel', this);
        });
        });
    </script>

    <script src="{{ asset('js/form-mitra.js') }}"></script>
</body>

</html>
