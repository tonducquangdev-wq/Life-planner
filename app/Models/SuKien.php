<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuKien extends Model
{
    protected $table = 'su_kien';

    protected $fillable = [
    'user_id',
    'tieu_de',
    'mo_ta',
    'loai_su_kien',
    'thoi_gian_bat_dau',
    'thoi_gian_ket_thuc',
    'mau_hien_thi'
];

    public function user()
{
    return $this->belongsTo(User::class);
}
}
