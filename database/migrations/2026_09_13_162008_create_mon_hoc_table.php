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
        Schema::create('mon_hoc', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('ma_mon');
            $table->string('ten_mon');
            $table->string('giang_vien')->nullable();
            $table->string('phong_hoc')->nullable();
            $table->integer('so_tin_chi');
            $table->integer('tien_do')->default(0);
            $table->decimal('diem_so', 4, 2)->nullable();
            
            $table->date('ngay_bat_dau')->nullable();
            $table->date('ngay_ket_thuc')->nullable();
            $table->string('mau_sac')->default('#6366f1');
            $table->enum('trang_thai', ['dang_hoc', 'da_hoan_thanh', 'tam_dung'])->default('dang_hoc');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mon_hoc');
    }
};
