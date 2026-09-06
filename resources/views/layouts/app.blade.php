<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Student Life Portal')</title>

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    @stack('styles')
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen font-sans antialiased flex">

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-30 hidden lg:hidden"></div>

    <!-- Sidebar Component -->
    @include('components.sidebar')

    <!-- Main Wrapper Area -->
    <div class="flex-1 flex flex-col min-w-0 lg:pl-64">
        
        <!-- Header Component -->
        @include('components.header', ['title' => View::getSection('title', 'Dashboard')])

        <!-- Main Content Area -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-4 px-6 border-t border-[#e3e3e0] dark:border-[#3E3E3A] text-center text-xs text-[#706f6c] dark:text-[#A1A09A]">
            Student Life &copy; 2026 - Đồ án tốt nghiệp Sinh viên
        </footer>
    </div>

    <!-- Global Mobile Menu Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('mobileMenuBtn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (btn && sidebar && overlay) {
                btn.addEventListener('click', function () {
                    sidebar.classList.toggle('-translate-x-full');
                    overlay.classList.toggle('hidden');
                });

                overlay.addEventListener('click', function () {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
