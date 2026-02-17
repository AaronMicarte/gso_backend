<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('propertytransfer', function (Blueprint $table) {
            if (!Schema::hasColumn('propertytransfer', 'RequestedItemsCount')) {
                $table->unsignedInteger('RequestedItemsCount')->nullable()->after('Reason');
            }

            if (!Schema::hasColumn('propertytransfer', 'ReleasingDecision')) {
                $table->enum('ReleasingDecision', ['pending', 'approved', 'rejected'])
                    ->default('pending')
                    ->after('RequestedItemsCount');
            }

            if (!Schema::hasColumn('propertytransfer', 'ReleasingApprovedCount')) {
                $table->unsignedInteger('ReleasingApprovedCount')->nullable()->after('ReleasingDecision');
            }

            if (!Schema::hasColumn('propertytransfer', 'ReleasingDecisionReason')) {
                $table->text('ReleasingDecisionReason')->nullable()->after('ReleasingApprovedCount');
            }

            if (!Schema::hasColumn('propertytransfer', 'ReleasingReviewedByUserID')) {
                $table->integer('ReleasingReviewedByUserID')->nullable()->after('ReleasingDecisionReason');
                $table->foreign('ReleasingReviewedByUserID')
                    ->references('UserID')
                    ->on('user')
                    ->onDelete('restrict');
            }

            if (!Schema::hasColumn('propertytransfer', 'ReleasingReviewedAt')) {
                $table->timestamp('ReleasingReviewedAt')->nullable()->after('ReleasingReviewedByUserID');
            }
        });
    }

    public function down(): void
    {
        Schema::table('propertytransfer', function (Blueprint $table) {
            if (Schema::hasColumn('propertytransfer', 'ReleasingReviewedByUserID')) {
                $table->dropForeign(['ReleasingReviewedByUserID']);
            }

            $columns = [
                'RequestedItemsCount',
                'ReleasingDecision',
                'ReleasingApprovedCount',
                'ReleasingDecisionReason',
                'ReleasingReviewedByUserID',
                'ReleasingReviewedAt',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('propertytransfer', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
