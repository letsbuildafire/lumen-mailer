<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailerListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('emailer_lists');
        Schema::create('emailer_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('emailer_id');
            $table->uuid('list_id');

            $table->foreign('emailer_id')->references('id')->on('emailers');
            $table->foreign('list_id')->references('id')->on('lists');
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
