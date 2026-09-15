<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaiTap extends Model
{
    protected $table = 'bai_tap';

    // BaiTap.php
    protected $fillable = [
        'mon_hoc_id',
        'tieu_de',
        'mo_ta',
        'han_nop',
        'muc_do_uu_tien',
        'trang_thai'
    ];

    public function monHoc()
    {
        return $this->belongsTo(MonHoc::class);
    }
}
