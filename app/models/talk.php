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

	public function comments()
	{
		return $this->hasMany('Comment');
	}

	public function attachments()
	{
		return $this->hasMany('Attachment');
	}

	public function creator()
	{
		return $this->belongsTo('User', 'creator_id');
	}

	public function future()
	{
		return (new DateTime($this->date_start)) 
			> (new DateTime("today"));
	}
}

?>
