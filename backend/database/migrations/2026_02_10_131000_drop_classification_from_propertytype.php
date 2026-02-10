<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('propertytype') && Schema::hasColumn('propertytype', 'ClassificationID')) {
            $constraint = DB::selectOne("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'propertytype' AND COLUMN_NAME = 'ClassificationID' AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1");
            if ($constraint && isset($constraint->CONSTRAINT_NAME)) {
                DB::statement("ALTER TABLE `propertytype` DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
            }

            Schema::table('propertytype', function (Blueprint $table) {
                $table->dropColumn('ClassificationID');
            });
        }

        if (Schema::hasTable('classification')) {
            Schema::drop('classification');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('classification')) {
            Schema::create('classification', function (Blueprint $table) {
                $table->integer('ClassificationID', true);
                $table->integer('CategoryID');
                $table->string('ClassificationName', 100);
                $table->string('Description', 255)->nullable();
                $table->dateTime('CreatedDate')->useCurrent();
                $table->dateTime('UpdatedDate')->nullable()->useCurrentOnUpdate();
                $table->timestamp('deleted_at')->nullable();
            });
        }

        if (Schema::hasTable('propertytype') && !Schema::hasColumn('propertytype', 'ClassificationID')) {
            Schema::table('propertytype', function (Blueprint $table) {
                $table->integer('ClassificationID')->nullable();
            });
        }
    }
};
