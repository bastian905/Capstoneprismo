<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Edit Profil - Prismo</title>
    <link rel="stylesheet" href="{{ asset('css/eprofil.css') }}?v={{ time() }}">
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Prismo">
            </div>
            <button class="back-btn" id="backBtn" onclick="window.location.href='{{ url('/customer/profil/uprofil') }}'" style="cursor: pointer;">
                <i class="ph ph-arrow-left"></i>
                Kembali
            </button>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="edit-profile-card">
                <h2 class="page-title">Edit Profil</h2>
                
                <form class="edit-form" id="editForm">
                    <div class="form-group">
                        <label for="fullName">Nama Lengkap</label>
                        <input 
                            type="text" 
                            id="fullName" 
                            name="fullName"
                            value="{{ auth()->user()->name }}"
                            placeholder="Nama Lengkap"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="phone">No Telepon/WhatsApp</label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone"
                            value="{{ auth()->user()->phone ?? '' }}"
                            placeholder="0123456789"
                        >
                    </div>

                    <button type="submit" class="submit-btn" id="submitBtn">
                        <span class="btn-text">Konfirmasi Edit Profil</span>
                        <span class="btn-loader"></span>
                    </button>
                </form>
            </div>
        </main>
    </div>

    <!-- Success Modal -->
    <div class="modal" id="successModal">
        <div class="modal-content" style="max-width: 300px; text-align: center;">
            <div class="success-icon" style="font-size: 80px; color: #4caf50; margin-bottom: 20px;">
                <i class="ph-fill ph-check-circle"></i>
            </div>
            <button class="ok-btn" id="okBtn" style="margin-top: 20px;">OK</button>
        </div>
    </div>

    <script src="{{ asset('js/eprofil.js') }}?v={{ time() }}"></script>
    <script>
        // Broadcast avatar update untuk sinkronisasi cross-tab
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function() {
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
