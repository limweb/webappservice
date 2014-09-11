<?php
include_once '../services/IstabService.php';

$app->get( '/istab(/)','authenticate', function () use ($app) {
	try {
		$is = new IstabService();
		$rs = $is->getAllIstab();
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