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
