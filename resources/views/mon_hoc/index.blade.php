<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Danh Sách Môn Học - Life Planner</title>

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

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Font: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom Dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <style>
        /* Dark mode variables and enhancements for Study Module */
        [data-bs-theme="dark"] body {
            background-color: #121212 !important;
            color: #E8E8EA !important;
        }

        [data-bs-theme="dark"] .card {
            background-color: #1E1E22 !important;
            border-color: #2E2E34 !important;
            color: #E8E8EA !important;
        }

        [data-bs-theme="dark"] .modal-content {
            background-color: #26262B !important;
            border: 1px solid #2E2E34 !important;
            color: #E8E8EA !important;
        }

        [data-bs-theme="dark"] .modal-header,
        [data-bs-theme="dark"] .modal-footer {
            border-color: #2E2E34 !important;
        }

        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background-color: #1E1E22 !important;
            border-color: #2E2E34 !important;
            color: #E8E8EA !important;
        }

        [data-bs-theme="dark"] .form-control:focus,
        [data-bs-theme="dark"] .form-select:focus {
            background-color: #1E1E22 !important;
            border-color: #6C8CFF !important;
            color: #E8E8EA !important;
            box-shadow: 0 0 0 2px rgba(108, 140, 255, 0.25) !important;
        }

        [data-bs-theme="dark"] .text-dark {
            color: #E8E8EA !important;
        }

        [data-bs-theme="dark"] .text-muted {
            color: #A0A0A8 !important;
        }

        [data-bs-theme="dark"] .bg-white {
            background-color: #1E1E22 !important;
        }

        [data-bs-theme="dark"] .bg-light {
            background-color: #26262B !important;
            color: #E8E8EA !important;
        }

        [data-bs-theme="dark"] .btn-light {
            background-color: #1E1E22 !important;
            border-color: #2E2E34 !important;
            color: #E8E8EA !important;
        }

        [data-bs-theme="dark"] .btn-light:hover {
            background-color: #2E2E34 !important;
            color: #FFFFFF !important;
        }

        /* Color Swatches inside Modal */
        .color-preset-btn {
            cursor: pointer;
            transition: transform 0.15s ease, border-color 0.15s ease;
        }

        .color-preset-btn:hover {
            transform: scale(1.15);
        }

        .color-preset-btn.active {
            transform: scale(1.2);
            box-shadow: 0 0 0 2px var(--bs-primary);
        }

        /* Course Card Animation */
        @keyframes slideInDownCourse {
            from {
                opacity: 0;
                transform: translateY(-16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-newly-added {
            animation: slideInDownCourse 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
    </style>
</head>

<body>

    <!-- MAIN WRAPPER -->
    <div class="main-wrapper main-wrapper-full">

        <!-- TOPBAR TRÊN CÙNG COMPACT 58PX -->
        @include('layouts.topbar', ['title' => 'Quản lý môn học'])

        <!-- CONTENT BODY -->
        <main class="content-body py-4 px-3 px-md-4">
            <div class="container-xl p-0">

                <!-- Page Title & Primary Actions -->
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Danh sách Môn Học</h4>
                        <p class="text-muted mb-0">Quản lý danh sách các môn học, thông tin lớp học và thời gian của bạn.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('study.tutors') }}" class="btn btn-outline-primary rounded-pill px-3 py-2 shadow-xs d-inline-flex align-items-center gap-1.5">
                            <i class="bi bi-person-video3"></i>
                            <span class="fw-semibold">Tìm Gia sư</span>
                        </a>

                        <!-- NÚT MỞ MODAL THÊM MÔN HỌC (KHÔNG CHUYỂN TRANG, KHÔNG MỞ TAB MỚI) -->
                        <button type="button" class="btn btn-primary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center gap-2"
                                data-bs-toggle="modal" data-bs-target="#createMonHocModal" id="btnOpenCreateMonHoc">
                            <i class="bi bi-plus-circle fs-5"></i>
                            <span class="fw-semibold">Thêm môn học mới</span>
                        </button>
                    </div>
                </div>

                <!-- Flash Alert Nếu có -->
                @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                    <span class="fw-medium">{{ session('status') }}</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <!-- Mon Hoc Card List Grid -->
                <div class="row g-4" id="monHocGrid">
                    @forelse($monHocs as $monHoc)
                    <div class="col-12 col-md-6 col-xl-4 mon-hoc-item" id="monHocCard{{ $monHoc->id }}">
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

                                    <!-- Mon Hoc Metadata (Thông tin môn học: giảng viên, phòng học, thời gian) -->
                                    <div class="small text-muted mb-3 space-y-1">
                                        <div class="d-flex align-items-center gap-2 mb-1.5">
                                            <i class="bi bi-person-badge text-primary fs-6"></i>
                                            <span>Giảng viên: <strong class="text-dark">{{ $monHoc->giang_vien ?? 'Chưa cập nhật' }}</strong></span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 mb-1.5">
                                            <i class="bi bi-geo-alt text-danger fs-6"></i>
                                            <span>Phòng học: <strong class="text-dark">{{ $monHoc->phong_hoc ?? 'Chưa cập nhật' }}</strong></span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-calendar-event text-info fs-6"></i>
                                            <span>Thời gian: <strong class="text-dark">{{ $monHoc->ngay_bat_dau ? $monHoc->ngay_bat_dau->format('d/m/Y') : 'Linh hoạt' }}@if($monHoc->ngay_ket_thuc) - {{ $monHoc->ngay_ket_thuc->format('d/m/Y') }}@endif</strong></span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <!-- Card Action Buttons -->
                                    <div class="d-flex align-items-center justify-content-between pt-3 border-top">
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
                    <div class="col-12" id="monHocEmptyState">
                        <div class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white">
                            <div class="card-body">
                                <div class="rounded-circle bg-light d-inline-flex p-4 mb-3 text-secondary">
                                    <i class="bi bi-journal-x fs-1"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">Chưa có môn học nào</h5>
                                <p class="text-muted mb-3">Bắt đầu quản lý việc học của bạn bằng cách thêm môn học đầu tiên.</p>
                                <button type="button" class="btn btn-primary rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#createMonHocModal">
                                    <i class="bi bi-plus-lg me-1"></i> Thêm môn học ngay
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforelse
                </div>

            </div>
        </main>
    </div>

    <!-- =========================================================================
         MODAL: THÊM MÔN HỌC MỚI (BOOTSTRAP 5 MODAL - POPUP CHÍNH GIỮA MÀN HÌNH)
         ========================================================================= -->
    <div class="modal fade" id="createMonHocModal" tabindex="-1" aria-labelledby="createMonHocModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 560px;">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                
                <!-- Modal Header -->
                <div class="modal-header border-0 py-3 px-4 pb-2">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-mortarboard-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-dark fs-6 mb-0" id="createMonHocModalLabel">
                                Thêm môn học mới
                            </h6>
                            <small class="text-muted fs-8">Nhập thông tin môn học để quản lý học tập</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="btnCloseCreateModalX"></button>
                </div>

                <!-- Modal Form -->
                <form id="createMonHocForm" autocomplete="off" novalidate>
                    @csrf
                    <div class="modal-body px-4 py-3">

                        <!-- Khung thông báo lỗi server/mạng nếu có -->
                        <div class="alert alert-danger border-0 rounded-3 py-2 px-3 small d-none" id="modalAlertError" role="alert">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                                <span id="modalAlertErrorText">Vui lòng kiểm tra lại thông tin đã nhập.</span>
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- 1. Tên môn học * (BẮT BUỘC) -->
                            <div class="col-12">
                                <label for="modal_ten_mon" class="form-label fw-semibold small mb-1">
                                    Tên môn học <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control rounded-3" 
                                       id="modal_ten_mon" 
                                       name="ten_mon" 
                                       placeholder="VD: Lập trình Web nâng cao" 
                                       required>
                                <div class="invalid-feedback small" id="error_ten_mon">Vui lòng nhập tên môn học.</div>
                            </div>

                            <!-- 2. Mã môn học (Tùy chọn) -->
                            <div class="col-12 col-sm-6">
                                <label for="modal_ma_mon" class="form-label fw-semibold small mb-1">
                                    Mã môn học <small class="text-muted fw-normal">(Tùy chọn)</small>
                                </label>
                                <input type="text" 
                                       class="form-control rounded-3 font-monospace text-uppercase" 
                                       id="modal_ma_mon" 
                                       name="ma_mon" 
                                       placeholder="VD: INT1234">
                                <div class="invalid-feedback small" id="error_ma_mon"></div>
                            </div>

                            <!-- 3. Giảng viên giảng dạy -->
                            <div class="col-12 col-sm-6">
                                <label for="modal_giang_vien" class="form-label fw-semibold small mb-1">
                                    Giảng viên giảng dạy
                                </label>
                                <input type="text" 
                                       class="form-control rounded-3" 
                                       id="modal_giang_vien" 
                                       name="giang_vien" 
                                       placeholder="VD: TS. Nguyễn Văn A">
                                <div class="invalid-feedback small" id="error_giang_vien"></div>
                            </div>

                            <!-- 4. Phòng học -->
                            <div class="col-12 col-sm-6">
                                <label for="modal_phong_hoc" class="form-label fw-semibold small mb-1">
                                    Phòng học
                                </label>
                                <input type="text" 
                                       class="form-control rounded-3" 
                                       id="modal_phong_hoc" 
                                       name="phong_hoc" 
                                       placeholder="VD: Phòng B2.04, Online...">
                                <div class="invalid-feedback small" id="error_phong_hoc"></div>
                            </div>

                            <!-- 5. Trạng thái học tập -->
                            <div class="col-12 col-sm-6">
                                <label for="modal_trang_thai" class="form-label fw-semibold small mb-1">
                                    Trạng thái học tập
                                </label>
                                <select class="form-select rounded-3" id="modal_trang_thai" name="trang_thai">
                                    <option value="dang_hoc" selected>Đang học</option>
                                    <option value="da_hoan_thanh">Đã hoàn thành</option>
                                    <option value="tam_dung">Tạm dừng</option>
                                </select>
                                <div class="invalid-feedback small" id="error_trang_thai"></div>
                            </div>

                            <!-- 6. Ngày bắt đầu & Ngày kết thúc -->
                            <div class="col-12 col-sm-6">
                                <label for="modal_ngay_bat_dau" class="form-label fw-semibold small mb-1">
                                    Ngày bắt đầu
                                </label>
                                <input type="date" class="form-control rounded-3" id="modal_ngay_bat_dau" name="ngay_bat_dau">
                                <div class="invalid-feedback small" id="error_ngay_bat_dau"></div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <label for="modal_ngay_ket_thuc" class="form-label fw-semibold small mb-1">
                                    Ngày kết thúc
                                </label>
                                <input type="date" class="form-control rounded-3" id="modal_ngay_ket_thuc" name="ngay_ket_thuc">
                                <div class="invalid-feedback small" id="error_ngay_ket_thuc"></div>
                            </div>

                            <!-- 7. Màu hiển thị nhận diện -->
                            <div class="col-12">
                                <label class="form-label fw-semibold small mb-1.5 d-flex align-items-center justify-content-between">
                                    <span>Màu hiển thị</span>
                                    <small class="text-muted">Nhận diện môn học trên lịch</small>
                                </label>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <input type="color" 
                                           class="form-control form-control-color border-0 p-0 rounded-circle flex-shrink-0 cursor-pointer shadow-2xs" 
                                           id="modal_mau_sac" 
                                           name="mau_sac" 
                                           value="#6366f1" 
                                           style="width: 36px; height: 36px;"
                                           title="Chọn màu tự do">
                                    
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap" id="modalColorPresets">
                                        <button type="button" class="btn btn-sm rounded-circle p-0 color-preset-btn active" data-color="#6366f1" style="width: 24px; height: 24px; background: #6366f1;"></button>
                                        <button type="button" class="btn btn-sm rounded-circle p-0 color-preset-btn" data-color="#3b82f6" style="width: 24px; height: 24px; background: #3b82f6;"></button>
                                        <button type="button" class="btn btn-sm rounded-circle p-0 color-preset-btn" data-color="#10b981" style="width: 24px; height: 24px; background: #10b981;"></button>
                                        <button type="button" class="btn btn-sm rounded-circle p-0 color-preset-btn" data-color="#f59e0b" style="width: 24px; height: 24px; background: #f59e0b;"></button>
                                        <button type="button" class="btn btn-sm rounded-circle p-0 color-preset-btn" data-color="#ef4444" style="width: 24px; height: 24px; background: #ef4444;"></button>
                                        <button type="button" class="btn btn-sm rounded-circle p-0 color-preset-btn" data-color="#8b5cf6" style="width: 24px; height: 24px; background: #8b5cf6;"></button>
                                        <button type="button" class="btn btn-sm rounded-circle p-0 color-preset-btn" data-color="#ec4899" style="width: 24px; height: 24px; background: #ec4899;"></button>
                                    </div>
                                </div>
                                <div class="invalid-feedback small" id="error_mau_sac"></div>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer border-0 px-4 pb-4 pt-2 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal" id="btnCancelCreateModal">
                            Hủy
                        </button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold d-inline-flex align-items-center gap-1.5" id="btnSubmitCreateMonHoc">
                            <i class="bi bi-plus-lg"></i>
                            <span id="btnSubmitText">Thêm môn học</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- =========================================================================
         TOAST NOTIFICATION THÀNH CÔNG (TOP-RIGHT NOTIFICATION)
         ========================================================================= -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100; margin-top: 60px;">
        <div id="monHocSuccessToast" class="toast align-items-center text-bg-success border-0 rounded-4 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2 fw-semibold py-3 px-3">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <span id="monHocToastMessage">Môn học đã được thêm thành công!</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- FLOATING ACTION MENU -->
    <x-floating-menu />

    <!-- SCRIPT ĐIỀU KHIỂN MODAL & AJAX FORM THÊM MÔN HỌC -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const createModalEl = document.getElementById('createMonHocModal');
            const createModal = createModalEl ? new bootstrap.Modal(createModalEl) : null;
            const createForm = document.getElementById('createMonHocForm');
            const btnSubmit = document.getElementById('btnSubmitCreateMonHoc');
            const btnSubmitText = document.getElementById('btnSubmitText');
            const toastEl = document.getElementById('monHocSuccessToast');
            const toastMsgEl = document.getElementById('monHocToastMessage');
            const toast = toastEl ? new bootstrap.Toast(toastEl, { delay: 3500 }) : null;
            const alertError = document.getElementById('modalAlertError');
            const alertErrorText = document.getElementById('modalAlertErrorText');

            // Đồng bộ theme icon cho topbar nếu có
            const quickThemeBtn = document.getElementById('quickThemeBtn');
            const quickThemeIcon = document.getElementById('quickThemeIcon');

            function syncThemeIcon(theme) {
                if (!quickThemeIcon) return;
                if (theme === 'dark') {
                    quickThemeIcon.className = 'bi bi-sun-fill text-warning fs-6';
                } else {
                    quickThemeIcon.className = 'bi bi-moon-stars-fill text-primary fs-6';
                }
            }

            const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            syncThemeIcon(currentTheme);

            if (quickThemeBtn) {
                quickThemeBtn.addEventListener('click', function () {
                    const activeTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
                    const newTheme = activeTheme === 'dark' ? 'light' : 'dark';

                    document.documentElement.setAttribute('data-bs-theme', newTheme);
                    if (newTheme === 'dark') {
                        document.documentElement.classList.add('dark-theme');
                    } else {
                        document.documentElement.classList.remove('dark-theme');
                    }
                    localStorage.setItem('theme', newTheme);
                    syncThemeIcon(newTheme);

                    if (csrfToken) {
                        fetch("{{ route('profile.theme.quick-toggle') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrfToken,
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({ theme: newTheme })
                        }).catch(() => {});
                    }
                });
            }

            // Hiển thị Toast thông báo
            function showToast(message) {
                if (toastMsgEl) toastMsgEl.textContent = message;
                if (toast) toast.show();
            }

            // Xóa toàn bộ trạng thái lỗi validation trong modal
            function clearValidationErrors() {
                if (alertError) alertError.classList.add('d-none');
                createForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                createForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
            }

            // Hiển thị lỗi trường cụ thể
            function setFieldError(fieldId, errorMsg) {
                const input = document.getElementById(`modal_${fieldId}`);
                const feedback = document.getElementById(`error_${fieldId}`);
                if (input) input.classList.add('is-invalid');
                if (feedback) feedback.textContent = errorMsg;
            }

            // Chọn màu nhanh từ swatches
            const colorInput = document.getElementById('modal_mau_sac');
            const presetButtons = document.querySelectorAll('#modalColorPresets .color-preset-btn');
            presetButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const color = this.getAttribute('data-color');
                    if (colorInput) colorInput.value = color;
                    presetButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            if (colorInput) {
                colorInput.addEventListener('input', function () {
                    presetButtons.forEach(b => b.classList.remove('active'));
                });
            }

            // Ràng buộc ngày kết thúc >= ngày bắt đầu
            const startDateInput = document.getElementById('modal_ngay_bat_dau');
            const endDateInput = document.getElementById('modal_ngay_ket_thuc');
            function syncDateConstraints() {
                if (startDateInput && endDateInput) {
                    if (startDateInput.value) {
                        endDateInput.min = startDateInput.value;
                    } else {
                        endDateInput.removeAttribute('min');
                    }
                }
            }
            if (startDateInput) {
                startDateInput.addEventListener('change', syncDateConstraints);
            }

            // Focus vào Tên môn học khi Modal mở
            if (createModalEl) {
                createModalEl.addEventListener('shown.bs.modal', function () {
                    const nameInput = document.getElementById('modal_ten_mon');
                    if (nameInput) nameInput.focus();
                });

                // Reset form khi đóng modal nếu bấm Hủy
                createModalEl.addEventListener('hidden.bs.modal', function () {
                    clearValidationErrors();
                });
            }

            // Chèn thẻ môn học mới vào grid
            function appendNewMonHocCard(monHoc) {
                const emptyState = document.getElementById('monHocEmptyState');
                if (emptyState) emptyState.remove();

                const statusMap = {
                    'dang_hoc': { badge: 'bg-success-subtle text-success border-success-subtle', text: 'Đang học' },
                    'da_hoan_thanh': { badge: 'bg-primary-subtle text-primary border-primary-subtle', text: 'Đã hoàn thành' },
                    'tam_dung': { badge: 'bg-secondary-subtle text-secondary', text: 'Tạm dừng' }
                };
                const statusInfo = statusMap[monHoc.trang_thai] || statusMap['dang_hoc'];

                const colDiv = document.createElement('div');
                colDiv.className = 'col-12 col-md-6 col-xl-4 mon-hoc-item card-newly-added';
                colDiv.id = `monHocCard${monHoc.id}`;
                colDiv.innerHTML = `
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative hover-shadow transition">
                        <div style="height: 6px; background-color: ${monHoc.mau_sac || '#6366f1'};"></div>

                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-light text-dark border px-2 py-1 fw-bold fs-7 rounded-2">
                                        ${monHoc.ma_mon}
                                    </span>
                                    <span class="badge ${statusInfo.badge} border px-2 py-1 rounded-pill small">
                                        ${statusInfo.text}
                                    </span>
                                </div>

                                <h5 class="fw-bold text-dark mb-3 text-truncate" title="${monHoc.ten_mon}">
                                    <a href="${monHoc.show_url}" class="text-dark text-decoration-none hover-primary">
                                        ${monHoc.ten_mon}
                                    </a>
                                </h5>

                                <div class="small text-muted mb-3 space-y-1">
                                    <div class="d-flex align-items-center gap-2 mb-1.5">
                                        <i class="bi bi-person-badge text-primary fs-6"></i>
                                        <span>Giảng viên: <strong class="text-dark">${monHoc.giang_vien || 'Chưa cập nhật'}</strong></span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mb-1.5">
                                        <i class="bi bi-geo-alt text-danger fs-6"></i>
                                        <span>Phòng học: <strong class="text-dark">${monHoc.phong_hoc || 'Chưa cập nhật'}</strong></span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-calendar-event text-info fs-6"></i>
                                        <span>Thời gian: <strong class="text-dark">${monHoc.ngay_bat_dau ? (monHoc.ngay_bat_dau + (monHoc.ngay_ket_thuc ? ' - ' + monHoc.ngay_ket_thuc : '')) : 'Linh hoạt'}</strong></span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                    <a href="${monHoc.show_url}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="bi bi-eye me-1"></i> Xem chi tiết
                                    </a>

                                    <div class="d-flex gap-1">
                                        <a href="${monHoc.edit_url}" class="btn btn-sm btn-light border rounded-circle text-warning p-2" title="Chỉnh sửa">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light border rounded-circle text-danger p-2"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal${monHoc.id}" title="Xóa môn học">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal xóa kèm theo cho môn học mới -->
                    <div class="modal fade" id="deleteModal${monHoc.id}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold text-danger">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Xác nhận xóa
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body py-3">
                                    Bạn có chắc chắn muốn xóa môn học <strong class="text-dark">${monHoc.ten_mon}</strong> (${monHoc.ma_mon})?
                                    <p class="text-muted small mt-2 mb-0">Môn học sẽ được chuyển vào thùng rác và có thể khôi phục sau này.</p>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Hủy bỏ</button>
                                    <form action="${monHoc.destroy_url}" method="POST">
                                        <input type="hidden" name="_token" value="${csrfToken}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-danger rounded-pill px-4">Đồng ý Xóa</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                const grid = document.getElementById('monHocGrid');
                if (grid) {
                    grid.prepend(colDiv);
                }
            }

            // Xử lý gửi Form bằng Fetch API / AJAX (KHÔNG RELOAD, KHÔNG CHUYỂN TRANG)
            if (createForm) {
                createForm.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    clearValidationErrors();

                    const nameInput = document.getElementById('modal_ten_mon');
                    const tenMonVal = nameInput ? nameInput.value.trim() : '';

                    // Frontend validation: kiểm tra tên môn học
                    if (!tenMonVal) {
                        setFieldError('ten_mon', 'Vui lòng nhập tên môn học.');
                        nameInput?.focus();
                        return;
                    }

                    // Frontend validation: kiểm tra ngày kết thúc >= ngày bắt đầu
                    if (startDateInput && endDateInput && startDateInput.value && endDateInput.value) {
                        if (endDateInput.value < startDateInput.value) {
                            setFieldError('ngay_ket_thuc', 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.');
                            endDateInput.focus();
                            return;
                        }
                    }

                    // Chuẩn bị dữ liệu gửi đi
                    const formData = new FormData(createForm);

                    // Trạng thái loading
                    btnSubmit.disabled = true;
                    if (btnSubmitText) btnSubmitText.textContent = 'Đang lưu...';

                    try {
                        const response = await fetch("{{ route('mon-hoc.store') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const resData = await response.json();

                        if (response.ok && resData.success) {
                            // 1. Đóng Modal
                            if (createModal) createModal.hide();

                            // 2. Reset form
                            createForm.reset();
                            if (colorInput) colorInput.value = '#6366f1';
                            presetButtons.forEach((b, idx) => {
                                if (idx === 0) b.classList.add('active');
                                else b.classList.remove('active');
                            });

                            // 3. Cập nhật ngay danh sách môn học trên giao diện (Không cần F5)
                            if (resData.data) {
                                appendNewMonHocCard(resData.data);
                            }

                            // 4. Hiển thị Toast thông báo thành công
                            showToast(resData.message || 'Môn học đã được thêm thành công!');
                        } else if (response.status === 422) {
                            // Lỗi validation từ Laravel backend
                            const errors = resData.errors || {};
                            let firstErrorInput = null;

                            for (const [field, messages] of Object.entries(errors)) {
                                const msg = messages[0] || 'Thông tin không hợp lệ.';
                                setFieldError(field, msg);
                                if (!firstErrorInput) {
                                    firstErrorInput = document.getElementById(`modal_${field}`);
                                }
                            }

                            if (alertError && alertErrorText) {
                                alertErrorText.textContent = resData.message || 'Vui lòng kiểm tra lại thông tin các trường báo đỏ.';
                                alertError.classList.remove('d-none');
                            }

                            if (firstErrorInput) firstErrorInput.focus();
                        } else {
                            // Lỗi khác từ server
                            if (alertError && alertErrorText) {
                                alertErrorText.textContent = resData.message || 'Đã có lỗi xảy ra từ máy chủ, vui lòng thử lại.';
                                alertError.classList.remove('d-none');
                            }
                        }
                    } catch (err) {
                        console.error('Fetch error:', err);
                        if (alertError && alertErrorText) {
                            alertErrorText.textContent = 'Lỗi kết nối máy chủ, vui lòng kiểm tra mạng và thử lại.';
                            alertError.classList.remove('d-none');
                        }
                    } finally {
                        btnSubmit.disabled = false;
                        if (btnSubmitText) btnSubmitText.textContent = 'Thêm môn học';
                    }
                });
            }
        });
    </script>
</body>

</html>