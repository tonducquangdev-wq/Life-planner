<!-- Modal Đổi Ảnh Đại Diện (Bootstrap 5) -->
<div class="modal fade" id="changeAvatarModal" tabindex="-1" aria-labelledby="changeAvatarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data" id="changeAvatarForm">
                @csrf
                @method('PATCH')

                <div class="modal-header border-0 pb-0 pt-3 pt-sm-4 px-3 px-sm-4">
                    <h5 class="modal-title fw-bold text-dark fs-6 fs-sm-5 d-flex align-items-center gap-2" id="changeAvatarModalLabel">
                        <i class="bi bi-camera text-primary"></i>
                        <span>Đổi ảnh đại diện</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeAvatarModalBtn"></button>
                </div>

                <div class="modal-body p-3 p-sm-4 text-center">
                    <!-- KHUNG PREVIEW HÌNH TRÒN -->
                    <div class="avatar-preview-box mb-3 shadow-xs" id="avatarPreviewBox">
                        <img src="{{ $user->avatar_url ?? '' }}" 
                             alt="Avatar Preview" 
                             class="avatar-preview-img {{ empty($user->avatar_url) ? 'd-none' : '' }}" 
                             id="avatarPreviewImg">
                        <div class="profile-avatar-lg {{ !empty($user->avatar_url) ? 'd-none' : '' }}" id="avatarPreviewFallback" style="width: 100%; height: 100%; font-size: 2.25rem;">
                            {{ $user->initials }}
                        </div>
                    </div>

                    <p class="text-muted small mb-3">Xem trước ảnh đại diện hiển thị của bạn</p>

                    <!-- INPUT CHỌN FILE -->
                    <div class="text-start mb-2">
                        <label for="avatarInput" class="form-label fw-semibold text-dark small mb-1">Chọn ảnh từ thiết bị</label>
                        <input type="file" 
                               name="avatar" 
                               id="avatarInput" 
                               class="form-control rounded-3 py-2 fs-7 @error('avatar') is-invalid @enderror" 
                               accept="image/jpeg,image/png,image/webp,image/jpg" 
                               required>
                        
                        <!-- Lỗi Client-side -->
                        <div class="text-danger small mt-1 d-none" id="avatarClientError"></div>

                        <!-- Lỗi Server-side -->
                        @error('avatar')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-start small text-muted">
                        <i class="bi bi-info-circle me-1"></i>Hỗ trợ: <strong>JPG, JPEG, PNG, WEBP</strong> (Dung lượng tối đa 2MB).
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 pb-3 pb-sm-4 px-3 px-sm-4 flex-column-reverse flex-sm-row justify-content-end gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-3.5 py-2 fw-medium text-nowrap w-100 w-sm-auto" data-bs-dismiss="modal" id="cancelAvatarBtn">Hủy</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold text-nowrap w-100 w-sm-auto d-inline-flex align-items-center justify-content-center gap-1.5" id="submitAvatarBtn">
                        <i class="bi bi-cloud-arrow-up-fill"></i>
                        <span>Cập nhật ảnh</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
