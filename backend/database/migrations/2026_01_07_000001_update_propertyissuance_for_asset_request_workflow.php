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
            // Add document type and number for PAR/ICS
            $table->string('DocumentType', 10)->nullable()->after('IssuedTo')->comment('PAR or ICS');
            $table->string('DocumentNumber', 50)->nullable()->after('DocumentType');
            
            // Add status tracking
            $table->string('Status', 50)->default('pending_acknowledgment')->after('DocumentNumber')
                ->comment('pending_acknowledgment, acknowledged, completed');
            
            // Add acknowledgment fields
            $table->text('AcknowledgmentSignature')->nullable()->after('Remarks')
                ->comment('Base64 encoded signature');
            $table->datetime('AcknowledgedAt')->nullable()->after('AcknowledgmentSignature');
            $table->text('AcknowledgmentNotes')->nullable()->after('AcknowledgedAt');
            
            // Add soft deletes for audit trail
            $table->timestamp('deleted_at')->nullable()->after('AcknowledgmentNotes');
        });
        
        // Add index for document number uniqueness
        Schema::table('propertyissuance', function (Blueprint $table) {
            $table->index('DocumentNumber');
            $table->index('Status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('propertyissuance', function (Blueprint $table) {
            $table->dropIndex(['DocumentNumber']);
            $table->dropIndex(['Status']);
            
            $table->dropColumn([
                'DocumentType',
                'DocumentNumber',
                'Status',
                'AcknowledgmentSignature',
                'AcknowledgedAt',
                'AcknowledgmentNotes',
                'deleted_at'
            ]);
        });
    }
};
