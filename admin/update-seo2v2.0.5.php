<?php
error_reporting(7);

$debug = 1;
$lastversion = Array("2.0.1 seo");
$version = "2.0.5";
$charset = "utf-8";
$scriptname = "update-seo2v2.0.5.php";


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

extract($_GET,EXTR_SKIP);
extract($_POST,EXTR_SKIP);

require "./class/forms.php";
$cpforms = new FORMS;

function pa_exit($text="") {
         global $step,$scriptname;
         echo "<p>$text</p>";
         echo "<p><a href=\"$scriptname?step=".($step-1)."\">返回上一步</a></p>";
         cpfooter();
         exit;
}

function cpheader($extraheader="",$extraheader1=""){
         global $version,$lastversion,$charset,$scriptname;
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
    <td><a href="<?php echo $scriptname;?>" title="phpArticle 文章管理系统"><img src="../images/logo.gif" border="0"></a></td>
    <td nowrap>
    <p align=right>phpArticle Version <?php echo $lastversion;?> - <?php echo $version;?> 升级程序</p>
    <b>在升级的过程中,请不要刷新页面.以免升级出错</b>
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
      <span class="middlefont">Copyright &copy; 2002 <a href="http://http://www.phparticle.cn/">phpArticle 文章管理系统</a><br>
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

    require "config.php";
    require "class/mysql.php";

    $DB = new DB_MySQL;

    $DB->servername=$servername;
    $DB->dbname=$dbname;
    $DB->dbusername=$dbusername;
    $DB->dbpassword=$dbpassword;

    $DB->connect();
    $DB->selectdb();

// step one
if (empty($step) or $step==1) {
    $step = 1;

    $getconfig = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."setting WHERE name='version'");
    $configuration[version] = $getconfig[value];

    if (!in_array($configuration[version],$lastversion)) {
        pa_exit("phpArticle 版本无效,本升级程序只针对 $lastversion 的升级,请先确认.");
    }
    echo "<p>注意: 升级时,数据库重复的表会被删除并重新建立,部分数据也分被重置,重置的数据包括 风格,用户组,还有基本设置,升级完成后,请登陆后台重新修改基本设置与风格</p>";
    echo "<p><a href=\"$scriptname?step=".($step+1)."&delete_existing=1\">开始升级 phpArticle</a></p>";
}


if ($step==2) {

    $mysql_data = "
CREATE TABLE `".$db_prefix."cache_bbs` (
  `name` varchar(50) NOT NULL default '',
  `content` longtext,
  `expiry` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`name`)
);
CREATE TABLE `".$db_prefix."htmllog_bbs` (
  `htmllogid` int(10) unsigned NOT NULL auto_increment,
  `type` mediumint(5) unsigned NOT NULL default '0',
  `bbs` varchar(50) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `startid` int(10) unsigned NOT NULL default '0',
  `pagenum` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`htmllogid`),
  KEY `sortid` (`dateline`),
  KEY `bbs` (`bbs`)
);
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
  PRIMARY KEY  (`tagid`),
  KEY `tagname` (`tagname`)
);
";

    $cpforms->formheader(array('title'=>'正在更新数据库','colspan'=>3));
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
                                                'value'=>'继续下一步',
                                                ))));



}


// step 3 import update style
if ($step==3) {


    $path="./2.0.5update.style";

    if (!file_exists($path)) {
        pa_exit("风格文件不存在,请确认 $path 已上传到 admin 目录.上传后再刷新本页面.");
    }

         $stylecontents = implode("",file($path));
         $stylecontent = explode("|P-@phpArticle@-A|",$stylecontents);

         list($key,$styleversion) = each($stylecontent);
         // check version
         //if ($styleversion!="$version") {
         //    pa_exit("风格的版本与当前系统版本不符,风格版本:$styleversion,系统版本:$version");
         //}

         list($key,$styleinfos) = each($stylecontent);
         // check style title
         $styleinfo = explode("|P-@style@-A|",$styleinfos);

         if ($styleinfo[0]=="2.0.5update") {
             $replacementsetid = -1;
             $templatesetid = -1;
         } else {
             echo "风格文件无效";
             exit;
         }

         list($key,$replacements) = each($stylecontent);
         $replacement = explode("|P-@replacement@-A|",$replacements);

			//	$DB->query("DELETE FROM ".$db_prefix."replacement WHERE replacementsetid='$replacementsetid'");

         for ($i=0;$i<$styleinfo[3];$i++) {
              list($key,$findword)=each($replacement);
              list($key,$replaceword)=each($replacement);
              if ($findword!="") {
                  $DB->query("INSERT INTO ".$db_prefix."replacement (replacementsetid,findword,replaceword)
                                     VALUES ($replacementsetid,'".addslashes($findword)."','".addslashes($replaceword)."')");
              }
         }

         list($key,$templates) = each($stylecontent);
         $temp = explode("|P-@template@-A|",$templates);

			//	$DB->query("DELETE FROM ".$db_prefix."template WHERE templatesetid='$templatesetid'");

         for ($i=0;$i<$styleinfo[4];$i++) {
              list($key,$title)=each($temp);
              list($key,$template)=each($temp);
              $DB->query("INSERT INTO ".$db_prefix."template (templatesetid,title,template)
                                 VALUES ($templatesetid,'".addslashes($title)."','".addslashes($template)."')");
         }


//		$DB->query("INSERT INTO ".$db_prefix."replacementset (title) VALUES ('default')");
//    $DB->query("INSERT INTO ".$db_prefix."templateset (title) VALUES ('default')");
//	$DB->query("INSERT INTO ".$db_prefix."style (title,replacementsetid,templatesetid) VALUES ('default',1,1)");

    $cpforms->formheader(array('title'=>'导入风格文件'));
    $cpforms->makehidden(array('name'=>'step','value'=>$step+1));
    $cpforms->maketd(array("正在导入风格: $path ...","成功"));

    $cpforms->formfooter(array('button'=>array('submit'=>array('type'=>'submit',
                                                'name'=>'next',
                                                'value'=>'完成升级',
                                                ))));

}

if ($step==4) {
		$DB->query("UPDATE ".$db_prefix."setting set value='$version' WHERE name='version'");
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

    echo "<p>升级完成,请删除本文件 $scriptname</p>";

}

cpfooter();
?>
