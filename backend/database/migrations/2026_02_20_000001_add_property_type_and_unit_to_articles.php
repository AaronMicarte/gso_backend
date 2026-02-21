<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $hasPropertyType = Schema::hasColumn('articles', 'PropertyTypeID');
        $hasDefaultUom = Schema::hasColumn('articles', 'DefaultUnitOfMeasureID');

        if (!$hasPropertyType || !$hasDefaultUom) {
            Schema::table('articles', function (Blueprint $table) use ($hasPropertyType, $hasDefaultUom) {
                if (!$hasPropertyType) {
                    $table->integer('PropertyTypeID')->default(13)->after('Description');
                }

                if (!$hasDefaultUom) {
                    $table->integer('DefaultUnitOfMeasureID')->default(2)->after('PropertyTypeID');
                }
            });
        }

        $this->backfillDefaults();

        $this->ensureIndex('articles', 'articles_propertytype_index', ['PropertyTypeID']);
        $this->ensureIndex('articles', 'articles_defaultuom_index', ['DefaultUnitOfMeasureID']);

        if (!$this->hasForeignKey('articles', 'articles_propertytype_foreign')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->foreign('PropertyTypeID', 'articles_propertytype_foreign')
                    ->references('PropertyTypeID')
                    ->on('propertytype');
            });
        }

        if (!$this->hasForeignKey('articles', 'articles_defaultuom_foreign')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->foreign('DefaultUnitOfMeasureID', 'articles_defaultuom_foreign')
                    ->references('UnitOfMeasureID')
                    ->on('unitofmeasure')
                    ->onUpdate('cascade');
            });
        }
    }

    private function ensureIndex(string $table, string $indexName, array $columns): void
    {
        $existing = DB::select(
            'SHOW INDEX FROM `' . $table . '` WHERE Key_name = ?',
            [$indexName]
        );

        if (!empty($existing)) {
            return;
        }

        $columnList = implode('`, `', $columns);
        DB::statement('CREATE INDEX `' . $indexName . '` ON `' . $table . '` (`' . $columnList . '`)');
    }

    private function hasForeignKey(string $table, string $keyName): bool
    {
        $rows = DB::select(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            [$table, $keyName]
        );

        return !empty($rows);
    }

    private function backfillDefaults(): void
    {
        $articlePropertyMap = [
            'Desktop Computer' => 7,
            'Laptop Computer' => 7,
            'Printer/Scanner' => 7,
            'Office Desk' => 6,
            'Office Chair' => 6,
            'Filing Cabinet' => 6,
            'Air Conditioner' => 8,
            'Projector' => 7,
            'Telephone Set' => 2,
            'Server Equipment' => 7,
            'Calculator' => 11,
            'Stapler' => 11,
            'Hole Puncher' => 11,
            'Office Supplies Set' => 11,
            'Table Fan' => 12,
            'Extension Cord' => 12,
            'Office Lamp' => 11,
            'Waste Basket' => 6,
            'Clock/Watch' => 11,
            'Hand Tools Set' => 12,
        ];

        foreach ($articlePropertyMap as $name => $propertyTypeId) {
            DB::table('articles')
                ->where('ArticleName', $name)
                ->update([
                    'PropertyTypeID' => $propertyTypeId,
                ]);
        }

        DB::table('articles')
            ->whereIn('ArticleName', ['Office Supplies Set', 'Hand Tools Set'])
            ->update(['DefaultUnitOfMeasureID' => 3]);

        DB::table('articles')
            ->whereNull('PropertyTypeID')
            ->update([
                'PropertyTypeID' => 13,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign('articles_propertytype_foreign');
            $table->dropForeign('articles_defaultuom_foreign');
            $table->dropIndex('articles_propertytype_index');
            $table->dropIndex('articles_defaultuom_index');
            $table->dropColumn(['PropertyTypeID', 'DefaultUnitOfMeasureID']);
        });
    }
};
