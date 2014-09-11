<?php 
session_start();
date_default_timezone_set('Asia/Bangkok');
$method = $_SERVER['REQUEST_METHOD'];
$request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));
$input = json_decode(file_get_contents("php://input"));
require_once 'incSession.php';
require_once '../services/UsersService.php';
$sv = new UsersService();
$user = NULL;

//--- test------start
// $method = 'PUT'; //POST PUT DELETE GET
// $input = '{"aaaa":"aaaa"}';
//--- test------end
$s = new Session();
$data = $s->read(session_id());

if(!empty($data->user_name)){
	$user= $data->user_name;
} else {
	if( $method == 'POST' && $request[0] =='login'){
		
	} else {
// 	$s->destroy(session_id());	
// 	http_response_code(401);
// 	echo "you have not permission to access";
// 	header("Location: /nutritionapps/");
// 	exit();
	}
}

if(!empty($input->userid)){
	$user= $input->userid;
} else {
	// http_response_code(401);
	// throw  new Exception('Error!!! You are not login');
	// echo "Error!!! You not login";
	// exit();
}

switch ($method) {
	case 'PUT':
		try {
			if($input) {
				if(!empty($request[0])){	
					$u = new Users();
		    			$u->
					$rs = $sv->updateUsers($u,$user);
					echo json_encode($rs);
				} else {
					throw new Exception('No Id for Update');
				}
			} else {
				throw new Exception('No data for Update');
			}
		} catch (Exception $e) {
			http_response_code(500);
			echo $e->getMessage();
			//throw new Exception($e->getMessage());
		}
		break;
	case 'POST':
		try {
			if($input){
				if(empty($resuest[0])) {
					$u = new Users();
					$u->
					$rs = $sv->createUsers($u,$user);
					echo json_encode($rs);
				// } else if($request[0] == ''){
					// $rs = $sv->function($u,$user);
					// echo json_encode($rs);
				}	
			} else {
				throw new Exception('No data for Insert');
			}
		} catch (Exception $e) {
			http_response_code(500);
			// 			throw new Exception($e->getMessage());
			echo $e->getMessage();
		}
		break;
	case 'GET':
		if(count($request) > 1) {
			// if($request[0] == ''){
				// if(!empty($request[1])){
				// 	$rs = $sv->function($request[1],$user);
				// 	echo json_encode($rs);
				// } else {
				// 	throw new Exception('Error Message');
				// }	
			// } else if($request[0] == ''){
			// 	$rs = $sv->function($request[1],$user);
			// 	echo json_encode($rs);
			// } else {

			// }
		} else {
			if(empty($request[0])){
				echo json_encode($sv->getAllUsers($user));
			} else {
				echo json_encode($sv->getUsersByID($request[0],$user));
			}
		}
		break;
	case 'DELETE':
		try {
			if(!empty($request[0])) {
				$rs = $sv->deleteUsers($request[0],$user);
				echo json_encode($rs);
			} else {
				throw new Exception('No Id for Delete');
			}
		} catch (Exception $e) {
			http_response_code(500);
			echo $e->getMessage();
			//throw new Exception($e->getMessage());
		}	
		break;
	default:
		break;
}