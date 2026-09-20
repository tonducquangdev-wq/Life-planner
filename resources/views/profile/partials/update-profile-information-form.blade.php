<section>
    <header class="mb-4">
        <h5 class="fw-bold text-dark mb-1">
            <i class="bi bi-person-lines-fill me-2 text-primary"></i>Thông tin cá nhân
        </h5>
        <p class="text-muted small mb-0">Cập nhật họ tên và địa chỉ email tài khoản của bạn.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="ho_ten" class="form-label fw-semibold text-dark small">Họ và tên <span class="text-danger">*</span></label>
            <input type="text" class="form-control rounded-3 @error('ho_ten') is-invalid @enderror" id="ho_ten" name="ho_ten" value="{{ old('ho_ten', $user->ho_ten) }}" required autofocus autocomplete="name" placeholder="Nhập họ và tên">
            @error('ho_ten')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold text-dark small">Địa chỉ email <span class="text-danger">*</span></label>
            <input type="email" class="form-control rounded-3 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username" placeholder="Nhập địa chỉ email">
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning rounded-3 mt-3 py-2 px-3 small">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Địa chỉ email của bạn chưa được xác thực.
                    <button form="send-verification" type="submit" class="btn btn-link p-0 m-0 align-baseline small fw-semibold text-decoration-none">
                        Nhấn vào đây để gửi lại email xác thực.
                    </button>
                    @if (session('status') === 'verification-link-sent')
                        <div class="mt-2 fw-semibold text-success">
                            <i class="bi bi-check-circle-fill me-1"></i>Một liên kết xác thực mới đã được gửi tới email của bạn.
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3 mt-4">
            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                <i class="bi bi-check2 me-1"></i>Lưu thay đổi
            </button>
        </div>
    </form>
</section>