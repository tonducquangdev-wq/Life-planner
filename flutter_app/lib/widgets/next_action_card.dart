import 'package:flutter/material.dart';

/// Next Action Card chuẩn thiết kế tham chiếu (Deep Indigo Gradient Card)
class NextActionCard extends StatelessWidget {
  final String statusLabel;
  final String countdownLabel;
  final String courseCode;
  final String title;
  final String locationAndTime;
  final VoidCallback? onPomodoroTap;
  final VoidCallback? onDocsTap;

  const NextActionCard({
    super.key,
    this.statusLabel = 'Đang diễn ra',
    this.countdownLabel = 'Còn 1h 45m',
    this.courseCode = 'IT4060 • HỌC PHẦN',
    this.title = 'Lập trình Web & Di động',
    this.locationAndTime = '📍 Phòng B204 - Giảng đường A  •  08:00 - 10:30',
    this.onPomodoroTap,
    this.onDocsTap,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 18.0, vertical: 6.0),
      child: Container(
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(24),
          gradient: const LinearGradient(
            colors: [
              Color(0xFF1E1B4B), // Deep Navy Indigo
              Color(0xFF312E81),
              Color(0xFF4338CA), // Royal Blue/Purple
            ],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF312E81).withAlpha(100),
              blurRadius: 18,
              offset: const Offset(0, 8),
            ),
          ],
        ),
        child: Padding(
          padding: const EdgeInsets.all(20.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              // Hàng 1: Badge Trạng thái & Countdown Timer
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  // Badge Đang diễn ra
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                    decoration: BoxDecoration(
                      color: Colors.white.withAlpha(35),
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: Colors.white.withAlpha(40)),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Container(
                          width: 7,
                          height: 7,
                          decoration: const BoxDecoration(
                            color: Color(0xFF22C55E), // Green dot
                            shape: BoxShape.circle,
                          ),
                        ),
                        const SizedBox(width: 6),
                        const Icon(Icons.flash_on_rounded, size: 13, color: Colors.amber),
                        const SizedBox(width: 3),
                        Text(
                          statusLabel,
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
                    ),
                  ),

                  // Countdown Pill
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                    decoration: BoxDecoration(
                      color: Colors.black.withAlpha(50),
                      borderRadius: BorderRadius.circular(18),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const Icon(Icons.access_time_rounded, size: 14, color: Colors.white70),
                        const SizedBox(width: 5),
                        Text(
                          countdownLabel,
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),

              const SizedBox(height: 16),

              // Mã môn / Học phần
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: Colors.white.withAlpha(30),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(
                  courseCode,
                  style: const TextStyle(
                    color: Color(0xFFC7D2FE),
                    fontSize: 11,
                    fontWeight: FontWeight.w800,
                    letterSpacing: 0.5,
                  ),
                ),
              ),

              const SizedBox(height: 8),

              // Tên môn học
              Text(
                title,
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 22,
                  fontWeight: FontWeight.w800,
                  letterSpacing: -0.5,
                ),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),

              const SizedBox(height: 8),

              // Địa điểm & Giờ
              Text(
                locationAndTime,
                style: TextStyle(
                  color: Colors.white.withAlpha(210),
                  fontSize: 13,
                  fontWeight: FontWeight.w500,
                ),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),

              const SizedBox(height: 20),

              // 2 Nút Thao Tác
              LayoutBuilder(
                builder: (context, constraints) {
                  return Row(
                    children: [
                      // Nút 1: Focus Pomodoro (Solid White Pill)
                      Expanded(
                        child: ElevatedButton.icon(
                          onPressed: onPomodoroTap,
                          icon: const Icon(Icons.play_circle_outline_rounded, size: 18),
                          label: const Text(
                            'Focus Pomodoro',
                            style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
                          ),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.white,
                            foregroundColor: const Color(0xFF1E1B4B),
                            elevation: 0,
                            padding: const EdgeInsets.symmetric(vertical: 12),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(20),
                            ),
                          ),
                        ),
                      ),

                      const SizedBox(width: 10),

                      // Nút 2: Tài liệu môn (Translucent Outlined Pill)
                      Expanded(
                        child: OutlinedButton.icon(
                          onPressed: onDocsTap,
                          icon: const Icon(Icons.menu_book_rounded, size: 18),
                          label: const Text(
                            'Tài liệu môn',
                            style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
                          ),
                          style: OutlinedButton.styleFrom(
                            foregroundColor: Colors.white,
                            backgroundColor: Colors.white.withAlpha(25),
                            side: BorderSide(color: Colors.white.withAlpha(50), width: 1.2),
                            padding: const EdgeInsets.symmetric(vertical: 12),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(20),
                            ),
                          ),
                        ),
                      ),
                    ],
                  );
                },
              ),
            ],
          ),
        ),
      ),
    );
  }
}
