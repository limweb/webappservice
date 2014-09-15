<?php
require_once __DIR__.'/../app/config/database.php'; 
ini_set("display_errors", 0);
date_default_timezone_set('Asia/Bangkok');
$sessionPath = sys_get_temp_dir();
session_save_path($sessionPath);
/**
 * ------------------------------------------------
 * Encrypt PHP session data using files
 * ------------------------------------------------
 * The encryption is built using mcrypt extension 
 * and the randomness is managed by openssl
 * The default encryption algorithm is AES (Rijndael-128)
 * and we use CBC+HMAC (Encrypt-then-mac) with SHA-256
 * 
 * @author    Enrico Zimuel (enrico@zimuel.it)
 * @copyright GNU General Public License
 */
$SESS_LIFE = get_cfg_var("session.gc_maxlifetime"); //To get the maximum valid session. 
$gbdomain = 'localhost';  // .tomatodev.info
$max = 30*60; // 30 mins 30*60
$pro = 1;
$divi = 1;
ini_set('session.use_trans_sid', 0); //Set the maximum survival time of garbage collection
ini_set('session.use_cookies', 1);
ini_set('session.cookie_path', '/'); //Many hosts share the save SESSION ID COOKIE
ini_set("session.cookie_domain", $gbdomain);
//echo   'set session init maxto ', $max,' sec  gc_probability ',$pro,' divisor ',$divi,'<br>';
ini_set('session.gc_maxlifetime',$max);
ini_set('session.gc_probability',$pro);
ini_set('session.gc_divisor',$divi);

class SecureSession {
    /**
     * Encryption algorithm
     * 
     * @var string
     */
    protected $_algo= MCRYPT_RIJNDAEL_128;    
    /**
     * Key for encryption/decryption
    * 
    * @var string
    */
    protected $_key;
    /**
     * Key for HMAC authentication
    * 
    * @var string
    */
    protected $_auth;
    /**
     * Path of the session file
     *
     * @var string
     */
    protected $_path;
    /**
     * Session name (optional)
     * 
     * @var string
     */
    protected $_name;
    /**
     * Size of the IV vector for encryption
     * 
     * @var integer
     */
    protected $_ivSize;
    /**
     * Cookie variable name of the encryption + auth key
     * 
     * @var string
     */
    protected $_keyName;


    protected $pdo;

    /**
     * Generate a random key using openssl
     * fallback to mcrypt_create_iv
     * 
     * @param  integer $length
     * @return string
     */
    protected function _randomKey($length=32) {
        //echo   __METHOD__,'<BR>';
        if(function_exists('openssl_random_pseudo_bytes')) {
            $rnd = openssl_random_pseudo_bytes($length, $strong);
            if ($strong === true) { 
                return $rnd;
            }    
        }
        if (defined('MCRYPT_DEV_URANDOM')) {
            return mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
        } else {
            throw new Exception("I cannot generate a secure pseudo-random key. Please install OpenSSL or Mcrypt extension");
        }   
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        echo   __METHOD__,'<BR>';
        session_set_save_handler(
            array($this, "open"),
            array($this, "close"),
            array($this, "read"),
            array($this, "write"),
            array($this, "destroy"),
            array($this, "gc")
        );
  }
    /**
     * Open the session
     * 
     * @param  string $save_path
     * @param  string $session_name
     * @return bool
     */
    public function open($save_path, $session_name) 
    {

            // global $gb_DBHOSTname, $gb_DBname, $gb_DBuser, $gb_DBpass, $SESS_DBH;
            // if (!$SESS_DBH = mysql_pconnect($gb_DBHOSTname, $gb_DBuser, $gb_DBpass)) {
            //     die('MySQL Error');
            // }
            // mysql_query("SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary", $SESS_DBH);
            // if (!mysql_select_db($gb_DBname, $SESS_DBH)) {
            //     die('MySQL Error');
            // }
            echo   __METHOD__,'<BR>';
            echo 'session id = ',session_id(),'<br>';
            return true;
            exit();
           $this->_path    = $save_path.'/';    
           $this->_name    = $session_name;
           $this->_keyName = "KEY_$session_name";
           $this->_ivSize  = mcrypt_get_iv_size($this->_algo, MCRYPT_MODE_CBC);

           if (empty($_COOKIE[$this->_keyName]) || strpos($_COOKIE[$this->_keyName],':')===false) {
            $keyLength    = mcrypt_get_key_size($this->_algo, MCRYPT_MODE_CBC);
            $this->_key   = self::_randomKey($keyLength);
            $this->_auth  = self::_randomKey(32);
            $cookie_param = session_get_cookie_params();
            setcookie(
                $this->_keyName,
                base64_encode($this->_key) . ':' . base64_encode($this->_auth),
                    ($cookie_param['lifetime'] > 0) ? time() + $cookie_param['lifetime'] : 0, // if session cookie lifetime > 0 then add to current time; otherwise leave it as zero, honoring zero's special meaning: expire at browser close.
                    $cookie_param['path'],
                    $cookie_param['domain'],
                    $cookie_param['secure'],
                    $cookie_param['httponly']
                    );
        } else {
            list ($this->_key, $this->_auth) = explode (':',$_COOKIE[$this->_keyName]);
            $this->_key  = base64_decode($this->_key);
            $this->_auth = base64_decode($this->_auth);
        } 
        return true;
}
    /**
     * Close the session
     * 
     * @return bool
     */
    public function close() 
    {
        // global $SESS_DBH;
        // //$SESS_DBH->Close();
        // return true;

        echo   __METHOD__,'<BR>';
        return true;
        
    }
    /**
     * Read and decrypt the session
     * 
     * @param  integer $id
     * @return string 
     */
    public function read($id) 
    {
            echo   __METHOD__,'<BR>';    
            echo 'id = ',$id,'<br>';    
            echo 'session_id = ',session_id(),'<br>';    
            // $sessinfo = DB::table('session')->where('session_id',"'$id'")->first();
            // $sessinfo = Session::where('session_id',$id)->get();
            $rs =R::getAll('select * from session where session_id = :id',array(':id'=>$id));
            // var_dump($rs);
            // var_dump($sessinfo->session_data);
            // $queries = DB::getQueryLog();
            // print_r($queries);
            // return $sessinfo->session_data;
            return $rs[0]['session_data'];
            exit();
            echo 'afterexit()<br>';
            // global $SESS_DBH, $SESS_LIFE;
            //     //      var_dump($SESS_DBH);
            // $qry = "select session_data from sessions where session_id = '$key' ";
            // $qid = mysql_query($qry, $SESS_DBH);
            //     //      var_dump($qid);
            // if (list ($value) = mysql_fetch_row($qid)) {
            //     return $value;
            // }
            // return false;

                $sess_file = $this->_path.$this->_name."_$id";
                if (!file_exists($sess_file)) {
                      // echo 'read false<br>';
                  return false;
              }    
                // echo 'session file = ',$sess_file,'<br>';
              $data      = file_get_contents($sess_file);
                // var_dump($data);
              list($hmac, $iv, $encrypted)= explode(':',$data);
              $iv        = base64_decode($iv);
              $encrypted = base64_decode($encrypted);
              $newHmac   = hash_hmac('sha256', $iv . $this->_algo . $encrypted, $this->_auth);
              if ($hmac !== $newHmac) {
                return false;
            }
            $decrypt = mcrypt_decrypt(
                $this->_algo,
                $this->_key,
                $encrypted,
                MCRYPT_MODE_CBC,
                $iv
                );
            return rtrim($decrypt, "\0"); 
                 // echo 'data =';
                 // var_dump($data);
                // return $data;
    }
    /**
     * Encrypt and write the session
     * 
     * @param integer $id
     * @param string $data
     * @return bool
     */
    public function write($id, $data) 
    {
            echo   __METHOD__,'<BR>';  
            echo 'session_id =',$id,'<br>';
            try {
                $sql = 'replace into `session` set `session`.session_id = :id ,`session`.session_data = :data , `session`.session_last_access = :time';
                echo 'data befor save = ',$data,'<br>';
                $rs =R::exec($sql,array(':id'=>$id,':data'=>$data,':time'=>time()));
                var_dump($rs);
                return true;
                exit();
            } catch (Exception $e) {
                echo 'Execption = ';
                echo $e->getMessage();
           }     
            echo 'aftersave<br>';
            //REPLACE
            // global $SESS_DBH, $SESS_LIFE;
            // $session_last_access = time();
            // $value = $val;
            // $qry = "insert into  sessions values('$key',$session_last_access,'$value')";
            // $qid = mysql_query($qry, $SESS_DBH);
            // if (!$qid) {
            //     $qry = "update sessions set session_last_access=$session_last_access, session_data='$value' where session_id='$key' ";
            //     $qid = mysql_query($qry, $SESS_DBH);
            // }
            // return $qid;

            // var_dump($data); 
            $sess_file = $this->_path . $this->_name . "_$id";
            // echo 'session file = ',$sess_file,'<br>';
            $iv        = mcrypt_create_iv($this->_ivSize, MCRYPT_DEV_URANDOM);
            $encrypted = mcrypt_encrypt(
                $this->_algo,
                $this->_key,
                $data,
                MCRYPT_MODE_CBC,
                $iv
                );
            $hmac  = hash_hmac('sha256', $iv . $this->_algo . $encrypted, $this->_auth);
            $bytes = file_put_contents($sess_file, $hmac . ':' . base64_encode($iv) . ':' . base64_encode($encrypted));
            // $bytes = file_put_contents($sess_file,$data);
               // echo 'write=',$bytes;
            return ($bytes !== false);  
    }
    /**
     * Destoroy the session
     * 
     * @param int $id
     * @return bool
     */
    public function destroy($id) 
    {
            // global $SESS_DBH;
            // $qry = "delete from sessions where session_id = '$key'";
            // $qid = mysql_query($qry, $SESS_DBH);
            // return $qid;
            echo   __METHOD__,'<BR>';        
            return true;
            exit();
            $sess_file = $this->_path . $this->_name . "_$id";
            setcookie ($this->_keyName, '', time() - 3600);
            return(@unlink($sess_file));
    }
    /**
     * Garbage Collector
     * 
     * @param int $max 
     * @return bool
     */
    public function gc($max) 
    {
             // global $SESS_DBH;
             // $old = time() - $maxlifetime;
             // $old = mysql_real_escape_string($old);
             // $qry = "delete from sessions where session_last_access <" . $old;
             // $qid = mysql_query($qry, $SESS_DBH);
             // return mysql_affected_rows($SESS_DBH);


            echo   __METHOD__,'<br>maxtime = ',$max,'<br>';        
            foreach (glob($this->_path . $this->_name . '_*') as $filename) {
                if (filemtime($filename) + $max < time()) {
                    $rs =@unlink($filename);
                    // $rs =unlink($filename);
                    // echo   'delete ',$filename,'=',$rs,'<br>';
                }
            }
            return true;
    }

    public function chktimeout() {
            // echo   __METHOD__,'<br>';
            //error_log(__METHOD__);
            if(isset($_SESSION) && isset($_SESSION['timelogin'])) {
                    $to = time() - $_SESSION['timelogin'];
                    if( $to > ini_get('session.gc_maxlifetime') ){
                        // echo 'timeout =', time() - $_SESSION['timelogin'];
                        session_destroy();
                        return  1;
                    } else {
                        // echo ' not time out =', time() - $_SESSION['timelogin'];
                        $_SESSION['timelogin'] = time();
                        return 0;
                    }
            } else {
                //error_log('no have session');
                return 1 ;
            }
    } 

    public  function checkApp($app) {
         $chk = 0;
        if( isset($_SESSION) && !$this->chktimeout() ) {
            foreach ($_SESSION['User']->pj as $pj) {
                    if($pj->gid == $app ) {
                        $chk = 1;
                        return $chk;
                        exit();
                    }
            }
        } else {
            return $chk;
        }
    }
}

$sv = new SecureSession();
echo 'start SesscesClass;'
?>