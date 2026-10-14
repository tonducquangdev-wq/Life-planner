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

<!-- CSS Tối ưu Safe Area (Tai thỏ & Thanh Home Bar trên iPhone/Android tràn viền) -->
<style>
    /* Safe Area insets khi chạy Standalone PWA */
    @supports (padding-top: env(safe-area-inset-top)) {
        .pwa-standalone .top-header {
            padding-top: calc(0.65rem + env(safe-area-inset-top, 0px)) !important;
            height: auto !important;
            min-height: calc(58px + env(safe-area-inset-top, 0px)) !important;
        }

        .pwa-standalone .mobile-bottom-nav,
        .pwa-standalone .bottom-nav-container {
            padding-bottom: calc(0.5rem + env(safe-area-inset-bottom, 0px)) !important;
            height: auto !important;
            min-height: calc(58px + env(safe-area-inset-bottom, 0px)) !important;
        }

        .pwa-standalone .content-body {
            padding-bottom: calc(75px + env(safe-area-inset-bottom, 0px)) !important;
        }
    }
</style>

<!-- Kịch bản quản lý PWA và Web Push -->
<script src="{{ asset('js/pwa-manager.js') }}?v={{ file_exists(public_path('js/pwa-manager.js')) ? filemtime(public_path('js/pwa-manager.js')) : time() }}" defer></script>
