<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('propertyreturnitems', function (Blueprint $table) {
            $table->integer('ConditionID')->nullable()->after('QuantityReturned');
            $table->index('ConditionID');
            $table->foreign('ConditionID')->references('ConditionID')->on('condition')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('propertyreturnitems', function (Blueprint $table) {
            $table->dropForeign(['ConditionID']);
            $table->dropIndex(['ConditionID']);
            $table->dropColumn('ConditionID');
        });
    }
};
