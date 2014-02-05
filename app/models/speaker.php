<?php

class Speaker extends Eloquent {

	public function talk()
	{
		return $this->belongsTo('Talk');
	}

	public function user()
	{
		return $this->belongsTo('User');
	}

}

?>

