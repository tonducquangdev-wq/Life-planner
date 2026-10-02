<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CongViec extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cong_viec';

    protected $fillable = [
        'user_id',
        'du_an_id',
        'ten_cong_viec',
        'mo_ta',
        'uu_tien',
        'trang_thai',
        'deadline',
        'tien_do',
        'dong_bo_calendar',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
            'tien_do' => 'integer',
            'dong_bo_calendar' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function duAn(): BelongsTo
    {
        return $this->belongsTo(DuAn::class, 'du_an_id');
    }

    public function suKien(): HasOne
    {
        return $this->hasOne(SuKien::class, 'cong_viec_id');
    }

    /**
     * Scope lọc các công việc quá hạn
     */
    public function scopeQuaHan($query)
    {
        return $query->where('trang_thai', '!=', 'hoan_thanh')
            ->whereNotNull('deadline')
            ->where('deadline', '<', now());
    }

    /**
     * Check if task is overdue
     */
    public function getIsQuaHanAttribute(): bool
    {
        return $this->trang_thai !== 'hoan_thanh'
            && $this->deadline !== null
            && $this->deadline->isPast();
    }

    /**
     * Overdue humanized description text
     */
    public function getQuaHanTextAttribute(): ?string
    {
        if (!$this->is_qua_han) {
            return null;
        }

        return 'Quá hạn ' . $this->deadline->diffForHumans(now(), [
            'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
            'parts' => 1,
        ]);
    }
}
