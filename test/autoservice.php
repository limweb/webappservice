<?php

require_once 'Pdoconfig.php';
//require_once 'autocomplete.php';

/**
 * @author Thongchai Lim  *  林生海
 *  Tel:0816477729  0866018771
 *  Email/MSN:limweb@hotmail.com,thongchai@servit.co.th
 *  GoogleTalk:lim.thongchai@gmail.com
 *  Social Network Name: “limweb” Skype/HI5/Twitter/Facebook
 *  @copyright 2013 TH/BKK
 **/

             class Model_Autocomplete extends BaseModel {
        public function open() {
        
        }
        public function dispense() {

        }
        public function update() {

        }
        public function after_update() {

        }
        public function delete() {

        }
        public function after_delete() {

        }

 }

if( isset($_SERVER['REQUEST_METHOD'])){

                  $method = $_SERVER['REQUEST_METHOD'];
                  $request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));
                  if($method == 'GET' && $request[0]=='test') {
                    $sv = new AutocompleteService();
                    $sv->test('json');
                  } else if($method =='GET' && $request[0] == 'search'  ) {
                      $sv = new AutocompleteService();
                      $rs = $sv->search($request[1]);
                  } else if($method =='POST' && $request[0] == 'test'  ) {
                      echo 'post';
                      if(isset($request[1]) && $request[1] == 'add' ) {
                          //add  json object { "a":"aa","b":"bb", user":{"userid":"aaaa"}}
                           echo 'add';
                          $sv = new AutocompleteService();
                          $input = json_decode(file_get_contents("php://input"));
                          // var_dump($input);
                          $rs =  $sv->createautocomplete($input, getuserid($input));
                          // var_dump($rs);
                          return $rs;
                          exit();
                        }  
                  } else if( $method =='PUT'  && $request[0] == 'test'  ) {
                      if(isset($request[1]) && is_numeric($request[1]) ){
                        $sv = new AutocompleteService();
                        $input = json_decode(file_get_contents("php://input"));
                        // var_dump($input);
                        $rs = $sv->updateautocomplete($input,getuserid($input));
                        // var_dump($rs);
                        return $rs;
                      exit();
                      }
                  } else if( $method =='DELETE'  && $request[0] == 'test' ) {
                      if(isset($request[1])  && is_numeric($request[1]) ){
                          $sv = new AutocompleteService();
                          $input = json_decode(file_get_contents("php://input"));
                          // var_dump($input);
                          $rs = $sv->deleteautocomplete($request[1],getuserid($input));
                          // var_dump($rs);
                          return $rs;
                          exit();
                        } 
                  } else {

                  }

}  else {
  // echo 'start';
  // $sv = new AutocompleteService();
  // $sv->search('dr');

}


function getuserid($input){
  // var_dump($input->user->userid);
    if (isset( $input->user->userid ) ){
         return $input->user->userid;
    } else {
       return 'system';
    }
}

class AutocompleteService {

  private $queryLogger = null;
  private $connection = null;
  var $tbname = "autocomplete";

  public function __construct()
  {
    global $option;
    Pdoconfig::setup();
    $this->connection = Pdoconfig::$dbh;
    $this->queryLogger = Pdoconfig::$queryLogger;
  }


  /**
   * @return array
   */
  public function getAllautocomplete($userid=NULL) {
    try {
      $rows = R::find($this->tbname);
      $rows = R::prepareForAMF(R::convertBeanToArray($rows), array(0 =>ucfirst($this->tbname)));
              // $log = new Systemlog();
              // $log->logs = 'get all autocomplete'; 
              // $log->query = json_encode( R::getLog()); 
              //// $log->query = $stmt->queryString;
              // $log->types = 'SEARCH';
              // $log->userid =  $userid;
              // $log->tbname = $this->tbname;
              // $log->module =  __METHOD__;
              // $log->parametor = json_encode(func_get_args());
              //  Pdoconfig::$logsrv->insertlog($log,$userid);
      return $rows;

//      $rows = Pdoconfig::getAll($this->tbname,null,null,'autocomplete');
//      return $rows;
    } catch (Exception $e) {
      throw  new Exception($e->getMessage());
    }
  }


  /**
   *
   * @param int $itemID
   * @return object
   */
  public function getautocompleteByID($itemID,$userid=NULL) {
    try {
      $row = R::load($this->tbname, $itemID);
      $row = R::prepareForAMF($row->export(), array(0 => ucfirst($this->tbname)));
              // $log = new Systemlog();
              // $log->logs = 'get autocomplete by id'; 
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
  public function deleteautocomplete($itemID,$userid=NULL) {
    try {
      $rs = R::load($this->tbname, $itemID);
      if($rs->id){
        $row = R::trash($rs);
              // $log = new Systemlog();
              // $log->logs = 'deletel autocomplete'; 
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
   * @param object $item autocomplete
   * @return int
   */
  public function createautocomplete($item,$userid = NULL) {
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
              // $log->logs = 'insert autocomplete'; 
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
   * @param object $item autocomplete
   * @return int
   */
  public function updateautocomplete($item,$userid=NULL) {
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
              // $log->logs = 'update autocomplete'; 
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
  public function getautocomplete_paged($startIndex, $numItems,$userid=NULL) {
    try {
      $rows = R::getAll('select * from '.$this->tbname.' limit '.$startIndex.','.$numItems.';');
      $rows = R::prepareForAMF($rows,array(0 => ucfirst ($this->tbname)));
              // $log = new Systemlog();
              // $log->logs = 'get  autocomplete by page'; 
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

//-----------------------------------------------------------------------------

public function  search($search){
    $search = "%$search%";
    $sql =  "SELECT * FROM `autocomplete` where name like '$search'  ORDER BY name asc";
    // echo "\r\n",'search =',$sql,"\r\n";
    $rs = R::getAll($sql);
   echo  json_encode($rs);
}


//-----------------------------------------------------------------------------













  public function  __destruct(){
    $this->connection = null;
    R::close();
  }
  
  public function test($format=null) {
    global $request;
    if(isset($request[1])){
          switch ( $request[1] ) {
             case 'all':
                  $rs = $this->getAllautocomplete();
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
                    $rs = $this ->getautocompleteByID($request[1]);
                     break;
              default:
                  //    $rs = $this->getautocomplete_paged(0,5);
                  //    $rs = $this->deleteautocomplete(1);
               break;
           } 
    }else {
         $rs = $this->getAllautocomplete();
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




