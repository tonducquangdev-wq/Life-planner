<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiaSu extends Model
{
    use HasFactory;

    protected $table = 'gia_su';

    protected $fillable = [
        'ho_ten',
        'email',
        'so_dien_thoai',
        'mon_hoc_id',
        'chuyen_mon',
        'hoc_phi_theo_gio',
        'danh_gia',
        'so_danh_gia',
        'mo_ta_kinh_nghiem',
        'anh_dai_dien',
        'trang_thai',
    ];

    protected function casts(): array
    {
        return [
            'hoc_phi_theo_gio' => 'integer',
            'danh_gia' => 'decimal:1',
            'so_danh_gia' => 'integer',
        ];
    }

    public function monHoc(): BelongsTo
    {
        return $this->belongsTo(MonHoc::class, 'mon_hoc_id');
    }

    public function getHocPhiFormattedAttribute(): string
    {
        return number_format($this->hoc_phi_theo_gio, 0, ',', '.') . 'đ/giờ';
    }

    public function getAvatarUrlAttribute(): string
    {
        if (!empty($this->anh_dai_dien)) {
            return $this->anh_dai_dien;
        }

        // Tạo avatar ngẫu nhiên theo họ tên bằng UI-Avatars
        $encodedName = urlencode($this->ho_ten);
        return "https://ui-avatars.com/api/?name={$encodedName}&background=4f46e5&color=fff&size=128&bold=true";
    }
}
