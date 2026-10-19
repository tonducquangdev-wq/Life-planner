<section>
    <header class="mb-3 mb-sm-4">
        <h5 class="fw-bold text-dark mb-1 fs-6 fs-sm-5 d-flex align-items-center gap-2">
            <i class="bi bi-shield-lock-fill text-info"></i>
            <span>Đổi mật khẩu</span>
        </h5>
        <p class="text-muted small mb-0">Đảm bảo tài khoản của bạn sử dụng mật khẩu mạnh để tăng cường tính bảo mật.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label fw-semibold text-dark small mb-1.5">Mật khẩu hiện tại <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0 text-muted rounded-start-3 px-3">
                    <i class="bi bi-key-fill"></i>
                </span>
                <input type="password" class="form-control border-start-0 border-end-0 py-2 fs-7 {{ $errors->updatePassword->has('current_password') ? 'is-invalid' : '' }}" id="update_password_current_password" name="current_password" autocomplete="current-password" placeholder="Nhập mật khẩu hiện tại">
                <button class="btn btn-outline-secondary border-start-0 rounded-end-3 px-3 toggle-pw-btn" type="button" data-pw-target="update_password_current_password" title="Hiện/ẩn mật khẩu">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @if ($errors->updatePassword->has('current_password'))
                <div class="invalid-feedback d-block">{{ $errors->updatePassword->first('current_password') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label fw-semibold text-dark small mb-1.5">Mật khẩu mới <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0 text-muted rounded-start-3 px-3">
                    <i class="bi bi-shield-lock-fill"></i>
                </span>
                <input type="password" class="form-control border-start-0 border-end-0 py-2 fs-7 {{ $errors->updatePassword->has('password') ? 'is-invalid' : '' }}" id="update_password_password" name="password" autocomplete="new-password" placeholder="Nhập mật khẩu mới">
                <button class="btn btn-outline-secondary border-start-0 rounded-end-3 px-3 toggle-pw-btn" type="button" data-pw-target="update_password_password" title="Hiện/ẩn mật khẩu">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @if ($errors->updatePassword->has('password'))
                <div class="invalid-feedback d-block">{{ $errors->updatePassword->first('password') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label fw-semibold text-dark small mb-1.5">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0 text-muted rounded-start-3 px-3">
                    <i class="bi bi-shield-check"></i>
                </span>
                <input type="password" class="form-control border-start-0 border-end-0 py-2 fs-7 {{ $errors->updatePassword->has('password_confirmation') ? 'is-invalid' : '' }}" id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password" placeholder="Nhập lại mật khẩu mới">
                <button class="btn btn-outline-secondary border-start-0 rounded-end-3 px-3 toggle-pw-btn" type="button" data-pw-target="update_password_password_confirmation" title="Hiện/ẩn mật khẩu">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @if ($errors->updatePassword->has('password_confirmation'))
                <div class="invalid-feedback d-block">{{ $errors->updatePassword->first('password_confirmation') }}</div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-2 mt-4 pt-1">
            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2.5 fw-semibold fs-7 shadow-xs w-100 w-sm-auto d-inline-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-shield-check fs-6"></i>
                <span>Cập nhật mật khẩu</span>
            </button>
        </div>
    </form>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.toggle-pw-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-pw-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');
                if (input) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon?.classList.replace('bi-eye', 'bi-eye-slash');
                    } else {
                        input.type = 'password';
                        icon?.classList.replace('bi-eye-slash', 'bi-eye');
                    }
                }
            });
        });
    });
</script>
