<?php

/*
IF YOU NEED TO DEBUG QUERIES 

<script type="text/javascript">
	var queries = {{ json_encode(DB::getQueryLog()) }};
	console.log('/------------------------------ Database Queries ------------------------------/');
	console.log(' ');
	queries.forEach(function(query) {
		console.log('   ' + query.time + ' | ' + query.query + ' | ' + query.bindings[0]);
	});
	console.log(' ');
	console.log('/------------------------------ End Queries -----------------------------------/');
</script>
*/

/*
 * GLOBAL PATTERNS
 */
Route::pattern('id', '[0-9]+');

/*
 * TALKS ROUTES
 */

// Index page is a list of upcoming talks
Route::get('/', 'TalkController@futureTalks');

// List of past talks
Route::get('past', 'TalkController@pastTalks');

// Talk description view
Route::get('talk/{id}', 'TalkController@viewTalk');

// New talk form
Route::get('talk_new', 
	array('before' => 'auth', 'uses' => 'TalkController@createTalk'));

// Edit talk form
Route::get('talk_edit/{id}', 
	array('before' => 'auth', 'uses' => 'TalkController@editTalk'));

// When the new post is submitted we handle that here
Route::post('talk_save', 
	array('before' => 'auth', 'uses' => 'TalkController@processTalk'));

// Upload attachment form
Route::get('talk_attach/{id}', 
	array('before' => 'auth', 'uses' => 'TalkController@attachTalk'));

// Upload attachment
Route::post('talk_attach/{id}', 
	array('before' => 'auth', 'uses' => 'TalkController@uploadAttachment'));

// Change Talk attachment privacy
Route::post('talk_attach/{id}/privacy', 
	array('before' => 'auth', 'uses' => 'TalkController@setAttachmentPrivacy'));

// Delete attachment
Route::delete('talk_attach/{id}', 
	array('before' => 'auth', 'uses' => 'TalkController@deleteAttachment'));

// Reservation add
Route::post('talk_res_add/{id}', 
	array('before' => 'auth', 'uses' => 'TalkController@addReservation'));

// Reservation del
Route::post('talk_res_del', 
	array('before' => 'auth', 'uses' => 'TalkController@delReservation'));

// Reservation Management (related to user)
Route::post('res_mgr/{id}', 
	array('before' => 'auth', 'uses' => 'TalkController@editReservations'));

// Change Talk status
Route::post('talk_status/{id}', 
	array('before' => 'auth', 'uses' => 'TalkController@changeStatus'));

// Delete a Talk
Route::delete('talk_delete/{id}', 
	array('before' => 'auth', 'uses' => 'TalkController@deleteTalk'));

// Get Attendance Sheet
Route::get('talk_attendance/{id}', 
	array('uses' => 'TalkController@attendance'));

// Get GRR resources
Route::post('talk_grr', 
	array('before' => 'auth', 'uses' => 'TalkController@checkGrrJson'));

/*
 * LOGIN PART
 */

// Login form
Route::get('login', 'AuthController@getLogin');

// Process login
Route::post('login', 'AuthController@postLogin');

// Process Logout process
Route::get('logout', 'AuthController@getLogout');


/*
 * USERS MANAGEMENT PART
 */

// list of all users
Route::get('user_list', 
	array('before' => 'auth', 'uses' => 'UserController@showList'));

// New user form
Route::get('user_new', 
	array('before' => 'auth', 'uses' => 'UserController@create'));

// User View
Route::get('user/{id}', 'UserController@view');

// User Edit
Route::get('user/{id}/edit', 
	array('before' => 'auth', 'uses' => 'UserController@edit'));

// User Rigths Change
Route::get('user/{id}/rights={rights}', 'UserController@changeRights');

// When the new post is submitted we handle that here
Route::post('user_save', 
	array('before' => 'auth', 'uses' => 'UserController@save'));

// Delete a User
Route::delete('user/{id}/delete', 
	array('before' => 'auth', 'uses' => 'UserController@delete'));


/*
|--------------------------------------------------------------------------
| Application 404 & 500 Error Handlers
|--------------------------------------------------------------------------
|
| To centralize and simplify 404 handling, Laravel uses an awesome event
| system to retrieve the response. Feel free to modify this function to
| your tastes and the needs of your application.
|
| Similarly, we use an event to handle the display of 500 level errors
| within the application. These errors are fired when there is an
| uncaught exception thrown in the application.
|
*/

Event::listen('404', function()
{
	return Response::error('404');
});

Event::listen('500', function()
{
	return Response::error('500');
});

/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
|
| Filters provide a convenient method for attaching functionality to your
| routes. The built-in before and after filters are called before and
| after every request to your application, and you may even create
| other filters that can be attached to individual routes.
|
| Let's walk through an example...
|
| First, define a filter:
|
|		Route::filter('filter', function()
|		{
|			return 'Filtered!';
|		});
|
| Next, attach the filter to a route:
|
|		Router::register('GET /', array('before' => 'filter', function()
|		{
|			return 'Hello World!';
|		}));
|
*/

Route::filter('before', function()
{
	// Do stuff before every request to your application...
});

Route::filter('after', function($response)
{
	// Do stuff after every request to your application...
});

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::to('login');
});

