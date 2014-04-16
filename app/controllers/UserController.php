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
		if ($this->loggedAdmin() && $id != Auth::user()->id) {
			Session::flash('user_admin_actions', 'true');
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
		if (!$this->loggedAdmin()) 
			return $this->unauthorized();

		if ($id != Auth::user()->id) {
			Session::flash('user_admin_actions', 'true');
		}
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
			->with('user', $userToEdit)
			->with('managers', $managers);
	}

	/**
	 * Save user changes
	 * @return View
	 */	
	public function save()
	{
		if (!$this->loggedAdmin()) 
			return $this->unauthorized();

		$rules = array(
			'username'	=> 'required|min:3',
			'password'	=> 'min:6',
			'email'		=> 'required|email',
			'name'		=> 'required|min:3',
		);
		$validator = Validator::make(
			Input::all(),
			$rules);
		if ($validator->fails())
			return Redirect::back()->withInput()
				->withErrors($validator);

		// get new or edited talk and validate rights
		$user_id = (Input::get('user_id'));
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


}



