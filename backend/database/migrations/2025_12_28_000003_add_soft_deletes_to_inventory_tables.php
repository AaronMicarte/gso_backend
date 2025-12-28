<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('propertyinventory', function (Blueprint $table) {
            if (!Schema::hasColumn('propertyinventory', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('propertyinventorydetail', function (Blueprint $table) {
            if (!Schema::hasColumn('propertyinventorydetail', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down()
    {
        Schema::table('propertyinventory', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('propertyinventorydetail', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
