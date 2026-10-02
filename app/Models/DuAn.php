<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DuAn extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'du_an';

    protected $fillable = [
        'user_id',
        'ten_du_an',
        'mo_ta',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'trang_thai',
        'mau_nhan',
    ];

    protected function casts(): array
    {
        return [
            'ngay_bat_dau' => 'date',
            'ngay_ket_thuc' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function congViecs(): HasMany
    {
        return $this->hasMany(CongViec::class, 'du_an_id');
    }

    /**
     * Calculated Project Progress percentage based on average of child tasks
     */
    public function getTienDoPercentAttribute(): int
    {
        $total = $this->tong_cong_viec_count ?? $this->congViecs()->count();
        if ($total === 0) {
            return 0;
        }

        if (isset($this->tien_do_trung_binh)) {
            return (int) round($this->tien_do_trung_binh);
        }

        $avg = $this->congViecs()->avg('tien_do');
        return (int) round($avg ?? 0);
    }
}
