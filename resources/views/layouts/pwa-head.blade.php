<!-- PWA Web App Manifest -->
<link rel="manifest" href="{{ asset('manifest.json') }}">

<!-- Theme Color cho thanh trạng thái di động -->
<meta name="theme-color" content="#6366f1">

<!-- Tối ưu cho thiết bị Apple iOS (iPhone/iPad) -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Life Planner">
<link rel="apple-touch-icon" href="{{ asset('images/icons/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icons/icon-192x192.png') }}">

<!-- CSS Tối ưu Safe Area (Tai thỏ & Thanh Home Bar trên iPhone/Android tràn viền & Dynamic Island) -->
<style>
    /* Safe Area insets tối ưu cho cả trình duyệt di động và Standalone PWA */
    @supports (padding-top: env(safe-area-inset-top)) {
        .top-header {
            padding-top: env(safe-area-inset-top, 0px) !important;
            padding-left: calc(14px + env(safe-area-inset-left, 0px)) !important;
            padding-right: calc(14px + env(safe-area-inset-right, 0px)) !important;
            height: calc(58px + env(safe-area-inset-top, 0px)) !important;
            min-height: calc(58px + env(safe-area-inset-top, 0px)) !important;
        }

        .mobile-bottom-nav,
        .bottom-nav-container {
            padding-bottom: env(safe-area-inset-bottom, 0px) !important;
            height: calc(58px + env(safe-area-inset-bottom, 0px)) !important;
            min-height: calc(58px + env(safe-area-inset-bottom, 0px)) !important;
        }

        .content-body {
            padding-bottom: calc(75px + env(safe-area-inset-bottom, 0px)) !important;
        }

        .modal-dialog {
            padding-top: calc(16px + env(safe-area-inset-top, 0px)) !important;
            padding-bottom: calc(20px + env(safe-area-inset-bottom, 0px)) !important;
        }
    }
</style>

<!-- Kịch bản quản lý PWA và Web Push -->
<script src="{{ asset('js/pwa-manager.js') }}?v={{ file_exists(public_path('js/pwa-manager.js')) ? filemtime(public_path('js/pwa-manager.js')) : time() }}" defer></script>

<!-- Kịch bản quản lý Giao diện Sáng/Tối (Theme Manager) -->
@include('layouts.theme-script')
