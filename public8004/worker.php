<?php
// filename: worker.php
 
//getting our requires we supply the id number we created it with
define('QUEUE', 21671);
 
$queue = msg_get_queue(QUEUE);
 
$msg_type = NULL;
$msg = NULL;
$max_msg_size = 512;
 
while (msg_receive($queue, 1, $msg_type, $max_msg_size, $msg)) {
        echo "Message pulled from queue - id:{$msg->id}, name:{$msg->name} \n";
         
       //do your business logic here and process this message!
 
       //finally, reset our msg vars for when we loop and run again
      $msg_type = NULL;
      $msg = NULL;
}