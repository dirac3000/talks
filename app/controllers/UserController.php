<?php
 
class UserController extends BaseController {

	/**
	 * Display the users list
	 * @return View
	 */
	public function showList()
	{
		if (!$this->loggedAdmin()) {
			Session::flash('error', 'unauthorized');
			return Redirect::to('/');
		}
		$search = Input::get('search');
		$users = null;
		if ($search == null)
			$users = User::orderBy('name','asc')->paginate(20);
		else {
			$input = Input::all();
			$rules = array( 
				'search' => 'alpha',
	        	);
			$validation = Validator::make($input, $rules);
			if ($validation->fails())
			{
				return Redirect::back()->withInput()
					->withErrors($validation);
			}
			$users = User::where('name','like', "%$search%")
				->orderBy('name','asc')->paginate(20);
		}
		return View::make('user_list')
			->with('users', $users);
	}

 	/**
	 * Display one user
	 * @return View
	 */	
	public function view($id)
	{
		$user_admin_actions = false;
		if (!Auth::guest() && ($this->loggedAdmin() || $id == Auth::user()->id)) {
			$user_admin_actions = true;
		}
		$userToEdit = User::findOrFail($id);

		// if manager is not null, get name
		$manager = null;
		if ($userToEdit->manager_id != null) {
			$manager = 
				User::find($userToEdit->manager_id);
		}
		
		// if logged show team members reservations
		$resa = null;
		$mgr_resa = null;
		if (Auth::user()) {
			$user_id = Auth::user()->id;
			if ($id == $user_id) {
				$mgr_resa = DB::select('select 
					r.id as id, user_id, name, title, 
					r.status as status, date_start, 
					talk_id, comment
				from reservations as r inner join users as u 
				on r.user_id = u.id
				inner join talks as t 
				on t.id = r.talk_id
				where t.date_start > now()
				and t.status ="approved"
				and u.manager_id = ?
				order by t.date_start', array($user_id));
			}
         
         		$resa = DB::select('select 
					title, r.status as status, 
					date_start, talk_id, comment
				from reservations as r
				inner join talks as t
				on t.id = r.talk_id
				where t.date_start > now()
				and t.status ="approved"
				and user_id = ?
				order by t.date_start', array($id));

		}

		return View::make('user')
			->with('user_admin_actions', $user_admin_actions)
			->with('user', $userToEdit)
			->with('manager', $manager)
			->with('reservations', $resa)
			->with('mgr_reservations', $mgr_resa);

	}

 	/**
	 * Edit one user
	 * @return View
	 */	
	public function edit($id)
	{
		if (!$this->loggedAdmin() && (!Auth::guest() && Auth::user()->id != $id)) 
			return $this->unauthorized();

		$user_admin_actions = true;
		$userToEdit = User::findOrFail($id);

		// if manager is not null, get name
		$manager = null;
		if ($userToEdit->manager_id != null) {
			$manager = 
				User::find($userToEdit->manager_id);
		}
		
		$managers = User::orderBy('name','asc')
			->get(array('id','name'));

		return View::make('user_edit')
			->with('user_admin_actions', $user_admin_actions)
			->with('user', $userToEdit)
			->with('managers', $managers);
	}

 	/**
	 * Create a user
	 * @return View
	 */	
	public function create()
	{
		if (!$this->loggedAdmin()) 
			return $this->unauthorized();

		$managers = User::orderBy('name','asc')
			->get(array('id','name'));

		$user = new User();
		return View::make('user_edit')
			->with('user_admin_actions', false)
			->with('user', $user)
			->with('managers', $managers);
	}

	/**
	 * Save user changes
	 * @return View
	 */	
	public function save()
	{
		$user_id = (Input::get('user_id'));
		if (!$this->loggedAdmin() && 
			(!Auth::guest() && Auth::user()->id != $user_id))
			return $this->unauthorized();

		$rules = array(
			'username'	=> 'required|min:3',
			'password'	=> 'min:6',
			'email'		=> 'required|email',
			'name'		=> 'required|min:3',
		);
		$username = Input::get('username');
		$userTest = User::where('username', $username)->first();
		if ($userTest != null)
		{
			if (Input::get('user_id') != $userTest->id)
				$rules['username'] = 'different:'.$username;
		}
		$validator = Validator::make(
			Input::all(),
			$rules);
		if ($validator->fails())
			return Redirect::back()->withInput()
				->withErrors($validator);

		// get new or edited talk and validate rights
		$user = null;
		if ($user_id != null) {
			$user = User::findOrFail($user_id);
		}
		else {
			$user = new User();
		}
		$user->username		=  (Input::get('username'));
		if (Input::get('password') != '')
			$user->password	=  Hash::make(Input::get('password'));
		$user->email		=  (Input::get('email'));
		$user->name		=  (Input::get('name'));
		if (Input::get('manager') != '')
			$user->manager_id	=  (Input::get('manager'));
		$user->rights		=  (Input::get('rights'));

		$user->save();
		// redirect to view saved user
		return Redirect::to('user/'.$user->id);
	}

 	/**
	 * Change a user's rights
	 * @return Redirect
	 */	
	public function changeRights($id, $rights)
	{
		if ($id == Auth::user()->id || !$this->loggedAdmin()) {
			Session::flash('error', 'unauthorized');
			return Redirect::to('/');
		}
		$user = User::findOrFail($id);
		$user->rights = $rights;
		$user->save();

		return Redirect::to('/user/'.$id);	
	}

	/**
	 * delete a user
	 * @return Redirect
	 */
	public function delete($user_id)
	{
		if (!$this->loggedAdmin() || Auth::user()->id == $user_id) {
			return $this->unauthorized();
		}
		$user = User::findOrFail($user_id);
		$name = $user->name;

		// get future reservations ids
		$resa_ids = Reservation::
			join('talks', 'reservations.talk_id', '=', 'talks.id')
			->where('talks.date_start', '>', new DateTime('today'))
			->where('reservations.user_id', $user_id)
			->get(array('reservations.id'));
		$ids = array();
		foreach ($resa_ids as $id)
			$ids[] = $id->id;

		DB::transaction(function() use ($ids, $user) 
		{
			if (!empty($ids))
				Reservation::whereIn('id', $ids)
					->delete();
			$user->delete();
		});

		// get back to the talk list
		$message = Lang::get('messages.userDeleted', 
			array('name' => $name));
		return Redirect::to('user_list')
			->with('message',$message);
	}

}



