<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class SuKien extends Model
{
    use HasFactory, SoftDeletes;

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
        'quy_tac_lap',
        'ngay_ket_thuc_lap',
        'nhom_lap_id',
        'cong_viec_id',
        'mon_hoc_id',
    ];

    protected function casts(): array
    {
        return [
            'bat_thong_bao' => 'boolean',
            'so_ngay_nhac' => 'integer',
            'thoi_gian_bat_dau' => 'datetime',
            'thoi_gian_ket_thuc' => 'datetime',
            'ngay_ket_thuc_lap' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function congViec(): BelongsTo
    {
        return $this->belongsTo(CongViec::class, 'cong_viec_id');
    }

    public function monHoc(): BelongsTo
    {
        return $this->belongsTo(MonHoc::class, 'mon_hoc_id');
    }

    /**
     * Scope lọc các sự kiện đến hạn gửi email nhắc nhở trong ngày.
     * Công thức: thoi_gian_bat_dau - so_ngay_nhac = date
     */
    public function scopeCanNhacThongBao($query, $date = null)
    {
        $targetDate = $date ? Carbon::parse($date)->toDateString() : Carbon::today()->toDateString();

        return $query->where('bat_thong_bao', true)
            ->whereRaw('DATE(DATE_SUB(thoi_gian_bat_dau, INTERVAL so_ngay_nhac DAY)) = ?', [$targetDate]);
    }
}
