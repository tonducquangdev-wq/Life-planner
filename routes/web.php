<?php

use App\Http\Controllers\Api\PushNotificationController;
use App\Http\Controllers\BaiTapController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CongViecController;
use App\Http\Controllers\CongViecDashboardController;
use App\Http\Controllers\DuAnController;
use App\Http\Controllers\MonHocController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuKienController;
use App\Http\Controllers\TapLuyenController;
use App\Http\Controllers\WorldChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('calendar.index');
});

// Route /dashboard tự động chuyển hướng sang Lịch (trang chính của ứng dụng)
Route::get('/dashboard', function () {
    return redirect()->route('calendar.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

    // CRUD Sự kiện Lịch (Bảng su_kien)
    Route::get('/calendar/events', [SuKienController::class, 'index'])->name('calendar.events.index');
    Route::post('/calendar/events', [SuKienController::class, 'store'])->name('calendar.events.store');
    Route::put('/calendar/events/{id}', [SuKienController::class, 'update'])->name('calendar.events.update');
    Route::delete('/calendar/events/{id}', [SuKienController::class, 'destroy'])->name('calendar.events.destroy');
    Route::get('/calendar/deadlines', [CalendarController::class, 'deadlines'])->name('calendar.deadlines');

    Route::get('/bao-cao-hoc-tap', [MonHocController::class, 'baoCaoHocTap'])->name('bao-cao-hoc-tap.index');
    Route::get('/study/tutors', [MonHocController::class, 'danhSachGiaSu'])->name('study.tutors');
    Route::get('/mon-hoc/gia-su', [MonHocController::class, 'danhSachGiaSu'])->name('mon-hoc.gia-su');
    Route::post('/gia-su/dang-ky', [MonHocController::class, 'dangKyGiaSu'])->name('gia-su.dang-ky');
    Route::resource('mon-hoc', MonHocController::class);
    Route::patch('/bai-tap/{baiTap}/toggle-status', [BaiTapController::class, 'toggleStatus'])->name('bai-tap.toggle-status');
    Route::resource('bai-tap', BaiTapController::class);

    Route::get('/tap-luyen', [TapLuyenController::class, 'index'])->name('tap-luyen.index');
    Route::get('/bao-cao-tap-luyen', [TapLuyenController::class, 'baoCaoTapLuyen'])->name('bao-cao-tap-luyen.index');
    Route::get('/workout/checklist', [TapLuyenController::class, 'checklist'])->name('workout.checklist');
    Route::get('/workout/community', [TapLuyenController::class, 'community'])->name('workout.community');
    Route::post('/workout/share/{id}', [TapLuyenController::class, 'generateShareCode'])->name('workout.share.generate');
    Route::get('/workout/share/{code}', [TapLuyenController::class, 'viewSharedPlan'])->name('workout.share.view');
    Route::post('/workout/copy/{code}', [TapLuyenController::class, 'copySharedPlan'])->name('workout.copy');
    Route::post('/tap-luyen/cap-nhat-buoi-tap', [TapLuyenController::class, 'capNhatBuoiTap'])->name('tap-luyen.cap-nhat-buoi-tap');
    Route::post('/tap-luyen/hoan-thanh', [TapLuyenController::class, 'hoanThanh'])->name('tap-luyen.hoan-thanh');

    // Quản lý Kế hoạch & Buổi tập tùy chỉnh
    Route::post('/tap-luyen/ke-hoach', [TapLuyenController::class, 'taoKeHoach'])->name('tap-luyen.ke-hoach.store');
    Route::post('/tap-luyen/ke-hoach-mau/ap-dung', [TapLuyenController::class, 'apDungLichMau'])->name('tap-luyen.ke-hoach.ap-dung-mau');
    Route::post('/tap-luyen/ke-hoach/{id}/kich-hoat', [TapLuyenController::class, 'kichHoatKeHoach'])->name('tap-luyen.ke-hoach.activate');
    Route::delete('/tap-luyen/ke-hoach/{id}', [TapLuyenController::class, 'xoaKeHoach'])->name('tap-luyen.ke-hoach.destroy');
    Route::post('/tap-luyen/buoi-tap', [TapLuyenController::class, 'themBuoiTap'])->name('tap-luyen.buoi-tap.store');
    Route::put('/tap-luyen/buoi-tap/{id}', [TapLuyenController::class, 'suaBuoiTap'])->name('tap-luyen.buoi-tap.update');
    Route::delete('/tap-luyen/buoi-tap/{id}', [TapLuyenController::class, 'xoaBuoiTap'])->name('tap-luyen.buoi-tap.destroy');

    // Module Công việc & Dự án
    Route::get('/cong-viec/dashboard', [CongViecDashboardController::class, 'index'])->name('cong-viec.dashboard');
    Route::resource('du-an', DuAnController::class);
    Route::resource('cong-viec', CongViecController::class);
    Route::patch('/cong-viec/{id}/trang-thai', [CongViecController::class, 'updateStatus'])->name('cong-viec.update-status');
    Route::patch('/cong-viec/{id}/tien-do', [CongViecController::class, 'updateProgress'])->name('cong-viec.update-progress');
    Route::post('/cong-viec/{id}/sync-calendar', [CongViecController::class, 'syncToCalendar'])->name('cong-viec.sync-calendar');

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

    // WEB PUSH NOTIFICATIONS (PWA)
    Route::get('/push/vapid-key', [PushNotificationController::class, 'getVapidPublicKey'])->name('push.vapid-key');
    Route::post('/push/subscribe', [PushNotificationController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [PushNotificationController::class, 'unsubscribe'])->name('push.unsubscribe');
    Route::get('/push/status', [PushNotificationController::class, 'status'])->name('push.status');
    Route::post('/push/test', [PushNotificationController::class, 'testPush'])->name('push.test');
    Route::post('/push/check-reminders', [PushNotificationController::class, 'checkReminders'])->name('push.check-reminders');

    // KÊNH CHAT THẾ GIỚI
    Route::get('/world-chat/messages', [WorldChatController::class, 'index'])->name('world-chat.index');
    Route::post('/world-chat/messages', [WorldChatController::class, 'store'])->name('world-chat.store');
});

// Route trang Offline dự phòng khi mất kết nối mạng
Route::view('/offline', 'offline')->name('offline');

require __DIR__.'/auth.php';
