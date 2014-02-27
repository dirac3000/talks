<?php

class BaseController extends Controller {

	/**
	 * Goes to home page with an unauthorized message
	 * @return Redirect
	 */
	protected function unauthorized()
	{
		Session::flash('error', 'unauthorized');
		return Redirect::to('/');
	}

	/**
	 * Check if current user is an administrator
	 * @return Boolean
	 */
	public function loggedAdmin()
	{
		if (Auth::guest())
			return false;
		return (Auth::user()->rights == "admin");
	} 


	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		View::share('admin_view', $this->loggedAdmin());
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

}
