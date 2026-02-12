<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function dropForeignKeysForColumn(string $table, string $column): void
    {
        $databaseName = DB::getDatabaseName();

        $constraints = DB::select(
            "SELECT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL",
            [$databaseName, $table, $column]
        );

        foreach ($constraints as $row) {
            $name = $row->CONSTRAINT_NAME ?? null;
            if (!$name) {
                continue;
            }

            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$name}`");
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $databaseName = DB::getDatabaseName();
        $rows = DB::select(
            "SELECT 1
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = ?
               AND INDEX_NAME = ?
             LIMIT 1",
            [$databaseName, $table, $indexName]
        );

        return !empty($rows);
    }

    private function foreignKeyExists(string $table, string $column): bool
    {
        $databaseName = DB::getDatabaseName();
        $rows = DB::select(
            "SELECT 1
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL
             LIMIT 1",
            [$databaseName, $table, $column]
        );

        return !empty($rows);
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1) Drop legacy foreign keys first (names may not follow Laravel conventions)
        if (Schema::hasColumn('deliveryreceipt', 'DepartmentID')) {
            $this->dropForeignKeysForColumn('deliveryreceipt', 'DepartmentID');
        }
        if (Schema::hasColumn('deliveryreceipt', 'RequestedByUserID')) {
            $this->dropForeignKeysForColumn('deliveryreceipt', 'RequestedByUserID');
        }

        // 2) If RequestedByUserID exists, copy it into RequestedBy before converting the type.
        //    (RequestedBy used to be a varchar; we are normalizing it to an INT FK.)
        if (Schema::hasColumn('deliveryreceipt', 'RequestedBy') && Schema::hasColumn('deliveryreceipt', 'RequestedByUserID')) {
            DB::statement(
                "UPDATE `deliveryreceipt`
                 SET `RequestedBy` = CAST(`RequestedByUserID` AS CHAR)
                 WHERE `RequestedByUserID` IS NOT NULL"
            );
        }

        // 3) Ensure RequestedBy exists and is INT UNSIGNED NULL
        if (!Schema::hasColumn('deliveryreceipt', 'RequestedBy')) {
            Schema::table('deliveryreceipt', function (Blueprint $table) {
                // Keep as signed INT to match existing `user.UserID` (int(11)).
                $table->integer('RequestedBy')->nullable()->after('SupplierName');
            });
        } else {

            // Null-out any non-numeric RequestedBy values so ALTER won't fail.
            DB::statement("UPDATE `deliveryreceipt` SET `RequestedBy` = NULL WHERE `RequestedBy` NOT REGEXP '^[0-9]+$'");

            // Now convert the column type safely to BIGINT UNSIGNED.
            DB::statement('ALTER TABLE `deliveryreceipt` MODIFY COLUMN `RequestedBy` INT NULL');
        }

        // 4) Backfill again now that RequestedBy is numeric
        if (Schema::hasColumn('deliveryreceipt', 'RequestedByUserID')) {
            DB::statement(
                "UPDATE `deliveryreceipt`
                 SET `RequestedBy` = `RequestedByUserID`
                 WHERE `RequestedByUserID` IS NOT NULL"
            );
        }

        // 5) Drop redundant/denormalized columns
        Schema::table('deliveryreceipt', function (Blueprint $table) {
            if (Schema::hasColumn('deliveryreceipt', 'DepartmentID')) {
                $table->dropColumn('DepartmentID');
            }
            if (Schema::hasColumn('deliveryreceipt', 'RequestedByUserID')) {
                $table->dropColumn('RequestedByUserID');
            }
        });

        // 6) Add index + FK on RequestedBy (if not yet present)
        Schema::table('deliveryreceipt', function (Blueprint $table) {
            if (!$this->indexExists('deliveryreceipt', 'deliveryreceipt_requestedby_index')) {
                $table->index('RequestedBy');
            }

            if (!$this->foreignKeyExists('deliveryreceipt', 'RequestedBy')) {
                $table->foreign('RequestedBy')
                    ->references('UserID')
                    ->on('user')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Best-effort rollback: restore the old columns (RequestedBy varchar + DepartmentID) and remove FK.
        if ($this->foreignKeyExists('deliveryreceipt', 'RequestedBy')) {
            $this->dropForeignKeysForColumn('deliveryreceipt', 'RequestedBy');
        }

        Schema::table('deliveryreceipt', function (Blueprint $table) {
            if (Schema::hasColumn('deliveryreceipt', 'RequestedBy')) {
                // Convert back to varchar (will stringify user id)
                DB::statement('ALTER TABLE `deliveryreceipt` MODIFY COLUMN `RequestedBy` VARCHAR(255) NULL');
            }

            if (!Schema::hasColumn('deliveryreceipt', 'DepartmentID')) {
                $table->unsignedInteger('DepartmentID')->nullable()->after('RequestedBy');
            }

            if (!Schema::hasColumn('deliveryreceipt', 'RequestedByUserID')) {
                $table->integer('RequestedByUserID')->nullable()->after('RequestedBy');
            }
        });
    }
};
