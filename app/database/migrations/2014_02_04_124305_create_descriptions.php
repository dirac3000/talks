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
			$table->integer('talk_id')->unsigned();
			$table->foreign('talk_id')->references('id')->on('talks');;
			$table->string('title', 255);
			$table->text('target')->nullable();; /* who this session is for */
			$table->text('aim')->nullable();; /* what is the aim */
			$table->text('requirements')->nullable();; /* what you should know before attending */
			$table->text('description')->nullable();; /* long description */
			$table->string('location', 255)->nullable();; /* where it is going to be */ 
			$table->timestamp('date_start')->nullable();;
			$table->timestamp('date_end')->nullable();;
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
