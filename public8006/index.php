<?php
require_once 'phpquery.php';
$file = 'http://www.nod325.com/';
// <div class="entry">
phpQuery::newDocumentFileHTML($file);

$container = pq('.entry p:nth-of-type(3)');
echo $container->html();
// echo $sp=strip_tags($container);

?>