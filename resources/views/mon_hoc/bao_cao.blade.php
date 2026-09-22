<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Báo Cáo Học Tập - Life Planner</title>

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

    <!-- CSS Tùy chỉnh Dashboard -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .kpi-card {
            transition: all 0.25s ease;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08) !important;
        }
        .gpa-hero-card {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff;
        }
        .gpa-badge-glow {
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
        }
        .table-custom tbody tr {
            transition: background-color 0.15s ease;
        }
        .table-custom tbody tr:hover {
            background-color: rgba(99, 102, 241, 0.03);
        }
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
                    <i class="bi bi-bar-chart-line-fill text-primary"></i> Báo Cáo Học Tập & GPA
                </h5>
            </div>

            <!-- Khu vực người dùng & Nút Thông báo -->
            <div class="header-actions d-flex align-items-center gap-3">
                <a href="{{ route('mon-hoc.index') }}" class="btn btn-outline-primary rounded-pill px-3 btn-sm fw-semibold">
                    <i class="bi bi-journal-text me-1"></i> Quản lý môn học
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

            <!-- THÔNG BÁO THÀNH CÔNG NẾU CÓ -->
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-xs mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- 1. HÀNG THỐNG KÊ KPI CARDS -->
            <div class="row g-3 mb-4">
                
                <!-- CARD HERO: ĐIỂM GPA TÍCH LŨY -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 rounded-4 p-4 gpa-hero-card shadow-sm h-100 position-relative overflow-hidden">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-white-50 fw-medium fs-7">Điểm GPA Tích Lũy</span>
                            <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-2.5 py-1 fs-8 fw-semibold gpa-badge-glow">
                                <i class="bi {{ $xepLoai['icon'] }} me-1"></i>{{ $xepLoai['label'] }}
                            </span>
                        </div>
                        <div class="d-flex align-items-baseline gap-2 mb-1">
                            <h2 class="fw-bold mb-0 text-white display-6">{{ number_format($gpa4, 2) }}</h2>
                            <span class="text-white-50 fs-6">/ 4.0</span>
                        </div>
                        <div class="text-white-50 fs-8 d-flex align-items-center gap-1">
                            <span>Thang điểm 10:</span>
                            <strong class="text-white">{{ number_format($gpa10, 2) }} / 10</strong>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: TỔNG TÍN CHỈ -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 rounded-4 p-4 bg-white shadow-sm h-100 kpi-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted fw-semibold fs-7">Tín Chỉ Tích Lũy</span>
                            <div class="p-2.5 rounded-circle bg-primary-subtle text-primary">
                                <i class="bi bi-journal-check fs-5"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">{{ $tinChiHoanThanh }} <span class="fs-6 text-muted font-normal">/ {{ $tongTinChi }} TC</span></h3>
                        <div class="progress rounded-pill bg-light mt-2" style="height: 6px;">
                            <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: {{ $tongTinChi > 0 ? round(($tinChiHoanThanh / $tongTinChi) * 100) : 0 }}%"></div>
                        </div>
                        <small class="text-muted fs-8 mt-2 d-block">Hoàn thành {{ $tongTinChi > 0 ? round(($tinChiHoanThanh / $tongTinChi) * 100) : 0 }}% tổng số tín chỉ đăng ký</small>
                    </div>
                </div>

                <!-- CARD 3: SỐ MÔN HỌC -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 rounded-4 p-4 bg-white shadow-sm h-100 kpi-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted fw-semibold fs-7">Môn Học Kỳ Này</span>
                            <div class="p-2.5 rounded-circle bg-info-subtle text-info">
                                <i class="bi bi-book fs-5"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">{{ $soMonDangHoc }} <span class="fs-6 text-muted font-normal">môn đang học</span></h3>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 fs-8">
                                <i class="bi bi-check-circle me-1"></i>{{ $soMonHoanThanh }} hoàn thành
                            </span>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 fs-8">
                                {{ $tongSoMon }} tổng số môn
                            </span>
                        </div>
                    </div>
                </div>

                <!-- CARD 4: TIẾN ĐỘ DEADLINE -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 rounded-4 p-4 bg-white shadow-sm h-100 kpi-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted fw-semibold fs-7">Bài Tập & Deadline</span>
                            <div class="p-2.5 rounded-circle bg-success-subtle text-success">
                                <i class="bi bi-check2-square fs-5"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">{{ $tyLeHoanThanhBaiTap }}% <span class="fs-6 text-muted font-normal">đã hoàn thành</span></h3>
                        <div class="progress rounded-pill bg-light mt-2" style="height: 6px;">
                            <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: {{ $tyLeHoanThanhBaiTap }}%"></div>
                        </div>
                        <small class="text-muted fs-8 mt-2 d-block">{{ $baiTapHoanThanh }} / {{ $tongBaiTap }} bài tập đã nộp đúng hạn</small>
                    </div>
                </div>

            </div>

            <!-- 2. HÀNG BIỂU ĐỒ TRỰC QUAN (CHARTS SECTION) -->
            <div class="row g-4 mb-4">
                
                <!-- BIỂU ĐỒ BAR: ĐIỂM SỐ THEO MÔN HỌC -->
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Kết Quả Điểm Số Theo Môn Học</h6>
                                <p class="text-muted fs-8 mb-0">Bảng so sánh điểm tổng kết (thang điểm 10) các môn học</p>
                            </div>
                            <span class="badge bg-light text-secondary rounded-pill border px-3 py-1.5 fs-8">Học Kỳ Hiện Tại</span>
                        </div>
                        <div style="position: relative; height: 280px; width: 100%;">
                            <canvas id="subjectGradesChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- BIỂU ĐỒ DOUGHNUT: PHÂN BỔ TRẠNG THÁI MÔN HỌC -->
                <div class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold text-dark mb-0">Trạng Thái Môn Học</h6>
                            <span class="badge bg-primary-subtle text-primary rounded-pill fs-8">{{ $tongSoMon }} Môn</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-center" style="position: relative; height: 220px;">
                            <canvas id="subjectStatusChart"></canvas>
                        </div>
                        <div class="mt-3 pt-2 border-top d-flex justify-content-around text-center">
                            <div>
                                <div class="fs-8 text-muted">Đang học</div>
                                <div class="fw-bold text-primary fs-6">{{ $soMonDangHoc }} môn</div>
                            </div>
                            <div class="border-end"></div>
                            <div>
                                <div class="fs-8 text-muted">Đã hoàn thành</div>
                                <div class="fw-bold text-success fs-6">{{ $soMonHoanThanh }} môn</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 3. BẢNG CHI TIẾT KẾT QUẢ MÔN HỌC -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Chi Tiết Kết Quả & Tiến Độ Môn Học</h6>
                        <p class="text-muted fs-8 mb-0">Danh sách môn học, số tín chỉ, điểm tổng kết và tiến độ hoàn thành nội dung.</p>
                    </div>
                    <a href="{{ route('mon-hoc.create') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-plus-lg me-1"></i>Thêm môn học
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-custom align-middle mb-0">
                        <thead class="bg-light rounded-3">
                            <tr>
                                <th class="border-0 text-secondary fs-8 uppercase ps-3 py-3">Mã Môn & Tên Môn Học</th>
                                <th class="border-0 text-secondary fs-8 uppercase py-3">Giảng Viên</th>
                                <th class="border-0 text-secondary fs-8 uppercase text-center py-3">Số TC</th>
                                <th class="border-0 text-secondary fs-8 uppercase py-3" style="width: 180px;">Tiến Độ HỌC</th>
                                <th class="border-0 text-secondary fs-8 uppercase text-center py-3">Điểm (Hệ 10)</th>
                                <th class="border-0 text-secondary fs-8 uppercase text-center py-3">Điểm Chữ / Hệ 4</th>
                                <th class="border-0 text-secondary fs-8 uppercase text-center py-3">Trạng Thái</th>
                                <th class="border-0 text-secondary fs-8 uppercase text-end pe-3 py-3">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monHocs as $m)
                                @php
                                    $score10 = (float) $m->diem_so;
                                    $letter = match (true) {
                                        $score10 >= 9.0 => ['letter' => 'A+', 'scale4' => '4.0', 'badge' => 'bg-success-subtle text-success'],
                                        $score10 >= 8.5 => ['letter' => 'A',  'scale4' => '4.0', 'badge' => 'bg-success-subtle text-success'],
                                        $score10 >= 8.0 => ['letter' => 'B+', 'scale4' => '3.5', 'badge' => 'bg-primary-subtle text-primary'],
                                        $score10 >= 7.0 => ['letter' => 'B',  'scale4' => '3.0', 'badge' => 'bg-primary-subtle text-primary'],
                                        $score10 >= 6.5 => ['letter' => 'C+', 'scale4' => '2.5', 'badge' => 'bg-info-subtle text-info'],
                                        $score10 >= 5.5 => ['letter' => 'C',  'scale4' => '2.0', 'badge' => 'bg-info-subtle text-info'],
                                        $score10 >= 5.0 => ['letter' => 'D+', 'scale4' => '1.5', 'badge' => 'bg-warning-subtle text-warning'],
                                        $score10 >= 4.0 => ['letter' => 'D',  'scale4' => '1.0', 'badge' => 'bg-warning-subtle text-warning'],
                                        default => ['letter' => 'F', 'scale4' => '0.0', 'badge' => 'bg-danger-subtle text-danger'],
                                    };
                                @endphp
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle flex-shrink-0" style="width: 12px; height: 12px; background-color: {{ $m->mau_sac ?: '#6366f1' }};"></div>
                                            <div>
                                                <a href="{{ route('mon-hoc.show', $m->id) }}" class="fw-bold text-dark text-decoration-none hover-primary">
                                                    {{ $m->ten_mon }}
                                                </a>
                                                <div class="text-muted fs-8">{{ $m->ma_mon }} • {{ $m->phong_hoc ?: 'Chưa xếp phòng' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fs-7 text-dark fw-medium">{{ $m->giang_vien ?: 'N/A' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 font-mono fs-7">{{ $m->so_tin_chi }} TC</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1 rounded-pill bg-light" style="height: 6px;">
                                                <div class="progress-bar rounded-pill" role="progressbar" style="width: {{ $m->tien_do ?: 0 }}%; background-color: {{ $m->mau_sac ?: '#6366f1' }};"></div>
                                            </div>
                                            <span class="fs-8 fw-semibold text-secondary">{{ $m->tien_do ?: 0 }}%</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($m->diem_so !== null)
                                            <span class="fw-bold fs-6 text-dark">{{ number_format($m->diem_so, 1) }}</span>
                                        @else
                                            <span class="text-muted fs-8">Chưa có</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($m->diem_so !== null)
                                            <span class="badge {{ $letter['badge'] }} rounded-pill px-2.5 py-1 font-semibold fs-7">
                                                {{ $letter['letter'] }} ({{ $letter['scale4'] }})
                                            </span>
                                        @else
                                            <span class="text-muted fs-8">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($m->trang_thai === 'da_hoan_thanh')
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 fs-8">
                                                <i class="bi bi-check-circle-fill me-1"></i>Đã xong
                                            </span>
                                        @elseif($m->trang_thai === 'tam_dung')
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 fs-8">Tạm dừng</span>
                                        @else
                                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 fs-8">
                                                <i class="bi bi-book-half me-1"></i>Đang học
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('mon-hoc.show', $m->id) }}" class="btn btn-light text-primary rounded-circle me-1" title="Chi tiết môn học">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            <a href="{{ route('mon-hoc.edit', $m->id) }}" class="btn btn-light text-secondary rounded-circle me-1" title="Chỉnh sửa">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-journal-x display-6 mb-3 text-secondary d-block"></i>
                                        Chưa có môn học nào. Nhấn "Thêm môn học" để khởi tạo báo cáo.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chart.js Rendering Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data từ PHP
            const subjectsData = @json($monHocs);
            
            const labels = subjectsData.map(s => s.ma_mon || s.ten_mon);
            const grades = subjectsData.map(s => s.diem_so !== null ? parseFloat(s.diem_so) : 0);
            const colors = subjectsData.map(s => s.mau_sac || '#6366f1');

            // 1. Render Biểu đồ Bar Điểm số
            const ctxGrades = document.getElementById('subjectGradesChart')?.getContext('2d');
            if (ctxGrades) {
                new Chart(ctxGrades, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Điểm Tổng Kết (Thang 10)',
                            data: grades,
                            backgroundColor: colors,
                            borderRadius: 8,
                            barThickness: 28,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `Điểm: ${context.parsed.y} / 10`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 10,
                                ticks: { stepSize: 2 }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            // 2. Render Biểu đồ Doughnut Trạng thái
            const ctxStatus = document.getElementById('subjectStatusChart')?.getContext('2d');
            if (ctxStatus) {
                const activeCount = {{ $soMonDangHoc }};
                const completedCount = {{ $soMonHoanThanh }};

                new Chart(ctxStatus, {
                    type: 'doughnut',
                    data: {
                        labels: ['Đang học', 'Đã hoàn thành'],
                        datasets: [{
                            data: [activeCount, completedCount],
                            backgroundColor: ['#6366f1', '#10b981'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }

            // Sidebar Offcanvas Toggle Mobile
            document.getElementById('sidebarToggle')?.addEventListener('click', function() {
                document.getElementById('sidebar')?.classList.toggle('show');
            });
        });
    </script>
</body>
</html>
