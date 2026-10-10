<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('property_issuance_assignments')) {
            return;
        }

        Schema::table('property_issuance_assignments', function (Blueprint $table) {
            if (! Schema::hasColumn('property_issuance_assignments', 'office_name')) {
                $table->string('office_name', 255)->nullable()->after('position_office');
            }
            if (! Schema::hasColumn('property_issuance_assignments', 'is_department_head')) {
                $table->boolean('is_department_head')->default(false)->after('office_name');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('property_issuance_assignments')) {
            return;
        }

        Schema::table('property_issuance_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('property_issuance_assignments', 'is_department_head')) {
                $table->dropColumn('is_department_head');
            }
            if (Schema::hasColumn('property_issuance_assignments', 'office_name')) {
                $table->dropColumn('office_name');
            }
        });
    }
};
