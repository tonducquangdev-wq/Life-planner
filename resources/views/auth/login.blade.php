<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Life Planner</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom Login CSS -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

    <div class="login-wrapper container-fluid p-0">
        <div class="row g-0 min-vh-100">

            <!-- CỘT TRÁI: THƯƠNG HIỆU & HÌNH MINH HỌA (DESKTOP) -->
            <div class="col-lg-6 d-none d-lg-flex brand-panel">
                <!-- Logo & Brand Header -->
                <div class="d-flex align-items-center gap-3">
                    <div class="brand-logo-badge">
                        <i class="bi bi-calendar2-check-fill"></i>
                    </div>
                    <span class="brand-title">Life Planner</span>
                </div>

                <!-- Hero Text -->
                <div class="my-auto py-5">
                    <h1 class="hero-heading">
                        Quản lý học tập và cuộc sống sinh viên
                    </h1>
                    <p class="hero-description">
                        Theo dõi môn học, bài tập, lịch học, mục tiêu và kế hoạch tập luyện trên một nền tảng duy nhất.
                    </p>

                    <!-- Sinh viên học tập Minh họa Interactive Micro Cards -->
                    <div class="illustration-box mt-4">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <div class="feature-pill">
                                <i class="bi bi-journal-check text-primary fs-5"></i>
                                <span>12 Môn học đang theo dõi</span>
                            </div>
                            <div class="feature-pill">
                                <i class="bi bi-trophy-fill text-warning fs-5"></i>
                                <span>Mục tiêu GPA 3.8/4.0</span>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <div class="feature-pill">
                                <i class="bi bi-activity text-danger fs-5"></i>
                                <span>3 Buổi tập thể chất / tuần</span>
                            </div>
                            <div class="feature-pill">
                                <i class="bi bi-alarm-fill text-info fs-5"></i>
                                <span>Nhắc nhở bài tập thông minh</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer note -->
                <div class="small opacity-75">
                    &copy; {{ date('Y') }} Life Planner - Hệ thống quản lý toàn diện dành cho Sinh viên.
                </div>
            </div>

            <!-- CỘT PHẢI: CARD FORM ĐĂNG NHẬP -->
            <div class="col-lg-6 form-panel">
                <div class="login-card">

                    <!-- Mobile Brand Logo -->
                    <div class="d-flex align-items-center gap-2 mb-4 d-lg-none">
                        <div class="brand-logo-badge bg-primary text-white" style="width: 40px; height: 40px; border-radius: 10px;">
                            <i class="bi bi-calendar2-check-fill fs-5"></i>
                        </div>
                        <span class="fw-bold fs-5 text-dark">Life Planner</span>
                    </div>

                    <!-- Header Form -->
                    <div class="login-header-icon">
                        <i class="bi bi-person-fill-lock"></i>
                    </div>

                    <h2 class="login-title">Đăng nhập</h2>
                    <p class="login-subtitle">Chào mừng bạn quay trở lại</p>

                    <!-- Session Status Alert -->
                    @if (session('status'))
                        <div class="alert alert-success alert-custom d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                            <div>{{ session('status') }}</div>
                        </div>
                    @endif

                    <!-- Validation General Errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-custom mb-4" role="alert">
                            <div class="d-flex align-items-center mb-1">
                                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                <strong class="fw-semibold">Thông tin đăng nhập không chính xác!</strong>
                            </div>
                            <ul class="mb-0 ps-3 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form Đăng Nhập -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Địa chỉ Email</label>
                            <div class="input-group-custom">
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="name@student.edu.vn" 
                                       required 
                                       autofocus 
                                       autocomplete="username">
                                <i class="bi bi-envelope input-icon"></i>
                            </div>
                        </div>

                        <!-- Mật khẩu -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <div class="input-group-custom">
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="••••••••" 
                                       required 
                                       autocomplete="current-password">
                                <i class="bi bi-lock input-icon"></i>
                                <button type="button" class="toggle-password" id="togglePassword" title="Ẩn/Hiện mật khẩu">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Checkbox Ghi nhớ & Quên mật khẩu -->
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="form-check m-0">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                                <label class="form-check-label" for="remember_me">
                                    Ghi nhớ đăng nhập
                                </label>
                            </div>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="forgot-link">
                                    Quên mật khẩu?
                                </a>
                            @else
                                <a href="#" class="forgot-link">
                                    Quên mật khẩu?
                                </a>
                            @endif
                        </div>

                        <!-- Nút Đăng nhập -->
                        <button type="submit" class="btn btn-primary-custom mb-4">
                            <span>Đăng nhập</span>
                            <i class="bi bi-arrow-right fs-5"></i>
                        </button>

                        <!-- Chân Form: Đăng ký -->
                        <div class="text-center register-prompt">
                            <span>Chưa có tài khoản?</span>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="register-link">Đăng ký ngay</a>
                            @else
                                <a href="#" class="register-link">Đăng ký ngay</a>
                            @endif
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Vanilla JS Toggle Password -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (togglePassword && passwordInput && toggleIcon) {
                togglePassword.addEventListener('click', function () {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    if (type === 'text') {
                        toggleIcon.classList.remove('bi-eye');
                        toggleIcon.classList.add('bi-eye-slash');
                    } else {
                        toggleIcon.classList.remove('bi-eye-slash');
                        toggleIcon.classList.add('bi-eye');
                    }
                });
            }
        });
    </script>
</body>
</html>
