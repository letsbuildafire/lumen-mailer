<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailerOpensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('emailer_opens');
        Schema::create('emailer_opens', function (Blueprint $table) {
            $table->uuid('emailer_id');
            $table->uuid('address_id');
            $table->timestamp('opened_at')
                ->default(DB::raw('CURRENT_TIMESTAMP'))
                ->nullable();

            $table->string('useragent');
            $table->string('ip_address');

            $table->foreign('emailer_id')->references('id')->on('emailers');
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
        Schema::dropIfExists('emailer_opens');
    }
}
