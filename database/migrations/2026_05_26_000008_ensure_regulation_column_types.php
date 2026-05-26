<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('regulations') || DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        if (Schema::hasColumn('regulations', 'description')) {
            DB::statement('ALTER TABLE regulations MODIFY description LONGTEXT NULL');
        }

        if (Schema::hasColumn('regulations', 'usage_notes')) {
            DB::statement('ALTER TABLE regulations MODIFY usage_notes LONGTEXT NULL');
        }

        if (Schema::hasColumn('regulations', 'status')) {
            DB::statement('ALTER TABLE regulations MODIFY status VARCHAR(255) NULL');
        }

        if (Schema::hasColumn('regulations', 'download_status')) {
            DB::statement("ALTER TABLE regulations MODIFY download_status ENUM('pending','downloaded','failed','manual_required') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        //
    }
};
