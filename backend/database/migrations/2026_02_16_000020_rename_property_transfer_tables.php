<?php

use Illuminate\Database\Migrations\Migration;

// Deprecated/NO-OP migration.
// The original create migration `2026_02_16_000010_create_property_transfer_tables.php`
// now creates `propertytransfer` and `propertytransferdetails` directly.
// This migration is retained only to preserve chronological order but intentionally
// performs no actions to avoid accidental renames.

return new class extends Migration
{
    public function up(): void
    {
        // no-op
    }

    public function down(): void
    {
        // no-op
    }
};
