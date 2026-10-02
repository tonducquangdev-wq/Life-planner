<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Tập Chia Sẻ: {{ $plan->ten_ke_hoach }} - Life Planner</title>

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
                    <i class="bi bi-share-fill text-primary"></i> Chi Tiết Kế Hoạch Được Chia Sẻ
                </h5>
            </div>

            <div class="header-actions d-flex align-items-center gap-3">
                <a href="{{ route('workout.community') }}" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
                    <i class="bi bi-globe-americas me-1"></i> Trở lại Cộng đồng
                </a>
            </div>
        </header>

        <!-- CONTENT BODY -->
        <main class="content-body py-4 px-3 px-md-4">
            <div class="container-lg p-0">

                <!-- HERO CARD KẾ HOẠCH -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 pb-3 border-bottom">
                        <div>
                            <span class="badge bg-primary text-white rounded-pill px-3 py-1 font-monospace fs-7 fw-bold mb-2">
                                MÃ CODE: {{ $plan->share_code }}
                            </span>
                            <h4 class="fw-bold text-dark mb-1">{{ $plan->ten_ke_hoach }}</h4>
                            <p class="text-muted fs-7 mb-0">
                                Người chia sẻ: <strong class="text-dark">{{ $plan->user ? $plan->user->ho_ten : 'Gymer' }}</strong>
                                &bull; Tổng số buổi: <strong class="text-dark">{{ $plan->buoiTaps->count() }} buổi/tuần</strong>
                            </p>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <!-- Nút copy code -->
                            <button type="button" class="btn btn-light border rounded-pill px-3 btn-sm fw-semibold" id="btnCopyCode" data-code="{{ $plan->share_code }}">
                                <i class="bi bi-clipboard me-1"></i> Sao chép Mã
                            </button>

                            <!-- Nút copy link -->
                            <button type="button" class="btn btn-light border rounded-pill px-3 btn-sm fw-semibold" id="btnCopyLink">
                                <i class="bi bi-link-45deg me-1"></i> Sao chép Link
                            </button>

                            <!-- Nút sao chép plan về tài khoản -->
                            <form method="POST" action="{{ route('workout.copy', ['code' => $plan->share_code]) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm fw-semibold"
                                        onclick="return confirm('Bạn có muốn sao chép toàn bộ lịch tập này về tài khoản cá nhân?')">
                                    <i class="bi bi-plus-circle me-1"></i> Sao chép về tài khoản của tôi
                                </button>
                            </form>
                        </div>
                    </div>

                    @if(!empty($plan->mo_ta))
                        <div class="mt-3 text-muted fs-7">
                            <i class="bi bi-info-circle me-1 text-primary"></i> {{ $plan->mo_ta }}
                        </div>
                    @endif
                </div>

                <!-- CHI TIẾT TỪNG BUỔI TẬP VÀ BÀI TẬP -->
                <h5 class="fw-bold text-dark mb-3">Lịch Trình Chi Tiết Các Buổi Tập</h5>

                <div class="row g-4">
                    @forelse($plan->buoiTaps as $bt)
                        <div class="col-12 col-lg-6">
                            <div class="card h-100 border-0 shadow-sm rounded-4 p-3.5 bg-white">
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">{{ $bt->ten_buoi_tap }}</h6>
                                        <small class="text-muted fs-8">{{ $bt->mo_ta ?: 'Buổi rèn luyện' }}</small>
                                    </div>
                                    @if($bt->ten_thu)
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 fs-8 fw-semibold">
                                            {{ $bt->ten_thu }}
                                        </span>
                                    @endif
                                </div>

                                <div class="list-group list-group-flush">
                                    @forelse($bt->chiTietBuoiTaps->sortBy('thu_tu') as $ct)
                                        <div class="list-group-item px-0 py-2 d-flex align-items-center justify-content-between border-0">
                                            <div class="d-flex align-items-center gap-2.5">
                                                <span class="badge bg-light text-muted rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 11px;">
                                                    {{ $loop->iteration }}
                                                </span>
                                                <div>
                                                    <div class="fw-semibold text-dark fs-7">
                                                        {{ $ct->baiTapTheChat ? $ct->baiTapTheChat->ten_bai_tap : 'Bài tập' }}
                                                    </div>
                                                    <small class="text-muted fs-8">
                                                        {{ $ct->baiTapTheChat ? $ct->baiTapTheChat->nhom_co : 'Thể chất' }}
                                                    </small>
                                                </div>
                                            </div>
                                            <span class="badge bg-light text-primary border rounded-pill px-2.5 py-1 fs-8 fw-semibold">
                                                {{ $ct->dinh_dang_thong_so }}
                                            </span>
                                        </div>
                                    @empty
                                        <div class="text-muted fs-8 py-2 text-center">Chưa có bài tập nào</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 p-4 text-center text-muted">
                                Kế hoạch này hiện chưa có buổi tập chi tiết.
                            </div>
                        </div>
                    @endforelse
                </div>

            </div>
        </main>
    </div>

    <!-- Floating Action Menu (Góc phải bên dưới) -->
    <x-floating-menu />

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnCopyCode = document.getElementById('btnCopyCode');
            const btnCopyLink = document.getElementById('btnCopyLink');

            btnCopyCode?.addEventListener('click', function() {
                const code = this.dataset.code;
                navigator.clipboard.writeText(code).then(() => {
                    this.innerHTML = '<i class="bi bi-check2 me-1"></i> Đã chép mã!';
                    setTimeout(() => {
                        this.innerHTML = '<i class="bi bi-clipboard me-1"></i> Sao chép Mã';
                    }, 2000);
                });
            });

            btnCopyLink?.addEventListener('click', function() {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    this.innerHTML = '<i class="bi bi-check2 me-1"></i> Đã chép link!';
                    setTimeout(() => {
                        this.innerHTML = '<i class="bi bi-link-45deg me-1"></i> Sao chép Link';
                    }, 2000);
                });
            });
        });
    </script>
</body>
</html>
