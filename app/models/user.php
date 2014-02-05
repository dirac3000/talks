<?php

class User extends Eloquent {

	public function manager()
	{
		return $this->hasOne('User', 'manager', 'id');
	}

	public function workers()
	{
		return $this->hasMany('User', 'manager');
	}

	public function attachments()
	{
		return $this->hasMany('Attachment');
	}

	public function comments()
	{
		return $this->hasMany('Comment');
	}

	public function reservations()
	{
		return $this->hasMany('Reservation');
	}

	public function sessions()
	{
		return $this->hasMany('Sessions');
	}

	public function speakers()
	{
		return $this->hasMany('Speaker');
	}
}
?>