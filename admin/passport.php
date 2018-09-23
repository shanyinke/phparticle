<script type="text/javascript">
function selectsys()
{
	document.passport.submit.disabled=true;
	location="passport.php?sys="+document.passport.sys.value;
}
function delpassport()
{
	document.passport.action.value='delete';
	document.passport.submit.click();
}
</script>
<?
define('CREATE_HTML_FILE', 1);
require "functions_bbs.php";
require "global.php";
if($sys)$passport_suffix=$sys;
else $passport_suffix = 'default';
if(file_exists("./loadsystem/passport_".$passport_suffix.".php"))
	require "./loadsystem/passport_".$passport_suffix.".php";
else
{
	$handle=opendir("./loadsystem/");

	while ($file = readdir($handle))
	{
		if($file != '.' && $file != '..' && strstr($file,"passport_"))
		{
			$tmppos1=strchr($file,'_');
			$tmppos2=strchr($tmppos1,'.');
			$systemsuffix=substr($tmppos1,1,strlen($tmppos1)-strlen($tmppos2)-1);
			$systemlist[$systemsuffix]=$systemsuffix;
		}
	}
	require "./loadsystem/passport_".$systemsuffix.".php";
}

$cpforms = new FORMS;
cpheader();
if($_GET['action']=='update')
{
	$content=readfromfile("config.php");
	$content=str_replace("'".$passport_suffix."'","'".$_GET['sys']."'",$content);
	writetofile("config.php",$content);
	redirect2($_SERVER["HTTP_REFERER"],"数据已经保存");
}else if($_GET['action']=='setpassport')
{
	$content=readfromfile("./loadsystem/passport_".$passport_suffix.".php");
	$content=str_replace(Array("'".$passportkey."'","'".$bbsurl."'"),
	Array("'".$_GET['passportkey']."'","'".$_GET['bbsurl']."'"),$content);
	writetofile("./loadsystem/passport_".$passport_suffix.".php",$content);
	if($passport_suffix!='default')
	{
		//@unlink("./loadsystem/passport_default.php");
		rename("./loadsystem/passport_".$passport_suffix.".php","./loadsystem/passport_default.php");
	}
	/*
	$content=readfromfile("config.php");
	$content=preg_replace("/\$passport_suffix='[[a-zA-Z_]*'/","\$passport_suffix='".$_GET['sys']."'",$content);
	writetofile("config.php",$content);
	*/
	redirect2($_SERVER["HTTP_REFERER"],"数据已经保存");
	
}else if($_GET['action']=='delete')
{
	if($passport_suffix=='default'){
		@unlink("./loadsystem/passport_".$bbsname.".php");
		rename("./loadsystem/passport_default.php","./loadsystem/passport_".$bbsname.".php");
	}
	redirect2($_SERVER["HTTP_REFERER"],"取消整合");
}
else
{
	if(!$systemlist){
		$handle=opendir("./loadsystem/");
	
		while ($file = readdir($handle))
		{
			if($file != '.' && $file != '..' && strstr($file,"passport_"))
			{
				$tmppos1=strchr($file,'_');
				$tmppos2=strchr($tmppos1,'.');
				$systemsuffix=substr($tmppos1,1,strlen($tmppos1)-strlen($tmppos2)-1);
				$systemlist[$systemsuffix]=$systemsuffix;
			}
		}
	}

//passport设置
$cpforms->formheader(array('title' => $passport_suffix.'通行证配置信息',
                        'name' => 'passport',
                        'method' => 'get',
                        'action' => 'passport.php'));
$cpforms->makeselect(array('text' => '选择你的系统(bbs)',
                        'name' => 'sys',
                        'option' => $systemlist,
                        'selected' => $passport_suffix,
                        'onchange' => "selectsys();"));
$cpforms->makeinput(array('text' => '论坛 URL',
                  'name' => 'bbsurl',
                  'value' => $bbsurl));
$cpforms->makeinput(array('text' => '通行证私有密匙',
                  'name' => 'passportkey',
                  'value' => $passportkey));

$cpforms->makehidden(array('name'=>'action',
                                'value'=>'setpassport'));
?>
<tr class="secondalt" nowrap><td><input type="button" name="cancelpassport" value="取消通行证" onclick="delpassport();"/></td>
<td></td></tr>
<?
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