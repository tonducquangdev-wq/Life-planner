@extends('layouts.app')

@section('title', 'Tổng quan (Dashboard)')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header Card -->
    <div class="p-6 bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-xs">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">
                    Chào mừng trở lại, Nguyễn Văn A! 👋
                </h2>
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">
                    Hôm nay là {{ now()->format('l, \n\à\y d \t\há\n\g m \nă\m Y') }}. Chúc bạn một ngày học tập và rèn luyện hiệu quả!
                </p>
            </div>
            <a href="{{ route('schedule') }}" class="px-4 py-2 bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] hover:bg-[#333330] dark:hover:bg-white text-xs font-semibold rounded-xl transition-all shadow-xs">
                Xem Lịch Hôm Nay
            </a>
        </div>
    </div>

    <!-- Quick Stats Overview Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stat 1: Điểm GPA / Tiến độ -->
        <div class="p-5 bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xl shrink-0">
                📚
            </div>
            <div>
                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A] font-medium">Môn học học kỳ này</span>
                <p class="text-xl font-extrabold text-[#1b1b18] dark:text-[#EDEDEC] mt-0.5">3 Môn học</p>
            </div>
        </div>

        <!-- Stat 2: Bài tập cần hoàn thành -->
        <div class="p-5 bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 flex items-center justify-center font-bold text-xl shrink-0">
                ⏳
            </div>
            <div>
                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A] font-medium">Deadline bài tập</span>
                <p class="text-xl font-extrabold text-[#1b1b18] dark:text-[#EDEDEC] mt-0.5">2 Bài tập</p>
            </div>
        </div>

        <!-- Stat 3: Lịch thể chất -->
        <div class="p-5 bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 dark:bg-green-950/40 text-green-600 dark:text-green-400 flex items-center justify-center font-bold text-xl shrink-0">
                🏋️
            </div>
            <div>
                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A] font-medium">Buổi tập tuần này</span>
                <p class="text-xl font-extrabold text-[#1b1b18] dark:text-[#EDEDEC] mt-0.5">3 Buổi tập</p>
            </div>
        </div>

        <!-- Stat 4: Mục tiêu cá nhân -->
        <div class="p-5 bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold text-xl shrink-0">
                🎯
            </div>
            <div>
                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A] font-medium">Mục tiêu theo đuổi</span>
                <p class="text-xl font-extrabold text-[#1b1b18] dark:text-[#EDEDEC] mt-0.5">2 Mục tiêu</p>
            </div>
        </div>
    </div>

    <!-- Main Content Grid Placeholder -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 p-6 bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-xs flex flex-col justify-center items-center py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-[#222220] flex items-center justify-center text-2xl mb-3">
                📊
            </div>
            <h3 class="text-base font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Vùng nội dung Tổng quan</h3>
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] max-w-sm mt-1">
                Khu vực này sẽ hiển thị biểu đồ tiến độ học tập, danh sách các môn học gần đây và lịch trình trong ngày.
            </p>
        </div>

        <div class="p-6 bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-xs flex flex-col justify-center items-center py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-[#222220] flex items-center justify-center text-2xl mb-3">
                🔔
            </div>
            <h3 class="text-base font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Thông báo gần đây</h3>
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] max-w-xs mt-1">
                Hiển thị nhắc nhở deadline và thông báo lịch tập luyện.
            </p>
        </div>
    </div>
</div>
@endsection
