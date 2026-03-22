<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('user_push_tokens')) {
            $this->dropConstraintIfExists('user_push_tokens', 'user_push_tokens_userid_foreign');
            DB::statement('ALTER TABLE user_push_tokens MODIFY UserID INT(11) NOT NULL');
            $this->addForeignKeyIfMissing('user_push_tokens', 'user_push_tokens_userid_foreign', 'UserID');
        }

        if (Schema::hasTable('user_notifications')) {
            $this->dropConstraintIfExists('user_notifications', 'user_notifications_userid_foreign');
            DB::statement('ALTER TABLE user_notifications MODIFY UserID INT(11) NOT NULL');
            $this->addForeignKeyIfMissing('user_notifications', 'user_notifications_userid_foreign', 'UserID');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_push_tokens')) {
            $this->dropConstraintIfExists('user_push_tokens', 'user_push_tokens_userid_foreign');
        }

        if (Schema::hasTable('user_notifications')) {
            $this->dropConstraintIfExists('user_notifications', 'user_notifications_userid_foreign');
        }
    }

    private function addForeignKeyIfMissing(string $table, string $constraintName, string $column): void
    {
        if ($this->constraintExists($table, $constraintName)) {
            return;
        }

        DB::statement(sprintf(
            'ALTER TABLE %s ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES user (UserID) ON DELETE CASCADE',
            $table,
            $constraintName,
            $column
        ));
    }

    private function dropConstraintIfExists(string $table, string $constraintName): void
    {
        if (!$this->constraintExists($table, $constraintName)) {
            return;
        }

        DB::statement(sprintf('ALTER TABLE %s DROP FOREIGN KEY %s', $table, $constraintName));
    }

    private function constraintExists(string $table, string $constraintName): bool
    {
        $schema = DB::getDatabaseName();

        $row = DB::selectOne(
            'SELECT CONSTRAINT_NAME
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = ?
               AND TABLE_NAME = ?
               AND CONSTRAINT_NAME = ?
               AND CONSTRAINT_TYPE = "FOREIGN KEY"
             LIMIT 1',
            [$schema, $table, $constraintName]
        );

        return $row !== null;
    }
};
