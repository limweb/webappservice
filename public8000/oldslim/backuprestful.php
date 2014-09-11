<?php
require_once '../services/BackupSQL.php';
$app->get('/backup(/)', 'authenticate', function () use ($app) {
	try {
		$us = new BackupSQL();
		$rs = $us->fullbackup();
		if($rs){
			header('Content-Type: text/html; charset=utf-8');
			echo $rs ;
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