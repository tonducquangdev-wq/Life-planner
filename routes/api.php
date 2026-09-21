<?php

use App\Http\Controllers\Api\BaiTapApiController;
use App\Http\Controllers\Api\MonHocApiController;
use App\Http\Controllers\Api\TapLuyenApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::as('api.')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::apiResource('bai-tap', BaiTapApiController::class);
    Route::apiResource('mon-hoc', MonHocApiController::class);

    // tập luyện
    Route::middleware('auth:sanctum')->group(function () {
        // Lịch tập và trạng thái tập luyện hôm nay
        Route::get('/tap-luyen/schedule', [TapLuyenApiController::class, 'schedule']);
        Route::post('/tap-luyen/cap-nhat-buoi-tap', [TapLuyenApiController::class, 'capNhatBuoiTap']);
        Route::post('/tap-luyen/hoan-thanh', [TapLuyenApiController::class, 'hoanThanh']);

        // Quản lý Kế hoạch tập luyện (Plans)
        Route::get('/ke-hoach-tap-luyen', [TapLuyenApiController::class, 'indexPlans']);
        Route::post('/ke-hoach-tap-luyen', [TapLuyenApiController::class, 'storePlan']);
        Route::post('/ke-hoach-tap-luyen/{id}/kich-hoat', [TapLuyenApiController::class, 'activatePlan']);
        Route::delete('/ke-hoach-tap-luyen/{id}', [TapLuyenApiController::class, 'destroyPlan']);

        // Quản lý Buổi tập (Sessions)
        Route::get('/buoi-tap', [TapLuyenApiController::class, 'indexSessions']);
        Route::post('/buoi-tap', [TapLuyenApiController::class, 'storeSession']);
        Route::put('/buoi-tap/{id}', [TapLuyenApiController::class, 'updateSession']);
        Route::delete('/buoi-tap/{id}', [TapLuyenApiController::class, 'destroySession']);
    });
});
