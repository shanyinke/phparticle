<?php
error_reporting(7);
define('CREATE_HTML_FILE', 1);
if(!$_GET['step'])
{
	require "functions.php";
	require "global.php";
	require "../modules/default/functions.php";
}
else{
	require "functions_bbs.php";
	require "global_bbs.php";
	require "../modules/default/functions_bbs.php";
}
chdir('./../');
cpheader();
if ($_GET['action'] == "do") {
	$styleid = 1;
        $style = getstyle();

    cachesorts();
    makesortlist();

	if(!$_GET['step'])
	{
		$tagdata=$DB->query("SELECT * FROM " . $db_prefix . "tag WHERE tagname='defaultsys' ORDER BY tagid");
	}else
	if($_GET['s'])
	{
		$tagdata=$DB->query("SELECT * FROM " . $db_prefix . "tag WHERE tagname='".$_GET['s']."' ORDER BY tagid");
	}
	if($tagdata)
        while($taginfo=$DB->fetch_array($tagdata))
        {
        	make_tag_articlelist("save",$taginfo['locate'],$taginfo['contenttype'],$taginfo['tagname'],$taginfo['type'],$taginfo['sortid'],$taginfo['maxarticles'],$taginfo['titlelen'],$taginfo['templatename']);
		}
	$startnum=intval($_GET['st']);
	$sysdata=$DB->fetch_one_array("SELECT tagname FROM " . $db_prefix . "tag WHERE tagname!='defaultsys' GROUP BY tagname LIMIT ".$startnum.",1");
	$_GET['s']=$sysdata['tagname'];
	if($startnum==0 || !empty($_GET['s']))
	{
		echo "<meta http-equiv=\"refresh\" content=\"0;url=renewtag.php?action=do&step=2&s=".$_GET['s']."&st=".($startnum+1)."\">";
		exit;
	}
    redirect2("./renewtag.php","标签已经刷新！");
} else {


$cpforms->inithtmlarea();
$cpforms->formheader(array('title' => '更新标签缓冲',
                'name' => 'homepage',
                'method' => 'get',
                'action' => 'renewtag.php'));
$cpforms->makehidden(array('name' => 'action',
                        'value' => "do"));
$cpforms->formfooter();
}
cpfooter();

?>