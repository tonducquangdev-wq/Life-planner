<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Danh Sách Môn Học - Life Planner</title>

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

    <style>
        /* ==========================================================================
           HỆ THỐNG BIẾN NEUMORPHISM HIỆN ĐẠI (MODERN SOFT UI DESIGN TOKENS)
           ========================================================================== */
        :root {
            /* Tone màu nền chủ đạo xám xanh nhạt dịu mắt theo chuẩn thiết kế */
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

            /* Đổ bóng dập lõm (Concave / Inset Shadows) cho inputs và trạng thái nhấn */
            --nm-shadow-inset: inset 3px 3px 6px rgba(163, 177, 198, 0.42), inset -3px -3px 6px rgba(255, 255, 255, 0.9);
            --nm-shadow-inset-deep: inset 4px 4px 8px rgba(163, 177, 198, 0.5), inset -4px -4px 8px rgba(255, 255, 255, 0.95);

            /* Viền tinh tế tạo gờ ánh sáng vi tế (Subtle Light Edge) */
            --nm-border: 1.5px solid rgba(255, 255, 255, 0.8);
            --nm-border-subtle: 1px solid rgba(163, 177, 198, 0.28);
            --nm-divider: rgba(163, 177, 198, 0.35);

            /* Màu văn bản phân cấp rõ ràng */
            --nm-text-main: #1e293b;
            --nm-text-muted: #64748b;
            --nm-text-light: #94a3b8;

            /* Màu nhấn (Primary Blue/Indigo/Violet) */
            --nm-primary: #4f46e5;
            --nm-primary-gradient: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            --nm-primary-hover-gradient: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
        }

        /* GIAO DIỆN TỐI (DARK MODE NEUMORPHISM / SOFT UI) */
        [data-bs-theme="dark"] {
            --nm-bg: #151821;
            --nm-surface: #1b1e2a;
            --nm-surface-hover: #222634;
            --nm-surface-subtle: #202432;

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
        }

        /* Áp dụng nền tổng thể chuẩn Neumorphism */
        body {
            background-color: var(--nm-bg) !important;
            color: var(--nm-text-main) !important;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        /* Chuẩn hóa Topbar cho phong cách Neumorphism */
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

        /* ==========================================================================
           COMPONENTS NEUMORPHIC DÙNG CHUNG TRÊN TRANG MÔN HỌC
           ========================================================================== */

        /* 1. NÚT BẤM NEUMORPHIC CHÍNH (PRIMARY - GRADIENT NỔI KHỐI) */
        .nm-btn-primary {
            background: var(--nm-primary-gradient) !important;
            color: #ffffff !important;
            border: 1.5px solid rgba(255, 255, 255, 0.45) !important;
            border-radius: 9999px !important;
            box-shadow: var(--nm-shadow-primary) !important;
            padding: 0.55rem 1.4rem !important;
            font-weight: 600;
            font-size: 0.9rem;
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
            box-shadow: 8px 8px 22px rgba(79, 70, 229, 0.45), -4px -4px 14px rgba(255, 255, 255, 0.9) !important;
        }

        .nm-btn-primary:active {
            transform: scale(0.97) translateY(0);
            box-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.25) !important;
        }

        /* 2. NÚT BẤM NEUMORPHIC PHỤ (SECONDARY - DẬP NỔI MỀM CONVEX) */
        .nm-btn-secondary {
            background: var(--nm-surface) !important;
            color: var(--nm-text-main) !important;
            border: var(--nm-border) !important;
            border-radius: 9999px !important;
            box-shadow: var(--nm-shadow-btn) !important;
            padding: 0.55rem 1.35rem !important;
            font-weight: 600;
            font-size: 0.9rem;
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

        /* 3. NÚT NGUY HIỂM (DANGER GRADIENT CHO THAO TÁC XÓA) */
        .nm-btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            color: #ffffff !important;
            border: 1.5px solid rgba(255, 255, 255, 0.4) !important;
            border-radius: 9999px !important;
            box-shadow: var(--nm-shadow-danger) !important;
            padding: 0.55rem 1.4rem !important;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.22s cubic-bezier(0.34, 1.3, 0.64, 1);
        }

        .nm-btn-danger:hover {
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 8px 8px 22px rgba(239, 68, 68, 0.45), -4px -4px 14px rgba(255, 255, 255, 0.9) !important;
        }

        .nm-btn-danger:active {
            transform: scale(0.97) translateY(0);
            box-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.25) !important;
        }

        /* 4. CHIP THỐNG KÊ NHANH NEUMORPHIC (HEADER STATS) */
        .nm-stat-chip {
            background: var(--nm-surface);
            border: var(--nm-border);
            box-shadow: var(--nm-shadow-flat);
            border-radius: 9999px;
            padding: 0.4rem 0.95rem;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--nm-text-muted);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .nm-stat-chip:hover {
            transform: translateY(-1px);
            box-shadow: var(--nm-shadow-btn-hover);
        }

        .nm-stat-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        /* 5. THẺ CARD MÔN HỌC NEUMORPHIC (COURSE CARD) */
        .nm-course-card {
            background: var(--nm-surface);
            border: var(--nm-border);
            border-radius: 28px;
            box-shadow: var(--nm-shadow-card);
            transition: transform 0.28s cubic-bezier(0.34, 1.3, 0.64, 1), box-shadow 0.28s ease;
            overflow: hidden;
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .nm-course-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--nm-shadow-card-hover);
        }

        /* Dải màu đỉnh card tinh tế */
        .nm-card-strip {
            height: 6px;
            width: 100%;
            border-top-left-radius: 28px;
            border-top-right-radius: 28px;
            transition: height 0.2s ease;
        }

        .nm-course-card:hover .nm-card-strip {
            height: 7px;
        }

        .nm-card-body {
            padding: 1.5rem 1.4rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Badge mã môn học dập lõm (Concave Code Badge) */
        .nm-code-badge {
            background: var(--nm-surface);
            border: var(--nm-border-subtle);
            box-shadow: var(--nm-shadow-inset);
            color: var(--nm-text-main);
            font-weight: 700;
            font-size: 0.78rem;
            padding: 0.35rem 0.75rem;
            border-radius: 10px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
        }

        /* Badge trạng thái đa sắc kết hợp Icon nhận diện */
        .nm-status-badge {
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.35rem 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            letter-spacing: -0.2px;
        }

        .nm-status-learning {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.25);
            box-shadow: 2px 2px 6px rgba(16, 185, 129, 0.12), -2px -2px 6px rgba(255, 255, 255, 0.8);
        }

        [data-bs-theme="dark"] .nm-status-learning {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border-color: rgba(16, 185, 129, 0.3);
            box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.4), -2px -2px 6px rgba(255, 255, 255, 0.03);
        }

        .nm-status-completed {
            background: rgba(79, 70, 229, 0.12);
            color: #4f46e5;
            border: 1px solid rgba(79, 70, 229, 0.25);
            box-shadow: 2px 2px 6px rgba(79, 70, 229, 0.12), -2px -2px 6px rgba(255, 255, 255, 0.8);
        }

        [data-bs-theme="dark"] .nm-status-completed {
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.4), -2px -2px 6px rgba(255, 255, 255, 0.03);
        }

        .nm-status-paused {
            background: rgba(245, 158, 11, 0.12);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.25);
            box-shadow: 2px 2px 6px rgba(245, 158, 11, 0.12), -2px -2px 6px rgba(255, 255, 255, 0.8);
        }

        [data-bs-theme="dark"] .nm-status-paused {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border-color: rgba(245, 158, 11, 0.3);
            box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.4), -2px -2px 6px rgba(255, 255, 255, 0.03);
        }

        /* Tên môn học - thông tin nổi bật nhất */
        .nm-card-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--nm-text-main);
            margin: 0.85rem 0 0.95rem 0;
            line-height: 1.35;
        }

        .nm-title-link {
            color: inherit;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .nm-title-link:hover {
            color: var(--nm-primary);
        }

        /* Danh sách thông tin chi tiết (Meta info) */
        .nm-meta-list {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            margin-bottom: 0.75rem;
        }

        .nm-meta-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        /* Khối squircle Neumorphic bọc icon */
        .nm-icon-squircle {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: var(--nm-surface);
            border: var(--nm-border);
            box-shadow: var(--nm-shadow-flat);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.88rem;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .nm-course-card:hover .nm-icon-squircle {
            transform: scale(1.06);
        }

        .nm-meta-content {
            font-size: 0.84rem;
            color: var(--nm-text-muted);
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .nm-meta-label {
            color: var(--nm-text-muted);
            font-weight: 500;
        }

        .nm-meta-value {
            color: var(--nm-text-main);
            font-weight: 600;
        }

        /* Chip phụ thông tin tín chỉ / bài tập */
        .nm-pill-chip {
            background: var(--nm-surface);
            border: var(--nm-border-subtle);
            box-shadow: var(--nm-shadow-flat);
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--nm-text-muted);
            padding: 0.25rem 0.65rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
        }

        /* Đường rãnh khắc Neumorphic (Grooved Divider) */
        .nm-card-divider {
            height: 2px;
            background: var(--nm-divider);
            box-shadow: 0 1px 0 rgba(255, 255, 255, 0.9);
            margin: 1.15rem 0 1rem 0;
            border: none;
            border-radius: 1px;
        }

        [data-bs-theme="dark"] .nm-card-divider {
            box-shadow: 0 1px 0 rgba(255, 255, 255, 0.03);
        }

        /* Nhóm nút thao tác trên Card */
        .nm-card-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }

        /* Nút Xem chi tiết */
        .nm-btn-detail {
            background: var(--nm-surface);
            border: var(--nm-border);
            border-radius: 9999px;
            box-shadow: var(--nm-shadow-btn);
            color: var(--nm-primary);
            font-size: 0.84rem;
            font-weight: 600;
            padding: 0.45rem 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.34, 1.3, 0.64, 1);
        }

        .nm-btn-detail:hover {
            color: var(--nm-primary);
            box-shadow: var(--nm-shadow-btn-hover);
            transform: translateY(-2px);
        }

        .nm-btn-detail:active {
            box-shadow: var(--nm-shadow-inset);
            transform: scale(0.97);
        }

        /* Nút icon tròn/squircle cho Sửa & Xóa */
        .nm-btn-icon {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: var(--nm-surface);
            border: var(--nm-border);
            box-shadow: var(--nm-shadow-btn);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s cubic-bezier(0.34, 1.3, 0.64, 1);
        }

        .nm-btn-icon:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: var(--nm-shadow-btn-hover);
        }

        .nm-btn-icon:active {
            box-shadow: var(--nm-shadow-inset);
            transform: scale(0.95);
        }

        .nm-btn-icon.nm-btn-warning {
            color: #d97706;
        }

        .nm-btn-icon.nm-btn-warning:hover {
            color: #b45309;
        }

        .nm-btn-icon.nm-btn-danger {
            color: #ef4444;
        }

        .nm-btn-icon.nm-btn-danger:hover {
            color: #dc2626;
        }

        /* 6. MODAL NEUMORPHIC CAO CẤP (TINH CHỈNH CHỐNG CHÓI, GIỮ NGUYÊN CHIỀU SÂU) */
        .nm-modal-content {
            background: var(--nm-surface) !important;
            border-radius: 32px !important;
            /* Viền mảnh màu xanh ghi nhạt phân tách êm dịu với nền sau, chống quầng sáng chói */
            border: 1px solid rgba(163, 177, 198, 0.42) !important;
            /* Đổ bóng Neumorphic đa tầng êm dịu: bóng tối sâu + bóng sáng mờ vi tế, triệt tiêu hào quang trắng */
            box-shadow: 
                0 24px 48px -12px rgba(15, 23, 42, 0.22),
                8px 16px 36px rgba(163, 177, 198, 0.35),
                -3px -3px 10px rgba(255, 255, 255, 0.35),
                inset 1px 1px 1px rgba(255, 255, 255, 0.5) !important;
            overflow: hidden;
        }

        [data-bs-theme="dark"] .nm-modal-content {
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 
                0 24px 48px -12px rgba(0, 0, 0, 0.8),
                0 12px 28px -6px rgba(0, 0, 0, 0.5),
                -1px -1px 6px rgba(255, 255, 255, 0.03),
                inset 1px 1px 1px rgba(255, 255, 255, 0.05) !important;
        }

        /* Overlay Backdrop: cân bằng thị giác, làm mờ nền nhẹ, không quá tối, chống chói */
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
            padding: 1.5rem 1.75rem 1rem 1.75rem;
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
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: var(--nm-surface);
            border: 1px solid rgba(163, 177, 198, 0.35);
            box-shadow: 3px 3px 8px rgba(163, 177, 198, 0.4), -2px -2px 6px rgba(255, 255, 255, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: var(--nm-primary);
            flex-shrink: 0;
        }

        [data-bs-theme="dark"] .nm-modal-icon {
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 3px 3px 8px rgba(0, 0, 0, 0.5), -1px -1px 4px rgba(255, 255, 255, 0.04);
        }

        .nm-modal-close-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--nm-surface);
            border: 1px solid rgba(163, 177, 198, 0.35);
            box-shadow: 3px 3px 8px rgba(163, 177, 198, 0.4), -2px -2px 6px rgba(255, 255, 255, 0.5);
            color: var(--nm-text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            outline: none;
            padding: 0;
        }

        [data-bs-theme="dark"] .nm-modal-close-btn {
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 3px 3px 8px rgba(0, 0, 0, 0.5), -1px -1px 4px rgba(255, 255, 255, 0.04);
        }

        .nm-modal-close-btn:hover {
            color: #ef4444;
            transform: scale(1.08);
            box-shadow: var(--nm-shadow-btn-hover);
        }

        .nm-modal-close-btn:active {
            box-shadow: var(--nm-shadow-inset);
            transform: scale(0.94);
        }

        /* Input fields dập lõm (Concave Form Controls) */
        .nm-form-control,
        .nm-form-select {
            background-color: var(--nm-surface) !important;
            border: var(--nm-border-subtle) !important;
            box-shadow: var(--nm-shadow-inset) !important;
            border-radius: 16px !important;
            color: var(--nm-text-main) !important;
            padding: 0.65rem 1rem !important;
            font-size: 0.9rem !important;
            transition: all 0.2s ease;
        }

        .nm-form-control:focus,
        .nm-form-select:focus {
            border-color: rgba(99, 102, 241, 0.5) !important;
            box-shadow: var(--nm-shadow-inset), 0 0 0 3px rgba(99, 102, 241, 0.18) !important;
            background-color: var(--nm-surface) !important;
            color: var(--nm-text-main) !important;
            outline: none;
        }

        .nm-form-control.is-invalid,
        .nm-form-select.is-invalid {
            border-color: #ef4444 !important;
            box-shadow: var(--nm-shadow-inset), 0 0 0 3px rgba(239, 68, 68, 0.2) !important;
        }

        /* Color Swatches bên trong Modal */
        .color-preset-btn {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid rgba(255, 255, 255, 0.9);
            box-shadow: 3px 3px 6px rgba(163, 177, 198, 0.5), -2px -2px 5px rgba(255, 255, 255, 0.9);
            transition: transform 0.2s cubic-bezier(0.34, 1.3, 0.64, 1), box-shadow 0.2s ease;
        }

        [data-bs-theme="dark"] .color-preset-btn {
            border-color: rgba(255, 255, 255, 0.15);
            box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.5), -2px -2px 5px rgba(255, 255, 255, 0.05);
        }

        .color-preset-btn:hover {
            transform: scale(1.2);
        }

        .color-preset-btn.active {
            transform: scale(1.25);
            box-shadow: 0 0 0 3px var(--nm-surface), 0 0 0 5px var(--nm-primary) !important;
        }

        /* 7. EMPTY STATES NEUMORPHIC */
        .nm-empty-card {
            background: var(--nm-surface);
            border: var(--nm-border);
            border-radius: 32px;
            box-shadow: var(--nm-shadow-card);
            padding: 3.5rem 2rem;
            text-align: center;
        }

        .nm-empty-icon-circle {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: var(--nm-surface);
            border: var(--nm-border);
            box-shadow: var(--nm-shadow-card);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: var(--nm-text-muted);
            margin-bottom: 1.5rem;
        }

        /* Course Card Entry Animation */
        @keyframes slideInDownCourse {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.97);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .card-newly-added {
            animation: slideInDownCourse 0.4s cubic-bezier(0.34, 1.3, 0.64, 1);
        }

        /* THANH TÌM KIẾM NHANH TRÊN NỘI TRANG (INLINE QUICK SEARCH) */
        .nm-inline-search {
            background: var(--nm-surface);
            border: var(--nm-border-subtle);
            box-shadow: var(--nm-shadow-inset);
            border-radius: 9999px;
            padding: 0.35rem 0.5rem 0.35rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            max-width: 380px;
            width: 100%;
            transition: all 0.2s ease;
        }

        .nm-inline-search:focus-within {
            border-color: rgba(99, 102, 241, 0.5);
            box-shadow: var(--nm-shadow-inset), 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .nm-inline-search input {
            border: none;
            background: transparent;
            outline: none;
            color: var(--nm-text-main);
            font-size: 0.88rem;
            width: 100%;
        }

        .nm-inline-search input::placeholder {
            color: var(--nm-text-light);
        }

        /* 8. RESPONSIVE FIXES */
        @media (max-width: 767.98px) {
            .nm-card-actions {
                flex-wrap: wrap;
            }
            .nm-empty-card {
                padding: 2.5rem 1.25rem;
            }
            .nm-modal-content {
                border-radius: 24px !important;
            }
        }
    </style>
</head>

<body>

    <!-- MAIN WRAPPER -->
    <div class="main-wrapper main-wrapper-full">

        <!-- TOPBAR TRÊN CÙNG COMPACT 58PX (KẾ THỪA TỪ LAYOUTS.TOPBAR) -->
        @include('layouts.topbar', ['title' => 'Quản lý môn học'])

        <!-- CONTENT BODY CHỨA NỘI DUNG CHÍNH -->
        <main class="content-body py-4 px-3 px-md-4">
            <div class="container-xl p-0">

                <!-- =========================================================================
                     HEADER NỘI DUNG: TIÊU ĐỀ, STATS CHIPS & HÀNH ĐỘNG CHÍNH
                     ========================================================================= -->
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h4 class="fw-bold text-dark-nm mb-0" style="letter-spacing: -0.02em;">
                                Danh sách Môn Học
                            </h4>
                            <span class="badge rounded-pill px-2.5 py-1 text-primary bg-primary-subtle fs-8 fw-bold">
                                {{ $monHocs->count() }} môn
                            </span>
                        </div>
                        <p class="text-muted mb-0 small">
                            Quản lý danh sách các môn học, thông tin lớp học và thời gian của bạn.
                        </p>
                    </div>

                    <!-- Khu vực các nút hành động chính & Thanh tìm kiếm trực quan -->
                    <div class="d-flex align-items-center gap-2 flex-wrap justify-content-start justify-content-lg-end">
                        <!-- Ô tìm kiếm nội trang Neumorphic (Real-time Filter) -->
                        <div class="nm-inline-search" id="nmInlineSearchBox">
                            <i class="bi bi-search text-muted fs-7"></i>
                            <input type="text" id="monHocSearchInput" placeholder="Tìm tên môn, mã môn, giảng viên..." autocomplete="off">
                            <button type="button" class="btn btn-sm p-0 text-muted d-none border-0 bg-transparent" id="btnClearSearch" title="Xóa tìm kiếm">
                                <i class="bi bi-x-circle-fill fs-6"></i>
                            </button>
                        </div>

                        <!-- Nút Tìm Gia Sư (Hành động phụ - Secondary Embossed) -->
                        <a href="{{ route('study.tutors') }}" class="nm-btn-secondary" title="Kết nối giảng viên và trợ giảng">
                            <i class="bi bi-person-video3 fs-6 text-primary"></i>
                            <span>Tìm Gia sư</span>
                        </a>

                        <!-- Nút Thêm Môn Học Mới (Hành động chính - Primary Gradient) -->
                        <button type="button" class="nm-btn-primary"
                                data-bs-toggle="modal" data-bs-target="#createMonHocModal" id="btnOpenCreateMonHoc">
                            <i class="bi bi-plus-circle-fill fs-6"></i>
                            <span>Thêm môn học mới</span>
                        </button>
                    </div>
                </div>

                <!-- Dải thống kê trạng thái nhanh (Neumorphic Status Chips) -->
                <div class="d-flex align-items-center gap-2 flex-wrap mb-4" id="statsChipRow">
                    <div class="nm-stat-chip">
                        <span class="nm-stat-dot bg-primary"></span>
                        <span>Tổng cộng: <strong class="text-dark-nm" id="statTotalCount">{{ $monHocs->count() }}</strong></span>
                    </div>
                    <div class="nm-stat-chip">
                        <span class="nm-stat-dot bg-success"></span>
                        <span>Đang học: <strong class="text-dark-nm" id="statLearningCount">{{ $monHocs->where('trang_thai', 'dang_hoc')->count() }}</strong></span>
                    </div>
                    <div class="nm-stat-chip">
                        <span class="nm-stat-dot bg-indigo" style="background-color: #6366f1;"></span>
                        <span>Đã hoàn thành: <strong class="text-dark-nm" id="statCompletedCount">{{ $monHocs->where('trang_thai', 'da_hoan_thanh')->count() }}</strong></span>
                    </div>
                    @if($monHocs->where('trang_thai', 'tam_dung')->count() > 0)
                    <div class="nm-stat-chip">
                        <span class="nm-stat-dot bg-warning"></span>
                        <span>Tạm dừng: <strong class="text-dark-nm">{{ $monHocs->where('trang_thai', 'tam_dung')->count() }}</strong></span>
                    </div>
                    @endif
                </div>

                <!-- Flash Alert Nếu có từ Backend Session -->
                @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 mb-4 d-flex align-items-center gap-2"
                     style="background: rgba(16, 185, 129, 0.12); color: #065f46; border: 1.5px solid rgba(16, 185, 129, 0.25) !important; box-shadow: var(--nm-shadow-flat);"
                     role="alert">
                    <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                    <span class="fw-semibold">{{ session('status') }}</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <!-- Trạng thái Không tìm thấy kết quả sau khi tìm kiếm (Search Empty State - Ẩn mặc định) -->
                <div class="col-12 d-none mb-4" id="monHocSearchEmptyState">
                    <div class="nm-empty-card py-5">
                        <div class="nm-empty-icon-circle text-primary">
                            <i class="bi bi-search"></i>
                        </div>
                        <h5 class="fw-bold text-dark-nm mb-2">Không tìm thấy môn học nào</h5>
                        <p class="text-muted mb-4 small" id="searchEmptyKeywordText">
                            Không có môn học nào khớp với từ khóa tìm kiếm của bạn.
                        </p>
                        <button type="button" class="nm-btn-secondary" id="btnResetSearchFromEmpty">
                            <i class="bi bi-arrow-counterclockwise"></i>
                            <span>Xóa từ khóa tìm kiếm</span>
                        </button>
                    </div>
                </div>

                <!-- =========================================================================
                     DANH SÁCH THẺ MÔN HỌC (NEUMORPHIC COURSE CARD GRID)
                     ========================================================================= -->
                <div class="row g-4" id="monHocGrid">
                    @forelse($monHocs as $monHoc)
                    <div class="col-12 col-md-6 col-xl-4 mon-hoc-item" 
                         id="monHocCard{{ $monHoc->id }}"
                         data-search="{{ Str::lower($monHoc->ten_mon . ' ' . $monHoc->ma_mon . ' ' . ($monHoc->giang_vien ?? '') . ' ' . ($monHoc->phong_hoc ?? '')) }}">
                        <div class="nm-course-card">
                            <!-- Dải màu trên đầu card xác định theo thuộc tính mau_sac -->
                            <div class="nm-card-strip" style="background-color: {{ $monHoc->mau_sac ?? '#6366f1' }};"></div>

                            <div class="nm-card-body">
                                <div>
                                    <!-- Header Card: Mã môn học & Huy hiệu trạng thái -->
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="nm-code-badge" title="Mã môn học">
                                            {{ $monHoc->ma_mon }}
                                        </span>

                                        @php
                                        $statusConfig = match($monHoc->trang_thai) {
                                            'dang_hoc' => [
                                                'class' => 'nm-status-learning',
                                                'icon' => 'bi bi-play-circle-fill',
                                                'text' => 'Đang học',
                                            ],
                                            'da_hoan_thanh' => [
                                                'class' => 'nm-status-completed',
                                                'icon' => 'bi bi-check-circle-fill',
                                                'text' => 'Đã hoàn thành',
                                            ],
                                            default => [
                                                'class' => 'nm-status-paused',
                                                'icon' => 'bi bi-pause-circle-fill',
                                                'text' => 'Tạm dừng',
                                            ],
                                        };
                                        @endphp
                                        <span class="nm-status-badge {{ $statusConfig['class'] }}">
                                            <i class="{{ $statusConfig['icon'] }}"></i>
                                            <span>{{ $statusConfig['text'] }}</span>
                                        </span>
                                    </div>

                                    <!-- Tên Môn Học - Thông tin nổi bật nhất -->
                                    <h5 class="nm-card-title text-truncate" title="{{ $monHoc->ten_mon }}">
                                        <a href="{{ route('mon-hoc.show', $monHoc) }}" class="nm-title-link">
                                            {{ $monHoc->ten_mon }}
                                        </a>
                                    </h5>

                                    <!-- Danh sách thông tin Metadata: Giảng viên, Phòng học, Thời gian -->
                                    <div class="nm-meta-list">
                                        <!-- Giảng viên -->
                                        <div class="nm-meta-item">
                                            <div class="nm-icon-squircle text-primary" title="Giảng viên">
                                                <i class="bi bi-person-badge"></i>
                                            </div>
                                            <div class="nm-meta-content">
                                                <span class="nm-meta-label">Giảng viên:</span>
                                                <strong class="nm-meta-value">{{ $monHoc->giang_vien ?? 'Chưa cập nhật' }}</strong>
                                            </div>
                                        </div>

                                        <!-- Phòng học -->
                                        <div class="nm-meta-item">
                                            <div class="nm-icon-squircle text-danger" title="Phòng học">
                                                <i class="bi bi-geo-alt"></i>
                                            </div>
                                            <div class="nm-meta-content">
                                                <span class="nm-meta-label">Phòng học:</span>
                                                <strong class="nm-meta-value">{{ $monHoc->phong_hoc ?? 'Chưa cập nhật' }}</strong>
                                            </div>
                                        </div>

                                        <!-- Thời gian học -->
                                        <div class="nm-meta-item">
                                            <div class="nm-icon-squircle text-info" title="Thời gian học">
                                                <i class="bi bi-calendar3"></i>
                                            </div>
                                            <div class="nm-meta-content">
                                                <span class="nm-meta-label">Thời gian:</span>
                                                <strong class="nm-meta-value">
                                                    {{ $monHoc->ngay_bat_dau ? $monHoc->ngay_bat_dau->format('d/m/Y') : 'Linh hoạt' }}@if($monHoc->ngay_ket_thuc) - {{ $monHoc->ngay_ket_thuc->format('d/m/Y') }}@endif
                                                </strong>
                                            </div>
                                        </div>

                                        <!-- Tín chỉ & Số lượng bài tập liên kết -->
                                        <div class="d-flex align-items-center gap-2 mt-1 pt-1">
                                            <span class="nm-pill-chip" title="Số tín chỉ">
                                                <i class="bi bi-award-fill text-warning me-1"></i>{{ $monHoc->so_tin_chi ?? 3 }} tín chỉ
                                            </span>
                                            @if(($monHoc->bai_taps_count ?? 0) > 0)
                                            <span class="nm-pill-chip" title="Số bài tập">
                                                <i class="bi bi-journal-check text-success me-1"></i>{{ $monHoc->bai_taps_count }} bài tập
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <!-- Đường rãnh phân cách Neumorphic -->
                                    <div class="nm-card-divider"></div>

                                    <!-- Nhóm nút thao tác: Xem chi tiết, Chỉnh sửa, Xóa -->
                                    <div class="nm-card-actions">
                                        <a href="{{ route('mon-hoc.show', $monHoc) }}" class="nm-btn-detail" title="Xem chi tiết môn học">
                                            <i class="bi bi-eye"></i>
                                            <span>Xem chi tiết</span>
                                        </a>

                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ route('mon-hoc.edit', $monHoc) }}" class="nm-btn-icon nm-btn-warning" title="Chỉnh sửa môn học">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>

                                            <button type="button" class="nm-btn-icon nm-btn-danger"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal{{ $monHoc->id }}" title="Xóa môn học">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- MODAL XÁC NHẬN XÓA MÔN HỌC (NEUMORPHIC CONFIRM DIALOG) -->
                        <div class="modal fade" id="deleteModal{{ $monHoc->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content nm-modal-content border-0 p-2">
                                    <div class="nm-modal-header border-0 pb-0">
                                        <div class="nm-modal-title-box">
                                            <div class="nm-modal-icon text-danger" style="background: rgba(239, 68, 68, 0.1);">
                                                <i class="bi bi-exclamation-triangle-fill"></i>
                                            </div>
                                            <div>
                                                <h5 class="modal-title fw-bold text-danger mb-0">Xác nhận xóa</h5>
                                                <small class="text-muted">Hành động này có thể khôi phục từ thùng rác</small>
                                            </div>
                                        </div>
                                        <button type="button" class="nm-modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                    <div class="modal-body px-4 py-3">
                                        <p class="mb-2 text-dark-nm">
                                            Bạn có chắc chắn muốn xóa môn học <strong class="text-primary">{{ $monHoc->ten_mon }}</strong> (Mã: <code>{{ $monHoc->ma_mon }}</code>)?
                                        </p>
                                        <div class="p-3 rounded-4 small" style="background: var(--nm-surface); box-shadow: var(--nm-shadow-inset); border: var(--nm-border-subtle);">
                                            <i class="bi bi-info-circle-fill text-muted me-1"></i>
                                            Môn học sẽ được chuyển vào mục lưu trữ và bạn có thể khôi phục lại khi cần thiết.
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 px-4 pb-3 pt-0 d-flex justify-content-end gap-2">
                                        <button type="button" class="nm-btn-secondary" data-bs-dismiss="modal">
                                            Hủy bỏ
                                        </button>
                                        <form action="{{ route('mon-hoc.destroy', $monHoc) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="nm-btn-danger">
                                                <i class="bi bi-trash-fill"></i>
                                                <span>Đồng ý Xóa</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    @empty
                    <!-- Trạng thái khi cơ sở dữ liệu chưa có môn học nào -->
                    <div class="col-12" id="monHocEmptyState">
                        <div class="nm-empty-card">
                            <div class="nm-empty-icon-circle text-primary">
                                <i class="bi bi-journal-album"></i>
                            </div>
                            <h4 class="fw-bold text-dark-nm mb-2">Chưa có môn học nào</h4>
                            <p class="text-muted mb-4 mx-auto" style="max-width: 480px;">
                                Bắt đầu quản lý việc học của bạn một cách khoa học bằng cách thêm môn học đầu tiên vào kế hoạch.
                            </p>
                            <button type="button" class="nm-btn-primary" data-bs-toggle="modal" data-bs-target="#createMonHocModal">
                                <i class="bi bi-plus-circle-fill"></i>
                                <span>Thêm môn học ngay</span>
                            </button>
                        </div>
                    </div>
                    @endforelse
                </div>

            </div>
        </main>
    </div>

    <!-- =========================================================================
         MODAL: THÊM MÔN HỌC MỚI (NEUMORPHIC MODAL FORM - BOOTSTRAP 5)
         ========================================================================= -->
    <div class="modal fade" id="createMonHocModal" tabindex="-1" aria-labelledby="createMonHocModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
            <div class="modal-content nm-modal-content border-0 p-2">
                
                <!-- Modal Header -->
                <div class="nm-modal-header border-0 pb-2">
                    <div class="nm-modal-title-box">
                        <div class="nm-modal-icon">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark-nm mb-0" id="createMonHocModalLabel">
                                Thêm môn học mới
                            </h5>
                            <small class="text-muted">Nhập thông tin môn học để quản lý học tập cá nhân</small>
                        </div>
                    </div>
                    <button type="button" class="nm-modal-close-btn" data-bs-dismiss="modal" aria-label="Close" id="btnCloseCreateModalX">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Modal Form -->
                <form id="createMonHocForm" autocomplete="off" novalidate>
                    @csrf
                    <div class="modal-body px-4 py-3">

                        <!-- Khung thông báo lỗi chung nếu có -->
                        <div class="alert alert-danger border-0 rounded-4 py-2.5 px-3 small d-none mb-3" id="modalAlertError" role="alert"
                             style="background: rgba(239, 68, 68, 0.12); color: #b91c1c; border: 1px solid rgba(239, 68, 68, 0.25) !important;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-triangle-fill flex-shrink-0 fs-6"></i>
                                <span id="modalAlertErrorText" class="fw-medium">Vui lòng kiểm tra lại thông tin đã nhập.</span>
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- 1. Tên môn học * (BẮT BUỘC) -->
                            <div class="col-12">
                                <label for="modal_ten_mon" class="form-label fw-semibold small mb-1 text-dark-nm">
                                    Tên môn học <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control nm-form-control" 
                                       id="modal_ten_mon" 
                                       name="ten_mon" 
                                       placeholder="VD: Lập trình Web nâng cao" 
                                       required>
                                <div class="invalid-feedback small" id="error_ten_mon">Vui lòng nhập tên môn học.</div>
                            </div>

                            <!-- 2. Mã môn học (Tùy chọn) -->
                            <div class="col-12 col-sm-6">
                                <label for="modal_ma_mon" class="form-label fw-semibold small mb-1 text-dark-nm">
                                    Mã môn học <small class="text-muted fw-normal">(Tùy chọn)</small>
                                </label>
                                <input type="text" 
                                       class="form-control nm-form-control font-monospace text-uppercase" 
                                       id="modal_ma_mon" 
                                       name="ma_mon" 
                                       placeholder="VD: INT1234">
                                <div class="invalid-feedback small" id="error_ma_mon"></div>
                            </div>

                            <!-- 3. Giảng viên giảng dạy -->
                            <div class="col-12 col-sm-6">
                                <label for="modal_giang_vien" class="form-label fw-semibold small mb-1 text-dark-nm">
                                    Giảng viên giảng dạy
                                </label>
                                <input type="text" 
                                       class="form-control nm-form-control" 
                                       id="modal_giang_vien" 
                                       name="giang_vien" 
                                       placeholder="VD: TS. Nguyễn Văn A">
                                <div class="invalid-feedback small" id="error_giang_vien"></div>
                            </div>

                            <!-- 4. Phòng học -->
                            <div class="col-12 col-sm-6">
                                <label for="modal_phong_hoc" class="form-label fw-semibold small mb-1 text-dark-nm">
                                    Phòng học
                                </label>
                                <input type="text" 
                                       class="form-control nm-form-control" 
                                       id="modal_phong_hoc" 
                                       name="phong_hoc" 
                                       placeholder="VD: Phòng B2.04, Online...">
                                <div class="invalid-feedback small" id="error_phong_hoc"></div>
                            </div>

                            <!-- 5. Trạng thái học tập -->
                            <div class="col-12 col-sm-6">
                                <label for="modal_trang_thai" class="form-label fw-semibold small mb-1 text-dark-nm">
                                    Trạng thái học tập
                                </label>
                                <select class="form-select nm-form-select" id="modal_trang_thai" name="trang_thai">
                                    <option value="dang_hoc" selected>Đang học</option>
                                    <option value="da_hoan_thanh">Đã hoàn thành</option>
                                    <option value="tam_dung">Tạm dừng</option>
                                </select>
                                <div class="invalid-feedback small" id="error_trang_thai"></div>
                            </div>

                            <!-- 6. Ngày bắt đầu & Ngày kết thúc -->
                            <div class="col-12 col-sm-6">
                                <label for="modal_ngay_bat_dau" class="form-label fw-semibold small mb-1 text-dark-nm">
                                    Ngày bắt đầu
                                </label>
                                <input type="date" class="form-control nm-form-control" id="modal_ngay_bat_dau" name="ngay_bat_dau">
                                <div class="invalid-feedback small" id="error_ngay_bat_dau"></div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <label for="modal_ngay_ket_thuc" class="form-label fw-semibold small mb-1 text-dark-nm">
                                    Ngày kết thúc
                                </label>
                                <input type="date" class="form-control nm-form-control" id="modal_ngay_ket_thuc" name="ngay_ket_thuc">
                                <div class="invalid-feedback small" id="error_ngay_ket_thuc"></div>
                            </div>

                            <!-- 7. Màu hiển thị nhận diện (Neumorphic Color Picker & Swatches) -->
                            <div class="col-12">
                                <label class="form-label fw-semibold small mb-1.5 d-flex align-items-center justify-content-between text-dark-nm">
                                    <span>Màu hiển thị nhận diện</span>
                                    <small class="text-muted">Đồng bộ trên lịch học</small>
                                </label>
                                <div class="d-flex align-items-center gap-2.5 flex-wrap p-2.5 rounded-4" 
                                     style="background: var(--nm-surface); box-shadow: var(--nm-shadow-inset); border: var(--nm-border-subtle);">
                                    <input type="color" 
                                           class="form-control form-control-color border-0 p-0 rounded-circle flex-shrink-0 cursor-pointer" 
                                           id="modal_mau_sac" 
                                           name="mau_sac" 
                                           value="#6366f1" 
                                           style="width: 34px; height: 34px;"
                                           title="Chọn màu tự do">
                                    
                                    <div class="d-flex align-items-center gap-2 flex-wrap" id="modalColorPresets">
                                        <button type="button" class="color-preset-btn active" data-color="#6366f1" style="background: #6366f1;" title="Indigo"></button>
                                        <button type="button" class="color-preset-btn" data-color="#3b82f6" style="background: #3b82f6;" title="Blue"></button>
                                        <button type="button" class="color-preset-btn" data-color="#10b981" style="background: #10b981;" title="Emerald"></button>
                                        <button type="button" class="color-preset-btn" data-color="#f59e0b" style="background: #f59e0b;" title="Amber"></button>
                                        <button type="button" class="color-preset-btn" data-color="#ef4444" style="background: #ef4444;" title="Red"></button>
                                        <button type="button" class="color-preset-btn" data-color="#8b5cf6" style="background: #8b5cf6;" title="Purple"></button>
                                        <button type="button" class="color-preset-btn" data-color="#ec4899" style="background: #ec4899;" title="Pink"></button>
                                    </div>
                                </div>
                                <div class="invalid-feedback small" id="error_mau_sac"></div>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer border-0 px-4 pb-4 pt-2 d-flex justify-content-end gap-2">
                        <button type="button" class="nm-btn-secondary" data-bs-dismiss="modal" id="btnCancelCreateModal">
                            Hủy bỏ
                        </button>
                        <button type="submit" class="nm-btn-primary" id="btnSubmitCreateMonHoc">
                            <i class="bi bi-plus-lg"></i>
                            <span id="btnSubmitText">Thêm môn học</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- =========================================================================
         TOAST NOTIFICATION THÀNH CÔNG (NEUMORPHIC FLOATING TOAST)
         ========================================================================= -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100; margin-top: 65px;">
        <div id="monHocSuccessToast" class="toast align-items-center border-0 rounded-4" role="alert" aria-live="assertive" aria-atomic="true"
             style="background: var(--nm-surface); color: var(--nm-text-main); border: var(--nm-border) !important; box-shadow: var(--nm-shadow-card);">
            <div class="d-flex align-items-center">
                <div class="toast-body d-flex align-items-center gap-2.5 fw-semibold py-3 px-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white" style="width: 28px; height: 28px;">
                        <i class="bi bi-check-lg fs-6"></i>
                    </div>
                    <span id="monHocToastMessage">Môn học đã được thêm thành công!</span>
                </div>
                <button type="button" class="btn-close me-3 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- FLOATING ACTION MENU -->
    <x-floating-menu />

    <!-- =========================================================================
         JAVASCRIPT: ĐIỀU KHIỂN TÌM KIẾM, MODAL & AJAX FORM THÊM MÔN HỌC
         ========================================================================= -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const createModalEl = document.getElementById('createMonHocModal');
            const createModal = createModalEl ? new bootstrap.Modal(createModalEl) : null;
            const createForm = document.getElementById('createMonHocForm');
            const btnSubmit = document.getElementById('btnSubmitCreateMonHoc');
            const btnSubmitText = document.getElementById('btnSubmitText');
            const toastEl = document.getElementById('monHocSuccessToast');
            const toastMsgEl = document.getElementById('monHocToastMessage');
            const toast = toastEl ? new bootstrap.Toast(toastEl, { delay: 3500 }) : null;
            const alertError = document.getElementById('modalAlertError');
            const alertErrorText = document.getElementById('modalAlertErrorText');

            // 1. TÌM KIẾM THỜI GIAN THỰC (REAL-TIME FILTERING)
            const inlineSearchInput = document.getElementById('monHocSearchInput');
            const topbarSearchInput = document.querySelector('.top-header .search-box input');
            const btnClearSearch = document.getElementById('btnClearSearch');
            const searchEmptyState = document.getElementById('monHocSearchEmptyState');
            const searchEmptyKeywordText = document.getElementById('searchEmptyKeywordText');
            const btnResetSearchFromEmpty = document.getElementById('btnResetSearchFromEmpty');

            function filterMonHocs(query) {
                const term = (query || '').trim().toLowerCase();
                const items = document.querySelectorAll('.mon-hoc-item');
                let visibleCount = 0;

                items.forEach(item => {
                    const searchData = item.getAttribute('data-search') || item.textContent.toLowerCase();
                    if (!term || searchData.includes(term)) {
                        item.classList.remove('d-none');
                        visibleCount++;
                    } else {
                        item.classList.add('d-none');
                    }
                });

                // Cập nhật trạng thái nút xóa tìm kiếm
                if (btnClearSearch) {
                    if (term) btnClearSearch.classList.remove('d-none');
                    else btnClearSearch.classList.add('d-none');
                }

                // Cập nhật Empty Search State nếu không có kết quả
                if (searchEmptyState) {
                    if (term && visibleCount === 0 && items.length > 0) {
                        searchEmptyState.classList.remove('d-none');
                        if (searchEmptyKeywordText) {
                            searchEmptyKeywordText.textContent = `Không tìm thấy môn học nào khớp với từ khóa "${query}".`;
                        }
                    } else {
                        searchEmptyState.classList.add('d-none');
                    }
                }
            }

            // Đồng bộ giữa hai ô tìm kiếm (Inline & Topbar)
            if (inlineSearchInput) {
                inlineSearchInput.addEventListener('input', function () {
                    const val = this.value;
                    if (topbarSearchInput && topbarSearchInput.value !== val) {
                        topbarSearchInput.value = val;
                    }
                    filterMonHocs(val);
                });
            }

            if (topbarSearchInput) {
                topbarSearchInput.setAttribute('placeholder', 'Tìm kiếm môn học, mã môn, giảng viên...');
                topbarSearchInput.addEventListener('input', function () {
                    const val = this.value;
                    if (inlineSearchInput && inlineSearchInput.value !== val) {
                        inlineSearchInput.value = val;
                    }
                    filterMonHocs(val);
                });
            }

            function clearSearch() {
                if (inlineSearchInput) inlineSearchInput.value = '';
                if (topbarSearchInput) topbarSearchInput.value = '';
                filterMonHocs('');
                if (inlineSearchInput) inlineSearchInput.focus();
            }

            if (btnClearSearch) btnClearSearch.addEventListener('click', clearSearch);
            if (btnResetSearchFromEmpty) btnResetSearchFromEmpty.addEventListener('click', clearSearch);

            // 2. THEME ICON VÀ CHUYỂN ĐỔI CHẾ ĐỘ SÁNG / TỐI
            const quickThemeBtn = document.getElementById('quickThemeBtn');
            const quickThemeIcon = document.getElementById('quickThemeIcon');

            function syncThemeIcon(theme) {
                if (!quickThemeIcon) return;
                if (theme === 'dark') {
                    quickThemeIcon.className = 'bi bi-sun-fill text-warning fs-6';
                } else {
                    quickThemeIcon.className = 'bi bi-moon-stars-fill text-primary fs-6';
                }
            }

            const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            syncThemeIcon(currentTheme);

            if (quickThemeBtn) {
                quickThemeBtn.addEventListener('click', function () {
                    const activeTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
                    const newTheme = activeTheme === 'dark' ? 'light' : 'dark';

                    document.documentElement.setAttribute('data-bs-theme', newTheme);
                    if (newTheme === 'dark') {
                        document.documentElement.classList.add('dark-theme');
                    } else {
                        document.documentElement.classList.remove('dark-theme');
                    }
                    localStorage.setItem('theme', newTheme);
                    syncThemeIcon(newTheme);

                    if (csrfToken) {
                        fetch("{{ route('profile.theme.quick-toggle') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrfToken,
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({ theme: newTheme })
                        }).catch(() => {});
                    }
                });
            }

            // Hiển thị Toast thông báo
            function showToast(message) {
                if (toastMsgEl) toastMsgEl.textContent = message;
                if (toast) toast.show();
            }

            // Xóa toàn bộ trạng thái lỗi validation trong modal
            function clearValidationErrors() {
                if (alertError) alertError.classList.add('d-none');
                createForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                createForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
            }

            // Hiển thị lỗi trường cụ thể
            function setFieldError(fieldId, errorMsg) {
                const input = document.getElementById(`modal_${fieldId}`);
                const feedback = document.getElementById(`error_${fieldId}`);
                if (input) input.classList.add('is-invalid');
                if (feedback) feedback.textContent = errorMsg;
            }

            // Chọn màu nhanh từ Swatches
            const colorInput = document.getElementById('modal_mau_sac');
            const presetButtons = document.querySelectorAll('#modalColorPresets .color-preset-btn');
            presetButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const color = this.getAttribute('data-color');
                    if (colorInput) colorInput.value = color;
                    presetButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            if (colorInput) {
                colorInput.addEventListener('input', function () {
                    presetButtons.forEach(b => b.classList.remove('active'));
                });
            }

            // Ràng buộc ngày kết thúc >= ngày bắt đầu
            const startDateInput = document.getElementById('modal_ngay_bat_dau');
            const endDateInput = document.getElementById('modal_ngay_ket_thuc');
            function syncDateConstraints() {
                if (startDateInput && endDateInput) {
                    if (startDateInput.value) {
                        endDateInput.min = startDateInput.value;
                    } else {
                        endDateInput.removeAttribute('min');
                    }
                }
            }
            if (startDateInput) {
                startDateInput.addEventListener('change', syncDateConstraints);
            }

            // Focus vào Tên môn học khi Modal mở
            if (createModalEl) {
                createModalEl.addEventListener('shown.bs.modal', function () {
                    const nameInput = document.getElementById('modal_ten_mon');
                    if (nameInput) nameInput.focus();
                });

                createModalEl.addEventListener('hidden.bs.modal', function () {
                    clearValidationErrors();
                });
            }

            // Cập nhật số liệu thống kê nhanh trên thanh stats chips
            function updateQuickStats() {
                const totalEl = document.getElementById('statTotalCount');
                const learningEl = document.getElementById('statLearningCount');
                const completedEl = document.getElementById('statCompletedCount');
                const allItems = document.querySelectorAll('.mon-hoc-item');
                
                let learning = 0;
                let completed = 0;
                allItems.forEach(item => {
                    if (item.querySelector('.nm-status-learning')) learning++;
                    if (item.querySelector('.nm-status-completed')) completed++;
                });

                if (totalEl) totalEl.textContent = allItems.length;
                if (learningEl) learningEl.textContent = learning;
                if (completedEl) completedEl.textContent = completed;
            }

            // Chèn thẻ môn học mới vào grid theo chuẩn Neumorphic mới
            function appendNewMonHocCard(monHoc) {
                const emptyState = document.getElementById('monHocEmptyState');
                if (emptyState) emptyState.remove();

                const statusMap = {
                    'dang_hoc': { class: 'nm-status-learning', icon: 'bi bi-play-circle-fill', text: 'Đang học' },
                    'da_hoan_thanh': { class: 'nm-status-completed', icon: 'bi bi-check-circle-fill', text: 'Đã hoàn thành' },
                    'tam_dung': { class: 'nm-status-paused', icon: 'bi bi-pause-circle-fill', text: 'Tạm dừng' }
                };
                const statusInfo = statusMap[monHoc.trang_thai] || statusMap['dang_hoc'];

                const searchMeta = `${monHoc.ten_mon} ${monHoc.ma_mon} ${monHoc.giang_vien || ''} ${monHoc.phong_hoc || ''}`.toLowerCase();

                const colDiv = document.createElement('div');
                colDiv.className = 'col-12 col-md-6 col-xl-4 mon-hoc-item card-newly-added';
                colDiv.id = `monHocCard${monHoc.id}`;
                colDiv.setAttribute('data-search', searchMeta);
                colDiv.innerHTML = `
                    <div class="nm-course-card">
                        <div class="nm-card-strip" style="background-color: ${monHoc.mau_sac || '#6366f1'};"></div>

                        <div class="nm-card-body">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="nm-code-badge" title="Mã môn học">
                                        ${monHoc.ma_mon}
                                    </span>
                                    <span class="nm-status-badge ${statusInfo.class}">
                                        <i class="${statusInfo.icon}"></i>
                                        <span>${statusInfo.text}</span>
                                    </span>
                                </div>

                                <h5 class="nm-card-title text-truncate" title="${monHoc.ten_mon}">
                                    <a href="${monHoc.show_url}" class="nm-title-link">
                                        ${monHoc.ten_mon}
                                    </a>
                                </h5>

                                <div class="nm-meta-list">
                                    <div class="nm-meta-item">
                                        <div class="nm-icon-squircle text-primary" title="Giảng viên">
                                            <i class="bi bi-person-badge"></i>
                                        </div>
                                        <div class="nm-meta-content">
                                            <span class="nm-meta-label">Giảng viên:</span>
                                            <strong class="nm-meta-value">${monHoc.giang_vien || 'Chưa cập nhật'}</strong>
                                        </div>
                                    </div>
                                    <div class="nm-meta-item">
                                        <div class="nm-icon-squircle text-danger" title="Phòng học">
                                            <i class="bi bi-geo-alt"></i>
                                        </div>
                                        <div class="nm-meta-content">
                                            <span class="nm-meta-label">Phòng học:</span>
                                            <strong class="nm-meta-value">${monHoc.phong_hoc || 'Chưa cập nhật'}</strong>
                                        </div>
                                    </div>
                                    <div class="nm-meta-item">
                                        <div class="nm-icon-squircle text-info" title="Thời gian học">
                                            <i class="bi bi-calendar3"></i>
                                        </div>
                                        <div class="nm-meta-content">
                                            <span class="nm-meta-label">Thời gian:</span>
                                            <strong class="nm-meta-value">${monHoc.ngay_bat_dau ? (monHoc.ngay_bat_dau + (monHoc.ngay_ket_thuc ? ' - ' + monHoc.ngay_ket_thuc : '')) : 'Linh hoạt'}</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-1 pt-1">
                                        <span class="nm-pill-chip" title="Số tín chỉ">
                                            <i class="bi bi-award-fill text-warning me-1"></i>${monHoc.so_tin_chi || 3} tín chỉ
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="nm-card-divider"></div>
                                <div class="nm-card-actions">
                                    <a href="${monHoc.show_url}" class="nm-btn-detail" title="Xem chi tiết môn học">
                                        <i class="bi bi-eye"></i>
                                        <span>Xem chi tiết</span>
                                    </a>

                                    <div class="d-flex align-items-center gap-2">
                                        <a href="${monHoc.edit_url}" class="nm-btn-icon nm-btn-warning" title="Chỉnh sửa môn học">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <button type="button" class="nm-btn-icon nm-btn-danger"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal${monHoc.id}" title="Xóa môn học">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal xóa kèm theo cho môn học mới -->
                    <div class="modal fade" id="deleteModal${monHoc.id}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content nm-modal-content border-0 p-2">
                                <div class="nm-modal-header border-0 pb-0">
                                    <div class="nm-modal-title-box">
                                        <div class="nm-modal-icon text-danger" style="background: rgba(239, 68, 68, 0.1);">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                        </div>
                                        <div>
                                            <h5 class="modal-title fw-bold text-danger mb-0">Xác nhận xóa</h5>
                                            <small class="text-muted">Hành động này có thể khôi phục từ thùng rác</small>
                                        </div>
                                    </div>
                                    <button type="button" class="nm-modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                <div class="modal-body px-4 py-3">
                                    <p class="mb-2 text-dark-nm">
                                        Bạn có chắc chắn muốn xóa môn học <strong class="text-primary">${monHoc.ten_mon}</strong> (Mã: <code>${monHoc.ma_mon}</code>)?
                                    </p>
                                    <div class="p-3 rounded-4 small" style="background: var(--nm-surface); box-shadow: var(--nm-shadow-inset); border: var(--nm-border-subtle);">
                                        <i class="bi bi-info-circle-fill text-muted me-1"></i>
                                        Môn học sẽ được chuyển vào mục lưu trữ và bạn có thể khôi phục lại khi cần thiết.
                                    </div>
                                </div>
                                <div class="modal-footer border-0 px-4 pb-3 pt-0 d-flex justify-content-end gap-2">
                                    <button type="button" class="nm-btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                                    <form action="${monHoc.destroy_url}" method="POST" class="m-0">
                                        <input type="hidden" name="_token" value="${csrfToken}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="nm-btn-danger">
                                            <i class="bi bi-trash-fill"></i>
                                            <span>Đồng ý Xóa</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                const grid = document.getElementById('monHocGrid');
                if (grid) {
                    grid.prepend(colDiv);
                }

                updateQuickStats();
            }

            // 3. XỬ LÝ GỬI FORM BẰNG FETCH API / AJAX (KHÔNG RELOAD TRANG)
            if (createForm) {
                createForm.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    clearValidationErrors();

                    const nameInput = document.getElementById('modal_ten_mon');
                    const tenMonVal = nameInput ? nameInput.value.trim() : '';

                    // Kiểm tra Frontend validation: Tên môn học
                    if (!tenMonVal) {
                        setFieldError('ten_mon', 'Vui lòng nhập tên môn học.');
                        nameInput?.focus();
                        return;
                    }

                    // Kiểm tra Frontend validation: Ngày kết thúc >= ngày bắt đầu
                    if (startDateInput && endDateInput && startDateInput.value && endDateInput.value) {
                        if (endDateInput.value < startDateInput.value) {
                            setFieldError('ngay_ket_thuc', 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.');
                            endDateInput.focus();
                            return;
                        }
                    }

                    const formData = new FormData(createForm);

                    // Trạng thái Loading
                    btnSubmit.disabled = true;
                    if (btnSubmitText) btnSubmitText.textContent = 'Đang lưu...';

                    try {
                        const response = await fetch("{{ route('mon-hoc.store') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const resData = await response.json();

                        if (response.ok && resData.success) {
                            // 1. Đóng Modal
                            if (createModal) createModal.hide();

                            // 2. Reset form
                            createForm.reset();
                            if (colorInput) colorInput.value = '#6366f1';
                            presetButtons.forEach((b, idx) => {
                                if (idx === 0) b.classList.add('active');
                                else b.classList.remove('active');
                            });

                            // 3. Cập nhật ngay danh sách môn học trên giao diện (Không cần F5)
                            if (resData.data) {
                                appendNewMonHocCard(resData.data);
                            }

                            // 4. Hiển thị Toast thông báo thành công
                            showToast(resData.message || 'Môn học đã được thêm thành công!');
                        } else if (response.status === 422) {
                            // Lỗi validation từ Laravel backend
                            const errors = resData.errors || {};
                            let firstErrorInput = null;

                            for (const [field, messages] of Object.entries(errors)) {
                                const msg = messages[0] || 'Thông tin không hợp lệ.';
                                setFieldError(field, msg);
                                if (!firstErrorInput) {
                                    firstErrorInput = document.getElementById(`modal_${field}`);
                                }
                            }

                            if (alertError && alertErrorText) {
                                alertErrorText.textContent = resData.message || 'Vui lòng kiểm tra lại thông tin các trường báo đỏ.';
                                alertError.classList.remove('d-none');
                            }

                            if (firstErrorInput) firstErrorInput.focus();
                        } else {
                            if (alertError && alertErrorText) {
                                alertErrorText.textContent = resData.message || 'Đã có lỗi xảy ra từ máy chủ, vui lòng thử lại.';
                                alertError.classList.remove('d-none');
                            }
                        }
                    } catch (err) {
                        console.error('Fetch error:', err);
                        if (alertError && alertErrorText) {
                            alertErrorText.textContent = 'Lỗi kết nối máy chủ, vui lòng kiểm tra mạng và thử lại.';
                            alertError.classList.remove('d-none');
                        }
                    } finally {
                        btnSubmit.disabled = false;
                        if (btnSubmitText) btnSubmitText.textContent = 'Thêm môn học';
                    }
                });
            }
        });
    </script>
</body>

</html>