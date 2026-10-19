<section>
    <header class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h6 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-palette-fill text-primary"></i> Giao Diện & Ngôn Ngữ
            </h6>
            <p class="text-muted small mb-0">
                Tùy chỉnh chế độ hiển thị Dark Mode và lựa chọn ngôn ngữ giao diện của ứng dụng.
            </p>
        </div>
    </header>

    @if (session('status') === 'appearance-updated')
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-xs mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>Cấu hình giao diện và ngôn ngữ đã được cập nhật thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="post" action="{{ route('profile.appearance.update') }}">
        @csrf
        @method('patch')

        <!-- SECTION 1: CHẾ ĐỘ GIAO DIỆN (DARK MODE) -->
        <div class="mb-4">
            <label class="form-label fw-bold text-dark small mb-2 mb-sm-3 d-flex align-items-center gap-1.5">
                <i class="bi bi-circle-half text-primary"></i>
                <span>Chế độ giao diện (Theme)</span>
            </label>
            <div class="row g-2 g-md-3">
                
                <!-- Option 1: Light Mode -->
                <div class="col-4 col-md-4">
                    <label class="theme-card-option border rounded-4 p-3 text-center text-md-start d-flex flex-column justify-content-between cursor-pointer position-relative h-100">
                        <input class="form-check-input position-absolute top-0 end-0 m-2 m-sm-3 theme-radio-input" type="radio" name="giao_dien" value="light" {{ old('giao_dien', $user->giao_dien ?? 'light') === 'light' ? 'checked' : '' }}>
                        <div>
                            <div class="d-flex flex-column flex-md-row align-items-center gap-1.5 gap-md-2 mb-1.5 mb-md-2">
                                <i class="bi bi-sun-fill text-warning fs-4 fs-md-5"></i>
                                <span class="fw-bold text-dark fs-8 fs-md-7">Sáng</span>
                            </div>
                            <p class="text-muted fs-8 mb-2 d-none d-md-block">Phông nền sáng dịu, thanh lịch phù hợp ban ngày</p>
                        </div>
                        <div class="p-1.5 p-md-2 rounded-3 border bg-white d-none d-md-flex gap-1 align-items-center mt-auto">
                            <span class="d-inline-block rounded-circle bg-primary" style="width: 8px; height: 8px;"></span>
                            <span class="d-inline-block rounded bg-light flex-grow-1" style="height: 6px;"></span>
                        </div>
                    </label>
                </div>

                <!-- Option 2: Dark Mode -->
                <div class="col-4 col-md-4">
                    <label class="theme-card-option border rounded-4 p-3 text-center text-md-start d-flex flex-column justify-content-between cursor-pointer position-relative h-100">
                        <input class="form-check-input position-absolute top-0 end-0 m-2 m-sm-3 theme-radio-input" type="radio" name="giao_dien" value="dark" {{ old('giao_dien', $user->giao_dien ?? 'light') === 'dark' ? 'checked' : '' }}>
                        <div>
                            <div class="d-flex flex-column flex-md-row align-items-center gap-1.5 gap-md-2 mb-1.5 mb-md-2">
                                <i class="bi bi-moon-stars-fill text-info fs-4 fs-md-5"></i>
                                <span class="fw-bold text-dark fs-8 fs-md-7">Tối</span>
                            </div>
                            <p class="text-muted fs-8 mb-2 d-none d-md-block">Phông nền tối hiện đại, dịu mắt ban đêm & tiết kiệm pin</p>
                        </div>
                        <div class="p-1.5 p-md-2 rounded-3 border bg-dark d-none d-md-flex gap-1 align-items-center mt-auto">
                            <span class="d-inline-block rounded-circle bg-info" style="width: 8px; height: 8px;"></span>
                            <span class="d-inline-block rounded bg-secondary flex-grow-1" style="height: 6px;"></span>
                        </div>
                    </label>
                </div>

                <!-- Option 3: System Mode -->
                <div class="col-4 col-md-4">
                    <label class="theme-card-option border rounded-4 p-3 text-center text-md-start d-flex flex-column justify-content-between cursor-pointer position-relative h-100">
                        <input class="form-check-input position-absolute top-0 end-0 m-2 m-sm-3 theme-radio-input" type="radio" name="giao_dien" value="system" {{ old('giao_dien', $user->giao_dien ?? 'light') === 'system' ? 'checked' : '' }}>
                        <div>
                            <div class="d-flex flex-column flex-md-row align-items-center gap-1.5 gap-md-2 mb-1.5 mb-md-2">
                                <i class="bi bi-laptop-fill text-primary fs-4 fs-md-5"></i>
                                <span class="fw-bold text-dark fs-8 fs-md-7">Tự động</span>
                            </div>
                            <p class="text-muted fs-8 mb-2 d-none d-md-block">Tự động đồng bộ theo cài đặt HĐH thiết bị</p>
                        </div>
                        <div class="p-1.5 p-md-2 rounded-3 border bg-light d-none d-md-flex gap-1 align-items-center mt-auto">
                            <span class="d-inline-block rounded-circle bg-secondary" style="width: 8px; height: 8px;"></span>
                            <span class="d-inline-block rounded bg-secondary-subtle flex-grow-1" style="height: 6px;"></span>
                        </div>
                    </label>
                </div>

            </div>
        </div>

        <!-- SECTION 2: NGÔN NGỮ GIAO DIỆN -->
        <div class="mb-4">
            <label class="form-label fw-bold text-dark small mb-2 mb-sm-3 d-flex align-items-center gap-1.5">
                <i class="bi bi-translate text-primary"></i>
                <span>Ngôn ngữ ứng dụng (Language)</span>
            </label>
            <div class="row g-2 g-sm-3">
                
                <!-- Tiếng Việt -->
                <div class="col-12 col-sm-6">
                    <label class="theme-card-option border rounded-4 p-3 d-flex align-items-center justify-content-between cursor-pointer h-100">
                        <div class="d-flex align-items-center gap-3 min-w-0">
                            <span class="fs-3 flex-shrink-0">🇻🇳</span>
                            <div class="min-w-0">
                                <div class="fw-bold text-dark fs-7 text-truncate">Tiếng Việt</div>
                                <div class="text-muted fs-8 text-truncate">Ngôn ngữ Tiếng Việt mặc định</div>
                            </div>
                        </div>
                        <input class="form-check-input m-0 flex-shrink-0" type="radio" name="ngon_ngu" value="vi" {{ old('ngon_ngu', $user->ngon_ngu ?? 'vi') === 'vi' ? 'checked' : '' }}>
                    </label>
                </div>

                <!-- English -->
                <div class="col-12 col-sm-6">
                    <label class="theme-card-option border rounded-4 p-3 d-flex align-items-center justify-content-between cursor-pointer h-100">
                        <div class="d-flex align-items-center gap-3 min-w-0">
                            <span class="fs-3 flex-shrink-0">🇬🇧</span>
                            <div class="min-w-0">
                                <div class="fw-bold text-dark fs-7 text-truncate">English (US)</div>
                                <div class="text-muted fs-8 text-truncate">English interface mode</div>
                            </div>
                        </div>
                        <input class="form-check-input m-0 flex-shrink-0" type="radio" name="ngon_ngu" value="en" {{ old('ngon_ngu', $user->ngon_ngu ?? 'vi') === 'en' ? 'checked' : '' }}>
                    </label>
                </div>

            </div>
        </div>

        <div class="d-flex align-items-center gap-2 pt-2">
            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2.5 fw-semibold fs-7 shadow-xs w-100 w-sm-auto d-inline-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-check2-circle fs-6"></i>
                <span>Lưu giao diện & ngôn ngữ</span>
            </button>
        </div>
    </form>
</section>

<style>
    .theme-card-option {
        transition: all 0.2s ease;
        background-color: #ffffff;
    }
    .theme-card-option:hover {
        border-color: #6366f1 !important;
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.12);
    }
    .theme-card-option:has(.form-check-input:checked) {
        border-color: #6366f1 !important;
        background-color: rgba(99, 102, 241, 0.04);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
    }
    [data-bs-theme="dark"] .theme-card-option {
        background-color: #1e293b;
        border-color: #334155 !important;
    }
    [data-bs-theme="dark"] .theme-card-option:hover {
        border-color: #818cf8 !important;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
    }
    [data-bs-theme="dark"] .theme-card-option:has(.form-check-input:checked) {
        border-color: #818cf8 !important;
        background-color: rgba(99, 102, 241, 0.16);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const themeInputs = document.querySelectorAll('.theme-radio-input');

        function applyTheme(theme) {
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
                document.body.classList.add('dark-theme');
                localStorage.setItem('theme', 'dark');
            } else if (theme === 'light') {
                document.documentElement.setAttribute('data-bs-theme', 'light');
                document.body.classList.remove('dark-theme');
                localStorage.setItem('theme', 'light');
            } else if (theme === 'system') {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.setAttribute('data-bs-theme', prefersDark ? 'dark' : 'light');
                if (prefersDark) {
                    document.body.classList.add('dark-theme');
                } else {
                    document.body.classList.remove('dark-theme');
                }
                localStorage.setItem('theme', 'system');
            }
        }

        themeInputs.forEach(input => {
            input.addEventListener('change', function () {
                applyTheme(this.value);
            });
        });
    });
</script>
