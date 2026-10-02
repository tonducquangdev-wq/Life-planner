<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cộng Đồng Gymer - Life Planner</title>

    <!-- Anti-flicker Theme Script -->
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
    <!-- Google Font: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom Dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tap-luyen.css') }}">
</head>

<body>

    <!-- MAIN WRAPPER FULL-WIDTH (HOME IS CALENDAR) -->
    <div class="main-wrapper main-wrapper-full">

        <!-- HEADER TRÊN CÙNG KÈM NÚT QUAY LẠI LỊCH -->
        <header class="top-header">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('calendar.index') }}" class="btn-back-to-calendar" title="Quay lại giao diện Lịch chính">
                    <i class="bi bi-arrow-left"></i>
                    <span>Quay lại Lịch</span>
                </a>
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-globe-americas text-primary"></i> Cộng Đồng Gymer & Chia Sẻ Lịch Tập
                </h5>
            </div>

            <div class="header-actions d-flex align-items-center gap-3">
                <div class="user-profile dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                        @if(Auth::check() && !empty(Auth::user()->avatar_url))
                            <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="user-avatar rounded-circle object-fit-cover" style="width: 38px; height: 38px;">
                        @else
                            <div class="user-avatar">
                                {{ Auth::check() ? Auth::user()->initials : 'U' }}
                            </div>
                        @endif
                        <span class="d-none d-md-inline fw-semibold text-dark">{{ Auth::user()->ho_ten ?? 'Người dùng' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2 text-primary"></i>Hồ sơ cá nhân</a></li>
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

        <!-- CONTENT BODY -->
        <main class="content-body py-4 px-3 px-md-4">
            <div class="container-xl p-0">

                @if(session('status'))
                    <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- SUB NAV TABS: THEO DÕI / BÁO CÁO -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div class="d-inline-flex p-1 bg-white border rounded-pill shadow-xs">
                        <a href="{{ route('tap-luyen.index') }}" class="btn btn-sm px-3 py-1.5 rounded-pill text-secondary fw-semibold">
                            <i class="bi bi-heart-pulse-fill me-1"></i> Theo dõi Thể chất
                        </a>
                        <a href="{{ route('bao-cao-tap-luyen.index') }}" class="btn btn-sm px-3 py-1.5 rounded-pill text-secondary fw-semibold">
                            <i class="bi bi-bar-chart-fill me-1"></i> Báo cáo
                        </a>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-primary rounded-pill btn-sm px-3.5 py-1.5 shadow-xs d-inline-flex align-items-center gap-1.5"
                                data-bs-toggle="modal" data-bs-target="#shareMyPlanModal">
                            <i class="bi bi-share-fill"></i>
                            <span class="fw-semibold">Chia sẻ Lịch tập của tôi</span>
                        </button>
                    </div>
                </div>

                <!-- BANNER TRA CỨU MÃ CODE NHANH -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white" style="background: linear-gradient(135deg, rgba(79, 70, 229, 0.05) 0%, rgba(59, 130, 246, 0.05) 100%);">
                    <div class="row align-items-center g-3">
                        <div class="col-12 col-lg-7">
                            <h5 class="fw-bold text-dark mb-1">Mở Lịch Tập Bằng Mã Code Chia Sẻ</h5>
                            <p class="text-muted fs-7 mb-0">Bạn nhận được mã từ bạn bè (ví dụ: <strong class="text-primary font-monospace">PPL-8X29K</strong>)? Nhập mã vào đây để xem và sao chép lịch tập về tài khoản ngay lập tức.</p>
                        </div>
                        <div class="col-12 col-lg-5">
                            <form method="GET" action="{{ route('workout.community') }}" class="d-flex gap-2" id="codeSearchForm">
                                <input type="text" name="code" value="{{ $searchCode ?? '' }}" 
                                       class="form-control rounded-pill bg-white border font-monospace text-uppercase" 
                                       placeholder="Nhập mã (VD: PPL-8X29K)" required>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold flex-shrink-0">
                                    <i class="bi bi-search me-1"></i> Tìm
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- DANH SÁCH LỊCH TẬP CHIA SẺ TRONG CỘNG ĐỒNG -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <span>Lịch tập cộng đồng chia sẻ</span>
                        <span class="badge bg-primary-subtle text-primary rounded-pill">{{ $sharedPlans->count() }} kế hoạch</span>
                    </h6>
                    @if(!empty($searchCode))
                        <a href="{{ route('workout.community') }}" class="text-primary fs-8 fw-semibold text-decoration-none">
                            <i class="bi bi-x-circle me-1"></i>Xem tất cả
                        </a>
                    @endif
                </div>

                <div class="row g-4">
                    @forelse($sharedPlans as $plan)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 p-3.5 bg-white d-flex flex-column" style="transition: transform 0.2s, box-shadow 0.2s;">
                                <!-- Header Card -->
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-2.5">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">{{ $plan->ten_ke_hoach }}</h6>
                                        <small class="text-muted fs-8">
                                            Bởi: <strong class="text-dark">{{ $plan->user ? $plan->user->ho_ten : 'Gymer' }}</strong>
                                        </small>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill font-monospace px-2.5 py-1 fs-8 fw-bold">
                                        {{ $plan->share_code }}
                                    </span>
                                </div>

                                <p class="text-muted fs-8 mb-3 text-truncate-2" style="min-height: 38px;">
                                    {{ $plan->mo_ta ?: 'Lịch tập tối ưu được cộng đồng Gymer khuyên dùng.' }}
                                </p>

                                <!-- Sessions pill list -->
                                <div class="p-2.5 rounded-3 bg-light mb-3">
                                    <div class="fs-8 fw-semibold text-muted text-uppercase mb-1.5">Lịch các buổi trong tuần:</div>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($plan->buoiTaps as $bt)
                                            <span class="badge bg-white text-dark border rounded-pill px-2 py-1 fs-8">
                                                {{ $bt->ten_thu ? "{$bt->ten_thu}: " : '' }}{{ $bt->ten_buoi_tap }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Card Footer: Actions -->
                                <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between gap-2">
                                    <a href="{{ route('workout.share.view', ['code' => $plan->share_code]) }}" 
                                       class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold">
                                        <i class="bi bi-eye me-1"></i> Xem chi tiết
                                    </a>

                                    <form method="POST" action="{{ route('workout.copy', ['code' => $plan->share_code]) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold"
                                                onclick="return confirm('Bạn có muốn sao chép toàn bộ kế hoạch này vào danh sách tập luyện cá nhân của bạn?')">
                                            <i class="bi bi-plus-lg me-1"></i> Sao chép plan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                                <div class="text-muted mb-3"><i class="bi bi-globe-americas fs-1"></i></div>
                                <h6 class="fw-bold text-dark">Chưa có lịch tập chia sẻ nào khớp với tìm kiếm</h6>
                                <p class="text-muted fs-7 mb-3">Hãy thử tìm với mã khác hoặc bấm "Chia sẻ Lịch tập của tôi" để đóng góp vào cộng đồng.</p>
                                <div>
                                    <a href="{{ route('workout.community') }}" class="btn btn-outline-primary rounded-pill px-4 btn-sm">
                                        Xem toàn bộ cộng đồng
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

            </div>
        </main>
    </div>

    <!-- MODAL CHIA SẺ LỊCH TẬP CỦA TÔI -->
    <div class="modal fade" id="shareMyPlanModal" tabindex="-1" aria-labelledby="shareMyPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2" id="shareMyPlanModalLabel">
                        <i class="bi bi-share-fill"></i> Chia sẻ Kế hoạch Tập luyện
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted fs-7 mb-3">Chọn một trong các kế hoạch của bạn để tạo Mã code và Đường link chia sẻ cho bạn bè:</p>

                    <div class="list-group rounded-3 mb-3">
                        @forelse($myPlans as $mp)
                            <div class="list-group-item p-3 d-flex align-items-center justify-content-between gap-2">
                                <div>
                                    <div class="fw-bold text-dark fs-7">{{ $mp->ten_ke_hoach }}</div>
                                    <small class="text-muted fs-8">{{ $mp->buoiTaps->count() }} buổi tập</small>
                                    @if($mp->is_shared && $mp->share_code)
                                        <div class="mt-1">
                                            <span class="badge bg-success-subtle text-success rounded-pill font-monospace fs-8">
                                                Code: {{ $mp->share_code }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <form method="POST" action="{{ route('workout.share.generate', ['id' => $mp->id]) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $mp->is_shared ? 'btn-outline-primary' : 'btn-primary' }} rounded-pill px-3 fw-semibold">
                                            {{ $mp->is_shared ? 'Lấy lại mã' : 'Tạo mã chia sẻ' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="p-3 text-center text-muted fs-7">
                                Bạn chưa có kế hoạch tập luyện nào. Vui lòng tạo kế hoạch trước khi chia sẻ.
                            </div>
                        @endforelse
                    </div>

                    <div class="alert alert-info border-0 rounded-3 fs-8 mb-0 d-flex align-items-start gap-2">
                        <i class="bi bi-shield-check fs-6 flex-shrink-0 mt-0.5"></i>
                        <span>Người nhận chỉ có thể xem và sao chép lịch tập sang tài khoản của họ. Kế hoạch gốc của bạn hoàn toàn được bảo vệ và không bị sửa đổi.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Menu (Góc phải bên dưới) -->
    <x-floating-menu />

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
