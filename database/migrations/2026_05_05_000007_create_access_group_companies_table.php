<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_group_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('access_group_id')->constrained('access_groups')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['access_group_id', 'company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_group_companies');
    }
};
