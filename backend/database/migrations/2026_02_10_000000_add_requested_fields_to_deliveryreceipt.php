<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('deliveryreceipt', function (Blueprint $table) {
            if (!Schema::hasColumn('deliveryreceipt', 'RequestedBy')) {
                $table->string('RequestedBy', 255)->nullable()->after('SupplierName');
            }
            if (!Schema::hasColumn('deliveryreceipt', 'DepartmentID')) {
                $table->unsignedInteger('DepartmentID')->nullable()->after('RequestedBy');
            }
            if (!Schema::hasColumn('deliveryreceipt', 'PRNumber')) {
                $table->string('PRNumber', 50)->nullable()->after('DepartmentID');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveryreceipt', function (Blueprint $table) {
            if (Schema::hasColumn('deliveryreceipt', 'PRNumber')) {
                $table->dropColumn('PRNumber');
            }
            if (Schema::hasColumn('deliveryreceipt', 'DepartmentID')) {
                $table->dropColumn('DepartmentID');
            }
            if (Schema::hasColumn('deliveryreceipt', 'RequestedBy')) {
                $table->dropColumn('RequestedBy');
            }
        });
    }
};
