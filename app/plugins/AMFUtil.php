<?php
// namespace RedBeanPHP\Plugin;
// use \RedBeanPHP\AssociationManager as AssociationManager;
// use \RedBeanPHP\OODB as OODB;
// use \RedBeanPHP\OODBBean as OODBBean;
// use \RedBeanPHP\RedException as RedException;
// use \RedBeanPHP\Facade as R;
// use \RedBeanPHP\ToolBox as ToolBox;
// use \RedBeanPHP\Plugin\AMFUtil\SystemlogsService as SystemlogsService;
// use \PDO as PDO;
// use \ReflectionObject as ReflectionObject;
// use \ReflectionClass as ReflectionClass;
// use \ReflectionMethod  as ReflectionMethod;
// use \SimpleXMLElement as SimpleXMLElement;



class AMFUtil {


	public static $connection = null;  //$pdo //$dsn
	public static $username = 'root';
	public static $password = '';
	public static $logsrv = '';
	public static $server = "localhost";
	public static $port = "3306";
	public static $databasename = 'testredbean';
	private static $scopeSelectVar = null;
	private static $stmt = null;

	private static $key64c = '6539626566356463316461643338393635363031333238663438646232343439';  //tlen
	
	/**
	 * @var RedBean_Toolbox
	 */
	private static $toolbox;

	/**
	 * @var RedBean_OODB
	 */
	private static $redbean;

	public function __construct()
	{
		set_time_limit(0);
	}


	/**
	 * Sets the toolbox to be used by graph()
	 *
	 * @param RedBean_Toolbox $toolbox toolbox
	 *
	 * @return void
	 */
	public function setToolbox( Toolbox $toolbox )
	{
		self::$toolbox = $toolbox;
		self::$redbean = self::$toolbox->getRedbean();
	}


	public function setConnection(\PDO $pdo){
		self::$connection = $pdo;
	}

	public function getConnection(){
		return self::$connection;
	}

	public static function setKey($key) {
		self::$key64c = $key;
	}
	
	public static function getKey(){
		return self::$key64c;
	}
	
	// Encrypt Function
	public static function mcencrypt($encrypt, $key=null){
		
		if(!$key){
			$key = self::getKey();
		}
		
		$encrypt = serialize($encrypt);
		$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
		$key = pack('H*', $key);
		$mac = hash_hmac('sha256', $encrypt, substr(bin2hex($key), -32));
		$passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $encrypt.$mac, MCRYPT_MODE_CBC, $iv);
		$encoded = base64_encode($passcrypt).'|'.base64_encode($iv);
		return $encoded;
	}
	
	// Decrypt Function
	public static function mcdecrypt($decrypt, $key=null){
		if(!$key){
			$key = self::getKey();
		}
		$decrypt = explode('|', $decrypt);
		$decoded = base64_decode($decrypt[0]);
		$iv = base64_decode($decrypt[1]);
		$key = pack('H*', $key);
		$decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_CBC, $iv));
		$mac = substr($decrypted, -64);
		$decrypted = substr($decrypted, 0, -64);
		$calcmac = hash_hmac('sha256', $decrypted, substr(bin2hex($key), -32));
		if($calcmac!==$mac){
			return false;
		}
		$decrypted = unserialize($decrypted);
		return $decrypted;
	}
	
	
	//     firset array is = 0 only  ownTable is a property of table
	//     $arrrrtypes = array('0'=>'tablea','ownTableb'=>'tableb','ownTablec'=>'tablec');
	//     stdClass is a object
	public static function prepareForAMF($data, $arrTypes)
	{
		if (count($data) == 0)
			return $data;

		$ret = array();
		$substract = false;


		if (!array_key_exists('0', $data)) {
			$data = array($data);
			$substract = true;
		}

		$arrTypes = self::cleanup($arrTypes);

		if (!is_array($arrTypes) || empty($arrTypes)) {
			return;
		}


		if($arrTypes[0]=='stdClass'){
			for ($i = 0; $i < count($data); $i++) {
				$o = new $arrTypes[0]();
				foreach ($data[$i] as $property => $value) {
					$o->$property = $value;
				}
				$ret[] = $o;
			}
		} else if (class_exists($arrTypes[0])) {
			for ($i = 0; $i < count($data); $i++) {
				$o = new $arrTypes[0]();
				foreach ($data[$i] as $property => $value) {
					//            $pproperty = strtolower($property);
					$pproperty = $property;
					if (!property_exists($o, $pproperty)) {
						continue;
					}
					if (array_key_exists($property, $arrTypes)) {
						if ($value == null) {
							$o->$property = array();
							continue;
						}
						$newArr = $arrTypes;
						$newArr[0] = $arrTypes[$property];
						$o->$pproperty = self::prepareForAMF($value, $newArr);
					} else {
						$o->$pproperty = $value;
					}
				}
				$ret[] = $o;
			}
		} else {
			$arColumn = self::getColumnFromTable($arrTypes[0]);
			eval("class $arrTypes[0]{}; \$cls = new $arrTypes[0]();");
			foreach ($arColumn as $col) {
				$cls->{$col} = null;
			}
			for ($i = 0; $i < count($data); $i++) {
				$o = clone $cls;
				foreach ($data[$i] as $property => $value) {
					$pproperty = $property;
					if (!property_exists($o, $pproperty)) {
						continue;
					}
					if (array_key_exists($property, $arrTypes)) {
						if ($value == null) {
							$o->$property = array();
							continue;
						}
						$newArr = $arrTypes;
						$newArr[0] = $arrTypes[$property];
						$o->$pproperty = self::prepareForAMF($value, $newArr);
					} else {
						$o->$pproperty = $value;
					}
				}
				$ret[] = $o;
			}
		}
		if ($substract)
			$ret = $ret[0];
		return $ret;
	}

	/**
	 *
	 * @param pdoConnection $connection
	 * @param String $type
	 * @return array
	 */
	public static function getColumnFromTable($type)
	{
		if (false !== ($list = R::inspect($type))) {
			$fields = array();
			foreach ($list as $key=>$record)
				$fields[] = $key;
			return $fields;
		}
		return array();
	}

	
	//importArraytoAttay data from sourcearry = descarray     sourcedata can > = < desc array
	public static function importArraytoAttay($arDesc, $arSource)
	{
		$arSource = (array )$arSource;
		foreach ($arDesc as $key => $val) {
			if (array_key_exists($key, $arSource)) {
				$arDesc[$key] = $arSource[$key];
			}
		}
	
		return $arDesc;
	}
	

	/**
	 * @param $obj data  for input
	 * @param $type Class Name want to convert
	 * @return Object Class by $type
	 * @throws Security
	 */
	public static function ConvertoObj($obj, $type)
	{
		// if not want in object use protected แทน public
		$o = new $type();
		foreach ($o as $key => $value) {
			foreach ($obj as $objkey => $objvalue) {
				if ($key == $objkey) {
					if ($objkey == 'id' && ($objvalue == null)) {
						if($value == null) {
							$o->$key = 0;
						}
					}
					$o->$key = $objvalue;
				}
			}
			//        		echo $key . " : " . $value ."<br>";
		}
		return $o;
	}

	/**
	 * @param $beans array of redbean bean
	 * @return array of amf objecet
	 * @throws Security
	 */
	public static function convertBeanToArray( array $beans )
	{
		$i = 0;
		$rows = array();
		foreach ($beans as $key => $bean) {
			$rows[$i] = $bean->export();
			$i++;
		}
		return $rows;
	}




	private static function cleanup($bind)
	{
		if (!is_array($bind)) {
			if (!empty($bind))
				$bind = array($bind);
			else
				$bind = array();
		}
		return $bind;
	}

	//public static function prepareForAMF($data, $arrTypes, $arrDates=NULL)
	//{
	//	if (count($data) == 0)
	//		return $data;
	//	$ret = array();
	//	$substract = false;
	//	if (!array_key_exists('0', $data)) {
	//		$data = array($data);
	//		$substract = true;
	//	}
	//	for ($i=0; $i<count($data); $i++) {
	//		$o = new $arrTypes[0]();
	//		foreach ($data[$i] as $property => $value) {
	//			$pproperty = strtolower($property);
	//			if (!property_exists($o, $pproperty)) {
	//				continue;
	//			}
	//			if (array_key_exists($property, $arrTypes)) {
	//				if ($value == NULL) {
	//					$o->$pproperty = array();
	//					continue;
	//				}
	//				$newArr = $arrTypes;
	//				$newArr[0] = $arrTypes[$property];
	//				$o->$pproperty = prepareForAMF($value, $newArr, $arrDates);
	//			} else {
	//				if ($arrDates && array_key_exists($pproperty, $arrDates)) {
	//					$o->$pproperty = new DateTime($value);
	//				} else {
	//					$o->$pproperty = $value;
	//				}
	//			}
	//		}
	//		$ret[] = $o;
	//	}
	//	if ($substract)
	//		$ret = $ret[0];
	//	return $ret;
	//}

	public static function uuid()
	{
		$uuid = R::getCol('select UUID()');
		return $uuid[0];
	}

	public static function shortuuid()
	{
		$uuid = R::getCol('select UUID_SHORT()');
		return $uuid[0];
	}

	public static function makeArrayFromObject($data, $arrDates = null)
	{
		$data = (array )$data;
		foreach ($data as $k => $v) {
			if (is_array($v)) {
				$data[$k] = self::makeArrayFromObject($v, $arrDates);
			} else {
				if ($arrDates && array_key_exists($k, $arrDates)) {
					if ($v instanceof DateTime) {
						$data[$k] = $v->format('Y-m-d');
					} else {
						$data[$k] = $v->toString('Y-M-d');
					}
				} else
					if (is_object($v)) {
					$data[$k] = (array)$v;
				}
			}
		}
		return $data;
	}


	//   RMTYPE == 1  delete all type     == 0  not check    type = ''  delete type
	//    $arrTypes = array('0'=>'Patient_history'); //,'RMTYPE'=>'1' //,'type'=>'' //,'ownPatient_history_foodslist' =>'0','ownPatient_history_foodslist_recommen' =>'0'
	public static function addtypeown(&$data, $arrTypes)
	{
		if (count($data) == 0)
			return $data;

		$substract = false;


		if (!array_key_exists('0', $data)) {
			$data = array($data);
			$substract = true;
		}

		//$arrTypes = Config::cleanup($arrTypes);

		if (!is_array($arrTypes) || empty($arrTypes)) {
			return;
		}

		for ($i = 0; $i < count($data); $i++) {
			if(array_key_exists('RMTYPE',$arrTypes)) {   // มี RMTYPE
				if($arrTypes['RMTYPE']== 1 ) {  // == 1  remote all type
					if(!array_key_exists($arrTypes[0], $data[$i])){
						if(is_array($data[$i])){
							unset($data[$i]['type']);
						} else {
							unset($data[$i]->type);
						}
					}
				} else {
					if(!array_key_exists($arrTypes[0], $data[$i])){
						if(is_array($data[$i])){
							$data[$i]['type'] = $arrTypes[0];
						} else {
							$data[$i]->type = $arrTypes[0];
						}
					}
				}

			} else  {
				if(!array_key_exists($arrTypes[0], $data[$i])){
					if(is_array($data[$i])){
						$data[$i]['type'] = $arrTypes[0];
					} else {
						$data[$i]->type = $arrTypes[0];
					}
				}
			}
			foreach ($data[$i] as $property => &$value) {
				if (array_key_exists($property, $arrTypes)) {
					$newArr = $arrTypes;
					$newArr[0] = $arrTypes[$property];
					if(empty($newArr[0])){
						if(is_array($data[$i])){
							unset($data[$i][$property]);
						} else {
							unset($data[$i]->{$property});
						}
					} else {
						if (strpos( $property, 'own' ) === 0 ) {
							self::addtypeown($value, $newArr);
						} else {
							$data[$i]['own'.ucfirst($arrTypes[$property])] = self::addtypeown($value, $newArr);
							if(is_array($data[$i])){
								unset($data[$i][$property]);
							} else {
								unset($data[$i]->{$property});
							}
						}
					}

				}
			}
		}
		return $data;
	}


	public static function getLog()
	{
		$log = R::getDatabaseAdapter()->getDatabase()->getLogger();
		return $log->getLogs();
	}


	public static function arrayCastRecursive($array)
	{
		if (is_array($array)) {
			foreach ($array as $key => $value) {
				if (is_array($value)) {
					$array[$key] = self::arrayCastRecursive($value);
				}
				if ($value instanceof stdClass) {
					$array[$key] = self::arrayCastRecursive((array)$value);
				}else if( gettype($value) == 'object' ){
					$array[$key] = self::arrayCastRecursive((array)$value);
				} else{

				}
			}
		}
		if ($array instanceof stdClass) {
			return self::arrayCastRecursive((array)$array);
		}
		return $array;
	}


	public static function deleteRBrelation($beans){
		try {
			if(is_array($beans)){
				foreach ($beans as $bean) {
					self::deleteRBrelation($bean);
				}
			} else {
				foreach ($beans as $key => $value) {
					if(is_array($value)){
						self::deleteRBrelation($value);
					}
				}
				R::trash($beans);
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}



	public static function CreateMultipleDynamicClasses()
	{
		$stmt = self::$connection->prepare("show tables");
		$stmt->execute();
		$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		$handle = null;
		$classcode = '';
		foreach ($result as $key => $value) {
			foreach ($value as $key => $value) {
				$classcode = "class {$value} { ";
				$stmt2 = self::$connection->prepare("DESC $value");
				$stmt2->execute();
				$result2 = $stmt2->fetchAll(\PDO::FETCH_ASSOC);
				foreach ($result2 as $key => $value) {
					$classcode .= "public \${$value['Field']}; ";
				}
				$classcode .= "}";
				eval($classcode);
			}
		}
		return 1;
	}



	public static function getServiceMethod($class = null)
	{
		if (!$class)
			return;
		if (is_object($class)) {
			$rClass = new \ReflectionObject($class);
			//     		echo 'objclass';
		} elseif (class_exists($class)) {
			$rClass = new \ReflectionClass($class);
			//     		echo 'refclass';
		} else {
			//     		echo 'noclass';
			return;
		}
		$array = null;

		foreach ($rClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $rMethod) {
			if ($rMethod->getName() == 'getService' || $rMethod->getName() == '__construct' ||
					$rMethod->getName() == '__destruct') {

			} else {

				try {
					$row = new returnfunction();
					$mh = new \ReflectionMethod($rClass->getName(), $rMethod->getName());

					$doc = $mh->getDocComment();
					$row->classname = $rClass->getName();
					$row->method = $mh->getName();
					$row->comments = $doc;
					if (preg_match_all('/@(param|return)[ \t]((unknown_type|boolean|int|string|unknown|class|stdClass|object)+[ \t](\S*)[ \t](\S*)|(unknown_type|boolean|int|string|unknown|class|object)+[ \t](\S*)|(\S*))/s',
							$doc, $matches, PREG_SET_ORDER)) {
						$params = $mh->getParameters();
						foreach ($matches as $m) {
							$match = preg_split('/[\ \n\,]+/', $m[0]);
							$p = new params();
							if ($match[0] == '@param') {
								foreach ($params as $param) {
									if ($match[2] == '$' . $param->name) {
										$p->name = $match[2];
										$p->paratype = $match[1];
										if (isset($match[3])) {
											$p->option = $match[3];
											if ($p->paratype == 'object') {
												$p->detailtype = new $p->option();
											}
										}
										$row->params[] = $p;
									}
								}
							} else
								if ($match[0] == '@return') {
								$p->name = 'return';
								$p->paratype = $match[1];
								if (isset($match[2])) {
									$p->option = $match[2];
								}
								if ($row->returns) {
									$row->option = $p;
								} else {
									$row->returns = $p;
								}
							} else {
								$p->name = $match[1];
								$p->paratype = $match[2];
								if (isset($match[3])) {
									$p->option = $match[3];
								}
								$row->option[] = $p;
							}
						}
					}

					$array[] = $row;
				}
				catch (exception $e) {
					/* was not in parent class! */
				}
			}
		}
		return $array;
	}

	public static function arrayToXml($array, $rootElement = null, $xml = null)
	{
		$_xml = $xml;

		if ($_xml === null) {
			if($rootElement == null){
				$rootElement = '<root></root>';
			}
			//     		$_xml = new \SimpleXMLElement( $rootElement !== null ? $rootElement:'<root><root/>');
			$_xml = new \SimpleXMLElement( $rootElement);
		}

		foreach ($array as $k => $v) {
			if (is_array($v)) { //nested array
				$k = 'array' . $k;
				self::arrayToXml($v, $k, $_xml->addChild($k));
			} else {
				$_xml->addChild($k, $v);
			}
		}
		return $_xml->asXML();
	}

	public static function arrayToJson($array) {
		$json = json_encode((array)$array);
		return $json;
	}

	public static function JsonToArray($j) {
		$j = trim($j);
		$ar = json_decode($j);
		return $ar;
	}


	/**
	 *
	 * @param array $arrColumn
	 * @param object $obj
	 * @return void|multitype:NULL array
	 * @example $rsItem = self::assignItem2Column($arrColumns, $item);
	 */
	public static function assignItem2Column(array $arrColumn, $obj)
	{
		$rs = array();
		$arObj = (array )$obj;
		foreach ($arrColumn as $col) {
			if (array_key_exists($col, $arObj)) {
				$rs[$col] = $arObj[$col];
			} else {
				$rs[$col] = null;
			}
		}
		return $rs;
	}


	public static function ConfigSetup($arrconfig = null)
	{
		if(is_array($arrconfig) && !empty($arrconfig)){
			if($arrconfig['server']) self::$server = $arrconfig['server'];
			if($arrconfig['databasename']) self::$databasename = $arrconfig['databasename'];
			if($arrconfig['username']) self::$username = $arrconfig['username'];
			if($arrconfig['password']) self::$password = $arrconfig['password'];
			if($arrconfig['port']) self::$port = $arrconfig['port'];
		}

		self::$connection = new PDO(
				'mysql:host='.self::$server.';port='.self::$port.';dbname='.self::$databasename .';', self::$username, self::$password);
		self::$connection->query('SET NAMES utf8');
		R::setup(self::$connection);
		R::freeze(true);
		self::$logsrv = new \RedBeanPHP\Plugin\SystemlogsService();
		R::debug(true,1);
	}

	public static function savelog($log,$userid) {
		if(self::if_table_exists('systemlog')){
			if(empty(self::$logsrv )) {
				self::$logsrv = new SystemlogsService();
			}
			$rs = self::$logsrv->insertlog($log,$userid);
			return $rs;
		} else {
			self::createSystemlog();
			return null;
		}
	}

	public static function showsystemlog($userid) {
		if(self::if_table_exists('systemlog')){
			if(empty(self::$logsrv )) {
				self::$logsrv = new SystemlogsService();
			}
			$rs = self::$logsrv->getAllsystemlogs($userid);
			return $rs;
		} else {
			self::createSystemlog();
			return null;
		}
	}

	public static function clearsystemlog($userid) {
		if(self::if_table_exists('systemlog')){
			$rs =  R::wipe('systemlog');
			return $rs;
		} else {
			self::createSystemlog();
			return 0;
		}
	}

	private static function if_table_exists ($tablename)
	{
		$res = R::exec("SHOW TABLES LIKE '".$tablename."'");
		if($res)
		{
			return true;
		}
		return false;
	}
	
	private static  function createSystemlog(){
		$sql = "
		CREATE TABLE `systemlog` (
		  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'run number',
		  `types` varchar(30) NOT NULL DEFAULT '' COMMENT 'NEW UPDATE DELETE LOGIN',
		  `userid` varchar(30) DEFAULT NULL COMMENT 'USERID',
		  `superuserid` varchar(30) DEFAULT NULL COMMENT 'SuperUser ผู้อนุมัติ',
		  `logs` text NOT NULL COMMENT 'คำอธิบาย',
		  `query` text NOT NULL COMMENT 'Message Query',
		  `parametor` text,
		  `tbname` varchar(255) DEFAULT NULL,
		  `module` varchar(255) DEFAULT NULL,
		  `status` int(1) DEFAULT '1',
		  `over_id` bigint(11) DEFAULT NULL,
		  `view` int(1) NOT NULL DEFAULT '0',
		  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `modify_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`),
		  KEY `module` (`module`) USING BTREE
		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Logs';
		";
		$rs = R::exec($sql);
		$log = new Systemlog();
		$log->logs = 'Create New systemlog table';
		$log->query = self::removeRNT(json_encode(R::getLog()));
		$log->types = 'Create';
		$log->userid =  'system';
		$log->tbname = 'systemlog';
		$log->module =  __METHOD__;
		$log->parametor = json_encode(func_get_args());
		self::savelog($log,'system');
	}
	
	
	public static function image_resize($src,$dst, $width, $height, $crop=0)  {

		  if(!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";

		  $type = strtolower(substr(strrchr($src,"."),1));
		  if($type == 'jpeg') $type = 'jpg';
		  switch($type){
		    case 'bmp': $img = imagecreatefromwbmp($src); break;
		    case 'gif': $img = imagecreatefromgif($src); break;
		    case 'jpg': $img = imagecreatefromjpeg($src); break;
		    case 'png': $img = imagecreatefrompng($src); break;
		    default : return "Unsupported picture type!";
		  }

		  // resize
		  if($crop){
		    if($w < $width or $h < $height) return "Picture is too small!";
		    $ratio = max($width/$w, $height/$h);
		    $h = $height / $ratio;
		    $x = ($w - $width / $ratio) / 2;
		    $w = $width / $ratio;
		  }  else {
		    if($w < $width and $h < $height) return "Picture is too small!";
		    $ratio = min($width/$w, $height/$h);
		    $width = $w * $ratio;
		    $height = $h * $ratio;
		    $x = 0;
		}

	 	 $new = imagecreatetruecolor($width, $height);

		  // preserve transparency
		if($type == "gif" or $type == "png"){
		    imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
		    imagealphablending($new, false);
		    imagesavealpha($new, true);
		}

	  	imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

		 switch($type){
		    case 'bmp': imagewbmp($new, $dst); break;
		    case 'gif': imagegif($new, $dst); break;
		    case 'jpg': imagejpeg($new, $dst); break;
		    case 'png': imagepng($new, $dst); break;
		  }
	 	 return true;
	}


	/*
	 * @access private
	*
	* void insertPrepare(array $bindData)
	* */
	private static function insertPrepare($bindData)
	{
		/* @param array $bindData       Data to be prepared for binding
		 * @return array $insertArray
		* */
		ksort($bindData);

		$insertArray = array(
				'fields' => implode("`,`", array_keys($bindData)),
				'placeholders' => ':' . implode(',:', array_keys($bindData)),
		);

		return $insertArray;
	}


	/*
	 * @access private
	*
	* void updatePrepare(array $bindData)
	* */
	private static function updatePrepare($bindData)
	{
		/*
		 * @param array $bindData   Data to be prepared for binding
		* @return string $placeHolders
		* */
		ksort($bindData);

		$placeHolders = null;
		foreach ($bindData as $key => $val) {
			$placeHolders .= "`$key`=:$key, ";
		}

		$placeHolders = rtrim($placeHolders, ', ');
		return $placeHolders;
	}


	private static function where($arguments = array(), $joinKeyword = 'AND')
	{
		ksort($arguments);

		$whereClause = null;
		foreach ($arguments as $key => $val) {
			if (is_int($val)) {
				$whereClause .= "`$key` = $val {$joinKeyword} ";
			} else {
				$whereClause .= "`$key` = '$val' {$joinKeyword} ";
			}
		}

		$whereClause = rtrim($whereClause, ' ' . $joinKeyword . ' ');
		$whereClause = "WHERE {$whereClause}";
		return $whereClause;
	}


	private static function defineParamType($val)
	{
		/*@param string $val    the update or insert query data passed*/
		/*@return const $param  the param type constant returned from the function*/
		switch ($val):

		case (is_int($val)):
			$param = PDO::PARAM_INT;
		break;

		case (is_string($val)):
			$param = PDO::PARAM_STR;
			break;

		case (is_bool($val)):
			$param = PDO::PARAM_BOOL;
			break;

		case (is_Null($val)):
			$param = PDO::PARAM_Null;
			break;

		default:
			$param = null;
			endswitch;

			return $param;
	}

	
	//     public static function filter($table, $info)
	//     {
	//         $driver = self::$connection->getAttribute(PDO::ATTR_DRIVER_NAME);
	//         if ($driver == 'sqlite') {
	//             $sql = "PRAGMA table_info('" . $table . "');";
	//             $key = "name";
	//         } elseif ($driver == 'mysql') {
	//             $sql = "DESCRIBE " . $table . ";";
	//             $key = "Field";
	//         } else {
	//             $sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '" .
	//                 $table . "';";
	//             $key = "column_name";
	//         }
	//         if (false !== ($list = self::run($sql))) {
	//             $fields = array();
	//             foreach ($list as $record)
	//                 $fields[] = $record[$key];
	//             return array_values(array_intersect($fields, array_keys($info)));
	//         }
	//         return array();
	//     }
	public static function filter($table, $info)
	{
		if (false !== ($list = R::inspect($type))) {
			$fields = array();
			foreach ($list as $key=>$record)
				$fields[] = $key;
			return array_values(array_intersect($fields, array_keys($info)));
		}
		return array();
	}


	/*
	 * @access public
	*
	* void query(string $sql)
	* */
	public static function query($sql)
	{
		/*
		 * @param string $sql       the sql command to execute
		* */
		self::$scopeSelectVar = null;
		self::$connection->query($sql);
	}

	/*
	 * @access public
	*
	* void insertRow(string $tableName, string $bindData)
	*
	* $array = array('field1'=>'field1Value')<-Notice the abscence of ":"
	*      $handler->insertRow('table', $array)
	*
	* */
	public static function insertRow($tableName, $bindData)
	{
		/*
		 * @param string $tableName     Name of the table that is inserted into
		* @param   array $bindData     array holding the set of column names
		*                              respective data to be inserted
		* */
		try {
			$insertData = self::insertPrepare($bindData);

			self::$stmt = parent::prepare("INSERT INTO
					`{$tableName}` (`{$insertData['fields']}`)
					VALUES({$insertData['placeholders']})");

			foreach ($bindData as $key => $val) {
				$param = self::defineParamType($val);

				self::$stmt->bindValue(":$key", $val, $param);
			}

			$query = self::$stmt->execute();
		}
		catch (PDOException $e) {

		}
	}




	/*
	 * @access public
	*
	* void updateRow(string $tableName, array $bindData, string $target)
	*
	* Way of use: to update
	*  $array = array('field1'=>'field1Value')<-Notice the abscence of ":"
	*      $handler->updateRow('table', $array, '`field2`='Val'')
	* */
	public static function updateRow($tableName, $bindData, $target, $targetClause =  'AND')
	{
		/*
		 * @param string $tableName     The name of the table you're updating
		* @param array $bindData       array of the values to be inserted.
		*                              includes $_POST and $_GET
		* @param string $target        The exact update target. I.e.
		*                              WHERE id='?'
		* */
		try {
			$updateData = self::updatePrepare($bindData);
			if (isset($target)) {
				$target = self::where($target, $targetClause);
			}
			self::$stmt = self::$connection->prepare("UPDATE {$tableName}
			SET {$updateData} {$target}");
			foreach ($bindData as $key => $val) {
				$param = self::defineParamType($val);

				self::$stmt->bindValue(":$key", $val, $param);
			}

			self::$stmt->execute();
		}
		catch (PDOException $e) {

		}
	}

	/*
	 * @access public
	*
	* void deleteRow(string $tableName, string $target)
	* */
	public static function deleteRow($tableName, $target)
	{
		/*
		 * @param string $tableName table to be deleted from
		* @param string $target  target of the delete query
		* */
		try {
			return self::$connection->exec("DELETE FROM {$tableName} WHERE	{$target}");
		}
		catch (PDOException $e) {

		}
	}

	/*
	 * @access public
	*
	* void selectRow(string $fields, string $tableName, string $target)
	* */
	public static function selectRow($fields, $tableName, array $target = null, $targetClause =  'AND')
	{
		/*
		 * @param  string $fields   the fields of selection. E.g. '`field`,`field2`'...
		* @param  string $tableName The name of the target table
		* */

		if (isset($target)) {
			$where = self::where($target, $targetClause);
		} else {
			$where = null;
		}
		self::query("SELECT {$fields} FROM {$tableName} {$where}");

	}

	/*
	 * @access public
	*
	* void fetch([string $singleReturn = false], [constant $fetchMode = PDO::FETCH_OBJ])
	* */
	public static function fetch($singleReturn = false, $fetchMode = PDO::FETCH_OBJ)
	{
		/*
		 * @param string $singleReturn  the name of the "single" field to be fetching
		* @param constant $fetchMode   The fetch mode in which the data recieved will be stored
		* @return mixed    Null when conditions are not met, stdClass(object) or string when
		*                  conditions are met.
		* */
		if (!isset($this->stmt)) {
			return false;
		}

		if ($singleReturn == true) {
			if ($this->scopeSelectVar == false) {
				$this->scopeSelectVar = $this->stmt->fetch($fetchMode);

				if (isset($this->scopeSelectVar->$singleReturn)) {
					return $this->scopeSelectVar->$singleReturn;
				} else
					return false;
			}
		} else {
			$this->scopeSelectVar = $this->stmt->fetch($fetchMode);
			return (isset($this->scopeSelectVar)) ? $this->scopeSelectVar : new StdClass;
		}
	}

	/*
	 * @access public
	*
	* void fetchAll([constant $fetchMode = PDO::FETCH_ASSOC])
	* */
	public static function fetchAll($fetchMode = PDO::FETCH_ASSOC)
	{
		/*
		 * @param constant $fetchMode Default is PDO::FETCH_ASSOC the mode of fetching data
		* */
		if (!isset($this->stmt)) {
			return false;
		}

		$fetchVar = $this->stmt->fetchAll($fetchMode);

		return (!empty($fetchVar)) ? $fetchVar : new StdClass;

	}

	/*
	 * @TODO    set a convenient method to quicly setup nested queries
	* */
	public static function setSubQuery($target, $subQuery, $mysqlSubQueryHandler)
	{
		//mysql nested query handler
	}

	/*
	 * @access public
	*
	* void rowCount()
	* */
	public static function rowCount()
	{
		/*
		 * @return numeric $this->stmt->rowCount()
		* */
		if (isset($this->stmt)) {
			return $this->stmt->rowCount();
		}
	}

	/*
	 * @access public
	*
	* void lastId()
	*
	* */
	public static function lastId()
	{
		if (isset($this->stmt)) {
			return self::$connection->lastInsertId();
		}
	}

	/*
	 * @access public
	*
	* void truncateTable(string $tableName)
	* */
	public static function truncateTable($tableName)
	{
		/*
		 * @param string $tableName     The name of table to be truncated
		* Notice: truncation will reset the table and delete the data
		* */
		return self::query("TRUNCATE TABLE {$tableName}");
// 		echo "Table {$tableName} Truncated on:" . date("d-m-Y h:i:s") . "\n";
	}

	/*
	 * @access public
	* void debugDumpParams()
	*
	* */
	public static function debugDumpParams()
	{
		return $this->stmt->debugDumpParams();
	}


	public static function rankTable($tableName, $rankableColumn, $orderByColumn, $ascDesc =
			'DESC', $sqlVarName = 'rankNum')
	{
		self::query("SET @{$sqlVarName}:= 0");
		self::query("UPDATE `{$tableName}` SET {$rankableColumn}
		=@{$sqlVarName}:=@{$sqlVarName}+1
		ORDER BY `{$orderByColumn}` {$ascDesc}");
	}


	public static function insert($table, $info)
	{
		$fields = self::filter($table, $info);
		$sql = "INSERT INTO " . $table . " (" . implode($fields, ", ") . ") VALUES (:" .
				implode($fields, ", :") . ");";
		$bind = array();
		foreach ($fields as $field)
			$bind[":$field"] = $info[$field];
		return self::run($sql, $bind);
	}

	// //Update #1
	// $update = array(
	// 		"FName" => "Jane",
	// 		"Gender" => "female"
	// );
	// $db->update("mytable", $update, "FName = \'John\'");

	// //Update #2 w/Prepared Statement
	// $update = array(
	// 		"Age" => 24
	// );
	// $fname = "Jane";
	// $lname = "Doe";
	// $bind = array(
	// 		":fname" => $fname,
	// 		":lname" => $lname
	// );
	// $db->update("mytable", $update, "FName = :fname AND LName = :lname", $bind);
	public static function update($table, $info, $where, $bind = "")
	{
		$fields = self::filter($table, $info);
		$fieldSize = sizeof($fields);

		$sql = "UPDATE " . $table . " SET ";
		for ($f = 0; $f < $fieldSize; ++$f) {
			if ($f > 0)
				$sql .= ", ";
			$sql .= $fields[$f] . " = :update_" . $fields[$f];
		}

		$sql .= " WHERE " . $where . ";";

		$bind = self::cleanup($bind);
		foreach ($fields as $field)
			$bind[":update_$field"] = $info[$field];

		return self::run($sql, $bind);
	}

	/**
	 * //SELECT #1
	 * $results = $db->select("mytable");
	 *
	 * //SELECT #2
	 * $results = $db->select("mytable", "Gender = \'male\'");
	 *
	 * //SELECT #3 w/Prepared Statement
	 * $search = "J";
	 * $bind = array(
	 * ":search" => "%$search"
	 * );
	 * $results = $db->select("mytable", "FName LIKE :search", $bind);
	 */
	public static function select($table, $where = "", $bind = "", $fields = "*")
	{
		$sql = "SELECT " . $fields . " FROM " . $table;
		if (!empty($where))
			$sql .= " WHERE " . $where;
		$sql .= ";";
		$rs = self::run($sql, $bind);
		$rs = self::prepareForAMF($rs, $table);
		return $rs;
	}
	/*
	 //DELETE #1
	$db->delete("mytable", "Age < 30");

	//DELETE #2 w/Prepared Statement
	$lname = "Doe";
	$bind = array(
			":lname" => $lname
	)
	$db->delete("mytable", "LName = :lname", $bind);
	*/
	public static function delete($table, $where, $bind = "")
	{
		$sql = "DELETE FROM " . $table . " WHERE " . $where . ";";
		$d = self::run($sql, $bind);
		return $d;
	}

	public static function run($sql, $bind = "")
	{
		$sql = trim($sql);
		$bind = self::cleanup($bind);
		$error = "";

		try {
			$pdostmt = self::$connection->prepare($sql);
			if ($pdostmt->execute($bind) !== false) {
				if (preg_match("/^(" . implode("|", array(
						"select",
						"describe",
						"pragma")) . ") /i", $sql))
					return $pdostmt->fetchAll(PDO::FETCH_ASSOC);
				elseif (preg_match("/^(" . implode("|", array(
						"delete",
						"insert",
						"update")) . ") /i", $sql))
						return $pdostmt->rowCount();
			}
		}
		catch (PDOException $e) {
			$error = $e->getMessage();
			return false;
		}
	}

	public static function getAll($tbname, $userid='system', $where = null, $arrayparam = null, $type = null,	$format = null ,$userid='system')
	{
		$sql = 'select * from ' . $tbname . ' where 1 = 1';
		$sql = self::chkSql($sql, $where);
		if ($where && $arrayparam) {
			$rows = R::getAll($sql, $arrayparam);
		} else {
			$rows = R::getAll($sql);
		}
		if ($type) {
			$rows = self::prepareForAMF($rows, array('0' => $type));
		}
		return $rows;
	}

	public static function searchAll($tbname, $where = null, $arrayparam = null, $type = null,		$format = null ,$userid='system' )
	{
		$sql = 'select * from ' . $tbname . ' ';
		$sql = self::chkSql($sql, $where);
		if ($where && $arrayparam) {
			$rows = R::getAll($sql, $arrayparam);
		} else {
			$rows = R::getAll($sql);
		}
		if ($type) {
			$rows = self::prepareForAMF($rows, array('0' => $type));
		}
		return $rows;
	}

	public static function getbyID($tbname, $where, $arrayparam, $type = null, $format = null ,$userid='system')
	{
		$sql = 'select * from ' . $tbname . ' where 1 = 1';
		$sql = self::chkSql($sql, $where);
		if ($where && $arrayparam) {
			$rows = R::getAll($sql, $arrayparam);
		} else {
			$rows = R::getAll($sql);
		}
		if ($type) {
			$rows = self::prepareForAMF($rows, array('0' => $type));
		}
		return $rows;
	}

	public static function delbyID($tbname, $where, $arrayparam,$userid='system')
	{
		$sql = 'delete  from ' . $tbname . ' where 1 = 1';
		$sql = self::chkSql($sql, $where);
		if ($where && $arrayparam) {
			$rows = R::exec($sql, $arrayparam);
			// 			$rows = R::getAll($sql, $arrayparam);
		} else {
			$rows = R::getAll($sql);
		}

		return $rows;
	}

	public static function count($tbname)
	{
		$count = R::count($tbname);
		return $count;
	}

	public static function getAllpage($tbname, $startIndex = 0, $numRows = 20, $type = null, $userid='system')
	{
		$sql = 'Select * from ' . $tbname . ' Limit ? ,  ? ';
		// echo $sql;
		$rows = R::getAll($sql, array($startIndex, $numRows));
		if ($type) {
			$rows = self::prepareForAMF($rows, array('0' => $type));
		}
		return $rows;
	}


	public static function createRecord($tbname, $item, $id = null,$userid='system')
	{
		try {
			$arrColumns = self::getColumnFromTable($tbname);
			$rsItem = self::assignItem2Column($arrColumns, $item);
			$sqlTbCol = '';
			$sqlTbval = '';
			$length = sizeof($rsItem);
			$i = 0;
			foreach ($rsItem as $key => $val) {
				$sqlTbCol .= $key;
				$sqlTbval .= '\'' . $val . '\'';
				$i++;
				if ($i < $length) {
					$sqlTbCol .= ' , ';
					$sqlTbval .= ' , ';
				}
			}
			$sql = 'insert into ' . $tbname . ' (' . $sqlTbCol . ') values (' . $sqlTbval .
			')';

			self::$stmt = self::$connection->prepare($sql);
			if (self::$stmt->execute()) {
				$autoid = self::$connection->lastInsertId();
			} else {
				throw new Exception('Con\'t Insert');
			}

			if ($id) {
				return (int)$item->{$id};
			} else {
				return (int)$autoid;
			}
		}
		catch (exception $e) {
			throw new Exception($e->getMessage());
		}
	}


	//$item require class extend from stdC
	public static function updateRecord($tbname, $item, $type=null,$userid='system')
	{
		try {
			$item = self::ConvertoObj($item, $type);
			$sql = 'select * from ' . $tbname . ' where  ' . $item->getKey() . ' = ' . $item->getId();
			// 			echo $sql;
			$row = R::getRow($sql);
			$rsItem = self::importArraytoAttay($row, $item);
			$sql = 'update ' . $tbname . ' set ';
			$length = sizeof($rsItem);
			$i = 0;
			foreach ($rsItem as $key => $val) {
				$sql .= $key . '= \'' . $val . '\'';
				$i++;
				if ($i < $length) {
					$sql .= ' , ';
				}
			}

			$sql .= '	WHERE ' . $item->getKey() . '=' . $item->getId();
			self::$stmt = self::$connection->prepare($sql);
			if (self::$stmt->execute()) {
				return self::$item->getId();
			} else {
				throw new Exception('Con\'t Insert');
			}
		}
		catch (exception $e) {
			throw new Exception($e->getMessage());
		}
	}







	private static function chkSql($sql, $where)
	{
		if ($where) {
			$sql .= ' and ' . $where;
		}
		return $sql;
	}

	
	public  static function getServiceClass($dir){
		$services = array();
		if(empty($dir)){
			$dir = dirname(__FILE__);
		}
		// /(\w+)\.(php|Php)$/i
		// /^[a-z,A-Z,0-9,\.'()_-\s]+Service\.[a-z,A-Z,0-9,\s]+$/
		// /^[a-z,A-Z,0-9,\.\'()_-\s]+Service\.[php,\s]+$/
		$dh = opendir($dir);
		var_dump($dir);
		while (($file = readdir($dh)) !== false) {
			if (is_file($dir .'/'. $file)) {
				if(preg_match('/^[a-z,A-Z,0-9,\.\'()_-\s]+Service\.[php,\s]+$/', $file)){
					$obj = new \stdClass();
					require_once $dir .'/'. $file;
					$file = basename($file);         // $file is set to "index.php"
					$srv = basename($file, ".php");
					$obj->filephp = $file;
					$obj->classname = $srv;
					$services[] = $obj;
				}
			}
		}
		
		return $services;
	}

	public static function removeRNT($str){
		$regex = '/(\s|\\\\[rntv]{1})/';
		$str = trim( preg_replace($regex, ' ', $str) );
		return $str;
	}


} // End of AMFUtil

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



// prepareForAMF($data, $arrTypes)
R::ext('prepareForAMF', function($data, $arrTypes){
	return AMFUtil::prepareForAMF($data, $arrTypes);
});
// getColumnFromTable($type)
R::ext('getColumnFromTable', function($type){
	return AMFUtil::getColumnFromTable($type);
});

// public static function uuid()
R::ext('uuid', function(){
	return AMFUtil::uuid();
});

// public static function shortuuid()
R::ext('shortuuid', function(){
	return AMFUtil::shortuuid();
});

// public static function ConvertoObj($obj, $type)
R::ext('ConvertoObj', function($obj, $type){
	return AMFUtil::ConvertoObj($obj, $type);
});

// public static function convertBeanToArray( array $beans )
R::ext('convertBeanToArray',function($beans ){
	return AMFUtil::convertBeanToArray($beans);
});

// public static function makeArrayFromObject($data, $arrDates = null)
R::ext('makeArrayFromObject',function($data, $arrDates = null ){
	return AMFUtil::makeArrayFromObject($data, $arrDates);
});



// public static function addtypeown(&$data, $arrTypes)
R::ext('addtypeown',function(&$data, $arrTypes ){
	return AMFUtil::addtypeown($data, $arrTypes);
});


// public static function getLog()
R::ext('getLog',function( ){
	$amf = new AMFUtil();
	$amf->setToolbox(R::getToolBox());
	return $amf::getLog();
});


// public static function arrayCastRecursive($array)
R::ext('arrayCastRecursive',function($array ){
	return AMFUtil::arrayCastRecursive($array);
});


// public static function deleteRBrelation($beans){
R::ext('deleteRBrelation',function($beans ){
	$amf = new AMFUtil();
	$amf->setToolbox(R::getToolBox());
	return $amf->deleteRBrelation($beans);
});



// public static function Create_Multiple_Dynamic_Classes()
R::ext('CreateMultipleDynamicClasses',function( ){
	$amf = new AMFUtil();
	$amf->setConnection(R::getDatabaseAdapter()->getDatabase()->getPDO());
	return $amf->CreateMultipleDynamicClasses();
});


//check PDO from RB
R::ext('getConnection',function( ){
	$amf = new AMFUtil();
	$amf->setConnection(R::getDatabaseAdapter()->getDatabase()->getPDO());
	return $amf->getConnection();
});


R::ext('checkDecaredClass',function($class ){
	$arClass = get_declared_classes();
	foreach ($arClass as $key => $value) {
		if($value == $class){
			return 1;
		}
	}
	return 0;
});


R::ext('xdispense', function($type) {
	return R::getRedBean()->dispense( $type);
});

// public static function getServiceMethod($class = null)
R::ext('getServiceMethod',function( $class = null){
	return AMFUtil::getServiceMethod($class);
});


// public static function arrayToXml($array, $rootElement = null, $xml = null)
R::ext('arrayToXml',function($array, $rootElement = null, $xml = null ){
	return AMFUtil::arrayToXml($array,$rootElement,$xml);
});


// public static function assignItem2Column(array $arrColumn, $obj)
R::ext('assignItem2Column',function(array $arrColumn, $obj ){
	return AMFUtil::assignItem2Column($arrColumn, $obj);
});


// public static function arrayToJson($array) {
R::ext('arrayToJson',function($array ){
	return AMFUtil::arrayToJson($array);
});

// public static functio JsonToArray($j) {
R::ext('JsonToArray',function($j ){
	return AMFUtil::JsonToArray($j);
});

// $arConfig = array(
// 		'server'=>'127.0.0.1',
// 		'databasename'=>'testredbean',
// 		'username'=>'root',
// 		'password'=>'',
// 		'port' => '3306'
// 		);
// public static function ConfigSetup($arrconfig = null)
R::ext('ConfigSetup',function( $arrconfig = null){
	return AMFUtil::ConfigSetup($arrconfig);
});

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
 // CREATE TABLE `systemlog` (
 //   `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'run number',
 //   `types` varchar(30) NOT NULL DEFAULT '' COMMENT 'NEW UPDATE DELETE LOGIN',
 //   `userid` varchar(30) DEFAULT NULL COMMENT 'USERID',
 //   `superuserid` varchar(30) DEFAULT NULL COMMENT 'SuperUser ผู้อนุมัติ',
 //   `logs` text NOT NULL COMMENT 'คำอธิบาย',
 //   `query` text NOT NULL COMMENT 'Message Query',
 //   `parametor` text,
 //   `tbname` varchar(255) DEFAULT NULL,
 //   `module` varchar(255) DEFAULT NULL,
 //   `status` int(1) DEFAULT '1',
 //   `over_id` bigint(11) DEFAULT NULL,
 //   `view` int(1) NOT NULL DEFAULT '0',
 //   `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 //   `modify_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
 //   PRIMARY KEY (`id`),
 //   KEY `module` (`module`) USING BTREE
 // ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Logs';
 */
// new  \RedBeanPHP\Plugin\Systemlog();
// public static function savelog($log,$userid) {
R::ext('savelog',function( $log,$userid ){
	return AMFUtil::savelog($log,$userid);
});

// public static function showsystemlog($userid) {
R::ext('showsystemlog',function( $userid){
	return AMFUtil::showsystemlog($userid);
});

// public static function clearsystemlog($userid) {
R::ext('clearsystemlog',function( $userid){
	return AMFUtil::clearsystemlog($userid);
});


// public  static 	function getServiceClass($dir){
R::ext('getServiceClass',function($dir ){
	return AMFUtil::getServiceClass($dir);
});

// removeRNT($str);
R::ext('removeRNT',function($str ){
	return AMFUtil::removeRNT($str);
});

//importArraytoAttay data from sourcearry = descarray     sourcedata can > = < desc array
// public static function importArraytoAttay($arDesc, $arSource)
R::ext('importArraytoAttay',function( $arDesc, $arSource){
	return AMFUtil::importArraytoAttay($arDesc, $arSource);
});




// //     public static function filter($table, $info)
// //     {
// //         $driver = self::$connection->getAttribute(PDO::ATTR_DRIVER_NAME);
// //         if ($driver == 'sqlite') {
// //             $sql = "PRAGMA table_info('" . $table . "');";
// //             $key = "name";
// //         } elseif ($driver == 'mysql') {
// //             $sql = "DESCRIBE " . $table . ";";
// //             $key = "Field";
// //         } else {
// //             $sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '" .
// //                 $table . "';";
// //             $key = "column_name";
// //         }
// //         if (false !== ($list = self::run($sql))) {
// //             $fields = array();
// //             foreach ($list as $record)
// //                 $fields[] = $record[$key];
// //             return array_values(array_intersect($fields, array_keys($info)));
// //         }
// //         return array();
// //     }
// public static function filter($table, $info)
R::ext('filter',function($table, $info ){
	return AMFUtil::filter($table, $info);
});

// /*
//  * @access public
// *
// * void query(string $sql)
// * */
// public static function query($sql)
R::ext('query',function($sql ){
	return AMFUtil::query($sql);
});


// /*
//  * @access public
// *
// * void insertRow(string $tableName, string $bindData)
// *
// * $array = array('field1'=>'field1Value')<-Notice the abscence of ":"
// *      $handler->insertRow('table', $array)
// * @param string $tableName     Name of the table that is inserted into
// * @param   array $bindData     array holding the set of column names
// *                              respective data to be inserted
// *
// * */
// public static function insertRow($tableName, $bindData)
R::ext('insertRow',function($tableName, $bindData ){
	return AMFUtil::insertRow($tableName, $bindData);
});



// /*
//  * @access public
// *
// * void updateRow(string $tableName, array $bindData, string $target)
// *
// * Way of use: to update
// *  $array = array('field1'=>'field1Value')<-Notice the abscence of ":"
// *      $handler->updateRow('table', $array, '`field2`='Val'')
// * @param string $tableName     The name of the table you're updating
// * @param array $bindData       array of the values to be inserted.
// *                              includes $_POST and $_GET
// * @param string $target        The exact update target. I.e.
// *                              WHERE id='?'
// * */
// public static function updateRow($tableName, $bindData, $target, $targetClause =  'AND')
R::ext('updateRow',function($tableName, $bindData, $target, $targetClause =  'AND' ){
	return AMFUtil::updateRow($tableName, $bindData, $target, $targetClause);
});

// /*
//  * @access public
// *
// * void deleteRow(string $tableName, string $target)
// * @param string $tableName table to be deleted from
// * @param string $target  target of the delete query
// * */
// public static function deleteRow($tableName, $target)
R::ext('deleteRow',function($tableName, $target ){
	return AMFUtil::deleteRow($tableName, $target);
});


// /*
//  * @access public
// *
// * void selectRow(string $fields, string $tableName, string $target)
// * @param  string $fields   the fields of selection. E.g. '`field`,`field2`'...
// * @param  string $tableName The name of the target table
// * */
// public static function selectRow($fields, $tableName, array $target = null, $targetClause =  'AND')
R::ext('selectRow',function($fields, $tableName, array $target = null, $targetClause =  'AND' ){
	return AMFUtil::selectRow($fields,$tableName,$target,$targetClause);
});


// /*
//  * @access public
// *
// * void fetch([string $singleReturn = false], [constant $fetchMode = PDO::FETCH_OBJ])
// * @param string $singleReturn  the name of the "single" field to be fetching
// * @param constant $fetchMode   The fetch mode in which the data recieved will be stored
// * @return mixed    Null when conditions are not met, stdClass(object) or string when
// *                  conditions are met.
// * */
// public static function fetch($singleReturn = false, $fetchMode = PDO::FETCH_OBJ)
R::ext('fetch',function($singleReturn = false, $fetchMode = PDO::FETCH_OBJ ){
	return AMFUtil::fetch($singleReturn, $fetchMode);
});

// /*
//  * @access public
// *
// * void fetchAll([constant $fetchMode = PDO::FETCH_ASSOC])
// * @param constant $fetchMode Default is PDO::FETCH_ASSOC the mode of fetching data
// * */
// public static function fetchAll($fetchMode = PDO::FETCH_ASSOC)
R::ext('fetchAll',function($fetchMode = PDO::FETCH_ASSOC ){
	return AMFUtil::fetchAll($fetchMode);
});


// /*
//  * @access public
// *
// * void rowCount()
// * @return numeric $this->stmt->rowCount()
// * */
// public static function rowCount()
R::ext('rowCount',function( ){
	return AMFUtil::rowCount();
});

// /*
//  * @access public
// *
// * void lastId()
// *
// * */
// public static function lastId()
R::ext('lastId',function( ){
	return AMFUtil::lastId();
});


// /*
//  * @access public
// *
// * void truncateTable(string $tableName)
// * @param string $tableName     The name of table to be truncated
// * Notice: truncation will reset the table and delete the data
// * */
// public static function truncateTable($tableName)
R::ext('truncateTable',function($tableName ){
	return AMFUtil::truncateTable($tableName);
});

// /*
//  * @access public
// * void debugDumpParams()
// *
// * */
// public static function debugDumpParams()
R::ext('debugDumpParams',function( ){
	return AMFUtil::debugDumpParams();
});

// public static function rankTable($tableName, $rankableColumn, $orderByColumn, $ascDesc =	'DESC', $sqlVarName = 'rankNum')
R::ext('rankTable',function($tableName, $rankableColumn, $orderByColumn, $ascDesc =	'DESC', $sqlVarName = 'rankNum' ){
	return AMFUtil::rankTable($tableName, $rankableColumn, $orderByColumn, $ascDesc, $sqlVarName);
});


// 		public static function insert($table, $info)
R::ext('insert',function($tableName, $rankableColumn, $orderByColumn, $ascDesc =	'DESC', $sqlVarName = 'rankNum' ){
	return AMFUtil::insert($tableName, $rankableColumn, $orderByColumn, $ascDesc, $sqlVarName);
});


// 		// //Update #1
// // $update = array(
// // 		"FName" => "Jane",
// // 		"Gender" => "female"
// // );
// // $db->update("mytable", $update, "FName = \'John\'");

// // //Update #2 w/Prepared Statement
// // $update = array(
// // 		"Age" => 24
// // );
// // $fname = "Jane";
// // $lname = "Doe";
// // $bind = array(
// // 		":fname" => $fname,
// // 		":lname" => $lname
// // );
// // $db->update("mytable", $update, "FName = :fname AND LName = :lname", $bind);
// public static function update($table, $info, $where, $bind = "")
R::ext('update',function($table, $info, $where, $bind = "" ){
	return AMFUtil::update($table, $info, $where, $bind);
});

// /**
//  * //SELECT #1
// * $results = $db->select("mytable");
// *
// * //SELECT #2
// * $results = $db->select("mytable", "Gender = \'male\'");
// *
// * //SELECT #3 w/Prepared Statement
// * $search = "J";
// * $bind = array(
// 	 * ":search" => "%$search"
// 	 * );
// * $results = $db->select("mytable", "FName LIKE :search", $bind);
// */
// public static function select($table, $where = "", $bind = "", $fields = "*")
R::ext('select',function($table, $where = "", $bind = "", $fields = "*" ){
	return AMFUtil::select($table, $where, $bind, $fields);
});

// /*
//  //DELETE #1
// $db->delete("mytable", "Age < 30");

// //DELETE #2 w/Prepared Statement
// $lname = "Doe";
// $bind = array(
// 		":lname" => $lname
// )
// $db->delete("mytable", "LName = :lname", $bind);
// */
// public static function delete($table, $where, $bind = "")
R::ext('delete',function($table, $where, $bind = "" ){
	return AMFUtil::delete($table, $where, $bind);
});


// public static function run($sql, $bind = "")
R::ext('run',function($sql, $bind = "" ){
	return AMFUtil::run($sql, $bind);
});


// public static function xgetAll($tbname, $userid='system', $where = null, $arrayparam = null, $type = null,	$format = null ,$userid='system')
R::ext('xgetAll',function($tbname, $where = null, $arrayparam = null, $type = null,	$format = null ,$userid='system' ){
	return AMFUtil::getAll($tbname,$where, $arrayparam, $type,	$format,$userid);
});


// public static function searchAll($tbname, $where = null, $arrayparam = null, $type = null,		$format = null ,$userid='system' )
R::ext('searchAll',function($tbname, $where = null, $arrayparam = null, $type = null,		$format = null ,$userid='system'  ){
	return AMFUtil::searchAll($tbname, $where, $arrayparam, $type,$format,$userid );
});



// public static function getbyID($tbname, $where, $arrayparam, $type = null, $format = null ,$userid='system')
R::ext('getbyID',function($tbname, $where, $arrayparam, $type = null, $format = null ,$userid='system' ){
	return AMFUtil::getbyID($tbname, $where, $arrayparam, $type, $format,$userid);
});



// public static function delbyID($tbname, $where, $arrayparam,$userid='system')
R::ext('delbyID',function($tbname, $where, $arrayparam,$userid='system' ){
	return AMFUtil::delbyID($tbname, $where, $arrayparam,$userid);
});



// public static function count($tbname)
R::ext('xCount',function($tbname ){
	return AMFUtil::count($tbname);
});  



// public static function getAllpage($tbname, $startIndex = 0, $numRows = 20, $type = null, $userid='system')
R::ext('getAllpage',function($tbname, $startIndex = 0, $numRows = 20, $type = null, $userid='system' ){
	return AMFUtil::getAllpage($tbname, $startIndex, $numRow, $type, $userid);
});

R::ext('getKey',function( ){
	return AMFUtil::getKey();
});

// public static function setKey($key) {
R::ext('setKey',function( $key){
	return AMFUtil::setKey($key);
});


// public static function mc_encrypt($encrypt, $key=null){
R::ext('mcencrypt',function($encrypt, $key=null ){
	return AMFUtil::mcencrypt($encrypt,$key);
});


// public static function mc_decrypt($decrypt, $key=null){
R::ext('mcdecrypt',function($decrypt, $key=null ){
	return AMFUtil::mcdecrypt($decrypt, $key);
});

R::ext('imageresize',function($src, $dst, $width, $height, $crop=0 ){
	return AMFUtil::image_resize($src, $dst, $width, $height, $crop);
});

// R::ext('',function( ){
// 	return AMFUtil::();
// });

// R::ext('',function( ){
// 	return AMFUtil::();
// });


// R::ext('',function( ){
// 	return AMFUtil::();
// });

