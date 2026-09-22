<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Life Planner - Nền Tảng Quản Lý Học Tập & Cuộc Sống Sinh Viên</title>

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
    <!-- Custom Welcome Page CSS -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body>

    <!-- ==========================================
         NAVIGATION BAR
         ========================================== -->
    <nav class="navbar navbar-expand-lg landing-navbar">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <div class="brand-logo-icon">
                    <i class="bi bi-calendar2-check-fill"></i>
                </div>
                <span>Life Planner</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#landingNav" aria-controls="landingNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-dark"></i>
            </button>

            <div class="collapse navbar-collapse" id="landingNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-2 text-center">
                    <li class="nav-item">
                        <a class="nav-link-custom d-inline-block" href="#tinh-nang">Tính năng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom d-inline-block" href="#loi-ich">Lợi ích</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom d-inline-block" href="#quy-trinh">Quy trình</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom d-inline-block" href="#con-so">Thống kê</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center justify-content-center gap-3 mt-3 mt-lg-0">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-nav-register d-flex align-items-center gap-2">
                            <i class="bi bi-speedometer2"></i>
                            <span>Bảng Điều Khiển</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-nav-login">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-nav-register">
                                <span>Đăng ký miễn phí</span>
                                <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- ==========================================
         HERO SECTION
         ========================================== -->
    <section class="hero-section text-center">
        <div class="container">

            <div class="hero-badge mx-auto">
                <i class="bi bi-stars text-warning"></i>
                <span>Nền tảng quản lý sinh viên toàn diện #1</span>
            </div>

            <h1 class="hero-heading hero-title mx-auto">
                Lập Kế Hoạch Học Tập &<br>
                <span class="gradient-text">Cuộc Sống Sinh Viên Thông Minh</span>
            </h1>

            <p class="hero-subtitle">
                Theo dõi môn học, tự động dự báo điểm GPA, quản lý thời khóa biểu thông minh và rèn luyện thể chất – Tất cả trên một bảng điều khiển duy nhất dành riêng cho sinh viên.
            </p>

            <div class="hero-cta-group">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-hero-primary">
                        <i class="bi bi-house-door-fill"></i>
                        <span>Vào Trang Cá Nhân</span>
                    </a>
                @else
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-hero-primary">
                            <span>Bắt đầu trải nghiệm ngay</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    @endif
                    <a href="{{ route('login') }}" class="btn-hero-secondary">
                        <i class="bi bi-person-circle"></i>
                        <span>Đăng nhập hệ thống</span>
                    </a>
                @endauth
            </div>

            <!-- Hero Mockup Container with Floating Pills -->
            <div class="dashboard-preview-wrapper mt-4">
                
                <!-- Floating Glass Pill Left -->
                <div class="floating-pill floating-pill-left d-none d-lg-flex">
                    <div class="badge bg-success-subtle text-success p-2 rounded-circle">
                        <i class="bi bi-trophy-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark fs-6">GPA Mục tiêu: 3.8 / 4.0</div>
                        <span class="text-muted small">Tăng 0.3 điểm so với kỳ trước</span>
                    </div>
                </div>

                <!-- Floating Glass Pill Right -->
                <div class="floating-pill floating-pill-right d-none d-lg-flex">
                    <div class="badge bg-primary-subtle text-primary p-2 rounded-circle">
                        <i class="bi bi-alarm-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark fs-6">Lịch Học Hôm Nay</div>
                        <span class="text-muted small">08:00 - Lập Trình Web (Phòng A2.04)</span>
                    </div>
                </div>

                <!-- Dashboard Interactive Mockup Window -->
                <div class="dashboard-preview-card">
                    <div class="dashboard-preview-header">
                        <div class="window-dots">
                            <span class="window-dot red"></span>
                            <span class="window-dot yellow"></span>
                            <span class="window-dot green"></span>
                        </div>
                        <div class="text-white-50 small font-monospace">life-planner.student.app/dashboard</div>
                        <div class="text-white-50 small"><i class="bi bi-shield-check text-success me-1"></i>Bảo mật</div>
                    </div>

                    <div class="mockup-grid">
                        <div class="mockup-item">
                            <span class="mockup-badge indigo">
                                <i class="bi bi-journal-check me-1"></i>HỌC TẬP
                            </span>
                            <div class="mockup-value">12 Môn Học</div>
                            <div class="mockup-label">Đang theo dõi trong học kỳ này</div>
                        </div>

                        <div class="mockup-item">
                            <span class="mockup-badge cyan">
                                <i class="bi bi-bar-chart-line-fill me-1"></i>KẾT QUẢ
                            </span>
                            <div class="mockup-value">3.82 GPA</div>
                            <div class="mockup-label">Đạt danh hiệu Sinh viên Xung kích</div>
                        </div>

                        <div class="mockup-item">
                            <span class="mockup-badge emerald">
                                <i class="bi bi-heart-pulse-fill me-1"></i>RÈN LUYỆN
                            </span>
                            <div class="mockup-value">3 Buổi/Tuần</div>
                            <div class="mockup-label">Kế hoạch tập gym & thể chất</div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ==========================================
         KEY FEATURES SECTION
         ========================================== -->
    <section class="features-section text-center" id="tinh-nang">
        <div class="container">
            <span class="section-tag">Tính Năng Nổi Bật</span>
            <h2 class="section-title">Giải Pháp Toàn Diện Cho Hành Trình Sinh Viên</h2>
            <p class="section-subtitle">Tất cả những gì bạn cần để sắp xếp việc học hiệu quả, cân bằng sức khỏe và đạt điểm số tối ưu.</p>

            <div class="row g-4">
                <!-- Feature Card 1 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-box indigo">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <h3 class="feature-card-title">Quản Lý Môn Học</h3>
                        <p class="feature-card-desc">Lưu trữ danh sách môn học, số tín chỉ, bài tập và lịch thi cực kỳ gọn gàng.</p>
                    </div>
                </div>

                <!-- Feature Card 2 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-box cyan">
                            <i class="bi bi-calculator"></i>
                        </div>
                        <h3 class="feature-card-title">Tính Điểm & GPA</h3>
                        <p class="feature-card-desc">Tự động tính điểm trung bình môn học, dự báo xếp loại học tập chính xác.</p>
                    </div>
                </div>

                <!-- Feature Card 3 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-box emerald">
                            <i class="bi bi-activity"></i>
                        </div>
                        <h3 class="feature-card-title">Kế Hoạch Tập Luyện</h3>
                        <p class="feature-card-desc">Theo dõi các bài tập thể chất, duy trì sức khỏe dẻo dai trong cả kỳ học.</p>
                    </div>
                </div>

                <!-- Feature Card 4 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-box amber">
                            <i class="bi bi-bell-ring"></i>
                        </div>
                        <h3 class="feature-card-title">Nhắc Nhở Thông Minh</h3>
                        <p class="feature-card-desc">Cảnh báo deadline bài tập, giờ lên lớp giúp bạn không bao giờ bỏ lỡ lịch hẹn.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==========================================
         STATS BAR SECTION
         ========================================== -->
    <section class="stats-section" id="con-so">
        <div class="container">
            <div class="row g-4 justify-content-center">
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">10,000+</div>
                        <div class="stat-label">Sinh viên tin dùng</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">3.8+</div>
                        <div class="stat-label">GPA Mục tiêu trung bình</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">98%</div>
                        <div class="stat-label">Cải thiện quản lý thời gian</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Miễn phí trải nghiệm</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==========================================
         WORKFLOW STEPS SECTION
         ========================================== -->
    <section class="workflow-section" id="quy-trinh">
        <div class="container text-center">
            <span class="section-tag">3 Bước Đơn Giản</span>
            <h2 class="section-title">Bắt Đầu Cùng Life Planner Ngay Hôm Nay</h2>
            <p class="section-subtitle">Chỉ mất 1 phút để thiết lập không gian quản lý học tập cá nhân chuyên nghiệp.</p>

            <div class="row g-4 mt-2">
                <div class="col-md-4">
                    <div class="workflow-card">
                        <span class="workflow-step-num">01</span>
                        <div class="feature-icon-box indigo mb-3">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <h3 class="workflow-title">Đăng Ký Tài Khoản</h3>
                        <p class="workflow-desc">Tạo tài khoản sinh viên miễn phí chỉ với địa chỉ email cá nhân hoặc trường học.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="workflow-card">
                        <span class="workflow-step-num">02</span>
                        <div class="feature-icon-box cyan mb-3">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <h3 class="workflow-title">Thêm Môn Học & Lịch Trình</h3>
                        <p class="workflow-desc">Cập nhật danh sách môn học, số tín chỉ và kế hoạch tập luyện cá nhân của bạn.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="workflow-card">
                        <span class="workflow-step-num">03</span>
                        <div class="feature-icon-box emerald mb-3">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h3 class="workflow-title">Theo Dõi & Đạt Mục Tiêu</h3>
                        <p class="workflow-desc">Theo dõi tiến độ hàng ngày, nâng cao GPA và giữ gìn thói quen thể chất khoa học.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==========================================
         BOTTOM CTA SECTION
         ========================================== -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-box">
                <h2 class="cta-title">Sẵn Sàng Nâng Tầm Cuộc Sống Sinh Viên?</h2>
                <p class="cta-subtitle">Tham gia cùng cộng đồng sinh viên năng động ngay hôm nay. Đăng ký hoàn toàn miễn phí!</p>

                <div class="d-flex flex-wrap align-items-center justify-content-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-cta-white">
                            <i class="bi bi-speedometer2"></i>
                            <span>Truy cập Bảng Điều Khiển</span>
                        </a>
                    @else
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-cta-white">
                                <span>Đăng ký tài khoản ngay</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        @endif
                        <a href="{{ route('login') }}" class="btn-cta-outline">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            <span>Đăng nhập</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- ==========================================
         FOOTER
         ========================================== -->
    <footer class="landing-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand">
                        <div class="brand-logo-icon" style="width: 36px; height: 36px; font-size: 1.1rem;">
                            <i class="bi bi-calendar2-check-fill"></i>
                        </div>
                        <span>Life Planner</span>
                    </div>
                    <p class="footer-desc">Hệ thống quản lý toàn diện dành cho Sinh viên – Học tập thông minh, lối sống khoa học.</p>
                </div>

                <div class="col-6 col-lg-2">
                    <h4 class="footer-heading">Tính năng</h4>
                    <ul class="footer-links">
                        <li><a href="#tinh-nang">Quản lý môn học</a></li>
                        <li><a href="#tinh-nang">Dự báo GPA</a></li>
                        <li><a href="#tinh-nang">Kế hoạch tập luyện</a></li>
                        <li><a href="#tinh-nang">Nhắc nhở thông minh</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-2">
                    <h4 class="footer-heading">Tài khoản</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('login') }}">Đăng nhập</a></li>
                        <li><a href="{{ route('register') }}">Đăng ký</a></li>
                        <li><a href="{{ route('password.request') }}">Quên mật khẩu</a></li>
                    </ul>
                </div>

                <div class="col-lg-4">
                    <h4 class="footer-heading">Hỗ trợ & Liên hệ</h4>
                    <p class="footer-desc">Có thắc mắc hoặc phản hồi? Hãy liên hệ với chúng tôi để được trợ giúp sớm nhất.</p>
                    <div class="d-flex gap-3 text-white fs-5 mt-3">
                        <a href="#" class="text-white-50 hover-text-white"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white-50 hover-text-white"><i class="bi bi-github"></i></a>
                        <a href="#" class="text-white-50 hover-text-white"><i class="bi bi-envelope-fill"></i></a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <div>&copy; {{ date('Y') }} Life Planner. All rights reserved.</div>
                <div class="d-flex gap-3">
                    <a href="#" class="text-white-50 text-decoration-none">Điều khoản sử dụng</a>
                    <a href="#" class="text-white-50 text-decoration-none">Chính sách bảo mật</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
