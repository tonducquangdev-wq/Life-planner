<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\MonHocController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TapLuyenController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route /dashboard tự động chuyển hướng sang Lịch (trang chính của ứng dụng)
Route::get('/dashboard', function () {
    return redirect()->route('calendar.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/bao-cao-hoc-tap', [MonHocController::class, 'baoCaoHocTap'])->name('bao-cao-hoc-tap.index');
    Route::resource('mon-hoc', MonHocController::class);

    Route::get('/tap-luyen', [TapLuyenController::class, 'index'])->name('tap-luyen.index');
    Route::get('/bao-cao-tap-luyen', [TapLuyenController::class, 'baoCaoTapLuyen'])->name('bao-cao-tap-luyen.index');
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

    Route::patch('/profile/notifications', [ProfileController::class, 'updateNotifications'])
        ->name('profile.notifications.update');

    Route::post('/profile/notifications/quick-toggle', [ProfileController::class, 'quickToggleNotification'])
        ->name('profile.notifications.quick-toggle');

    Route::patch('/profile/appearance', [ProfileController::class, 'updateAppearance'])
        ->name('profile.appearance.update');

    Route::post('/profile/theme/quick-toggle', [ProfileController::class, 'quickToggleTheme'])
        ->name('profile.theme.quick-toggle');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';
