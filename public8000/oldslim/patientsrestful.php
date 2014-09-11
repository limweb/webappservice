<?php
require_once '../services/PatientsService.php';

$app->get('/patients','authenticate',function() use ($app) {
	try {
		$ps = new PatientsService();
		$rs = $ps->getAllPatients();
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

$app->get('/patients/:id(/)','authenticate',function($id) use ($app) {
	try {
		$ps = new PatientsService();
		$rs = $ps->getPatientsByID($id);
		if($rs->id != 0 ){
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

$app->post('/patients(/)','authenticate',function() use ($app) {
	try {
		$ps = new PatientsService();
		$request = $app->request();
		$mediaType = $request->getMediaType();
		$body = $request->getBody();
		$input = json_decode($body);
		
		$p = new Patients();
		$p->puid = (string)  $input->puid;
		$p->pid =  (string) $input->pid;
		$p->pictures =  (string) $input->pictures;
		$p->group_id = (string)  $input->group_id;
		$p->p_name = (string)  $input->p_name;
		$p->p_surname = (string)  $input->p_surname;
		$p->p_email = (string)  $input->p_email;
		$p->p_addr1 = (string)  $input->p_addr1;
		$p->p_addr2 =  (string) $input->p_addr2;
		$p->p_tel = (string) $input->p_tel;
		$p->p_blood =  (string) $input->p_blood;
		$p->p_gender =  (string) $input->p_gender;
		$p->p_birth =  (string) $input->p_birth;
		$p->status = (string)  $input->status;
		$p->p_contact =  (string) $input->p_contact;
		$p->note =  (string) $input->note;
		$p->create_by = $input->create_by;
		$p->create_date = date('Y-m-d H:i:s');
		$p->modify_by = (string)  $input->modify_by;
		$p->modify_date =  date('Y-m-d H:i:s');
		
		$rs = $ps->createPatients($p);
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

$app->delete('/patients/:id(/)','authenticate',function($id) use ($app) {
	try {
		$ps = new PatientsService();
		$rs = $ps->deletePatients($id);
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

$app->put('/patients/:id(/)','authenticate',function($id) use ($app) {
	try {
		$ps = new PatientsService();
		$request = $app->request();
		$mediaType = $request->getMediaType();
		$body = $request->getBody();
		$input = json_decode($body);
		$p = new Patients();
		$p->id = $id;
		$p->puid = (string)  $input->puid;
		$p->pid =  (string) $input->pid;
		$p->pictures =  (string) $input->pictures;
		$p->group_id = (string)  $input->group_id;
		$p->p_name = (string)  $input->p_name;
		$p->p_surname = (string)  $input->p_surname;
		$p->p_email = (string)  $input->p_email;
		$p->p_addr1 = (string)  $input->p_addr1;
		$p->p_addr2 =  (string) $input->p_addr2;
		$p->p_tel = (string) $input->p_tel;
		$p->p_blood =  (string) $input->p_blood;
		$p->p_gender =  (string) $input->p_gender;
		$p->p_birth =  (string) $input->p_birth;
		$p->status = (string)  $input->status;
		$p->p_contact =  (string) $input->p_contact;
		$p->note =  (string) $input->note;
		// 			$p->create_by = $input->create_by;
		// 			$p->create_date = $input->create_date;
		$p->modify_by = (string)  $input->modify_by;
		$p->modify_date =  date('Y-m-d H:i:s');
		$rs = $ps->updatePatients($p);
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


$app->get('/patientsbygroup/:id(/)','authenticate',function($id) use ($app) {
	try {
		$ps = new PatientsService();
		$rs = $ps->getPatientsBygroup($id);
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


$app->get('/foodslistbyhistory/:id(/)','authenticate',function($id) use ($app) {
	try {
		$ps = new PatientsService();
		$rs = $ps->getFoodslistbyhistory($id);
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


$app->get('/patienthistorybyid/:id(/)','authenticate',function($id) use ($app) {
	try {
		$ps = new PatientsService();
		$rs = $ps->getPatientHistorybyid($id);
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

// {
// 	"gender":"",
// 	"name":"",
// 	"puid":"",
// 	"usergroup":"1",  //require
// 	"username":"e" //require
// }
$app->post('/searchpatients(/)','authenticate',function() use ($app){
	try {
		$ps = new PatientsService();
		
		$request = $app->request();
		$mediaType = $request->getMediaType();
		$body = $request->getBody();
		$input = json_decode($body);
// 		var_dump($input);
		$search = new searchModel();
		$search->gender = (string) $input->gender;
		$search->name  = (string) $input->name;
		$search->puid = (string) $input->puid;
		$search->usergroup = (int) $input->usergroup;
		$search->username = (int) $input->username;
		$rs = $ps->searchPatients($search);
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
	

