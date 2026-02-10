<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('propertytype', 'RequiresVerification')) {
            Schema::table('propertytype', function (Blueprint $table) {
                $table->boolean('RequiresVerification')->default(false);
            });
        }

        DB::table('propertytype')->update(['RequiresVerification' => 0]);

        DB::table('propertytype')
            ->whereIn('PropertyName', [
                'Agricultural and Forestry Equipment',
                'Communication Equipment',
                'Construction and Heavy Equipment',
                'Disaster Response and Rescue Equipment',
                'Information and Communication Technology Equipment',
                'Machinery',
                'Marine and Fishery Equipment',
                'Medical Equipment',
                'Office Equipment',
                'Other Machinery and Equipment',
                'Other Property, Plant and Equipment',
                'Technical and Scientific Equipment',
            ])
            ->update(['RequiresVerification' => 1]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('propertytype', 'RequiresVerification')) {
            Schema::table('propertytype', function (Blueprint $table) {
                $table->dropColumn('RequiresVerification');
            });
        }
    }
};
