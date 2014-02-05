<?php

use Illuminate\Database\Migrations\Migration;

class CreateDescriptions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('descriptions', function($table) {
			$table->increments('id');
			$table->integer('session_id')->unsigned();
			$table->foreign('session_id')->references('id')->on('talks');;
			$table->string('title', 255);
			$table->text('target'); /* who this session is for */
			$table->text('aim'); /* what is the aim */
			$table->text('requirements'); /* what you should know before attending */
			$table->text('description'); /* long description */
			$table->string('location', 255); /* where it is going to be */ 
			$table->timestamp('date_start');
			$table->timestamp('date_end');
			$table->integer('places'); /* how many places are available */
			$table->enum('status', 
				array('pending', 'approved', 'cancelled'))->default('pending');
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
			Schema::drop('descriptions');
	}

}
