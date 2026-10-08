import 'package:flutter/material.dart';
import '../theme/app_theme.dart';

enum EventCategory {
  all('Tất cả', Icons.grid_view_rounded, AppTheme.primaryColor),
  study('Học tập', Icons.menu_book_rounded, AppTheme.colorStudy),
  workout('Tập luyện', Icons.fitness_center_rounded, AppTheme.colorWorkout),
  deadline('Deadline', Icons.timer_outlined, AppTheme.colorDeadline),
  personal('Cá nhân', Icons.person_outline_rounded, AppTheme.colorPersonal),
  work('Công việc', Icons.work_outline_rounded, AppTheme.colorWork);

  final String label;
  final IconData icon;
  final Color color;

  const EventCategory(this.label, this.icon, this.color);
}

enum EventStatus {
  ongoing('Đang diễn ra', Colors.amber),
  next('Tiếp theo', Colors.cyan),
  completed('Hoàn thành', Colors.green);

  final String label;
  final Color badgeColor;

  const EventStatus(this.label, this.badgeColor);
}

class EventModel {
  final String id;
  final String title;
  final String time;
  final String location;
  final EventCategory category;
  final EventStatus status;
  final String? countdown;
  final bool hasPomodoro;
  final bool hasDocs;

  const EventModel({
    required this.id,
    required this.title,
    required this.time,
    required this.location,
    required this.category,
    this.status = EventStatus.next,
    this.countdown,
    this.hasPomodoro = false,
    this.hasDocs = false,
  });
}
