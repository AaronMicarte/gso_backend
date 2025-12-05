<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * NOTE: This migration is for reference only.
 * The actual table is created from gso_db.sql with the correct schema.
 * Table name in DB: user (singular, lowercase)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table) {
            $table->id('UserID');
            $table->unsignedBigInteger('UserRoleID');
            $table->unsignedBigInteger('DepartmentID');
            $table->string('Username', 50)->unique();
            $table->string('Email', 100)->unique();
            $table->string('Password', 255);
            $table->string('FullName', 100)->nullable();
            $table->boolean('IsActive')->default(true);
            $table->timestamp('CreatedDate')->useCurrent();
            $table->timestamp('UpdatedDate')->nullable()->useCurrentOnUpdate();
            $table->foreign('UserRoleID')->references('UserRoleID')->on('userrole');
            $table->foreign('DepartmentID')->references('DepartmentID')->on('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
