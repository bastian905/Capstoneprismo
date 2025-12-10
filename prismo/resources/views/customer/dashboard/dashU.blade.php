<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Prismo - Booking Steam Mobil</title>
    <link rel="stylesheet" href="{{ asset('css/dashU.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <div class="logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Prismo Logo">
                </div>
                <ul class="nav-menu" id="mainNav">
                    <li class="mobile-profile-menu">
                        <div class="user-profile mobile-user-profile" onclick="window.location.href='{{ url('/customer/profil/uprofil') }}'" style="cursor: pointer;">
                            @php
                                $avatarUrl = auth()->user()->avatar ?? 'images/profile.png';
                                $isGoogleAvatar = str_starts_with($avatarUrl, 'http');
                                $finalUrl = $isGoogleAvatar ? $avatarUrl : asset($avatarUrl) . '?v=' . auth()->user()->updated_at->timestamp;
                            @endphp
                            <img src="{{ $finalUrl }}" alt="User" class="user-icon-img" id="mobileProfileImg" onerror="this.src='{{ asset('images/profile.png') }}'">
                            <div class="user-info">
                                <span class="user-name" id="mobileUserName">{{ auth()->user()->name }}</span>
                                <span class="user-role">User</span>
                            </div>
                        </div>
                    </li>
                    <li><a href="{{ url('/customer/dashboard/dashU') }}" class="nav-link active">Beranda</a></li>
                    <li><a href="{{ url('/customer/booking/Rbooking') }}" class="nav-link">Booking</a></li>
                    <li><a href="{{ url('/customer/voucher/voucher') }}" class="nav-link">Voucher</a></li>
                </ul>
                <div class="nav-right">
                    <button class="notification-btn" id="notifBtn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">5</span>
                    </button>
                    <div class="user-profile desktop-user-profile" onclick="window.location.href='{{ url('/customer/profil/uprofil') }}'" style="cursor: pointer;">
                        @php
                            $avatarUrl = auth()->user()->avatar ?? 'images/profile.png';
                            $isGoogleAvatar = str_starts_with($avatarUrl, 'http');
                            $finalUrl = $isGoogleAvatar ? $avatarUrl : asset($avatarUrl) . '?v=' . auth()->user()->updated_at->timestamp;
                        @endphp
                        <img src="{{ $finalUrl }}" alt="User" class="user-icon-img" id="headerProfileImg" onerror="this.src='{{ asset('images/profile.png') }}'">
                        <div class="user-info">
                            <span class="user-name" id="headerUserName">{{ auth()->user()->name }}</span>
                            <span class="user-role">User</span>
                        </div>
                    </div>
                    <button class="mobile-menu-toggle" id="menuToggle" aria-label="Toggle Menu">
                        ☰
                    </button>
                </div>
            </nav>
        </div>
    </header>

    <!-- Overlay -->
    <div class="notification-overlay" id="notifOverlay"></div>

    <!-- Notification Panel -->
    <div class="notification-panel" id="notifPanel">
        <div class="panel-header">
            <h2>Notifikasi</h2>
            <button class="mark-all-read-btn" id="markAllReadBtn" style="display: none; font-size: 12px; padding: 4px 8px; background: #1c98f5; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Tandai Semua Dibaca
            </button>
        </div>

        <div id="notificationList">
            <!-- Notifications will be loaded here dynamically -->
            <div class="loading-state" style="text-align: center; padding: 40px; color: #999;">
                <i class="fas fa-spinner fa-spin" style="font-size: 24px; margin-bottom: 12px;"></i>
                <p>Memuat notifikasi...</p>
            </div>
        </div>
    </div>

    <!-- Overlay -->
    <div class="sort-overlay" id="sortOverlay"></div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-image-wrapper">
            <img src="{{ asset('images/cvdash1.png') }}" alt="Cars" class="hero-cars-img">
        </div>
    </section>

    <!-- Sort Panel -->
    <div class="sort-panel" id="sortPanel">
        <div class="sort-panel-header">
            <h3>Filter Lokasi</h3>
            <button class="close-sort-btn" id="closeSortBtn">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="sort-panel-body">
            <div class="filter-group">
                <label for="provinsiSelect">Provinsi</label>
                <select id="provinsiSelect" class="filter-select">
                    <option value="">Semua Provinsi</option>
                    <option value="Aceh">Aceh</option>
                    <option value="Sumatera Utara">Sumatera Utara</option>
                    <option value="Sumatera Barat">Sumatera Barat</option>
                    <option value="Riau">Riau</option>
                    <option value="Jambi">Jambi</option>
                    <option value="Sumatera Selatan">Sumatera Selatan</option>
                    <option value="Bengkulu">Bengkulu</option>
                    <option value="Lampung">Lampung</option>
                    <option value="Kepulauan Bangka Belitung">Kepulauan Bangka Belitung</option>
                    <option value="Kepulauan Riau">Kepulauan Riau</option>
                    <option value="DKI Jakarta">DKI Jakarta</option>
                    <option value="Jawa Barat">Jawa Barat</option>
                    <option value="Jawa Tengah">Jawa Tengah</option>
                    <option value="DI Yogyakarta">DI Yogyakarta</option>
                    <option value="Jawa Timur">Jawa Timur</option>
                    <option value="Banten">Banten</option>
                    <option value="Bali">Bali</option>
                    <option value="Nusa Tenggara Barat">Nusa Tenggara Barat</option>
                    <option value="Nusa Tenggara Timur">Nusa Tenggara Timur</option>
                    <option value="Kalimantan Barat">Kalimantan Barat</option>
                    <option value="Kalimantan Tengah">Kalimantan Tengah</option>
                    <option value="Kalimantan Selatan">Kalimantan Selatan</option>
                    <option value="Kalimantan Timur">Kalimantan Timur</option>
                    <option value="Kalimantan Utara">Kalimantan Utara</option>
                    <option value="Sulawesi Utara">Sulawesi Utara</option>
                    <option value="Sulawesi Tengah">Sulawesi Tengah</option>
                    <option value="Sulawesi Selatan">Sulawesi Selatan</option>
                    <option value="Sulawesi Tenggara">Sulawesi Tenggara</option>
                    <option value="Gorontalo">Gorontalo</option>
                    <option value="Sulawesi Barat">Sulawesi Barat</option>
                    <option value="Maluku">Maluku</option>
                    <option value="Maluku Utara">Maluku Utara</option>
                    <option value="Papua Barat">Papua Barat</option>
                    <option value="Papua">Papua</option>
                    <option value="Papua Barat Daya">Papua Barat Daya</option>
                    <option value="Papua Pegunungan">Papua Pegunungan</option>
                    <option value="Papua Selatan">Papua Selatan</option>
                    <option value="Papua Tengah">Papua Tengah</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="kotaSelect">Kota/Kabupaten</label>
                <select id="kotaSelect" class="filter-select" disabled>
                    <option value="">Pilih provinsi terlebih dahulu</option>
                </select>
            </div>
            <div class="sort-panel-actions">
                <button class="btn-reset" id="resetFilterBtn">Reset</button>
                <button class="btn-apply" id="applyFilterBtn">Terapkan</button>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <section class="search-section">
        <div class="container">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" id="searchInput" placeholder="Cari berdasarkan nama steam">
                <button class="sort-btn" id="sortBtn">
                    <i class="fas fa-filter"></i>
                    <span>Sortir</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section">
        <div class="container">
            <h2 class="section-title">Layanan Cuci Steam Tersedia</h2>
            <div class="services-container">
                <div class="services-wrapper">
                    <div class="services-grid" id="servicesGrid">
                        <!-- Service cards akan di-generate oleh JavaScript -->
                    </div>
                </div>
                <div class="services-navigation">
                    <button class="services-nav services-nav-left" id="servicesNavLeft">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="services-pagination" id="servicesPagination">
                        <!-- Pagination dots will be generated by JavaScript -->
                    </div>
                    <button class="services-nav services-nav-right" id="servicesNavRight">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Coverage Section -->
    <section class="coverage-section">
        <div class="container">
            <div class="coverage-box">
                <h2 class="coverage-title">Titik Mitra Tersebar di JABOTABEK</h2>
                <p class="coverage-description">PRISMO akan terus melebarkan sayap agar dapat melayani<br>Anda lebih dekat, dimanapun dan kapanpun.</p>
                <div class="coverage-map">
                    <img src="{{ asset('images/Peta-Indo1.jpg') }}" alt="Peta Indonesia">
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-column">
                <img src="{{ asset('images/logo.png') }}" alt="Prismo Logo" class="footer-logo-img">
                <p class="footer-description">Platform booking cuci steam kendaraan terpercaya dengan layanan profesional di seluruh Indonesia.</p>
                <h4 class="footer-subtitle">Keep Connected</h4>
                <div class="social-links">
                    <a href="https://www.instagram.com/prismo_id?igsh=d3d4Mmo3NHBhbTBz" target="_blank" rel="noopener noreferrer" class="social-link">
                        <i class="fab fa-instagram"></i>
                        <span>Prismo_id</span>
                    </a>
                    <a href="https://www.facebook.com/share/1G9mzz7TSH/" target="_blank" rel="noopener noreferrer" class="social-link">
                        <i class="fab fa-facebook"></i>
                        <span>Prismo.id</span>
                    </a>
                    <a href="https://www.tiktok.com/@prismo_id?_r=1&_t=ZS-91git6qegI3" target="_blank" rel="noopener noreferrer" class="social-link">
                        <i class="fab fa-tiktok"></i>
                        <span>Prismo_id</span>
                    </a>
                </div>
            </div>
            <div class="footer-column">
                <h4 class="footer-title">Layanan</h4>
                <ul class="footer-links">
                    <li><a href="#">Cuci Steam Mobil</a></li>
                    <li><a href="#">Cuci Steam Biasa</a></li>
                    <li><a href="#">Detailing Premium</a></li>
                    <li><a href="#">Perawatan Interior</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4 class="footer-title">Kontak</h4>
                <ul class="footer-contact">
                    <li>
                        <i class="fas fa-phone"></i>
                        <span>0822-2767-1561</span>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span>prismobook@gmail.com</span>
                    </li>
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Jl. Cikeas No. 123, Bogor Timur</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Prismo. All rights reserved.</p>
        </div>
    </div>
</footer>

    <script>
        // Inject real mitra data from server
        window.servicesData = @json($mitras);
        console.log('📊 Mitra data injected from server:', window.servicesData);
        
        // Debug: Check rating data
        if (window.servicesData && window.servicesData.length > 0) {
            window.servicesData.forEach(mitra => {
                console.log(`Mitra: ${mitra.name}, Rating: ${mitra.rating}, Reviews: ${mitra.reviews}`);
            });
        }
    </script>
    <script src="{{ asset('js/browser-notification.js') }}"></script>
    <script src="{{ asset('js/notification-system.js') }}"></script>
    <script src="{{ asset('js/prevent-back.js') }}"></script>
    <script src="{{ asset('js/dashU.js') }}?v={{ time() }}"></script>
    <script>
        // Listen untuk update avatar dari halaman profil
        if (typeof BroadcastChannel !== 'undefined') {
            const channel = new BroadcastChannel('profile_update');
            channel.onmessage = (event) => {
                if (event.data.type === 'avatar_updated') {
                    // Update all avatar images
                    document.querySelectorAll('.user-icon-img, .avatar__image').forEach(img => {
                        img.src = event.data.avatar;
                    });
                    console.log('🔄 Avatar synced from other tab');
                }
            };
        }
    </script>
</body>
</html>
