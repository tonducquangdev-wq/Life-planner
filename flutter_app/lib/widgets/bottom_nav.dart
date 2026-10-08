import 'package:flutter/material.dart';

/// Bottom Navigation Component chuẩn thiết kế tham chiếu (5 mục, FAB Center Docked (+))
class MainBottomNav extends StatelessWidget {
  final int currentIndex;
  final ValueChanged<int> onTap;
  final VoidCallback onFabTap;

  const MainBottomNav({
    super.key,
    required this.currentIndex,
    required this.onTap,
    required this.onFabTap,
  });

  @override
  Widget build(BuildContext context) {
    return BottomAppBar(
      shape: const CircularNotchedRectangle(),
      notchMargin: 8.0,
      elevation: 12,
      padding: EdgeInsets.zero,
      height: 66,
      backgroundColor: Colors.white,
      surfaceTintColor: Colors.white,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          // 1. Lịch biểu
          _buildNavItem(
            index: 0,
            icon: Icons.calendar_month_rounded,
            label: 'Lịch biểu',
          ),

          // 2. Học tập
          _buildNavItem(
            index: 1,
            icon: Icons.school_outlined,
            label: 'Học tập',
          ),

          // Khoảng trống cho FAB Center Docked (+)
          const SizedBox(width: 48),

          // 3. Thói quen
          _buildNavItem(
            index: 2,
            icon: Icons.fitness_center_rounded,
            label: 'Thói quen',
          ),

          // 4. Hồ sơ
          _buildNavItem(
            index: 3,
            icon: Icons.person_outline_rounded,
            label: 'Hồ sơ',
          ),
        ],
      ),
    );
  }

  Widget _buildNavItem({
    required int index,
    required IconData icon,
    required String label,
  }) {
    final isSelected = currentIndex == index;
    final color = isSelected ? const Color(0xFF1D4ED8) : const Color(0xFF64748B);

    return InkWell(
      onTap: () => onTap(index),
      borderRadius: BorderRadius.circular(16),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 3),
              decoration: BoxDecoration(
                color: isSelected ? const Color(0xFFDBEAFE) : Colors.transparent,
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(icon, color: color, size: 20),
            ),
            const SizedBox(height: 2),
            Text(
              label,
              style: TextStyle(
                color: color,
                fontSize: 10.5,
                fontWeight: isSelected ? FontWeight.bold : FontWeight.w500,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
