<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['ho_ten', 'email', 'password', 'anh_dai_dien'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function monHocs()
    {
        return $this->hasMany(MonHoc::class);
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