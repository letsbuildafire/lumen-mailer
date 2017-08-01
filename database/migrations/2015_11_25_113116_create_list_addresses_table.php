<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('list_addresses');
        Schema::create('list_addresses', function (Blueprint $table) {
            $table->uuid('list_id');
            $table->uuid('address_id');
            $table->json('custom_data');

            $table->foreign('list_id')->references('id')->on('lists');
            $table->foreign('address_id')->references('id')->on('addresses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('list_addresses');
    }
}
