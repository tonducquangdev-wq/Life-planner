<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - Life Planner</title>

    <!-- Anti-Flicker Theme Initialization -->
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
    <!-- Neumorphic Auth CSS -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>

    <div class="neu-page-wrapper">
        <div class="neu-flip-container">
            <!-- Starting in Flipped State (Sign Up active on /register) -->
            <div class="neu-flip-card is-flipped" id="flipCard">

                <!-- ==========================================
                     FRONT PANEL: LOGIN
                     ========================================== -->
                <div class="neu-form-panel neu-login-panel" id="loginPanel">
                    <!-- Icon Badge -->
                    <div class="neu-header">
                        <div class="neu-icon-badge" title="Bảo mật Life Planner">
                            <i class="bi bi-lock-fill"></i>
                        </div>
                        <h2 class="neu-title">Chào mừng</h2>
                        <p class="neu-subtitle">Đăng nhập để tiếp tục hành trình của bạn</p>
                    </div>

                    <!-- Demo Account Quick Fill Card -->
                    <div class="neu-demo-card">
                        <div>
                            <div class="neu-demo-title">Tài khoản dùng thử</div>
                            <div class="neu-demo-details">quang@gmail.com &bull; 12345678</div>
                        </div>
                        <button type="button" class="neu-btn-demo" id="btnDemoFill">Nạp dữ liệu</button>
                    </div>

                    <!-- Session Status Alert -->
                    @if (session('status'))
                    <div class="neu-alert neu-alert-success d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf

                        <!-- Email Input -->
                        <div class="neu-form-group">
                            <label for="email" class="neu-input-label">Địa chỉ Email</label>
                            <div class="neu-input-container @error('email') is-invalid @enderror">
                                <i class="bi bi-envelope neu-input-icon"></i>
                                <input type="email"
                                    class="neu-form-control"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="name@student.edu.vn"
                                    required
                                    autocomplete="username">
                            </div>
                            @error('email')
                            <div class="neu-field-error">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        <!-- Password Input -->
                        <div class="neu-form-group">
                            <label for="password" class="neu-input-label">Mật khẩu</label>
                            <div class="neu-input-container @error('password') is-invalid @enderror">
                                <i class="bi bi-lock neu-input-icon"></i>
                                <input type="password"
                                    class="neu-form-control"
                                    id="password"
                                    name="password"
                                    placeholder="••••••••"
                                    required
                                    autocomplete="current-password">
                                <button type="button" class="neu-toggle-pwd" id="togglePassword" title="Ẩn/Hiện mật khẩu">
                                    <i class="bi bi-eye-slash" id="toggleIcon"></i>
                                </button>
                            </div>
                            @error('password')
                            <div class="neu-field-error">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        <!-- Options Row (Remember & Forgot) -->
                        <div class="neu-options-row">
                            <label class="neu-checkbox-label" for="remember_me">
                                <input type="checkbox" name="remember" id="remember_me" class="neu-checkbox">
                                <span>Ghi nhớ đăng nhập</span>
                            </label>

                            @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="neu-forgot-link">Quên mật khẩu?</a>
                            @else
                            <a href="#" class="neu-forgot-link">Quên mật khẩu?</a>
                            @endif
                        </div>

                        <!-- Login Submit Button -->
                        <button type="submit" class="neu-btn-submit" id="btnLoginSubmit">
                            <span>Đăng nhập</span>
                        </button>

                        <!-- Footer Flip Prompt -->
                        <div class="neu-footer-prompt">
                            <span>Chưa có tài khoản?</span>
                            <a href="{{ route('register') }}" class="neu-flip-link" id="linkToRegister">Đăng ký</a>
                            <button type="button" class="neu-flip-circle-btn" id="btnFlipToRegister" title="Chuyển sang Đăng ký">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ==========================================
                     BACK PANEL: REGISTER (SIGN UP)
                     ========================================== -->
                <div class="neu-form-panel neu-signup-panel" id="signupPanel">
                    <!-- Icon Badge -->
                    <div class="neu-header">
                        <div class="neu-icon-badge" title="Tạo tài khoản mới">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <h1 class="neu-title">Tạo tài khoản</h1>
                        <p class="neu-subtitle">Bắt đầu hành trình cùng Life Planner</p>
                    </div>

                    <!-- Validation Error Alerts -->
                    @if ($errors->any())
                    <div class="neu-alert neu-alert-danger" role="alert">
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

                    <!-- Register Form -->
                    <form method="POST" action="{{ route('register') }}" id="registerForm">
                        @csrf

                        <!-- Họ tên sinh viên -->
                        <div class="neu-form-group">
                            <label for="ho_ten" class="neu-input-label">Họ và tên</label>
                            <div class="neu-input-container @error('ho_ten') is-invalid @enderror">
                                <i class="bi bi-person neu-input-icon"></i>
                                <input type="text"
                                    class="neu-form-control"
                                    id="ho_ten"
                                    name="ho_ten"
                                    value="{{ old('ho_ten') }}"
                                    placeholder="Nguyễn Văn A"
                                    required
                                    autofocus
                                    autocomplete="name">
                            </div>
                            @error('ho_ten')
                            <div class="neu-field-error">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="neu-form-group">
                            <label for="reg_email" class="neu-input-label">Địa chỉ Email</label>
                            <div class="neu-input-container @error('email') is-invalid @enderror">
                                <i class="bi bi-envelope neu-input-icon"></i>
                                <input type="email"
                                    class="neu-form-control"
                                    id="reg_email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="name@student.edu.vn"
                                    required
                                    autocomplete="username">
                            </div>
                            @error('email')
                            <div class="neu-field-error">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        <!-- Mật khẩu -->
                        <div class="neu-form-group" style="margin-bottom: 0.2rem;">
                            <label for="reg_password" class="neu-input-label">Mật khẩu</label>
                            <div class="neu-input-container @error('password') is-invalid @enderror">
                                <i class="bi bi-lock neu-input-icon"></i>
                                <input type="password"
                                    class="neu-form-control"
                                    id="reg_password"
                                    name="password"
                                    placeholder="••••••••"
                                    required
                                    autocomplete="new-password">
                                <button type="button" class="neu-toggle-pwd" id="toggleRegPassword" title="Ẩn/Hiện mật khẩu">
                                    <i class="bi bi-eye-slash" id="toggleRegIcon"></i>
                                </button>
                            </div>
                            @error('password')
                            <div class="neu-field-error">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        <!-- Password Strength Bar -->
                        <div class="neu-pwd-strength-box">
                            <div class="neu-pwd-strength-track">
                                <div class="neu-pwd-strength-fill" id="strengthFill"></div>
                            </div>
                            <span class="neu-pwd-strength-text" id="strengthText">Nhập mật khẩu an toàn</span>
                        </div>

                        <!-- Xác nhận mật khẩu -->
                        <div class="neu-form-group">
                            <label for="password_confirmation" class="neu-input-label">Xác nhận mật khẩu</label>
                            <div class="neu-input-container @error('password_confirmation') is-invalid @enderror">
                                <i class="bi bi-shield-lock neu-input-icon"></i>
                                <input type="password"
                                    class="neu-form-control"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    placeholder="••••••••"
                                    required
                                    autocomplete="new-password">
                                <button type="button" class="neu-toggle-pwd" id="toggleConfirmPassword" title="Ẩn/Hiện mật khẩu">
                                    <i class="bi bi-eye-slash" id="toggleConfirmIcon"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                            <div class="neu-field-error">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        <!-- Điều khoản sử dụng -->
                        <div class="neu-options-row">
                            <label class="neu-checkbox-label" for="terms">
                                <input type="checkbox" name="terms" id="terms" class="neu-checkbox" required checked>
                                <span>Đồng ý với <a href="#" class="neu-forgot-link">Điều khoản dịch vụ</a></span>
                            </label>
                        </div>

                        <!-- Register Submit Button -->
                        <button type="submit" class="neu-btn-submit" id="btnRegisterSubmit">
                            <span>Đăng ký</span>
                        </button>

                        <!-- Footer Flip Prompt -->
                        <div class="neu-footer-prompt">
                            <span>Đã có tài khoản?</span>
                            <a href="{{ route('login') }}" class="neu-flip-link" id="linkToLogin">Đăng nhập</a>
                            <button type="button" class="neu-flip-circle-btn" id="btnFlipToLogin" title="Quay lại Đăng nhập">
                                <i class="bi bi-arrow-return-left"></i>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Neumorphic Interactions & 3D Flip Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const flipCard = document.getElementById('flipCard');
            const loginPanel = document.getElementById('loginPanel');
            const signupPanel = document.getElementById('signupPanel');

            const btnFlipToRegister = document.getElementById('btnFlipToRegister');
            const linkToRegister = document.getElementById('linkToRegister');
            const btnFlipToLogin = document.getElementById('btnFlipToLogin');
            const linkToLogin = document.getElementById('linkToLogin');

            // 1. Đồng bộ chiều cao thẻ lật linh hoạt theo mặt hiển thị
            function syncCardHeight(isFlipped) {
                if (!flipCard || !loginPanel || !signupPanel) return;
                const activePanel = isFlipped ? signupPanel : loginPanel;
                const targetHeight = activePanel.scrollHeight;
                if (targetHeight > 0) {
                    flipCard.style.minHeight = Math.min(targetHeight, window.innerHeight - 20) + 'px';
                }
            }

            // 2. Hàm kích hoạt lật thẻ 3D
            function setCardFlipped(flipped, updateHistory = true) {
                if (!flipCard) return;

                if (flipped) {
                    flipCard.classList.add('is-flipped');
                    document.title = 'Đăng ký tài khoản - Life Planner';
                    if (updateHistory && window.history.pushState) {
                        window.history.pushState({ panel: 'register' }, '', "{{ route('register') }}");
                    }
                    setTimeout(() => {
                        const hoTenInput = document.getElementById('ho_ten');
                        if (hoTenInput) hoTenInput.focus();
                    }, 400);
                } else {
                    flipCard.classList.remove('is-flipped');
                    document.title = 'Đăng nhập - Life Planner';
                    if (updateHistory && window.history.pushState) {
                        window.history.pushState({ panel: 'login' }, '', "{{ route('login') }}");
                    }
                    setTimeout(() => {
                        const emailInput = document.getElementById('email');
                        if (emailInput) emailInput.focus();
                    }, 400);
                }

                syncCardHeight(flipped);
            }

            // Gán sự kiện lật thẻ
            if (btnFlipToRegister) {
                btnFlipToRegister.addEventListener('click', function(e) {
                    e.preventDefault();
                    setCardFlipped(true);
                });
            }

            if (linkToRegister) {
                linkToRegister.addEventListener('click', function(e) {
                    e.preventDefault();
                    setCardFlipped(true);
                });
            }

            if (btnFlipToLogin) {
                btnFlipToLogin.addEventListener('click', function(e) {
                    e.preventDefault();
                    setCardFlipped(false);
                });
            }

            if (linkToLogin) {
                linkToLogin.addEventListener('click', function(e) {
                    e.preventDefault();
                    setCardFlipped(false);
                });
            }

            // Hỗ trợ nút Back/Forward của trình duyệt
            window.addEventListener('popstate', function(e) {
                const path = window.location.pathname;
                if (path.includes('login')) {
                    setCardFlipped(false, false);
                } else {
                    setCardFlipped(true, false);
                }
            });

            // Khởi tạo kích thước ban đầu (mặt Register active)
            window.addEventListener('resize', () => {
                const isFlipped = flipCard.classList.contains('is-flipped');
                syncCardHeight(isFlipped);
            });
            setTimeout(() => syncCardHeight(true), 50);

            // 3. Demo Account Auto Fill Button
            const btnDemoFill = document.getElementById('btnDemoFill');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');

            if (btnDemoFill && emailInput && passwordInput) {
                btnDemoFill.addEventListener('click', function() {
                    emailInput.value = 'quang@gmail.com';
                    passwordInput.value = '12345678';
                    emailInput.focus();

                    btnDemoFill.textContent = 'Đã nạp!';
                    btnDemoFill.style.color = '#10b981';

                    setTimeout(() => {
                        btnDemoFill.textContent = 'Nạp dữ liệu';
                        btnDemoFill.style.color = '';
                    }, 2000);
                });
            }

            // 4. Password Visibility Toggles
            function setupPasswordToggle(btnId, inputId, iconId) {
                const btn = document.getElementById(btnId);
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);

                if (btn && input && icon) {
                    btn.addEventListener('click', function() {
                        const isPassword = input.getAttribute('type') === 'password';
                        input.setAttribute('type', isPassword ? 'text' : 'password');

                        if (isPassword) {
                            icon.classList.remove('bi-eye-slash');
                            icon.classList.add('bi-eye');
                        } else {
                            icon.classList.remove('bi-eye');
                            icon.classList.add('bi-eye-slash');
                        }
                    });
                }
            }

            setupPasswordToggle('togglePassword', 'password', 'toggleIcon');
            setupPasswordToggle('toggleRegPassword', 'reg_password', 'toggleRegIcon');
            setupPasswordToggle('toggleConfirmPassword', 'password_confirmation', 'toggleConfirmIcon');

            // 5. Password Strength Meter Calculation
            const regPasswordInput = document.getElementById('reg_password');
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');

            if (regPasswordInput && strengthFill && strengthText) {
                regPasswordInput.addEventListener('input', function() {
                    const val = regPasswordInput.value;
                    let score = 0;

                    if (val.length >= 6) score += 25;
                    if (val.length >= 8) score += 25;
                    if (/[A-Z]/.test(val)) score += 25;
                    if (/[0-9]/.test(val) || /[^A-Za-z0-9]/.test(val)) score += 25;

                    strengthFill.style.width = score + '%';

                    if (score === 0) {
                        strengthFill.style.backgroundColor = '#cbd5e1';
                        strengthText.textContent = 'Nhập mật khẩu an toàn';
                        strengthText.style.color = '#64748b';
                    } else if (score <= 50) {
                        strengthFill.style.backgroundColor = '#ef4444';
                        strengthText.textContent = 'Mật khẩu yếu';
                        strengthText.style.color = '#ef4444';
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
