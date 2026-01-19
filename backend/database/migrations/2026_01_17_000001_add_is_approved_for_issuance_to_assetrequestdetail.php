<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assetrequestdetail', function (Blueprint $table) {
            if (!Schema::hasColumn('assetrequestdetail', 'IsApprovedForIssuance')) {
                $table->boolean('IsApprovedForIssuance')->default(false)->after('AdminNotes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assetrequestdetail', function (Blueprint $table) {
            if (Schema::hasColumn('assetrequestdetail', 'IsApprovedForIssuance')) {
                $table->dropColumn('IsApprovedForIssuance');
            }
        });
    }
};
