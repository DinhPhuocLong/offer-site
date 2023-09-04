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
        Schema::create('offers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('offer_name')->unique();
            $table->integer('network_id')->unsigned();
            $table->foreign('network_id')->references('id')->on('networks')->onDelete('cascade');
            $table->integer('offer_domain')->unsigned();
            $table->foreign('offer_domain')->references('id')->on('domains')->onDelete('cascade');
            $table->tinyInteger('offer_type')->default(0); // description in config/constants.php
            $table->string('offer_link');
            $table->string('country_allowed')->nullable();
            $table->decimal('offer_payout', 7, 2)->nullable();
            $table->tinyInteger('is_hidden')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offers');
    }
};
