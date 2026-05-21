<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change loan_type from ENUM to VARCHAR so any admin-defined loan type name can be stored
        DB::statement("ALTER TABLE `loans` MODIFY `loan_type` VARCHAR(100) NOT NULL DEFAULT 'personal'");
    }

    public function down(): void
    {
        // Restore ENUM — update non-standard values first to avoid truncation
        DB::statement("UPDATE `loans` SET `loan_type` = 'personal'
            WHERE `loan_type` NOT IN ('personal','home','business','vehicle','education')");
        DB::statement("ALTER TABLE `loans` MODIFY `loan_type` ENUM('personal','home','business','vehicle','education') NOT NULL DEFAULT 'personal'");
    }
};
