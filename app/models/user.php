<?php

use Illuminate\Auth\UserInterface;
//use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface {
        /**
         * The database table used by the model.
         *
         * @var string
         */
        protected $table = 'users';

        /**
         * The attributes excluded from the model's JSON form.
         *
         * @var array
         */
        protected $hidden = array('password');

	/**
	 * We won't actually delete users
	 */
	protected $softDelete = true;

        /**
         * Get the unique identifier for the user.
         *
         * @return mixed
         */
        public function getAuthIdentifier()
        {
                return $this->getKey();
        }

        /**
         * Get the password for the user.
         *
         * @return string
         */
        public function getAuthPassword()
        {
                return $this->password;
        }

	public function getRememberToken()
	{
	}

	public function setRememberToken($value)
	{
	}

	public function getRememberTokenName()
	{
	}

	public function manager()
	{
		return $this->hasOne('User', 'id', 'manager_id');
	}

	public function workers()
	{
		return $this->hasMany('User', 'manager_id');
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

	public function talks()
	{
		return $this->hasMany('Talk');
	}

	public function speakers()
	{
		return $this->hasMany('Speaker');
	}
}
?>
