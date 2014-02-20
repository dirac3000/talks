<?php

use Illuminate\Database\Migrations\Migration;

class CreateUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		/* We won't have password or email info: we will get this info via LDAP */
		Schema::create('users', function($table) {
			$table->increments('id');
			$table->string('username', 64)->nullable(); /* this could actually be an LDAP username */
			$table->string('password', 64)->nullable(); /* we won't use this if we use LDAP */
			$table->string('name', 320)->nullable(); /* descriptive name (name+surname) */
			$table->string('email', 320)->nullable(); /* we won't use this if we use LDAP */
			$table->enum('rights', 
				array('admin', 'advanced', 'simple'))->default('simple');
			$table->integer('manager_id')->unsigned()->nullable();
			$table->foreign('manager_id')->references('id')->on('users');
			$table->softDeletes(); /* This doesn't really delete users. */
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
		Schema::drop('users');
	}

}
