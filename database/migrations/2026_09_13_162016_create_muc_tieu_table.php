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
        Schema::create('muc_tieu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                    ->constrained()
                    ->onDelete('cascade');

                $table->string('tieu_de');

                $table->enum('loai_muc_tieu', [
                    'gpa',
                    'can_nang',
                    'tap_luyen',
                    'khac'
                ]);

                $table->decimal('gia_tri_muc_tieu', 10, 2);

                $table->decimal('gia_tri_hien_tai', 10, 2)
                    ->default(0);

                $table->enum('trang_thai', [
                    'dang_thuc_hien',
                    'hoan_thanh'
                ])->default('dang_thuc_hien');

                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('muc_tieu');
    }
};
