<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('aadhaar_document')->nullable()->after('aadhaar_number');
            $table->string('pan_document')->nullable()->after('pan_number');
            $table->string('selfie_document')->nullable()->after('pan_document');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['aadhaar_document', 'pan_document', 'selfie_document']);
        });
    }
};
