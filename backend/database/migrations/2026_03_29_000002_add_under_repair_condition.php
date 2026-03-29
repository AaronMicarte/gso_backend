<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('condition')->updateOrInsert(
            ['ConditionName' => 'Under Repair'],
            ['Description' => 'Under Repair Assets']
        );

        DB::table('condition')->updateOrInsert(
            ['ConditionName' => 'Serviceable'],
            ['Description' => 'Serviceable Assets']
        );

        DB::table('condition')->updateOrInsert(
            ['ConditionName' => 'Unserviceable'],
            ['Description' => 'Unserviceable Assets']
        );
    }

    public function down(): void
    {
        DB::table('condition')->where('ConditionName', 'Under Repair')->delete();
    }
};
