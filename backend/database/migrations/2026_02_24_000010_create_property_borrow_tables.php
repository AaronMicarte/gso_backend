<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propertyborrow', function (Blueprint $table) {
            $table->bigIncrements('BorrowID');
            $table->string('BorrowNumber', 50)->unique();
            $table->integer('RequestedByUserID');
            $table->integer('FromDepartmentID');
            $table->integer('ToDepartmentID');
            $table->integer('FromEndUserID')->nullable();
            $table->integer('ToEndUserID')->nullable();
            $table->text('Purpose')->nullable();
            $table->unsignedInteger('RequestedItemsCount')->nullable();
            $table->date('DueDate');
            $table->enum('ReleasingDecision', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedInteger('ReleasingApprovedCount')->nullable();
            $table->text('ReleasingDecisionReason')->nullable();
            $table->integer('ReleasingReviewedByUserID')->nullable();
            $table->timestamp('ReleasingReviewedAt')->nullable();
            $table->enum('Status', ['pending', 'approved', 'rejected', 'borrowed', 'returned', 'cancelled'])->default('pending');
            $table->integer('ApprovedByUserID')->nullable();
            $table->timestamp('ApprovedAt')->nullable();
            $table->timestamp('ReleasedSignedAt')->nullable();
            $table->timestamp('ReceivedSignedAt')->nullable();
            $table->timestamp('BorrowedAt')->nullable();
            $table->integer('ReturnedByUserID')->nullable();
            $table->timestamp('ReturnedAt')->nullable();
            $table->text('ReturnNotes')->nullable();
            $table->text('GsoNotes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['Status', 'FromDepartmentID']);
            $table->index(['Status', 'ToDepartmentID']);
            $table->index('RequestedByUserID');
            $table->index('DueDate');

            $table->foreign('RequestedByUserID')->references('UserID')->on('user')->onDelete('restrict');
            $table->foreign('FromDepartmentID')->references('DepartmentID')->on('department')->onDelete('restrict');
            $table->foreign('ToDepartmentID')->references('DepartmentID')->on('department')->onDelete('restrict');
            $table->foreign('FromEndUserID')->references('EndUserID')->on('enduser')->onDelete('restrict');
            $table->foreign('ToEndUserID')->references('EndUserID')->on('enduser')->onDelete('restrict');
            $table->foreign('ReleasingReviewedByUserID')->references('UserID')->on('user')->onDelete('restrict');
            $table->foreign('ApprovedByUserID')->references('UserID')->on('user')->onDelete('restrict');
            $table->foreign('ReturnedByUserID')->references('UserID')->on('user')->onDelete('restrict');
        });

        Schema::create('propertyborrowitems', function (Blueprint $table) {
            $table->bigIncrements('BorrowItemID');
            $table->unsignedBigInteger('BorrowID');
            $table->integer('InventoryDetailID');
            $table->integer('Quantity')->default(1);
            $table->text('Remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('InventoryDetailID');

            $table->foreign('BorrowID')->references('BorrowID')->on('propertyborrow')->onDelete('cascade');
            $table->foreign('InventoryDetailID')->references('InventoryDetailID')->on('propertyinventorydetail')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propertyborrowitems');
        Schema::dropIfExists('propertyborrow');
    }
};
