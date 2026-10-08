<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản Lý Công Việc (Kanban Board) - Life Planner</title>

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


    <style>
        .kanban-column {
            min-height: 600px;
            background-color: var(--bs-tertiary-bg, #f8fafc);
            border-radius: 1rem;
            padding: 1rem;
        }
        .kanban-card {
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }
        .kanban-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
        }
    </style>
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
                    <i class="bi bi-kanban-fill text-primary"></i> Quản Lý Công Việc
                </h5>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('cong-viec.dashboard') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                </a>
                <a href="{{ route('cong-viec.index') }}" class="btn btn-primary btn-sm rounded-pill px-3 active">
                    <i class="bi bi-kanban me-1"></i> Công việc (Kanban)
                </a>
                <a href="{{ route('du-an.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-folder-symlink me-1"></i> Dự án
                </a>
                <button type="button" class="btn btn-success btn-sm rounded-pill px-3 ms-2" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                    <i class="bi bi-plus-lg me-1"></i> Thêm Công việc
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

            <!-- THANH BỘ LỌC TÌM KIẾM -->
            <form method="GET" action="{{ route('cong-viec.index') }}" class="row g-3 mb-4 bg-white p-3 rounded-4 shadow-sm border">
                <div class="col-12 col-md-4">
                    <input type="text" name="search" class="form-control rounded-pill" placeholder="🔍 Tìm tên công việc..." value="{{ request('search') }}">
                </div>
                <div class="col-12 col-md-3">
                    <select name="du_an_id" class="form-select rounded-pill" onchange="this.form.submit()">
                        <option value="">📁 Tất cả Dự án</option>
                        @foreach($duAns as $da)
                        <option value="{{ $da->id }}" {{ request('du_an_id') == $da->id ? 'selected' : '' }}>{{ $da->ten_du_an }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <select name="uu_tien" class="form-select rounded-pill" onchange="this.form.submit()">
                        <option value="">⚡ Tất cả Mức ưu tiên</option>
                        <option value="khan_cap" {{ request('uu_tien') == 'khan_cap' ? 'selected' : '' }}>🚨 Khẩn cấp</option>
                        <option value="cao" {{ request('uu_tien') == 'cao' ? 'selected' : '' }}>🔴 Cao</option>
                        <option value="trung_binh" {{ request('uu_tien') == 'trung_binh' ? 'selected' : '' }}>🟡 Trung bình</option>
                        <option value="thap" {{ request('uu_tien') == 'thap' ? 'selected' : '' }}>⚪ Thấp</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 text-end">
                    <a href="{{ route('cong-viec.index') }}" class="btn btn-outline-secondary rounded-pill w-100">Xóa lọc</a>
                </div>
            </form>

            <!-- KANBAN BOARD (4 CỘT) -->
            <div class="row g-4">
                @php
                    $columns = [
                        'can_lam' => ['Cần làm', 'bg-secondary-subtle text-secondary', 'bi-list-stars'],
                        'dang_lam' => ['Đang làm', 'bg-primary-subtle text-primary', 'bi-arrow-repeat'],
                        'cho_duyet' => ['Chờ duyệt', 'bg-warning-subtle text-warning', 'bi-hourglass-split'],
                        'hoan_thanh' => ['Hoàn thành', 'bg-success-subtle text-success', 'bi-check2-circle'],
                    ];
                @endphp

                @foreach($columns as $colKey => $colMeta)
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="kanban-column border shadow-2xs">
                        <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                            <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                                <span class="badge {{ $colMeta[1] }} rounded-pill p-2"><i class="bi {{ $colMeta[2] }}"></i></span>
                                {{ $colMeta[0] }}
                            </h6>
                            <span class="badge bg-white text-dark border rounded-circle fs-8">{{ count($kanban[$colKey] ?? []) }}</span>
                        </div>

                        <div class="d-flex flex-column gap-3" id="col-{{ $colKey }}">
                            @forelse($kanban[$colKey] ?? [] as $task)
                            <div class="card kanban-card border-0 shadow-sm rounded-3 p-3 bg-white position-relative {{ $task->is_qua_han ? 'border-start border-3 border-danger' : '' }}" onclick="openEditTaskModal({{ json_encode($task) }})">
                                <!-- Tiêu đề & Dự án -->
                                <div class="mb-2">
                                    @if($task->duAn)
                                    <span class="badge mb-1 fs-8 fw-semibold" style="background-color: {{ $task->duAn->mau_nhan }}; color: #fff;">
                                        {{ $task->duAn->ten_du_an }}
                                    </span>
                                    @endif
                                    <div class="fw-bold text-dark fs-6">{{ $task->ten_cong_viec }}</div>
                                </div>

                                @if($task->mo_ta)
                                <p class="text-secondary fs-8 mb-2 text-truncate-2" style="max-height: 2.6em; overflow: hidden;">
                                    {{ $task->mo_ta }}
                                </p>
                                @endif

                                <!-- Thống kê Deadline, Ưu tiên & Calendar -->
                                <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-2">
                                    <div class="d-flex align-items-center gap-1.5 fs-8">
                                        @php
                                            $prioBadge = [
                                                'khan_cap' => ['bg-danger text-white', 'Khẩn cấp'],
                                                'cao' => ['bg-danger-subtle text-danger', 'Cao'],
                                                'trung_binh' => ['bg-warning-subtle text-warning', 'Trung bình'],
                                                'thap' => ['bg-secondary-subtle text-secondary', 'Thấp'],
                                            ][$task->uu_tien] ?? ['bg-light text-dark', $task->uu_tien];
                                        @endphp
                                        <span class="badge {{ $prioBadge[0] }} fs-8">{{ $prioBadge[1] }}</span>

                                        @if($task->dong_bo_calendar)
                                        <span class="badge bg-indigo-subtle text-indigo border border-indigo-subtle fs-8" style="background-color: #eef2ff; color: #6366f1;" title="Đã đồng bộ lên Calendar">
                                            <i class="bi bi-calendar-check-fill"></i>
                                        </span>
                                        @endif
                                    </div>

                                    @if($task->deadline)
                                    <div class="fs-8 {{ $task->is_qua_han ? 'text-danger fw-bold' : 'text-muted' }}">
                                        <i class="bi bi-clock me-1"></i>{{ $task->deadline->format('d/m H:i') }}
                                    </div>
                                    @endif
                                </div>

                                @if($task->is_qua_han)
                                <div class="mt-2 text-end">
                                    <span class="badge bg-danger-subtle text-danger fs-8 border border-danger-subtle">{{ $task->qua_han_text }}</span>
                                </div>
                                @endif
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted fs-8 border border-dashed rounded-3">
                                Chưa có công việc
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </main>
    </div>

    <!-- MODAL TẠO CÔNG VIỆC MỚI -->
    <div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle-fill text-primary"></i> Tạo Công Việc Mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('cong-viec.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">Tên công việc <span class="text-danger">*</span></label>
                            <input type="text" name="ten_cong_viec" class="form-control rounded-3" placeholder="Ví dụ: Thiết kế slide thuyết trình Đồ án" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">Thuộc Dự án</label>
                            <select name="du_an_id" class="form-select rounded-3">
                                <option value="">(Công việc tự do - Không thuộc dự án)</option>
                                @foreach($duAns as $da)
                                <option value="{{ $da->id }}">{{ $da->ten_du_an }}</option>
                                @endforeach
                            </select>
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
                            <textarea name="mo_ta" class="form-control rounded-3" rows="3" placeholder="Ghi chú thêm nội dung công việc..."></textarea>
                        </div>

                        <!-- CẤU HÌNH ĐỒNG BỘ CALENDAR -->
                        <div class="card border-0 bg-light p-3 rounded-3">
                            <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                <input class="form-check-input mt-0 cursor-pointer" type="checkbox" name="dong_bo_calendar" value="1" id="createTaskSyncCalendar">
                                <label class="form-check-label fw-semibold fs-7 text-dark cursor-pointer" for="createTaskSyncCalendar">
                                    <i class="bi bi-calendar-plus text-primary me-1"></i> Đồng bộ lên Lịch cá nhân
                                </label>
                            </div>
                            <small class="text-muted fs-8 mt-1 d-block">
                                Nếu bật, hệ thống sẽ tạo một sự kiện nhắc hạn chót công việc trên Calendar.
                            </small>
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

    <!-- MODAL SỬA CÔNG VIỆC -->
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square text-primary"></i> Chỉnh Sửa Công Việc
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditTask" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">Tên công việc <span class="text-danger">*</span></label>
                            <input type="text" id="edit_ten_cong_viec" name="ten_cong_viec" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">Thuộc Dự án</label>
                            <select id="edit_du_an_id" name="du_an_id" class="form-select rounded-3">
                                <option value="">(Công việc tự do - Không thuộc dự án)</option>
                                @foreach($duAns as $da)
                                <option value="{{ $da->id }}">{{ $da->ten_du_an }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-7 text-secondary">Mức ưu tiên</label>
                                <select id="edit_uu_tien" name="uu_tien" class="form-select rounded-3">
                                    <option value="thap">⚪ Thấp</option>
                                    <option value="trung_binh">🟡 Trung bình</option>
                                    <option value="cao">🔴 Cao</option>
                                    <option value="khan_cap">🚨 Khẩn cấp</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-7 text-secondary">Trạng thái</label>
                                <select id="edit_trang_thai" name="trang_thai" class="form-select rounded-3">
                                    <option value="can_lam">Cần làm</option>
                                    <option value="dang_lam">Đang làm</option>
                                    <option value="cho_duyet">Chờ duyệt</option>
                                    <option value="hoan_thanh">Hoàn thành</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">Hạn chót (Deadline)</label>
                            <input type="datetime-local" id="edit_deadline" name="deadline" class="form-control rounded-3">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">Mô tả chi tiết</label>
                            <textarea id="edit_mo_ta" name="mo_ta" class="form-control rounded-3" rows="3"></textarea>
                        </div>

                        <!-- CẤU HÌNH ĐỒNG BỘ CALENDAR -->
                        <div class="card border-0 bg-light p-3 rounded-3">
                            <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                <input class="form-check-input mt-0 cursor-pointer" type="checkbox" name="dong_bo_calendar" value="1" id="editTaskSyncCalendar">
                                <label class="form-check-label fw-semibold fs-7 text-dark cursor-pointer" for="editTaskSyncCalendar">
                                    <i class="bi bi-calendar-plus text-primary me-1"></i> Đồng bộ lên Lịch cá nhân
                                </label>
                            </div>
                            <small class="text-muted fs-8 mt-1 d-block">
                                Nếu bỏ chọn, sự kiện nhắc công việc sẽ được tự động gỡ khỏi Calendar.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 justify-content-between">
                        <button type="button" class="btn btn-outline-danger rounded-pill px-3" onclick="deleteCurrentTask()">Xóa công việc</button>
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
        let activeEditTaskId = null;

        function openEditTaskModal(task) {
            activeEditTaskId = task.id;
            const form = document.getElementById('formEditTask');
            form.action = `/cong-viec/${task.id}`;

            document.getElementById('edit_ten_cong_viec').value = task.ten_cong_viec || '';
            document.getElementById('edit_du_an_id').value = task.du_an_id || '';
            document.getElementById('edit_uu_tien').value = task.uu_tien || 'trung_binh';
            document.getElementById('edit_trang_thai').value = task.trang_thai || 'can_lam';
            document.getElementById('edit_mo_ta').value = task.mo_ta || '';
            document.getElementById('editTaskSyncCalendar').checked = Boolean(task.dong_bo_calendar);

            if (task.deadline) {
                const dt = new Date(task.deadline);
                const formatted = dt.toISOString().slice(0, 16);
                document.getElementById('edit_deadline').value = formatted;
            } else {
                document.getElementById('edit_deadline').value = '';
            }

            const editModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
            editModal.show();
        }

        function deleteCurrentTask() {
            if (!activeEditTaskId) return;
            if (confirm('Bạn có chắc chắn muốn xóa công việc này không?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/cong-viec/${activeEditTaskId}`;
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

