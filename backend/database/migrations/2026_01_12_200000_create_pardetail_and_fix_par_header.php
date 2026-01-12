<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // `par` is logically a header table keyed by PARNumber, but the legacy schema
        // requires `PropertyID` NOT NULL. Make it nullable so a single PAR header can
        // represent multiple properties via `pardetail`.
        DB::statement('ALTER TABLE `par` MODIFY `PropertyID` int(11) NULL');

        if (!Schema::hasTable('pardetail')) {
            Schema::create('pardetail', function (Blueprint $table) {
                $table->increments('PARDetailID');
                $table->integer('PARID');
                $table->integer('PropertyID');
                $table->integer('QuantityIssued');
                $table->decimal('UnitCost', 18, 2)->nullable();
                $table->decimal('TotalCost', 18, 2)->nullable();
                $table->text('Remarks')->nullable();

                $table->unique(['PARID', 'PropertyID'], 'pardetail_parid_propertyid_unique');
                $table->index('PARID');
                $table->index('PropertyID');

                $table->foreign('PARID', 'pardetail_ibfk_1')
                    ->references('PARID')
                    ->on('par')
                    ->onDelete('cascade');

                $table->foreign('PropertyID', 'pardetail_ibfk_2')
                    ->references('PropertyID')
                    ->on('assignedproperty');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pardetail')) {
            Schema::drop('pardetail');
        }

        // Revert to legacy constraint.
        DB::statement('ALTER TABLE `par` MODIFY `PropertyID` int(11) NOT NULL');
    }
};
