<!-- PWA INSTALL MODAL (HỘP THOẠI HƯỚNG DẪN CÀI ĐẶT ỨNG DỤNG CHO ANDROID & IOS) -->
<div class="modal fade" id="pwaInstallModal" tabindex="-1" aria-labelledby="pwaInstallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content nm-modal-content border p-2 shadow-lg" style="border-radius: 28px; background: var(--nm-surface, var(--bs-body-bg, #ffffff));">
            
            <!-- Modal Header -->
            <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2.5">
                    <img src="{{ asset('images/icons/icon-192x192.png') }}" alt="Life Planner" width="40" height="40" class="rounded-3 shadow-sm">
                    <div>
                        <h6 class="modal-title fw-bold text-body mb-0" id="pwaInstallModalLabel">Cài đặt Life Planner</h6>
                        <small class="text-body-secondary fs-8">Ứng dụng web tiến bộ (PWA)</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body px-3 py-3">
                <p class="text-body-secondary small mb-3">
                    Cài đặt ứng dụng lên màn hình chính giúp bạn truy cập nhanh chỉ bằng một chạm, hoạt động mượt mà toàn màn hình như app gốc và nhận thông báo đẩy cho lịch học, hạn chót bài tập.
                </p>

                <!-- CẢNH BÁO: TRUY CẬP QUA HTTP MẠNG NỘI BỘ (CẦN LINK HTTPS NGROK) -->
                <div id="pwaHttpsNotice" class="d-none mb-3">
                    <div class="p-3 rounded-4" style="background: rgba(245, 158, 11, 0.1); border: 1px dashed rgba(245, 158, 11, 0.4);">
                        <div class="fw-bold text-warning mb-1 fs-7 d-flex align-items-center gap-1.5">
                            <i class="bi bi-shield-exclamation"></i>
                            <span>Yêu cầu liên kết bảo mật HTTPS:</span>
                        </div>
                        <p class="small text-body-secondary mb-2" style="line-height: 1.5;">
                            Trình duyệt di động (Safari / Chrome) chỉ cho phép cài đặt PWA và bật thông báo đẩy khi truy cập qua giao thức <strong>HTTPS</strong>. Khi dùng IP LAN (<code>http://...</code>), trình duyệt tự động chặn Service Worker.
                        </p>
                        <div class="small p-2 rounded-3 bg-body border text-body-secondary font-monospace fs-9">
                            Khởi chạy: <code>ngrok http 8000</code> rồi mở link <code>https://...ngrok-free.app</code> trên điện thoại.
                        </div>
                    </div>
                </div>

                <!-- CẢNH BÁO: MỞ BẰNG TRÌNH DUYỆT TRONG ỨNG DỤNG (ZALO, FACEBOOK) -->
                <div id="pwaInAppNotice" class="d-none mb-3">
                    <div class="p-3 rounded-4" style="background: rgba(239, 68, 68, 0.08); border: 1px dashed rgba(239, 68, 68, 0.35);">
                        <div class="fw-bold text-danger mb-1 fs-7 d-flex align-items-center gap-1.5">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span>Đang mở trong ứng dụng Zalo / Facebook:</span>
                        </div>
                        <p class="small text-body-secondary mb-0" style="line-height: 1.5;">
                            Trình duyệt nội bộ của Zalo/Facebook chặn việc cài PWA. Vui lòng bấm vào biểu tượng <strong>menu 3 chấm (···)</strong> ở góc trên bên phải màn hình và chọn <strong>"Mở bằng trình duyệt"</strong> (Safari trên iPhone hoặc Chrome trên Android).
                        </p>
                    </div>
                </div>

                <!-- SECTION CHO ANDROID VÀ DESKTOP -->
                <div id="pwaAndroidInstructions">
                    <!-- Nút 1-Chạm (chỉ hiển thị khi có beforeinstallprompt) -->
                    <div id="pwaAndroidPromptBtn" class="d-none mb-3">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary rounded-pill py-2.5 fw-semibold fs-7 shadow-sm d-flex align-items-center justify-content-center gap-2"
                                    onclick="window.LifePlannerPWA?.promptInstall()">
                                <i class="bi bi-download"></i>
                                <span>Cài đặt lên thiết bị ngay</span>
                            </button>
                        </div>
                    </div>

                    <!-- Hướng dẫn cài đặt thủ công trên Android Chrome -->
                    <div id="pwaAndroidManual" class="p-3 rounded-4" style="background: rgba(99, 102, 241, 0.06); border: 1px dashed rgba(99, 102, 241, 0.3);">
                        <div class="fw-bold text-primary mb-2 fs-7 d-flex align-items-center gap-1.5">
                            <i class="bi bi-android2"></i>
                            <span>Cách cài đặt trên Android (Google Chrome):</span>
                        </div>
                        <ol class="small text-body-secondary mb-0 ps-3" style="line-height: 1.6;">
                            <li>Bấm vào biểu tượng <strong>menu 3 chấm <i class="bi bi-three-dots-vertical text-primary"></i></strong> ở góc trên bên phải Chrome.</li>
                            <li>Chọn <strong>"Cài đặt ứng dụng" <i class="bi bi-download text-primary"></i></strong> (hoặc <strong>"Thêm vào màn hình chính"</strong>).</li>
                            <li>Nhấn xác nhận <strong>"Cài đặt"</strong> (Install) để thêm biểu tượng Life Planner ra màn hình.</li>
                        </ol>
                    </div>

                    <div class="text-center mt-2.5">
                        <small class="text-body-secondary fs-9">Không cần tải tệp APK hay qua Google Play</small>
                    </div>
                </div>

                <!-- SECTION DÀNH RIÊNG CHO IPHONE / IPAD (SAFARI) -->
                <div id="pwaIosInstructions" class="d-none">
                    <div class="p-3 rounded-4 mb-2" style="background: rgba(99, 102, 241, 0.06); border: 1px dashed rgba(99, 102, 241, 0.3);">
                        <div class="fw-bold text-primary mb-2 fs-7 d-flex align-items-center gap-1.5">
                            <i class="bi bi-apple"></i>
                            <span>Hướng dẫn cài đặt trên iOS (Safari):</span>
                        </div>
                        <ol class="small text-body-secondary mb-2 ps-3" style="line-height: 1.65;">
                            <li>Bấm vào biểu tượng <strong>Chia sẻ (Share) <i class="bi bi-box-arrow-up text-primary"></i></strong> ở thanh công cụ dưới Safari.</li>
                            <li>Cuộn menu xuống và chọn <strong>"Thêm vào MH chính" <i class="bi bi-plus-square text-primary"></i></strong> (Add to Home Screen).</li>
                            <li>Nhấn <strong>"Thêm" (Add)</strong> ở góc trên bên phải màn hình để hoàn tất.</li>
                            <li>Mở ứng dụng từ màn hình chính và vào lại mục Hồ sơ để bật chuông thông báo đẩy!</li>
                        </ol>
                        <div class="p-2 rounded-3 bg-body border small text-body-secondary fs-9 d-flex align-items-center gap-1.5">
                            <i class="bi bi-info-circle text-primary flex-shrink-0"></i>
                            <span>Apple iOS yêu cầu thêm vào Màn hình chính trước mới có thể bật thông báo Web Push.</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-0 pt-0 justify-content-between">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fs-8" onclick="navigator.clipboard && navigator.clipboard.writeText(window.location.href); window.LifePlannerPWA && window.LifePlannerPWA.showToast('info', 'Đã sao chép link website!', 'Sao chép');">
                    <i class="bi bi-clipboard me-1"></i> Sao chép link
                </button>
                <button type="button" class="btn btn-sm btn-light rounded-pill px-3.5 py-1.5 text-body-secondary border fs-8 fw-medium" data-bs-dismiss="modal">
                    Đã hiểu
                </button>
            </div>

        </div>
    </div>
</div>
