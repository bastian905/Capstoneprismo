<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile Mitra</title>
    <link rel="stylesheet" href="{{ asset('css/edit-profile.css') }}">
</head>
<body>
    <div class="container">
        <header>
            <h1>Edit Profile Mitra</h1>
            <p class="subtitle">Perbarui informasi bisnis dan kontak Anda</p>
        </header>

        <form id="editProfileForm">
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
                            <label for="address" class="required">Alamat Lengkap</label>
                            <input type="text" id="address" name="address"
                                placeholder="Jl. Cikeas No. 123, Bogor Timur" required>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="city" class="required">Kota/Kabupaten</label>
                                    <input type="text" id="city" name="city" placeholder="Jakarta Timur" required>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="postalCode" class="required">Kode Pos</label>
                                    <input type="text" id="postalCode" name="postalCode" placeholder="10110" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mapLocation" class="required">Lokasi di Peta</label>
                            <input type="url" id="mapLocation" name="mapLocation"
                                placeholder="https://www.google.com/maps/place/" required>
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
                    <!-- Foto Fasilitas -->
                    <section class="form-section">
                        <h2>Foto Fasilitas</h2>

                        <div class="form-group" id="facilityGroup">
                            <label class="required">Foto Fasilitas (Maksimal 5 foto)</label>
                            <div class="upload-card" id="facilityUpload">
                                <div class="upload-content">
                                    <div class="upload-icon">
                                        <img src="{{ asset('images/fasilitas.png') }}" alt="Fasilitas">
                                    </div>
                                    <div class="upload-text">
                                        <p class="upload-title">Klik untuk upload foto fasilitas</p>
                                        <p class="upload-subtitle">JPG, PNG maksimal 5MB per foto</p>
                                    </div>
                                </div>
                            </div>
                            <div id="facilityPreview" class="upload-preview"></div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Bagian Tombol -->
            <div class="button-section">
                <button type="button" class="btn-cancel" onclick="window.history.back()">Batal</button>
                <button type="submit" class="btn-save">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <script src="{{ asset('js/edit-profile.js') }}"></script>
    <script>
        // Broadcast avatar update untuk sinkronisasi cross-tab
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function() {
                // Broadcast setelah form submit sukses
                setTimeout(() => {
                    if (typeof BroadcastChannel !== 'undefined') {
                        const channel = new BroadcastChannel('profile_update');
                        const avatarInput = document.querySelector('input[name="avatar"]');
                        if (avatarInput && avatarInput.files.length > 0) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                channel.postMessage({
                                    type: 'avatar_updated',
                                    avatar: e.target.result
                                });
                            };
                            reader.readAsDataURL(avatarInput.files[0]);
                        }
                    }
                }, 500);
            });
        }
    </script>
</body>
</html>
