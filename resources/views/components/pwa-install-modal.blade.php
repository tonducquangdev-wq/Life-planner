<!-- PWA INSTALL MODAL (HỘP THOẠI HƯỚNG DẪN CÀI ĐẶT ỨNG DỤNG CHO ANDROID & IOS) -->
<div class="modal fade" id="pwaInstallModal" tabindex="-1" aria-labelledby="pwaInstallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content nm-modal-content border-0 p-2 shadow-lg" style="border-radius: 28px; background: var(--nm-surface, #ffffff);">
            
            <!-- Modal Header -->
            <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2.5">
                    <img src="{{ asset('images/icons/icon-192x192.png') }}" alt="Life Planner" width="40" height="40" class="rounded-3 shadow-sm">
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="pwaInstallModalLabel">Cài đặt Life Planner</h6>
                        <small class="text-muted fs-8">Ứng dụng web tiến bộ (PWA)</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body px-3 py-3">
                <p class="text-muted small mb-3">
                    Cài đặt ứng dụng lên màn hình chính giúp bạn truy cập nhanh chỉ bằng một chạm, hoạt động mượt mà như app gốc và nhận thông báo đẩy cho lịch học, hạn chót bài tập.
                </p>

                <!-- SECTION CHO ANDROID VÀ DESKTOP -->
                <div id="pwaAndroidInstructions">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-sm btn-primary rounded-pill py-2 fw-semibold fs-7 shadow-sm d-flex align-items-center justify-content-center gap-2"
                                onclick="window.LifePlannerPWA?.promptInstall()">
                            <i class="bi bi-download"></i>
                            <span>Cài đặt lên thiết bị ngay</span>
                        </button>
                    </div>
                    <div class="text-center mt-2.5">
                        <small class="text-muted fs-9">Không cần qua Google Play hay App Store</small>
                    </div>
                </div>

                <!-- SECTION DÀNH RIÊNG CHO IPHONE / IPAD (SAFARI) -->
                <div id="pwaIosInstructions" class="d-none">
                    <div class="p-3 rounded-4 mb-2" style="background: rgba(99, 102, 241, 0.06); border: 1px dashed rgba(99, 102, 241, 0.3);">
                        <div class="fw-bold text-primary mb-2 fs-7 d-flex align-items-center gap-1.5">
                            <i class="bi bi-apple"></i>
                            <span>Hướng dẫn cài đặt trên iOS Safari:</span>
                        </div>
                        <ol class="small text-muted mb-0 ps-3" style="line-height: 1.6;">
                            <li>Bấm vào biểu tượng <strong>Chia sẻ (Share) <i class="bi bi-box-arrow-up text-primary"></i></strong> ở thanh công cụ dưới Safari.</li>
                            <li>Cuộn danh sách xuống và chọn <strong>"Thêm vào MH chính" <i class="bi bi-plus-square text-primary"></i></strong> (Add to Home Screen).</li>
                            <li>Nhấn <strong>"Thêm" (Add)</strong> ở góc trên bên phải màn hình để hoàn tất.</li>
                        </ol>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-sm btn-light rounded-pill px-3.5 py-1.5 text-muted border fs-8 fw-medium" data-bs-dismiss="modal">
                    Để sau
                </button>
            </div>

        </div>
    </div>
</div>
