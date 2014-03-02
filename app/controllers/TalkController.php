<?php
 
class TalkController extends BaseController {

	/*
	 * Resolve type of rights on this talk 
	 * @return String (admin, speaker, null)
	 */
	protected function talkRights($talk_id)
	{
		$talk_rights = null;
		if ($this->loggedAdmin())
			$talk_rights = 'admin';
		else if (!Auth::guest()) {
			$speaker = Speaker::where('user_id', Auth::user())
				->where('talk_id', $talk_id);
			if ($speaker)
				$talk_rights = 'speaker';
		}
		return $talk_rights;
	}

	/**
	 * Display a talks list with a given time condition
	 * @return View
	 */
	protected function getTalks($condition)
	{
		if ($this->loggedAdmin()) {
			$talks = Talk::where('date_start', $condition, new DateTime('today'))->
				orderBy('date_start', 'asc')->paginate(20);
		}
		else {
			$talks = Talk::where('status','=','approved')->
				where('date_start', $condition, new DateTime('today'))->
				orderBy('date_start', 'asc')->paginate(20);
		}

		return View::make('talk_list')
			->with('talks', $talks);
	}

	/**
	 * Display upcoming talks list
	 * @return View
	 */
	public function pastTalks()
	{
		return $this->getTalks('<=')
			->with('title', trans('messages.pastTalksTitle'));
	}


	/**
	 * Display past talks list
	 * @return View
	 */
	public function futureTalks()
	{
		return $this->getTalks('>=')
			->with('title', trans('messages.upcomingTalksTitle'));
	}

 	/**
	 * Display one talk with description
	 * @return View
	 */	
	public function viewTalk($id)
	{
		$talk = Talk::findOrFail($id);
	
		// Determine what level of rights the visitor has on this talk
		$talk_rights = $this->talkRights($id);
		if ($talk_rights == null && $talk->status != 'approved')
			return $this->unauthorized(); 

		// get speakers name as array
		$name_list = DB::select('select name from users 
			inner join speakers on speakers.user_id = users.id
			where speakers.talk_id = ?', array($talk->id));
		$speaker_names = array();
		foreach ($name_list as $spk)
			$speaker_names[] = $spk->name;

		$user_id = (Auth::guest()? null : Auth::user()->id);
		$resa = DB::select('select r.id as id, name, status, user_id
			from reservations as r inner join users as u 
			on r.user_id = u.id
			where r.talk_id = ? 
			and
		       	(status != "refused" or u.id = ?)
			order by status', array($talk->id, $user_id));

		$confirmed = DB::select('select count(id) as c
			from reservations
			where talk_id = ?
			and status = "approved"', array($talk->id))[0]->c;

		return View::make('talk_view')
			->with('talk', $talk)
			->with('speaker_names', $speaker_names)
			->with('reservations', $resa)
			->with('confirmed', $confirmed)
			->with('talk_rights', $talk_rights);
	}

 	/**
	 * Show talk creation or edit form
	 * @return View
	 */
	public function editTalkForm($talk)
	{
		$user = Auth::user();
		$talksUser = User::all();
	
		$name_list = array();
		foreach ($talksUser as $u) {
			$name_list[$u->id] = $u->name;
		}

		return View::make('talk_edit')->with('user', $user)
			->with('name_list',$name_list)
			->with('talk', $talk);
	}

 	/**
	 * Prepare creation form
	 * @return View
	 */
	public function createTalk()
	{
		$talk = new Talk();
		return $this->editTalkForm($talk)->
			with('title', trans('messages.newTalkTitle'))
			->with('talk_rights', null);
	}

 	/**
	 * Show talk edit form
	 * @return View
	 */
	public function editTalk($talk_id)
	{
		$talk_rights = $this->talkRights($talk_id);
		if ($talk_rights == null)
			return $this->unauthorized();
		$talk = Talk::findOrFail($talk_id);
		
		// get speakers name as array
		$speakers = $talk->speakers()
			->get();
		$speakers_list = array();
		foreach ($speakers as $spk)
			$speakers_list[] = $spk->user_id;

		return $this->editTalkForm($talk)
			->with('title', trans('messages.editTalkTitle'))
			->with('speakers_list', $speakers_list)
			->with('talk_rights', $talk_rights);
			
	}

 	/**
	 * Process new talk input and creates it
	 * @return Redirect
	 */
	public function processTalk()
	{
		// Form validation
		$before_end =	strtotime(Input::get('date_end'))?
			'|before:'.Input::get('date_end') : '';
		$after_start =	strtotime(Input::get('date_start'))?
			'|after:'.Input::get('date_start') : '';
		$rules = array(
			'talk_id'	=> 'numeric',
			'title'		=> 'required|max:255',
			'speakers'	=> 'required',
			'target'	=> 'min:3',
			'aim'		=> 'required',
			'reqs'		=> 'min:3',
			'desc'		=> 'min:3',
			'date_start'	=> 
				'date'.$before_end,
			'date_end'	=> 
				'date'.$after_start,
			'places'	=> 'numeric',
		);
		$validator = Validator::make(
			Input::all(),
			$rules);
		if ($validator->fails())
			return Redirect::back()->withInput()
				->withErrors($validator);

		// get new or edited talk and validate rights
		$talk_id = (Input::get('talk_id'));
		if ($talk_id != null) {
			$talk = Talk::findOrFail($talk_id);
		}
		else {
			$talk = new Talk();
			$talk->creator_id = Auth::user()->id;	
		}
		// Verify rights on this talk 
		if ($this->talkRights($talk_id) == null) {
			return $this->unauthorized();
		}

		$talk->title		= e(Input::get('title'));
		$talk->target		= e(Input::get('target'));
		$talk->aim		= e(Input::get('aim'));
		$talk->requirements	= e(Input::get('reqs'));
		$talk->description	= e(Input::get('desc'));
		$talk->date_start	= Input::get('date_start');
		$talk->date_end		= Input::get('date_end');
		$talk->places		= e(Input::get('places'));
		$talk->location		= e(Input::get('location'));

		// Do the database changes within a transaction
		DB::transaction(function($talk) use ($talk)
		{
			$talk->save();
			// We do it the easiest way: we delete all speakers
			// and then add them again.
			$talk->speakers()->delete();
			// Add speakers
			foreach (Input::get('speakers') as $speaker_id) {
				$speaker = new Speaker();
				$speaker->user_id = $speaker_id;
				$speaker->talk_id = $talk->id;
				$speaker->save();
			}
		});
		DB::commit();

		// redirect to view saved post
		return Redirect::to('talk/' . $talk->id);
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
			return $this->unauthorized();

		/* Delete reservation */
		$res->delete();
	
		return Redirect::to('talk/'.$talk_id);
	}

	/**
	 * Edits reservation (status and comment)
	 * @return Redirect
	 */
	public function editReservations($mgr_id)
	{
		if (Auth::user()->id != $mgr_id)
			return $this->unauthorized();


		$status = 	Input::get('status');
		$comments =	Input::get('comment');
		$res_ids =	array_keys($status);

		$resa = Reservation::whereIn('id', $res_ids)->get();

		$valid = true;
		foreach($resa as $res) {
			$res->status = $status[$res->id];
			$res->comment = e($comments[$res->id]);
			if ($res->status == 'refused' && $res->comment == '') {
				$valid = false;
				continue;
			}
			// save whatever is already valid
			$res->save();
		}
		if (!$valid) {
			Session::flash('reservation_errors', 
				Lang::get('errors.needRefusalComment'));
			return Redirect::to('user/'.$mgr_id);
		}


		$inputs= Input::all();

		ob_start();
		var_dump($inputs);
		$result = ob_get_clean();

		return Redirect::to('user/'.$mgr_id);

	}

	/**
	 * change talk status
	 * @return Redirect
	 */
	public function changeStatus($talk_id)
	{
		if (!$this->loggedAdmin()) {
			return $this->unauthorized();
		}

		$talk = Talk::findOrFail($talk_id);
		$status = Input::get('talk_status');
		if (!in_array($status, 
			array('pending', 'approved', 'cancelled')))
			return $this->unauthorized();
		// status validation already in route pattern
		$talk->status = $status;

		$talk->save();

		// get back to the talk view
		return Redirect::to('talk/'.$talk_id);
	}

	/**
	 * delete a talk
	 * @return Redirect
	 */
	public function deleteTalk($talk_id)
	{
		if (!$this->loggedAdmin()) {
			return $this->unauthorized();
		}
		$talk = Talk::findOrFail($talk_id);
		$title = $talk->title;
		
		DB::transaction(function($talk) use ($talk)
		{
			$talk->delete();	
		});
		DB::commit();
		// get back to the talk list
		$message = Lang::get('messages.talkDeleted', 
			array('title' => $title));
		return Redirect::to('/')
			->with('message',$message);
	}
}


