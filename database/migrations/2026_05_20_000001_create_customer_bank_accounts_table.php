<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->unsignedTinyInteger('sort_order')->default(1)->comment('1 = primary, 2 = secondary');
            $table->string('bank_name', 100);
            $table->string('account_number', 20);
            $table->string('ifsc_code', 11);
            $table->string('holder_name', 100);
            $table->timestamps();

            $table->unique(['customer_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_bank_accounts');
    }
};
