<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('propertystatus')) {
            return;
        }

        DB::table('propertystatus')
            ->whereIn('StatusName', ['Available', 'Unassigned'])
            ->update(['StatusName' => 'For issuance']);

        DB::table('propertystatus')
            ->whereIn('StatusName', ['Assigned', 'Issued'])
            ->update(['StatusName' => 'Issued']);

        $existsForIssuance = DB::table('propertystatus')->where('StatusName', 'For issuance')->exists();
        if (!$existsForIssuance) {
            DB::table('propertystatus')->insert([
                'StatusName' => 'For issuance',
                'Description' => 'Ready for issuance to a custodian',
            ]);
        }

        $existsIssued = DB::table('propertystatus')->where('StatusName', 'Issued')->exists();
        if (!$existsIssued) {
            DB::table('propertystatus')->insert([
                'StatusName' => 'Issued',
                'Description' => 'Issued to a custodian',
            ]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('propertystatus')) {
            return;
        }

        DB::table('propertystatus')
            ->where('StatusName', 'For issuance')
            ->update(['StatusName' => 'Available']);

        DB::table('propertystatus')
            ->where('StatusName', 'Issued')
            ->update(['StatusName' => 'Assigned']);
    }
};
