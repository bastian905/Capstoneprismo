// Browser Notification System - Prismo
class BrowserNotificationManager {
    constructor() {
        this.permission = 'default';
        this.lastNotificationId = null;
        this.checkInterval = null;
    }

    // Initialize notification system
    async init() {
        console.log('🔔 Initializing Browser Notification System...');
        
        // Check if browser supports notifications
        if (!('Notification' in window)) {
            console.warn('⚠️ Browser does not support notifications');
            return false;
        }

        this.permission = Notification.permission;
        console.log('🔔 Current permission:', this.permission);

        // If permission already granted, start monitoring
        if (this.permission === 'granted') {
            this.startMonitoring();
        }

        return true;
    }

    // Request notification permission
    async requestPermission() {
        if (!('Notification' in window)) {
            alert('Browser Anda tidak mendukung notifikasi');
            return false;
        }

        if (this.permission === 'granted') {
            console.log('✅ Permission already granted');
            return true;
        }

        try {
            const permission = await Notification.requestPermission();
            this.permission = permission;
            
            if (permission === 'granted') {
                console.log('✅ Notification permission granted');
                this.showWelcomeNotification();
                this.startMonitoring();
                return true;
            } else if (permission === 'denied') {
                console.warn('❌ Notification permission denied');
                alert('Anda telah menolak izin notifikasi. Untuk mengaktifkan kembali, silakan ubah pengaturan browser Anda.');
                return false;
            } else {
                console.log('⏸️ Notification permission dismissed');
                return false;
            }
        } catch (error) {
            console.error('❌ Error requesting permission:', error);
            return false;
        }
    }

    // Show welcome notification
    showWelcomeNotification() {
        this.showNotification(
            'Notifikasi Aktif! 🎉',
            'Anda akan menerima notifikasi untuk pembaruan penting',
            '/images/logo.png'
        );
    }

    // Show browser notification
    showNotification(title, message, icon = '/images/logo.png', data = {}) {
        if (this.permission !== 'granted') {
            console.warn('⚠️ Cannot show notification - permission not granted');
            return;
        }

        try {
            const notification = new Notification(title, {
                body: message,
                icon: icon,
                badge: '/images/logo.png',
                tag: data.id || 'prismo-notification',
                requireInteraction: false,
                silent: false,
                data: data
            });

            // Auto close after 10 seconds
            setTimeout(() => notification.close(), 10000);

            // Handle notification click
            notification.onclick = (event) => {
                event.preventDefault();
                window.focus();
                
                // Navigate to relevant page based on notification type
                if (data.url) {
                    window.location.href = data.url;
                }
                
                notification.close();
            };

            console.log('✅ Notification shown:', title);
            return notification;
        } catch (error) {
            console.error('❌ Error showing notification:', error);
            return null;
        }
    }

    // Start monitoring for new notifications
    startMonitoring() {
        if (this.checkInterval) {
            console.log('⚠️ Monitoring already started');
            return;
        }

        console.log('🔄 Starting notification monitoring...');
        
        // Check immediately
        this.checkNewNotifications();
        
        // Then check every 30 seconds
        this.checkInterval = setInterval(() => {
            this.checkNewNotifications();
        }, 30000);
    }

    // Stop monitoring
    stopMonitoring() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
            this.checkInterval = null;
            console.log('⏸️ Notification monitoring stopped');
        }
    }

    // Check for new notifications from API
    async checkNewNotifications() {
        try {
            const response = await fetch('/api/notifications/unread-count', {
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch notifications');
            }

            const data = await response.json();
            
            // If there are unread notifications, fetch the latest one
            if (data.unread_count > 0) {
                await this.fetchLatestNotification();
            }
        } catch (error) {
            console.error('❌ Error checking notifications:', error);
        }
    }

    // Fetch latest notification and show if new
    async fetchLatestNotification() {
        try {
            const response = await fetch('/api/notifications?limit=1', {
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch latest notification');
            }

            const data = await response.json();
            const notifications = data.notifications || [];
            
            if (notifications.length > 0) {
                const latest = notifications[0];
                
                // Only show if it's a new notification (not the last one we showed)
                if (this.lastNotificationId !== latest.id && !latest.is_read) {
                    this.showNotificationFromData(latest);
                    this.lastNotificationId = latest.id;
                }
            }
        } catch (error) {
            console.error('❌ Error fetching latest notification:', error);
        }
    }

    // Show notification from API data
    showNotificationFromData(notificationData) {
        const iconMap = {
            'booking_confirmed': '✅',
            'booking_cancelled': '❌',
            'booking_completed': '🎉',
            'payment_success': '💰',
            'refund_completed': '💸'
        };

        const icon = iconMap[notificationData.type] || '🔔';
        const title = `${icon} ${notificationData.title}`;
        
        this.showNotification(
            title,
            notificationData.message,
            '/images/logo.png',
            {
                id: notificationData.id,
                type: notificationData.type,
                url: this.getNotificationUrl(notificationData.type)
            }
        );
    }

    // Get URL to navigate when notification is clicked
    getNotificationUrl(type) {
        const role = this.getUserRole();
        
        if (role === 'customer') {
            return '/customer/booking/Rbooking';
        } else if (role === 'mitra') {
            return '/mitra/antrian/antrian';
        } else if (role === 'admin') {
            return '/admin/kelolabooking/kelolabooking';
        }
        
        return '/';
    }

    // Get user role from page or meta tag
    getUserRole() {
        // Try to get from meta tag
        const roleMeta = document.querySelector('meta[name="user-role"]');
        if (roleMeta) {
            return roleMeta.content;
        }

        // Try to detect from URL
        const path = window.location.pathname;
        if (path.includes('/customer/')) return 'customer';
        if (path.includes('/mitra/')) return 'mitra';
        if (path.includes('/admin/')) return 'admin';
        
        return 'guest';
    }

    // Check if permission is granted
    isGranted() {
        return this.permission === 'granted';
    }

    // Check if permission is denied
    isDenied() {
        return this.permission === 'denied';
    }
}

// Create global instance
window.browserNotification = new BrowserNotificationManager();

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.browserNotification.init();
});

console.log('✅ Browser Notification System loaded');
