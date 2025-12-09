// ========== NOTIFICATION SYSTEM ==========
// Shared notification system for all customer pages

// Get CSRF token from meta tag
function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
}

async function loadNotifications() {
    try {
        console.log('📡 Fetching notifications from API...');
        const response = await fetch('/api/notifications', {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });

        console.log('📊 Response status:', response.status);
        
        if (response.status === 401) {
            console.log('🔒 User not authenticated, redirecting to login...');
            window.location.href = '/login';
            return;
        }
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('❌ API Error:', errorText);
            throw new Error(`Failed to load notifications: ${response.status}`);
        }

        const data = await response.json();
        console.log('✅ Notifications loaded:', data);
        renderNotifications(data.notifications);
        updateNotificationBadge(data.unread_count);
    } catch (error) {
        console.error('❌ Error loading notifications:', error);
        showEmptyNotifications();
    }
}

function renderNotifications(notifications) {
    const notificationList = document.getElementById('notificationList');
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    
    if (!notificationList) {
        console.warn('⚠️ notificationList element not found');
        return;
    }
    
    if (!notifications || notifications.length === 0) {
        console.log('📭 No notifications to display');
        showEmptyNotifications();
        if (markAllReadBtn) {
            markAllReadBtn.style.display = 'none';
        }
        return;
    }

    console.log('📋 Rendering', notifications.length, 'notifications');

    // Show mark all read button if there are unread notifications
    const hasUnread = notifications.some(n => !n.is_read);
    if (markAllReadBtn && hasUnread) {
        markAllReadBtn.style.display = 'block';
    } else if (markAllReadBtn) {
        markAllReadBtn.style.display = 'none';
    }

    notificationList.innerHTML = notifications.map(notif => {
        const iconClass = getNotificationIcon(notif.type);
        const unreadClass = notif.is_read ? '' : 'unread';
        
        return `
            <div class="panel-item ${unreadClass}" data-id="${notif.id}" onclick="markNotificationAsRead(${notif.id})">
                <div class="panel-icon ${iconClass}">
                    <i class="${getNotificationIconClass(notif.type)}"></i>
                </div>
                <div class="panel-content">
                    <div class="panel-title">${notif.title}</div>
                    <div class="panel-description">${notif.message}</div>
                    <div class="panel-time">${notif.created_at}</div>
                </div>
                ${!notif.is_read ? '<span class="unread-indicator"></span>' : ''}
            </div>
        `;
    }).join('');
}

function showEmptyNotifications() {
    const notificationList = document.getElementById('notificationList');
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    
    if (!notificationList) return;
    
    if (markAllReadBtn) {
        markAllReadBtn.style.display = 'none';
    }
    
    notificationList.innerHTML = `
        <div class="empty-state" style="text-align: center; padding: 60px 20px; color: #999;">
            <i class="fas fa-bell-slash" style="font-size: 64px; margin-bottom: 16px; opacity: 0.3; color: #ccc;"></i>
            <p style="font-size: 16px; font-weight: 500; margin: 0;">Tidak ada notifikasi</p>
            <p style="font-size: 14px; margin: 8px 0 0 0; opacity: 0.7;">Anda belum memiliki notifikasi</p>
        </div>
    `;
    
    console.log('📭 Empty state displayed');
}

function getNotificationIcon(type) {
    const icons = {
        'booking_cancelled': 'icon-booking',
        'booking_confirmed': 'icon-booking',
        'booking_completed': 'icon-booking',
        'payment_success': 'icon-payment',
        'refund_completed': 'icon-money'
    };
    return icons[type] || 'icon-booking';
}

function getNotificationIconClass(type) {
    const icons = {
        'booking_cancelled': 'fas fa-times-circle',
        'booking_confirmed': 'fas fa-check-circle',
        'booking_completed': 'fas fa-calendar-check',
        'payment_success': 'fas fa-dollar-sign',
        'refund_completed': 'fas fa-coins'
    };
    return icons[type] || 'fas fa-bell';
}

async function markNotificationAsRead(notifId) {
    try {
        const response = await fetch(`/api/notifications/${notifId}/read`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) throw new Error('Failed to mark as read');

        // Delete notification after marking as read
        await deleteNotification(notifId);
        
        // Reload notifications
        loadNotifications();
    } catch (error) {
        console.error('❌ Error marking notification as read:', error);
    }
}

async function deleteNotification(notifId) {
    try {
        const response = await fetch(`/api/notifications/${notifId}`, {
            method: 'DELETE',
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) throw new Error('Failed to delete notification');
        
        return true;
    } catch (error) {
        console.error('❌ Error deleting notification:', error);
        return false;
    }
}

async function markAllNotificationsAsRead() {
    try {
        const response = await fetch('/api/notifications/read-all', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) throw new Error('Failed to mark all as read');

        // Reload notifications
        loadNotifications();
    } catch (error) {
        console.error('❌ Error marking all notifications as read:', error);
    }
}

function updateNotificationBadge(count) {
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
    }
}

// Initialize notification system
let notificationSystemInitialized = false;

function initNotificationSystem() {
    // Prevent double initialization
    if (notificationSystemInitialized) {
        console.log('⚠️ Notification system already initialized');
        return;
    }

    console.log('🔍 Looking for notification elements...');
    const notifBtn = document.getElementById('notifBtn');
    const notifPanel = document.getElementById('notifPanel');
    const notifOverlay = document.getElementById('notifOverlay');
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    
    console.log('🔍 Elements found:', {
        notifBtn: !!notifBtn,
        notifPanel: !!notifPanel,
        notifOverlay: !!notifOverlay,
        markAllReadBtn: !!markAllReadBtn
    });
    
    if (!notifBtn || !notifPanel || !notifOverlay) {
        console.warn('⚠️ Notification elements not found, retrying in 100ms...');
        // Retry after a short delay
        setTimeout(initNotificationSystem, 100);
        return;
    }

    notificationSystemInitialized = true;
    console.log('✨ Attaching event listeners...');

    // Toggle notification panel
    notifBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        console.log('🔔 Notification button clicked');
        const isOpen = notifPanel.classList.contains('show');
        
        if (!isOpen) {
            console.log('📡 Loading notifications...');
            loadNotifications(); // Load fresh notifications when opening
        }
        
        notifPanel.classList.toggle('show');
        notifOverlay.classList.toggle('show');
        console.log('🎭 Panel toggled, show:', !isOpen);
    });

    // Close panel when clicking overlay
    notifOverlay.addEventListener('click', function() {
        notifPanel.classList.remove('show');
        notifOverlay.classList.remove('show');
    });

    // Prevent panel close when clicking inside
    notifPanel.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // Close panel when clicking outside
    document.addEventListener('click', function(e) {
        if (!notifPanel.contains(e.target) && !notifBtn.contains(e.target)) {
            notifPanel.classList.remove('show');
            notifOverlay.classList.remove('show');
        }
    });

    // Mark all as read button
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            markAllNotificationsAsRead();
        });
    }

    // Add browser notification enable button if not granted
    addBrowserNotificationButton();

    // Load notifications on page load
    loadNotifications();
    
    // Refresh notifications every 30 seconds
    setInterval(loadNotifications, 30000);
    
    console.log('🔔 Notification system initialized successfully');
}

// Add button to enable browser notifications
function addBrowserNotificationButton() {
    if (!window.browserNotification) {
        console.warn('⚠️ Browser notification system not loaded');
        return;
    }

    // Only show if permission not granted
    if (window.browserNotification.isGranted()) {
        console.log('✅ Browser notifications already enabled');
        return;
    }

    const panelHeader = document.querySelector('.panel-header');
    if (!panelHeader) return;

    // Check if button already exists
    if (document.getElementById('enableBrowserNotifBtn')) return;

    // Create enable button
    const enableBtn = document.createElement('button');
    enableBtn.id = 'enableBrowserNotifBtn';
    enableBtn.className = 'enable-browser-notif-btn';
    enableBtn.innerHTML = '🔔 Aktifkan Notifikasi Browser';
    enableBtn.style.cssText = `
        font-size: 11px;
        padding: 4px 10px;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-left: auto;
        transition: background 0.2s;
    `;

    enableBtn.addEventListener('click', async function(e) {
        e.stopPropagation();
        const granted = await window.browserNotification.requestPermission();
        if (granted) {
            enableBtn.remove();
        }
    });

    enableBtn.addEventListener('mouseenter', function() {
        this.style.background = '#218838';
    });

    enableBtn.addEventListener('mouseleave', function() {
        this.style.background = '#28a745';
    });

    panelHeader.appendChild(enableBtn);
    console.log('✅ Browser notification button added');
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    console.log('📋 DOM still loading, waiting...');
    document.addEventListener('DOMContentLoaded', function() {
        console.log('✅ DOM loaded, initializing notification system...');
        initNotificationSystem();
    });
} else {
    console.log('✅ DOM already loaded, initializing notification system...');
    initNotificationSystem();
}

console.log('📦 Notification system script loaded');
