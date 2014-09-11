<?php
require_once '../services/StylefoodService.php';

$app->get('/foodstyle(/)','authenticate',function() use ($app){ 
	try {
		$fts = new StylelfoodService();
		$rs = $fts->getAllStylelfood();
		if($rs){
			$app->response()->header('Content-Type', 'application/json');
			echo json_encode( $rs );
		} else {
			throw new ResourceNotFoundException();
		}
	} catch (ResourceNotFoundException $e) {
		$app->response()->status(404);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	} catch (Exception $e) {
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}
});

$app->get('/foodstyle/:id(/)','authenticate',function($id) use ($app){ 
	try {
		$fts = new StylelfoodService();
		$rs = $fts->getStylelfoodByID($id);
		if($rs[0]->id != 0){
			$app->response()->header('Content-Type', 'application/json');
			echo json_encode( $rs );
		} else {
			throw new ResourceNotFoundException();
		}
	} catch (ResourceNotFoundException $e) {
		$app->response()->status(404);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	} catch (Exception $e) {
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}
});

$app->put('/foodstyle/:id(/)','authenticate',function($id) use ($app){ 
	try {
		$fts = new StylelfoodService();
		$request = $app->request();
		$mediaType = $request->getMediaType();
		$body = $request->getBody();
		$input = json_decode($body);
	
		$foodts = new Stylelfood();
		$foodts->id = $id;
		$foodts->name = (string) $input->name;
		$foodts->status = (string) $input->status;

		$rs = $fts->updateStylelfood($foodts);
		if($rs){
			$app->response()->header('Content-Type', 'application/json');
			echo json_encode( $rs );
		} else {
			throw new ResourceNotFoundException();
		}
	} catch (ResourceNotFoundException $e) {
		$app->response()->status(404);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	} catch (Exception $e) {
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}
	
});

$app->delete('/foodstyle/:id(/)','authenticate',function($id) use ($app){ 
	try {
		$fts = new StylelfoodService();
		$rs = $fts->deleteStylelfood($id);
		if($rs[0]->id != 0){
			$app->response()->header('Content-Type', 'application/json');
			echo json_encode( $rs );
		} else {
			throw new ResourceNotFoundException();
		}
	} catch (ResourceNotFoundException $e) {
		$app->response()->status(404);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	} catch (Exception $e) {
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}
});

$app->post('/foodstyle(/)','authenticate',function() use ($app){ 
	try {
		$fts = new StylelfoodService();
		$request = $app->request();
		$mediaType = $request->getMediaType();
		$body = $request->getBody();
		$input = json_decode($body);
	
		$foodts = new Stylelfood();
// 		$foodts->id;
		$foodts->name = (string) $input->name;
		$foodts->status = (string) $input->status;
		
		$rs = $fts->createStylelfood($foodts);
	
		if($rs){
			$app->response()->header('Content-Type', 'application/json');
			echo json_encode( $rs );
		} else {
			throw new ResourceNotFoundException();
		}
	} catch (ResourceNotFoundException $e) {
		$app->response()->status(404);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	} catch (Exception $e) {
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}
});
