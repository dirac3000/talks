<?php

class Comment extends Eloquent {

	public function session()
	{
		return $this->belongsTo('Talk');
	}

	public function user()
	{
		return $this->belongsTo('User');
	}

}

?>

