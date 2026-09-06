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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subject_id')->constrained()->onDelete('cascade');

            $table->string('title');
            $table->text('description')->nullable();

            $table->dateTime('due_date');

            $table->enum('priority', [
                'low',
                'medium',
                'high'
            ]);

            $table->enum('status', [
                'pending',
                'completed'
            ])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
