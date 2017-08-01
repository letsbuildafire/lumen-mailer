<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHelpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('help');
        Schema::create('help', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->string('title');
            $table->text('content')->nullable();
            $table->enum('section', [
                'EMAILERS',
                'TEMPLATES',
                'LISTS',
                'GENERAL'
            ])->default('GENERAL');

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
        Schema::drop('help');
    }
}
