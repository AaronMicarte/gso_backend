<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Temporarily allow new values while converting existing legacy values.
        DB::statement("ALTER TABLE asset_service_records_details MODIFY HandlingDecision ENUM('warranty_claim','vendor_repair','replacement_evaluation','warranty','no_warranty') NULL");

        DB::statement("UPDATE asset_service_records_details SET HandlingDecision='warranty' WHERE HandlingDecision='warranty_claim'");
        DB::statement("UPDATE asset_service_records_details SET HandlingDecision='no_warranty' WHERE HandlingDecision='vendor_repair'");

        // Finalize enum definition to new values.
        DB::statement("ALTER TABLE asset_service_records_details MODIFY HandlingDecision ENUM('warranty','no_warranty') NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to previous enum values for rollback. Existing data may not map exactly.
        DB::statement("ALTER TABLE asset_service_records_details MODIFY HandlingDecision ENUM('warranty_claim','vendor_repair','replacement_evaluation') NULL");
        DB::statement("UPDATE asset_service_records_details SET HandlingDecision='warranty_claim' WHERE HandlingDecision='warranty'");
        DB::statement("UPDATE asset_service_records_details SET HandlingDecision='vendor_repair' WHERE HandlingDecision='no_warranty'");
    }
};