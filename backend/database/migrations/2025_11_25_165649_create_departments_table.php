<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * NOTE: This migration is for reference only.
 * The actual table is created from gso_db.sql with the correct schema.
 * Table name in DB: department (singular, lowercase)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('department', function (Blueprint $table) {
            $table->id('DepartmentID');
            $table->string('DepartmentName', 100);
            $table->string('DepartmentCode', 20)->nullable();
            $table->boolean('IsActive')->default(true);
            $table->timestamp('CreatedDate')->useCurrent();
            $table->timestamp('UpdatedDate')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department');
    }
};
