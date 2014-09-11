<?php 




$app->get('/admin', function () use ($app) {    
  try {
	include '../page/Login.php';
  } catch (Exception $e) {
    $app->response()->status(400);
    $app->response()->header('X-Status-Reason', $e->getMessage());
  }
});





//-------------------------------------//
//	  DASHBOARD CONTROLLER SECTION 
//-------------------------------------//

$app->get('/dashboard', function () use ($app) {    
  try {
	include '../page/dashboard.php';
  } catch (Exception $e) {
    $app->response()->status(400);
    $app->response()->header('X-Status-Reason', $e->getMessage());
  }
});






//-------------------------------------//
//	   FACTOR CONTROLLER SECTION 
//-------------------------------------//

	$app->get('/factor-list', function () use ($app) {    
		try {
			include '../page/factor.php';
		} catch (Exception $e) {
			$app->response()->status(400);
			$app->response()->header('X-Status-Reason', $e->getMessage());
		}
	});
	
	$app->get('/factor-create', function () use ($app) {    
		try {
			include '../page/factor_form.php';
		} catch (Exception $e) {
			$app->response()->status(400);
			$app->response()->header('X-Status-Reason', $e->getMessage());
		}
	});






//-------------------------------------//
//		FOOD CONTROLLER SECTION 
//-------------------------------------//

	$app->get('/food-list', function () use ($app) {    
		try {
			include '../page/food.php';
		} catch (Exception $e) {
			$app->response()->status(400);
			$app->response()->header('X-Status-Reason', $e->getMessage());
		}
	});
	
	$app->get('/food-create', function () use ($app) {    
		try {
			include '../page/food_form.php';
		} catch (Exception $e) {
			$app->response()->status(400);
			$app->response()->header('X-Status-Reason', $e->getMessage());
		}
	});

	$app->get('/food-scan', function () use ($app) {    
		try {
			include '../page/food_scan_check.php';
		} catch (Exception $e) {
			$app->response()->status(400);
			$app->response()->header('X-Status-Reason', $e->getMessage());
		}
	});

	$app->get('/food-category', function () use ($app) {    
		try {
			include '../page/food_category.php';
			
		} catch (Exception $e) {
			$app->response()->status(400);
			$app->response()->header('X-Status-Reason', $e->getMessage());
		}
	});
	
	$app->get('/food-style', function () use ($app) {    
		try {
			include '../page/food_style.php';
		} catch (Exception $e) {
			$app->response()->status(400);
			$app->response()->header('X-Status-Reason', $e->getMessage());
		}
	});







//-------------------------------------//
//	  CONTAINER CONTROLLER SECTION 
//-------------------------------------//

	$app->get('/container-list', function () use ($app) {    
		try {
			include '../page/container.php';

		} catch (Exception $e) {
			$app->response()->status(400);
			$app->response()->header('X-Status-Reason', $e->getMessage());
		}
	});
	
	$app->get('/container-create', function () use ($app) {    
		try {
			include '../page/container_form.php';

		} catch (Exception $e) {
			$app->response()->status(400);
			$app->response()->header('X-Status-Reason', $e->getMessage());
		}
	});

	$app->get('/container-scan', function () use ($app) {    
		try {
			include '../page/container_scan_check.php';

		} catch (Exception $e) {
			$app->response()->status(400);
			$app->response()->header('X-Status-Reason', $e->getMessage());
		}
	});






//-------------------------------------//
//	   MEMBER CONTROLLER SECTION 
//-------------------------------------//

	$app->get('/member-list', function () use ($app) {    
		try {
			include '../page/member.php';

		} catch (Exception $e) {
			$app->response()->status(400);
			$app->response()->header('X-Status-Reason', $e->getMessage());
		}
	});
	
	$app->get('/member-create', function () use ($app) {    
		try {
			include '../page/member_form.php';

		} catch (Exception $e) {
			$app->response()->status(400);
			$app->response()->header('X-Status-Reason', $e->getMessage());
		}
	});
	
	$app->get('/member-group', function () use ($app) {    
		try {
			include '../page/group_list.php';

		} catch (Exception $e) {
			$app->response()->status(400);
			$app->response()->header('X-Status-Reason', $e->getMessage());
		}
	});







?>