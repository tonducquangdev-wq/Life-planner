@extends('layouts.app')

@section('title', 'Hồ sơ Sinh viên')

@section('content')
<div class="space-y-6">
    <!-- Header Action Bar -->
    <div class="p-6 bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-xs">
        <h2 class="text-xl font-bold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">
            Hồ sơ Cá nhân & Sinh viên
        </h2>
        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">
            Quản lý thông tin tài khoản, mã số sinh viên, lớp học và thông tin liên hệ.
        </p>
    </div>

    <!-- Empty State / Placeholder Container -->
    <div class="p-8 bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-xs flex flex-col items-center text-center py-16">
        <div class="w-16 h-16 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-3xl mb-4">
            👤
        </div>
        <h3 class="text-base font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Vùng giao diện Hồ sơ Sinh viên</h3>
        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] max-w-md mt-2 leading-relaxed">
            Nơi cập nhật ảnh đại diện avatar, họ tên, email sinh viên, số điện thoại, mật khẩu tài khoản và thông tin chuyên ngành.
        </p>
    </div>
</div>
@endsection
