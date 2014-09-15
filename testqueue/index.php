<?php
require __DIR__.'/../app/config/database.php'; 

$queue->push('SendEmail', array('message' => $message));

// If setAsGlobal has been called...
// Queue::push('SendEmail', array('message' => $message));