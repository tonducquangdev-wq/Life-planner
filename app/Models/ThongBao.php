<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThongBao extends Model
{
    protected $table = 'thong_bao';

    protected $fillable = [
    'user_id',
    'tieu_de',
    'noi_dung',
    'loai_thong_bao',
    'da_doc'
];

    public function user()
{
    return $this->belongsTo(User::class);
}
}
