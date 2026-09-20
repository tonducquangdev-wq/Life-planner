<x-app-layout>
    <!-- Tiêu đề trang & Hành động chính -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Hồ sơ cá nhân</h4>
            <p class="text-muted mb-0">Quản lý thông tin tài khoản và bảo mật của bạn.</p>
        </div>
    </div>

    <!-- Thông báo trạng thái nếu có -->
    @if (session('status') === 'avatar-updated')
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-xs mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>Ảnh đại diện đã được cập nhật thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('status') === 'profile-updated')
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-xs mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>Thông tin cá nhân đã được cập nhật thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-xs mb-4" role="alert">
            <i class="bi bi-shield-check me-2"></i>Mật khẩu tài khoản đã được cập nhật thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- CỘT TRÁI: PROFILE SUMMARY CARD (Col 12 / Col-lg-4) -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 text-center">
                <div class="profile-avatar-wrapper mb-3">
                    <div class="mb-2 position-relative d-inline-block">
                        @if(!empty($user->avatar_url))
                            <img src="{{ $user->avatar_url }}" alt="Avatar" class="profile-avatar-img">
                        @else
                            <div class="profile-avatar-lg">
                                {{ $user->initials }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-primary profile-avatar-edit-btn" data-bs-toggle="modal" data-bs-target="#changeAvatarModal">
                            <i class="bi bi-camera-fill me-1"></i>Đổi ảnh
                        </button>
                    </div>
                </div>

                <h5 class="fw-bold text-dark mb-1">{{ $user->ho_ten ?? 'Người dùng' }}</h5>
                <p class="text-muted small mb-3">{{ $user->email }}</p>

                <div class="d-inline-flex align-items-center justify-content-center gap-1 mb-3">
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-semibold">
                        <i class="bi bi-patch-check-fill me-1"></i>Thành viên Student Life
                    </span>
                </div>

                <hr class="my-3 text-muted opacity-25">

                <div class="d-flex justify-content-between align-items-center text-start px-2 small text-muted">
                    <span><i class="bi bi-calendar3 me-2"></i>Ngày tham gia:</span>
                    <span class="fw-semibold text-dark">{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'Mới tham gia' }}</span>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: CÁC CARD FORM (Col 12 / Col-lg-8) -->
        <div class="col-12 col-lg-8 d-flex flex-column gap-4">

            <!-- Card 1: Thông tin cá nhân -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- Card 2: Đổi mật khẩu -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                @include('profile.partials.update-password-form')
            </div>

            <!-- Card 3: Danger Zone (Xóa tài khoản) -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 danger-zone-card">
                @include('profile.partials.delete-user-form')
            </div>

        </div>
    </div>

    <!-- Modal Đổi Avatar -->
    @include('profile.partials.update-avatar-modal')

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const avatarInput = document.getElementById('avatarInput');
            const avatarPreviewImg = document.getElementById('avatarPreviewImg');
            const avatarPreviewFallback = document.getElementById('avatarPreviewFallback');
            const avatarClientError = document.getElementById('avatarClientError');
            const changeAvatarModalEl = document.getElementById('changeAvatarModal');
            const initialAvatarUrl = @json($user->avatar_url);

            // Xử lý khi người dùng chọn file
            avatarInput?.addEventListener('change', function (e) {
                const file = e.target.files && e.target.files[0];
                if (avatarClientError) {
                    avatarClientError.classList.add('d-none');
                    avatarClientError.textContent = '';
                }
                avatarInput.classList.remove('is-invalid');

                if (!file) {
                    resetPreview();
                    return;
                }

                // Kiểm tra loại file
                const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
                if (!validTypes.includes(file.type)) {
                    if (avatarClientError) {
                        avatarClientError.textContent = 'Ảnh đại diện phải là file JPG, JPEG, PNG hoặc WEBP.';
                        avatarClientError.classList.remove('d-none');
                    }
                    avatarInput.classList.add('is-invalid');
                    avatarInput.value = '';
                    resetPreview();
                    return;
                }

                // Kiểm tra dung lượng file (tối đa 2MB = 2 * 1024 * 1024 bytes)
                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    if (avatarClientError) {
                        avatarClientError.textContent = 'Ảnh đại diện không được vượt quá 2MB.';
                        avatarClientError.classList.remove('d-none');
                    }
                    avatarInput.classList.add('is-invalid');
                    avatarInput.value = '';
                    resetPreview();
                    return;
                }

                // Đọc và preview ảnh
                const reader = new FileReader();
                reader.onload = function (event) {
                    if (avatarPreviewImg) {
                        avatarPreviewImg.src = event.target.result;
                        avatarPreviewImg.classList.remove('d-none');
                    }
                    if (avatarPreviewFallback) {
                        avatarPreviewFallback.classList.add('d-none');
                    }
                };
                reader.readAsDataURL(file);
            });

            function resetPreview() {
                if (initialAvatarUrl) {
                    if (avatarPreviewImg) {
                        avatarPreviewImg.src = initialAvatarUrl;
                        avatarPreviewImg.classList.remove('d-none');
                    }
                    if (avatarPreviewFallback) {
                        avatarPreviewFallback.classList.add('d-none');
                    }
                } else {
                    if (avatarPreviewImg) {
                        avatarPreviewImg.src = '';
                        avatarPreviewImg.classList.add('d-none');
                    }
                    if (avatarPreviewFallback) {
                        avatarPreviewFallback.classList.remove('d-none');
                    }
                }
            }

            // Khi đóng modal, reset về trạng thái ban đầu nếu chưa upload
            changeAvatarModalEl?.addEventListener('hidden.bs.modal', function () {
                avatarInput.value = '';
                if (avatarClientError) {
                    avatarClientError.classList.add('d-none');
                    avatarClientError.textContent = '';
                }
                avatarInput.classList.remove('is-invalid');
                resetPreview();
            });

            @if ($errors->has('avatar'))
                // Tự động mở modal nếu server trả về lỗi validation
                const modalInstance = new bootstrap.Modal(changeAvatarModalEl);
                modalInstance.show();
            @endif
        });
    </script>
    @endpush
</x-app-layout>
