<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Mini Profile - Prismo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/minipro.css') }}?v={{ time() }}">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Prismo Logo">
            </div>
            <a href="{{ url('/customer/dashboard/dashU') }}" class="back-btn">← Kembali</a>
        </div>
    </header>

    <div class="gallery-container">
        <div class="gallery-slider">
            <div class="gallery-track" id="galleryTrack">
                <!-- Slides will be populated by JavaScript -->
            </div>
            <button class="gallery-nav prev" onclick="moveSlide(-1)">‹</button>
            <button class="gallery-nav next" onclick="moveSlide(1)">›</button>
        </div>
        <div class="gallery-dots" id="dotsContainer"></div>
    </div>

    <div class="main-content">
        <div class="title-section">
            <h1 id="businessName">
                <!-- Business name will be populated by JavaScript -->
                <span class="rating" id="businessRating"></span>
            </h1>
        </div>
        
        <p class="description" id="businessDescription">
            <!-- Description will be populated by JavaScript -->
        </p>

        <div class="info-item address" id="businessAddress">
            <!-- Address will be populated by JavaScript -->
        </div>
        <div class="info-item phone" id="businessPhone">
            <!-- Phone will be populated by JavaScript -->
        </div>
        <div class="info-item time" id="businessHours">
            <!-- Hours will be populated by JavaScript -->
        </div>
    </div>

    <h2 class="services-title">Kategori Layanan</h2>

    <div class="services-container">
        <div class="services-slider">
            <button class="slider-nav prev" onclick="moveServices(-1)">‹</button>
            <div class="services-track" id="servicesTrack">
                <!-- Service cards will be populated by JavaScript -->
            </div>
            <button class="slider-nav next" onclick="moveServices(1)">›</button>
        </div>
        <div class="services-dots" id="servicesDots"></div>
    </div>

    <div class="reviews-section">
        <div class="reviews-header">
            <h2>Review Pelanggan</h2>
            <a href="#" class="view-all">Lihat Semua ></a>
        </div>

        <div class="reviews-container">
            <button class="slider-nav prev" onclick="moveReviews(-1)">‹</button>
            <div class="reviews-track" id="reviewsTrack">
                <!-- Review cards will be populated by JavaScript -->
            </div>
            <button class="slider-nav next" onclick="moveReviews(1)">›</button>
        </div>
        <div class="reviews-dots" id="reviewsDots"></div>
    </div>

    <script>
        // Inject real data from server - NO MORE MOCK DATA
        window.mitraBusinessData = @json($business);
        window.mitraGalleryImages = @json($galleryImages);
        window.mitraServices = @json($services);
        window.mitraReviews = @json($reviews);
    </script>
    <script src="{{ asset('js/minipro.js') }}?v={{ time() }}"></script>
    <script>
        // Listen untuk update avatar dari halaman profil
        if (typeof BroadcastChannel !== 'undefined') {
            const channel = new BroadcastChannel('profile_update');
            channel.onmessage = (event) => {
                if (event.data.type === 'avatar_updated') {
                    // Update all avatar images in comments/reviews
                    document.querySelectorAll('.user-icon-img, .avatar__image, .reviewer-photo').forEach(img => {
                        img.src = event.data.avatar;
                    });
                    console.log('🔄 Avatar synced from other tab');
                }
            };
        }
    </script>
</body>
</html>
