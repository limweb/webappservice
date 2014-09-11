<?php
require __DIR__ . '/../../vendor/autoload.php';
class_exists ( 'AMFUtil' );
ini_set ( "display_errors", 1 );
date_default_timezone_set ( 'Asia/Bangkok' );
// require_once 'rb.phar';
// require_once 'faker.phar';
// namespace Faker\Provider;
// for Sqlite db
// R::setup('sqlite:dbfile.db');
// R::nuke();
class DB extends Illuminate\Database\Capsule\Manager {
}
; // DB::table('users') = DB::table('users')
class Eloquent extends Illuminate\Database\Eloquent\Model {
}
;

$faker = Faker\Factory::create ();
class PostObserver {
	public function creating($post) {
		echo 'Creating';
		echo json_encode ( $post );
	}
	public function created($post) {
		echo 'Created';
		echo json_encode ( $post );
	}
	public function updating($post) {
		echo 'Updating';
		echo json_encode ( $post );
	}
	public function updated($post) {
		echo 'Updated';
		echo json_encode ( $post );
	}
	public function deleting($post) {
		echo 'Deleting';
		echo json_encode ( $post );
	}
	public function deleted($post) {
		echo 'Deleted';
		echo json_encode ( $post );
	}
	public function saving($post) {
		echo 'Saving';
		echo json_encode ( $post );
	}
	public function saved($post) {
		echo 'Saved';
		echo json_encode ( $post );
	}
}

$capsule = new DB ();

// $capsule->addConnection(array(
// 'driver' => 'mysql',
// 'host' => 'localhost',
// 'database' => 'test',
// 'username' => 'root',
// 'password' => '',
// 'charset' => 'utf8',
// 'collation' => 'utf8_unicode_ci',
// 'prefix' => ''
// ));

// $connection = array(

// $default => 'lv4',

// 'connections' => array(

// # Our primary database connection
// 'lv4' => array(
// 'driver' => 'mysql',
// 'host' => 'localhost',
// 'database' => 'lv4',
// 'username' => 'root',
// 'password' => '',
// 'charset' => 'utf8',
// 'collation' => 'utf8_unicode_ci',
// 'prefix' => ''
// ),

// # Our secondary database connection
// 'test' => array(
// 'driver' => 'mysql',
// 'host' => 'localhost',
// 'database' => 'test',
// 'username' => 'root',
// 'password' => '',
// 'charset' => 'utf8',
// 'collation' => 'utf8_unicode_ci',
// 'prefix' => ''
// ),

// 'sqlite' => array(
// 'driver' => 'sqlite',
// 'database' => __DIR__.'/../db/dbfile.db',
// 'prefix' => ''
// ),
// ),
// );

$onnections = array (
		// Our primary database connection
		'lv4' => array (
				'driver' => 'mysql',
				'host' => 'localhost',
				'database' => 'lv4',
				'username' => 'root',
				'password' => '',
				'charset' => 'utf8',
				'collation' => 'utf8_unicode_ci',
				'prefix' => '' 
		),
		
		// Our secondary database connection
		'test' => array (
				'driver' => 'mysql',
				'host' => 'localhost',
				'database' => 'test',
				'username' => 'root',
				'password' => '',
				'charset' => 'utf8',
				'collation' => 'utf8_unicode_ci',
				'prefix' => '' 
		),
		
		'sqlite' => array (
				'driver' => 'sqlite',
				'database' => __DIR__ . '/../db/dbfile.db',
				'prefix' => '' 
		), 
);

$capsule->addConnection ($onnections['lv4'],'default');
$capsule->addConnection ($onnections['test'],'test');
$capsule->addConnection ($onnections['sqlite'],'sqlite');


use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
$capsule->setEventDispatcher ( new Dispatcher ( new Container () ) );
$capsule->setAsGlobal ();
$capsule->bootEloquent ();

$observer = new PostObserver ();
$capsule->getContainer ()->instance ( 'PostObserver', $observer );
$pdo = $capsule->getConnection ()->getPdo ();
R::setup ( $pdo );
// $dsn = 'mysql:host='.$host.';dbname='.$db;
// R::setup($dsn,$user,$pass);

