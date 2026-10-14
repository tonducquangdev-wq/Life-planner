# HƯỚNG DẪN CÁC LỆNH PWA & WEB PUSH — LIFE PLANNER

Tài liệu này tổng hợp các lệnh Artisan, Pint và Test liên quan đến hệ thống **PWA (Progressive Web App)** và **Web Push Notifications** trong dự án Life Planner, cùng hướng dẫn chi tiết về thời điểm sử dụng và trạng thái thực thi hiện tại.

---

## I. BẢNG TỔNG HỢP CÁC LỆNH VÀ THỜI ĐIỂM CHẠY

| Lệnh | Mục đích | Có cần chạy lại khi bật Web không? | Trạng thái hiện tại |
| :--- | :--- | :--- | :--- |
| `php artisan test --compact tests/Feature/PwaAndPushNotificationTest.php` | Chạy 11 kịch bản kiểm thử tự động PWA & Web Push | **KHÔNG CẦN**. Chỉ chạy khi lập trình viên sửa code muốn kiểm tra tính đúng đắn | **ĐÃ CHẠY** (11/11 Passed, 43 assertions) |
| `vendor/bin/pint --dirty --format agent` | Tự động định dạng code PHP theo chuẩn PSR-12 / Laravel Pint | **KHÔNG CẦN**. Chỉ chạy khi lập trình viên vừa viết thêm/sửa file PHP | **ĐÃ CHẠY** (Code đã chuẩn hóa 100%) |
| `php artisan push:send-reminders --window=30` | Quét DB và gửi thông báo Web Push ngay lập tức cho các sự kiện sắp diễn ra | **TÙY CHỌN**. Chạy khi muốn ép gửi thông báo tức thì để test. Bình thường Cron Job sẽ tự chạy | **ĐÃ CHẠY THỬ** (Exit code 0, hoàn tất) |
| `php artisan webpush:vapid` | Tạo cặp khóa VAPID mới (EC P-256) và ghi vào `.env` | **TUYỆT ĐỐI KHÔNG CHẠY LẠI** khi web đang hoạt động bình thường | **ĐÃ CHẠY** (Khóa đã có trong `.env`) |
| `php artisan schedule:list` | Xem danh sách các tiến trình tự động đang được lên lịch | **KHÔNG CẦN**. Đây là lệnh tra cứu thông tin (read-only) | **ĐÃ CHẠY** (Đã lên lịch quét 15 phút/lần) |

---

## II. CHI TIẾT TỪNG LỆNH & CÁCH SỬ DỤNG

### 1. Kiểm thử tự động (Feature Test)
```bash
php artisan test --compact tests/Feature/PwaAndPushNotificationTest.php
```
- **Ý nghĩa**: Kiểm tra tự động 11 thành phần cốt lõi:
  1. File `manifest.json` tồn tại, đúng định dạng JSON và khai báo đầy đủ các trường PWA W3C.
  2. Toàn bộ file icons (`192x192`, `512x512`, `maskable`, `apple-touch-icon`) tồn tại thực tế trên ổ đĩa.
  3. Service Worker `public/sw.js` đăng ký đầy đủ các sự kiện (`install`, `activate`, `fetch`, `push`, `notificationclick`).
  4. Trang ngoại tuyến `/offline` và file `public/offline.html` hoạt động tốt.
  5. Các view chính chứa đầy đủ thẻ PWA meta head và file `pwa-manager.js`.
  6. Endpoint `/push/vapid-key` bắt buộc đăng nhập (bảo vệ an toàn).
  7. Người dùng đăng nhập lấy được Public Key thành công.
  8. Lưu thiết bị nhận thông báo vào database (`push_subscriptions`) thành công.
  9. Kiểm tra trạng thái thiết bị (`/push/status`).
  10. Hủy đăng ký thiết bị (`/push/unsubscribe`).
  11. Lệnh quét nhắc nhở `push:send-reminders` thực thi trơn tru.

---

### 2. Định dạng mã nguồn (Laravel Pint)
```bash
vendor/bin/pint --dirty --format agent
```
- **Ý nghĩa**: Quét các file PHP vừa chỉnh sửa và tự động sửa các lỗi format (khoảng trắng, thụt dòng, dấu ngoặc, namespace) theo đúng tiêu chuẩn mã nguồn Laravel.

---

### 3. Quét và gửi thông báo nhắc nhở (Web Push Reminders)
```bash
# Quét các sự kiện sẽ diễn ra trong 30 phút tới (mặc định)
php artisan push:send-reminders --window=30

# Hoặc quét trong cửa sổ 60 phút tới
php artisan push:send-reminders --window=60
```
- **Ý nghĩa**: Lệnh này quét 4 nguồn dữ liệu:
  - Sự kiện lịch cá nhân (bảng `su_kien`)
  - Deadline công việc / bài tập (bảng `cong_viec`)
  - Lịch học trong ngày theo thứ (bảng `lich_hoc`)
  - Lịch tập luyện thể chất
- **Cơ chế chạy thực tế**:
  - Khi triển khai Production, hệ thống sẽ chạy thông qua **Cron Job** của máy chủ (`* * * * * php artisan schedule:run`). Scheduler đã được đặt lịch tự động chạy lệnh này **mỗi 15 phút** trong file `routes/console.php`.
  - Nếu đang test ở môi trường Local (máy cá nhân), bạn có thể mở 1 tab terminal riêng và chạy:
    ```bash
    php artisan schedule:work
    ```
    Lệnh này sẽ giả lập Cron Job để tự động kích hoạt các tác vụ theo đúng lịch.

---

### 4. Tạo cặp khóa VAPID (Chỉ dùng khi cài đặt lần đầu hoặc reset)
```bash
php artisan webpush:vapid
```
- **LƯU Ý QUAN TRỌNG**:
  - Lệnh này **ĐÃ ĐƯỢC CHẠY** và cặp khóa đã được lưu an toàn trong file `.env`:
    - `VAPID_PUBLIC_KEY`
    - `VAPID_PRIVATE_KEY`
    - `VAPID_SUBJECT`
  - **KHÔNG CHẠY LẠI LỆNH NÀY** khi hệ thống đã có người dùng đăng ký nhận thông báo. Nếu chạy lại, máy chủ sẽ sinh ra cặp khóa mới, khiến toàn bộ token push cũ của các thiết bị trước đó không còn hợp lệ nữa (trình duyệt sẽ báo lỗi 401/403 khi gửi).

---

### 5. Kiểm tra danh sách Scheduler
```bash
php artisan schedule:list
```
- **Ý nghĩa**: Hiển thị bảng danh sách các lệnh được lên lịch tự động trong hệ thống Laravel và thời gian lần chạy kế tiếp.

---

## III. HIỆN TẠI BẠN CẦN LÀM GÌ ĐỂ TRẢI NGHIỆM ỨNG DỤNG?

Hiện tại, web của bạn đã chạy sẵn (`php artisan serve` trên cổng `8000`), cơ sở dữ liệu đã migrate xong, PWA và Web Push đã nạp đầy đủ.

👉 **Bạn KHÔNG CẦN CHẠY LẠI BẤT KỲ LỆNH NÀO TRÊN.**

Bạn chỉ cần:
1. Mở trình duyệt truy cập: `http://localhost:8000` (hoặc `http://127.0.0.1:8000`).
2. Vào trang **Lịch** hoặc **Hồ sơ** -> **Cài đặt thông báo**:
   - Thử bấm nút **"📲 Cài App"** trên Floating Menu để cài đặt PWA vào máy tính/điện thoại.
   - Thử bật công tắc **"Bật thông báo đẩy"** và bấm **"Gửi thử thông báo"** để xem thông báo Web Push xuất hiện trên màn hình.
