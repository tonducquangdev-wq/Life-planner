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
        Schema::create('lich_hoc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('mon_hoc_id')->constrained('mon_hoc')->onDelete('cascade');
            $table->unsignedTinyInteger('ngay_trong_tuan')->comment('1: T2, 2: T3, 3: T4, 4: T5, 5: T6, 6: T7, 7: CN');
            $table->time('gio_bat_dau');
            $table->time('gio_ket_thuc');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_hoc');
    }
};
