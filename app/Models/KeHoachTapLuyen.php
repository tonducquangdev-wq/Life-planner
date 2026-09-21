<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeHoachTapLuyen extends Model
{
    protected $table = 'ke_hoach_tap_luyen';

    protected $fillable = [
        'user_id',
        'ten_ke_hoach',
        'mo_ta',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function buoiTaps()
    {
        return $this->hasMany(BuoiTap::class)->orderBy('thu_tu')->orderBy('id');
    }
}
