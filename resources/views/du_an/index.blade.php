<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản Lý Dự Án - Life Planner</title>

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
                <a href="{{ route('calendar.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại Lịch
                </a>
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-folder-symlink-fill text-primary"></i> Quản Lý Dự Án
                </h5>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('cong-viec.dashboard') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                </a>
                <a href="{{ route('cong-viec.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-kanban me-1"></i> Công việc (Kanban)
                </a>
                <a href="{{ route('du-an.index') }}" class="btn btn-primary btn-sm rounded-pill px-3 active">
                    <i class="bi bi-folder-symlink me-1"></i> Dự án
                </a>
                <button type="button" class="btn btn-success btn-sm rounded-pill px-3 ms-2" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                    <i class="bi bi-plus-lg me-1"></i> Thêm Dự án
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

            <!-- BỘ LỌC DỰ ÁN -->
            <form method="GET" action="{{ route('du-an.index') }}" class="row g-3 mb-4 bg-white p-3 rounded-4 shadow-sm border">
                <div class="col-12 col-md-6">
                    <input type="text" name="search" class="form-control rounded-pill" placeholder="🔍 Tìm kiếm dự án..." value="{{ request('search') }}">
                </div>
                <div class="col-12 col-md-4">
                    <select name="trang_thai" class="form-select rounded-pill" onchange="this.form.submit()">
                        <option value="">📂 Tất cả Trạng thái</option>
                        <option value="chua_bat_dau" {{ request('trang_thai') == 'chua_bat_dau' ? 'selected' : '' }}>⚪ Chưa bắt đầu</option>
                        <option value="dang_thuc_hien" {{ request('trang_thai') == 'dang_thuc_hien' ? 'selected' : '' }}>🔵 Đang thực hiện</option>
                        <option value="tam_dung" {{ request('trang_thai') == 'tam_dung' ? 'selected' : '' }}>🟡 Tạm dừng</option>
                        <option value="hoan_thanh" {{ request('trang_thai') == 'hoan_thanh' ? 'selected' : '' }}>🟢 Hoàn thành</option>
                        <option value="da_huy" {{ request('trang_thai') == 'da_huy' ? 'selected' : '' }}>🔴 Đã hủy</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 text-end">
                    <a href="{{ route('du-an.index') }}" class="btn btn-outline-secondary rounded-pill w-100">Xóa lọc</a>
                </div>
            </form>

            <!-- LƯỚI CARD DỰ ÁN -->
            <div class="row g-4">
                @forelse($duAns as $duAn)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="custom-card p-4 border-0 shadow-sm rounded-4 bg-white h-100 d-flex flex-column justify-content-between position-relative">
                        <div>
                            <!-- Header Card -->
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="d-inline-block rounded-circle" style="width: 14px; height: 14px; background-color: {{ $duAn->mau_nhan }};"></span>
                                    <h6 class="fw-bold text-dark mb-0 fs-6">{{ $duAn->ten_du_an }}</h6>
                                </div>
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
                                <span class="badge {{ $st[0] }} border rounded-pill fs-8">{{ $st[1] }}</span>
                            </div>

                            @if($duAn->mo_ta)
                            <p class="text-secondary fs-7 mb-3 text-truncate-2" style="max-height: 2.8em; overflow: hidden;">
                                {{ $duAn->mo_ta }}
                            </p>
                            @endif

                            <div class="d-flex align-items-center gap-3 fs-8 text-secondary mb-3">
                                @if($duAn->ngay_bat_dau)
                                <span><i class="bi bi-calendar-check me-1"></i>S: {{ $duAn->ngay_bat_dau->format('d/m/Y') }}</span>
                                @endif
                                @if($duAn->ngay_ket_thuc)
                                <span><i class="bi bi-calendar-x me-1"></i>E: {{ $duAn->ngay_ket_thuc->format('d/m/Y') }}</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <!-- Tiến độ Dự án -->
                            <div class="d-flex align-items-center justify-content-between fs-8 text-secondary mb-1">
                                <span>Tiến độ công việc ({{ $duAn->cong_viec_hoan_thanh_count ?? 0 }}/{{ $duAn->tong_cong_viec_count ?? 0 }})</span>
                                <span class="fw-bold text-dark fs-7">{{ $duAn->tien_do_percent }}%</span>
                            </div>
                            <div class="progress mb-3" style="height: 8px;">
                                <div class="progress-bar rounded-pill" role="progressbar" style="width: {{ $duAn->tien_do_percent }}%; background-color: {{ $duAn->mau_nhan }};" aria-valuenow="{{ $duAn->tien_do_percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>

                            <!-- Thao tác -->
                            <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                <a href="{{ route('du-an.show', $duAn->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-eye me-1"></i> Chi tiết & Task
                                </a>
                                <button type="button" class="btn btn-sm btn-light text-secondary rounded-circle p-2" title="Chỉnh sửa dự án" onclick="openEditProjectModal({{ json_encode($duAn) }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5 text-muted bg-white rounded-4 border">
                    <i class="bi bi-folder-plus fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    Chưa có dự án nào được tạo.
                </div>
                @endforelse
            </div>
        </main>
    </div>

    <!-- MODAL TẠO DỰ ÁN MỚI -->
    <div class="modal fade" id="createProjectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-folder-plus text-primary"></i> Tạo Dự Án Mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('du-an.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">Tên dự án <span class="text-danger">*</span></label>
                            <input type="text" name="ten_du_an" class="form-control rounded-3" placeholder="Ví dụ: Đồ án Life Planner & Xây dựng Website" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-7 text-secondary">Ngày bắt đầu</label>
                                <input type="date" name="ngay_bat_dau" class="form-control rounded-3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-7 text-secondary">Ngày kết thúc</label>
                                <input type="date" name="ngay_ket_thuc" class="form-control rounded-3">
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-7 text-secondary">Trạng thái</label>
                                <select name="trang_thai" class="form-select rounded-3">
                                    <option value="chua_bat_dau" selected>⚪ Chưa bắt đầu</option>
                                    <option value="dang_thuc_hien">🔵 Đang thực hiện</option>
                                    <option value="tam_dung">🟡 Tạm dừng</option>
                                    <option value="hoan_thanh">🟢 Hoàn thành</option>
                                    <option value="da_huy">🔴 Đã hủy</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-7 text-secondary">Màu nhãn nhận diện</label>
                                <input type="color" name="mau_nhan" class="form-control form-control-color w-100 rounded-3" value="#4F46E5">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">Mô tả dự án</label>
                            <textarea name="mo_ta" class="form-control rounded-3" rows="3" placeholder="Mục tiêu và ghi chú về dự án..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Lưu dự án</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL SỬA DỰ ÁN -->
    <div class="modal fade" id="editProjectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square text-primary"></i> Chỉnh Sửa Dự Án
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditProject" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">Tên dự án <span class="text-danger">*</span></label>
                            <input type="text" id="edit_ten_du_an" name="ten_du_an" class="form-control rounded-3" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-7 text-secondary">Ngày bắt đầu</label>
                                <input type="date" id="edit_ngay_bat_dau" name="ngay_bat_dau" class="form-control rounded-3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-7 text-secondary">Ngày kết thúc</label>
                                <input type="date" id="edit_ngay_ket_thuc" name="ngay_ket_thuc" class="form-control rounded-3">
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-7 text-secondary">Trạng thái</label>
                                <select id="edit_trang_thai_du_an" name="trang_thai" class="form-select rounded-3">
                                    <option value="chua_bat_dau">⚪ Chưa bắt đầu</option>
                                    <option value="dang_thuc_hien">🔵 Đang thực hiện</option>
                                    <option value="tam_dung">🟡 Tạm dừng</option>
                                    <option value="hoan_thanh">🟢 Hoàn thành</option>
                                    <option value="da_huy">🔴 Đã hủy</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-7 text-secondary">Màu nhãn nhận diện</label>
                                <input type="color" id="edit_mau_nhan" name="mau_nhan" class="form-control form-control-color w-100 rounded-3">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">Mô tả dự án</label>
                            <textarea id="edit_mo_ta_du_an" name="mo_ta" class="form-control rounded-3" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 justify-content-between">
                        <button type="button" class="btn btn-outline-danger rounded-pill px-3" onclick="deleteCurrentProject()">Xóa dự án</button>
                        <div>
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 me-2" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Cập nhật</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let activeEditProjectId = null;

        function openEditProjectModal(duAn) {
            activeEditProjectId = duAn.id;
            const form = document.getElementById('formEditProject');
            form.action = `/du-an/${duAn.id}`;

            document.getElementById('edit_ten_du_an').value = duAn.ten_du_an || '';
            document.getElementById('edit_ngay_bat_dau').value = duAn.ngay_bat_dau ? duAn.ngay_bat_dau.slice(0, 10) : '';
            document.getElementById('edit_ngay_ket_thuc').value = duAn.ngay_ket_thuc ? duAn.ngay_ket_thuc.slice(0, 10) : '';
            document.getElementById('edit_trang_thai_du_an').value = duAn.trang_thai || 'chua_bat_dau';
            document.getElementById('edit_mau_nhan').value = duAn.mau_nhan || '#4F46E5';
            document.getElementById('edit_mo_ta_du_an').value = duAn.mo_ta || '';

            const editModal = new bootstrap.Modal(document.getElementById('editProjectModal'));
            editModal.show();
        }

        function deleteCurrentProject() {
            if (!activeEditProjectId) return;
            if (confirm('Bạn có chắc chắn muốn xóa dự án này không? Các công việc thuộc dự án sẽ trở thành công việc tự do.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/du-an/${activeEditProjectId}`;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

    <!-- Floating Action Menu & Shared Profile Modal -->
    <x-floating-menu />
</body>

</html>
