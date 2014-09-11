<?php
require_once '../services/FoodgroupsService.php';
//foodsgrouprestful.php
$app->get('/foodgroups(/)','authenticate',function() use ($app){
	try {
		$fgs = new FoodgroupsService();
		$rs = $fgs->getAllFood_groups();
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

$app->get('/foodgroups/:id(/)','authenticate',function($id) use ($app){ 
	try {
		$fgs = new FoodgroupsService();
		$rs = $fgs->getFood_groupsByID($id);
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
$app->put('/foodgroups/:id(/)','authenticate',function($id) use ($app){
	try {
		$fgs = new FoodsService();
		$request = $app->request();
		$mediaType = $request->getMediaType();
		$body = $request->getBody();
		$input = json_decode($body);
	
		$foodgs = new Foodgroups();
		$foodgs->id = $id;
		$foodgs->fg_name = (string) $input->fg_name;
		$foodgs->create_by = (string) $input->create_by;
		$foodgs->create_date = (string) $input->create_date;
		$foodgs->modify_by = (string) $input->modify_by;
		$foodgs->modify_date = (string) $input->modify_date;
	
		$rs = $fgs->updateFoods($foodgs);
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
$app->delete('/foodgroups/:id(/)','authenticate',function($id) use ($app){
	try {
		$fgs = new FoodgroupsService();
		$rs = $fgs->deleteFood_groups($id);
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
$app->post('/foodgroups(/)','authenticate',function() use ($app){ 
	try {
		$fgs = new FoodsService();
		$request = $app->request();
		$mediaType = $request->getMediaType();
		$body = $request->getBody();
		$input = json_decode($body);
	
		$foodgs = new Foodgroups();
		$foodgs->id = null;
		$foodgs->fg_name = (string) $input->fg_name;
		$foodgs->create_by = (string) $input->create_by;
		$foodgs->create_date = (string) $input->create_date;
		$foodgs->modify_by = (string) $input->modify_by;
		$foodgs->modify_date = (string) $input->modify_date;
		$rs = $fgs->createFoods($foodgs);
		
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
