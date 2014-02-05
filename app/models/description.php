<?php

class Description extends Eloquent {

	public function session()
	{
		return $this->belongsTo('Talk');
	}

}

?>