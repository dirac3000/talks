<?php

use Illuminate\Database\Migrations\Migration;

class CreateAttachments extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attachments', function($table) {
			$table->increments('id');
			$table->string('path', 500); 
			$table->integer('talk_id')->unsigned();
			$table->foreign('talk_id')->references('id')
				->on('talks');->onDelete('cascade');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->enum('privacy', 
				array('public', 'private'))->default('public');
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
		Schema::drop('attachments');
	}

}
