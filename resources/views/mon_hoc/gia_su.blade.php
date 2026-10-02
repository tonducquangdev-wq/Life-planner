<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm Gia Sư - Life Planner</title>

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
                    <i class="bi bi-mortarboard-fill text-primary"></i> Tìm Gia Sư & Trợ Giảng
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

                <!-- SUB NAV TABS: QUẢN LÝ MÔN HỌC / BÁO CÁO GPA / TÌM GIA SƯ -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div class="d-inline-flex p-1 bg-white border rounded-pill shadow-xs">
                        <a href="{{ route('mon-hoc.index') }}" class="btn btn-sm px-3 py-1.5 rounded-pill text-secondary fw-semibold">
                            <i class="bi bi-book-half me-1"></i> Quản lý Môn học
                        </a>
                        <a href="{{ route('bao-cao-hoc-tap.index') }}" class="btn btn-sm px-3 py-1.5 rounded-pill text-secondary fw-semibold">
                            <i class="bi bi-bar-chart-line-fill me-1"></i> Báo cáo GPA
                        </a>
                        <a href="{{ route('study.tutors') }}" class="btn btn-sm px-3 py-1.5 rounded-pill btn-primary fw-semibold shadow-xs">
                            <i class="bi bi-person-video3 me-1"></i> Tìm Gia sư
                        </a>
                    </div>

                    <div class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fs-7">
                        <i class="bi bi-check-circle-fill me-1"></i> {{ $giaSus->count() }} gia sư sẵn sàng kết nối 1-1
                    </div>
                </div>

                <!-- KHUNG TÌM KIẾM & BỘ LỌC -->
                <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
                    <form method="GET" action="{{ route('study.tutors') }}" class="row g-2 align-items-center">
                        <div class="col-12 col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3 text-muted">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="q" value="{{ $keyword ?? '' }}" 
                                       class="form-control bg-light border-start-0 rounded-end-pill" 
                                       placeholder="Tìm tên gia sư, kỹ năng, chuyên môn...">
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <select name="mon_hoc_id" class="form-select rounded-pill bg-light border-0">
                                <option value="">Tất cả môn học</option>
                                @foreach($monHocs as $mh)
                                    <option value="{{ $mh->id }}" {{ (string)($monHocId ?? '') === (string)$mh->id ? 'selected' : '' }}>
                                        {{ $mh->ten_mon }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-md-2">
                            <select name="muc_gia" class="form-select rounded-pill bg-light border-0">
                                <option value="">Mọi mức phí</option>
                                <option value="duoi_180" {{ ($mucGia ?? '') === 'duoi_180' ? 'selected' : '' }}>≤ 180.000đ/h</option>
                                <option value="tren_180" {{ ($mucGia ?? '') === 'tren_180' ? 'selected' : '' }}>&gt; 180.000đ/h</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill w-100 fw-semibold">
                                Lọc
                            </button>
                            @if(!empty($keyword) || !empty($monHocId) || !empty($mucGia))
                                <a href="{{ route('study.tutors') }}" class="btn btn-light border rounded-pill px-3" title="Đặt lại bộ lọc">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- DANH SÁCH GIA SƯ -->
                <div class="row g-4">
                    @forelse($giaSus as $tutor)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 position-relative transition-all bg-white" style="transition: transform 0.2s, box-shadow 0.2s;">
                                <div class="d-flex align-items-start gap-3 mb-3">
                                    <img src="{{ $tutor->avatar_url }}" alt="{{ $tutor->ho_ten }}" 
                                         class="rounded-circle object-fit-cover shadow-xs flex-shrink-0" 
                                         style="width: 58px; height: 58px; border: 2px solid #e0e7ff;">
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h6 class="fw-bold text-dark mb-0 text-truncate">{{ $tutor->ho_ten }}</h6>
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5 fs-8">
                                                <i class="bi bi-circle-fill fs-9 me-1"></i>Sẵn sàng
                                            </span>
                                        </div>
                                        <small class="text-primary fw-semibold d-block text-truncate mt-0.5">
                                            {{ $tutor->monHoc ? $tutor->monHoc->ten_mon : 'Chuyên gia IT' }}
                                        </small>
                                        <div class="d-flex align-items-center gap-1.5 mt-1 text-warning fs-8">
                                            <i class="bi bi-star-fill"></i>
                                            <span class="fw-bold text-dark">{{ $tutor->danh_gia }}</span>
                                            <span class="text-muted">({{ $tutor->so_danh_gia }} đánh giá)</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-2.5 rounded-3 bg-light mb-3">
                                    <div class="fs-8 fw-semibold text-secondary text-uppercase letter-spacing-1 mb-1">Chuyên môn & kỹ năng</div>
                                    <div class="fs-7 text-dark fw-medium text-truncate-2">{{ $tutor->chuyen_mon }}</div>
                                </div>

                                <p class="text-muted fs-8 mb-3 text-truncate-3" style="min-height: 48px;">
                                    {{ $tutor->mo_ta_kinh_nghiem }}
                                </p>

                                <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between">
                                    <div>
                                        <small class="text-muted d-block fs-8">Học phí tham khảo</small>
                                        <span class="fw-bold text-primary fs-6">{{ $tutor->hoc_phi_formatted }}</span>
                                    </div>
                                    <button type="button" 
                                            class="btn btn-outline-primary rounded-pill btn-sm px-3 fw-semibold d-inline-flex align-items-center gap-1.5 btn-contact-tutor"
                                            data-name="{{ $tutor->ho_ten }}"
                                            data-email="{{ $tutor->email }}"
                                            data-phone="{{ $tutor->so_dien_thoai }}"
                                            data-major="{{ $tutor->chuyen_mon }}"
                                            data-rate="{{ $tutor->hoc_phi_formatted }}"
                                            data-avatar="{{ $tutor->avatar_url }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#contactTutorModal">
                                        <i class="bi bi-chat-dots-fill"></i>
                                        <span>Liên hệ</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                                <div class="mb-3 text-muted">
                                    <i class="bi bi-search fs-1"></i>
                                </div>
                                <h6 class="fw-bold text-dark">Không tìm thấy gia sư phù hợp</h6>
                                <p class="text-muted fs-7 mb-3">Thử thay đổi từ khóa tìm kiếm hoặc chọn bộ lọc môn học khác.</p>
                                <div>
                                    <a href="{{ route('study.tutors') }}" class="btn btn-outline-primary rounded-pill px-4 btn-sm">
                                        Xóa bộ lọc tìm kiếm
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

            </div>
        </main>
    </div>

    <!-- MODAL LIÊN HỆ GIA SƯ -->
    <div class="modal fade" id="contactTutorModal" tabindex="-1" aria-labelledby="contactTutorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2" id="contactTutorModalLabel">
                        <i class="bi bi-person-check-fill"></i> Thông tin kết nối Gia sư
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3">
                        <img src="" id="modalTutorAvatar" class="rounded-circle object-fit-cover border" style="width: 54px; height: 54px;">
                        <div>
                            <h6 class="fw-bold text-dark mb-0" id="modalTutorName">Tên Gia Sư</h6>
                            <small class="text-primary fw-medium" id="modalTutorMajor">Chuyên môn</small>
                            <div class="fs-8 text-muted mt-0.5" id="modalTutorRate">180.000đ/giờ</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-8 text-uppercase fw-bold text-muted">Số điện thoại / Zalo</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-telephone-fill text-success"></i></span>
                            <input type="text" id="modalTutorPhone" class="form-control bg-light border-0 fw-bold text-dark" readonly>
                            <button class="btn btn-outline-secondary" type="button" id="btnCopyPhone" title="Sao chép số điện thoại">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-8 text-uppercase fw-bold text-muted">Email liên hệ</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-envelope-fill text-primary"></i></span>
                            <input type="text" id="modalTutorEmail" class="form-control bg-light border-0 fw-medium text-dark" readonly>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 rounded-3 fs-8 mb-0 d-flex align-items-start gap-2">
                        <i class="bi bi-info-circle-fill fs-6 flex-shrink-0 mt-0.5"></i>
                        <span>Bạn có thể liên hệ trực tiếp qua số Zalo hoặc gửi email để trao đổi lịch học và bài tập cần hỗ trợ kèm 1-1.</span>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                    <a href="#" id="btnZaloLink" target="_blank" class="btn btn-primary rounded-pill px-4 d-inline-flex align-items-center gap-1.5">
                        <i class="bi bi-chat-fill"></i> Nhắn tin Zalo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Menu (Góc phải bên dưới) -->
    <x-floating-menu />

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contactButtons = document.querySelectorAll('.btn-contact-tutor');
            const modalAvatar = document.getElementById('modalTutorAvatar');
            const modalName = document.getElementById('modalTutorName');
            const modalMajor = document.getElementById('modalTutorMajor');
            const modalRate = document.getElementById('modalTutorRate');
            const modalPhone = document.getElementById('modalTutorPhone');
            const modalEmail = document.getElementById('modalTutorEmail');
            const btnZaloLink = document.getElementById('btnZaloLink');
            const btnCopyPhone = document.getElementById('btnCopyPhone');

            contactButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const name = this.dataset.name;
                    const email = this.dataset.email || 'Chưa cập nhật';
                    const phone = this.dataset.phone || '0900000000';
                    const major = this.dataset.major;
                    const rate = this.dataset.rate;
                    const avatar = this.dataset.avatar;

                    modalAvatar.src = avatar;
                    modalName.textContent = name;
                    modalMajor.textContent = major;
                    modalRate.textContent = rate;
                    modalPhone.value = phone;
                    modalEmail.value = email;

                    const cleanPhone = phone.replace(/\D/g, '');
                    btnZaloLink.href = 'https://zalo.me/' + cleanPhone;
                });
            });

            btnCopyPhone?.addEventListener('click', function() {
                modalPhone.select();
                navigator.clipboard.writeText(modalPhone.value).then(() => {
                    this.innerHTML = '<i class="bi bi-check2"></i>';
                    setTimeout(() => {
                        this.innerHTML = '<i class="bi bi-clipboard"></i>';
                    }, 2000);
                });
            });
        });
    </script>
</body>
</html>
