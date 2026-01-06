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
        // Modify assetrequestdetail table to support new item requests (not just from existing inventory)
        Schema::table('assetrequestdetail', function (Blueprint $table) {
            // Make PropertyInventoryDetailID nullable (for new item requests)
            $table->integer('PropertyInventoryDetailID')->nullable()->change();
            
            // Add fields for new item requests
            $table->string('ItemDescription', 255)->nullable()->after('PropertyInventoryDetailID');
            $table->string('Unit', 50)->nullable()->after('ItemDescription');
            $table->string('IntendedEndUser', 255)->nullable()->after('Purpose');
            $table->text('AdditionalNotes')->nullable()->after('IntendedEndUser');
            
            // Add admin review fields
            $table->string('PropertyClassification', 50)->nullable()->after('AdditionalNotes');
            $table->integer('AssignedEndUserID')->nullable()->after('PropertyClassification');
            $table->integer('AssignedDepartmentID')->nullable()->after('AssignedEndUserID');
            $table->text('AdminNotes')->nullable()->after('AssignedDepartmentID');
            
            // Add indexes
            $table->index('AssignedEndUserID');
            $table->index('AssignedDepartmentID');
        });
        
        // Add foreign key constraints
        Schema::table('assetrequestdetail', function (Blueprint $table) {
            $table->foreign('AssignedEndUserID')->references('EndUserID')->on('enduser')->onDelete('set null');
            $table->foreign('AssignedDepartmentID')->references('DepartmentID')->on('department')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assetrequestdetail', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['AssignedEndUserID']);
            $table->dropForeign(['AssignedDepartmentID']);
            
            // Drop columns
            $table->dropColumn([
                'ItemDescription',
                'Unit', 
                'IntendedEndUser',
                'AdditionalNotes',
                'PropertyClassification',
                'AssignedEndUserID',
                'AssignedDepartmentID',
                'AdminNotes'
            ]);
            
            // Restore PropertyInventoryDetailID as not nullable
            $table->integer('PropertyInventoryDetailID')->nullable(false)->change();
        });
    }
};