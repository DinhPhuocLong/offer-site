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
            $table->integer('network_id')->unsigned();
            $table->foreign('network_id')->references('id')->on('networks')->onDelete('cascade');
            $table->string('name')->unique();
            $table->tinyInteger('offer_type')->default(1); //1 = desktop, 0 = mobile
            $table->string('offer_link');
            $table->string('country_allowed')->default('all');
            $table->decimal('offer_payout', 5, 2)->default(1);
            $table->tinyInteger('is_hidden')->default(1);
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
