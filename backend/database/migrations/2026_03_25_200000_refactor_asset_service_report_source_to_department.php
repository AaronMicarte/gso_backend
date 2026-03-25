<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('asset_service_records')) {
            return;
        }

        Schema::table('asset_service_records', function (Blueprint $table) {
            if (!Schema::hasColumn('asset_service_records', 'ReportSourceDepartmentID')) {
                $table->integer('ReportSourceDepartmentID')->nullable()->after('ReportSource');
            }
        });

        DB::statement('UPDATE asset_service_records SET ReportSourceDepartmentID = ReportingDepartmentID WHERE ReportSourceDepartmentID IS NULL');

        DB::statement('UPDATE asset_service_records sr LEFT JOIN user u ON u.UserID = sr.CreatedByUserID SET sr.ReportSourceDepartmentID = u.DepartmentID WHERE sr.ReportSourceDepartmentID IS NULL');

        $fallbackDepartmentId = DB::table('department')->orderBy('DepartmentID')->value('DepartmentID');
        if (!empty($fallbackDepartmentId)) {
            DB::table('asset_service_records')
                ->whereNull('ReportSourceDepartmentID')
                ->update(['ReportSourceDepartmentID' => (int) $fallbackDepartmentId]);
        }

        $this->dropForeignIfExists('asset_service_records', 'SubmittedByEndUserID');
        $this->dropForeignIfExists('asset_service_records', 'ReportingDepartmentID');
        $this->dropForeignIfExists('asset_service_records', 'ReportSource');

        $this->dropIndexIfExists('asset_service_records', 'asset_service_records_status_reportingdepartmentid_index');
        $this->dropIndexIfExists('asset_service_records', 'asset_service_records_submittedbyenduserid_foreign');
        $this->dropIndexIfExists('asset_service_records', 'asset_service_records_reportingdepartmentid_foreign');

        Schema::table('asset_service_records', function (Blueprint $table) {
            if (Schema::hasColumn('asset_service_records', 'SubmittedByName')) {
                $table->dropColumn('SubmittedByName');
            }
            if (Schema::hasColumn('asset_service_records', 'SubmittedByEndUserID')) {
                $table->dropColumn('SubmittedByEndUserID');
            }
            if (Schema::hasColumn('asset_service_records', 'ReportingDepartmentID')) {
                $table->dropColumn('ReportingDepartmentID');
            }
            if (Schema::hasColumn('asset_service_records', 'ReportSource')) {
                $table->dropColumn('ReportSource');
            }
        });

        DB::statement('ALTER TABLE asset_service_records CHANGE ReportSourceDepartmentID ReportSource INT NULL');

        Schema::table('asset_service_records', function (Blueprint $table) {
            $table->index(['Status', 'ReportSource'], 'asset_service_records_status_reportsource_index');
            $table->foreign('ReportSource')->references('DepartmentID')->on('department')->onDelete('set null');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('asset_service_records')) {
            return;
        }

        $this->dropForeignIfExists('asset_service_records', 'ReportSource');
        $this->dropIndexIfExists('asset_service_records', 'asset_service_records_status_reportsource_index');

        Schema::table('asset_service_records', function (Blueprint $table) {
            if (!Schema::hasColumn('asset_service_records', 'SubmittedByName')) {
                $table->string('SubmittedByName', 150)->nullable()->after('ReportSource');
            }
            if (!Schema::hasColumn('asset_service_records', 'SubmittedByEndUserID')) {
                $table->integer('SubmittedByEndUserID')->nullable()->after('SubmittedByName');
            }
            if (!Schema::hasColumn('asset_service_records', 'ReportingDepartmentID')) {
                $table->integer('ReportingDepartmentID')->nullable()->after('SubmittedByEndUserID');
            }
        });

        DB::statement('UPDATE asset_service_records SET ReportingDepartmentID = ReportSource WHERE ReportingDepartmentID IS NULL');

        Schema::table('asset_service_records', function (Blueprint $table) {
            if (Schema::hasColumn('asset_service_records', 'ReportSource')) {
                $table->dropColumn('ReportSource');
            }
            $table->enum('ReportSource', ['cce_user', 'inventory_check', 'audit', 'other'])->default('other')->after('DateReported');
            $table->index(['Status', 'ReportingDepartmentID'], 'asset_service_records_status_reportingdepartmentid_index');
            $table->foreign('SubmittedByEndUserID')->references('EndUserID')->on('enduser')->onDelete('set null');
            $table->foreign('ReportingDepartmentID')->references('DepartmentID')->on('department')->onDelete('set null');
        });
    }

    private function dropForeignIfExists(string $table, string $column): void
    {
        $constraintNames = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->select('CONSTRAINT_NAME')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->pluck('CONSTRAINT_NAME');

        foreach ($constraintNames as $constraintName) {
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$constraintName}");
        }
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        $exists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $indexName)
            ->exists();

        if ($exists) {
            DB::statement("ALTER TABLE {$table} DROP INDEX {$indexName}");
        }
    }
};
