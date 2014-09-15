<?php
date_default_timezone_set ( 'Asia/Bangkok' );

function do_stuff(){
$now = new DateTime();
echo $now->format('Y-m-d H:i:s');   
echo "\n"; // MySQL datetime format
// echo $now->getTimestamp(); 
 sleep(10); // wait 20 seconds
do_stuff(); // call this function again
}

do_stuff();
?>
