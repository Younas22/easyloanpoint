<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->foreignId('loan_type_id')
                  ->nullable()
                  ->after('loan_number')
                  ->constrained('loan_types')
                  ->nullOnDelete();
            $table->unsignedSmallInteger('repayment_days')->default(6)->after('tenure_months');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['loan_type_id']);
            $table->dropColumn(['loan_type_id', 'repayment_days']);
        });
    }
};
