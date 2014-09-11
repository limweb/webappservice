<?php 
require_once 'database.php'; 

// for ($i=0; $i < 100000 ; $i++) { 
//   $autocomplete = R::dispense('autocomplete');
//   $autocomplete->name = $faker->name;
//   $autocomplete->address =  $faker->address;
//   $autocomplete->status = $faker->numberBetween($min = 0, $max = 1);
//   $autocomplete->modify_by = 'system';
//   $autocomplete->create_by = 'system';
//   $autocomplete->modify_date = R::isoDateTime();
//   $autocomplete->create_date = R::isoDateTime();
//   $id = R::store($autocomplete);
// }

// exit();

// $b = R::load('main',1);
// $b->ownMaintoone;
// $b->ownMaintomany;

// var_dump($b->export());
// exit();

class  Main   extends    Eloquent {

    protected     $table = 'main';
    protected     $primaryKey   = 'id';
    protected     $fillable = array('name', 'lastname');
    
    public function maintoone()
    {
      $this->Maintoone =  $this->hasOne('Maintoone','main_id')->first()->toArray();
      return  $this;
    }
    
    public function maintomany()
    {
    	$this->Maintomany = $this->hasMany('Maintomany','main_id')->get()->toArray();
      return $this;
    }
        
}

class Maintoone extends Eloquent  {
     protected    $table = 'maintoone';
	protected    $primaryKey   = 'main_id';
	
	public function main()
	{
		return $this->belongsTo('Main');
	}
	
}


class Maintomany extends Eloquent {
	protected  $table = 'maintomany';
	protected  $primaryKey = 'id';
	
}

// // find
// $main = Maintoone::find(3);
// echo $main->idcard;
// exit();

// //get all
// $mains = Main::get();
// $mains = Main::all();
// echo $mains[1]->name;
// $maintoones = Maintoone::get();
// echo $maintoones[1]->idcard;
// exit();


// //where
// $name = 'ccccc';
// $main = Main::where('name', '=', $name)->first();
// $main = Main::where('name', '=', $name)->first();
// $main = Main::where_name($name)->first();
// $mains = Main::whereIn('id', array(1, 2, 3))->orWhere('name', '=', $name)->get()->toArray();
// $mains = Main::orderBy('name', 'desc')->take(10)->get()->toArray();
// echo $main->name;
// var_dump($mains);
// exit();

// $min = Main::min('id');
// echo $min,"\n\r";
// $max = Main::max('id');
// echo $max,"\n\r";
// $avg = Main::avg('id');
// echo $avg,"\n\r";
// $sum = Main::sum('id');
// echo $sum,"\n\r";
// $count = Main::count();
// echo $count,"\n\r";
// $count = Main::where('id', '>', 10)->count();
// echo $count,"\n\r";
//exit();


// //new or Create
// $main = new Main;
// $main->name = 'ffffffffx';
// $main->lastname = 'lfffffx';
// $main->save();
// $main = Main::create(array('name' => 'ggggy','lastname'=> 'lgggggy'));
// exit(); 

// // update timestamp
// $main = Main::find(1);
// $main->touch();
// $main = Main::find(1);
// $main->timestamps = true;
// echo $main->freshTimestamp();
// $main->touch();
// //do something else here, but not modifying the $comment model data
// $main->save();
// exit();



// // 1:1
// $onetoone = Main::find(1)->maintoone();
// var_dump($onetoone->toArray());
// exit();
// echo Maintoone::find(2)->main()->first()->name;
// echo Maintoone::find(2)->main->name;
// $main = Main::find(1)->maintoone();//->toArray();
// var_dump($main->toArray());
// echo 'idcard = ',$main->Maintoone->idcard;
// echo 'idcard = ',$main->Maintoone['idcard'];
// exit();

//1:N
// $main = Main::find(1)->toArray();
// $main = Main::find(1)->maintomany();
// var_dump($main->toArray());
// exit();



