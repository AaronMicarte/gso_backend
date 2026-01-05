<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
            $table->unsignedInteger('IssuedBy')->nullable(); // Who issued it
            $table->timestamp('IssuedAt')->nullable();
            $table->timestamp('RequestDate')->nullable();
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
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_requests');
    }
};
