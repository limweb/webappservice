<?php

$app->delete('/articles/:id', 'authenticate','deletearticle');
$app->get('/articles(/)', 'authenticate','getall');		
$app->get('/articles/:id', 'authenticate','getbyid');
$app->post('/articles', 'authenticate','newarticles');
$app->put('/articles/:id', 'authenticate','editarticles');

function editarticles(){
		try {
			$request = $app->request();
			$mediaType = $request->getMediaType();
			$body = $request->getBody();
			$input = json_decode($body);
			$article = R::findOne('articles', 'id=?', array($id));
			if ($article) {
				$article->title = (string)$input->title;
				$article->url = (string)$input->url;
				$article->date = (string)$input->date;
				R::store($article);
				echo json_encode(R::exportAll($article));
			} else {
				throw new ResourceNotFoundException();
			}
		} catch (ResourceNotFoundException $e) {
			$app->response()->status(404);
		} catch (Exception $e) {
			$app->response()->status(400);
			$app->response()->header('X-Status-Reason', $e->getMessage());
		}
}


function newarticles(){
	global  $app;
	try {
			$request = $app->request();
			$mediaType = $request->getMediaType();
			$body = $request->getBody();
			$input = json_decode($body);
			if($input) {
				$article = R::dispense('articles');
				$article->title = (string)$input->title;
				$article->url = (string)$input->url;
				$article->date = (string)$input->date;
				$id = R::store($article);
				$app->response()->header('Content-Type', 'application/json');
				echo json_encode(R::exportAll($article));	
			} else {
				throw new ResourceNotFoundException();
			}
				
		} catch (ResourceNotFoundException $e) {
			$app->response()->status(404);
		} catch (Exception $e) {
			$app->response()->status(400);
			$app->response()->header('X-Status-Reason', $e->getMessage());
		}
}

function getall() {
	try {
		$articles = R::find('articles');
		echo json_encode(R::exportAll($articles));
	} catch (Exception $e) {
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}
}


function getbyid($id) {
		global  $app;
	try {
		$article = R::findOne('articles', 'id=?', array($id));
		if($article){
			echo json_encode(R::exportAll($article));
		} else {
			throw new ResourceNotFoundException();
		}
	} catch (ResourceNotFoundException $e) {
		$app->response()->status(404);
	} catch (Exception $e) {
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}
}


function deletearticle($id) {
		global  $app;
		  try {
		    $request = $app->request();
		    $article = R::findOne('articles', 'id=?', array($id));
		    if ($article) {
		      R::trash($article);
		      $app->response()->status(204);
		    } else {
		      throw new ResourceNotFoundException();
		    }
		  } catch (ResourceNotFoundException $e) {
		    $app->response()->status(404);
		  } catch (Exception $e) {
		    $app->response()->status(400);
		    $app->response()->header('X-Status-Reason', $e->getMessage());
		  }
}




//--------------- not used -------------
// $app->get('/articles', 'authenticate', function () use ($app) {
//   try {
//     $articles = R::find('articles');
// 	echo json_encode(R::exportAll($articles));
// //     $mediaType = $app->request()->getMediaType();
// //     var_dump($mediaType);
// //     if ($mediaType == 'application/xml') {
// //       $app->response()->header('Content-Type', 'application/xml');
// //       $xml = new SimpleXMLElement('<root/>');
// //       $result = R::exportAll($articles);
// //       foreach ($result as $r) {
// //         $item = $xml->addChild('item');
// //         $item->addChild('id', $r['id']);
// //         $item->addChild('title', $r['title']);
// //         $item->addChild('url', $r['url']);
// //         $item->addChild('date', $r['date']);
// //       }
// //       echo $xml->asXml();
// //     } else if (($mediaType == 'application/json')) {
// //       $app->response()->header('Content-Type', 'application/json');
// //       echo json_encode(R::exportAll($articles));
		// //     }


		//   } catch (Exception $e) {
		//     $app->response()->status(400);
		//     $app->response()->header('X-Status-Reason', $e->getMessage());
		// 		  }
		//   });

// // handle GET requests for /articles/:id
// $app->get('/articles/:id', 'authenticate', function ($id) use ($app) {
		// 		  try {
		// 		    $article = R::findOne('articles', 'id=?', array($id));
		// 		    if($article){
		// 		        echo json_encode(R::exportAll($article));
		// 		    } else {
		// 		    	echo "no data";
		// 		    }
		// // 		    if ($article) {
		// // 		      $mediaType = $app->request()->getMediaType();
		// // 		      if ($mediaType == 'application/xml') {
		// // 		        $app->response()->header('Content-Type', 'application/xml');
		// // 		        $xml = new SimpleXMLElement('<root/>');
		// // 		        $result = R::exportAll($article);
		// // 		        foreach ($result as $r) {
		// // 		          $item = $xml->addChild('item');
		// // 		          $item->addChild('id', $r['id']);
		// // 		          $item->addChild('title', $r['title']);
		// // 		          $item->addChild('url', $r['url']);
		// // 		          $item->addChild('date', $r['date']);
		// // 		        }
		// // 		        echo $xml->asXml();
		// // 		      } else if (($mediaType == 'application/json')) {
		// // 		        $app->response()->header('Content-Type', 'application/json');
		// // 		        echo json_encode(R::exportAll($article));
		// // 		      }
		// // 		    } else {
		// // 		      throw new ResourceNotFoundException();
		// // 		    }
		// 		  } catch (ResourceNotFoundException $e) {
		// 		    $app->response()->status(404);
		// 		  } catch (Exception $e) {
		// 		    $app->response()->status(400);
		// 		    $app->response()->header('X-Status-Reason', $e->getMessage());
		// 		  }
		// 		});

// handle POST requests for /articles
// $app->post('/articles', 'authenticate', function () use ($app) {
		// 		  try {
		// // 		  	echo "<pre>";
		// 		    $request = $app->request();
		// // 		    echo "request";
		// // 		    var_dump($request);
		// // 		    echo "app";
		// // 		    var_dump($app);
		// 		    $mediaType = $request->getMediaType();
		// // 		    echo "mediatype";
		// // 		    var_dump($mediaType);
		// 		    $body = $request->getBody();
		// // 		    echo "body";
		// // 		    var_dump($body);
		// // 			echo "POST OK";
		// // 			var_dump($_POST);
		// // 			echo "INPUT";
		// 		      $input = json_decode($body);
		// // 			 var_dump($input);
	
		// // 		    if ($mediaType == 'application/xml') {
		// // 		      $input = simplexml_load_string($body);
		// // 		    } elseif ($mediaType == 'application/json') {
		// // 		      $input = json_decode($body);
		// // 		    }
		// 		    $article = R::dispense('articles');
		// 		    $article->title = (string)$input->title;
		// 		    $article->url = (string)$input->url;
		// 		    $article->date = (string)$input->date;
		// 		    $id = R::store($article);
		// 			$app->response()->header('Content-Type', 'application/json');
		// 			echo json_encode(R::exportAll($article));
	
	
		// // 		    if ($mediaType == 'application/xml') {
		// // 		      $app->response()->header('Content-Type', 'application/xml');
		// // 		      $xml = new SimpleXMLElement('<root/>');
		// // 		      $result = R::exportAll($article);
		// // 		      foreach ($result as $r) {
		// // 		        $item = $xml->addChild('item');
		// // 		        $item->addChild('id', $r['id']);
		// // 		        $item->addChild('title', $r['title']);
		// // 		        $item->addChild('url', $r['url']);
		// // 		        $item->addChild('date', $r['date']);
		// // 		      }
		// // 		      echo $xml->asXml();
		// // 		    } elseif ($mediaType == 'application/json') {
		// // 		      $app->response()->header('Content-Type', 'application/json');
		// // 		      echo json_encode(R::exportAll($article));
		// // 		    }
		// 		  } catch (Exception $e) {
		// 		    $app->response()->status(400);
		// 		    $app->response()->header('X-Status-Reason', $e->getMessage());
		// 		  }
		// 		});

// handle PUT requests for /articles
/* $app->put('/articles/:id', 'authenticate', function ($id) use ($app) {
 try {
		$request = $app->request();
		$mediaType = $request->getMediaType();
		$body = $request->getBody();
		$input = json_decode($body);
		$article = R::findOne('articles', 'id=?', array($id));
		if ($article) {
		$article->title = (string)$input->title;
		$article->url = (string)$input->url;
		$article->date = (string)$input->date;
		R::store($article);
		echo json_encode(R::exportAll($article));
		} else {
		throw new ResourceNotFoundException();
		}
		 
		 
		 
		// 		    $request = $app->request();
		// 		    $mediaType = $request->getMediaType();
		// 		    $body = $request->getBody();
		// 		    if ($mediaType == 'application/xml') {
		// 		      $input = simplexml_load_string($body);
		// 		    } elseif ($mediaType == 'application/json') {
		// 		      $input = json_decode($body);
		// 		    }
		// 		    $article = R::findOne('articles', 'id=?', array($id));
		// 		    if ($article) {
		// 		      $article->title = (string)$input->title;
		// 		      $article->url = (string)$input->url;
		// 		      $article->date = (string)$input->date;
		// 		      R::store($article);
		// 		      if ($mediaType == 'application/xml') {
		// 		        $app->response()->header('Content-Type', 'application/xml');
		// 		        $xml = new SimpleXMLElement('<root/>');
		// 		        $result = R::exportAll($article);
		// 		        foreach ($result as $r) {
		// 		          $item = $xml->addChild('item');
		// 		          $item->addChild('id', $r['id']);
		// 		          $item->addChild('title', $r['title']);
		// 		          $item->addChild('url', $r['url']);
		// 		          $item->addChild('date', $r['date']);
		// 		        }
		// 		        echo $xml->asXml();
		// 		      } elseif ($mediaType == 'application/json') {
		// 		        $app->response()->header('Content-Type', 'application/json');
		// 		        echo json_encode(R::exportAll($article));
		// 		      }
		// 		    } else {
		// 		      throw new ResourceNotFoundException();
		// 		    }
		} catch (ResourceNotFoundException $e) {
		$app->response()->status(404);
		} catch (Exception $e) {
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
		}
		}); */

// handle DELETE requests for /articles
// $app->delete('/articles/:id', 'authenticate', function ($id) use ($app) {
		// 		  try {
		// 		    $request = $app->request();
		// 		    $article = R::findOne('articles', 'id=?', array($id));
		// 		    if ($article) {
		// 		      R::trash($article);
		// 		      $app->response()->status(204);
		// 		    } else {
		// 		      throw new ResourceNotFoundException();
		// 		    }
		// 		  } catch (ResourceNotFoundException $e) {
		// 		    $app->response()->status(404);
		// 		  } catch (Exception $e) {
		// 		    $app->response()->status(400);
		// 		    $app->response()->header('X-Status-Reason', $e->getMessage());
		// 		  }
		// 		});