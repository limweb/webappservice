<?php
// require_once 'R.php';
namespace RedBeanPHP\Plugin;
use \RedBeanPHP\Facade as R;
use \RedBeanPHP\Plugin\AMFUtil\AMFUtil as AMF;
/**
 * @author Thongchai Lim  *  林生海
 *	Tel:0816477729  0866018771
 *	Email/MSN:limweb@hotmail.com,thongchai@servit.co.th
 *	GoogleTalk:lim.thongchai@gmail.com
 *	Social Network Name: “limweb” Skype/HI5/Twitter/Facebook
 *  @copyright 2013 TH/BKK
 **/
//$sv = new systemlogsService();
//$sv->test();

class SystemlogsService {

	public $connection = null;
	var $tbname = "systemlog";

	public function __construct()
	{
		global $option;
	}


	/**
	 * @return array
	 */
	public function getAllsystemlogs($userid=NULL) {
		try {
			$rows = R::find($this->tbname);
			$rows = R::prepareForAMF(R::convertBeanToArray($rows), array(0 => $this->tbname));
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
	public function getsystemlogsByID($itemID,$userid=NULL) {
		try {
			$row = R::load($this->tbname, $itemID);
			$row = R::prepareForAMF($row->export(), array(0 => $this->tbname));
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
	public function deletesystemlogs($itemID,$userid=NULL) {
		try {
			$rs = R::load($this->tbname, $itemID);
			if($rs->id){
				$row = R::trash($rs);
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
	 * @param String $ty   NEW UPDATE DELETE LIST
	 * @param String $userid
	 * @param String $superuserid default = null
	 * @param String $logs
	 * @param String $query
	 * @param String $parametor
	 * @param String $tbname
	 * @param String $module default = null
	 * @param int    $overid default = 0
	 * CREATE TABLE `systemlog` (
	 * `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'run number',
	 * `types` varchar(30) NOT NULL DEFAULT '' COMMENT 'NEW UPDATE DELETE LOGIN',
	 * `userid` varchar(30) DEFAULT NULL COMMENT 'USERID',
	 * `superuserid` varchar(30) DEFAULT NULL COMMENT 'SuperUser ผู้อนุมัติ',
	 * `logs` text NOT NULL COMMENT 'คำอธิบาย',
	 * `query` text NOT NULL COMMENT 'Message Query',
	 * `parametor` text,
	 * `tbname` varchar(255) DEFAULT NULL,
	 * `module` varchar(255) DEFAULT NULL,
	 * `status` int(1) DEFAULT '1',
	 * `over_id` bigint(11) DEFAULT NULL,
	 * `view` int(1) NOT NULL DEFAULT '0',
	 * `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	 * `modify_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
	 * PRIMARY KEY (`id`),
	 * KEY `module` (`module`) USING BTREE
	 *  )*ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Logs';
	 */
	/**
	 *
	 * @param object $item RedBeanPHP\Plugin\Systemlog
	 * @return int
	 */
	public function insertlog($item,$userid=NULL) {
		try {
			$arrCol = R::getColumnFromTable($this->tbname);
			$item = R::assignItem2Column($arrCol,$item);
		  //$item = json_decode (json_encode ($item), FALSE);
			$item = (object) $item;
			$item->id = 0;
			$item->create_date = date('Y-m-d H:i:s');
			$item->modify_date = date('Y-m-d H:i:s');
			$bean = R::dispense($this->tbname);
			$bean->import($item);
			$id = R::store($bean);
			if($id){
				return $id;
			} else {
				throw new Exception("Can't Insert Item");
			}
		} catch (Exception $e) {
			throw  new Exception($e->getMessage());
		}
	}
	


	/**
	 * CREATE TABLE `errorlogs` (
	 * `id` int(11) NOT NULL AUTO_INCREMENT,
	 * `errtype` varchar(255) DEFAULT NULL,
	 * `errcode` varchar(255) DEFAULT NULL,
	 * `errmessage` varchar(255) DEFAULT NULL,
	 * `method` varchar(255) DEFAULT NULL,
	 * `user` varchar(255) DEFAULT NULL,
	 * `create_date` datetime DEFAULT NULL,
	 * PRIMARY KEY (`id`)
	 * @param object $item RedBeanPHP\Plugin\errorlog
	*/
	public function inserterrorlog($errlog,$userid=NULL) {
		try {
			$arrCol = R::getColumnFromTable('errorlogs');
			$item = R::assignItem2Column($arrCol,$errlog);
			//$item = json_decode (json_encode ($item), FALSE);
			$errbean = R::dispense('errorlogs');
			$errbean->errtype = $errlog->errtype;
			$errbean->errcode = $errlog->errcode;
			$errbean->errmessage = $errlog->errmessage;
			$errbean->method = $errlog->method;
			$errbean->user = $errlog->user;
			$errbean->create_date = R::isoDateTime();
			$id = R::store($errbean);
			error_log($errbean->errmessage);
		} catch (Exception $e) {
			throw  new Exception($e->getMessage());
		}
	}

	/*
	 * @param object $item RedBeanPHP\Plugin\Systemlog
	 * @return int
	 */
	public function updatesystemlogs($item,$userid=NULL) {
		try {
			$arrCol = R::getColumnFromTable($this->tbname);
			$item = R::assignItem2Column($arrCol,$item);
		  //$item = json_decode (json_encode ($item), FALSE);
			$item = (object) $item;
			$item->modify_date = date('Y-m-d H:i:s');
			$bean = R::load($this->tbname,$item->id);
			if($bean->id){
				$bean->import($item);
				$id = R::store($bean);
				if($id){
					throw new Exception("Can't Update Item");
					return $id;
				} else {
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

	
	
	public function clearlogbydate($startDate,$endDate,$userid) {
		try {
			$buser = (object) R::getRow('select * from users where user_name = ? ',array($userid));
			$sql = "DELETE FROM `systemlog` WHERE DATE_FORMAT(create_date,'%Y-%m-%d') BETWEEN '$startDate' and '$endDate'";
			$item = new Systemlog();
			$item->query = $sql;
			$item->userid = $userid;
			$item->tbname = $this->tbname;
			if( $buser->groups_id == 1 &&  $buser->user_level > 5 ) {
				$rows = R::exec($sql);
				$item->parametor = "$startDate/$endDate/numrows = ".$rows;
				$item->logs = 'Clear Log between '. $startDate .' to '.$endDate;
				$item->types = 'CLEAR LOGS';
				$this->insertlog($item,$userid);
			} else {
				$item->parametor = "$startDate/$endDate";
				$item->logs = 'Warning::Clear Log between '. $startDate .' to '.$endDate;
				$item->types = 'Warning::CLEAR LOGS';
				$this->insertlog($item,$userid);
				throw new Exception('คุณไม่มีสิทธิ์ ในการ ลย');
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
	
	/**
	 * @param int $startIndex
	 * @param int $numItems
	 * @return array
	 */
	public function getsystemlogs_paged($startIndex, $numItems,$userid=NULL) {
		try {
			$rows = R::find($this->tbname,' LIMIT ? , ? ',array($startIndex,$numItems));
			$rows = R::prepareForAMF(R::convertBeanToArray($rows),array(0 => $this->tbname) );
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
//      $rs = $this->getAllsystemlogs();
// 		$rs = $this->count();
// 		$rs = $this ->getsystemlogsByID(1);
		$rs = $this->getsystemlogs_paged(0,5);
// 		$rs = $this->deletesystemlogs(1);
		if($format){
			if($format == 'json'){
				header("Content-type: text/json; charset=utf-8");
				echo json_encode($rs);
			} else if($format == 'xml'){
				header("Content-type: text/xml; charset=utf-8");
				$js = json_encode($rs);
				$arjs = json_decode($js,true);
				echo R::arrayToXml($arjs,'<XML></XML>');
			}
		} else {
			var_dump($rs);
		}
	}
}

class Systemlog{

	public $id = 0;
	public $types;
	public $userid;
	public $superuserid;
	public $logs;
	public $query;
	public $parametor;
	public $tbname;
	public $module = null;
	public $status  = 1;
	public $over_id = 0;
	public $view = 0;
	public $create_date;
	public $modify_date;

}

class errorlog {
	public $id = 0;
	public $errtype;
	public $errcode;
	public $errmessage;
	public $method;
	public $user;
	public $create_date;
}