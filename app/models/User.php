<?php

class UserObserver {

	public function saving($model)   {
		echo 'saveing';
	}
	public function saved($model)   {
		echo 'saved';
	}

}

class User extends Eloquent {
	public $timestamps = false;

	public static function boot()   {
		parent::boot();

		//         static::created(function($post) {
		//             echo json_encode($post);
		//             echo 'Creating';
		//         });

	}



	public function address(){
		return $this->hasOne('Address');
	}

	public function groups(){
		return $this->belongsToMany('Group');
	}

	public function country(){
		return $this->belongsTo('Country');
	}

	public function posts(){
		return $this->hasMany('post');
	}


}
User::observe(new PostObserver);
// User::observe(new UserObserver);
