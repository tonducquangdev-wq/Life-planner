import 'package:flutter/material.dart';

/// AppTheme định nghĩa hệ thống Màu sắc & Typography chuẩn Material 3 cho Life Planner
class AppTheme {
  // Brand Colors
  static const Color primaryColor = Color(0xFF4F46E5); // Indigo Primary
  static const Color secondaryColor = Color(0xFF8B5CF6); // Purple Secondary
  static const Color accentGradientStart = Color(0xFF6366F1);
  static const Color accentGradientEnd = Color(0xFF4F46E5);

  // Category Accent Colors
  static const Color colorStudy = Color(0xFF3B82F6); // Blue
  static const Color colorWorkout = Color(0xFF22C55E); // Green
  static const Color colorDeadline = Color(0xFFF97316); // Orange
  static const Color colorPersonal = Color(0xFFA855F7); // Purple
  static const Color colorWork = Color(0xFF6366F1); // Indigo

  // Background & Neutral Colors
  static const Color lightBg = Color(0xFFF8FAFC);
  static const Color darkBg = Color(0xFF0F172A);
  static const Color lightCardBg = Colors.white;
  static const Color darkCardBg = Color(0xFF1E293B);

  static ThemeData get lightTheme {
    final colorScheme = ColorScheme.fromSeed(
      seedColor: primaryColor,
      brightness: Brightness.light,
      primary: primaryColor,
      secondary: secondaryColor,
      surface: lightCardBg,
    );

    return ThemeData(
      useMaterial3: true,
      colorScheme: colorScheme,
      scaffoldBackgroundColor: lightBg,
      fontFamily: 'Plus Jakarta Sans',
      appBarTheme: const AppBarTheme(
        backgroundColor: Colors.transparent,
        elevation: 0,
        scrolledUnderElevation: 0,
      ),
      cardTheme: CardTheme(
        elevation: 2,
        shadowColor: Colors.black.withAlpha(15),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
        ),
        color: lightCardBg,
      ),
      chipTheme: ChipThemeData(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
        ),
        side: BorderSide.none,
      ),
    );
  }

  static ThemeData get darkTheme {
    final colorScheme = ColorScheme.fromSeed(
      seedColor: primaryColor,
      brightness: Brightness.dark,
      primary: const Color(0xFF818CF8),
      secondary: const Color(0xFFA78BFA),
      surface: darkCardBg,
    );

    return ThemeData(
      useMaterial3: true,
      colorScheme: colorScheme,
      scaffoldBackgroundColor: darkBg,
      fontFamily: 'Plus Jakarta Sans',
      appBarTheme: const AppBarTheme(
        backgroundColor: Colors.transparent,
        elevation: 0,
        scrolledUnderElevation: 0,
      ),
      cardTheme: CardTheme(
        elevation: 4,
        shadowColor: Colors.black.withAlpha(60),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
        ),
        color: darkCardBg,
      ),
      chipTheme: ChipThemeData(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
        ),
        side: BorderSide.none,
      ),
    );
  }
}
