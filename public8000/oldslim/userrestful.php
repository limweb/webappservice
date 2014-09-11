<?php
require_once '../services/UsersService.php';

$app->post('/login(/)', 'authenticate', function () use ($app) {
	try {
		$us = new UsersService();
		$request = $app->request();
		$mediaType = $request->getMediaType();
		$body = $request->getBody();
		$input = json_decode($body);
		$u =  (string)$input->user;
		$p = (string)$input->pass;
		$user = $us->login($u,$p);
		if($user){
			$app->response()->header('Content-Type', 'application/json');
			echo json_encode( $user );
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


$app->get('/login(/)', 'authenticate',function () use ($app) {
	try {
		$us = new UsersService();
		$user = $us->getAllUsers();
		if($user){
			$app->response()->header('Content-Type', 'application/json');
			echo json_encode($user);
		} else {
			throw new ResourceNotFoundException();
		}
	} catch (ResourceNotFoundException $e) {
		$app->response()->status(401);
	} catch (Exception $e) {
		$app->response()->status(404);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}
});

$app->get('/login/:id(/)','authenticate','getuserbyid');

function getuserbyid($id) {
	global  $app;
	try {
		$us = new UsersService();
		$user = $us->getUsersByID($id);
		if($user){
			$app->response()->header('Content-Type', 'application/json');
			echo json_encode($user);
		} else {
			throw new ResourceNotFoundException();
		}
	} catch (ResourceNotFoundException $e) {
		$app->response()->status(404);
		$app->response()->header('X-Status-Reason',$e->getMessage());
	} catch (ResourceNotFoundException $e) {
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}
}

$app->post('/changepass(/)','authenticate', function() use ($app){

	try {
		$us = new UsersService();
		$request = $app->request();
		$mediaType = $request->getMediaType();
		$body = $request->getBody();
		$input = json_decode($body);
		$u =  (string) $input->user;
		$p = (string)$input->pass;
		$np = (string)$input->newpass;
		$user = $us->changepass($u,$p,$np);
		if($user){
			$app->response()->header('Content-Type', 'application/json');
			echo json_encode( $user );
		} else {
			throw new ResourceNotFoundException();
		}
	} catch (ResourceNotFoundException $e) {
		$app->response()->status(404);
		$app->response()->header('X-Status-Reason',$e->getMessage());
	} catch (ResourceNotFoundException $e) {
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}
});


$app->post('/changepuserid(/)','authenticate',function() use ($app) {
	try {
		$us = new UsersService();
		$request = $app->request();
		$mediaType = $request->getMediaType();
		$body = $request->getBody();
		$input = json_decode($body);
		$u =  (string) $input->user;
		$p =  (string) $input->pass;
		$nu = (string) $input->newuser;
		$user = $us->changeuserid($u, $p, $nu);
		if($user){
			$app->response()->header('Content-Type', 'application/json');
			echo json_encode( $user );
		} else {
			throw new ResourceNotFoundException();
		}
	} catch (ResourceNotFoundException $e) {
		$app->response()->status(404);
		$app->response()->header('X-Status-Reason',$e->getMessage());
	} catch (ResourceNotFoundException $e) {
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}
});

$app->post('/searchUser(/)', 'authenticate', function () use ($app) {
	try {
			
		$us = new UsersService();
		$request = $app->request();
		$mediaType = $request->getMediaType();
		$body = $request->getBody();
		$input = json_decode($body);
		if($input){
			$su =Pdoconfig::ConvertoObj($input, 'searchUser');
		} else {
			$su = new searchUser();
		}

		$user = $us->searchUser($su);
		if($user){
			echo json_encode($user);
		} else {
			throw new ResourceNotFoundException();
		}

	} catch (ResourceNotFoundException $e) {
		$app->response()->status(404);
	} catch (ResourceNotFoundException $e) {
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}
});


$app->post('/addmember(/)','authenticate',function() use ($app){
	try {
		$us = new UsersService();
		$request = $app->request();
		$mediaType = $request->getMediaType();
		$body = $request->getBody();
		$input = json_decode($body);
		$u =  (string) $input->userid;
		$id= $us->createUsers($input,$u);
		if($id){
			echo json_encode($id);
		} else {
			throw new ResourceNotFoundException();
		}

	} catch (ResourceNotFoundException $e) {
		$app->response()->status(404);
	} catch (ResourceNotFoundException $e) {
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}

});


$app->post('/editmember/:id(/)','authenticate',function($id) use ($app){
	try {
		$us = new UsersService();
		$request = $app->request();
		$mediaType = $request->getMediaType();
		$body = $request->getBody();
		$input = json_decode($body);
		$u =  (string) $input->userid;
		$id= $us->updateUsers($input,$u);
		echo json_encode($id);
	} catch (Exception $e) {
		throw new Exception($e->getMessage());
	}
});

$app->delete('/delmember/:id(/)','authenticate',function($id) use ($app){
	try {
		$us = new UsersService();
		$request = $app->request();
		$mediaType = $request->getMediaType();
		$body = $request->getBody();
		$input = json_decode($body,TRUE);
		$u =  (string) $input['userid'];
		$id = $us->deleteUsers($id,$u);
		echo json_encode($id);
	} catch (Exception $e) {
		throw new Exception($e->getMessage());
		
	}
});

// $app->get('/login/:id', 'authenticate', function ($id) use ($app) {
// 	try {
// 			$us = new UsersService();
// 			$user = $us->getUsersByID($id);
// 		if($user){
// 			echo json_encode($user);
// 		} else {
// 			throw new ResourceNotFoundException();
// 		}
// 	} catch (ResourceNotFoundException $e) {
// 		$app->response()->status(404);
// 	} catch (ResourceNotFoundException $e) {
// 		$app->response()->status(400);
// 		$app->response()->header('X-Status-Reason', $e->getMessage());
// 	}
// });


