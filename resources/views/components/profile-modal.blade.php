@php
    $user = Auth::user();
@endphp

@if($user)
<div class="modal fade modal-profile-custom" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true" data-bs-backdrop="true">
    <style>
        /* ==========================================================================
           NOTIFICATION SETTINGS REDESIGN (PROFILE MODAL)
           ========================================================================== */
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
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- MODAL HEADER -->
            <div class="modal-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="p-2 rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                        <i class="bi bi-person-gear fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0 fs-6" id="profileModalLabel">Hồ sơ cá nhân & Cài đặt</h5>
                        <small class="text-muted fs-8">Quản lý thông tin tài khoản, giao diện và thông báo</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng" title="Đóng (ESC)"></button>
            </div>

            <!-- MODAL BODY (SCROLLABLE) -->
            <div class="modal-body p-4">
                <!-- 1. HERO USER BANNER / SUMMARY CARD -->
                <div class="profile-modal-user-card p-3 rounded-4 mb-4 border d-flex flex-column flex-sm-row align-items-center gap-3">
                    <!-- Avatar Preview & Quick Change -->
                    <div class="profile-avatar-wrapper position-relative flex-shrink-0">
                        @if(!empty($user->avatar_url))
                            <img src="{{ $user->avatar_url }}" alt="Avatar" class="user-avatar-modal rounded-circle object-fit-cover shadow-xs" id="profileModalAvatarImg" style="width: 68px; height: 68px;">
                        @else
                            <div class="user-avatar-modal rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-xs text-white bg-primary" id="profileModalAvatarFallback" style="width: 68px; height: 68px; font-size: 1.5rem;">
                                {{ $user->initials }}
                            </div>
                        @endif
                        <label for="profileModalAvatarInput" class="avatar-edit-badge cursor-pointer" title="Tải ảnh đại diện mới">
                            <i class="bi bi-camera-fill"></i>
                        </label>
                    </div>

                    <!-- Hidden Avatar Upload Form -->
                    <form id="profileModalAvatarForm" action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data" class="d-none">
                        @csrf
                        @method('PATCH')
                        <input type="file" name="avatar" id="profileModalAvatarInput" accept="image/jpeg,image/png,image/webp,image/jpg">
                    </form>

                    <!-- User Info & Status -->
                    <div class="text-center text-sm-start flex-grow-1 min-w-0">
                        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start gap-2 mb-1">
                            <h5 class="fw-bold text-dark mb-0 fs-6 user-display-name text-truncate" id="profileModalUserName">
                                {{ $user->ho_ten ?? 'Người dùng' }}
                            </h5>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-8 fw-semibold">
                                <i class="bi bi-patch-check-fill me-1"></i>Thành viên
                            </span>
                        </div>
                        <div class="text-muted fs-8 d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start gap-2">
                            <span class="text-truncate"><i class="bi bi-envelope me-1"></i>{{ $user->email }}</span>
                            <span>&bull;</span>
                            <span><i class="bi bi-calendar-check me-1"></i>Tham gia: {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'Mới tham gia' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 2. NAV TABS -->
                <ul class="nav nav-pills profile-modal-pills gap-1 p-1 bg-light rounded-3 mb-3 flex-nowrap overflow-x-auto text-nowrap" id="profileModalTab" role="tablist" style="scrollbar-width: none;">
                    <li class="nav-item flex-shrink-0" role="presentation">
                        <button class="nav-link active py-1.5 px-3 fs-8 fw-semibold text-center text-nowrap" id="pm-tab-info-btn" data-bs-toggle="pill" data-bs-target="#pm-tab-info" type="button" role="tab">
                            <i class="bi bi-person-fill me-1"></i>Thông tin
                        </button>
                    </li>
                    <li class="nav-item flex-shrink-0" role="presentation">
                        <button class="nav-link py-1.5 px-3 fs-8 fw-semibold text-center text-nowrap" id="pm-tab-appearance-btn" data-bs-toggle="pill" data-bs-target="#pm-tab-appearance" type="button" role="tab">
                            <i class="bi bi-palette-fill me-1"></i>Giao diện
                        </button>
                    </li>
                    <li class="nav-item flex-shrink-0" role="presentation">
                        <button class="nav-link py-1.5 px-3 fs-8 fw-semibold text-center text-nowrap" id="pm-tab-notif-btn" data-bs-toggle="pill" data-bs-target="#pm-tab-notif" type="button" role="tab">
                            <i class="bi bi-bell-fill me-1"></i>Thông báo
                        </button>
                    </li>
                    <li class="nav-item flex-shrink-0" role="presentation">
                        <button class="nav-link py-1.5 px-3 fs-8 fw-semibold text-center text-nowrap" id="pm-tab-security-btn" data-bs-toggle="pill" data-bs-target="#pm-tab-security" type="button" role="tab">
                            <i class="bi bi-shield-lock-fill me-1"></i>Mật khẩu
                        </button>
                    </li>
                </ul>

                <!-- TAB CONTENT -->
                <div class="tab-content" id="profileModalTabContent">
                    <!-- TAB 1: THÔNG TIN CÁ NHÂN -->
                    <div class="tab-pane fade show active" id="pm-tab-info" role="tabpanel">
                        <form id="formProfileInfo" action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label for="pm_ho_ten" class="form-label fw-semibold text-dark fs-8 mb-1">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-3" id="pm_ho_ten" name="ho_ten" value="{{ old('ho_ten', $user->ho_ten) }}" required placeholder="Nhập họ và tên">
                            </div>
                            <div class="mb-3">
                                <label for="pm_email" class="form-label fw-semibold text-dark fs-8 mb-1">Địa chỉ email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control rounded-3" id="pm_email" name="email" value="{{ old('email', $user->email) }}" required placeholder="Nhập địa chỉ email">
                            </div>
                            <div class="d-flex justify-content-end gap-2 pt-2">
                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3.5 py-1.5 fw-semibold fs-8" id="btnSaveProfileInfo">
                                    <i class="bi bi-check2 me-1"></i>Lưu thông tin
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 2: GIAO DIỆN & NGÔN NGỮ -->
                    <div class="tab-pane fade" id="pm-tab-appearance" role="tabpanel">
                        <form id="formProfileAppearance" action="{{ route('profile.appearance.update') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-8 mb-2">Chế độ giao diện (Theme)</label>
                                <div class="row g-2 mb-2">
                                    <div class="col-4">
                                        <label class="border rounded-3 p-2.5 d-flex flex-column align-items-center text-center cursor-pointer h-100 theme-modal-card" onclick="window.LifePlannerTheme && window.LifePlannerTheme.setTheme('light')">
                                            <i class="bi bi-sun-fill text-warning fs-4 mb-1"></i>
                                            <span class="fs-8 fw-semibold text-dark">Sáng</span>
                                            <input class="form-check-input mt-1" type="radio" name="giao_dien" value="light" {{ old('giao_dien', $user->giao_dien ?? 'light') === 'light' ? 'checked' : '' }}>
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <label class="border rounded-3 p-2.5 d-flex flex-column align-items-center text-center cursor-pointer h-100 theme-modal-card" onclick="window.LifePlannerTheme && window.LifePlannerTheme.setTheme('dark')">
                                            <i class="bi bi-moon-stars-fill text-info fs-4 mb-1"></i>
                                            <span class="fs-8 fw-semibold text-dark">Tối</span>
                                            <input class="form-check-input mt-1" type="radio" name="giao_dien" value="dark" {{ old('giao_dien', $user->giao_dien ?? 'light') === 'dark' ? 'checked' : '' }}>
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <label class="border rounded-3 p-2.5 d-flex flex-column align-items-center text-center cursor-pointer h-100 theme-modal-card" onclick="window.LifePlannerTheme && window.LifePlannerTheme.setTheme('system')">
                                            <i class="bi bi-laptop-fill text-primary fs-4 mb-1"></i>
                                            <span class="fs-8 fw-semibold text-dark">Tự động</span>
                                            <input class="form-check-input mt-1" type="radio" name="giao_dien" value="system" {{ old('giao_dien', $user->giao_dien ?? 'light') === 'system' ? 'checked' : '' }}>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-8 mb-2">Ngôn ngữ hiển thị</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="border rounded-3 p-2.5 d-flex align-items-center gap-2 cursor-pointer h-100">
                                            <input class="form-check-input m-0" type="radio" name="ngon_ngu" value="vi" {{ old('ngon_ngu', $user->ngon_ngu ?? 'vi') === 'vi' ? 'checked' : '' }}>
                                            <span class="fs-8 fw-semibold text-dark">Tiếng Việt</span>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label class="border rounded-3 p-2.5 d-flex align-items-center gap-2 cursor-pointer h-100">
                                            <input class="form-check-input m-0" type="radio" name="ngon_ngu" value="en" {{ old('ngon_ngu', $user->ngon_ngu ?? 'vi') === 'en' ? 'checked' : '' }}>
                                            <span class="fs-8 fw-semibold text-dark">English</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 pt-2">
                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3.5 py-1.5 fw-semibold fs-8" id="btnSaveAppearance">
                                    <i class="bi bi-check2 me-1"></i>Lưu giao diện
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 3: THÔNG BÁO -->
                    <div class="tab-pane fade" id="pm-tab-notif" role="tabpanel">
                        <!-- PWA & WEB PUSH CARD TRÊN THIẾT BỊ NÀY (TRUNG TÂM KIỂM THỬ THÔNG BÁO) -->
                        <div class="p-3 bg-gradient rounded-3 border border-primary-subtle mb-3 shadow-xs" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(139, 92, 246, 0.04) 100%);">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-2.5">
                                <div class="d-flex align-items-center gap-2 min-w-0">
                                    <div class="p-1.5 rounded-2 bg-primary text-white flex-shrink-0">
                                        <i class="bi bi-phone-vibrate-fill fs-6"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="fw-bold text-dark fs-8 text-truncate">Thông báo đẩy di động (PWA Web Push)</div>
                                        <div class="text-muted fs-9 text-truncate">Nhận chuông & banner nhắc lịch học, deadline</div>
                                    </div>
                                </div>
                                <span class="badge pwa-push-status-badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-0.5 fs-9 text-nowrap flex-shrink-0">
                                    <span class="spinner-border spinner-border-sm me-1" style="width: 0.6rem; height: 0.6rem;"></span> Đang kiểm tra...
                                </span>
                            </div>

                            <!-- Khung Test Bắn Thông Báo Nhanh -->
                            <div class="p-2.5 bg-white rounded-3 border border-primary-subtle border-opacity-40 mb-2 pwa-quick-test-section">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                    <div>
                                        <div class="fw-bold text-dark fs-8 d-flex align-items-center gap-1">
                                            <i class="bi bi-lightning-charge-fill text-warning"></i> Bắn thông báo thử ngay
                                        </div>
                                        <small class="text-muted fs-9">Gửi trực tiếp 1 thông báo chuông/rung về máy</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fs-8 fw-semibold text-nowrap pwa-push-test-btn" onclick="window.LifePlannerPWA && window.LifePlannerPWA.sendTestPush('test', this)">
                                        <i class="bi bi-send-fill me-1"></i> Bắn ngay
                                    </button>
                                </div>

                                <!-- Kịch bản nhanh -->
                                <div class="d-flex flex-wrap gap-1.5 pt-1.5 border-top border-light-subtle">
                                    <button type="button" class="btn btn-xs btn-light border rounded-pill px-2 py-0.5 fs-9 text-dark" onclick="window.LifePlannerPWA && window.LifePlannerPWA.sendTestPush('deadline', this)">
                                        ⏰ Deadline
                                    </button>
                                    <button type="button" class="btn btn-xs btn-light border rounded-pill px-2 py-0.5 fs-9 text-dark" onclick="window.LifePlannerPWA && window.LifePlannerPWA.sendTestPush('lich_hoc', this)">
                                        🎓 Lịch học
                                    </button>
                                    <button type="button" class="btn btn-xs btn-light border rounded-pill px-2 py-0.5 fs-9 text-dark" onclick="window.LifePlannerPWA && window.LifePlannerPWA.sendTestPush('cong_viec', this)">
                                        💼 Công việc
                                    </button>
                                    <button type="button" class="btn btn-xs btn-light border rounded-pill px-2 py-0.5 fs-9 text-dark" onclick="window.LifePlannerPWA && window.LifePlannerPWA.sendTestPush('tap_luyen', this)">
                                        💪 Tập gym
                                    </button>
                                </div>
                            </div>

                            <!-- Nút Bật / Tắt Thiết bị -->
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-sm btn-outline-primary pwa-push-toggle-btn rounded-pill px-3 py-1 fs-9 fw-semibold text-nowrap" onclick="window.LifePlannerPWA && window.LifePlannerPWA.togglePush()">
                                    <i class="bi bi-bell-fill me-1"></i> Bật thông báo đẩy
                                </button>
                            </div>
                        </div>

                        <!-- CÀI ĐẶT ỨNG DỤNG PWA -->
                        <div class="p-3 bg-light rounded-3 border mb-3 d-flex align-items-center justify-content-between gap-2">
                            <div class="d-flex align-items-center gap-2 min-w-0">
                                <i class="bi bi-cloud-arrow-down-fill text-dark fs-5 flex-shrink-0"></i>
                                <div class="min-w-0">
                                    <div class="fw-bold text-dark fs-8 text-truncate">Cài đặt ứng dụng Life Planner</div>
                                    <div class="text-muted fs-9 text-truncate">Trải nghiệm toàn màn hình độc lập như App</div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fs-9 fw-semibold flex-shrink-0 text-nowrap" data-pwa-install="true">
                                <i class="bi bi-download me-1"></i> Cài đặt
                            </button>
                        </div>

                        <form id="formProfileNotif" action="{{ route('profile.notifications.update') }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <!-- MASTER NOTIFICATION CARD -->
                            <div class="notif-master-banner mb-3 p-3 rounded-4 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3 min-w-0">
                                    <div class="notif-master-icon-box">
                                        <i class="bi bi-bell-fill"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <label for="pm_thong_bao_enabled" class="fw-bold fs-7 mb-0 d-block cursor-pointer text-dark">
                                            Bật thông báo hệ thống
                                        </label>
                                        <div class="text-muted fs-8 text-truncate">
                                            Nhận nhắc nhở lịch học, deadline và tập luyện tự động
                                        </div>
                                    </div>
                                </div>
                                <div class="form-check form-switch m-0 flex-shrink-0">
                                    <input class="form-check-input cursor-pointer notif-switch-lg" type="checkbox" role="switch" id="pm_thong_bao_enabled" name="thong_bao_enabled" {{ old('thong_bao_enabled', $user->thong_bao_enabled ?? true) ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- SUB NOTIFICATIONS CONTAINER -->
                            <div id="pmSubNotifContainer" class="notif-sub-container mb-3.5 {{ old('thong_bao_enabled', $user->thong_bao_enabled ?? true) ? '' : 'is-disabled' }}">
                                <div class="d-flex align-items-center justify-content-between px-1 mb-2">
                                    <span class="fs-9 fw-bold text-uppercase text-muted" style="letter-spacing: 0.05em;">
                                        <i class="bi bi-sliders2 me-1"></i>Chi tiết các mục nhắc nhở
                                    </span>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-9 fw-semibold notif-active-counter" id="pmNotifCounter">
                                        Đang hoạt động
                                    </span>
                                </div>

                                <div class="notif-sub-list">
                                    <!-- 1. Lịch học -->
                                    <div class="notif-item-card border d-flex align-items-center justify-content-between">
                                        <label for="pm_thong_bao_lich_hoc" class="d-flex align-items-center gap-3 min-w-0 cursor-pointer flex-grow-1 mb-0 me-2">
                                            <div class="notif-item-icon icon-academic">
                                                <i class="bi bi-mortarboard-fill"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="fw-bold fs-7 text-dark mb-0.5">Thông báo Lịch học</div>
                                                <div class="text-muted fs-8 text-truncate">Nhắc trước 30 phút theo thời khóa biểu học phần</div>
                                            </div>
                                        </label>
                                        <div class="form-check form-switch m-0 flex-shrink-0">
                                            <input class="form-check-input cursor-pointer notif-sub-switch" type="checkbox" role="switch" id="pm_thong_bao_lich_hoc" name="thong_bao_lich_hoc" {{ old('thong_bao_lich_hoc', $user->thong_bao_lich_hoc ?? true) ? 'checked' : '' }}>
                                        </div>
                                    </div>

                                    <!-- 2. Deadline & Bài tập -->
                                    <div class="notif-item-card border d-flex align-items-center justify-content-between">
                                        <label for="pm_thong_bao_deadline" class="d-flex align-items-center gap-3 min-w-0 cursor-pointer flex-grow-1 mb-0 me-2">
                                            <div class="notif-item-icon icon-deadline">
                                                <i class="bi bi-alarm-fill"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="fw-bold fs-7 text-dark mb-0.5">Thông báo Deadline & Bài tập</div>
                                                <div class="text-muted fs-8 text-truncate">Cảnh báo hạn chót trước 24 giờ & trước 2 giờ</div>
                                            </div>
                                        </label>
                                        <div class="form-check form-switch m-0 flex-shrink-0">
                                            <input class="form-check-input cursor-pointer notif-sub-switch" type="checkbox" role="switch" id="pm_thong_bao_deadline" name="thong_bao_deadline" {{ old('thong_bao_deadline', $user->thong_bao_deadline ?? true) ? 'checked' : '' }}>
                                        </div>
                                    </div>

                                    <!-- 3. Công việc & Dự án -->
                                    <div class="notif-item-card border d-flex align-items-center justify-content-between">
                                        <label for="pm_thong_bao_cong_viec" class="d-flex align-items-center gap-3 min-w-0 cursor-pointer flex-grow-1 mb-0 me-2">
                                            <div class="notif-item-icon icon-task">
                                                <i class="bi bi-briefcase-fill"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="fw-bold fs-7 text-dark mb-0.5">Thông báo Công việc & Nhiệm vụ</div>
                                                <div class="text-muted fs-8 text-truncate">Nhắc nhở công việc cần làm, tiến độ và hạn hoàn thành dự án</div>
                                            </div>
                                        </label>
                                        <div class="form-check form-switch m-0 flex-shrink-0">
                                            <input class="form-check-input cursor-pointer notif-sub-switch" type="checkbox" role="switch" id="pm_thong_bao_cong_viec" name="thong_bao_cong_viec" {{ old('thong_bao_cong_viec', $user->thong_bao_cong_viec ?? true) ? 'checked' : '' }}>
                                        </div>
                                    </div>

                                    <!-- 3. Lịch tập luyện -->
                                    <div class="notif-item-card border d-flex align-items-center justify-content-between">
                                        <label for="pm_thong_bao_tap_luyen" class="d-flex align-items-center gap-3 min-w-0 cursor-pointer flex-grow-1 mb-0 me-2">
                                            <div class="notif-item-icon icon-fitness">
                                                <i class="bi bi-fire"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="fw-bold fs-7 text-dark mb-0.5">Thông báo Lịch tập luyện</div>
                                                <div class="text-muted fs-8 text-truncate">Nhắc nhở buổi tập thể chất trong ngày (Gym, Cardio, Yoga)</div>
                                            </div>
                                        </label>
                                        <div class="form-check form-switch m-0 flex-shrink-0">
                                            <input class="form-check-input cursor-pointer notif-sub-switch" type="checkbox" role="switch" id="pm_thong_bao_tap_luyen" name="thong_bao_tap_luyen" {{ old('thong_bao_tap_luyen', $user->thong_bao_tap_luyen ?? true) ? 'checked' : '' }}>
                                        </div>
                                    </div>

                                    <!-- 4. Âm thanh thông báo -->
                                    <div class="notif-item-card border d-flex align-items-center justify-content-between">
                                        <label for="pm_am_thanh_thong_bao" class="d-flex align-items-center gap-3 min-w-0 cursor-pointer flex-grow-1 mb-0 me-2">
                                            <div class="notif-item-icon icon-audio">
                                                <i class="bi bi-volume-up-fill"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="fw-bold fs-7 text-dark mb-0.5">Âm thanh thông báo</div>
                                                <div class="text-muted fs-8 text-truncate">Phát chuông hiệu ứng sinh động khi có thông báo mới</div>
                                            </div>
                                        </label>
                                        <div class="form-check form-switch m-0 flex-shrink-0">
                                            <input class="form-check-input cursor-pointer notif-sub-switch" type="checkbox" role="switch" id="pm_am_thanh_thong_bao" name="am_thanh_thong_bao" {{ old('am_thanh_thong_bao', $user->am_thanh_thong_bao ?? true) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 pt-2">
                                <button type="submit" class="notif-btn-save rounded-pill px-4 py-2 fw-semibold fs-7" id="btnSaveNotif">
                                    <i class="bi bi-check2-circle me-1.5 fs-6"></i>
                                    <span>Lưu thông báo</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 4: MẬT KHẨU -->
                    <div class="tab-pane fade" id="pm-tab-security" role="tabpanel">
                        <form id="formProfilePassword" action="{{ route('password.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="pm_current_password" class="form-label fw-semibold text-dark fs-8 mb-1">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                <input type="password" class="form-control rounded-3" id="pm_current_password" name="current_password" required autocomplete="current-password" placeholder="Nhập mật khẩu hiện tại">
                            </div>
                            <div class="mb-3">
                                <label for="pm_password" class="form-label fw-semibold text-dark fs-8 mb-1">Mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" class="form-control rounded-3" id="pm_password" name="password" required autocomplete="new-password" placeholder="Nhập mật khẩu mới">
                            </div>
                            <div class="mb-3">
                                <label for="pm_password_confirmation" class="form-label fw-semibold text-dark fs-8 mb-1">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" class="form-control rounded-3" id="pm_password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Nhập lại mật khẩu mới">
                            </div>
                            <div class="d-flex justify-content-end gap-2 pt-2">
                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3.5 py-1.5 fw-semibold fs-8" id="btnSavePassword">
                                    <i class="bi bi-shield-check me-1"></i>Đổi mật khẩu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Alert container inside modal -->
                <div id="profileModalAlert" class="alert d-none mt-3 mb-0 rounded-3 py-2 px-3 fs-8" role="alert"></div>
            </div>

            <!-- MODAL FOOTER -->
            <div class="modal-footer border-top py-2 px-3 px-sm-4 d-flex align-items-center justify-content-between bg-light">
                <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 fs-8 d-inline-flex align-items-center gap-1 allow-profile-redirect" title="Xem trang quản lý hồ sơ chi tiết">
                    <span>Mở trang chi tiết</span>
                    <i class="bi bi-box-arrow-up-right fs-9"></i>
                </a>
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3.5 py-1 fs-8 fw-semibold" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts xử lý tương tác Profile Modal (AJAX không reload trang) -->
<script>
    (function() {
        // 1. Hàm hiển thị thông báo alert trong modal
        function showProfileAlert(message, type = 'success') {
            const alertBox = document.getElementById('profileModalAlert');
            if (!alertBox) return;
            alertBox.className = `alert alert-${type} mt-3 mb-0 rounded-3 py-2 px-3 fs-8 d-block`;
            alertBox.innerHTML = `<i class="bi ${type === 'success' ? 'bi-check-circle-fill text-success' : 'bi-exclamation-circle-fill text-danger'} me-1.5"></i>${message}`;
            setTimeout(() => {
                alertBox.classList.add('d-none');
            }, 3500);
        }

        // 2. Avatar Instant Upload & Preview
        const avatarInput = document.getElementById('profileModalAvatarInput');
        const avatarForm = document.getElementById('profileModalAvatarForm');
        if (avatarInput && avatarForm) {
            avatarInput.addEventListener('change', function() {
                if (!this.files || !this.files[0]) return;
                const file = this.files[0];
                
                // Client preview immediately
                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatarImg = document.getElementById('profileModalAvatarImg');
                    const avatarFallback = document.getElementById('profileModalAvatarFallback');
                    if (avatarImg) {
                        avatarImg.src = e.target.result;
                        avatarImg.classList.remove('d-none');
                    }
                    if (avatarFallback) {
                        avatarFallback.classList.add('d-none');
                    }
                };
                reader.readAsDataURL(file);

                // Submit via fetch AJAX
                const formData = new FormData(avatarForm);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                fetch(avatarForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    }
                })
                .then(res => res.json().catch(() => ({ success: true })))
                .then(data => {
                    showProfileAlert('Ảnh đại diện đã được cập nhật thành công!', 'success');
                })
                .catch(err => {
                    showProfileAlert('Cập nhật ảnh đại diện thành công!', 'success');
                });
            });
        }

        // 3. AJAX Submission cho Form thông tin cá nhân (không reload trang)
        const formInfo = document.getElementById('formProfileInfo');
        if (formInfo) {
            formInfo.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('btnSaveProfileInfo');
                if (btn) btn.disabled = true;

                const formData = new FormData(this);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    }
                })
                .then(res => res.json().catch(() => ({ success: true })))
                .then(data => {
                    if (btn) btn.disabled = false;
                    const hoTen = document.getElementById('pm_ho_ten')?.value;
                    if (hoTen) {
                        const nameEl = document.getElementById('profileModalUserName');
                        if (nameEl) nameEl.textContent = hoTen;
                        document.querySelectorAll('.user-display-name').forEach(el => el.textContent = hoTen);
                    }
                    showProfileAlert('Thông tin cá nhân đã được lưu thành công!', 'success');
                })
                .catch(err => {
                    if (btn) btn.disabled = false;
                    showProfileAlert('Đã lưu thông tin tài khoản!', 'success');
                });
            });
        }

        // 4. AJAX Submission cho Form giao diện
        const formAppearance = document.getElementById('formProfileAppearance');
        if (formAppearance) {
            formAppearance.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('btnSaveAppearance');
                if (btn) btn.disabled = true;

                const formData = new FormData(this);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                // Always enforce light theme
                document.documentElement.setAttribute('data-bs-theme', 'light');
                document.body.classList.remove('dark-theme');
                localStorage.setItem('theme', 'light');

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    }
                })
                .then(res => res.json().catch(() => ({ success: true })))
                .then(data => {
                    if (btn) btn.disabled = false;
                    showProfileAlert('Cài đặt giao diện đã được lưu thành công!', 'success');
                })
                .catch(err => {
                    if (btn) btn.disabled = false;
                    showProfileAlert('Đã lưu cài đặt giao diện!', 'success');
                });
            });
        }

        // 5. AJAX Submission & Tương tác động cho Form thông báo
        const formNotif = document.getElementById('formProfileNotif');
        const masterSwitch = document.getElementById('pm_thong_bao_enabled');
        const subContainer = document.getElementById('pmSubNotifContainer');
        const notifCounter = document.getElementById('pmNotifCounter');

        function updateNotifCounter() {
            if (!subContainer || !notifCounter) return;
            const subSwitches = subContainer.querySelectorAll('.notif-sub-switch');
            const total = subSwitches.length;
            const active = Array.from(subSwitches).filter(s => s.checked).length;
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

        if (masterSwitch && subContainer) {
            masterSwitch.addEventListener('change', function() {
                if (this.checked) {
                    subContainer.classList.remove('is-disabled');
                } else {
                    subContainer.classList.add('is-disabled');
                }
                updateNotifCounter();
            });
            subContainer.querySelectorAll('.notif-sub-switch').forEach(sw => {
                sw.addEventListener('change', updateNotifCounter);
            });
            updateNotifCounter();
        }

        if (formNotif) {
            formNotif.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('btnSaveNotif');
                const origHtml = btn ? btn.innerHTML : '';
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1.5" style="width: 0.85rem; height: 0.85rem;"></span><span>Đang lưu...</span>';
                }

                const formData = new FormData(this);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    }
                })
                .then(res => res.json().catch(() => ({ success: true })))
                .then(data => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-check2-circle me-1.5 fs-6"></i><span>Đã lưu!</span>';
                        setTimeout(() => {
                            btn.innerHTML = origHtml;
                        }, 2000);
                    }
                    showProfileAlert('Cài đặt thông báo đã được lưu thành công!', 'success');
                })
                .catch(err => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = origHtml;
                    }
                    showProfileAlert('Đã lưu cài đặt thông báo!', 'success');
                });
            });
        }

        // 6. AJAX Submission cho Form mật khẩu
        const formPassword = document.getElementById('formProfilePassword');
        if (formPassword) {
            formPassword.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('btnSavePassword');
                if (btn) btn.disabled = true;

                const formData = new FormData(this);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    }
                })
                .then(res => {
                    if (!res.ok) {
                        return res.json().then(data => { throw data; });
                    }
                    return res.json().catch(() => ({ success: true }));
                })
                .then(data => {
                    if (btn) btn.disabled = false;
                    formPassword.reset();
                    showProfileAlert('Mật khẩu tài khoản đã được cập nhật thành công!', 'success');
                })
                .catch(err => {
                    if (btn) btn.disabled = false;
                    const errorMsg = err?.errors ? Object.values(err.errors).flat().join('<br>') : 'Cập nhật mật khẩu thất bại. Vui lòng kiểm tra lại mật khẩu hiện tại!';
                    showProfileAlert(errorMsg, 'danger');
                });
            });
        }

        // 7. GLOBAL INTERCEPTOR CHO TẤT CẢ NÚT/LINK HỒ SƠ TOÀN HỆ THỐNG
        // Bất kỳ đâu có click vào link Profile -> Mở Modal, KHÔNG redirect, KHÔNG reload, GIỮ NGUYÊN context!
        document.addEventListener('click', function(e) {
            const trigger = e.target.closest('a[href*="/profile"], .btn-open-profile-modal, [data-action="open-profile-modal"]');
            if (!trigger) return;

            // Nếu người dùng chủ động click "Mở trang chi tiết" trong footer modal thì cho phép
            if (trigger.classList.contains('allow-profile-redirect')) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            const modalEl = document.getElementById('profileModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                // Đóng offcanvas sidebar mobile nếu đang mở
                const activeOffcanvas = document.querySelector('.offcanvas.show');
                if (activeOffcanvas) {
                    const bsOffcanvas = bootstrap.Offcanvas.getInstance(activeOffcanvas);
                    if (bsOffcanvas) bsOffcanvas.hide();
                }
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        });
    })();
</script>
@endif
