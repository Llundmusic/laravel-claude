<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('active_company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
            $table->string('phone_code', 10)->nullable()->after('email');
            $table->string('phone', 30)->nullable()->after('phone_code');
            $table->string('billing_reference')->nullable()->after('phone');
            $table->string('language', 10)->default('en')->after('billing_reference');
            $table->boolean('darkmode')->default(false)->after('language');
            $table->boolean('is_active')->default(true)->after('darkmode');
            $table->boolean('access_all_companies')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('active_company_id');
            $table->dropColumn(['phone_code', 'phone', 'billing_reference', 'language', 'darkmode', 'is_active', 'access_all_companies']);
        });
    }
};
