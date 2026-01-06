<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check and add columns that don't exist
        $columns = Schema::getColumnListing('assetrequestdetail');
        
        Schema::table('assetrequestdetail', function (Blueprint $table) use ($columns) {
            // Make PropertyInventoryDetailID nullable (for new item requests)
            $table->integer('PropertyInventoryDetailID')->nullable()->change();
            
            // Add fields only if they don't exist
            if (!in_array('ItemDescription', $columns)) {
                $table->string('ItemDescription', 255)->nullable()->after('PropertyInventoryDetailID');
            }
            if (!in_array('Unit', $columns)) {
                $table->string('Unit', 50)->nullable()->after('ItemDescription');
            }
            if (!in_array('IntendedEndUser', $columns)) {
                $table->string('IntendedEndUser', 255)->nullable()->after('Purpose');
            }
            if (!in_array('AdditionalNotes', $columns)) {
                $table->text('AdditionalNotes')->nullable()->after('IntendedEndUser');
            }
            if (!in_array('PropertyClassification', $columns)) {
                $table->string('PropertyClassification', 50)->nullable()->after('AdditionalNotes');
            }
            if (!in_array('AssignedEndUserID', $columns)) {
                $table->integer('AssignedEndUserID')->nullable()->after('PropertyClassification');
            }
            if (!in_array('AssignedDepartmentID', $columns)) {
                $table->integer('AssignedDepartmentID')->nullable()->after('AssignedEndUserID');
            }
            if (!in_array('AdminNotes', $columns)) {
                $table->text('AdminNotes')->nullable()->after('AssignedDepartmentID');
            }
        });
        
        // Add indexes if columns were created
        if (Schema::hasColumn('assetrequestdetail', 'AssignedEndUserID')) {
            Schema::table('assetrequestdetail', function (Blueprint $table) {
                if (!Schema::hasIndex('assetrequestdetail', 'assetrequestdetail_assignedenduserid_index')) {
                    $table->index('AssignedEndUserID');
                }
            });
        }
        
        if (Schema::hasColumn('assetrequestdetail', 'AssignedDepartmentID')) {
            Schema::table('assetrequestdetail', function (Blueprint $table) {
                if (!Schema::hasIndex('assetrequestdetail', 'assetrequestdetail_assigneddepartmentid_index')) {
                    $table->index('AssignedDepartmentID');
                }
            });
        }
        
        // Add Purpose column to assetrequest table if it doesn't exist
        $requestColumns = Schema::getColumnListing('assetrequest');
        if (!in_array('Purpose', $requestColumns)) {
            Schema::table('assetrequest', function (Blueprint $table) {
                $table->text('Purpose')->nullable()->after('DepartmentID');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assetrequestdetail', function (Blueprint $table) {
            // Drop indexes first if they exist
            if (Schema::hasIndex('assetrequestdetail', 'assetrequestdetail_assignedenduserid_index')) {
                $table->dropIndex(['AssignedEndUserID']);
            }
            if (Schema::hasIndex('assetrequestdetail', 'assetrequestdetail_assigneddepartmentid_index')) {
                $table->dropIndex(['AssignedDepartmentID']);
            }
            
            // Drop columns if they exist
            $columns = Schema::getColumnListing('assetrequestdetail');
            $columnsToRemove = ['ItemDescription', 'Unit', 'IntendedEndUser', 'AdditionalNotes', 'PropertyClassification', 'AssignedEndUserID', 'AssignedDepartmentID', 'AdminNotes'];
            $existingColumns = array_intersect($columnsToRemove, $columns);
            
            if (!empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }
            
            // Restore PropertyInventoryDetailID as not nullable
            $table->integer('PropertyInventoryDetailID')->nullable(false)->change();
        });
        
        // Remove Purpose from assetrequest if it exists
        if (Schema::hasColumn('assetrequest', 'Purpose')) {
            Schema::table('assetrequest', function (Blueprint $table) {
                $table->dropColumn('Purpose');
            });
        }
    }
};