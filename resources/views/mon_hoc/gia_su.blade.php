<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tìm Gia Sư & Trợ Giảng - Life Planner</title>

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
           HỆ THỐNG BIẾN SOFT NEUMORPHISM ĐỒNG BỘ (MODERN SOFT UI TOKENS)
           Tinh chỉnh giảm độ lan và tương phản bóng, triệt tiêu hào quang trắng chói.
           ========================================================================== */
        :root {
            --nm-bg: #e8ecf2;
            --nm-surface: #eef2f6;
            --nm-surface-hover: #f3f6fa;
            --nm-surface-subtle: #e4eaf2;

            /* Soft Dual Shadows - Giảm độ lan, tạo chiều sâu êm dịu */
            --nm-shadow-flat: 4px 4px 10px rgba(163, 177, 198, 0.42), -4px -4px 10px rgba(255, 255, 255, 0.85);
            --nm-shadow-flat-sm: 2px 2px 6px rgba(163, 177, 198, 0.35), -2px -2px 6px rgba(255, 255, 255, 0.8);
            --nm-shadow-card: 8px 8px 18px rgba(163, 177, 198, 0.38), -8px -8px 18px rgba(255, 255, 255, 0.85);
            --nm-shadow-card-hover: 12px 12px 24px rgba(163, 177, 198, 0.46), -10px -10px 22px rgba(255, 255, 255, 0.95);
            --nm-shadow-btn: 4px 4px 10px rgba(163, 177, 198, 0.38), -4px -4px 10px rgba(255, 255, 255, 0.85);
            --nm-shadow-btn-hover: 6px 6px 14px rgba(163, 177, 198, 0.44), -5px -5px 12px rgba(255, 255, 255, 0.95);
            --nm-shadow-primary: 5px 5px 14px rgba(79, 70, 229, 0.32), -3px -3px 8px rgba(255, 255, 255, 0.7);

            /* Inset / Concave Shadows cho inputs & container tabs */
            --nm-shadow-inset: inset 3px 3px 6px rgba(163, 177, 198, 0.38), inset -3px -3px 6px rgba(255, 255, 255, 0.85);
            --nm-shadow-inset-deep: inset 4px 4px 8px rgba(163, 177, 198, 0.46), inset -4px -4px 8px rgba(255, 255, 255, 0.9);

            /* Đường viền tinh tế */
            --nm-border: 1.5px solid rgba(255, 255, 255, 0.75);
            --nm-border-subtle: 1px solid rgba(163, 177, 198, 0.28);
            --nm-divider: rgba(163, 177, 198, 0.32);

            /* Màu văn bản phân cấp chuẩn */
            --nm-text-main: #1e293b;
            --nm-text-muted: #64748b;
            --nm-text-light: #94a3b8;

            /* Tone màu nhấn Indigo / Violet */
            --nm-primary: #4f46e5;
            --nm-primary-hover: #4338ca;
            --nm-primary-gradient: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            --nm-primary-hover-gradient: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
        }

        /* GIAO DIỆN TỐI (DARK MODE NEUMORPHISM) */
        [data-bs-theme="dark"] {
            --nm-bg: #0f172a;
            --nm-surface: #1e293b;
            --nm-surface-hover: #334155;
            --nm-surface-subtle: #1e293b;

            --nm-shadow-flat: 4px 4px 10px rgba(0, 0, 0, 0.45), -3px -3px 8px rgba(255, 255, 255, 0.035);
            --nm-shadow-flat-sm: 2px 2px 6px rgba(0, 0, 0, 0.4), -2px -2px 5px rgba(255, 255, 255, 0.03);
            --nm-shadow-card: 8px 8px 20px rgba(0, 0, 0, 0.5), -6px -6px 16px rgba(255, 255, 255, 0.035);
            --nm-shadow-card-hover: 12px 12px 26px rgba(0, 0, 0, 0.6), -8px -8px 20px rgba(255, 255, 255, 0.045);
            --nm-shadow-btn: 4px 4px 10px rgba(0, 0, 0, 0.42), -3px -3px 8px rgba(255, 255, 255, 0.03);
            --nm-shadow-btn-hover: 6px 6px 14px rgba(0, 0, 0, 0.52), -4px -4px 10px rgba(255, 255, 255, 0.045);
            --nm-shadow-primary: 5px 5px 14px rgba(99, 102, 241, 0.35), -2px -2px 6px rgba(255, 255, 255, 0.04);

            --nm-shadow-inset: inset 3px 3px 6px rgba(0, 0, 0, 0.6), inset -3px -3px 6px rgba(255, 255, 255, 0.03);
            --nm-shadow-inset-deep: inset 4px 4px 8px rgba(0, 0, 0, 0.7), inset -4px -4px 8px rgba(255, 255, 255, 0.04);

            --nm-border: 1.5px solid rgba(255, 255, 255, 0.06);
            --nm-border-subtle: 1px solid rgba(255, 255, 255, 0.05);
            --nm-divider: rgba(255, 255, 255, 0.08);

            --nm-text-main: #f1f5f9;
            --nm-text-muted: #94a3b8;
            --nm-text-light: #64748b;

            --nm-primary: #6366f1;
            --nm-primary-hover: #4f46e5;
            --nm-primary-gradient: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
            --nm-primary-hover-gradient: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        }

        /* Cấu hình cơ bản */
        body {
            background-color: var(--nm-bg) !important;
            color: var(--nm-text-main) !important;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
        }

        .text-dark-nm {
            color: var(--nm-text-main) !important;
        }

        .letter-spacing-1 {
            letter-spacing: 0.5px;
        }

        /* 1. TOP HEADER NEUMORPHIC */
        .top-header {
            background-color: var(--nm-surface) !important;
            border-bottom: 1px solid rgba(163, 177, 198, 0.25) !important;
            box-shadow: 0 4px 16px rgba(163, 177, 198, 0.22) !important;
            transition: all 0.25s ease;
        }

        [data-bs-theme="dark"] .top-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.45) !important;
        }

        .top-header .btn-back-to-calendar {
            background: var(--nm-surface) !important;
            border: var(--nm-border) !important;
            box-shadow: var(--nm-shadow-btn) !important;
            color: var(--nm-text-main) !important;
            font-weight: 600;
            border-radius: 9999px !important;
            padding: 0.45rem 1.15rem !important;
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

        .top-header .user-avatar {
            box-shadow: var(--nm-shadow-btn) !important;
            border: 2px solid rgba(255, 255, 255, 0.8) !important;
        }

        [data-bs-theme="dark"] .top-header .user-avatar {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        /* 2. SUB-NAVIGATION TABS (STUDY MODULE TRACK) */
        .nm-subnav-track {
            background: var(--nm-surface);
            border: var(--nm-border-subtle);
            box-shadow: var(--nm-shadow-inset);
            border-radius: 9999px;
            padding: 5px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .nm-subnav-tab {
            padding: 0.45rem 1.15rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--nm-text-muted);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            transition: all 0.2s cubic-bezier(0.34, 1.3, 0.64, 1);
        }

        .nm-subnav-tab:hover {
            color: var(--nm-text-main);
        }

        .nm-subnav-tab.active {
            background: var(--nm-surface);
            color: var(--nm-primary);
            border: var(--nm-border);
            box-shadow: var(--nm-shadow-btn);
        }

        [data-bs-theme="dark"] .nm-subnav-tab.active {
            color: #818cf8;
        }

        .nm-stat-chip {
            background: var(--nm-surface);
            border: var(--nm-border);
            box-shadow: var(--nm-shadow-flat);
            border-radius: 9999px;
            padding: 0.45rem 1rem;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--nm-text-muted);
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            transition: all 0.2s ease;
        }

        .nm-status-dot-pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #10b981;
            display: inline-block;
            position: relative;
        }

        .nm-status-dot-pulse::after {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            background-color: rgba(16, 185, 129, 0.4);
            animation: nmPulse 2s infinite;
        }

        @keyframes nmPulse {
            0% { transform: scale(0.9); opacity: 0.8; }
            50% { transform: scale(1.6); opacity: 0; }
            100% { transform: scale(0.9); opacity: 0; }
        }

        /* 3. KHUNG TÌM KIẾM & BỘ LỌC NEUMORPHIC */
        .nm-filter-card {
            background: var(--nm-surface);
            border: var(--nm-border);
            border-radius: 24px;
            box-shadow: var(--nm-shadow-card);
            padding: 1.25rem 1.4rem;
        }

        .nm-input-group {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .nm-input-icon {
            position: absolute;
            left: 1.1rem;
            color: var(--nm-text-light);
            font-size: 0.95rem;
            pointer-events: none;
            z-index: 2;
        }

        .nm-form-input {
            width: 100%;
            background: var(--nm-surface) !important;
            border: var(--nm-border-subtle) !important;
            box-shadow: var(--nm-shadow-inset) !important;
            border-radius: 9999px !important;
            padding: 0.6rem 1rem 0.6rem 2.6rem !important;
            font-size: 0.88rem !important;
            color: var(--nm-text-main) !important;
            outline: none;
            transition: all 0.2s ease;
        }

        .nm-form-input:focus {
            border-color: rgba(99, 102, 241, 0.5) !important;
            box-shadow: var(--nm-shadow-inset), 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
        }

        .nm-select-wrapper {
            position: relative;
            width: 100%;
        }

        .nm-form-select {
            width: 100%;
            background: var(--nm-surface) !important;
            border: var(--nm-border-subtle) !important;
            box-shadow: var(--nm-shadow-inset) !important;
            border-radius: 9999px !important;
            padding: 0.6rem 2.2rem 0.6rem 1.1rem !important;
            font-size: 0.88rem !important;
            color: var(--nm-text-main) !important;
            outline: none;
            appearance: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .nm-form-select:focus {
            border-color: rgba(99, 102, 241, 0.5) !important;
            box-shadow: var(--nm-shadow-inset), 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
        }

        .nm-select-arrow {
            position: absolute;
            right: 1.1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--nm-text-light);
            pointer-events: none;
            font-size: 0.75rem;
        }

        /* 4. CÁC NÚT BẤM NEUMORPHIC */
        .nm-btn-primary {
            background: var(--nm-primary-gradient) !important;
            color: #ffffff !important;
            border: 1.5px solid rgba(255, 255, 255, 0.4) !important;
            border-radius: 9999px !important;
            box-shadow: var(--nm-shadow-primary) !important;
            padding: 0.55rem 1.35rem !important;
            font-weight: 600;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.22s cubic-bezier(0.34, 1.3, 0.64, 1);
        }

        .nm-btn-primary:hover {
            background: var(--nm-primary-hover-gradient) !important;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 6px 6px 18px rgba(79, 70, 229, 0.42), -3px -3px 10px rgba(255, 255, 255, 0.8) !important;
        }

        .nm-btn-primary:active {
            transform: scale(0.97) translateY(0);
            box-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.25) !important;
        }

        .nm-btn-secondary {
            background: var(--nm-surface) !important;
            color: var(--nm-text-main) !important;
            border: var(--nm-border) !important;
            border-radius: 9999px !important;
            box-shadow: var(--nm-shadow-btn) !important;
            padding: 0.55rem 1.35rem !important;
            font-weight: 600;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.22s cubic-bezier(0.34, 1.3, 0.64, 1);
        }

        .nm-btn-secondary:hover {
            color: var(--nm-primary) !important;
            transform: translateY(-2px);
            box-shadow: var(--nm-shadow-btn-hover) !important;
        }

        .nm-btn-secondary:active {
            transform: scale(0.97) translateY(0);
            box-shadow: var(--nm-shadow-inset) !important;
        }

        /* 5. THẺ CARD GIA SƯ NEUMORPHIC */
        .nm-tutor-card {
            background: var(--nm-surface);
            border: var(--nm-border);
            border-radius: 24px;
            box-shadow: var(--nm-shadow-card);
            overflow: hidden;
            position: relative;
            transition: transform 0.28s cubic-bezier(0.34, 1.3, 0.64, 1), box-shadow 0.28s ease;
            display: flex;
            flex-direction: column;
        }

        .nm-tutor-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--nm-shadow-card-hover);
        }

        .nm-card-strip {
            height: 5px;
            width: 100%;
            background: var(--nm-primary-gradient);
            border-top-left-radius: 24px;
            border-top-right-radius: 24px;
            transition: height 0.2s ease;
        }

        .nm-tutor-card:hover .nm-card-strip {
            height: 6px;
        }

        .nm-avatar-wrapper {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            padding: 2px;
            background: var(--nm-surface);
            border: var(--nm-border);
            box-shadow: var(--nm-shadow-flat-sm);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nm-avatar-wrapper img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
        }

        .nm-badge-status {
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.25rem 0.65rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .nm-badge-status.ready {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.25);
            box-shadow: var(--nm-shadow-flat-sm);
        }

        .nm-badge-status.ready i {
            font-size: 0.45rem;
        }

        [data-bs-theme="dark"] .nm-badge-status.ready {
            background: rgba(16, 185, 129, 0.16);
            color: #34d399;
            border-color: rgba(16, 185, 129, 0.3);
        }

        .nm-skills-box {
            background: var(--nm-surface);
            border: var(--nm-border-subtle);
            box-shadow: var(--nm-shadow-inset);
            border-radius: 14px;
            padding: 0.65rem 0.85rem;
        }

        .nm-skills-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--nm-text-muted);
            margin-bottom: 0.25rem;
        }

        .nm-skills-content {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--nm-text-main);
            line-height: 1.4;
        }

        .nm-tutor-desc {
            font-size: 0.82rem;
            color: var(--nm-text-muted);
            line-height: 1.5;
            min-height: 3.75rem;
        }

        .nm-card-divider {
            height: 1.5px;
            background: var(--nm-divider);
            box-shadow: 0 1px 0 rgba(255, 255, 255, 0.85);
            margin: 0.75rem 0;
            border: none;
        }

        [data-bs-theme="dark"] .nm-card-divider {
            box-shadow: 0 1px 0 rgba(255, 255, 255, 0.03);
        }

        .nm-btn-contact {
            background: var(--nm-surface);
            border: var(--nm-border);
            border-radius: 9999px;
            box-shadow: var(--nm-shadow-btn);
            color: var(--nm-primary);
            font-size: 0.84rem;
            font-weight: 600;
            padding: 0.45rem 1.15rem;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.34, 1.3, 0.64, 1);
        }

        .nm-btn-contact:hover {
            color: var(--nm-primary);
            box-shadow: var(--nm-shadow-btn-hover);
            transform: translateY(-2px);
        }

        .nm-btn-contact:active {
            box-shadow: var(--nm-shadow-inset);
            transform: scale(0.97);
        }

        [data-bs-theme="dark"] .nm-btn-contact {
            color: #818cf8;
        }

        /* 6. MODAL NEUMORPHIC (TINH TẾ, CHỐNG CHÓI, SHADOW VÀ OVERLAY TÁCH BIỆT) */
        .nm-modal-content {
            background: var(--nm-surface) !important;
            border-radius: 28px !important;
            border: 1px solid rgba(163, 177, 198, 0.4) !important;
            box-shadow: 
                0 20px 40px -10px rgba(15, 23, 42, 0.22),
                6px 12px 28px rgba(163, 177, 198, 0.32),
                inset 1px 1px 1px rgba(255, 255, 255, 0.6) !important;
            overflow: hidden;
        }

        [data-bs-theme="dark"] .nm-modal-content {
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 
                0 24px 48px -12px rgba(0, 0, 0, 0.8),
                0 12px 28px -6px rgba(0, 0, 0, 0.5),
                inset 1px 1px 1px rgba(255, 255, 255, 0.04) !important;
        }

        .modal-backdrop.show {
            opacity: 0.45 !important;
            background-color: #0f172a !important;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        [data-bs-theme="dark"] .modal-backdrop.show {
            opacity: 0.65 !important;
            background-color: #020617 !important;
        }

        .nm-modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--nm-divider);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nm-modal-title-box {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .nm-modal-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--nm-surface);
            border: var(--nm-border);
            box-shadow: var(--nm-shadow-flat-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: var(--nm-primary);
            flex-shrink: 0;
        }

        .nm-modal-close-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--nm-surface);
            border: var(--nm-border);
            box-shadow: var(--nm-shadow-btn);
            color: var(--nm-text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            outline: none;
            padding: 0;
        }

        .nm-modal-close-btn:hover {
            color: #ef4444;
            transform: scale(1.08);
            box-shadow: var(--nm-shadow-btn-hover);
        }

        .nm-modal-close-btn:active {
            box-shadow: var(--nm-shadow-inset);
            transform: scale(0.95);
        }

        .nm-tutor-mini-card {
            background: var(--nm-surface);
            border: var(--nm-border-subtle);
            box-shadow: var(--nm-shadow-inset);
            border-radius: 16px;
        }

        .nm-copy-group {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .nm-copy-addon {
            position: absolute;
            left: 1.1rem;
            font-size: 0.95rem;
            pointer-events: none;
            z-index: 2;
        }

        .nm-form-input-readonly {
            width: 100%;
            background: var(--nm-surface) !important;
            border: var(--nm-border-subtle) !important;
            box-shadow: var(--nm-shadow-inset) !important;
            border-radius: 14px !important;
            padding: 0.65rem 6.5rem 0.65rem 2.6rem !important;
            font-size: 0.88rem !important;
            outline: none;
        }

        .nm-btn-copy {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--nm-surface);
            border: var(--nm-border);
            box-shadow: var(--nm-shadow-btn);
            color: var(--nm-text-main);
            border-radius: 10px;
            padding: 0.35rem 0.75rem;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .nm-btn-copy:hover {
            color: var(--nm-primary);
            box-shadow: var(--nm-shadow-btn-hover);
        }

        .nm-btn-copy:active {
            box-shadow: var(--nm-shadow-inset);
            transform: translateY(-50%) scale(0.96);
        }

        .nm-modal-alert {
            background: rgba(79, 70, 229, 0.06);
            border: 1px solid rgba(79, 70, 229, 0.18);
            border-radius: 14px;
        }

        [data-bs-theme="dark"] .nm-modal-alert {
            background: rgba(99, 102, 241, 0.08);
            border-color: rgba(99, 102, 241, 0.22);
        }

        .nm-modal-footer {
            padding: 1rem 1.5rem 1.25rem 1.5rem;
            border-top: 1px solid var(--nm-divider);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        /* 7. EMPTY STATE NEUMORPHIC */
        .nm-empty-card {
            background: var(--nm-surface);
            border: var(--nm-border);
            border-radius: 28px;
            box-shadow: var(--nm-shadow-card);
            padding: 3.5rem 2rem;
            text-align: center;
        }

        .nm-empty-icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--nm-surface);
            border: var(--nm-border);
            box-shadow: var(--nm-shadow-card);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            color: var(--nm-text-muted);
            margin-bottom: 1.25rem;
        }

        /* Clamping text */
        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .text-truncate-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Responsive */
        @media (max-width: 767.98px) {
            .nm-filter-card {
                padding: 1rem;
            }
            .nm-empty-card {
                padding: 2.5rem 1.25rem;
            }
        }
    </style>
</head>

<body>

    <!-- MAIN WRAPPER FULL-WIDTH -->
    <div class="main-wrapper main-wrapper-full">

        <!-- HEADER TRÊN CÙNG KÈM NÚT QUAY LẠI LỊCH (ĐỒNG BỘ NEUMORPHIC) -->
        <header class="top-header">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('calendar.index') }}" class="btn-back-to-calendar" title="Quay lại giao diện Lịch chính">
                    <i class="bi bi-arrow-left"></i>
                    <span>Quay lại Lịch</span>
                </a>
                <h5 class="fw-bold text-dark-nm mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-mortarboard-fill text-primary"></i> Tìm Gia Sư & Trợ Giảng
                </h5>
            </div>

            <div class="header-actions d-flex align-items-center gap-3">
                <div class="user-profile dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                        @if(Auth::check() && !empty(Auth::user()->avatar_url))
                            <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="user-avatar rounded-circle object-fit-cover" style="width: 38px; height: 38px;">
                        @else
                            <div class="user-avatar">
                                {{ Auth::check() ? Auth::user()->initials : 'U' }}
                            </div>
                        @endif
                        <span class="d-none d-md-inline fw-semibold text-dark-nm">{{ Auth::user()->ho_ten ?? 'Người dùng' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                        <li><a class="dropdown-item btn-open-profile-modal cursor-pointer" href="#" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="bi bi-person me-2 text-primary"></i>Hồ sơ cá nhân</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- CONTENT BODY -->
        <main class="content-body py-4 px-3 px-md-4">
            <div class="container-xl p-0">

                <!-- SUB NAV TABS: QUẢN LÝ MÔN HỌC / BÁO CÁO GPA / TÌM GIA SƯ -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div class="nm-subnav-track">
                        <a href="{{ route('mon-hoc.index') }}" class="nm-subnav-tab">
                            <i class="bi bi-book-half"></i>
                            <span>Quản lý Môn học</span>
                        </a>
                        <a href="{{ route('bao-cao-hoc-tap.index') }}" class="nm-subnav-tab">
                            <i class="bi bi-bar-chart-line-fill"></i>
                            <span>Báo cáo GPA</span>
                        </a>
                        <a href="{{ route('study.tutors') }}" class="nm-subnav-tab active">
                            <i class="bi bi-person-video3"></i>
                            <span>Tìm Gia sư</span>
                        </a>
                    </div>

                    <div class="nm-stat-chip">
                        <span class="nm-status-dot-pulse"></span>
                        <span><strong class="text-primary">{{ $giaSus->count() }}</strong> gia sư sẵn sàng kết nối 1-1</span>
                    </div>
                </div>

                <!-- KHUNG TÌM KIẾM & BỘ LỌC NEUMORPHIC -->
                <div class="nm-filter-card mb-4">
                    <form method="GET" action="{{ route('study.tutors') }}" class="row g-3 align-items-center">
                        <div class="col-12 col-md-5">
                            <div class="nm-input-group">
                                <span class="nm-input-icon">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="q" value="{{ $keyword ?? '' }}" 
                                       class="nm-form-input" 
                                       placeholder="Tìm theo tên gia sư, kỹ năng, chuyên môn...">
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="nm-select-wrapper">
                                <select name="mon_hoc_id" class="nm-form-select">
                                    <option value="">Tất cả môn học</option>
                                    @foreach($monHocs as $mh)
                                        <option value="{{ $mh->id }}" {{ (string)($monHocId ?? '') === (string)$mh->id ? 'selected' : '' }}>
                                            {{ $mh->ten_mon }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down nm-select-arrow"></i>
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <div class="nm-select-wrapper">
                                <select name="muc_gia" class="nm-form-select">
                                    <option value="">Mọi mức phí</option>
                                    <option value="duoi_180" {{ ($mucGia ?? '') === 'duoi_180' ? 'selected' : '' }}>≤ 180.000đ/h</option>
                                    <option value="tren_180" {{ ($mucGia ?? '') === 'tren_180' ? 'selected' : '' }}>&gt; 180.000đ/h</option>
                                </select>
                                <i class="bi bi-chevron-down nm-select-arrow"></i>
                            </div>
                        </div>

                        <div class="col-12 col-md-2 d-flex gap-2">
                            <button type="submit" class="nm-btn-primary w-100 justify-content-center">
                                <i class="bi bi-funnel-fill"></i>
                                <span>Lọc</span>
                            </button>
                            @if(!empty($keyword) || !empty($monHocId) || !empty($mucGia))
                                <a href="{{ route('study.tutors') }}" class="nm-btn-secondary px-3" title="Đặt lại bộ lọc">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- DANH SÁCH GIA SƯ -->
                <div class="row g-4">
                    @forelse($giaSus as $tutor)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="nm-tutor-card h-100">
                                <!-- Top Accent Strip -->
                                <div class="nm-card-strip"></div>

                                <div class="p-3 p-xl-3.5 d-flex flex-column justify-content-between h-100">
                                    <!-- Top section: Avatar + Info -->
                                    <div>
                                        <div class="d-flex align-items-start gap-3 mb-3">
                                            <div class="nm-avatar-wrapper flex-shrink-0">
                                                <img src="{{ $tutor->avatar_url }}" alt="{{ $tutor->ho_ten }}" 
                                                     class="rounded-circle object-fit-cover" 
                                                     style="width: 54px; height: 54px;">
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                                    <h6 class="fw-bold text-dark-nm mb-0 text-truncate" title="{{ $tutor->ho_ten }}">
                                                        {{ $tutor->ho_ten }}
                                                    </h6>
                                                    <span class="nm-badge-status ready flex-shrink-0">
                                                        <i class="bi bi-circle-fill"></i>
                                                        <span>Sẵn sàng</span>
                                                    </span>
                                                </div>
                                                <div class="d-flex align-items-center gap-1.5 text-primary fw-semibold fs-8 text-truncate mb-1.5">
                                                    <i class="bi bi-mortarboard-fill fs-9"></i>
                                                    <span>{{ $tutor->monHoc ? $tutor->monHoc->ten_mon : 'Chuyên môn mở rộng' }}</span>
                                                </div>
                                                <div class="d-flex align-items-center gap-1.5 text-warning fs-8">
                                                    <i class="bi bi-star-fill"></i>
                                                    <strong class="text-dark-nm">{{ $tutor->danh_gia }}</strong>
                                                    <span class="text-muted">({{ $tutor->so_danh_gia }} đánh giá)</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Specialty & Skills sunken box -->
                                        <div class="nm-skills-box mb-3">
                                            <div class="nm-skills-label">
                                                <i class="bi bi-award-fill me-1 text-primary"></i>Chuyên môn & kỹ năng
                                            </div>
                                            <div class="nm-skills-content text-truncate-2" title="{{ $tutor->chuyen_mon }}">
                                                {{ $tutor->chuyen_mon }}
                                            </div>
                                        </div>

                                        <!-- Bio / Experience Description -->
                                        <p class="nm-tutor-desc mb-3 text-truncate-3" title="{{ $tutor->mo_ta_kinh_nghiem }}">
                                            {{ $tutor->mo_ta_kinh_nghiem }}
                                        </p>
                                    </div>

                                    <!-- Bottom section: Fee & Action Button -->
                                    <div>
                                        <div class="nm-card-divider"></div>
                                        <div class="d-flex align-items-center justify-content-between pt-1">
                                            <div>
                                                <small class="text-muted d-block fs-8 fw-medium">Học phí tham khảo</small>
                                                <span class="fw-bold text-primary fs-6">{{ $tutor->hoc_phi_formatted }}</span>
                                            </div>
                                            <button type="button" 
                                                    class="nm-btn-contact btn-contact-tutor"
                                                    data-name="{{ $tutor->ho_ten }}"
                                                    data-email="{{ $tutor->email }}"
                                                    data-phone="{{ $tutor->so_dien_thoai }}"
                                                    data-major="{{ $tutor->chuyen_mon }}"
                                                    data-rate="{{ $tutor->hoc_phi_formatted }}"
                                                    data-avatar="{{ $tutor->avatar_url }}"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#contactTutorModal">
                                                <i class="bi bi-chat-dots-fill"></i>
                                                <span>Liên hệ</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="nm-empty-card">
                                <div class="nm-empty-icon-circle">
                                    <i class="bi bi-person-x-fill"></i>
                                </div>
                                <h5 class="fw-bold text-dark-nm mb-2">Không tìm thấy gia sư phù hợp</h5>
                                <p class="text-muted fs-7 mb-4 mx-auto" style="max-width: 480px;">
                                    Thử thay đổi từ khóa tìm kiếm, mức giá hoặc chọn bộ lọc môn học khác để tìm thêm chuyên gia hỗ trợ bạn.
                                </p>
                                <a href="{{ route('study.tutors') }}" class="nm-btn-secondary">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                    <span>Xóa bộ lọc tìm kiếm</span>
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>

            </div>
        </main>
    </div>

    <!-- MODAL LIÊN HỆ GIA SƯ NEUMORPHIC -->
    <div class="modal fade" id="contactTutorModal" tabindex="-1" aria-labelledby="contactTutorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="nm-modal-content">
                <!-- Modal Header -->
                <div class="nm-modal-header">
                    <div class="nm-modal-title-box">
                        <div class="nm-modal-icon">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-dark-nm mb-0" id="contactTutorModalLabel">
                                Thông tin kết nối Gia sư
                            </h6>
                            <small class="text-muted fs-8">Kết nối trực tiếp 1-1 qua Zalo hoặc Email</small>
                        </div>
                    </div>
                    <button type="button" class="nm-modal-close-btn" data-bs-dismiss="modal" aria-label="Close" title="Đóng">
                        <i class="bi bi-x-lg fs-7"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-4">
                    <!-- Tutor Mini Card Preview -->
                    <div class="nm-tutor-mini-card d-flex align-items-center gap-3 mb-4 p-3">
                        <div class="nm-avatar-wrapper flex-shrink-0" style="width: 58px; height: 58px;">
                            <img src="" id="modalTutorAvatar" class="rounded-circle object-fit-cover" style="width: 54px; height: 54px;" alt="Tutor">
                        </div>
                        <div class="min-w-0 flex-grow-1">
                            <h6 class="fw-bold text-dark-nm mb-0 text-truncate" id="modalTutorName">Tên Gia Sư</h6>
                            <div class="text-primary fw-semibold fs-8 text-truncate mt-0.5" id="modalTutorMajor">Chuyên môn</div>
                            <div class="fs-8 text-muted mt-0.5 fw-medium" id="modalTutorRate">180.000đ/giờ</div>
                        </div>
                    </div>

                    <!-- Phone / Zalo Input Group -->
                    <div class="mb-3">
                        <label class="form-label fs-8 text-uppercase fw-bold text-muted letter-spacing-1">Số điện thoại / Zalo</label>
                        <div class="nm-copy-group">
                            <span class="nm-copy-addon text-success">
                                <i class="bi bi-telephone-fill"></i>
                            </span>
                            <input type="text" id="modalTutorPhone" class="nm-form-input-readonly fw-bold text-dark-nm" readonly>
                            <button class="nm-btn-copy" type="button" id="btnCopyPhone" title="Sao chép số điện thoại">
                                <i class="bi bi-clipboard"></i>
                                <span class="copy-text d-none d-sm-inline fs-8">Sao chép</span>
                            </button>
                        </div>
                    </div>

                    <!-- Email Input Group -->
                    <div class="mb-3.5">
                        <label class="form-label fs-8 text-uppercase fw-bold text-muted letter-spacing-1">Email liên hệ</label>
                        <div class="nm-copy-group">
                            <span class="nm-copy-addon text-primary">
                                <i class="bi bi-envelope-fill"></i>
                            </span>
                            <input type="text" id="modalTutorEmail" class="nm-form-input-readonly fw-medium text-dark-nm" readonly>
                        </div>
                    </div>

                    <!-- Informative Tip Alert -->
                    <div class="nm-modal-alert d-flex align-items-start gap-2.5 p-3 rounded-3 mt-3">
                        <i class="bi bi-info-circle-fill fs-6 text-primary flex-shrink-0 mt-0.5"></i>
                        <span class="fs-8 text-muted">
                            Bạn có thể liên hệ trực tiếp qua số Zalo hoặc gửi email để trao đổi lịch học và bài tập cần hỗ trợ kèm 1-1.
                        </span>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="nm-modal-footer">
                    <button type="button" class="nm-btn-secondary" data-bs-dismiss="modal">
                        Đóng
                    </button>
                    <a href="#" id="btnZaloLink" target="_blank" class="nm-btn-primary text-decoration-none">
                        <i class="bi bi-chat-fill"></i>
                        <span>Nhắn tin Zalo</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Menu (Góc phải bên dưới) -->
    <x-floating-menu />

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contactButtons = document.querySelectorAll('.btn-contact-tutor');
            const modalAvatar = document.getElementById('modalTutorAvatar');
            const modalName = document.getElementById('modalTutorName');
            const modalMajor = document.getElementById('modalTutorMajor');
            const modalRate = document.getElementById('modalTutorRate');
            const modalPhone = document.getElementById('modalTutorPhone');
            const modalEmail = document.getElementById('modalTutorEmail');
            const btnZaloLink = document.getElementById('btnZaloLink');
            const btnCopyPhone = document.getElementById('btnCopyPhone');

            contactButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const name = this.dataset.name;
                    const email = this.dataset.email || 'Chưa cập nhật';
                    const phone = this.dataset.phone || '0900000000';
                    const major = this.dataset.major;
                    const rate = this.dataset.rate;
                    const avatar = this.dataset.avatar;

                    modalAvatar.src = avatar;
                    modalName.textContent = name;
                    modalMajor.textContent = major;
                    modalRate.textContent = rate;
                    modalPhone.value = phone;
                    modalEmail.value = email;

                    const cleanPhone = phone.replace(/\D/g, '');
                    btnZaloLink.href = 'https://zalo.me/' + cleanPhone;
                });
            });

            btnCopyPhone?.addEventListener('click', function() {
                modalPhone.select();
                navigator.clipboard.writeText(modalPhone.value).then(() => {
                    const originalHtml = this.innerHTML;
                    this.innerHTML = '<i class="bi bi-check2 text-success"></i> <span class="copy-text d-none d-sm-inline fs-8 text-success">Đã chép</span>';
                    setTimeout(() => {
                        this.innerHTML = originalHtml;
                    }, 2000);
                }).catch(() => {
                    document.execCommand('copy');
                    const originalHtml = this.innerHTML;
                    this.innerHTML = '<i class="bi bi-check2 text-success"></i> <span class="copy-text d-none d-sm-inline fs-8 text-success">Đã chép</span>';
                    setTimeout(() => {
                        this.innerHTML = originalHtml;
                    }, 2000);
                });
            });
        });
    </script>
</body>
</html>
