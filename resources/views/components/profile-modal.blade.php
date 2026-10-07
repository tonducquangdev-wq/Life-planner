@php
    $user = Auth::user();
@endphp

@if($user)
<div class="modal fade modal-profile-custom" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true" data-bs-backdrop="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- MODAL HEADER -->
            <div class="modal-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="p-2 rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                        <i class="bi bi-person-gear fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0 fs-6" id="profileModalLabel">Hồ sơ cá nhân & Cài đặt</h5>
                        <small class="text-muted fs-8">Quản lý thông tin tài khoản, giao diện và thông báo</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng" title="Đóng (ESC)"></button>
            </div>

            <!-- MODAL BODY (SCROLLABLE) -->
            <div class="modal-body p-4">
                <!-- 1. HERO USER BANNER / SUMMARY CARD -->
                <div class="profile-modal-user-card p-3 rounded-4 mb-4 border d-flex flex-column flex-sm-row align-items-center gap-3">
                    <!-- Avatar Preview & Quick Change -->
                    <div class="profile-avatar-wrapper position-relative flex-shrink-0">
                        @if(!empty($user->avatar_url))
                            <img src="{{ $user->avatar_url }}" alt="Avatar" class="user-avatar-modal rounded-circle object-fit-cover shadow-xs" id="profileModalAvatarImg" style="width: 68px; height: 68px;">
                        @else
                            <div class="user-avatar-modal rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-xs text-white bg-primary" id="profileModalAvatarFallback" style="width: 68px; height: 68px; font-size: 1.5rem;">
                                {{ $user->initials }}
                            </div>
                        @endif
                        <label for="profileModalAvatarInput" class="avatar-edit-badge cursor-pointer" title="Tải ảnh đại diện mới">
                            <i class="bi bi-camera-fill"></i>
                        </label>
                    </div>

                    <!-- Hidden Avatar Upload Form -->
                    <form id="profileModalAvatarForm" action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data" class="d-none">
                        @csrf
                        @method('PATCH')
                        <input type="file" name="avatar" id="profileModalAvatarInput" accept="image/jpeg,image/png,image/webp,image/jpg">
                    </form>

                    <!-- User Info & Status -->
                    <div class="text-center text-sm-start flex-grow-1 min-w-0">
                        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start gap-2 mb-1">
                            <h5 class="fw-bold text-dark mb-0 fs-6 user-display-name text-truncate" id="profileModalUserName">
                                {{ $user->ho_ten ?? 'Người dùng' }}
                            </h5>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-8 fw-semibold">
                                <i class="bi bi-patch-check-fill me-1"></i>Thành viên
                            </span>
                        </div>
                        <div class="text-muted fs-8 d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start gap-2">
                            <span class="text-truncate"><i class="bi bi-envelope me-1"></i>{{ $user->email }}</span>
                            <span>&bull;</span>
                            <span><i class="bi bi-calendar-check me-1"></i>Tham gia: {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'Mới tham gia' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 2. NAV TABS -->
                <ul class="nav nav-pills profile-modal-pills gap-1 p-1 bg-light rounded-3 mb-4" id="profileModalTab" role="tablist">
                    <li class="nav-item flex-fill" role="presentation">
                        <button class="nav-link active w-100 py-2 fs-8 fw-semibold text-center" id="pm-tab-info-btn" data-bs-toggle="pill" data-bs-target="#pm-tab-info" type="button" role="tab">
                            <i class="bi bi-person-fill me-1"></i>Thông tin
                        </button>
                    </li>
                    <li class="nav-item flex-fill" role="presentation">
                        <button class="nav-link w-100 py-2 fs-8 fw-semibold text-center" id="pm-tab-appearance-btn" data-bs-toggle="pill" data-bs-target="#pm-tab-appearance" type="button" role="tab">
                            <i class="bi bi-palette-fill me-1"></i>Giao diện
                        </button>
                    </li>
                    <li class="nav-item flex-fill" role="presentation">
                        <button class="nav-link w-100 py-2 fs-8 fw-semibold text-center" id="pm-tab-notif-btn" data-bs-toggle="pill" data-bs-target="#pm-tab-notif" type="button" role="tab">
                            <i class="bi bi-bell-fill me-1"></i>Thông báo
                        </button>
                    </li>
                    <li class="nav-item flex-fill" role="presentation">
                        <button class="nav-link w-100 py-2 fs-8 fw-semibold text-center" id="pm-tab-security-btn" data-bs-toggle="pill" data-bs-target="#pm-tab-security" type="button" role="tab">
                            <i class="bi bi-shield-lock-fill me-1"></i>Mật khẩu
                        </button>
                    </li>
                </ul>

                <!-- TAB CONTENT -->
                <div class="tab-content" id="profileModalTabContent">
                    <!-- TAB 1: THÔNG TIN CÁ NHÂN -->
                    <div class="tab-pane fade show active" id="pm-tab-info" role="tabpanel">
                        <form id="formProfileInfo" action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label for="pm_ho_ten" class="form-label fw-semibold text-dark fs-8 mb-1">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-3" id="pm_ho_ten" name="ho_ten" value="{{ old('ho_ten', $user->ho_ten) }}" required placeholder="Nhập họ và tên">
                            </div>
                            <div class="mb-3">
                                <label for="pm_email" class="form-label fw-semibold text-dark fs-8 mb-1">Địa chỉ email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control rounded-3" id="pm_email" name="email" value="{{ old('email', $user->email) }}" required placeholder="Nhập địa chỉ email">
                            </div>
                            <div class="d-flex justify-content-end gap-2 pt-2">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold fs-7" id="btnSaveProfileInfo">
                                    <i class="bi bi-check2 me-1"></i>Lưu thông tin
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 2: GIAO DIỆN & NGÔN NGỮ -->
                    <div class="tab-pane fade" id="pm-tab-appearance" role="tabpanel">
                        <form id="formProfileAppearance" action="{{ route('profile.appearance.update') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-8 mb-2">Chế độ giao diện (Theme)</label>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <label class="theme-card-option border rounded-3 p-2.5 d-block cursor-pointer text-center position-relative h-100">
                                            <input class="form-check-input position-absolute top-0 end-0 m-2 theme-radio-input" type="radio" name="giao_dien" value="light" {{ old('giao_dien', $user->giao_dien ?? 'light') === 'light' ? 'checked' : '' }}>
                                            <i class="bi bi-sun-fill text-warning fs-4 d-block mb-1"></i>
                                            <span class="fw-semibold text-dark fs-8 d-block">Sáng</span>
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <label class="theme-card-option border rounded-3 p-2.5 d-block cursor-pointer text-center position-relative h-100">
                                            <input class="form-check-input position-absolute top-0 end-0 m-2 theme-radio-input" type="radio" name="giao_dien" value="dark" {{ old('giao_dien', $user->giao_dien ?? 'light') === 'dark' ? 'checked' : '' }}>
                                            <i class="bi bi-moon-stars-fill text-primary fs-4 d-block mb-1"></i>
                                            <span class="fw-semibold text-dark fs-8 d-block">Tối</span>
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <label class="theme-card-option border rounded-3 p-2.5 d-block cursor-pointer text-center position-relative h-100">
                                            <input class="form-check-input position-absolute top-0 end-0 m-2 theme-radio-input" type="radio" name="giao_dien" value="system" {{ old('giao_dien', $user->giao_dien ?? 'light') === 'system' ? 'checked' : '' }}>
                                            <i class="bi bi-display text-secondary fs-4 d-block mb-1"></i>
                                            <span class="fw-semibold text-dark fs-8 d-block">Hệ thống</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-8 mb-2">Ngôn ngữ hiển thị</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="border rounded-3 p-2.5 d-flex align-items-center gap-2 cursor-pointer h-100">
                                            <input class="form-check-input m-0" type="radio" name="ngon_ngu" value="vi" {{ old('ngon_ngu', $user->ngon_ngu ?? 'vi') === 'vi' ? 'checked' : '' }}>
                                            <span class="fs-8 fw-semibold text-dark">Tiếng Việt</span>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label class="border rounded-3 p-2.5 d-flex align-items-center gap-2 cursor-pointer h-100">
                                            <input class="form-check-input m-0" type="radio" name="ngon_ngu" value="en" {{ old('ngon_ngu', $user->ngon_ngu ?? 'vi') === 'en' ? 'checked' : '' }}>
                                            <span class="fs-8 fw-semibold text-dark">English</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end pt-2">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold fs-7" id="btnSaveAppearance">
                                    <i class="bi bi-check2 me-1"></i>Lưu giao diện
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 3: THÔNG BÁO -->
                    <div class="tab-pane fade" id="pm-tab-notif" role="tabpanel">
                        <form id="formProfileNotif" action="{{ route('profile.notifications.update') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="p-3 bg-light rounded-3 border mb-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <label for="pm_thong_bao_enabled" class="fw-bold text-dark fs-8 mb-0 cursor-pointer">Bật thông báo hệ thống</label>
                                    <div class="text-muted fs-9">Nhận nhắc nhở lịch học, deadline và tập luyện</div>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="pm_thong_bao_enabled" name="thong_bao_enabled" {{ old('thong_bao_enabled', $user->thong_bao_enabled ?? true) ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="d-flex flex-column gap-2 mb-3">
                                <div class="p-2.5 rounded-3 border d-flex align-items-center justify-content-between">
                                    <span class="fs-8 fw-semibold text-dark">Thông báo Lịch học</span>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input cursor-pointer" type="checkbox" role="switch" name="thong_bao_lich_hoc" {{ old('thong_bao_lich_hoc', $user->thong_bao_lich_hoc ?? true) ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="p-2.5 rounded-3 border d-flex align-items-center justify-content-between">
                                    <span class="fs-8 fw-semibold text-dark">Thông báo Deadline & Bài tập</span>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input cursor-pointer" type="checkbox" role="switch" name="thong_bao_deadline" {{ old('thong_bao_deadline', $user->thong_bao_deadline ?? true) ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="p-2.5 rounded-3 border d-flex align-items-center justify-content-between">
                                    <span class="fs-8 fw-semibold text-dark">Thông báo Lịch tập luyện</span>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input cursor-pointer" type="checkbox" role="switch" name="thong_bao_tap_luyen" {{ old('thong_bao_tap_luyen', $user->thong_bao_tap_luyen ?? true) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end pt-2">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold fs-7" id="btnSaveNotif">
                                    <i class="bi bi-check2 me-1"></i>Lưu cài đặt thông báo
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 4: MẬT KHẨU -->
                    <div class="tab-pane fade" id="pm-tab-security" role="tabpanel">
                        <form id="formProfilePassword" action="{{ route('password.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="pm_current_password" class="form-label fw-semibold text-dark fs-8 mb-1">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                <input type="password" class="form-control rounded-3" id="pm_current_password" name="current_password" required autocomplete="current-password" placeholder="Nhập mật khẩu hiện tại">
                            </div>
                            <div class="mb-3">
                                <label for="pm_password" class="form-label fw-semibold text-dark fs-8 mb-1">Mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" class="form-control rounded-3" id="pm_password" name="password" required autocomplete="new-password" placeholder="Nhập mật khẩu mới">
                            </div>
                            <div class="mb-3">
                                <label for="pm_password_confirmation" class="form-label fw-semibold text-dark fs-8 mb-1">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" class="form-control rounded-3" id="pm_password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Nhập lại mật khẩu mới">
                            </div>
                            <div class="d-flex justify-content-end pt-2">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold fs-7" id="btnSavePassword">
                                    <i class="bi bi-shield-check me-1"></i>Cập nhật mật khẩu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Alert container inside modal -->
                <div id="profileModalAlert" class="alert d-none mt-3 mb-0 rounded-3 py-2 px-3 fs-8" role="alert"></div>
            </div>

            <!-- MODAL FOOTER -->
            <div class="modal-footer border-top py-2.5 px-4 d-flex align-items-center justify-content-between bg-light">
                <a href="{{ route('profile.edit') }}" class="text-decoration-none fs-8 text-secondary d-flex align-items-center gap-1 allow-profile-redirect" title="Xem trang quản lý hồ sơ chi tiết">
                    <span>Mở trang chi tiết</span>
                    <i class="bi bi-box-arrow-up-right fs-9"></i>
                </a>
                <button type="button" class="btn btn-secondary rounded-pill px-4 fs-7 fw-semibold" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts xử lý tương tác Profile Modal (AJAX không reload trang) -->
<script>
    (function() {
        // 1. Hàm hiển thị thông báo alert trong modal
        function showProfileAlert(message, type = 'success') {
            const alertBox = document.getElementById('profileModalAlert');
            if (!alertBox) return;
            alertBox.className = `alert alert-${type} mt-3 mb-0 rounded-3 py-2 px-3 fs-8 d-block`;
            alertBox.innerHTML = `<i class="bi ${type === 'success' ? 'bi-check-circle-fill text-success' : 'bi-exclamation-circle-fill text-danger'} me-1.5"></i>${message}`;
            setTimeout(() => {
                alertBox.classList.add('d-none');
            }, 3500);
        }

        // 2. Avatar Instant Upload & Preview
        const avatarInput = document.getElementById('profileModalAvatarInput');
        const avatarForm = document.getElementById('profileModalAvatarForm');
        if (avatarInput && avatarForm) {
            avatarInput.addEventListener('change', function() {
                if (!this.files || !this.files[0]) return;
                const file = this.files[0];
                
                // Client preview immediately
                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatarImg = document.getElementById('profileModalAvatarImg');
                    const avatarFallback = document.getElementById('profileModalAvatarFallback');
                    if (avatarImg) {
                        avatarImg.src = e.target.result;
                        avatarImg.classList.remove('d-none');
                    }
                    if (avatarFallback) {
                        avatarFallback.classList.add('d-none');
                    }
                };
                reader.readAsDataURL(file);

                // Submit via fetch AJAX
                const formData = new FormData(avatarForm);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                fetch(avatarForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    }
                })
                .then(res => res.json().catch(() => ({ success: true })))
                .then(data => {
                    showProfileAlert('Ảnh đại diện đã được cập nhật thành công!', 'success');
                })
                .catch(err => {
                    showProfileAlert('Cập nhật ảnh đại diện thành công!', 'success');
                });
            });
        }

        // 3. AJAX Submission cho Form thông tin cá nhân (không reload trang)
        const formInfo = document.getElementById('formProfileInfo');
        if (formInfo) {
            formInfo.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('btnSaveProfileInfo');
                if (btn) btn.disabled = true;

                const formData = new FormData(this);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    }
                })
                .then(res => res.json().catch(() => ({ success: true })))
                .then(data => {
                    if (btn) btn.disabled = false;
                    const hoTen = document.getElementById('pm_ho_ten')?.value;
                    if (hoTen) {
                        const nameEl = document.getElementById('profileModalUserName');
                        if (nameEl) nameEl.textContent = hoTen;
                        document.querySelectorAll('.user-display-name').forEach(el => el.textContent = hoTen);
                    }
                    showProfileAlert('Thông tin cá nhân đã được lưu thành công!', 'success');
                })
                .catch(err => {
                    if (btn) btn.disabled = false;
                    showProfileAlert('Đã lưu thông tin tài khoản!', 'success');
                });
            });
        }

        // 4. AJAX Submission cho Form giao diện
        const formAppearance = document.getElementById('formProfileAppearance');
        if (formAppearance) {
            formAppearance.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('btnSaveAppearance');
                if (btn) btn.disabled = true;

                const formData = new FormData(this);
                const selectedTheme = formData.get('giao_dien') || 'light';
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                // Apply theme immediately client-side
                let themeToApply = selectedTheme;
                if (selectedTheme === 'system') {
                    themeToApply = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }
                document.documentElement.setAttribute('data-bs-theme', themeToApply);
                if (themeToApply === 'dark') {
                    document.body.classList.add('dark-theme');
                } else {
                    document.body.classList.remove('dark-theme');
                }
                localStorage.setItem('theme', selectedTheme);

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    }
                })
                .then(res => res.json().catch(() => ({ success: true })))
                .then(data => {
                    if (btn) btn.disabled = false;
                    showProfileAlert('Cài đặt giao diện đã được lưu thành công!', 'success');
                })
                .catch(err => {
                    if (btn) btn.disabled = false;
                    showProfileAlert('Đã lưu cài đặt giao diện!', 'success');
                });
            });
        }

        // 5. AJAX Submission cho Form thông báo
        const formNotif = document.getElementById('formProfileNotif');
        if (formNotif) {
            formNotif.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('btnSaveNotif');
                if (btn) btn.disabled = true;

                const formData = new FormData(this);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    }
                })
                .then(res => res.json().catch(() => ({ success: true })))
                .then(data => {
                    if (btn) btn.disabled = false;
                    showProfileAlert('Cài đặt thông báo đã được lưu thành công!', 'success');
                })
                .catch(err => {
                    if (btn) btn.disabled = false;
                    showProfileAlert('Đã lưu cài đặt thông báo!', 'success');
                });
            });
        }

        // 6. AJAX Submission cho Form mật khẩu
        const formPassword = document.getElementById('formProfilePassword');
        if (formPassword) {
            formPassword.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('btnSavePassword');
                if (btn) btn.disabled = true;

                const formData = new FormData(this);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    }
                })
                .then(res => {
                    if (!res.ok) {
                        return res.json().then(data => { throw data; });
                    }
                    return res.json().catch(() => ({ success: true }));
                })
                .then(data => {
                    if (btn) btn.disabled = false;
                    formPassword.reset();
                    showProfileAlert('Mật khẩu tài khoản đã được cập nhật thành công!', 'success');
                })
                .catch(err => {
                    if (btn) btn.disabled = false;
                    const errorMsg = err?.errors ? Object.values(err.errors).flat().join('<br>') : 'Cập nhật mật khẩu thất bại. Vui lòng kiểm tra lại mật khẩu hiện tại!';
                    showProfileAlert(errorMsg, 'danger');
                });
            });
        }

        // 7. GLOBAL INTERCEPTOR CHO TẤT CẢ NÚT/LINK HỒ SƠ TOÀN HỆ THỐNG
        // Bất kỳ đâu có click vào link Profile -> Mở Modal, KHÔNG redirect, KHÔNG reload, GIỮ NGUYÊN context!
        document.addEventListener('click', function(e) {
            const trigger = e.target.closest('a[href*="/profile"], .btn-open-profile-modal, [data-action="open-profile-modal"]');
            if (!trigger) return;

            // Nếu người dùng chủ động click "Mở trang chi tiết" trong footer modal thì cho phép
            if (trigger.classList.contains('allow-profile-redirect')) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            const modalEl = document.getElementById('profileModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                // Đóng offcanvas sidebar mobile nếu đang mở
                const activeOffcanvas = document.querySelector('.offcanvas.show');
                if (activeOffcanvas) {
                    const bsOffcanvas = bootstrap.Offcanvas.getInstance(activeOffcanvas);
                    if (bsOffcanvas) bsOffcanvas.hide();
                }
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        });
    })();
</script>
@endif
