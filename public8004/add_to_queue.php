<?php
// filename: add_to_queue.php
 
//creating a queue requires we come up with an arbitrary number
define('QUEUE', 21671);
 
//add message to queue
$queue = msg_get_queue(QUEUE);
 
// Create dummy message object
$object = new stdclass;
$object->name = 'foo';
$object->id = uniqid();
 
//try to add message to queue
if (msg_send($queue, 1, $object)) {
        echo "added to queue  \n";
        // you can use the msg_stat_queue() function to see queue status
        print_r(msg_stat_queue($queue));
}
else {
        echo "could not add message to queue \n";
}