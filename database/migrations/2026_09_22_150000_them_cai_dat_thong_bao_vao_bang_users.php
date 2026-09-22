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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'thong_bao_enabled')) {
                $table->boolean('thong_bao_enabled')->default(true)->after('anh_dai_dien');
            }
            if (! Schema::hasColumn('users', 'thong_bao_lich_hoc')) {
                $table->boolean('thong_bao_lich_hoc')->default(true)->after('thong_bao_enabled');
            }
            if (! Schema::hasColumn('users', 'thong_bao_deadline')) {
                $table->boolean('thong_bao_deadline')->default(true)->after('thong_bao_lich_hoc');
            }
            if (! Schema::hasColumn('users', 'thong_bao_tap_luyen')) {
                $table->boolean('thong_bao_tap_luyen')->default(true)->after('thong_bao_deadline');
            }
            if (! Schema::hasColumn('users', 'am_thanh_thong_bao')) {
                $table->boolean('am_thanh_thong_bao')->default(true)->after('thong_bao_tap_luyen');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'thong_bao_enabled',
                'thong_bao_lich_hoc',
                'thong_bao_deadline',
                'thong_bao_tap_luyen',
                'am_thanh_thong_bao',
            ]);
        });
    }
};
