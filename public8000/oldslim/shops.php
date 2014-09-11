<?php

$app->get('/shop', 'authenticate', function () use ($app) {
	try {
		$book = R::findAll('shop');
		// 		var_dump($book);
		echo json_encode(R::exportAll($book));
	} catch (Exception $e) {
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}
});


$app->get('/shop/:id', 'authenticate', function ($id) use ($app) {
	try {
		$book = R::load('shop',$id);
		// 		var_dump($book);
		echo json_encode($book->export());
	} catch (Exception $e) {
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}
});