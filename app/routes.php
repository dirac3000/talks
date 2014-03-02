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

// When the new post is submitted we handle that here
Route::post('talk_new', 
	array('before' => 'auth', 'uses' => 'TalkController@processNewTalk'));

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

// User Edit
Route::get('user/{id}', 'UserController@view');

// User Rigths Change
Route::get('user/{id}/rights={rights}', 'UserController@changeRights');


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

