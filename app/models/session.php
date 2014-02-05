<?php

class Session extends Eloquent {

	public function descriptions()
	{
		return $this->hasMany('Description');
	}

	public function reservations()
	{
		return $this->hasMany('Reservation');
	}

	public function speakers()
	{
		return $this->hasMany('Speaker');
	}

	public function commentss()
	{
		return $this->hasMany('Comment');
	}

	public function attachments()
	{
		return $this->hasMany('attachments');
	}

	public function manager()
	{
		return $this->belongsTo('User','manager');
	}

}

?>
