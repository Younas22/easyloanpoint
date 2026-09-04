<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Add super_admin to users.role enum ─────────────────────────────
        // MySQL-specific enum widening. SQLite (used by the test suite) has no
        // native ENUM type — the column already accepts any string, so there is
        // nothing to alter there.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','hr','customer','super_admin') NOT NULL DEFAULT 'hr'");
        }

        // ── 2. Create panel_permissions table ─────────────────────────────────
        Schema::create('panel_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('panel', 20);        // admin | hr
            $table->string('key', 100)->unique();
            $table->string('label', 100);
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
        });

        // ── 3. Seed default permissions ───────────────────────────────────────
        $now = now();
        DB::table('panel_permissions')->insert([
            // Admin panel
            ['panel' => 'admin', 'key' => 'admin_dashboard',    'label' => 'Dashboard',          'is_hidden' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['panel' => 'admin', 'key' => 'admin_hr',           'label' => 'HR Management',      'is_hidden' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['panel' => 'admin', 'key' => 'admin_customers',    'label' => 'Customers',          'is_hidden' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['panel' => 'admin', 'key' => 'admin_loans',        'label' => 'Loan Applications',  'is_hidden' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['panel' => 'admin', 'key' => 'admin_loan_types',   'label' => 'Loan Types',         'is_hidden' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['panel' => 'admin', 'key' => 'admin_assignments',  'label' => 'Assignments',        'is_hidden' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['panel' => 'admin', 'key' => 'admin_notifications','label' => 'Notifications',      'is_hidden' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['panel' => 'admin', 'key' => 'admin_reports',      'label' => 'Reports',            'is_hidden' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['panel' => 'admin', 'key' => 'admin_settings',     'label' => 'Settings',           'is_hidden' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['panel' => 'admin', 'key' => 'admin_logs',         'label' => 'Activity Logs',      'is_hidden' => 0, 'created_at' => $now, 'updated_at' => $now],
            // HR panel
            ['panel' => 'hr',    'key' => 'hr_dashboard',       'label' => 'Dashboard',          'is_hidden' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['panel' => 'hr',    'key' => 'hr_customers',       'label' => 'My Customers',       'is_hidden' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['panel' => 'hr',    'key' => 'hr_loans',           'label' => 'Loan Applications',  'is_hidden' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['panel' => 'hr',    'key' => 'hr_notifications',   'label' => 'Notifications',      'is_hidden' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['panel' => 'hr',    'key' => 'hr_profile',         'label' => 'Profile',            'is_hidden' => 0, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── 4. Create super_admin user ────────────────────────────────────────
        if (! DB::table('users')->where('email', 'superadmin@easyloanpoint.com')->exists()) {
            DB::table('users')->insert([
                'name'       => 'Super Admin',
                'email'      => 'superadmin@easyloanpoint.com',
                'password'   => bcrypt('SuperAdmin@123'),
                'role'       => 'super_admin',
                'status'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('users')->where('email', 'superadmin@easyloanpoint.com')->delete();
        Schema::dropIfExists('panel_permissions');

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','hr','customer') NOT NULL DEFAULT 'hr'");
        }
    }
};
