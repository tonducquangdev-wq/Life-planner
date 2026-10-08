import 'package:flutter/material.dart';
import '../widgets/ai_insight_card.dart';
import '../widgets/bottom_nav.dart';
import '../widgets/dashboard_header.dart';
import '../widgets/event_card.dart';
import '../widgets/filter_chips.dart';
import '../widgets/next_action_card.dart';
import '../widgets/weekly_calendar.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  int _currentBottomNavIndex = 0;
  DateTime _selectedDate = DateTime(2024, 10, 16);

  // Danh sách 4 sự kiện mẫu chuẩn hình ảnh tham chiếu
  final List<TimelineEventItem> _referenceEvents = const [
    TimelineEventItem(
      id: '1',
      badgeText: 'Cá nhân',
      timeText: '11:30 - 12:30',
      title: 'Ăn trưa & Nghỉ ngơi',
      locationText: 'Căng tin sinh viên Tòa H1',
      locationIcon: Icons.restaurant_rounded,
      accentColor: Color(0xFF10B981), // Green Accent
    ),
    TimelineEventItem(
      id: '2',
      badgeText: 'Gấp - Hạn 13:30!',
      timeText: '13:30 Hôm nay',
      title: 'Hạn chót: Nộp đồ án Cơ sở Dữ liệu',
      locationText: 'Hệ thống LMS Khoa CNTT (File .zip + Báo cáo)',
      locationIcon: Icons.description_outlined,
      accentColor: Color(0xFFE11D48),
      isUrgent: true, // Thẻ Đỏ Cảnh Báo Gấp
    ),
    TimelineEventItem(
      id: '3',
      badgeText: 'Tập luyện',
      timeText: '17:00 - 18:15',
      title: 'Gym: Thân trên & Cardio',
      locationText: 'CLB Thể Thao ĐH Bách Khoa',
      locationIcon: Icons.fitness_center_rounded,
      accentColor: Color(0xFFF97316), // Orange Accent
    ),
    TimelineEventItem(
      id: '4',
      badgeText: 'Tự học',
      timeText: '19:30 - 21:00',
      title: 'Ôn tập Giải tích cùng nhóm',
      locationText: 'Thư viện Tạ Quang Bửu (Tầng 3)',
      locationIcon: Icons.account_balance_rounded,
      accentColor: Color(0xFF8B5CF6), // Purple Accent
    ),
  ];

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      body: SafeArea(
        child: RefreshIndicator(
          onRefresh: () async {
            await Future.delayed(const Duration(milliseconds: 500));
            if (mounted) setState(() {});
          },
          child: CustomScrollView(
            physics: const BouncingScrollPhysics(),
            slivers: [
              // 1. Dashboard Header
              SliverToBoxAdapter(
                child: DashboardHeader(
                  userName: 'Minh Triết',
                  dateText: 'Thứ Tư, 16 Tháng 10, 2024',
                  notificationCount: 1,
                  onNotificationTap: () => _showSnackBar(context, 'Mở danh sách Thông báo'),
                  onAvatarTap: () => _showSnackBar(context, 'Mở Hồ sơ cá nhân'),
                ),
              ),

              // 2. Hero Next Action Card
              const SliverToBoxAdapter(
                child: NextActionCard(
                  statusLabel: 'Đang diễn ra',
                  countdownLabel: 'Còn 1h 45m',
                  courseCode: 'IT4060 • HỌC PHẦN',
                  title: 'Lập trình Web & Di động',
                  locationAndTime: '📍 Phòng B204 - Giảng đường A  •  08:00 - 10:30',
                ),
              ),

              const SliverToBoxAdapter(child: SizedBox(height: 4)),

              // 3. Weekly Calendar
              SliverToBoxAdapter(
                child: WeeklyCalendar(
                  initialDate: _selectedDate,
                  onDateSelected: (date) {
                    setState(() {
                      _selectedDate = date;
                    });
                  },
                ),
              ),

              const SliverToBoxAdapter(child: SizedBox(height: 8)),

              // 4. Filter Chips Carousel
              SliverToBoxAdapter(
                child: FilterChips(
                  onCategorySelected: (cat) {
                    _showSnackBar(context, 'Đã lọc theo: $cat');
                  },
                ),
              ),

              const SliverToBoxAdapter(child: SizedBox(height: 14)),

              // Tiêu đề Lịch trình hôm nay + 4 mục + Xem chế độ giờ
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 4.0),
                  child: Row(
                    children: [
                      Text(
                        'Lịch trình hôm nay',
                        style: theme.textTheme.titleMedium?.copyWith(
                          fontWeight: FontWeight.w800,
                          fontSize: 17,
                          color: const Color(0xFF0F172A),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(
                          color: const Color(0xFFE0E7FF),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: const Text(
                          '4 mục',
                          style: TextStyle(
                            color: Color(0xFF4338CA),
                            fontSize: 11,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                      const Spacer(),
                      GestureDetector(
                        onTap: () => _showSnackBar(context, 'Chuyển sang chế độ giờ'),
                        child: Row(
                          children: const [
                            Text(
                              'Xem chế độ giờ',
                              style: TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                                color: Color(0xFF4338CA),
                              ),
                            ),
                            SizedBox(width: 2),
                            Icon(Icons.chevron_right_rounded, size: 16, color: Color(0xFF4338CA)),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ),

              const SliverToBoxAdapter(child: SizedBox(height: 4)),

              // 5. Timeline Event List (4 Thẻ chuẩn hình tham chiếu)
              SliverList(
                delegate: SliverChildBuilderDelegate(
                  (context, index) {
                    final item = _referenceEvents[index];
                    return EventCard(
                      item: item,
                      onTap: () => _showSnackBar(context, 'Chi tiết: ${item.title}'),
                      onMoreTap: () => _showSnackBar(context, 'Thao tác: ${item.title}'),
                    );
                  },
                  childCount: _referenceEvents.length,
                ),
              ),

              const SliverToBoxAdapter(child: SizedBox(height: 8)),

              // 6. AI Insight Card
              SliverToBoxAdapter(
                child: AiInsightCard(
                  onOptimizeTap: () =>
                      _showSnackBar(context, '✨ AI đang tối ưu lịch trình chiều nay...'),
                ),
              ),

              // Thêm khoảng trống đệm phía dưới cho BottomNav
              const SliverToBoxAdapter(child: SizedBox(height: 80)),
            ],
          ),
        ),
      ),

      // 7. Floating Action Button Center Docked (+)
      floatingActionButton: FloatingActionButton(
        onPressed: () => _showSnackBar(context, 'Mở Modal Tạo sự kiện mới'),
        backgroundColor: const Color(0xFF4F46E5),
        foregroundColor: Colors.white,
        elevation: 8,
        shape: const CircleBorder(),
        child: const Icon(Icons.add_rounded, size: 32),
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerDocked,

      // 7. Bottom Navigation 5 Mục
      bottomNavigationBar: MainBottomNav(
        currentIndex: _currentBottomNavIndex,
        onTap: (index) {
          setState(() {
            _currentBottomNavIndex = index;
          });
        },
        onFabTap: () => _showSnackBar(context, 'Mở Modal Tạo sự kiện mới'),
      ),
    );
  }

  void _showSnackBar(BuildContext context, String message) {
    ScaffoldMessenger.of(context).hideCurrentSnackBar();
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        behavior: SnackBarBehavior.floating,
        duration: const Duration(seconds: 2),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12),
        ),
      ),
    );
  }
}
