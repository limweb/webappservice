<?php
include_once '../services/ContainerService.php';

//edit by id
$app->put('/container/:id(/)','authenticate',function($id) use ($app){ 
	try {
		$cs = new ContainerService();
		$request = $app->request();
		$mediaType = $request->getMediaType();
		$body = $request->getBody();
		$input = json_decode($body);
		
		$contain = new Container();
		$contain->id = $id;
		$contain->name = (string) $input->name;
		$contain->type = (int) $input->type;
		$contain->unit = (string) $input->unit;
		$contain->barcode = (string) $input->barcode;
		$contain->val = $input->val;
		$contain->picarray =(string) $input->picarray;
		$rs = $cs->updateContainer($contain);
		
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



//new
$app->post('/container','authenticate',function () use ($app){
	try {
		$request = $app->request();
		$mediaType = $request->getMediaType();
		$body = $request->getBody();
		$input = json_decode($body);
				
		$contain = new Container();
		$contain->name = (string) $input->name;
		$contain->type = (int) $input->type;
		$contain->unit = (string) $input->unit;
		$contain->barcode = (string) $input->barcode;
		$contain->val = (float) $input->val;
		$contain->picarray = (string) $input->picarray;
		
		$cs = new ContainerService();
		$rs = $cs->createContainer($contain);
		
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