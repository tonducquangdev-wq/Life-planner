# BÁO CÁO TOÀN DIỆN HỆ THỐNG LIFE PLANNER
## HIỆN TRẠNG CHỨC NĂNG VÀ ĐỊNH HƯỚNG PHÁT TRIỂN

> **Dự án:** Life Planner - Nền tảng quản trị cuộc sống cá nhân, học tập và rèn luyện thể chất  
> **Phiên bản hiện tại:** 1.2.0 (Hỗ trợ Calendar First, Recurring Events, Soft Deletes & Full RESTful API)  
> **Ngôn ngữ & Công nghệ chính:** PHP 8.3+, Laravel Framework 13+, Bootstrap 5.3, Chart.js, Laravel Sanctum, MySQL / SQLite  
> **Ngày lập báo cáo:** 28/09/2026  

---

## MỤC LỤC
1. [Tổng quan dự án & Triết lý thiết kế](#1-tổng-quan-dự-án--triết-lý-thiết-kế)
2. [Kiến trúc hệ thống & Công nghệ sử dụng](#2-kiến-trúc-hệ-thống--công-nghệ-sử-dụng)
3. [Hiện trạng chức năng chi tiết](#3-hiện-trạng-chức-năng-chi-tiết)
   - [3.1. Phân hệ Xác thực & Hồ sơ cá nhân (Auth & User Profile)](#31-phân-hệ-xác-thực--hồ-sơ-cá-nhân-auth--user-profile)
   - [3.2. Phân hệ Lịch cá nhân trung tâm (Calendar First)](#32-phân-hệ-lịch-cá-nhân-trung-tâm-calendar-first)
   - [3.3. Phân hệ Quản lý Học tập & Phân tích GPA](#33-phân-hệ-quản-lý-học-tập--phân-tích-gpa)
   - [3.4. Phân hệ Rèn luyện Thể chất & Theo dõi Thể lực](#34-phân-hệ-rèn-luyện-thể-chất--theo-dõi-thể-lực)
   - [3.5. Hệ thống RESTful API hoàn chỉnh](#35-hệ-thống-restful-api-hoàn-chỉnh)
   - [3.6. Cơ chế Bảo mật, Toàn vẹn dữ liệu & Kiểm thử](#36-cơ-chế-bảo-mật-toàn-vẹn-dữ-liệu--kiểm-thử)
4. [Đánh giá điểm mạnh và Hạn chế hiện tại](#4-đánh-giá-điểm-mạnh-và-hạn-chế-hiện-tại)
5. [Định hướng và Lộ trình phát triển tương lai](#5-định-hướng-và-lộ-trình-phát-triển-tương-lai)
   - [Giai đoạn 1: Tự động hóa thông báo & Đồng bộ Lịch ngoại vi](#giai-đoạn-1-tự-động-hóa-thông-báo--đồng-bộ-lịch-ngoại-vi)
   - [Giai đoạn 2: Trợ lý thông minh AI & Tự động trích xuất lịch](#giai-đoạn-2-trợ-lý-thông-minh-ai--tự-động-trích-xuất-lịch)
   - [Giai đoạn 3: Hệ sinh thái Mobile App & Tích hợp Wearable](#giai-đoạn-3-hệ-sinh-thái-mobile-app--tích-hợp-wearable)
   - [Giai đoạn 4: Xã hội hóa & Game hóa (Gamification)](#giai-đoạn-4-xã-hội-hóa--game-hóa-gamification)

---

## 1. TỔNG QUAN DỰ ÁN & TRIẾT LÝ THIẾT KẾ

**Life Planner** là nền tảng quản trị cuộc sống số toàn diện (All-in-one Life OS) hướng đến đối tượng người dùng là sinh viên, người đi làm và những cá nhân mong muốn duy trì lối sống kỷ luật, khoa học. 

Khác với các ứng dụng rời rạc trên thị trường (chỉ quản lý công việc Todo-list đơn giản hoặc chỉ đếm bài tập gym), Life Planner kết nối 3 trụ cột thiết yếu nhất của một cá nhân:
1. **Thời gian biểu (Calendar First):** Mọi công việc, sự kiện, hạn chót đều được quy tụ về một trục thời gian trực quan duy nhất.
2. **Năng lực trí tuệ (Học tập & Deadline):** Theo dõi môn học, tiến độ học tập, bài tập cần nộp, tính điểm trung bình tích lũy GPA tự động theo tín chỉ.
3. **Sức khỏe thể chất (Fitness & Thói quen):** Thiết lập giáo án tập luyện theo tuần (PPL, Upper-Lower, Cardio), ghi nhận nhật ký tập, đo lường chuỗi ngày rèn luyện liên tục (Streak).

---

## 2. KIẾN TRÚC HỆ THỐNG & CÔNG NGHỆ SỬ DỤNG

### 2.1. Backend
- **Framework:** Laravel 13.x (PHP 8.3+), áp dụng chuẩn kiến trúc MVC kết hợp RESTful API Resource.
- **Xác thực (Authentication):**
  - Session-based authentication cho giao diện Web (Laravel Breeze).
  - Token-based authentication cho REST API qua **Laravel Sanctum**.
- **Cơ sở dữ liệu:**
  - Hệ quản trị CSDL quan hệ MySQL (môi trường Production / Dev).
  - SQLite in-memory cho môi trường Automated Testing.
  - Sử dụng Eloquent ORM, Database Migrations, Seeder và Foreign Key Constraints.

### 2.2. Frontend
- **Template Engine:** Laravel Blade Templates.
- **Giao diện & Thành phần UI:** Bootstrap 5.3.3 kết hợp Bootstrap Icons 1.11.3.
- **Typography:** Google Fonts (`Plus Jakarta Sans`) chuẩn quốc tế.
- **Hiệu ứng & Trải nghiệm (UX):**
  - Kịch bản **Anti-Flicker Script** ngăn chặn hiện tượng nhấp nháy khi tải giao diện Dark/Light mode.
  - Sidebar dạng **Desktop Fixed** trên màn hình lớn và **Offcanvas Sidebar** tiện dụng trên thiết bị di động / máy tính bảng (< 992px).
  - Biểu đồ thống kê chuyên sâu với **Chart.js** (Line Chart, Bar Chart, Doughnut Chart).
- **Styling:** Vanilla CSS tối ưu hóa theo từng trang (`dashboard.css`, `calendar.css`, `tap-luyen.css`, `welcome.css`, `login.css`).

---

## 3. HIỆN TRẠNG CHỨC NĂNG CHI TIẾT

```
+-------------------------------------------------------------------------------+
|                            HỆ THỐNG LIFE PLANNER                              |
+-------------------+--------------------+------------------+-------------------+
|  1. LỊCH CÁ NHÂN  | 2. QUẢN LÝ HỌC TẬP | 3. RÈN LUYỆN GYM | 4. HỒ SƠ & BẢO MẬT|
|  - Lưới tháng     | - CRUD Môn học     | - Kế hoạch tuần  | - Dark / Light    |
|  - 4 Loại sự kiện | - Tính GPA 10 & 4  | - Buổi tập PPL   | - Đổi mật khẩu    |
|  - Lặp tự động    | - Quản lý deadline | - Streak rèn     | - Avatar upload   |
|  - Nhắc nhở email | - Xếp loại học lực | - Heatmap tháng  | - API Sanctum     |
+-------------------+--------------------+------------------+-------------------+
```

### 3.1. Phân hệ Xác thực & Hồ sơ cá nhân (Auth & User Profile)
- **Đầy đủ chu trình bảo mật tài khoản:**
  - Đăng ký tài khoản, Đăng nhập, Ghi nhớ phiên đăng nhập (`remember_token`).
  - Quên mật khẩu & Gửi link đặt lại mật khẩu qua email token an toàn.
  - Xác thực Email (`MustVerifyEmail`), Xác nhận mật khẩu trước các thao tác nhạy cảm.
  - Thay đổi mật khẩu mới, Xóa tài khoản vĩnh viễn (yêu cầu mật khẩu xác nhận).
- **Quản lý thông tin & Ảnh đại diện (Avatar):**
  - Tải lên ảnh đại diện định dạng JPG, PNG, WEBP (tối đa 2MB), lưu trữ trên `storage/app/public/avatars/`.
  - Tự động dọn dẹp ảnh cũ trong bộ nhớ khi cập nhật ảnh mới.
  - Fallback thông minh: Tự động trích xuất 2 chữ cái viết tắt của tên người dùng (Initials) hiển thị khi chưa có ảnh, ngăn chặn lỗi vỡ layout.
- **Cá nhân hóa giao diện (Dark / Light Mode):**
  - Chuyển đổi linh hoạt giữa giao diện Sáng, Tối và Theo hệ điều hành (`system`).
  - Nút chuyển đổi nhanh một chạm trên thanh Topbar và Sidebar lưu cấu hình qua AJAX.
- **Cấu hình thông báo (Notification Settings):**
  - Công tắc bật/tắt toàn bộ thông báo (Master Switch).
  - Tùy chọn chi tiết từng danh mục: Nhắc lịch học, Nhắc deadline, Nhắc tập luyện, Âm thanh thông báo.

---

### 3.2. Phân hệ Lịch cá nhân trung tâm (Calendar First)
*Đường dẫn truy cập: `/calendar` (Mặc định khi truy cập root `/` hoặc `/dashboard` đều được chuyển hướng tại đây).*

- **Lưới lịch tháng thông minh (Interactive Calendar Grid):**
  - Hiển thị trực quan 35 ô ngày theo tháng, làm nổi bật ngày hiện tại ("Hôm nay").
  - Phân loại màu sắc nhận diện tức thì cho 4 nhóm sự kiện:
    - 📘 **Lịch học (`hoc-tap`):** Môn học, giờ lên lớp, giảng viên, phòng học.
    - 🏋️ **Lịch tập (`tap-luyen`):** Buổi tập thể chất, số bài tập, thời gian.
    - ⏰ **Deadline (`deadline`):** Hạn nộp bài tập lớn, đồ án, lịch thi cử.
    - 🎉 **Sự kiện cá nhân (`ca-nhan`):** Họp nhóm, sinh hoạt ngoại khóa, ngày lễ.
- **Tạo mới sự kiện đa năng (Modal 4 Tab chuyên biệt):**
  - Form thêm mới được chia thành 4 Tab riêng biệt với màu sắc và trường thông tin phù hợp cho từng loại sự kiện.
  - Hỗ trợ chọn giờ bắt đầu, giờ kết thúc (có kiểm tra tính hợp lệ `thoi_gian_ket_thuc >= thoi_gian_bat_dau`).
- **Quy tắc lặp sự kiện tự động (Recurring Events - Cập nhật mới nhất):**
  - Hỗ trợ thiết lập chuỗi lặp: Hằng ngày (`daily` - tạo 30 ngày), Hằng tuần (`weekly` - tạo 12 tuần), Hằng tháng (`monthly` - tạo 6 tháng) hoặc sự kiện đơn lẻ (`once`).
  - Gom nhóm các buổi trong chuỗi bằng mã định danh duy nhất `nhom_lap_id` (UUID).
- **Chỉnh sửa & Xóa hàng loạt linh hoạt (Single vs. Series):**
  - Khi cập nhật hoặc xóa sự kiện lặp, hệ thống cho phép người dùng lựa chọn:
    - `single`: Chỉ cập nhật/xóa buổi được chọn.
    - `all`: Cập nhật đồng loạt các thông số dùng chung (tiêu đề, màu sắc, thông báo, ghi chú) hoặc xóa toàn bộ các buổi trong chuỗi.
- **Cài đặt nhắc nhở & Gửi thông báo:**
  - Bật/tắt thông báo cho từng sự kiện.
  - Tùy chọn nhắc trước 1 ngày hoặc trước 2 ngày. Có sẵn Eloquent Scope `canNhacThongBao` để phục vụ tác vụ gửi email tự động.
- **Cột thông tin tổng hợp nhanh (Dashboard Sidebar Right 25%):**
  - **Lịch trình hôm nay:** Danh sách chi tiết các công việc, lớp học, buổi tập cần thực hiện trong ngày.
  - **Sắp đến hạn:** Danh sách các bài tập / sự kiện có hạn chót gần nhất (kèm badge đếm ngược số ngày còn lại).
  - **Thống kê nhanh:** Số môn học trong tuần, số buổi tập trong tuần, số deadline chưa nộp.

---

### 3.3. Phân hệ Quản lý Học tập & Phân tích GPA
*Đường dẫn truy cập: `/mon-hoc` và `/bao-cao-hoc-tap`.*

- **Quản lý danh mục môn học (CRUD):**
  - Mã môn, Tên môn học, Giảng viên phụ trách, Phòng học, Số tín chỉ (1 - 20).
  - Thanh tiến độ học tập (0 - 100%), Điểm số đạt được (thang điểm 10).
  - Ngày bắt đầu và Ngày kết thúc môn học. Hệ thống trang bị bộ xử lý `normalizeDate` tự động đồng bộ giữa chuẩn Việt Nam (`dd/mm/yyyy`) và chuẩn ISO (`yyyy-mm-dd`).
  - Trạng thái môn học: Đang học (`dang_hoc`), Đã hoàn thành (`da_hoan_thanh`), Tạm dừng (`tam_dung`).
  - Màu sắc đại diện cho từng môn học để dễ dàng phân biệt trên lịch biểu.
- **Quản lý Bài tập & Deadline môn học:**
  - Tạo bài tập gắn trực tiếp với môn học tương ứng.
  - Phân cấp mức độ ưu tiên: Cao (`cao`), Trung bình (`trung_binh`), Thấp (`thap`).
  - Trạng thái thực hiện: Chưa hoàn thành, Đang thực hiện, Đã hoàn thành.
- **Trang Báo cáo Học tập & Thống kê GPA chuyên sâu:**
  - Tự động cung cấp dữ liệu mẫu sinh động nếu tài khoản mới khởi tạo chưa có dữ liệu.
  - **Tính toán GPA tự động:** Áp dụng công thức tính điểm trung bình tích lũy có trọng số theo số tín chỉ trên cả Thang điểm 10 (`gpa10`) và Thang điểm 4 (`gpa4`).
  - **Tự động xếp loại học lực học thuật:** Xuất sắc ($\ge 3.6$), Giỏi ($\ge 3.2$), Khá ($\ge 2.5$), Trung bình ($\ge 2.0$), Yếu / Kém ($< 2.0$).
  - Thống kê tỷ lệ hoàn thành bài tập, tổng số tín chỉ tích lũy, số tín chỉ đã vượt qua.
  - Biểu đồ phân bổ điểm số và tiến độ từng môn học bằng Chart.js.

---

### 3.4. Phân hệ Rèn luyện Thể chất & Theo dõi Thể lực
*Đường dẫn truy cập: `/tap-luyen` và `/bao-cao-tap-luyen`.*

- **Quản lý Kế hoạch rèn luyện (Workout Plans):**
  - Tạo mới nhiều kế hoạch tập luyện khác nhau (VD: Kế hoạch PPL, Kế hoạch Tăng cơ giảm mỡ, Cardio...).
  - Kích hoạt kế hoạch đang theo đuổi (`is_active = true`), tự động chuyển đổi giữa các kế hoạch.
- **Thiết lập Lịch tập 7 ngày trong tuần (Data-driven Weekly Schedule):**
  - Phân bổ các buổi tập vào từng ngày cụ thể từ Thứ 2 đến Chủ Nhật (chuẩn ISO-8601).
  - Tự động nhận diện những ngày không có lịch là **Ngày Nghỉ (Rest Day)** để phục hồi cơ bắp.
- **Quản lý Chi tiết Bài tập (Exercise Details):**
  - Đa dạng các nhóm bài tập: Sức mạnh (`strength`), Tim mạch (`cardio`), Cơ bụng / Trọng tâm (`core`).
  - Lưu trữ thông số chuyên nghiệp: Số sets, Số reps, Thời lượng (phút/giây).
  - Tự động tạo chuỗi hiển thị thông số thông minh (VD: `4 sets × 8–10 reps`, `30 phút`).
  - Thao tác thêm/sửa/xóa bài tập tức thì qua AJAX và lưu trữ an toàn trong DB Transaction.
- **Ghi nhận Lịch sử & Đo lường Chuỗi ngày kỷ luật (Streak Tracking):**
  - Đánh dấu hoàn thành buổi tập kèm ghi nhận thời gian tập thực tế (phút) và nhật ký.
  - Thuật toán tính toán chuỗi ngày rèn luyện liên tục (Workout Streak) dựa trên các mốc thời gian thực trong CSDL.
  - Thống kê số buổi tập tuần này, thời lượng tập trong ngày, tổng số buổi tích lũy.
- **Báo cáo Thể chất & Heatmap rèn luyện:**
  - **Bản đồ nhiệt hoạt động theo tháng (Monthly Heatmap Grid):** Trực quan hóa tần suất tập luyện từng ngày trong tháng với 4 cấp độ màu sắc (Lvl 0 - Lvl 3) tương tự phong cách GitHub Contribution Graph.
  - **Biểu đồ phân bổ nhóm cơ:** Tỷ lệ phần trăm giữa các nhóm bài tập (Push, Pull, Legs, Cardio, Khác).
  - Biểu đồ tần suất tập luyện phân bổ theo các ngày trong tuần (T2 - CN).
  - Bảng nhật ký ghi lại 15 buổi tập gần nhất.

---

### 3.5. Hệ thống RESTful API hoàn chỉnh
Hệ thống cung cấp trọn bộ RESTful API chuẩn mực tại tiền tố `/api/`, hỗ trợ giao tiếp với Mobile App hoặc SPA:

| Nhóm API | Endpoint | Phương thức | Mô tả chức năng |
| :--- | :--- | :---: | :--- |
| **Auth** | `/api/login` | POST | Đăng nhập lấy Bearer Token (Sanctum) |
| | `/api/logout` | POST | Hủy Token đăng nhập hiện tại |
| | `/api/me`, `/api/user`| GET | Lấy thông tin tài khoản đang đăng nhập |
| **Dashboard**| `/api/dashboard` | GET | Tổng hợp số liệu KPI toàn hệ thống |
| **Môn học** | `/api/mon-hoc` | GET, POST | Lấy danh sách / Thêm mới môn học |
| | `/api/mon-hoc/{id}` | GET, PUT, DELETE | Chi tiết, cập nhật, xóa môn học |
| **Bài tập** | `/api/bai-tap` | GET, POST | Lấy danh sách / Tạo mới bài tập deadline |
| | `/api/bai-tap/{id}` | GET, PUT, DELETE | Cập nhật tiến độ, sửa, xóa bài tập |
| **Sự kiện** | `/api/su-kien` | GET, POST | Lấy danh sách / Tạo sự kiện lịch |
| | `/api/su-kien/{id}` | GET, PUT, DELETE | Cập nhật, xóa sự kiện (hỗ trợ mode all/single) |
| **Lịch học** | `/api/lich-hoc` | GET, POST | Lấy / Cập nhật thời khóa biểu môn học |
| **Tập luyện** | `/api/tap-luyen/schedule` | GET | Lấy lịch trình tập luyện tuần hiện tại |
| | `/api/tap-luyen/cap-nhat-buoi-tap`| POST | Cập nhật cấu trúc bài tập của buổi tập |
| | `/api/tap-luyen/hoan-thanh` | POST | Ghi nhận hoàn thành buổi tập vào lịch sử |
| | `/api/ke-hoach-tap-luyen` | GET, POST | Quản lý kế hoạch tập luyện tổng thể |
| | `/api/ke-hoach-tap-luyen/{id}/kich-hoat` | POST | Kích hoạt kế hoạch tập luyện |

---

### 3.6. Cơ chế Bảo mật, Toàn vẹn dữ liệu & Kiểm thử
- **Chống lỗi kiểm soát truy cập (Anti-IDOR):** Mọi Model khi truy vấn, cập nhật hoặc xóa đều được ràng buộc chặt chẽ với ID của người dùng đang xác thực (`user_id = Auth::id()`). Người dùng tuyệt đối không thể xem hoặc sửa dữ liệu của người khác.
- **Xóa mềm (Soft Deletes):** Tích hợp Soft Deletes cho cả bảng `su_kien` và `mon_hoc`, tránh mất mát dữ liệu do thao tác bấm nhầm.
- **Rate Limiting:** Giới hạn tần suất gọi API đăng nhập (`throttle:login`) phòng chống tấn công dò mật khẩu Brute-force.
- **Automated Testing:** Bộ test tự động (PHPUnit / Pest) trong thư mục `tests/Feature/` bao gồm:
  - `AuthenticationTest`, `PasswordResetTest`, `RegistrationTest`, `EmailVerificationTest`.
  - `MonHocDateValidationTest`: Kiểm tra độ chuẩn hóa các định dạng ngày tháng.
  - `ProfileTest`: Kiểm tra cập nhật hồ sơ, avatar, toggle dark mode và notification.
  - `SuKienRepeatTest`: Kiểm tra tính năng tạo chuỗi lặp sự kiện và sửa/xóa hàng loạt.
  - `TapLuyenTest`, `TapLuyenCustomScheduleTest`, `TapLuyenApiTest`: Kiểm thử tính năng lịch tập, streak và API tập luyện.

---

## 4. ĐÁNH GIÁ ĐIỂM MẠNH VÀ HẠN CHẾ HIỆN TẠI

### Điểm mạnh
1. **Thiết kế UI/UX xuất sắc:** Giao diện trực quan, phối màu hài hòa, hỗ trợ chế độ Dark Mode chuẩn xác, responsive hoàn chỉnh trên cả Desktop lẫn Mobile.
2. **Khả năng tích hợp độc đáo (All-in-one):** Giải quyết đồng thời cả bài toán quản lý thời gian, học tập và thể chất của một người trẻ trên một nền tảng duy nhất.
3. **Độ ổn định & Tính tin cậy cao:** Kiến trúc dữ liệu chuẩn mực, có xử lý ngoại lệ đầy đủ (như fallback Avatar, chuẩn hóa định dạng ngày).
4. **Hệ thống API đã sẵn sàng:** Không cần phải viết lại backend khi phát triển ứng dụng di động trong tương lai.

### Hạn chế cần cải thiện
1. **Thông báo mới ở dạng In-App tĩnh:** Hệ thống đã lưu trữ các cài đặt thông báo và có trường dữ liệu nhắc trước, nhưng chưa có Cron Job tự động gửi email hoặc thông báo đẩy (Web Push) thời gian thực đến thiết bị của người dùng.
2. **Chưa có chức năng Nhập/Xuất dữ liệu Lịch (iCal / Google Calendar):** Người dùng vẫn phải nhập thủ công từng sự kiện mà chưa thể đồng bộ một chạm với Google Calendar hay tải file `.ics` lịch thi của trường học.
3. **Chưa có Trợ lý gợi ý thông minh:** Kế hoạch tập luyện và phân bổ thời gian học vẫn do người dùng tự sắp xếp thủ công, chưa có tính năng AI đề xuất lịch biểu tối ưu.

---

## 5. ĐỊNH HƯỚNG VÀ LỘ TRÌNH PHÁT TRIỂN TƯƠNG LAI

```
                  LỘ TRÌNH PHÁT TRIỂN HỆ THỐNG LIFE PLANNER
                  
  +-------------------------------------------------------------------------+
  | GIAI ĐOẠN 1: HOÀN THIỆN LÕI & TỰ ĐỘNG HÓA (1 - 2 tháng)                 |
  | - Cron job gửi Email nhắc lịch học, deadline hàng ngày                  |
  | - Đồng bộ 2 chiều với Google Calendar & Apple Calendar (iCal .ics)      |
  | - Web Push Notifications thời gian thực với Laravel Reverb              |
  +-------------------------------------------------------------------------+
                                      |
                                      v
  +-------------------------------------------------------------------------+
  | GIAI ĐOẠN 2: TRÍ TUỆ NHÂN TẠO & TỰ ĐỘNG HÓA DỮ LIỆU (3 - 4 tháng)       |
  | - AI Smart Planner (Gemini API): Tự động lên lịch học & giáo án tập gym |
  | - OCR Scanner: Chụp ảnh Thời khóa biểu trường / Giáo trình tự tạo lịch  |
  | - Dự báo điểm thi & Cảnh báo rủi ro học thuật dựa trên tiến độ          |
  +-------------------------------------------------------------------------+
                                      |
                                      v
  +-------------------------------------------------------------------------+
  | GIAI ĐOẠN 3: MOBILE APP & KẾT NỐI WEARABLE (5 - 6 tháng)                |
  | - Ra mắt Mobile App đa nền tảng (Flutter / React Native) dùng chung API |
  | - Đồng bộ bước chân, calo, nhịp tim từ Apple Health & Google Fit        |
  | - Hỗ trợ chế độ Offline-first (PWA)                                     |
  +-------------------------------------------------------------------------+
                                      |
                                      v
  +-------------------------------------------------------------------------+
  | GIAI ĐOẠN 4: CỘNG ĐỒNG & GAME HÓA (GAMIFICATION) (6+ tháng)             |
  | - Hệ thống tính điểm kỷ luật XP, Huy hiệu vinh danh, Cấp bậc sinh viên  |
  | - Thách đấu chuỗi ngày tập luyện (Streak Challenge) cùng bạn bè         |
  | - Nhóm học tập chia sẻ đề cương, tài liệu và tiến độ chung              |
  +-------------------------------------------------------------------------+
```

### Giai đoạn 1: Hoàn thiện Lõi & Tự động hóa Thông báo (1 - 2 tháng)
1. **Tự động hóa gửi Email & Web Push:**
   - Cấu hình Laravel Task Scheduler (`app/Console/Kernel.php` hoặc `routes/console.php`) chạy ngầm mỗi sáng 07:00.
   - Quét các sự kiện có `bat_thong_bao = true` và khớp với `so_ngay_nhac` để gửi email nhắc nhở kèm bảng tổng hợp công việc trong ngày.
   - Tích hợp thông báo đẩy trình duyệt (Web Push Notifications qua Service Worker).
2. **Đồng bộ Lịch ngoại vi (Google Calendar & iCal):**
   - Cung cấp nút xuất lịch biểu ra file `.ics` chuẩn quốc tế.
   - Tích hợp Google Calendar API (OAuth 2.0) cho phép đồng bộ 2 chiều: khi tạo lịch trên Life Planner, sự kiện tự động xuất hiện trên điện thoại Android/iOS của người dùng.
3. **Bộ đếm thời gian tập trung (Pomodoro Focus Timer):**
   - Tích hợp đồng hồ đếm ngược Pomodoro (25 phút học, 5 phút nghỉ) trực tiếp trên trang Lịch hoặc Môn học, ghi nhận thời gian tự học vào báo cáo học tập.

### Giai đoạn 2: Trợ lý Thông minh AI & Tự động hóa Dữ liệu (3 - 4 tháng)
1. **AI Smart Scheduling (Tích hợp Google Gemini API):**
   - Người dùng chỉ cần nhập: *"Tôi muốn tập gym 4 buổi/tuần theo giáo án Push-Pull-Legs và cần ôn thi 3 môn Toán rời rạc, Lập trình Web, Cơ sở dữ liệu"*. Trợ lý AI sẽ tự động phân tích thời gian trống trên Lịch và xếp lịch học, lịch tập tối ưu nhất, không bị trùng lịch.
2. **Quét thời khóa biểu từ Ảnh / PDF (OCR Syllabus Scanner):**
   - Sinh viên chỉ cần chụp ảnh màn hình Thời khóa biểu của cổng đào tạo trường Đại học; hệ thống tự động bóc tách tên môn, phòng học, giảng viên, thứ trong tuần và lưu vào CSDL chỉ trong vài giây.
3. **Cảnh báo sớm kết quả học tập (Academic Risk Alert):**
   - Dựa trên tiến độ bài tập và điểm thành phần, thuật toán sẽ dự báo điểm chữ (A, B, C, D, F) và đưa ra cảnh báo nếu người dùng đang có nguy cơ không đạt chuẩn điểm môn học.

### Giai đoạn 3: Hệ sinh thái Mobile App & Kết nối Wearable (5 - 6 tháng)
1. **Phát triển Ứng dụng Di động Native/Cross-platform:**
   - Xây dựng ứng dụng di động bằng **Flutter** hoặc **React Native** kết nối trực tiếp với hệ sinh thái RESTful API đã có sẵn (`/api/*`).
   - Tận dụng thông báo Notification cục bộ của hệ điều hành iOS/Android để nhắc nhở mà không tốn chi phí SMS/Email.
2. **Tích hợp Apple Health & Google Fit:**
   - Tự động lấy dữ liệu bước chân, calo tiêu hao hàng ngày, thời lượng vận động thực tế từ đồng hồ thông minh (Apple Watch, Garmin, Galaxy Watch) đưa vào biểu đồ thể chất của Life Planner.
3. **Hỗ trợ Offline-first (Progressive Web App - PWA):**
   - Cho phép người dùng đánh dấu hoàn thành bài tập, xem lịch ngay cả khi ở phòng gym tầng hầm không có sóng Internet.

### Giai đoạn 4: Cộng đồng & Game hóa (Gamification) (6+ tháng)
1. **Hệ thống Điểm kinh nghiệm (XP) & Huy hiệu:**
   - Thiết kế cơ chế thưởng điểm khi người dùng hoàn thành deadline trước hạn, duy trì chuỗi Streak tập luyện 7 ngày, 30 ngày liên tục.
   - Hệ thống cấp bậc vinh danh (Tân binh $\rightarrow$ Chiến binh Kỷ luật $\rightarrow$ Bậc thầy Cân bằng cuộc sống).
2. **Bạn đồng hành & Thách đấu (Accountability Partner):**
   - Cho phép 2 hoặc nhiều người bạn kết nối với nhau để cùng theo dõi tiến độ học tập và rèn luyện. Khi một người bỏ tập hoặc trễ hạn deadline, hệ thống sẽ gửi thông báo khích lệ cho đối phương.

---

## 6. KẾT LUẬN

Dự án **Life Planner** hiện tại đã đạt độ hoàn thiện rất cao ở tầng tính năng cốt lõi (Core Features), giao diện người dùng đạt tiêu chuẩn hiện đại, responsive mượt mà và nền tảng dữ liệu vững chắc với đầy đủ hệ thống RESTful API, cơ chế bảo mật Anti-IDOR, xử lý Soft Deletes và lặp sự kiện thông minh.

Với lộ trình phát triển rõ ràng từ việc tự động hóa thông báo, tích hợp AI đến phát triển ứng dụng di động, **Life Planner** có đầy đủ tiềm năng để phát triển thành một sản phẩm công nghệ hoàn chỉnh, mang lại giá trị thiết thực và lâu dài cho người sử dụng.
