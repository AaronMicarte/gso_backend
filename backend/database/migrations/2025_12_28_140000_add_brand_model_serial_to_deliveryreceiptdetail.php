<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliveryreceiptdetail', function (Blueprint $table) {
            if (!Schema::hasColumn('deliveryreceiptdetail', 'Brand')) {
                $table->string('Brand', 100)->nullable()->after('ItemDescription');
            }
            if (!Schema::hasColumn('deliveryreceiptdetail', 'ModelNumber')) {
                $table->string('ModelNumber', 100)->nullable()->after('Brand');
            }
            if (!Schema::hasColumn('deliveryreceiptdetail', 'SerialNumber')) {
                $table->string('SerialNumber', 100)->nullable()->after('ModelNumber');
            }
        });
    }

    public function down(): void
    {
        Schema::table('deliveryreceiptdetail', function (Blueprint $table) {
            if (Schema::hasColumn('deliveryreceiptdetail', 'Brand')) {
                $table->dropColumn('Brand');
            }
            if (Schema::hasColumn('deliveryreceiptdetail', 'ModelNumber')) {
                $table->dropColumn('ModelNumber');
            }
            if (Schema::hasColumn('deliveryreceiptdetail', 'SerialNumber')) {
                $table->dropColumn('SerialNumber');
            }
        });
    }
};
