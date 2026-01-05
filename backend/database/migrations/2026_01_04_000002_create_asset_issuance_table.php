<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_issuance', function (Blueprint $table) {
            $table->id('IssuanceID');
            $table->unsignedBigInteger('RequestID'); // Link to asset_requests
            $table->string('DocumentType', 10); // PAR or ICS
            $table->string('DocumentNumber', 50)->unique(); // PAR-2026-0001 or ICS-2026-0001
            $table->unsignedInteger('PropertyInventoryDetailID');
            $table->integer('QuantityIssued');
            $table->unsignedInteger('IssuedTo'); // UserID (recipient)
            $table->unsignedInteger('IssuedBy'); // UserID (issuer)
            $table->timestamp('IssuanceDate')->nullable();
            $table->enum('Status', [
                'pending_acknowledgment',
                'acknowledged',
                'returned',
                'disposed'
            ])->default('pending_acknowledgment');
            $table->text('IssuanceNotes')->nullable();
            $table->unsignedInteger('AcknowledgedBy')->nullable(); // UserID
            $table->timestamp('AcknowledgedAt')->nullable();
            $table->string('AcknowledgmentSignature')->nullable(); // Path to signature image
            $table->text('AcknowledgmentNotes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('RequestID')->references('RequestID')->on('asset_requests')->onDelete('restrict');
            $table->foreign('PropertyInventoryDetailID')->references('InventoryDetailID')->on('propertyinventorydetail')->onDelete('restrict');
            $table->foreign('IssuedTo')->references('UserID')->on('user')->onDelete('restrict');
            $table->foreign('IssuedBy')->references('UserID')->on('user')->onDelete('restrict');
            $table->foreign('AcknowledgedBy')->references('UserID')->on('user')->onDelete('restrict');

            // Indexes
            $table->index('DocumentType');
            $table->index('Status');
            $table->index('IssuedTo');
            $table->index('IssuanceDate');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_issuance');
    }
};
