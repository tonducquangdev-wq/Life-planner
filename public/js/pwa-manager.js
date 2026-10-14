/**
 * Life Planner - PWA & Web Push Manager
 * Handles Service Worker registration, install prompts (Android/iOS),
 * and Web Push Notifications subscription.
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

        // Kiểm tra ứng dụng có đang chạy ở chế độ Standalone (Đã cài đặt) không
        isStandalone: function () {
            return (
                window.matchMedia('(display-mode: standalone)').matches ||
                window.navigator.standalone === true ||
                document.referrer.includes('android-app://')
            );
        },

        // Kiểm tra thiết bị iOS (iPhone/iPad)
        isIOS: function () {
            const ua = window.navigator.userAgent.toLowerCase();
            return /iphone|ipad|ipod/.test(ua);
        },

        // Khởi tạo Service Worker và các trình lắng nghe
        init: function () {
            // Thêm class pwa-standalone vào body nếu mở từ icon màn hình chính
            if (this.isStandalone()) {
                document.documentElement.classList.add('pwa-standalone');
                document.body?.classList.add('pwa-standalone');
            }

            // Đăng ký Service Worker
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
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
                });
            }

            // Lắng nghe sự kiện trước khi cài đặt trên Android / Chrome
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredInstallPrompt = e;
                console.log('[PWA] Thiết bị đủ điều kiện cài đặt.');

                // Báo cho giao diện biết để hiển thị nút/banner cài đặt nếu cần
                window.dispatchEvent(new CustomEvent('pwa-installable', { detail: { prompt: e } }));

                // Hiển thị nút cài đặt trong các phần tử có data-pwa-install
                document.querySelectorAll('[data-pwa-install]').forEach(el => {
                    el.classList.remove('d-none');
                });
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
            });
        },

        // Kích hoạt hộp thoại cài đặt ứng dụng (Android / Desktop)
        promptInstall: async function () {
            if (this.isStandalone()) {
                alert('Ứng dụng Life Planner đã được cài đặt trên thiết bị của bạn!');
                return;
            }

            // Nếu là thiết bị iOS
            if (this.isIOS()) {
                this.showIOSInstallInstructions();
                return;
            }

            // Nếu là Android / Desktop có deferredPrompt
            if (deferredInstallPrompt) {
                deferredInstallPrompt.prompt();
                const choiceResult = await deferredInstallPrompt.userChoice;
                if (choiceResult.outcome === 'accepted') {
                    console.log('[PWA] Người dùng đã chấp nhận cài đặt.');
                } else {
                    console.log('[PWA] Người dùng từ chối cài đặt.');
                }
                deferredInstallPrompt = null;
            } else {
                // Nếu trình duyệt không bắn beforeinstallprompt (đã từ chối hoặc đã cài)
                this.showGenericInstallModal();
            }
        },

        // Hiển thị hướng dẫn thêm vào màn hình chính cho iOS Safari
        showIOSInstallInstructions: function () {
            const modalEl = document.getElementById('pwaInstallModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
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
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        },

        // ======================================================================
        // WEB PUSH NOTIFICATIONS
        // ======================================================================

        // Đăng ký nhận thông báo đẩy
        subscribeToPush: async function () {
            // Kiểm tra iOS Safari nếu chưa thêm vào MH chính
            if (this.isIOS() && !this.isStandalone()) {
                this.showIOSInstallInstructions();
                throw new Error('Trên iPhone/iPad (iOS), bạn cần bấm Chia sẻ ⎋ -> "Thêm vào MH chính" ➕ trước thì Safari mới cho phép nhận thông báo đẩy.');
            }

            if (!('Notification' in window)) {
                throw new Error('Trình duyệt của bạn không hỗ trợ Notifications API.');
            }

            if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
                throw new Error('Trình duyệt của bạn không hỗ trợ Push API.');
            }

            // 1. Yêu cầu quyền từ người dùng
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                throw new Error('Bạn chưa cấp quyền thông báo. Vui lòng cho phép quyền thông báo trong cài đặt trình duyệt.');
            }

            // 2. Chờ Service Worker sẵn sàng
            const registration = await navigator.serviceWorker.ready;

            // 3. Lấy VAPID Public Key từ backend
            const vapidRes = await fetch('/push/vapid-key');
            if (!vapidRes.ok) {
                throw new Error('Không thể lấy VAPID Public Key từ máy chủ.');
            }
            const vapidData = await vapidRes.json();
            if (!vapidData.publicKey) {
                throw new Error('Máy chủ chưa cấu hình VAPID keys.');
            }

            const applicationServerKey = urlBase64ToUint8Array(vapidData.publicKey);

            // 4. Tạo Subscription với PushManager
            let subscription = await registration.pushManager.getSubscription();
            if (!subscription) {
                subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: applicationServerKey
                });
            }

            // 5. Gửi subscription lên backend lưu trữ
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
                    'Accept': 'application/json'
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
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ endpoint: endpoint })
                }).catch(err => console.warn('Lỗi báo hủy lên backend:', err));

                return true;
            }

            return false;
        },

        // Kiểm tra trạng thái Push hiện tại và đồng bộ với máy chủ
        getPushStatus: async function () {
            if (!('Notification' in window) || !('serviceWorker' in navigator) || !('PushManager' in window)) {
                return { supported: false, subscribed: false, permission: 'unsupported' };
            }
            const permission = Notification.permission;
            if (permission !== 'granted') {
                return { supported: true, subscribed: false, permission: permission };
            }
            try {
                const reg = await navigator.serviceWorker.ready;
                const sub = await reg.pushManager.getSubscription();
                if (!sub) {
                    return { supported: true, subscribed: false, permission: permission, endpoint: null };
                }

                // Kiểm tra xem máy chủ đã có lưu thiết bị này chưa
                try {
                    const statusRes = await fetch('/push/status', {
                        headers: { 'Accept': 'application/json' }
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

                return { supported: true, subscribed: true, permission: permission, endpoint: sub.endpoint };
            } catch (err) {
                return { supported: true, subscribed: false, permission: permission, error: err.message };
            }
        },

        // Đồng bộ giao diện trạng thái Push Notification
        syncPushUI: async function () {
            const status = await this.getPushStatus();
            const badges = document.querySelectorAll('.pwa-push-status-badge, #pwa-push-status-badge');
            const toggleBtns = document.querySelectorAll('.pwa-push-toggle-btn, #pwa-push-toggle-btn');
            const testBtns = document.querySelectorAll('.pwa-push-test-btn, #pwa-push-test-btn');

            if (!status.supported) {
                badges.forEach(badge => {
                    badge.className = 'badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1';
                    badge.textContent = 'Trình duyệt không hỗ trợ';
                });
                toggleBtns.forEach(btn => btn.disabled = true);
                testBtns.forEach(btn => btn.style.display = 'none');
                return;
            }

            if (status.subscribed) {
                badges.forEach(badge => {
                    badge.className = 'badge bg-success-subtle text-success rounded-pill px-2.5 py-1';
                    badge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Đã bật trên thiết bị';
                });
                toggleBtns.forEach(btn => {
                    btn.disabled = false;
                    btn.className = 'btn btn-sm btn-outline-danger rounded-pill px-3';
                    btn.innerHTML = '<i class="bi bi-bell-slash-fill me-1"></i> Tắt thông báo';
                });
                testBtns.forEach(btn => btn.style.display = 'inline-flex');
            } else {
                badges.forEach(badge => {
                    badge.className = 'badge bg-warning-subtle text-warning-emphasis rounded-pill px-2.5 py-1';
                    badge.innerHTML = '<i class="bi bi-bell-slash me-1"></i>Chưa kích hoạt';
                });
                toggleBtns.forEach(btn => {
                    btn.disabled = false;
                    btn.className = 'btn btn-sm btn-primary rounded-pill px-3';
                    btn.innerHTML = '<i class="bi bi-bell-fill me-1"></i> Bật thông báo đẩy';
                });
                testBtns.forEach(btn => btn.style.display = 'none');
            }
        },

        // Chuyển đổi trạng thái nhận thông báo (Toggle)
        togglePush: async function () {
            const status = await this.getPushStatus();
            if (!status.supported) {
                alert('Trình duyệt hoặc môi trường hiện tại không hỗ trợ Web Push Notification.');
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
                    alert('Đã tắt nhận thông báo đẩy trên thiết bị.');
                } else {
                    await this.subscribeToPush();
                    alert('🎉 Tuyệt vời! Đã bật thông báo đẩy thành công cho thiết bị này.');
                }
            } catch (err) {
                alert('Không thể thay đổi trạng thái thông báo: ' + err.message);
            } finally {
                await this.syncPushUI();
                if (toggleBtn && originalHtml) {
                    toggleBtn.disabled = false;
                }
            }
        },

        // Gửi thử nghiệm một thông báo đẩy đến thiết bị
        sendTestPush: async function () {
            const testBtn = document.getElementById('pwa-push-test-btn') || document.querySelector('.pwa-push-test-btn');
            const originalHtml = testBtn ? testBtn.innerHTML : null;
            if (testBtn) {
                testBtn.disabled = true;
                testBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Đang gửi...';
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch('/push/test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    alert('🎉 Đã gửi thông báo thử nghiệm thành công! Vui lòng kiểm tra màn hình thông báo của thiết bị.');
                } else {
                    alert('Gửi thông báo thử thất bại: ' + (data.message || 'Lỗi không xác định'));
                }
                return data;
            } catch (err) {
                alert('Lỗi kết nối khi gửi thử thông báo: ' + err.message);
            } finally {
                if (testBtn && originalHtml) {
                    testBtn.disabled = false;
                    testBtn.innerHTML = originalHtml;
                }
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
