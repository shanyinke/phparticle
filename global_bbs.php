<?php
error_reporting(7);

if ($showqueries==1 OR $_GET[showqueries]==1){
    $script_start_time = microtime();
}
$configuration = array();
require "admin/config.php";
require "admin/class/mysql.php";
$DB = new DB_MySQL;

$DB->servername = $servername;
$DB->dbname = $dbname;
$DB->dbusername = $dbusername;
$DB->dbpassword = $dbpassword;
$DB->mysqlver=$mysqlver;
$DB->dbcharset=$dbcharset;
$DB->connect();
$DB->selectdb();

$startnum=intval($_GET['st']);
if(!$_GET['s']){
$sysdata=$DB->fetch_one_array("SELECT tagname FROM " . $db_prefix . "tag WHERE tagname!='defaultsys' GROUP BY tagname LIMIT ".$startnum.",1");
$_GET['s']=$sysdata['tagname'];
}
if($_GET['s'])$loadsystem_suffix=$_GET['s'];
require "admin/loadsystem/config".$loadsystem_suffix.".php";

require "admin/configs/setting.php";
require "admin/loadsystem/dbrelation".$loadsystem_suffix.".php";

foreach($tablelist['article'] AS $btfieldname => $afieldname)
{
	$alist[]=$afieldname ." AS ". $btfieldname;
	$alist2[]=$db_prefix_bbs.$forumlist['article'].".".$afieldname ." AS ". $btfieldname;
}
$aconvertlist=join(',',$alist);
$aconvertlist2=join(',',$alist2);

foreach($tablelist['sort'] AS $bsfieldname => $sfieldname)
{
	$slist[]=$sfieldname ." AS ". $bsfieldname;
	$slist2[]=$db_prefix_bbs.$forumlist['sort'].".".$sfieldname ." AS ". $bsfieldname;
}
$sconvertlist=join(',',$slist);
$sconvertlist2=join(',',$slist2);

foreach($tablelist['articletext'] AS $bpfieldname => $atfieldname)
{
	$atlist[]=$atfieldname ." AS ". $bpfieldname;
	$atlist2[]=$db_prefix_bbs.$forumlist['articletext'].".".$atfieldname ." AS ". $bpfieldname;
}
$atconvertlist=join(',',$atlist);
$atconvertlist2=join(',',$atlist2);
if($joinforumlist)
{
	foreach($joinforumlist AS $tablename => $trealname)
	{
		$atlist=Array();
		if($tablename=='articletext')
		{
			$fieldlist=Array();
			foreach($jointablelist[$tablename] AS $rawfield => $fieldname)
			{
				$fieldlist[]=$fieldname;
				$rawfieldlist[]=$rawfield;
				$atlist[]=$db_prefix_bbs.$joinforumlist[$tablename].".".$fieldname." AS ".$rawfield;
			}
			$atjoinconvertlist=",".join(',',$atlist);
			$atjoinoption="LEFT JOIN ".$db_prefix_bbs.$joinforumlist[$tablename]." ON ".$db_prefix_bbs.$joinforumlist[$tablename].".".$fieldlist[0]."=".$db_prefix_bbs.$forumlist[$tablename].".".$tablelist[$tablename][$rawfieldlist[0]];
		}else if($tablename=='article')
		{
			$fieldlist=Array();
			foreach($jointablelist[$tablename] AS $rawfield => $fieldname)
			{
				$fieldlist[]=$fieldname;
				$rawfieldlist[]=$rawfield;
				$alist[]=$db_prefix_bbs.$joinforumlist[$tablename].".".$fieldname." AS ".$rawfield;
			}
			$ajoinconvertlist=",".join(',',$alist);
			$ajoinoption="LEFT JOIN ".$db_prefix_bbs.$joinforumlist[$tablename]." ON ".$db_prefix_bbs.$joinforumlist[$tablename].".".$fieldlist[0]."=".$db_prefix_bbs.$forumlist[$tablename].".".$tablelist[$tablename][$rawfieldlist[0]];
		}
	}
}

extract($configuration,EXTR_OVERWRITE);


$DB_bbs = new DB_MySQL;

$DB_bbs->servername=$servername_bbs;
$DB_bbs->dbname=$dbname_bbs;
$DB_bbs->dbusername=$dbusername_bbs;
$DB_bbs->dbpassword=$dbpassword_bbs;

$DB_bbs->connect();

require "admin/functions_bbs.php";

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
    $_SESSION=array_merge($_SESSION, $pauserinfo);
}
$pauserinfo = $_SESSION;

//print_rr($_SESSION);
//print_rr($pauserinfo);

$onlineusers = $DB->fetch_one_array("SELECT COUNT(DISTINCT ipaddress) AS total FROM ".$db_prefix."session WHERE expiry>".time()."");
$onlineuser += $onlineusers[total];
$htmldir = $bbshtmldir;
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