<?php
// phpinfo();
require __DIR__.'/../RestServer.php';
require __DIR__.'/TestController.php';

// $server = new RestServer('debug');
// $server->addClass('TestController');
// $server->handle();

spl_autoload_register(); // don't load our classes unless we use them

$mode = 'production'; // 'debug' or 'production'
$server = new RestServer($mode);
// $server->refreshCache(); // uncomment momentarily to clear the cache if classes change in production mode

$server->addClass('TestController');
// $server->addClass('ProductsController', '/products'); // adds this as a base to all the URLs in this class

$server->handle();