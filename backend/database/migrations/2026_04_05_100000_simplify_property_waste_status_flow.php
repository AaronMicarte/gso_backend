<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement(<<<'SQL'
            UPDATE property_waste_reports r
            LEFT JOIN (
                SELECT
                    PropertyWasteReportID,
                    SUM(CASE WHEN Status = 'disposed' THEN 1 ELSE 0 END) AS disposed_items,
                    COUNT(*) AS total_items
                FROM property_waste_items
                GROUP BY PropertyWasteReportID
            ) x ON x.PropertyWasteReportID = r.PropertyWasteReportID
            SET r.Status = CASE
                WHEN COALESCE(x.total_items, 0) > 0 AND COALESCE(x.disposed_items, 0) = x.total_items THEN 'completed'
                WHEN r.Status = 'cancelled' THEN 'cancelled'
                ELSE 'draft'
            END
            WHERE r.Status IN ('draft', 'for_approval', 'approved', 'completed')
        SQL);
    }

    public function down(): void
    {
        // Intentionally not reverting status normalization.
    }
};
