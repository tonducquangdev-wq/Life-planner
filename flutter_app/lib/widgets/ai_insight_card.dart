import 'package:flutter/material.dart';

/// AI Insight Card Component chuẩn thiết kế tham chiếu (Trợ lý AI Gợi ý, Progress 65%, Cảnh báo & Button Tối ưu)
class AiInsightCard extends StatelessWidget {
  final VoidCallback? onOptimizeTap;

  const AiInsightCard({super.key, this.onOptimizeTap});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 18.0, vertical: 8.0),
      child: Container(
        padding: const EdgeInsets.all(18.0),
        decoration: BoxDecoration(
          color: const Color(0xFFF3F0FF), // Soft Lavender/Purple background
          borderRadius: BorderRadius.circular(24),
          border: Border.all(color: const Color(0xFFDDD6FE), width: 1.2),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            // Header: Trợ lý AI Gợi ý & Tiến độ 65% Badge
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    Container(
                      width: 32,
                      height: 32,
                      decoration: const BoxDecoration(
                        color: Color(0xFF1D4ED8), // Deep Blue icon bg
                        shape: BoxShape.circle,
                      ),
                      child: const Icon(
                        Icons.auto_awesome_rounded,
                        color: Colors.white,
                        size: 16,
                      ),
                    ),
                    const SizedBox(width: 8),
                    const Text(
                      'Trợ lý AI Gợi ý',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w800,
                        color: Color(0xFF0F172A),
                      ),
                    ),
                  ],
                ),

                // Badge Tiến độ 65%
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: const Color(0xFFDDD6FE),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Text(
                    'Tiến độ 65%',
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF4338CA),
                    ),
                  ),
                ),
              ],
            ),

            const SizedBox(height: 14),

            // Progress Bar: Hoàn thành 3 / 5 mục tiêu hôm nay
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: const [
                Text(
                  'Hoàn thành 3 / 5 mục tiêu hôm nay',
                  style: TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                    color: Color(0xFF475569),
                  ),
                ),
                Text(
                  '65%',
                  style: TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.w800,
                    color: Color(0xFF1D4ED8),
                  ),
                ),
              ],
            ),

            const SizedBox(height: 6),

            ClipRRect(
              borderRadius: BorderRadius.circular(10),
              child: const LinearProgressIndicator(
                value: 0.65,
                minHeight: 8,
                backgroundColor: Color(0xFFCBD5E1),
                valueColor: AlwaysStoppedAnimation<Color>(Color(0xFF1D4ED8)),
              ),
            ),

            const SizedBox(height: 14),

            // Cảnh báo AI
            RichText(
              text: const TextSpan(
                style: TextStyle(fontSize: 12.5, color: Color(0xFF334155), height: 1.4),
                children: [
                  TextSpan(
                    text: '⚠️ Cảnh báo: ',
                    style: TextStyle(fontWeight: FontWeight.bold, color: Color(0xFFD97706)),
                  ),
                  TextSpan(text: 'Bạn còn '),
                  TextSpan(
                    text: '1 giờ 15 phút ',
                    style: TextStyle(fontWeight: FontWeight.bold, color: Color(0xFFDC2626)),
                  ),
                  TextSpan(
                      text:
                          'trước hạn nộp Đồ án CSDL. Hãy nộp trước 12:00 để duy trì phong độ và buổi tập gym chiều đạt hiệu quả cao nhất!'),
                ],
              ),
            ),

            const SizedBox(height: 16),

            // Nút Tối ưu lịch chiều nay ✨
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: onOptimizeTap,
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.white,
                  foregroundColor: const Color(0xFF1D4ED8),
                  elevation: 0,
                  padding: const EdgeInsets.symmetric(vertical: 12),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(20),
                    side: const BorderSide(color: Color(0xFFCBD5E1), width: 1),
                  ),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: const [
                    Icon(Icons.auto_awesome_rounded, size: 16, color: Color(0xFF1D4ED8)),
                    SizedBox(width: 6),
                    Text(
                      'Tối ưu lịch chiều nay ✨',
                      style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
