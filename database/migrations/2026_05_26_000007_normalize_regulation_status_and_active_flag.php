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

        if (DB::connection()->getDriverName() === 'mysql' && Schema::hasColumn('regulations', 'status')) {
            DB::statement('ALTER TABLE regulations MODIFY status VARCHAR(255) NULL');
        }

        if (DB::connection()->getDriverName() === 'mysql') {
            if (Schema::hasColumn('regulations', 'description')) {
                DB::statement('ALTER TABLE regulations MODIFY description LONGTEXT NULL');
            }
            if (Schema::hasColumn('regulations', 'usage_notes')) {
                DB::statement('ALTER TABLE regulations MODIFY usage_notes LONGTEXT NULL');
            }
            if (Schema::hasColumn('regulations', 'download_status')) {
                DB::statement("ALTER TABLE regulations MODIFY download_status ENUM('pending','downloaded','failed','manual_required') NOT NULL DEFAULT 'pending'");
            }
        }

        Schema::table('regulations', function (Blueprint $table) {
            if (!Schema::hasColumn('regulations', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('status');
            }
        });

        DB::table('regulations')
            ->where('status', 'active')
            ->update(['status' => 'Berlaku', 'is_active' => true]);

        DB::table('regulations')
            ->where('status', 'inactive')
            ->update(['status' => 'Tidak Aktif', 'is_active' => false]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('regulations')) {
            return;
        }

        Schema::table('regulations', function (Blueprint $table) {
            if (Schema::hasColumn('regulations', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
