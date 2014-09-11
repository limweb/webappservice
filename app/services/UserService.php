<?php
// require_once __DIR__.'/../../app/config/database.php';
// require_once 'Pdoconfig.php';
//require_once 'User.php';

/**
 * @author Thongchai Lim  *  ๆ�—็”�ๆตท
 *  Tel:0816477729  0866018771
 *  Email/MSN:limweb@hotmail.com,thongchai@servit.co.th
 *  GoogleTalk:lim.thongchai@gmail.com
 *  Social Network Name: โ€�limwebโ€� Skype/HI5/Twitter/Facebook
 *  @copyright 2013 TH/BKK
 **/
 
 // if no  BaseModel please use pconfig to gen Pdoconfig.php  it have BaseModel
if(isset($_SERVER['REQUEST_METHOD'])){
  
      $method = $_SERVER['REQUEST_METHOD'];
      $request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));
      if($method == 'GET' && $request[0]=='test') {
          $sv = new UserService();
          $sv->test('json');
      } else if($method =='GET' && $request[0] == 'search'  ) {
            echo 'mofidy search function ';
      } else if($method =='POST' && $request[0] == 'test'  ) {
          if(isset($request[1]) && $request[1] == 'add' ) {
              //add  json object { "a":"aa","b":"bb", user":{"userid":"aaaa"}}
              $sv = new UserService();
              $input = json_decode(file_get_contents("php://input"));
              // var_dump($input);
              $rs =  $sv->createUser($input, getuserid($input));
              // var_dump($rs);
              return $rs;
              exit();
            }  
      } else if( $method =='PUT'  && $request[0] == 'test'  ) {
          if(isset($request[1]) && is_numeric($request[1]) ){
            $sv = new UserService();
            $input = json_decode(file_get_contents("php://input"));
            // var_dump($input);
            $rs = $sv->updateUser($input,getuserid($input));
            // var_dump($rs);
            return $rs;
          exit();
          }
      } else if( $method =='DELETE'  && $request[0] == 'test' ) {
          if(isset($request[1])  && is_numeric($request[1]) ){
              $sv = new UserService();
              $input = json_decode(file_get_contents("php://input"));
              // var_dump($input);
              $rs = $sv->deleteUser($request[1],getuserid($input));
              // var_dump($rs);
              return $rs;
              exit();
            } 
      } else {

      }
} else  {
       // $sv = new UserService();
       // $rs = $sv->getAllUser();
       // var_dump($rs);
  
}
function getuserid($input){
  // var_dump($input->user->userid);
    if (isset( $input->user->userid ) ){
         return $input->user->userid;
    } else {
       return 'system';
    }
}

class UserService {

  private $queryLogger = null;
  private $connection = null;
  var $tbname = "Users";

  public function __construct()
  {
//     global $option;
//     Pdoconfig::setup();
//     $this->connection = Pdoconfig::$dbh;
//     $this->queryLogger = Pdoconfig::$queryLogger;
  }


  /**
   * @return array
   */
  public function getAllUser($userid=NULL) {
    try {
      
            $rows = R::find(strtolower($this->tbname));
            $rows = R::prepareForAMF(R::convertBeanToArray($rows), array(0 =>ucfirst($this->tbname)));
              // $log = new Systemlog();
              // $log->logs = 'get all User'; 
              // $log->query = json_encode( R::getLog()); 
              //// $log->query = $stmt->queryString;
              // $log->types = 'SEARCH';
              // $log->userid =  $userid;
              // $log->tbname = $this->tbname;
              // $log->module =  __METHOD__;
              // $log->parametor = json_encode(func_get_args());
              //  Pdoconfig::$logsrv->insertlog($log,$userid);
              //      $rows = Pdoconfig::getAll($this->tbname,null,null,'User');
              //      return $rows;

              return $rows;
    } catch (Exception $e) {
      throw  new Exception($e->getMessage());
    }
  }


  /**
   *
   * @param int $itemID
   * @return object
   */
  public function getUserByID($itemID,$userid=NULL) {
    try {
      $row = R::load($this->tbname, $itemID);
      $row = R::prepareForAMF($row->export(), array(0 => ucfirst($this->tbname)));
              // $log = new Systemlog();
              // $log->logs = 'get User by id'; 
              // $log->query = json_encode( R::getLog()); 
              //// $log->query = $stmt->queryString;
              // $log->types = 'SEARCH';
              // $log->userid =  $userid;
              // $log->tbname = $this->tbname;
              // $log->module =  __METHOD__;
              // $log->parametor = json_encode(func_get_args());
              //  Pdoconfig::$logsrv->insertlog($log,$userid);

      return $row;
    } catch (Exception $e) {
      throw  new Exception($e->getMessage());
    }
  }

  /**
   *
   * @param int $itemID
   * @return int
   */
  public function deleteUser($itemID,$userid=NULL) {
    try {
      $rs = R::load($this->tbname, $itemID);
      if($rs->id){
        $row = R::trash($rs);
              // $log = new Systemlog();
              // $log->logs = 'deletel User'; 
              // $log->query = json_encode( R::getLog()); 
              //// $log->query = $stmt->queryString;
              // $log->types = 'DEL';
              // $log->userid =  $userid;
              // $log->tbname = $this->tbname;
              // $log->module =  __METHOD__;
              // $log->parametor = json_encode(func_get_args());
              //  Pdoconfig::$logsrv->insertlog($log,$userid);

        
        return $itemID;
      } else {
        throw new Exception('No Data for Delete');
      }
    } catch (Exception $e) {
      throw  new Exception($e->getMessage());
    }
  }

  /**
   *
   * @param object $item User
   * @return int
   */
  public function createUser($item,$userid = NULL) {
    try {
      $arrCol =R::getColumnFromTable($this->tbname);
      $item = R::assignItem2Column($arrCol,$item);
      //$item = json_decode (json_encode ($item), FALSE);
      $item = (object) $item;
      $item->id = 0;
      $item->create_by = $userid;
      $item->modify_by = $userid;
      $item->create_date = date('Y-m-d H:i:s');
      $item->modify_date = date('Y-m-d H:i:s');
      $bean = R::dispense($this->tbname);
      $bean->import($item);
      $id = R::store($bean);
      if($id){
              // $log = new Systemlog();
              // $log->logs = 'insert User'; 
              // $log->query = json_encode( R::getLog()); 
              //// $log->query = $stmt->queryString;
              // $log->types = 'NEW';
              // $log->userid =  $userid;
              // $log->tbname = $this->tbname;
              // $log->module =  __METHOD__;
              // $log->parametor = json_encode(func_get_args());
              //  Pdoconfig::$logsrv->insertlog($log,$userid);

      
        return $id;
      } else {
        throw new Exception("Can't Insert Item");
      }
    } catch (Exception $e) {
      throw  new Exception($e->getMessage());
    }
  }

  /**
   *
   * @param object $item User
   * @return int
   */
  public function updateUser($item,$userid=NULL) {
    try {
      $arrCol =R::getColumnFromTable($this->tbname);
      $item = R::assignItem2Column($arrCol,$item);
      //$item = json_decode (json_encode ($item), FALSE);
      $item = (object) $item;
      $item->modify_date = date('Y-m-d H:i:s');
      $item->modify_by = $userid;
      $bean = R::load($this->tbname,$item->id);
      if($bean->id){
        $bean->import($item);
        $id = R::store($bean);
        if($id){
              // $log = new Systemlog();
              // $log->logs = 'update User'; 
              // $log->query = json_encode( R::getLog()); 
              //// $log->query = $stmt->queryString;
              // $log->types = 'UPDATE';
              // $log->userid =  $userid;
              // $log->tbname = $this->tbname;
              // $log->module =  __METHOD__;
              // $log->parametor = json_encode(func_get_args());
              //  Pdoconfig::$logsrv->insertlog($log,$userid);

        
          return $id;
        } else {
          throw new Exception("Can't Update Item");
        }
      } else {
        throw new Exception('No record for Update');
      }
    } catch (Exception $e) {
      throw  new Exception($e->getMessage());
    }
  }

  /**
   * @return int
   */
  public function count($userid=NULL) {
    $count = R::count($this->tbname);
    return $count;
  }

  /**
   * @param int $startIndex
   * @param int $numItems
   * @return array
   */
  public function getUser_paged($startIndex, $numItems,$userid=NULL) {
    try {
      $rows = R::getAll('select * from '.$this->tbname.' limit '.$startIndex.','.$numItems.';');
      $rows = R::prepareForAMF($rows,array(0 => ucfirst ($this->tbname)));
              // $log = new Systemlog();
              // $log->logs = 'get  User by page'; 
              // $log->query = json_encode( R::getLog()); 
              //// $log->query = $stmt->queryString;
              // $log->types = 'SEARCH';
              // $log->userid =  $userid;
              // $log->tbname = $this->tbname;
              // $log->module =  __METHOD__;
              // $log->parametor = json_encode(func_get_args());
              //  Pdoconfig::$logsrv->insertlog($log,$userid);

      
      return $rows;
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }


  private function echoError($errorInfo) {
    throw new Exception('MySQL Error ' . $errorInfo[2], $errorInfo[1]);
  }

  public function  __destruct(){
    $this->connection = null;
    R::close();
  }
  
  public function test($format=null) {
    global $request;
    if(isset($request[1])){
          switch ( $request[1] ) {
             case 'all':
                  $rs = $this->getAllUser();
                   break;
             case 'count':
                   $rs = $this->count();
                   break;
             case 'page':
                    if(isset($request[2]) && isset($request[3]) && is_numeric($request[2]) && is_numeric($request[3]) ) {
                          $rs = $this->getUsers_paged($request[2],$request[3]);
                    } else {
                          $rs = $this->getUsers_paged(0,20);
                    }
                   break;
             case $request[1]:
                    $rs = $this ->getUserByID($request[1]);
                     break;
              default:
                  //    $rs = $this->getUser_paged(0,5);
                  //    $rs = $this->deleteUser(1);
               break;
           } 
    }else {
         $rs = $this->getAllUser();
    } 
  if($format){
      if($format == 'json'){
        header("Content-type: text/json; charset=utf-8");
            echo json_encode($rs);
      } else if($format == 'xml'){
        header("Content-type: text/xml; charset=utf-8");
        $js = json_encode($rs);
        $arjs = json_decode($js,true);
        echo Pdoconfig::arrayToXml($arjs,'<XML></XML>');
      }
    } else {
      var_dump($rs);
    }
  }

}





?>