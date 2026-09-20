<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuoiTap extends Model
{
    protected $table = 'buoi_tap';

    protected $fillable = [
        'ke_hoach_tap_luyen_id',
        'ten_buoi_tap',
        'mo_ta',
        'thu_tu',
        'ngay_trong_tuan',
    ];

    protected $casts = [
        'thu_tu' => 'integer',
        'ngay_trong_tuan' => 'integer',
    ];

    public function keHoachTapLuyen()
    {
        return $this->belongsTo(KeHoachTapLuyen::class);
    }

    public function chiTietBuoiTaps()
    {
        return $this->hasMany(ChiTietBuoiTap::class)->orderBy('thu_tu')->orderBy('id');
    }

    public function lichSuTapLuyens()
    {
        return $this->hasMany(LichSuTapLuyen::class);
    }

    public function getTenThuAttribute(): ?string
    {
        $map = [
            1 => 'Thứ 2',
            2 => 'Thứ 3',
            3 => 'Thứ 4',
            4 => 'Thứ 5',
            5 => 'Thứ 6',
            6 => 'Thứ 7',
            7 => 'Chủ Nhật',
        ];
        return $this->ngay_trong_tuan ? ($map[$this->ngay_trong_tuan] ?? null) : null;
    }
}
