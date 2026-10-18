<section>
    <header class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h6 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-bell-fill text-primary"></i> Cài đặt Thông báo
            </h6>
            <p class="text-muted small mb-0">
                Tùy chỉnh bật/tắt nhận thông báo nhắc nhở lịch học, bài tập deadline và lịch tập luyện.
            </p>
        </div>
    </header>

    @if (session('status') === 'notifications-updated')
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-xs mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>Cài đặt thông báo đã được cập nhật thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 1. PWA & WEB PUSH NOTIFICATIONS ON CURRENT DEVICE (TRUNG TÂM KIỂM THỬ THÔNG BÁO) -->
    <div class="card border border-primary-subtle bg-gradient rounded-4 p-3.5 mb-4 shadow-sm" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(139, 92, 246, 0.04) 100%);">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div class="d-flex align-items-center gap-2.5 min-w-0">
                <div class="p-2.5 rounded-3 bg-primary text-white flex-shrink-0 shadow-xs">
                    <i class="bi bi-phone-vibrate-fill fs-5"></i>
                </div>
                <div class="min-w-0">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-0.5">
                        <h6 class="fw-bold text-dark mb-0 fs-6 text-truncate">Thông báo đẩy di động (Web Push PWA)</h6>
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-0.5 fs-9 text-nowrap pwa-push-status-badge" id="pwa-push-status-badge">
                            <span class="spinner-border spinner-border-sm me-1" style="width: 0.7rem; height: 0.7rem;"></span> Đang kiểm tra...
                        </span>
                    </div>
                    <small class="text-muted fs-8 d-block">Nhận chuông và popup nhắc lịch học, deadline ngay cả khi đã đóng ứng dụng</small>
                </div>
            </div>

            <!-- Nút bật/tắt thiết bị -->
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fs-8 fw-semibold text-nowrap pwa-push-toggle-btn" id="pwa-push-toggle-btn" onclick="window.LifePlannerPWA && window.LifePlannerPWA.togglePush()">
                <i class="bi bi-bell-fill me-1"></i> Bật thông báo đẩy
            </button>
        </div>

        <!-- KHU VỰC NÚT TEST THÔNG BÁO ĐẨY TỨC THÌ (HERO TEST BUTTON) -->
        <div class="p-3 bg-white rounded-3 border border-primary-subtle border-opacity-40 shadow-xs pwa-quick-test-section">
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2.5 mb-2.5">
                <div>
                    <div class="fw-bold text-dark fs-7 d-flex align-items-center gap-1.5">
                        <i class="bi bi-lightning-charge-fill text-warning"></i>
                        <span>Thử nghiệm bắn thông báo tức thì</span>
                    </div>
                    <small class="text-muted fs-8">Bấm nút để hệ thống gửi ngay 1 thông báo đẩy có chuông & rung về thiết bị của bạn.</small>
                </div>

                <!-- Nút Test Chính 1-Chạm -->
                <button type="button" class="btn btn-primary rounded-pill px-3.5 py-2 fs-7 fw-semibold text-nowrap flex-shrink-0 shadow-sm d-inline-flex align-items-center justify-content-center gap-2 pwa-push-test-btn" id="pwa-push-test-btn" onclick="window.LifePlannerPWA && window.LifePlannerPWA.sendTestPush('test', this)">
                    <i class="bi bi-send-fill"></i>
                    <span>Bắn thông báo thử</span>
                </button>
            </div>

            <!-- Các kịch bản test nhanh theo tình huống thực tế -->
            <div class="pt-2 border-top border-light-subtle">
                <div class="text-muted fs-9 mb-1.5 fw-semibold text-uppercase tracking-wider">Hoặc thử theo kịch bản sự kiện:</div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 fs-8 text-dark d-inline-flex align-items-center gap-1.5 hover-lift" onclick="window.LifePlannerPWA && window.LifePlannerPWA.sendTestPush('deadline', this)" title="Thử thông báo hạn chót bài tập">
                        <span>⏰</span> <span>Nhắc Deadline</span>
                    </button>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 fs-8 text-dark d-inline-flex align-items-center gap-1.5 hover-lift" onclick="window.LifePlannerPWA && window.LifePlannerPWA.sendTestPush('lich_hoc', this)" title="Thử thông báo lịch học">
                        <span>🎓</span> <span>Nhắc Lịch học</span>
                    </button>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 fs-8 text-dark d-inline-flex align-items-center gap-1.5 hover-lift" onclick="window.LifePlannerPWA && window.LifePlannerPWA.sendTestPush('tap_luyen', this)" title="Thử thông báo tập gym">
                        <span>💪</span> <span>Nhắc Tập luyện</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. CÀI ĐẶT ỨNG DỤNG PWA (ANDROID & IOS) -->
    <div class="card border rounded-4 p-3 mb-4 bg-white shadow-xs">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2.5 min-w-0">
                <div class="p-2 rounded-circle bg-dark-subtle text-dark flex-shrink-0">
                    <i class="bi bi-cloud-arrow-down-fill fs-5"></i>
                </div>
                <div class="min-w-0">
                    <h6 class="fw-bold text-dark mb-0 fs-7 text-truncate">Cài đặt Life Planner (PWA)</h6>
                    <small class="text-muted fs-8 d-none d-sm-block text-truncate">Trải nghiệm toàn màn hình như ứng dụng gốc</small>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fs-8 fw-semibold flex-shrink-0 text-nowrap" data-pwa-install="true">
                <i class="bi bi-download me-1"></i> Cài đặt
            </button>
        </div>
    </div>

    <form method="post" action="{{ route('profile.notifications.update') }}">
        @csrf
        @method('patch')

        <style>
            .notif-master-banner {
                background: linear-gradient(135deg, rgba(79, 70, 229, 0.08) 0%, rgba(99, 102, 241, 0.03) 100%);
                border: 1.5px solid rgba(79, 70, 229, 0.2);
                box-shadow: 0 4px 16px rgba(79, 70, 229, 0.06);
                transition: all 0.25s ease;
            }
            [data-bs-theme="dark"] .notif-master-banner {
                background: linear-gradient(135deg, rgba(99, 102, 241, 0.16) 0%, rgba(30, 34, 48, 0.7) 100%);
                border-color: rgba(99, 102, 241, 0.35);
                box-shadow: 0 4px 18px rgba(0, 0, 0, 0.3);
            }
            .notif-master-icon-box {
                width: 44px;
                height: 44px;
                border-radius: 14px;
                background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
                color: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.25rem;
                box-shadow: 0 6px 16px rgba(79, 70, 229, 0.32);
                flex-shrink: 0;
            }
            .notif-sub-container {
                transition: opacity 0.25s ease, filter 0.25s ease;
            }
            .notif-sub-container.is-disabled {
                opacity: 0.42;
                filter: grayscale(0.25);
                pointer-events: none;
            }
            .notif-sub-list {
                display: flex !important;
                flex-direction: column !important;
                gap: 12px !important;
            }
            .notif-item-card {
                background: #ffffff;
                border: 1px solid rgba(0, 0, 0, 0.08) !important;
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.03);
                border-radius: 16px !important;
                padding: 13px 18px !important;
                transition: all 0.22s cubic-bezier(0.34, 1.3, 0.64, 1);
                position: relative;
                user-select: none;
                margin-bottom: 0 !important;
            }
            .notif-item-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 22px rgba(0, 0, 0, 0.07);
                border-color: rgba(99, 102, 241, 0.35) !important;
            }
            [data-bs-theme="dark"] .notif-item-card {
                background: #1e1e24;
                border-color: rgba(255, 255, 255, 0.08) !important;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            }
            [data-bs-theme="dark"] .notif-item-card:hover {
                background: #25252d;
                border-color: rgba(99, 102, 241, 0.45) !important;
                box-shadow: 0 8px 22px rgba(0, 0, 0, 0.45);
            }
            .notif-item-icon {
                width: 40px;
                height: 40px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.15rem;
                flex-shrink: 0;
                transition: transform 0.2s ease;
            }
            .notif-item-card:hover .notif-item-icon {
                transform: scale(1.08);
            }
            .notif-item-icon.icon-academic {
                background: rgba(14, 165, 233, 0.12);
                color: #0284c7;
                border: 1px solid rgba(14, 165, 233, 0.25);
            }
            [data-bs-theme="dark"] .notif-item-icon.icon-academic {
                background: rgba(14, 165, 233, 0.2);
                color: #38bdf8;
                border-color: rgba(14, 165, 233, 0.35);
            }
            .notif-item-icon.icon-deadline {
                background: rgba(239, 68, 68, 0.12);
                color: #dc2626;
                border: 1px solid rgba(239, 68, 68, 0.25);
            }
            [data-bs-theme="dark"] .notif-item-icon.icon-deadline {
                background: rgba(239, 68, 68, 0.2);
                color: #f87171;
                border-color: rgba(239, 68, 68, 0.35);
            }
            .notif-item-icon.icon-task {
                background: rgba(147, 51, 234, 0.12);
                color: #7e22ce;
                border: 1px solid rgba(147, 51, 234, 0.25);
            }
            [data-bs-theme="dark"] .notif-item-icon.icon-task {
                background: rgba(147, 51, 234, 0.2);
                color: #c084fc;
                border-color: rgba(147, 51, 234, 0.35);
            }
            .notif-item-icon.icon-fitness {
                background: rgba(16, 185, 129, 0.12);
                color: #059669;
                border: 1px solid rgba(16, 185, 129, 0.25);
            }
            [data-bs-theme="dark"] .notif-item-icon.icon-fitness {
                background: rgba(16, 185, 129, 0.2);
                color: #34d399;
                border-color: rgba(16, 185, 129, 0.35);
            }
            .notif-item-icon.icon-audio {
                background: rgba(245, 158, 11, 0.12);
                color: #d97706;
                border: 1px solid rgba(245, 158, 11, 0.25);
            }
            [data-bs-theme="dark"] .notif-item-icon.icon-audio {
                background: rgba(245, 158, 11, 0.2);
                color: #fbbf24;
                border-color: rgba(245, 158, 11, 0.35);
            }
            .notif-switch-lg {
                width: 2.85rem !important;
                height: 1.55rem !important;
                cursor: pointer;
            }
            .notif-sub-switch {
                width: 2.5rem !important;
                height: 1.4rem !important;
                cursor: pointer;
            }
            .notif-btn-save {
                background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%) !important;
                color: #ffffff !important;
                border: 1.5px solid rgba(255, 255, 255, 0.45) !important;
                box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
                cursor: pointer;
                text-decoration: none;
                transition: all 0.22s cubic-bezier(0.34, 1.3, 0.64, 1);
            }
            .notif-btn-save:hover {
                background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%) !important;
                color: #ffffff !important;
                transform: translateY(-2px);
                box-shadow: 0 8px 22px rgba(79, 70, 229, 0.45);
            }
            .notif-btn-save:active {
                transform: scale(0.97);
            }
        </style>

        <!-- Master Switch: Bật / Tắt tất cả thông báo -->
        <div class="notif-master-banner mb-3 p-3 rounded-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <div class="notif-master-icon-box">
                    <i class="bi bi-bell-fill"></i>
                </div>
                <div class="min-w-0">
                    <label for="thong_bao_enabled" class="fw-bold fs-7 mb-0 d-block cursor-pointer text-dark">
                        Bật tất cả thông báo hệ thống
                    </label>
                    <div class="text-muted fs-8 text-truncate">
                        Cho phép Life Planner gửi thông báo nhắc nhở real-time và hiển thị trên ứng dụng
                    </div>
                </div>
            </div>
            <div class="form-check form-switch m-0 flex-shrink-0">
                <input class="form-check-input cursor-pointer notif-switch-lg" type="checkbox" role="switch" id="thong_bao_enabled" name="thong_bao_enabled" {{ old('thong_bao_enabled', $user->thong_bao_enabled ?? true) ? 'checked' : '' }}>
            </div>
        </div>

        <div id="subNotificationSettings" class="notif-sub-container mb-4 {{ old('thong_bao_enabled', $user->thong_bao_enabled ?? true) ? '' : 'is-disabled' }}">
            <div class="d-flex align-items-center justify-content-between px-1 mb-2">
                <span class="fs-9 fw-bold text-uppercase text-muted" style="letter-spacing: 0.05em;">
                    <i class="bi bi-sliders2 me-1"></i>Chi tiết các mục nhắc nhở
                </span>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-9 fw-semibold notif-active-counter" id="pageNotifCounter">
                    Đang hoạt động
                </span>
            </div>

            <div class="notif-sub-list">
                <!-- Option 1: Thông báo Lịch học -->
                <div class="notif-item-card border d-flex align-items-center justify-content-between">
                    <label for="thong_bao_lich_hoc" class="d-flex align-items-center gap-3 min-w-0 cursor-pointer flex-grow-1 mb-0 me-2">
                        <div class="notif-item-icon icon-academic">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="fw-bold fs-7 text-dark mb-0.5">Thông báo Lịch học</div>
                            <div class="text-muted fs-8 text-truncate">Nhắc nhở lịch học trước 30 phút theo thời khóa biểu môn học</div>
                        </div>
                    </label>
                    <div class="form-check form-switch m-0 flex-shrink-0">
                        <input class="form-check-input cursor-pointer sub-notif-switch" type="checkbox" role="switch" id="thong_bao_lich_hoc" name="thong_bao_lich_hoc" {{ old('thong_bao_lich_hoc', $user->thong_bao_lich_hoc ?? true) ? 'checked' : '' }}>
                    </div>
                </div>

                <!-- Option 2: Thông báo Deadline -->
                <div class="notif-item-card border d-flex align-items-center justify-content-between">
                    <label for="thong_bao_deadline" class="d-flex align-items-center gap-3 min-w-0 cursor-pointer flex-grow-1 mb-0 me-2">
                        <div class="notif-item-icon icon-deadline">
                            <i class="bi bi-alarm-fill"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="fw-bold fs-7 text-dark mb-0.5">Thông báo Deadline & Bài tập</div>
                            <div class="text-muted fs-8 text-truncate">Cảnh báo hạn nộp bài tập chuẩn bị đến hạn (trước 24 giờ và trước 2 giờ)</div>
                        </div>
                    </label>
                    <div class="form-check form-switch m-0 flex-shrink-0">
                        <input class="form-check-input cursor-pointer sub-notif-switch" type="checkbox" role="switch" id="thong_bao_deadline" name="thong_bao_deadline" {{ old('thong_bao_deadline', $user->thong_bao_deadline ?? true) ? 'checked' : '' }}>
                    </div>
                </div>

                <!-- Option 3: Thông báo Công việc & Dự án -->
                <div class="notif-item-card border d-flex align-items-center justify-content-between">
                    <label for="thong_bao_cong_viec" class="d-flex align-items-center gap-3 min-w-0 cursor-pointer flex-grow-1 mb-0 me-2">
                        <div class="notif-item-icon icon-task">
                            <i class="bi bi-briefcase-fill"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="fw-bold fs-7 text-dark mb-0.5">Thông báo Công việc & Nhiệm vụ</div>
                            <div class="text-muted fs-8 text-truncate">Nhắc nhở công việc cần làm, tiến độ và hạn hoàn thành dự án</div>
                        </div>
                    </label>
                    <div class="form-check form-switch m-0 flex-shrink-0">
                        <input class="form-check-input cursor-pointer sub-notif-switch" type="checkbox" role="switch" id="thong_bao_cong_viec" name="thong_bao_cong_viec" {{ old('thong_bao_cong_viec', $user->thong_bao_cong_viec ?? true) ? 'checked' : '' }}>
                    </div>
                </div>

                <!-- Option 3: Thông báo Tập luyện -->
                <div class="notif-item-card border d-flex align-items-center justify-content-between">
                    <label for="thong_bao_tap_luyen" class="d-flex align-items-center gap-3 min-w-0 cursor-pointer flex-grow-1 mb-0 me-2">
                        <div class="notif-item-icon icon-fitness">
                            <i class="bi bi-fire"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="fw-bold fs-7 text-dark mb-0.5">Thông báo Lịch tập luyện</div>
                            <div class="text-muted fs-8 text-truncate">Nhắc nhở buổi tập thể chất trong ngày (Push, Pull, Legs, Cardio)</div>
                        </div>
                    </label>
                    <div class="form-check form-switch m-0 flex-shrink-0">
                        <input class="form-check-input cursor-pointer sub-notif-switch" type="checkbox" role="switch" id="thong_bao_tap_luyen" name="thong_bao_tap_luyen" {{ old('thong_bao_tap_luyen', $user->thong_bao_tap_luyen ?? true) ? 'checked' : '' }}>
                    </div>
                </div>

                <!-- Option 4: Âm thanh thông báo -->
                <div class="notif-item-card border d-flex align-items-center justify-content-between">
                    <label for="am_thanh_thong_bao" class="d-flex align-items-center gap-3 min-w-0 cursor-pointer flex-grow-1 mb-0 me-2">
                        <div class="notif-item-icon icon-audio">
                            <i class="bi bi-volume-up-fill"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="fw-bold fs-7 text-dark mb-0.5">Âm thanh thông báo</div>
                            <div class="text-muted fs-8 text-truncate">Phát âm thanh hiệu ứng sinh động khi có thông báo mới phát sinh</div>
                        </div>
                    </label>
                    <div class="form-check form-switch m-0 flex-shrink-0">
                        <input class="form-check-input cursor-pointer sub-notif-switch" type="checkbox" role="switch" id="am_thanh_thong_bao" name="am_thanh_thong_bao" {{ old('am_thanh_thong_bao', $user->am_thanh_thong_bao ?? true) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="submit" class="notif-btn-save rounded-pill px-4 py-2 fw-semibold fs-7 shadow-xs">
                <i class="bi bi-check2-circle me-1.5 fs-6"></i>
                <span>Lưu cấu hình thông báo</span>
            </button>
        </div>
    </form>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const masterSwitch = document.getElementById('thong_bao_enabled');
        const subSwitches = document.querySelectorAll('.sub-notif-switch');
        const subContainer = document.getElementById('subNotificationSettings');
        const notifCounter = document.getElementById('pageNotifCounter');

        function updateCounter() {
            if (!subContainer || !notifCounter) return;
            const switches = subContainer.querySelectorAll('.sub-notif-switch');
            const total = switches.length;
            const active = Array.from(switches).filter(s => s.checked).length;
            if (masterSwitch && !masterSwitch.checked) {
                notifCounter.textContent = 'Đã tạm tắt';
                notifCounter.className = 'badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-0.5 fs-9 fw-semibold notif-active-counter';
            } else {
                notifCounter.textContent = `${active}/${total} đang bật`;
                notifCounter.className = active > 0 
                    ? 'badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-9 fw-semibold notif-active-counter'
                    : 'badge bg-warning-subtle text-warning rounded-pill px-2.5 py-0.5 fs-9 fw-semibold notif-active-counter';
            }
        }

        function toggleSubSettings() {
            const isEnabled = masterSwitch ? masterSwitch.checked : true;
            if (subContainer) {
                if (isEnabled) {
                    subContainer.classList.remove('is-disabled');
                } else {
                    subContainer.classList.add('is-disabled');
                }
            }
            updateCounter();
        }

        masterSwitch?.addEventListener('change', toggleSubSettings);
        subSwitches.forEach(sw => sw.addEventListener('change', updateCounter));
        toggleSubSettings();
    });
</script>
