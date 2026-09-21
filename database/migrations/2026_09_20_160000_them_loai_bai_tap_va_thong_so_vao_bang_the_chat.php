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
        // 1. Mở rộng bảng danh mục bài tập thể chất (bai_tap_the_chat)
        Schema::table('bai_tap_the_chat', function (Blueprint $table) {
            if (!Schema::hasColumn('bai_tap_the_chat', 'loai_bai_tap')) {
                $table->string('loai_bai_tap', 30)->nullable()->default('strength')->after('nhom_co');
            }
        });

        // 2. Mở rộng bảng chi tiết bài tập trong buổi tập (chi_tiet_buoi_tap)
        Schema::table('chi_tiet_buoi_tap', function (Blueprint $table) {
            if (!Schema::hasColumn('chi_tiet_buoi_tap', 'loai_bai_tap')) {
                $table->string('loai_bai_tap', 30)->nullable()->default('strength')->after('bai_tap_the_chat_id');
            }
            if (!Schema::hasColumn('chi_tiet_buoi_tap', 'so_sets')) {
                $table->unsignedTinyInteger('so_sets')->nullable()->after('thu_tu');
            }
            if (!Schema::hasColumn('chi_tiet_buoi_tap', 'so_reps')) {
                $table->string('so_reps', 50)->nullable()->after('so_sets');
            }
            if (!Schema::hasColumn('chi_tiet_buoi_tap', 'thoi_luong')) {
                $table->unsignedSmallInteger('thoi_luong')->nullable()->after('so_reps');
            }
            if (!Schema::hasColumn('chi_tiet_buoi_tap', 'don_vi_thoi_gian')) {
                $table->string('don_vi_thoi_gian', 20)->nullable()->default('phut')->after('thoi_luong');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chi_tiet_buoi_tap', function (Blueprint $table) {
            $table->dropColumn(['loai_bai_tap', 'so_sets', 'so_reps', 'thoi_luong', 'don_vi_thoi_gian']);
        });

        Schema::table('bai_tap_the_chat', function (Blueprint $table) {
            $table->dropColumn(['loai_bai_tap']);
        });
    }
};
