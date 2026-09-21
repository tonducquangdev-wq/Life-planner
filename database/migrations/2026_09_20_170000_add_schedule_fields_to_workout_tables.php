<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Thêm trường is_active cho bảng ke_hoach_tap_luyen
        Schema::table('ke_hoach_tap_luyen', function (Blueprint $table) {
            if (!Schema::hasColumn('ke_hoach_tap_luyen', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('mo_ta');
            }
        });

        // 2. Thêm trường ngay_trong_tuan cho bảng buoi_tap (1 = Thứ 2 ... 7 = Chủ Nhật theo ISO-8601)
        Schema::table('buoi_tap', function (Blueprint $table) {
            if (!Schema::hasColumn('buoi_tap', 'ngay_trong_tuan')) {
                $table->unsignedTinyInteger('ngay_trong_tuan')->nullable()->after('thu_tu')
                    ->comment('1: Thứ 2, 2: Thứ 3, 3: Thứ 4, 4: Thứ 5, 5: Thứ 6, 6: Thứ 7, 7: Chủ Nhật');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buoi_tap', function (Blueprint $table) {
            if (Schema::hasColumn('buoi_tap', 'ngay_trong_tuan')) {
                $table->dropColumn('ngay_trong_tuan');
            }
        });

        Schema::table('ke_hoach_tap_luyen', function (Blueprint $table) {
            if (Schema::hasColumn('ke_hoach_tap_luyen', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
