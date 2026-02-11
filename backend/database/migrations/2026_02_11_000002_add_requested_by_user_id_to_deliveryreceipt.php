<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('deliveryreceipt', function (Blueprint $table) {
            if (!Schema::hasColumn('deliveryreceipt', 'RequestedByUserID')) {
                $table->unsignedInteger('RequestedByUserID')->nullable()->after('RequestedBy');
                $table->index('RequestedByUserID');
                $table->foreign('RequestedByUserID')
                    ->references('UserID')
                    ->on('user')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveryreceipt', function (Blueprint $table) {
            if (Schema::hasColumn('deliveryreceipt', 'RequestedByUserID')) {
                $table->dropForeign(['RequestedByUserID']);
                $table->dropIndex(['RequestedByUserID']);
                $table->dropColumn('RequestedByUserID');
            }
        });
    }
};
