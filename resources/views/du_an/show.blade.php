<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chi Tiết Dự Án {{ $duAn->ten_du_an }} - Life Planner</title>

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
    <link rel="stylesheet" href="{{ asset('css/calendar.css') }}">
</head>

<body>
    <div class="main-wrapper main-wrapper-full">
        <!-- HEADER -->
        <header class="top-header border-bottom bg-white px-4 py-2 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('du-an.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1">
                    <i class="bi bi-arrow-left me-1"></i> Danh sách Dự án
                </a>
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <span class="d-inline-block rounded-circle" style="width: 14px; height: 14px; background-color: {{ $duAn->mau_nhan }};"></span>
                    {{ $duAn->ten_du_an }}
                </h5>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('cong-viec.index', ['du_an_id' => $duAn->id]) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-kanban me-1"></i> Xem Kanban Dự án
                </a>
                <button type="button" class="btn btn-success btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                    <i class="bi bi-plus-lg me-1"></i> Thêm Task Dự án
                </button>
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

            <!-- THÔNG TIN DỰ ÁN & TIẾN ĐỘ -->
            <div class="custom-card p-4 border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-12 col-md-8">
                        @php
                            $badgeMap = [
                                'chua_bat_dau' => ['bg-secondary-subtle text-secondary border-secondary-subtle', 'Chưa bắt đầu'],
                                'dang_thuc_hien' => ['bg-primary-subtle text-primary border-primary-subtle', 'Đang thực hiện'],
                                'tam_dung' => ['bg-warning-subtle text-warning border-warning-subtle', 'Tạm dừng'],
                                'hoan_thanh' => ['bg-success-subtle text-success border-success-subtle', 'Hoàn thành'],
                                'da_huy' => ['bg-danger-subtle text-danger border-danger-subtle', 'Đã hủy'],
                            ];
                            $st = $badgeMap[$duAn->trang_thai] ?? ['bg-secondary-subtle text-secondary', $duAn->trang_thai];
                        @endphp
                        <span class="badge {{ $st[0] }} border rounded-pill fs-8 mb-2">{{ $st[1] }}</span>
                        <h4 class="fw-bold text-dark mb-2">{{ $duAn->ten_du_an }}</h4>
                        <p class="text-secondary fs-7 mb-3">{{ $duAn->mo_ta ?: 'Chưa có mô tả chi tiết cho dự án này.' }}</p>

                        <div class="d-flex align-items-center gap-4 fs-7 text-secondary">
                            @if($duAn->ngay_bat_dau)
                            <span><i class="bi bi-calendar-check me-1 text-primary"></i>Ngày bắt đầu: <strong>{{ $duAn->ngay_bat_dau->format('d/m/Y') }}</strong></span>
                            @endif
                            @if($duAn->ngay_ket_thuc)
                            <span><i class="bi bi-calendar-x me-1 text-danger"></i>Ngày kết thúc: <strong>{{ $duAn->ngay_ket_thuc->format('d/m/Y') }}</strong></span>
                            @endif
                        </div>
                    </div>

                    <div class="col-12 col-md-4 text-md-end border-start-md ps-md-4">
                        <div class="fs-8 text-secondary mb-1">Tiến độ tổng thể dự án</div>
                        <h2 class="fw-bold text-primary mb-2">{{ $duAn->tien_do_percent }}%</h2>
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar rounded-pill" role="progressbar" style="width: {{ $duAn->tien_do_percent }}%; background-color: {{ $duAn->mau_nhan }};" aria-valuenow="{{ $duAn->tien_do_percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted fs-8">{{ $duAn->cong_viec_hoan_thanh_count ?? 0 }} / {{ $duAn->tong_cong_viec_count ?? 0 }} công việc đã xong</small>
                    </div>
                </div>
            </div>

            <!-- DANH SÁCH CÔNG VIỆC THUỘC DỰ ÁN -->
            <div class="custom-card p-4 border-0 shadow-sm rounded-4 bg-white">
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2 border-bottom pb-3">
                    <i class="bi bi-list-task text-primary fs-5"></i> Danh Sách Công Việc Thuộc Dự Án ({{ count($congViecs) }})
                </h6>

                @if($congViecs->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-journal-plus fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    Dự án này chưa có công việc nào. <button type="button" class="btn btn-link p-0 fw-semibold" data-bs-toggle="modal" data-bs-target="#createTaskModal">Thêm công việc đầu tiên</button>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light fs-7">
                            <tr>
                                <th>Công việc</th>
                                <th>Mức ưu tiên</th>
                                <th>Trạng thái</th>
                                <th>Deadline</th>
                                <th>Lịch</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($congViecs as $task)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark fs-7">{{ $task->ten_cong_viec }}</div>
                                    @if($task->mo_ta)
                                    <small class="text-muted fs-8 text-truncate d-block" style="max-width: 300px;">{{ $task->mo_ta }}</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $prioBadge = [
                                            'khan_cap' => ['bg-danger text-white', 'Khẩn cấp'],
                                            'cao' => ['bg-danger-subtle text-danger', 'Cao'],
                                            'trung_binh' => ['bg-warning-subtle text-warning', 'Trung bình'],
                                            'thap' => ['bg-secondary-subtle text-secondary', 'Thấp'],
                                        ][$task->uu_tien] ?? ['bg-light text-dark', $task->uu_tien];
                                    @endphp
                                    <span class="badge {{ $prioBadge[0] }} fs-8">{{ $prioBadge[1] }}</span>
                                </td>
                                <td>
                                    @php
                                        $stBadge = [
                                            'can_lam' => ['bg-secondary-subtle text-secondary', 'Cần làm'],
                                            'dang_lam' => ['bg-primary-subtle text-primary', 'Đang làm'],
                                            'cho_duyet' => ['bg-warning-subtle text-warning', 'Chờ duyệt'],
                                            'hoan_thanh' => ['bg-success-subtle text-success', 'Hoàn thành'],
                                        ][$task->trang_thai] ?? ['bg-light text-dark', $task->trang_thai];
                                    @endphp
                                    <span class="badge {{ $stBadge[0] }} fs-8">{{ $stBadge[1] }}</span>
                                </td>
                                <td>
                                    @if($task->deadline)
                                    <span class="fs-8 {{ $task->is_qua_han ? 'text-danger fw-bold' : 'text-secondary' }}">
                                        {{ $task->deadline->format('d/m/Y H:i') }}
                                        @if($task->is_qua_han)
                                        <br><small class="badge bg-danger-subtle text-danger border border-danger-subtle fs-8">{{ $task->qua_han_text }}</small>
                                        @endif
                                    </span>
                                    @else
                                    <span class="text-muted fs-8">Không có</span>
                                    @endif
                                </td>
                                <td>
                                    @if($task->dong_bo_calendar)
                                    <span class="badge bg-indigo-subtle text-indigo border border-indigo-subtle fs-8" style="background-color: #eef2ff; color: #6366f1;">
                                        <i class="bi bi-calendar-check-fill me-1"></i> Đã đồng bộ
                                    </span>
                                    @else
                                    <span class="text-muted fs-8">Chưa đồng bộ</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('cong-viec.index', ['du_an_id' => $duAn->id]) }}" class="btn btn-sm btn-outline-secondary rounded-circle p-2" title="Xem trên Kanban">
                                        <i class="bi bi-kanban"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </main>
    </div>

    <!-- MODAL TẠO CÔNG VIỆC THUỘC DỰ ÁN NÀY -->
    <div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle-fill text-primary"></i> Tạo Công Việc Cho {{ $duAn->ten_du_an }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('cong-viec.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="du_an_id" value="{{ $duAn->id }}">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">Tên công việc <span class="text-danger">*</span></label>
                            <input type="text" name="ten_cong_viec" class="form-control rounded-3" placeholder="Ví dụ: Hoàn thiện tính năng đăng nhập OAuth" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-7 text-secondary">Mức ưu tiên</label>
                                <select name="uu_tien" class="form-select rounded-3">
                                    <option value="thap">⚪ Thấp</option>
                                    <option value="trung_binh" selected>🟡 Trung bình</option>
                                    <option value="cao">🔴 Cao</option>
                                    <option value="khan_cap">🚨 Khẩn cấp</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-7 text-secondary">Trạng thái</label>
                                <select name="trang_thai" class="form-select rounded-3">
                                    <option value="can_lam" selected>Cần làm</option>
                                    <option value="dang_lam">Đang làm</option>
                                    <option value="cho_duyet">Chờ duyệt</option>
                                    <option value="hoan_thanh">Hoàn thành</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">Hạn chót (Deadline)</label>
                            <input type="datetime-local" name="deadline" class="form-control rounded-3">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">Mô tả chi tiết</label>
                            <textarea name="mo_ta" class="form-control rounded-3" rows="3"></textarea>
                        </div>

                        <div class="card border-0 bg-light p-3 rounded-3">
                            <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                <input class="form-check-input mt-0 cursor-pointer" type="checkbox" name="dong_bo_calendar" value="1" id="projectTaskSyncCalendar">
                                <label class="form-check-label fw-semibold fs-7 text-dark cursor-pointer" for="projectTaskSyncCalendar">
                                    <i class="bi bi-calendar-plus text-primary me-1"></i> Đồng bộ lên Lịch cá nhân
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Lưu công việc</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
