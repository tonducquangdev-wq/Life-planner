/**
 * Life Planner - PWA & Web Push Manager
 * Xử lý đăng ký Service Worker, tương thích đa nền tảng (Android Chrome & iOS Safari),
 * giải quyết các ràng buộc bảo mật HTTPS, Standalone mode trên iPhone và Web Push Notifications.
 */

(function () {
    'use strict';

    let deferredInstallPrompt = null;

    // Helper chuyển đổi VAPID public key sang Uint8Array
    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
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

    const LifePlannerPWA = {
        swRegistration: null,

        // Kiểm tra ứng dụng có đang chạy ở chế độ Standalone (Đã cài đặt vào màn hình chính) không
        isStandalone: function () {
            return (
                window.matchMedia('(display-mode: standalone)').matches ||
                window.navigator.standalone === true ||
                document.referrer.includes('android-app://')
            );
        },

        // Kiểm tra thiết bị iOS (iPhone / iPad / iPod kể cả iPadOS mới)
        isIOS: function () {
            const ua = (window.navigator.userAgent || '').toLowerCase();
            const isStandardIOS = /iphone|ipad|ipod/.test(ua);
            const isIPadOS = (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
            return isStandardIOS || isIPadOS;
        },

        // Kiểm tra môi trường kết nối bảo mật (HTTPS hoặc localhost)
        isSecure: function () {
            if (typeof window.isSecureContext !== 'undefined') {
                return window.isSecureContext;
            }
            return (
                location.protocol === 'https:' ||
                location.hostname === 'localhost' ||
                location.hostname === '127.0.0.1'
            );
        },

        // Kiểm tra trình duyệt nội bộ trong ứng dụng (In-app WebView: Zalo, Facebook, Messenger, Instagram, TikTok)
        isInAppBrowser: function () {
            const ua = (window.navigator.userAgent || '').toLowerCase();
            return /fban|fbav|instagram|line|zalo|micromessenger|tiktok|bytedance/i.test(ua);
        },

        // Kiểm tra có sự kiện cài đặt tự động từ trình duyệt (beforeinstallprompt) hay không
        hasInstallPrompt: function () {
            return !!deferredInstallPrompt;
        },

        // Khởi tạo Service Worker và các trình lắng nghe
        init: function () {
            // Thêm class pwa-standalone vào body nếu mở từ icon màn hình chính
            if (this.isStandalone()) {
                document.documentElement.classList.add('pwa-standalone');
                document.body?.classList.add('pwa-standalone');
            }

            // Đăng ký Service Worker an toàn (bảo đảm chạy kể cả khi tài liệu đã load xong)
            if ('serviceWorker' in navigator) {
                const registerWorker = () => {
                    navigator.serviceWorker.register('/sw.js', { scope: '/' })
                        .then((registration) => {
                            this.swRegistration = registration;
                            console.log('[PWA] Service Worker đăng ký thành công:', registration.scope);

                            // Lắng nghe cập nhật SW mới
                            registration.addEventListener('updatefound', () => {
                                const newWorker = registration.installing;
                                if (newWorker) {
                                    newWorker.addEventListener('statechange', () => {
                                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                            console.log('[PWA] Phiên bản mới đã sẵn sàng.');
                                        }
                                    });
                                }
                            });
                        })
                        .catch((error) => {
                            console.warn('[PWA] Không thể đăng ký Service Worker:', error);
                        });
                };

                if (document.readyState === 'complete') {
                    registerWorker();
                } else {
                    window.addEventListener('load', registerWorker);
                }
            }

            // Lắng nghe sự kiện trước khi cài đặt trên Android / Chrome
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredInstallPrompt = e;
                console.log('[PWA] Thiết bị đủ điều kiện cài đặt tự động.');

                // Báo cho giao diện biết để hiển thị nút/banner cài đặt nếu cần
                window.dispatchEvent(new CustomEvent('pwa-installable', { detail: { prompt: e } }));

                // Hiển thị nút cài đặt trong các phần tử có data-pwa-install
                document.querySelectorAll('[data-pwa-install]').forEach(el => {
                    el.classList.remove('d-none');
                });

                // Cập nhật modal cài đặt nếu đang mở
                this.setupInstallModal();
            });

            // Lắng nghe khi cài đặt thành công
            window.addEventListener('appinstalled', () => {
                deferredInstallPrompt = null;
                localStorage.setItem('pwa_installed', 'true');
                console.log('[PWA] Ứng dụng đã được cài đặt thành công!');

                document.querySelectorAll('[data-pwa-install]').forEach(el => {
                    el.classList.add('d-none');
                });

                const modalEl = document.getElementById('pwaInstallModal');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal?.hide();
                }

                this.showToast('success', 'Ứng dụng Life Planner đã được cài đặt thành công lên màn hình chính!', 'Cài đặt thành công');
            });

            // Tự động cấu hình modal khi được kích hoạt
            const modalEl = document.getElementById('pwaInstallModal');
            if (modalEl) {
                modalEl.addEventListener('show.bs.modal', () => {
                    this.setupInstallModal();
                });
            }

            // Tự động quét và phát thông báo đẩy định kỳ mỗi 60 giây khi client mở
            setTimeout(() => this.checkReminders(), 3000);
            setInterval(() => {
                if (document.visibilityState === 'visible') {
                    this.checkReminders();
                }
            }, 60000);
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') {
                    this.checkReminders();
                }
            });
        },

        // Đồng bộ và điều chỉnh các thành phần bên trong Modal Cài đặt
        setupInstallModal: function () {
            const modalEl = document.getElementById('pwaInstallModal');
            if (!modalEl) return;

            const iosSection = document.getElementById('pwaIosInstructions');
            const androidSection = document.getElementById('pwaAndroidInstructions');
            const androidPromptBtn = document.getElementById('pwaAndroidPromptBtn');
            const androidManual = document.getElementById('pwaAndroidManual');
            const httpsNotice = document.getElementById('pwaHttpsNotice');
            const inAppNotice = document.getElementById('pwaInAppNotice');

            const isIos = this.isIOS();
            const isSec = this.isSecure();
            const inApp = this.isInAppBrowser();

            // Cảnh báo HTTPS
            if (httpsNotice) {
                if (!isSec) {
                    httpsNotice.classList.remove('d-none');
                } else {
                    httpsNotice.classList.add('d-none');
                }
            }

            // Cảnh báo In-app browser (Zalo/Facebook)
            if (inAppNotice) {
                if (inApp) {
                    inAppNotice.classList.remove('d-none');
                } else {
                    inAppNotice.classList.add('d-none');
                }
            }

            // Phân nhánh hiển thị theo hệ điều hành
            if (isIos) {
                if (iosSection) iosSection.classList.remove('d-none');
                if (androidSection) androidSection.classList.add('d-none');
            } else {
                if (iosSection) iosSection.classList.add('d-none');
                if (androidSection) androidSection.classList.remove('d-none');

                // Nếu trình duyệt hỗ trợ bấm cài đặt 1-chạm
                if (this.hasInstallPrompt()) {
                    if (androidPromptBtn) androidPromptBtn.classList.remove('d-none');
                } else {
                    if (androidPromptBtn) androidPromptBtn.classList.add('d-none');
                }

                // Luôn hiển thị hướng dẫn thủ công để người dùng biết cách thao tác
                if (androidManual) {
                    androidManual.classList.remove('d-none');
                }
            }
        },

        // Kích hoạt hộp thoại cài đặt ứng dụng (Android / Desktop / iOS)
        promptInstall: async function () {
            if (this.isStandalone()) {
                this.showToast('info', 'Ứng dụng Life Planner đã được cài đặt và đang chạy ở chế độ app độc lập!', 'Đã cài đặt');
                return;
            }

            // Nếu đang mở trong Zalo/Facebook Webview
            if (this.isInAppBrowser()) {
                this.showGenericInstallModal();
                this.showToast('warning', 'Vui lòng bấm menu (···) chọn "Mở bằng trình duyệt" ngoài để cài đặt PWA.', 'Mở trình duyệt');
                return;
            }

            // Nếu là thiết bị iOS
            if (this.isIOS()) {
                this.showIOSInstallInstructions();
                return;
            }

            // Nếu là Android / Desktop có deferredPrompt
            if (deferredInstallPrompt) {
                try {
                    deferredInstallPrompt.prompt();
                    const choiceResult = await deferredInstallPrompt.userChoice;
                    if (choiceResult && choiceResult.outcome === 'accepted') {
                        console.log('[PWA] Người dùng đã chấp nhận cài đặt.');
                    } else {
                        console.log('[PWA] Người dùng từ chối cài đặt.');
                    }
                } catch (err) {
                    console.warn('[PWA] Lỗi kích hoạt hộp thoại cài đặt:', err);
                    this.showGenericInstallModal();
                }
                deferredInstallPrompt = null;
            } else {
                // Nếu trình duyệt không bắn beforeinstallprompt (đã từ chối hoặc cần cài qua menu)
                this.showGenericInstallModal();
            }
        },

        // Hiển thị hướng dẫn thêm vào màn hình chính cho iOS Safari
        showIOSInstallInstructions: function () {
            const modalEl = document.getElementById('pwaInstallModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                this.setupInstallModal();
                const iosSection = document.getElementById('pwaIosInstructions');
                const androidSection = document.getElementById('pwaAndroidInstructions');
                if (iosSection) iosSection.classList.remove('d-none');
                if (androidSection) androidSection.classList.add('d-none');

                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            } else {
                alert("Để cài đặt trên iPhone/iPad:\n1. Bấm nút Chia sẻ (Share) ⎋ ở cuối Safari.\n2. Chọn 'Thêm vào MH chính' (Add to Home Screen) ➕.\n3. Bấm 'Thêm' ở góc trên.");
            }
        },

        // Modal hướng dẫn cài đặt chung
        showGenericInstallModal: function () {
            const modalEl = document.getElementById('pwaInstallModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                this.setupInstallModal();
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        },

        // ======================================================================
        // WEB PUSH NOTIFICATIONS
        // ======================================================================

        // Đăng ký nhận thông báo đẩy
        subscribeToPush: async function () {
            // 1. Kiểm tra môi trường kết nối bảo mật HTTPS
            if (!this.isSecure()) {
                const msg = 'Tính năng Web Push yêu cầu kết nối bảo mật HTTPS (hoặc localhost). Vui lòng truy cập qua link ngrok HTTPS để bật trên điện thoại.';
                this.showToast('warning', msg, 'Yêu cầu HTTPS');
                this.showGenericInstallModal();
                throw new Error(msg);
            }

            // 2. Kiểm tra in-app browser
            if (this.isInAppBrowser()) {
                const msg = 'Trình duyệt nội bộ Zalo/Facebook không hỗ trợ Web Push. Vui lòng bấm menu (···) chọn "Mở bằng trình duyệt" ngoài.';
                this.showToast('warning', msg, 'Mở trình duyệt ngoài');
                this.showGenericInstallModal();
                throw new Error(msg);
            }

            // 3. Kiểm tra iOS Safari: Apple chỉ cho phép Push khi đã Thêm vào MH chính (Standalone mode)
            if (this.isIOS() && !this.isStandalone()) {
                this.showIOSInstallInstructions();
                throw new Error('Trên iPhone/iPad (iOS), bạn cần bấm Chia sẻ ⎋ -> "Thêm vào MH chính" ➕ trước thì Safari mới cho phép nhận thông báo đẩy.');
            }

            if (!('Notification' in window)) {
                throw new Error('Trình duyệt của bạn không hỗ trợ Notifications API.');
            }

            // 4. Kiểm tra quyền nếu người dùng đã bấm chặn
            if (Notification.permission === 'denied') {
                throw new Error('Bạn đã chặn quyền thông báo của trang này. Vui lòng bấm vào biểu tượng 🔒 hoặc ⚙️ trên thanh địa chỉ để bật lại quyền Thông báo.');
            }

            if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
                throw new Error('Trình duyệt của bạn không hỗ trợ Push API.');
            }

            // 5. Yêu cầu quyền từ người dùng
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                throw new Error('Bạn chưa cấp quyền thông báo. Vui lòng cho phép quyền thông báo trong cài đặt trình duyệt.');
            }

            // 6. Chờ Service Worker sẵn sàng
            const registration = await navigator.serviceWorker.ready;

            // 7. Lấy VAPID Public Key từ backend
            const vapidRes = await fetch('/push/vapid-key', {
                headers: {
                    'Accept': 'application/json',
                    'ngrok-skip-browser-warning': 'true'
                }
            });
            if (!vapidRes.ok) {
                throw new Error('Không thể lấy VAPID Public Key từ máy chủ.');
            }
            const vapidData = await vapidRes.json();
            if (!vapidData.publicKey) {
                throw new Error('Máy chủ chưa cấu hình VAPID keys.');
            }

            const applicationServerKey = urlBase64ToUint8Array(vapidData.publicKey);

            // 8. Tạo Subscription với PushManager
            let subscription = await registration.pushManager.getSubscription();
            if (!subscription) {
                subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: applicationServerKey
                });
            }

            // 9. Gửi subscription lên backend lưu trữ
            try {
                return await this.sendSubscriptionToBackend(subscription);
            } catch (err) {
                // Nếu lưu máy chủ thất bại, hủy đăng ký cục bộ để tránh lệch trạng thái
                try {
                    await subscription.unsubscribe();
                } catch (e) {}
                throw err;
            }
        },

        // Trích xuất tên thiết bị ngắn gọn và thân thiện
        getFriendlyDeviceName: function () {
            const ua = navigator.userAgent || '';
            let device = 'Thiết bị';
            if (/iPhone/.test(ua)) device = 'iPhone';
            else if (/iPad/.test(ua)) device = 'iPad';
            else if (/Android/.test(ua)) device = 'Android';
            else if (/Macintosh|Mac OS/.test(ua)) device = 'Máy Mac';
            else if (/Windows/.test(ua)) device = 'Máy tính Windows';
            else if (/Linux/.test(ua)) device = 'Máy Linux';

            let browser = 'Trình duyệt';
            if (/Edg\//.test(ua)) browser = 'Edge';
            else if (/Chrome\//.test(ua)) browser = 'Chrome';
            else if (/Safari\//.test(ua) && !/Chrome\//.test(ua)) browser = 'Safari';
            else if (/Firefox\//.test(ua)) browser = 'Firefox';

            return `${device} (${browser})`.substring(0, 100);
        },

        // Trích xuất keys an toàn từ subscription
        extractSubscriptionKeys: function (subscription) {
            const subJson = (typeof subscription.toJSON === 'function') ? subscription.toJSON() : {};
            let p256dh = subJson.keys?.p256dh || '';
            let auth = subJson.keys?.auth || '';

            if (!p256dh && typeof subscription.getKey === 'function') {
                try {
                    const rawKey = subscription.getKey('p256dh');
                    if (rawKey) {
                        p256dh = btoa(String.fromCharCode.apply(null, new Uint8Array(rawKey)))
                            .replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
                    }
                } catch (e) {}
            }

            if (!auth && typeof subscription.getKey === 'function') {
                try {
                    const rawAuth = subscription.getKey('auth');
                    if (rawAuth) {
                        auth = btoa(String.fromCharCode.apply(null, new Uint8Array(rawAuth)))
                            .replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
                    }
                } catch (e) {}
            }

            return {
                p256dh: p256dh,
                auth: auth
            };
        },

        // Gửi subscription lên máy chủ Laravel lưu trữ
        sendSubscriptionToBackend: async function (subscription) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const keys = this.extractSubscriptionKeys(subscription);

            const saveRes = await fetch('/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                    'ngrok-skip-browser-warning': 'true'
                },
                body: JSON.stringify({
                    endpoint: subscription.endpoint,
                    keys: keys,
                    device_name: this.getFriendlyDeviceName()
                })
            });

            if (!saveRes.ok) {
                let errorMsg = 'Không thể lưu thông tin đăng ký thông báo trên máy chủ.';
                try {
                    const errJson = await saveRes.json();
                    if (errJson.message) {
                        errorMsg = errJson.message;
                    }
                } catch (e) {}
                throw new Error(errorMsg);
            }

            return await saveRes.json();
        },

        // Hủy đăng ký thông báo đẩy
        unsubscribeFromPush: async function () {
            if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
                return false;
            }

            const registration = await navigator.serviceWorker.ready;
            const subscription = await registration.pushManager.getSubscription();

            if (subscription) {
                const endpoint = subscription.endpoint;
                await subscription.unsubscribe();

                // Báo cho backend xóa
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                await fetch('/push/unsubscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                        'Accept': 'application/json',
                        'ngrok-skip-browser-warning': 'true'
                    },
                    body: JSON.stringify({ endpoint: endpoint })
                }).catch(err => console.warn('Lỗi báo hủy lên backend:', err));

                return true;
            }

            return false;
        },

        // Kiểm tra trạng thái Push hiện tại và đồng bộ với máy chủ
        getPushStatus: async function () {
            // 1. Kiểm tra môi trường HTTPS
            if (!this.isSecure()) {
                return {
                    supported: false,
                    reason: 'insecure_http',
                    subscribed: false,
                    permission: 'unsupported'
                };
            }

            // 2. Kiểm tra In-App Browser (Zalo / Facebook)
            if (this.isInAppBrowser()) {
                return {
                    supported: false,
                    reason: 'in_app_browser',
                    subscribed: false,
                    permission: 'unsupported'
                };
            }

            // 3. Xử lý trường hợp iOS Safari
            if (this.isIOS()) {
                // Trên iOS Safari thường (chưa Add to Home Screen), Web Push chưa thể bật
                if (!this.isStandalone()) {
                    return {
                        supported: true,
                        needsInstall: true,
                        reason: 'ios_needs_home_screen',
                        subscribed: false,
                        permission: ('Notification' in window) ? Notification.permission : 'default'
                    };
                }
                // Nếu đã chạy Standalone mà vẫn không có PushManager (iOS < 16.4)
                if (!('PushManager' in window)) {
                    return {
                        supported: false,
                        reason: 'ios_version_too_old',
                        subscribed: false,
                        permission: 'unsupported'
                    };
                }
            }

            // 4. Kiểm tra các API tiêu chuẩn
            if (!('Notification' in window) || !('serviceWorker' in navigator) || !('PushManager' in window)) {
                return {
                    supported: false,
                    reason: 'api_unsupported',
                    subscribed: false,
                    permission: 'unsupported'
                };
            }

            const permission = Notification.permission;
            if (permission === 'denied') {
                return {
                    supported: true,
                    reason: 'permission_denied',
                    subscribed: false,
                    permission: 'denied'
                };
            }

            if (permission !== 'granted') {
                return {
                    supported: true,
                    subscribed: false,
                    permission: permission
                };
            }

            try {
                const reg = await navigator.serviceWorker.ready;
                const sub = await reg.pushManager.getSubscription();
                if (!sub) {
                    return {
                        supported: true,
                        subscribed: false,
                        permission: permission,
                        endpoint: null
                    };
                }

                // Kiểm tra xem máy chủ đã có lưu thiết bị này chưa
                try {
                    const statusRes = await fetch('/push/status', {
                        headers: {
                            'Accept': 'application/json',
                            'ngrok-skip-browser-warning': 'true'
                        }
                    });
                    if (statusRes.ok) {
                        const statusData = await statusRes.json();
                        const deviceCount = statusData?.data?.device_count ?? 0;
                        if (deviceCount === 0) {
                            console.log('[PWA] Tự động đồng bộ Subscription của thiết bị lên máy chủ...');
                            await this.sendSubscriptionToBackend(sub);
                        }
                    }
                } catch (syncErr) {
                    console.warn('[PWA] Lỗi kiểm tra /push/status:', syncErr);
                }

                return {
                    supported: true,
                    subscribed: true,
                    permission: permission,
                    endpoint: sub.endpoint
                };
            } catch (err) {
                return {
                    supported: true,
                    subscribed: false,
                    permission: permission,
                    error: err.message
                };
            }
        },

        // Hiển thị thông báo Toast hiện đại trên giao diện (Tương thích Dark Mode)
        showToast: function (type, message, title = 'Life Planner PWA') {
            let container = document.getElementById('pwaToastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'pwaToastContainer';
                container.style.cssText = 'position: fixed; top: 18px; right: 18px; z-index: 10999; display: flex; flex-direction: column; gap: 10px; max-width: calc(100vw - 36px); width: 360px; pointer-events: none;';
                document.body.appendChild(container);
            }

            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark' || document.documentElement.classList.contains('dark-mode');
            const toastBg = isDark ? 'rgba(30, 41, 59, 0.96)' : 'rgba(255, 255, 255, 0.96)';
            const titleColor = isDark ? '#f8fafc' : '#1e293b';
            const textColor = isDark ? '#94a3b8' : '#64748b';
            const borderColor = isDark ? 'rgba(255, 255, 255, 0.12)' : 'rgba(0, 0, 0, 0.08)';

            const toast = document.createElement('div');
            toast.className = 'pwa-modern-toast shadow-lg';
            toast.style.cssText = `pointer-events: auto; background: ${toastBg}; border: 1px solid ${borderColor}; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-radius: 16px; padding: 12px 16px; display: flex; align-items: flex-start; gap: 12px; transform: translateY(-12px); opacity: 0; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);`;

            const iconMap = {
                success: { icon: 'bi-check-circle-fill', color: '#10b981', bg: 'rgba(16, 185, 129, 0.14)' },
                info: { icon: 'bi-info-circle-fill', color: '#6366f1', bg: 'rgba(99, 102, 241, 0.14)' },
                warning: { icon: 'bi-exclamation-triangle-fill', color: '#f59e0b', bg: 'rgba(245, 158, 11, 0.14)' },
                danger: { icon: 'bi-x-circle-fill', color: '#ef4444', bg: 'rgba(239, 68, 68, 0.14)' }
            };

            const cfg = iconMap[type] || iconMap.info;

            toast.innerHTML = `
                <div style="width: 34px; height: 34px; border-radius: 10px; background: ${cfg.bg}; color: ${cfg.color}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.1rem;">
                    <i class="bi ${cfg.icon}"></i>
                </div>
                <div style="flex-grow: 1; min-width: 0;">
                    <div style="font-weight: 700; font-size: 0.85rem; color: ${titleColor}; margin-bottom: 2px;">${title}</div>
                    <div style="font-size: 0.8rem; color: ${textColor}; line-height: 1.35; word-break: break-word;">${message}</div>
                </div>
                <button type="button" style="border: none; background: none; color: ${textColor}; font-size: 1.1rem; line-height: 1; cursor: pointer; padding: 0;" aria-label="Close">&times;</button>
            `;

            container.appendChild(toast);

            // Hiệu ứng trượt vào
            requestAnimationFrame(() => {
                toast.style.transform = 'translateY(0)';
                toast.style.opacity = '1';
            });

            const closeToast = () => {
                toast.style.transform = 'translateY(-12px)';
                toast.style.opacity = '0';
                setTimeout(() => {
                    toast.remove();
                }, 300);
            };

            toast.querySelector('button')?.addEventListener('click', closeToast);
            setTimeout(closeToast, 5000);
        },

        // Đồng bộ giao diện trạng thái Push Notification
        syncPushUI: async function () {
            const status = await this.getPushStatus();
            const badges = document.querySelectorAll('.pwa-push-status-badge, #pwa-push-status-badge');
            const toggleBtns = document.querySelectorAll('.pwa-push-toggle-btn, #pwa-push-toggle-btn');
            const testBtns = document.querySelectorAll('.pwa-push-test-btn, #pwa-push-test-btn');
            const quickTestCards = document.querySelectorAll('.pwa-quick-test-section');

            // TRƯỜNG HỢP 1: Mở qua HTTP thường trên IP mạng LAN (cần link HTTPS / ngrok)
            if (status.reason === 'insecure_http') {
                badges.forEach(badge => {
                    badge.className = 'badge bg-warning-subtle text-warning-emphasis rounded-pill px-2.5 py-1 text-nowrap';
                    badge.innerHTML = '<i class="bi bi-shield-exclamation me-1"></i>Cần link HTTPS (ngrok)';
                });
                toggleBtns.forEach(btn => {
                    btn.disabled = false;
                    btn.className = 'btn btn-sm btn-outline-warning rounded-pill px-3.5 py-2 fs-8 fw-semibold text-nowrap w-100 w-sm-auto text-center d-inline-flex align-items-center justify-content-center gap-2 pwa-push-toggle-btn';
                    btn.innerHTML = '<i class="bi bi-link-45deg me-1"></i> Bật qua HTTPS';
                });
                testBtns.forEach(btn => {
                    btn.style.display = 'inline-flex';
                    btn.disabled = false;
                });
                return;
            }

            // TRƯỜNG HỢP 2: Mở trong Zalo / Facebook In-app browser
            if (status.reason === 'in_app_browser') {
                badges.forEach(badge => {
                    badge.className = 'badge bg-warning-subtle text-warning-emphasis rounded-pill px-2.5 py-1 text-nowrap';
                    badge.innerHTML = '<i class="bi bi-browser-safari me-1"></i>Mở bằng Safari / Chrome';
                });
                toggleBtns.forEach(btn => {
                    btn.disabled = false;
                    btn.className = 'btn btn-sm btn-outline-warning rounded-pill px-3';
                    btn.innerHTML = '<i class="bi bi-box-arrow-up-right me-1"></i> Mở trình duyệt ngoài';
                });
                testBtns.forEach(btn => {
                    btn.style.display = 'inline-flex';
                    btn.disabled = false;
                });
                return;
            }

            // TRƯỜNG HỢP 3: iPhone / iPad trong Safari tab (cần thêm vào MH chính trước)
            if (status.reason === 'ios_needs_home_screen' || status.needsInstall) {
                badges.forEach(badge => {
                    badge.className = 'badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 text-nowrap';
                    badge.innerHTML = '<i class="bi bi-apple me-1"></i>Thêm vào MH chính để bật';
                });
                toggleBtns.forEach(btn => {
                    btn.disabled = false;
                    btn.className = 'btn btn-sm btn-outline-primary rounded-pill px-3';
                    btn.innerHTML = '<i class="bi bi-box-arrow-up me-1"></i> Cách bật trên iPhone';
                });
                testBtns.forEach(btn => {
                    btn.style.display = 'inline-flex';
                    btn.disabled = false;
                });
                return;
            }

            // TRƯỜNG HỢP 4: iPhone chạy Standalone nhưng iOS quá cũ (< 16.4)
            if (status.reason === 'ios_version_too_old') {
                badges.forEach(badge => {
                    badge.className = 'badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 text-nowrap';
                    badge.innerHTML = '<i class="bi bi-exclamation-octagon me-1"></i>Yêu cầu iOS 16.4+';
                });
                toggleBtns.forEach(btn => {
                    btn.disabled = true;
                    btn.className = 'btn btn-sm btn-outline-secondary rounded-pill px-3';
                    btn.innerHTML = '<i class="bi bi-slash-circle me-1"></i> Cần iOS 16.4+';
                });
                return;
            }

            // TRƯỜNG HỢP 5: Quyền thông báo bị người dùng từ chối (Block)
            if (status.reason === 'permission_denied') {
                badges.forEach(badge => {
                    badge.className = 'badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1 text-nowrap';
                    badge.innerHTML = '<i class="bi bi-bell-slash me-1"></i>Đã chặn quyền thông báo';
                });
                toggleBtns.forEach(btn => {
                    btn.disabled = false;
                    btn.className = 'btn btn-sm btn-outline-danger rounded-pill px-3';
                    btn.innerHTML = '<i class="bi bi-gear-fill me-1"></i> Bỏ chặn thông báo';
                });
                testBtns.forEach(btn => {
                    btn.style.display = 'inline-flex';
                    btn.disabled = false;
                });
                return;
            }

            // TRƯỜNG HỢP 6: Trình duyệt hoàn toàn không hỗ trợ Service Worker / Push
            if (!status.supported) {
                badges.forEach(badge => {
                    badge.className = 'badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 text-nowrap';
                    badge.innerHTML = '<i class="bi bi-x-circle me-1"></i>Trình duyệt chưa hỗ trợ';
                });
                toggleBtns.forEach(btn => {
                    btn.disabled = true;
                    btn.className = 'btn btn-sm btn-outline-secondary rounded-pill px-3.5 py-2 fs-8 fw-semibold text-nowrap w-100 w-sm-auto text-center d-inline-flex align-items-center justify-content-center gap-2 pwa-push-toggle-btn';
                    btn.innerHTML = '<i class="bi bi-slash-circle me-1"></i> Không hỗ trợ';
                });
                return;
            }

            // TRƯỜNG HỢP 7: Đã đăng ký thành công trên thiết bị này
            if (status.subscribed) {
                badges.forEach(badge => {
                    badge.className = 'badge bg-success-subtle text-success rounded-pill px-2.5 py-1 text-nowrap';
                    badge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Đã kích hoạt trên thiết bị này';
                });
                toggleBtns.forEach(btn => {
                    btn.disabled = false;
                    btn.className = 'btn btn-sm btn-outline-danger rounded-pill px-3.5 py-2 fs-8 fw-semibold text-nowrap w-100 w-sm-auto text-center d-inline-flex align-items-center justify-content-center gap-2 pwa-push-toggle-btn';
                    btn.innerHTML = '<i class="bi bi-bell-slash-fill me-1"></i> Tắt thông báo';
                });
                testBtns.forEach(btn => {
                    btn.style.display = 'inline-flex';
                    btn.disabled = false;
                });
                quickTestCards.forEach(c => c.classList.remove('opacity-75'));
            } else {
                // TRƯỜNG HỢP 8: Thiết bị đủ điều kiện nhưng chưa bấm bật
                badges.forEach(badge => {
                    badge.className = 'badge bg-warning-subtle text-warning-emphasis rounded-pill px-2.5 py-1 text-nowrap';
                    badge.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Chưa kích hoạt trên máy';
                });
                toggleBtns.forEach(btn => {
                    btn.disabled = false;
                    btn.className = 'btn btn-sm btn-primary rounded-pill px-3.5 py-2 fs-8 fw-semibold text-nowrap w-100 w-sm-auto text-center d-inline-flex align-items-center justify-content-center gap-2 pwa-push-toggle-btn';
                    btn.innerHTML = '<i class="bi bi-bell-fill me-1"></i> Bật thông báo đẩy';
                });
                testBtns.forEach(btn => {
                    btn.style.display = 'inline-flex';
                    btn.disabled = false;
                });
            }
        },

        // Chuyển đổi trạng thái nhận thông báo (Toggle)
        togglePush: async function () {
            const status = await this.getPushStatus();

            if (status.reason === 'insecure_http') {
                this.showToast('warning', 'Web Push yêu cầu kết nối bảo mật HTTPS. Vui lòng mở trang web qua link ngrok HTTPS trên điện thoại để bật.', 'Cần liên kết HTTPS');
                this.showGenericInstallModal();
                return;
            }

            if (status.reason === 'in_app_browser') {
                this.showToast('warning', 'Trình duyệt Zalo/Facebook chặn tính năng thông báo. Vui lòng bấm menu (···) chọn "Mở bằng trình duyệt ngoài" (Safari/Chrome).', 'Mở trình duyệt ngoài');
                this.showGenericInstallModal();
                return;
            }

            if (status.reason === 'ios_needs_home_screen' || status.needsInstall) {
                this.showIOSInstallInstructions();
                this.showToast('info', 'Trên iPhone: Bấm nút Chia sẻ ⎋ -> chọn "Thêm vào MH chính" ➕ rồi mở app từ icon ngoài màn hình để bật thông báo nhé!', 'Hướng dẫn iPhone');
                return;
            }

            if (status.reason === 'permission_denied') {
                this.showToast('danger', 'Bạn đã chặn quyền thông báo trước đó. Vui lòng bấm vào biểu tượng 🔒 hoặc ⚙️ trên thanh địa chỉ trình duyệt để Cho phép Thông báo.', 'Đã chặn quyền');
                return;
            }

            if (status.reason === 'ios_version_too_old' || !status.supported) {
                this.showToast('danger', 'Trình duyệt hoặc phiên bản hệ điều hành hiện tại không hỗ trợ Web Push Notification.', 'Không hỗ trợ');
                return;
            }

            const toggleBtn = document.getElementById('pwa-push-toggle-btn') || document.querySelector('.pwa-push-toggle-btn');
            const originalHtml = toggleBtn ? toggleBtn.innerHTML : null;
            if (toggleBtn) {
                toggleBtn.disabled = true;
                toggleBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Đang xử lý...';
            }

            try {
                if (status.subscribed) {
                    const confirmed = confirm('Bạn có chắc muốn tắt nhận thông báo đẩy trên thiết bị này?');
                    if (!confirmed) return;
                    await this.unsubscribeFromPush();
                    this.showToast('info', 'Đã tắt nhận thông báo đẩy trên thiết bị này.', 'Đã tắt thông báo');
                } else {
                    await this.subscribeToPush();
                    this.showToast('success', 'Đã kích hoạt thông báo đẩy thành công trên thiết bị này!', 'Bật thông báo thành công');
                }
            } catch (err) {
                this.showToast('danger', err.message, 'Lỗi thiết lập thông báo');
            } finally {
                await this.syncPushUI();
                if (toggleBtn && originalHtml) {
                    toggleBtn.disabled = false;
                    toggleBtn.innerHTML = originalHtml;
                }
            }
        },

        // Gửi thử nghiệm một thông báo đẩy đến thiết bị (Hỗ trợ kịch bản: test, deadline, lich_hoc, tap_luyen)
        sendTestPush: async function (type = 'test', clickedBtn = null) {
            let activeBtn = clickedBtn;
            if (!activeBtn) {
                activeBtn = document.getElementById('pwa-push-test-btn') || document.querySelector('.pwa-push-test-btn');
            }

            const originalHtml = activeBtn ? activeBtn.innerHTML : null;
            if (activeBtn) {
                activeBtn.disabled = true;
                activeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Đang test...';
            }

            try {
                const status = await this.getPushStatus();

                if (status.reason === 'insecure_http') {
                    this.showToast('warning', 'Cần mở qua liên kết HTTPS (ngrok) trên điện thoại mới có thể nhận thông báo đẩy.', 'Yêu cầu HTTPS');
                    this.showGenericInstallModal();
                    return;
                }

                if (status.reason === 'in_app_browser') {
                    this.showToast('warning', 'Vui lòng mở bằng Safari hoặc Chrome bên ngoài để nhận thông báo đẩy.', 'Mở trình duyệt ngoài');
                    this.showGenericInstallModal();
                    return;
                }

                if (status.reason === 'ios_needs_home_screen' || status.needsInstall) {
                    this.showIOSInstallInstructions();
                    this.showToast('info', 'Trên iPhone, bạn cần Thêm vào Màn hình chính trước thì Safari mới cho phép nhận thông báo.', 'Hướng dẫn iPhone');
                    return;
                }

                if (status.reason === 'permission_denied') {
                    this.showToast('danger', 'Trình duyệt đang bị chặn quyền thông báo. Vui lòng mở cài đặt trình duyệt để cho phép.', 'Bị chặn quyền');
                    return;
                }

                if (!status.supported) {
                    this.showToast('danger', 'Trình duyệt này chưa hỗ trợ Web Push.', 'Không hỗ trợ');
                    return;
                }

                // Nếu thiết bị chưa đăng ký Push, tự động kích hoạt quyền trước (1-Click Experience)
                if (!status.subscribed) {
                    this.showToast('info', 'Đang yêu cầu cấp quyền thông báo đẩy trên thiết bị của bạn...', 'Cấp quyền thông báo');
                    try {
                        await this.subscribeToPush();
                        await this.syncPushUI();
                    } catch (subErr) {
                        this.showToast('warning', subErr.message || 'Bạn chưa cấp quyền nhận thông báo.', 'Chưa cấp quyền');
                        return;
                    }
                }

                // Gửi lệnh Test Push lên máy chủ có Timeout 15s bảo vệ
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 15000);

                try {
                    const res = await fetch('/push/test', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken || '',
                            'Accept': 'application/json',
                            'ngrok-skip-browser-warning': 'true'
                        },
                        body: JSON.stringify({ type: type }),
                        signal: controller.signal
                    });
                    clearTimeout(timeoutId);

                    const data = await res.json();
                    if (res.ok && data.success) {
                        if (navigator.vibrate) {
                            try { navigator.vibrate([100, 50, 100]); } catch (e) {}
                        }
                        this.showToast('success', data.body || 'Đã gửi test thông báo đẩy đến thiết bị! Hãy nhìn lên thanh thông báo / màn hình khóa.', data.title || 'Đã gửi test thông báo');
                    } else {
                        this.showToast('danger', data.message || 'Gửi thông báo thử thất bại.', 'Lỗi gửi thông báo');
                    }
                    return data;
                } catch (fetchErr) {
                    clearTimeout(timeoutId);
                    if (fetchErr.name === 'AbortError') {
                        this.showToast('danger', 'Quá thời gian chờ phản hồi từ máy chủ (Timeout). Vui lòng kiểm tra lại mạng hoặc thử lại.', 'Hết thời gian chờ');
                    } else {
                        throw fetchErr;
                    }
                }
            } catch (err) {
                this.showToast('danger', 'Lỗi kết nối máy chủ: ' + err.message, 'Lỗi kết nối');
            } finally {
                if (activeBtn && originalHtml) {
                    activeBtn.disabled = false;
                    activeBtn.innerHTML = originalHtml;
                }
            }
        },

        // Tự động kiểm tra và kích hoạt thông báo đến hạn
        checkReminders: async function () {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) return;
                await fetch('/push/check-reminders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'ngrok-skip-browser-warning': 'true'
                    }
                });
            } catch (e) {
                // Im lặng khi chạy ngầm
            }
        }
    };

    // Đăng ký toàn cục
    window.LifePlannerPWA = LifePlannerPWA;
    window.pwaManager = LifePlannerPWA;

    // Lắng nghe sự kiện click cài đặt trên toàn trang
    document.addEventListener('click', function (e) {
        const installBtn = e.target.closest('[data-pwa-install]');
        if (installBtn) {
            e.preventDefault();
            LifePlannerPWA.promptInstall();
        }
    });

    // Tự động khởi chạy khi tài liệu nạp xong
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            LifePlannerPWA.init();
            LifePlannerPWA.syncPushUI();
        });
    } else {
        LifePlannerPWA.init();
        LifePlannerPWA.syncPushUI();
    }
})();
