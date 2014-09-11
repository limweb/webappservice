<?php
require_once '../services/Pdoconfig.php';
include '../services/MenuService.php';
// require_once '../services/rb.php';
// require 'rb.php';
require_once 'Slim/Slim.php';
// require 'Slim/Middleware.php';
// require 'Slim/Middleware/HttpBasicAuth.php';
// require_once '../services/UsersService.php';

\Slim\Slim::registerAutoloader();
// R::setup('mysql:host='.Pdoconfig::$server.';dbname='.Pdoconfig::$databasename,Pdoconfig::$username,Pdoconfig::$password);

$app = new \Slim\Slim(array(
  'cookies.secret_key' => 'my_secret_key'
));


// $app = new \Slim\Slim();
// $app->add(new \HttpBasicAuth());

// set default conditions for route parameters
\Slim\Route::setDefaultConditions(array(
  'id' => '[0-9]{1,}',
));

class ResourceNotFoundException extends Exception {}

// route middleware for simple API authentication
function authenticate(\Slim\Route $route) {
    $app = \Slim\Slim::getInstance();
    $uid = $app->getEncryptedCookie('uid');
    $key = $app->getEncryptedCookie('key');
    if (validateUserKey($uid, $key) === false) {
      $app->halt(401);
    }
}

function validateUserKey($uid, $key) {
  // insert your (hopefully more complex) validation routine here
  if ($uid == 'demo' && $key == 'demo') {
    return true;
  } else {
//     return false;
    return true;
  }
  
}

// generates a temporary API key using cookies
// call this first to gain access to API methods
$app->get('/demo', function () use ($app) {    
  try {
    $app->setEncryptedCookie('uid', 'demo', '5 minutes');
    $app->setEncryptedCookie('key', 'demo', '5 minutes');
  } catch (Exception $e) {
    $app->response()->status(400);
    $app->response()->header('X-Status-Reason', $e->getMessage());
  }
});


$app->get('/logout',function() use ($app){
// 		var_dump( $HTTP_SERVER_VARS['PHP_AUTH_PW'] );
// 		$req = $app->request();
// 		var_dump($req);
		echo "logout Successed";
});

$app->get('/',function() use ($app){
// 		var_dump( $HTTP_SERVER_VARS['PHP_AUTH_PW'] );
// 		$req = $app->request();
// 		var_dump($req);
		$d = date('Y-m-d H:i:s');
		echo "Welcome " . $d;
});


$app->get('/menu',function() use ($app){
	$m = new MenuService();
	$rs = $m->getMenu(9,1);
	echo json_encode($rs);
});
//require
require_once 'PageController.php';
require_once 'userrestful.php';
require_once 'grouprestful.php';
require_once 'patientsrestful.php';
require_once 'istabrestful.php';
require_once 'backuprestful.php';
require_once 'containerrestful.php';
require_once 'foodsrestful.php';
require_once 'foodsgrouprestful.php';
require_once 'foodstylerestful.php';
// require 'samplecrud.php';
// require 'books.php';
// require 'shops.php';

// run, $key)
$app->run();
?>