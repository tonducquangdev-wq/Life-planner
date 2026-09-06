<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_session_exercises', function (Blueprint $table) {
            $table->id();
            // Khóa ngoại nối tới bảng workout_sessions
            $table->foreignId('workout_session_id')
                ->constrained('workout_sessions')
                ->onDelete('cascade');

            // Khóa ngoại nối tới bảng exercises gốc vừa tạo ở trên
            $table->foreignId('exercise_id')
                ->constrained('exercises')
                ->onDelete('cascade');

            $table->integer('sets'); // Số hiệp
            $table->integer('reps'); // Số lần
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_session_exercises');
    }
};
