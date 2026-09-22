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
        Schema::table('su_kien', function (Blueprint $table) {
            $table->boolean('bat_thong_bao')->default(false)->after('mau_hien_thi');
            $table->tinyInteger('so_ngay_nhac')->default(1)->after('bat_thong_bao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('su_kien', function (Blueprint $table) {
            $table->dropColumn(['bat_thong_bao', 'so_ngay_nhac']);
        });
    }
};

