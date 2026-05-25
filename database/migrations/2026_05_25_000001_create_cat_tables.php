<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('duration_minutes');
            $table->unsignedInteger('total_questions');
            $table->decimal('passing_grade', 8, 2)->default(0);
            $table->date('available_from')->nullable();
            $table->date('available_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('exam_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 10);
            $table->unsignedInteger('question_count')->default(0);
            $table->decimal('passing_score', 8, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_category_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->text('option_a');
            $table->text('option_b');
            $table->text('option_c');
            $table->text('option_d');
            $table->text('option_e');
            $table->char('correct_answer', 1);
            $table->unsignedInteger('score')->default(5);
            $table->unsignedInteger('order_number')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedInteger('score_total')->default(0);
            $table->unsignedInteger('score_twk')->default(0);
            $table->unsignedInteger('score_tiu')->default(0);
            $table->unsignedInteger('score_tkp')->default(0);
            $table->enum('status', ['ongoing', 'finished', 'expired'])->default('ongoing');
            $table->boolean('is_passed')->default(false);
            $table->timestamps();
        });

        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->char('selected_answer', 1)->nullable();
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('score_obtained')->default(0);
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();
            $table->unique(['exam_attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
        Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('exam_categories');
        Schema::dropIfExists('exams');
    }
};
