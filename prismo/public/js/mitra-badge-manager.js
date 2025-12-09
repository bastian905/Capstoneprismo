/**
 * Mitra Badge Manager
 * Manages real-time updates for antrian and review badges
 */

class MitraBadgeManager {
    constructor() {
        this.updateInterval = null;
        this.updateFrequency = 30000; // Update every 30 seconds
    }

    init() {
        // Initial badge update
        this.updateBadges();
        
        // Start periodic updates
        this.startPeriodicUpdates();
        
        console.log('✅ Mitra Badge Manager initialized');
    }

    async updateBadges() {
        try {
            const response = await fetch('/api/mitra/badge-counts', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });

            if (response.status === 401) {
                console.log('🔒 User not authenticated, redirecting to login...');
                window.location.href = '/login';
                return;
            }
            
            if (!response.ok) {
                console.warn('⚠️ Failed to fetch badge counts');
                return;
            }

            const data = await response.json();
            
            // Update antrian badge
            this.updateBadge('antrian-badge', data.antrian_badge);
            this.updateBadge('mobile-antrian-badge', data.antrian_badge);
            
            // Update review badge
            this.updateBadge('review-badge', data.review_badge);
            this.updateBadge('mobile-review-badge', data.review_badge);
            
            console.log('🔄 Badges updated:', data);
        } catch (error) {
            console.error('❌ Error updating badges:', error);
        }
    }

    updateBadge(elementId, count) {
        const badge = document.getElementById(elementId);
        if (badge) {
            badge.textContent = count;
            badge.setAttribute('aria-label', `${count} notifikasi`);
            
            // Show/hide badge based on count
            if (count > 0) {
                badge.style.display = '';
            } else {
                badge.style.display = 'none';
            }
        }
    }

    startPeriodicUpdates() {
        this.updateInterval = setInterval(() => {
            this.updateBadges();
        }, this.updateFrequency);
    }

    stopPeriodicUpdates() {
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
            this.updateInterval = null;
        }
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Only initialize for mitra users
    const userRole = document.querySelector('meta[name="user-role"]')?.content;
    if (userRole === 'mitra') {
        window.mitraBadgeManager = new MitraBadgeManager();
        window.mitraBadgeManager.init();
    }
});
