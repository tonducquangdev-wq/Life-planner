<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LichHoc extends Model
{
    protected $table = 'lich_hoc';

    protected $fillable = [
        'user_id',
        'mon_hoc_id',
        'ngay_trong_tuan',
        'gio_bat_dau',
        'gio_ket_thuc',
        'ghi_chu',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function monHoc(): BelongsTo
    {
        return $this->belongsTo(MonHoc::class, 'mon_hoc_id');
    }
}
