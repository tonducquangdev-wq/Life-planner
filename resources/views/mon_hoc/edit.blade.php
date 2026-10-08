<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Môn Học - Life Planner</title>
    
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
        /* Dark mode variables and enhancements */
        [data-bs-theme="dark"] body {
            background-color: #121212 !important;
            color: #E8E8EA !important;
        }

        [data-bs-theme="dark"] .card {
            background-color: #1E1E22 !important;
            border-color: #2E2E34 !important;
            color: #E8E8EA !important;
        }

        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background-color: #1E1E22 !important;
            border-color: #2E2E34 !important;
            color: #E8E8EA !important;
        }

        [data-bs-theme="dark"] .form-control:focus,
        [data-bs-theme="dark"] .form-select:focus {
            background-color: #1E1E22 !important;
            border-color: #6C8CFF !important;
            color: #E8E8EA !important;
        }

        [data-bs-theme="dark"] .text-dark {
            color: #E8E8EA !important;
        }

        [data-bs-theme="dark"] .text-muted {
            color: #A0A0A8 !important;
        }

        [data-bs-theme="dark"] .bg-white {
            background-color: #1E1E22 !important;
        }

        [data-bs-theme="dark"] .bg-light {
            background-color: #26262B !important;
            color: #E8E8EA !important;
        }

        [data-bs-theme="dark"] .btn-light {
            background-color: #1E1E22 !important;
            border-color: #2E2E34 !important;
            color: #E8E8EA !important;
        }

        [data-bs-theme="dark"] .btn-light:hover {
            background-color: #2E2E34 !important;
            color: #FFFFFF !important;
        }
    </style>
</head>
<body>

    <!-- MAIN WRAPPER -->
    <div class="main-wrapper main-wrapper-full">
        
        <!-- TOPBAR TRÊN CÙNG COMPACT 58PX -->
        @include('layouts.topbar', ['title' => 'Chỉnh sửa môn học'])

        <!-- CONTENT BODY -->
        <main class="content-body py-4 px-3 px-md-4">
            <div class="container-xl p-0">
                <div class="mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Cập nhật Môn học: {{ $monHoc->ten_mon }}</h4>
                        <p class="text-muted mb-0">Chỉnh sửa thông tin chi tiết môn học và lưu thay đổi.</p>
                    </div>
                    <a href="{{ route('mon-hoc.index') }}" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 shadow-xs d-inline-flex align-items-center gap-1.5">
                        <i class="bi bi-arrow-left"></i>
                        <span>Quay lại</span>
                    </a>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white" style="max-width: 900px; margin: 0 auto;">
                    <form action="{{ route('mon-hoc.update', $monHoc) }}" method="POST">
                        @method('PUT')
                        @include('mon_hoc.form')

                        <div class="mt-4 pt-3 border-top d-flex gap-2 justify-content-end">
                            <a href="{{ route('mon-hoc.index') }}" class="btn btn-light rounded-pill px-4">Hủy bỏ</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold d-inline-flex align-items-center gap-1.5">
                                <i class="bi bi-check-lg"></i>
                                <span>Cập nhật thay đổi</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
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
