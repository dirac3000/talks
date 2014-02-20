<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		DB::table('users')->insert(array(
			'username'  	=> 'admin',
			'name'		=> 'Administrator',
            		'password'  	=> Hash::make('password'),
            		'rights'  	=> 'admin',
		));


	}

}
