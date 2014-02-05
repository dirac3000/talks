<?php

use Illuminate\Database\Migrations\Migration;

class CreateSpeakers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('speakers', function($table) {
			$table->increments('id');
			$table->integer('session_id')->unsigned();
			$table->foreign('session_id')->references('id')->on('talks');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');;
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('speakers');
	}

}
