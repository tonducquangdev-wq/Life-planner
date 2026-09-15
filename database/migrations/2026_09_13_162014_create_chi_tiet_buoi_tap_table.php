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
        Schema::create('chi_tiet_buoi_tap', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buoi_tap_id')
                ->constrained('buoi_tap')
                ->onDelete('cascade');

            $table->foreignId('bai_tap_the_chat_id')
                ->constrained('bai_tap_the_chat')
                ->onDelete('cascade');

            $table->integer('thu_tu')
                ->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_buoi_tap');
    }
};
