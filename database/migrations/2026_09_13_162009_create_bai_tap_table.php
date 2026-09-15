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
        Schema::create('bai_tap', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mon_hoc_id')
                ->constrained('mon_hoc')
                ->onDelete('cascade');

            $table->string('tieu_de');
            $table->text('mo_ta')->nullable();

            $table->dateTime('han_nop');

            $table->enum('muc_do_uu_tien', [
                'thap',
                'trung_binh',
                'cao'
            ])->default('trung_binh');

            $table->enum('trang_thai', [
                'chua_hoan_thanh',
                'dang_thuc_hien',
                'da_hoan_thanh'
            ])->default('chua_hoan_thanh');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bai_tap');
    }
};
