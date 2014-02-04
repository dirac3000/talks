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
			$table->integer('session_id'); 
			$table->integer('user_id');
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