<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailerStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('emailer_stats');
        Schema::create('emailer_stats', function (Blueprint $table) {
            $table->primary(['emailer_id', 'address_id']);
            $table->uuid('emailer_id');
            $table->uuid('address_id');

            $table->json('extended_status');
            
            $table->boolean('bounced')->default(false);
            $table->integer('opens')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('unsubscribed')->default(false);
            $table->enum('status', [
                'UNKNOWN',
                'ACCEPTED',
                'DEFERRED',
                'BOUNCED'
            ])->default('UNKNOWN');

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
        Schema::dropIfExists('emailer_stats');
    }
}
