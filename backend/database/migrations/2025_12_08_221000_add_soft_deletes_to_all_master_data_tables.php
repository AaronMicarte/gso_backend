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
        // Master Data Tables - Reference/Lookup Tables
        $masterDataTables = [
            'category',
            'classification',
            'condition',
            'department',
            'disposalmethod',
            'enduser',
            'iiruprecommendation',
            'joborderstatus',
            'position',
            'propertyclassification',
            'propertystatus',
            'propertytype',
            'purchaseorderstatus',
            'purchaserequeststatus',
            'supplier',
            'unitofmeasure',
            'userrole',
        ];

        foreach ($masterDataTables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $masterDataTables = [
            'category',
            'classification',
            'condition',
            'department',
            'disposalmethod',
            'enduser',
            'iiruprecommendation',
            'joborderstatus',
            'position',
            'propertyclassification',
            'propertystatus',
            'propertytype',
            'purchaseorderstatus',
            'purchaserequeststatus',
            'supplier',
            'unitofmeasure',
            'userrole',
        ];

        foreach ($masterDataTables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
