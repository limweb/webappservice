<?php
require __DIR__.'/../app/config/database.php'; 
// $user = User::find(1);
// var_dump($user->toArray()// $main = Maintoone::find(3);
// );
// $address =$user->address();
// var_dump($address->get()[0]->toArray());

// echo R::getVersion();
 
// $addr =  Address::find(2);
// $u = $addr->user()->get();
// var_dump($addr->toArray());
// var_dump($u->toArray());



// $users = DB::table('users')->get();
// foreach ($users as $user)
// {
//     var_dump($user);
// }

//Multi Database Used
// $users = DB::connection('test')->table('users')->get();
// $users = DB::connection('default')->table('users')->get();
// $users = DB::connection('sqlite')->table('users')->get();
// foreach ($users as $user)
// {
    // var_dump($user);
//     echo json_encode($user),'<br>';
// }

// $rs = User::on('sqlite')->get()->toArray();
// $rs = User::get()->toArray();
// var_dump($rs);


// $user = DB::table('users')->where('name', 'Bob')->first();
// var_dump($user['name']);

// $name = DB::table('users')->where('name', 'Bob')->pluck('name');
// echo $name;


// $users = DB::table('users')->lists('name');


// $users = DB::table('users')->lists('name','id');
// var_dump(json_encode($users));

// $users = DB::table('users')->select('name', 'id')->get();
// var_dump($users);

// $users = DB::table('users')->distinct()->get();
// var_dump($users);

// $users = DB::table('users')->select('name as user_name','id as idx')->get();
// var_dump($users);

// $query = DB::table('users')->select('name');
// $users = $query->addSelect('name','id as idx')->get();
// var_dump($users);


// $users = DB::table('users')->where('id', '>', 1)->get();
// var_dump($users);

// $users = DB::table('users')
//                     ->where('id', '>', 2)
//                     ->orWhere('name', 'Jack')
//                     ->get();
// var_dump($users);

// $users = DB::table('users')
//                     ->where('id', '>', 1)
//                     ->Where('name', 'Jack')
//                     ->get();
// var_dump($users);

// $users = DB::table('users')
//                     ->whereBetween('id', array(2, 3))->get();
// var_dump($users);

// $users = DB::table('users')
//                     ->whereNotBetween('id', array(2, 3))->get();
// var_dump($users);

// $users = DB::table('users')
//                     ->whereIn('id', array(1, 2, 3))->get();
// var_dump($users);

// $users = DB::table('users')
//                     ->whereNotIn('id', array( 2, 3))->get();
// var_dump($users);

// $users = DB::table('users')
//                     ->whereNull('name')->get();
// var_dump($users);

//can't test for this
// $users = DB::table('users')
//                     ->orderBy('name', 'desc')
//                     ->groupBy('count')
//                     ->having('count', '>', 100)
//                     ->get();
// var_dump($users);

// $users = DB::table('users')->skip(0)->take(3)->get();
// var_dump($users);


// $users = User::all();
// var_dump($users->toArray());
// $user = User::find(1);
// var_dump($user->name);


// $model = User::findOrFail(1);
// var_dump($model->toArray());

// $model = User::where('id', '>', 3)->firstOrFail();
// var_dump($model->toArray());

// $users = User::where('id', '>', 1)->take(2)->get();
// foreach ($users as $user)
// {
//     var_dump($user->name);
// }
 $rs = User::all()->toArray();
 $rs = User::find(2);
 $rs->address;
 $rs->groups;

 var_dump($rs->toArray());

// $user = User::find(1);
// $user->address = $user->address()->get()->toArray();
// var_dump($user->toArray());

// $user = User::find(1);
// $user->address = $user->address; //->toArray();
// var_dump($user->toArray());


// $address = User::find(1)->address;
// var_dump($address->toArray());

// $roles = User::find(1)->groups;
// var_dump($roles->toArray());

// foreach (User::with('groups')->get() as $book)
// {
//     var_dump($book->groups->toArray());
// }
// $queries = DB::getQueryLog();


// $country = Country::find(1)->toArray();
// var_dump($country);
//with solves the  n+1 issue (http://goo.gl/aQwpQf).
// $country = Country::find(1)->users;
// $country = Country::find(1)->posts;

// $country = Country::with(['users', 'users.posts'])->where('code', 'NZ')->get();
// var_dump($country->toArray());
// echo  $country;

// $photo = Photo::find(1)->owner;
// var_dump($photo->toJson());

// Lazy load
// $user = User::all();
// $user->load('groups', 'posts','country');
// echo $user;
// require_once './app/plugins/AMFUtil.php';
// if( class_exists('AMFUtil') ) {
//     echo 'ok';

// } else {
//     echo ' no ok';
// }
// echo R::getKey();
// echo "\r\n";
// echo AMFUtil::getkey();

// $u = new User();
// $u->name = 'thongchai';
// $u->country_id = 2;
// $u->save();
// $u = User::find(6);
// $u->name = 'thongchai'. time();
// $u->save();

// echo R::getversion();
// $queries = DB::getQueryLog();
// print_r($queries);
echo 'ok';
exit();
