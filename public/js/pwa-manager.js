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

        // Hiển thị thông báo Toast hiện đại trên giao diện
        showToast: function (type, message, title = 'Life Planner PWA') {
            let container = document.getElementById('pwaToastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'pwaToastContainer';
                container.style.cssText = 'position: fixed; top: 18px; right: 18px; z-index: 10999; display: flex; flex-direction: column; gap: 10px; max-width: calc(100vw - 36px); width: 360px; pointer-events: none;';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            toast.className = 'pwa-modern-toast shadow-lg border';
            toast.style.cssText = 'pointer-events: auto; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-radius: 16px; padding: 12px 16px; display: flex; align-items: flex-start; gap: 12px; transform: translateY(-12px); opacity: 0; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);';

            const iconMap = {
                success: { icon: 'bi-check-circle-fill', color: '#10b981', bg: 'rgba(16, 185, 129, 0.12)' },
                info: { icon: 'bi-info-circle-fill', color: '#6366f1', bg: 'rgba(99, 102, 241, 0.12)' },
                warning: { icon: 'bi-exclamation-triangle-fill', color: '#f59e0b', bg: 'rgba(245, 158, 11, 0.12)' },
                danger: { icon: 'bi-x-circle-fill', color: '#ef4444', bg: 'rgba(239, 68, 68, 0.12)' }
            };

            const cfg = iconMap[type] || iconMap.info;

            toast.innerHTML = `
                <div style="width: 34px; height: 34px; border-radius: 10px; background: ${cfg.bg}; color: ${cfg.color}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.1rem;">
                    <i class="bi ${cfg.icon}"></i>
                </div>
                <div style="flex-grow: 1; min-width: 0;">
                    <div style="font-weight: 700; font-size: 0.85rem; color: #1e293b; margin-bottom: 2px;">${title}</div>
                    <div style="font-size: 0.8rem; color: #64748b; line-height: 1.35; word-break: break-word;">${message}</div>
                </div>
                <button type="button" style="border: none; background: none; color: #94a3b8; font-size: 1.1rem; line-height: 1; cursor: pointer; padding: 0;" aria-label="Close">&times;</button>
            `;

            container.appendChild(toast);

            // Kích hoạt hiệu ứng trượt vào
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
            setTimeout(closeToast, 4500);
        },

        // Đồng bộ giao diện trạng thái Push Notification
        syncPushUI: async function () {
            const status = await this.getPushStatus();
            const badges = document.querySelectorAll('.pwa-push-status-badge, #pwa-push-status-badge');
            const toggleBtns = document.querySelectorAll('.pwa-push-toggle-btn, #pwa-push-toggle-btn');
            const testBtns = document.querySelectorAll('.pwa-push-test-btn, #pwa-push-test-btn');
            const quickTestCards = document.querySelectorAll('.pwa-quick-test-section');

            if (!status.supported) {
                badges.forEach(badge => {
                    badge.className = 'badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1';
                    badge.innerHTML = '<i class="bi bi-x-circle me-1"></i>Trình duyệt chưa hỗ trợ';
                });
                toggleBtns.forEach(btn => btn.disabled = true);
                return;
            }

            if (status.subscribed) {
                badges.forEach(badge => {
                    badge.className = 'badge bg-success-subtle text-success rounded-pill px-2.5 py-1';
                    badge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Đã kích hoạt trên thiết bị này';
                });
                toggleBtns.forEach(btn => {
                    btn.disabled = false;
                    btn.className = 'btn btn-sm btn-outline-danger rounded-pill px-3';
                    btn.innerHTML = '<i class="bi bi-bell-slash-fill me-1"></i> Tắt thông báo';
                });
                testBtns.forEach(btn => {
                    btn.style.display = 'inline-flex';
                    btn.disabled = false;
                });
                quickTestCards.forEach(c => c.classList.remove('opacity-75'));
            } else {
                badges.forEach(badge => {
                    badge.className = 'badge bg-warning-subtle text-warning-emphasis rounded-pill px-2.5 py-1';
                    badge.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Chưa kích hoạt trên máy';
                });
                toggleBtns.forEach(btn => {
                    btn.disabled = false;
                    btn.className = 'btn btn-sm btn-primary rounded-pill px-3';
                    btn.innerHTML = '<i class="bi bi-bell-fill me-1"></i> Bật thông báo đẩy';
                });
                // Vẫn giữ nút test hiển thị để người dùng bấm 1 chạm là tự kích hoạt và bắn test luôn!
                testBtns.forEach(btn => {
                    btn.style.display = 'inline-flex';
                    btn.disabled = false;
                });
            }
        },

        // Chuyển đổi trạng thái nhận thông báo (Toggle)
        togglePush: async function () {
            const status = await this.getPushStatus();
            if (!status.supported) {
                this.showToast('danger', 'Trình duyệt hoặc môi trường hiện tại không hỗ trợ Web Push Notification.', 'Không hỗ trợ');
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
                activeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Đang gửi...';
            }

            try {
                // 1. Kiểm tra trạng thái hỗ trợ
                const status = await this.getPushStatus();
                if (!status.supported) {
                    this.showToast('danger', 'Trình duyệt hoặc môi trường hiện tại không hỗ trợ Web Push (Cần HTTPS hoặc cài đặt PWA).', 'Không hỗ trợ');
                    return;
                }

                // 2. Nếu thiết bị chưa đăng ký Push, tự động kích hoạt quyền trước (1-Click Experience)
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

                // 3. Gửi lệnh Test Push lên máy chủ
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch('/push/test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ type: type })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    if (navigator.vibrate) {
                        try { navigator.vibrate([100, 50, 100]); } catch (e) {}
                    }
                    this.showToast('success', data.body || 'Đã bắn thông báo đẩy đến thiết bị! Hãy nhìn lên thanh thông báo / màn hình khóa.', data.title || 'Đã gửi thông báo đẩy');
                } else {
                    this.showToast('danger', data.message || 'Gửi thông báo thử thất bại.', 'Lỗi gửi thông báo');
                }
                return data;
            } catch (err) {
                this.showToast('danger', 'Lỗi kết nối máy chủ: ' + err.message, 'Lỗi kết nối');
            } finally {
                if (activeBtn && originalHtml) {
                    activeBtn.disabled = false;
                    activeBtn.innerHTML = originalHtml;
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
