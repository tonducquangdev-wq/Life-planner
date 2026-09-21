<section>
    <header class="mb-3">
        <h5 class="fw-bold text-danger mb-1">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Khu vực nguy hiểm: Xóa tài khoản
        </h5>
        <p class="text-muted small mb-0">
            Sau khi xóa tài khoản, toàn bộ dữ liệu (môn học, bài tập, kế hoạch rèn luyện và lịch sử) sẽ bị xóa vĩnh viễn theo logic hệ thống. Hành động này không thể hoàn tác.
        </p>
    </header>

    <div class="mt-3">
        <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
            <i class="bi bi-trash3-fill me-1"></i>Xóa tài khoản
        </button>
    </div>

    <!-- Modal Xác nhận xóa tài khoản (Bootstrap 5) -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger" id="deleteAccountModalLabel">
                        <i class="bi bi-exclamation-octagon-fill me-2"></i>Xác nhận xóa tài khoản
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-body py-3">
                        <div class="alert alert-danger-subtle text-danger border border-danger-subtle rounded-3 py-2 px-3 small mb-3">
                            <i class="bi bi-shield-exclamation me-1"></i><strong>Cảnh báo:</strong> Việc xóa tài khoản là vĩnh viễn và không thể khôi phục lại dữ liệu đã xóa.
                        </div>

                        <p class="text-dark fw-semibold small mb-2">
                            Vui lòng nhập mật khẩu tài khoản của bạn để xác nhận hành động:
                        </p>

                        <div class="mb-2">
                            <input type="password" class="form-control rounded-3 {{ $errors->userDeletion->has('password') ? 'is-invalid' : '' }}" id="password" name="password" placeholder="Nhập mật khẩu hiện tại" required>
                            @if ($errors->userDeletion->has('password'))
                                <div class="invalid-feedback d-block">{{ $errors->userDeletion->first('password') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">
                            <i class="bi bi-trash3-fill me-1"></i>Xác nhận xóa vĩnh viễn
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
