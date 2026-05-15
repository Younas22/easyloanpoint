<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('assigned_hr_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('loan_number', 20)->unique();
            $table->enum('loan_type', ['personal', 'home', 'business', 'vehicle', 'education'])->default('personal');
            $table->decimal('amount_requested', 12, 2);
            $table->decimal('amount_approved', 12, 2)->nullable();
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->unsignedSmallInteger('tenure_months')->default(12);
            $table->string('purpose')->nullable();
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'disbursed'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('disbursed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
