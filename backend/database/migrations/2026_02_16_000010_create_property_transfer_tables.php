<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propertytransfer', function (Blueprint $table) {
            $table->bigIncrements('TransferID');
            $table->string('TransferNumber', 50)->unique();
            $table->integer('RequestedByUserID');
            $table->integer('FromDepartmentID');
            $table->integer('ToDepartmentID');
            $table->integer('FromEndUserID')->nullable();
            $table->integer('ToEndUserID')->nullable();
            $table->text('Reason')->nullable();
            $table->enum('Status', ['pending', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->integer('ApprovedByUserID')->nullable();
            $table->timestamp('ApprovedAt')->nullable();
            $table->integer('CompletedByUserID')->nullable();
            $table->timestamp('CompletedAt')->nullable();
            $table->timestamp('BorrowerSignedAt')->nullable();
            $table->timestamp('BorrowingSignedAt')->nullable();
            $table->text('GsoNotes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['Status', 'FromDepartmentID']);
            $table->index(['Status', 'ToDepartmentID']);
            $table->index('RequestedByUserID');

            $table->foreign('RequestedByUserID')->references('UserID')->on('user')->onDelete('restrict');
            $table->foreign('FromDepartmentID')->references('DepartmentID')->on('department')->onDelete('restrict');
            $table->foreign('ToDepartmentID')->references('DepartmentID')->on('department')->onDelete('restrict');
            $table->foreign('FromEndUserID')->references('EndUserID')->on('enduser')->onDelete('restrict');
            $table->foreign('ToEndUserID')->references('EndUserID')->on('enduser')->onDelete('restrict');
            $table->foreign('ApprovedByUserID')->references('UserID')->on('user')->onDelete('restrict');
            $table->foreign('CompletedByUserID')->references('UserID')->on('user')->onDelete('restrict');
        });

        Schema::create('propertytransferdetails', function (Blueprint $table) {
            $table->bigIncrements('TransferItemID');
            $table->unsignedBigInteger('TransferID');
            $table->integer('InventoryDetailID');
            $table->integer('Quantity')->default(1);
            $table->text('Remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('InventoryDetailID');

            $table->foreign('TransferID')->references('TransferID')->on('propertytransfer')->onDelete('cascade');
            $table->foreign('InventoryDetailID')->references('InventoryDetailID')->on('propertyinventorydetail')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propertytransferdetails');
        Schema::dropIfExists('propertytransfer');
    }
};
