<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('questions')) {
            Schema::table('questions', function (Blueprint $table) {
                if (!Schema::hasColumn('questions', 'question_type')) {
                    $table->string('question_type')->nullable()->after('source_reference');
                }

                if (!Schema::hasColumn('questions', 'source_page')) {
                    $table->unsignedInteger('source_page')->nullable()->after('question_type');
                }
            });
        }

        if (Schema::hasTable('generated_questions')) {
            Schema::table('generated_questions', function (Blueprint $table) {
                if (!Schema::hasColumn('generated_questions', 'source_page')) {
                    $table->unsignedInteger('source_page')->nullable()->after('source_reference');
                }

                if (!Schema::hasColumn('generated_questions', 'source_chunk_index')) {
                    $table->unsignedInteger('source_chunk_index')->nullable()->after('source_page');
                }

                if (!Schema::hasColumn('generated_questions', 'validation_status')) {
                    $table->enum('validation_status', ['valid', 'warning', 'invalid'])->nullable()->after('question_type');
                }

                if (!Schema::hasColumn('generated_questions', 'validation_notes')) {
                    $table->text('validation_notes')->nullable()->after('validation_status');
                }

                if (!Schema::hasColumn('generated_questions', 'ai_model')) {
                    $table->string('ai_model')->nullable()->after('validation_notes');
                }

                if (!Schema::hasColumn('generated_questions', 'ai_raw_response')) {
                    $table->longText('ai_raw_response')->nullable()->after('ai_model');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('questions')) {
            Schema::table('questions', function (Blueprint $table) {
                foreach (['source_page', 'question_type'] as $column) {
                    if (Schema::hasColumn('questions', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('generated_questions')) {
            Schema::table('generated_questions', function (Blueprint $table) {
                foreach (['ai_raw_response', 'ai_model', 'validation_notes', 'validation_status', 'source_chunk_index', 'source_page'] as $column) {
                    if (Schema::hasColumn('generated_questions', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
