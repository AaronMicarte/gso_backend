<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('userrole')->where('UserRoleID', 6)->orWhere('RoleName', 'Inspection Officer')->exists();
        if (! $exists) {
            DB::table('userrole')->insert([
                'UserRoleID' => 6,
                'RoleName' => 'Inspection Officer',
                'Description' => 'Performs inspection and verification of delivered items',
                'CreatedDate' => now(),
                'UpdatedDate' => null,
                'deleted_at' => null,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('userrole')->where('RoleName', 'Inspection Officer')->delete();
    }
};
