<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-role" content="mitra">
    <title>PRISMO - Review</title>
    <link rel="stylesheet" href="{{ asset('css/review.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preload" href="/images/logo.png" as="image">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="header__content">
                <div class="header__left">
                    <div class="header__brand">
                        <img src="{{ asset('images/logo.png') }}" alt="PRISMO" class="logo" width="120" height="40">
                    </div>
                    
                    <nav class="nav nav--main" aria-label="Navigasi utama">
                        <a href="{{ url('/dashboard-mitra') }}" class="nav__item" data-page="dashboard">
                            Dashboard
                        </a>
                        <a href="{{ url('/mitra/saldo/saldo') }}" class="nav__item" data-page="saldo">
                            Saldo
                        </a>
                        <a href="{{ url('/mitra/antrian/antrian') }}" class="nav__item" data-page="antrian">
                            Antrian
                        </a>
                        <a href="#" class="nav__item nav__item--active" data-page="review">
                            Review
                        </a>
                    </nav>
                </div>

                <button class="notification-btn" id="notifBtn" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">0</span>
                </button>

                <div class="user-menu">
                    <button class="user-menu__toggle" aria-expanded="false" aria-label="Menu pengguna">
                        <div class="avatar">
                            <img src="{{ asset(auth()->user()->avatar ?? 'images/profile.png') }}?v={{ auth()->user()->updated_at->timestamp }}"
                                 alt="Avatar Mitra PRISMO" class="avatar__image" width="40" height="40">
                        </div>
                        <div class="user-info">
                            <span class="user-info__name">{{ auth()->user()->name }}</span>
                            <span class="user-info__role">Mitra</span>
                        </div>
                    </button>
                </div>

                <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu mobile" aria-expanded="false">
                    <span class="menu-toggle__bar"></span>
                    <span class="menu-toggle__bar"></span>
                    <span class="menu-toggle__bar"></span>
                </button>
            </div>
        </header>

        <main class="main" id="mainContent">
            <!-- Page Header dengan Rating Summary dan Distribution -->
            <div class="page-header">
                <div class="page-header__content">
                    
                    <div class="page-header__main-content">
                        <!-- Rating Summary Sederhana -->
                        <div class="rating-summary-simple">
                            <div class="rating-display">
                                <span class="rating-star-large">★</span>
                                <div class="rating-text">
                                    <span class="rating-score" id="averageRating">0.0</span>
                                    <span class="rating-out-of">/5.0</span>
                                </div>
                            </div>
                            <div class="total-reviews" id="totalReviewsCount">0 Reviews</div>
                        </div>

                        <!-- Rating Distribution -->
                        <div class="rating-distribution">
                            <div class="distribution-item">
                                <span class="distribution-label">5</span>
                                <div class="distribution-bar">
                                    <div class="distribution-progress" id="progress5" style="width: 0%"></div>
                                </div>
                                <span class="distribution-count" id="count5">0</span>
                            </div>
                            <div class="distribution-item">
                                <span class="distribution-label">4</span>
                                <div class="distribution-bar">
                                    <div class="distribution-progress" id="progress4" style="width: 0%"></div>
                                </div>
                                <span class="distribution-count" id="count4">0</span>
                            </div>
                            <div class="distribution-item">
                                <span class="distribution-label">3</span>
                                <div class="distribution-bar">
                                    <div class="distribution-progress" id="progress3" style="width: 0%"></div>
                                </div>
                                <span class="distribution-count" id="count3">0</span>
                            </div>
                            <div class="distribution-item">
                                <span class="distribution-label">2</span>
                                <div class="distribution-bar">
                                    <div class="distribution-progress" id="progress2" style="width: 0%"></div>
                                </div>
                                <span class="distribution-count" id="count2">0</span>
                            </div>
                            <div class="distribution-item">
                                <span class="distribution-label">1</span>
                                <div class="distribution-bar">
                                    <div class="distribution-progress" id="progress1" style="width: 0%"></div>
                                </div>
                                <span class="distribution-count" id="count1">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="review-filters">
                <div class="filter-group">
                    <label for="sortFilter" class="filter-label">Urutkan:</label>
                    <select id="sortFilter" class="filter-select">
                        <option value="newest">Terbaru</option>
                        <option value="oldest">Terlama</option>
                        <option value="highest">Rating Tertinggi</option>
                        <option value="lowest">Rating Terendah</option>
                        <option value="with_images">Dengan Foto</option>
                        <option value="needs_reply">Perlu Respon</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="ratingFilter" class="filter-label">Filter Rating:</label>
                    <select id="ratingFilter" class="filter-select">
                        <option value="all">Semua Rating</option>
                        <option value="5">5 Bintang</option>
                        <option value="4">4 Bintang</option>
                        <option value="3">3 Bintang</option>
                        <option value="2">2 Bintang</option>
                        <option value="1">1 Bintang</option>
                    </select>
                </div>
            </div>

            <!-- Review List Container -->
            <div class="reviews-container" id="reviewsContainer">
                <!-- Review items will be populated by JavaScript -->
            </div>

            <!-- Pagination seperti Shopee -->
            <div class="pagination-container" id="paginationContainer">
                <nav class="pagination" aria-label="Navigasi halaman review">
                    <button class="pagination__btn pagination__btn--prev" id="prevPage" disabled>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M10.707 2.293a1 1 0 010 1.414L6.414 8l4.293 4.293a1 1 0 01-1.414 1.414l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 0z"/>
                        </svg>
                    </button>
                    
                    <div class="pagination__pages" id="paginationPages">
                        <!-- Page numbers will be generated by JavaScript -->
                    </div>
                    
                    <button class="pagination__btn pagination__btn--next" id="nextPage">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M5.293 2.293a1 1 0 011.414 0l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414-1.414L9.586 8 5.293 3.707a1 1 0 010-1.414z"/>
                        </svg>
                    </button>
                </nav>
                
                <div class="pagination-info">
                    Menampilkan <span id="currentRange">1-10</span> dari <span id="totalReviews">0</span> review
                </div>
            </div>
        </main>
    </div>

    <!-- Reply Modal Template -->
    <template id="replyModalTemplate">
        <div class="modal-overlay" role="dialog" aria-labelledby="replyModalTitle" aria-modal="true">
            <div class="modal modal--reply">
                <div class="modal__header">
                    <h3 id="replyModalTitle" class="modal__title">Respon Review</h3>
                    <button class="modal__close" aria-label="Tutup modal">✕</button>
                </div>
                <div class="modal__content">
                    <div class="original-review" id="originalReviewContent">
                        <!-- Original review content will be inserted here -->
                    </div>
                    <form class="reply-form" id="replyForm">
                        <div class="form-group">
                            <label for="replyMessage" class="form-label">Respon Anda:</label>
                            <textarea 
                                id="replyMessage" 
                                class="form-textarea" 
                                placeholder="Tulis respon untuk review ini..."
                                rows="4"
                                required
                            ></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn--secondary" data-action="cancel">Batal</button>
                            <button type="submit" class="btn btn--primary">Kirim Respon</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- Edit Reply Modal Template -->
    <template id="editReplyModalTemplate">
        <div class="modal-overlay" role="dialog" aria-labelledby="editReplyModalTitle" aria-modal="true">
            <div class="modal modal--reply">
                <div class="modal__header">
                    <h3 id="editReplyModalTitle" class="modal__title">Edit Respon</h3>
                    <button class="modal__close" aria-label="Tutup modal">✕</button>
                </div>
                <div class="modal__content">
                    <div class="original-review" id="editOriginalReviewContent">
                        <!-- Original review content will be inserted here -->
                    </div>
                    <form class="reply-form" id="editReplyForm">
                        <div class="form-group">
                            <label for="editReplyMessage" class="form-label">Edit Respon Anda:</label>
                            <textarea 
                                id="editReplyMessage" 
                                class="form-textarea" 
                                placeholder="Edit respon untuk review ini..."
                                rows="4"
                                required
                            ></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn--secondary" data-action="cancel">Batal</button>
                            <button type="submit" class="btn btn--primary">Update Respon</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- Delete Confirmation Modal Template -->
    <template id="deleteConfirmModalTemplate">
        <div class="modal-overlay" role="dialog" aria-labelledby="deleteConfirmTitle" aria-modal="true">
            <div class="modal modal--confirm">
                <div class="modal__header">
                    <h3 id="deleteConfirmTitle" class="modal__title">Konfirmasi Hapus</h3>
                </div>
                <div class="modal__body">
                    <p class="confirm-message">Apakah Anda yakin ingin menghapus respon ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal__footer">
                    <button type="button" class="btn btn--secondary" data-action="cancel">Batal</button>
                    <button type="button" class="btn btn--danger" data-action="confirm-delete">Hapus Respon</button>
                </div>
            </div>
        </div>
    </template>

    <!-- Success Modal Template -->
    <template id="successModalTemplate">
        <div class="modal-overlay" role="dialog" aria-labelledby="successModalTitle" aria-modal="true">
            <div class="modal modal--success">
                <div class="modal__content modal__content--centered">
                    <div class="success-icon" aria-hidden="true">✓</div>
                    <h3 id="successModalTitle" class="modal__title">Berhasil!</h3>
                    <p class="modal__message" id="successMessage">Respon telah dikirim</p>
                    <button type="button" class="btn btn--success" data-action="close">Tutup</button>
                </div>
            </div>
        </div>
    </template>

    <!-- Mobile Menu Template -->
    <template id="mobileMenuTemplate">
        <div class="mobile-menu" id="mobileMenu">
            <div class="mobile-menu__header">
                <img src="{{ asset('images/logo.png') }}" alt="PRISMO" class="logo" width="120" height="40">
                <button class="mobile-menu__close" id="mobileMenuClose" aria-label="Tutup menu">
                    ✕
                </button>
            </div>

            <div class="mobile-user-profile" id="mobileUserProfile">
                <div class="avatar">
                    <img src="{{ asset(auth()->user()->avatar ?? 'images/profile.png') }}?v={{ auth()->user()->updated_at->timestamp }}"
                         alt="Avatar Mitra PRISMO" class="avatar__image" width="50" height="50">
                </div>
                <div class="mobile-user-profile__info">
                    <span class="user-info__name">{{ auth()->user()->name }}</span>
                    <span class="user-info__role">Mitra</span>
                </div>
            </div>

            <nav class="mobile-nav" aria-label="Navigasi mobile">
                <a href="{{ url('/dashboard-mitra') }}" class="mobile-nav__item" data-page="dashboard">
                    <div class="mobile-nav__item-content">
                        Dashboard
                    </div>
                </a>
                <a href="{{ url('/mitra/saldo/saldo') }}" class="mobile-nav__item" data-page="saldo">
                    <div class="mobile-nav__item-content">
                        Saldo
                    </div>
                </a>
                <a href="{{ url('/mitra/antrian/antrian') }}" class="mobile-nav__item" data-page="antrian">
                    <div class="mobile-nav__item-content">
                        Antrian
                    </div>
                </a>
                <a href="#" class="mobile-nav__item mobile-nav__item--active" data-page="review">
                    <div class="mobile-nav__item-content">
                        Review
                    </div>
                </a>
            </nav>
        </div>
    </template>

    <!-- Notification Panel -->
    <div id="notifPanel" class="notification-panel">
        <div class="notification-panel-header">
            <h3>Notifikasi</h3>
            <button id="markAllReadBtn" class="mark-all-read-btn">Tandai Semua Dibaca</button>
        </div>
        <div id="notificationList" class="notification-list">
            <div class="notification-loading">Memuat notifikasi...</div>
        </div>
    </div>
    <div id="notifOverlay" class="notification-overlay"></div>

    <script>
        // Inject real data from server - NO MORE MOCK DATA
        window.reviewsData = @json($reviews);
        window.totalReviews = @json($totalReviews);
        window.averageRating = @json($averageRating);
        window.ratingDistribution = @json($ratingDistribution);
    </script>
    <script src="{{ asset('js/browser-notification.js') }}"></script>
    <script src="{{ asset('js/notification-system.js') }}"></script>
    <script src="{{ asset('js/mitra-badge-manager.js') }}"></script>
    <script src="{{ asset('js/review.js') }}"></script>
    <script>
        // Listen untuk update avatar dari halaman profil
        if (typeof BroadcastChannel !== 'undefined') {
            const channel = new BroadcastChannel('profile_update');
            channel.onmessage = (event) => {
                if (event.data.type === 'avatar_updated') {
                    document.querySelectorAll('.avatar__image').forEach(img => {
                        img.src = event.data.avatar;
                    });
                    console.log('🔄 Avatar synced from other tab');
                }
            };
        }
    </script>
</body>
</html>
