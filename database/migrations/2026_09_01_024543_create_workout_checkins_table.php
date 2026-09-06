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
        Schema::create('workout_checkins', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->foreignId('workout_session_id')
                ->constrained()
                ->onDelete('cascade');

            $table->dateTime('started_at')->nullable();

            $table->dateTime('finished_at')->nullable();

            $table->integer('duration')->nullable();

            $table->enum('status', [
                'started',
                'completed',
                'cancelled'
            ]);

            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_checkins');
    }
};
