# 🌟 LIFE PLANNER — NỀN TẢNG QUẢN LÝ HỌC TẬP & ĐỜI SỐNG SINH VIÊN

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3" />
  <img src="https://img.shields.io/badge/Laravel-11%2F12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel" />
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap" />
  <img src="https://img.shields.io/badge/PWA-Web%20Push-5A0FC8?style=for-the-badge&logo=pwa&logoColor=white" alt="PWA" />
  <img src="https://img.shields.io/badge/Flutter-Ready-02569B?style=for-the-badge&logo=flutter&logoColor=white" alt="Flutter" />
</p>

> **Đồ Án Môn Học**: Chuyên Đề PHP & Công Nghệ Mã Nguồn Mở  
> **Sinh viên thực hiện**: Tôn Đức Quang

                            Đỗ Nguyễn Hoàng Lâm

> **Ứng dụng**: Hệ thống quản lý toàn diện dành cho sinh viên (All-in-One Life & Study Management Platform).

---

## 📖 1. Giới Thiệu Dự Án

**Life Planner** là nền tảng quản lý thông minh được thiết kế chuyên biệt cho sinh viên đại học nhằm giải quyết tình trạng **"App Overload"** (phải dùng quá nhiều ứng dụng rời rạc cho Lịch, To-Do list, Điểm số, Thể hình).

Hệ thống kết hợp 3 trụ cột thiết yếu của đời sống sinh viên trên **một bảng điều khiển duy nhất (Unified Dashboard)**:

1. 🎓 **Học tập đỉnh cao (Academic)**: Theo dõi môn học, tiến độ làm bài tập, tự động tính & dự báo điểm tích lũy GPA.
2. 📅 **Thời gian thông minh (Master Calendar)**: Đồng bộ lịch học, deadline, sự kiện cá nhân với thuật toán cảnh báo trùng lịch.
3. 🏋️ **Sức khỏe & Rèn luyện (Fitness)**: Thiết lập kế hoạch tập gym/thể chất, áp dụng lịch mẫu và chia sẻ giáo án cộng đồng qua mã **Share Code**.

Hệ thống hoạt động như một ứng dụng web lũy tiến **PWA (Progressive Web App)** với cơ chế **Web Push Notification** chuẩn mã hóa **VAPID**, gửi thông báo nhắc lịch học và deadline tự động ngay cả khi người dùng không mở trình duyệt.

---

## 🚀 2. Các Tính Năng Nổi Bật

### 📅 2.1. Lịch Biểu Tổng Hợp Thông Minh (Master Calendar)

- **Hội tụ đa nguồn dữ liệu**: Hiển thị trên cùng một giao diện trực quan: Lịch học cố định trong tuần, Sự kiện cá nhân, Deadline bài tập/công việc và Lịch tập thể hình.
- **Phát hiện xung đột lịch (Conflict Detection)**: Thuật toán kiểm tra và cảnh báo tức thì khi hai sự kiện hoặc lịch học bị trùng khung giờ.
- **Sự kiện lặp định kỳ (Recurrence - RRULE)**: Hỗ trợ cấu hình sự kiện lặp hàng ngày, hàng tuần, hàng tháng kèm ngày kết thúc chu kỳ lặp.

### 🎓 2.2. Quản Lý Môn Học & Dự Báo GPA

- Quản lý danh sách môn học theo từng học kỳ kèm số tín chỉ và trọng số điểm (chuyên cần, giữa kỳ, đồ án, cuối kỳ).
- **Tự động tính điểm**: Quy đổi điểm trung bình môn sang thang điểm 4 (GPA) và thang điểm chữ (A, B+, B, C,...).
- **Báo cáo học tập**: Biểu đồ trực quan phân tích kết quả học tập và dự báo xếp loại sinh viên (Xuất sắc, Giỏi, Khá).
- **Kết nối Gia sư (Tutor Match)**: Đăng ký tìm kiếm và ghép nối gia sư hỗ trợ các môn học khó.

### 🏋️ 2.3. Lập Kế Hoạch Tập Luyện Thể Chất (Workout Planner)

- **Giáo án tập mẫu có sẵn**: Tích hợp sẵn các lộ trình phổ biến (Push / Pull / Legs, Upper / Lower, Full Body).
- **Tùy biến lịch tập cá nhân**: Tự tạo kế hoạch, thêm buổi tập và cấu hình chi tiết bài tập (số hiệp, số lần lặp, mức tạ, thời gian nghỉ).
- **Chia sẻ cộng đồng qua Share Code**: Tạo mã chia sẻ độc nhất cho giáo án của mình; sinh viên khác chỉ cần nhập mã để sao chép toàn bộ giáo án về tài khoản cá nhân.
- **Nhật ký & Báo cáo**: Ghi nhận lịch sử hoàn thành buổi tập, tính toán tổng thời gian và calo tiêu hao.

### 📋 2.4. Quản Lý Dự Án & Công Việc (Project & Tasks)

- Phân chia công việc theo dự án/môn học.
- Theo dõi trạng thái (Chưa làm, Đang làm, Hoàn thành), mức độ ưu tiên và thanh phần trăm tiến độ (0% - 100%).
- **Đồng bộ Lịch 1-Chạm**: Đồng bộ trực tiếp hạn chót của công việc sang Master Calendar.

### 🔔 2.5. PWA & Web Push Notifications Tự Động

- **Cài đặt như Native App**: Đạt chuẩn PWA W3C (`manifest.json`, Service Worker, Icons đa độ phân giải).
- **Chế độ Offline**: Trang đệm ngoại tuyến thông minh khi người dùng mất mạng Internet.
- **Thông báo đẩy bảo mật (VAPID)**: Hệ thống background schedule quét định kỳ mỗi phút và gửi Web Push nhắc nhở trước 15 - 30 - 60 phút cho lịch học, deadline và lịch tập gym.

### 💬 2.6. Trò Chuyện Cộng Đồng & Giao Diện

- **World Chat**: Kênh chat thời gian thực kết nối cộng đồng sinh viên chia sẻ kinh nghiệm học tập.
- **Giao diện hiện đại**: Hỗ trợ chế độ **Sáng (Light) / Tối (Dark)** với kịch bản chống chớp màn hình (Anti-Flicker).

### 📱 2.7. Ứng Dụng Di Động (Flutter) & RESTful API

- Bộ RESTful API đầy đủ được bảo vệ bởi **Laravel Sanctum** (Bearer Token) phục vụ cho ứng dụng di động Flutter (trong thư mục `flutter_app/`).

---

## 🛠️ 3. Công Nghệ Sử Dụng

| Tầng hệ thống            | Công nghệ / Thư viện chính                                                           |
| :----------------------- | :----------------------------------------------------------------------------------- |
| **Backend**              | PHP 8.3+, Laravel 11/12 (MVC, Eloquent ORM, Artisan CLI, Task Scheduling)            |
| **Bảo mật & Auth**       | Laravel Breeze, Laravel Sanctum, CSRF Token, Bcrypt Hash                             |
| **Web Push & PWA**       | `minishlink/web-push` 11.0 (chuẩn mã hóa EC P-256 / VAPID), Service Worker Cache API |
| **Database**             | MySQL 8.0+ (34+ migrations, hỗ trợ Soft Deletes, Index tối ưu hóa)                   |
| **Frontend Web**         | Blade Template, Bootstrap 5.3, Tailwind CSS, Alpine.js, Vite 8.0                     |
| **Mobile App**           | Flutter 3.x (Dart), Material 3 Responsive UI                                         |
| **Kiểm thử & Định dạng** | PHPUnit 12, Laravel Pint (chuẩn PSR-12)                                              |

---

## ⚙️ 4. Hướng Dẫn Cài Đặt Dự Án Chi Tiết

### 4.1. Yêu Cầu Môi Trường (Prerequisites)

Trước khi cài đặt, hãy đảm bảo máy tính của bạn đã có:

- **Hệ điều hành**: Windows 10/11, macOS hoặc Linux.
- **PHP**: Phiên bản `>= 8.3` (với các extension: `pdo_mysql`, `curl`, `openssl`, `mbstring`, `gmp` để hỗ trợ WebPush VAPID).
- **Web Server & Database**: Khuyến nghị dùng **Laragon** (Windows) hoặc XAMPP / Docker.
- **Composer**: Phiên bản `>= 2.5`.
- **Node.js**: Phiên bản `>= 18.x` & **NPM**.
- **Git** (nếu clone từ repository).

---

### 4.2. Các Bước Cài Đặt (Step-by-Step)

#### Bước 1: Mở Terminal tại thư mục dự án

Nếu dự án đã nằm trong máy (ví dụ đường dẫn Laragon: `d:/laragon/laragon/www/DoAnCuoiKi/life-planner`):

```bash
cd d:/laragon/laragon/www/DoAnCuoiKi/life-planner
```

#### Bước 2: Cài đặt các gói phụ thuộc PHP (Composer)

```bash
composer install
```

#### Bước 3: Thiết lập tệp cấu hình môi trường (.env)

Sao chép tệp mẫu nếu chưa có tệp `.env`:

```bash
copy .env.example .env     # Trên Windows (CMD / PowerShell)
# cp .env.example .env     # Trên Linux / macOS
```

Mở tệp `.env` và kiểm tra cấu hình kết nối Database (ví dụ với MySQL của Laragon):

```dotenv
APP_NAME="Life Planner"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=life_planner
DB_USERNAME=root
DB_PASSWORD=
```

> _(Lưu ý: Bạn hãy tạo sẵn một database rỗng có tên `life_planner` trong MySQL phpMyAdmin hoặc HeidiSQL)_.

#### Bước 4: Tạo Application Key

```bash
php artisan key:generate
```

#### Bước 5: Chạy Migration và Nạp Dữ Liệu Mẫu (Seed Data)

Lệnh này sẽ khởi tạo 34 bảng cơ sở dữ liệu và nạp sẵn tài khoản mẫu, môn học, bài tập và giáo án gym:

```bash
php artisan migrate:fresh --seed
```

#### Bước 6: Khởi tạo cặp khóa Web Push VAPID

Để kích hoạt tính năng thông báo đẩy trên trình duyệt:

```bash
php artisan webpush:vapid
```

_(Lệnh sẽ tự động sinh cặp khóa công khai / bí mật và cập nhật vào biến `VAPID_PUBLIC_KEY` và `VAPID_PRIVATE_KEY` trong `.env`)_.

#### Bước 7: Cài đặt và biên dịch tài nguyên Frontend (Vite)

```bash
npm install
npm run build
```

_(Nếu đang trong quá trình phát triển giao diện, bạn có thể chạy `npm run dev`)_.

#### Bước 8: Tạo liên kết Symbolic Link cho thư mục lưu trữ ảnh/avatar

```bash
php artisan storage:link
```

---

### 4.3. Khởi Chạy Ứng Dụng

#### Cách 1: Sử dụng máy chủ phát triển nội bộ của Laravel

Mở 2 cửa sổ terminal:

- **Terminal 1 - Web Server**:

    ```bash
    php artisan serve
    ```

    Truy cập ứng dụng tại: `http://127.0.0.1:8000`

- **Terminal 2 - Tiến trình gửi thông báo nền (Scheduler)**:
    ```bash
    php artisan schedule:work
    ```
    _(Tiến trình này sẽ tự động quét và gửi Web Push nhắc nhở trước giờ học/sự kiện)_.

#### Cách 2: Chạy trực tiếp qua Virtual Host của Laragon

- Khởi động Laragon (Apache/Nginx & MySQL).
- Truy cập domain ảo tự sinh: `http://life-planner.test`

---

## 🔑 5. Tài Khoản Trải Nghiệm Mẫu (Demo Account)

Hệ thống đã chuẩn bị sẵn tài khoản sinh viên mẫu đầy đủ dữ liệu thời khóa biểu, lịch tập và bài tập:

| Thông tin    | Giá trị đăng nhập                  |
| :----------- | :--------------------------------- |
| **Email**    | `quang@gmail.com`                  |
| **Mật khẩu** | `12345678`                         |
| **Họ tên**   | Tôn Đức Quang                      |
| **Vai trò**  | Sinh viên / Quản trị viên hệ thống |

---

## 🧪 6. Kiểm Thử & Tự Động Hóa (Testing & Quality)

Dự án được xây dựng với độ tin cậy cao, có sẵn 17 bộ kiểm thử chức năng (Feature Tests):

```bash
# Chạy toàn bộ 100% kịch bản kiểm thử tự động
php artisan test

# Kiểm tra riêng bộ kiểm thử PWA & Thông báo đẩy (11 assertions)
php artisan test --compact tests/Feature/PwaAndPushNotificationTest.php

# Kiểm tra kịch bản xung đột lịch và sự kiện lặp
php artisan test --compact tests/Feature/SuKienConflictTest.php
php artisan test --compact tests/Feature/SuKienRepeatTest.php

# Quét và định dạng mã nguồn theo chuẩn PSR-12 (Laravel Pint)
vendor/bin/pint --dirty --format agent

# Thử nghiệm ép gửi thông báo nhắc lịch ngay lập tức (cửa sổ 30 phút)
php artisan push:send-reminders --window=30
```

---

## 📂 7. Cấu Trúc Thư Mục Chính

```text
life-planner/
├── app/
│   ├── Console/Commands/        # Các lệnh Artisan tùy biến (VAPID key, Send Push Reminders)
│   ├── Http/Controllers/        # Controllers xử lý nghiệp vụ (Lịch, Môn học, Tập luyện, Chat...)
│   │   └── Api/                 # RESTful API Controllers cho Mobile App (Sanctum)
│   └── Models/                  # Eloquent Models (User, SuKien, MonHoc, KeHoachTapLuyen, CongViec...)
├── database/
│   ├── migrations/              # 34 migrations quản lý cấu trúc cơ sở dữ liệu
│   └── seeders/                 # Dữ liệu mẫu (DatabaseSeeder, UserSeeder, GiaSuSeeder...)
├── flutter_app/                 # Ứng dụng di động Flutter Material 3
├── public/
│   ├── css/                     # Tệp CSS giao diện tùy biến (dashboard, welcome, login...)
│   ├── js/                      # JavaScript xử lý PWA, Web Push, Chat
│   ├── icons/                   # Bộ icon PWA đa kích thước (192x192, 512x512, maskable)
│   ├── manifest.json            # Cấu hình PWA W3C Manifest
│   └── sw.js                    # Service Worker xử lý Push Notification & Offline Caching
├── resources/views/             # Giao diện Blade Views (calendar, mon_hoc, tap_luyen, cong_viec...)
├── routes/
│   ├── web.php                  # Web Routes (Toàn bộ trang ứng dụng quản lý)
│   ├── api.php                  # RESTful API Endpoints cho Flutter App
│   └── console.php              # Lên lịch tự động (Schedule push:send-reminders)
└── tests/Feature/               # Bộ test tự động kiểm tra toàn bộ logic hệ thống
```

---

## 🎯 8. Hướng Phát Triển Tiếp Theo

- [ ] **Tích hợp Trợ lý Trí tuệ Nhân tạo (AI Coach)**: Tự động phân bổ thời khóa biểu tối ưu và gợi ý bài tập theo thể trạng sinh viên.
- [ ] **Đồng bộ hóa Google Calendar & Canvas LMS**: Cho phép sinh viên nhập lịch học tự động từ cổng đào tạo nhà trường.
- [ ] **Đưa ứng dụng Flutter lên Ch Play / App Store**: Đóng gói và phát hành ứng dụng di động chính thức.

---

## 📄 9. Bản Quyền & Giấy Phép

Dự án được phát triển cho mục đích học tập và nghiên cứu trong môn học **Chuyên Đề PHP**.  
Mã nguồn mở theo giấy phép [MIT License](LICENSE).
