@extends('layouts.app')

@section('title', 'Quản lý Học tập')

@section('content')
<div class="space-y-6">
    <!-- Header Action Bar -->
    <div class="p-6 bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">
                Quản lý Môn học & Bài tập
            </h2>
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">
                Theo dõi tín chỉ, điểm số, tiến độ học tập và danh sách deadline bài tập lớn.
            </p>
        </div>
        <button class="px-4 py-2 bg-[#f53003] dark:bg-[#FF4433] text-white hover:bg-[#d42700] text-xs font-semibold rounded-xl transition-all shadow-xs flex items-center gap-2">
            <span>+</span> Thêm Môn Học Mới
        </button>
    </div>

    <!-- Empty State / Placeholder Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="p-8 bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-xs flex flex-col items-center text-center col-span-full py-16">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-3xl mb-4">
                📖
            </div>
            <h3 class="text-base font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Vùng giao diện Danh sách Môn học</h3>
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] max-w-md mt-2 leading-relaxed">
                Nơi quản lý danh sách các môn học (Lập trình PHP, Cơ sở dữ liệu...), giảng viên phụ trách, số tín chỉ, tiến độ hoàn thành và điểm số thi kỳ.
            </p>
        </div>
    </div>
</div>
@endsection
