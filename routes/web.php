<?php

use Illuminate\Support\Facades\Route;

// Redirect trang chủ mặc định sang Dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Các Route Giao diện Nền tảng (Sử dụng Closures render View)
Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');

Route::get('/subjects', function () {
    return view('subjects.index');
})->name('subjects');

Route::get('/schedule', function () {
    return view('schedule.index');
})->name('schedule');

Route::get('/workout', function () {
    return view('workout.index');
})->name('workout');

Route::get('/profile', function () {
    return view('profile.index');
})->name('profile');

Route::get('/settings', function () {
    return view('settings.index');
})->name('settings');
