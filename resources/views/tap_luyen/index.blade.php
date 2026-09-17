<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theo Dõi Thể Chất - Life Planner</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Font: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS Dashboard dùng chung của hệ thống -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <!-- CSS riêng của Module Thể Chất -->
    <link rel="stylesheet" href="{{ asset('css/tap-luyen.css') }}">
</head>

<body>

    <!-- SIDEBAR BÊN TRÁI DÙNG CHUNG -->
    @include('layouts.sidebar')

    <!-- MAIN WRAPPER (KHUNG NỘI DUNG CHÍNH 100VH) -->
    <div class="main-wrapper">

        <!-- TOP HEADER THANH CÔNG CỤ -->
        <header class="top-header">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none p-1 px-2 border" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h5 class="fw-bold text-dark mb-0">Theo dõi Thể chất</h5>
            </div>

            <!-- Ô tìm kiếm nhanh -->
            <div class="search-box d-none d-md-block">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" placeholder="Tìm kiếm bài tập, lịch trình...">
            </div>

            <!-- Khu vực người dùng & thông báo -->
            <div class="header-actions">
                <div class="notification-btn" title="Thông báo">
                    <i class="bi bi-bell-fill"></i>
                    <span class="badge-dot"></span>
                </div>

                <div class="user-profile dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ mb_substr(Auth::user()->ho_ten ?? 'User', 0, 1) }}
                        </div>
                        <div class="d-none d-md-block text-start">
                            <div class="fw-bold text-dark fs-6 leading-tight">{{ Auth::user()->ho_ten ?? 'User' }}</div>
                            <small class="text-muted fs-7">{{ Auth::user()->email ?? 'user@lifeplanner.local' }}</small>
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

        <!-- CONTENT BODY (CUỘN ĐỘC LẬP) -->
        <main class="content-body">

            <!-- Tiêu đề trang & Hành động chính -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Theo dõi Thể chất & Sức khỏe</h4>
                    <p class="text-muted mb-0">Theo dõi quá trình tập luyện và sức khỏe của bạn mỗi ngày.</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#editWorkoutModal">
                        <i class="bi bi-pencil-square"></i>
                        <span class="fw-semibold">Chỉnh sửa buổi tập</span>
                    </button>
                </div>
            </div>

            <!-- 4 Thẻ KPI Thống kê (Đồng bộ với Dashboard) -->
            <div class="row g-3 mb-4">
                <!-- 1. Thời gian tập hôm nay -->
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 kpi-card-hover">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-primary-subtle text-primary rounded-3">
                                <i class="bi bi-stopwatch-fill fs-3"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold">Thời gian tập hôm nay</small>
                                <h4 class="fw-bold text-dark mb-0">{{ $chiSoSucKhoe['thoi_gian_tap_hom_nay'] }} <span class="fs-6 fw-normal text-muted">phút</span></h4>
                                <small class="text-muted fs-7">Tổng thời gian</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Buổi tập trong tuần -->
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 kpi-card-hover">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-danger-subtle text-danger rounded-3">
                                <i class="bi bi-fire fs-3"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold">Buổi tập trong tuần</small>
                                <h4 class="fw-bold text-dark mb-0">{{ $buoiTapTuanNay }} <span class="fs-6 fw-normal text-muted">buổi</span></h4>
                                <small class="text-muted fs-7">Từ Thứ 2 đến nay</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Giấc ngủ trung bình -->
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 kpi-card-hover">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-info-subtle text-info rounded-3">
                                <i class="bi bi-moon-stars-fill fs-3"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold">Giấc ngủ trung bình</small>
                                <h4 class="fw-bold text-dark mb-0">{{ $chiSoSucKhoe['giac_ngu'] }}</h4>
                                <small class="text-muted fs-7">Mỗi ngày</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Chuỗi hoạt động -->
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 kpi-card-hover">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-warning-subtle text-warning rounded-3">
                                <i class="bi bi-lightning-charge-fill fs-3"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold">Chuỗi hoạt động</small>
                                <h4 class="fw-bold text-dark mb-0">{{ $chiSoSucKhoe['chuoi_ngay'] }} <span class="fs-6 fw-normal text-muted">ngày</span></h4>
                                <small class="text-muted fs-7">Liên tục rèn luyện</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Khu vực chính: Trình tập luyện & Cột bên phải (Lịch + Hoạt động) -->
            <div class="row g-4 mb-4">

                <!-- CỘT TRÁI: TRÌNH TẬP LUYỆN (WORKOUT PLAYER) -->
                <div class="col-12 col-xl-8">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100 d-flex flex-column">

                        <!-- Header card & Tabs phân loại -->
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">Buổi tập hôm nay</h5>
                                <span class="text-muted small" id="workout-title-display">Push — 3 ngực, 2 tay sau, 1 vai</span>
                            </div>

                            <!-- Tabs chọn loại buổi tập (Push / Pull / Legs / Khác) -->
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="workout-type-tab workout-tab active" data-type="Push">Push</button>
                                <button type="button" class="workout-type-tab workout-tab" data-type="Pull">Pull</button>
                                <button type="button" class="workout-type-tab workout-tab" data-type="Legs">Legs</button>
                                <button type="button" class="workout-type-tab workout-tab" data-type="Other">Khác</button>
                            </div>
                        </div>

                        <!-- Khung tập luyện trung tâm (Workout Box) -->
                        <div class="workout-timer-box p-4 p-md-5 flex-grow-1 d-flex flex-column align-items-center justify-content-center text-center">

                            <!-- Trạng thái & Thứ tự bài tập -->
                            <div class="d-inline-flex align-items-center gap-2 bg-white px-3 py-1 rounded-pill shadow-sm border mb-3">
                                <span class="badge bg-primary rounded-circle text-white d-flex align-items-center justify-content-center"
                                    style="width: 24px; height: 24px;" id="active-ex-number">1</span>
                                <span class="fw-bold text-primary small">Đang tập luyện</span>
                            </div>

                            <!-- Đồng hồ đếm thời gian tập -->
                            <div class="timer-display mb-2" id="workout-timer">00:00:00</div>

                            <!-- Tên bài tập hiện tại -->
                            <h2 class="fw-bold text-dark mb-1" id="active-ex-name" style="font-size: clamp(1.4rem, 3.5vw, 2rem);">Barbell Bench Press</h2>

                            <!-- Thông số Sets x Reps -->
                            <div class="text-muted fw-semibold fs-5 mb-4" id="active-ex-set-rep">4 x 8-10</div>

                            <!-- Hai nút hành động chính -->
                            <div class="row g-2 g-sm-3 w-100 mb-3 justify-content-center" style="max-width: 500px;">
                                <div class="col-12 col-sm-6">
                                    <button class="btn btn-outline-secondary w-100 py-3 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2" id="next-ex-btn">
                                        <i class="bi bi-skip-forward-fill"></i>
                                        <span>Bài tiếp theo</span>
                                    </button>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <button class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" id="finish-ex-btn">
                                        <i class="bi bi-check2-circle fs-5"></i>
                                        <span>Hoàn thành</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Nút phụ điều khiển thời gian & thêm bài -->
                            <div class="d-flex flex-column flex-sm-row gap-2 w-100 justify-content-center" style="max-width: 500px;">
                                <button class="btn btn-light border rounded-pill py-2 px-3 fw-semibold text-primary d-flex align-items-center justify-content-center gap-2" id="stop-timer-btn">
                                    <i class="bi bi-play-fill fs-5"></i>
                                    <span>Bắt đầu tập</span>
                                </button>
                                <button class="btn btn-white border border-dashed rounded-pill py-2 px-3 fw-semibold text-secondary d-flex align-items-center justify-content-center gap-2"
                                    data-bs-toggle="modal" data-bs-target="#editWorkoutModal">
                                    <i class="bi bi-plus-circle"></i>
                                    <span>Thêm bài tập</span>
                                </button>
                            </div>

                        </div>

                    </div>
                </div>

                <!-- CỘT PHẢI: LỊCH TẬP & HOẠT ĐỘNG GẦN ĐÂY -->
                <div class="col-12 col-xl-4 d-flex flex-column gap-4">

                    <!-- CARD: LỊCH TẬP TRONG THÁNG (HEATMAP) -->
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Lịch tập trong tháng</h6>
                                <small class="text-muted fw-medium" id="calendar-month-title">Tháng 10, 2026</small>
                            </div>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-light border rounded-circle" id="prev-month-btn" title="Tháng trước">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <button class="btn btn-sm btn-light border rounded-circle" id="next-month-btn" title="Tháng sau">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Grid 7 ngày trong tuần -->
                        <div class="calendar-heatmap-grid" id="calendar-grid">
                            <div class="text-center text-muted fw-semibold small">T2</div>
                            <div class="text-center text-muted fw-semibold small">T3</div>
                            <div class="text-center text-muted fw-semibold small">T4</div>
                            <div class="text-center text-muted fw-semibold small">T5</div>
                            <div class="text-center text-muted fw-semibold small">T6</div>
                            <div class="text-center text-muted fw-semibold small">T7</div>
                            <div class="text-center text-muted fw-semibold small">CN</div>
                        </div>

                        <!-- Chú thích mức độ tập (Legend) -->
                        <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top">
                            <span class="text-muted small">Ít</span>
                            <div class="d-flex gap-1">
                                <div class="rounded-1" style="width: 12px; height: 12px; background-color: #f1f5f9;" title="Nghỉ"></div>
                                <div class="rounded-1" style="width: 12px; height: 12px; background-color: #c7d2fe;" title="Nhẹ"></div>
                                <div class="rounded-1" style="width: 12px; height: 12px; background-color: #818cf8;" title="Vừa"></div>
                                <div class="rounded-1" style="width: 12px; height: 12px; background-color: #6366f1;" title="Nhiều"></div>
                                <div class="rounded-1" style="width: 12px; height: 12px; background-color: #4338ca;" title="Rất nhiều"></div>
                            </div>
                            <span class="text-muted small">Nhiều</span>
                        </div>
                    </div>

                    <!-- CARD: HOẠT ĐỘNG GẦN ĐÂY -->
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0">Hoạt động gần đây</h6>
                            <span class="badge bg-light text-primary border rounded-pill px-3 py-1">Lịch sử</span>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            @foreach($hoatDongGanDay as $hoatDong)
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border-bottom pb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="metric-icon-box bg-{{ $hoatDong['color'] }}-subtle text-{{ $hoatDong['color'] }}">
                                        <i class="bi {{ $hoatDong['icon'] }}"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-6">{{ $hoatDong['loai'] }}</div>
                                        <small class="text-muted">{{ $hoatDong['thoi_gian'] }} • {{ $hoatDong['so_bai'] }}</small>
                                    </div>
                                </div>
                                <div>
                                    <span class="badge bg-light text-dark border rounded-pill">{{ $hoatDong['thoi_diem'] }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>

            </div>

            <!-- CARD: CÁC CHỈ SỐ SỨC KHỎE (HEALTH METRICS) -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-heart-pulse-fill text-danger me-2"></i>Chỉ số sức khỏe
                    </h6>
                    <small class="text-muted">Cập nhật hôm nay</small>
                </div>

                <div class="row g-3">
                    <!-- 1. Nhịp tim -->
                    <div class="col-6 col-lg-3">
                        <div class="p-3 bg-light rounded-4 d-flex align-items-center gap-3">
                            <div class="metric-icon-box bg-danger-subtle text-danger">
                                <i class="bi bi-heart-pulse"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold">Nhịp tim trung bình</small>
                                <div class="fw-bold text-dark fs-5">{{ $chiSoSucKhoe['nhip_tim'] }} <span class="fs-7 fw-normal text-muted">bpm</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Cân nặng -->
                    <div class="col-6 col-lg-3">
                        <div class="p-3 bg-light rounded-4 d-flex align-items-center gap-3">
                            <div class="metric-icon-box bg-success-subtle text-success">
                                <i class="bi bi-speedometer2"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold">Cân nặng</small>
                                <div class="fw-bold text-dark fs-5">{{ $chiSoSucKhoe['can_nang'] }} <span class="fs-7 fw-normal text-muted">kg</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Tỷ lệ mỡ -->
                    <div class="col-6 col-lg-3">
                        <div class="p-3 bg-light rounded-4 d-flex align-items-center gap-3">
                            <div class="metric-icon-box bg-warning-subtle text-warning">
                                <i class="bi bi-person-standing"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold">Tỷ lệ mỡ</small>
                                <div class="fw-bold text-dark fs-5">{{ $chiSoSucKhoe['ty_le_mo'] }} <span class="fs-7 fw-normal text-muted">%</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Lượng nước -->
                    <div class="col-6 col-lg-3">
                        <div class="p-3 bg-light rounded-4 d-flex align-items-center gap-3">
                            <div class="metric-icon-box bg-info-subtle text-info">
                                <i class="bi bi-droplet-half"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold">Lượng nước</small>
                                <div class="fw-bold text-dark fs-5">{{ $chiSoSucKhoe['luong_nuoc'] }} <span class="fs-7 fw-normal text-muted">lít</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- MODAL CHỈNH SỬA / THÊM BÀI TẬP (MODAL BOOTSTRAP 5) -->
    <div class="modal fade" id="editWorkoutModal" tabindex="-1" aria-labelledby="editWorkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="editWorkoutModalLabel">
                        <i class="bi bi-sliders me-2 text-primary"></i>Chỉnh sửa buổi tập
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-3">
                    <!-- Chọn loại buổi tập và nhập tiêu đề -->
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-5">
                            <label class="form-label fw-semibold small text-muted">Loại buổi tập</label>
                            <select class="form-select rounded-3" id="modal-workout-type">
                                <option value="Push">Push (Ngực, Vai, Tay sau)</option>
                                <option value="Pull">Pull (Lưng, Tay trước)</option>
                                <option value="Legs">Legs (Chân, Mông)</option>
                                <option value="Other">Khác (Cardio, Core)</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-7">
                            <label class="form-label fw-semibold small text-muted">Tiêu đề buổi tập</label>
                            <input type="text" class="form-control rounded-3" id="modal-workout-title" placeholder="VD: Push — 3 ngực, 2 tay sau, 1 vai">
                        </div>
                    </div>

                    <!-- Thêm bài tập mới -->
                    <div class="card border-0 bg-light rounded-4 p-3 mb-3">
                        <h6 class="fw-bold text-dark mb-2 small">
                            <i class="bi bi-plus-circle-fill text-primary me-1"></i>Thêm bài tập mới
                        </h6>
                        <div class="row g-2">
                            <div class="col-12 col-md-5">
                                <input type="text" class="form-control form-control-sm rounded-3" id="new-ex-name" placeholder="Tên bài tập (VD: Incline Press)">
                            </div>
                            <div class="col-6 col-md-3">
                                <input type="number" class="form-control form-control-sm rounded-3" id="new-ex-sets" placeholder="Số sets (VD: 4)">
                            </div>
                            <div class="col-6 col-md-2">
                                <input type="text" class="form-control form-control-sm rounded-3" id="new-ex-reps" placeholder="Reps (VD: 8-10)">
                            </div>
                            <div class="col-12 col-md-2">
                                <button type="button" class="btn btn-primary btn-sm rounded-3 w-100 fw-semibold" id="add-exercise-btn">
                                    <i class="bi bi-plus-lg me-1"></i>Thêm
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Danh sách bài tập trong buổi -->
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-dark mb-0 small">Danh sách bài tập hiện tại</h6>
                            <span class="badge bg-primary-subtle text-primary rounded-pill small" id="exercise-count-badge">0 bài tập</span>
                        </div>

                        <div class="d-flex flex-column gap-2" id="modal-exercise-list" style="max-height: 260px; overflow-y: auto;">
                            <!-- Render tự động bởi JS -->
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" id="save-workout-btn">
                        <i class="bi bi-check-lg me-1"></i>Lưu thay đổi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script Chức năng Thể Chất / Tập Luyện -->
    <script src="{{ asset('js/tap-luyen.js') }}"></script>
</body>

</html>
