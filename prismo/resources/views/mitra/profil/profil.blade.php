<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PRISMO - Profil</title>
    <style>
        /* ===== VARIABLES ===== */
        :root {
            /* Colors - Light Mode (Sinar) */
            --primary-50: #f0f7ff;
            --primary-100: #e1effe;
            --primary-200: #bae6fd;
            --primary-300: #7dd3fc;
            --primary-500: #3b82f6;
            --primary-600: #2563eb;
            --primary-700: #1d4ed8;
            --success-500: #10b981;
            --success-600: #059669;
            --success-700: #047857;
            --warning-500: #f59e0b;
            --danger-500: #ef4444;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --white: #ffffff;
            
            /* Typography */
            --font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            --font-size-xs: 0.75rem;
            --font-size-sm: 0.875rem;
            --font-size-base: 1rem;
            --font-size-lg: 1.125rem;
            --font-size-xl: 1.25rem;
            --font-size-2xl: 1.5rem;
            --font-size-3xl: 1.875rem;
            --font-size-4xl: 2.25rem;
            
            /* Spacing */
            --space-1: 0.25rem;
            --space-2: 0.5rem;
            --space-3: 0.75rem;
            --space-4: 1rem;
            --space-5: 1.25rem;
            --space-6: 1.5rem;
            --space-8: 2rem;
            --space-12: 3rem;
            
            /* Border Radius */
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --radius-2xl: 1.5rem;
            
            /* Shadows - Lebih Soft */
            --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            
            /* Transitions */
            --transition-normal: 0.3s ease;
            --transition-slow: 0.5s ease;
            --z-modal: 1050;
        }

        /* ===== BASE STYLES ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
            color: var(--gray-800);
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===== HEADER & NAVIGATION ===== */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-sm);
            border-bottom: 1px solid var(--gray-200);
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .header__content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: var(--space-4) var(--space-6);
            max-width: 1400px;
            margin: 0 auto;
            gap: var(--space-6);
            min-height: 88px    ;
        }

        .header__left {
            display: flex;
            align-items: center;
            gap: var(--space-6);
            flex: 1;
        }

        .logo {
            height: 40px;
            width: auto;
        }

        /* ===== USER MENU ===== */
        .user-menu {
            flex-shrink: 0;
        }

        /* ===== MAIN CONTENT ===== */
        .main {
            flex: 1;
            padding: var(--space-6);
            max-width: 800px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        /* ===== PROFILE CARD ===== */
        .profile-card {
            background: var(--white);
            border-radius: var(--radius-2xl);
            padding: var(--space-8);
            box-shadow: var(--shadow-lg);
            margin-bottom: var(--space-6);
        }

        .profile-content {
            display: flex;
            gap: var(--space-8);
            margin-bottom: var(--space-6);
        }

        .profile-left {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: var(--space-4);
            flex-shrink: 0;
        }

        .profile-avatar-container {
            position: relative;
            width: 120px;
            height: 120px;
        }

        .profile-avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .edit-photo-button {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-500);
            border: 3px solid var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all var(--transition-normal);
            box-shadow: var(--shadow-md);
        }

        .edit-photo-button:hover {
            background: var(--primary-600);
            transform: scale(1.1);
        }

        .edit-photo-button img {
            width: 16px;
            height: 16px;
            filter: brightness(0) invert(1);
        }

        .profile-info {
            flex: 1;
        }

        .profile-name {
            font-size: var(--font-size-3xl);
            font-weight: 800;
            color: var(--gray-800);
            margin-bottom: var(--space-4);
        }

        .profile-contact {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            color: var(--gray-600);
            margin-bottom: var(--space-3);
            font-size: var(--font-size-base);
        }

        .profile-member {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            color: var(--gray-500);
            font-size: var(--font-size-sm);
            margin-bottom: var(--space-6);
        }

        .contact-icon {
            width: 20px;
            height: 20px;
            color: var(--gray-500);
            flex-shrink: 0;
        }

        /* ===== SETTINGS CARD ===== */
        .settings-card {
            background: var(--white);
            border-radius: var(--radius-2xl);
            padding: var(--space-8);
            box-shadow: var(--shadow-lg);
            position: relative;
        }

        .section-title {
            font-size: var(--font-size-xl);
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: var(--space-6);
        }

        .settings-list {
            list-style: none;
        }

        .settings-item {
            display: flex;
            align-items: center;
            padding: var(--space-5) 0;
            cursor: pointer;
            transition: all var(--transition-normal);
            text-decoration: none;
            color: inherit;
        }

        .settings-item:last-child {
            border-bottom: none;
        }

        .settings-item:hover {
            background: var(--gray-50);
            margin: 0 calc(-1 * var(--space-4));
            padding-left: var(--space-4);
            padding-right: var(--space-4);
            border-radius: var(--radius-lg);
        }

        .settings-icon {
            width: 24px;
            height: 24px;
            margin-right: var(--space-4);
            flex-shrink: 0;
        }

        .settings-label {
            font-size: var(--font-size-base);
            color: var(--gray-700);
            font-weight: 500;
        }

        .settings-footer {
            display: flex;
            justify-content: flex-end;
            padding-top: var(--space-6);
        }

        /* ===== BUTTONS ===== */
        .btn {
            padding: var(--space-3) var(--space-4);
            border: none;
            border-radius: var(--radius-lg);
            cursor: pointer;
            font-size: var(--font-size-sm);
            font-weight: 600;
            transition: all var(--transition-normal);
            letter-spacing: 0.5px;
            box-shadow: var(--shadow-sm);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            position: relative;
            overflow: hidden;
            text-decoration: none;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn--success {
            background: linear-gradient(135deg, var(--success-500), var(--success-600));
            color: var(--white);
            max-width: 300px;
        }

        .btn--success:hover {
            background: linear-gradient(135deg, var(--success-600), var(--success-700));
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn--danger {
            background: linear-gradient(135deg, var(--danger-500), #dc2626);
            color: var(--white);
        }

        .btn--danger:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn--secondary {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .btn--secondary:hover {
            background: var(--gray-300);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn--back {
            background: transparent;
            color: black;
            box-shadow: none;
            padding: var(--space-2) var(--space-4);
        }

        .btn--back:hover {
            background: var(--primary-500);
            color: var(--white);
        }

        .btn-icon {
            width: 16px;
            height: 16px;
            margin-right: 8px;
            filter: brightness(0) invert(1);
        }

        /* ===== MODAL STYLES ===== */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: var(--z-modal);
            opacity: 0;
            visibility: hidden;
            transition: all var(--transition-normal);
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal {
            background: var(--white);
            border-radius: var(--radius-2xl);
            padding: var(--space-8);
            box-shadow: var(--shadow-xl);
            max-width: 400px;
            width: 90%;
            transform: translateY(-20px);
            transition: transform var(--transition-normal);
        }

        .modal-overlay.active .modal {
            transform: translateY(0);
        }

        .modal-header {
            display: flex;
            align-items: center;
            margin-bottom: var(--space-6);
        }

        .modal-icon {
            width: 48px;
            height: 48px;
            background: var(--danger-500);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: var(--space-4);
            flex-shrink: 0;
        }

        .modal-icon svg {
            width: 24px;
            height: 24px;
            color: var(--white);
        }

        .modal-title {
            font-size: var(--font-size-xl);
            font-weight: 700;
            color: var(--gray-800);
        }

        .modal-body {
            margin-bottom: var(--space-6);
        }

        .modal-text {
            color: var(--gray-600);
            line-height: 1.6;
        }

        .modal-footer {
            display: flex;
            gap: var(--space-4);
            justify-content: flex-end;
        }

        .modal-footer .btn {
            min-width: 100px;
        }

        /* ===== RESPONSIVE DESIGN ===== */
        @media (max-width: 768px) {
            .header__content {
                padding: var(--space-3) var(--space-4);
            }
            
            .main {
                padding: var(--space-4);
            }
            
            .profile-card,
            .settings-card {
                padding: var(--space-6);
            }
            
            .profile-content {
                flex-direction: column;
                gap: var(--space-6);
            }
            
            .profile-left {
                align-items: center;
            }
            
            .profile-name {
                font-size: var(--font-size-2xl);
                text-align: center;
            }
            
            .btn--success {
                max-width: 100%;
            }
            
            .modal {
                padding: var(--space-6);
                margin: var(--space-4);
            }
            
            .modal-footer {
                flex-direction: column;
            }
            
            .modal-footer .btn {
                min-width: auto;
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .header__content {
                padding: var(--space-3);
            }
            
            .main {
                padding: var(--space-3);
            }
            
            .profile-card,
            .settings-card {
                padding: var(--space-4);
            }
            
            .profile-name {
                font-size: var(--font-size-xl);
            }
            
            .settings-item {
                padding: var(--space-4) 0;
            }
            
            .profile-avatar-container {
                width: 100px;
                height: 100px;
            }
            
            .modal {
                padding: var(--space-4);
            }
            
            .modal-header {
                flex-direction: column;
                text-align: center;
                gap: var(--space-4);
            }
            
            .modal-icon {
                margin-right: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="header__content">
                <div class="header__left">
                    <div class="header__brand">
                        <img src="{{ asset('images/logo.png') }}" alt="PRISMO" class="logo" width="120" height="40">
                    </div>
                </div>

                <div class="user-menu">
                    <button class="btn btn--back" onclick="goBack()">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="margin-right: 8px;">
                            <path d="M10.707 2.293a1 1 0 010 1.414L6.414 8l4.293 4.293a1 1 0 01-1.414 1.414l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 0z"/>
                        </svg>
                        Kembali
                    </button>
                </div>
            </div>
        </header>

        <main class="main">
            <!-- Profile Card -->
            <div class="profile-card">
                <div class="profile-content">
                    <!-- Bagian Kiri: Foto Profil -->
                    <div class="profile-left">
                        <div class="profile-avatar-container">
                            <div class="profile-avatar">
                                <img src="{{ asset(auth()->user()->avatar ?? 'images/profile.png') }}?v={{ auth()->user()->updated_at->timestamp }}" alt="{{ auth()->user()->name }}" id="profileImage">
                            </div>
                            <div class="edit-photo-button" onclick="changeProfilePhoto()">
                                <img src="{{ asset('images/camera.png') }}" alt="Edit Foto">
                            </div>
                        </div>
                    </div>

                    <!-- Bagian Kanan: Informasi Profil -->
                    <div class="profile-info">
                        <h1 class="profile-name">{{ $user->name }}</h1>
                        
                        <div class="profile-contact">
                            <img src="{{ asset('images/email.png') }}" alt="Email" class="contact-icon">
                            {{ $user->email }}
                        </div>
                        
                        <div class="profile-contact">
                            <img src="{{ asset('images/telepon.png') }}" alt="Telepon" class="contact-icon">
                            {{ $user->phone ?? '+62 812-3456-7890' }}
                        </div>
                        
                        <div class="profile-member">
                            <img src="{{ asset('images/siganteng.png') }}" alt="Member Since" class="contact-icon">
                            Member Since: {{ $user->created_at->format('d F Y') }}
                        </div>
                        
                        <button class="btn btn--success" onclick="editProfileBusiness()">
                            <img src="{{ asset('images/toko.png') }}" alt="Edit Deskripsi" class="btn-icon">
                            Edit profile bisnis
                        </button>
                    </div>
                </div>
            </div>

            <!-- Settings Card -->
            <div class="settings-card">
                <h2 class="section-title">Pengaturan Akun</h2>
                <ul class="settings-list">
                    @if(empty(auth()->user()->google_id))
                    <a href="{{ url('/profile/change-password') }}" class="settings-item">
                        <img src="{{ asset('images/password.png') }}" alt="Ubah Password" class="settings-icon">
                        <span class="settings-label">Ubah password</span>
                    </a>
                    @else
                    <div class="settings-item disabled" style="opacity: 0.5; cursor: not-allowed;" title="Tidak tersedia untuk akun Google">
                        <img src="{{ asset('images/password.png') }}" alt="Ubah Password" class="settings-icon">
                        <span class="settings-label">Ubah password (Login via Google)</span>
                    </div>
                    @endif
                    <a href="{{ url('/help/faq') }}" class="settings-item">
                        <img src="{{ asset('images/tanya.png') }}" alt="Bantuan & FAQ" class="settings-icon">
                        <span class="settings-label">Bantuan & FAQ</span>
                    </a>
                </ul>

                <!-- Tombol Keluar di Kanan Bawah Settings Card -->
                <div class="settings-footer">
                    <button class="btn btn--danger" onclick="openLogoutModal()">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="margin-right: 8px;">
                            <path d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0v2z"/>
                            <path d="M10.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L9.293 7.5H2.5a.5.5 0 0 0 0 1h6.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                        </svg>
                        Keluar
                    </button>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Konfirmasi Keluar -->
    <div class="modal-overlay" id="logoutModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.5 3.75A1.5 1.5 0 006 5.25v13.5a1.5 1.5 0 001.5 1.5h6a1.5 1.5 0 001.5-1.5V15a.75.75 0 011.5 0v3.75a3 3 0 01-3 3h-6a3 3 0 01-3-3V5.25a3 3 0 013-3h6a3 3 0 013 3V9A.75.75 0 0115 9V5.25a1.5 1.5 0 00-1.5-1.5h-6zm10.72 4.72a.75.75 0 011.06 0l3 3a.75.75 0 010 1.06l-3 3a.75.75 0 11-1.06-1.06l1.72-1.72H9a.75.75 0 010-1.5h10.94l-1.72-1.72a.75.75 0 010-1.06z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h2 class="modal-title">Konfirmasi Keluar</h2>
            </div>
            <div class="modal-body">
                <p class="modal-text">Apakah Anda yakin ingin keluar dari akun Anda? Anda perlu login kembali untuk mengakses akun ini.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn--secondary" onclick="closeLogoutModal()">Batal</button>
                <button class="btn btn--danger" onclick="handleLogout()">Ya, Keluar</button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Ubah Foto Profil -->
    <div class="modal-overlay" id="avatarConfirmModal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Konfirmasi Perubahan Foto</h2>
            </div>
            <div class="modal-body">
                <p class="modal-text">Apakah Anda yakin ingin mengubah foto profil?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn--secondary" onclick="cancelAvatarChange()">Batal</button>
                <button class="btn btn--primary" onclick="confirmAvatarChange()">Ya, Ubah</button>
            </div>
        </div>
    </div>

    <script>
        // Variable untuk menyimpan file foto yang akan diupload
        let pendingAvatarFile = null;
        // Fungsi untuk tombol kembali
        function goBack() {
            window.location.href = '{{ url('/dashboard-mitra') }}';
        }

        // Fungsi untuk edit profile bisnis
        function editProfileBusiness() {
            window.location.href = '{{ url('/mitra/profil/edit-profile') }}';
        }

        // Fungsi untuk mengganti foto profil
        function changeProfilePhoto() {
            // Membuat input file tersembunyi
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            
            input.onchange = function(event) {
                const file = event.target.files[0];
                if (file) {
                    // Validasi ukuran file (max 5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Ukuran file maksimal 5MB!');
                        return;
                    }
                    
                    // Simpan file dan tampilkan modal konfirmasi
                    pendingAvatarFile = file;
                    showAvatarConfirmModal();
                }
            };
            
            // Trigger click pada input file
            input.click();
        }

        // Tampilkan modal konfirmasi avatar
        function showAvatarConfirmModal() {
            document.getElementById('avatarConfirmModal').classList.add('active');
        }

        // Batal ubah avatar
        function cancelAvatarChange() {
            document.getElementById('avatarConfirmModal').classList.remove('active');
            pendingAvatarFile = null;
        }

        // Konfirmasi ubah avatar
        function confirmAvatarChange() {
            if (!pendingAvatarFile) return;
            
            // Close modal
            document.getElementById('avatarConfirmModal').classList.remove('active');
            
            // Upload foto menggunakan API
            const formData = new FormData();
            formData.append('photo', pendingAvatarFile);
            
            fetch('{{ url('/profile/photo/upload') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log('✅ Profile photo updated and saved to storage');
                    alert('Foto profil berhasil diperbarui!');
                    
                    // Update avatar langsung dengan timestamp baru
                    const newAvatarUrl = data.avatar + '?v=' + data.cache_buster;
                    const avatarImgs = document.querySelectorAll('#profileImage, .avatar__image');
                    avatarImgs.forEach(img => {
                        img.src = newAvatarUrl;
                    });
                    
                    // Broadcast ke tab lain untuk update avatar
                    if (typeof BroadcastChannel !== 'undefined') {
                        const channel = new BroadcastChannel('profile_update');
                        channel.postMessage({
                            type: 'avatar_updated',
                            avatar: newAvatarUrl
                        });
                        channel.close();
                    }
                    
                    console.log('🔄 Avatar updated to:', newAvatarUrl);
                } else {
                    alert('Gagal mengupload foto: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error uploading photo:', error);
                alert('Terjadi kesalahan saat mengupload foto. Silakan coba lagi.');
            })
            .finally(() => {
                // Clear pending file
                pendingAvatarFile = null;
            });
        }

        // Fungsi untuk membuka modal logout
        function openLogoutModal() {
            document.getElementById('logoutModal').classList.add('active');
        }

        // Fungsi untuk menutup modal logout
        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.remove('active');
        }

        // Fungsi untuk tombol keluar
        function handleLogout() {
            // Tutup modal
            closeLogoutModal();
            
            // Tampilkan pesan konfirmasi
            alert('Anda telah berhasil keluar dari akun.');
            
            // Redirect ke halaman login
            window.location.href = '/login';
        }

        // Handle edit info button
        document.addEventListener('DOMContentLoaded', function() {
            const editButton = document.querySelector('.btn--success');
            if (editButton) {
                editButton.addEventListener('click', function() {
                    editProfileBusiness();
                });
            }
            
            // Tutup modal saat klik di luar modal
            const modalOverlay = document.getElementById('logoutModal');
            modalOverlay.addEventListener('click', function(e) {
                if (e.target === modalOverlay) {
                    closeLogoutModal();
                }
            });
            
            // Tutup modal dengan tombol ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modalOverlay.classList.contains('active')) {
                    closeLogoutModal();
                }
            });

            // Close avatar modal with ESC key
            document.addEventListener('keydown', function(e) {
                const avatarModal = document.getElementById('avatarConfirmModal');
                if (e.key === 'Escape' && avatarModal.classList.contains('active')) {
                    cancelAvatarChange();
                }
            });

            // Close avatar modal on outside click
            const avatarModal = document.getElementById('avatarConfirmModal');
            avatarModal.addEventListener('click', function(e) {
                if (e.target === avatarModal) {
                    cancelAvatarChange();
                }
            });
        });
    </script>
</body>
</html>
