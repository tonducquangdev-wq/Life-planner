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
            if (! Schema::hasColumn('users', 'thong_bao_cong_viec')) {
                $table->boolean('thong_bao_cong_viec')->default(true)->after('thong_bao_deadline');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'thong_bao_cong_viec')) {
                $table->dropColumn('thong_bao_cong_viec');
            }
        });
    }
};
