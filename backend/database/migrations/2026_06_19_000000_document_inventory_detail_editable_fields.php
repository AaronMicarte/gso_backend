<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('propertyinventorydetail')) {
            return;
        }

        DB::statement("ALTER TABLE `propertyinventorydetail`
            MODIFY COLUMN `Description` varchar(255) DEFAULT NULL COMMENT 'Item description (editable)',
            MODIFY COLUMN `Brand` varchar(100) DEFAULT NULL COMMENT 'Brand (editable)',
            MODIFY COLUMN `ModelNumber` varchar(100) DEFAULT NULL COMMENT 'Model number (editable)',
            MODIFY COLUMN `SerialNumber` varchar(100) DEFAULT NULL COMMENT 'Serial number (editable)'");
    }

    public function down(): void
    {
        if (!Schema::hasTable('propertyinventorydetail')) {
            return;
        }

        DB::statement("ALTER TABLE `propertyinventorydetail`
            MODIFY COLUMN `Description` varchar(255) DEFAULT NULL COMMENT 'Item Description',
            MODIFY COLUMN `Brand` varchar(100) DEFAULT NULL,
            MODIFY COLUMN `ModelNumber` varchar(100) DEFAULT NULL,
            MODIFY COLUMN `SerialNumber` varchar(100) DEFAULT NULL");
    }
};
