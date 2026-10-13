<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class MonHoc extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mon_hoc';

    protected $fillable = [
        'user_id',
        'ma_mon',
        'ten_mon',
        'giang_vien',
        'phong_hoc',
        'so_tin_chi',
        'tien_do',
        'diem_so',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'mau_sac',
        'trang_thai',
    ];

    protected function casts(): array
    {
        return [
            'ngay_bat_dau' => 'date',
            'ngay_ket_thuc' => 'date',
            'so_tin_chi' => 'integer',
            'tien_do' => 'integer',
            'diem_so' => 'decimal:2',
        ];
    }

    protected $appends = [
        'ten_mon_hoc',
        'diem_hien_tai',
        'hoc_ky',
        'nam_hoc',
    ];

    public function getTenMonHocAttribute(): ?string
    {
        return $this->ten_mon;
    }

    public function getDiemHienTaiAttribute(): mixed
    {
        return $this->diem_so;
    }

    public function getHocKyAttribute(): ?int
    {
        if ($this->ngay_bat_dau) {
            $month = (int) $this->ngay_bat_dau->format('m');

            return ($month >= 9 || $month <= 1) ? 1 : (($month >= 2 && $month <= 6) ? 2 : 3);
        }

        return null;
    }

    public function getNamHocAttribute(): ?string
    {
        if ($this->ngay_bat_dau) {
            $year = (int) $this->ngay_bat_dau->format('Y');
            $month = (int) $this->ngay_bat_dau->format('m');

            return $month >= 9 ? "{$year}-".($year + 1) : ($year - 1)."-{$year}";
        }

        return null;
    }

    protected static function booted(): void
    {
        static::deleting(function (MonHoc $monHoc) {
            $monHoc->suKiens()->delete();
            $monHoc->lichHocs()->delete();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function baiTaps()
    {
        return $this->hasMany(BaiTap::class);
    }

    public function lichHocs()
    {
        return $this->hasMany(LichHoc::class, 'mon_hoc_id');
    }

    public function suKiens()
    {
        return $this->hasMany(SuKien::class, 'mon_hoc_id');
    }

    /**
     * Tự động đồng bộ lịch học của môn học vào bảng lich_hoc và bảng su_kien (Trang Lịch)
     *
     * @param  array|null  $daysOfWeek  Mảng thứ trong tuần (1=T2, 2=T3, ..., 7=CN)
     * @param  string|null  $gioBatDau  HH:mm (mặc định '08:00')
     * @param  string|null  $gioKetThuc  HH:mm (mặc định '10:30')
     * @return int Số sự kiện Lịch đã tạo
     */
    public function syncToCalendar(
        ?array $daysOfWeek = null,
        ?string $gioBatDau = null,
        ?string $gioKetThuc = null,
        bool $dongBoCalendar = true
    ): int {
        $userId = $this->user_id;

        $gioBatDau = ! empty($gioBatDau) ? trim($gioBatDau) : '08:00';
        $gioKetThuc = ! empty($gioKetThuc) ? trim($gioKetThuc) : '10:30';

        $selectedDays = [];
        if (! empty($daysOfWeek) && is_array($daysOfWeek)) {
            foreach ($daysOfWeek as $d) {
                $val = (int) $d;
                if ($val >= 1 && $val <= 7) {
                    $selectedDays[] = $val;
                }
            }
            $selectedDays = array_values(array_unique($selectedDays));
        }

        // Nếu người dùng không chọn thứ nào, tự động xác định thứ từ ngày bắt đầu hoặc hôm nay
        if (empty($selectedDays)) {
            if ($this->ngay_bat_dau) {
                $selectedDays[] = (int) $this->ngay_bat_dau->dayOfWeekIso;
            } else {
                $selectedDays[] = (int) Carbon::today()->dayOfWeekIso;
            }
        }

        // Cập nhật bảng lich_hoc
        $this->lichHocs()->delete();
        $ghiChu = trim(
            ($this->phong_hoc ? 'Phòng: '.$this->phong_hoc : '').
            ($this->giang_vien ? ($this->phong_hoc ? ' • ' : '').'GV: '.$this->giang_vien : '')
        );

        foreach ($selectedDays as $dayIso) {
            LichHoc::create([
                'user_id' => $userId,
                'mon_hoc_id' => $this->id,
                'ngay_trong_tuan' => $dayIso,
                'gio_bat_dau' => $gioBatDau,
                'gio_ket_thuc' => $gioKetThuc,
                'ghi_chu' => $ghiChu ?: null,
            ]);
        }

        // Xóa các sự kiện lịch cũ của môn học này trên su_kien
        $this->suKiens()->delete();

        if (! $dongBoCalendar) {
            return 0;
        }

        $startDate = $this->ngay_bat_dau ? $this->ngay_bat_dau->copy() : Carbon::today();
        $endDate = $this->ngay_ket_thuc ? $this->ngay_ket_thuc->copy() : $startDate->copy()->addWeeks(10);
        if ($endDate->lt($startDate)) {
            $endDate = $startDate->copy()->addWeeks(10);
        }

        $soTuan = max(1, min(24, (int) $startDate->diffInWeeks($endDate) + 1));
        $eventsCount = 0;

        foreach ($selectedDays as $dayIso) {
            $firstDate = $startDate->copy();
            while ($firstDate->dayOfWeekIso !== $dayIso) {
                $firstDate->addDay();
            }

            if ($firstDate->gt($endDate)) {
                continue;
            }

            $firstStart = Carbon::parse($firstDate->toDateString().' '.$gioBatDau.':00');
            $firstEnd = Carbon::parse($firstDate->toDateString().' '.$gioKetThuc.':00');
            if ($firstEnd->lte($firstStart)) {
                $firstEnd = $firstStart->copy()->addHours(2)->addMinutes(30);
            }

            $ngayKetThucLap = $endDate->toDateString();
            $nhomLapId = (string) Str::uuid();

            for ($w = 0; $w < $soTuan; $w++) {
                $instStart = $firstStart->copy()->addWeeks($w);
                $instEnd = $firstEnd->copy()->addWeeks($w);

                if ($instStart->toDateString() > $endDate->toDateString()) {
                    break;
                }

                SuKien::create([
                    'user_id' => $userId,
                    'mon_hoc_id' => $this->id,
                    'tieu_de' => 'Lịch học: '.$this->ten_mon,
                    'mo_ta' => trim('Phòng: '.($this->phong_hoc ?: 'Chưa xếp phòng').' • GV: '.($this->giang_vien ?: 'Chưa cập nhật').($this->ma_mon ? " • Mã: {$this->ma_mon}" : '')),
                    'loai_su_kien' => 'hoc_tap',
                    'thoi_gian_bat_dau' => $instStart,
                    'thoi_gian_ket_thuc' => $instEnd,
                    'mau_hien_thi' => $this->mau_sac ?: '#6366f1',
                    'bat_thong_bao' => true,
                    'so_ngay_nhac' => 1,
                    'quy_tac_lap' => 'weekly',
                    'ngay_ket_thuc_lap' => $ngayKetThucLap,
                    'nhom_lap_id' => $nhomLapId,
                ]);
                $eventsCount++;
            }
        }

        return $eventsCount;
    }
}
