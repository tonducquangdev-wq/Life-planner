import 'package:flutter/material.dart';

/// Event Item Model cho Timeline Event Cards
class TimelineEventItem {
  final String id;
  final String badgeText;
  final String timeText;
  final String title;
  final String locationText;
  final IconData locationIcon;
  final Color accentColor;
  final bool isUrgent;

  const TimelineEventItem({
    required this.id,
    required this.badgeText,
    required this.timeText,
    required this.title,
    required this.locationText,
    required this.locationIcon,
    required this.accentColor,
    this.isUrgent = false,
  });
}

/// Timeline Event Card Component chuẩn thiết kế tham chiếu
class EventCard extends StatelessWidget {
  final TimelineEventItem item;
  final VoidCallback? onTap;
  final VoidCallback? onMoreTap;

  const EventCard({
    super.key,
    required this.item,
    this.onTap,
    this.onMoreTap,
  });

  @override
  Widget build(BuildContext context) {
    // Nếu là sự kiện Gấp/Deadline -> Hiển thị thẻ màu Hồng Nhạt với viền Đỏ
    if (item.isUrgent) {
      return Padding(
        padding: const EdgeInsets.symmetric(horizontal: 18.0, vertical: 5.0),
        child: Container(
          decoration: BoxDecoration(
            color: const Color(0xFFFFF1F2), // Light Red tint
            borderRadius: BorderRadius.circular(22),
            border: Border.all(color: const Color(0xFFFECDD3), width: 1.2),
          ),
          child: Padding(
            padding: const EdgeInsets.all(16.0),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Badges Hàng 1
                      Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                            decoration: BoxDecoration(
                              color: const Color(0xFFE11D48), // Deep Red
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Text(
                              item.badgeText,
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                          const SizedBox(width: 8),
                          Row(
                            children: [
                              const Icon(Icons.access_time_filled_rounded,
                                  size: 14, color: Color(0xFFE11D48)),
                              const SizedBox(width: 4),
                              Text(
                                item.timeText,
                                style: const TextStyle(
                                  color: Color(0xFFE11D48),
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),

                      const SizedBox(height: 8),

                      // Tiêu đề Hạn chót
                      Text(
                        item.title,
                        style: const TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.w800,
                          color: Color(0xFF881337),
                        ),
                      ),

                      const SizedBox(height: 6),

                      // Địa điểm
                      Row(
                        children: [
                          Icon(item.locationIcon, size: 14, color: const Color(0xFF9F1239)),
                          const SizedBox(width: 6),
                          Expanded(
                            child: Text(
                              item.locationText,
                              style: const TextStyle(
                                fontSize: 12,
                                color: Color(0xFF9F1239),
                                fontWeight: FontWeight.w500,
                              ),
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),

                // 3 Dots Menu
                IconButton(
                  onPressed: onMoreTap,
                  icon: const Icon(Icons.more_vert_rounded, color: Color(0xFF881337), size: 20),
                  padding: EdgeInsets.zero,
                  constraints: const BoxConstraints(),
                ),
              ],
            ),
          ),
        ),
      );
    }

    // Các thẻ Sự Kiện Bình Thường (Cá nhân, Tập luyện, Tự học) có Thanh Màu bên Trái (Left Accent Bar)
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 18.0, vertical: 5.0),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(22),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withAlpha(6),
              blurRadius: 10,
              offset: const Offset(0, 3),
            ),
          ],
        ),
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Thanh Accent Color Dọc Bên Trái
              Container(
                width: 4.5,
                height: 56,
                decoration: BoxDecoration(
                  color: item.accentColor,
                  borderRadius: BorderRadius.circular(3),
                ),
              ),

              const SizedBox(width: 14),

              // Nội dung Sự kiện
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Badge & Giờ
                    Row(
                      children: [
                        Text(
                          item.badgeText,
                          style: TextStyle(
                            color: item.accentColor,
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(width: 10),
                        Row(
                          children: [
                            Icon(Icons.access_time_rounded,
                                size: 13, color: Colors.grey.shade600),
                            const SizedBox(width: 4),
                            Text(
                              item.timeText,
                              style: TextStyle(
                                color: Colors.grey.shade700,
                                fontSize: 12,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),

                    const SizedBox(height: 6),

                    // Tiêu đề
                    Text(
                      item.title,
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w800,
                        color: Color(0xFF0F172A),
                      ),
                    ),

                    const SizedBox(height: 6),

                    // Location / Ghi chú
                    Row(
                      children: [
                        Icon(item.locationIcon, size: 14, color: Colors.grey.shade600),
                        const SizedBox(width: 6),
                        Expanded(
                          child: Text(
                            item.locationText,
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.grey.shade600,
                              fontWeight: FontWeight.w500,
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              // 3 Dots Menu
              IconButton(
                onPressed: onMoreTap,
                icon: Icon(Icons.more_vert_rounded, color: Colors.grey.shade700, size: 20),
                padding: EdgeInsets.zero,
                constraints: const BoxConstraints(),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
