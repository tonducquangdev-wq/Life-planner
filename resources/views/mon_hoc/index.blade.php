<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Môn Học - Life Planner</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Font: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom Dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body>

    <!-- SIDEBAR -->
    @include('layouts.sidebar')

    <!-- MAIN WRAPPER -->
    <div class="main-wrapper">

        <!-- HEADER -->
        <header class="top-header">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none p-1 px-2 border" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h5 class="fw-bold text-dark mb-0">Quản lý môn học</h5>
            </div>

            <div class="header-actions">
                <div class="user-profile dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ mb_substr(Auth::user()->ho_ten ?? 'User', 0, 1) }}
                        </div>
                        <div class="d-none d-md-block text-start">
                            <div class="fw-bold text-dark fs-6 leading-tight">{{ Auth::user()->ho_ten ?? 'User' }}</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Hồ sơ</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
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
        <main class="content-body">

            <!-- Page Title & Primary Actions -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Danh sách Môn Học</h4>
                    <p class="text-muted mb-0">Quản lý danh sách các môn học, tiến độ và lịch học của bạn. jqk</p>
                </div>
                <div>
                    <a href="{{ route('mon-hoc.create') }}" class="btn btn-primary rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle fs-5"></i>
                        <span class="fw-semibold">Thêm môn học mới</span>
                    </a>
                </div>
            </div>

            <!-- Flash Alert -->
            @if(session('status'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Mon Hoc Card List Grid -->
            <div class="row g-4">
                @forelse($monHocs as $monHoc)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative hover-shadow transition">
                        <!-- Top Colored Strip -->
                        <div style="height: 6px; background-color: {{ $monHoc->mau_sac ?? '#6366f1' }};"></div>

                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <!-- Card Header: Code & Status Badge -->
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-light text-dark border px-2 py-1 fw-bold fs-7 rounded-2">
                                        {{ $monHoc->ma_mon }}
                                    </span>

                                    @php
                                    $statusBadge = match($monHoc->trang_thai) {
                                    'dang_hoc' => 'bg-success-subtle text-success border-success-subtle',
                                    'da_hoan_thanh' => 'bg-primary-subtle text-primary border-primary-subtle',
                                    default => 'bg-secondary-subtle text-secondary'
                                    };
                                    $statusText = match($monHoc->trang_thai) {
                                    'dang_hoc' => 'Đang học',
                                    'da_hoan_thanh' => 'Đã hoàn thành',
                                    default => 'Tạm dừng'
                                    };
                                    @endphp
                                    <span class="badge {{ $statusBadge }} border px-2 py-1 rounded-pill small">
                                        {{ $statusText }}
                                    </span>
                                </div>

                                <!-- Mon Hoc Name -->
                                <h5 class="fw-bold text-dark mb-3 text-truncate" title="{{ $monHoc->ten_mon }}">
                                    <a href="{{ route('mon-hoc.show', $monHoc) }}" class="text-dark text-decoration-none hover-primary">
                                        {{ $monHoc->ten_mon }}
                                    </a>
                                </h5>

                                <!-- Mon Hoc Metadata -->
                                <div class="small text-muted mb-3 space-y-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="bi bi-person-badge text-primary fs-6"></i>
                                        <span>Giảng viên: <strong class="text-dark">{{ $monHoc->giang_vien ?? 'Chưa cập nhật' }}</strong></span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="bi bi-geo-alt text-danger fs-6"></i>
                                        <span>Phòng học: <strong class="text-dark">{{ $monHoc->phong_hoc ?? 'Chưa cập nhật' }}</strong></span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-journal-bookmark text-warning fs-6"></i>
                                        <span>Số tín chỉ: <strong class="text-dark">{{ $monHoc->so_tin_chi }} tín chỉ</strong></span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <!-- Progress Bar -->
                                <div class="mt-2 pt-2 border-top">
                                    <div class="d-flex justify-content-between align-items-center mb-1 small">
                                        <span class="text-muted font-semibold">Tiến độ</span>
                                        <span class="fw-bold text-dark">{{ $monHoc->tien_do ?? 0 }}%</span>
                                    </div>
                                    <div class="progress rounded-pill" style="height: 8px;">
                                        <div class="progress-bar rounded-pill" role="progressbar"
                                            style="width: {{ $monHoc->tien_do ?? 0 }}%; background-color: {{ $monHoc->mau_sac ?? '#6366f1' }};"
                                            aria-valuenow="{{ $monHoc->tien_do ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Action Buttons -->
                                <div class="d-flex align-items-center justify-content-between pt-3 mt-2 border-top">
                                    <a href="{{ route('mon-hoc.show', $monHoc) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="bi bi-eye me-1"></i> Xem chi tiết
                                    </a>

                                    <div class="d-flex gap-1">
                                        <a href="{{ route('mon-hoc.edit', $monHoc) }}" class="btn btn-sm btn-light border rounded-circle text-warning p-2" title="Chỉnh sửa">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light border rounded-circle text-danger p-2"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $monHoc->id }}" title="Xóa môn học">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MODAL XÁC NHẬN XÓA -->
                    <div class="modal fade" id="deleteModal{{ $monHoc->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold text-danger">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Xác nhận xóa
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body py-3">
                                    Bạn có chắc chắn muốn xóa môn học <strong class="text-dark">{{ $monHoc->ten_mon }}</strong> ({{ $monHoc->ma_mon }})?
                                    <p class="text-muted small mt-2 mb-0">Môn học sẽ được chuyển vào thùng rác và có thể khôi phục sau này.</p>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Hủy bỏ</button>
                                    <form action="{{ route('mon-hoc.destroy', $monHoc) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger rounded-pill px-4">Đồng ý Xóa</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white">
                        <div class="card-body">
                            <div class="rounded-circle bg-light d-inline-flex p-4 mb-3 text-secondary">
                                <i class="bi bi-journal-x fs-1"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Chưa có môn học nào</h5>
                            <p class="text-muted mb-3">Bắt đầu quản lý việc học của bạn bằng cách thêm môn học đầu tiên.</p>
                            <a href="{{ route('mon-hoc.create') }}" class="btn btn-primary rounded-pill px-4 py-2">
                                <i class="bi bi-plus-lg me-1"></i> Thêm môn học ngay
                            </a>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar')?.classList.toggle('show');
        });
    </script>
</body>

</html>