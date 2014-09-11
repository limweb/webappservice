<?php

$path =  __DIR__;


require_once 'rb.phar';
R::setup('mysql:host=localhost;port=3306;dbname=test','root','');
$table = 'autocomplete';
$cols = R::inspect($table);
$phpfile = '';
$phpfilename = $path . '/autocomplete.php';

echo '<?php ',"\r\n\r\n";
$phpfile .=  '<?php '."\r\n\r\n";

echo 'class  Autocomplete   {',"\r\n";
$phpfile .=  'class  Autocomplete   {'."\r\n";

foreach ($cols as $key => $value) {
  echo "\t\t\t\t",'public $'.$key,";\r\n";
  $phpfile .= "\t".'public $'.$key.";\r\n";
}

 echo "   } \r\n\r\n ?>\r\n\r\n";
 $phpfile .=  "   } \r\n\r\n ?>\r\n\r\n";

 file_put_contents($phpfilename, $phpfile);
 
 $asfile = '';
 $asfilename = $path . '/Autocomplete.as';

 echo "package vo\r\n";
 $asfile .= "package vo\r\n";
 echo "{\r\n";
 $asfile .= "{\r\n";
 echo  '  [RemoteClass(alias="Autocomplete")]',"\r\n";
 $asfile .=  '  [RemoteClass(alias="Autocomplete")]'."\r\n";
 echo "  [Bindable]\r\n";
 $asfile .= "  [Bindable]\r\n";
 echo "  public class Autocomplete\r\n";
 $asfile .= "  public class Autocomplete\r\n";
 echo "  {\r\n";
 $asfile .= "  {\r\n";
 foreach ($cols as $key => $value) {
  if (stripos($value, "int") !== false) {
      echo "\t\t\t\t",'public var '.$key.':int',";\r\n";
      $asfile .= "\t".'public var '.$key.':int'.";\r\n";
  } else {
      echo "\t\t\t\t",'public var '.$key.':String',";\r\n";
      $asfile .= "\t".'public var '.$key.':String'.";\r\n";
  } 
 }
 echo "     }\r\n";
 $asfile .= "     }\r\n";
 echo "}\r\n";
 $asfile .= "}\r\n";
 echo "\r\n";
 $asfile .= "\r\n";

file_put_contents($asfilename,$asfile);
