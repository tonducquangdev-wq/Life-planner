<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - Life Planner</title>

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
                <h1 class="login-title-2026">Đăng ký</h1>
                <p class="login-subtitle-2026">Bắt đầu hành trình của bạn</p>
            </div>

            <!-- Validation Error Alerts -->
            @if ($errors->any())
                <div class="alert alert-danger alert-custom" role="alert">
                    <div class="d-flex align-items-center mb-1">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <strong>Thông tin đăng ký chưa hợp lệ</strong>
                    </div>
                    <ul class="mb-0 ps-3 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Đăng Ký -->
            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

                <!-- Họ và tên Input Group -->
                <div class="form-group-56">
                    <label for="ho_ten" class="form-label-2026">Họ và tên sinh viên</label>
                    <div class="input-container-56">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" 
                               class="form-control-clean @error('ho_ten') is-invalid @enderror" 
                               id="ho_ten" 
                               name="ho_ten" 
                               value="{{ old('ho_ten') }}" 
                               placeholder="Nguyễn Văn A" 
                               required 
                               autofocus 
                               autocomplete="name">
                    </div>
                </div>

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
                               autocomplete="username">
                    </div>
                </div>

                <!-- Password Input Group -->
                <div class="form-group-56" style="margin-bottom: 0.5rem;">
                    <label for="password" class="form-label-2026">Mật khẩu</label>
                    <div class="input-container-56">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" 
                               class="form-control-clean @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="••••••••" 
                               required 
                               autocomplete="new-password">
                        <button type="button" class="toggle-pwd-btn" id="togglePassword" title="Ẩn/Hiện mật khẩu">
                            <i class="bi bi-eye-slash" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Password Strength Bar -->
                <div class="password-strength-box">
                    <div class="password-strength-bar">
                        <div class="password-strength-fill" id="strengthFill"></div>
                    </div>
                    <span class="password-strength-text" id="strengthText">Nhập mật khẩu an toàn</span>
                </div>

                <!-- Confirm Password Input Group -->
                <div class="form-group-56">
                    <label for="password_confirmation" class="form-label-2026">Xác nhận mật khẩu</label>
                    <div class="input-container-56">
                        <i class="bi bi-shield-lock input-icon"></i>
                        <input type="password" 
                               class="form-control-clean @error('password_confirmation') is-invalid @enderror" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               placeholder="••••••••" 
                               required 
                               autocomplete="new-password">
                        <button type="button" class="toggle-pwd-btn" id="toggleConfirmPassword" title="Ẩn/Hiện mật khẩu">
                            <i class="bi bi-eye-slash" id="toggleConfirmIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Terms Options Row -->
                <div class="options-row-2026" style="margin-bottom: 1.5rem;">
                    <div class="checkbox-custom">
                        <input type="checkbox" name="terms" id="terms" required checked>
                        <label for="terms">Tôi đồng ý với <a href="#" class="forgot-link-2026">Điều khoản & Chính sách</a></label>
                    </div>
                </div>

                <!-- Submit 56px Button -->
                <button type="submit" class="btn-submit-56">
                    <span>Đăng ký tài khoản</span>
                </button>

                <!-- Footer Link -->
                <div class="footer-prompt-2026">
                    <span>Đã có tài khoản?</span>
                    <a href="{{ route('login') }}" class="register-link-2026">Đăng nhập</a>
                </div>

            </form>

        </div>
    </div>

    <!-- Vanilla JS Interactions -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Password Visibility Toggle
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (togglePassword && passwordInput && toggleIcon) {
                togglePassword.addEventListener('click', function () {
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

            // 2. Confirm Password Visibility Toggle
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const toggleConfirmIcon = document.getElementById('toggleConfirmIcon');

            if (toggleConfirmPassword && confirmPasswordInput && toggleConfirmIcon) {
                toggleConfirmPassword.addEventListener('click', function () {
                    const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    confirmPasswordInput.setAttribute('type', type);
                    
                    if (type === 'text') {
                        toggleConfirmIcon.classList.remove('bi-eye-slash');
                        toggleConfirmIcon.classList.add('bi-eye');
                    } else {
                        toggleConfirmIcon.classList.remove('bi-eye');
                        toggleConfirmIcon.classList.add('bi-eye-slash');
                    }
                });
            }

            // 3. Password Strength Bar Calculator
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');

            if (passwordInput && strengthFill && strengthText) {
                passwordInput.addEventListener('input', function () {
                    const val = passwordInput.value;
                    let score = 0;

                    if (val.length >= 6) score += 25;
                    if (val.length >= 8) score += 25;
                    if (/[A-Z]/.test(val)) score += 25;
                    if (/[0-9]/.test(val) || /[^A-Za-z0-9]/.test(val)) score += 25;

                    strengthFill.style.width = score + '%';

                    if (score === 0) {
                        strengthFill.style.backgroundColor = '#e2e8f0';
                        strengthText.textContent = 'Nhập mật khẩu an toàn';
                        strengthText.style.color = '#64748b';
                    } else if (score <= 50) {
                        strengthFill.style.backgroundColor = '#f43f5e';
                        strengthText.textContent = 'Mật khẩu yếu';
                        strengthText.style.color = '#f43f5e';
                    } else if (score <= 75) {
                        strengthFill.style.backgroundColor = '#f59e0b';
                        strengthText.textContent = 'Mật khẩu trung bình';
                        strengthText.style.color = '#d97706';
                    } else {
                        strengthFill.style.backgroundColor = '#10b981';
                        strengthText.textContent = 'Mật khẩu rất mạnh!';
                        strengthText.style.color = '#059669';
                    }
                });
            }
        });
    </script>
</body>
</html>
