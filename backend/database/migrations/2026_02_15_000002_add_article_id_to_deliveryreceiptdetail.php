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
        Schema::table('deliveryreceiptdetail', function (Blueprint $table) {
            $table->unsignedBigInteger('ArticleID')->nullable()->after('DRID');
            
            // Add foreign key constraint
            $table->foreign('ArticleID')->references('ArticleID')->on('articles')->onDelete('set null');
            
            // Add index for performance
            $table->index('ArticleID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveryreceiptdetail', function (Blueprint $table) {
            $table->dropForeign(['ArticleID']);
            $table->dropIndex(['ArticleID']);
            $table->dropColumn('ArticleID');
        });
    }
};