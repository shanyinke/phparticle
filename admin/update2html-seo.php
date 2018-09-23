<?php
error_reporting(7);

$debug = 1;
$lastversion = Array("2.0.1 special");
$version = "2.0.1 seo";
$charset = "utf-8";
$scriptname = "update2html-seo.php";


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
INSERT INTO `".$db_prefix."setting` VALUES ('', '10', '静态文件名生成方法', 'filenamemethod', '2', 'integer', '3', '1代表文件名不优化，2代表文件名英文/yahoo搜索引擎优化，3百度优化');
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
                                                'value'=>'完成升级',
                                                ))));



}


if ($step==3) {
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
