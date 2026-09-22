<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BaiTapApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\LichHocApiController;
use App\Http\Controllers\Api\MonHocApiController;
use App\Http\Controllers\Api\SuKienApiController;
use App\Http\Controllers\Api\TapLuyenApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Life Planner System
|--------------------------------------------------------------------------
*/

Route::as('api.')->group(function () {

    // MODULE 1: AUTHENTICATION (Public Login)
    Route::post('/login', [AuthApiController::class, 'login'])->middleware('throttle:login')->name('login');

    // PROTECTED ROUTES (Sanctum Middleware)
    Route::middleware('auth:sanctum')->group(function () {

        // MODULE 1: AUTHENTICATION (Protected)
        Route::post('/logout', [AuthApiController::class, 'logout'])->name('logout');
        Route::get('/me', [AuthApiController::class, 'me'])->name('me');
        Route::get('/user', [AuthApiController::class, 'user'])->name('user');

        // MODULE 6: DASHBOARD / BÁO CÁO API
        Route::get('/dashboard', [DashboardApiController::class, 'index'])->name('dashboard');

        // MODULE 2: MÔN HỌC (RESTful API)
        Route::apiResource('mon-hoc', MonHocApiController::class);

        // MODULE 3: BÀI TẬP / DEADLINE (RESTful API)
        Route::apiResource('bai-tap', BaiTapApiController::class);

        // MODULE 4: SỰ KIỆN LỊCH (RESTful API)
        Route::apiResource('su-kien', SuKienApiController::class);

        // MODULE 5: TẬP LUYỆN (RESTful & Specialized Workouts API)
        // 5.1 Workout Schedule & History helpers (Đặt trước apiResource để tránh route shadowing)
        Route::get('/tap-luyen/schedule', [TapLuyenApiController::class, 'schedule']);
        Route::post('/tap-luyen/cap-nhat-buoi-tap', [TapLuyenApiController::class, 'capNhatBuoiTap']);
        Route::post('/tap-luyen/hoan-thanh', [TapLuyenApiController::class, 'hoanThanh']);

        // 5.2 Kế hoạch & Buổi tập theo tiền tố /tap-luyen/
        Route::get('/tap-luyen/ke-hoach', [TapLuyenApiController::class, 'indexPlans']);
        Route::post('/tap-luyen/ke-hoach', [TapLuyenApiController::class, 'storePlan']);

        Route::get('/tap-luyen/buoi-tap', [TapLuyenApiController::class, 'indexSessions']);
        Route::post('/tap-luyen/buoi-tap', [TapLuyenApiController::class, 'storeSession']);

        Route::get('/tap-luyen/lich-su', [TapLuyenApiController::class, 'indexHistory']);

        // 5.3 RESTful Resource Kế hoạch tập luyện
        Route::apiResource('tap-luyen', TapLuyenApiController::class);

        // 5.4 RESTful Aliases & Extended Endpoints (Tương thích Feature Tests & REST client)
        Route::get('/ke-hoach-tap-luyen', [TapLuyenApiController::class, 'indexPlans']);
        Route::post('/ke-hoach-tap-luyen', [TapLuyenApiController::class, 'storePlan']);
        Route::post('/ke-hoach-tap-luyen/{id}/kich-hoat', [TapLuyenApiController::class, 'activatePlan']);
        Route::delete('/ke-hoach-tap-luyen/{id}', [TapLuyenApiController::class, 'destroyPlan']);

        Route::get('/buoi-tap', [TapLuyenApiController::class, 'indexSessions']);
        Route::post('/buoi-tap', [TapLuyenApiController::class, 'storeSession']);
        Route::put('/buoi-tap/{id}', [TapLuyenApiController::class, 'updateSession']);
        Route::delete('/buoi-tap/{id}', [TapLuyenApiController::class, 'destroySession']);

        // Extra Module: LỊCH HỌC
        Route::apiResource('lich-hoc', LichHocApiController::class);
    });
});
