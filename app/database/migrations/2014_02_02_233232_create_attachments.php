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
			$table->integer('session_id');
			$table->integer('user_id');
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