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
        // Add deleted_at column to all master data tables
        $tables = [
            'category',
            'propertytype',
            'department',
            'supplier',
            'position',
            'enduser',
        ];

        foreach ($tables as $table) {
            // Only add soft deletes if table exists
            if (!Schema::hasTable($table)) {
                continue;
            }
            Schema::table($table, function (Blueprint $table) {
                if (!Schema::hasColumn($table->getTable(), 'deleted_at')) {
                    $table->softDeletes()->after('UpdatedDate');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove deleted_at column from all master data tables
        $tables = [
            'category',
            'propertytype',
            'department',
            'supplier',
            'position',
            'enduser',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
