<?
//error_reporting(E_ALL ^ E_NOTICE); 
define('CREATE_HTML_FILE', 1);
if(!isset($_GET['type']))$_GET['type']=1;

if($_GET['type']==1)
{
	if(!isset($_GET['auto'])&&$_GET['mod'])
	{
		require "admin/functions.php";
		require "admin/global2.php";
	}else
	{
		require "global.php";
		require "admin/class/forms.php";
		$cpforms = new FORMS;
	}
	if(!$_GET['mod'])require "modules/default/functions.php";
}else{
	if(!$_GET['auto']&&$_GET['mod'])
	{
		require "admin/functions_bbs.php";
		require "admin/global2_bbs.php";
	}else
	{
		require "global_bbs.php";
		require "admin/class/forms.php";
		$cpforms = new FORMS;
	}
	if(!$_GET['mod'])require "modules/default/functions_bbs.php";
}

if (function_exists("set_time_limit")==1 and get_cfg_var("safe_mode")==0) {
   @set_time_limit(0);
}
$timestamp=time();
$filemodifytime=@filemtime("admin/configs/updateing.txt");
$filemodifytime2=@filemtime("admin/configs/".$_SERVER['REMOTE_ADDR'].".tmp");
if($filemodifytime<$timestamp-15*60||(!$_GET['auto']&&$_GET['mod'])||$filemodifytime2>0)
{
	if(writetofile("admin/configs/updateing.txt",""))//生成空文件，用以标记数据正在更新
	{
		if($_GET['mod'])
		{
			if(empty($_GET['s'])&&$_GET['mod']=="index"&&$_GET['st']>0)
			{
				echo "<meta http-equiv=\"refresh\" content=\"60;url=htmlauto.php\">\n完成";
			}else if(empty($_GET['s'])&&$_GET['st']>0&&$_GET['mod'])
			{
			//	unlink("admin/configs/updateing.txt");
				if($_GET['mod']=="mkarticle_bbs")
				{
					echo "<meta http-equiv=\"refresh\" content=\"0;url=htmlauto.php?mod=mksort_bbs&auto=1&createlimit=200&type=2\">\n开始更新论坛分类";
				}else if($_GET['mod']=="mksort_bbs")
				{
					echo "<meta http-equiv=\"refresh\" content=\"0;url=htmlauto.php?mod=index&auto=1\">\n正在更新首页";
				}
			}else if(!strpos($_GET['mod'],"/")){
				if(!include "modules/default/$_GET[mod].php"){
					echo "error:data/modules/default/$_GET[mod].php";exit;
				}else if($_GET['mod']=="index")
				{
					unlink("admin/configs/".$_SERVER['REMOTE_ADDR'].".tmp");
					echo "自动生成完毕！";
				}
			}
			exit;
		}
		if(!writetofile("admin/configs/".$_SERVER['REMOTE_ADDR'].".tmp",""))unlink("admin/configs/updateing.txt");
		$styleid = 1;
		$style = getstyle();
	
		cachesorts();
		makesortlist();

		if($_GET['type']==1)
		{
			$tagdata=$DB->query("SELECT * FROM " . $db_prefix . "tag WHERE tagname='defaultsys' and renew=1 ORDER BY tagid");
		}
		else if($_GET['s'])
		{
			$tagdata=$DB->query("SELECT * FROM " . $db_prefix . "tag WHERE tagname='".$_GET['s']."' ORDER BY tagid");
		}
		if($tagdata)
	        while($taginfo=$DB->fetch_array($tagdata))
	        {
	        	make_tag_articlelist("save",$taginfo['locate'],$taginfo['contenttype'],$taginfo['tagname'],$taginfo['type'],$taginfo['sortid'],$taginfo['maxarticles'],$taginfo['titlelen'],$taginfo['templatename']);
			}
		if($_GET['type']==1)
		{
		//	unlink("admin/configs/updateing.txt");
			echo "<meta http-equiv=\"refresh\" content=\"0;url=htmlauto.php?type=2&st=0\">";
		}else
		if($startnum==0 || !empty($_GET['s']))
		{
		//	unlink("admin/configs/updateing.txt");
			echo "<meta http-equiv=\"refresh\" content=\"0;url=htmlauto.php?type=2&st=".($startnum+1)."\">";
		}
		else{
		//	unlink("admin/configs/updateing.txt");
			echo "<meta http-equiv=\"refresh\" content=\"0;url=htmlauto.php?mod=mkarticle&auto=1&createlimit=200\">\n缓冲更新新完毕";
		}
	}else echo "<meta http-equiv=\"refresh\" content=\"60;url=htmlauto.php\">\n数据阻塞";
}else echo "<meta http-equiv=\"refresh\" content=\"60;url=htmlauto.php\">\n您的数据是最新的。";

?>
