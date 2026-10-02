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
        Schema::create('du_an', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('ten_du_an');
            $table->text('mo_ta')->nullable();
            $table->date('ngay_bat_dau')->nullable();
            $table->date('ngay_ket_thuc')->nullable();
            $table->string('trang_thai')->default('chua_bat_dau'); // chua_bat_dau, dang_thuc_hien, tam_dung, hoan_thanh, da_huy
            $table->string('mau_nhan')->default('#4F46E5');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'trang_thai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('du_an');
    }
};
