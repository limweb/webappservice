<?php 

class Address extends Eloquent {  
    public function user(){
        return $this->belongsTo('User');
    }
}


class Post extends Eloquent {  

    public function comments(){
        return $this->hasMany('Comment');
    }

    public function user(){
        return $this->belongsTo('User');
    }
}

class Comment extends Eloquent {  
    public function post(){
        return $this->belongsTo('Post');
    }
}


class Group extends Eloquent {  
    public function users(){
        return $this->belongsToMany('User');
    }
}


class Country extends Eloquent {  

    public function users(){
        return $this->hasMany('User');
    }

    public function posts(){
        return $this->hasManyThrough('Post', 'User');
    }
}

class Photo extends Eloquent {

    public function owner(){
        return $this->morphTo('owner');
    }

    public function tags(){
        return $this->morphToMany('Tag', 'taggable');
    }

}


class Game extends Eloquent {  
    public function photos(){
        return $this->morphMany('Photo', 'owner');
    }
}

class Tag extends Eloquent {  
    public function posts(){
        return $this->morphedByMany('Post', 'taggable');
    }

    public function photos(){
        return $this->morphedByMany('Photo', 'taggable');
    }

}