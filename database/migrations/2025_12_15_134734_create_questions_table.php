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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_id')->constrained();
            $table->json('question_data'); // Stores type, question_text, choices
            $table->boolean('is_selected')->default(false);
            $table->boolean('is_available')->default(false);
            $table->boolean('accepting_answers')->default(false);
            $table->timestamp('timer_started_at')->nullable();
            $table->integer('points')->default(0);
            $table->integer('bonus_points')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
