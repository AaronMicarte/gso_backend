<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE propertyborrow MODIFY COLUMN Status ENUM('pending','approved','rejected','borrowed','partially_returned','returned','cancelled') NOT NULL DEFAULT 'pending'"
        );
    }

    public function down(): void
    {
        DB::statement("UPDATE propertyborrow SET Status='borrowed' WHERE Status='partially_returned'");
        DB::statement(
            "ALTER TABLE propertyborrow MODIFY COLUMN Status ENUM('pending','approved','rejected','borrowed','returned','cancelled') NOT NULL DEFAULT 'pending'"
        );
    }
};
