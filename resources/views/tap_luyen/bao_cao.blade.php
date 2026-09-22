<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Báo Cáo Tập Luyện & Thể Chất - Life Planner</title>

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
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- CSS Tùy chỉnh Dashboard & Tap luyen -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tap-luyen.css') }}">
    <style>
        .kpi-card {
            transition: all 0.25s ease;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08) !important;
        }
        .streak-hero-card {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
        }
        .heatmap-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
        }
        .heatmap-cell {
            aspect-ratio: 1;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            transition: all 0.15s ease;
        }
        .heatmap-cell:hover {
            transform: scale(1.1);
            z-index: 2;
        }
        .heatmap-lvl-0 { background-color: #f1f5f9; color: #94a3b8; }
        .heatmap-lvl-1 { background-color: #d1fae5; color: #047857; }
        .heatmap-lvl-2 { background-color: #6ee7b7; color: #065f46; }
        .heatmap-lvl-3 { background-color: #10b981; color: #ffffff; }
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
                    <i class="bi bi-activity text-success"></i> Báo Cáo Tập Luyện & Thể Chất
                </h5>
            </div>

            <!-- Khu vực người dùng & Nút Thao tác -->
            <div class="header-actions d-flex align-items-center gap-3">
                <a href="{{ route('tap-luyen.index') }}" class="btn btn-outline-success rounded-pill px-3 btn-sm fw-semibold">
                    <i class="bi bi-play-circle-fill me-1"></i> Bắt đầu tập ngay
                </a>

                <!-- User Profile Dropdown -->
                <div class="user-profile dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            @if(!empty(Auth::user()->avatar_url))
                                <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-100 h-100 rounded-circle object-fit-cover">
                            @else
                                {{ Auth::user()->initials ?? 'U' }}
                            @endif
                        </div>
                        <div class="d-none d-md-block text-start">
                            <div class="fw-bold text-dark fs-6 leading-tight">{{ Auth::user()->ho_ten ?? 'User' }}</div>
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

            <!-- 1. HÀNG THỐNG KÊ KPI CARDS -->
            <div class="row g-3 mb-4">
                
                <!-- CARD HERO: CHUỖI TẬP STREAK -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 rounded-4 p-4 streak-hero-card shadow-sm h-100 position-relative overflow-hidden">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-white-50 fw-medium fs-7">Chuỗi Ngày Rèn Luyện</span>
                            <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-2.5 py-1 fs-8 fw-semibold">
                                <i class="bi bi-fire me-1 text-warning"></i>Streak
                            </span>
                        </div>
                        <div class="d-flex align-items-baseline gap-2 mb-1">
                            <h2 class="fw-bold mb-0 text-white display-6">{{ $chuoiNgayTap }}</h2>
                            <span class="text-white-50 fs-6">ngày liên tiếp 🔥</span>
                        </div>
                        <div class="text-white-50 fs-8 d-flex align-items-center gap-1">
                            <span>Kế hoạch:</span>
                            <strong class="text-white">{{ $activePlan ? $activePlan->ten_ke_hoach : 'Kế hoạch cá nhân' }}</strong>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: TỔNG THỜI GIAN TẬP -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 rounded-4 p-4 bg-white shadow-sm h-100 kpi-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted fw-semibold fs-7">Tổng Thời Gian Tập</span>
                            <div class="p-2.5 rounded-circle bg-success-subtle text-success">
                                <i class="bi bi-clock-history fs-5"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">{{ $tongThoiGianHour }} <span class="fs-6 text-muted font-normal">Giờ</span></h3>
                        <small class="text-muted fs-8 mt-2 d-block">Tương đương {{ $tongThoiGianMinute }} phút vận động tích lũy</small>
                    </div>
                </div>

                <!-- CARD 3: TỔNG SỐ BUỔI TẬP -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 rounded-4 p-4 bg-white shadow-sm h-100 kpi-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted fw-semibold fs-7">Tổng Số Buổi Tập</span>
                            <div class="p-2.5 rounded-circle bg-primary-subtle text-primary">
                                <i class="bi bi-check-circle-fill fs-5"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">{{ $tongBuoiTap }} <span class="fs-6 text-muted font-normal">Buổi</span></h3>
                        <small class="text-muted fs-8 mt-2 d-block">Trung bình {{ $thoiGianTrungBinhBuoi }} phút / buổi tập</small>
                    </div>
                </div>

                <!-- CARD 4: PHONG ĐỘ TẬP LUYỆN -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 rounded-4 p-4 bg-white shadow-sm h-100 kpi-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted fw-semibold fs-7">Đánh Giá Phong Độ</span>
                            <div class="p-2.5 rounded-circle bg-warning-subtle text-warning">
                                <i class="bi bi-trophy-fill fs-5"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">Xuất sắc</h3>
                        <div class="d-flex align-items-center gap-1 mt-2">
                            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 fs-8">
                                <i class="bi bi-arrow-up-right me-1"></i>Tăng 15% tuần này
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 2. HÀNG HEATMAP & BIỂU ĐỒ TRỰC QUAN -->
            <div class="row g-4 mb-4">
                
                <!-- HEATMAP TẦN SUẤT TẬP TRONG THÁNG -->
                <div class="col-12 col-lg-5 col-xl-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Heatmap Tháng {{ $currentMonth }}/{{ $currentYear }}</h6>
                                <p class="text-muted fs-8 mb-0">Tần suất và mức độ chăm chỉ tập luyện</p>
                            </div>
                        </div>

                        <!-- Lưới 7 Cột Thứ 2 -> Chủ Nhật -->
                        <div class="d-flex justify-content-between text-muted fs-8 fw-semibold text-center mb-2">
                            <span>T2</span><span>T3</span><span>T4</span><span>T5</span><span>T6</span><span>T7</span><span>CN</span>
                        </div>

                        <div class="heatmap-grid mb-3">
                            @php
                                $daysInMonth = \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->daysInMonth;
                                $firstDayIso = \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->dayOfWeekIso; // 1 = T2 ... 7 = CN
                            @endphp

                            <!-- Ô trống đầu tháng -->
                            @for ($i = 1; $i < $firstDayIso; $i++)
                                <div class="heatmap-cell bg-transparent"></div>
                            @endfor

                            <!-- Các ngày trong tháng -->
                            @for ($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $dateKey = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                                    $data = $monthlyHeatmap[$dateKey] ?? null;
                                    $dur = $data['duration'] ?? 0;

                                    $lvlClass = match (true) {
                                        $dur >= 60 => 'heatmap-lvl-3',
                                        $dur >= 30 => 'heatmap-lvl-2',
                                        $dur > 0 => 'heatmap-lvl-1',
                                        default => 'heatmap-lvl-0',
                                    };
                                @endphp
                                <div class="heatmap-cell {{ $lvlClass }}" title="Ngày {{ $day }}/{{ $currentMonth }}: {{ $dur }} phút tập">
                                    {{ $day }}
                                </div>
                            @endfor
                        </div>

                        <!-- Legend chú giải màu -->
                        <div class="d-flex align-items-center justify-content-center gap-2 fs-8 text-muted pt-2 border-top">
                            <span>Ít</span>
                            <span class="d-inline-block rounded" style="width: 12px; height: 12px; background: #f1f5f9;"></span>
                            <span class="d-inline-block rounded" style="width: 12px; height: 12px; background: #d1fae5;"></span>
                            <span class="d-inline-block rounded" style="width: 12px; height: 12px; background: #6ee7b7;"></span>
                            <span class="d-inline-block rounded" style="width: 12px; height: 12px; background: #10b981;"></span>
                            <span>Nhiều</span>
                        </div>
                    </div>
                </div>

                <!-- BIỂU ĐỒ TẦN SUẤT TẬP THEO THỨ TRONG TUẦN -->
                <div class="col-12 col-lg-7 col-xl-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Tần Suất Tập Theo Thứ Trong Tuần</h6>
                                <p class="text-muted fs-8 mb-0">Thống kê số buổi tập tích lũy từ Thứ 2 đến Chủ Nhật</p>
                            </div>
                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1.5 fs-8">Toàn thời gian</span>
                        </div>
                        <div style="position: relative; height: 260px; width: 100%;">
                            <canvas id="workoutDayOfWeekChart"></canvas>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 3. HÀNG BIỂU ĐỒ PHÂN BỔ LOẠI BUỔI TẬP & NHẬT KÝ -->
            <div class="row g-4 mb-4">

                <!-- BIỂU ĐỒ DOUGHNUT LOẠI BUỔI TẬP -->
                <div class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold text-dark mb-0">Phân Bổ Nhóm Bài Tập</h6>
                            <span class="badge bg-light text-dark rounded-pill fs-8">{{ $tongBuoiTap }} Buổi</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-center" style="position: relative; height: 220px;">
                            <canvas id="workoutTypeChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- BẢNG NHẬT KÝ HOÀN THÀNH TẬP LUYỆN MỚI NHẤT -->
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Nhật Ký Tập Luyện Mới Nhất</h6>
                                <p class="text-muted fs-8 mb-0">Lịch sử ghi nhận các buổi tập đã hoàn thành</p>
                            </div>
                            <a href="{{ route('tap-luyen.index') }}" class="btn btn-sm btn-light rounded-pill px-3">Xem tất cả</a>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light rounded-3">
                                    <tr>
                                        <th class="border-0 text-secondary fs-8 uppercase ps-3 py-2.5">Thời Gian</th>
                                        <th class="border-0 text-secondary fs-8 uppercase py-2.5">Tên Buổi Tập</th>
                                        <th class="border-0 text-secondary fs-8 uppercase text-center py-2.5">Thời Lượng</th>
                                        <th class="border-0 text-secondary fs-8 uppercase text-end pe-3 py-2.5">Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lichSuRecords as $ls)
                                        @php
                                            $startedAt = \Carbon\Carbon::parse($ls->thoi_gian_bat_dau);
                                        @endphp
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-semibold text-dark fs-7">{{ $startedAt->format('d/m/Y') }}</div>
                                                <div class="text-muted fs-8">{{ $startedAt->format('H:i') }}</div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark fs-7">{{ $ls->buoiTap ? $ls->buoiTap->ten_buoi_tap : ($ls->ghi_chu ?: 'Tập tự do') }}</div>
                                                <div class="text-muted fs-8 text-truncate" style="max-width: 250px;">{{ $ls->ghi_chu }}</div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 font-mono fs-7">
                                                    <i class="bi bi-clock me-1 text-success"></i>{{ $ls->tong_thoi_luong ?: 0 }} phút
                                                </span>
                                            </td>
                                            <td class="text-end pe-3">
                                                <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 fs-8">
                                                    <i class="bi bi-check-circle-fill me-1"></i>Hoàn thành
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted fs-8">
                                                Chưa có nhật ký tập luyện nào.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chart.js Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Render Biểu đồ Tần suất tập theo thứ trong tuần
            const dayStats = @json($dayOfWeekStats);
            const ctxDayOfWeek = document.getElementById('workoutDayOfWeekChart')?.getContext('2d');
            if (ctxDayOfWeek) {
                new Chart(ctxDayOfWeek, {
                    type: 'bar',
                    data: {
                        labels: ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ Nhật'],
                        datasets: [{
                            label: 'Số buổi tập',
                            data: [
                                dayStats[1] || 0,
                                dayStats[2] || 0,
                                dayStats[3] || 0,
                                dayStats[4] || 0,
                                dayStats[5] || 0,
                                dayStats[6] || 0,
                                dayStats[7] || 0,
                            ],
                            backgroundColor: '#10b981',
                            borderRadius: 8,
                            barThickness: 24,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            // 2. Render Biểu đồ Doughnut Loại buổi tập
            const typeData = @json($typeDistribution);
            const ctxType = document.getElementById('workoutTypeChart')?.getContext('2d');
            if (ctxType) {
                new Chart(ctxType, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(typeData),
                        datasets: [{
                            data: Object.values(typeData),
                            backgroundColor: ['#ef4444', '#3b82f6', '#10b981', '#06b6d4', '#6b7280'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth: 12, padding: 12 }
                            }
                        }
                    }
                });
            }

            // Sidebar Toggle Mobile
            document.getElementById('sidebarToggle')?.addEventListener('click', function() {
                document.getElementById('sidebar')?.classList.toggle('show');
            });
        });
    </script>
</body>
</html>
