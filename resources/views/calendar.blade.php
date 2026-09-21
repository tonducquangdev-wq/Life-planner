<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Life Planner - Lịch Cá Nhân (Calendar First)</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Font: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS Tùy chỉnh Dashboard & Calendar -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/calendar.css') }}">
</head>
<body>

    <!-- ==========================================================================
         1. SIDEBAR BÊN TRÁI (LEFT SIDEBAR)
         ========================================================================== -->
    @include('layouts.sidebar')

    <!-- ==========================================================================
         MAIN WRAPPER (HEADER & NỘI DUNG CHÍNH)
         ========================================================================== -->
    <div class="main-wrapper">

        <!-- ==========================================================================
             2. HEADER THANH CÔNG CỤ TRÊN CÙNG
             ========================================================================== -->
        <header class="top-header">
            <div class="d-flex align-items-center gap-3">
                <!-- Nút bật/tắt Sidebar trên mobile -->
                <button class="btn btn-light d-lg-none p-1 px-2 border" id="sidebarToggle" type="button">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <!-- Thanh tìm kiếm nhanh -->
                <div class="search-box d-none d-md-block">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" placeholder="Tìm kiếm sự kiện, deadline, lịch trình...">
                </div>
            </div>

            <!-- Khu vực người dùng & thông báo -->
            <div class="header-actions">
                <!-- Nút thông báo -->
                <div class="notification-btn" title="Thông báo">
                    <i class="bi bi-bell-fill"></i>
                    <span class="badge-dot"></span>
                </div>

                <!-- Avatar người dùng & dropdown -->
                <div class="user-profile dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown" aria-expanded="false">
                        @if(Auth::check() && !empty(Auth::user()->avatar_url))
                            <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="user-avatar rounded-circle object-fit-cover" style="width: 40px; height: 40px;">
                        @else
                            <div class="user-avatar">
                                {{ Auth::check() ? Auth::user()->initials : 'U' }}
                            </div>
                        @endif
                        <div class="d-none d-md-block text-start">
                            <div class="fw-bold text-dark fs-6 leading-tight">{{ Auth::user()->ho_ten ?? 'Tôn Đức Quang' }}</div>
                            <small class="text-muted fs-7">{{ Auth::user()->email ?? 'quang@gmail.com' }}</small>
                        </div>
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

        <!-- ==========================================================================
             3. NỘI DUNG CHÍNH (MAIN CONTENT - CALENDAR FIRST)
             ========================================================================== -->
        <main class="content-body">
            
            <!-- Tiêu đề trang & Nút hành động -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Lịch cá nhân & Lịch trình 🗓️</h4>
                    <p class="text-muted mb-0 fs-7">Màn hình chính tập trung quản lý toàn bộ lịch học, lịch tập, deadline và sự kiện của bạn.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary rounded-pill px-3 py-2 fs-7 fw-semibold bg-white shadow-sm">
                        <i class="bi bi-calendar-event me-1"></i>Hôm nay
                    </button>
                    <button class="btn btn-primary rounded-pill px-3 py-2 shadow-sm d-flex align-items-center gap-2 fs-7 fw-semibold">
                        <i class="bi bi-plus-lg"></i>
                        <span>Tạo sự kiện mới</span>
                    </button>
                </div>
            </div>

            <!-- BỐ CỤC 2 CỘT (CỘT TRÁI 75% | CỘT PHẢI 25%) -->
            <div class="row g-4">
                
                <!-- ==========================================================================
                     CỘT TRÁI (75%): LỊCH THÁNG LỚN (MONTHLY CALENDAR GRID)
                     ========================================================================== -->
                <div class="col-lg-8 col-xl-9">
                    <div class="custom-card mb-0">
                        <!-- HEADER CARD LỊCH -->
                        <div class="card-header-custom flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <h5 class="fw-bold text-dark mb-0 fs-5 d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar3 text-primary"></i>
                                    <span>{{ $currentMonthYear ?? 'Tháng 9, 2026' }}</span>
                                </h5>
                                <div class="btn-group border rounded-pill p-1 bg-light">
                                    <button class="btn btn-sm btn-white rounded-circle shadow-none py-0 px-2" title="Tháng trước">
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                    <button class="btn btn-sm btn-white rounded-circle shadow-none py-0 px-2" title="Tháng sau">
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- CHÚ THÍCH MÀU & NÚT LỌC SỰ KIỆN (LEGEND FILTER BUTTONS) -->
                            <div class="d-flex flex-wrap align-items-center gap-2" id="calendarFilterGroup">
                                <button type="button" class="legend-btn active" data-filter="all">
                                    <i class="bi bi-grid-fill me-1 fs-8"></i>Tất cả
                                </button>
                                <button type="button" class="legend-btn hoc-tap" data-filter="hoc-tap">
                                    <span class="legend-dot"></span>Học tập
                                </button>
                                <button type="button" class="legend-btn deadline" data-filter="deadline">
                                    <span class="legend-dot"></span>Deadline
                                </button>
                                <button type="button" class="legend-btn tap-luyen" data-filter="tap-luyen">
                                    <span class="legend-dot"></span>Tập luyện
                                </button>
                                <button type="button" class="legend-btn ca-nhan" data-filter="ca-nhan">
                                    <span class="legend-dot"></span>Cá nhân
                                </button>
                            </div>
                        </div>

                        <!-- LƯỚI LỊCH THÁNG LỚN (GRID 7 CỘT) -->
                        <div class="card-body p-3">
                            <div class="calendar-grid-container">
                                <!-- Hàng 0: Tiêu đề các thứ trong tuần (CN -> T7) -->
                                <div class="calendar-header-day weekend">CN</div>
                                <div class="calendar-header-day">T2</div>
                                <div class="calendar-header-day">T3</div>
                                <div class="calendar-header-day">T4</div>
                                <div class="calendar-header-day">T5</div>
                                <div class="calendar-header-day">T6</div>
                                <div class="calendar-header-day weekend">T7</div>

                                <!-- Ô các ngày trong tháng -->
                                @foreach($calendarDays ?? [] as $dayData)
                                    <div class="calendar-day-cell {{ !$dayData['is_current_month'] ? 'other-month' : '' }} {{ $dayData['is_today'] ? 'is-today' : '' }}">
                                        <!-- Tiêu đề ngày -->
                                        <div class="day-header">
                                            <span class="day-number">{{ $dayData['day'] }}</span>
                                            @if($dayData['is_today'])
                                                <span class="today-tag">Hôm nay</span>
                                            @endif
                                        </div>

                                        <!-- Danh sách sự kiện trong ô -->
                                        <div class="event-pill-list" data-day-events>
                                            @foreach($dayData['events'] as $evt)
                                                <div class="event-pill {{ $evt['type'] }}" data-type="{{ $evt['type'] }}" title="{{ $evt['title'] }} ({{ $evt['time'] }})">
                                                    @if($evt['type'] === 'hoc-tap')
                                                        <i class="bi bi-book-fill fs-8"></i>
                                                    @elseif($evt['type'] === 'deadline')
                                                        <i class="bi bi-exclamation-triangle-fill fs-8"></i>
                                                    @elseif($evt['type'] === 'tap-luyen')
                                                        <i class="bi bi-activity fs-8"></i>
                                                    @else
                                                        <i class="bi bi-person-fill fs-8"></i>
                                                    @endif
                                                    <span>{{ $evt['title'] }}</span>
                                                </div>
                                            @endforeach

                                            <div class="event-more-pill d-none"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==========================================================================
                     CỘT PHẢI (25%): 3 CARDS THÔNG TIN PHỤ
                     ========================================================================== -->
                <div class="col-lg-4 col-xl-3">
                    
                    <!-- CARD 1: LỊCH TRÌNH HÔM NAY -->
                    <div class="side-card">
                        <div class="side-card-header">
                            <h6 class="side-card-title">
                                <i class="bi bi-clock-history text-primary"></i>
                                <span>Lịch trình hôm nay</span>
                            </h6>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 fs-7">3</span>
                        </div>
                        <div class="card-body p-3">
                            @forelse($lichTrinhHomNay ?? [] as $item)
                                @php
                                    $typeSlug = 'ca-nhan';
                                    if (($item['badge'] ?? '') === 'Học tập') $typeSlug = 'hoc-tap';
                                    elseif (($item['badge'] ?? '') === 'Tập luyện') $typeSlug = 'tap-luyen';
                                    elseif (($item['badge'] ?? '') === 'Deadline') $typeSlug = 'deadline';
                                @endphp
                                <div class="schedule-item" data-type="{{ $typeSlug }}">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-bold text-dark fs-7">{{ $item['thoi_gian'] }}</span>
                                        <span class="badge {{ $item['badge_class'] }} rounded-pill px-2 py-0 fs-8">{{ $item['badge'] }}</span>
                                    </div>
                                    <div class="fw-semibold text-dark fs-6 mb-1">{{ $item['tieu_de'] }}</div>
                                    <small class="text-muted fs-7"><i class="bi bi-geo-alt me-1"></i>{{ $item['dia_diem'] }}</small>
                                </div>
                            @empty
                                <div class="text-center py-3 text-muted fs-7">
                                    Không có lịch trình hôm nay.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- CARD 2: SẮP ĐẾN HẠN -->
                    <div class="side-card">
                        <div class="side-card-header">
                            <h6 class="side-card-title">
                                <i class="bi bi-hourglass-split text-warning"></i>
                                <span>Sắp đến hạn</span>
                            </h6>
                            <a href="#" class="text-decoration-none small text-primary fw-semibold fs-7">Xem hết</a>
                        </div>
                        <div class="card-body p-3">
                            @forelse($sapDenHan ?? [] as $dl)
                                <div class="deadline-item">
                                    <div>
                                        <div class="fw-bold text-dark fs-7 mb-1">{{ $dl['tieu_de'] }}</div>
                                        <small class="text-muted d-block fs-8"><i class="bi bi-journal-text me-1"></i>{{ $dl['mon_hoc'] }}</small>
                                    </div>
                                    <span class="badge {{ $dl['badge_class'] }} rounded-pill px-2 py-1 fs-8 fw-bold">
                                        {{ $dl['con_lai'] }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center py-3 text-muted fs-7">
                                    Không có deadline sắp tới.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- CARD 3: THỐNG KÊ NHANH -->
                    <div class="side-card">
                        <div class="side-card-header">
                            <h6 class="side-card-title">
                                <i class="bi bi-bar-chart-fill text-info"></i>
                                <span>Thống kê nhanh</span>
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="quick-stat-row">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-book-half text-primary fs-5"></i>
                                    <span class="fs-7 text-secondary">Môn học tuần này</span>
                                </div>
                                <span class="fw-bold text-dark fs-6">{{ $thongKeNhanh['mon_hoc_tuan_nay'] ?? 5 }}</span>
                            </div>

                            <div class="quick-stat-row">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-activity text-success fs-5"></i>
                                    <span class="fs-7 text-secondary">Buổi tập tuần này</span>
                                </div>
                                <span class="fw-bold text-dark fs-6">{{ $thongKeNhanh['buoi_tap_tuan_nay'] ?? 4 }}</span>
                            </div>

                            <div class="quick-stat-row">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                                    <span class="fs-7 text-secondary">Deadline chưa nộp</span>
                                </div>
                                <span class="fw-bold text-dark fs-6">{{ $thongKeNhanh['deadline_chua_nop'] ?? 3 }}</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script tương tác giao diện (Bật/tắt Sidebar trên mobile & Lọc đa sự kiện theo Nút) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Toggle Sidebar Mobile
            document.getElementById('sidebarToggle')?.addEventListener('click', function() {
                document.getElementById('sidebar')?.classList.toggle('show');
            });

            // 2. Bộ lọc sự kiện Lịch theo Nút đa lựa chọn (Multi-select Legend Filter Buttons)
            const allBtn = document.querySelector('#calendarFilterGroup .legend-btn[data-filter="all"]');
            const categoryButtons = document.querySelectorAll('#calendarFilterGroup .legend-btn:not([data-filter="all"])');
            const filterContainer = document.getElementById('calendarFilterGroup');
            const dayCells = document.querySelectorAll('[data-day-events]');
            const scheduleItems = document.querySelectorAll('.schedule-item[data-type]');

            // Set lưu trữ các loại sự kiện đang được chọn
            const selectedCategories = new Set();
            const allCategoryTypes = Array.from(categoryButtons).map(btn => btn.getAttribute('data-filter'));

            function updateDayCellEvents(cell) {
                const events = cell.querySelectorAll('.event-pill');
                const morePill = cell.querySelector('.event-more-pill');
                let totalMatching = 0;

                events.forEach(evt => {
                    const evtType = evt.getAttribute('data-type');
                    // Khi không chọn nút nào hoặc đã chọn tất cả => hiển thị toàn bộ
                    const matches = (selectedCategories.size === 0 || selectedCategories.has(evtType));

                    if (matches) {
                        totalMatching++;
                        if (totalMatching <= 3) {
                            evt.style.display = 'flex';
                        } else {
                            evt.style.display = 'none';
                        }
                    } else {
                        evt.style.display = 'none';
                    }
                });

                if (morePill) {
                    if (totalMatching > 3) {
                        morePill.textContent = `+${totalMatching - 3} khác`;
                        morePill.classList.remove('d-none');
                        morePill.style.display = 'block';
                    } else {
                        morePill.classList.add('d-none');
                        morePill.style.display = 'none';
                    }
                }
            }

            function applyFilters() {
                const isFiltering = selectedCategories.size > 0 && selectedCategories.size < allCategoryTypes.length;

                // Nút "Tất cả" sáng khi không chọn lọc riêng hoặc đã chọn tất cả
                if (selectedCategories.size === 0 || selectedCategories.size === allCategoryTypes.length) {
                    allBtn?.classList.add('active');
                    filterContainer?.classList.remove('filter-active');
                } else {
                    allBtn?.classList.remove('active');
                    filterContainer?.classList.add('filter-active');
                }

                // Cập nhật trạng thái active cho từng nút danh mục
                categoryButtons.forEach(btn => {
                    const cat = btn.getAttribute('data-filter');
                    if (selectedCategories.has(cat)) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });

                // Lọc sự kiện trên lưới Lịch tháng
                dayCells.forEach(cell => {
                    updateDayCellEvents(cell);
                });

                // Lọc trên danh sách Lịch trình hôm nay ở cột bên phải
                scheduleItems.forEach(item => {
                    const itemType = item.getAttribute('data-type');
                    if (selectedCategories.size === 0 || selectedCategories.has(itemType)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            // Click nút "Tất cả" -> xóa mọi lựa chọn riêng và hiển thị tất cả
            allBtn?.addEventListener('click', function() {
                selectedCategories.clear();
                applyFilters();
            });

            // Click các nút loại sự kiện -> toggle bật/tắt (cho phép chọn cùng lúc nhiều lựa chọn)
            categoryButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const cat = this.getAttribute('data-filter');
                    if (selectedCategories.has(cat)) {
                        selectedCategories.delete(cat);
                    } else {
                        selectedCategories.add(cat);
                    }
                    applyFilters();
                });
            });

            // Khởi tạo hiển thị ban đầu
            applyFilters();
        });
    </script>
</body>
</html>
