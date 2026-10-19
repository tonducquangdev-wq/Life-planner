<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chi Tiết Môn Học - {{ $monHoc->ten_mon }} | Life Planner</title>
    
    <!-- Kịch bản Khởi tạo Giao diện Sáng/Tối chống giật trang (Anti-Flicker) -->
    <script>
        (function() {
            var userTheme = "{{ Auth::check() ? (Auth::user()->giao_dien ?? 'system') : 'system' }}";
            var savedTheme = localStorage.getItem('theme');
            var themeToApply = 'light';
            if (savedTheme && (savedTheme === 'dark' || savedTheme === 'light')) {
                themeToApply = savedTheme;
            } else if (userTheme && (userTheme === 'dark' || userTheme === 'light')) {
                themeToApply = userTheme;
            } else {
                themeToApply = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-bs-theme', themeToApply);
            if (themeToApply === 'dark') {
                document.documentElement.classList.add('dark-theme');
            } else {
                document.documentElement.classList.remove('dark-theme');
            }
        })();
    </script>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Font: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom Dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @include('layouts.pwa-head')

    <style>
        /* ==========================================================================
           HỆ THỐNG BIẾN NEUMORPHISM CHUẨN LIFE PLANNER (SOFT UI DESIGN SYSTEM)
           ========================================================================== */
        :root {
            /* Tone màu nền chủ đạo xám xanh nhạt dịu mắt chuẩn Neumorphic */
            --nm-bg: #e7ebf0;
            --nm-surface: #e7ebf0;
            --nm-surface-hover: #edf1f6;
            --nm-surface-subtle: #edf2f7;

            /* Đổ bóng kép mềm mại (Soft Dual Shadows) */
            --nm-shadow-flat: 6px 6px 14px rgba(163, 177, 198, 0.45), -6px -6px 14px rgba(255, 255, 255, 0.95);
            --nm-shadow-card: 14px 14px 28px rgba(163, 177, 198, 0.48), -14px -14px 28px rgba(255, 255, 255, 0.98);
            --nm-shadow-card-hover: 18px 18px 36px rgba(163, 177, 198, 0.58), -18px -18px 36px rgba(255, 255, 255, 1);
            --nm-shadow-btn: 5px 5px 12px rgba(163, 177, 198, 0.45), -5px -5px 12px rgba(255, 255, 255, 0.95);
            --nm-shadow-btn-hover: 7px 7px 16px rgba(163, 177, 198, 0.52), -7px -7px 16px rgba(255, 255, 255, 1);
            --nm-shadow-primary: 6px 6px 16px rgba(79, 70, 229, 0.35), -4px -4px 12px rgba(255, 255, 255, 0.85);
            --nm-shadow-danger: 6px 6px 16px rgba(239, 68, 68, 0.32), -4px -4px 12px rgba(255, 255, 255, 0.85);

            /* Đổ bóng dập lõm (Concave / Inset Shadows) cho inputs và thẻ sunken */
            --nm-shadow-inset: inset 3px 3px 6px rgba(163, 177, 198, 0.42), inset -3px -3px 6px rgba(255, 255, 255, 0.9);
            --nm-shadow-inset-deep: inset 4px 4px 8px rgba(163, 177, 198, 0.5), inset -4px -4px 8px rgba(255, 255, 255, 0.95);

            /* Viền tinh tế tạo gờ ánh sáng vi tế */
            --nm-border: 1.5px solid rgba(255, 255, 255, 0.8);
            --nm-border-subtle: 1px solid rgba(163, 177, 198, 0.28);
            --nm-divider: rgba(163, 177, 198, 0.35);

            /* Màu văn bản phân cấp rõ ràng */
            --nm-text-main: #1e293b;
            --nm-text-muted: #64748b;
            --nm-text-light: #94a3b8;

            /* Màu nhấn (Primary Indigo & Accent Gradients) */
            --nm-primary: #4f46e5;
            --nm-primary-gradient: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            --nm-primary-hover-gradient: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
            --nm-danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            --nm-danger-hover-gradient: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);

            /* Ánh xạ biến tương thích */
            --sd-bg: var(--nm-bg);
            --sd-card-bg: var(--nm-surface);
            --sd-card-border: rgba(255, 255, 255, 0.8);
            --sd-text-main: var(--nm-text-main);
            --sd-text-muted: var(--nm-text-muted);
            --sd-surface: var(--nm-surface);
            --sd-surface-border: var(--nm-border-subtle);
            --sd-surface-hover: var(--nm-surface-hover);
            --sd-primary: var(--nm-primary);
            --sd-primary-hover: #4338ca;
            --sd-primary-subtle: rgba(79, 70, 229, 0.12);
            --sd-danger: #ef4444;
            --sd-danger-hover: #dc2626;
            --sd-danger-subtle: rgba(239, 68, 68, 0.12);
            --sd-radius-card: 26px;
            --sd-radius-btn: 9999px;
            --sd-radius-tile: 20px;
            --sd-shadow-sm: var(--nm-shadow-btn);
            --sd-shadow-card: var(--nm-shadow-card);
        }

        /* GIAO DIỆN TỐI (DARK MODE NEUMORPHISM / SOFT UI) */
        [data-bs-theme="dark"] {
            --nm-bg: #0f172a;
            --nm-surface: #1e293b;
            --nm-surface-hover: #334155;
            --nm-surface-subtle: #1e293b;

            --nm-shadow-flat: 6px 6px 14px rgba(0, 0, 0, 0.5), -4px -4px 12px rgba(255, 255, 255, 0.04);
            --nm-shadow-card: 14px 14px 28px rgba(0, 0, 0, 0.55), -10px -10px 24px rgba(255, 255, 255, 0.04);
            --nm-shadow-card-hover: 18px 18px 36px rgba(0, 0, 0, 0.65), -12px -12px 28px rgba(255, 255, 255, 0.06);
            --nm-shadow-btn: 5px 5px 12px rgba(0, 0, 0, 0.45), -4px -4px 10px rgba(255, 255, 255, 0.04);
            --nm-shadow-btn-hover: 7px 7px 16px rgba(0, 0, 0, 0.55), -5px -5px 14px rgba(255, 255, 255, 0.06);
            --nm-shadow-primary: 6px 6px 18px rgba(99, 102, 241, 0.35), -3px -3px 10px rgba(255, 255, 255, 0.05);
            --nm-shadow-danger: 6px 6px 18px rgba(239, 68, 68, 0.35), -3px -3px 10px rgba(255, 255, 255, 0.05);

            --nm-shadow-inset: inset 3px 3px 6px rgba(0, 0, 0, 0.6), inset -3px -3px 6px rgba(255, 255, 255, 0.03);
            --nm-shadow-inset-deep: inset 4px 4px 8px rgba(0, 0, 0, 0.7), inset -4px -4px 8px rgba(255, 255, 255, 0.04);

            --nm-border: 1.5px solid rgba(255, 255, 255, 0.07);
            --nm-border-subtle: 1px solid rgba(255, 255, 255, 0.05);
            --nm-divider: rgba(255, 255, 255, 0.08);

            --nm-text-main: #f1f5f9;
            --nm-text-muted: #94a3b8;
            --nm-text-light: #64748b;

            --nm-primary: #6366f1;
            --nm-primary-gradient: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
            --nm-primary-hover-gradient: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);

            --sd-card-border: rgba(255, 255, 255, 0.07);
        }

        body {
            background-color: var(--nm-bg) !important;
            color: var(--nm-text-main) !important;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            overflow-x: hidden;
        }

        .subject-detail-wrapper {
            background-color: var(--nm-bg);
            min-height: calc(100vh - 58px);
        }

        /* Đồng bộ Topbar chuẩn phong cách Neumorphism của toàn web */
        .top-header {
            background-color: var(--nm-surface) !important;
            border-bottom: 1px solid rgba(163, 177, 198, 0.25) !important;
            box-shadow: 0 4px 20px rgba(163, 177, 198, 0.2) !important;
            transition: all 0.25s ease;
        }

        [data-bs-theme="dark"] .top-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.45) !important;
        }

        .top-header .btn-back-to-calendar {
            background: var(--nm-surface) !important;
            border: var(--nm-border) !important;
            box-shadow: var(--nm-shadow-btn) !important;
            color: var(--nm-text-main) !important;
            font-weight: 600;
            border-radius: 9999px !important;
            padding: 0.45rem 1.1rem !important;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.22s cubic-bezier(0.34, 1.3, 0.64, 1);
        }

        .top-header .btn-back-to-calendar:hover {
            box-shadow: var(--nm-shadow-btn-hover) !important;
            color: var(--nm-primary) !important;
            transform: translateX(-3px) translateY(-1px);
        }

        .top-header .btn-back-to-calendar:active {
            box-shadow: var(--nm-shadow-inset) !important;
            transform: translateX(-1px) scale(0.98);
        }

        .top-header .search-box input {
            background: var(--nm-surface) !important;
            border: var(--nm-border-subtle) !important;
            box-shadow: var(--nm-shadow-inset) !important;
            color: var(--nm-text-main) !important;
            border-radius: 9999px !important;
            height: 38px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .top-header .search-box input:focus {
            border-color: rgba(99, 102, 241, 0.5) !important;
            box-shadow: var(--nm-shadow-inset), 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
        }

        .top-header .user-avatar {
            box-shadow: var(--nm-shadow-btn) !important;
            border: 2px solid rgba(255, 255, 255, 0.8) !important;
        }

        [data-bs-theme="dark"] .top-header .user-avatar {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        .topbar-theme-toggle {
            background: var(--nm-surface) !important;
            border: var(--nm-border) !important;
            box-shadow: var(--nm-shadow-btn) !important;
            color: var(--nm-text-main) !important;
            transition: all 0.22s cubic-bezier(0.34, 1.3, 0.64, 1);
        }

        .topbar-theme-toggle:hover {
            transform: scale(1.08);
            box-shadow: var(--nm-shadow-btn-hover) !important;
        }

        .topbar-theme-toggle:active {
            box-shadow: var(--nm-shadow-inset) !important;
            transform: scale(0.96);
        }

        .dropdown-menu {
            background: var(--nm-surface) !important;
            border: var(--nm-border) !important;
            border-radius: 20px !important;
            box-shadow: var(--nm-shadow-card) !important;
            color: var(--nm-text-main) !important;
            overflow: hidden;
        }

        .dropdown-item {
            color: var(--nm-text-main) !important;
            transition: all 0.18s ease;
        }

        .dropdown-item:hover {
            background: var(--nm-surface-hover) !important;
            color: var(--nm-primary) !important;
        }

        .dropdown-divider {
            border-color: var(--nm-divider) !important;
        }

        /* 1. Thanh điều hướng & Thao tác Header */
        .subject-top-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .subject-btn-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 42px;
            padding: 0 20px;
            border-radius: 9999px;
            background: var(--nm-surface);
            border: var(--nm-border);
            color: var(--nm-text-main);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.22s cubic-bezier(0.34, 1.3, 0.64, 1);
            box-shadow: var(--nm-shadow-btn);
        }

        .subject-btn-back:hover {
            background: var(--nm-surface-hover);
            color: var(--nm-primary);
            box-shadow: var(--nm-shadow-btn-hover);
            transform: translateX(-3px) translateY(-1px);
        }

        .subject-btn-back:active {
            box-shadow: var(--nm-shadow-inset);
            transform: translateX(-1px) scale(0.98);
        }

        .subject-action-group {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .subject-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 42px;
            padding: 0 20px;
            border-radius: 9999px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: var(--nm-border);
            transition: all 0.22s cubic-bezier(0.34, 1.3, 0.64, 1);
            white-space: nowrap;
        }

        .subject-action-btn.btn-calendar {
            background: var(--nm-surface);
            color: var(--nm-text-main);
            box-shadow: var(--nm-shadow-btn);
        }

        .subject-action-btn.btn-calendar:hover {
            color: var(--nm-primary);
            box-shadow: var(--nm-shadow-btn-hover);
            transform: translateY(-2px);
        }

        .subject-action-btn.btn-calendar:active {
            box-shadow: var(--nm-shadow-inset);
            transform: scale(0.98);
        }

        .subject-action-btn.btn-edit {
            background: var(--nm-primary-gradient) !important;
            color: #ffffff !important;
            border: 1.5px solid rgba(255, 255, 255, 0.45) !important;
            box-shadow: var(--nm-shadow-primary) !important;
        }

        .subject-action-btn.btn-edit:hover {
            background: var(--nm-primary-hover-gradient) !important;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 8px 8px 22px rgba(79, 70, 229, 0.45), -4px -4px 14px rgba(255, 255, 255, 0.9) !important;
        }

        .subject-action-btn.btn-edit:active {
            box-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.25) !important;
            transform: scale(0.98);
        }

        .subject-action-btn.btn-delete {
            background: var(--nm-surface) !important;
            color: #ef4444 !important;
            border: 1.5px solid rgba(239, 68, 68, 0.3) !important;
            box-shadow: var(--nm-shadow-btn) !important;
        }

        .subject-action-btn.btn-delete:hover {
            background: var(--nm-danger-gradient) !important;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.4) !important;
            transform: translateY(-2px);
            box-shadow: var(--nm-shadow-danger) !important;
        }

        .subject-action-btn.btn-delete:active {
            box-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.25) !important;
            transform: scale(0.98);
        }

        /* 2. Thẻ Hero Môn Học (Neumorphic Course Hero) */
        .subject-hero-card {
            background: var(--nm-surface);
            border: var(--nm-border);
            border-radius: 28px;
            padding: 26px 28px;
            margin-bottom: 24px;
            box-shadow: var(--nm-shadow-card);
            position: relative;
            overflow: hidden;
            transition: transform 0.28s ease, box-shadow 0.28s ease;
        }

        .subject-hero-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 6px;
            border-top-left-radius: 28px;
            border-bottom-left-radius: 28px;
            background-color: {{ $monHoc->mau_sac ?? '#6366f1' }};
        }

        .subject-hero-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .subject-hero-main {
            display: flex;
            align-items: center;
            gap: 20px;
            min-width: 0;
        }

        .subject-hero-icon {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background-color: {{ $monHoc->mau_sac ?? '#6366f1' }};
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 28px;
            flex-shrink: 0;
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.2), inset 0 2px 4px rgba(255, 255, 255, 0.35);
            border: 2px solid rgba(255, 255, 255, 0.25);
        }

        .subject-hero-meta {
            min-width: 0;
        }

        .subject-hero-tags {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .subject-code-tag {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 8px;
            background: var(--nm-surface);
            border: var(--nm-border-subtle);
            box-shadow: var(--nm-shadow-inset);
            color: var(--nm-text-muted);
            letter-spacing: 0.04em;
        }

        .subject-status-pill {
            font-size: 12px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: var(--nm-shadow-flat);
        }

        .status-dang_hoc {
            background-color: rgba(34, 197, 94, 0.12);
            color: #16a34a;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        [data-bs-theme="dark"] .status-dang_hoc {
            color: #4ade80;
        }

        .status-da_hoan_thanh {
            background-color: rgba(99, 102, 241, 0.12);
            color: #4f46e5;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }
        [data-bs-theme="dark"] .status-da_hoan_thanh {
            color: #818cf8;
        }

        .status-tam_dung {
            background-color: rgba(245, 158, 11, 0.12);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        [data-bs-theme="dark"] .status-tam_dung {
            color: #fbbf24;
        }

        .subject-hero-title {
            font-size: 26px;
            font-weight: 800;
            color: var(--nm-text-main);
            margin: 0;
            line-height: 1.3;
            letter-spacing: -0.02em;
            word-break: break-word;
        }

        .subject-hero-aside {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .subject-color-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: var(--nm-surface);
            border: var(--nm-border);
            border-radius: 9999px;
            box-shadow: var(--nm-shadow-flat);
            font-size: 13px;
            font-weight: 700;
            color: var(--nm-text-main);
        }

        .subject-color-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background-color: {{ $monHoc->mau_sac ?? '#6366f1' }};
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.25);
        }

        /* 3. Khung Nội Dung Card Neumorphic */
        .subject-card {
            background: var(--nm-surface);
            border: var(--nm-border);
            border-radius: 28px;
            padding: 26px 28px;
            box-shadow: var(--nm-shadow-card);
            margin-bottom: 24px;
            transition: transform 0.28s ease, box-shadow 0.28s ease;
        }

        .subject-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 16px;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--nm-divider);
        }

        .subject-card-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--nm-text-main);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 9px;
            letter-spacing: -0.01em;
        }

        .subject-card-title i {
            color: var(--nm-primary);
            font-size: 20px;
        }

        .subject-card-subtitle {
            font-size: 13px;
            color: var(--nm-text-muted);
            margin: 4px 0 0 0;
        }

        /* 4. Thẻ Chi Tiết Thông Tin (2x2 Grid) */
        .subject-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .subject-info-tile {
            background: var(--nm-surface);
            border: var(--nm-border);
            border-radius: 20px;
            box-shadow: var(--nm-shadow-flat);
            padding: 18px 20px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            transition: all 0.22s cubic-bezier(0.34, 1.3, 0.64, 1);
        }

        .subject-info-tile:hover {
            transform: translateY(-3px);
            box-shadow: var(--nm-shadow-btn-hover);
            border-color: rgba(99, 102, 241, 0.4);
        }

        .subject-tile-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: var(--nm-surface);
            box-shadow: var(--nm-shadow-btn);
            color: var(--nm-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .subject-tile-icon.icon-geo {
            color: #ef4444;
        }

        .subject-tile-icon.icon-status {
            color: #22c55e;
        }

        .subject-tile-icon.icon-term {
            color: #f59e0b;
        }

        .subject-tile-content {
            min-width: 0;
            flex-grow: 1;
        }

        .subject-tile-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--nm-text-muted);
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .subject-tile-value {
            font-size: 15px;
            font-weight: 700;
            color: var(--nm-text-main);
            margin: 0;
            line-height: 1.35;
            word-break: break-word;
        }

        .subject-tile-sub {
            font-size: 12px;
            color: var(--nm-text-muted);
            margin-top: 3px;
        }

        /* 5. Khung Ngày Tháng Đào Tạo (Timeline) */
        .subject-date-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .subject-date-card {
            background: var(--nm-surface);
            border: var(--nm-border);
            border-radius: 20px;
            box-shadow: var(--nm-shadow-flat);
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.22s cubic-bezier(0.34, 1.3, 0.64, 1);
        }

        .subject-date-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--nm-shadow-btn-hover);
        }

        .subject-date-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: var(--nm-surface);
            box-shadow: var(--nm-shadow-btn);
            color: var(--nm-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .subject-date-icon.icon-end {
            color: #22c55e;
        }

        .subject-date-meta {
            min-width: 0;
        }

        .subject-date-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--nm-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 2px;
        }

        .subject-date-val {
            font-size: 16px;
            font-weight: 700;
            color: var(--nm-text-main);
            margin: 0;
        }

        .subject-date-desc {
            font-size: 12px;
            color: var(--nm-text-muted);
            margin-top: 2px;
        }

        /* 6. Widget Cột Phải (Color & Calendar Navigation) */
        .subject-color-preview-box {
            background: var(--nm-surface);
            box-shadow: var(--nm-shadow-inset);
            border: var(--nm-border-subtle);
            border-radius: 20px;
            padding: 22px 18px;
            text-align: center;
            margin-bottom: 18px;
        }

        .subject-color-large-bubble {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            margin: 0 auto 12px;
            background-color: {{ $monHoc->mau_sac ?? '#6366f1' }};
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.22), inset 0 2px 5px rgba(255, 255, 255, 0.4);
            border: 3px solid var(--nm-surface);
        }

        .subject-color-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 15px;
            font-weight: 700;
            color: var(--nm-text-main);
            letter-spacing: 0.05em;
        }

        .subject-calendar-nav-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            height: 44px;
            border-radius: 9999px;
            background: var(--nm-primary-gradient) !important;
            color: #ffffff !important;
            border: 1.5px solid rgba(255, 255, 255, 0.45);
            box-shadow: var(--nm-shadow-primary);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.22s cubic-bezier(0.34, 1.3, 0.64, 1);
        }

        .subject-calendar-nav-btn:hover {
            background: var(--nm-primary-hover-gradient) !important;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 8px 8px 22px rgba(79, 70, 229, 0.45), -4px -4px 14px rgba(255, 255, 255, 0.9) !important;
        }

        .subject-calendar-nav-btn:active {
            box-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.25) !important;
            transform: scale(0.98);
        }

        /* Lịch học tuần */
        .subject-schedule-item {
            background: var(--nm-surface);
            border: var(--nm-border);
            border-radius: 18px;
            box-shadow: var(--nm-shadow-flat);
            padding: 14px 18px;
            transition: all 0.22s cubic-bezier(0.34, 1.3, 0.64, 1);
        }

        .subject-schedule-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--nm-shadow-btn-hover);
            border-color: rgba(99, 102, 241, 0.35);
        }

        .subject-schedule-dow {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--nm-primary);
            display: inline-flex;
            align-items: center;
        }

        .subject-schedule-time {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 8px;
            background: var(--nm-surface);
            border: var(--nm-border-subtle);
            box-shadow: var(--nm-shadow-inset);
            color: var(--nm-text-main);
            letter-spacing: 0.03em;
        }

        .subject-schedule-note {
            font-size: 12px;
            color: var(--nm-text-muted);
            display: flex;
            align-items: center;
            line-height: 1.4;
        }

        /* Empty state for weekly schedule */
        .subject-schedule-empty {
            text-align: center;
            padding: 26px 18px;
            background: var(--nm-surface);
            border: var(--nm-border-subtle);
            box-shadow: var(--nm-shadow-inset);
            border-radius: 20px;
        }

        .subject-schedule-empty i {
            font-size: 36px;
            color: var(--nm-text-muted);
            margin-bottom: 10px;
            display: inline-block;
        }

        .subject-schedule-empty-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--nm-text-main);
            margin-bottom: 4px;
        }

        .subject-schedule-empty-desc {
            font-size: 12px;
            color: var(--nm-text-muted);
            margin: 0;
            line-height: 1.5;
        }

        /* 7. Modal Neumorphism Polish */
        .subject-modal .modal-content {
            background-color: var(--nm-surface) !important;
            border: var(--nm-border) !important;
            border-radius: 28px !important;
            box-shadow: var(--nm-shadow-card) !important;
            color: var(--nm-text-main) !important;
        }

        .subject-modal .modal-header,
        .subject-modal .modal-footer {
            border-color: var(--nm-divider) !important;
        }

        .subject-modal .modal-icon-warning {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--nm-surface);
            box-shadow: var(--nm-shadow-inset);
            border: var(--nm-border-subtle);
            color: #ef4444;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin: 0 auto 14px;
        }

        .subject-action-btn.btn-modal-cancel {
            background: var(--nm-surface) !important;
            color: var(--nm-text-main) !important;
            border: var(--nm-border) !important;
            box-shadow: var(--nm-shadow-btn) !important;
        }

        .subject-action-btn.btn-modal-cancel:hover {
            background: var(--nm-surface-hover) !important;
            box-shadow: var(--nm-shadow-btn-hover) !important;
            transform: translateY(-2px);
        }

        .subject-action-btn.btn-modal-cancel:active {
            box-shadow: var(--nm-shadow-inset) !important;
            transform: scale(0.98);
        }

        .subject-modal .btn-delete {
            background: var(--nm-danger-gradient) !important;
            color: #ffffff !important;
            border: 1.5px solid rgba(255, 255, 255, 0.4) !important;
            box-shadow: var(--nm-shadow-danger) !important;
        }

        .subject-modal .btn-delete:hover {
            background: var(--nm-danger-hover-gradient) !important;
            box-shadow: 8px 8px 22px rgba(239, 68, 68, 0.45), -4px -4px 14px rgba(255, 255, 255, 0.9) !important;
            transform: translateY(-2px);
        }

        /* ==========================================================================
           RESPONSIVE BREAKPOINTS
           ========================================================================== */
        @media (max-width: 991.98px) {
            .subject-info-grid,
            .subject-date-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (min-width: 576px) and (max-width: 991.98px) {
            .subject-top-nav {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }

            .subject-btn-back {
                width: auto;
            }

            .subject-action-group {
                width: auto;
                display: inline-flex;
                align-items: center;
                gap: 10px;
            }

            .subject-hero-card {
                padding: 22px 20px;
            }

            .subject-card {
                padding: 22px 20px;
            }
        }

        @media (max-width: 575.98px) {
            .subject-top-nav {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            .subject-btn-back {
                width: 100%;
                justify-content: center;
            }

            .subject-action-group {
                width: 100%;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .subject-action-group .subject-action-btn {
                width: 100%;
                justify-content: center;
            }

            .subject-hero-card {
                padding: 20px 18px;
            }

            .subject-hero-title {
                font-size: 20px;
            }

            .subject-hero-icon {
                width: 52px;
                height: 52px;
                font-size: 24px;
            }

            .subject-hero-aside {
                width: 100%;
                justify-content: flex-start;
                margin-top: 6px;
            }

            .subject-card {
                padding: 20px 18px;
            }
        }

        @media (max-width: 390px) {
            .subject-hero-main {
                gap: 14px;
            }

            .subject-hero-title {
                font-size: 18px;
            }

            .subject-action-btn {
                padding: 0 12px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>

    <!-- MAIN WRAPPER -->
    <div class="main-wrapper main-wrapper-full">
        
        <!-- TOPBAR TRÊN CÙNG COMPACT 58PX -->
        @include('layouts.topbar', ['title' => 'Chi tiết môn học'])

        <!-- CONTENT BODY -->
        <main class="content-body subject-detail-wrapper py-4 px-3 px-md-4">
            <div class="container-xl">
                
                <!-- 1. BREADCRUMB / TOP NAVIGATION BAR -->
                <div class="subject-top-nav">
                    <!-- Bên trái: [← Quay lại Học tập] -->
                    <a href="{{ route('mon-hoc.index') }}" class="subject-btn-back" id="btnBackToStudy">
                        <i class="bi bi-arrow-left"></i>
                        <span>Quay lại Học tập</span>
                    </a>

                    <!-- Bên phải: [Chỉnh sửa] [Xóa] -->
                    <div class="subject-action-group">
                        <a href="{{ route('calendar.index') }}" class="subject-action-btn btn-calendar d-none d-sm-inline-flex" title="Xem trên Lịch trình">
                            <i class="bi bi-calendar3"></i>
                            <span>Lịch trình</span>
                        </a>

                        <a href="{{ route('mon-hoc.edit', $monHoc) }}" class="subject-action-btn btn-edit" id="btnEditSubject">
                            <i class="bi bi-pencil-square"></i>
                            <span>Chỉnh sửa</span>
                        </a>

                        <button type="button" class="subject-action-btn btn-delete" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $monHoc->id }}" id="btnDeleteSubject">
                            <i class="bi bi-trash3"></i>
                            <span>Xóa</span>
                        </button>
                    </div>
                </div>

                @php
                    $statusClass = match($monHoc->trang_thai) {
                        'dang_hoc' => 'status-dang_hoc',
                        'da_hoan_thanh' => 'status-da_hoan_thanh',
                        default => 'status-tam_dung'
                    };
                    $statusText = match($monHoc->trang_thai) {
                        'dang_hoc' => 'Đang học',
                        'da_hoan_thanh' => 'Đã hoàn thành',
                        default => 'Tạm dừng'
                    };
                    $statusDotColor = match($monHoc->trang_thai) {
                        'dang_hoc' => '#4ADE80',
                        'da_hoan_thanh' => '#6C8CFF',
                        default => '#FBBF24'
                    };
                @endphp

                <!-- 2. HERO / SUBJECT HEADER -->
                <div class="subject-hero-card">
                    <div class="subject-hero-inner">
                        <div class="subject-hero-main">
                            <!-- Icon môn học -->
                            <div class="subject-hero-icon">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>

                            <div class="subject-hero-meta">
                                <!-- Tags row -->
                                <div class="subject-hero-tags">
                                    <span class="subject-code-tag">
                                        {{ $monHoc->ma_mon }}
                                    </span>
                                    <span class="subject-status-pill {{ $statusClass }}">
                                        <i class="bi bi-circle-fill" style="font-size: 6px;"></i>
                                        {{ $statusText }}
                                    </span>
                                    @if($monHoc->hoc_ky)
                                        <span class="subject-code-tag d-none d-sm-inline-block">
                                            Học kỳ {{ $monHoc->hoc_ky }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Tên môn học -->
                                <h1 class="subject-hero-title">{{ $monHoc->ten_mon }}</h1>
                            </div>
                        </div>

                        <!-- Right badge on Hero: Nhận diện màu sắc -->
                        <div class="subject-hero-aside">
                            <div class="subject-color-chip" title="Màu sắc nhận diện trên lịch">
                                <span class="subject-color-dot"></span>
                                <span>{{ $monHoc->mau_sac ?? '#6C8CFF' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. MAIN CONTENT GRID (2 COLUMNS DESKTOP, 1 COLUMN MOBILE) -->
                <div class="row g-4">
                    
                    <!-- CỘT CHÍNH (BÊN TRÁI): THÔNG TIN HỌC PHẦN & THỜI GIAN -->
                    <div class="col-lg-8">
                        
                        <!-- CARD 1: THÔNG TIN MÔN HỌC & GIẢNG DẠY -->
                        <div class="subject-card">
                            <div class="subject-card-header">
                                <div>
                                    <h2 class="subject-card-title">
                                        <i class="bi bi-journal-text"></i>
                                        <span>Thông tin Môn học</span>
                                    </h2>
                                    <p class="subject-card-subtitle">Chi tiết về học phần, giảng viên phụ trách và địa điểm tổ chức lớp</p>
                                </div>
                            </div>

                            <div class="subject-info-grid">
                                <!-- Giảng viên giảng dạy -->
                                <div class="subject-info-tile">
                                    <div class="subject-tile-icon">
                                        <i class="bi bi-person-badge"></i>
                                    </div>
                                    <div class="subject-tile-content">
                                        <div class="subject-tile-label">Giảng viên</div>
                                        <div class="subject-tile-value">
                                            {{ $monHoc->giang_vien ?: 'Chưa cập nhật' }}
                                        </div>
                                        <div class="subject-tile-sub">Giảng viên phụ trách học phần</div>
                                    </div>
                                </div>

                                <!-- Địa điểm / Phòng học -->
                                <div class="subject-info-tile">
                                    <div class="subject-tile-icon icon-geo">
                                        <i class="bi bi-geo-alt"></i>
                                    </div>
                                    <div class="subject-tile-content">
                                        <div class="subject-tile-label">Phòng học / Địa điểm</div>
                                        <div class="subject-tile-value">
                                            {{ $monHoc->phong_hoc ?: 'Chưa cập nhật' }}
                                        </div>
                                        <div class="subject-tile-sub">Địa điểm tổ chức buổi học</div>
                                    </div>
                                </div>

                                <!-- Trạng thái môn học -->
                                <div class="subject-info-tile">
                                    <div class="subject-tile-icon icon-status">
                                        <i class="bi bi-flag"></i>
                                    </div>
                                    <div class="subject-tile-content">
                                        <div class="subject-tile-label">Trạng thái</div>
                                        <div class="subject-tile-value">
                                            <span class="subject-status-pill {{ $statusClass }} py-0.5 px-2">
                                                {{ $statusText }}
                                            </span>
                                        </div>
                                        <div class="subject-tile-sub">Tiến trình đăng ký & đào tạo</div>
                                    </div>
                                </div>

                                <!-- Học kỳ & Niên khóa -->
                                <div class="subject-info-tile">
                                    <div class="subject-tile-icon icon-term">
                                        <i class="bi bi-calendar3-range"></i>
                                    </div>
                                    <div class="subject-tile-content">
                                        <div class="subject-tile-label">Kỳ học & Niên khóa</div>
                                        <div class="subject-tile-value">
                                            @if($monHoc->hoc_ky || $monHoc->nam_hoc)
                                                Học kỳ {{ $monHoc->hoc_ky ?? 1 }} ({{ $monHoc->nam_hoc ?? 'Hiện tại' }})
                                            @else
                                                Chưa cập nhật
                                            @endif
                                        </div>
                                        <div class="subject-tile-sub">Khung niên giám đào tạo</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CARD 2: THỜI GIAN ĐÀO TẠO & KẾ HOẠCH -->
                        <div class="subject-card">
                            <div class="subject-card-header">
                                <div>
                                    <h2 class="subject-card-title">
                                        <i class="bi bi-calendar2-week"></i>
                                        <span>Thời gian Đào tạo</span>
                                    </h2>
                                    <p class="subject-card-subtitle">Khung thời gian bắt đầu và kết thúc chương trình học tập</p>
                                </div>
                            </div>

                            <div class="subject-date-grid">
                                <!-- Ngày bắt đầu -->
                                <div class="subject-date-card">
                                    <div class="subject-date-icon">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>
                                    <div class="subject-date-meta">
                                        <div class="subject-date-label">Ngày bắt đầu</div>
                                        <div class="subject-date-val">
                                            {{ $monHoc->ngay_bat_dau ? $monHoc->ngay_bat_dau->format('d/m/Y') : 'Chưa cập nhật' }}
                                        </div>
                                        <div class="subject-date-desc">Thời điểm mở lớp học</div>
                                    </div>
                                </div>

                                <!-- Ngày kết thúc -->
                                <div class="subject-date-card">
                                    <div class="subject-date-icon icon-end">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                    <div class="subject-date-meta">
                                        <div class="subject-date-label">Ngày kết thúc</div>
                                        <div class="subject-date-val">
                                            {{ $monHoc->ngay_ket_thuc ? $monHoc->ngay_ket_thuc->format('d/m/Y') : 'Chưa cập nhật' }}
                                        </div>
                                        <div class="subject-date-desc">Thời điểm kết thúc môn học</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- CỘT PHỤ (BÊN PHẢI): NHẬN DIỆN & LỊCH TRÌNH -->
                    <div class="col-lg-4">
                        
                        <!-- CARD 3: NHẬN DIỆN MÔN HỌC -->
                        <div class="subject-card">
                            <div class="subject-card-header">
                                <div>
                                    <h2 class="subject-card-title">
                                        <i class="bi bi-palette"></i>
                                        <span>Nhận diện Môn học</span>
                                    </h2>
                                    <p class="subject-card-subtitle">Màu đại diện trên Lịch trình Life Planner</p>
                                </div>
                            </div>

                            <div class="subject-color-preview-box">
                                <div class="subject-color-large-bubble"></div>
                                <div class="subject-color-code">{{ $monHoc->mau_sac ?? '#6C8CFF' }}</div>
                                <p class="text-muted small mt-2 mb-0">
                                    Màu sắc này được đồng bộ để phân biệt các tiết học và nhắc nhở trên Lịch trình cá nhân.
                                </p>
                            </div>

                            <a href="{{ route('calendar.index') }}" class="subject-calendar-nav-btn" id="btnViewOnCalendar">
                                <i class="bi bi-calendar3"></i>
                                <span>Xem trên Lịch trình</span>
                            </a>
                        </div>

                        <!-- CARD 4: LỊCH HỌC HÀNG TUẦN -->
                        <div class="subject-card">
                            <div class="subject-card-header">
                                <div>
                                    <h2 class="subject-card-title">
                                        <i class="bi bi-clock-history"></i>
                                        <span>Lịch học Tuần</span>
                                    </h2>
                                    <p class="subject-card-subtitle">Thời khóa biểu cố định của môn</p>
                                </div>
                            </div>

                            @if($monHoc->lichHocs && $monHoc->lichHocs->isNotEmpty())
                                <div class="d-flex flex-column gap-3">
                                    @foreach($monHoc->lichHocs as $lich)
                                        <div class="subject-schedule-item">
                                            @php
                                                $dowMap = [1 => 'Thứ 2', 2 => 'Thứ 3', 3 => 'Thứ 4', 4 => 'Thứ 5', 5 => 'Thứ 6', 6 => 'Thứ 7', 7 => 'Chủ Nhật'];
                                                $tenThu = $dowMap[$lich->ngay_trong_tuan] ?? ('Thứ ' . $lich->ngay_trong_tuan);
                                            @endphp
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="subject-schedule-dow">
                                                    <i class="bi bi-clock-fill me-1.5 fs-7"></i>{{ $tenThu }}
                                                </span>
                                                <span class="subject-schedule-time">
                                                    {{ substr($lich->gio_bat_dau, 0, 5) }} - {{ substr($lich->gio_ket_thuc, 0, 5) }}
                                                </span>
                                            </div>
                                            @if($lich->ghi_chu)
                                                <p class="subject-schedule-note mb-0 mt-1">
                                                    <i class="bi bi-info-circle me-1"></i>{{ $lich->ghi_chu }}
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="subject-schedule-empty">
                                    <i class="bi bi-calendar-event"></i>
                                    <div class="subject-schedule-empty-title">Chưa có lịch học định kỳ</div>
                                    <p class="subject-schedule-empty-desc">
                                        Môn học chưa được xếp lịch học tuần. Bạn có thể theo dõi và đặt lịch hẹn trên Lịch trình.
                                    </p>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- MODAL XÁC NHẬN XÓA MÔN HỌC -->
    <div class="modal fade subject-modal" id="deleteModal{{ $monHoc->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $monHoc->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 p-3">
                <div class="modal-body text-center pt-3 pb-2 px-3">
                    <div class="modal-icon-warning">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2" id="deleteModalLabel{{ $monHoc->id }}">Xác nhận xóa môn học</h5>
                    <p class="text-muted small mb-0" style="line-height: 1.5;">
                        Bạn có chắc chắn muốn xóa môn học <strong class="text-body fw-bold">"{{ $monHoc->ten_mon }}"</strong> (Mã: {{ $monHoc->ma_mon }})? 
                        Dữ liệu sẽ được chuyển vào thùng rác (Soft Delete).
                    </p>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-center gap-2 pt-2 pb-1">
                    <button type="button" class="subject-action-btn btn-modal-cancel" data-bs-dismiss="modal">
                        Hủy bỏ
                    </button>
                    <form action="{{ route('mon-hoc.destroy', $monHoc) }}" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="subject-action-btn btn-delete px-4" id="btnConfirmDelete">
                            <i class="bi bi-trash3 me-1"></i>
                            Đồng ý Xóa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar')?.classList.toggle('show');
        });
    </script>
    <!-- Floating Action Menu (Góc phải bên dưới) -->
    <x-floating-menu />
</body>
</html>
