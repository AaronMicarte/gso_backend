<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add column only if it doesn't already exist (idempotent)
        if (!Schema::hasColumn('deliveryreceiptdetail', 'UnitOfMeasureID')) {
            Schema::table('deliveryreceiptdetail', function (Blueprint $table) {
                // default to 'pc' (assumes seeded UnitOfMeasureID 1 = 'pc')
                // Match existing signed int PK on `unitofmeasure` (avoid unsigned/signed mismatch)
                $table->integer('UnitOfMeasureID')->default(1)->after('QuantityDelivered');
            });
        }

        // Try to add foreign key constraint only if it's not already present
        try {
            DB::statement("ALTER TABLE `deliveryreceiptdetail` ADD CONSTRAINT `deliveryreceiptdetail_unitofmeasureid_foreign` FOREIGN KEY (`UnitOfMeasureID`) REFERENCES `unitofmeasure` (`UnitOfMeasureID`)");
        } catch (\Exception $e) {
            // Ignore; constraint may already exist or types may be incompatible
        }
    }

    public function down(): void
    {
        Schema::table('deliveryreceiptdetail', function (Blueprint $table) {
            $table->dropForeign('UnitOfMeasureID');
            $table->dropColumn('UnitOfMeasureID');
        });
    }
};
