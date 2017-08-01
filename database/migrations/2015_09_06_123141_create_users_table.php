<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('users');
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->string('username')->unique();
            $table->string('email');
            $table->string('password');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->text('signature')->nullable();
            $table->enum('role', [
                'ADMIN',
                'CONTENTADMIN',
                'USER'
            ])->default('CONTENTADMIN');

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
        Schema::drop('users');
    }
}
