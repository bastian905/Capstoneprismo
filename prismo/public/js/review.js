// ===== PRISMO REVIEW MANAGER =====
class PrismoReviewManager {
    constructor() {
        this.notifications = {
            antrian: 3,
            review: 5
        };

        // Load real data from server - NO MORE MOCK DATA
        this.reviews = window.reviewsData || this.getInitialReviews();
        this.currentSort = 'newest';
        this.currentRatingFilter = 'all';
        this.isInitialized = false;
        this.isMobileMenuOpen = false;
        this.currentReplyReviewId = null;

        // Pagination settings
        this.currentPage = 1;
        this.itemsPerPage = 10;
        this.totalPages = 1;

        // Template untuk image modal
        this.imagesModalTemplate = `
            <div class="modal-overlay" role="dialog" aria-labelledby="imageModalTitle" aria-modal="true">
                <div class="modal modal--image">
                    <div class="modal__header">
                        <h3 id="imageModalTitle" class="modal__title">Gambar Review</h3>
                        <button class="modal__close" aria-label="Tutup modal">✕</button>
                    </div>
                    <div class="modal__content image-modal__content">
                        <img src="" alt="" class="image-modal__image" id="modalImage">
                        <p class="image-modal__caption" id="modalImageCaption"></p>
                    </div>
                </div>
            </div>
        `;

        this.init();
    }

    getInitialReviews() {
        // Generate more reviews for pagination demo
        const baseReviews = [
            {
                id: 1,
                reviewer: {
                    name: "Ahmad Rizki",
                    avatar: "/images/profile.png"
                },
                rating: 5,
                comment: "Pelayanan sangat memuaskan! Mobil jadi bersih mengkilap dan wangi! Teknisinya juga ramah dan profesional. Pasti akan kembali lagi!",
                time: "2 jam yang lalu",
                service: "Premium Steam",
                hasReply: true,
                images: [
                    "https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=150&h=150&fit=crop"
                ],
                reply: {
                    text: "Terima kasih atas reviewnya, Pak Ahmad! Kami senang bisa memberikan pelayanan terbaik. Ditunggu kunjungan berikutnya!",
                    time: "1 jam yang lalu"
                }
            },
            {
                id: 2,
                reviewer: {
                    name: "Sari Dewi",
                    avatar: "/images/profile.png"
                },
                rating: 4,
                comment: "Hasilnya bagus, tapi agak lama menunggu. Mungkin bisa dipercepat prosesnya. Overall puas dengan hasilnya.",
                time: "2 jam yang lalu",
                service: "Premium Steam",
                hasReply: false,
                images: [
                    "https://images.unsplash.com/photo-1507136566006-cfc505b114fc?w=150&h=150&fit=crop"
                ]
            },
            {
                id: 3,
                reviewer: {
                    name: "Rudi Santoso",
                    avatar: "/images/profile.png"
                },
                rating: 3,
                comment: "Cukup puas dengan pelayanan, tapi ada beberapa area yang kurang bersih. Bagian dalam mobil masih ada debu di sudut-sudutnya.",
                time: "3 jam yang lalu",
                service: "Basic Wash",
                hasReply: true,
                images: [
                    "https://images.unsplash.com/photo-1568602471122-7832951cc4c5?w=150&h=150&fit=crop",
                    "https://images.unsplash.com/photo-1566479179814-9bf9775fb572?w=150&h=150&fit=crop",
                    "https://images.unsplash.com/photo-1566566716926-1c0d506c01c9?w=150&h=150&fit=crop",
                    "https://images.unsplash.com/photo-1578708813026-ab9c8ad02275?w=150&h=150&fit=crop"
                ],
                reply: {
                    text: "Terima kasih atas masukannya, Pak Rudi! Kami akan perbaiki kualitas pelayanan dan lebih detail dalam membersihkan sudut-sudut mobil.",
                    time: "2 jam yang lalu"
                }
            },
            {
                id: 4,
                reviewer: {
                    name: "Robi Wijaya",
                    avatar: "/images/profile.png"
                },
                rating: 2,
                comment: "Kurang puas dengan pelayanan. Mobil masih ada bekas sabun dan tidak dikeringkan dengan baik. Harus diulang lagi.",
                time: "5 jam yang lalu",
                service: "Express Wash",
                hasReply: false,
                images: [
                    "https://images.unsplash.com/photo-1507136566006-cfc505b114fc?w=150&h=150&fit=crop"
                ]
            },
            {
                id: 5,
                reviewer: {
                    name: "Maya Putri",
                    avatar: "/images/profile.png"
                },
                rating: 1,
                comment: "Pelayanan sangat buruk! Mobil saya malah ada baret baru setelah dicuci. Sangat kecewa!",
                time: "6 jam yang lalu",
                service: "Premium Steam",
                hasReply: true,
                images: [],
                reply: {
                    text: "Kami mohon maaf atas ketidaknyamanannya, Bu Maya. Tim kami akan segera memeriksa dan memberikan solusi terbaik. Silakan hubungi customer service kami untuk penanganan lebih lanjut.",
                    time: "4 jam yang lalu"
                }
            },
            {
                id: 6,
                reviewer: {
                    name: "David Wilson",
                    avatar: "/images/profile.png"
                },
                rating: 5,
                comment: "Excellent service! My car has never been this clean. The team is very professional and thorough.",
                time: "1 hari yang lalu",
                service: "Full Detailing",
                hasReply: false,
                images: [
                    "https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=150&h=150&fit=crop",
                    "https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=150&h=150&fit=crop"
                ]
            }
        ];

        // Generate more reviews for pagination
        const additionalReviews = [];
        const names = ["Budi Santoso", "Siti Rahayu", "Joko Widodo", "Dewi Lestari", "Agus Pratama", "Maya Sari", "Rizki Ramadhan", "Linda Wijaya", "Hendra Kurniawan", "Fitriani"];
        const services = ["Premium Steam", "Basic Wash", "Express Wash", "Full Detailing", "Interior Cleaning"];
        const times = ["1 hari yang lalu", "2 hari yang lalu", "3 hari yang lalu", "1 minggu yang lalu", "2 minggu yang lalu"];

        for (let i = 7; i <= 1248; i++) {
            additionalReviews.push({
                id: i,
                reviewer: {
                    name: names[Math.floor(Math.random() * names.length)],
                    avatar: "/images/profile.png"
                },
                rating: Math.floor(Math.random() * 5) + 1,
                comment: `Review otomatis ${i}. Pelayanan ${Math.random() > 0.3 ? 'sangat memuaskan' : 'cukup baik'}.`,
                time: times[Math.floor(Math.random() * times.length)],
                service: services[Math.floor(Math.random() * services.length)],
                hasReply: Math.random() > 0.7,
                images: Math.random() > 0.5 ? [
                    "https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=150&h=150&fit=crop"
                ] : [],
                reply: Math.random() > 0.7 ? {
                    text: "Terima kasih atas reviewnya! Kami akan terus berusaha memberikan pelayanan terbaik.",
                    time: "1 hari yang lalu"
                } : null
            });
        }

        return [...baseReviews, ...additionalReviews];
    }

    init() {
    if (this.isInitialized) return;

    try {
        this.setupEventListeners();
        this.setupMobileMenu();
        this.setupProfileNavigation();
        this.updateAllBadges();
        this.calculateTotalPages();
        this.displayReviews();
        this.updateRatingSummary(); // Hanya ini
        this.updatePaginationInfo();
        this.isInitialized = true;

        console.log('✅ PRISMO Review Manager initialized');
    } catch (error) {
        console.error('❌ Failed to initialize PRISMO Review Manager:', error);
    }
}

    setupEventListeners() {
        // Use direct event delegation for better reliability
        document.addEventListener('click', (event) => {
            this.handleGlobalClick(event);
        });
        
        document.addEventListener('keydown', (event) => {
            this.handleGlobalKeydown(event);
        });
        
        // Filter events
        const sortFilter = document.getElementById('sortFilter');
        const ratingFilter = document.getElementById('ratingFilter');
        
        if (sortFilter) {
            sortFilter.addEventListener('change', (event) => {
                this.currentSort = event.target.value;
                this.currentPage = 1; // Reset to first page
                this.displayReviews();
                this.updatePagination();
            });
        }
        
        if (ratingFilter) {
            ratingFilter.addEventListener('change', (event) => {
                this.currentRatingFilter = event.target.value;
                this.currentPage = 1; // Reset to first page
                this.calculateTotalPages();
                this.displayReviews();
                this.updatePagination();
            });
        }

        // Pagination events
        this.setupPaginationEvents();
    }

    setupPaginationEvents() {
        document.addEventListener('click', (event) => {
            // Previous page
            if (event.target.closest('#prevPage')) {
                event.preventDefault();
                if (this.currentPage > 1) {
                    this.currentPage--;
                    this.displayReviews();
                    this.updatePagination();
                    this.updatePaginationInfo();
                }
                return;
            }

            // Next page
            if (event.target.closest('#nextPage')) {
                event.preventDefault();
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                    this.displayReviews();
                    this.updatePagination();
                    this.updatePaginationInfo();
                }
                return;
            }

            // Page number click
            const pageBtn = event.target.closest('.pagination__page');
            if (pageBtn && !pageBtn.classList.contains('pagination__page--active')) {
                event.preventDefault();
                const page = parseInt(pageBtn.dataset.page);
                if (page && page !== this.currentPage) {
                    this.currentPage = page;
                    this.displayReviews();
                    this.updatePagination();
                    this.updatePaginationInfo();
                }
            }
        });
    }

    setupProfileNavigation() {
        // Setup user menu to navigate to profile (desktop)
        const userMenuToggle = document.querySelector('.user-menu__toggle');
        if (userMenuToggle) {
            userMenuToggle.addEventListener('click', (e) => {
                e.preventDefault();
                window.location.href = '/mitra/profil/profil';
            });
        }
        
        // Avatar now loaded from database via Blade template

        // Setup mobile user profile to navigate to profile
        const mobileUserProfile = document.getElementById('mobileUserProfile');
        if (mobileUserProfile) {
            mobileUserProfile.addEventListener('click', (e) => {
                e.preventDefault();
                this.closeMobileMenu();
                window.location.href = '/mitra/profil/profil';
            });
        }
    }

    handleGlobalClick(event) {
        const target = event.target;

        // Reply buttons
        if (target.closest('[data-action="reply"]')) {
            event.preventDefault();
            event.stopPropagation();
            const button = target.closest('[data-action="reply"]');
            const reviewId = parseInt(button.getAttribute('data-review-id'));
            console.log('🔍 Reply button clicked, reviewId:', reviewId);
            this.handleReplyClick(reviewId);
            return;
        }

        // Edit reply buttons
        if (target.closest('[data-action="edit-reply"]')) {
            event.preventDefault();
            event.stopPropagation();
            const button = target.closest('[data-action="edit-reply"]');
            const reviewId = parseInt(button.getAttribute('data-review-id'));
            console.log('✏️ Edit reply button clicked, reviewId:', reviewId);
            this.handleEditReplyClick(reviewId);
            return;
        }

        // Delete reply buttons
        if (target.closest('[data-action="delete-reply"]')) {
            event.preventDefault();
            event.stopPropagation();
            const button = target.closest('[data-action="delete-reply"]');
            const reviewId = parseInt(button.getAttribute('data-review-id'));
            console.log('🗑️ Delete reply button clicked, reviewId:', reviewId);
            this.handleDeleteReplyClick(reviewId);
            return;
        }

        // Modal actions
        if (target.matches('.modal-overlay')) {
            this.closeModal(target);
            return;
        }

        if (target.closest('[data-action="cancel"]')) {
            event.preventDefault();
            this.closeCurrentModal();
            return;
        }

        if (target.closest('[data-action="close"]')) {
            event.preventDefault();
            this.closeCurrentModal();
            return;
        }

        if (target.closest('[data-action="delete"]')) {
            event.preventDefault();
            this.handleDeleteReplyFromModal();
            return;
        }

        // Form submissions
        if (target.closest('#replyForm') && target.type === 'submit') {
            event.preventDefault();
            console.log('📝 Form submit detected');
            this.handleReplySubmit();
            return;
        }

        if (target.closest('#editReplyForm') && target.type === 'submit') {
            event.preventDefault();
            console.log('📝 Edit form submit detected');
            this.handleEditReplySubmit();
            return;
        }

        // Image click handler
        if (target.closest('.review-image')) {
            const reviewImage = target.closest('.review-image');
            this.handleImageClick(reviewImage);
            return;
        }

        // Mobile menu toggle
        if (target.closest('.menu-toggle')) {
            this.toggleMobileMenu();
            return;
        }

        // Mobile menu close
        if (target.closest('.mobile-menu__close')) {
            this.closeMobileMenu();
            return;
        }
    }

    handleGlobalKeydown(event) {
        if (event.key === 'Escape') {
            const openModal = document.querySelector('.modal-overlay');
            if (openModal) {
                this.closeModal(openModal);
            }
            
            if (this.isMobileMenuOpen) {
                this.closeMobileMenu();
            }
        }
    }

    // ===== PAGINATION METHODS =====
    calculateTotalPages() {
        const filteredReviews = this.filterReviews();
        this.totalPages = Math.ceil(filteredReviews.length / this.itemsPerPage);
    }

    getPaginatedReviews() {
        const filteredReviews = this.filterReviews();
        const sortedReviews = this.sortReviews(filteredReviews);
        
        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        
        return sortedReviews.slice(startIndex, endIndex);
    }

    updatePagination() {
        const paginationPages = document.getElementById('paginationPages');
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');

        if (!paginationPages) return;

        // Update button states
        prevBtn.disabled = this.currentPage === 1;
        nextBtn.disabled = this.currentPage === this.totalPages;

        // Generate page numbers (Shopee-like: show first, last, and pages around current)
        let pagesHTML = '';
        const maxVisiblePages = 5;
        
        if (this.totalPages <= maxVisiblePages) {
            // Show all pages
            for (let i = 1; i <= this.totalPages; i++) {
                pagesHTML += this.createPageButton(i);
            }
        } else {
            // Complex pagination logic like Shopee
            if (this.currentPage <= 3) {
                // Show first 4 pages and last page
                for (let i = 1; i <= 4; i++) {
                    pagesHTML += this.createPageButton(i);
                }
                pagesHTML += '<span class="pagination__ellipsis">...</span>';
                pagesHTML += this.createPageButton(this.totalPages);
            } else if (this.currentPage >= this.totalPages - 2) {
                // Show first page and last 4 pages
                pagesHTML += this.createPageButton(1);
                pagesHTML += '<span class="pagination__ellipsis">...</span>';
                for (let i = this.totalPages - 3; i <= this.totalPages; i++) {
                    pagesHTML += this.createPageButton(i);
                }
            } else {
                // Show first, current-1, current, current+1, last
                pagesHTML += this.createPageButton(1);
                pagesHTML += '<span class="pagination__ellipsis">...</span>';
                for (let i = this.currentPage - 1; i <= this.currentPage + 1; i++) {
                    pagesHTML += this.createPageButton(i);
                }
                pagesHTML += '<span class="pagination__ellipsis">...</span>';
                pagesHTML += this.createPageButton(this.totalPages);
            }
        }

        paginationPages.innerHTML = pagesHTML;
    }

    createPageButton(page) {
        const isActive = page === this.currentPage;
        return `
            <button class="pagination__page ${isActive ? 'pagination__page--active' : ''}" 
                    data-page="${page}"
                    ${isActive ? 'aria-current="page"' : ''}
                    aria-label="Halaman ${page}">
                ${page}
            </button>
        `;
    }

    updatePaginationInfo() {
        const currentRange = document.getElementById('currentRange');
        const totalReviews = document.getElementById('totalReviews');
        
        if (currentRange && totalReviews) {
            const filteredReviews = this.filterReviews();
            const startIndex = (this.currentPage - 1) * this.itemsPerPage + 1;
            const endIndex = Math.min(startIndex + this.itemsPerPage - 1, filteredReviews.length);
            
            currentRange.textContent = `${startIndex}-${endIndex}`;
            totalReviews.textContent = filteredReviews.length.toLocaleString();
        }
    }

    // ===== RATING SUMMARY =====
    updateRatingSummary() {
    const stats = this.getReviewStats();
    
    // Update average rating
    const averageRating = document.getElementById('averageRating');
    const totalReviewsCount = document.getElementById('totalReviewsCount');
    
    if (averageRating && totalReviewsCount) {
        averageRating.textContent = stats.average;
        totalReviewsCount.textContent = `${stats.total.toLocaleString()} Reviews`;
    }

    // Update rating distribution
    this.updateRatingDistribution(stats.ratingCounts, stats.total);
}

    updateRatingDistribution(ratingCounts, totalReviews) {
    for (let i = 1; i <= 5; i++) {
        const count = ratingCounts[i] || 0;
        const percentage = totalReviews > 0 ? (count / totalReviews) * 100 : 0;
        
        const progressBar = document.getElementById(`progress${i}`);
        const countElement = document.getElementById(`count${i}`);
        
        if (progressBar) {
            progressBar.style.width = `${percentage}%`;
        }
        
        if (countElement) {
            countElement.textContent = count.toLocaleString();
        }
    }
}

    calculateAverageRating() {
        if (this.reviews.length === 0) return 0;
        
        const total = this.reviews.reduce((sum, review) => sum + review.rating, 0);
        return (total / this.reviews.length).toFixed(1);
    }

    // ===== PAGE HEADER STATS =====
    updatePageHeaderStats() {
        const stats = this.getReviewStats();
        const pendingReplies = this.reviews.filter(review => !review.hasReply).length;
        
        const totalReviewsCount = document.getElementById('totalReviewsCount');
        const pendingRepliesCount = document.getElementById('pendingRepliesCount');
        
        if (totalReviewsCount) {
            totalReviewsCount.textContent = stats.total.toLocaleString();
        }
        
        if (pendingRepliesCount) {
            pendingRepliesCount.textContent = pendingReplies.toLocaleString();
        }
    }

    // ===== REVIEW DISPLAY =====
    displayReviews() {
        console.log('🔄 Displaying reviews...');
        console.log('📋 Current page:', this.currentPage);
        console.log('🔍 Current filter:', this.currentRatingFilter);
        console.log('📊 Current sort:', this.currentSort);
        
        const paginatedReviews = this.getPaginatedReviews();
        this.renderReviews(paginatedReviews);
        this.updatePagination();
    }

    filterReviews() {
        let filtered = [...this.reviews];
        
        // Apply rating filter
        if (this.currentRatingFilter !== 'all') {
            const rating = parseInt(this.currentRatingFilter);
            filtered = filtered.filter(review => review.rating === rating);
        }

        // Apply special filters
        if (this.currentSort === 'with_images') {
            filtered = filtered.filter(review => review.images && review.images.length > 0);
        } else if (this.currentSort === 'needs_reply') {
            filtered = filtered.filter(review => !review.hasReply);
        }

        return filtered;
    }

    sortReviews(reviews) {
        const sorted = [...reviews];
        
        switch (this.currentSort) {
            case 'newest':
                return sorted.sort((a, b) => b.id - a.id);
            case 'oldest':
                return sorted.sort((a, b) => a.id - b.id);
            case 'highest':
                return sorted.sort((a, b) => b.rating - a.rating);
            case 'lowest':
                return sorted.sort((a, b) => a.rating - b.rating);
            case 'with_images':
            case 'needs_reply':
                // Already filtered, just sort by newest
                return sorted.sort((a, b) => b.id - a.id);
            default:
                return sorted;
        }
    }

    renderReviews(reviews) {
        const container = document.getElementById('reviewsContainer');
        if (!container) {
            console.error('❌ Reviews container not found!');
            return;
        }

        console.log('🎨 Rendering', reviews.length, 'reviews to container');

        container.innerHTML = '';

        if (reviews.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <p>Tidak ada review yang sesuai dengan filter</p>
                </div>
            `;
            return;
        }

        reviews.forEach(review => {
            const reviewElement = this.createReviewElement(review);
            container.appendChild(reviewElement);
        });

        console.log('✅ Reviews rendered successfully');
    }

    createReviewElement(review) {
        const element = document.createElement('div');
        element.className = 'review-item';
        element.setAttribute('data-review-id', review.id);

        const stars = this.generateStars(review.rating);
        
        let imagesSection = '';
        if (review.images && review.images.length > 0) {
            const imagesHTML = this.generateImageGrid(review.images, review.id, review.reviewer.name);
            imagesSection = `
                <div class="review-images">
                    ${imagesHTML}
                </div>
            `;
        }

        let replySection = '';
        let actionButtons = '';

        if (review.hasReply && review.reply) {
            // Review sudah direspon - tampilkan reply dan tombol edit/hapus
            replySection = `
                <div class="reply-section">
                    <div class="reply-header">
                        <div>
                            <span class="reply-badge">Respon Anda</span>
                            <span class="reply-time">${review.reply.time}</span>
                        </div>
                        <div class="reply-actions">
                            <button class="btn btn--outline btn--small" 
                                    data-action="edit-reply" 
                                    data-review-id="${review.id}"
                                    aria-label="Edit respon">
                                Edit
                            </button>
                            <button class="btn btn--danger btn--small" 
                                    data-action="delete-reply" 
                                    data-review-id="${review.id}"
                                    aria-label="Hapus respon">
                                Hapus
                            </button>
                        </div>
                    </div>
                    <div class="reply-text">
                        ${review.reply.text}
                    </div>
                </div>
            `;
        } else {
            // Review belum direspon - tampilkan tombol respon
            actionButtons = `
                <div class="review-actions">
                    <button class="btn btn--primary" 
                            data-action="reply" 
                            data-review-id="${review.id}"
                            aria-label="respon review dari ${review.reviewer.name}">
                        Respon
                    </button>
                </div>
            `;
        }

        element.innerHTML = `
            <div class="review-header">
                <div class="reviewer-info">
                    <div class="reviewer-avatar">
                        <img src="${review.reviewer.avatar}" 
                             alt="${review.reviewer.name}" 
                             class="reviewer-avatar-image"
                             loading="lazy">
                    </div>
                    <div class="reviewer-details">
                        <h3 class="reviewer-name">${review.reviewer.name}</h3>
                    </div>
                </div>
                <div class="review-meta">
                    <div class="rating-stars">${stars}</div>
                    <div class="review-time">${review.time}</div>
                    <div class="service-info">${review.service}</div>
                </div>
            </div>
            
            <div class="review-content">
                <div class="review-text">
                    ${review.comment}
                </div>
                ${imagesSection}
            </div>
            
            ${replySection}
            ${actionButtons}
        `;

        return element;
    }

    // ===== IMAGE GRID SYSTEM =====
    generateImageGrid(images, reviewId, reviewerName) {
        let imagesHTML = '';
        
        images.forEach((image, index) => {
            imagesHTML += `
                <div class="review-image" data-image-index="${index}" data-review-id="${reviewId}">
                    <img src="${image}" alt="Gambar review ${index + 1} dari ${reviewerName}" loading="lazy">
                </div>
            `;
        });

        return imagesHTML;
    }

    // ===== STAR RATING SYSTEM =====
    generateStars(rating) {
        let stars = '';
        const fullStar = '★';
        const emptyStar = '☆';
        
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                stars += fullStar;
            } else {
                stars += emptyStar;
            }
        }
        
        return stars;
    }

    // ===== IMAGE FUNCTIONALITY =====
    handleImageClick(imageElement) {
        const reviewId = parseInt(imageElement.dataset.reviewId);
        const imageIndex = parseInt(imageElement.dataset.imageIndex);
        
        const review = this.reviews.find(r => r.id === reviewId);
        if (!review || !review.images || !review.images[imageIndex]) {
            console.error('Image not found');
            this.showErrorAlert('Gambar tidak ditemukan');
            return;
        }

        this.showImageModal(review.images[imageIndex], review.reviewer.name, imageIndex + 1, review.images.length);
    }

    showImageModal(imageUrl, reviewerName, imageNumber, totalImages) {
        const modal = document.createElement('div');
        modal.innerHTML = this.imagesModalTemplate;
        
        const modalElement = modal.firstElementChild;
        const modalImage = modalElement.querySelector('#modalImage');
        const modalCaption = modalElement.querySelector('#modalImageCaption');
        
        const largeImageUrl = imageUrl.replace('w=150&h=150', 'w=600&h=600');
        modalImage.src = largeImageUrl;
        modalImage.alt = `Gambar review dari ${reviewerName}`;
        modalCaption.textContent = `Gambar ${imageNumber} dari ${totalImages} - ${reviewerName}`;
        
        document.body.appendChild(modalElement);
        
        const closeButton = modalElement.querySelector('.modal__close');
        const closeModal = () => {
            document.body.removeChild(modalElement);
        };
        
        closeButton.addEventListener('click', closeModal);
        modalElement.addEventListener('click', (event) => {
            if (event.target === modalElement) {
                closeModal();
            }
        });

        this.setupModalFocusTrap(modalElement);
    }

    // ===== REPLY FUNCTIONALITY =====
    handleReplyClick(reviewId) {
        console.log('🚀 REPLY CLICKED - Review ID:', reviewId);
        
        // Store the review ID globally
        this.currentReplyReviewId = reviewId;
        
        const review = this.reviews.find(r => r.id === reviewId);
        
        if (!review) {
            console.error('❌ REVIEW NOT FOUND - Looking for:', reviewId, 'Available:', this.reviews.map(r => r.id));
            this.showErrorAlert('Review tidak ditemukan');
            return;
        }

        console.log('✅ REVIEW FOUND:', review.reviewer.name);
        this.showReplyModal(review);
    }

    showReplyModal(review) {
        console.log('📝 SHOWING MODAL FOR:', review.reviewer.name, 'ID:', review.id);
        
        const template = document.getElementById('replyModalTemplate');
        if (!template) {
            console.error('❌ Modal template not found');
            this.showErrorAlert('Template modal tidak ditemukan');
            return;
        }

        const modal = template.content.cloneNode(true);
        const originalReview = modal.querySelector('#originalReviewContent');
        
        if (originalReview) {
            const stars = this.generateStars(review.rating);
            
            originalReview.innerHTML = `
                <div class="review-meta">
                    <div class="rating-stars">${stars}</div>
                    <div class="review-time">${review.time} • ${review.service}</div>
                </div>
                <strong>${review.reviewer.name}</strong>
                <div class="review-text">${review.comment}</div>
            `;
        }

        document.body.appendChild(modal);

        const modalElement = document.querySelector('.modal-overlay');
        const closeButton = modalElement.querySelector('.modal__close');
        const cancelButton = modalElement.querySelector('[data-action="cancel"]');
        const form = modalElement.querySelector('#replyForm');

        const closeModal = () => {
            document.body.removeChild(modalElement);
            this.currentReplyReviewId = null; // Reset
        };

        closeButton.addEventListener('click', closeModal);
        cancelButton.addEventListener('click', closeModal);

        // SIMPLE FORM HANDLING
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            console.log('📤 FORM SUBMIT - Current review ID:', this.currentReplyReviewId);
            this.handleReplySubmit();
        });

        const textarea = modalElement.querySelector('#replyMessage');
        if (textarea) {
            setTimeout(() => {
                textarea.focus();
            }, 100);
        }

        this.setupModalFocusTrap(modalElement);
    }

    handleReplySubmit() {
        // Use the globally stored review ID
        const reviewId = this.currentReplyReviewId;
        
        if (!reviewId) {
            console.error('❌ NO REVIEW ID FOUND');
            this.showErrorAlert('Tidak dapat menemukan review');
            return;
        }

        console.log('🔄 PROCESSING REPLY FOR REVIEW ID:', reviewId);
        
        const form = document.querySelector('#replyForm');
        const textarea = form.querySelector('#replyMessage');
        const replyText = textarea.value.trim();

        if (!replyText) {
            this.showErrorAlert('Silakan tulis respon terlebih dahulu');
            textarea.focus();
            return;
        }

        if (replyText.length < 10) {
            this.showErrorAlert('Respon terlalu pendek. Minimal 10 karakter.');
            textarea.focus();
            return;
        }

        const reviewIndex = this.reviews.findIndex(r => r.id === reviewId);
        console.log('🔍 REVIEW INDEX FOUND:', reviewIndex);
        
        if (reviewIndex === -1) {
            console.error('❌ REVIEW NOT FOUND IN ARRAY - ID:', reviewId, 'Available IDs:', this.reviews.map(r => r.id));
            this.showErrorAlert('Review tidak ditemukan');
            return;
        }

        console.log('✅ UPDATING REVIEW...');
        
        // Simulate API call
        setTimeout(() => {
        this.reviews[reviewIndex].hasReply = true;
        this.reviews[reviewIndex].reply = {
            text: replyText,
            time: 'Baru saja'
        };

        this.closeCurrentModal();
        this.displayReviews(); // Refresh the display
        this.updateRatingSummary(); // Hanya update rating summary
        this.showSuccessAlert('Respon berhasil dikirim!');

        // Update notification count
        this.notifications.review = Math.max(0, this.notifications.review - 1);
        this.updateAllBadges();

        this.currentReplyReviewId = null; // Reset
        console.log('🎉 REPLY SUCCESSFULLY SENT FOR REVIEW:', reviewId);
    }, 1000);
    }

    // ===== EDIT REPLY FUNCTIONALITY =====
    handleEditReplyClick(reviewId) {
        console.log('✏️ EDIT REPLY CLICKED - Review ID:', reviewId);
        
        this.currentReplyReviewId = reviewId;
        const review = this.reviews.find(r => r.id === reviewId);
        
        if (!review || !review.reply) {
            console.error('Review or reply not found');
            this.showErrorAlert('Respon tidak ditemukan');
            return;
        }

        this.showEditReplyModal(review);
    }

    showEditReplyModal(review) {
        const template = document.getElementById('editReplyModalTemplate');
        if (!template) {
            this.showErrorAlert('Template modal tidak ditemukan');
            return;
        }

        const modal = template.content.cloneNode(true);
        const originalReview = modal.querySelector('#editOriginalReviewContent');
        
        if (originalReview) {
            const stars = this.generateStars(review.rating);
            
            originalReview.innerHTML = `
                <div class="review-meta">
                    <div class="rating-stars">${stars}</div>
                    <div class="review-time">${review.time} • ${review.service}</div>
                </div>
                <strong>${review.reviewer.name}</strong>
                <div class="review-text">${review.comment}</div>
            `;
        }

        document.body.appendChild(modal);

        const modalElement = document.querySelector('.modal-overlay');
        const textarea = modalElement.querySelector('#editReplyMessage');
        const form = modalElement.querySelector('#editReplyForm');
        const closeButton = modalElement.querySelector('.modal__close');
        const cancelButton = modalElement.querySelector('[data-action="cancel"]');

        // Pre-fill with existing reply
        textarea.value = review.reply.text;

        const closeModal = () => {
            document.body.removeChild(modalElement);
            this.currentReplyReviewId = null;
        };

        closeButton.addEventListener('click', closeModal);
        cancelButton.addEventListener('click', closeModal);

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            this.handleEditReplySubmit();
        });

        setTimeout(() => {
            textarea.focus();
        }, 100);

        this.setupModalFocusTrap(modalElement);
    }

    handleEditReplySubmit() {
        const reviewId = this.currentReplyReviewId;
        
        if (!reviewId) {
            this.showErrorAlert('Tidak dapat menemukan review');
            return;
        }

        const form = document.querySelector('#editReplyForm');
        const textarea = form.querySelector('#editReplyMessage');
        const replyText = textarea.value.trim();

        if (!replyText) {
            this.showErrorAlert('Silakan tulis respon terlebih dahulu');
            textarea.focus();
            return;
        }

        if (replyText.length < 10) {
            this.showErrorAlert('Respon terlalu pendek. Minimal 10 karakter.');
            textarea.focus();
            return;
        }

        const reviewIndex = this.reviews.findIndex(r => r.id === reviewId);
        
        if (reviewIndex === -1) {
            this.showErrorAlert('Review tidak ditemukan');
            return;
        }

        // Simulate API call
        setTimeout(() => {
            this.reviews[reviewIndex].reply.text = replyText;
            this.reviews[reviewIndex].reply.time = 'Baru saja (diedit)';

            this.closeCurrentModal();
            this.displayReviews();
            this.showSuccessAlert('Respon berhasil diupdate!');

            this.currentReplyReviewId = null;
        }, 1000);
    }

    // ===== DELETE REPLY FUNCTIONALITY =====
    handleDeleteReplyClick(reviewId) {
        this.showDeleteConfirmModal(reviewId);
    }

    handleDeleteReplyFromModal() {
        this.showDeleteConfirmModal(this.currentReplyReviewId);
    }

    showDeleteConfirmModal(reviewId) {
        const template = document.getElementById('deleteConfirmModalTemplate');
        if (!template) return;

        const clone = template.content.cloneNode(true);
        const modalElement = clone.querySelector('.modal-overlay');
        
        document.body.appendChild(modalElement);

        requestAnimationFrame(() => {
            modalElement.classList.add('active');
        });

        const closeModal = () => {
            modalElement.classList.remove('active');
            setTimeout(() => modalElement.remove(), 300);
        };

        const cancelButton = modalElement.querySelector('[data-action="cancel"]');
        const confirmButton = modalElement.querySelector('[data-action="confirm-delete"]');

        cancelButton.addEventListener('click', closeModal);
        
        confirmButton.addEventListener('click', () => {
            this.handleDeleteReply(reviewId);
            closeModal();
        });

        // Close on overlay click
        modalElement.addEventListener('click', (e) => {
            if (e.target === modalElement) {
                closeModal();
            }
        });

        // Close on ESC key
        const escapeHandler = (e) => {
            if (e.key === 'Escape') {
                closeModal();
                document.removeEventListener('keydown', escapeHandler);
            }
        };
        document.addEventListener('keydown', escapeHandler);

        this.setupModalFocusTrap(modalElement);
    }

    handleDeleteReply(reviewId) {
        console.log('🗑️ DELETING REPLY FOR REVIEW:', reviewId);
        
        const reviewIndex = this.reviews.findIndex(r => r.id === reviewId);
        
        if (reviewIndex === -1) {
            this.showErrorAlert('Review tidak ditemukan');
            return;
        }

        // Simulate API call
        setTimeout(() => {
        this.reviews[reviewIndex].hasReply = false;
        this.reviews[reviewIndex].reply = null;

        this.displayReviews();
        this.updateRatingSummary(); // Hanya update rating summary
        this.showSuccessAlert('Respon berhasil dihapus!');

        // Update notification count
        this.notifications.review = Math.max(0, this.notifications.review + 1);
        this.updateAllBadges();

        console.log('✅ REPLY DELETED FOR REVIEW:', reviewId);
    }, 1000);
    }

    // ===== BADGE MANAGEMENT =====
    updateAllBadges() {
        this.updateDesktopBadges();
        this.updateMobileBadges();
    }

    updateDesktopBadges() {
        // Update antrian badge
        const antrianBadge = document.getElementById('antrian-badge');
        if (antrianBadge) {
            const count = this.notifications.antrian;
            if (count > 0) {
                antrianBadge.textContent = count;
                antrianBadge.style.display = 'flex';
                antrianBadge.setAttribute('aria-label', `${count} notifikasi antrian`);
            } else {
                antrianBadge.style.display = 'none';
            }
        }

        // Update review badge
        const reviewBadge = document.getElementById('review-badge');
        if (reviewBadge) {
            const count = this.notifications.review;
            if (count > 0) {
                reviewBadge.textContent = count;
                reviewBadge.style.display = 'flex';
                reviewBadge.setAttribute('aria-label', `${count} notifikasi review`);
            } else {
                reviewBadge.style.display = 'none';
            }
        }
    }

    updateMobileBadges() {
        // Update mobile antrian badge
        const mobileAntrianBadge = document.getElementById('mobile-antrian-badge');
        if (mobileAntrianBadge) {
            const count = this.notifications.antrian;
            if (count > 0) {
                mobileAntrianBadge.textContent = count;
                mobileAntrianBadge.style.display = 'flex';
            } else {
                mobileAntrianBadge.style.display = 'none';
            }
        }

        // Update mobile review badge
        const mobileReviewBadge = document.getElementById('mobile-review-badge');
        if (mobileReviewBadge) {
            const count = this.notifications.review;
            if (count > 0) {
                mobileReviewBadge.textContent = count;
                mobileReviewBadge.style.display = 'flex';
            } else {
                mobileReviewBadge.style.display = 'none';
            }
        }
    }

    // ===== MODAL MANAGEMENT =====
    setupModalFocusTrap(modal) {
        const focusableElements = modal.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );

        if (focusableElements.length === 0) return;

        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        const handleTabKey = (event) => {
            if (event.key !== 'Tab') return;

            if (event.shiftKey) {
                if (document.activeElement === firstElement) {
                    event.preventDefault();
                    lastElement.focus();
                }
            } else {
                if (document.activeElement === lastElement) {
                    event.preventDefault();
                    firstElement.focus();
                }
            }
        };

        modal.addEventListener('keydown', handleTabKey);
        firstElement.focus();

        modal._focusTrapHandler = handleTabKey;
    }

    closeCurrentModal() {
        const modal = document.querySelector('.modal-overlay');
        if (modal) {
            if (modal._focusTrapHandler) {
                modal.removeEventListener('keydown', modal._focusTrapHandler);
            }
            document.body.removeChild(modal);
        }
        this.currentReplyReviewId = null;
    }

    closeModal(modal) {
        if (modal._focusTrapHandler) {
            modal.removeEventListener('keydown', modal._focusTrapHandler);
        }
        document.body.removeChild(modal);
        this.currentReplyReviewId = null;
    }

    // ===== ALERTS & NOTIFICATIONS =====
    showSuccessAlert(message) {
        const template = document.getElementById('successModalTemplate');
        if (!template) {
            alert(message);
            return;
        }

        const modal = template.content.cloneNode(true);
        const messageElement = modal.querySelector('#successMessage');

        if (messageElement) {
            messageElement.textContent = message;
        }

        document.body.appendChild(modal);

        const modalElement = document.querySelector('.modal-overlay');
        const closeBtn = modalElement.querySelector('[data-action="close"]');

        closeBtn.addEventListener('click', () => {
            document.body.removeChild(modalElement);
        });

        setTimeout(() => {
            if (document.body.contains(modalElement)) {
                document.body.removeChild(modalElement);
            }
        }, 3000);

        this.setupModalFocusTrap(modalElement);
    }

    showErrorAlert(message) {
        alert(`Error: ${message}`);
    }

    showLoadingAlert(message) {
        console.log(message);
    }

    // ===== MOBILE MENU =====
    setupMobileMenu() {
        const template = document.getElementById('mobileMenuTemplate');
        if (template) {
            const mobileMenu = template.content.cloneNode(true);
            document.body.appendChild(mobileMenu);
            this.setupMobileMenuEvents();
        }
    }

    setupMobileMenuEvents() {
        const mobileMenu = document.getElementById('mobileMenu');
        const closeButton = document.getElementById('mobileMenuClose');
        const menuItems = mobileMenu.querySelectorAll('.mobile-nav__item');

        if (!mobileMenu || !closeButton) return;

        closeButton.addEventListener('click', () => {
            this.closeMobileMenu();
        });

        mobileMenu.addEventListener('click', (event) => {
            if (event.target === mobileMenu) {
                this.closeMobileMenu();
            }
        });

        mobileMenu.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                this.closeMobileMenu();
            }
        });
    }

    toggleMobileMenu() {
        if (this.isMobileMenuOpen) {
            this.closeMobileMenu();
        } else {
            this.openMobileMenu();
        }
    }

    openMobileMenu() {
        const mobileMenu = document.getElementById('mobileMenu');
        const menuToggle = document.getElementById('menuToggle');

        if (mobileMenu && menuToggle) {
            mobileMenu.classList.add('mobile-menu--open');
            menuToggle.setAttribute('aria-expanded', 'true');
            menuToggle.classList.add('menu-toggle--active');
            this.isMobileMenuOpen = true;
            document.body.style.overflow = 'hidden';

            this.setupMobileMenuFocusTrap();
        }
    }

    closeMobileMenu() {
        const mobileMenu = document.getElementById('mobileMenu');
        const menuToggle = document.getElementById('menuToggle');

        if (mobileMenu && menuToggle) {
            mobileMenu.classList.remove('mobile-menu--open');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.classList.remove('menu-toggle--active');
            this.isMobileMenuOpen = false;
            document.body.style.overflow = '';

            menuToggle.focus();
        }
    }

    setupMobileMenuFocusTrap() {
        const mobileMenu = document.getElementById('mobileMenu');
        if (!mobileMenu) return;

        const focusableElements = mobileMenu.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );

        if (focusableElements.length === 0) return;

        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        const handleTabKey = (event) => {
            if (event.key !== 'Tab') return;

            if (event.shiftKey) {
                if (document.activeElement === firstElement) {
                    event.preventDefault();
                    lastElement.focus();
                }
            } else {
                if (document.activeElement === lastElement) {
                    event.preventDefault();
                    firstElement.focus();
                }
            }
        };

        mobileMenu.addEventListener('keydown', handleTabKey);
        firstElement.focus();

        mobileMenu._focusTrapHandler = handleTabKey;
    }

    // ===== UTILITY METHODS =====
    addNewReview(reviewData) {
        const newReview = {
            id: Date.now(),
            ...reviewData,
            time: 'Baru saja',
            hasReply: false,
            images: reviewData.images || []
        };

        this.reviews.unshift(newReview);
        this.calculateTotalPages();
        this.displayReviews();
        this.updateRatingSummary();
        this.updatePageHeaderStats();
        this.notifications.review++;
        this.updateAllBadges();

        this.showSuccessAlert('Review baru ditambahkan!');
    }

    getReviewStats() {
        const total = this.reviews.length;
        const average = this.calculateAverageRating();
        const ratingCounts = { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 };

        this.reviews.forEach(review => {
            ratingCounts[review.rating]++;
        });

        return {
            total,
            average: average,
            ratingCounts
        };
    }
}

// ===== INITIALIZATION =====
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 DOM Content Loaded - Initializing PRISMO Review Manager...');
    
    try {
        const reviewManager = new PrismoReviewManager();
        window.prismoReview = reviewManager;
        
        window.getReviewStats = () => reviewManager.getReviewStats();
        
        console.log('🎉 PRISMO Review System loaded successfully');
        console.log('📊 Total reviews count:', reviewManager.reviews.length);
        console.log('📄 Total pages:', reviewManager.totalPages);
        
        // Force display reviews after a short delay to ensure DOM is ready
        setTimeout(() => {
            reviewManager.displayReviews();
        }, 100);
        
    } catch (error) {
        console.error('❌ Failed to load PRISMO Review System:', error);
        
        const container = document.getElementById('reviewsContainer');
        if (container) {
            container.innerHTML = `
                <div class="empty-state">
                    <p>Terjadi kesalahan saat memuat sistem review. Silakan refresh halaman.</p>
                    <button onclick="location.reload()" class="btn btn--primary" style="margin-top: 1rem;">
                        Refresh Halaman
                    </button>
                </div>
            `;
        }
    }
});

// Debug function to check if reviews are loading
function debugReviews() {
    if (window.prismoReview) {
        console.log('🔍 DEBUG REVIEWS:');
        console.log('Total reviews:', window.prismoReview.reviews.length);
        console.log('Current page:', window.prismoReview.currentPage);
        console.log('Total pages:', window.prismoReview.totalPages);
        console.log('Container exists:', !!document.getElementById('reviewsContainer'));
    } else {
        console.log('❌ PRISMO Review Manager not initialized');
    }
}

window.debugReviews = debugReviews;

// ===== DEMO UTILITIES =====
function demoAddReview() {
    if (window.prismoReview) {
        const newReview = {
            reviewer: {
                name: "Customer Demo",
                avatar: "/images/profile.png"
            },
            rating: 3,
            comment: "Ini adalah review demo yang ditambahkan melalui console. Pelayanan cukup memuaskan!",
            service: "Demo Service",
            images: [
                "https://images.unsplash.com/photo-1568602471122-7832951cc4c5?w=150&h=150&fit=crop"
            ]
        };
        
        window.prismoReview.addNewReview(newReview);
    } else {
        console.log('PRISMO Review Manager belum diinisialisasi');
    }
}

window.demoAddReview = demoAddReview;

// Global error handler
window.addEventListener('error', (event) => {
    console.error('Global error caught:', event.error);
});


