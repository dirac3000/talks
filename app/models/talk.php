<?php

class Talk extends Eloquent {

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

	public function creator()
	{
		return $this->belongsTo('User', 'creator_id');
	}

}

?>
