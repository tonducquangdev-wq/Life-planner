<header class="h-16 bg-white dark:bg-[#161615] border-b border-[#e3e3e0] dark:border-[#3E3E3A] px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30 shadow-xs">
    <!-- Left: Mobile Toggle & Page Title -->
    <div class="flex items-center gap-4">
        <button id="mobileMenuBtn" type="button" class="lg:hidden p-2 rounded-lg text-gray-500 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#222220] transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div>
            <h1 class="text-base sm:text-lg font-bold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">
                {{ $title ?? 'Dashboard' }}
            </h1>
            <p class="text-[11px] text-[#706f6c] dark:text-[#A1A09A] hidden sm:block">
                Hệ thống quản lý học tập & cuộc sống sinh viên
            </p>
        </div>
    </div>

    <!-- Right: Actions, Notifications & Avatar -->
    <div class="flex items-center gap-3">
        <!-- Notification Button -->
        <button type="button" class="relative p-2 rounded-xl text-gray-500 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#222220] transition-all" title="Thông báo">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#f53003] ring-2 ring-white dark:ring-[#161615]"></span>
        </button>

        <!-- Divider -->
        <div class="w-px h-6 bg-[#e3e3e0] dark:bg-[#3E3E3A] hidden sm:block"></div>

        <!-- User Avatar & Quick Info -->
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] flex items-center justify-center font-bold text-xs shadow-xs">
                A
            </div>
            <span class="text-xs font-semibold text-[#1b1b18] dark:text-[#EDEDEC] hidden md:inline">
                Nguyễn Văn A
            </span>
        </div>
    </div>
</header>
