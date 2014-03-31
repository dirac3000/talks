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


		if (Config::get('app.use_grr')) {
			$conn = DB::connection('grr');
			if ($conn->table('type_area')
				->where('type_name','Talks')
				->where('type_letter','T')
				->first() != null) {
					$conn->table('type_area')->insert(array(
						'type_name' => 'Talks',
						'type_letter' => 'T'));
			}
		}

	}

}
