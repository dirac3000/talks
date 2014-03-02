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
			$table->integer('talk_id')->unsigned();
			$table->foreign('talk_id')->references('id')
				->on('talks')->onDelete('cascade');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')
				->on('users');
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
		Schema::drop('speakers');
	}

}
