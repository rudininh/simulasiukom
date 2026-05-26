<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('regulations')) {
            return;
        }

        Schema::table('regulations', function (Blueprint $table) {
            if (!Schema::hasColumn('regulations', 'category')) {
                $table->string('category')->nullable()->after('year');
            }

            if (!Schema::hasColumn('regulations', 'priority')) {
                $table->string('priority')->nullable()->after('category');
            }

            if (!Schema::hasColumn('regulations', 'usage_notes')) {
                $table->text('usage_notes')->nullable()->after('description');
            }

            if (!Schema::hasColumn('regulations', 'original_filename')) {
                $table->string('original_filename')->nullable()->after('file_path');
            }

            if (!Schema::hasColumn('regulations', 'mime_type')) {
                $table->string('mime_type')->nullable()->after('original_filename');
            }

            if (!Schema::hasColumn('regulations', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable()->after('mime_type');
            }

            if (!Schema::hasColumn('regulations', 'extraction_status')) {
                $table->enum('extraction_status', ['pending', 'extracted', 'need_ocr', 'ocr_processing', 'ocr_completed', 'failed'])
                    ->default('pending')
                    ->after('extracted_text');
            }

            if (!Schema::hasColumn('regulations', 'extraction_method')) {
                $table->string('extraction_method')->nullable()->after('extraction_status');
            }

            if (!Schema::hasColumn('regulations', 'extraction_error')) {
                $table->longText('extraction_error')->nullable()->after('extraction_method');
            }

            if (!Schema::hasColumn('regulations', 'extracted_at')) {
                $table->timestamp('extracted_at')->nullable()->after('extraction_error');
            }

            if (!Schema::hasColumn('regulations', 'page_count')) {
                $table->unsignedInteger('page_count')->nullable()->after('extracted_at');
            }

            if (!Schema::hasColumn('regulations', 'ocr_language')) {
                $table->string('ocr_language')->default('ind')->after('page_count');
            }

            if (!Schema::hasColumn('regulations', 'ocr_confidence')) {
                $table->decimal('ocr_confidence', 5, 2)->nullable()->after('ocr_language');
            }

            if (!Schema::hasColumn('regulations', 'summary')) {
                $table->longText('summary')->nullable()->after('ocr_confidence');
            }

            if (!Schema::hasColumn('regulations', 'keywords')) {
                $table->json('keywords')->nullable()->after('summary');
            }
        });

        if (Schema::hasColumn('regulations', 'category')) {
            DB::table('regulations')
                ->whereNull('category')
                ->where('title', 'like', '%Aparatur Sipil Negara%')
                ->update(['category' => 'Regulasi ASN']);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('regulations')) {
            return;
        }

        Schema::table('regulations', function (Blueprint $table) {
            foreach ([
                'keywords',
                'summary',
                'ocr_confidence',
                'ocr_language',
                'page_count',
                'extracted_at',
                'extraction_error',
                'extraction_method',
                'extraction_status',
                'file_size',
                'mime_type',
                'original_filename',
                'usage_notes',
                'priority',
                'category',
            ] as $column) {
                if (Schema::hasColumn('regulations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
