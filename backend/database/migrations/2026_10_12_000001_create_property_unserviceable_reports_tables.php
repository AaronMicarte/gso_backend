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
        Schema::create('property_unserviceable_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('report_number', 50)->unique();
            $table->string('status', 30)->default('active');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->string('attachment_path', 255)->nullable();
            $table->string('attachment_name', 255)->nullable();
            $table->string('attachment_mime', 100)->nullable();
            $table->unsignedBigInteger('attachment_size')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('created_by_user_id');
            $table->index('status');
        });

        Schema::create('property_unserviceable_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('unserviceable_report_id');
            $table->unsignedBigInteger('inventory_detail_id');
            $table->string('property_number', 100)->nullable();
            $table->text('item_description')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('unserviceable_report_id');
            $table->index('inventory_detail_id');
        });

        Schema::create('property_unserviceable_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('unserviceable_report_id');
            $table->string('file_path', 255);
            $table->string('file_name', 255);
            $table->string('file_mime', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamps();

            $table->index('unserviceable_report_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_unserviceable_attachments');
        Schema::dropIfExists('property_unserviceable_items');
        Schema::dropIfExists('property_unserviceable_reports');
    }
};
