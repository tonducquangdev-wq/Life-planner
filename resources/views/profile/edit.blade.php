<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
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
        .avatar-preview-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>

<body>

    <!-- 1. SIDEBAR BÊN TRÁI -->
    @include('layouts.sidebar')

    <!-- 2. MAIN WRAPPER -->
    <div class="main-wrapper">

        <!-- HEADER THANH CÔNG CỤ TRÊN CÙNG -->
        <header class="top-header border-bottom bg-white px-3 px-lg-4 py-2">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none p-1 px-2 border rounded-3" id="sidebarToggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                    <i class="bi bi-list fs-4 text-primary"></i>
                </button>
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-person-bounding-box text-primary"></i> Hồ Sơ Cá Nhân & Cài Đặt
                </h5>
            </div>

            <!-- Khu vực người dùng & Nút Thông báo -->
            <div class="header-actions d-flex align-items-center gap-3">
                <!-- User Profile Dropdown -->
                <div class="user-profile dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
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
        <main class="content-body p-3 p-lg-4">

            <!-- THÔNG BÁO THÀNH CÔNG NẾU CÓ -->
            @if (session('status') === 'avatar-updated')
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-xs mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>Ảnh đại diện đã được cập nhật thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('status') === 'profile-updated')
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-xs mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>Thông tin cá nhân đã được cập nhật thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('status') === 'password-updated')
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-xs mb-4" role="alert">
                    <i class="bi bi-shield-check me-2"></i>Mật khẩu tài khoản đã được cập nhật thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('status') === 'notifications-updated')
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-xs mb-4" role="alert">
                    <i class="bi bi-bell-check-fill me-2"></i>Cấu hình thông báo đã được cập nhật thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('status') === 'appearance-updated')
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-xs mb-4" role="alert">
                    <i class="bi bi-palette-fill me-2"></i>Cấu hình giao diện và ngôn ngữ đã được cập nhật thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- 1. HERO COVER & PROFILE SUMMARY CARD HEADER -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
                <div class="profile-cover-banner"></div>
                <div class="px-4 pb-4 position-relative">
                    <div class="d-flex flex-column flex-md-row align-items-center align-items-md-end justify-content-between gap-3 text-center text-md-start">
                        <div class="d-flex flex-column flex-md-row align-items-center align-items-md-end gap-3">
                            <div class="profile-avatar-wrapper">
                                @if(!empty($user->avatar_url))
                                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="profile-avatar-xl">
                                @else
                                    <div class="profile-avatar-xl">
                                        {{ $user->initials }}
                                    </div>
                                @endif
                                <button type="button" class="avatar-edit-badge" data-bs-toggle="modal" data-bs-target="#changeAvatarModal" title="Đổi ảnh đại diện">
                                    <i class="bi bi-camera-fill fs-6"></i>
                                </button>
                            </div>
                            <div class="pt-2 pt-md-0 mb-md-2">
                                <h4 class="fw-bold text-dark mb-1">{{ $user->ho_ten ?? 'Người dùng' }}</h4>
                                <p class="text-muted small mb-0 d-flex align-items-center justify-content-center justify-content-md-start gap-2">
                                    <span><i class="bi bi-envelope me-1"></i>{{ $user->email }}</span>
                                    <span>•</span>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1">
                                        <i class="bi bi-patch-check-fill me-1"></i>Student Life Planner
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 mb-md-2">
                            <button type="button" class="btn btn-outline-primary rounded-pill px-3 btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#changeAvatarModal">
                                <i class="bi bi-camera me-1"></i>Đổi ảnh đại diện
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. BỐ CỤC CHÍNH (SIDE NAV TAB & CONTENT CARDS) -->
            <div class="row g-4">
                
                <!-- CỘT TRÁI: MENU TAB ĐIỀU HƯỚNG PROFILE (Col-lg-3) -->
                <div class="col-12 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 sticky-top" style="top: 80px; z-index: 10;">
                        <div class="nav flex-column nav-pills profile-nav-pills gap-1" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <button class="nav-link active" id="tab-info-btn" data-bs-toggle="pill" data-bs-target="#tab-info" type="button" role="tab" aria-controls="tab-info" aria-selected="true">
                                <i class="bi bi-person-lines-fill fs-5"></i>
                                <span>Thông tin cá nhân</span>
                            </button>
                            <button class="nav-link" id="tab-notif-btn" data-bs-toggle="pill" data-bs-target="#tab-notif" type="button" role="tab" aria-controls="tab-notif" aria-selected="false">
                                <i class="bi bi-bell-fill fs-5 text-warning"></i>
                                <span>Cài đặt thông báo</span>
                            </button>
                            <button class="nav-link" id="tab-appearance-btn" data-bs-toggle="pill" data-bs-target="#tab-appearance" type="button" role="tab" aria-controls="tab-appearance" aria-selected="false">
                                <i class="bi bi-palette-fill fs-5 text-primary"></i>
                                <span>Giao diện & Ngôn ngữ</span>
                            </button>
                            <button class="nav-link" id="tab-password-btn" data-bs-toggle="pill" data-bs-target="#tab-password" type="button" role="tab" aria-controls="tab-password" aria-selected="false">
                                <i class="bi bi-shield-lock-fill fs-5 text-info"></i>
                                <span>Mật khẩu & Bảo mật</span>
                            </button>
                            <button class="nav-link text-danger" id="tab-danger-btn" data-bs-toggle="pill" data-bs-target="#tab-danger" type="button" role="tab" aria-controls="tab-danger" aria-selected="false">
                                <i class="bi bi-trash3-fill fs-5 text-danger"></i>
                                <span>Xóa tài khoản</span>
                            </button>
                        </div>

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

                <!-- CỘT PHẢI: NỘI DUNG TỪNG TAB (Col-lg-9) -->
                <div class="col-12 col-lg-9">
                    <div class="tab-content" id="v-pills-tabContent">
                        
                        <!-- TAB 1: THÔNG TIN CÁ NHÂN -->
                        <div class="tab-pane fade show active" id="tab-info" role="tabpanel" aria-labelledby="tab-info-btn">
                            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                        </div>

                        <!-- TAB 2: CÀI ĐẶT THÔNG BÁO -->
                        <div class="tab-pane fade" id="tab-notif" role="tabpanel" aria-labelledby="tab-notif-btn">
                            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                                @include('profile.partials.update-notification-settings-form')
                            </div>
                        </div>

                        <!-- TAB 3: GIAO DIỆN & NGÔN NGỮ -->
                        <div class="tab-pane fade" id="tab-appearance" role="tabpanel" aria-labelledby="tab-appearance-btn">
                            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                                @include('profile.partials.update-appearance-form')
                            </div>
                        </div>

                        <!-- TAB 3: ĐỔI MẬT KHẨU -->
                        <div class="tab-pane fade" id="tab-password" role="tabpanel" aria-labelledby="tab-password-btn">
                            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>

                        <!-- TAB 4: XÓA TÀI KHOẢN -->
                        <div class="tab-pane fade" id="tab-danger" role="tabpanel" aria-labelledby="tab-danger-btn">
                            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 danger-zone-card">
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
        });
    </script>
</body>
</html>
