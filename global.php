<?php
error_reporting(7);

if ($showqueries==1 OR $_GET[showqueries]==1){
    $script_start_time = microtime();
}
$configuration = array();
require "admin/config.php";
require "admin/class/mysql.php";
require "admin/configs/setting.php";

//var_dump($configuration);
//extract($configuration,EXTR_SKIP);

extract($configuration,EXTR_OVERWRITE);


$DB = new DB_MySQL;

$DB->servername = $servername;
$DB->dbname = $dbname;
$DB->dbusername = $dbusername;
$DB->dbpassword = $dbpassword;
$DB->mysqlver=$mysqlver;
$DB->dbcharset=$dbcharset;
$DB->connect();
$DB->selectdb();


require "admin/functions.php";

if (get_magic_quotes_gpc()) {

    $_GET = stripslashes_array($_GET);
    $_POST = stripslashes_array($_POST);
    $_COOKIE = stripslashes_array($_COOKIE);
    //$GLOBALS = stripslashes_array($GLOBALS);

}

extract($_GET,EXTR_SKIP);
extract($_POST,EXTR_SKIP);

unset($debug);
unset($exp);


set_magic_quotes_runtime(0);

$onlineuser = 0;
require "admin/class/session.php";

if (empty($url)) {
    $url = $_SERVER[REQUEST_URI];
}
$url = htmlspecialchars($url);

unset($pauserinfo);

if (!$_SESSION[logined]) {
    $pauserinfo = getuserinfo($_COOKIE[pauserid],$_COOKIE[papasswordhash]);
    //print_rr($pauserinfo);
    //exit;
    if (empty($pauserinfo)) {
        $pauserinfo = array();
        $usergroup = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."usergroup WHERE usergroupid=4"); // get guest's permission
        $pauserinfo = $usergroup;
        $pauserinfo[timezoneoffset] = $timezone;
        $pauserinfo['username']=$pauserinfo['title'];
        $pauserinfo['ip'] = $_SERVER['REMOTE_ADDR'];
        unset($usergroup);
    } else {
        $_SESSION[logined] = 1;
    }
    //$_SESSION=array_merge($_SESSION, $pauserinfo);//for php5.x
    $_SESSION[pauserinfo] = $pauserinfo;//for php4.x
}
//$pauserinfo = $_SESSION;//for php5.x
$pauserinfo = $_SESSION[pauserinfo];//for php4.x

//print_rr($_SESSION);
//print_rr($pauserinfo);

$onlineusers = $DB->fetch_one_array("SELECT COUNT(DISTINCT ipaddress) AS total FROM ".$db_prefix."session WHERE expiry>".time()."");
$onlineuser += $onlineusers[total];

define('HTMLDIR',$htmldir);
define('HTMLEXT',$htmlfileext);
$g_o_back2root='.';
if($_GET['mod'])
	$filename = $_GET['mod'];//basename($_SERVER['PHP_SELF']);
else $filename="index";
if(in_array($filename,Array('index','sort','article'))){
	if(empty($_GET['pagename'])){
		if($filename=="index")
			$writename = $filename;//substr($filename,0,strrpos($filename,'.'));
		$pagetemplate = "home";
	}else{
		$writename = $_GET['pagename'];
		$pagetemplate = $_GET['pagename']."home";
	}
}else unset($writename);

$mainpages = array("index","friend","love","marry");
if(!in_array($filename,$mainpages) && $filename){
	$writedir = HTMLDIR."/";
}else unset($writedir);
unset($styleid);
if(!$_GET[pagenum])$_GET[pagenum] = 1;
if ($filename=="article") {

    if (!empty($_GET[articleid])) {
        $articleid = intval($_GET[articleid]);
        $pagenum = intval($_GET[pagenum]);
    }

    if (empty($articleid)) {
        $nav = $_SERVER["REQUEST_URI"];
        $script = $_SERVER["SCRIPT_NAME"];
        $nav = ereg_replace("^$script","",urldecode($nav));
        $vars = explode("/",$nav);
        $articleid = intval($vars[1]);
        if (!empty($vars[2])) {
            $pagenum = intval($vars[2]);
        }
    }
    $article = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."article WHERE articleid='$articleid'");
    $sort = $DB->fetch_one_array("SELECT styleid FROM ".$db_prefix."sort WHERE sortid='$article[sortid]'");

    $styleid = $sort['styleid'];
    $prefilename=mkfilename($filenamemethod,$article['title'],1);
    $writename = $prefilename.$article['articleid']."_".intval($pagenum);
    unset($sort);
    //unset($article);

} else if ($filename=="sort") {

    if (!empty($_GET['sortid'])) {
        $sortid = intval($_GET['sortid']);
        $pagenum = intval($_GET['pagenum']);
    } else {
        $nav = $_SERVER["REQUEST_URI"];
        $script = $_SERVER["SCRIPT_NAME"];
        $nav = ereg_replace("^$script","",urldecode($nav));
        $vars = explode("/", $nav);
        $sortid = intval($vars[1]);

        if (!empty($vars[2])) {
            $pagenum = intval($vars[2]);
        }
    }

    $sortinfo = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."sort WHERE sortid='$sortid'");
    $prefilename=mkfilename($filenamemethod,$sortinfo['title'],2);
    $writename = $prefilename.$sortinfo['sortid']."_".intval($pagenum);
    if (!empty($sortinfo['styleid'])) {
        $styleid = $sortinfo['styleid'];
    }
    //unset($sortinfo);

}

$style = getstyle();

?>