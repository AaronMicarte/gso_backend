<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisition_issue_slips', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('asset_request_id');

            // Control number format: YYYY-MM-#### (serial increments per year)
            $table->string('ris_number', 20)->unique();
            $table->unsignedSmallInteger('ris_year');
            $table->unsignedTinyInteger('ris_month');
            $table->unsignedInteger('ris_serial');

            // Header fields printed on the form
            $table->string('entity_name', 255);
            $table->string('fund_cluster', 50);
            $table->string('division', 255);
            $table->string('office', 255);
            $table->string('responsibility_center_code', 50);

            $table->text('purpose');

            $table->string('status', 30)->default('for_issuance'); // for_issuance | issued | received | cancelled

            // IMPORTANT: user.UserID is int(11) signed in the existing schema
            $table->integer('requested_by_user_id');
            // Use datetime to avoid strict-mode timestamp default issues in MariaDB
            $table->dateTime('requested_at');

            $table->integer('approved_by_user_id');
            $table->dateTime('approved_at');

            $table->integer('prepared_by_user_id');
            $table->dateTime('prepared_at');

            $table->integer('issued_by_user_id')->nullable();
            $table->dateTime('issued_at')->nullable();

            $table->integer('received_by_user_id')->nullable();
            $table->dateTime('received_at')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique('asset_request_id');
            $table->index(['ris_year', 'ris_serial']);
            $table->index('asset_request_id');
            $table->index('status');

            $table->foreign('asset_request_id')->references('RequestID')->on('assetrequest')->onDelete('restrict');
            $table->foreign('requested_by_user_id')->references('UserID')->on('user')->onDelete('restrict');
            $table->foreign('approved_by_user_id')->references('UserID')->on('user')->onDelete('restrict');
            $table->foreign('prepared_by_user_id')->references('UserID')->on('user')->onDelete('restrict');
            $table->foreign('issued_by_user_id')->references('UserID')->on('user')->onDelete('restrict');
            $table->foreign('received_by_user_id')->references('UserID')->on('user')->onDelete('restrict');
        });

        Schema::create('requisition_issue_slip_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ris_id');

            $table->unsignedBigInteger('asset_request_detail_id');

            // Link to inventory stock item (optional until issuance mapping happens)
            // IMPORTANT: propertyinventorydetail.InventoryDetailID is int(11) signed
            $table->integer('inventory_detail_id')->nullable();

            // Snapshot columns for printing (never blank in UI; DB allows safe defaults)
            $table->string('stock_no', 80)->default('N/A');
            $table->string('unit', 50)->default('N/A');
            $table->string('description', 255)->default('N/A');

            $table->unsignedInteger('quantity_requested');

            // Stock availability as checked by supply/property office at time of issuance preparation
            $table->boolean('stock_available')->default(false);

            $table->unsignedInteger('quantity_issued')->default(0);
            $table->string('issue_remarks', 255)->default('-');

            $table->timestamps();
            $table->softDeletes();

            $table->index('ris_id');
            $table->index('inventory_detail_id');

            $table->foreign('ris_id')->references('id')->on('requisition_issue_slips')->onDelete('cascade');
            $table->foreign('asset_request_detail_id')->references('RequestDetailID')->on('assetrequestdetail')->onDelete('restrict');
            $table->foreign('inventory_detail_id')->references('InventoryDetailID')->on('propertyinventorydetail')->onDelete('restrict');
        });

        Schema::create('requisition_issue_slip_signatures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ris_id');
            $table->string('role', 30); // requested_by | approved_by | issued_by | received_by
            // IMPORTANT: user.UserID is int(11) signed
            $table->integer('signed_by_user_id');

            $table->string('source', 20); // canvas|upload|linked
            $table->string('storage_disk', 32)->default('public');
            $table->string('storage_path', 512);
            $table->string('url', 512)->nullable();
            $table->string('mime', 50)->default('image/png');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('size_bytes')->nullable();
            $table->char('sha256_hash', 64);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->unique(['ris_id', 'role']);
            $table->index('signed_by_user_id');
            $table->index('sha256_hash');

            $table->foreign('ris_id')->references('id')->on('requisition_issue_slips')->onDelete('cascade');
            $table->foreign('signed_by_user_id')->references('UserID')->on('user')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisition_issue_slip_signatures');
        Schema::dropIfExists('requisition_issue_slip_items');
        Schema::dropIfExists('requisition_issue_slips');
    }
};
