<?php
$postdata1=array('method'=>'get_',
		'data'=> array(
				'name' => 'Mahfuj',
				'sex' => 'Male'
		)
);

$postdata = http_build_query($postdata1);

$opts = array('http' =>
		array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $postdata
		)
);

$context  = stream_context_create($opts);
$result = file_get_contents('http://localhost/nutritionapps/app/foodlist.php', false, $context);
echo $result;