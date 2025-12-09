<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prismo - Booking Steam Mobil Jadi Mudah</title>
    <link rel="stylesheet" href="{{ asset('css/tentang.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                     <a href="#">
                    <img src="{{ asset('images/logo.png') }}" alt="Prismo Logo">
                    </a>
                </div>
                <nav class="nav" id="mainNav">
                    <ul>
                        <li><a href="{{ url('/') }}">Beranda</a></li>
                        <li><a href="{{ url('/tentang') }}" class="active">Tentang Kami</a></li>
                        <li><a href="https://prismobook.blogspot.com/2025/11/tren-kebersihan-kendaraan-meningkat" target="_blank">Artikel</a></li>

                    </ul>
                </nav>
                <div class="header-buttons">
                    <button class="btn-outline" onclick="window.location.href='{{ url('/login?tab=login') }}'">Masuk</button>
                    <button class="btn-primary" onclick="window.location.href='{{ url('/register?tab=register') }}'">Daftar</button>
                </div>
                <button class="mobile-menu-toggle" id="menuToggle" aria-label="Toggle Menu">
                    ☰
                </button>
            </div>
        </div>
    </header>

    <section class="about-section">
    <div class="container">
        <!-- Header Title -->
        <div class="about-header">
            <h2 class="about-title">
                Prismo Platform Cuci Mobil di Bogor Timur Temukan Toko<br>
                Steam Terdekat, Cepat & Praktis!
            </h2>
        </div>

        <!-- Content Section -->
        <div class="about-content">
            <div class="prismo-intro">
                <img src="{{ asset('images/logo.png') }}" alt="Prismo Logo">
            </div>
            <div class="prismo-description">
                <p>
                    Kami adalah platform digital yang menghubungkan 
                    Anda dengan berbagai layanan steam mobil terpercaya 
                    di Bogor Timur.
                </p>
                <p>
                    Melalui sistem yang praktis, Anda dapat dengan 
                    mudah menemukan, memilih, dan memesan jasa steam 
                    mobil sesuai lokasi, kebutuhan, dan waktu yang 
                    diinginkan.
                </p>
            </div>
        </div>

        <!-- Features Section -->
        <div class="features-section">
            <!-- Untuk Pelanggan -->
            <div class="feature-card">
                <h3 class="feature-title">Untuk Pelanggan</h3>
                <div class="feature-icon1">
                </div>
                <p class="feature-description">
                    Kami menghadirkan pengalaman mencuci mobil yang praktis, 
                    aman, dan terpercaya. Tidak perlu repot mencari satu per satu, 
                    karena kami sudah menyediakan mitra profesional dengan kualitas 
                    layanan yang terjamin.
                </p>
            </div>

            <!-- Untuk Mitra -->
            <div class="feature-card">
                <h3 class="feature-title">Untuk Mitra</h3>
                <div class="feature-icon2">
                </div>
                <p class="feature-description">
                    Penyedia jasa steam mobil, kami hadir sebagai wadah untuk 
                    mengembangkan usaha dan menjangkau lebih banyak pelanggan. 
                    Dengan bergabung bersama kami, Anda tidak hanya memperluas 
                    jangkauan, tetapi juga mendapatkan sistem pemesanan yang 
                    terstruktur dan transparan.
                </p>
            </div>
        </div>
    </div>
</section>
<!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Kenapa Pilih Prismo?</h2>
                <p class="section-subtitle">Platform terpercaya untuk booking steam mobil dengan berbagai keunggulan</p>
            </div>
            <div class="features-grid">
                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="feature-content">
                        <h3 class="feature-number">500+</h3>
                        <p class="feature-label">Mitra Langganan</p>
                        <p class="feature-desc">Tersebar di berbagai kota</p>
                    </div>
                </div>
                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <div class="feature-icon">
                            <i class="fas fa-user-group"></i>
                        </div>
                    </div>
                    <div class="feature-content">
                        <h3 class="feature-number">10K</h3>
                        <p class="feature-label">Customers Terdaftar</p>
                        <p class="feature-desc">Pelanggan setia kami</p>
                    </div>
                </div>
                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <div class="feature-icon">
                            <i class="fas fa-location-dot"></i>
                        </div>
                    </div>
                    <div class="feature-content">
                        <h3 class="feature-number">50+</h3>
                        <p class="feature-label">Kota Terdekat</p>
                        <p class="feature-desc">Jangkauan layanan luas</p>
                    </div>
                </div>
                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <div class="feature-icon">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                    </div>
                    <div class="feature-content">
                        <h3 class="feature-number">Terpercaya</h3>
                        <p class="feature-label">& Aman</p>
                        <p class="feature-desc">Semua mitra terverifikasi dengan standar kualitas tinggi</p>
                    </div>
                </div>
                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                    <div class="feature-content">
                        <h3 class="feature-number">Booking</h3>
                        <p class="feature-label">Fleksibel</p>
                        <p class="feature-desc">Atur waktu booking sesuai kebutuhan Anda</p>
                    </div>
                </div>
                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <div class="feature-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                    </div>
                    <div class="feature-content">
                        <h3 class="feature-number">Cepat</h3>
                        <p class="feature-label">& Efisien</p>
                        <p class="feature-desc">Tim profesional kami siap melayani dengan cepat</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Gabung bersama Prismo sekarang!</h2>
                <p class="cta-subtitle">Nikmati kemudahan, kecepatan, dan kualitas layanan booking steam mobil terkemuka. Mari bergabung menjadi bagian dari keluarga besar kami!</p>
                <button class="btn-cta" onclick="window.location.href='{{ url('/register?tab=register') }}'">Gabung Sekarang</button>
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

     <!-- JavaScript di akhir body -->
    <script>
        // Set active nav berdasarkan URL saat halaman load
        window.addEventListener('DOMContentLoaded', function() {
            const currentPage = window.location.pathname.split('/').pop() || 'tentang.html';
            const navLinks = document.querySelectorAll('.nav a');
            
            navLinks.forEach(link => {
                // Hapus semua active class
                link.classList.remove('active');
                
                // Ambil nama file dari href
                const linkPage = link.getAttribute('href');
                
                // Tambahkan active ke link yang sesuai
                if (linkPage === currentPage) {
                    link.classList.add('active');
                }
            });
        });
    </script>
    <script src="{{ asset('js/nav.js') }}"></script>
    <script src="{{ asset('js/navhead.js') }}"></script>
</body>
</html>
