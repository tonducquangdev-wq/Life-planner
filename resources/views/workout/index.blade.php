@extends('layouts.app')

@section('title', 'Kế hoạch Thể chất')

@section('content')
<div class="space-y-6">
    <!-- Header Action Bar -->
    <div class="p-6 bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">
                Lịch Tập Luyện & Thể Chất
            </h2>
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">
                Quản lý các kế hoạch tập luyện Fitness, theo dõi buổi tập theo thứ và điểm danh tập hàng ngày.
            </p>
        </div>
        <button class="px-4 py-2 bg-[#22c55e] text-white hover:bg-[#16a34a] text-xs font-semibold rounded-xl transition-all shadow-xs flex items-center gap-2">
            <span>+</span> Tạo Kế Hoạch Tập Mới
        </button>
    </div>

    <!-- Empty State / Placeholder Grid -->
    <div class="p-8 bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-xs flex flex-col items-center text-center py-16">
        <div class="w-16 h-16 rounded-2xl bg-green-50 dark:bg-green-950/40 text-green-600 dark:text-green-400 flex items-center justify-center text-3xl mb-4">
            🏋️
        </div>
        <h3 class="text-base font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Vùng giao diện Thể chất & Fitness</h3>
        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] max-w-md mt-2 leading-relaxed">
            Nơi quản lý thư viện bài tập gốc (Hít đất, Squat, Plank...), kế hoạch tập luyện tuần (Push/Pull/Legs), các buổi tập nhỏ và điểm danh Check-in tập luyện.
        </p>
    </div>
</div>
@endsection
