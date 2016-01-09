<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->increments('id');

            $table->text('body')->nullable();
            $table->string('return_path')->nullable();
            $table->string('x_original_to')->nullable();
            $table->string('delivered_to')->nullable();
            $table->string('mailto')->nullable(); 
            $table->string('subject')->nullable(); 
            $table->string('message_id')->nullable();
            $table->tinyInteger('maildate')->nullable();
            $table->string('mailfrom')->nullable();
            $table->tinyInteger('added')->nullable();
            $table->string('content_transfer_encoding')->nullable();

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
        Schema::drop('ads');
    }
}
