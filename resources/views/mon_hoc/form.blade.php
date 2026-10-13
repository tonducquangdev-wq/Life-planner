@csrf

<div class="row g-3">
    <!-- Mã môn học -->
    <div class="col-md-4">
        <label for="ma_mon" class="form-label fw-semibold">Mã môn học <span class="text-danger">*</span></label>
        <input type="text" 
               class="form-control @error('ma_mon') is-invalid @enderror" 
               id="ma_mon" 
               name="ma_mon" 
               value="{{ old('ma_mon', $monHoc->ma_mon ?? '') }}" 
               placeholder="VD: INT1234" 
               required>
        @error('ma_mon')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Tên môn học -->
    <div class="col-md-8">
        <label for="ten_mon" class="form-label fw-semibold">Tên môn học <span class="text-danger">*</span></label>
        <input type="text" 
               class="form-control @error('ten_mon') is-invalid @enderror" 
               id="ten_mon" 
               name="ten_mon" 
               value="{{ old('ten_mon', $monHoc->ten_mon ?? '') }}" 
               placeholder="VD: Lập trình Web nâng cao" 
               required>
        @error('ten_mon')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Giảng viên -->
    <div class="col-md-6">
        <label for="giang_vien" class="form-label fw-semibold">Giảng viên giảng dạy</label>
        <input type="text" 
               class="form-control @error('giang_vien') is-invalid @enderror" 
               id="giang_vien" 
               name="giang_vien" 
               value="{{ old('giang_vien', $monHoc->giang_vien ?? '') }}" 
               placeholder="VD: TS. Nguyễn Văn A">
        @error('giang_vien')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Phòng học -->
    <div class="col-md-6">
        <label for="phong_hoc" class="form-label fw-semibold">Phòng học</label>
        <input type="text" 
               class="form-control @error('phong_hoc') is-invalid @enderror" 
               id="phong_hoc" 
               name="phong_hoc" 
               value="{{ old('phong_hoc', $monHoc->phong_hoc ?? '') }}" 
               placeholder="VD: Phòng B2.04">
        @error('phong_hoc')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>



    <!-- Ngày bắt đầu -->
    <div class="col-md-6">
        <label for="ngay_bat_dau" class="form-label fw-semibold">Ngày bắt đầu</label>
        <input type="date" 
               class="form-control @error('ngay_bat_dau') is-invalid @enderror" 
               id="ngay_bat_dau" 
               name="ngay_bat_dau" 
               value="{{ old('ngay_bat_dau', isset($monHoc) && $monHoc->ngay_bat_dau ? $monHoc->ngay_bat_dau->format('Y-m-d') : '') }}">
        @error('ngay_bat_dau')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Ngày kết thúc -->
    <div class="col-md-6">
        <label for="ngay_ket_thuc" class="form-label fw-semibold">Ngày kết thúc</label>
        <input type="date" 
               class="form-control @error('ngay_ket_thuc') is-invalid @enderror" 
               id="ngay_ket_thuc" 
               name="ngay_ket_thuc" 
               value="{{ old('ngay_ket_thuc', isset($monHoc) && $monHoc->ngay_ket_thuc ? $monHoc->ngay_ket_thuc->format('Y-m-d') : '') }}">
        @error('ngay_ket_thuc')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Màu hiển thị -->
    <div class="col-md-6">
        <label for="mau_sac" class="form-label fw-semibold">Màu hiển thị</label>
        <div class="d-flex align-items-center gap-2">
            <input type="color" 
                   class="form-control form-control-color @error('mau_sac') is-invalid @enderror" 
                   id="mau_sac" 
                   name="mau_sac" 
                   value="{{ old('mau_sac', $monHoc->mau_sac ?? '#6366f1') }}" 
                   title="Chọn màu đại diện">
            <span class="text-muted small">Chọn màu nhận diện cho môn học</span>
        </div>
        @error('mau_sac')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <!-- Trạng thái -->
    <div class="col-md-6">
        <label for="trang_thai" class="form-label fw-semibold">Trạng thái <span class="text-danger">*</span></label>
        <select class="form-select @error('trang_thai') is-invalid @enderror" id="trang_thai" name="trang_thai" required>
            <option value="dang_hoc" {{ old('trang_thai', $monHoc->trang_thai ?? '') == 'dang_hoc' ? 'selected' : '' }}>Đang học</option>
            <option value="da_hoan_thanh" {{ old('trang_thai', $monHoc->trang_thai ?? '') == 'da_hoan_thanh' ? 'selected' : '' }}>Đã hoàn thành</option>
            <option value="tam_dung" {{ old('trang_thai', $monHoc->trang_thai ?? '') == 'tam_dung' ? 'selected' : '' }}>Tạm dừng</option>
        </select>
        @error('trang_thai')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Lịch học & Đồng bộ Lịch trình -->
    <div class="col-12">
        <div class="card border bg-light-subtle rounded-3 p-3 mt-2">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="form-label fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-check text-primary fs-5"></i>
                    <span>Lịch học hàng tuần & Đồng bộ Lịch trình</span>
                </label>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input cursor-pointer" type="checkbox" id="dong_bo_calendar" name="dong_bo_calendar" value="1" checked>
                    <label class="form-check-label small fw-semibold text-primary cursor-pointer" for="dong_bo_calendar">
                        Tự động thêm vào Lịch trình
                    </label>
                </div>
            </div>
            <p class="text-muted small mb-3">
                Chọn các thứ học trong tuần và khung giờ học để hệ thống tự động sinh thời khóa biểu vào Trang Lịch.
            </p>

            @php
                $existingDays = isset($monHoc) && $monHoc->lichHocs ? $monHoc->lichHocs->pluck('ngay_trong_tuan')->toArray() : [];
                $firstLich = isset($monHoc) && $monHoc->lichHocs ? $monHoc->lichHocs->first() : null;
                $defaultStartH = $firstLich ? substr($firstLich->gio_bat_dau, 0, 5) : '08:00';
                $defaultEndH = $firstLich ? substr($firstLich->gio_ket_thuc, 0, 5) : '10:30';
            @endphp

            <label class="form-label small fw-semibold text-muted mb-1.5">Các thứ học trong tuần:</label>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                @foreach([1 => 'Thứ 2', 2 => 'Thứ 3', 3 => 'Thứ 4', 4 => 'Thứ 5', 5 => 'Thứ 6', 6 => 'Thứ 7', 7 => 'Chủ Nhật'] as $dowVal => $dowName)
                    <input type="checkbox" class="btn-check" id="dow_{{ $dowVal }}" name="ngay_trong_tuan[]" value="{{ $dowVal }}" autocomplete="off" {{ in_array($dowVal, old('ngay_trong_tuan', $existingDays)) ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary btn-sm rounded-pill px-3" for="dow_{{ $dowVal }}">{{ $dowName }}</label>
                @endforeach
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="gio_bat_dau" class="form-label small fw-semibold text-muted mb-1">Giờ bắt đầu học</label>
                    <input type="time" class="form-control" id="gio_bat_dau" name="gio_bat_dau" value="{{ old('gio_bat_dau', $defaultStartH) }}">
                </div>
                <div class="col-md-6">
                    <label for="gio_ket_thuc" class="form-label small fw-semibold text-muted mb-1">Giờ kết thúc học</label>
                    <input type="time" class="form-control" id="gio_ket_thuc" name="gio_ket_thuc" value="{{ old('gio_ket_thuc', $defaultEndH) }}">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startInput = document.getElementById('ngay_bat_dau');
        const endInput = document.getElementById('ngay_ket_thuc');

        function syncDateConstraints() {
            if (startInput && endInput) {
                if (startInput.value) {
                    endInput.min = startInput.value;
                } else {
                    endInput.removeAttribute('min');
                }
            }
        }

        if (startInput && endInput) {
            syncDateConstraints();
            startInput.addEventListener('change', syncDateConstraints);
            startInput.addEventListener('input', syncDateConstraints);
        }
    });
</script>
