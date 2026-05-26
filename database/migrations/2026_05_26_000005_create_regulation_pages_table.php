<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('regulation_pages')) {
            Schema::create('regulation_pages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('regulation_id')->constrained()->cascadeOnDelete();
                $table->unsignedInteger('page_number');
                $table->longText('text')->nullable();
                $table->longText('ocr_text')->nullable();
                $table->string('extraction_method')->nullable();
                $table->decimal('confidence', 5, 2)->nullable();
                $table->string('image_path')->nullable();
                $table->enum('status', ['pending', 'extracted', 'ocr_completed', 'failed'])->default('pending');
                $table->longText('error_message')->nullable();
                $table->timestamps();
                $table->unique(['regulation_id', 'page_number']);
            });
        }

        if (!Schema::hasTable('regulation_pages')) {
            return;
        }

        $regulations = DB::table('regulations')
            ->select('id', 'extracted_text', 'extraction_method', 'extraction_status')
            ->whereNotNull('extracted_text')
            ->where('extracted_text', '<>', '')
            ->get();

        foreach ($regulations as $regulation) {
            DB::table('regulation_pages')->updateOrInsert(
                [
                    'regulation_id' => $regulation->id,
                    'page_number' => 1,
                ],
                [
                    'text' => $regulation->extracted_text,
                    'ocr_text' => null,
                    'extraction_method' => $regulation->extraction_method ?: 'legacy',
                    'confidence' => null,
                    'image_path' => null,
                    'status' => $regulation->extraction_status === 'ocr_completed' ? 'ocr_completed' : 'extracted',
                    'error_message' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('regulation_pages');
    }
};
