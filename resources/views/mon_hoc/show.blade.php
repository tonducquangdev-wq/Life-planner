<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    <style>
        /* ==========================================================================
           SUBJECT DETAIL DESIGN SYSTEM - LIFE PLANNER
           ========================================================================== */
        :root {
            --sd-bg: #F8FAFC;
            --sd-card-bg: #FFFFFF;
            --sd-card-border: #E2E8F0;
            --sd-text-main: #0F172A;
            --sd-text-muted: #64748B;
            --sd-surface: #F8FAFC;
            --sd-surface-border: #E2E8F0;
            --sd-surface-hover: #F1F5F9;
            --sd-primary: #4F46E5;
            --sd-primary-hover: #4338CA;
            --sd-primary-subtle: rgba(79, 70, 229, 0.08);
            --sd-danger: #EF4444;
            --sd-danger-hover: #DC2626;
            --sd-danger-subtle: rgba(239, 68, 68, 0.08);
            --sd-radius-card: 16px;
            --sd-radius-btn: 12px;
            --sd-radius-tile: 12px;
            --sd-shadow-sm: 0 2px 8px rgba(15, 23, 42, 0.04);
            --sd-shadow-card: 0 4px 16px -2px rgba(15, 23, 42, 0.05);
        }

        [data-bs-theme="dark"] {
            --sd-bg: #121212;
            --sd-card-bg: #1E1E22;
            --sd-card-border: #2E2E34;
            --sd-text-main: #E8E8EA;
            --sd-text-muted: #A0A0A8;
            --sd-surface: #26262B;
            --sd-surface-border: #33333A;
            --sd-surface-hover: #2E2E35;
            --sd-primary: #6C8CFF;
            --sd-primary-hover: #8AA3FF;
            --sd-primary-subtle: rgba(108, 140, 255, 0.15);
            --sd-danger: #F87171;
            --sd-danger-hover: #FCA5A5;
            --sd-danger-subtle: rgba(248, 113, 113, 0.15);
            --sd-shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.25);
            --sd-shadow-card: 0 4px 20px rgba(0, 0, 0, 0.35);
        }

        body {
            background-color: var(--sd-bg) !important;
            color: var(--sd-text-main) !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .subject-detail-wrapper {
            background-color: var(--sd-bg);
            min-height: calc(100vh - 58px);
        }

        /* 1. Header Navigation Bar */
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
            height: 40px;
            padding: 0 16px;
            border-radius: var(--sd-radius-btn);
            background: var(--sd-card-bg);
            border: 1px solid var(--sd-card-border);
            color: var(--sd-text-main);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--sd-shadow-sm);
        }

        .subject-btn-back:hover {
            background: var(--sd-surface);
            border-color: var(--sd-primary);
            color: var(--sd-primary);
            transform: translateX(-2px);
        }

        .subject-action-group {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .subject-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            height: 40px;
            padding: 0 16px;
            border-radius: var(--sd-radius-btn);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
        }

        .subject-action-btn.btn-edit {
            background-color: var(--sd-primary);
            color: #0B1020;
            border-color: var(--sd-primary);
        }

        [data-bs-theme="light"] .subject-action-btn.btn-edit {
            background-color: var(--sd-primary);
            color: #FFFFFF;
        }

        .subject-action-btn.btn-edit:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .subject-action-btn.btn-delete {
            background-color: var(--sd-danger-subtle);
            color: var(--sd-danger);
            border-color: rgba(239, 68, 68, 0.25);
        }

        .subject-action-btn.btn-delete:hover {
            background-color: var(--sd-danger);
            color: #FFFFFF;
            border-color: var(--sd-danger);
            transform: translateY(-1px);
        }

        .subject-action-btn.btn-calendar {
            background-color: var(--sd-card-bg);
            color: var(--sd-text-main);
            border-color: var(--sd-card-border);
        }

        .subject-action-btn.btn-calendar:hover {
            background-color: var(--sd-surface);
            border-color: var(--sd-primary);
            color: var(--sd-primary);
            transform: translateY(-1px);
        }

        /* 2. Hero Subject Card */
        .subject-hero-card {
            background-color: var(--sd-card-bg);
            border: 1px solid var(--sd-card-border);
            border-radius: var(--sd-radius-card);
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--sd-shadow-card);
            position: relative;
            overflow: hidden;
        }

        .subject-hero-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 5px;
            background-color: {{ $monHoc->mau_sac ?? '#6C8CFF' }};
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
            gap: 18px;
            min-width: 0;
        }

        .subject-hero-icon {
            width: 58px;
            height: 58px;
            border-radius: 14px;
            background-color: {{ $monHoc->mau_sac ?? '#6C8CFF' }};
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            font-size: 26px;
            flex-shrink: 0;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
        }

        .subject-hero-meta {
            min-width: 0;
        }

        .subject-hero-tags {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 6px;
        }

        .subject-code-tag {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 6px;
            background-color: var(--sd-surface);
            border: 1px solid var(--sd-surface-border);
            color: var(--sd-text-main);
            letter-spacing: 0.04em;
        }

        .subject-status-pill {
            font-size: 12px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-dang_hoc {
            background-color: rgba(74, 222, 128, 0.15);
            color: #4ADE80;
            border: 1px solid rgba(74, 222, 128, 0.3);
        }

        .status-da_hoan_thanh {
            background-color: rgba(108, 140, 255, 0.15);
            color: #6C8CFF;
            border: 1px solid rgba(108, 140, 255, 0.3);
        }

        .status-tam_dung {
            background-color: rgba(251, 191, 36, 0.15);
            color: #FBBF24;
            border: 1px solid rgba(251, 191, 36, 0.3);
        }

        .subject-hero-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--sd-text-main);
            margin: 0;
            line-height: 1.3;
            letter-spacing: -0.015em;
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
            padding: 6px 12px;
            background-color: var(--sd-surface);
            border: 1px solid var(--sd-surface-border);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--sd-text-main);
        }

        .subject-color-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background-color: {{ $monHoc->mau_sac ?? '#6C8CFF' }};
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.15);
        }

        /* 3. Section Cards */
        .subject-card {
            background-color: var(--sd-card-bg);
            border: 1px solid var(--sd-card-border);
            border-radius: var(--sd-radius-card);
            padding: 24px;
            box-shadow: var(--sd-shadow-card);
            margin-bottom: 24px;
        }

        .subject-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 16px;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--sd-card-border);
        }

        .subject-card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--sd-text-main);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 9px;
            letter-spacing: -0.01em;
        }

        .subject-card-title i {
            color: var(--sd-primary);
            font-size: 18px;
        }

        .subject-card-subtitle {
            font-size: 13px;
            color: var(--sd-text-muted);
            margin: 2px 0 0 0;
        }

        /* 4. Info Tiles (2x2 Grid) */
        .subject-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .subject-info-tile {
            background-color: var(--sd-surface);
            border: 1px solid var(--sd-surface-border);
            border-radius: var(--sd-radius-tile);
            padding: 16px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            transition: border-color 0.2s ease, background-color 0.2s ease;
        }

        .subject-info-tile:hover {
            background-color: var(--sd-surface-hover);
            border-color: rgba(108, 140, 255, 0.3);
        }

        .subject-tile-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background-color: var(--sd-primary-subtle);
            color: var(--sd-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .subject-tile-icon.icon-geo {
            background-color: rgba(248, 113, 113, 0.12);
            color: #F87171;
        }

        .subject-tile-icon.icon-status {
            background-color: rgba(74, 222, 128, 0.12);
            color: #4ADE80;
        }

        .subject-tile-icon.icon-term {
            background-color: rgba(251, 191, 36, 0.12);
            color: #FBBF24;
        }

        .subject-tile-content {
            min-width: 0;
            flex-grow: 1;
        }

        .subject-tile-label {
            font-size: 12px;
            font-weight: 500;
            color: var(--sd-text-muted);
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .subject-tile-value {
            font-size: 15px;
            font-weight: 600;
            color: var(--sd-text-main);
            margin: 0;
            line-height: 1.35;
            word-break: break-word;
        }

        .subject-tile-sub {
            font-size: 12px;
            color: var(--sd-text-muted);
            margin-top: 3px;
        }

        /* 5. Date Cards (Timeline) */
        .subject-date-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .subject-date-card {
            background-color: var(--sd-surface);
            border: 1px solid var(--sd-surface-border);
            border-radius: var(--sd-radius-tile);
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .subject-date-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background-color: var(--sd-primary-subtle);
            color: var(--sd-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .subject-date-icon.icon-end {
            background-color: rgba(74, 222, 128, 0.12);
            color: #4ADE80;
        }

        .subject-date-meta {
            min-width: 0;
        }

        .subject-date-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--sd-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 2px;
        }

        .subject-date-val {
            font-size: 16px;
            font-weight: 700;
            color: var(--sd-text-main);
            margin: 0;
        }

        .subject-date-desc {
            font-size: 12px;
            color: var(--sd-text-muted);
            margin-top: 2px;
        }

        /* 6. Right Column Widgets */
        .subject-color-preview-box {
            background-color: var(--sd-surface);
            border: 1px solid var(--sd-surface-border);
            border-radius: var(--sd-radius-tile);
            padding: 16px;
            text-align: center;
            margin-bottom: 16px;
        }

        .subject-color-large-bubble {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            margin: 0 auto 10px;
            background-color: {{ $monHoc->mau_sac ?? '#6C8CFF' }};
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border: 3px solid var(--sd-card-bg);
        }

        .subject-color-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 14px;
            font-weight: 700;
            color: var(--sd-text-main);
            letter-spacing: 0.05em;
        }

        .subject-calendar-nav-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            height: 42px;
            border-radius: var(--sd-radius-btn);
            background-color: var(--sd-primary);
            color: #0B1020;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-bs-theme="light"] .subject-calendar-nav-btn {
            background-color: var(--sd-primary);
            color: #FFFFFF;
        }

        .subject-calendar-nav-btn:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        /* Empty state for weekly schedule */
        .subject-schedule-empty {
            text-align: center;
            padding: 24px 16px;
            background-color: var(--sd-surface);
            border: 1px dashed var(--sd-surface-border);
            border-radius: var(--sd-radius-tile);
        }

        .subject-schedule-empty i {
            font-size: 32px;
            color: var(--sd-text-muted);
            margin-bottom: 8px;
            display: inline-block;
        }

        .subject-schedule-empty-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--sd-text-main);
            margin-bottom: 4px;
        }

        .subject-schedule-empty-desc {
            font-size: 12px;
            color: var(--sd-text-muted);
            margin: 0;
            line-height: 1.45;
        }

        /* 7. Modal Dark Mode Polish */
        .subject-modal .modal-content {
            background-color: var(--sd-surface) !important;
            border: 1px solid var(--sd-surface-border) !important;
            border-radius: 20px !important;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.4) !important;
            color: var(--sd-text-main) !important;
        }

        .subject-modal .modal-header,
        .subject-modal .modal-footer {
            border-color: var(--sd-surface-border) !important;
        }

        .subject-modal .modal-icon-warning {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: var(--sd-danger-subtle);
            color: var(--sd-danger);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin: 0 auto 12px;
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

        /* Tablet (576px - 991px): Keep header navigation on one neat horizontal line */
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
                gap: 8px;
            }

            .subject-hero-card {
                padding: 20px 18px;
            }

            .subject-card {
                padding: 20px 18px;
            }
        }

        /* Mobile (< 576px): Stack header and equalize buttons */
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
                padding: 18px 16px;
            }

            .subject-hero-title {
                font-size: 20px;
            }

            .subject-hero-icon {
                width: 48px;
                height: 48px;
                font-size: 22px;
            }

            .subject-hero-aside {
                width: 100%;
                justify-content: flex-start;
                margin-top: 4px;
            }

            .subject-card {
                padding: 18px 16px;
            }
        }

        @media (max-width: 390px) {
            .subject-hero-main {
                gap: 12px;
            }

            .subject-hero-title {
                font-size: 18px;
            }

            .subject-action-btn {
                padding: 0 10px;
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
                                <div class="d-flex flex-column gap-2.5">
                                    @foreach($monHoc->lichHocs as $lich)
                                        <div class="p-3 rounded-3" style="background: var(--sd-surface); border: 1px solid var(--sd-surface-border);">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span class="fw-bold fs-7 text-primary">Thứ {{ $lich->ngay_trong_tuan }}</span>
                                                <span class="badge bg-secondary-subtle text-secondary small">
                                                    {{ substr($lich->gio_bat_dau, 0, 5) }} - {{ substr($lich->gio_ket_thuc, 0, 5) }}
                                                </span>
                                            </div>
                                            @if($lich->ghi_chu)
                                                <p class="text-muted small mb-0">{{ $lich->ghi_chu }}</p>
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
                    <button type="button" class="subject-action-btn" style="background: var(--sd-surface); color: var(--sd-text-main); border: 1px solid var(--sd-surface-border);" data-bs-dismiss="modal">
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

        // Quick Theme Toggle Hook
        const quickThemeBtn = document.getElementById('quickThemeBtn');
        const quickThemeIcon = document.getElementById('quickThemeIcon');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

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
    </script>
    <!-- Floating Action Menu (Góc phải bên dưới) -->
    <x-floating-menu />
</body>
</html>
