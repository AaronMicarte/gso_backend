<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('propertyborrow', function (Blueprint $table) {
            $table->dropForeign(['ReturnedByUserID']);
            $table->dropColumn([
                'ReleasedSignedAt',
                'ReceivedSignedAt',
                'ReturnedByUserID',
                'ReturnedAt',
                'ReturnNotes',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('propertyborrow', function (Blueprint $table) {
            $table->timestamp('ReleasedSignedAt')->nullable();
            $table->timestamp('ReceivedSignedAt')->nullable();
            $table->integer('ReturnedByUserID')->nullable();
            $table->timestamp('ReturnedAt')->nullable();
            $table->text('ReturnNotes')->nullable();

            $table->foreign('ReturnedByUserID')->references('UserID')->on('user')->onDelete('restrict');
        });
    }
};
