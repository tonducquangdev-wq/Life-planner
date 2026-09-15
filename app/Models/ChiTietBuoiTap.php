<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietBuoiTap extends Model
{
    protected $table = 'chi_tiet_buoi_tap';

    protected $fillable = [
    'buoi_tap_id',
    'bai_tap_the_chat_id',
    'thu_tu'
];

    public function buoiTap()
{
    return $this->belongsTo(BuoiTap::class);
}

public function baiTapTheChat()
{
    return $this->belongsTo(BaiTapTheChat::class);
}
}
