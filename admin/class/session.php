<?php
error_reporting(7);

$SESS_LIFE = get_cfg_var("session.gc_maxlifetime");
// $SESS_LIFE = 1440;
// $SESS_LIFE = 300;
function sess_open($save_path, $session_name) {
        sess_gc(0);
        return true;
} 

function sess_close() {
        return true;
} 

function sess_read($key) {
        global $DB, $db_prefix, $session, $onlineuser;
        $session = $DB->fetch_one_array("SELECT * FROM " . $db_prefix . "session WHERE sessionid='$key'");
        if (!empty($session) AND $session[expiry] > time()) {
                return $session[value];
        } else {
                $onlineuser++;
                return "";
        } 
} 

function sess_write($key, $val) {
        global $DB, $db_prefix, $SESS_LIFE, $pauserinfo, $session;
		//echo $db_prefix;
        $expiry = time() + $SESS_LIFE;
        $value = addslashes($val);

        $ipaddress = getip();
        $useragent = $_SERVER[HTTP_USER_AGENT];
        $REQUEST_URI = $_SERVER[REQUEST_URI];
		
	$DB->selectdb();
	
        if (empty($session) AND $key != $session['key']) {
                $query = $DB->query("INSERT IGNORE INTO " . $db_prefix . "session (sessionid,expiry,value,userid,ipaddress,useragent,location,lastactivity)
                                         VALUES ('$key',$expiry,'$value','$pauserinfo[userid]','" . addslashes($ipaddress) . "','" . addslashes($useragent) . "','" . addslashes($REQUEST_URI) . "','" . time() . "')");
        } else {$DB->selectdb();
                $query = $DB->query("UPDATE " . $db_prefix . "session SET
                                         expiry='$expiry',
                                         userid='$pauserinfo[userid]',
                                         value='$value',
                                         ipaddress='" . addslashes($ipaddress) . "',
                                         useragent='" . addslashes(substr($useragent,0,255)) . "',
                                         location='" . addslashes($REQUEST_URI) . "',
                                         lastactivity='" . time() . "'
                                         WHERE sessionid='$key'");
        } 
        if ($pauserinfo['userid'] != 0) {
                if ((time() - $pnuserinfo['lastactivity']) > $SESS_LIFE) {
                        $DB->query("UPDATE " . $db_prefix . "user SET lastvisit=lastactivity,lastactivity='" . time() . "' WHERE userid='$pauserinfo[userid]'");
                } else {
                        $DB->query("UPDATE " . $db_prefix . "user SET lastactivity='" . time() . "' WHERE userid='$pauserinfo[userid]'");
                } 
        } 
        return $query;
} 

function sess_destroy($key) {
        global $DB, $db_prefix;
        return $DB->query("DELETE LOW_PRIORITY FROM " . $db_prefix . "session WHERE sessionid='$key'");
} 

function sess_gc($maxlifetime) {
        global $DB, $db_prefix;
        $query = $DB->query("DELETE LOW_PRIORITY FROM " . $db_prefix . "session WHERE expiry<" . time() . "");
        return $DB->affected_rows();
} 
register_shutdown_function('session_write_close');//php5.4以上需要 修改 2016-10-7
session_set_save_handler("sess_open",
        "sess_close",
        "sess_read",
        "sess_write",
        "sess_destroy",
        "sess_gc");

@session_start();

?>