<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('lists');
        Schema::create('lists', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->string('name')->unique();
            $table->json('custom_fields')->nullable();

            $table->uuid('quadrant_uid')->unique()->nullable();

            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emailer_lists');
        Schema::dropIfExists('list_addresses');
        Schema::drop('lists');
    }
}
