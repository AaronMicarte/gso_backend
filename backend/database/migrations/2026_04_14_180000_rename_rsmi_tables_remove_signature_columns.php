<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rsmi_reports') && !Schema::hasTable('rsmi_header')) {
            Schema::rename('rsmi_reports', 'rsmi_header');
        }

        if (Schema::hasTable('rsmi_report_items') && !Schema::hasTable('rsmi_items')) {
            Schema::rename('rsmi_report_items', 'rsmi_items');
        }

        if (Schema::hasTable('rsmi_items') && Schema::hasColumn('rsmi_items', 'rsmi_report_id')) {
            $this->dropForeignKeyByColumn('rsmi_items', 'rsmi_report_id');
            $this->dropIndexByColumn('rsmi_items', 'rsmi_report_id');

            Schema::table('rsmi_items', function (Blueprint $table) {
                $table->renameColumn('rsmi_report_id', 'rsmi_header_id');
            });

            Schema::table('rsmi_items', function (Blueprint $table) {
                $table->index('rsmi_header_id');
                $table->foreign('rsmi_header_id')->references('id')->on('rsmi_header')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('rsmi_header')) {
            $this->dropForeignKeyByColumn('rsmi_header', 'certified_by_user_id');

            $columnsToDrop = [
                'certification_name',
                'certified_by_user_id',
                'certified_at',
                'certification_signature_source',
                'certification_signature_disk',
                'certification_signature_path',
                'certification_signature_url',
            ];

            $existingColumns = array_values(array_filter($columnsToDrop, static fn (string $column): bool => Schema::hasColumn('rsmi_header', $column)));

            if (count($existingColumns) > 0) {
                Schema::table('rsmi_header', function (Blueprint $table) use ($existingColumns) {
                    $table->dropColumn($existingColumns);
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('rsmi_items') && Schema::hasColumn('rsmi_items', 'rsmi_header_id')) {
            $this->dropForeignKeyByColumn('rsmi_items', 'rsmi_header_id');
            $this->dropIndexByColumn('rsmi_items', 'rsmi_header_id');

            Schema::table('rsmi_items', function (Blueprint $table) {
                $table->renameColumn('rsmi_header_id', 'rsmi_report_id');
            });

            Schema::table('rsmi_items', function (Blueprint $table) {
                $table->index('rsmi_report_id');
                $table->foreign('rsmi_report_id')->references('id')->on('rsmi_header')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('rsmi_header')) {
            Schema::table('rsmi_header', function (Blueprint $table) {
                if (!Schema::hasColumn('rsmi_header', 'certification_name')) {
                    $table->string('certification_name', 255)->nullable();
                }
                if (!Schema::hasColumn('rsmi_header', 'certified_by_user_id')) {
                    $table->integer('certified_by_user_id')->nullable();
                }
                if (!Schema::hasColumn('rsmi_header', 'certified_at')) {
                    $table->dateTime('certified_at')->nullable();
                }
                if (!Schema::hasColumn('rsmi_header', 'certification_signature_source')) {
                    $table->string('certification_signature_source', 20)->nullable();
                }
                if (!Schema::hasColumn('rsmi_header', 'certification_signature_disk')) {
                    $table->string('certification_signature_disk', 32)->nullable();
                }
                if (!Schema::hasColumn('rsmi_header', 'certification_signature_path')) {
                    $table->string('certification_signature_path', 512)->nullable();
                }
                if (!Schema::hasColumn('rsmi_header', 'certification_signature_url')) {
                    $table->string('certification_signature_url', 512)->nullable();
                }
            });

            if (Schema::hasColumn('rsmi_header', 'certified_by_user_id')) {
                Schema::table('rsmi_header', function (Blueprint $table) {
                    $table->foreign('certified_by_user_id')->references('UserID')->on('user')->onDelete('restrict');
                });
            }
        }

        if (Schema::hasTable('rsmi_items') && !Schema::hasTable('rsmi_report_items')) {
            Schema::rename('rsmi_items', 'rsmi_report_items');
        }

        if (Schema::hasTable('rsmi_header') && !Schema::hasTable('rsmi_reports')) {
            Schema::rename('rsmi_header', 'rsmi_reports');
        }
    }

    private function dropForeignKeyByColumn(string $tableName, string $columnName): void
    {
        $databaseName = DB::getDatabaseName();
        $constraints = DB::select(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$databaseName, $tableName, $columnName]
        );

        foreach ($constraints as $constraint) {
            $constraintName = $constraint->CONSTRAINT_NAME ?? null;
            if (!is_string($constraintName) || $constraintName === '') {
                continue;
            }
            DB::statement(sprintf('ALTER TABLE `%s` DROP FOREIGN KEY `%s`', $tableName, $constraintName));
        }
    }

    private function dropIndexByColumn(string $tableName, string $columnName): void
    {
        $indexes = DB::select(
            'SHOW INDEX FROM `' . $tableName . '` WHERE Column_name = ? AND Key_name <> "PRIMARY"',
            [$columnName]
        );

        foreach ($indexes as $index) {
            $indexName = $index->Key_name ?? null;
            if (!is_string($indexName) || $indexName === '') {
                continue;
            }
            DB::statement(sprintf('ALTER TABLE `%s` DROP INDEX `%s`', $tableName, $indexName));
        }
    }
};
