<?php
   require_once __DIR__.'/../app/config/database.php'; 
   require_once __DIR__.'/session.php';
  // session_start();
    if (!isset($_SESSION)) {
          session_start();
            echo 'start session<br>';
         } else {
            echo 'have session <br>';
         } 
            $currentCookieParams = session_get_cookie_params();  
            var_dump($currentCookieParams);
            $sidvalue = session_id();  
            setcookie(  
                'PHPSESSID',//name  
                $sidvalue,//value  
                0,//expires at end of session  
                $currentCookieParams['path'],//path  
                $currentCookieParams['domain'],//domain  
                false //secure  
            ); 

            $currentCookieParams1 = session_get_cookie_params();  
            var_dump($currentCookieParams1);
  // $sv = new SecureSession();
   header('Content-Type: text/html; charset=utf-8');
   echo 'start info<br>data read =<br>';
   var_dump($_SESSION);
    echo '<br>';

   if(!isset($_SESSION['visit']))
    {
        echo "This is the first time you're visiting this server<br>";
        $_SESSION['visit'] = 0;
    }
    else
            echo "Your number of visits: ".$_SESSION['visit'] . "<br>";

    $_SESSION['visit']++;
    $_SESSION['clientip'] = $_SERVER['REMOTE_ADDR'];
    echo '<br>';
    var_dump($_SESSION);
    echo "Server IP: ".$_SERVER['SERVER_NAME'] . "<br>";
    echo "Client IP: ".$_SERVER['REMOTE_ADDR'] . "<br>";
    var_dump($_SERVER);
    ?>