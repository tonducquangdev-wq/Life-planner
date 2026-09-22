<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Life Planner</title>

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

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom 2026 Auth CSS -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>

    <div class="login-page-wrapper">
        <div class="login-card-2026">

            <!-- Header Section -->
            <div class="login-header-2026">
                <div class="brand-icon-box">
                    <i class="bi bi-calendar2-check-fill"></i>
                </div>
                <h1 class="login-title-2026">Đăng nhập</h1>
                <p class="login-subtitle-2026">Chào mừng quay trở lại</p>
            </div>

            <!-- Refined Demo Account Card -->
            <div class="demo-card-minimal">
                <div>
                    <div class="demo-title">Demo Account</div>
                    <div class="demo-details">quang@gmail.com &bull; 12345678</div>
                </div>
                <button type="button" class="btn-demo-fill" id="btnDemoFill">Nạp dữ liệu</button>
            </div>

            <!-- Session Status Alert -->
            @if (session('status'))
            <div class="alert alert-success alert-custom d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('status') }}</div>
            </div>
            @endif

            <!-- Validation Error Alerts -->
            @if ($errors->any())
            <div class="alert alert-danger alert-custom" role="alert">
                <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <strong>Thông tin đăng nhập không chính xác</strong>
                </div>
                <ul class="mb-0 ps-3 small">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Form Đăng Nhập -->
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Email Input Group -->
                <div class="form-group-56">
                    <label for="email" class="form-label-2026">Địa chỉ Email</label>
                    <div class="input-container-56">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email"
                            class="form-control-clean @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="name@student.edu.vn"
                            required
                            autofocus
                            autocomplete="username">
                    </div>
                </div>

                <!-- Password Input Group -->
                <div class="form-group-56">
                    <label for="password" class="form-label-2026">Mật khẩu</label>
                    <div class="input-container-56">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password"
                            class="form-control-clean @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password">
                        <button type="button" class="toggle-pwd-btn" id="togglePassword" title="Ẩn/Hiện mật khẩu">
                            <i class="bi bi-eye-slash" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Options Row (Ghi nhớ & Quên mật khẩu) -->
                <div class="options-row-2026">
                    <div class="checkbox-custom">
                        <input type="checkbox" name="remember" id="remember_me">
                        <label for="remember_me">Ghi nhớ đăng nhập</label>
                    </div>

                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link-2026">Quên mật khẩu?</a>
                    @else
                    <a href="#" class="forgot-link-2026">Quên mật khẩu?</a>
                    @endif
                </div>

                <!-- Submit 56px Button -->
                <button type="submit" class="btn-submit-56">
                    <span>Đăng nhập</span>
                </button>

                <!-- Footer Link -->
                <div class="footer-prompt-2026">
                    <span>Chưa có tài khoản?</span>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="register-link-2026">Đăng ký</a>
                    @else
                    <a href="#" class="register-link-2026">Đăng ký</a>
                    @endif
                </div>

            </form>

        </div>
    </div>

    <!-- Vanilla JS Interactions -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Password Visibility Toggle
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (togglePassword && passwordInput && toggleIcon) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    if (type === 'text') {
                        toggleIcon.classList.remove('bi-eye-slash');
                        toggleIcon.classList.add('bi-eye');
                    } else {
                        toggleIcon.classList.remove('bi-eye');
                        toggleIcon.classList.add('bi-eye-slash');
                    }
                });
            }

            // 2. Quick Demo Fill Button
            const btnDemoFill = document.getElementById('btnDemoFill');
            const emailInput = document.getElementById('email');

            if (btnDemoFill && emailInput && passwordInput) {
                btnDemoFill.addEventListener('click', function() {
                    emailInput.value = 'quang@gmail.com';
                    passwordInput.value = '12345678';
                    emailInput.focus();

                    btnDemoFill.classList.add('bg-primary', 'text-white');
                    btnDemoFill.innerHTML = 'Đã nạp!';

                    setTimeout(() => {
                        btnDemoFill.classList.remove('bg-primary', 'text-white');
                        btnDemoFill.innerHTML = 'Nạp dữ liệu';
                    }, 2000);
                });
            }
        });
    </script>
</body>

</html>