<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("UPDATE property_waste_reports SET Status = 'draft' WHERE Status IN ('for_approval', 'approved')");

        DB::statement(<<<'SQL'
            ALTER TABLE property_waste_reports
            MODIFY COLUMN Status ENUM('draft', 'completed', 'cancelled') NOT NULL DEFAULT 'draft'
        SQL);
    }

    public function down(): void
    {
        DB::statement(<<<'SQL'
            ALTER TABLE property_waste_reports
            MODIFY COLUMN Status ENUM('draft', 'for_approval', 'approved', 'completed', 'cancelled') NOT NULL DEFAULT 'draft'
        SQL);
    }
};
