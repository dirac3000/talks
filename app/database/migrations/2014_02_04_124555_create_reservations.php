<?php

use Illuminate\Database\Migrations\Migration;

class CreateReservations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reservations', function($table) {
			$table->increments('id');
			$table->integer('talk_id')->unsigned();
			$table->foreign('talk_id')->references('id')->on('talks');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->enum('status',
				array('approved', 'pending', 'refused'))->default('pending');
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
		Schema::drop('reservations');
	}

}
