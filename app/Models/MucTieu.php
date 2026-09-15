<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MucTieu extends Model
{
    protected $table = 'muc_tieu';

    protected $fillable = [
    'user_id',
    'tieu_de',
    'loai_muc_tieu',
    'gia_tri_muc_tieu',
    'gia_tri_hien_tai',
    'trang_thai'
];

    public function user()
{
    return $this->belongsTo(User::class);
}
}
