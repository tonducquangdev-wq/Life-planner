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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('subject_code');
            $table->string('subject_name');
            $table->string('lecturer')->nullable();
            $table->string('classroom')->nullable();
            $table->integer('credits');
            $table->integer('progress')->default(0);
            $table->decimal('grade', 4, 2)->nullable();
            $table->string('semester');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
