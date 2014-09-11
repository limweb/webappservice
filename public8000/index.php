<?php
require_once  __DIR__.'/../app/config/database.php'; 
// Create Slim app
$app = new \Slim\Slim();

$app->get('/foo', function () {
    // Fetch all books
    $users = \User::all();
    echo $users->toJson();

    // Or create a new book
    // $book = new \Book(array(
    //     'title' => 'Sahara',
    //     'author' => 'Clive Cussler'
    // ));
    // $book->save();
    // echo $book->toJson();
    exit();
});

$app->get('/userall',function(){
        $sv = new UserService();
        $rs = $sv->getAlluser('system');
        // var_dump($rs);
        echo json_encode($rs);
});

$app->run();