<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create asset_requests table for the request workflow
        Schema::create('asset_requests', function (Blueprint $table) {
            $table->id('RequestID');
            $table->string('RequestNumber', 50)->unique();
            $table->unsignedInteger('RequestedBy'); // UserID
            $table->unsignedInteger('DepartmentID');
            $table->unsignedInteger('PropertyInventoryDetailID'); // What they're requesting
            $table->integer('QuantityRequested');
            $table->text('Purpose'); // Required: Why they need it
            $table->enum('Status', [
                'pending',
                'approved',
                'rejected',
                'issued',
                'cancelled'
            ])->default('pending');
            $table->unsignedInteger('ReviewedBy')->nullable(); // Property Officer
            $table->timestamp('ReviewedAt')->nullable();
            $table->text('ReviewNotes')->nullable();
            $table->string('IssuedDocumentType', 10)->nullable(); // 'PAR' or 'ICS'
            $table->unsignedInteger('IssuedDocumentID')->nullable(); // PARID or ICSID
            $table->unsignedInteger('IssuedBy')->nullable(); // Who issued it
            $table->timestamp('IssuedAt')->nullable();
            $table->timestamp('RequestDate');
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('RequestedBy')->references('UserID')->on('user')->onDelete('restrict');
            $table->foreign('DepartmentID')->references('DepartmentID')->on('department')->onDelete('restrict');
            $table->foreign('PropertyInventoryDetailID')->references('InventoryDetailID')->on('propertyinventorydetail')->onDelete('restrict');
            $table->foreign('ReviewedBy')->references('UserID')->on('user')->onDelete('restrict');
            $table->foreign('IssuedBy')->references('UserID')->on('user')->onDelete('restrict');

            // Indexes
            $table->index('Status');
            $table->index('RequestedBy');
            $table->index('DepartmentID');
            $table->index('RequestDate');
        });

        // Add missing columns to PAR table
        if (!Schema::hasColumn('par', 'RequestID')) {
            Schema::table('par', function (Blueprint $table) {
                $table->unsignedBigInteger('RequestID')->nullable()->after('PARID');
                $table->enum('Status', ['pending_acknowledgment', 'acknowledged', 'returned', 'disposed'])->default('pending_acknowledgment')->after('Remarks');
                $table->unsignedInteger('AcknowledgedBy')->nullable()->after('Status');
                $table->timestamp('AcknowledgedAt')->nullable()->after('AcknowledgedBy');
                $table->string('AcknowledgmentSignature', 255)->nullable()->after('AcknowledgedAt');
                $table->text('AcknowledgmentNotes')->nullable()->after('AcknowledgmentSignature');
                $table->timestamp('updated_at')->nullable()->after('CreatedDate');
                $table->softDeletes()->after('updated_at');

                $table->foreign('RequestID')->references('RequestID')->on('asset_requests')->onDelete('set null');
                $table->foreign('AcknowledgedBy')->references('UserID')->on('user')->onDelete('restrict');
            });
        }

        // Add missing columns to ICS table
        if (!Schema::hasColumn('inventorycustodianslip', 'RequestID')) {
            Schema::table('inventorycustodianslip', function (Blueprint $table) {
                $table->unsignedBigInteger('RequestID')->nullable()->after('ICSID');
                $table->enum('Status', ['pending_acknowledgment', 'acknowledged', 'returned', 'disposed'])->default('pending_acknowledgment')->after('Remarks');
                $table->unsignedInteger('AcknowledgedBy')->nullable()->after('Status');
                $table->timestamp('AcknowledgedAt')->nullable()->after('AcknowledgedBy');
                $table->string('AcknowledgmentSignature', 255)->nullable()->after('AcknowledgedAt');
                $table->text('AcknowledgmentNotes')->nullable()->after('AcknowledgmentSignature');
                $table->timestamp('updated_at')->nullable()->after('CreatedDate');
                $table->softDeletes()->after('updated_at');

                $table->foreign('RequestID')->references('RequestID')->on('asset_requests')->onDelete('set null');
                $table->foreign('AcknowledgedBy')->references('UserID')->on('user')->onDelete('restrict');
            });
        }

        // Update icsdetail to use InventoryDetailID instead of PropertyID
        if (!Schema::hasColumn('icsdetail', 'PropertyInventoryDetailID')) {
            Schema::table('icsdetail', function (Blueprint $table) {
                $table->unsignedBigInteger('PropertyInventoryDetailID')->nullable()->after('PropertyID');
                $table->foreign('PropertyInventoryDetailID')->references('InventoryDetailID')->on('propertyinventorydetail')->onDelete('restrict');
            });
        }

        // Create audit logs table
        Schema::create('asset_audit_logs', function (Blueprint $table) {
            $table->id('AuditID');
            $table->string('EntityType', 50); // 'asset_request', 'par', 'ics'
            $table->unsignedBigInteger('EntityID'); // RequestID, PARID, or ICSID
            $table->string('Action', 100); // 'created', 'approved', 'rejected', 'issued', 'acknowledged'
            $table->unsignedInteger('PerformedBy'); // UserID
            $table->text('Details')->nullable(); // JSON with before/after or additional context
            $table->string('IPAddress', 50)->nullable();
            $table->text('UserAgent')->nullable();
            $table->timestamp('CreatedAt');

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
        
        if (Schema::hasColumn('icsdetail', 'PropertyInventoryDetailID')) {
            Schema::table('icsdetail', function (Blueprint $table) {
                $table->dropForeign(['PropertyInventoryDetailID']);
                $table->dropColumn('PropertyInventoryDetailID');
            });
        }

        if (Schema::hasColumn('inventorycustodianslip', 'RequestID')) {
            Schema::table('inventorycustodianslip', function (Blueprint $table) {
                $table->dropForeign(['RequestID']);
                $table->dropForeign(['AcknowledgedBy']);
                $table->dropColumn([
                    'RequestID',
                    'Status',
                    'AcknowledgedBy',
                    'AcknowledgedAt',
                    'AcknowledgmentSignature',
                    'AcknowledgmentNotes',
                    'updated_at',
                    'deleted_at'
                ]);
            });
        }

        if (Schema::hasColumn('par', 'RequestID')) {
            Schema::table('par', function (Blueprint $table) {
                $table->dropForeign(['RequestID']);
                $table->dropForeign(['AcknowledgedBy']);
                $table->dropColumn([
                    'RequestID',
                    'Status',
                    'AcknowledgedBy',
                    'AcknowledgedAt',
                    'AcknowledgmentSignature',
                    'AcknowledgmentNotes',
                    'updated_at',
                    'deleted_at'
                ]);
            });
        }

        Schema::dropIfExists('asset_requests');
    }
};
