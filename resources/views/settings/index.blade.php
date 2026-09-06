@extends('layouts.app')

@section('title', 'Cài đặt Hệ thống')

@section('content')
<div class="space-y-6">
    <!-- Header Action Bar -->
    <div class="p-6 bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-xs">
        <h2 class="text-xl font-bold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">
            Cài đặt Hệ thống & Tùy chọn
        </h2>
        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">
            Cấu hình giao diện, bật/tắt nhận thông báo bài tập và cài đặt quyền riêng tư.
        </p>
    </div>

    <!-- Empty State / Placeholder Container -->
    <div class="p-8 bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-xs flex flex-col items-center text-center py-16">
        <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-[#222220] text-gray-700 dark:text-gray-300 flex items-center justify-center text-3xl mb-4">
            ⚙️
        </div>
        <h3 class="text-base font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Vùng giao diện Cài đặt</h3>
        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] max-w-md mt-2 leading-relaxed">
            Nơi tùy chỉnh cài đặt giao diện (Sáng/Tối), thời gian gửi thông báo nhắc nhở deadline và các tùy chọn hệ thống khác.
        </p>
    </div>
</div>
@endsection
