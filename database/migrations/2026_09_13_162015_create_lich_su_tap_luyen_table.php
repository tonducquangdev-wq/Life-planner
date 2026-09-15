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
        Schema::create('lich_su_tap_luyen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('buoi_tap_id')
                ->constrained('buoi_tap')
                ->onDelete('cascade');

            $table->dateTime('thoi_gian_bat_dau');

            $table->dateTime('thoi_gian_ket_thuc');

            $table->integer('tong_thoi_luong');

            $table->text('ghi_chu')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_su_tap_luyen');
    }
};
