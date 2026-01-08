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
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id('AssignmentID');
            $table->unsignedBigInteger('AssetRequestDetailID')->nullable();
            $table->unsignedBigInteger('PropertyInventoryDetailID')->nullable();
            $table->unsignedBigInteger('AssignedUserID')->nullable(); // From user table
            $table->unsignedBigInteger('AssignedDepartmentID')->nullable(); // From department table
            $table->enum('AssignmentType', ['request', 'issuance']); // Whether for request review or actual issuance
            $table->enum('PropertyClassification', ['ICS', 'PAR'])->nullable();
            $table->text('AdminNotes')->nullable();
            $table->unsignedBigInteger('AssignedBy'); // User who made the assignment
            $table->timestamp('AssignedAt');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('AssetRequestDetailID')->references('RequestDetailID')->on('assetrequestdetail');
            // $table->foreign('PropertyInventoryDetailID')->references('InventoryDetailID')->on('propertyinventorydetail');
            $table->foreign('AssignedUserID')->references('UserID')->on('user');
            $table->foreign('AssignedDepartmentID')->references('DepartmentID')->on('department');
            $table->foreign('AssignedBy')->references('UserID')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
    }
};