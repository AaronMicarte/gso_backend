<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('deliveryreceipt', function (Blueprint $table) {
            if (!Schema::hasColumn('deliveryreceipt', 'InspectedBy')) {
                $table->unsignedInteger('InspectedBy')->nullable()->after('ReceivedBy');
            }
            if (!Schema::hasColumn('deliveryreceipt', 'DateInspected')) {
                $table->date('DateInspected')->nullable()->after('DateReceived');
            }
        });

        Schema::table('deliveryreceiptdetail', function (Blueprint $table) {
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
            if (!Schema::hasColumn('deliveryreceiptdetail', 'ChangeReason')) {
                $table->string('ChangeReason', 255)->nullable()->after('InspectionRemarks');
            }
        });
    }

    public function down(): void
    {
        Schema::table('deliveryreceipt', function (Blueprint $table) {
            if (Schema::hasColumn('deliveryreceipt', 'InspectedBy')) {
                $table->dropColumn('InspectedBy');
            }
            if (Schema::hasColumn('deliveryreceipt', 'DateInspected')) {
                $table->dropColumn('DateInspected');
            }
        });

        Schema::table('deliveryreceiptdetail', function (Blueprint $table) {
            $columns = [
                'QuantityAccepted',
                'QuantityRejected',
                'ConditionID',
                'VerifiedBrand',
                'VerifiedModel',
                'VerifiedSerial',
                'NoSerial',
                'InspectionRemarks',
                'ChangeReason',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('deliveryreceiptdetail', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
