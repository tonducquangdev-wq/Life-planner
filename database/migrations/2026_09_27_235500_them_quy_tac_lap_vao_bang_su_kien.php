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
            $table->string('quy_tac_lap')->default('once')->nullable()->after('so_ngay_nhac');
            $table->string('nhom_lap_id')->nullable()->after('quy_tac_lap')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('su_kien', function (Blueprint $table) {
            $table->dropColumn(['quy_tac_lap', 'nhom_lap_id']);
        });
    }
};
