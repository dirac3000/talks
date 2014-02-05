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

		// $this->call('UserTableSeeder');
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

}