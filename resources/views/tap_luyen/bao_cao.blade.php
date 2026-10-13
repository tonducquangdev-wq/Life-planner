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
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/tap-luyen.css') }}?v={{ time() }}">
</head>

<body>

    <!-- MAIN WRAPPER -->
    <div class="main-wrapper main-wrapper-full">

        <!-- TOPBAR TRÊN CÙNG COMPACT 58PX -->
        @include('layouts.topbar', ['title' => 'Báo Cáo Thể Chất'])

        <!-- NỘI DUNG CHÍNH (CONTENT BODY) -->
        <main class="content-body p-3 p-lg-4">

            <!-- SUB NAV SWITCHER (THEO DÕI / BÁO CÁO) -->
            <div class="report-page-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="workout-title-icon">
                        <i class="bi bi-bar-chart-fill text-primary"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="workout-page-title mb-0">Báo Cáo Thể Chất</h4>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-8 fw-bold">Phân tích</span>
                        </div>
                        <p class="workout-page-subtitle mb-0 mt-0.5">Thống kê tần suất, thời gian và phân tích tiến độ rèn luyện</p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2.5 flex-wrap">
                    <div class="nav-segment-control d-inline-flex p-1 rounded-pill">
                        <a href="{{ route('tap-luyen.index') }}" class="btn btn-sm px-3 rounded-pill text-secondary fw-semibold">
                            <i class="bi bi-lightning-charge-fill me-1 text-warning"></i>Buổi tập
                        </a>
                        <a href="{{ route('bao-cao-tap-luyen.index') }}" class="btn btn-sm px-3 rounded-pill btn-primary fw-semibold">
                            <i class="bi bi-bar-chart-fill me-1"></i>Báo cáo
                        </a>
                    </div>
                    <a href="{{ route('tap-luyen.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-2xs">
                        <i class="bi bi-play-circle me-1"></i>Vào tập ngay
                    </a>
                </div>
            </div>

            <!-- 1. HÀNG THỐNG KÊ KPI CARDS -->
            <div class="row g-3 mb-4">
                
                <!-- CARD HERO: CHUỖI TẬP STREAK -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="report-kpi-card streak-hero-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fw-semibold fs-7">Chuỗi Ngày Rèn Luyện</span>
                            <div class="streak-icon-box" title="Chuỗi ngày rèn luyện liên tục">
                                <i class="bi bi-fire"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-2 mb-2">
                            <h2 class="fw-bold mb-0 text-dark display-6">{{ $chuoiNgayTap }}</h2>
                            <span class="text-muted fs-6">ngày liên tiếp 🔥</span>
                        </div>
                        <div class="text-muted fs-8 d-flex align-items-center gap-1">
                            <span>Kế hoạch:</span>
                            <strong class="text-dark">{{ $activePlan ? $activePlan->ten_ke_hoach : 'Kế hoạch cá nhân' }}</strong>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: TỔNG THỜI GIAN TẬP -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="report-kpi-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted fw-semibold fs-7">Tổng Thời Gian Tập</span>
                            <div class="kpi-icon-container text-success">
                                <i class="bi bi-clock-history"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">{{ $tongThoiGianHour }} <span class="fs-6 text-muted fw-normal">Giờ</span></h3>
                        <small class="text-muted fs-8 mt-2 d-block">Tương đương {{ $tongThoiGianMinute }} phút vận động tích lũy</small>
                    </div>
                </div>

                <!-- CARD 3: TỔNG SỐ BUỔI TẬP -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="report-kpi-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted fw-semibold fs-7">Tổng Số Buổi Tập</span>
                            <div class="kpi-icon-container text-primary">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">{{ $tongBuoiTap }} <span class="fs-6 text-muted fw-normal">Buổi</span></h3>
                        <small class="text-muted fs-8 mt-2 d-block">Trung bình {{ $thoiGianTrungBinhBuoi }} phút / buổi tập</small>
                    </div>
                </div>

                <!-- CARD 4: PHONG ĐỘ TẬP LUYỆN -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="report-kpi-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted fw-semibold fs-7">Đánh Giá Phong Độ</span>
                            <div class="kpi-icon-container text-warning">
                                <i class="bi bi-trophy-fill"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">{{ $tongBuoiTap >= 5 ? 'Xuất sắc' : ($tongBuoiTap >= 1 ? 'Khởi sắc' : 'Bắt đầu') }}</h3>
                        <div class="d-flex align-items-center gap-1 mt-2">
                            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 fs-8">
                                <i class="bi bi-arrow-up-right me-1"></i>Duy trì phong độ đều đặn
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 2. HÀNG HEATMAP & BIỂU ĐỒ TRỰC QUAN -->
            <div class="row g-4 mb-4">
                
                <!-- HEATMAP TẦN SUẤT TẬP TRONG THÁNG -->
                <div class="col-12 col-lg-5 col-xl-4">
                    <div class="report-heatmap-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Heatmap Tháng {{ $currentMonth }}/{{ $currentYear }}</h6>
                                <p class="text-muted fs-8 mb-0">Tần suất và mức độ chăm chỉ tập luyện</p>
                            </div>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 fs-8 fw-semibold">
                                Tháng {{ $currentMonth }}
                            </span>
                        </div>

                        <!-- Lưới 7 Cột Thứ 2 -> Chủ Nhật -->
                        <div class="heatmap-day-labels">
                            <span>T2</span><span>T3</span><span>T4</span><span>T5</span><span>T6</span><span>T7</span><span>CN</span>
                        </div>

                        <div class="heatmap-grid mb-3">
                            @php
                                $daysInMonth = \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->daysInMonth;
                                $firstDayIso = \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->dayOfWeekIso; // 1 = T2 ... 7 = CN
                            @endphp

                            <!-- Ô trống đầu tháng -->
                            @for ($i = 1; $i < $firstDayIso; $i++)
                                <div class="heatmap-cell bg-transparent" style="cursor: default;"></div>
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
                        <div class="heatmap-legend">
                            <span>Ít</span>
                            <span class="heatmap-legend-cell heatmap-lvl-0" title="Chưa tập"></span>
                            <span class="heatmap-legend-cell heatmap-lvl-1" title="Dưới 30 phút"></span>
                            <span class="heatmap-legend-cell heatmap-lvl-2" title="30-59 phút"></span>
                            <span class="heatmap-legend-cell heatmap-lvl-3" title="Từ 60 phút trở lên"></span>
                            <span>Nhiều</span>
                        </div>
                    </div>
                </div>

                <!-- BIỂU ĐỒ TẦN SUẤT TẬP THEO THỨ TRONG TUẦN -->
                <div class="col-12 col-lg-7 col-xl-8">
                    <div class="report-chart-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Tần Suất Tập Theo Thứ Trong Tuần</h6>
                                <p class="text-muted fs-8 mb-0">Thống kê số buổi tập tích lũy từ Thứ 2 đến Chủ Nhật</p>
                            </div>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 fs-8 fw-semibold">Toàn thời gian</span>
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
                    <div class="report-chart-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Phân Bổ Nhóm Bài Tập</h6>
                                <p class="text-muted fs-8 mb-0">Tỉ lệ phân bố theo dạng bài rèn luyện</p>
                            </div>
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 fs-8 fw-semibold">{{ $tongBuoiTap }} Buổi</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-center" style="position: relative; height: 240px;">
                            <canvas id="workoutTypeChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- BẢNG NHẬT KÝ HOÀN THÀNH TẬP LUYỆN MỚI NHẤT -->
                <div class="col-12 col-lg-8">
                    <div class="report-table-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Nhật Ký Tập Luyện Mới Nhất</h6>
                                <p class="text-muted fs-8 mb-0">Lịch sử ghi nhận các buổi tập đã hoàn thành</p>
                            </div>
                            <a href="{{ route('tap-luyen.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-2xs">
                                <i class="bi bi-calendar-check me-1"></i>Quản lý buổi tập
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table report-table align-middle">
                                <thead>
                                    <tr>
                                        <th class="ps-3">Thời Gian</th>
                                        <th>Tên Buổi Tập</th>
                                        <th class="text-center">Thời Lượng</th>
                                        <th class="text-end pe-3">Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lichSuRecords as $ls)
                                        @php
                                            $startedAt = \Carbon\Carbon::parse($ls->thoi_gian_bat_dau);
                                        @endphp
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-bold text-dark fs-7">{{ $startedAt->format('d/m/Y') }}</div>
                                                <div class="text-muted fs-8">{{ $startedAt->format('H:i') }}</div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark fs-7">{{ $ls->buoiTap ? $ls->buoiTap->ten_buoi_tap : ($ls->ghi_chu ?: 'Tập tự do') }}</div>
                                                <div class="text-muted fs-8 text-truncate" style="max-width: 250px;">{{ $ls->ghi_chu }}</div>
                                            </td>
                                            <td class="text-center">
                                                <span class="report-duration-pill">
                                                    <i class="bi bi-clock-fill text-primary"></i>
                                                    <span>{{ $ls->tong_thoi_luong ?: 0 }} phút</span>
                                                </span>
                                            </td>
                                            <td class="text-end pe-3">
                                                <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 fs-8 fw-semibold">
                                                    <i class="bi bi-check-circle-fill me-1"></i>Hoàn thành
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                                    <div class="kpi-icon-container mb-2" style="width: 52px; height: 52px; font-size: 1.5rem;">
                                                        <i class="bi bi-clipboard2-x text-muted"></i>
                                                    </div>
                                                    <span class="fw-semibold fs-7 text-dark mb-1">Chưa có nhật ký tập luyện nào</span>
                                                    <p class="fs-8 text-muted mb-3">Hãy hoàn thành buổi tập đầu tiên để ghi nhận số liệu</p>
                                                    <a href="{{ route('tap-luyen.index') }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-xs">
                                                        <i class="bi bi-play-circle me-1"></i>Bắt đầu tập ngay
                                                    </a>
                                                </div>
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
            // Xác định màu chữ và đường lưới theo theme
            const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            const textColor = isDarkMode ? '#94a3b8' : '#64748b';
            const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.06)' : 'rgba(148, 163, 184, 0.12)';

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
                            backgroundColor: '#6366f1',
                            hoverBackgroundColor: '#4f46e5',
                            borderRadius: 8,
                            barThickness: 26,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleFont: { family: "'Plus Jakarta Sans', sans-serif", weight: 'bold' },
                                bodyFont: { family: "'Plus Jakarta Sans', sans-serif" },
                                padding: 10,
                                cornerRadius: 8,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    color: textColor,
                                    font: { family: "'Plus Jakarta Sans', sans-serif" }
                                },
                                grid: { color: gridColor }
                            },
                            x: {
                                ticks: {
                                    color: textColor,
                                    font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600' }
                                },
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
                            backgroundColor: ['#6366f1', '#3b82f6', '#10b981', '#f59e0b', '#94a3b8'],
                            hoverOffset: 4,
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
                                labels: {
                                    boxWidth: 12,
                                    padding: 14,
                                    color: textColor,
                                    font: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: '500' }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleFont: { family: "'Plus Jakarta Sans', sans-serif", weight: 'bold' },
                                bodyFont: { family: "'Plus Jakarta Sans', sans-serif" },
                                padding: 10,
                                cornerRadius: 8,
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
    <!-- Floating Action Menu (Góc phải bên dưới) -->
    <x-floating-menu />
</body>
</html>
