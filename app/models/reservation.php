<?php

class Reservation extends Eloquent {

	public function session()
	{
		return $this->belongsTo('Session');
	}

	public function user()
	{
		return $this->belongsTo('User');
	}

}

?>

