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
			$table->string('username', 64); /* this could actually be an LDAP id */
			$table->string('password', 32)->nullable(); /* we won't use this if we use LDAP */
			$table->string('email', 64)->nullable(); /* we won't use this if we use LDAP */
			$table->enum('rights', 
				array('admin', 'advanced', 'simple'))->default('simple');
		});

		DB::table('users')->insert(array(
            'username'  => 'admin',
            'password'  => Hash::make('password'),
            'rights'  => 'admin'
        ));
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