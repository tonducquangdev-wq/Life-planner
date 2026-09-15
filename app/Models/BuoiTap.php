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
    'thu_tu'
];

    public function keHoachTapLuyen()
{
    return $this->belongsTo(KeHoachTapLuyen::class);
}

public function chiTietBuoiTaps()
{
    return $this->hasMany(ChiTietBuoiTap::class);
}

public function lichSuTapLuyens()
{
    return $this->hasMany(LichSuTapLuyen::class);
}
}
