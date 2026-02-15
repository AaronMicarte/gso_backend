<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('inspection_reports')) {
            Schema::create('inspection_reports', function (Blueprint $table) {
                $table->bigIncrements('InspectionReportID');
                $table->integer('DRID');
                $table->integer('InspectedBy');
            $table->string('InspectorName', 150);
            $table->date('DateInspected');
            $table->enum('Status', ['pending', 'inspected'])->default('pending');
            $table->text('Remarks')->nullable();
            $table->timestamp('CompletedAt')->nullable();
            $table->string('IPAddress', 45)->nullable();
            $table->string('UserAgent', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('DRID');
            $table->foreign('DRID')->references('DRID')->on('deliveryreceipt');
            $table->foreign('InspectedBy')->references('UserID')->on('user');
            });
        }

        if (! Schema::hasTable('inspection_report_items')) {
            Schema::create('inspection_report_items', function (Blueprint $table) {
                $table->bigIncrements('InspectionReportDetailID');
                $table->unsignedBigInteger('InspectionReportID');
                $table->integer('DRDetailID');
            $table->enum('Status', ['pending', 'inspected'])->default('pending');
            $table->text('Remarks')->nullable();
            $table->timestamps();

            $table->unique(['InspectionReportID', 'DRDetailID']);
            $table->foreign('InspectionReportID')->references('InspectionReportID')->on('inspection_reports');
            $table->foreign('DRDetailID')->references('DRDetailID')->on('deliveryreceiptdetail');
            });
        }

        if (! Schema::hasColumn('deliveryreceipt', 'InspectionStatus')) {
            Schema::table('deliveryreceipt', function (Blueprint $table) {
                $table->enum('InspectionStatus', ['pending', 'inspected'])->default('pending')->after('Remarks');
                $table->timestamp('InspectionCompletedAt')->nullable()->after('InspectionStatus');
            });
        }

        if (! Schema::hasColumn('deliveryreceiptdetail', 'InspectionStatus')) {
            Schema::table('deliveryreceiptdetail', function (Blueprint $table) {
                $table->enum('InspectionStatus', ['pending', 'inspected'])->default('pending')->after('TotalCost');
            });
        }

        DB::table('deliveryreceipt')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('inspectionacceptancereport')
                    ->whereColumn('inspectionacceptancereport.DRID', 'deliveryreceipt.DRID')
                    ->whereNull('inspectionacceptancereport.deleted_at');
            })
            ->update([
                'InspectionStatus' => 'inspected',
                'InspectionCompletedAt' => now(),
            ]);

        DB::table('deliveryreceiptdetail')
            ->whereIn('DRID', function ($query) {
                $query->select('DRID')
                    ->from('deliveryreceipt')
                    ->where('InspectionStatus', 'inspected');
            })
            ->update([
                'InspectionStatus' => 'inspected',
            ]);
    }

    public function down(): void
    {
        Schema::table('deliveryreceiptdetail', function (Blueprint $table) {
            $table->dropColumn('InspectionStatus');
        });

        Schema::table('deliveryreceipt', function (Blueprint $table) {
            $table->dropColumn(['InspectionStatus', 'InspectionCompletedAt']);
        });

        Schema::dropIfExists('inspection_report_items');
        Schema::dropIfExists('inspection_reports');
    }
};
