<?php
require_once 'database.php';

class Post extends Eloquent
{
	protected $table = 'posts';

	/**
	 * You can define your own custom boot method.
	 *
	 * @return void
	 **/
	public static function boot()
	{
		parent::boot();

		static::creating(function($post) {
			echo 'Creating';
			error_log('Creating');
		});
		
	
		static::updating(function($post)
		{
			error_log('updateing');
			echo 'updateing';
		});
			
	}

	/**
	 * You can access the database connection in a static model method with the resolver.
	 *
	 * @return void
	 **/
	static public function doSomething()
	{
		$db = static::resolveConnection();
	}
}

// use Illuminate\Database\Capsule\Manager as Capsule;

// class  Test   extends  Eloquent {
// 	public $timestamps = false;
// 	public $table = 'product';
// 	public $primaryKey = 'productId';
// }

// Test::observe($observer);
// $max = Test::max('productId');
// $min = Test::min('productId');
// $count = Test::count();
// $users = Eloquent::query('select * from users');

// $users = DB::table('users')->get();

Post::observe($observer);
// Post::forget('saved');
// Post::subscribe(new PostEventSubscriber($dependency));
// Create / Update / Delete a Post model.
$post = new Post;
$post->title = 'Hello, World!';
$post->save();
$post->delete();

// Get the query log.
$queries = $capsule->connection()->getQueryLog();

print_r($queries);