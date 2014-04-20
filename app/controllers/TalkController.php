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
			$speaker = Speaker::where('user_id', Auth::user()->id)
				->where('talk_id', $talk_id)->first();
			if (!empty($speaker)) {
				$talk_rights = 'speaker';
			}
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
			$talks = Talk::where('date_start', $condition, new DateTime('today'));
		}
		else {
			$talks = Talk::where('status','=','approved')->
				where('date_start', $condition, new DateTime('today'));
		}
		if ($condition == '>=')
			$talks = $talks->orWhere('date_start', '0-0-0');
		else
			$talks = $talks->where('date_start', '<>', '0-0-0');

		$talks = $talks->orderBy('date_start', 'asc')->paginate(20);

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
	 * Display attendance sheet for a given talk
	 * (This is a light version of the talk view)
	 * @return View
	 */	
	public function attendance($id)
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
			u.deleted_at is null
			and
		       	status <> "refused"
			order by status', array($talk->id));


		return View::make('talk_attendance')
			->with('talk', $talk)
			->with('speaker_names', $speaker_names)
			->with('reservations', $resa);
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
			u.deleted_at is null
			and
		       	(status != "refused" or u.id = ?)
			order by status', array($talk->id, $user_id));

		$confirmed = DB::select('select count(id) as c
			from reservations
			where talk_id = ?
			and status <> "refused"', array($talk->id))[0]->c;

		$attachments = null;
		if ($talk_rights != null)
			$attachments = $talk->attachments()->get();
		else
			$attachments = $talk->attachments()
				->where('privacy','public')->get();

		return View::make('talk_view')
			->with('talk', $talk)
			->with('speaker_names', $speaker_names)
			->with('reservations', $resa)
			->with('confirmed', $confirmed)
			->with('talk_rights', $talk_rights)
			->with('attachments', $attachments);
	}

 	/**
	 * Show talk creation or edit form
	 * @return View
	 */
	public function editTalkForm($talk)
	{
		$user = Auth::user();
		$talksUser = User::orderBy('name')->get();
	
		$name_list = array();
		foreach ($talksUser as $u) {
			$name_list[$u->id] = $u->name;
		}
		$grr_rooms = null;
		if (Config::get('app.use_grr')) {
			$grr_rooms = GrrRoom::orderBy('room_name')
				->get(array('id','room_name'));
		}

		return View::make('talk_edit')->with('user', $user)
			->with('name_list',$name_list)
			->with('talk', $talk)
			->with('grr_rooms', $grr_rooms);		
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
			'target'	=> 'min:10',
			'aim'		=> 'required',
			'reqs'		=> 'min:10',
			'desc'		=> 'min:10',
			'date_start'	=> 
				'date'.$before_end,
			'date_end'	=> 
				'date'.$after_start,
			'places'	=> 'numeric',
		);
		// If we use grr, add grr validation
		if (Config::get('app.use_grr') && !$this->checkGrr()) {
			$rules['location'] = 
				'different:'.Input::get('location');
		}
		$validator = Validator::make(
			Input::all(),
			$rules);
		if ($validator->fails())
			return Redirect::back()->withInput()
				->withErrors($validator);

		// get new or edited talk and validate rights
		$talk_id = (Input::get('talk_id'));
		$talk = null;
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
		if (Config::get('app.use_grr') and $talk->location) {
			$grrUser = Config::get('app.grr_user');
			$title_old = Input::get('talk_title_old');	
			$entry = null;
			if ($title_old != null) {
				$entry = GrrEntry::where('description', 
					URL::to('talk/'. $talk->id))->first();
				if ($entry == null)
					$entry = new GrrEntry();
			}
			else
				$entry = new GrrEntry();
			$entry->timestamps = false;
			$room = GrrRoom::where('room_name', $talk->location)->first();
			// fill entry in GRR
			$entry->name = $talk->title;
			$entry->type = 'T';
			$entry->room_id = $room->id;
			$entry->start_time = strtotime($talk->date_start);
			$entry->end_time = strtotime($talk->date_end);
			$entry->create_by = $grrUser;
			$entry->beneficiaire = $grrUser;
			$entry->description = URL::to('talk/'. $talk->id);
			//dd($entry->toArray());
			$entry->save();
		}

		// if it was a new talk send an email
		if ($talk_id == null) {
			$cc = array();
			$speakers = User::whereIn('id', Input::get('speakers'))->get();
			foreach ($speakers as $spk) {
				$cc[$spk->email] = ucwords(strtolower($spk->name));
			}

			Mail::send('emails.talk_new', 
				array('talk' => $talk), 
				function($message) use ($cc)
			{
				$message->to(Auth::user()->email, Auth::user()->name)
					->subject(trans('email.newTalkSubject'))
					->cc($cc);
			});
		}

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
		
		$user = Auth::user();
		$res = new Reservation();
		$res->talk_id = $talk_id;
		$res->user_id = $user->id;
		$res->save();

		// If it has a manager, ask for permission
		$to = $user->manager()->first();
		if (!empty($to)) {
			$talk = Talk::findOrFail($talk_id);
			Mail::send('emails.talk_reservation', 
				array(	'talk' => $talk, 
					'user' => $user, 
					'manager' => $to), 
				function($message) use ($to)
			{
				$message->subject(trans('email.resAskSubject'))
					->to($to->email, ucwords(strtolower($to->name)));
			});
		}	

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
		$user = 	Auth::user();

		$status = 	Input::get('status');
		$comments =	Input::get('comment');
		$res_ids =	array_keys($status);

		$resa = Reservation::whereIn('id', $res_ids)->get();

		$valid = true;
		foreach($resa as $res) {
			$statusMail = ($res->status != $status[$res->id]);
			$res->status = $status[$res->id];
			$res->comment = e($comments[$res->id]);
			if ($res->status == 'refused' && $res->comment == '') {
				$valid = false;
				continue;
			}
			// save whatever is already valid
			$res->save();
			if (!$statusMail)
				continue;
			$to = $res->user()->firstOrFail();
			$talk = $res->talk()->firstOrFail();
			Mail::send('emails.talk_res_status', 
				array('talk' => $talk, 'res' => $res), 
				function($message) use ($to)
			{
				$message->subject(trans('email.resStatusSubject'))
					->to($to->email, ucwords(strtolower($to->name)));
			});
		}
		if (!$valid) {
			Session::flash('reservation_errors', 
				Lang::get('errors.needRefusalComment'));
			return Redirect::to('user/'.$mgr_id);
		}


		return Redirect::to('user/'.$mgr_id);

	}

	/**
	 * Send email to reservers for cancelled or deleted talks 
	 */
	protected function mailDeletedReservations($talk) {
		$mail = 'emails.talk_deleted';
		$bcc = array();
		$resUsers = $talk->reservationUsers()
			->where('reservations.status','!=','cancelled')->get();
		foreach ($resUsers as $user) {
			if ($user->email != null)
				$bcc[$user->email] = ucwords(strtolower($user->name));
		}
		if (empty($bcc))
			return;

		// Now send email!
		Mail::send('emails.talk_deleted', 
			array('talk' => $talk), 
			function($message) use ($bcc)
		{
			$message->subject(trans('email.delTalkSubject'))
				->bcc($bcc);
		});
	}

	/**
	 * Send email for changed status (for speakers)
	 */
	protected function mailStatusChange($talk, $status) 
	{
		if ($status == 'approved') {
			$subject = 'email.statusApprSubject';
		}
		else if ($status == 'cancelled') {
			$subject = 'email.statusCancelSubject';
		}
		else if ($status == 'deleted') {
			$subject = 'email.statusDeletedSubject';
		}
		else {
			return $this->unauthorized();
		}

		$speakers = $talk->speakerUsers()->get();
		$to = $talk->creator()->first(); 
		$cc = array();	
		foreach ($speakers as $spk) {
			$cc[$spk->email] = ucwords(strtolower($spk->name));
		}
		Mail::send('emails.talk_status', 
			array('talk' => $talk, 'status' => $status), 
			function($message) use ($cc, $subject, $to)
		{
			$message->subject(trans($subject))
				->to($to->email, ucwords(strtolower($to->name)))
				->cc($cc);
		});
		
	}

	/**
	 * change talk status
	 * @return Redirect
	 */
	public function changeStatus($talk_id)
	{
		if (!$this->loggedAdmin()) 
			return $this->unauthorized();

		$talk = Talk::findOrFail($talk_id);
		$status = Input::get('talk_status');
		if (!in_array($status, 
			array('pending', 'approved', 'cancelled')))
			return $this->unauthorized();
		$talk->status = $status;
		$talk->save();
		
		// prepare mail based on status change
		$mailSend = null;
		if ($talk->future()) {
			if ($status == 'approved') {
				$this->mailStatusChange($talk, $status);
			}
			else if ($status == 'cancelled') {
				$this->mailStatusChange($talk, $status);
				$this->mailDeletedReservations($talk);
				// if we cancel talk, delete reservations
				Reservation::where('talk_id', $talk->id)->delete();
			}
		}	

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
		
		if ($talk->future()) {
			// first send mails to speakers and reserved users
			$this->mailStatusChange($talk, 'deleted');
			$this->mailDeletedReservations($talk);
		}

		if (Config::get('app.use_grr')) {
			$grrUser = Config::get('app.grr_user');
			$entry = null;
			$entry = GrrEntry::where('description', 
				URL::to('talk/'. $talk->id))->first();
			if ($entry != null) {
				$entry->delete();
			}
		}
		DB::transaction(function($talk) use ($talk)
		{
			// this will delete cascade on tables related to talk
			$talk->delete();	
		});
		DB::commit();
		// get back to the talk list
		$message = Lang::get('messages.talkDeleted', 
			array('title' => $title));
		return Redirect::to('/')
			->with('message',$message);
	}

	/**
	 * show talk's upload attachment form
	 * @return View
	 */
	public function attachTalk($talk_id)
	{
		$talk_rights = $this->talkRights($talk_id);
		if ($talk_rights == null)
			return $this->unauthorized();
		$talk = Talk::findOrFail($talk_id);


		return View::make('talk_attach')
			->with('talk_rights',$talk_rights)
			->with('talk', $talk);
	}

	/**
	 * show talk's upload attachment form
	 * @return Redirect
	 */
	public function uploadAttachment($talk_id)
	{
		$talk_rights = $this->talkRights($talk_id);
		if ($talk_rights == null)
			return $this->unauthorized();

		$input = Input::all();
		$rules = array( 
			'attachment' => 'required|max:50000000',
			'visibility' => 'required|in:public,private'
        	);
		$validation = Validator::make($input, $rules);
		if ($validation->fails())
		{
			return Redirect::back()->withInput()
				->withErrors($validation);
		}

		$file = Input::file('attachment');
		$destinationPath = 'public/uploads/'.$talk_id.'/'.str_random(8);
		$filename = $file->getClientOriginalName();
		//$filename = $file['name'];
		$uploadSuccess = $file->move($destinationPath, $filename);

		if ($uploadSuccess == false) {
			return Redirect::back()->withInput()
				->with('error', trans('uploadError'));
		}

		$att = new Attachment();
		$att->talk_id =	$talk_id;
		$att->user_id =	Auth::user()->id;
		$att->path =	$destinationPath . '/' . $filename;
		$att->privacy =	Input::get('visibility');

		$att->save();
		// get back to the talk view
		return Redirect::to('talk/'.$talk_id);
	}


	/**
	 * change attachment privacy
	 * @return Redirect
	 */
	public function setAttachmentPrivacy($att_id)
	{
		$att = Attachment::findOrFail($att_id);
		$talk_id = $att->talk_id;
		$privacy = Input::get('privacy');
		$talk_rights = $this->talkRights($talk_id);
		if ($talk_rights == null ||
			!in_array($privacy, array('public', 'private')))
			return $this->unauthorized();
		
		$att->privacy = $privacy;
		$att->save();
		return Redirect::to('talk/'.$talk_id);

	}

	/**
	 * delete attachment
	 * @return Redirect
	 */
	public function deleteAttachment($att_id)
	{
		$att = Attachment::findOrFail($att_id);
		$talk_id = $att->talk_id;
		$talk_rights = $this->talkRights($talk_id);
		if ($talk_rights == null)
			return $this->unauthorized();
		$path = $att->path;
		// remove from db
		$att->delete();
		// remove file
		File::delete($path);
		// remove parent directory (it was a random name)
		rmdir(dirname($path));

		return Redirect::to('talk/'.$talk_id)
			->with('message', trans('messages.attachmentDeleted'));

	}

	/**
	 * Check if the resource specified in the form is available
	 * @return bool
	 */
	protected function checkGrr()
	{
		$title = Input::get('talk_title_old');
		$date_start = Input::get('date_start');
		$date_end = Input::get('date_end');
		$location = Input::get('location');
		$talk_id = (Input::get('talk_id'));

		$entry = GrrEntry::join('room', 'entry.room_id', '=', 'room.id')
			->where('room.room_name', $location)
			->where('start_time', '<=', strtotime($date_end))
			->where('end_time', '>=', strtotime($date_start))
			->where('entry.description', '<>', URL::to('talk/'.$talk_id))
			->first(array('entry.*'));
		//dd(DB::connection('grr')->getQueryLog());
		return ($entry == null);
	}

	/**
	 * Check if the resource is available
	 * @return json
	 */
	public function checkGrrJson()
	{
		if (Auth::guest()) {
			return Response::json(null, 404);;
		}		
		$check = $this->checkGrr();
		return Response::json($check, 200);
	}

}


