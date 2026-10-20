<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Lên lịch quét và gửi thông báo đẩy (Web Push) định kỳ mỗi phút cho lịch học, deadline, sự kiện
Schedule::command('push:send-reminders')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
