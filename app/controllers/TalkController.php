<?php
 
class TalkController extends BaseController {

	/**
	 * Display the talks list
	 * @return View
	 */
	public function getTalks()
	{
		if ($this->loggedAdmin()) {
			$talks = Talk::where('date_start', '>=', new DateTime('today'))->
				orderBy('updated_at', 'asc')->paginate(20);
		}
		else {
			$talks = Talk::where('status','=','approved')->
				where('date_start', '>=', new DateTime('today'))->
				orderBy('updated_at', 'asc')->paginate(20);
		}

		return View::make('home')
			->with('talks', $talks);
	}

 	/**
	 * Display one talk with description
	 * @return View
	 */	
	public function viewTalk($id)
	{
		$talk = Talk::findOrFail($id);
	
		$name_list = DB::select('select name from users 
			inner join speakers on speakers.user_id = users.id
			where speakers.talk_id = ?', array($talk->id));
		$speaker_names = $name_list[0];

		$resa = DB::select('select r.id as id, name, status, user_id
			from reservations as r inner join users as u 
			on r.user_id = u.id
			where r.talk_id = ? 
			order by status', array($talk->id));
		
		$confirmed = DB::select('select count(id) as c
			from reservations
			where talk_id = ?', array($talk->id))[0]->c;

		return View::make('talk_view')
			->with('talk', $talk)
			->with('speaker_names', $speaker_names)
			->with('reservations', $resa)
			->with('confirmed', $confirmed);
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
			return $this->unauthorized();
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

 	/**
	 * Process new reservation
	 * @return Redirect
	 */
	public function addReservation($talk_id)
	{
		if (Auth::guest()) {
			return $this->unauthorized();
		}		
		
		$res = new Reservation();
		$res->talk_id = $talk_id;
		$res->user_id = Auth::user()->id;
		$res->save();

		// get back to the talk view
		return Redirect::to('talk/'.$talk_id);
	}

 	/**
	 * Deletes reservation
	 * @return Redirect
	 */
	public function delReservation()
	{
		if (Auth::guest()) {
			return $this->unauthorized();
		}		
		$res_id = Input::get('res_id');

		$res = Reservation::findOrFail($res_id);
		$talk_id = $res->talk_id;

		if ($res->user_id != Auth::user()->id)
			;//return $this->unauthorized();

		/* Delete reservation */
		$res->delete();
			return Redirect::to('talk/'.$talk_id);
	}

}


