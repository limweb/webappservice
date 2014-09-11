<?php


require_once 'rb.phar';
// require_once 'SystemlogsService.php';
class Pdoconfig
{
    public static $dbh = null;
    public static $username = 'root';
    public static $password = '';
    public static $logsrv = '';
    public static $server = "localhost";
    public static $port = "3306";
    public static $databasename = 'test'; 
    public static $queryLogger = null;
    private static $scopeSelectVar = null;
    private static $stmt = null;
    
    public static function setup()
    {

      self::init();
      self::$dbh = new PDO('mysql:host=' . self::$server . ';dbname=' . self::$databasename .';', self::$username, self::$password);
      self::$dbh->query('SET NAMES utf8');
      R::setup(self::$dbh);
      R::debug(true,1);
      R::freeze(true);
     // self::$logsrv = new SystemlogsService();
      self::$queryLogger = R::getDatabaseAdapter()->getDatabase()->getLogger();
    }
    
    
    public function __construct()
    {
    date_default_timezone_set('Asia/Bangkok');
    // set_time_limit(0);
      set_time_limit(500);
      ignore_user_abort(1);
      ini_set('upload_max_filesize', '10M');
      ini_set('post_max_size', '10M');
      ini_set('max_input_time', 500);
      ini_set('max_execution_time', 500);
      ini_set("display_errors", 1);
    }

    public static function init()
    {
      //      set_time_limit(0);
      set_time_limit(500);
      ignore_user_abort(1);
      ini_set('upload_max_filesize', '10M');
      ini_set('post_max_size', '10M');
      ini_set('max_input_time', 500);
      ini_set('max_execution_time', 500);
      ini_set("display_errors", 1);
    }
    
    
    public function __destruct() {
      self::$dbh = null;
      R::close();
    }
    
  }

class returnfunction
{
    public $method;
    public $classname;
    public $comments;
    public $params = array();
    public $returns;
    public $option = array();
}

class params
{
    public $name;
    public $paratype;
    public $option;
    public $detailtype;
}


class stdC
{

    const PK = 'id';

    public function getId()
    {
        return $this->{static::PK};
    }
    public function getKey()
    {
        return static::PK;
    }
}



 class BaseModel extends RedBean_SimpleModel {


 }


