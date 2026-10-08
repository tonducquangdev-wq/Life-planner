<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Công Việc & Dự Án - Life Planner</title>

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
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/calendar.css') }}?v={{ time() }}">

</head>

<body>
    <div class="main-wrapper main-wrapper-full">
        <!-- HEADER -->
        <header class="top-header border-bottom bg-white px-4 py-2 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('calendar.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại Lịch
                </a>
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-briefcase-fill text-primary"></i> Công Việc & Dự Án
                </h5>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('cong-viec.dashboard') }}" class="btn btn-primary btn-sm rounded-pill px-3 active">
                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                </a>
                <a href="{{ route('cong-viec.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-kanban me-1"></i> Công việc (Kanban)
                </a>
                <a href="{{ route('du-an.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-folder-symlink me-1"></i> Dự án
                </a>
            </div>
        </header>

        <!-- CONTENT BODY -->
        <main class="content-body p-4">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- 1. CHỈ SỐ STAT CARDS -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-xl-2.4 col-lg-3">
                    <div class="custom-card p-3 border-0 shadow-sm rounded-4 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-secondary fs-7 fw-semibold">Tổng Công Việc</span>
                            <div class="bg-primary-subtle text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="bi bi-list-task fs-5"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark mb-0">{{ $stats['tong_cong_viec'] }}</h3>
                        <small class="text-muted fs-8">Toàn bộ công việc</small>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-2.4 col-lg-3">
                    <div class="custom-card p-3 border-0 shadow-sm rounded-4 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-secondary fs-7 fw-semibold">Đang Làm</span>
                            <div class="bg-info-subtle text-info rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="bi bi-arrow-repeat fs-5"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark mb-0">{{ $stats['dang_lam'] }}</h3>
                        <small class="text-muted fs-8">Công việc tiến hành</small>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-2.4 col-lg-3">
                    <div class="custom-card p-3 border-0 shadow-sm rounded-4 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-secondary fs-7 fw-semibold">Hoàn Thành</span>
                            <div class="bg-success-subtle text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="bi bi-check2-circle fs-5"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark mb-0">{{ $stats['hoan_thanh'] }}</h3>
                        <small class="text-success fs-8 fw-semibold">{{ $stats['ty_le_hoan_thanh'] }}% hoàn tất</small>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-2.4 col-lg-3">
                    <div class="custom-card p-3 border-0 shadow-sm rounded-4 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-danger fs-7 fw-bold">Quá Hạn</span>
                            <div class="bg-danger-subtle text-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-danger mb-0">{{ $stats['qua_han'] }}</h3>
                        <small class="text-danger fs-8">Cần xử lý ngay</small>
                    </div>
                </div>
            </div>

            <!-- 2. NỘI DUNG CHÍNH: DỰ ÁN NỔI BẬT & CÔNG VIỆC GẤP -->
            <div class="row g-4">
                <!-- CỘT TRÁI: DỰ ÁN GẦN ĐÂY -->
                <div class="col-12 col-lg-6">
                    <div class="custom-card p-4 border-0 shadow-sm rounded-4 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-folder-fill text-primary fs-5"></i> Dự Án Gần Đây
                            </h6>
                            <a href="{{ route('du-an.index') }}" class="btn btn-link text-decoration-none p-0 fs-7 fw-semibold">Xem tất cả <i class="bi bi-arrow-right"></i></a>
                        </div>

                        @if($duAns->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-folder-plus fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Chưa có dự án nào. <a href="{{ route('du-an.index') }}" class="fw-semibold">Tạo dự án mới</a>
                        </div>
                        @else
                        <div class="d-flex flex-column gap-3">
                            @foreach($duAns as $duAn)
                            <div class="p-3 rounded-3 bg-light border d-flex flex-column gap-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="{{ route('du-an.show', $duAn->id) }}" class="fw-bold text-dark text-decoration-none fs-6 hover-primary">
                                        <span class="d-inline-block rounded-circle me-1" style="width: 10px; height: 10px; background-color: {{ $duAn->mau_nhan }};"></span>
                                        {{ $duAn->ten_du_an }}
                                    </a>
                                    @php
                                        $badgeMap = [
                                            'chua_bat_dau' => ['bg-secondary-subtle text-secondary', 'Chưa bắt đầu'],
                                            'dang_thuc_hien' => ['bg-primary-subtle text-primary', 'Đang thực hiện'],
                                            'tam_dung' => ['bg-warning-subtle text-warning', 'Tạm dừng'],
                                            'hoan_thanh' => ['bg-success-subtle text-success', 'Hoàn thành'],
                                            'da_huy' => ['bg-danger-subtle text-danger', 'Đã hủy'],
                                        ];
                                        $st = $badgeMap[$duAn->trang_thai] ?? ['bg-secondary-subtle text-secondary', $duAn->trang_thai];
                                    @endphp
                                    <span class="badge {{ $st[0] }} rounded-pill fs-8">{{ $st[1] }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between fs-8 text-secondary">
                                    <span>{{ $duAn::first() ? $duAn->cong_viec_hoan_thanh_count ?? 0 : 0 }}/{{ $duAn->tong_cong_viec_count ?? 0 }} công việc</span>
                                    <span class="fw-bold text-dark">{{ $duAn->tien_do_percent }}%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: {{ $duAn->tien_do_percent }}%" aria-valuenow="{{ $duAn->tien_do_percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <!-- CỘT PHẢI: CÔNG VIỆC GẤP & QUÁ HẠN -->
                <div class="col-12 col-lg-6">
                    <div class="custom-card p-4 border-0 shadow-sm rounded-4 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-clock-history text-danger fs-5"></i> Công Việc Hạn Chót & Quá Hạn
                            </h6>
                            <a href="{{ route('cong-viec.index') }}" class="btn btn-link text-decoration-none p-0 fs-7 fw-semibold">Quản lý Kanban <i class="bi bi-arrow-right"></i></a>
                        </div>

                        @if($congViecGap->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-check-circle-fill fs-1 d-block mb-2 text-success opacity-50"></i>
                            Tuyệt vời! Không có công việc nào bị quá hạn.
                        </div>
                        @else
                        <div class="d-flex flex-column gap-2.5">
                            @foreach($congViecGap as $task)
                            <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between {{ $task->is_qua_han ? 'bg-danger-subtle border-danger-subtle' : 'bg-white' }}">
                                <div>
                                    <div class="fw-bold text-dark fs-7 mb-1">
                                        {{ $task->ten_cong_viec }}
                                    </div>
                                    <div class="d-flex align-items-center gap-2 fs-8 text-secondary">
                                        @if($task->deadline)
                                        <span><i class="bi bi-calendar-event me-1"></i>{{ $task->deadline->format('d/m/Y H:i') }}</span>
                                        @endif
                                        @if($task->is_qua_han)
                                        <span class="badge bg-danger text-white rounded-pill fs-8">{{ $task->qua_han_text }}</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="badge bg-light text-dark border rounded-pill fs-8">{{ $task->trang_thai }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Floating Action Menu & Shared Profile Modal -->
    <x-floating-menu />
</body>

</html>

