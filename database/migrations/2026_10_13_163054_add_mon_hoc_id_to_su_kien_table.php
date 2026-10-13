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
            if (! Schema::hasColumn('su_kien', 'mon_hoc_id')) {
                $table->foreignId('mon_hoc_id')->nullable()->after('cong_viec_id')->constrained('mon_hoc')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('su_kien', function (Blueprint $table) {
            if (Schema::hasColumn('su_kien', 'mon_hoc_id')) {
                $table->dropForeign(['mon_hoc_id']);
                $table->dropColumn('mon_hoc_id');
            }
        });
    }
};
