<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propertyreturn', function (Blueprint $table) {
            $table->bigIncrements('ReturnID');
            $table->string('ReturnNumber', 50)->unique();
            $table->unsignedBigInteger('BorrowID');
            $table->integer('ReturnedByUserID');
            $table->timestamp('ReturnedAt')->nullable();
            $table->text('Notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('BorrowID');
            $table->index('ReturnedByUserID');
            $table->index('ReturnedAt');

            $table->foreign('BorrowID')->references('BorrowID')->on('propertyborrow')->onDelete('cascade');
            $table->foreign('ReturnedByUserID')->references('UserID')->on('user')->onDelete('restrict');
        });

        Schema::create('propertyreturnitems', function (Blueprint $table) {
            $table->bigIncrements('ReturnItemID');
            $table->unsignedBigInteger('ReturnID');
            $table->unsignedBigInteger('BorrowItemID');
            $table->integer('InventoryDetailID');
            $table->integer('QuantityReturned')->default(1);
            $table->text('Remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('BorrowItemID');
            $table->index('InventoryDetailID');

            $table->foreign('ReturnID')->references('ReturnID')->on('propertyreturn')->onDelete('cascade');
            $table->foreign('BorrowItemID')->references('BorrowItemID')->on('propertyborrowitems')->onDelete('restrict');
            $table->foreign('InventoryDetailID')->references('InventoryDetailID')->on('propertyinventorydetail')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propertyreturnitems');
        Schema::dropIfExists('propertyreturn');
    }
};
