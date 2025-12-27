<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Ensure partially-created table is removed (safe for idempotent migration retries)
        Schema::dropIfExists('iar_signatures');

        Schema::create('iar_signatures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('iar_id');
            $table->string('role', 20); // 'inspected' | 'accepted'
            $table->integer('signed_by_user_id');
            $table->string('source', 20); // 'canvas'|'upload'|'typed'
            $table->string('storage_disk', 32)->default('public');
            $table->string('storage_path', 512);
            $table->string('url', 512)->nullable();
            $table->string('mime', 50);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('size_bytes')->nullable();
            $table->char('sha256_hash', 64);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->unique(['iar_id', 'role']);

            $table->index('signed_by_user_id');
            $table->index('sha256_hash');

            $table->foreign('iar_id')->references('IARID')->on('inspectionacceptancereport')->onDelete('cascade');
            $table->foreign('signed_by_user_id')->references('UserID')->on('user')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('iar_signatures');
    }
};
