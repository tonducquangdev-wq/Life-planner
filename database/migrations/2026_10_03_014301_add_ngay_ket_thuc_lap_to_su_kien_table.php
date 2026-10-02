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
            if (!Schema::hasColumn('su_kien', 'ngay_ket_thuc_lap')) {
                $table->date('ngay_ket_thuc_lap')->nullable()->after('quy_tac_lap');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('su_kien', function (Blueprint $table) {
            if (Schema::hasColumn('su_kien', 'ngay_ket_thuc_lap')) {
                $table->dropColumn('ngay_ket_thuc_lap');
            }
        });
    }
};
