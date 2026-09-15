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
        Schema::create('buoi_tap', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ke_hoach_tap_luyen_id')
                ->constrained('ke_hoach_tap_luyen')
                ->onDelete('cascade');

            $table->string('ten_buoi_tap');

            $table->text('mo_ta')
                ->nullable();

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
        Schema::dropIfExists('buoi_tap');
    }
};
