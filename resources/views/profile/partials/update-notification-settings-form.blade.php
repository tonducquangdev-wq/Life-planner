<section>
    <header class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h6 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-bell-fill text-primary"></i> Cài đặt Thông báo
            </h6>
            <p class="text-muted small mb-0">
                Tùy chỉnh bật/tắt nhận thông báo nhắc nhở lịch học, bài tập deadline và lịch tập luyện.
            </p>
        </div>
    </header>

    @if (session('status') === 'notifications-updated')
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-xs mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>Cài đặt thông báo đã được cập nhật thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="post" action="{{ route('profile.notifications.update') }}">
        @csrf
        @method('patch')

        <!-- Master Switch: Bật / Tắt tất cả thông báo -->
        <div class="p-3 bg-light rounded-4 border mb-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="p-2.5 rounded-circle bg-primary-subtle text-primary">
                    <i class="bi bi-broadcast fs-5"></i>
                </div>
                <div>
                    <label for="thong_bao_enabled" class="fw-bold text-dark mb-0 cursor-pointer">Bật tất cả thông báo hệ thống</label>
                    <div class="text-muted fs-8">Cho phép Life Planner gửi thông báo nhắc nhở real-time và hiển thị trên ứng dụng</div>
                </div>
            </div>
            <div class="form-check form-switch form-switch-lg m-0">
                <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="thong_bao_enabled" name="thong_bao_enabled" {{ old('thong_bao_enabled', $user->thong_bao_enabled ?? true) ? 'checked' : '' }}>
            </div>
        </div>

        <div id="subNotificationSettings" class="d-flex flex-column gap-3 mb-4">
            <!-- Option 1: Thông báo Lịch học -->
            <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded-circle bg-info-subtle text-info">
                        <i class="bi bi-book-fill fs-6"></i>
                    </div>
                    <div>
                        <label for="thong_bao_lich_hoc" class="fw-semibold text-dark mb-0 cursor-pointer">Thông báo Lịch học</label>
                        <div class="text-muted fs-8">Nhắc nhở lịch học trước 30 phút theo thời khóa biểu môn học</div>
                    </div>
                </div>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input cursor-pointer sub-notif-switch" type="checkbox" role="switch" id="thong_bao_lich_hoc" name="thong_bao_lich_hoc" {{ old('thong_bao_lich_hoc', $user->thong_bao_lich_hoc ?? true) ? 'checked' : '' }}>
                </div>
            </div>

            <!-- Option 2: Thông báo Deadline -->
            <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded-circle bg-danger-subtle text-danger">
                        <i class="bi bi-exclamation-triangle-fill fs-6"></i>
                    </div>
                    <div>
                        <label for="thong_bao_deadline" class="fw-semibold text-dark mb-0 cursor-pointer">Thông báo Deadline & Bài tập</label>
                        <div class="text-muted fs-8">Cảnh báo hạn nộp bài tập chuẩn bị đến hạn (trước 24 giờ và trước 2 giờ)</div>
                    </div>
                </div>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input cursor-pointer sub-notif-switch" type="checkbox" role="switch" id="thong_bao_deadline" name="thong_bao_deadline" {{ old('thong_bao_deadline', $user->thong_bao_deadline ?? true) ? 'checked' : '' }}>
                </div>
            </div>

            <!-- Option 3: Thông báo Tập luyện -->
            <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded-circle bg-success-subtle text-success">
                        <i class="bi bi-activity fs-6"></i>
                    </div>
                    <div>
                        <label for="thong_bao_tap_luyen" class="fw-semibold text-dark mb-0 cursor-pointer">Thông báo Lịch tập luyện</label>
                        <div class="text-muted fs-8">Nhắc nhở buổi tập thể chất trong ngày (Push, Pull, Legs, Cardio)</div>
                    </div>
                </div>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input cursor-pointer sub-notif-switch" type="checkbox" role="switch" id="thong_bao_tap_luyen" name="thong_bao_tap_luyen" {{ old('thong_bao_tap_luyen', $user->thong_bao_tap_luyen ?? true) ? 'checked' : '' }}>
                </div>
            </div>

            <!-- Option 4: Âm thanh thông báo -->
            <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded-circle bg-warning-subtle text-warning">
                        <i class="bi bi-volume-up-fill fs-6"></i>
                    </div>
                    <div>
                        <label for="am_thanh_thong_bao" class="fw-semibold text-dark mb-0 cursor-pointer">Âm thanh thông báo</label>
                        <div class="text-muted fs-8">Phát âm thanh hiệu ứng sinh động khi có thông báo mới phát sinh</div>
                    </div>
                </div>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input cursor-pointer sub-notif-switch" type="checkbox" role="switch" id="am_thanh_thong_bao" name="am_thanh_thong_bao" {{ old('am_thanh_thong_bao', $user->am_thanh_thong_bao ?? true) ? 'checked' : '' }}>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-check-lg me-1"></i>Lưu cấu hình thông báo
            </button>
        </div>
    </form>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const masterSwitch = document.getElementById('thong_bao_enabled');
        const subSwitches = document.querySelectorAll('.sub-notif-switch');
        const subContainer = document.getElementById('subNotificationSettings');

        function toggleSubSettings() {
            const isEnabled = masterSwitch.checked;
            if (subContainer) {
                if (isEnabled) {
                    subContainer.style.opacity = '1';
                    subContainer.style.pointerEvents = 'auto';
                } else {
                    subContainer.style.opacity = '0.5';
                    subContainer.style.pointerEvents = 'none';
                }
            }
        }

        masterSwitch?.addEventListener('change', toggleSubSettings);
        toggleSubSettings();
    });
</script>
