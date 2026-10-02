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
        Schema::create('gia_su', function (Blueprint $table) {
            $table->id();
            $table->string('ho_ten');
            $table->string('email')->nullable();
            $table->string('so_dien_thoai')->nullable();
            $table->foreignId('mon_hoc_id')->nullable()->constrained('mon_hoc')->nullOnDelete();
            $table->string('chuyen_mon')->nullable();
            $table->unsignedInteger('hoc_phi_theo_gio')->default(150000);
            $table->decimal('danh_gia', 2, 1)->default(5.0);
            $table->unsignedInteger('so_danh_gia')->default(10);
            $table->text('mo_ta_kinh_nghiem')->nullable();
            $table->string('anh_dai_dien')->nullable();
            $table->string('trang_thai')->default('san_sang'); // san_sang, ban
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gia_su');
    }
};
