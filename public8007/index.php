<?php
date_default_timezone_set ( 'Asia/Bangkok' );
require_once 'rb.phar';
R::setup('sqlite:./data.db');
function do_stuff(){
/* 26.9|0.0|0.0|31.4|0.00|0.00|0.00|0.00 */    
$result = file_get_contents('http://ourproject.dyndns-server.com:8099/&');
echo $result;
$rs = explode('|', $result);
echo "\n"; 
$now = new DateTime();
$b = R::dispense('timedate');
$b->time= $now->format('Y-m-d H:i:s');   
$b->T1 =  $rs[0];
$b->T2 =  $rs[1];
$b->T3 =  $rs[2];
$b->T4 =  $rs[3];
$b->V1 =  $rs[4];
$b->V2 =  $rs[5];
$b->V3 =  $rs[6];
$b->V4 =  $rs[7];
R::store($b);
echo $b->time;
echo "\n"; // MySQL datetime format
sleep(10); // wait 20 seconds
do_stuff(); // call this function again
}

do_stuff();
?>
