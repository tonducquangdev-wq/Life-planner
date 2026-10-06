<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

            return $month >= 9 ? "{$year}-" . ($year + 1) : ($year - 1) . "-{$year}";
        }

        return null;
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
}
