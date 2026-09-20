<section>
    <header class="mb-4">
        <h5 class="fw-bold text-dark mb-1">
            <i class="bi bi-shield-lock-fill me-2 text-primary"></i>Đổi mật khẩu
        </h5>
        <p class="text-muted small mb-0">Đảm bảo tài khoản của bạn sử dụng mật khẩu mạnh để tăng cường tính bảo mật.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label fw-semibold text-dark small">Mật khẩu hiện tại <span class="text-danger">*</span></label>
            <input type="password" class="form-control rounded-3 {{ $errors->updatePassword->has('current_password') ? 'is-invalid' : '' }}" id="update_password_current_password" name="current_password" autocomplete="current-password" placeholder="Nhập mật khẩu hiện tại">
            @if ($errors->updatePassword->has('current_password'))
                <div class="invalid-feedback d-block">{{ $errors->updatePassword->first('current_password') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label fw-semibold text-dark small">Mật khẩu mới <span class="text-danger">*</span></label>
            <input type="password" class="form-control rounded-3 {{ $errors->updatePassword->has('password') ? 'is-invalid' : '' }}" id="update_password_password" name="password" autocomplete="new-password" placeholder="Nhập mật khẩu mới">
            @if ($errors->updatePassword->has('password'))
                <div class="invalid-feedback d-block">{{ $errors->updatePassword->first('password') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label fw-semibold text-dark small">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
            <input type="password" class="form-control rounded-3 {{ $errors->updatePassword->has('password_confirmation') ? 'is-invalid' : '' }}" id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password" placeholder="Nhập lại mật khẩu mới">
            @if ($errors->updatePassword->has('password_confirmation'))
                <div class="invalid-feedback d-block">{{ $errors->updatePassword->first('password_confirmation') }}</div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3 mt-4">
            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                <i class="bi bi-key-fill me-1"></i>Cập nhật mật khẩu
            </button>
        </div>
    </form>
</section>
