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
        Schema::create('property_issuance_assignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('issuance_id');
            $table->unsignedBigInteger('issuance_detail_id');
            $table->string('receiver_name');
            $table->integer('quantity')->unsigned();
            $table->timestamps();

            $table->index('issuance_id');
            $table->index('issuance_detail_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_issuance_assignments');
    }
};
