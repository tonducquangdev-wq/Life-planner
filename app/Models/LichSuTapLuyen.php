<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichSuTapLuyen extends Model
{
    protected $table = 'lich_su_tap_luyen';

    protected $fillable = [
    'user_id',
    'buoi_tap_id',
    'thoi_gian_bat_dau',
    'thoi_gian_ket_thuc',
    'tong_thoi_luong',
    'ghi_chu'
];

    public function user()
{
    return $this->belongsTo(User::class);
}

public function buoiTap()
{
    return $this->belongsTo(BuoiTap::class);
}
}
