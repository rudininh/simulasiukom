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
            $table->string('exam_type')->nullable();
            $table->unsignedInteger('duration_minutes');
            $table->unsignedInteger('total_questions');
            $table->decimal('passing_grade', 8, 2)->default(0);
            $table->date('available_from')->nullable();
            $table->date('available_until')->nullable();
            $table->string('regulation_basis')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('exam_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 50);
            $table->unsignedInteger('question_count')->default(0);
            $table->decimal('passing_score', 8, 2)->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('regulations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('regulation_number')->nullable();
            $table->unsignedInteger('year')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->longText('extracted_text')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('regulation_id')->nullable()->constrained()->nullOnDelete();
            $table->text('question_text');
            $table->text('option_a');
            $table->text('option_b');
            $table->text('option_c');
            $table->text('option_d');
            $table->text('option_e');
            $table->char('correct_answer', 1);
            $table->text('explanation')->nullable();
            $table->string('source_reference')->nullable();
            $table->unsignedInteger('score')->default(1);
            $table->enum('difficulty', ['easy', 'medium', 'hard', 'case'])->default('medium');
            $table->unsignedInteger('order_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('generated_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regulation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_category_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->text('option_a');
            $table->text('option_b');
            $table->text('option_c');
            $table->text('option_d');
            $table->text('option_e');
            $table->char('correct_answer', 1);
            $table->text('explanation')->nullable();
            $table->string('source_reference')->nullable();
            $table->string('difficulty')->default('medium');
            $table->string('question_type')->default('Pemahaman pasal');
            $table->enum('status', ['draft', 'approved', 'rejected'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
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
            $table->unsignedInteger('score_regulasi_asn')->default(0);
            $table->unsignedInteger('score_manajemen_asn')->default(0);
            $table->unsignedInteger('score_kepemimpinan')->default(0);
            $table->unsignedInteger('score_pelayanan_publik')->default(0);
            $table->unsignedInteger('score_studi_kasus')->default(0);
            $table->unsignedInteger('total_answered')->default(0);
            $table->unsignedInteger('total_correct')->default(0);
            $table->unsignedInteger('total_wrong')->default(0);
            $table->enum('status', ['ongoing', 'finished', 'expired'])->default('ongoing');
            $table->enum('competency_status', ['kompeten', 'belum_kompeten'])->nullable();
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
        Schema::dropIfExists('generated_questions');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('regulations');
        Schema::dropIfExists('exam_categories');
        Schema::dropIfExists('exams');
    }
};
