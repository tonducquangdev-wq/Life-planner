import 'package:flutter/material.dart';

/// Filter Chips Component chuẩn thiết kế tham chiếu (Tất cả, Học tập, Tập luyện, Deadline)
class FilterChips extends StatefulWidget {
  final Function(String category)? onCategorySelected;

  const FilterChips({super.key, this.onCategorySelected});

  @override
  State<FilterChips> createState() => _FilterChipsState();
}

class _FilterChipsState extends State<FilterChips> {
  String _selected = 'Tất cả';

  final List<Map<String, dynamic>> _categories = [
    {'id': 'Tất cả', 'label': 'Tất cả', 'icon': Icons.check_rounded, 'color': const Color(0xFF1D4ED8)},
    {'id': 'Học tập', 'label': 'Học tập', 'emoji': '📚', 'color': Colors.blue},
    {'id': 'Tập luyện', 'label': 'Tập luyện', 'emoji': '🏋️', 'color': Colors.amber},
    {'id': 'Deadline', 'label': 'Deadline', 'emoji': '🚨', 'color': Colors.redAccent},
    {'id': 'Cá nhân', 'label': 'Cá nhân', 'emoji': '🎉', 'color': Colors.purple},
    {'id': 'Công việc', 'label': 'Công việc', 'emoji': '💼', 'color': Colors.indigo},
  ];

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 40,
      child: ListView.separated(
        padding: const EdgeInsets.symmetric(horizontal: 18.0),
        scrollDirection: Axis.horizontal,
        itemCount: _categories.length,
        separatorBuilder: (_, __) => const SizedBox(width: 8),
        itemBuilder: (context, index) {
          final cat = _categories[index];
          final isSelected = cat['id'] == _selected;

          return GestureDetector(
            onTap: () {
              setState(() {
                _selected = cat['id'] as String;
              });
              widget.onCategorySelected?.call(_selected);
            },
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 180),
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              decoration: BoxDecoration(
                color: isSelected
                    ? (cat['id'] == 'Deadline' ? const Color(0xFFFEE2E2) : const Color(0xFF1D4ED8))
                    : (cat['id'] == 'Deadline' ? const Color(0xFFFEF2F2) : Colors.white),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(
                  color: isSelected
                      ? Colors.transparent
                      : (cat['id'] == 'Deadline' ? const Color(0xFFFCA5A5) : Colors.grey.shade200),
                ),
                boxShadow: isSelected && cat['id'] != 'Deadline'
                    ? [
                        BoxShadow(
                          color: const Color(0xFF1D4ED8).withAlpha(80),
                          blurRadius: 8,
                          offset: const Offset(0, 3),
                        )
                      ]
                    : null,
              ),
              child: Row(
                children: [
                  if (isSelected && cat['id'] == 'Tất cả')
                    const Icon(Icons.check_rounded, size: 16, color: Colors.white)
                  else if (cat['emoji'] != null)
                    Text(cat['emoji'] as String, style: const TextStyle(fontSize: 14)),
                  const SizedBox(width: 6),
                  Text(
                    cat['label'] as String,
                    style: TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.bold,
                      color: isSelected
                          ? (cat['id'] == 'Deadline' ? const Color(0xFF991B1B) : Colors.white)
                          : (cat['id'] == 'Deadline' ? const Color(0xFF991B1B) : const Color(0xFF0F172A)),
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
