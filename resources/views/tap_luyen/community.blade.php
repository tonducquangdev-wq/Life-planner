<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cộng Đồng Gymer - Life Planner</title>

    <!-- Anti-flicker Theme Script -->
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
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Font: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom Dashboard & Workout CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tap-luyen.css') }}">

    <style>
        :root {
            --comm-bg: #F8FAFC;
            --comm-card-bg: #FFFFFF;
            --comm-card-border: #E2E8F0;
            --comm-text-main: #0F172A;
            --comm-text-muted: #64748B;
            --comm-primary: #3B82F6;
            --comm-primary-subtle: rgba(59, 130, 246, 0.1);
            --comm-accent: #6C8CFF;
            --comm-surface: #F1F5F9;
        }

        [data-bs-theme="dark"] {
            --comm-bg: #121212;
            --comm-card-bg: #1E1E22;
            --comm-card-border: #2E2E34;
            --comm-text-main: #E8E8EA;
            --comm-text-muted: #A0A0A8;
            --comm-primary: #6C8CFF;
            --comm-primary-subtle: rgba(108, 140, 255, 0.15);
            --comm-accent: #6C8CFF;
            --comm-surface: #26262B;
        }

        body {
            background-color: var(--comm-bg);
            color: var(--comm-text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        /* Hero Sub Header */
        .community-hero-badge {
            background: var(--comm-primary-subtle);
            color: var(--comm-primary);
            font-weight: 600;
            font-size: 0.78rem;
            padding: 4px 12px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .community-header-title {
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--comm-text-main);
        }

        .community-header-subtitle {
            color: var(--comm-text-muted);
            font-size: 0.88rem;
        }

        /* Action Cards: Search & Share */
        .comm-action-card {
            background-color: var(--comm-card-bg);
            border: 1px solid var(--comm-card-border);
            border-radius: 18px;
            padding: 1.25rem 1.5rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 20px -6px rgba(0, 0, 0, 0.05);
        }

        .comm-action-card:hover {
            border-color: rgba(108, 140, 255, 0.4);
            box-shadow: 0 8px 24px -6px rgba(0, 0, 0, 0.1);
        }

        .comm-icon-box {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: var(--comm-primary-subtle);
            color: var(--comm-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            flex-shrink: 0;
        }

        /* Plan Card Grid */
        .comm-plan-card {
            background-color: var(--comm-card-bg);
            border: 1px solid var(--comm-card-border);
            border-radius: 18px;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.22s ease, border-color 0.22s ease;
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.06);
            position: relative;
        }

        .comm-plan-card:hover {
            transform: translateY(-4px);
            border-color: rgba(108, 140, 255, 0.45);
            box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.15);
        }

        .comm-plan-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--comm-text-main);
            line-height: 1.35;
        }

        .comm-plan-author {
            color: var(--comm-text-muted);
            font-size: 0.8rem;
        }

        .comm-plan-desc {
            color: var(--comm-text-muted);
            font-size: 0.82rem;
            line-height: 1.45;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.4em;
        }

        .comm-code-badge {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            background: var(--comm-primary-subtle);
            color: var(--comm-primary);
            border: 1px solid rgba(108, 140, 255, 0.25);
            font-size: 0.78rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            transition: background 0.15s ease, transform 0.15s ease;
        }

        .comm-code-badge:hover {
            transform: scale(1.04);
            background: rgba(108, 140, 255, 0.25);
        }

        .comm-sessions-container {
            background-color: var(--comm-surface);
            border-radius: 12px;
            padding: 10px 12px;
            margin-bottom: 1rem;
        }

        .comm-session-chip {
            background-color: var(--comm-card-bg);
            color: var(--comm-text-main);
            border: 1px solid var(--comm-card-border);
            font-size: 0.75rem;
            font-weight: 500;
            padding: 3px 8px;
            border-radius: 8px;
            white-space: nowrap;
        }

        /* Buttons & Actions */
        .btn-comm-primary {
            background-color: var(--comm-primary);
            color: #FFFFFF;
            border: none;
            font-weight: 600;
            border-radius: 9999px;
            transition: opacity 0.18s, transform 0.18s;
        }

        .btn-comm-primary:hover {
            color: #FFFFFF;
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .btn-comm-outline {
            background-color: transparent;
            color: var(--comm-text-main);
            border: 1px solid var(--comm-card-border);
            font-weight: 600;
            border-radius: 9999px;
            transition: all 0.18s ease;
        }

        .btn-comm-outline:hover {
            background-color: var(--comm-surface);
            color: var(--comm-primary);
            border-color: var(--comm-primary);
        }

        /* Toast Container */
        .comm-toast-container {
            position: fixed;
            top: 76px;
            right: 24px;
            z-index: 1099;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .comm-toast {
            background: var(--comm-card-bg);
            color: var(--comm-text-main);
            border: 1px solid var(--comm-card-border);
            box-shadow: 0 12px 32px -4px rgba(0, 0, 0, 0.35);
            border-radius: 14px;
            padding: 12px 18px;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            pointer-events: auto;
            animation: slideInToast 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            max-width: 380px;
        }

        @keyframes slideInToast {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Modal Overrides for Dark Mode */
        [data-bs-theme="dark"] .modal-content {
            background-color: #26262B;
            border: 1px solid #2E2E34;
            color: #E8E8EA;
        }

        [data-bs-theme="dark"] .modal-header {
            border-bottom-color: #2E2E34;
        }

        [data-bs-theme="dark"] .modal-footer {
            border-top-color: #2E2E34;
        }

        [data-bs-theme="dark"] .list-group-item {
            background-color: #1E1E22;
            border-color: #2E2E34;
            color: #E8E8EA;
        }

        [data-bs-theme="dark"] .table {
            --bs-table-bg: transparent;
            --bs-table-color: #E8E8EA;
            --bs-table-border-color: #2E2E34;
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .community-header-title {
                font-size: 1.35rem;
            }
            .comm-action-card {
                padding: 1.1rem;
            }
            .comm-toast-container {
                right: 14px;
                top: 68px;
                left: 14px;
            }
            .comm-toast {
                max-width: 100%;
            }
        }
    </style>
</head>

<body>

    <!-- MAIN WRAPPER FULL-WIDTH -->
    <div class="main-wrapper main-wrapper-full">

        <!-- TOPBAR TRÊN CÙNG COMPACT 58PX (Chứa nút Quay lại lịch & Đổi giao diện) -->
        @include('layouts.topbar', ['title' => 'Cộng đồng Gymer'])

        <!-- CONTENT BODY -->
        <main class="content-body py-4 px-3 px-md-4">
            <div class="container-xl p-0">

                <!-- THÔNG BÁO FLASH STATUS NẾU CÓ -->
                @if(session('status'))
                    <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-check fs-5 text-success"></i>
                        <span class="fw-medium">{{ session('status') }}</span>
                    </div>
                @endif

                <!-- =========================================================================
                     HEADER TRANG CỘNG ĐỒNG: TÊN TRANG NỔI BẬT & THANH ĐIỀU HƯỚNG
                     ========================================================================= -->
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4 pb-1">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1.5">
                            <span class="community-hero-badge">
                                <i class="fa-solid fa-users"></i> Cộng đồng Giáo án Gymer
                            </span>
                        </div>
                        <h3 class="community-header-title mb-1">Cộng đồng Gymer</h3>
                        <p class="community-header-subtitle mb-0">Khám phá và chia sẻ lịch tập cùng cộng đồng.</p>
                    </div>

                    <!-- SUB NAV TABS + CTA ACTIONS -->
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="d-inline-flex p-1 rounded-pill border" style="background: var(--comm-card-bg);">
                            <a href="{{ route('tap-luyen.index') }}" class="btn btn-sm px-3 py-1.5 rounded-pill text-secondary fw-semibold">
                                <i class="fa-solid fa-heart-pulse me-1 text-danger"></i> Theo dõi Thể chất
                            </a>
                            <a href="{{ route('bao-cao-tap-luyen.index') }}" class="btn btn-sm px-3 py-1.5 rounded-pill text-secondary fw-semibold">
                                <i class="fa-solid fa-chart-column me-1 text-primary"></i> Báo cáo
                            </a>
                            <a href="{{ route('workout.community') }}" class="btn btn-sm px-3 py-1.5 rounded-pill btn-primary text-white fw-semibold shadow-xs">
                                <i class="fa-solid fa-users me-1"></i> Cộng đồng
                            </a>
                        </div>

                        <button type="button" class="btn btn-comm-primary btn-sm px-3.5 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-xs"
                                data-bs-toggle="modal" data-bs-target="#shareMyPlanModal" id="btnOpenShareModal">
                            <i class="fa-solid fa-share-nodes"></i>
                            <span>Chia sẻ kế hoạch</span>
                        </button>
                    </div>
                </div>

                <!-- =========================================================================
                     ROW 2 CỘT: 1) NHẬP MÃ LỊCH TẬP  |  2) CHIA SẺ KẾ HOẠCH CỦA TÔI
                     ========================================================================= -->
                <div class="row g-3 mb-4">
                    
                    <!-- CỘT 1: NHẬP MÃ LỊCH TẬP (SEARCH / IMPORT CODE) -->
                    <div class="col-12 col-lg-6">
                        <div class="comm-action-card h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center gap-2.5 mb-2">
                                    <div class="comm-icon-box">
                                        <i class="fa-solid fa-link"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark-emphasis fs-6">Nhập mã lịch tập</h6>
                                        <small class="text-secondary fs-8">Nhập code được bạn bè chia sẻ để tìm và sao chép kế hoạch về tài khoản.</small>
                                    </div>
                                </div>
                            </div>

                            <form method="GET" action="{{ route('workout.community') }}" class="mt-3" id="codeSearchForm">
                                <div class="d-flex flex-column flex-sm-row gap-2">
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0 bg-transparent text-secondary">
                                            <i class="fa-solid fa-hashtag"></i>
                                        </span>
                                        <input type="text" name="code" value="{{ $searchCode ?? '' }}" 
                                               id="searchCodeInput"
                                               class="form-control rounded-end-pill border-start-0 font-monospace text-uppercase" 
                                               style="background: var(--comm-surface); color: var(--comm-text-main); font-weight: 600; letter-spacing: 1px;"
                                               placeholder="VD: PPL-8CTX5" required autocomplete="off">
                                    </div>
                                    <button type="submit" class="btn btn-comm-primary px-4 py-2 flex-shrink-0 d-inline-flex align-items-center justify-content-center gap-1.5">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                        <span>Tìm</span>
                                    </button>
                                </div>

                                @if(!empty($searchCode))
                                    <div class="mt-2.5 d-flex align-items-center justify-content-between fs-8">
                                        <span class="text-secondary">
                                            Đang tìm mã: <strong class="text-primary font-monospace">{{ $searchCode }}</strong>
                                        </span>
                                        <a href="{{ route('workout.community') }}" class="text-danger fw-semibold text-decoration-none">
                                            <i class="fa-solid fa-xmark me-1"></i>Xóa bộ lọc
                                        </a>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>

                    <!-- CỘT 2: CHIA SẺ KẾ HOẠCH CỦA TÔI (SHARE MY PLAN) -->
                    <div class="col-12 col-lg-6">
                        <div class="comm-action-card h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center gap-2.5 mb-2">
                                    <div class="comm-icon-box text-success" style="background: rgba(34, 197, 94, 0.12); color: #22C55E;">
                                        <i class="fa-solid fa-share-nodes"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark-emphasis fs-6">Chia sẻ kế hoạch của tôi</h6>
                                        <small class="text-secondary fs-8">Chọn lịch tập cá nhân để tạo mã code và chia sẻ cho bạn bè.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2 pt-1" id="quickShareContainer">
                                @if(isset($myPlans) && $myPlans->count() > 0)
                                    <div class="row g-2 align-items-center">
                                        <div class="col-12 col-sm-7">
                                            <select class="form-select form-select-sm rounded-pill fw-semibold" id="quickPlanSelect" style="background: var(--comm-surface); color: var(--comm-text-main);">
                                                @foreach($myPlans as $mp)
                                                    <option value="{{ $mp->id }}" 
                                                            data-is-shared="{{ $mp->is_shared ? '1' : '0' }}" 
                                                            data-share-code="{{ $mp->share_code ?? '' }}"
                                                            data-name="{{ $mp->ten_ke_hoach }}"
                                                            data-sessions="{{ $mp->buoiTaps->count() }}"
                                                            {{ $loop->first ? 'selected' : '' }}>
                                                        {{ $mp->ten_ke_hoach }} ({{ $mp->buoiTaps->count() }} buổi)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-sm-5 d-flex gap-1.5">
                                            <button type="button" class="btn btn-comm-primary btn-sm w-100 py-1.5 d-inline-flex align-items-center justify-content-center gap-1 text-truncate" id="btnQuickShareAction">
                                                <i class="fa-solid fa-copy" id="quickShareBtnIcon"></i>
                                                <span id="quickShareBtnText">Lấy mã</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Khu vực hiển thị mã code nhanh sau khi chọn/tạo -->
                                    <div class="mt-2.5 p-2 px-3 rounded-3 d-flex align-items-center justify-content-between" id="quickCodeDisplayBox" style="background: var(--comm-surface); font-size: 0.8rem;">
                                        <span class="text-secondary">Mã chia sẻ:</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="font-monospace fw-bold text-primary fs-7" id="quickDisplayCodeText">
                                                {{ $myPlans->first()->share_code ?: 'Chưa tạo mã' }}
                                            </span>
                                            <button type="button" class="btn btn-sm btn-link p-0 text-primary text-decoration-none" id="btnCopyQuickCode" title="Sao chép mã">
                                                <i class="fa-solid fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="p-2.5 rounded-3 text-center fs-8 text-secondary" style="background: var(--comm-surface);">
                                        Bạn chưa có kế hoạch rèn luyện nào. 
                                        <a href="{{ route('tap-luyen.index') }}" class="text-primary fw-semibold text-decoration-none ms-1">
                                            Tạo kế hoạch ngay &rarr;
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                <!-- =========================================================================
                     DANH SÁCH KẾ HOẠCH CỘNG ĐỒNG CHIA SẺ
                     ========================================================================= -->
                <div class="d-flex align-items-center justify-content-between mb-3.5 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2.5">
                        <h5 class="fw-bold mb-0 text-dark-emphasis fs-6 d-flex align-items-center gap-2">
                            <span>Lịch tập cộng đồng chia sẻ</span>
                        </h5>
                        <span class="badge rounded-pill fw-semibold" style="background: var(--comm-primary-subtle); color: var(--comm-primary);">
                            {{ $sharedPlans->count() }} kế hoạch
                        </span>
                    </div>

                    @if(!empty($searchCode))
                        <a href="{{ route('workout.community') }}" class="btn btn-sm btn-comm-outline px-3 py-1 fs-8">
                            <i class="fa-solid fa-arrows-rotate me-1"></i> Xem tất cả kế hoạch
                        </a>
                    @endif
                </div>

                <!-- GRID KẾ HOẠCH CỘNG ĐỒNG (3 CARD / HÀNG DESKTOP, 2 CARD TABLET, 1 CARD MOBILE) -->
                <div class="row g-4" id="communityPlansGrid">
                    @forelse($sharedPlans as $plan)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="comm-plan-card">
                                
                                <!-- Card Header: Tên kế hoạch & Mã chia sẻ -->
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                    <div class="min-w-0">
                                        <h5 class="comm-plan-title mb-1 text-truncate" title="{{ $plan->ten_ke_hoach }}">
                                            {{ $plan->ten_ke_hoach }}
                                        </h5>
                                        <div class="comm-plan-author d-flex align-items-center gap-1.5">
                                            <i class="fa-solid fa-user-circle text-secondary fs-8"></i>
                                            <span>Bởi: <strong class="text-dark-emphasis">{{ $plan->user ? $plan->user->ho_ten : 'Gymer' }}</strong></span>
                                        </div>
                                    </div>

                                    <!-- Mã Code Chia Sẻ -->
                                    <span class="comm-code-badge flex-shrink-0 btn-copy-code" 
                                          data-code="{{ $plan->share_code }}" 
                                          title="Bấm để sao chép mã">
                                        <i class="fa-solid fa-link fs-9"></i>
                                        <span>{{ $plan->share_code }}</span>
                                    </span>
                                </div>

                                <!-- Mô tả kế hoạch -->
                                <p class="comm-plan-desc mb-3" title="{{ $plan->mo_ta }}">
                                    {{ $plan->mo_ta ?: 'Giáo án rèn luyện tối ưu được cộng đồng Gymer khuyên dùng.' }}
                                </p>

                                <!-- Tóm tắt các buổi tập trong tuần -->
                                <div class="comm-sessions-container">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fs-8 fw-bold text-secondary text-uppercase letter-spacing-1">
                                            <i class="fa-regular fa-calendar-days me-1 text-primary"></i> 
                                            {{ $plan->buoiTaps->count() }} buổi / tuần
                                        </span>
                                    </div>
                                    <div class="d-flex flex-wrap gap-1.5">
                                        @forelse($plan->buoiTaps->take(4) as $bt)
                                            <span class="comm-session-chip">
                                                <strong class="text-primary">{{ $bt->ten_thu ? "{$bt->ten_thu}:" : '' }}</strong> {{ $bt->ten_buoi_tap }}
                                            </span>
                                        @empty
                                            <span class="text-secondary fs-8 fst-italic">Chưa phân chia buổi</span>
                                        @endforelse
                                        @if($plan->buoiTaps->count() > 4)
                                            <span class="comm-session-chip text-secondary">+{{ $plan->buoiTaps->count() - 4 }} buổi khác</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Action Buttons: Xem chi tiết & Sao chép plan -->
                                <div class="mt-auto pt-2 border-top d-flex align-items-center gap-2" style="border-color: var(--comm-card-border) !important;">
                                    <!-- Xem chi tiết mở Modal -->
                                    <button type="button" 
                                            class="btn btn-sm btn-comm-outline w-50 py-1.5 d-inline-flex align-items-center justify-content-center gap-1.5 btn-view-plan-detail"
                                            data-plan-id="{{ $plan->id }}">
                                        <i class="fa-regular fa-eye"></i>
                                        <span>Xem chi tiết</span>
                                    </button>

                                    <!-- Sao chép plan về tài khoản (AJAX Toast) -->
                                    <button type="button" 
                                            class="btn btn-sm btn-comm-primary w-50 py-1.5 d-inline-flex align-items-center justify-content-center gap-1.5 btn-copy-plan-action"
                                            data-code="{{ $plan->share_code }}"
                                            data-name="{{ $plan->ten_ke_hoach }}">
                                        <i class="fa-solid fa-clone"></i>
                                        <span>Sao chép plan</span>
                                    </button>
                                </div>

                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="comm-action-card p-5 text-center my-4">
                                <div class="text-secondary mb-3 fs-1">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </div>
                                <h5 class="fw-bold text-dark-emphasis mb-2">Chưa tìm thấy kế hoạch chia sẻ nào phù hợp</h5>
                                <p class="text-secondary fs-7 mb-4 mx-auto" style="max-width: 480px;">
                                    Không tìm thấy lịch tập nào khớp với mã bạn vừa nhập. Hãy kiểm tra lại mã hoặc bấm "Xem tất cả kế hoạch" để khám phá thư viện giáo án.
                                </p>
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <a href="{{ route('workout.community') }}" class="btn btn-comm-primary px-4 py-2 fs-8">
                                        <i class="fa-solid fa-globe me-1"></i> Xem tất cả kế hoạch
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

            </div>
        </main>
    </div>

    <!-- =========================================================================
         MODAL 1: CHI TIẾT KẾ HOẠCH RÈN LUYỆN (VIEW DETAIL MODAL)
         ========================================================================= -->
    <div class="modal fade" id="planDetailModal" tabindex="-1" aria-labelledby="planDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 py-3 px-4" style="background: var(--comm-surface);">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="comm-icon-box" style="width: 32px; height: 32px; font-size: 0.95rem;">
                            <i class="fa-solid fa-dumbbell"></i>
                        </div>
                        <h6 class="modal-title fw-bold text-dark-emphasis fs-6" id="planDetailModalLabel">
                            Chi tiết Kế hoạch Rèn luyện
                        </h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Header Info Box -->
                    <div class="p-3 rounded-4 mb-4" style="background: var(--comm-surface);">
                        <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2 pb-2 mb-2 border-bottom" style="border-color: var(--comm-card-border) !important;">
                            <div>
                                <h5 class="fw-bold text-dark-emphasis mb-0" id="detailPlanTitle">Kế hoạch rèn luyện</h5>
                                <div class="text-secondary fs-8 mt-1">
                                    Người tạo: <strong class="text-dark-emphasis" id="detailPlanAuthor">Gymer</strong> 
                                    &bull; <span id="detailPlanSessionsCount">0</span> buổi/tuần
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge font-monospace fs-8 px-2.5 py-1.5 fw-bold" style="background: var(--comm-primary-subtle); color: var(--comm-primary);" id="detailPlanCode">
                                    PPL-CODE
                                </span>
                                <button type="button" class="btn btn-sm btn-comm-outline px-2.5 py-1 fs-8" id="btnDetailCopyCode" title="Sao chép mã">
                                    <i class="fa-solid fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <div class="text-secondary fs-8 mb-0" id="detailPlanDesc">
                            Mô tả kế hoạch...
                        </div>
                    </div>

                    <!-- Danh sách các buổi tập & Bài tập -->
                    <h6 class="fw-bold text-dark-emphasis fs-7 mb-2.5 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-list-check text-primary"></i> Lịch trình các buổi tập & Danh sách bài:
                    </h6>

                    <div id="detailSessionsListContainer" class="d-flex flex-column gap-3" style="max-height: 380px; overflow-y: auto;">
                        <!-- Sẽ được fill bằng JavaScript -->
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 py-3" style="background: var(--comm-surface);">
                    <button type="button" class="btn btn-comm-outline btn-sm px-3.5" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-comm-primary btn-sm px-4 d-inline-flex align-items-center gap-1.5" id="btnDetailCopyPlanAction">
                        <i class="fa-solid fa-clone"></i>
                        <span>Sao chép kế hoạch về tài khoản</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         MODAL 2: CHIA SẺ KẾ HOẠCH CỦA TÔI (MANAGE MY SHARED PLANS)
         ========================================================================= -->
    <div class="modal fade" id="shareMyPlanModal" tabindex="-1" aria-labelledby="shareMyPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 py-3 px-4" style="background: var(--comm-surface);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="comm-icon-box text-success" style="width: 32px; height: 32px; font-size: 0.95rem; background: rgba(34, 197, 94, 0.12); color: #22C55E;">
                            <i class="fa-solid fa-share-nodes"></i>
                        </div>
                        <h6 class="modal-title fw-bold text-dark-emphasis fs-6" id="shareMyPlanModalLabel">
                            Chia sẻ Kế hoạch Tập luyện
                        </h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <p class="text-secondary fs-8 mb-3">Chọn kế hoạch của bạn để lấy Mã code chia sẻ cho bạn bè hoặc cộng đồng Gymer:</p>

                    <div class="list-group rounded-3 mb-3">
                        @forelse($myPlans as $mp)
                            <div class="list-group-item p-3 d-flex align-items-center justify-content-between gap-2" id="myPlanItem-{{ $mp->id }}">
                                <div class="min-w-0">
                                    <div class="fw-bold text-dark-emphasis fs-7 text-truncate">{{ $mp->ten_ke_hoach }}</div>
                                    <small class="text-secondary fs-8">{{ $mp->buoiTaps->count() }} buổi tập</small>
                                    @if($mp->is_shared && $mp->share_code)
                                        <div class="mt-1 d-flex align-items-center gap-1.5">
                                            <span class="badge rounded-pill font-monospace fs-8" style="background: rgba(34, 197, 94, 0.15); color: #22C55E;">
                                                Mã: {{ $mp->share_code }}
                                            </span>
                                            <button type="button" class="btn btn-sm btn-link p-0 text-primary btn-copy-code fs-8" data-code="{{ $mp->share_code }}" title="Sao chép">
                                                <i class="fa-solid fa-copy"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-shrink-0">
                                    @if($mp->is_shared && $mp->share_code)
                                        <button type="button" class="btn btn-sm btn-comm-outline rounded-pill px-3 py-1 fs-8 fw-semibold btn-copy-code" data-code="{{ $mp->share_code }}">
                                            <i class="fa-solid fa-copy me-1"></i> Copy mã
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-comm-primary rounded-pill px-3 py-1 fs-8 fw-semibold btn-generate-code-action" data-plan-id="{{ $mp->id }}">
                                            <i class="fa-solid fa-plus me-1"></i> Tạo mã
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-secondary fs-8 rounded-3" style="background: var(--comm-surface);">
                                Bạn chưa có kế hoạch rèn luyện nào. Hãy tạo kế hoạch trước khi chia sẻ.
                            </div>
                        @endforelse
                    </div>

                    <div class="alert alert-info border-0 rounded-3 fs-8 mb-0 d-flex align-items-start gap-2" style="background: rgba(59, 130, 246, 0.1); color: var(--comm-primary);">
                        <i class="fa-solid fa-shield-halved fs-6 flex-shrink-0 mt-0.5"></i>
                        <span>Người nhận chỉ có thể xem và sao chép lịch tập. Kế hoạch gốc của bạn hoàn toàn được bảo vệ và không bị sửa đổi.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         KÊNH CHAT THẾ GIỚI GYMER - GLOBAL COMMUNITY MESSENGER POPUP
         ========================================================================= -->
    <div class="community-messenger-popup shadow-lg" id="communityMessengerPopup" role="dialog" aria-modal="true" aria-label="Kênh Chat Thế Giới Gymer">
        <!-- HEADER KÊNH CHAT THẾ GIỚI -->
        <div class="community-popup-header d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
            <div class="d-flex align-items-center gap-2 min-w-0">
                <div class="header-icon-circle bg-primary-subtle text-primary flex-shrink-0">
                    <i class="fa-solid fa-earth-americas"></i>
                </div>
                <div class="min-w-0">
                    <h6 class="fw-bold mb-0 text-dark-emphasis fs-7 text-truncate">Kênh Chat Thế Giới</h6>
                    <small class="text-success fs-9 d-flex align-items-center gap-1 fw-medium">
                        <span class="online-indicator-dot"></span>
                        <span id="worldOnlineCountText">12 Gymer đang online</span>
                    </small>
                </div>
            </div>

            <!-- Nút công cụ & đóng -->
            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                <button type="button" class="btn btn-sm btn-light rounded-circle p-1 text-muted" id="btnRefreshWorldChat" title="Tải lại tin nhắn">
                    <i class="fa-solid fa-arrows-rotate fs-8"></i>
                </button>
                <button type="button" class="btn btn-sm btn-light rounded-circle p-1 text-muted" id="btnCloseCommunityPopup" aria-label="Đóng chat" title="Đóng">
                    <i class="fa-solid fa-xmark fs-7"></i>
                </button>
            </div>
        </div>

        <!-- BODY: DÒNG THỜI GIAN TIN NHẮN (CUỘN ĐỘC LẬP) -->
        <div class="world-chat-body flex-grow-1 p-2.5 d-flex flex-column min-h-0" id="worldChatContainer">
            <div class="world-chat-messages d-flex flex-column gap-2 flex-grow-1 overflow-y-auto" id="worldChatMessages">
                <!-- Sẽ được fill bằng JavaScript -->
            </div>
        </div>

        <!-- FOOTER: KHUNG NHẬP LIỆU GỬI TIN NHẮN -->
        <div class="world-chat-footer p-2 border-top">
            <form id="worldChatForm" class="d-flex align-items-center gap-1.5" onsubmit="return false;">
                <input type="text" class="form-control form-control-sm rounded-pill px-3 py-1 fs-8" id="worldChatMessageInput" placeholder="Nhắn tin lên kênh thế giới..." autocomplete="off">
                <button type="submit" class="btn btn-primary btn-sm rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" id="btnSendWorldMessage" style="width: 32px; height: 32px;" title="Gửi">
                    <i class="fa-solid fa-paper-plane fs-8"></i>
                </button>
            </form>
            <div class="mt-1 px-1 d-flex align-items-center justify-content-between text-nowrap">
                <small class="text-muted fs-10 text-truncate me-2">Tin nhắn công khai toàn cầu</small>
                <span class="text-primary fs-10 fw-semibold flex-shrink-0">
                    <i class="fa-solid fa-dumbbell me-0.5"></i> gymer-world
                </span>
            </div>
        </div>
    </div>

    <!-- Nút Cộng đồng Gymer nổi (Mở Messenger Popup ở góc phải bên dưới) -->
    <button type="button" class="floating-community-btn" id="floatingCommunityToggle" title="Chat Thế Giới Gymer" aria-label="Chat Thế Giới Gymer">
        <i class="fa-solid fa-comments icon-community-open"></i>
        <i class="fa-solid fa-xmark icon-community-close"></i>
        <span class="community-tooltip">Chat Thế Giới</span>
    </button>

    <!-- Floating Action Menu (Góc phải bên dưới) -->
    <x-floating-menu />

    <!-- FLOATING TOAST NOTIFICATION CONTAINER -->
    <div class="comm-toast-container" id="commToastContainer"></div>

    <!-- DATA PLANS SCRIPT ĐỂ TỐI ƯU HIỂN THỊ CHI TIẾT KHÔNG CẦN F5 / NETWORK DELAY -->
    <script>
        window.COMMUNITY_PLANS_DATA = @json($sharedPlans);
        window.COMMUNITY_CONFIG = {
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            generateShareCodeUrl: "{{ url('/workout/share') }}",
            copyPlanUrl: "{{ url('/workout/copy') }}"
        };
    </script>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- COMMUNITY LOGIC & INTERACTION CONTROLLER -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const config = window.COMMUNITY_CONFIG || {};
            const plansData = window.COMMUNITY_PLANS_DATA || [];
            const toastContainer = document.getElementById('commToastContainer');

            // -------------------------------------------------------------
            // Helper Toast Notification
            // -------------------------------------------------------------
            function showToast(message, type = 'success') {
                if (!toastContainer) return;
                const toast = document.createElement('div');
                toast.className = 'comm-toast';
                const iconClass = type === 'success' ? 'fa-circle-check text-success' : 'fa-circle-info text-primary';
                toast.innerHTML = `
                    <i class="fa-solid ${iconClass} fs-6 flex-shrink-0"></i>
                    <div class="flex-grow-1">${message}</div>
                `;
                toastContainer.appendChild(toast);

                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(30px)';
                    toast.style.transition = 'all 0.25s ease';
                    setTimeout(() => toast.remove(), 250);
                }, 3000);
            }

            // -------------------------------------------------------------
            // Copy text to clipboard helper
            // -------------------------------------------------------------
            function copyToClipboard(text, successMsg = 'Đã sao chép mã thành công!') {
                if (!text) return;
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(() => {
                        showToast(successMsg);
                    }).catch(() => {
                        fallbackCopyText(text, successMsg);
                    });
                } else {
                    fallbackCopyText(text, successMsg);
                }
            }

            function fallbackCopyText(text, successMsg) {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    showToast(successMsg);
                } catch (err) {
                    showToast('Không thể sao chép, vui lòng sao chép thủ công.', 'info');
                }
                textArea.remove();
            }

            // -------------------------------------------------------------
            // Sự kiện Copy mã chia sẻ từ các nút
            // -------------------------------------------------------------
            document.querySelectorAll('.btn-copy-code').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const code = this.getAttribute('data-code');
                    if (code) {
                        copyToClipboard(code, `Đã sao chép mã "${code}" vào bộ nhớ tạm!`);
                    }
                });
            });

            // -------------------------------------------------------------
            // Chia sẻ kế hoạch của tôi (Quick Share Widget)
            // -------------------------------------------------------------
            const quickPlanSelect = document.getElementById('quickPlanSelect');
            const quickDisplayCode = document.getElementById('quickDisplayCodeText');
            const btnQuickShareAction = document.getElementById('btnQuickShareAction');
            const quickShareBtnIcon = document.getElementById('quickShareBtnIcon');
            const quickShareBtnText = document.getElementById('quickShareBtnText');
            const btnCopyQuickCode = document.getElementById('btnCopyQuickCode');

            function updateQuickShareState() {
                if (!quickPlanSelect) return;
                const selectedOpt = quickPlanSelect.options[quickPlanSelect.selectedIndex];
                if (!selectedOpt) return;

                const isShared = selectedOpt.getAttribute('data-is-shared') === '1';
                const shareCode = selectedOpt.getAttribute('data-share-code');

                if (isShared && shareCode) {
                    if (quickDisplayCode) quickDisplayCode.textContent = shareCode;
                    if (quickShareBtnIcon) quickShareBtnIcon.className = 'fa-solid fa-copy';
                    if (quickShareBtnText) quickShareBtnText.textContent = 'Copy mã';
                } else {
                    if (quickDisplayCode) quickDisplayCode.textContent = 'Chưa tạo mã';
                    if (quickShareBtnIcon) quickShareBtnIcon.className = 'fa-solid fa-share-nodes';
                    if (quickShareBtnText) quickShareBtnText.textContent = 'Tạo mã chia sẻ';
                }
            }

            if (quickPlanSelect) {
                quickPlanSelect.addEventListener('change', updateQuickShareState);
                updateQuickShareState();
            }

            if (btnQuickShareAction) {
                btnQuickShareAction.addEventListener('click', async function() {
                    const selectedOpt = quickPlanSelect.options[quickPlanSelect.selectedIndex];
                    if (!selectedOpt) return;

                    const planId = selectedOpt.value;
                    const isShared = selectedOpt.getAttribute('data-is-shared') === '1';
                    const shareCode = selectedOpt.getAttribute('data-share-code');

                    if (isShared && shareCode) {
                        copyToClipboard(shareCode, `Đã sao chép mã "${shareCode}" vào bộ nhớ tạm!`);
                    } else {
                        // Gọi AJAX tạo mã mới
                        try {
                            btnQuickShareAction.disabled = true;
                            btnQuickShareAction.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang tạo...';

                            const res = await fetch(`${config.generateShareCodeUrl}/${planId}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': config.csrfToken,
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                }
                            });

                            const data = await res.json();
                            if (data.success && data.share_code) {
                                selectedOpt.setAttribute('data-is-shared', '1');
                                selectedOpt.setAttribute('data-share-code', data.share_code);
                                updateQuickShareState();
                                copyToClipboard(data.share_code, `Đã tạo mã thành công: "${data.share_code}"!`);
                            } else {
                                showToast('Không thể tạo mã, vui lòng thử lại.', 'info');
                            }
                        } catch (err) {
                            showToast('Lỗi kết nối khi tạo mã chia sẻ.', 'info');
                        } finally {
                            btnQuickShareAction.disabled = false;
                            updateQuickShareState();
                        }
                    }
                });
            }

            if (btnCopyQuickCode) {
                btnCopyQuickCode.addEventListener('click', function() {
                    const selectedOpt = quickPlanSelect ? quickPlanSelect.options[quickPlanSelect.selectedIndex] : null;
                    const shareCode = selectedOpt?.getAttribute('data-share-code');
                    if (shareCode) {
                        copyToClipboard(shareCode, `Đã sao chép mã "${shareCode}" vào bộ nhớ tạm!`);
                    } else {
                        showToast('Kế hoạch này chưa có mã chia sẻ, vui lòng bấm Tạo mã.', 'info');
                    }
                });
            }

            // Tạo mã trong modal Chia sẻ
            document.querySelectorAll('.btn-generate-code-action').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const planId = this.getAttribute('data-plan-id');
                    if (!planId) return;

                    try {
                        this.disabled = true;
                        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang tạo...';

                        const res = await fetch(`${config.generateShareCodeUrl}/${planId}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': config.csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        });

                        const data = await res.json();
                        if (data.success && data.share_code) {
                            copyToClipboard(data.share_code, `Tạo mã thành công: ${data.share_code}`);
                            setTimeout(() => window.location.reload(), 1200);
                        } else {
                            showToast('Không thể tạo mã lúc này.', 'info');
                            this.disabled = false;
                            this.innerHTML = '<i class="fa-solid fa-plus me-1"></i> Tạo mã';
                        }
                    } catch (err) {
                        showToast('Lỗi kết nối khi tạo mã chia sẻ.', 'info');
                        this.disabled = false;
                        this.innerHTML = '<i class="fa-solid fa-plus me-1"></i> Tạo mã';
                    }
                });
            });

            // -------------------------------------------------------------
            // Sao chép Kế hoạch (Copy Shared Plan)
            // -------------------------------------------------------------
            async function executeCopyPlan(code, planName) {
                if (!code) return;
                try {
                    const res = await fetch(`${config.copyPlanUrl}/${code}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': config.csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });

                    if (res.ok) {
                        const data = await res.json().catch(() => ({ success: true }));
                        showToast(`Đã sao chép kế hoạch thành công!`);
                        const detailModalEl = document.getElementById('planDetailModal');
                        if (detailModalEl) {
                            const modalInstance = bootstrap.Modal.getInstance(detailModalEl);
                            if (modalInstance) modalInstance.hide();
                        }
                    } else {
                        showToast('Không thể sao chép lịch tập lúc này.', 'info');
                    }
                } catch (err) {
                    showToast('Lỗi khi sao chép lịch tập.', 'info');
                }
            }

            document.querySelectorAll('.btn-copy-plan-action').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const code = this.getAttribute('data-code');
                    const name = this.getAttribute('data-name');
                    if (confirm(`Bạn có muốn sao chép toàn bộ kế hoạch "${name}" về danh sách tập luyện cá nhân của mình?`)) {
                        executeCopyPlan(code, name);
                    }
                });
            });

            // -------------------------------------------------------------
            // Xem Chi Tiết Kế Hoạch (Plan Detail Modal)
            // -------------------------------------------------------------
            const planDetailModalEl = document.getElementById('planDetailModal');
            const planDetailModal = planDetailModalEl ? new bootstrap.Modal(planDetailModalEl) : null;
            const detailPlanTitle = document.getElementById('detailPlanTitle');
            const detailPlanAuthor = document.getElementById('detailPlanAuthor');
            const detailPlanSessionsCount = document.getElementById('detailPlanSessionsCount');
            const detailPlanCode = document.getElementById('detailPlanCode');
            const detailPlanDesc = document.getElementById('detailPlanDesc');
            const btnDetailCopyCode = document.getElementById('btnDetailCopyCode');
            const detailSessionsListContainer = document.getElementById('detailSessionsListContainer');
            const btnDetailCopyPlanAction = document.getElementById('btnDetailCopyPlanAction');

            let currentActiveDetailCode = null;
            let currentActiveDetailName = null;

            document.querySelectorAll('.btn-view-plan-detail').forEach(btn => {
                btn.addEventListener('click', function() {
                    const planId = parseInt(this.getAttribute('data-plan-id'), 10);
                    const plan = plansData.find(p => p.id === planId);
                    if (!plan || !planDetailModal) return;

                    currentActiveDetailCode = plan.share_code;
                    currentActiveDetailName = plan.ten_ke_hoach;

                    if (detailPlanTitle) detailPlanTitle.textContent = plan.ten_ke_hoach;
                    if (detailPlanAuthor) detailPlanAuthor.textContent = plan.user ? plan.user.ho_ten : 'Gymer';
                    if (detailPlanSessionsCount) detailPlanSessionsCount.textContent = plan.buoi_taps ? plan.buoi_taps.length : 0;
                    if (detailPlanCode) detailPlanCode.textContent = plan.share_code;
                    if (detailPlanDesc) detailPlanDesc.textContent = plan.mo_ta || 'Giáo án rèn luyện tối ưu được cộng đồng Gymer khuyên dùng.';

                    // Render danh sách các buổi tập và bài tập
                    if (detailSessionsListContainer) {
                        detailSessionsListContainer.innerHTML = '';
                        if (plan.buoi_taps && plan.buoi_taps.length > 0) {
                            plan.buoi_taps.forEach((bt, idx) => {
                                const sessionDiv = document.createElement('div');
                                sessionDiv.className = 'p-3 rounded-3 border';
                                sessionDiv.style.backgroundColor = 'var(--comm-card-bg)';
                                sessionDiv.style.borderColor = 'var(--comm-card-border)';

                                const exercises = bt.chi_tiet_buoi_taps || [];
                                let exercisesHtml = '';

                                if (exercises.length > 0) {
                                    exercisesHtml = `
                                        <div class="mt-2.5 pt-2 border-top d-flex flex-column gap-1.5" style="border-color: var(--comm-card-border) !important;">
                                            ${exercises.map((ct, exIdx) => {
                                                const exName = ct.bai_tap_the_chat ? ct.bai_tap_the_chat.ten_bai_tap : 'Bài tập';
                                                const exMuscle = ct.bai_tap_the_chat ? ct.bai_tap_the_chat.nhom_co : 'Thể chất';
                                                const exSpecs = ct.dinh_dang_thong_so || (ct.so_sets ? `${ct.so_sets} sets × ${ct.so_reps || '10'} reps` : 'Tiêu chuẩn');
                                                return `
                                                    <div class="d-flex align-items-center justify-content-between py-1 text-truncate">
                                                        <div class="d-flex align-items-center gap-2 min-w-0">
                                                            <span class="badge rounded-circle d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 10px; background: var(--comm-surface); color: var(--comm-text-muted);">
                                                                ${exIdx + 1}
                                                            </span>
                                                            <div class="min-w-0">
                                                                <span class="fw-semibold text-dark-emphasis fs-8 text-truncate d-block">${exName}</span>
                                                                <small class="text-secondary fs-9">${exMuscle}</small>
                                                            </div>
                                                        </div>
                                                        <span class="badge rounded-pill fs-9 fw-semibold flex-shrink-0" style="background: var(--comm-surface); color: var(--comm-primary);">
                                                            ${exSpecs}
                                                        </span>
                                                    </div>
                                                `;
                                            }).join('')}
                                        </div>
                                    `;
                                } else {
                                    exercisesHtml = `<div class="text-secondary fs-9 mt-1 fst-italic">Chưa thêm bài tập cụ thể</div>`;
                                }

                                sessionDiv.innerHTML = `
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge rounded-pill fs-8 fw-semibold" style="background: var(--comm-primary-subtle); color: var(--comm-primary);">
                                                ${bt.ten_thu || `Buổi ${idx + 1}`}
                                            </span>
                                            <span class="fw-bold text-dark-emphasis fs-7">${bt.ten_buoi_tap}</span>
                                        </div>
                                        <small class="text-secondary fs-8">${exercises.length} bài tập</small>
                                    </div>
                                    ${exercisesHtml}
                                `;

                                detailSessionsListContainer.appendChild(sessionDiv);
                            });
                        } else {
                            detailSessionsListContainer.innerHTML = '<div class="text-secondary fs-8 p-3 text-center">Kế hoạch này chưa có buổi tập nào.</div>';
                        }
                    }

                    planDetailModal.show();
                });
            });

            if (btnDetailCopyCode) {
                btnDetailCopyCode.addEventListener('click', function() {
                    if (currentActiveDetailCode) {
                        copyToClipboard(currentActiveDetailCode, `Đã sao chép mã "${currentActiveDetailCode}"!`);
                    }
                });
            }

            if (btnDetailCopyPlanAction) {
                btnDetailCopyPlanAction.addEventListener('click', function() {
                    if (currentActiveDetailCode) {
                        executeCopyPlan(currentActiveDetailCode, currentActiveDetailName);
                    }
                });
            }

            // =========================================================================
            // CHAT THẾ GIỚI GYMER - GLOBAL COMMUNITY MESSENGER
            // =========================================================================
            function initWorldChat() {
                const toggleBtn = document.getElementById('floatingCommunityToggle');
                const popup = document.getElementById('communityMessengerPopup');
                const btnClose = document.getElementById('btnCloseCommunityPopup');
                const btnRefresh = document.getElementById('btnRefreshWorldChat');
                const messagesArea = document.getElementById('worldChatMessages');
                const chatForm = document.getElementById('worldChatForm');
                const chatInput = document.getElementById('worldChatMessageInput');
                const iconOpen = toggleBtn?.querySelector('.icon-community-open');
                const iconClose = toggleBtn?.querySelector('.icon-community-close');

                if (!toggleBtn || !popup) return;

                let isCommunityOpen = false;
                const STORAGE_KEY = 'life_planner_world_chat_v1';

                const DEFAULT_WORLD_MESSAGES = [
                    {
                        isMe: false,
                        author: 'Hoàng Lâm',
                        avatar: 'HL',
                        text: 'Chào cả nhà gymer! Hôm nay ai tập Push (Ngực - Vai - Tay sau) không? 💪',
                        time: '08:15 AM'
                    },
                    {
                        isMe: false,
                        author: 'Nguyễn Văn Hùng (HLV)',
                        avatar: 'VH',
                        text: 'Chào anh em! Nhớ khởi động xoay khớp vai kỹ trước khi đẩy tạ nặng nhé các bro!',
                        time: '08:24 AM'
                    },
                    {
                        isMe: false,
                        author: 'Tôn Đức Quang',
                        avatar: 'TQ',
                        text: 'Sáng nay vừa hoàn thành 4 sets Squat 110kg, mỏi nhưng phê quá anh em ơi! 🔥',
                        time: '08:50 AM'
                    },
                    {
                        isMe: false,
                        author: 'Trần Hoàng Nam',
                        avatar: 'HN',
                        text: 'Hôm nay ngày nghỉ (Rest Day) nạp đủ dinh dưỡng để mai vào việc tiếp. Chúc anh em tập cháy nhé!',
                        time: '09:10 AM'
                    }
                ];

                function loadWorldMessages() {
                    try {
                        const raw = localStorage.getItem(STORAGE_KEY);
                        if (raw) {
                            const parsed = JSON.parse(raw);
                            if (Array.isArray(parsed) && parsed.length > 0) return parsed;
                        }
                    } catch (e) {}
                    return [...DEFAULT_WORLD_MESSAGES];
                }

                function saveWorldMessages(messages) {
                    try {
                        localStorage.setItem(STORAGE_KEY, JSON.stringify(messages));
                    } catch (e) {}
                }

                function renderWorldMessages() {
                    if (!messagesArea) return;
                    const messages = loadWorldMessages();

                    messagesArea.innerHTML = `
                        <div class="text-center my-1">
                            <span class="badge bg-light text-muted border rounded-pill px-3 py-1 fs-9 fw-normal shadow-2xs">
                                <i class="fa-solid fa-earth-americas me-1 text-primary"></i> Kênh Chat Thế Giới Gymer • Đang hoạt động
                            </span>
                        </div>
                    `;

                    messages.forEach(msg => {
                        const row = document.createElement('div');
                        row.className = `world-msg-row ${msg.isMe ? 'sent-by-me' : ''}`;

                        if (msg.isMe) {
                            row.innerHTML = `
                                <div class="world-msg-bubble">
                                    <div>${msg.text}</div>
                                    <span class="world-msg-time">${msg.time}</span>
                                </div>
                            `;
                        } else {
                            row.innerHTML = `
                                <div class="world-msg-avatar">${msg.avatar || 'G'}</div>
                                <div class="min-w-0">
                                    <div class="world-msg-author">${msg.author}</div>
                                    <div class="world-msg-bubble">
                                        <div>${msg.text}</div>
                                        <span class="world-msg-time">${msg.time}</span>
                                    </div>
                                </div>
                            `;
                        }

                        messagesArea.appendChild(row);
                    });

                    messagesArea.scrollTop = messagesArea.scrollHeight;
                }

                function isFloatingMenuOpenHelper() {
                    if (typeof window.isFloatingMenuOpen === 'function') {
                        return window.isFloatingMenuOpen();
                    }
                    const menuContainer = document.getElementById('floatingMenuContainer');
                    return menuContainer ? menuContainer.classList.contains('active') : false;
                }

                function closeFloatingMenuHelper() {
                    if (typeof window.closeFloatingMenu === 'function') {
                        return window.closeFloatingMenu();
                    }
                    const menuContainer = document.getElementById('floatingMenuContainer');
                    if (menuContainer && menuContainer.classList.contains('active')) {
                        menuContainer.classList.remove('active');
                        return true;
                    }
                    return false;
                }

                function openPopup() {
                    // Đóng Menu điều hướng khi mở Chat
                    closeFloatingMenuHelper();
                    popup.classList.add('active');
                    isCommunityOpen = true;
                    toggleBtn.classList.add('active');
                    if (iconOpen) iconOpen.classList.add('d-none');
                    if (iconClose) iconClose.classList.remove('d-none');
                    renderWorldMessages();
                    setTimeout(() => {
                        if (chatInput) chatInput.focus();
                    }, 100);
                }

                function closePopup() {
                    popup.classList.remove('active');
                    isCommunityOpen = false;
                    toggleBtn.classList.remove('active');
                    if (iconOpen) iconOpen.classList.remove('d-none');
                    if (iconClose) iconClose.classList.add('d-none');
                }

                window.isCommunityChatOpen = function() {
                    return isCommunityOpen;
                };

                window.closeCommunityChat = function() {
                    if (isCommunityOpen) {
                        closePopup();
                        return true;
                    }
                    return false;
                };

                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();

                    // QUY TẮC MENU & CHAT:
                    // Khi Menu đang mở: Bấm icon Chat -> Chỉ đóng Menu! Chat vẫn CLOSED.
                    if (isFloatingMenuOpenHelper()) {
                        closeFloatingMenuHelper();
                        return;
                    }

                    if (isCommunityOpen) {
                        closePopup();
                    } else {
                        openPopup();
                    }
                });

                if (btnClose) {
                    btnClose.addEventListener('click', function(e) {
                        e.stopPropagation();
                        closePopup();
                    });
                }

                if (btnRefresh) {
                    btnRefresh.addEventListener('click', function(e) {
                        e.stopPropagation();
                        renderWorldMessages();
                    });
                }

                document.addEventListener('click', function(e) {
                    if (isCommunityOpen && !popup.contains(e.target) && !toggleBtn.contains(e.target)) {
                        if (!chatInput || chatInput.value.trim().length === 0) {
                            closePopup();
                        }
                    }
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && isCommunityOpen) {
                        closePopup();
                    }
                });

                function sendWorldMessage() {
                    if (!chatInput) return;
                    const text = chatInput.value.trim();
                    if (!text) return;

                    const now = new Date();
                    const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                    const newMsg = {
                        isMe: true,
                        author: 'Bạn',
                        avatar: 'ME',
                        text: text,
                        time: timeStr
                    };

                    const messages = loadWorldMessages();
                    messages.push(newMsg);
                    saveWorldMessages(messages);

                    const row = document.createElement('div');
                    row.className = 'world-msg-row sent-by-me';
                    row.innerHTML = `
                        <div class="world-msg-bubble">
                            <div>${text}</div>
                            <span class="world-msg-time">${timeStr}</span>
                        </div>
                    `;
                    messagesArea.appendChild(row);
                    chatInput.value = '';
                    messagesArea.scrollTop = messagesArea.scrollHeight;

                    // Phản hồi ngẫu nhiên từ cộng đồng sau 1.2s tạo không khí sôi động
                    const replies = [
                        { author: 'Nguyễn Văn Hùng (HLV)', avatar: 'VH', text: 'Tuyệt vời bro! Giữ form chuẩn và tập đều đặn nhé 🔥' },
                        { author: 'Tôn Đức Quang', avatar: 'TQ', text: 'Cố lên bạn ơi, rep cuối đẩy cháy hết mình luôn nhé! 💪' },
                        { author: 'Trần Hoàng Nam', avatar: 'HN', text: 'Chuẩn luôn anh em, tập xong nhớ nạp đủ protein và uống nước nha!' },
                        { author: 'Hoàng Lâm', avatar: 'HL', text: 'Đồng đội rèn luyện cùng nhau thế này năng lượng lên hẳn! 🔥' }
                    ];

                    setTimeout(() => {
                        const randomReply = replies[Math.floor(Math.random() * replies.length)];
                        const replyMsg = {
                            isMe: false,
                            author: randomReply.author,
                            avatar: randomReply.avatar,
                            text: randomReply.text,
                            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                        };

                        const currentMsgs = loadWorldMessages();
                        currentMsgs.push(replyMsg);
                        saveWorldMessages(currentMsgs);

                        const replyRow = document.createElement('div');
                        replyRow.className = 'world-msg-row';
                        replyRow.innerHTML = `
                            <div class="world-msg-avatar">${replyMsg.avatar}</div>
                            <div class="min-w-0">
                                <div class="world-msg-author">${replyMsg.author}</div>
                                <div class="world-msg-bubble">
                                    <div>${replyMsg.text}</div>
                                    <span class="world-msg-time">${replyMsg.time}</span>
                                </div>
                            </div>
                        `;
                        messagesArea.appendChild(replyRow);
                        messagesArea.scrollTop = messagesArea.scrollHeight;
                    }, 1200);
                }

                if (chatForm) {
                    chatForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        sendWorldMessage();
                    });
                }
            }

            // Đồng bộ nút đổi giao diện Sáng/Tối nhanh
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

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (csrfToken) {
                        fetch("{{ route('profile.theme.quick-toggle') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrfToken,
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({ theme: newTheme })
                        }).catch(err => console.log('Lỗi lưu giao diện:', err));
                    }
                });
            }

            initWorldChat();
        });
    </script>
</body>
</html>
