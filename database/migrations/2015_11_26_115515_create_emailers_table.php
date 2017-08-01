<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('emailers');
        Schema::create('emailers', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->string('subject');
            $table->text('content')->nullable();
            $table->text('signature')->nullable();
            $table->uuid('template_id')->nullable();

            $table->timestamp('distribute_at')
                ->default(DB::raw('CURRENT_TIMESTAMP'))
                ->nullable();
                
            $table->string('return_name');
            $table->string('return_address');

            $table->boolean('approved')->default(false);
            $table->enum('status', [
                'UNAPPROVED',
                'APPROVED',
                'PENDING',
                'RUNNING',
                'COMPLETED',
                'PAUSED',
            ])->default('UNAPPROVED');

            $table->string('quadrant_uid')->unique()->nullable();
            $table->string('quadrant_list_uid')->unique()->nullable();
            $table->boolean('api_extended_status_received')->default(0);
            $table->json('api_total_recipients_per_group')->nullable();
            $table->json('api_sending_status_numbers')->nullable();

            $table->primary('id');
            $table->foreign('template_id')->references('id')->on('templates');
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
        Schema::dropIfExists('emailer_lists');
        Schema::drop('emailers');
    }
}
