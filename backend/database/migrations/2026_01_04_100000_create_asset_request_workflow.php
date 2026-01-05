<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create assetrequest table (header)
        Schema::create('assetrequest', function (Blueprint $table) {
            $table->id('RequestID');
            $table->string('RequestNumber', 50)->unique();
            $table->integer('RequestedBy'); // UserID (Int Signed)
            $table->integer('DepartmentID'); // DepartmentID (Int Signed)
            $table->enum('Status', [
                'pending',
                'approved',
                'rejected',
                'issued',
                'cancelled'
            ])->default('pending');
            $table->integer('ReviewedBy')->nullable(); // Property Officer (User ID)
            $table->timestamp('ReviewedAt')->nullable();
            $table->text('ReviewNotes')->nullable();
            $table->integer('IssuedBy')->nullable(); // Who issued it (User ID)
            $table->timestamp('IssuedAt')->nullable();
            $table->timestamp('RequestDate')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('Status');
            $table->index('RequestedBy');
            $table->index('DepartmentID');
            $table->index('RequestDate');
            
            // Foreign keys
            $table->foreign('RequestedBy')->references('UserID')->on('user')->onDelete('restrict');
            $table->foreign('DepartmentID')->references('DepartmentID')->on('department')->onDelete('restrict');
            $table->foreign('ReviewedBy')->references('UserID')->on('user')->onDelete('restrict');
            $table->foreign('IssuedBy')->references('UserID')->on('user')->onDelete('restrict');
        });

        // Create assetrequestdetail table (detail lines)
        Schema::create('assetrequestdetail', function (Blueprint $table) {
            $table->id('RequestDetailID');
            $table->unsignedBigInteger('RequestID'); // FK to assetrequest
            $table->integer('PropertyInventoryDetailID')->nullable(); // InventoryDetailID (Int Signed) - Nullable for RIS
            $table->string('ItemDescription')->nullable(); // For free-form RIS
            $table->string('Unit', 50)->nullable(); // Unit of measure
            $table->integer('QuantityRequested');
            $table->text('Purpose'); // Required: Why they need this specific item
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('RequestID');
            $table->index('PropertyInventoryDetailID');
            
            // Foreign keys
            $table->foreign('RequestID')->references('RequestID')->on('assetrequest')->onDelete('cascade');
            $table->foreign('PropertyInventoryDetailID')->references('InventoryDetailID')->on('propertyinventorydetail')->onDelete('restrict');
        });

        // Add RequestID to propertyissuance to link back to request (if table exists)
        if (Schema::hasTable('propertyissuance')) {
            Schema::table('propertyissuance', function (Blueprint $table) {
                $table->unsignedBigInteger('RequestID')->nullable()->after('IssuanceID');
                $table->foreign('RequestID')->references('RequestID')->on('assetrequest')->onDelete('set null');
            });
        }

        // Create audit log table
        Schema::create('asset_audit_logs', function (Blueprint $table) {
            $table->id('AuditID');
            $table->string('EntityType', 50); // 'assetrequest', 'assetrequestdetail', 'property_issuance'
            $table->integer('EntityID');
            $table->string('Action', 100);
            $table->integer('PerformedBy');
            $table->json('Details')->nullable();
            $table->string('IPAddress', 50)->nullable();
            $table->text('UserAgent')->nullable();
            $table->timestamp('CreatedAt')->nullable();

            $table->foreign('PerformedBy')->references('UserID')->on('user')->onDelete('restrict');
            $table->index(['EntityType', 'EntityID']);
            $table->index('PerformedBy');
            $table->index('CreatedAt');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_audit_logs');
        
        if (Schema::hasTable('propertyissuance') && Schema::hasColumn('propertyissuance', 'RequestID')) {
            Schema::table('propertyissuance', function (Blueprint $table) {
                $table->dropForeign(['RequestID']);
                $table->dropColumn('RequestID');
            });
        }
        
        Schema::dropIfExists('asset_requests');
    }
};
