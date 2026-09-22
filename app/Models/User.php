<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'ho_ten',
    'email',
    'password',
    'anh_dai_dien',
    'thong_bao_enabled',
    'thong_bao_lich_hoc',
    'thong_bao_deadline',
    'thong_bao_tap_luyen',
    'am_thanh_thong_bao',
    'ngon_ngu',
    'giao_dien',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'thong_bao_enabled' => 'boolean',
            'thong_bao_lich_hoc' => 'boolean',
            'thong_bao_deadline' => 'boolean',
            'thong_bao_tap_luyen' => 'boolean',
            'am_thanh_thong_bao' => 'boolean',
        ];
    }

    /**
     * Get avatar full URL if exists and is valid.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        try {
            if (empty($this->anh_dai_dien)) {
                return null;
            }

            $rawPath = trim($this->anh_dai_dien);

            // External URLs (e.g., OAuth/Google avatar)
            if (str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://')) {
                return $rawPath;
            }

            // Normalize path (handle slashes & backslashes)
            $relativePath = str_replace('\\', '/', $rawPath);
            $relativePath = ltrim(preg_replace('#^storage/#', '', $relativePath), '/');

            // Fail-safe: Check if file actually exists on public disk
            if (! Storage::disk('public')->exists($relativePath)) {
                return null; // File missing -> fallback to initials!
            }

            return asset('storage/' . $relativePath);
        } catch (\Throwable $e) {
            // Fail-safe: Any filesystem error will return null so page never crashes!
            return null;
        }
    }

    /**
     * Get initials from user's full name (e.g. "Nguyễn Hoàng Lâm" -> "NL", "Tôn Đức Quang" -> "TQ").
     */
    public function getInitialsAttribute(): string
    {
        try {
            $name = trim($this->ho_ten ?? '');
            if (empty($name)) {
                return 'U';
            }

            $words = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY);
            if (is_array($words) && count($words) >= 2) {
                $first = mb_substr($words[0], 0, 1);
                $last = mb_substr(end($words), 0, 1);

                return mb_strtoupper($first . $last);
            }

            if (is_array($words) && count($words) === 1) {
                return mb_strtoupper(mb_substr($words[0], 0, min(2, mb_strlen($words[0]))));
            }

            return 'U';
        } catch (\Throwable $e) {
            return 'U';
        }
    }

    public function monHocs()
    {
        return $this->hasMany(MonHoc::class);
    }

    public function lichHocs()
    {
        return $this->hasMany(LichHoc::class);
    }

    public function suKiens()
    {
        return $this->hasMany(SuKien::class);
    }

    public function keHoachTapLuyens()
    {
        return $this->hasMany(KeHoachTapLuyen::class);
    }

    public function mucTieus()
    {
        return $this->hasMany(MucTieu::class);
    }

    public function thongBaos()
    {
        return $this->hasMany(ThongBao::class);
    }
}
