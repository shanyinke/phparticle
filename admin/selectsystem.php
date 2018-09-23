<script>
function selectsys()
{
	document.sysconfig.submit.disabled=true;
	location="selectsystem.php?sys="+document.sysconfig.sys.value;
}
</script>
<?
define('CREATE_HTML_FILE', 1);
require "functions_bbs.php";
require "global.php";
if($sys)$loadsystem_suffix=$sys;
require "./loadsystem/config".$loadsystem_suffix.".php";
$cpforms = new FORMS;
cpheader();
if($_GET['action']=='update')
{
	$content=readfromfile("config.php");
	$content=str_replace("'".$loadsystem_suffix."'","'".$_GET['sys']."'",$content);
	writetofile("config.php",$content);
	redirect2($_SERVER["HTTP_REFERER"],"数据已经保存");
}else if($_GET['action']=='setdboption')
{
	$content=readfromfile("./loadsystem/config".$loadsystem_suffix.".php");
	$content=str_replace(Array("'".$servername_bbs."'","'".$dbusername_bbs."'","'".$dbpassword_bbs."'","'".$dbname_bbs."'","'".$technicalemail_bbs."'","'".$db_prefix_bbs."'","'".$bbsurl."'","'".$bbshtmldir."'"),
	Array("'".$_GET['servername_bbs']."'","'".$_GET['dbusername_bbs']."'","'".$_GET['dbpassword_bbs']."'","'".$_GET['dbname_bbs']."'","'".$_GET['technicalemail_bbs']."'","'".$_GET['db_prefix_bbs']."'","'".$_GET['bbsurl']."'","'".$_GET['bbshtmldir']."'"),$content);
	writetofile("./loadsystem/config".$loadsystem_suffix.".php",$content);
	
	$content=readfromfile("config.php");
	$content=preg_replace("/\$loadsystem_suffix='[[a-zA-Z_]*'/","\$loadsystem_suffix='".$_GET['sys']."'",$content);
	writetofile("config.php",$content);
	redirect2($_SERVER["HTTP_REFERER"],"数据已经保存");
}
else
{
	$handle=opendir("./loadsystem/");

	while ($file = readdir($handle))
	{
		if($file != '.' && $file != '..' && strstr($file,"config"))
		{
			$tmppos1=strchr($file,'_');
			$tmppos2=strchr($tmppos1,'.');
			$systemsuffix=substr($tmppos1,0,strlen($tmppos1)-strlen($tmppos2));
			$systemlist[$systemsuffix]=$systemsuffix;
		}
	}
/*
$cpforms->formheader(array('title' => '选择系统',
                        'name' => 'form1',
                        'method' => 'get',
                        'action' => 'selectsystem.php'));
$cpforms->makeselect(array('text' => '系统列表',
                        'name' => 'loadsystem_suffix',
                        'option' => $systemlist,
                        'selected' => $loadsystem_suffix));
$cpforms->makehidden(array('name'=>'action',
                                'value'=>'update'));
$cpforms->formfooter();*/
//数据库设置
$cpforms->formheader(array('title' => $loadsystem_suffix.'系统数据库配置信息',
                        'name' => 'sysconfig',
                        'method' => 'get',
                        'action' => 'selectsystem.php'));
$cpforms->makeselect(array('text' => '选择你的系统(bbs)',
                        'name' => 'sys',
                        'option' => $systemlist,
                        'selected' => $loadsystem_suffix,
                        'onchange' => "selectsys();"));
$cpforms->makeinput(array('text' => '服务器地址',
                  'name' => 'servername_bbs',
                  'value' => $servername_bbs));
$cpforms->makeinput(array('text' => '数据库名',
                  'name' => 'dbname_bbs',
                  'value' => $dbname_bbs));
$cpforms->makeinput(array('text' => '数据库用户名',
                  'name' => 'dbusername_bbs',
                  'value' => $dbusername_bbs));
$cpforms->makeinput(array('text' => '数据库密码',
                  'name' => 'dbpassword_bbs',
                  'type' => 'password',
                  'value' => $dbpassword_bbs));
$cpforms->makeinput(array('text' => '技术支持邮箱',
                  'name' => 'technicalemail_bbs',
                  'value' => $technicalemail_bbs));
$cpforms->makeinput(array('text' => '数据库表名前缀',
                  'name' => 'db_prefix_bbs',
                  'value' => $db_prefix_bbs));
$cpforms->makeinput(array('text' => '系统url',
                  'name' => 'bbsurl',
                  'value' => $bbsurl));
$cpforms->makeinput(array('text' => '系统静态文件存放目录',
                  'name' => 'bbshtmldir',
                  'value' => $bbshtmldir));
$cpforms->makehidden(array('name'=>'action',
                                'value'=>'setdboption'));
$cpforms->formfooter();
}
cpfooter();
function readfromfile($file_name) {
 if(file_exists($file_name)==0) {
        return "";
 } else {
  $filenum=fopen($file_name,"r");
  flock($filenum,LOCK_SH);
  $file_data=fread($filenum,filesize($file_name));
  fclose($filenum);
  return $file_data;
 }
}
?>