import 'package:flutter/material.dart';

/// Weekly Calendar Component chuẩn thiết kế tham chiếu (Tháng 10, 2024, Tuần/Tháng toggle, 7 Cột Ngày)
class WeeklyCalendar extends StatefulWidget {
  final DateTime initialDate;
  final Function(DateTime selectedDate)? onDateSelected;
  final Function(bool isWeekView)? onViewModeChanged;

  const WeeklyCalendar({
    super.key,
    required this.initialDate,
    this.onDateSelected,
    this.onViewModeChanged,
  });

  @override
  State<WeeklyCalendar> createState() => _WeeklyCalendarState();
}

class _WeeklyCalendarState extends State<WeeklyCalendar> {
  late DateTime _selectedDate;
  bool _isWeekView = true;

  @override
  void initState() {
    super.initState();
    _selectedDate = widget.initialDate;
  }

  // Dải 7 ngày (T2 -> CN)
  List<Map<String, dynamic>> _getWeekDays() {
    return [
      {'name': 'T2', 'num': 14, 'color': Colors.blue},
      {'name': 'T3', 'num': 15, 'color': Colors.amber},
      {'name': 'T4', 'num': 16, 'color': Colors.white}, // Selected
      {'name': 'T5', 'num': 17, 'color': Colors.redAccent},
      {'name': 'T6', 'num': 18, 'color': Colors.grey},
      {'name': 'T7', 'num': 19, 'color': Colors.green},
      {'name': 'CN', 'num': 20, 'color': Colors.transparent},
    ];
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final colorScheme = theme.colorScheme;
    final weekDays = _getWeekDays();

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 18.0, vertical: 6.0),
      child: Container(
        padding: const EdgeInsets.all(16.0),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(24),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withAlpha(8),
              blurRadius: 14,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Hàng Header: Month Dropdown Title & Segmented Toggle (Tuần / Tháng)
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                // Month Selector Dropdown
                InkWell(
                  onTap: () {},
                  borderRadius: BorderRadius.circular(10),
                  child: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 2),
                    child: Row(
                      children: const [
                        Text(
                          'Tháng 10, 2024',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w800,
                            color: Color(0xFF0F172A),
                          ),
                        ),
                        SizedBox(width: 4),
                        Icon(Icons.keyboard_arrow_down_rounded, size: 20, color: Color(0xFF0F172A)),
                      ],
                    ),
                  ),
                ),

                // Toggle Switch (Tuần / Tháng)
                Container(
                  padding: const EdgeInsets.all(3),
                  decoration: BoxDecoration(
                    color: Colors.grey.shade100,
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Row(
                    children: [
                      _buildToggleBtn('Tuần', _isWeekView, () {
                        setState(() => _isWeekView = true);
                        widget.onViewModeChanged?.call(true);
                      }),
                      _buildToggleBtn('Tháng', !_isWeekView, () {
                        setState(() => _isWeekView = false);
                        widget.onViewModeChanged?.call(false);
                      }),
                    ],
                  ),
                ),
              ],
            ),

            const SizedBox(height: 16),

            // Dải 7 Ngày trong tuần (T2 -> CN)
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: weekDays.map((item) {
                final isSelected = item['num'] == _selectedDate.day;

                return GestureDetector(
                  onTap: () {
                    setState(() {
                      _selectedDate = DateTime(2024, 10, item['num'] as int);
                    });
                    widget.onDateSelected?.call(_selectedDate);
                  },
                  child: AnimatedContainer(
                    duration: const Duration(milliseconds: 200),
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
                    decoration: BoxDecoration(
                      color: isSelected ? const Color(0xFF1D4ED8) : Colors.transparent,
                      borderRadius: BorderRadius.circular(22),
                      boxShadow: isSelected
                          ? [
                              BoxShadow(
                                color: const Color(0xFF1D4ED8).withAlpha(80),
                                blurRadius: 10,
                                offset: const Offset(0, 4),
                              )
                            ]
                          : null,
                    ),
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(
                          item['name'] as String,
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                            color: isSelected ? Colors.white70 : Colors.grey.shade600,
                          ),
                        ),
                        const SizedBox(height: 6),
                        Text(
                          '${item['num']}',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w800,
                            color: isSelected ? Colors.white : const Color(0xFF0F172A),
                          ),
                        ),
                        const SizedBox(height: 6),
                        // Event Dot
                        Container(
                          width: 5,
                          height: 5,
                          decoration: BoxDecoration(
                            color: isSelected ? Colors.white : (item['color'] as Color),
                            shape: BoxShape.circle,
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              }).toList(),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildToggleBtn(String label, bool active, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 180),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
        decoration: BoxDecoration(
          color: active ? Colors.white : Colors.transparent,
          borderRadius: BorderRadius.circular(18),
          boxShadow: active
              ? [
                  BoxShadow(
                    color: Colors.black.withAlpha(15),
                    blurRadius: 6,
                    offset: const Offset(0, 2),
                  )
                ]
              : null,
        ),
        child: Text(
          label,
          style: TextStyle(
            fontSize: 12,
            fontWeight: active ? FontWeight.bold : FontWeight.w600,
            color: active ? const Color(0xFF0F172A) : Colors.grey.shade600,
          ),
        ),
      ),
    );
  }
}
