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
            <label class="form-label fw-bold text-dark small mb-3">Chế độ giao diện (Theme)</label>
            <div class="row g-3">
                
                <!-- Option 1: Light Mode -->
                <div class="col-12 col-md-4">
                    <label class="theme-card-option border rounded-4 p-3 d-block cursor-pointer position-relative h-100">
                        <input class="form-check-input position-absolute top-0 end-0 m-3 theme-radio-input" type="radio" name="giao_dien" value="light" {{ old('giao_dien', $user->giao_dien ?? 'light') === 'light' ? 'checked' : '' }}>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-sun-fill text-warning fs-5"></i>
                            <span class="fw-bold text-dark fs-7">Giao diện Sáng</span>
                        </div>
                        <p class="text-muted fs-8 mb-2">Phông nền sáng dịu, thanh lịch phù hợp ban ngày</p>
                        <div class="p-2 rounded-3 border bg-white d-flex gap-1 align-items-center">
                            <span class="d-inline-block rounded-circle bg-primary" style="width: 8px; height: 8px;"></span>
                            <span class="d-inline-block rounded bg-light flex-grow-1" style="height: 6px;"></span>
                        </div>
                    </label>
                </div>

                <!-- Option 2: Dark Mode -->
                <div class="col-12 col-md-4">
                    <label class="theme-card-option border rounded-4 p-3 d-block cursor-pointer position-relative h-100">
                        <input class="form-check-input position-absolute top-0 end-0 m-3 theme-radio-input" type="radio" name="giao_dien" value="dark" {{ old('giao_dien', $user->giao_dien ?? 'light') === 'dark' ? 'checked' : '' }}>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-moon-stars-fill text-info fs-5"></i>
                            <span class="fw-bold text-dark fs-7">Giao diện Tối</span>
                        </div>
                        <p class="text-muted fs-8 mb-2">Phông nền tối hiện đại, dịu mắt ban đêm & tiết kiệm pin</p>
                        <div class="p-2 rounded-3 border bg-dark d-flex gap-1 align-items-center">
                            <span class="d-inline-block rounded-circle bg-info" style="width: 8px; height: 8px;"></span>
                            <span class="d-inline-block rounded bg-secondary flex-grow-1" style="height: 6px;"></span>
                        </div>
                    </label>
                </div>

                <!-- Option 3: System Mode -->
                <div class="col-12 col-md-4">
                    <label class="theme-card-option border rounded-4 p-3 d-block cursor-pointer position-relative h-100">
                        <input class="form-check-input position-absolute top-0 end-0 m-3 theme-radio-input" type="radio" name="giao_dien" value="system" {{ old('giao_dien', $user->giao_dien ?? 'light') === 'system' ? 'checked' : '' }}>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-laptop-fill text-primary fs-5"></i>
                            <span class="fw-bold text-dark fs-7">Theo Hệ Thống</span>
                        </div>
                        <p class="text-muted fs-8 mb-2">Tự động đồng bộ theo cài đặt HĐH Windows / macOS</p>
                        <div class="p-2 rounded-3 border bg-light d-flex gap-1 align-items-center">
                            <span class="d-inline-block rounded-circle bg-secondary" style="width: 8px; height: 8px;"></span>
                            <span class="d-inline-block rounded bg-secondary-subtle flex-grow-1" style="height: 6px;"></span>
                        </div>
                    </label>
                </div>

            </div>
        </div>

        <!-- SECTION 2: NGÔN NGỮ GIAO DIỆN -->
        <div class="mb-4">
            <label class="form-label fw-bold text-dark small mb-3">Ngôn ngữ ứng dụng (Language)</label>
            <div class="row g-3">
                
                <!-- Tiếng Việt -->
                <div class="col-12 col-md-6">
                    <label class="theme-card-option border rounded-4 p-3 d-flex align-items-center justify-content-between cursor-pointer">
                        <div class="d-flex align-items-center gap-3">
                            <span class="fs-4">🇻🇳</span>
                            <div>
                                <div class="fw-bold text-dark fs-7">Tiếng Việt</div>
                                <div class="text-muted fs-8">Ngôn ngữ Tiếng Việt mặc định</div>
                            </div>
                        </div>
                        <input class="form-check-input" type="radio" name="ngon_ngu" value="vi" {{ old('ngon_ngu', $user->ngon_ngu ?? 'vi') === 'vi' ? 'checked' : '' }}>
                    </label>
                </div>

                <!-- English -->
                <div class="col-12 col-md-6">
                    <label class="theme-card-option border rounded-4 p-3 d-flex align-items-center justify-content-between cursor-pointer">
                        <div class="d-flex align-items-center gap-3">
                            <span class="fs-4">🇬🇧</span>
                            <div>
                                <div class="fw-bold text-dark fs-7">English (US)</div>
                                <div class="text-muted fs-8">English interface mode</div>
                            </div>
                        </div>
                        <input class="form-check-input" type="radio" name="ngon_ngu" value="en" {{ old('ngon_ngu', $user->ngon_ngu ?? 'vi') === 'en' ? 'checked' : '' }}>
                    </label>
                </div>

            </div>
        </div>

        <div class="d-flex align-items-center gap-3 pt-2">
            <button type="submit" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-check-lg me-1"></i>Lưu giao diện & ngôn ngữ
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
