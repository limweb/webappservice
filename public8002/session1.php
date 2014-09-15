<?php

$gb_DBHOSTname = "127.0.0.1"; //The host name or IP address
$gb_DBname = "lv4"; //The database name
$gb_DBuser = "root"; //Database user name
$gb_DBpass = ""; //Database password
$gb_COOKIE_DOMAIN = 'localhost';
$SESS_DBH = "";
$SESS_LIFE = get_cfg_var("session.gc_maxlifetime"); //To get the maximum valid session. 
 session_id(); //Do not use the GET/POST variable
ini_set('session.use_trans_sid', 0); //Set the maximum survival time of garbage collection
ini_set('session.gc_maxlifetime', 13600); //Use COOKIE to save SESSION ID way
ini_set('session.use_cookies', 1);
ini_set('session.cookie_path', '/'); //Many hosts share the save SESSION ID COOKIE
ini_set("session.cookie_domain", $gb_COOKIE_DOMAIN);
//Session.save_handler is set to user, rather than the default files session_module_name('user');
function sess_open($save_path, $session_name) {
    global $gb_DBHOSTname, $gb_DBname, $gb_DBuser, $gb_DBpass, $SESS_DBH;
    if (!$SESS_DBH = mysql_pconnect($gb_DBHOSTname, $gb_DBuser, $gb_DBpass)) {
        die('MySQL Error');
    }
    mysql_query("SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary", $SESS_DBH);
    if (!mysql_select_db($gb_DBname, $SESS_DBH)) {
        die('MySQL Error');
    }
    return true;
}

function sess_close() {
    global $SESS_DBH;
    //$SESS_DBH->Close();
    return true;
}

function sess_read($key) {
    global $SESS_DBH, $SESS_LIFE;
     var_dump($SESS_DBH);
    $qry = "select session_data from sessions where session_id = '$key' ";
    $qid = mysql_query($qry, $SESS_DBH);
     var_dump($qid);

    // if (list ($value) = mysql_fetch_row($qid)) {
        // return $value;
    // }
    // return false;
    $rs = mysql_fetch_row($qid);
    return $rs;
}

function sess_write($key, $val) {
    global $SESS_DBH, $SESS_LIFE;
    $session_last_access = time();
    $value = $val;
    $qry = "insert into  sessions values('$key',$session_last_access,'$value')";
    $qid = mysql_query($qry, $SESS_DBH);
    if (!$qid) {
        $qry = "update sessions set session_last_access=$session_last_access, session_data='$value' where session_id='$key' ";
        $qid = mysql_query($qry, $SESS_DBH);
    }
    return $qid;
}

function sess_destroy($key) {
    global $SESS_DBH;
    $qry = "delete from sessions where session_id = '$key'";
    $qid = mysql_query($qry, $SESS_DBH);
    return $qid;
}

function sess_gc($maxlifetime) {
    global $SESS_DBH;
    $old = time() - $maxlifetime;
    $old = mysql_real_escape_string($old);
    $qry = "delete from sessions where session_last_access <" . $old;
    $qid = mysql_query($qry, $SESS_DBH);
    return mysql_affected_rows($SESS_DBH);
}
session_module_name();
session_set_save_handler("sess_open", "sess_close", "sess_read", "sess_write", "sess_destroy", "sess_gc");
session_start();
