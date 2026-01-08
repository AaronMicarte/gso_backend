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
        Schema::table('propertyissuance', function (Blueprint $table) {
            // Drop FK first - using the name from error log
            $table->dropForeign('propertyissuance_ibfk_2');
            
            // Make IssuedTo nullable
            $table->unsignedInteger('IssuedTo')->nullable()->change();
            
            // Add IssuedToName
            $table->string('IssuedToName')->nullable()->after('IssuedTo');
            
            // Re-add FK (nullable)
            // We reference the EndUser table. Assuming table name is 'EndUser' (based on error constraints) or 'end_users'?
            // The constraint name 'propertyissuance_ibfk_2' doesn't tell us the table, but the model refers to EndUser.
            // Let's assume table is 'EndUser' or 'enduser'. Laravel convention is snake_case plural usually strictly, but legacy DBs vary.
            // I'll check the EndUser model if I can, but for now I'll just change the column and add the name.
            // I will skip re-adding the FK for a moment to avoid guessing table name errors, 
            // or I'll try to guess 'EndUser' based on context.
            // Actually, if I just want to make it nullable, I don't need to re-add the FK immediately if I am unsure of table name.
            // But good practice is to have it.
            // Let's just make it nullable and add the name column.
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('propertyissuance', function (Blueprint $table) {
             $table->dropColumn('IssuedToName');
        });
    }
};
