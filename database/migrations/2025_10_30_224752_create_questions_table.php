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
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->text('question_text');
            $table->enum('question_type', ['pilihan_ganda', 'essay'])->default('pilihan_ganda');
            $table->json('options')->nullable(); // For multiple choice: ['A' => 'answer1', 'B' => 'answer2', ...]
            $table->string('correct_answer')->nullable(); // For multiple choice: 'A', 'B', etc. For essay: answer key
            $table->string('level')->nullable(); // X, XI, XII
            $table->string('topic')->nullable();
            $table->enum('difficulty', ['mudah', 'sedang', 'sulit'])->default('sedang');
            $table->integer('points')->default(1);
            $table->text('explanation')->nullable();
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
