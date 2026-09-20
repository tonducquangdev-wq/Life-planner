<?php
use App\Http\Controllers\MonHocController;
use App\Http\Controllers\TapLuyenController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('mon-hoc', MonHocController::class);
    Route::get('/tap-luyen', [TapLuyenController::class, 'index'])->name('tap-luyen.index');
    Route::post('/tap-luyen/cap-nhat-buoi-tap', [TapLuyenController::class, 'capNhatBuoiTap'])->name('tap-luyen.cap-nhat-buoi-tap');
    Route::post('/tap-luyen/hoan-thanh', [TapLuyenController::class, 'hoanThanh'])->name('tap-luyen.hoan-thanh');

    // Quản lý Kế hoạch & Buổi tập tùy chỉnh
    Route::post('/tap-luyen/ke-hoach', [TapLuyenController::class, 'taoKeHoach'])->name('tap-luyen.ke-hoach.store');
    Route::post('/tap-luyen/ke-hoach/{id}/kich-hoat', [TapLuyenController::class, 'kichHoatKeHoach'])->name('tap-luyen.ke-hoach.activate');
    Route::delete('/tap-luyen/ke-hoach/{id}', [TapLuyenController::class, 'xoaKeHoach'])->name('tap-luyen.ke-hoach.destroy');
    Route::post('/tap-luyen/buoi-tap', [TapLuyenController::class, 'themBuoiTap'])->name('tap-luyen.buoi-tap.store');
    Route::put('/tap-luyen/buoi-tap/{id}', [TapLuyenController::class, 'suaBuoiTap'])->name('tap-luyen.buoi-tap.update');
    Route::delete('/tap-luyen/buoi-tap/{id}', [TapLuyenController::class, 'xoaBuoiTap'])->name('tap-luyen.buoi-tap.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::patch('/profile/avatar', [ProfileController::class, 'updateAvatar'])
        ->name('profile.avatar.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';