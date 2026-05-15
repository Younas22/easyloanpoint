<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('employment_type', ['salaried', 'self_employed', 'business', 'unemployed'])
                  ->nullable()->after('gender');
            $table->decimal('salary', 12, 2)->nullable()->after('employment_type');
            $table->text('notes')->nullable()->after('salary');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['employment_type', 'salary', 'notes']);
        });
    }
};
