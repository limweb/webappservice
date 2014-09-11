<?php 
require_once '../services/FoodsService.php';



// $app->get('','authenticate',function() use ($app){ });

$app->get('/foods-scan/:id(/)','authenticate',function($id) use ($app){ 
	try {
		$fs = new FoodsService();
		$rs = $fs->getFoodsByBarcode($id);
		if($rs){
			$app->response()->header('Content-Type', 'application/json');
			echo json_encode( $rs );
		} else {
			$app->response()->header('Content-Type', 'application/json');
			echo json_encode( $rs );
		}
	} catch (Exception $e) {
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}
});

//create foods 
$app->post('/foods(/)','authenticate',function() use ($app){ 
	try {
		$request = $app->request();
		$mediaType = $request->getMediaType();
		$body = $request->getBody();
		$input = json_decode($body);
		
		$foods = new Foods();
		$foods->food_groups_id = (int) $input->food_groups_id;
		$foods->fname = (string) $input->fname;
		$foods->calories = (float) $input->calories;
		$foods->weight = (float) $input->weight;
		$foods->picture = (string) $input->picture;
		$foods->barcode = (string) $input->barcode;
		$foods->status = (int) $input->status;
		$foods->foodtype = (int) $input->foodtype;
		$foods->note = (string) $input->note;
		$foods->favorite = (string) $input->favorite;
		$foods->Carbohydrate = (float) $input->Carbohydrate;
		$foods->Protein = (float) $input->Protein;
		$foods->Fat = (float) $input->Fat;
		$foods->Na = (float) $input->Na;
		$foods->Fiber = (float) $input->Fiber;
		$foods->Sugar = (float) $input->Sugar;
		$foods->Cholesterol = (float) $input->Cholesterol;
		$foods->SatFat = (float) $input->SatFat;
		$foods->K = (float) $input->K;
		$foods->P = (float) $input->P;
		$foods->Ca = (float) $input->Ca;
		$foods->Fe = (float) $input->Fe;
		$foods->Zn = (float) $input->Zn;
		$foods->Mg = (float) $input->Mg;
		$foods->VITB1 = (float) $input->VITB1;
		$foods->VITB2 = (float) $input->VITB2;
		$foods->VITC = (float) $input->VITC;
		$foods->Niacin = (float) $input->Niacin;
		$foods->VITB6 = (float) $input->VITB6;
		$foods->VITB12 = (float) $input->VITB12;
		$foods->VITA = (float) $input->VITA;
		$foods->VITD = (float) $input->VITD;
		$foods->VITE = (float) $input->VITE;
		$foods->VITK = (float) $input->GRice;
		$foods->GRice = (float) $input->GRice;
		$foods->GVegetable = (string) $input->GVegetable;
		$foods->GFruit = (string) $input->GFruit;
		$foods->GMeat = (string) $input->GMeat;
		$foods->GFat = (string) $input->GFat;
		$foods->GMilk = (string) $input->GMilk;
		$foods->create_by = (string) $input->create_by;
		$foods->create_date = (string) $input->create_date;
		$foods->modify_by = (string) $input->modify_by;
		$foods->modify_date = (string) $input->modify_date;
		$fs = new FoodsService();
		$rs = $fs->createFoods($foods);

// 		$foodstyle = (array) $input->foodstyle;
// 		$fs->updateFoodstyle($rs,$foodstyle);

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

//update food by id
$app->put('/foods/:id(/)','authenticate',function($id) use ($app){

	try {
		$fs = new FoodsService();
		$request = $app->request();
		$mediaType = $request->getMediaType();
		$body = $request->getBody();
		$input = json_decode($body);
	
		$foods = new Foods();
		$foods->id = $id;
		$foods->food_groups_id = (int) $input->food_groups_id;
		$foods->fname = (string) $input->fname;
		$foods->calories = (float) $input->calories;
		$foods->weight = (float) $input->weight;
		$foods->picture = (string) $input->picture;
		$foods->barcode = (string) $input->barcode;
		$foods->status = (int) $input->status;
		$foods->foodtype = (int) $input->foodtype;
		$foods->note = (string) $input->note;
		$foods->favorite = (string) $input->favorite;
		$foods->Carbohydrate = (float) $input->Carbohydrate;
		$foods->Protein = (float) $input->Protein;
		$foods->Fat = (float) $input->Fat;
		$foods->Na = (float) $input->Na;
		$foods->Fiber = (float) $input->Fiber;
		$foods->Sugar = (float) $input->Sugar;
		$foods->Cholesterol = (float) $input->Cholesterol;
		$foods->SatFat = (float) $input->SatFat;
		$foods->K = (float) $input->K;
		$foods->P = (float) $input->P;
		$foods->Ca = (float) $input->Ca;
		$foods->Fe = (float) $input->Fe;
		$foods->Zn = (float) $input->Zn;
		$foods->Mg = (float) $input->Mg;
		$foods->VITB1 = (float) $input->VITB1;
		$foods->VITB2 = (float) $input->VITB2;
		$foods->VITC = (float) $input->VITC;
		$foods->Niacin = (float) $input->Niacin;
		$foods->VITB6 = (float) $input->VITB6;
		$foods->VITB12 = (float) $input->VITB12;
		$foods->VITA = (float) $input->VITA;
		$foods->VITD = (float) $input->VITD;
		$foods->VITE = (float) $input->VITE;
		$foods->VITK = (float) $input->GRice;
		$foods->GRice = (float) $input->GRice;
		$foods->GVegetable = (string) $input->GVegetable;
		$foods->GFruit = (string) $input->GFruit;
		$foods->GMeat = (string) $input->GMeat;
		$foods->GFat = (string) $input->GFat;
		$foods->GMilk = (string) $input->GMilk;
		$foods->create_by = (string) $input->create_by;
		$foods->create_date = (string) $input->create_date;
		$foods->modify_by = (string) $input->modify_by;
		$foods->modify_date = (string) $input->modify_date;
		
		$rs = $fs->updateFoods($foods);

// 		$foodstyle = (array) $input->foodstyle;
// 		$fs->updateFoodstyle($rs,$foodstyle);
		
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


//delete by id
$app->delete('/foods/:id(/)','authenticate',function($id) use ($app){ 
	try {
		$fs = new FoodsService();
		$rs = $fs->deleteFoods($id);

// 		$foodstyle = (array) $input->foodstyle;
// 		$fs->updateFoodstyle($rs,$foodstyle);

		if($rs != 0){
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



// get by id
$app->get('/foods/:id(/)','authenticate',function($id) use ($app){
	try {
		$fs = new FoodsService();
		$rs = $fs->getFoodsByID($id);
			echo json_encode( $rs );
		if($rs->id != 0){
			$app->response()->header('Content-Type', 'application/json');
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



// get all foods 
$app->get('/foods(/)','authenticate',function() use ($app){ 
	try {
		$fs = new FoodsService();
		$rs = $fs->getAllFoods();
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