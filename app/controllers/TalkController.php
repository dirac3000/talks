<?php
 
class TalkController extends BaseController {
 	/**
	 * Display the talks list
	 * @return View
	 */
	public function getTalks()
	{
		$talks = Talk::where('status','=','approved')->
			where('date_start', '>=', new DateTime('today'))->
			orderBy('updated_at', 'asc')->paginate(5);
	
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
		return View::make('talk_view')
			->with('talk', $talk)
			->with('speaker_names', $speaker_names);
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
	$user = User::findOrFail( Auth::user()->id);
	if ($user->rights != 'admin') {
		Session::flash('error', 'unauthorized');
		Redirect::to('/');
		die();
	}

	$new_talk = array(
		'creator_id'	=> $user->id,	
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


