<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Môn Học - {{ $monHoc->ten_mon }}</title>
    
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
                <button class="btn btn-light d-lg-none p-1 px-2 border rounded-3" id="sidebarToggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                    <i class="bi bi-list fs-4 text-primary"></i>
                </button>
                <h5 class="fw-bold text-dark mb-0">Chi tiết môn học</h5>
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
        <main class="content-body">
            <!-- Header Bar -->
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 text-white d-flex align-items-center justify-content-center shadow-sm"
                         style="width: 54px; height: 54px; background-color: {{ $monHoc->mau_sac ?? '#6366f1' }};">
                        <i class="bi bi-book-half fs-3"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-dark-subtle text-dark border">{{ $monHoc->ma_mon }}</span>
                            @php
                                $statusBadge = match($monHoc->trang_thai) {
                                    'dang_hoc' => 'bg-success-subtle text-success border border-success-subtle',
                                    'da_hoan_thanh' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                    default => 'bg-secondary-subtle text-secondary border'
                                };
                                $statusText = match($monHoc->trang_thai) {
                                    'dang_hoc' => 'Đang học',
                                    'da_hoan_thanh' => 'Đã hoàn thành',
                                    default => 'Tạm dừng'
                                };
                            @endphp
                            <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                        </div>
                        <h4 class="fw-bold text-dark mb-0 mt-1">{{ $monHoc->ten_mon }}</h4>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('mon-hoc.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Quay lại
                    </a>
                    <a href="{{ route('mon-hoc.edit', $monHoc) }}" class="btn btn-warning text-dark rounded-pill px-3 fw-semibold">
                        <i class="bi bi-pencil-square me-1"></i> Sửa
                    </a>
                    <button type="button" class="btn btn-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $monHoc->id }}">
                        <i class="bi bi-trash me-1"></i> Xóa
                    </button>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="row g-4">
                <!-- Cột trái: Thông tin tổng quan -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                        <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Thông tin chung</h5>
                        
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Giảng viên giảng dạy</small>
                                <span class="fw-semibold text-dark">{{ $monHoc->giang_vien ?? 'Chưa cập nhật' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Phòng học</small>
                                <span class="fw-semibold text-dark">{{ $monHoc->phong_hoc ?? 'Chưa cập nhật' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Số tín chỉ</small>
                                <span class="fw-semibold text-dark">{{ $monHoc->so_tin_chi }} Tín chỉ</span>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Điểm số môn học</small>
                                <span class="fw-bold text-primary fs-5">{{ $monHoc->diem_so !== null ? number_format($monHoc->diem_so, 1) : 'Chưa có' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Ngày bắt đầu</small>
                                <span class="fw-semibold text-dark">{{ $monHoc->ngay_bat_dau ? $monHoc->ngay_bat_dau->format('d/m/Y') : 'Chưa cập nhật' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Ngày kết thúc</small>
                                <span class="fw-semibold text-dark">{{ $monHoc->ngay_ket_thuc ? $monHoc->ngay_ket_thuc->format('d/m/Y') : 'Chưa cập nhật' }}</span>
                            </div>
                        </div>

                        <!-- Tiến độ hoàn thành -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-dark">Tiến độ môn học</span>
                                <span class="fw-bold text-primary">{{ $monHoc->tien_do ?? 0 }}%</span>
                            </div>
                            <div class="progress rounded-pill" style="height: 10px;">
                                <div class="progress-bar rounded-pill" role="progressbar" 
                                     style="width: {{ $monHoc->tien_do ?? 0 }}%; background-color: {{ $monHoc->mau_sac ?? '#6366f1' }};">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cột phải: Danh sách Bài tập thuộc môn học -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <h5 class="fw-bold text-dark mb-0">Danh sách bài tập ({{ $monHoc->baiTaps->count() }})</h5>
                        </div>

                        @forelse($monHoc->baiTaps as $baiTap)
                            <div class="p-3 mb-2 rounded-3 border bg-light">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="fw-bold text-dark mb-1">{{ $baiTap->tieu_de }}</h6>
                                    @php
                                        $statusClass = match($baiTap->trang_thai) {
                                            'da_hoan_thanh' => 'bg-success-subtle text-success',
                                            'dang_thuc_hien' => 'bg-warning-subtle text-warning',
                                            default => 'bg-danger-subtle text-danger'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }} fs-8">{{ $baiTap->trang_thai }}</span>
                                </div>
                                <small class="text-muted d-block"><i class="bi bi-clock me-1"></i>Hạn nộp: {{ \Carbon\Carbon::parse($baiTap->han_nop)->format('d/m/Y H:i') }}</small>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Chú có bài tập nào được giao cho môn học này.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL XÁC NHẬN XÓA -->
    <div class="modal fade" id="deleteModal{{ $monHoc->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Xác nhận xóa môn học</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    Bạn có chắc chắn muốn xóa môn học <strong class="text-dark">{{ $monHoc->ten_mon }}</strong> (Mã: {{ $monHoc->ma_mon }})? 
                    Dữ liệu sẽ được di chuyển vào thùng rác (Soft Delete).
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

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar')?.classList.toggle('show');
        });
    </script>
</body>
</html>
