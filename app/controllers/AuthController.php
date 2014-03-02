<?php
 
class AuthController extends BaseController {
 	/**
	 * Display the login page
	 * @return View
	 */
	public function getLogin()
	{
		return View::make('login');
	}
 
	/**
	 * Login action
	 * @return Redirect
	 */
	public function postLogin()
	{
		// TODO: Rewrite login to use LDAP!
		//var_dump(debug_backtrace());
		$userinfo = array(
			'username' => Input::get('username'),
			'password' => Input::get('password')
		);
		$attempt = Auth::attempt($userinfo);
		if ( $attempt )
		{
			return Redirect::to('/');
		}
		else
		{
			return Redirect::to('login')
				->with('login_errors', true);
		}
	}
 
	/**
	 * Logout action
	 * @return Redirect
	 */
	public function getLogout()
	{
		Auth::logout();
		return Redirect::to('/')
			->with('message', 
			Lang::get('messages.logoutMessage'));
	}

}


