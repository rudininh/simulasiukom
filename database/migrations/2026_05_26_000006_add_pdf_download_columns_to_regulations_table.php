<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('regulations')) {
            return;
        }

        Schema::table('regulations', function (Blueprint $table) {
            if (!Schema::hasColumn('regulations', 'official_url')) {
                $table->string('official_url')->nullable()->after('usage_notes');
            }

            if (!Schema::hasColumn('regulations', 'pdf_url')) {
                $table->string('pdf_url')->nullable()->after('official_url');
            }

            if (!Schema::hasColumn('regulations', 'download_status')) {
                $table->string('download_status', 30)->default('manual_required')->after('pdf_url');
            }

            if (!Schema::hasColumn('regulations', 'download_error')) {
                $table->longText('download_error')->nullable()->after('download_status');
            }

            if (!Schema::hasColumn('regulations', 'downloaded_at')) {
                $table->timestamp('downloaded_at')->nullable()->after('download_error');
            }

            if (!Schema::hasColumn('regulations', 'can_download_by_participant')) {
                $table->boolean('can_download_by_participant')->default(false)->after('downloaded_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('regulations')) {
            return;
        }

        Schema::table('regulations', function (Blueprint $table) {
            foreach ([
                'can_download_by_participant',
                'downloaded_at',
                'download_error',
                'download_status',
                'pdf_url',
                'official_url',
            ] as $column) {
                if (Schema::hasColumn('regulations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
