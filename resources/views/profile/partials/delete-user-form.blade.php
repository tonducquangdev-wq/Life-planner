<section>
    <header class="mb-3 mb-sm-4">
        <h5 class="fw-bold text-danger mb-1 fs-6 fs-sm-5 d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-danger"></i>
            <span>Khu vực nguy hiểm: Xóa tài khoản</span>
        </h5>
        <p class="text-muted small mb-0">
            Sau khi xóa tài khoản, toàn bộ dữ liệu (môn học, bài tập, kế hoạch rèn luyện và lịch sử) sẽ bị xóa vĩnh viễn theo logic hệ thống. Hành động này không thể hoàn tác.
        </p>
    </header>

    <div class="mt-3">
        <button type="button" class="btn btn-outline-danger rounded-pill px-4 py-2.5 fw-semibold fs-7 shadow-2xs w-100 w-sm-auto d-inline-flex align-items-center justify-content-center gap-1.5" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
            <i class="bi bi-trash3-fill"></i>
            <span>Xóa tài khoản</span>
        </button>
    </div>

    <!-- Modal Xác nhận xóa tài khoản (Bootstrap 5) -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 pb-0 pt-3 pt-sm-4 px-3 px-sm-4">
                    <h5 class="modal-title fw-bold text-danger fs-6 fs-sm-5 d-flex align-items-center gap-2" id="deleteAccountModalLabel">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                        <span>Xác nhận xóa tài khoản</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-body py-3 px-3 px-sm-4">
                        <div class="alert alert-danger-subtle text-danger border border-danger-subtle rounded-3 py-2 px-3 small mb-3">
                            <i class="bi bi-shield-exclamation me-1"></i><strong>Cảnh báo:</strong> Việc xóa tài khoản là vĩnh viễn và không thể khôi phục lại dữ liệu đã xóa.
                        </div>

                        <p class="text-dark fw-semibold small mb-2">
                            Vui lòng nhập mật khẩu tài khoản của bạn để xác nhận hành động:
                        </p>

                        <div class="mb-2">
                            <input type="password" class="form-control rounded-3 py-2 fs-7 {{ $errors->userDeletion->has('password') ? 'is-invalid' : '' }}" id="password" name="password" placeholder="Nhập mật khẩu hiện tại" required>
                            @if ($errors->userDeletion->has('password'))
                                <div class="invalid-feedback d-block">{{ $errors->userDeletion->first('password') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0 pb-3 pb-sm-4 px-3 px-sm-4 gap-2 flex-column-reverse flex-sm-row justify-content-end">
                        <button type="button" class="btn btn-light rounded-pill px-3.5 py-2 text-nowrap w-100 w-sm-auto" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 fw-semibold text-nowrap w-100 w-sm-auto d-inline-flex align-items-center justify-content-center gap-1.5">
                            <i class="bi bi-trash3-fill"></i>
                            <span>Xác nhận xóa</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->userDeletion->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var modalEl = document.getElementById('deleteAccountModal');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    var deleteModal = new bootstrap.Modal(modalEl);
                    deleteModal.show();
                }
            });
        </script>
    @endif
</section>
