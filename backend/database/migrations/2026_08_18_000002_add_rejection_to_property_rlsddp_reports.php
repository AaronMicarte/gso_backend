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
        Schema::table('property_rlsddp_reports', function (Blueprint $table) {
            // Update the ApprovalStatus enum to include 'Rejected'
            $table->enum('ApprovalStatus', ['Pending', 'Approved', 'Rejected'])
                ->default('Pending')
                ->change();
            
            // Add rejection notes column
            $table->text('RejectionNotes')->nullable()->after('ApprovalStatus');
            
            // Add timestamp for when rejection was made
            $table->timestamp('RejectedAt')->nullable()->after('RejectionNotes');
            
            // Add index for rejection status queries
            $table->index('ApprovalStatus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_rlsddp_reports', function (Blueprint $table) {
            $table->dropIndex(['ApprovalStatus']);
            $table->dropColumn(['RejectionNotes', 'RejectedAt']);
            
            // Revert the ApprovalStatus enum back to original 
            $table->enum('ApprovalStatus', ['Pending', 'Approved'])
                ->default('Pending')
                ->change();
        });
    }
};
