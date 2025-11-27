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
        Schema::create('user', function (Blueprint $table) {
            $table->id('UserID');
            $table->unsignedBigInteger('UserRoleID');
            $table->unsignedBigInteger('DepartmentID');
            $table->string('Username', 100)->unique();
            $table->string('Email', 150)->unique();
            $table->string('Password', 255);
            $table->foreign('UserRoleID')->references('UserRoleID')->on('userroles');
            $table->foreign('DepartmentID')->references('DepartmentID')->on('departments');
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
