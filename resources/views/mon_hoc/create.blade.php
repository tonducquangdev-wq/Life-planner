<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Môn Học Mới - Life Planner</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Font: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom Dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>

    <!-- SIDEBAR -->
    @include('layouts.sidebar')

    <!-- MAIN WRAPPER -->
    <div class="main-wrapper">
        
        <!-- HEADER -->
        <header class="top-header">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none p-1 px-2 border" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h5 class="fw-bold text-dark mb-0">Thêm môn học mới</h5>
            </div>

            <div class="header-actions">
                <div class="user-profile dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ mb_substr(Auth::user()->ho_ten ?? 'User', 0, 1) }}
                        </div>
                        <div class="d-none d-md-block text-start">
                            <div class="fw-bold text-dark fs-6 leading-tight">{{ Auth::user()->ho_ten ?? 'User' }}</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Hồ sơ</a></li>
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
        <main class="content-body">
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Tạo mới Môn học</h4>
                    <p class="text-muted mb-0">Nhập đầy đủ thông tin để theo dõi tiến độ và lịch học môn học mới.</p>
                </div>
                <a href="{{ route('mon-hoc.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <form action="{{ route('mon-hoc.store') }}" method="POST">
                    @include('mon_hoc.form')

                    <div class="mt-4 pt-3 border-top d-flex gap-2 justify-content-end">
                        <a href="{{ route('mon-hoc.index') }}" class="btn btn-light rounded-pill px-4">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-check-lg me-1"></i> Lưu môn học
                        </button>
                    </div>
                </form>
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
</body>
</html>
