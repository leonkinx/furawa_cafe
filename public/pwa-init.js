// PWA Initialization Script for Furawa Cafe Admin
console.log('[PWA] Initializing...');

// Check if service workers are supported
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        // Register Service Worker
        navigator.serviceWorker.register('/service-worker.js')
            .then((registration) => {
                console.log('[PWA] Service Worker registered:', registration.scope);
                
                // Check for updates
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    console.log('[PWA] New Service Worker found');
                    
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            // New service worker available
                            showUpdateNotification();
                        }
                    });
                });
            })
            .catch((error) => {
                console.error('[PWA] Service Worker registration failed:', error);
            });
        
        // Request notification permission
        requestNotificationPermission();
        
        // Setup push notifications
        setupPushNotifications();
    });
}

// Request Notification Permission
function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission().then((permission) => {
            console.log('[PWA] Notification permission:', permission);
            if (permission === 'granted') {
                showWelcomeNotification();
            }
        });
    }
}

// Show welcome notification
function showWelcomeNotification() {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('Furawa Cafe Admin', {
            body: 'Notifikasi berhasil diaktifkan! Anda akan menerima update pesanan baru.',
            icon: '/images/icon-192x192.png',
            badge: '/images/icon-72x72.png',
            tag: 'welcome'
        });
    }
}

// Setup Push Notifications
async function setupPushNotifications() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        console.warn('[PWA] Push notifications not supported');
        return;
    }

    try {
        const registration = await navigator.serviceWorker.ready;
        
        // Check if already subscribed
        let subscription = await registration.pushManager.getSubscription();
        
        if (!subscription) {
            // Subscribe to push notifications
            subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(getVapidPublicKey())
            });
            
            console.log('[PWA] Push subscription:', subscription);
            
            // Send subscription to server
            await sendSubscriptionToServer(subscription);
        }
    } catch (error) {
        console.error('[PWA] Push subscription failed:', error);
    }
}

// Get VAPID public key (you need to generate this)
function getVapidPublicKey() {
    // TODO: Replace with your actual VAPID public key
    // Generate using: php artisan webpush:vapid
    return 'YOUR_VAPID_PUBLIC_KEY_HERE';
}

// Convert VAPID key
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

// Send subscription to server
async function sendSubscriptionToServer(subscription) {
    try {
        const response = await fetch('/admin/push/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify(subscription)
        });
        
        if (response.ok) {
            console.log('[PWA] Subscription sent to server');
        }
    } catch (error) {
        console.error('[PWA] Failed to send subscription:', error);
    }
}

// Show update notification
function showUpdateNotification() {
    const notification = document.createElement('div');
    notification.className = 'fixed bottom-4 right-4 bg-blue-600 text-white px-6 py-4 rounded-lg shadow-2xl z-50 animate-bounce';
    notification.innerHTML = `
        <div class="flex items-center gap-3">
            <i class="fas fa-sync-alt"></i>
            <div>
                <p class="font-semibold">Update Tersedia!</p>
                <p class="text-sm">Klik untuk memperbarui aplikasi</p>
            </div>
        </div>
    `;
    notification.style.cursor = 'pointer';
    notification.onclick = () => {
        window.location.reload();
    };
    document.body.appendChild(notification);
}

// Install prompt
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    console.log('[PWA] Install prompt available');
    e.preventDefault();
    deferredPrompt = e;
    
    // Show custom install button
    showInstallButton();
});

// Show install button
function showInstallButton() {
    const installButton = document.getElementById('pwa-install-button');
    if (installButton) {
        installButton.style.display = 'block';
        installButton.addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                console.log('[PWA] Install outcome:', outcome);
                deferredPrompt = null;
                installButton.style.display = 'none';
            }
        });
    }
}

// App installed
window.addEventListener('appinstalled', () => {
    console.log('[PWA] App installed successfully');
    deferredPrompt = null;
    
    // Show success message
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('Furawa Cafe Admin', {
            body: 'Aplikasi berhasil diinstall! Sekarang bisa diakses dari home screen.',
            icon: '/images/icon-192x192.png'
        });
    }
});

// Online/Offline detection
window.addEventListener('online', () => {
    console.log('[PWA] Back online');
    showConnectionStatus('online');
});

window.addEventListener('offline', () => {
    console.log('[PWA] Gone offline');
    showConnectionStatus('offline');
});

function showConnectionStatus(status) {
    const statusDiv = document.createElement('div');
    statusDiv.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg z-50 ${
        status === 'online' ? 'bg-green-500' : 'bg-red-500'
    } text-white`;
    statusDiv.innerHTML = `
        <i class="fas fa-${status === 'online' ? 'wifi' : 'wifi-slash'} mr-2"></i>
        ${status === 'online' ? 'Kembali Online' : 'Tidak Ada Koneksi'}
    `;
    document.body.appendChild(statusDiv);
    
    setTimeout(() => {
        statusDiv.remove();
    }, 3000);
}

console.log('[PWA] Initialization complete');
