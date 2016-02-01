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
            $table->increments('id')->unique();
            $table->string('owner');
            $table->string('subject');
            // $table->string('slug')->unique();
            $table->text('body');
            $table->string('message_id');
            $table->string('message_number'); 
            $table->string('sent_date');
            $table->string('recieved_date');
            $table->string('sender_address');
            $table->string('from_address');
            $table->string('size');
            $table->integer('udate');
            $table->text('attachments');

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
