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
			$table->integer('manager_id')->unsigned()->nullable();
			$table->foreign('manager_id')->references('id')->on('users');
		});

		DB::table('users')->insert(array(
            'username'  => 'admin',
            'password'  => Hash::make('password'),
            'rights'  => 'admin',
        ));

		DB::table('users')->insert(array(
            'username'  => 'henri',
            'password'  => Hash::make('password'),
            'email' => 'henri@example.com',
            'rights'  => 'advanced',
        ));

		DB::table('users')->insert(array(
            'username'  => 'christopher',
            'password'  => Hash::make('password'),
            'email' => 'christopher@example.com',
            'rights'  => 'advanced',
            'manager_id' => 2, /* I know henri will be 2 */
        ));

		DB::table('users')->insert(array(
            'username'  => 'karine',
            'password'  => Hash::make('password'),
            'email' => 'karine@example.com',
            'rights'  => 'simple',
            'manager_id' => 3, /* I know christophe will be 3 */ 
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