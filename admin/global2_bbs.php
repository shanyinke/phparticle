<?php
error_reporting(7);


$charset = "utf-8";

require "admin/config.php";
require "admin/class/mysql.php";

$DB = new DB_MySQL;

$DB->servername=$servername;
$DB->dbname=$dbname;
$DB->dbusername=$dbusername;
$DB->dbpassword=$dbpassword;

$DB->connect();
$DB->selectdb();

$startnum=intval($_GET['st']);
if(!$_GET['s'])
{
$sysdata=$DB->fetch_one_array("SELECT tagname FROM " . $db_prefix . "tag WHERE tagname!='defaultsys' GROUP BY tagname LIMIT ".$startnum.",1");
$_GET['s']=$sysdata['tagname'];
}

if($_GET['s'])$loadsystem_suffix=$_GET['s'];
require "admin/loadsystem/config".$loadsystem_suffix.".php";
require "admin/adminfunctions.php";
require "admin/class/pagenav.php";
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

$DB_bbs = new DB_MySQL;

$DB_bbs->servername=$servername_bbs;
$DB_bbs->dbname=$dbname_bbs;
$DB_bbs->dbusername=$dbusername_bbs;
$DB_bbs->dbpassword=$dbpassword_bbs;

$DB_bbs->connect();

require "admin/class/forms.php";
$cpforms = new FORMS;

require "admin/class/session.php";



if (intval(str_replace(".","",phpversion()))<410) {
    cpheader();
    pa_exit("PHP 的版本太低,本程序最低要求是 4.1.0 或以下的版本,当前服务器所安装的版本为 ".phpversion());
}


if (get_magic_quotes_gpc()) {

    $_GET = stripslashes_array($_GET);
    $_POST = stripslashes_array($_POST);
    $_COOKIE = stripslashes_array($_COOKIE);

}

set_magic_quotes_runtime(0);


if (!ini_get("register_globals")) {
    extract($_GET,EXTR_SKIP);
    extract($_POST,EXTR_SKIP);
}

require "admin/configs/setting.php";
extract($configuration,EXTR_OVERWRITE);
$htmldir=$bbshtmldir;
define('HTMLDIR',$htmldir);
define('HTMLEXT',$htmlfileext);
unset($debug);
unset($showqueries);


unset($pauserinfo);
if ($_POST[action]=="login") {

    if (getuser_stat($_POST[username],md5($_POST[password]))) {
        $_SESSION[isadmin] = 1;
        $_SESSION[logined] = 1;
        $_SESSION=array_merge($_SESSION, $pauserinfo);

        cpheader();
        redirect("admin/index.php","登陆成功,请稍候......");
        cpfooter();
    } else {
        loginlog($_POST[username],$_POST[password],"Referer: ".getenv("HTTP_REFERER"));
        displaylogin();
    }
}

if (!$_SESSION[isadmin]) {
    displaylogin();
}
$pauserinfo = $_SESSION;
getlog();
//$debug=1;
?>