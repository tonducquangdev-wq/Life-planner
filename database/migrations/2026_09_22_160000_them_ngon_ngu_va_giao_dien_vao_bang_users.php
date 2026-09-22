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
            if (! Schema::hasColumn('users', 'ngon_ngu')) {
                $table->string('ngon_ngu', 10)->default('vi')->after('am_thanh_thong_bao');
            }
            if (! Schema::hasColumn('users', 'giao_dien')) {
                $table->string('giao_dien', 20)->default('light')->after('ngon_ngu');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ngon_ngu', 'giao_dien']);
        });
    }
};
