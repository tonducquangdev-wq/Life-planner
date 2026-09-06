@extends('layouts.app')

@section('title', 'Lịch trình & Thời gian biểu')

@section('content')
<div class="space-y-6">
    <!-- Header Action Bar -->
    <div class="p-6 bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">
                Lịch trình & Thời gian biểu
            </h2>
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">
                Lịch tổng hợp tự động từ Môn học, Deadline bài tập, Buổi tập luyện và Sự kiện cá nhân.
            </p>
        </div>
    </div>

    <!-- Calendar Container Card -->
    <div class="p-8 bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-xs flex flex-col items-center text-center py-16">
        <div class="w-16 h-16 rounded-2xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center text-3xl mb-4">
            📅
        </div>
        <h3 class="text-base font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Vùng giao diện Lịch FullCalendar</h3>
        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] max-w-md mt-2 leading-relaxed">
            Nơi hiển thị lịch tháng tự động với FullCalendar. Tích hợp phân màu Môn học (Xanh dương), Deadline (Cam), Tập luyện (Xanh lá) và Sự kiện cá nhân (Tím).
        </p>
    </div>
</div>
@endsection