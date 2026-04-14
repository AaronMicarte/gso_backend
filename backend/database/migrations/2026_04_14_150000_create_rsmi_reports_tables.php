<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rsmi_reports', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->date('report_date')->unique();
            $table->string('serial_no', 20)->unique();
            $table->unsignedSmallInteger('serial_year');
            $table->unsignedTinyInteger('serial_month');
            $table->unsignedInteger('serial_sequence');

            $table->string('entity_name', 255);
            $table->string('fund_cluster', 50)->nullable();

            $table->string('certification_name', 255)->nullable();
            $table->integer('certified_by_user_id')->nullable();
            $table->dateTime('certified_at')->nullable();
            $table->string('certification_signature_source', 20)->nullable();
            $table->string('certification_signature_disk', 32)->nullable();
            $table->string('certification_signature_path', 512)->nullable();
            $table->string('certification_signature_url', 512)->nullable();

            $table->integer('created_by_user_id');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['serial_year', 'serial_month', 'serial_sequence'], 'rsmi_serial_unique');
            $table->index('report_date');

            $table->foreign('certified_by_user_id')->references('UserID')->on('user')->onDelete('restrict');
            $table->foreign('created_by_user_id')->references('UserID')->on('user')->onDelete('restrict');
        });

        Schema::create('rsmi_report_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('rsmi_report_id');

            $table->unsignedBigInteger('ris_id')->nullable();
            $table->string('ris_no', 20)->nullable();
            $table->string('responsibility_center_code', 50)->nullable();

            $table->string('stock_no', 80);
            $table->string('item_description', 255);
            $table->string('unit', 50)->default('N/A');
            $table->unsignedInteger('quantity_issued');

            $table->timestamps();

            $table->index('rsmi_report_id');
            $table->index('stock_no');

            $table->foreign('rsmi_report_id')->references('id')->on('rsmi_reports')->onDelete('cascade');
            $table->foreign('ris_id')->references('id')->on('requisition_issue_slips')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rsmi_report_items');
        Schema::dropIfExists('rsmi_reports');
    }
};
