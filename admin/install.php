<?php
error_reporting(7);
$debug=1;
$version = "3.0";
$charset = "utf-8";
$install_lock_filename = "./configs/install.lock";
if (file_exists($install_lock_filename)) {
	header("Content-Type: text/html; charset=UTF-8");
	die("安全警告: install.lock 已存在, 即系统已安装, 若需要重新安装, 请先 FTP 删除该文件!");
}
if (function_exists("set_time_limit")==1 and get_cfg_var("safe_mode")==0) {
    @set_time_limit(1200);
}


function stripslashes_array(&$array) {

         while(list($k,$v) = each($array)) {
               if (is_string($v)) {
                   $array[$k] = stripslashes($v);
               }
               if (is_array($v))  {
                   $array[$k] = stripslashes_array($v);
              }

        }
        return $array;

}
if (get_magic_quotes_gpc()) {

        stripslashes_array($_GET);
        stripslashes_array($_POST);
        stripslashes_array($_COOKIE);
}

set_magic_quotes_runtime(0);

if (!ini_get("register_globals")) {
    extract($_GET,EXTR_SKIP);
    extract($_POST,EXTR_SKIP);
}

require "./class/forms.php";
$cpforms = new FORMS;

function pa_exit($text="") {
         global $step;
         echo "<p>$text</p>";
         echo "<p><a href=\"install.php?step=".($step-1)."\">返回上一步</a></p>";
         cpfooter();
         exit;
}
function cpheader($extraheader="",$extraheader1=""){
         global $version;
?>
<html>
<head>
<title>phpArticle 文章管理系统 - Powered by phpArticle <?php echo $version;?></title>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<link rel="stylesheet" href="../admin/cp.css" type="text/css">
</head>
<body bgcolor="#DEDEDE" text="#000000" leftmargin="10" topmargin="10" >
<center>
<!-- start logo -->
<table width="760" border="0" cellspacing="0" cellpadding="3" bgcolor="#FFFFFF">
  <tr>
    <td><a href="./install.php" title="phpArticle 文章管理系统"><img src="../images/logo.gif" border="0"></a></td>
    <td nowrap>
    <p align=right>phpArticle Version <?php echo $version;?> 安装程序</p>
    <b>在安装的过程中,请不要刷新页面.以免安装出错</b>
    </td>
  </tr>
  <tr bgcolor="#EEEEEE">
    <td colspan="2" height="1"></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#F1F1F1">
      <div align="right"><span class="normalfont">
      欢迎使用 phpArticle 文章管理系统
      </span></div>
    </td>
  </tr>
  <tr bgcolor="#505f94">
    <td colspan="2" height="1"></td>
  </tr>
</table>

<!-- end logo -->
<table cellspacing="0" cellpadding="15" width="760" bgcolor="#FFFFFF" border="0">
    <tr>
      <td>
<?
}

function cpfooter(){
         global $version;
?>
             </td>
            </tr>
          </table>
<table width="760" border="0" cellpadding="4" cellspacing="0" bgcolor="#EEEEEE">
  <tr>
    <td width="50%">
      <span class="middlefont">Copyright &copy; 2002 - 2016 <a href="http://http://www.phparticle.cn/">phpArticle 文章管理系统</a><br>
        All rights reserved. </span>
    </td>
    <td width="50%" align="right">
      <span class="middlefont">Powered by: <a href="http://www.phparticle.cn">phpArticle</a>
        <?php echo $version;?></span>
    </td>
  </tr>
</table>
</center>
</body>
</html>
<?php

}

function redirect($url){
         echo "<meta http-equiv=\"refresh\" content=\"1;URL=$url\">";
}

cpheader();

if (intval(str_replace(".","",phpversion()))<410) {
    cpheader();
    pa_exit("PHP 的版本太低,本程序最低要求是 4.1.0 或以下的版本,当前服务器所安装的版本为 ".phpversion());
}


// step one
if (empty($step) or $step==1) {

    $step = 1;
    $cpforms->formheader(array('title'=>'系统信息','colspan'=>3));
    $cpforms->makehidden(array('name'=>'step','value'=>$step+1));
    echo "<tr class=\"tbcat\" id=\"cat\">
           <td></td>
           <td>基本要求</td>
           <td>当前配置</td>
          </tr>";
    $cpforms->maketd(array("PHP Version:","4.1.0 或以上",PHP_VERSION));
    $cpforms->maketd(array("服务器操作系统:","无要求",defined('PHP_OS')?PHP_OS:'未知'));
    $cpforms->maketd(array("安全模式:","关或开",get_cfg_var('safe_mode')?'打开':'关闭'));
    $cpforms->maketd(array("Register Globals:","on 或 off",get_cfg_var('register_globals')?'on':'off'));
    $cpforms->maketd(array("Magic Quotes:","建议 gpc on,runtime on",get_cfg_var('magic_quotes_gpc')?'on':'off'));
    $cpforms->maketd(array("服务器软件:",isset($_SERVER['SERVER_SOFTWARE'])?$_SERVER['SERVER_SOFTWARE']:'unknown',"Apache"));
    $cpforms->formfooter(array('colspan'=>3,
                               'button'=>array('submit'=>array('type'=>'submit',
                                                'name'=>'next',
                                                'value'=>'开始安装',
                                                ))));
}

if ($step==2) {

    require "config.php";
    echo"
		<pre>
		请确认以下目录与文件的属性为0777，而且目录下面的文件属性也要改成0777\n";
	if(!is_writable("../htmldata"))
	{
		echo "<font color=red>htmldata 目录 属性不正确，需设置为0777</font>\n";
	}else echo "<font color=blue>htmldata 目录 检测通过</font>\n";
	if(!is_writable("../backup"))
	{
		echo "<font color=red>backup 目录 属性不正确，需设置为0777</font>\n";
	}else echo "<font color=blue>backup 目录 检测通过</font>\n";
	if(!is_writable("../templates/default/html"))
	{
		echo "<font color=red>templates/default/html 目录 属性不正确，需设置为0777</font>\n";
	}else echo "<font color=blue>templates/default/html 目录 检测通过</font>\n";
	if(!is_writable("../modules/default"))
	{
		echo "<font color=red>modules/default 目录 属性不正确，需设置为0777</font>\n";
	}else echo "<font color=blue>modules/default 目录 检测通过</font>\n";
	if(!is_writable("../admin/config.php"))
	{
		echo "<font color=red>admin/config.php 属性不正确，需设置为0777</font>\n";
	}else echo "<font color=blue>admin/config.php 检测通过</font>\n";
	if(!is_writable("../admin/configs"))
	{
		echo "<font color=red>admin/configs 属性不正确，需设置为0777</font>\n";
	}else echo "<font color=blue>admin/configs 检测通过</font>\n";
	if(!is_writable("../admin/style"))
	{
		echo "<font color=red>admin/style 属性不正确，需设置为0777</font>\n";
	}else echo "<font color=blue>admin/style 检测通过</font>\n";
	if(!is_writable("../admin/loadsystem"))
	{
		echo "<font color=red>admin/loadsystem 属性不正确，需设置为0777</font>\n";
	}else echo "<font color=blue>admin/loadsystem 检测通过</font>\n";
	if(!is_writable("../upload/images"))
	{
		echo "<font color=red>upload/images 属性不正确，需设置为0777</font>\n";
	}else echo "<font color=blue>upload/images 检测通过</font>\n";
	echo"</pre>";
    $cpforms->formheader(array('title'=>'设置数据库','colspan'=>3,));
    $cpforms->makehidden(array('name'=>'step','value'=>$step+1));
    echo "<tr class=\"tbcat\" id=\"cat\">
           <td width=\"40%\"></td>
           <td width=\"30%\">基本设置</td>
           <td width=\"30%\">范例</td>
          </tr>";
    $cpforms->maketd(array("服务器地址:<br>一般为localhost",
                            "<input type=\"text\" value=\"".htmlspecialchars($servername)."\" name=\"servername\">",
                            "localhost"));
    $cpforms->maketd(array("数据库名:",
                            "<input type=\"text\" value=\"".htmlspecialchars($dbname)."\" name=\"dbname\">",
                            "phpArticle"));
    $cpforms->maketd(array("数据库用户名:",
                            "<input type=\"text\" value=\"".htmlspecialchars($dbusername)."\" name=\"dbusername\">",
                            "root"));
    $cpforms->maketd(array("数据库用户密码:",
                            "<input type=\"password\" value=\"".htmlspecialchars($dbpassword)."\" name=\"dbpassword\">",
                            "******"));
    $cpforms->maketd(array("技术支持邮箱:",
                            "<input type=\"text\" value=\"".htmlspecialchars($technicalemail)."\" name=\"technicalemail\">",
                            "technical@phparticle.cn"));
    $cpforms->maketd(array("表前缀:",
                            "<input type=\"text\" value=\"".htmlspecialchars($db_prefix)."\" name=\"db_prefix\">",
                            "pa_"));
/*	$mysqlversion=explode('-',mysql_get_server_info());
		if($mysqlver<='4.1.11')$dbcharset='latin1';
	$cpforms->maketd(array("mysql版本:",
                            "<input type=\"text\" value=\"".$mysqlversion[0]."\" name=\"mysqlver\">",
                            '4.1.11'));*/
	$cpforms->makehidden(array('name'=>'dbcharset','value'=>$dbcharset));

    $cpforms->formfooter(array('colspan'=>3,
                               'button'=>array('submit'=>array('type'=>'submit',
                                                'name'=>'next',
                                                'value'=>'确认并继续下一步',
                                                ))));

}

// step three
if($step==3){
   if(trim($dbname)=="" or trim($servername)=="" or trim($dbusername=="")){
      pa_exit("请返回并确认所有选项均已正确填写");
   }
   $file = "./config.php";

	$link = @mysql_connect($dbhost,$dbuser,$dbpasswd);
	if($link)
	{
		$mysqlversion=explode('-',@mysql_get_server_info());
		@mysql_close($link);
		$mysqlver = $mysqlversion[0];
		if($mysqlversion[0]<='4.1.11')$dbcharset='latin1';
	}
   if (file_exists($file)){
      @chmod ($file, 0777);
   }

   $fp = fopen($file,w);
   $text = "<?php
// 服务器名或服务器ip
\$servername='$servername';
// 数据库用户与密码
\$dbusername='$dbusername';
\$dbpassword='$dbpassword';
// 数据库名
\$dbname='$dbname';
// 技术支持邮箱
\$technicalemail='$technicalemail';

\$usepconnect=0;
// 数据库表名前缀
\$db_prefix='$db_prefix';
//调用其他系统的数据库所定义的后缀
\$loadsystem_suffix='_bbs';
//mysql版本
\$mysqlver='$mysqlver';
\$dbcharset='$dbcharset';
?>";
	require "config.php";
   fwrite($fp,$text,strlen($text));
   fclose($fp);


   $link = @mysql_connect($servername,$dbusername,$dbpassword);
   if ($link) {
       echo "<p>数据库服务器连接成功</p>";
       if (@mysql_select_db($dbname)) {
		   mysql_query("SET NAMES 'utf8'");
           echo "<p><a href=\"./install.php?step=".($step+1)."&delete_existing=1\">继续下一步(删除已存在的表)</a></p>";
           echo "<p><a href=\"./install.php?step=".($step+1)."&delete_existing=0\">继续下一步(不删除已存在的表)</a></p>";
       } else {
           echo "<p>正尝试创建数据库 $dbname</p>";

           if (@mysql_query("CREATE DATABASE $dbname  DEFAULT CHARACTER SET utf8 COLLATE utf8_chinese_ci;",$link)) {
			   mysql_query("SET NAMES 'utf8'");
               echo "<p>数据库创建成功</p>";
               echo "<p><a href=\"./install.php?step=".($step+1)."&delete_existing=1\">继续下一步(删除已存在的表)</a></p>";
               echo "<p><a href=\"./install.php?step=".($step+1)."&delete_existing=0\">继续下一步(不删除已存在的表)</a></p>";
           } else {
               echo "<p>数据库创建失败</p>";
               echo "<p><a href=\"./install.php?step=".($step-1)."\">返回上一步</a></p>";
           }
       }

   } else {
       echo "<p>数据库服务器连接失败</p>";
   }
   @mysql_close($link);
}

// step 4 connect db
if($step>=4){

   //$delete_existing = 1;
   //$debug=1;
   require "config.php";
   require "class/mysql.php";

   $DB = new DB_MySQL;

   $DB->servername=$servername;
   $DB->dbname=$dbname;
   $DB->dbusername=$dbusername;
   $DB->dbpassword=$dbpassword;
	$DB->mysqlver=$mysqlver;
	$DB->dbcharset=$dbcharset;
   $DB->connect();
   $DB->selectdb();

}
// 24 tables

// step creat tables
if($step==4){

   $mysql_data = "
# MySQL-Front Dump 2.5
#
# Host: localhost   Database: article
# --------------------------------------------------------
# Server version 3.23.53-max-nt


#
# Table structure for table '".$db_prefix."adminlog'
#

CREATE TABLE ".$db_prefix."adminlog (
  adminlogid int(10) unsigned NOT NULL auto_increment,
  userid int(3) unsigned NOT NULL default '0',
  action varchar(50) default NULL,
  script varchar(255) NOT NULL default '',
  date int(10) unsigned NOT NULL default '0',
  ipaddress varchar(16) default NULL,
  PRIMARY KEY  (adminlogid),
  KEY userid (userid),
  KEY date (date)
)ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."article'
#

CREATE TABLE `".$db_prefix."article` (
  `articleid` int(10) unsigned NOT NULL auto_increment,
  `sortid` int(10) unsigned NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `source` varchar(50) default NULL,
  `author` varchar(50) default NULL,
  `contact` varchar(50) default NULL,
  `description` text NOT NULL,
  `views` int(10) unsigned NOT NULL default '0',
  `date` int(10) unsigned NOT NULL default '0',
  `totalscore` int(10) unsigned NOT NULL default '0',
  `voters` int(10) unsigned NOT NULL default '0',
  `imageid` int(10) unsigned NOT NULL default '0',
  `lastupdate` int(10) default '0',
  `editor` varchar(50) default NULL,
  `userid` int(10) unsigned NOT NULL default '0',
  `visible` tinyint(1) unsigned NOT NULL default '1',
  `keyword` varchar(100) default NULL,
  `comments` int(10) unsigned NOT NULL default '0',
  `highlight` tinyint(1) NOT NULL default '0',
  `expiry` int(10) NOT NULL default '0',
  PRIMARY KEY  (`articleid`),
  KEY `date` (`date`),
  KEY `userid` (`userid`),
  KEY `title` (`title`),
  KEY `views` (`views`),
  KEY `visible` (`visible`),
  KEY `sortid` (`sortid`),
  KEY `lastupdate` (`lastupdate`)
)ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."articlerate'
#

CREATE TABLE `".$db_prefix."articlerate` (
  `articlerateid` int(10) unsigned NOT NULL auto_increment,
  `articleid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `date` int(10) unsigned NOT NULL default '0',
  `vote` tinyint(2) unsigned NOT NULL default '0',
  `reason` mediumtext,
  `ip` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`articlerateid`),
  KEY `userid` (`userid`),
  KEY `articleid` (`articleid`),
  KEY `ip` (`ip`)
)ENGINE=MyISAM;


#
# Table structure for table '".$db_prefix."articletext'
#

CREATE TABLE ".$db_prefix."articletext (
  id int(10) unsigned NOT NULL auto_increment,
  subhead varchar(100) NOT NULL default '',
  articleid int(10) unsigned NOT NULL default '0',
  articletext text NOT NULL,
  displayorder tinyint(3) NOT NULL default '1',
  PRIMARY KEY  (id),
  KEY articleid (articleid),
  KEY displayorder (displayorder)
)ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."cache'
#

CREATE TABLE ".$db_prefix."cache (
  name varchar(50) NOT NULL default '',
  content longtext,
  expiry tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (name)
)ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."comment'
#

CREATE TABLE ".$db_prefix."comment (
  commentid int(10) unsigned NOT NULL auto_increment,
  articleid int(10) unsigned NOT NULL default '0',
  title varchar(100) NOT NULL default '',
  author varchar(50) NOT NULL default '',
  userid int(10) unsigned NOT NULL default '0',
  date int(10) unsigned NOT NULL default '0',
  views int(10) NOT NULL default '0',
  replies int(10) unsigned NOT NULL default '0',
  lastupdate int(10) unsigned NOT NULL default '0',
  lastreplier varchar(50) NOT NULL default '',
  PRIMARY KEY  (commentid),
  KEY articleid (articleid,userid,date,views,replies,lastupdate)
)ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."favorite'
#

CREATE TABLE ".$db_prefix."favorite (
  favoriteid smallint(5) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  articleid int(10) unsigned NOT NULL default '0',
  adddate int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (favoriteid)
)ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."gallery'
#

CREATE TABLE ".$db_prefix."gallery (
  id smallint(5) unsigned NOT NULL auto_increment,
  original varchar(100) NOT NULL default '',
  filename varchar(50) NOT NULL default '',
  type varchar(50) NOT NULL default '',
  size smallint(5) unsigned NOT NULL default '0',
  dateline varchar(50) NOT NULL default '0',
  userid int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
)ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."loginlog'
#

CREATE TABLE ".$db_prefix."loginlog (
  loginlogid int(10) unsigned NOT NULL auto_increment,
  username varchar(100) default NULL,
  password varchar(100) default NULL,
  date int(10) unsigned NOT NULL default '0',
  ipaddress varchar(16) NOT NULL default '',
  extra text,
  PRIMARY KEY  (loginlogid)
)ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."manager'
#

CREATE TABLE ".$db_prefix."manager (
  managerid smallint(5) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  sortid int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (managerid)
)ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."message'
#

CREATE TABLE ".$db_prefix."message (
  messageid int(10) unsigned NOT NULL auto_increment,
  commentid int(10) unsigned NOT NULL default '0',
  userid int(10) unsigned NOT NULL default '0',
  author varchar(50) NOT NULL default '',
  parentid int(10) NOT NULL default '0',
  title varchar(100) NOT NULL default '',
  message text NOT NULL,
  date int(10) unsigned NOT NULL default '0',
  ipaddress varchar(16) NOT NULL default '',
  removed tinyint(1) unsigned NOT NULL default '0',
  lastupdate int(10) NOT NULL default '0',
  lastupdater varchar(255) NOT NULL default '',
  PRIMARY KEY  (messageid),
  KEY parentid (parentid),
  KEY userid (userid),
  KEY commentid (commentid),
  KEY removed (removed)
)ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."news'
#

CREATE TABLE ".$db_prefix."news (
  newsid int(10) unsigned NOT NULL auto_increment,
  userid int(10) NOT NULL default '0',
  title varchar(100) NOT NULL default '',
  content text NOT NULL,
  startdate int(10) NOT NULL default '0',
  enddate int(10) NOT NULL default '0',
  PRIMARY KEY  (newsid),
  KEY userid (userid),
  KEY startdate (startdate),
  KEY enddate (enddate)
)ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."relatedlink'
#

CREATE TABLE ".$db_prefix."relatedlink (
  relatedlinkid int(10) unsigned NOT NULL auto_increment,
  articleid int(10) unsigned NOT NULL default '0',
  text varchar(100) NOT NULL default '',
  link varchar(255) NOT NULL default '',
  PRIMARY KEY  (relatedlinkid),
  KEY articleid (articleid)
)ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."replacement'
#

CREATE TABLE ".$db_prefix."replacement (
  replacementid int(10) unsigned NOT NULL auto_increment,
  replacementsetid int(11) NOT NULL default '0',
  findword text,
  replaceword text,
  title varchar(100) NOT NULL default '',
  description text,
  type varchar(100) NOT NULL default '',
  PRIMARY KEY  (replacementid)
)ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."replacementset'
#

CREATE TABLE ".$db_prefix."replacementset (
  replacementsetid int(10) unsigned NOT NULL auto_increment,
  title char(250) NOT NULL default '',
  PRIMARY KEY  (replacementsetid)
)ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."session'
#

CREATE TABLE `".$db_prefix."session` (
  `sessionid` varchar(32) NOT NULL default '',
  `userid` int(10) unsigned NOT NULL default '0',
  `useragent` varchar(255) NOT NULL default '',
  `ipaddress` varchar(16) NOT NULL default '',
  `lastactivity` int(10) unsigned NOT NULL default '0',
  `location` varchar(255) NOT NULL default '',
  `expiry` int(10) unsigned NOT NULL default '0',
  `value` text,
  PRIMARY KEY  (`sessionid`),
  KEY `expiry` (`expiry`),
  KEY `ipaddress` (`ipaddress`)
)ENGINE=MyISAM;


#
# Table structure for table '".$db_prefix."setting'
#

CREATE TABLE ".$db_prefix."setting (
  settingid int(10) unsigned NOT NULL auto_increment,
  settinggroupid int(10) unsigned NOT NULL default '0',
  title varchar(100) NOT NULL default '',
  name varchar(100) NOT NULL default '',
  value mediumtext,
  type varchar(100) NOT NULL default '',
  displayorder int(10) unsigned NOT NULL default '0',
  description text,
  PRIMARY KEY  (settingid),
  KEY displayorder (displayorder)
)ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."settinggroup'
#

CREATE TABLE ".$db_prefix."settinggroup (
  settinggroupid int(10) unsigned NOT NULL auto_increment,
  title varchar(100) NOT NULL default '',
  displayorder tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (settinggroupid),
  KEY displayorder (displayorder)
)ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."sort'
#

CREATE TABLE `".$db_prefix."sort` (
  `sortid` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(50) NOT NULL default '',
  `description` varchar(250) NOT NULL default '',
  `displayorder` int(10) unsigned NOT NULL default '1',
  `parentid` int(11) NOT NULL default '-1',
  `img` varchar(100) default NULL,
  `articlecount` int(10) unsigned NOT NULL default '0',
  `showinrecent` tinyint(3) unsigned NOT NULL default '1',
  `showinhot` tinyint(3) unsigned NOT NULL default '1',
  `showinrate` tinyint(3) unsigned NOT NULL default '1',
  `showinlast` tinyint(3) unsigned NOT NULL default '1',
  `parentlist` varchar(255) NOT NULL default '',
  `division_sort` tinyint(1) unsigned NOT NULL default '3',
  `division_article` tinyint(1) unsigned NOT NULL default '1',
  `perpage` tinyint(3) unsigned NOT NULL default '10',
  `showsortinfos` tinyint(1) unsigned NOT NULL default '0',
  `styleid` int(10) unsigned NOT NULL default '0',
  `ratearticlenum` tinyint(3) unsigned NOT NULL default '10',
  `hotarticlenum` tinyint(3) unsigned NOT NULL default '10',
  `dirname` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`sortid`),
  KEY `displayorder` (`displayorder`)
)ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."style'
#

CREATE TABLE ".$db_prefix."style (
  styleid int(10) unsigned NOT NULL auto_increment,
  replacementsetid int(10) unsigned NOT NULL default '0',
  templatesetid int(10) unsigned NOT NULL default '0',
  title char(250) NOT NULL default '',
  PRIMARY KEY  (styleid)
)ENGINE=MyISAM;


#
# Table structure for table '".$db_prefix."templateset'
#

CREATE TABLE `".$db_prefix."templateset` (
  templatesetid int(10) unsigned NOT NULL auto_increment,
  title char(250) NOT NULL default '',
  PRIMARY KEY  (templatesetid)
)ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."user'
#

CREATE TABLE `".$db_prefix."user` (
  `userid` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(50) NOT NULL default '',
  `usergroupid` int(10) unsigned NOT NULL default '0',
  `password` varchar(50) NOT NULL default '',
  `radompassword` varchar(32) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `joindate` int(10) unsigned NOT NULL default '0',
  `homepage` varchar(100) default NULL,
  `sex` varchar(10) NOT NULL default 'unknow',
  `address` varchar(250) default NULL,
  `qq` varchar(16) default NULL,
  `icq` varchar(16) default NULL,
  `msn` varchar(50) default NULL,
  `intro` text,
  `tel` varchar(20) default NULL,
  `rememberpw` tinyint(1) unsigned NOT NULL default '1',
  `posts` int(10) unsigned NOT NULL default '0',
  `lastvisit` int(10) unsigned NOT NULL default '0',
  `lastactivity` int(10) unsigned NOT NULL default '0',
  `timezoneoffset` float NOT NULL default '8',
  `regip` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`userid`),
  KEY `usergroupid` (`usergroupid`)
) ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."useractivation'
#

CREATE TABLE ".$db_prefix."useractivation (
  useractivationid int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  time int(10) unsigned NOT NULL default '0',
  activationcode varchar(20) NOT NULL default '',
  PRIMARY KEY  (useractivationid),
  KEY userid (userid)
)ENGINE=MyISAM;



#
# Table structure for table '".$db_prefix."usergroup'
#

CREATE TABLE `".$db_prefix."usergroup` (
  `usergroupid` smallint(5) NOT NULL auto_increment,
  `title` varchar(50) NOT NULL default '',
  `isadmin` tinyint(1) unsigned NOT NULL default '0',
  `ismanager` tinyint(1) unsigned NOT NULL default '0',
  `canaddarticle` tinyint(1) unsigned NOT NULL default '0',
  `caneditarticle` tinyint(1) unsigned NOT NULL default '0',
  `canremovearticle` tinyint(1) unsigned NOT NULL default '0',
  `canaddnews` tinyint(1) unsigned NOT NULL default '0',
  `caneditnews` tinyint(1) unsigned NOT NULL default '0',
  `canremovenews` tinyint(1) unsigned NOT NULL default '0',
  `canaddsort` tinyint(1) unsigned NOT NULL default '0',
  `caneditsort` tinyint(1) unsigned NOT NULL default '0',
  `canremovesort` tinyint(1) unsigned NOT NULL default '0',
  `canviewarticle` tinyint(1) unsigned NOT NULL default '1',
  `canratearticle` tinyint(1) unsigned NOT NULL default '0',
  `canviewcomment` tinyint(1) unsigned NOT NULL default '1',
  `cancomment` tinyint(1) unsigned NOT NULL default '0',
  `cancontribute` tinyint(1) unsigned NOT NULL default '0',
  `onedaypostmax` tinyint(3) NOT NULL default '0',
  `postoptions` int(10) NOT NULL default '0',
  PRIMARY KEY  (`usergroupid`)
)ENGINE=MyISAM;

CREATE TABLE `".$db_prefix."htmllog` (
  `htmllogid` int(10) unsigned NOT NULL auto_increment,
  `type` mediumint(5) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `startid` int(10) unsigned NOT NULL default '0',
  `pagenum` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`htmllogid`),
  KEY `startid` (`startid`),
  KEY `dateline` (`dateline`)
)ENGINE=MyISAM;

CREATE TABLE `".$db_prefix."cache_bbs` (
  `name` varchar(50) NOT NULL default '',
  `content` longtext,
  `expiry` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`name`)
)ENGINE=MyISAM;

CREATE TABLE `".$db_prefix."htmllog_bbs` (
  `htmllogid` int(10) unsigned NOT NULL auto_increment,
  `type` mediumint(5) unsigned NOT NULL default '0',
  `bbs` varchar(50) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `startid` int(10) unsigned NOT NULL default '0',
  `pagenum` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`htmllogid`),
  KEY `bbs` (`bbs`),
  KEY `startid` (`startid`),
  KEY `dateline` (`dateline`)
)ENGINE=MyISAM;

CREATE TABLE `".$db_prefix."tag` (
  `tagid` int(10) NOT NULL auto_increment,
  `tagname` varchar(50) NOT NULL default '',
  `locate` varchar(50) NOT NULL default '',
  `contenttype` varchar(50) NOT NULL default '',
  `type` varchar(50) NOT NULL default '',
  `sortid` varchar(25) NOT NULL default '',
  `maxarticles` int(10) NOT NULL default '0',
  `titlelen` int(10) NOT NULL default '0',
  `templatename` varchar(255) NOT NULL default '',
  `renew` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`tagid`),
  KEY `tagname` (`tagname`),
  KEY `renew` (`renew`)
)ENGINE=MyISAM;

CREATE TABLE `".$db_prefix."friendlink` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `displayorder` tinyint(3) NOT NULL default '0',
  `sitename` varchar(100) NOT NULL default '',
  `note` varchar(200) NOT NULL default '',
  `siteurl` varchar(100) NOT NULL default '',
  `isimg` tinyint(1) NOT NULL default '0',
  `logourl` varchar(100) NOT NULL default '',
  `visible` tinyint(1) unsigned NOT NULL default '1',
  `editor` varchar(50) NOT NULL default '',
  `request` text NOT NULL,
  `reason` text NOT NULL,
  `jointime` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `displayorder` (`displayorder`)
)ENGINE=MyISAM;

#
# Dumping data for table 'pa_setting'
#

INSERT INTO `".$db_prefix."setting` VALUES('1', '1', '主页地址', 'homepage', '', 'string', '3', '');
INSERT INTO `".$db_prefix."setting` VALUES('2', '1', '网站标题', 'phparticletitle', '', 'string', '2', '');
INSERT INTO `".$db_prefix."setting` VALUES('3', '1', '首页地址', 'phparticleurl', '', 'string', '1', '文章系统所安装的地址,结尾不必加斜杆\'/\'.');
INSERT INTO `".$db_prefix."setting` VALUES('4', '2', '是否显示模板注释?', 'showcomment', '0', 'boolean', '1', '方便调试界面.');
INSERT INTO `".$db_prefix."setting` VALUES('5', '1', '管理员Email地址', 'webmastermail', '', 'string', '4', '');
INSERT INTO `".$db_prefix."setting` VALUES('6', '1', '版本号', 'version', '2.1', 'string', '0', '');
INSERT INTO `".$db_prefix."setting` VALUES('7', '3', '是否显示最近更新文章列表(详细)?', 'showrecentarticle', '1', 'boolean', '1', '');
INSERT INTO `".$db_prefix."setting` VALUES('8', '3', '显示多少篇最近更新的文章?', 'recentarticlenum', '10', 'integer', '2', '');
INSERT INTO `".$db_prefix."setting` VALUES('9', '3', '分多少列显示最近更新的文章?', 'recentarticledivision', '2', 'integer', '3', '');
INSERT INTO `".$db_prefix."setting` VALUES('10', '3', '是否显示热门文章排行(按评分排行)?', 'showratearticle', '1', 'boolean', '4', '');
INSERT INTO `".$db_prefix."setting` VALUES('11', '3', '显示多少篇?', 'ratearticlenum', '10', 'integer', '5', '');
INSERT INTO `".$db_prefix."setting` VALUES('12', '3', '是否显示热门文章列表(按点击排行)?', 'showhotarticle', '1', 'boolean', '6', '');
INSERT INTO `".$db_prefix."setting` VALUES('13', '3', '显示多少篇?', 'hotarticlenum', '10', 'integer', '7', '');
INSERT INTO `".$db_prefix."setting` VALUES('14', '3', '是否显示最后更新文章列表(简单)?', 'showlastupdate', '1', 'boolean', '8', '');
INSERT INTO `".$db_prefix."setting` VALUES('15', '3', '显示多少篇?', 'lastupdatenum', '10', 'integer', '9', '');
INSERT INTO `".$db_prefix."setting` VALUES('16', '4', '是否显示评分结果?', 'showrating', '1', 'boolean', '1', '');
INSERT INTO `".$db_prefix."setting` VALUES('17', '5', '是否允许新会员注册?', 'allowregister', '1', 'boolean', '1', '');
INSERT INTO `".$db_prefix."setting` VALUES('18', '5', '会员名的最小长度(单位:字符)?', 'username_length_min', '4', 'integer', '3', '');
INSERT INTO `".$db_prefix."setting` VALUES('19', '5', '会员名的最大长度(单位:字符)?', 'username_length_max', '15', 'integer', '4', '');
INSERT INTO `".$db_prefix."setting` VALUES('20', '5', '密码的最小长度(单位:字符)?', 'password_length_min', '4', 'integer', '5', '');
INSERT INTO `".$db_prefix."setting` VALUES('21', '5', '密码的最大长度(单位:字符)?', 'password_length_max', '15', 'integer', '6', '');
INSERT INTO `".$db_prefix."setting` VALUES('22', '5', '最多可以收藏多少篇文章?', 'favoritelimit', '100', 'integer', '7', '');
INSERT INTO `".$db_prefix."setting` VALUES('23', '6', '每页显示多少个搜索结果?', 'searchperpage', '10', 'integer', '1', '');
INSERT INTO `".$db_prefix."setting` VALUES('24', '7', '是否使用Gzip压缩页面?', 'gzipoutput', '0', 'boolean', '1', '这样可以加速页面的显示和减少带宽的使用,但同时也会增加服务器的负担.');
INSERT INTO `".$db_prefix."setting` VALUES('25', '7', '页面压缩的级别', 'gziplevel', '3', 'integer', '2', 'Max:9,Min:1');
INSERT INTO `".$db_prefix."setting` VALUES('26', '7', '服务器所在的时区', 'timezone', '8', 'integer', '3', '');
INSERT INTO `".$db_prefix."setting` VALUES('27', '8', '新闻时间格式', 'dateformat_news', 'Y-m-d h:i', 'string', '1', '时间显示格式的及意义,请参看 <a href=\'http://www.php.net/manual/en/function.date.php\' target=\'_blank\'>date()</a>.');
INSERT INTO `".$db_prefix."setting` VALUES('28', '8', '文章日期格式', 'dateformat_article', 'Y-m-d', 'string', '2', '');
INSERT INTO `".$db_prefix."setting` VALUES('29', '5', '是否需要通过email验证会员身份?', 'require_activation', '0', 'boolean', '2', '如果选择\'是\',游客注册后,系统会自动发发送帐号激活email给他们,提示他们如何激活会员帐号.');
INSERT INTO `".$db_prefix."setting` VALUES('30', '8', '文章时间格式', 'timeformat_article', 'h:i A', 'string', '3', '');
INSERT INTO `".$db_prefix."setting` VALUES('31', '9', '评论标题最大长度(单位:字符)?', 'comment_title_limit', '50', 'integer', '1', '');
INSERT INTO `".$db_prefix."setting` VALUES('32', '9', '评论内容最大长度(单位:字符)?', 'comment_message_limit', '400', 'integer', '2', '');
INSERT INTO `".$db_prefix."setting` VALUES('33', '10', '静态页存放目录', 'htmldir', 'htmldata', 'string', '1', '');
INSERT INTO `".$db_prefix."setting` VALUES('34', '10', '静态文件后缀名', 'htmlfileext', 'html', 'string', '2', '');
INSERT INTO `".$db_prefix."setting` VALUES('35', '10', '静态文件名生成方法', 'filenamemethod', '2', 'integer', '3', '1代表文件名不优化，2代表文件名英文/yahoo搜索引擎优化，3百度优化');
INSERT INTO `".$db_prefix."setting` VALUES('36', '10', '文章静态页默认前缀', 'articleprefix', 'article_', 'string', '4', '');
INSERT INTO `".$db_prefix."setting` VALUES('37', '10', '分类静态页前缀', 'sortprefix', 'sort_', 'string', '5', '');
INSERT INTO `".$db_prefix."setting` VALUES('38', '3', '分类文章调用数量', 'main_article', '10', 'integer', '0', '首页每个分类调用的文章数量');
INSERT INTO `".$db_prefix."setting` VALUES('39', '3', '文章调用标题长度', 'main_len', '20', 'integer', '0', '首页每篇文章的标题长度');
INSERT INTO `".$db_prefix."setting` VALUES('40', '3', '图片文章长度', 'img_article_len', '20', 'integer', '0', '图片文章调用标题，和摘要长度');
INSERT INTO `".$db_prefix."setting` VALUES('41', '3', '图片文章数量', 'img_article', '5', 'integer', '0', '图片文章调用的条数');
INSERT INTO `".$db_prefix."setting` VALUES ('42', '10', '使用日期作为目录', 'usedate', '1', 'boolean', '6', '是否在静态文件目录后面添加一个日期作为子目录。');
INSERT INTO `".$db_prefix."setting` VALUES ('43', '10', '使用分类名字作为目录', 'usename', '1', 'boolean', '7', '该分类名可以自己修改，默认为分类名的拼音');
INSERT INTO `".$db_prefix."setting` VALUES ('44', '10', '单一子目录', 'singledir', '2', 'integer', '8', '=1使用所在分类的分类名作为唯一子目录。=2，没有子目录');

#
# Dumping data for table '".$db_prefix."settinggroup'
#

INSERT INTO `".$db_prefix."settinggroup` VALUES('1', '基本设置', '1');
INSERT INTO `".$db_prefix."settinggroup` VALUES('2', '模板设置', '2');
INSERT INTO `".$db_prefix."settinggroup` VALUES('3', '首页显示设置', '3');
INSERT INTO `".$db_prefix."settinggroup` VALUES('4', '文章设置', '4');
INSERT INTO `".$db_prefix."settinggroup` VALUES('5', '会员选项设置', '6');
INSERT INTO `".$db_prefix."settinggroup` VALUES('6', '搜索选项设置', '7');
INSERT INTO `".$db_prefix."settinggroup` VALUES('7', '服务器设置', '8');
INSERT INTO `".$db_prefix."settinggroup` VALUES('8', '时间显示格式设置', '9');
INSERT INTO `".$db_prefix."settinggroup` VALUES('9', '评论设置', '5');
INSERT INTO `".$db_prefix."settinggroup` VALUES('10', '静态生成设置', '10');

#
# Dumping data for table '".$db_prefix."usergroup'
#

INSERT INTO `".$db_prefix."usergroup` VALUES('1', '超级管理员', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0', '0','1','1','1','1','1',0,0);
INSERT INTO `".$db_prefix."usergroup` VALUES('2', '普通管理员', '0', '1', '1', '1', '1', '0', '0', '0', '1', '1', '1','1','1','1','1','1',0,0);
INSERT INTO `".$db_prefix."usergroup` VALUES('3', '一般会员', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0','1','1','1','1','1',0,0);
INSERT INTO `".$db_prefix."usergroup` VALUES('4', '游客', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0','1','0','1','0','0',0,0);
INSERT INTO `".$db_prefix."usergroup` VALUES('5', '等待email激活会员', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0','1','0','1','0','0',0,0);

INSERT INTO `".$db_prefix."friendlink` VALUES ('1', 0, 'PAHTML官方', 'phpArticle 官方站', 'http://www.phparticle.net', 0, 'http://', 1, 'niuboy', '尊敬的 niuboy 站长:', '', 1150480937);
";

    $cpforms->formheader(array('title'=>'正在建立表','action'=>'install.php','colspan'=>3));
    $cpforms->makehidden(array('name'=>'step','value'=>$step+1));

    $a_query = explode(";",$mysql_data);
    while (list(,$query) = each($a_query)) {
           $query = trim($query);
           if ($query) {
               if (strstr($query,'CREATE TABLE')) {
                   ereg('CREATE TABLE ([^ ]*)',$query,$regs);
                   if ($delete_existing) {
                       $DB->query("DROP TABLE IF EXISTS $regs[1]");
                   }
                   $cpforms->maketd(array("正在建立表: $regs[1] ...",$DB->query($query)?"成功":"失败"));
               } else {
                   $DB->query($query);
               }

           }
    }

    $cpforms->formfooter(array('colspan'=>3,
                               'button'=>array('submit'=>array('type'=>'submit',
                                                'name'=>'next',
                                                'value'=>'继续安装',
                                                ))));


}

// step 5 import style
if ($step==5) {

    if (!is_writable("../modules/default")) {
        pa_exit("modules/default目录不可写，请设置属性为0777.");
    }
	$cpforms->formheader(array('title'=>'模板缓冲...'));
	require_once "./makefile.php";
	chdir("../templates/default");
	$handle=opendir("html");
	while ($file = readdir($handle)) 
	{
		if (is_file("html/".$file)&&!strchr($file,'_'))//||strstr($file,'_bbs'
		{
			$mod = substr($file,0,strpos($file,"."));
		}else{
			continue;
		}
		$cpforms->maketd(array("正在缓冲".$mod."模板...","成功"));
		compile_module_file($mod, "../../modules/default/", "../../templates/default/html", "../../templates/default/src");
	}
	$cpforms->maketd(array("正在缓冲mkarticle_bbs模板...","成功"));
	compile_module_file("mkarticle_bbs", "../../modules/default/", "../../templates/default/html", "../../templates/default/src");
	$cpforms->maketd(array("正在缓冲functions_bbs模板...","成功"));
	compile_module_file("functions_bbs", "../../modules/default/", "../../templates/default/html", "../../templates/default/src");
	$cpforms->maketd(array("正在缓冲mksort_bbs模板...","成功"));
	compile_module_file("mksort_bbs", "../../modules/default/", "../../templates/default/html", "../../templates/default/src");
	$cpforms->maketd(array("正在缓冲comment_add模板...","成功"));
	compile_module_file("comment_add", "../../modules/default/", "../../templates/default/html", "../../templates/default/src");
	closedir($handle);
	chdir("../../admin");
	$DB->query("DELETE FROM ".$db_prefix."style");
    $DB->query("INSERT INTO ".$db_prefix."replacementset (title) VALUES ('default')");
    $DB->query("INSERT INTO ".$db_prefix."templateset (title) VALUES ('default')");
    $DB->query("INSERT INTO ".$db_prefix."style (title,replacementsetid,templatesetid) VALUES ('default',1,1)");
    $cpforms->makehidden(array('name'=>'step','value'=>$step+1));
    $cpforms->formfooter(array('button'=>array('submit'=>array('type'=>'submit',
                                                'name'=>'next',
                                                'value'=>'继续安装',
                                                ))));


}

if ($step==6) {

    $cpforms->formheader(array('title'=>'管理员帐号'));
    $cpforms->makehidden(array('name'=>'step','value'=>$step+1));
    $cpforms->makeinput(array('text'=>'用户名(管理员):',
                               'name'=>'username'));
    $cpforms->makeinput(array('text'=>'用户密码:',
                               'name'=>'password',
                               'type'=>'password'));
    $cpforms->makeinput(array('text'=>'确认用户密码:',
                               'name'=>'password2',
                               'type'=>'password'));
    $cpforms->makeinput(array('text'=>'email:',
                               'name'=>'email'));

    $cpforms->makecategory("网站设置");
    $cpforms->makeinput(array('text'=>'网站标题:',
                               'name'=>'phparticletitle'));
    $cpforms->makeinput(array('text'=>'首页地址:',
                               'name'=>'phparticleurl',
                               'value'=>"http://$_SERVER[SERVER_NAME]".substr($_SERVER[PHP_SELF],0,strpos($_SERVER[PHP_SELF],"/admin/"))
                               ));
    $cpforms->makeinput(array('text'=>'主页:',
                               'name'=>'homepage',
                               'value'=>"http://$_SERVER[SERVER_NAME]"
                               ));

    $cpforms->formfooter(array('button'=>array('submit'=>array('type'=>'submit',
                                                'name'=>'next',
                                                'value'=>'确认并完成安装',
                                                ))));


}

if ($step==7) {

    if (trim($username)=="" OR trim($password)=="" OR trim($email)=="") {
        pa_exit("请返回并输入所有选项");
    }

    if ($password!=$password2) {
        pa_exit("两个输入的密码不相同,请返回重新输入");
    }

    if ($_POST[email]=="") {
        pa_exit("请返回并输入 email 地址");
    }

    $DB->query("INSERT INTO ".$db_prefix."user (username,password,usergroupid,email,joindate)
                       VALUES ('".addslashes(htmlspecialchars(trim($_POST[username])))."','".md5(trim($_POST[password]))."','1','".addslashes($_POST[email])."','".time()."')");

    $DB->query("UPDATE ".$db_prefix."setting SET
                       value='".addslashes($_POST[phparticletitle])."'
                       WHERE name='phparticletitle'");
    $DB->query("UPDATE ".$db_prefix."setting SET
                       value='".addslashes($_POST[phparticleurl])."'
                       WHERE name='phparticleurl'");
    $DB->query("UPDATE ".$db_prefix."setting SET
                       value='".addslashes($_POST[homepage])."'
                       WHERE name='homepage'");
    $DB->query("UPDATE ".$db_prefix."setting SET
                       value='".addslashes($_POST[email])."'
                       WHERE name='webmastermail'");

    $config_filename = "configs/setting.php";
    $fp = fopen($config_filename,w);

    $settinggroups = $DB->query("SELECT * FROM ".$db_prefix."settinggroup ORDER BY displayorder");
    $contents = "<?php\n";
    while ($settinggroup = $DB->fetch_array($settinggroups)) {

           $contents .= "/*  settinggroup $settinggroup[title]  */\n";

           $settings = $DB->query("SELECT * FROM ".$db_prefix."setting WHERE settinggroupid='$settinggroup[settinggroupid]' ORDER BY displayorder");
           while ($setting = $DB->fetch_array($settings)) {
                  $contents .= "    /*  setting $setting[title]  */\n";
                  if ($setting[type]=="boolean") {
                      $contents .= "\$configuration[$setting[name]] = \"".intval($setting[value])."\";\n";
                  } elseif ($setting[type]=="string") {
                      $contents .= "\$configuration[$setting[name]] = \"".addslashes($setting[value])."\";\n";
                  } elseif ($setting[type]=="text") {
                      $contents .= "\$configuration[$setting[name]] = \"".addslashes($setting[value])."\";\n";
                  } elseif ($setting[type]=="integer") {
                      $contents .= "\$configuration[$setting[name]] = \"".intval($setting[value])."\";\n";
                  }
           }
    }
    $contents .= "?>";

    fwrite($fp,$contents);
    fclose($fp);
    $install_lock_filename = "configs/install.lock";
    $fp = fopen($install_lock_filename, w);
    fclose($fp);

    echo "<p>安装完毕,请删除风格文件 phpArticle.php 和 安装文件 install.php,以免被他人恶意利用</p>";
    echo "<p>感谢您购买 phpArticle 文章管理系统.</p>";
    echo "<p><a href=\"index.php\">登陆控制面版</a></p>";

}
cpfooter();
?>