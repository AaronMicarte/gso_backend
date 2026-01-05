<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_audit_logs', function (Blueprint $table) {
            $table->id('AuditID');
            $table->string('EntityType', 50); // 'asset_request', 'asset_issuance', etc.
            $table->unsignedBigInteger('EntityID'); // RequestID or IssuanceID
            $table->string('Action', 100); // 'created', 'approved', 'rejected', 'issued', 'acknowledged'
            $table->unsignedInteger('PerformedBy'); // UserID
            $table->text('Details')->nullable(); // JSON with before/after or additional context
            $table->string('IPAddress', 50)->nullable();
            $table->text('UserAgent')->nullable();
            $table->timestamp('CreatedAt')->nullable();

            // Foreign keys
            $table->foreign('PerformedBy')->references('UserID')->on('user')->onDelete('restrict');

            // Indexes
            $table->index(['EntityType', 'EntityID']);
            $table->index('PerformedBy');
            $table->index('CreatedAt');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_audit_logs');
    }
};
