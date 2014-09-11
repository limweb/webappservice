<?php
// handle GET requests for /articles

$app->get('/mybook(/)', 'authenticate', 'testbook' );

// $app->get('/book', 'authenticate', function () use ($app) {
// 	try {
// 		$book = R::findAll('book');
// // 		var_dump($book);
// 		echo json_encode( R::exportAll( $book ) );
// 	} catch (Exception $e) {
// 		$app->response()->status(400);
// 		$app->response()->header('X-Status-Reason', $e->getMessage());
// 	}
// });

$app->get('/mybook/:id(/)', 'authenticate', function ($id) use ($app) {
	try {
		$book = R::load('book',$id);
		// 		var_dump($book);
		echo json_encode($book->export());
	} catch (Exception $e) {
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}
});


function testbook() {
	global  $app;
	try {
		$book = R::findAll('book');
// 				var_dump($book);
				echo json_encode( R::exportAll( $book ) );
	} catch (Exception $e) {
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}
}