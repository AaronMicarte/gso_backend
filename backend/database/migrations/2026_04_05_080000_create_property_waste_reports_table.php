<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_waste_reports', function (Blueprint $table) {
            $table->bigIncrements('PropertyWasteReportID');
            $table->string('WasteNumber', 30)->unique();
            $table->unsignedBigInteger('SourceServiceRecordID')->nullable();
            $table->integer('DepartmentID')->nullable();
            $table->enum('Status', ['draft', 'for_approval', 'approved', 'completed', 'cancelled'])->default('draft');
            $table->text('Notes')->nullable();
            $table->integer('CreatedByUserID')->nullable();
            $table->integer('UpdatedByUserID')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('SourceServiceRecordID')
                ->references('ServiceRecordID')
                ->on('asset_service_records')
                ->nullOnDelete();

            $table->foreign('DepartmentID')
                ->references('DepartmentID')
                ->on('department')
                ->nullOnDelete();

            $table->foreign('CreatedByUserID')
                ->references('UserID')
                ->on('user')
                ->nullOnDelete();

            $table->foreign('UpdatedByUserID')
                ->references('UserID')
                ->on('user')
                ->nullOnDelete();
        });

        Schema::create('property_waste_items', function (Blueprint $table) {
            $table->bigIncrements('PropertyWasteItemID');
            $table->unsignedBigInteger('PropertyWasteReportID');
            $table->unsignedBigInteger('ServiceRecordDetailID')->nullable();
            $table->integer('InventoryDetailID');
            $table->string('PropertyNumber', 80)->nullable();
            $table->string('ItemDescription', 255)->nullable();
            $table->integer('ConditionID')->nullable();
            $table->integer('DisposalMethodID')->nullable();
            $table->text('DisposalMethodNote')->nullable();
            $table->enum('Status', ['pending_method', 'method_selected', 'for_disposal', 'disposed'])->default('pending_method');
            $table->integer('ActionedByUserID')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('ServiceRecordDetailID');

            $table->foreign('PropertyWasteReportID')
                ->references('PropertyWasteReportID')
                ->on('property_waste_reports')
                ->cascadeOnDelete();

            $table->foreign('ServiceRecordDetailID')
                ->references('ServiceRecordDetailID')
                ->on('asset_service_records_details')
                ->nullOnDelete();

            $table->foreign('InventoryDetailID')
                ->references('InventoryDetailID')
                ->on('propertyinventorydetail')
                ->cascadeOnDelete();

            $table->foreign('ConditionID')
                ->references('ConditionID')
                ->on('condition')
                ->nullOnDelete();

            $table->foreign('DisposalMethodID')
                ->references('DisposalMethodID')
                ->on('disposalmethod')
                ->nullOnDelete();

            $table->foreign('ActionedByUserID')
                ->references('UserID')
                ->on('user')
                ->nullOnDelete();
        });

        if (!Schema::hasColumn('disposalmethod', 'IconKey')) {
            Schema::table('disposalmethod', function (Blueprint $table) {
                $table->string('IconKey', 50)->nullable()->after('Description');
            });
        }

        DB::table('disposalmethod')
            ->whereNull('IconKey')
            ->whereRaw('LOWER(MethodName) LIKE ?', ['%condemn%'])
            ->update(['IconKey' => 'gavel']);

        DB::table('disposalmethod')
            ->whereNull('IconKey')
            ->whereRaw('LOWER(MethodName) LIKE ?', ['%donat%'])
            ->update(['IconKey' => 'gift']);

        DB::table('disposalmethod')
            ->whereNull('IconKey')
            ->whereRaw('LOWER(MethodName) LIKE ?', ['%auction%'])
            ->update(['IconKey' => 'badge-dollar-sign']);

        DB::table('disposalmethod')
            ->whereNull('IconKey')
            ->whereRaw('LOWER(MethodName) LIKE ?', ['%recycl%'])
            ->update(['IconKey' => 'recycle']);

        DB::table('disposalmethod')
            ->whereNull('IconKey')
            ->whereRaw('LOWER(MethodName) LIKE ?', ['%sale%'])
            ->update(['IconKey' => 'hand-coins']);

        DB::table('disposalmethod')
            ->whereNull('IconKey')
            ->update(['IconKey' => 'trash-2']);
    }

    public function down(): void
    {
        Schema::dropIfExists('property_waste_items');
        Schema::dropIfExists('property_waste_reports');

        if (Schema::hasColumn('disposalmethod', 'IconKey')) {
            Schema::table('disposalmethod', function (Blueprint $table) {
                $table->dropColumn('IconKey');
            });
        }
    }
};
