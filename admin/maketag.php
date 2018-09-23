<script type="text/javascript">
function checkpm()
{
		if (confirm("您确定真的要刷新缓冲吗？"))
		{
			window.location.replace("renewcache_bbs.php?action=do");
		}else
		document.write("您已经取消操作.")
	return;
}
function changetmp()
{
	if(document.add.type.value == 'img')
	document.add.template.value="home_imgart";
	else document.add.template.value="home_myart";
}
</script>
<?php
error_reporting(7);
define('CREATE_HTML_FILE', 1);
if(!$_GET['tagname']||$_GET['tagname']=='defaultsys')
{
require "functions.php";
require "global.php";
require "../modules/default/functions.php";
}else
{
	require "functions_bbs.php";
	require "global_bbs.php";
	require "../modules/default/functions_bbs.php";
}
$systemlist=Array();
$systemlist['defaultsys']='defaultsys';
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
chdir('./../');
cpheader();
if ($_GET['action'] == "do"||$_GET['method']=='del'||$_GET['method']=='update') {
        $styleid = 1;
        $style = getstyle();
/*        if (empty($noheader)) {
                if (trim($templatelist) != "") {
                        $templatelist .= ",";
                }
                $templatelist .= "header,footer,";
        }

        if (trim($templatelist) != "") {
                $templatelist .= ",";
        }

        $templatelist .= "headinclude,searchcode,sortlist,sortlistbit_level1,sortlistbit_level2,sortlistbit_level3,nav,nav_joiner,logincode,logoutcode,navbar,navbar_joiner,navbar_sort";
        cachetemplatelist($templatelist);
        eval("\$headinclude = \"" . gettemplate('headinclude') . "\";");
        if (empty($noheader)) {
                eval("\$header = \"" . gettemplate('header') . "\";");
                eval("\$footer = \"" . gettemplate('footer') . "\";");
        }
*/
        cachesorts();
        makesortlist();
        make_tag_articlelist($_GET['method'],$_GET['locate'],$_GET['contenttype'],$_GET['tagname'],$_GET['type'],$_GET['sortid'],$_GET['maxarticles'],$_GET['titlelen'],$_GET['template']);
        if($_GET['method']=='del')
        {
        	$DB->query("DELETE FROM " . $db_prefix . "tag WHERE tagid=".$_GET['tagid']);
        	echo("标签已经删除！");
        }else if($_GET['method']=='update')
        {
        	$DB->query("UPDATE " . $db_prefix . "tag SET `locate`='".$_GET['locate']."',`contenttype`='".$_GET['contenttype']."',`tagname`='".$_GET['tagname']."',`type`='".$_GET['type']."',`sortid`='".$_GET['sortid']."',`maxarticles`='".$_GET['maxarticles']."',`titlelen`='".$_GET['titlelen']."',`templatename`='".$_GET['template']."' WHERE tagid=".$_GET['tagid']);
        	echo("标签已经修改！");
        }
        else
        {
        	$DB->query("INSERT INTO " . $db_prefix . "tag (`locate`,`contenttype`,`tagname`,`type`,`sortid`,`maxarticles`,`titlelen`,`templatename`) VALUES ('".$_GET['locate']."','".$_GET['contenttype']."','".$_GET['tagname']."','".$_GET['type']."','".$_GET['sortid']."','".$_GET['maxarticles']."','".$_GET['titlelen']."','".$_GET['template']."')");
        	echo("标签已经添加！");
      	}
} else if($_GET['method']=='edit')
{
	$DB->selectdb();
	$taginfo=$DB->fetch_one_array("SELECT * FROM " . $db_prefix . "tag WHERE tagid=".$_GET['tagid']." ORDER BY tagid");
	$cpforms->formheader(array('title' => '标签管理',
                        'name' => 'add',
                        'method' => 'get',
                        'action' => 'maketag.php'));
  $locateoption = Array('index'=>'首页','sort'=>'分类页','article'=>'文章内容页');
  $cpforms->makeselect(array('text' => '标签所放目标页',
                  'name' => 'locate',
                  'option' => $locateoption,
                  'selected' => $taginfo['locate']));
  $typeoption = Array('text'=>'文字','img'=>'图片');
  $cpforms->makeselect(array('text' => '标签类型(图片或文字)图片调用只适用于本系统数据库',
                  'name' => 'type',
                  'option' => $typeoption,
                  'selected' => $taginfo['type'],
                  'onchange' => "changetmp()"));
  $contenttypeoption = Array('new'=>'最新','hot'=>'热门','recommend'=>'推荐','special'=>'专题');
  $cpforms->makeselect(array('text' => '标签内容类型',
                  'name' => 'contenttype',
                  'option' => $contenttypeoption,
                  'selected' => $taginfo['contenttype']));
  $cpforms->makeselect(array('text' => '标签所属系统(defaultsys代表本系统内部文章)',
                  'name' => 'tagname',
                  'option' => $systemlist,
                  'selected' => $taginfo['tagname']));
  $cpforms->makeinput(array('text' => '模板名字(<b>如果是图片文章,请填写home_imgart</b>)',
                  'name' => 'template',
                  'value' => $taginfo['templatename']));
  $cpforms->makeinput(array('text' => '所在分类id',
                  'name' => 'sortid',
                  'value' => $taginfo['sortid']));
  $cpforms->makeinput(array('text' => '文章显示条数',
                  'name' => 'maxarticles',
                  'value' => $taginfo['maxarticles']));
  $cpforms->makeinput(array('text' => '文章题目长度',
                  'name' => 'titlelen',
                  'value' => $taginfo['titlelen']));
  $cpforms->makehidden(array('name'=>'method',
                          'value'=>"update"));
  $cpforms->makehidden(array('name'=>'tagid',
                          'value'=>$_GET['tagid']));
  $cpforms->formfooter();
}else{

        $cpforms->formheader(array('title' => '标签管理',
                        'name' => 'add',
                        'method' => 'get',
                        'action' => 'maketag.php'));
        $locateoption = Array('index'=>'首页','sort'=>'分类页','article'=>'文章内容页');
        $cpforms->makeselect(array('text' => '标签所放目标页',
                        'name' => 'locate',
                        'option' => $locateoption));
        $typeoption = Array('text'=>'文字','img'=>'图片');
        $cpforms->makeselect(array('text' => '标签类型(图片或文字)',
                        'name' => 'type',
                        'option' => $typeoption,
                  'onchange' => "changetmp()"));
        $contenttypeoption = Array('new'=>'最新','hot'=>'热门','recommend'=>'推荐','special'=>'专题');
  			$cpforms->makeselect(array('text' => '标签内容类型',
                  'name' => 'contenttype',
                  'option' => $contenttypeoption,
                  'selected' => $taginfo['contenttype']));
        $cpforms->makeselect(array('text' => '标签所属系统(defaultsys代表本系统内部文章)',
                  'name' => 'tagname',
                  'option' => $systemlist));
        $cpforms->makeinput(array('text' => '模板名字(<b>如果是图片文章,请填写home_imgart</b>)',
                        'name' => 'template',
                        'value' => 'home_myart'));
        $cpforms->makeinput(array('text' => '所在分类id',
                        'name' => 'sortid',
                        'value' => ''));
        $cpforms->makeinput(array('text' => '文章显示条数',
                        'name' => 'maxarticles',
                        'value' => 10));
        $cpforms->makeinput(array('text' => '文章题目长度',
                        'name' => 'titlelen',
                        'value' => 35));
        $cpforms->makehidden(array('name'=>'action',
                                'value'=>'do'));
        $cpforms->makehidden(array('name'=>'method',
                                'value'=>"save"));
        $cpforms->formfooter();
        $DB->selectdb();
        $tagdata=$DB->query("SELECT * FROM " . $db_prefix . "tag WHERE 1 ORDER BY tagid");
        $cpforms->tableheader();
        $cpforms->maketd(Array("标签(放在模板里面)","所在位置","内容类型","系统","类型","分类id","显示文章数","标题长度","模板名字","管理"));
        $tagtype=Array("img"=>"图片","text"=>"文字");
        $taglocate=Array("index"=>"首页","sort"=>"分类页","article"=>"文章内容页");
        $tagcontenttype = Array('new'=>'最新','hot'=>'热门','recommend'=>'推荐','special'=>'专题');
        while($taginfo=$DB->fetch_array($tagdata))
        {
        	$dellink="<a href=\"maketag.php?method=del&locate=".$taginfo['locate']."&conttenttype=".$taginfo['contenttype']."&type=".$taginfo['type']."&sortid=".$taginfo['sortid']."&maxarticles=".$taginfo['maxarticles']."&titlelen=".$taginfo['titlelen']."&template=".$taginfo['templatename']."&tagid=".$taginfo['tagid']."&tagname=".$taginfo['tagname']."\">删除</a>";
        	$editlink="<a href=\"maketag.php?method=edit&tagid=".$taginfo['tagid']."\">编辑</a>";
        	$cpforms->maketd(Array("&lt;?=\\\$tag_articlelist[".$taginfo['type']."][".$taginfo['tagname']."][".$taginfo['sortid']."]?&gt;",$taglocate[$taginfo['locate']],$tagcontenttype[$taginfo['contenttype']],$taginfo['tagname'],$tagtype[$taginfo['type']],$taginfo['sortid'],$taginfo['maxarticles'],$taginfo['titlelen'],$taginfo['templatename'],$editlink."&nbsp;".$dellink));
	}
        $cpforms->tablefooter();
}
cpfooter();

?>