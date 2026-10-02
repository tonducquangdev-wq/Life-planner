<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist Bài Tập - Life Planner</title>

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

    <style>
        .checklist-item {
            transition: all 0.2s ease;
            border-left: 4px solid #6366f1;
        }
        .checklist-item.completed {
            border-left-color: #10b981;
            background-color: #f0fdf4 !important;
            opacity: 0.85;
        }
        [data-bs-theme="dark"] .checklist-item.completed {
            background-color: rgba(16, 185, 129, 0.1) !important;
        }
        .checklist-item.completed .exercise-title {
            text-decoration: line-through;
            color: #64748b !important;
        }
        .custom-checkbox {
            width: 24px;
            height: 24px;
            cursor: pointer;
            accent-color: #10b981;
        }
    </style>
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
                    <i class="bi bi-check2-square text-success"></i> Checklist Bài Tập Rèn Luyện
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

                <!-- SUB NAV TABS: THEO DÕI / CHECKLIST / BÁO CÁO -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div class="d-inline-flex p-1 bg-white border rounded-pill shadow-xs">
                        <a href="{{ route('tap-luyen.index') }}" class="btn btn-sm px-3 py-1.5 rounded-pill text-secondary fw-semibold">
                            <i class="bi bi-heart-pulse-fill me-1"></i> Theo dõi Thể chất
                        </a>
                        <a href="{{ route('workout.checklist') }}" class="btn btn-sm px-3 py-1.5 rounded-pill btn-primary fw-semibold shadow-xs">
                            <i class="bi bi-check2-square me-1"></i> Checklist Bài tập
                        </a>
                        <a href="{{ route('bao-cao-tap-luyen.index') }}" class="btn btn-sm px-3 py-1.5 rounded-pill text-secondary fw-semibold">
                            <i class="bi bi-bar-chart-fill me-1"></i> Báo cáo
                        </a>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5" id="btnResetChecklist">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Đặt lại tất cả
                        </button>
                    </div>
                </div>

                <!-- CHỌN BUỔI TẬP HIỆN TẠI -->
                @if($activePlan)
                    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 fs-8 fw-semibold mb-1">
                                    {{ $activePlan->ten_ke_hoach }}
                                </span>
                                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <span>Buổi tập: {{ $currentBuoiTap ? $currentBuoiTap->ten_buoi_tap : 'Chưa có buổi tập' }}</span>
                                    @if($currentBuoiTap && $currentBuoiTap->ten_thu)
                                        <span class="badge bg-light text-muted border rounded-pill fs-8">{{ $currentBuoiTap->ten_thu }}</span>
                                    @endif
                                </h5>
                                <small class="text-muted">{{ $currentBuoiTap ? ($currentBuoiTap->mo_ta ?: 'Tập trung rèn luyện theo đúng sets và reps') : '' }}</small>
                            </div>

                            <!-- Selector đổi buổi tập -->
                            <div class="d-flex align-items-center gap-2">
                                <form method="GET" action="{{ route('workout.checklist') }}" class="d-flex gap-2">
                                    <select name="buoi_tap_id" class="form-select form-select-sm rounded-pill bg-light border-0" onchange="this.form.submit()">
                                        @foreach($activePlan->buoiTaps as $bt)
                                            <option value="{{ $bt->id }}" {{ $currentBuoiTap && $currentBuoiTap->id === $bt->id ? 'selected' : '' }}>
                                                {{ $bt->ten_buoi_tap }} {{ $bt->ten_thu ? "({$bt->ten_thu})" : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- TIẾN ĐỘ HOÀN THÀNH CHECKLIST -->
                <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fs-7 fw-bold text-dark d-flex align-items-center gap-1.5">
                            <i class="bi bi-trophy-fill text-warning"></i> Tiến độ buổi tập
                        </span>
                        <span class="fs-7 fw-bold text-primary" id="progressText">0 / 0 hoàn thành (0%)</span>
                    </div>
                    <div class="progress rounded-pill" style="height: 10px;">
                        <div class="progress-bar bg-success rounded-pill transition-all" id="progressBar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <!-- DANH SÁCH BÀI TẬP CHECKLIST -->
                <div class="row g-3" id="checklistContainer">
                    @if($currentBuoiTap && $currentBuoiTap->chiTietBuoiTaps->isNotEmpty())
                        @foreach($currentBuoiTap->chiTietBuoiTaps->sortBy('thu_tu') as $index => $item)
                            <div class="col-12">
                                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white checklist-item" id="item-{{ $item->id }}" data-id="{{ $item->id }}">
                                    <div class="d-flex align-items-center justify-content-between gap-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <!-- Checkbox tương tác -->
                                            <input type="checkbox" class="custom-checkbox exercise-checkbox" data-id="{{ $item->id }}">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1 exercise-title fs-6">
                                                    {{ $item->baiTapTheChat ? $item->baiTapTheChat->ten_bai_tap : 'Bài tập' }}
                                                </h6>
                                                <div class="d-flex flex-wrap align-items-center gap-2 text-muted fs-8">
                                                    <span class="badge bg-light text-dark border rounded-pill px-2 py-0.5">
                                                        {{ $item->baiTapTheChat ? $item->baiTapTheChat->nhom_co : 'Toàn thân' }}
                                                    </span>
                                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5 fw-semibold">
                                                        {{ $item->dinh_dang_thong_so }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 fs-8 status-badge d-none">
                                                <i class="bi bi-check-circle-fill me-1"></i>Đã xong
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                                <div class="text-muted mb-3"><i class="bi bi-inbox fs-1"></i></div>
                                <h6 class="fw-bold text-dark">Buổi tập chưa có bài tập nào</h6>
                                <p class="text-muted fs-7 mb-3">Vui lòng quay lại giao diện Theo dõi thể chất để thêm bài tập vào buổi rèn luyện này.</p>
                                <div>
                                    <a href="{{ route('tap-luyen.index') }}" class="btn btn-primary rounded-pill px-4 btn-sm">
                                        Đến trang Tập luyện
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
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
            const currentSessionId = "{{ $currentBuoiTap ? $currentBuoiTap->id : 0 }}";
            const storageKey = 'workout_checklist_session_' + currentSessionId;
            const checkboxes = document.querySelectorAll('.exercise-checkbox');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');
            const btnReset = document.getElementById('btnResetChecklist');

            // Load saved checklist state from localStorage
            let savedState = {};
            try {
                savedState = JSON.parse(localStorage.getItem(storageKey)) || {};
            } catch(e) {
                savedState = {};
            }

            function updateUI() {
                let total = checkboxes.length;
                let checkedCount = 0;

                checkboxes.forEach(cb => {
                    const id = cb.dataset.id;
                    const card = document.getElementById('item-' + id);
                    const badge = card?.querySelector('.status-badge');

                    if (cb.checked) {
                        checkedCount++;
                        card?.classList.add('completed');
                        badge?.classList.remove('d-none');
                    } else {
                        card?.classList.remove('completed');
                        badge?.classList.add('d-none');
                    }
                });

                const percent = total > 0 ? Math.round((checkedCount / total) * 100) : 0;
                if (progressBar) progressBar.style.width = percent + '%';
                if (progressText) progressText.textContent = `${checkedCount} / ${total} hoàn thành (${percent}%)`;
            }

            // Restore state
            checkboxes.forEach(cb => {
                const id = cb.dataset.id;
                if (savedState[id]) {
                    cb.checked = true;
                }

                cb.addEventListener('change', function() {
                    savedState[id] = this.checked;
                    localStorage.setItem(storageKey, JSON.stringify(savedState));
                    updateUI();
                });
            });

            updateUI();

            btnReset?.addEventListener('click', function() {
                if (confirm('Bạn có chắc muốn đặt lại toàn bộ checklist bài tập của buổi này?')) {
                    savedState = {};
                    localStorage.removeItem(storageKey);
                    checkboxes.forEach(cb => cb.checked = false);
                    updateUI();
                }
            });
        });
    </script>
</body>
</html>
