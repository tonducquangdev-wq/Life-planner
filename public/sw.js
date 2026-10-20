/**
 * Life Planner Progressive Web App (PWA) - Service Worker
 * Version: 1.0.0
 * Scope: /
 */

const CACHE_VERSION = 'life-planner-v1.0.6';
const STATIC_CACHE_NAME = `lp-static-${CACHE_VERSION}`;
const DYNAMIC_CACHE_NAME = `lp-dynamic-${CACHE_VERSION}`;
const OFFLINE_URL = '/offline.html';

// Danh sách tài nguyên tĩnh cốt lõi cần nạp sẵn khi cài đặt
const PRECACHE_ASSETS = [
    OFFLINE_URL,
    '/manifest.json',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-512x512.png',
    '/images/icons/icon-maskable-512x512.png',
    '/images/icons/apple-touch-icon.png',
    '/images/icons/icon.svg',
    '/css/dashboard.css',
    '/css/mon-hoc.css',
    '/css/calendar.css',
    '/css/tap-luyen.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
    'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap'
];

/* ==========================================================================
   1. INSTALL EVENT - Lưu cache các tài nguyên tĩnh nền tảng
   ========================================================================== */
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE_NAME).then((cache) => {
            // Dùng Promise.allSettled để tránh lỗi nếu một tài nguyên CDN tạm thời gián đoạn
            return Promise.allSettled(
                PRECACHE_ASSETS.map((asset) =>
                    cache.add(asset).catch((err) => {
                        console.warn(`[PWA SW] Không thể nạp cache trước cho: ${asset}`, err);
                    })
                )
            );
        })
    );
    // Kích hoạt ngay Service Worker mới mà không cần chờ tab cũ đóng
    self.skipWaiting();
});

/* ==========================================================================
   2. ACTIVATE EVENT - Dọn dẹp cache cũ khi nâng cấp phiên bản
   ========================================================================== */
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== STATIC_CACHE_NAME && cacheName !== DYNAMIC_CACHE_NAME) {
                        console.log(`[PWA SW] Đang xóa cache cũ: ${cacheName}`);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

/* ==========================================================================
   3. FETCH EVENT - Chiến lược Caching an toàn, bảo vệ dữ liệu cá nhân
   ========================================================================== */
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // Bỏ qua các request không phải GET (POST, PUT, DELETE, PATCH)
    if (request.method !== 'GET') {
        return;
    }

    // Bỏ qua các URL nội bộ trình duyệt hoặc extension (chrome-extension, etc.)
    if (!url.protocol.startsWith('http')) {
        return;
    }

    // CHIẾN LƯỢC 1: API Requests (/api/*) & Sanctum Auth -> LUÔN LUÔN NETWORK ONLY
    // Không bao giờ cache API để bảo vệ dữ liệu cá nhân và tránh rò rỉ phiên người dùng khác
    if (url.pathname.startsWith('/api/') || url.pathname.startsWith('/sanctum/')) {
        event.respondWith(fetch(request));
        return;
    }

    // CHIẾN LƯỢC 2: HTML Page Navigation (request.mode === 'navigate') -> NETWORK FIRST
    // Ưu tiên nạp dữ liệu mới nhất từ máy chủ để kiểm tra xác thực và hiển thị lịch mới.
    // Nếu mất mạng hoàn toàn -> fallback về trang offline.html
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .catch(() => {
                    return caches.match(OFFLINE_URL);
                })
        );
        return;
    }

    // CHIẾN LƯỢC 3: Static Assets (CSS, JS, Fonts, Images) -> STALE-WHILE-REVALIDATE
    const isStaticAsset = (
        url.pathname.endsWith('.css') ||
        url.pathname.endsWith('.js') ||
        url.pathname.endsWith('.woff2') ||
        url.pathname.endsWith('.woff') ||
        url.pathname.endsWith('.ttf') ||
        url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.jpg') ||
        url.pathname.endsWith('.svg') ||
        url.hostname.includes('cdn.jsdelivr.net') ||
        url.hostname.includes('fonts.googleapis.com') ||
        url.hostname.includes('fonts.gstatic.com')
    );

    if (isStaticAsset) {
        const hasVersion = url.searchParams.has('v');

        // Đối với tài nguyên có chuỗi phiên bản (?v=...): Ưu tiên Network First để nhận ngay CSS/JS mới nhất mà không cần F5
        if (hasVersion) {
            event.respondWith(
                fetch(request).then((networkResponse) => {
                    if (networkResponse && (networkResponse.status === 200 || networkResponse.type === 'opaque')) {
                        const responseToCache = networkResponse.clone();
                        caches.open(DYNAMIC_CACHE_NAME).then((cache) => {
                            cache.put(request, responseToCache);
                        });
                    }
                    return networkResponse;
                }).catch(() => {
                    return caches.match(request, { ignoreSearch: true });
                })
            );
            return;
        }

        // Đối với tài nguyên tĩnh khác: Cache First với cập nhật nền an toàn
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                if (cachedResponse) {
                    fetch(request).then((networkResponse) => {
                        if (networkResponse && (networkResponse.status === 200 || networkResponse.type === 'opaque')) {
                            const responseToCache = networkResponse.clone();
                            caches.open(DYNAMIC_CACHE_NAME).then((cache) => {
                                cache.put(request, responseToCache);
                            });
                        }
                    }).catch(() => {});
                    return cachedResponse;
                }

                return fetch(request).then((networkResponse) => {
                    if (networkResponse && (networkResponse.status === 200 || networkResponse.type === 'opaque')) {
                        const responseToCache = networkResponse.clone();
                        caches.open(DYNAMIC_CACHE_NAME).then((cache) => {
                            cache.put(request, responseToCache);
                        });
                    }
                    return networkResponse;
                });
            })
        );
        return;
    }

    // Mặc định: Network First
    event.respondWith(
        fetch(request).catch(() => caches.match(request, { ignoreSearch: true }))
    );
});

/* ==========================================================================
   4. PUSH EVENT - Lắng nghe thông báo đẩy từ Web Push VAPID
   ========================================================================== */
self.addEventListener('push', (event) => {
    let data = {
        title: 'Life Planner',
        body: 'Bạn có một nhắc nhở mới từ Life Planner.',
        icon: '/images/icons/icon-192x192.png',
        badge: '/images/icons/icon-192x192.png',
        url: '/calendar',
        tag: 'lp-notification',
        timestamp: Date.now()
    };

    if (event.data) {
        try {
            const jsonPayload = event.data.json();
            data = Object.assign(data, jsonPayload);
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: data.icon || '/images/icons/icon-192x192.png',
        badge: data.badge || '/images/icons/icon-192x192.png',
        tag: data.tag || 'lp-notification',
        data: {
            url: data.url || '/calendar',
            timestamp: data.timestamp || Date.now(),
            extra: data.data || {}
        },
        vibrate: [100, 50, 100],
        renotify: true,
        requireInteraction: false
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

/* ==========================================================================
   5. NOTIFICATION CLICK - Mở trang liên quan khi bấm vào thông báo
   ========================================================================== */
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = event.notification.data?.url || '/calendar';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            // Nếu đã có tab/cửa sổ Life Planner đang mở, điều hướng tới trang và focus
            for (const client of clientList) {
                if ('focus' in client) {
                    client.navigate(targetUrl);
                    return client.focus();
                }
            }
            // Nếu chưa có cửa sổ nào, mở cửa sổ mới
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
