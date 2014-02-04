<?php

use Illuminate\Database\Migrations\Migration;

class CreateSessions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sessions', function($table) {
			$table->increments('id');
			$table->integer('manager')->unsigned();
			$table->foreign('manager')->references('id')->on('users');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
			Schema::drop('sessions');
	}

}
