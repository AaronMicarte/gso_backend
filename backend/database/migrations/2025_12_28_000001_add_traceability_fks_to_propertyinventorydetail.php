<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('propertyinventorydetail', function (Blueprint $table) {
            // Add FKs for traceability if not already present
            if (!Schema::hasColumn('propertyinventorydetail', 'SourceIARDetailID')) {
                $table->unsignedInteger('SourceIARDetailID')->nullable();
            }
            if (!Schema::hasColumn('propertyinventorydetail', 'SourceDRDetailID')) {
                $table->unsignedInteger('SourceDRDetailID')->nullable();
            }
            // Add FKs (optional, for strict mode)
            // $table->foreign('SourceIARDetailID')->references('IARDetailID')->on('iardetail')->nullOnDelete();
            // $table->foreign('SourceDRDetailID')->references('DRDetailID')->on('deliveryreceiptdetail')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('propertyinventorydetail', function (Blueprint $table) {
            if (Schema::hasColumn('propertyinventorydetail', 'SourceIARDetailID')) {
                $table->dropColumn('SourceIARDetailID');
            }
            if (Schema::hasColumn('propertyinventorydetail', 'SourceDRDetailID')) {
                $table->dropColumn('SourceDRDetailID');
            }
        });
    }
};
