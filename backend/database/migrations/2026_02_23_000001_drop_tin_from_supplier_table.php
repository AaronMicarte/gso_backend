<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('supplier', 'TIN')) {
            Schema::table('supplier', function (Blueprint $table) {
                $table->dropColumn('TIN');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('supplier', 'TIN')) {
            Schema::table('supplier', function (Blueprint $table) {
                $table->string('TIN', 50)->nullable()->after('Email');
            });
        }
    }
};
