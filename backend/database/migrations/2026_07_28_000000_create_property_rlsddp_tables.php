<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_rlsddp_reports', function (Blueprint $table) {
            $table->bigIncrements('RlsddpID');
            $table->string('RlsddpNumber')->unique();
            $table->unsignedBigInteger('DepartmentID');
            $table->string('AccountableOfficer');
            $table->enum('Status', ['Lost', 'Stolen', 'Damaged', 'Destroyed']);
            $table->boolean('PoliceNotified')->default(false);
            $table->string('PoliceStation')->nullable();
            $table->date('DateReported')->nullable();
            $table->text('Remarks')->nullable();
            $table->unsignedBigInteger('CreatedByUserID');
            $table->unsignedBigInteger('UpdatedByUserID')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('DepartmentID');
            $table->index('CreatedByUserID');
            $table->index('Status');
        });

        Schema::create('property_rlsddp_items', function (Blueprint $table) {
            $table->bigIncrements('RlsddpItemID');
            $table->unsignedBigInteger('RlsddpID');
            $table->unsignedBigInteger('InventoryDetailID');
            $table->string('PropertyNumber')->nullable();
            $table->string('ParNumber')->nullable();
            $table->decimal('AcquisitionCost', 15, 2)->default(0.00);
            $table->text('Description');
            $table->text('Remarks')->nullable();
            $table->timestamps();

            $table->index('RlsddpID');
            $table->index('InventoryDetailID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_rlsddp_items');
        Schema::dropIfExists('property_rlsddp_reports');
    }
};
