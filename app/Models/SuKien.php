<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuKien extends Model
{
    use HasFactory;

    protected $table = 'su_kien';

    protected $fillable = [
        'user_id',
        'tieu_de',
        'mo_ta',
        'loai_su_kien',
        'thoi_gian_bat_dau',
        'thoi_gian_ket_thuc',
        'mau_hien_thi',
        'bat_thong_bao',
        'so_ngay_nhac',
    ];

    protected function casts(): array
    {
        return [
            'bat_thong_bao' => 'boolean',
            'so_ngay_nhac' => 'integer',
            'thoi_gian_bat_dau' => 'datetime',
            'thoi_gian_ket_thuc' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope lọc các sự kiện đến hạn gửi email nhắc nhở trong ngày.
     * Công thức: thoi_gian_bat_dau - so_ngay_nhac = date
     */
    public function scopeCanNhacThongBao($query, $date = null)
    {
        $targetDate = $date ? \Illuminate\Support\Carbon::parse($date)->toDateString() : \Illuminate\Support\Carbon::today()->toDateString();
        return $query->where('bat_thong_bao', true)
            ->whereRaw('DATE(DATE_SUB(thoi_gian_bat_dau, INTERVAL so_ngay_nhac DAY)) = ?', [$targetDate]);
    }
}

