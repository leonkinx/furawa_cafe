// Service Worker untuk Furawa Cafe Admin PWA
const CACHE_NAME = 'furawa-admin-v1';
const OFFLINE_URL = '/offline.html';

// Assets yang akan di-cache
const STATIC_CACHE = [
    '/offline.html',
    '/admin/dashboard',
    '/admin/login',
    'https://cdn.tailwindcss.com',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://cdn.jsdelivr.net/npm/chart.js'
];

// Install Service Worker
self.addEventListener('install', (event) => {
    console.log('[SW] Installing Service Worker...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[SW] Caching static assets');
                return cache.addAll(STATIC_CACHE);
            })
            .catch((error) => {
                console.error('[SW] Cache failed:', error);
            })
    );
    self.skipWaiting();
});

// Activate Service Worker
self.addEventListener('activate', (event) => {
    console.log('[SW] Activating Service Worker...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('[SW] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    return self.clients.claim();
});

// Fetch Strategy: Network First, fallback to Cache
self.addEventListener('fetch', (event) => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') return;

    // Skip chrome extensions
    if (event.request.url.startsWith('chrome-extension://')) return;

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // Clone response untuk cache
                const responseToCache = response.clone();
                
                // Cache successful responses
                if (response.status === 200) {
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                }
                
                return response;
            })
            .catch(() => {
                // Network failed, try cache
                return caches.match(event.request)
                    .then((cachedResponse) => {
                        if (cachedResponse) {
                            return cachedResponse;
                        }
                        
                        // If no cache, return offline page for navigation
                        if (event.request.mode === 'navigate') {
                            return caches.match(OFFLINE_URL);
                        }
                        
                        // Return a basic response for other requests
                        return new Response('Offline - No cached data available', {
                            status: 503,
                            statusText: 'Service Unavailable',
                            headers: new Headers({
                                'Content-Type': 'text/plain'
                            })
                        });
                    });
            })
    );
});

// Push Notification Handler
self.addEventListener('push', (event) => {
    console.log('[SW] Push notification received');
    
    let data = {
        title: 'Furawa Cafe',
        body: 'Ada notifikasi baru',
        icon: '/images/icon-192x192.png',
        badge: '/images/icon-72x72.png',
        tag: 'furawa-notification',
        requireInteraction: true
    };

    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: data.icon || '/images/icon-192x192.png',
        badge: data.badge || '/images/icon-72x72.png',
        tag: data.tag || 'furawa-notification',
        requireInteraction: data.requireInteraction || true,
        vibrate: [200, 100, 200],
        data: {
            url: data.url || '/admin/orders',
            dateOfArrival: Date.now()
        },
        actions: [
            {
                action: 'view',
                title: 'Lihat',
                icon: '/images/icon-72x72.png'
            },
            {
                action: 'close',
                title: 'Tutup',
                icon: '/images/icon-72x72.png'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Notification Click Handler
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification clicked:', event.action);
    
    event.notification.close();

    if (event.action === 'close') {
        return;
    }

    // Default action or 'view' action
    const urlToOpen = event.notification.data.url || '/admin/orders';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Check if there's already a window open
                for (let i = 0; i < clientList.length; i++) {
                    const client = clientList[i];
                    if (client.url.includes('/admin') && 'focus' in client) {
                        return client.focus().then(() => {
                            return client.navigate(urlToOpen);
                        });
                    }
                }
                // If no window is open, open a new one
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// Background Sync
self.addEventListener('sync', (event) => {
    console.log('[SW] Background sync:', event.tag);
    
    if (event.tag === 'sync-orders') {
        event.waitUntil(syncOrders());
    }
});

async function syncOrders() {
    try {
        // Implement your sync logic here
        console.log('[SW] Syncing orders...');
        // Example: fetch new orders from server
        const response = await fetch('/admin/orders/notifications');
        const data = await response.json();
        console.log('[SW] Sync complete:', data);
    } catch (error) {
        console.error('[SW] Sync failed:', error);
    }
}

// Periodic Background Sync (if supported)
self.addEventListener('periodicsync', (event) => {
    if (event.tag === 'check-new-orders') {
        event.waitUntil(checkNewOrders());
    }
});

async function checkNewOrders() {
    try {
        const response = await fetch('/admin/orders/notifications');
        const data = await response.json();
        
        if (data.success && data.total_notifications > 0) {
            // Show notification
            self.registration.showNotification('Pesanan Baru!', {
                body: `Ada ${data.total_notifications} pesanan yang perlu diproses`,
                icon: '/images/icon-192x192.png',
                badge: '/images/icon-72x72.png',
                tag: 'new-orders',
                requireInteraction: true,
                data: {
                    url: '/admin/orders'
                }
            });
        }
    } catch (error) {
        console.error('[SW] Check new orders failed:', error);
    }
}

console.log('[SW] Service Worker loaded');
