<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaiTapTheChat extends Model
{
    protected $table = 'bai_tap_the_chat';

    protected $fillable = [
    'ten_bai_tap',
    'nhom_co',
    'mo_ta'
];

    public function chiTietBuoiTaps()
{
    return $this->hasMany(ChiTietBuoiTap::class);
}
}
