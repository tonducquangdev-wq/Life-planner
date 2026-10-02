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
        Schema::table('ke_hoach_tap_luyen', function (Blueprint $table) {
            $table->string('share_code', 32)->nullable()->unique()->after('is_active');
            $table->boolean('is_shared')->default(false)->after('share_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ke_hoach_tap_luyen', function (Blueprint $table) {
            $table->dropColumn(['share_code', 'is_shared']);
        });
    }
};
