<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL-specific enum widening. SQLite (used by the test suite) has no
        // native ENUM type — the column already accepts any string, so there is
        // nothing to alter there.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE loans MODIFY COLUMN status ENUM('pending','under_review','approved','rejected','disbursed','closed') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE loans MODIFY COLUMN status ENUM('pending','under_review','approved','rejected','disbursed') NOT NULL DEFAULT 'pending'");
        }
    }
};
