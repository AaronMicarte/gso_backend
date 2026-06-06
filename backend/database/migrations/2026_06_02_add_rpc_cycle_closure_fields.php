<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rpc_inventory_cycles', function (Blueprint $table) {
            // Add columns if they don't exist (match user.UserID type: int(11))
            if (!Schema::hasColumn('rpc_inventory_cycles', 'closed_by_user_id')) {
                $table->integer('closed_by_user_id')->nullable()->after('closed_at');
            }
            if (!Schema::hasColumn('rpc_inventory_cycles', 'close_reason')) {
                $table->text('close_reason')->nullable();
            }
            if (!Schema::hasColumn('rpc_inventory_cycles', 'reopened_by_user_id')) {
                $table->integer('reopened_by_user_id')->nullable();
            }
            if (!Schema::hasColumn('rpc_inventory_cycles', 'reopened_at')) {
                $table->dateTime('reopened_at')->nullable();
            }
            if (!Schema::hasColumn('rpc_inventory_cycles', 'reopen_reason')) {
                $table->text('reopen_reason')->nullable();
            }
            if (!Schema::hasColumn('rpc_inventory_cycles', 'reopen_count')) {
                $table->unsignedInteger('reopen_count')->default(0);
            }
        });

        // Add foreign keys - match user.UserID type (int(11))
        try {
            DB::statement('ALTER TABLE `rpc_inventory_cycles` ADD CONSTRAINT `rpc_inventory_cycles_closed_by_user_id_foreign` FOREIGN KEY (`closed_by_user_id`) REFERENCES `user` (`UserID`) ON DELETE SET NULL');
        } catch (\Exception $e) {
            // Foreign key may already exist
        }

        try {
            DB::statement('ALTER TABLE `rpc_inventory_cycles` ADD CONSTRAINT `rpc_inventory_cycles_reopened_by_user_id_foreign` FOREIGN KEY (`reopened_by_user_id`) REFERENCES `user` (`UserID`) ON DELETE SET NULL');
        } catch (\Exception $e) {
            // Foreign key may already exist
        }

        // Add indexes with error handling
        DB::statement('ALTER TABLE `rpc_inventory_cycles` ADD INDEX IF NOT EXISTS `rpc_inventory_cycles_status_index` (`status`)');
        DB::statement('ALTER TABLE `rpc_inventory_cycles` ADD INDEX IF NOT EXISTS `rpc_inventory_cycles_closed_by_at_index` (`closed_by_user_id`, `closed_at`)');
        DB::statement('ALTER TABLE `rpc_inventory_cycles` ADD INDEX IF NOT EXISTS `rpc_inventory_cycles_reopened_by_at_index` (`reopened_by_user_id`, `reopened_at`)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rpc_inventory_cycles', function (Blueprint $table) {
            $table->dropForeign(['closed_by_user_id']);
            $table->dropForeign(['reopened_by_user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['closed_by_user_id', 'closed_at']);
            $table->dropIndex(['reopened_by_user_id', 'reopened_at']);
            $table->dropColumn([
                'closed_by_user_id',
                'close_reason',
                'reopened_by_user_id',
                'reopened_at',
                'reopen_reason',
                'reopen_count',
            ]);
        });
    }
};
