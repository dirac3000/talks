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
		$users = User::orderBy('name','asc')->paginate(20);
		return View::make('user_list')
			->with('users', $users);
	}

 	/**
	 * Display one user
	 * @return View
	 */	
	public function view($id)
	{
		$adminActions = false;
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
	
		return View::make('user')
			->with('user', $userToEdit)
			->with('manager', $manager);
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
	 * Show talk creation form
	 * @return View
	 */
	public function createTalk()
	{
		$user = Auth::user();
		$talksUser = User::all();
	
		$name_list = array();
		foreach ($talksUser as $u) {
			$name_list[$u->id] = $u->name;
		}
	
		return View::make('talk_new')->with('user', $user)->
			with('name_list',$name_list);
	}

 	/**
	 * Process new talk input and creates it
	 * @return Redirect
	 */
	public function processNewTalk()
	{
		if (!$this->loggedAdmin()) {
			Session::flash('error', 'unauthorized');
			return Redirect::to('/');
		}
	
		$new_talk = array(
			'creator_id'	=> Auth::user()->id,	
			'title'		=> Input::get('title'),
			'target'	=> Input::get('target'),
			'aim'		=> Input::get('aim'),
			'requirements'	=> Input::get('reqs'),
			'description'	=> Input::get('desc'),
			'date_start'	=> Input::get('date_start'),
			'date_end'	=> Input::get('date_end'),
			'places'	=> Input::get('places'),
			'location'	=> Input::get('location'),
		);
		
		// TODO: Add validation here!
		
		// create the new talk after passing validation
		$talk = new Talk();
		$talk->creator_id	= $user->id;	
		$talk->title		= Input::get('title');
		$talk->target		= Input::get('target');
		$talk->aim		= Input::get('aim');
		$talk->requirements	= Input::get('reqs');
		$talk->description	= Input::get('desc');
		$talk->date_start	= Input::get('date_start');
		$talk->date_end		= Input::get('date_end');
		$talk->places		= Input::get('places');
		$talk->location		= Input::get('location');
	
		$talk->save();
		// now that we have the talk go on with the spakers
		foreach (Input::get('speakers') as $speaker_id) {
			$speaker = new Speaker();
			$speaker->user_id = $speaker_id;
			$speaker->talk_id = $talk->id;
			$speaker->save();
		}
	
		// redirect to viewing all posts
		return Redirect::to('/');
	}

}



