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
        Schema::create('cong_viec', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('du_an_id')->nullable()->constrained('du_an')->onDelete('set null');
            $table->string('ten_cong_viec');
            $table->text('mo_ta')->nullable();
            $table->enum('uu_tien', ['thap', 'trung_binh', 'cao', 'khan_cap'])->default('trung_binh');
            $table->enum('trang_thai', ['can_lam', 'dang_lam', 'cho_duyet', 'hoan_thanh'])->default('can_lam');
            $table->dateTime('deadline')->nullable();
            $table->unsignedTinyInteger('tien_do')->default(0); // 0 - 100%
            $table->boolean('dong_bo_calendar')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'trang_thai', 'deadline']);
            $table->index(['du_an_id', 'trang_thai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cong_viec');
    }
};
