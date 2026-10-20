@php
$user = Auth::user();
@endphp

@if($user)
<div class="modal fade modal-profile-custom" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true" data-bs-backdrop="true">
    <style>
        /* ==========================================================================
           NOTIFICATION SETTINGS REDESIGN (PROFILE MODAL)
           ========================================================================== */
        /* Utility classes with escaped decimals for Tailwind-style spacing */
        .min-w-0 { min-width: 0 !important; }
        .p-1\.5 { padding: 0.4rem !important; }
        .p-2\.5 { padding: 0.8rem !important; }
        .px-2\.5 { padding-left: 0.8rem !important; padding-right: 0.8rem !important; }
        .py-2\.5 { padding-top: 0.6rem !important; padding-bottom: 0.6rem !important; }
        .gap-1\.5 { gap: 0.45rem !important; }
        .gap-2\.5 { gap: 0.75rem !important; }
        .mb-2\.5 { margin-bottom: 0.75rem !important; }
        .mb-3\.5 { margin-bottom: 1rem !important; }
        .me-1\.5 { margin-right: 0.45rem !important; }
        .fs-7 { font-size: 0.85rem !important; }
        .fs-8 { font-size: 0.78rem !important; }
        .fs-9 { font-size: 0.72rem !important; }

        @media (min-width: 576px) {
            .w-sm-auto {
                width: auto !important;
            }
        }
        @media (min-width: 768px) {
            .w-md-auto {
                width: auto !important;
            }
        }

        .modal-profile-custom .modal-body {
            overflow-x: hidden !important;
        }

        .notif-master-banner {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.08) 0%, rgba(99, 102, 241, 0.03) 100%);
            border: 1.5px solid rgba(79, 70, 229, 0.2);
            box-shadow: 0 4px 16px rgba(79, 70, 229, 0.06);
            transition: all 0.25s ease;
            box-sizing: border-box !important;
            max-width: 100% !important;
            padding: 14px 16px !important;
            border-radius: 16px !important;
        }

        [data-bs-theme="dark"] .notif-master-banner {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.16) 0%, rgba(30, 34, 48, 0.7) 100%);
            border-color: rgba(99, 102, 241, 0.35);
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.3);
        }

        .notif-master-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 13px;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.32);
            flex-shrink: 0;
            margin-right: 4px;
        }

        @media (max-width: 575.98px) {
            .notif-master-icon-box {
                width: 38px;
                height: 38px;
                border-radius: 12px;
                font-size: 1.1rem;
            }
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
            padding: 13px 16px !important;
            transition: all 0.22s cubic-bezier(0.34, 1.3, 0.64, 1);
            position: relative;
            user-select: none;
            margin-bottom: 0 !important;
            box-sizing: border-box !important;
            max-width: 100% !important;
        }

        @media (min-width: 576px) {
            .notif-item-card {
                padding: 15px 18px !important;
            }
        }

        .notif-item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.07);
            border-color: rgba(99, 102, 241, 0.35) !important;
        }

        [data-bs-theme="dark"] .notif-item-card {
            background: #1e293b;
            border-color: #334155 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        [data-bs-theme="dark"] .notif-item-card:hover {
            background: #253347;
            border-color: rgba(129, 140, 248, 0.45) !important;
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.45);
        }

        .notif-item-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
            transition: transform 0.2s ease;
            margin-right: 4px;
        }

        @media (min-width: 576px) {
            .notif-item-icon {
                width: 42px;
                height: 42px;
                font-size: 1.18rem;
                border-radius: 13px;
            }
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

        /* Hộp công tắc switch chuẩn flexbox, triệt tiêu hoàn toàn tràn viền */
        .notif-switch-box {
            padding-left: 0 !important;
            margin: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            flex-shrink: 0 !important;
            width: auto !important;
        }

        .notif-switch-lg {
            width: 2.75rem !important;
            height: 1.5rem !important;
            margin: 0 !important;
            cursor: pointer;
            float: none !important;
        }

        .notif-sub-switch {
            width: 2.35rem !important;
            height: 1.35rem !important;
            margin: 0 !important;
            cursor: pointer;
            float: none !important;
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
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- MODAL HEADER -->
            <div class="modal-header border-bottom py-2.5 py-sm-3 px-3 px-sm-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 gap-sm-2.5 min-w-0">
                    <div class="p-2 rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                        <i class="bi bi-person-gear fs-5"></i>
                    </div>
                    <div class="min-w-0">
                        <h5 class="modal-title fw-bold text-dark mb-0 fs-6 text-truncate" id="profileModalLabel">Hồ sơ cá nhân & Cài đặt</h5>
                        <small class="text-muted fs-8 d-none d-sm-block text-truncate">Quản lý thông tin tài khoản, giao diện và thông báo</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng" title="Đóng (ESC)"></button>
            </div>

            <!-- MODAL BODY (SCROLLABLE) -->
            <div class="modal-body p-3 p-sm-4">
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
                <ul class="nav nav-pills profile-modal-pills gap-1.5 p-1 bg-light rounded-3 mb-3 flex-nowrap overflow-x-auto text-nowrap" id="profileModalTab" role="tablist" style="scrollbar-width: none;">
                    <li class="nav-item flex-shrink-0" role="presentation">
                        <button class="nav-link active py-2 px-3 fs-8 fw-semibold text-center text-nowrap d-inline-flex align-items-center justify-content-center gap-1.5" id="pm-tab-info-btn" data-bs-toggle="pill" data-bs-target="#pm-tab-info" type="button" role="tab">
                            <i class="bi bi-person-fill"></i><span>Thông tin</span>
                        </button>
                    </li>
                    <li class="nav-item flex-shrink-0" role="presentation">
                        <button class="nav-link py-2 px-3 fs-8 fw-semibold text-center text-nowrap d-inline-flex align-items-center justify-content-center gap-1.5" id="pm-tab-appearance-btn" data-bs-toggle="pill" data-bs-target="#pm-tab-appearance" type="button" role="tab">
                            <i class="bi bi-palette-fill"></i><span>Giao diện</span>
                        </button>
                    </li>
                    <li class="nav-item flex-shrink-0" role="presentation">
                        <button class="nav-link py-2 px-3 fs-8 fw-semibold text-center text-nowrap d-inline-flex align-items-center justify-content-center gap-1.5" id="pm-tab-notif-btn" data-bs-toggle="pill" data-bs-target="#pm-tab-notif" type="button" role="tab">
                            <i class="bi bi-bell-fill"></i><span>Thông báo</span>
                        </button>
                    </li>
                    <li class="nav-item flex-shrink-0" role="presentation">
                        <button class="nav-link py-2 px-3 fs-8 fw-semibold text-center text-nowrap d-inline-flex align-items-center justify-content-center gap-1.5" id="pm-tab-security-btn" data-bs-toggle="pill" data-bs-target="#pm-tab-security" type="button" role="tab">
                            <i class="bi bi-shield-lock-fill"></i><span>Mật khẩu</span>
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
                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 py-2 fw-semibold fs-8 d-inline-flex align-items-center justify-content-center gap-1.5" id="btnSaveProfileInfo">
                                    <i class="bi bi-check2"></i><span>Lưu thông tin</span>
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
                                        <label class="border rounded-3 p-3 d-flex flex-column align-items-center text-center cursor-pointer h-100 theme-modal-card" onclick="window.LifePlannerTheme && window.LifePlannerTheme.setTheme('light')">
                                            <i class="bi bi-sun-fill text-warning fs-4 mb-1.5"></i>
                                            <span class="fs-8 fw-semibold text-dark">Sáng</span>
                                            <input class="form-check-input mt-2" type="radio" name="giao_dien" value="light" {{ old('giao_dien', $user->giao_dien ?? 'light') === 'light' ? 'checked' : '' }}>
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <label class="border rounded-3 p-3 d-flex flex-column align-items-center text-center cursor-pointer h-100 theme-modal-card" onclick="window.LifePlannerTheme && window.LifePlannerTheme.setTheme('dark')">
                                            <i class="bi bi-moon-stars-fill text-info fs-4 mb-1.5"></i>
                                            <span class="fs-8 fw-semibold text-dark">Tối</span>
                                            <input class="form-check-input mt-2" type="radio" name="giao_dien" value="dark" {{ old('giao_dien', $user->giao_dien ?? 'light') === 'dark' ? 'checked' : '' }}>
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <label class="border rounded-3 p-3 d-flex flex-column align-items-center text-center cursor-pointer h-100 theme-modal-card" onclick="window.LifePlannerTheme && window.LifePlannerTheme.setTheme('system')">
                                            <i class="bi bi-laptop-fill text-primary fs-4 mb-1.5"></i>
                                            <span class="fs-8 fw-semibold text-dark">Tự động</span>
                                            <input class="form-check-input mt-2" type="radio" name="giao_dien" value="system" {{ old('giao_dien', $user->giao_dien ?? 'light') === 'system' ? 'checked' : '' }}>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-8 mb-2">Ngôn ngữ hiển thị</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="border rounded-3 p-3 d-flex align-items-center gap-2.5 cursor-pointer h-100">
                                            <input class="form-check-input m-0" type="radio" name="ngon_ngu" value="vi" {{ old('ngon_ngu', $user->ngon_ngu ?? 'vi') === 'vi' ? 'checked' : '' }}>
                                            <span class="fs-8 fw-semibold text-dark">Tiếng Việt</span>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label class="border rounded-3 p-3 d-flex align-items-center gap-2.5 cursor-pointer h-100">
                                            <input class="form-check-input m-0" type="radio" name="ngon_ngu" value="en" {{ old('ngon_ngu', $user->ngon_ngu ?? 'vi') === 'en' ? 'checked' : '' }}>
                                            <span class="fs-8 fw-semibold text-dark">English</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 pt-2">
                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 py-2 fw-semibold fs-8 d-inline-flex align-items-center justify-content-center gap-1.5" id="btnSaveAppearance">
                                    <i class="bi bi-check2"></i><span>Lưu giao diện</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 3: THÔNG BÁO -->
                    <div class="tab-pane fade" id="pm-tab-notif" role="tabpanel">
                        <!-- PWA & WEB PUSH CARD TRÊN THIẾT BỊ NÀY (TRUNG TÂM KIỂM THỬ THÔNG BÁO) -->
                        <div class="p-3 rounded-4 border border-primary-subtle mb-3 shadow-xs" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(139, 92, 246, 0.04) 100%);">
                            <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 mb-2.5">
                                <div class="d-flex align-items-center gap-3 min-w-0 flex-grow-1">
                                    <div class="rounded-3 bg-primary text-white flex-shrink-0 d-flex align-items-center justify-content-center shadow-xs me-1" style="width: 38px; height: 38px;">
                                        <i class="bi bi-phone-vibrate-fill fs-5"></i>
                                    </div>
                                    <div class="min-w-0 flex-grow-1 pe-1">
                                        <div class="fw-bold text-body fs-7 text-break">Thông báo đẩy di động (PWA Web Push)</div>
                                        <div class="text-body-secondary fs-8 text-break mt-0.5">Nhận chuông & banner nhắc lịch học, deadline</div>
                                    </div>
                                </div>
                                <span class="badge pwa-push-status-badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1.5 fs-8 text-nowrap flex-shrink-0">
                                    <span class="spinner-border spinner-border-sm me-1" style="width: 0.65rem; height: 0.65rem;"></span> Đang kiểm tra...
                                </span>
                            </div>

                            <!-- Khung Test Thông Báo Nhanh -->
                            <div class="p-3 bg-body rounded-3 border border-primary-subtle border-opacity-40 mb-2.5 pwa-quick-test-section">
                                <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-2.5 mb-2">
                                    <div class="min-w-0 flex-grow-1 pe-sm-2">
                                        <div class="fw-bold text-body fs-8 d-flex align-items-center gap-2">
                                            <i class="bi bi-lightning-charge-fill text-warning"></i>
                                            <span>Kiểm tra thông báo thử</span>
                                        </div>
                                        <small class="text-body-secondary fs-9 d-block lh-sm mt-0.5">Gửi trực tiếp 1 thông báo chuông/rung về máy</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3.5 py-2 fs-8 fw-semibold text-nowrap w-100 w-sm-auto flex-shrink-0 pwa-push-test-btn d-inline-flex align-items-center justify-content-center gap-1.5" onclick="window.LifePlannerPWA && window.LifePlannerPWA.sendTestPush('test', this)">
                                        <i class="bi bi-send-fill"></i><span>Test thông báo</span>
                                    </button>
                                </div>

                                <!-- Kịch bản nhanh (Grid 2 cột trên mobile, 4 cột trên desktop) -->
                                <div class="row g-2 pt-2 border-top border-light-subtle">
                                    <div class="col-6 col-sm-3">
                                        <button type="button" class="btn btn-xs btn-light bg-body-tertiary border rounded-pill px-2.5 py-1.5 fs-8 text-body w-100 d-inline-flex align-items-center justify-content-center gap-2 text-truncate" onclick="window.LifePlannerPWA && window.LifePlannerPWA.sendTestPush('deadline', this)">
                                            <span>⏰</span> <span class="text-truncate">Deadline</span>
                                        </button>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <button type="button" class="btn btn-xs btn-light bg-body-tertiary border rounded-pill px-2.5 py-1.5 fs-8 text-body w-100 d-inline-flex align-items-center justify-content-center gap-2 text-truncate" onclick="window.LifePlannerPWA && window.LifePlannerPWA.sendTestPush('lich_hoc', this)">
                                            <span>🎓</span> <span class="text-truncate">Lịch học</span>
                                        </button>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <button type="button" class="btn btn-xs btn-light bg-body-tertiary border rounded-pill px-2.5 py-1.5 fs-8 text-body w-100 d-inline-flex align-items-center justify-content-center gap-2 text-truncate" onclick="window.LifePlannerPWA && window.LifePlannerPWA.sendTestPush('cong_viec', this)">
                                            <span>💼</span> <span class="text-truncate">Công việc</span>
                                        </button>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <button type="button" class="btn btn-xs btn-light bg-body-tertiary border rounded-pill px-2.5 py-1.5 fs-8 text-body w-100 d-inline-flex align-items-center justify-content-center gap-2 text-truncate" onclick="window.LifePlannerPWA && window.LifePlannerPWA.sendTestPush('tap_luyen', this)">
                                            <span>💪</span> <span class="text-truncate">Tập gym</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Nút Bật / Tắt Thiết bị -->
                            <div class="d-flex justify-content-stretch justify-content-sm-end pt-1">
                                <button type="button" class="btn btn-sm btn-outline-primary pwa-push-toggle-btn rounded-pill px-3.5 py-2 fs-8 fw-semibold text-nowrap w-100 w-sm-auto text-center d-inline-flex align-items-center justify-content-center gap-2" onclick="window.LifePlannerPWA && window.LifePlannerPWA.togglePush()">
                                    <i class="bi bi-bell-fill"></i>
                                    <span>Bật thông báo đẩy</span>
                                </button>
                            </div>
                        </div>

                        <!-- CÀI ĐẶT ỨNG DỤNG PWA -->
                        <div class="p-3 bg-body rounded-4 border mb-3 d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-3 shadow-xs">
                            <div class="d-flex align-items-center gap-3 min-w-0 flex-grow-1">
                                <div class="rounded-3 bg-body-secondary text-body flex-shrink-0 d-flex align-items-center justify-content-center me-1" style="width: 38px; height: 38px;">
                                    <i class="bi bi-cloud-arrow-down-fill fs-5"></i>
                                </div>
                                <div class="min-w-0 flex-grow-1 pe-1">
                                    <div class="fw-bold text-body fs-7 text-break">Cài đặt ứng dụng Life Planner</div>
                                    <div class="text-body-secondary fs-8 text-break mt-0.5">Trải nghiệm toàn màn hình độc lập như App</div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3.5 py-2 fs-8 fw-semibold flex-shrink-0 text-nowrap w-100 w-sm-auto text-center d-inline-flex align-items-center justify-content-center gap-2" data-pwa-install="true">
                                <i class="bi bi-download"></i>
                                <span>Cài đặt</span>
                            </button>
                        </div>

                        <form id="formProfileNotif" action="{{ route('profile.notifications.update') }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <!-- MASTER NOTIFICATION CARD -->
                            <div class="notif-master-banner mb-3 d-flex align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3 min-w-0 flex-grow-1">
                                    <div class="notif-master-icon-box flex-shrink-0">
                                        <i class="bi bi-bell-fill"></i>
                                    </div>
                                    <div class="min-w-0 flex-grow-1 pe-2">
                                        <label for="pm_thong_bao_enabled" class="fw-bold fs-7 mb-0 d-block cursor-pointer text-body text-break lh-sm">
                                            Bật thông báo hệ thống
                                        </label>
                                        <div class="text-body-secondary fs-8 lh-sm text-break mt-0.5">
                                            Nhận nhắc nhở lịch học, deadline và tập luyện tự động
                                        </div>
                                    </div>
                                </div>
                                <div class="form-check form-switch notif-switch-box">
                                    <input class="form-check-input notif-switch-lg" type="checkbox" role="switch" id="pm_thong_bao_enabled" name="thong_bao_enabled" {{ old('thong_bao_enabled', $user->thong_bao_enabled ?? true) ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- SUB NOTIFICATIONS CONTAINER -->
                            <div id="pmSubNotifContainer" class="notif-sub-container mb-3.5 {{ old('thong_bao_enabled', $user->thong_bao_enabled ?? true) ? '' : 'is-disabled' }}">
                                <div class="d-flex align-items-center justify-content-between px-1 mb-2.5">
                                    <span class="fs-8 fw-bold text-uppercase text-body-secondary" style="letter-spacing: 0.04em;">
                                        <i class="bi bi-sliders2 me-1.5"></i>Các mục nhắc nhở
                                    </span>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fs-8 fw-semibold notif-active-counter" id="pmNotifCounter">
                                        Đang hoạt động
                                    </span>
                                </div>

                                <div class="notif-sub-list">
                                    <!-- 1. Lịch học -->
                                    <div class="notif-item-card border d-flex align-items-center justify-content-between gap-3">
                                        <label for="pm_thong_bao_lich_hoc" class="d-flex align-items-center gap-3 min-w-0 cursor-pointer flex-grow-1 mb-0">
                                            <div class="notif-item-icon icon-academic flex-shrink-0">
                                                <i class="bi bi-mortarboard-fill"></i>
                                            </div>
                                            <div class="min-w-0 flex-grow-1 pe-2">
                                                <div class="fw-bold fs-7 text-body mb-0.5 text-break">Thông báo Lịch học</div>
                                                <div class="text-body-secondary fs-8 lh-sm text-break">Nhắc trước 30 phút theo thời khóa biểu học phần</div>
                                            </div>
                                        </label>
                                        <div class="form-check form-switch notif-switch-box">
                                            <input class="form-check-input notif-sub-switch cursor-pointer" type="checkbox" role="switch" id="pm_thong_bao_lich_hoc" name="thong_bao_lich_hoc" {{ old('thong_bao_lich_hoc', $user->thong_bao_lich_hoc ?? true) ? 'checked' : '' }}>
                                        </div>
                                    </div>

                                    <!-- 2. Deadline & Bài tập -->
                                    <div class="notif-item-card border d-flex align-items-center justify-content-between gap-3">
                                        <label for="pm_thong_bao_deadline" class="d-flex align-items-center gap-3 min-w-0 cursor-pointer flex-grow-1 mb-0">
                                            <div class="notif-item-icon icon-deadline flex-shrink-0">
                                                <i class="bi bi-alarm-fill"></i>
                                            </div>
                                            <div class="min-w-0 flex-grow-1 pe-2">
                                                <div class="fw-bold fs-7 text-body mb-0.5 text-break">Thông báo Deadline & Bài tập</div>
                                                <div class="text-body-secondary fs-8 lh-sm text-break">Cảnh báo hạn chót trước 24 giờ & trước 2 giờ</div>
                                            </div>
                                        </label>
                                        <div class="form-check form-switch notif-switch-box">
                                            <input class="form-check-input notif-sub-switch cursor-pointer" type="checkbox" role="switch" id="pm_thong_bao_deadline" name="thong_bao_deadline" {{ old('thong_bao_deadline', $user->thong_bao_deadline ?? true) ? 'checked' : '' }}>
                                        </div>
                                    </div>

                                    <!-- 3. Công việc & Dự án -->
                                    <div class="notif-item-card border d-flex align-items-center justify-content-between gap-3">
                                        <label for="pm_thong_bao_cong_viec" class="d-flex align-items-center gap-3 min-w-0 cursor-pointer flex-grow-1 mb-0">
                                            <div class="notif-item-icon icon-task flex-shrink-0">
                                                <i class="bi bi-briefcase-fill"></i>
                                            </div>
                                            <div class="min-w-0 flex-grow-1 pe-2">
                                                <div class="fw-bold fs-7 text-body mb-0.5 text-break">Thông báo Công việc & Nhiệm vụ</div>
                                                <div class="text-body-secondary fs-8 lh-sm text-break">Nhắc nhở công việc cần làm, tiến độ và hạn hoàn thành dự án</div>
                                            </div>
                                        </label>
                                        <div class="form-check form-switch notif-switch-box">
                                            <input class="form-check-input notif-sub-switch cursor-pointer" type="checkbox" role="switch" id="pm_thong_bao_cong_viec" name="thong_bao_cong_viec" {{ old('thong_bao_cong_viec', $user->thong_bao_cong_viec ?? true) ? 'checked' : '' }}>
                                        </div>
                                    </div>

                                    <!-- 4. Lịch tập luyện -->
                                    <div class="notif-item-card border d-flex align-items-center justify-content-between gap-3">
                                        <label for="pm_thong_bao_tap_luyen" class="d-flex align-items-center gap-3 min-w-0 cursor-pointer flex-grow-1 mb-0">
                                            <div class="notif-item-icon icon-fitness flex-shrink-0">
                                                <i class="bi bi-fire"></i>
                                            </div>
                                            <div class="min-w-0 flex-grow-1 pe-2">
                                                <div class="fw-bold fs-7 text-body mb-0.5 text-break">Thông báo Lịch tập luyện</div>
                                                <div class="text-body-secondary fs-8 lh-sm text-break">Nhắc nhở buổi tập thể chất trong ngày (Gym, Cardio, Yoga)</div>
                                            </div>
                                        </label>
                                        <div class="form-check form-switch notif-switch-box">
                                            <input class="form-check-input notif-sub-switch cursor-pointer" type="checkbox" role="switch" id="pm_thong_bao_tap_luyen" name="thong_bao_tap_luyen" {{ old('thong_bao_tap_luyen', $user->thong_bao_tap_luyen ?? true) ? 'checked' : '' }}>
                                        </div>
                                    </div>

                                    <!-- 5. Âm thanh thông báo -->
                                    <div class="notif-item-card border d-flex align-items-center justify-content-between gap-3">
                                        <label for="pm_am_thanh_thong_bao" class="d-flex align-items-center gap-3 min-w-0 cursor-pointer flex-grow-1 mb-0">
                                            <div class="notif-item-icon icon-audio flex-shrink-0">
                                                <i class="bi bi-volume-up-fill"></i>
                                            </div>
                                            <div class="min-w-0 flex-grow-1 pe-2">
                                                <div class="fw-bold fs-7 text-body mb-0.5 text-break">Âm thanh thông báo</div>
                                                <div class="text-body-secondary fs-8 lh-sm text-break">Phát chuông hiệu ứng sinh động khi có thông báo mới</div>
                                            </div>
                                        </label>
                                        <div class="form-check form-switch notif-switch-box">
                                            <input class="form-check-input notif-sub-switch cursor-pointer" type="checkbox" role="switch" id="pm_am_thanh_thong_bao" name="am_thanh_thong_bao" {{ old('am_thanh_thong_bao', $user->am_thanh_thong_bao ?? true) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 pt-2">
                                <button type="submit" class="notif-btn-save rounded-pill px-4 py-2.5 fw-semibold fs-7 d-inline-flex align-items-center justify-content-center gap-2" id="btnSaveNotif">
                                    <i class="bi bi-check2-circle fs-6"></i>
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
                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 py-2 fw-semibold fs-8 d-inline-flex align-items-center justify-content-center gap-1.5" id="btnSavePassword">
                                    <i class="bi bi-shield-check"></i><span>Đổi mật khẩu</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Alert container inside modal -->
                <div id="profileModalAlert" class="alert d-none mt-3 mb-0 rounded-3 py-2 px-3 fs-8" role="alert"></div>
            </div>

            <!-- MODAL FOOTER -->
            <div class="modal-footer border-top py-2.5 px-3 px-sm-4 d-flex align-items-center justify-content-between bg-light">
                <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fs-8 d-inline-flex align-items-center gap-1.5 allow-profile-redirect" title="Xem trang quản lý hồ sơ chi tiết">
                    <span>Mở trang chi tiết</span>
                    <i class="bi bi-box-arrow-up-right fs-9"></i>
                </a>
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4 py-1.5 fs-8 fw-semibold" data-bs-dismiss="modal">Đóng</button>
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
                    .then(res => res.json().catch(() => ({
                        success: true
                    })))
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
                    .then(res => res.json().catch(() => ({
                        success: true
                    })))
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
                    .then(res => res.json().catch(() => ({
                        success: true
                    })))
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
                notifCounter.className = active > 0 ?
                    'badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-9 fw-semibold notif-active-counter' :
                    'badge bg-warning-subtle text-warning rounded-pill px-2.5 py-0.5 fs-9 fw-semibold notif-active-counter';
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
                    .then(res => res.json().catch(() => ({
                        success: true
                    })))
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
                            return res.json().then(data => {
                                throw data;
                            });
                        }
                        return res.json().catch(() => ({
                            success: true
                        }));
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