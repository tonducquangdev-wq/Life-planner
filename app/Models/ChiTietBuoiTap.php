<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietBuoiTap extends Model
{
    protected $table = 'chi_tiet_buoi_tap';

    protected $fillable = [
        'buoi_tap_id',
        'bai_tap_the_chat_id',
        'thu_tu',
        'loai_bai_tap',
        'so_sets',
        'so_reps',
        'thoi_luong',
        'don_vi_thoi_gian',
    ];

    public function buoiTap()
    {
        return $this->belongsTo(BuoiTap::class);
    }

    public function baiTapTheChat()
    {
        return $this->belongsTo(BaiTapTheChat::class);
    }

    /**
     * Trả về định dạng chuỗi thông số phù hợp theo loại bài tập
     * Ví dụ:
     * - Strength: "4 sets × 8–10 reps"
     * - Cardio: "30 phút"
     * - Core: "3 sets × 10–12 reps" hoặc "30 giây"
     */
    public function getDinhDangThongSoAttribute(): string
    {
        $type = mb_strtolower($this->loai_bai_tap ?: ($this->baiTapTheChat->loai_bai_tap ?? 'strength'));
        $unit = ($this->don_vi_thoi_gian === 'giay') ? 'giây' : 'phút';

        if ($type === 'cardio') {
            $duration = $this->thoi_luong ?: 30;
            return "{$duration} {$unit}";
        }

        if ($type === 'core') {
            if (!empty($this->thoi_luong)) {
                if (!empty($this->so_sets) && $this->so_sets > 1) {
                    return "{$this->so_sets} sets × {$this->thoi_luong} {$unit}";
                }
                return "{$this->thoi_luong} {$unit}";
            }
            $sets = $this->so_sets ?: 3;
            $reps = $this->so_reps ?: '10-12';
            return "{$sets} sets × {$reps} reps";
        }

        // Strength & Other mặc định
        if (!empty($this->thoi_luong) && empty($this->so_reps)) {
            return "{$this->thoi_luong} {$unit}";
        }

        $sets = $this->so_sets ?: 3;
        $reps = $this->so_reps ?: '8-12';
        return "{$sets} sets × {$reps} reps";
    }
}
