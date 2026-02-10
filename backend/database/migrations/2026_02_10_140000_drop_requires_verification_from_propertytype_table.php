<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('propertytype', 'RequiresVerification')) {
            Schema::table('propertytype', function (Blueprint $table) {
                $table->dropColumn('RequiresVerification');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('propertytype', 'RequiresVerification')) {
            Schema::table('propertytype', function (Blueprint $table) {
                $table->boolean('RequiresVerification')->default(false);
            });
        }
    }
};
