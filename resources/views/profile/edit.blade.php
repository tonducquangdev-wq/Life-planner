<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hồ Sơ Cá Nhân & Cài Đặt - Life Planner</title>

    <!-- Kịch bản Khởi tạo Giao diện Sáng/Tối chống giật trang (Anti-Flicker) -->
    <script>
        (function() {
            var userTheme = "{{ Auth::check() ? (Auth::user()->giao_dien ?? 'system') : 'system' }}";
            var savedTheme = localStorage.getItem('theme');
            var themeToApply = 'light';
            if (savedTheme && (savedTheme === 'dark' || savedTheme === 'light')) {
                themeToApply = savedTheme;
            } else if (userTheme && (userTheme === 'dark' || userTheme === 'light')) {
                themeToApply = userTheme;
            } else {
                themeToApply = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-bs-theme', themeToApply);
            if (themeToApply === 'dark') {
                document.documentElement.classList.add('dark-theme');
            } else {
                document.documentElement.classList.remove('dark-theme');
            }
        })();
    </script>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Font: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS Tùy chỉnh Dashboard -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ file_exists(public_path('css/dashboard.css')) ? filemtime(public_path('css/dashboard.css')) : '1.0' }}">

    <!-- CẤU HÌNH PWA & WEB PUSH (MANIFEST, ICONS, SERVICE WORKER) -->
    @include('layouts.pwa-head')

    <style>
        .profile-cover-banner {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #3b82f6 100%);
            height: 140px;
            border-radius: 1rem 1rem 0 0;
            position: relative;
        }
        .profile-avatar-wrapper {
            position: relative;
            margin-top: -60px;
            display: inline-block;
        }
        .profile-avatar-xl {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            border: 4px solid #ffffff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
            background-color: #6366f1;
            color: #ffffff;
            font-size: 2.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            object-fit: cover;
        }
        [data-bs-theme="dark"] .profile-avatar-xl {
            border-color: #1e1e24;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
        }
        .avatar-edit-badge {
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background-color: #ffffff;
            color: #4f46e5;
            border: 2px solid #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        [data-bs-theme="dark"] .avatar-edit-badge {
            background-color: #272732;
            color: #818cf8;
            border-color: #1e1e24;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        }
        .avatar-edit-badge:hover {
            background-color: #4f46e5;
            color: #ffffff;
            transform: scale(1.1);
        }
        .profile-nav-pills .nav-link {
            border-radius: 0.75rem;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            color: #64748b;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .profile-nav-pills .nav-link:hover {
            background-color: #f1f5f9;
            color: #1e293b;
        }
        .profile-nav-pills .nav-link.active {
            background-color: #6366f1;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
        }
        .avatar-preview-box {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto;
            overflow: hidden;
            border: 3px solid #6366f1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
        }
        [data-bs-theme="dark"] .avatar-preview-box {
            background-color: #1a1a22;
        }
        .avatar-preview-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Tối ưu hiển thị Responsive trên thiết bị Mobile */
        @media (max-width: 767.98px) {
            .profile-cover-banner {
                height: 95px;
                border-radius: 0.85rem 0.85rem 0 0;
            }
            .profile-avatar-wrapper {
                margin-top: -44px;
            }
            .profile-avatar-xl {
                width: 84px;
                height: 84px;
                font-size: 1.85rem;
                border-width: 3px;
            }
            .avatar-edit-badge {
                width: 30px;
                height: 30px;
                bottom: 2px;
                right: 2px;
                font-size: 0.85rem;
            }
        }

        /* Menu Tabs cuộn ngang mượt mà (Sticky Horizontal Scrollable Chips) trên Mobile & Tablet */
        @media (max-width: 991.98px) {
            .profile-tabs-card {
                position: sticky !important;
                top: 54px !important;
                z-index: 1015 !important;
                background: rgba(var(--bs-body-bg-rgb, 248, 250, 252), 0.94) !important;
                backdrop-filter: blur(12px) !important;
                -webkit-backdrop-filter: blur(12px) !important;
                padding: 0.45rem 0 !important;
                margin-bottom: 0.75rem !important;
                border: none !important;
                border-radius: 0 !important;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04) !important;
            }
            [data-bs-theme="dark"] .profile-tabs-card {
                background: rgba(22, 22, 28, 0.92) !important;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.35) !important;
            }
            .profile-nav-pills {
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
                gap: 0.5rem !important;
                padding: 0.25rem 0.5rem !important;
            }
            .profile-nav-pills::-webkit-scrollbar {
                display: none;
            }
            .profile-nav-pills .nav-link {
                white-space: nowrap !important;
                flex-shrink: 0 !important;
                padding: 0.45rem 0.9rem !important;
                font-size: 0.85rem !important;
                border-radius: 9999px !important;
                background-color: #ffffff;
                border: 1px solid rgba(226, 232, 240, 0.9);
                box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
            }
            [data-bs-theme="dark"] .profile-nav-pills .nav-link {
                background-color: #1e293b;
                border-color: #334155;
                color: #cbd5e1;
            }
            .profile-nav-pills .nav-link.active {
                background-color: #6366f1 !important;
                border-color: #6366f1 !important;
                color: #ffffff !important;
                box-shadow: 0 4px 12px rgba(99, 102, 241, 0.32);
            }
            .profile-nav-pills .nav-link i {
                font-size: 0.95rem !important;
            }
            .content-body {
                padding-bottom: calc(90px + env(safe-area-inset-bottom, 20px)) !important; /* Khoảng cách an toàn tránh bị Bottom Nav che */
            }
        }

        @media (min-width: 576px) {
            .w-sm-auto {
                width: auto !important;
            }
        }
    </style>
</head>

<body>

    <!-- 1. SIDEBAR BÊN TRÁI -->
    @include('layouts.sidebar')

    <!-- 2. MAIN WRAPPER -->
    <div class="main-wrapper">

        <!-- HEADER THANH CÔNG CỤ TRÊN CÙNG -->
        <header class="top-header border-bottom bg-white px-3 px-lg-4 py-2 d-flex align-items-center justify-content-between sticky-top" style="z-index: 1020;">
            <div class="d-flex align-items-center gap-2 gap-sm-3 min-w-0">
                <!-- Nút mở Menu Offcanvas trên màn hình Mobile & Tablet -->
                <button class="btn btn-sm btn-light border-0 d-lg-none p-1.5 rounded-3 me-1 d-inline-flex align-items-center justify-content-center text-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas" title="Mở danh mục menu">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <a href="{{ route('calendar.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1.5 flex-shrink-0" title="Quay lại Lịch">
                    <i class="bi bi-arrow-left"></i>
                    <span class="d-none d-sm-inline">Lịch</span>
                </a>
                <h5 class="fw-bold text-dark mb-0 fs-6 fs-md-5 d-flex align-items-center gap-2 text-truncate">
                    <i class="bi bi-person-bounding-box text-primary flex-shrink-0"></i>
                    <span class="text-truncate">Hồ Sơ Cá Nhân & Cài Đặt</span>
                </h5>
            </div>

            <!-- Khu vực người dùng & Nút Dropdown -->
            <div class="header-actions d-flex align-items-center gap-2 gap-sm-3 flex-shrink-0">
                <!-- User Profile Dropdown -->
                <div class="user-profile dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar">
                            @if(!empty($user->avatar_url))
                                <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-100 h-100 rounded-circle object-fit-cover">
                            @else
                                {{ $user->initials ?? 'U' }}
                            @endif
                        </div>
                        <div class="d-none d-md-block text-start">
                            <div class="fw-bold text-dark fs-6 leading-tight">{{ $user->ho_ten ?? 'User' }}</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Hồ sơ</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- NỘI DUNG CHÍNH (CONTENT BODY) -->
        <main class="content-body p-2 p-sm-3 p-lg-4">

            <!-- THÔNG BÁO THÀNH CÔNG NẾU CÓ -->
            @if (session('status') === 'avatar-updated')
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-xs mb-3 mb-md-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>Ảnh đại diện đã được cập nhật thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('status') === 'profile-updated')
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-xs mb-3 mb-md-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>Thông tin cá nhân đã được cập nhật thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('status') === 'password-updated')
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-xs mb-3 mb-md-4" role="alert">
                    <i class="bi bi-shield-check me-2"></i>Mật khẩu tài khoản đã được cập nhật thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('status') === 'notifications-updated')
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-xs mb-3 mb-md-4" role="alert">
                    <i class="bi bi-bell-check-fill me-2"></i>Cấu hình thông báo đã được cập nhật thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('status') === 'appearance-updated')
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-xs mb-3 mb-md-4" role="alert">
                    <i class="bi bi-palette-fill me-2"></i>Cấu hình giao diện và ngôn ngữ đã được cập nhật thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- 1. HERO COVER & PROFILE SUMMARY CARD HEADER -->
            <div class="card border shadow-sm rounded-4 bg-body mb-3 mb-md-4 overflow-hidden">
                <div class="profile-cover-banner"></div>
                <div class="px-3 px-sm-4 pb-3 pb-sm-4 position-relative">
                    <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-end justify-content-between gap-3 text-center text-sm-start">
                        <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-end gap-2.5 gap-sm-3 min-w-0">
                            <div class="profile-avatar-wrapper flex-shrink-0">
                                @if(!empty($user->avatar_url))
                                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="profile-avatar-xl">
                                @else
                                    <div class="profile-avatar-xl">
                                        {{ $user->initials }}
                                    </div>
                                @endif
                                <button type="button" class="avatar-edit-badge" data-bs-toggle="modal" data-bs-target="#changeAvatarModal" title="Đổi ảnh đại diện">
                                    <i class="bi bi-camera-fill"></i>
                                </button>
                            </div>
                            <div class="pt-1 pt-sm-0 mb-sm-1 min-w-0">
                                <h4 class="fw-bold text-body mb-1 fs-5 fs-md-4 text-truncate">{{ $user->ho_ten ?? 'Người dùng' }}</h4>
                                <div class="text-body-secondary small mb-0 d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start gap-1.5 gap-sm-2">
                                    <span class="text-truncate fs-8 fs-sm-7"><i class="bi bi-envelope me-1"></i>{{ $user->email }}</span>
                                    <span class="d-none d-sm-inline">•</span>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-9 fs-sm-8 fw-semibold">
                                        <i class="bi bi-patch-check-fill me-1"></i>Student Life Planner
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="w-100 w-sm-auto d-flex justify-content-center justify-content-sm-end mb-sm-1">
                            <button type="button" class="btn btn-outline-primary rounded-pill px-3.5 py-2 btn-sm fw-semibold w-100 w-sm-auto d-inline-flex align-items-center justify-content-center gap-2 shadow-2xs" data-bs-toggle="modal" data-bs-target="#changeAvatarModal">
                                <i class="bi bi-camera-fill"></i>
                                <span>Đổi ảnh đại diện</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. BỐ CỤC CHÍNH (SIDE NAV TAB & CONTENT CARDS) -->
            <div class="row g-3 g-lg-4">
                
                <!-- CỘT TRÁI / TOP (MOBILE): MENU TAB ĐIỀU HƯỚNG PROFILE (Col-lg-3) -->
                <div class="col-12 col-lg-3">
                    <div class="card border shadow-sm rounded-4 bg-body p-2 p-sm-3 profile-tabs-card">
                        <div class="nav flex-column nav-pills profile-nav-pills gap-1" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <button class="nav-link active d-inline-flex align-items-center gap-2" id="tab-info-btn" data-bs-toggle="pill" data-bs-target="#tab-info" type="button" role="tab" aria-controls="tab-info" aria-selected="true">
                                <i class="bi bi-person-lines-fill"></i>
                                <span>Thông tin cá nhân</span>
                            </button>
                            <button class="nav-link d-inline-flex align-items-center gap-2" id="tab-notif-btn" data-bs-toggle="pill" data-bs-target="#tab-notif" type="button" role="tab" aria-controls="tab-notif" aria-selected="false">
                                <i class="bi bi-bell-fill text-warning"></i>
                                <span>Cài đặt thông báo</span>
                            </button>
                            <button class="nav-link d-inline-flex align-items-center gap-2" id="tab-appearance-btn" data-bs-toggle="pill" data-bs-target="#tab-appearance" type="button" role="tab" aria-controls="tab-appearance" aria-selected="false">
                                <i class="bi bi-palette-fill text-primary"></i>
                                <span>Giao diện & Ngôn ngữ</span>
                            </button>
                            <button class="nav-link d-inline-flex align-items-center gap-2" id="tab-password-btn" data-bs-toggle="pill" data-bs-target="#tab-password" type="button" role="tab" aria-controls="tab-password" aria-selected="false">
                                <i class="bi bi-shield-lock-fill text-info"></i>
                                <span>Mật khẩu & Bảo mật</span>
                            </button>
                            <button class="nav-link text-danger d-inline-flex align-items-center gap-2" id="tab-danger-btn" data-bs-toggle="pill" data-bs-target="#tab-danger" type="button" role="tab" aria-controls="tab-danger" aria-selected="false">
                                <i class="bi bi-trash3-fill text-danger"></i>
                                <span>Xóa tài khoản</span>
                            </button>
                        </div>

                        <div class="d-none d-lg-block">
                            <hr class="my-3 text-muted opacity-25">

                            <div class="px-2 text-muted fs-8">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Ngày tham gia:</span>
                                    <strong class="text-dark">{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'Mới tham gia' }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Trạng thái:</span>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2">Hoạt động</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CỘT PHẢI: NỘI DUNG TỪNG TAB (Col-lg-9) -->
                <div class="col-12 col-lg-9">
                    <div class="tab-content" id="v-pills-tabContent">
                        
                        <!-- TAB 1: THÔNG TIN CÁ NHÂN -->
                        <div class="tab-pane fade show active" id="tab-info" role="tabpanel" aria-labelledby="tab-info-btn">
                            <div class="card border shadow-sm rounded-4 bg-body p-3 p-sm-4">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                        </div>

                        <!-- TAB 2: CÀI ĐẶT THÔNG BÁO -->
                        <div class="tab-pane fade" id="tab-notif" role="tabpanel" aria-labelledby="tab-notif-btn">
                            <div class="card border shadow-sm rounded-4 bg-body p-3 p-sm-4">
                                @include('profile.partials.update-notification-settings-form')
                            </div>
                        </div>

                        <!-- TAB 3: GIAO DIỆN & NGÔN NGỮ -->
                        <div class="tab-pane fade" id="tab-appearance" role="tabpanel" aria-labelledby="tab-appearance-btn">
                            <div class="card border shadow-sm rounded-4 bg-body p-3 p-sm-4">
                                @include('profile.partials.update-appearance-form')
                            </div>
                        </div>

                        <!-- TAB 3: ĐỔI MẬT KHẨU -->
                        <div class="tab-pane fade" id="tab-password" role="tabpanel" aria-labelledby="tab-password-btn">
                            <div class="card border shadow-sm rounded-4 bg-body p-3 p-sm-4">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>

                        <!-- TAB 4: XÓA TÀI KHOẢN -->
                        <div class="tab-pane fade" id="tab-danger" role="tabpanel" aria-labelledby="tab-danger-btn">
                            <div class="card border shadow-sm rounded-4 bg-body p-3 p-sm-4 danger-zone-card">
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </main>
    </div>

    <!-- Modal Đổi Avatar -->
    @include('profile.partials.update-avatar-modal')

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // JS Preview Avatar Upload
            const avatarInput = document.getElementById('avatarInput');
            const avatarPreviewImg = document.getElementById('avatarPreviewImg');
            const avatarPreviewFallback = document.getElementById('avatarPreviewFallback');
            const avatarClientError = document.getElementById('avatarClientError');
            const changeAvatarModalEl = document.getElementById('changeAvatarModal');
            const initialAvatarUrl = @json($user->avatar_url);

            avatarInput?.addEventListener('change', function (e) {
                const file = e.target.files && e.target.files[0];
                if (avatarClientError) {
                    avatarClientError.classList.add('d-none');
                    avatarClientError.textContent = '';
                }
                avatarInput.classList.remove('is-invalid');

                if (!file) {
                    resetPreview();
                    return;
                }

                const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
                if (!validTypes.includes(file.type)) {
                    if (avatarClientError) {
                        avatarClientError.textContent = 'Ảnh đại diện phải là file JPG, JPEG, PNG hoặc WEBP.';
                        avatarClientError.classList.remove('d-none');
                    }
                    avatarInput.classList.add('is-invalid');
                    avatarInput.value = '';
                    resetPreview();
                    return;
                }

                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    if (avatarClientError) {
                        avatarClientError.textContent = 'Ảnh đại diện không được vượt quá 2MB.';
                        avatarClientError.classList.remove('d-none');
                    }
                    avatarInput.classList.add('is-invalid');
                    avatarInput.value = '';
                    resetPreview();
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (event) {
                    if (avatarPreviewImg) {
                        avatarPreviewImg.src = event.target.result;
                        avatarPreviewImg.classList.remove('d-none');
                    }
                    if (avatarPreviewFallback) {
                        avatarPreviewFallback.classList.add('d-none');
                    }
                };
                reader.readAsDataURL(file);
            });

            function resetPreview() {
                if (initialAvatarUrl) {
                    if (avatarPreviewImg) {
                        avatarPreviewImg.src = initialAvatarUrl;
                        avatarPreviewImg.classList.remove('d-none');
                    }
                    if (avatarPreviewFallback) {
                        avatarPreviewFallback.classList.add('d-none');
                    }
                } else {
                    if (avatarPreviewImg) {
                        avatarPreviewImg.src = '';
                        avatarPreviewImg.classList.add('d-none');
                    }
                    if (avatarPreviewFallback) {
                        avatarPreviewFallback.classList.remove('d-none');
                    }
                }
            }

            changeAvatarModalEl?.addEventListener('hidden.bs.modal', function () {
                if (avatarInput) avatarInput.value = '';
                if (avatarClientError) {
                    avatarClientError.classList.add('d-none');
                    avatarClientError.textContent = '';
                }
                avatarInput?.classList.remove('is-invalid');
                resetPreview();
            });

            // Sidebar Offcanvas Toggle Mobile
            document.getElementById('sidebarToggle')?.addEventListener('click', function() {
                document.getElementById('sidebar')?.classList.toggle('show');
            });

            // Tự động cuộn ngang đưa Tab đang chọn vào giữa tầm nhìn trên Mobile
            const tabButtons = document.querySelectorAll('.profile-nav-pills .nav-link');
            tabButtons.forEach(btn => {
                btn.addEventListener('shown.bs.tab', function() {
                    if (window.innerWidth < 992) {
                        this.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
                    }
                });
            });
        });
    </script>

    <!-- Floating Action Menu & Mobile Bottom Navigation Bar (Đã bao gồm bottom-nav) -->
    <x-floating-menu />

    <!-- MODAL HƯỚNG DẪN CÀI ĐẶT PWA CHO ANDROID & IOS -->
    @include('components.pwa-install-modal')
</body>
</html>

