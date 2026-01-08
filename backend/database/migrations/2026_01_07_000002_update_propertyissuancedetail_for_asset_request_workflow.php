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
        Schema::table('propertyissuancedetail', function (Blueprint $table) {
            // Make InventoryDetailID nullable since asset requests may not have inventory assigned yet
            $table->integer('InventoryDetailID')->nullable()->change();
            
            // Add link to asset request detail
            $table->bigInteger('RequestDetailID')->unsigned()->nullable()->after('IssuanceID');
            
            // Add item description fields for cases where inventory isn't assigned
            $table->string('ItemDescription', 255)->nullable()->after('RequestDetailID');
            $table->string('Unit', 50)->nullable()->after('ItemDescription');
            
            // Add timestamps and soft deletes
            $table->timestamp('created_at')->nullable()->after('Remarks');
            $table->timestamp('updated_at')->nullable()->after('created_at');
            $table->timestamp('deleted_at')->nullable()->after('updated_at');
        });
        
        // Add foreign key constraints
        Schema::table('propertyissuancedetail', function (Blueprint $table) {
            $table->foreign('RequestDetailID')
                ->references('RequestDetailID')
                ->on('assetrequestdetail')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('propertyissuancedetail', function (Blueprint $table) {
            $table->dropForeign(['RequestDetailID']);
            
            $table->dropColumn([
                'RequestDetailID',
                'ItemDescription',
                'Unit',
                'created_at',
                'updated_at',
                'deleted_at'
            ]);
            
            // Restore InventoryDetailID to NOT NULL
            $table->integer('InventoryDetailID')->nullable(false)->change();
        });
    }
};
