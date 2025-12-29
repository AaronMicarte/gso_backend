<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            UPDATE propertyinventorydetail pid
            INNER JOIN deliveryreceiptdetail drd ON pid.SourceDRDetailID = drd.DRDetailID
            SET pid.ItemDescription = drd.ItemDescription
            WHERE pid.ItemDescription IS NULL OR pid.ItemDescription = ''
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed, as this is data population
    }
};
