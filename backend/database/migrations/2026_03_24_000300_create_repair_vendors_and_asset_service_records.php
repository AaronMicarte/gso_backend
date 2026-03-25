<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor', function (Blueprint $table) {
            $table->bigIncrements('VendorID');
            $table->string('VendorName', 100);
            $table->string('Address', 255)->nullable();
            $table->string('ContactPerson', 100)->nullable();
            $table->string('ContactNumber', 20)->nullable();
            $table->string('Email', 100)->nullable();
            $table->boolean('IsActive')->default(true);
            $table->dateTime('CreatedDate')->nullable();
            $table->dateTime('UpdatedDate')->nullable();
            $table->softDeletes();

            $table->index('VendorName');
            $table->index('IsActive');
        });

        Schema::create('asset_service_records', function (Blueprint $table) {
            $table->bigIncrements('ServiceRecordID');
            $table->string('ServiceNumber', 50)->unique();
            $table->date('DateReported');
            $table->enum('ReportSource', ['cce_user', 'inventory_check', 'audit', 'other'])->default('other');
            $table->string('SubmittedByName', 150)->nullable();
            $table->integer('SubmittedByEndUserID')->nullable();
            $table->integer('ReportingDepartmentID')->nullable();
            $table->enum('Status', ['pending', 'under_review', 'work_approved', 'repair_in_progress', 'verification', 'closed', 'unrepairable'])->default('pending');
            $table->text('RecordNotes')->nullable();
            $table->integer('CreatedByUserID');
            $table->integer('UpdatedByUserID')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['Status', 'ReportingDepartmentID']);
            $table->index(['DateReported']);

            $table->foreign('SubmittedByEndUserID')->references('EndUserID')->on('enduser')->onDelete('set null');
            $table->foreign('ReportingDepartmentID')->references('DepartmentID')->on('department')->onDelete('set null');
            $table->foreign('CreatedByUserID')->references('UserID')->on('user')->onDelete('restrict');
            $table->foreign('UpdatedByUserID')->references('UserID')->on('user')->onDelete('set null');
        });

        Schema::create('asset_service_records_details', function (Blueprint $table) {
            $table->bigIncrements('ServiceRecordDetailID');
            $table->unsignedBigInteger('ServiceRecordID');
            $table->integer('InventoryDetailID');
            $table->string('PropertyNumber', 100)->nullable();
            $table->string('ItemDescription', 255)->nullable();
            $table->text('ProblemDescription');
            $table->enum('HandlingDecision', ['warranty_claim', 'vendor_repair', 'replacement_evaluation'])->nullable();
            $table->enum('BudgetStatus', ['pending_check', 'warranty_covered', 'budget_available', 'budget_unavailable'])->default('pending_check');
            $table->enum('Status', ['pending', 'under_review', 'work_approved', 'repair_in_progress', 'verification', 'closed', 'unrepairable'])->default('pending');
            $table->bigInteger('AssignedVendorID')->unsigned()->nullable();
            $table->decimal('EstimatedCost', 12, 2)->nullable();
            $table->decimal('ActualCost', 12, 2)->nullable();
            $table->boolean('WarrantyCovered')->default(false);
            $table->string('WarrantyOutcome', 255)->nullable();
            $table->text('CompletionNotes')->nullable();
            $table->text('VerificationNotes')->nullable();
            $table->string('InvoiceAttachment', 255)->nullable();
            $table->json('SupportingAttachments')->nullable();
            $table->integer('CreatedByUserID');
            $table->integer('UpdatedByUserID')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['ServiceRecordID', 'Status']);
            $table->index(['InventoryDetailID']);
            $table->index(['AssignedVendorID']);

            $table->foreign('ServiceRecordID')->references('ServiceRecordID')->on('asset_service_records')->onDelete('cascade');
            $table->foreign('InventoryDetailID')->references('InventoryDetailID')->on('propertyinventorydetail')->onDelete('restrict');
            $table->foreign('AssignedVendorID')->references('VendorID')->on('vendor')->onDelete('set null');
            $table->foreign('CreatedByUserID')->references('UserID')->on('user')->onDelete('restrict');
            $table->foreign('UpdatedByUserID')->references('UserID')->on('user')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_service_records_details');
        Schema::dropIfExists('asset_service_records');
        Schema::dropIfExists('vendor');
    }
};
