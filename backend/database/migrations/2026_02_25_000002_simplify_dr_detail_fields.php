<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('deliveryreceiptdetail', function (Blueprint $table) {
            if (!Schema::hasColumn('deliveryreceiptdetail', 'QuantityRequested')) {
                $table->integer('QuantityRequested')->nullable()->after('QuantityDelivered');
            }

            $columns = [
                'QuantityAccepted',
                'QuantityRejected',
                'ConditionID',
                'VerifiedBrand',
                'VerifiedModel',
                'VerifiedSerial',
                'NoSerial',
                'InspectionRemarks',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('deliveryreceiptdetail', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        if (Schema::hasColumn('deliveryreceiptdetail', 'QuantityRequested')) {
            DB::table('deliveryreceiptdetail')
                ->whereNull('QuantityRequested')
                ->update(['QuantityRequested' => DB::raw('QuantityDelivered')]);
        }
    }

    public function down(): void
    {
        Schema::table('deliveryreceiptdetail', function (Blueprint $table) {
            if (Schema::hasColumn('deliveryreceiptdetail', 'QuantityRequested')) {
                $table->dropColumn('QuantityRequested');
            }

            if (!Schema::hasColumn('deliveryreceiptdetail', 'QuantityAccepted')) {
                $table->integer('QuantityAccepted')->nullable()->after('QuantityDelivered');
            }
            if (!Schema::hasColumn('deliveryreceiptdetail', 'QuantityRejected')) {
                $table->integer('QuantityRejected')->nullable()->after('QuantityAccepted');
            }
            if (!Schema::hasColumn('deliveryreceiptdetail', 'ConditionID')) {
                $table->integer('ConditionID')->nullable()->after('QuantityRejected');
            }
            if (!Schema::hasColumn('deliveryreceiptdetail', 'VerifiedBrand')) {
                $table->string('VerifiedBrand', 100)->nullable()->after('ConditionID');
            }
            if (!Schema::hasColumn('deliveryreceiptdetail', 'VerifiedModel')) {
                $table->string('VerifiedModel', 100)->nullable()->after('VerifiedBrand');
            }
            if (!Schema::hasColumn('deliveryreceiptdetail', 'VerifiedSerial')) {
                $table->string('VerifiedSerial', 100)->nullable()->after('VerifiedModel');
            }
            if (!Schema::hasColumn('deliveryreceiptdetail', 'NoSerial')) {
                $table->boolean('NoSerial')->default(false)->after('VerifiedSerial');
            }
            if (!Schema::hasColumn('deliveryreceiptdetail', 'InspectionRemarks')) {
                $table->text('InspectionRemarks')->nullable()->after('NoSerial');
            }
        });
    }
};
