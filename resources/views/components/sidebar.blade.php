<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-white dark:bg-[#161615] border-r border-[#e3e3e0] dark:border-[#3E3E3A] flex flex-col transition-transform duration-300 transform -translate-x-full lg:translate-x-0">
    <!-- Brand Logo Area -->
    <div class="h-16 flex items-center px-6 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-[#f53003] dark:bg-[#FF4433] text-white flex items-center justify-center font-bold text-lg shadow-sm tracking-tighter">
                SL
            </div>
            <div>
                <span class="font-bold text-base tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">Student Life</span>
                <span class="block text-[10px] text-[#706f6c] dark:text-[#A1A09A] font-medium tracking-wider uppercase">Portal Sinh Viên</span>
            </div>
        </a>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
        <div class="px-3 pb-2 text-[10px] font-bold text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">
            Menu Chính
        </div>

        <!-- 1. Tổng quan (Dashboard) -->
        <a href="{{ route('dashboard') }}" 
           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all group {{ request()->routeIs('dashboard') ? 'bg-[#1b1b18] text-white dark:bg-[#EDEDEC] dark:text-[#1b1b18] shadow-sm' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-100 dark:hover:bg-[#222220] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
            <svg class="w-4 h-4 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Tổng quan</span>
        </a>

        <!-- 2. Học tập (Subjects & Assignments) -->
        <a href="{{ route('subjects') }}" 
           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all group {{ request()->routeIs('subjects') ? 'bg-[#1b1b18] text-white dark:bg-[#EDEDEC] dark:text-[#1b1b18] shadow-sm' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-100 dark:hover:bg-[#222220] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
            <svg class="w-4 h-4 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span>Học tập</span>
        </a>

        <!-- 3. Lịch (Schedule) -->
        <a href="{{ route('schedule') }}" 
           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all group {{ request()->routeIs('schedule') ? 'bg-[#1b1b18] text-white dark:bg-[#EDEDEC] dark:text-[#1b1b18] shadow-sm' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-100 dark:hover:bg-[#222220] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
            <svg class="w-4 h-4 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>Lịch trình</span>
        </a>

        <!-- 4. Thể chất (Workout) -->
        <a href="{{ route('workout') }}" 
           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all group {{ request()->routeIs('workout') ? 'bg-[#1b1b18] text-white dark:bg-[#EDEDEC] dark:text-[#1b1b18] shadow-sm' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-100 dark:hover:bg-[#222220] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
            <svg class="w-4 h-4 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <span>Thể chất</span>
        </a>

        <div class="pt-4 px-3 pb-2 text-[10px] font-bold text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">
            Cá Nhân
        </div>

        <!-- 5. Hồ sơ (Profile) -->
        <a href="{{ route('profile') }}" 
           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all group {{ request()->routeIs('profile') ? 'bg-[#1b1b18] text-white dark:bg-[#EDEDEC] dark:text-[#1b1b18] shadow-sm' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-100 dark:hover:bg-[#222220] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
            <svg class="w-4 h-4 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span>Hồ sơ sinh viên</span>
        </a>

        <!-- 6. Cài đặt (Settings) -->
        <a href="{{ route('settings') }}" 
           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all group {{ request()->routeIs('settings') ? 'bg-[#1b1b18] text-white dark:bg-[#EDEDEC] dark:text-[#1b1b18] shadow-sm' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-gray-100 dark:hover:bg-[#222220] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}">
            <svg class="w-4 h-4 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>Cài đặt</span>
        </a>
    </nav>

    <!-- User Profile Footer Area -->
    <div class="p-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
        <div class="flex items-center gap-3 p-2 rounded-xl bg-gray-50 dark:bg-[#0a0a0a] border border-[#e3e3e0] dark:border-[#3E3E3A]">
            <div class="w-8 h-8 rounded-lg bg-[#3b82f6] text-white flex items-center justify-center font-bold text-xs shrink-0">
                NV
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-[#1b1b18] dark:text-[#EDEDEC] truncate">Nguyễn Văn A</p>
                <p class="text-[10px] text-[#706f6c] dark:text-[#A1A09A] truncate">sv2026@student.edu.vn</p>
            </div>
        </div>
    </div>
</aside>
