const CACHE_NAME = 'hulul-pos-v1.0.0';
const RUNTIME_CACHE = 'hulul-pos-runtime';

// Assets to cache on install
const PRECACHE_ASSETS = [
    '/',
    '/css/app.css',
    '/js/app.js',
    '/manifest.json',
    '/images/icon-192x192.png',
    '/images/icon-512x512.png'
];

// Install event - cache essential assets
self.addEventListener('install', (event) => {
    console.log('[Service Worker] Installing...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[Service Worker] Precaching assets');
                return cache.addAll(PRECACHE_ASSETS.map(url => new Request(url, { cache: 'reload' })));
            })
            .catch((error) => {
                console.error('[Service Worker] Precaching failed:', error);
            })
    );
    self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('[Service Worker] Activating...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME && cacheName !== RUNTIME_CACHE) {
                        console.log('[Service Worker] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    return self.clients.claim();
});

// Fetch event - network first, then cache fallback
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip cross-origin requests
    if (url.origin !== location.origin) {
        return;
    }

    // Skip API calls and POST requests from being cached
    if (request.method !== 'GET' || url.pathname.startsWith('/api/')) {
        return;
    }

    event.respondWith(
        // Try network first
        fetch(request)
            .then((response) => {
                // If successful, clone and cache the response
                if (response && response.status === 200) {
                    const responseClone = response.clone();
                    caches.open(RUNTIME_CACHE).then((cache) => {
                        cache.put(request, responseClone);
                    });
                }
                return response;
            })
            .catch(() => {
                // Network failed, try cache
                return caches.match(request).then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }

                    // If not in cache and it's a navigation request, return offline page
                    if (request.mode === 'navigate') {
                        return caches.match('/').then((response) => {
                            return response || new Response('Offline - No cached content available', {
                                headers: { 'Content-Type': 'text/html' }
                            });
                        });
                    }

                    return new Response('Network error', {
                        status: 408,
                        headers: { 'Content-Type': 'text/plain' }
                    });
                });
            })
    );
});

// Background sync for offline transactions (if needed in future)
self.addEventListener('sync', (event) => {
    console.log('[Service Worker] Background sync:', event.tag);
    if (event.tag === 'sync-transactions') {
        event.waitUntil(syncTransactions());
    }
});

async function syncTransactions() {
    // Placeholder for future offline transaction syncing
    console.log('[Service Worker] Syncing transactions...');
}

// Push notifications (if needed in future)
self.addEventListener('push', (event) => {
    const data = event.data ? event.data.json() : {};
    const title = data.title || 'Hulul POS';
    const options = {
        body: data.body || 'New notification',
        icon: '/images/icon-192x192.png',
        badge: '/images/icon-72x72.png',
        vibrate: [200, 100, 200],
        data: data.data || {}
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url || '/')
    );
});
