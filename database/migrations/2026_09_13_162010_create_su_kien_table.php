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
        Schema::create('su_kien', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('tieu_de');

            $table->text('mo_ta')->nullable();

            $table->enum('loai_su_kien', [
                'hoc_tap',
                'deadline',
                'tap_luyen',
                'ca_nhan'
            ]);

            $table->dateTime('thoi_gian_bat_dau');

            $table->dateTime('thoi_gian_ket_thuc')
                ->nullable();

            $table->string('mau_hien_thi')
                ->default('#3B82F6');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('su_kien');
    }
};
