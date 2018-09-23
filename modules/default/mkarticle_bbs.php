<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<script>
function selectsys()
{
	document.sysconfig.submit.disabled=true;
	location="?mod=mkarticle_bbs&s="+document.sysconfig.s.value;
}
</script>
<?php
if($s)$loadsystem_suffix=$s;
$cpforms->cpheader();
if (!$_GET['createlimit']) {
	if(!$_GET['articleid'])
        $datainfo = $DB->fetch_one_array("SELECT * FROM " . $db_prefix . "htmllog_bbs WHERE type = 1 AND bbs='".$loadsystem_suffix."'");
        else
        {
        	$datainfo['startid']=$_GET['articleid'];
        }
        $handle=opendir("admin/loadsystem/");

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
        $cpforms->formheader(array('title' => '文章静态生成',
                        'name' => 'sysconfig',
                        'method' => 'get',
                        'action' => 'htmlauto.php'));
		$cpforms->makeselect(array('text' => '选择你的系统(bbs)',
                        'name' => 's',
                        'option' => $systemlist,
                        'selected' => $loadsystem_suffix,
                        'onchange' => "selectsys();"));
        $cpforms->makeinput(array('text' => '需要生成的文章开始ID',
                        'name' => 'startaid',
                        'value' => $datainfo['startid']));
        $cpforms->makeinput(array('text' => '需要生成的文章结束ID',
                        'name' => 'endid',
                        'value' => $_GET['articleid']));
        $cpforms->makeinput(array('text' => '每次生成文章静态页个数',
                        'name' => 'createlimit',
                        'value' => 200));
	$cpforms->makehidden(array('name' => 'type',
                        'value' => "2"));
        $cpforms->makehidden(array('name' => 'mod',
                        'value' => "mkarticle_bbs"));
        $cpforms->formfooter();
        $cpforms->cpfooter();
        exit;
}else if($_GET['auto']&&!$_GET['startaid'])
{
	$datainfo = $DB->fetch_one_array("SELECT startid FROM " . $db_prefix . "htmllog_bbs WHERE type = 1 AND bbs='".$_GET['s']."'");
	$_GET['startaid']=$datainfo['startid'];
}

$styleid = 1;
$style = getstyle();

cachesorts();
$hot_rate_articlelist = makehot_recommend_articlelist();
//$tag_article=make_tag_articlelist("get","article","new","bbs","text",2,10,30,'home_myart');
// $sortlist = makesortlist();

if (!$_GET['createlimit']) $_GET['createlimit'] = 1000;
if (!$_GET['startaid'] || $_GET['startaid'] < 0) $_GET['startaid'] = 0;
if (!intval($_GET['endid'])) $sqlopt = "";
else $sqlopt = "AND ".$tablelist['article']['sortid']." <= $_GET[endid]";
$DB_bbs->selectdb2($dbname_bbs);
$articles = $DB_bbs->query("SELECT ".$aconvertlist." FROM " . $db_prefix_bbs . $forumlist['article']." WHERE ".$tablelist['article']['articleid']." > $_GET[startaid] $sqlopt ORDER BY articleid LIMIT $_GET[createlimit]");
while ($article = $DB_bbs->fetch_array($articles)) {
        if ($createcount >= $_GET['createlimit']) {
		break;
        } 
        $articleid = &$article['articleid'];
        $g_o_back2root="..";
        $navbar = makearticlenavbar($article);
       /* if($article[sortid]!=$sortid&&isset($sortid))
        {
	        $sort = $DB->fetch_one_array("SELECT styleid,sortid FROM " . $db_prefix . "sort WHERE sortid='$article[sortid]'");
	        $sortid = $article['sortid'];
	        if (!empty($sort['styleid']) && $sort['styleid'] != $styleid) { // 复位风格
	                $styleid = $sort['styleid'];
	                $style = getstyle();
		//	cachetemplatelist($templatelist);
		//	eval("\$headinclude = \"" . gettemplate('headinclude') . "\";");
	                if (empty($noheader)||$dirdepth!=$g_depth) {
			//	eval("\$header = \"" . gettemplate('header') . "\";");
			//	eval("\$footer = \"" . gettemplate('footer') . "\";");
	                } 
	                cachesorts(); 
	                // $sortlist = makesortlist();
	                $hot_rate_articlelist = makehot_recommend_articlelist();
	        }
	}*/

        if ($subdirs = mkdirname($article['sortid'],-1,$article['date'],0,0))//get_sortdirs($article['sortid'])
                $writedir = HTMLDIR . "/" . $subdirs;// . "/" . date("Y_m", $article['date']);
        else $writedir = HTMLDIR."/";//continue;
        $article[description] = htmlspecialchars(trim($article[description]));
        $articledate = $article[date];
        $article[date] = padate($dateformat_article, $article[date]);
	$DB_bbs->selectdb2($dbname_bbs);
//	$pages = $DB_bbs->query("SELECT ".$tablelist['articletext']['id']." AS id, ".$tablelist['articletext']['subhead']." AS subhead FROM " . $db_prefix_bbs . $forumlist['articletext']." WHERE ".$tablelist['articletext']['articleid']."='$articleid' ORDER BY id LIMIT 1");

        $totalpages = 1;//$DB_bbs->num_rows($pages);
        unset($pagejump);
        unset($pagejumpbits);
        if (!$_GET['pagenum']) $_GET['pagenum'] = 1;
        for($pagenum = $_GET['pagenum']; $pagenum <= $totalpages; $pagenum ++) {
                $offset = $pagenum-1;
                // $DB->query("UPDATE ".$db_prefix."article SET views=views+1 WHERE articleid='$articleid'");
                $articletext = $DB_bbs->fetch_one_array("SELECT ".$atconvertlist2.$atjoinconvertlist."
                                            FROM " . $db_prefix_bbs . $forumlist['articletext']." ".$atjoinoption."
                                            WHERE ".$tablelist['articletext']['articleid']."='$articleid'
                                            ORDER BY ".$db_prefix_bbs.$forumlist['articletext'].".".$tablelist['articletext']['id']." LIMIT 1");
		$articletext['articletext']=stripslashes($articletext['articletext']);
	//	$articletext['articletext'] = htmlspecialchars($articletext['articletext'], ENT_QUOTES);
		if(function_exists("convert_content"))
		{
			$articletext['articletext']=convert_content($articletext['articletext']);
		}
                $articletext['articletext']=preg_replace(Array("/[\r\n]+/","/\[url\]([^'\"]*)\[\/url\]/eiU","/\[img\](.*?)\[\/img\]/i"),
                Array("<br>","limitlength('\\1')","<img src=\"\\1\"/>"),$articletext['articletext']);
		$prefilename=mkfilename($filenamemethod,$article['title'],1);
                $writename = $prefilename . $article['articleid'] . "_" . $pagenum;


                // sort hotarticles
                if ($showhotarticle == "1") {
                       // $hotarticlelist = gethotsort_articles($article['sortid']);
                       $g_back2path = get_back2path($subdirs)."..";
                        $hotarticlelist = preg_replace(Array("@\"(".$g_back2path."|)/@","@\"./@"),Array("\"".$g_back2path."/../","\"../"),$hot_rate_articlelist['hot'][$article['sortid']]);
                }
                $threadarg=str_replace(Array('{articleid}','{sortid}'),Array("$articleid","$article[sortid]"),$bbsthreadarg);
                //需要把全局模板放在最后，保证所有变量都已经生成。 
                $DB->selectdb();
	//	eval("\$headinclude = \"" . gettemplate('headinclude') . "\";");
		if (empty($noheader)||$dirdepth!=$g_depth) {
		//	eval("\$header = \"" . gettemplate('header') . "\";");
		//	eval("\$footer = \"" . gettemplate('footer') . "\";");
		}
		$dirdepth=$g_depth;
		$g_depth=0;//reset
			$cpforms->cpheader();
				ob_start();
				ob_implicit_flush(0);
                ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
<title><?=$phparticletitle?> - <?=$article[title]?> - Powered By phpArticle</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-CN" />
<link rel="icon" href="<?=$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<link rel="shortcut icon" href="<?$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<meta name="description" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<meta name="keywords" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<script src="<?=$g_o_back2root?>/images/js/chinahtml.js" type="text/javascript"></script>
<link href="<?=$g_o_back2root?>/images/css/css.css" rel="stylesheet" type="text/css" />
</head>
<body>
<a name="top"></a>
<div id="header">
    <div id="topmenu">
		<div class="left">
			<div id="topdate"><?=$phparticleurl?></div>
		</div>
		<div class="right">
			<div id="topnavlist">
			<ul>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=usercp">会员面板</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=member&action=login">会员登陆</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=register">免费注册</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=search">高级搜索</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=search&action=lastupdate">最后更新</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=contribute">我要投稿</a></li>
				<li> <a href="<?=$g_o_back2root?>/index.php?mod=member&action=logout">退出登陆</a></li>
			</ul>
			</div>
		</div>
	</div>
	<div id="maintop">
		<div id="Logo"><a href="/"><img src="<?=$g_o_back2root?>/images/palogo.gif" alt="首页" /></a></div>
		<div id="TopAds"><a href="http://www.phparticle.net/" target="_blank"><img src="<?=$g_o_back2root?>/images/topbanner.jpg" width="468" height="60" alt="" /></a></div>
		<div id="toprightmenu">
			<ul>
				<li><a href="<?=$phparticleurl?>" onClick="this.style.behavior='url(#default#homepage)';this.setHomePage('<?=$phparticleurl?>');return(false);" style="behavior: url(#default#homepage)">设为首页</a></li>
				<li><a href=javascript:window.external.AddFavorite('<?=$phparticleurl?>','<?=$phparticletitle?>') title="希望您喜欢本站">加入收藏</a></li>
				<li><a href="mailto:semi.rock@gmail.com">联系我们</a></li>
			</ul>
		</div>
	</div>

	<div class="nav">

		<div class="nav-up-left"><a href="/" class="white">首页</a>　<a href="<?=$g_o_back2root?>/html/2/" class="white">精品文章</a>　<a href="<?=$g_o_back2root?>/cet/2/" class="white">使用帮助</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/1/13/" class="white">网站建设</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/4/6/" class="white">软件共享</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/1/12/" class="white">VC/C#/编成</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/1/9" class="white">UNIX/LINUX资料</a>　<a href="<?=$g_o_back2root?>/bbs" class="white">支持论坛</a>　<a href="http://www.utspeed.com" class="white">极速科技</a>　<a href="http://proxygo.com.ru" class="white">代理猎狗</a>  <a href="http://mp3.utspeed.com" class="white">音乐搜索</a></div>

	</div>

	<div class="navline">&nbsp;</div>

	<div class="nav">
		<div class="nav-down-left"><a href="<?=$g_o_back2root?>/bbshtmldata/4/6/" class="white">休闲娱乐</a>　<a href="http://music.utspeed.com" class="white">极速音乐</a>　<a href="http://4tc.com.ru" class="white">极速网址</a>　<a href="http://article.utspeed.com" class="white">幽默笑话</a>　<a href="http://woman.utspeed.com" class="white">女性美容</a>　<a href="http://nuskin.net.ru" class="white">如新网上购物商城</a>  <a href="http://bt.utspeed.com" class="white">电驴/电骡/emule</a></div>
		<div class="nav-down-right">
		<span>当前在线: <b><?=$onlineuser?></b></span></div>
	</div>







</div>

<script language=JavaScript>
function doZoom(size){
document.getElementById('zoom').style.fontSize=size+'px'
}
</script>

<div class="mainline">&nbsp;</div>
<!-- 主栏目开始 -->
<div class="maincolumn">

		<div class="pagelist">
		<div class="pagelisttitleico">&nbsp;</div>
		<div class="pagelisttitlebg">
		<div class="pagelisttitlename">
<?=$navbar?>
		</div>
		<div class="pagelisttitlemore"><a href="#"><img height="9" src="images/xml.gif" width="29" border="0" alt="" /></a></div>
		</div>

		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
					<div class="title"> 

<span class=rated>


</span>

<span>
					  <h1><?=$article[title]?></h1> 
					  <h2><strong>日期：</strong><?=$article[date]?> 13:32:12&nbsp;&nbsp;<strong>点击：</strong><script src="<?=$phparticleurl?>/count_bbs.php?aid=<?=$articleid?>&sys=<?=$_GET[s]?>"></script>&nbsp;&nbsp;<strong>作者：</strong><?=$article[author]?>&nbsp;&nbsp;<strong>来源：</strong><a href='#' target=_blank><?=$article[source]?></a>  
<br>
<b><a href="<?=$bbsurl?>/<?=$threadarg?>">发表评论</a></b> | <b><a href="<?=$bbsurl?>/<?=$threadarg?>">查看评论</a></b> | <b><a href="<?=$g_o_back2root?>/index.php?mod=favorite&articleid=<?=$articleid?>&pagenum=<?=$pagenum?>&action=add">加入收藏</a></b> | <b><a href="<?=$g_o_back2root?>/index.php?mod=recommend&articleid=<?=$articleid?>&pagenum=<?=$pagenum?>">Email给朋友</a></b> | <b><a href="<?=$g_o_back2root?>/index.php?mod=print&articleid=<?=$articleid?>">打印本文</a></b> | 字体：[<A href="javascript:doZoom(16)">大</A> <A href="javascript:doZoom(14)">中</A> <A href="javascript:doZoom(12)">小</A>] </h2>
</span>


					</div>
					<div class="articlecontent">


<font id=zoom><?=$articletext[articletext]?></font>

<div class="clear">&nbsp;</div>
</div>



<div>

<?
if (pa_isset($article[editor])) {
                        ?>
<b>责任编辑:</b><?=$article[editor]?>
<?
                }
?>
</div>

</div>
		<div class="tool">
			<span></span>
			<a class="button_content" href="<?=$bbsurl?>/<?=$threadarg?>">发表看法</a>
			<a class="button_content" href="<?=$bbsurl?>/<?=$threadarg?>">查看详情</a>
			<a href="<?=$g_o_back2root?>/index.php?mod=print&articleid=<?=$articleid?>" class="button_content" title="打印本文" target="_self">打印本文</a>
			<a href="javascript:window.close();" class="button_content">关闭窗口</a>
		</div>
		<div class="clear">&nbsp;</div>
		<div class="pagelistfooter">
			<div id="bklist"><a href="javascript:history.go(-1);"><img src="<?=$phparticleurl?>/images/ar2u.gif" width="5" height="8" /> 返回列表</a></div> 
          <div id="prv">
            <img src="<?=$phparticleurl?>/images/ar2b.gif" width="6" height="7" />&nbsp;
          </div> 
          <div id="next">
            <img src="<?=$phparticleurl?>/images/ar2.gif" width="6" height="7" />&nbsp;下面没有链接了
          </div> 
		</div>
		</div>
</div>
<div class="mainline">&nbsp;</div>
<div id="footer">
	<div id="bottommenu">
	  <a href="#">关于站点</a> - <a href="#">广告服务</a> - <a href="#">联系我们</a> - <a href="#">版权隐私</a> - <a href="#">免责声明</a> - <a href="http://utspeed.com">合作伙伴</a> - <a href="http://phparticle.net" target="_blank">程序支持</a> - <a  href="#">网站地图</a> - <a href="#top">返回顶部</a>
	</div>
	<div class="topline">&nbsp;</div>
	<div id="bottom">版权所有：<a href="<?=$phparticleurl?>"><?=$phparticletitle?></a> 2006 未经授权禁止复制或建立镜像<br />

	  <span>Powered by: <a href="http://www.phparticle.net">phpArticle html</a> Version <?=$version?>.</span><br />
	</div>
	</div>
</div>
</body>
</html><?
                $outputdata = ob_get_contents();
				ob_end_clean();
                dooutput($outputdata);
			$createcount ++;
			if ($createcount >= $_GET['createlimit'])break;
        }
        $_GET['pagenum'] = 0;
        $DB_bbs->selectdb2($dbname_bbs);
//	$DB_bbs->free_result($pages);
}
$DB_bbs->free_result($articles);
$DB->selectdb();
if ($createcount == 0 || ($_GET['endid'] && $articleid > $_GET['endid'])) {
	//	unlink(HTMLDIR."/updateing.txt");
		?>
		<script>
		if(top!=undefined)
		{
			var url = ""+top.document.location;
			if(url.indexOf("/admin/index.php")==-1)
			location="htmlauto.php?mod=mkarticle_bbs&auto=1&createlimit=200&type=2&st=<?=$startnum+1?>";
		}
		</script>
		<?
        echo "生成完毕!";
        $htmllogdata = $DB->query("SELECT htmllogid,startid FROM " . $db_prefix . "htmllog_bbs WHERE type = 1 AND bbs='".$loadsystem_suffix."'");
        if ($htmlloginfo = $DB->fetch_array($htmllogdata))
        {
        	if($_GET['startaid']>$htmlloginfo['startid'])
                $DB->query("UPDATE " . $db_prefix . "htmllog_bbs SET startid='" . $_GET['startaid'] . "',dateline=" . time() . " WHERE type=1 AND bbs='".$loadsystem_suffix."'");
        }
        else $DB->query("INSERT INTO " . $db_prefix . "htmllog_bbs (`type`,`startid`,`dateline`,`bbs`) VALUES ('1','" . $_GET['startaid'] . "','" . time() . "','".$loadsystem_suffix."')");
        $cpforms->cpfooter();
} else {
	//	unlink(HTMLDIR."/updateing.txt");
        echo "<meta http-equiv=\"refresh\" content=\"1;url=htmlauto.php?mod=mkarticle_bbs&startaid=" . $articleid . "&endid=$_GET[endid]&createlimit=$_GET[createlimit]&pagenum=$pagenum&auto=$_GET[auto]&type=2&s=$_GET[s]&st=$startnum\">";
        $cpforms->cpfooter();
}
function limitlength($link,$maxlen=55)
{
	if(strlen($link)>$maxlen)
	{
		$link2 = substr($link,0,$maxlen)."...";
	}else $link2=&$link;
	return "<a href=$link>$link2</a>";
}
?>


<?
function makesortnavbar($sortid) {
        global $DB_bbs, $db_prefix, $phparticletitle, $phparticleurl,$g_o_back2root, $parentsort;

        $navbit .= makesortnavbarbit($sortid, $parentsort);

        $navbar = "
你的位置：<a href='$homepage/' class='classlinkclass'>首页</a>&nbsp;>&nbsp;<a href='$phparticleurl' class='classlinkclass'>$phparticletitle</a>$navbit
";
        return $navbar;
}

function makesortnavbarbit($sortid, $parentsort, $isarticle = 0) {
        global $writedir, $subsort,$g_o_back2root,$g_depth,$usename,$singledir,$subsort,$usedate;
        static $outdirs;
        if ($sortid != -1) {
        	if($singledir==2)//没有子目录
        	{
        		$sorthtmllink = $subsort["dirname_$sortid"].".".HTMLEXT;
        		//$g_o_back2root="..";//当前对应的根目录相对路径
        		if ($parentsort[$sortid])
	                        foreach ($parentsort[$sortid] as $parentsortid => $title) {
	                        $navbit = "
&nbsp;>&nbsp;
";
	                        $navbit .= "
<a href='$sorthtmllink' class='classlinkclass'>$title</a>
";
	                        $g_depth++;
	                        $navbit = makesortnavbarbit($parentsortid, $parentsort) . $navbit;
	                }
	                //$writedir = "";
        	}else if($singledir==1)//只有一个子目录
        	{
        		$g_o_back2root="../".$g_o_back2root;//当前对应的根目录相对路径
        		if ($parentsort[$sortid])
	                        foreach ($parentsort[$sortid] as $parentsortid => $title) {
	                        $sortdirs = ($usename?$subsort["dirname_$sortid"]:$sortid)."/";
	                        $sorthtmllink = $outdirs.$subsort["dirname_$sortid"].".".HTMLEXT;
	                        $navbit = "
&nbsp;>&nbsp;
";
	                        $navbit .= "
<a href='$sorthtmllink' class='classlinkclass'>$title</a>
";
	                        $outdirs = "../";
	                        $g_depth++;
	                        $navbit = makesortnavbarbit($parentsortid, $parentsort) . $navbit;
	                        $outdirs = "";
	                }
	                $writedir .= $sortdirs."/";
        	}else{
	                if ($isarticle == 1&&$usedate)
	                {
	                	$outdirs .= "../";
	                	$g_o_back2root="../".$g_o_back2root;
	                	$g_depth=1;
	                }
	                if ($parentsort[$sortid])
	                        foreach ($parentsort[$sortid] as $parentsortid => $title) {
	                        if ($sortdirs)
	                                $sortdirs = ($usename?$subsort["dirname_$sortid"]:$sortid) . "/" . $sortdirs."/";
	                        else $sortdirs = ($usename?$subsort["dirname_$sortid"]:$sortid)."/";
	                        $sorthtmllink = $outdirs.$subsort["dirname_$sortid"].".".HTMLEXT; //.rawurlencode(mkfilename($filenamemethod,$sortinfo['title'],2)).$sortid."_".ceil($subsort["total_$sortid"]/$subsort["perpage_$sortid"]).".".HTMLEXT;
	                        $navbit = "
&nbsp;>&nbsp;
";
	                        $navbit .= "
<a href='$sorthtmllink' class='classlinkclass'>$title</a>
";
	                        $outdirs = "../" . $outdirs;
	                        $g_depth++;
	                        $g_o_back2root="../".$g_o_back2root;//当前对应的根目录相对路径
	                        $navbit = makesortnavbarbit($parentsortid, $parentsort) . $navbit;
	                        $outdirs = "";
	                }
	
	                $writedir .= $sortdirs."/";
	        }
        }
        return $navbit;
} 

function makearticlenavbar($article = array()) {
        global $DB_bbs, $db_prefix, $phparticletitle, $phparticleurl,$g_o_back2root, $parentsort;
        $navbit .= makesortnavbarbit($article['sortid'], $parentsort, 1);
        $navbit .= "
&nbsp;>&nbsp;
";
        $navbit .= "
$article[title]
";

        $navbar = "
你的位置：<a href='$homepage/' class='classlinkclass'>首页</a>&nbsp;>&nbsp;<a href='$phparticleurl' class='classlinkclass'>$phparticletitle</a>$navbit
";
        return $navbar;
}
?>

<?
function makehot_recommend_articlelist() {
        global $phparticleurl,$g_o_back2root,$g_back2path,$filenamemethod,$loadsystem_suffix;
        global $subsort;
        global $DB_bbs,$DB, $db_prefix;
        global $styleid,$style,$forumlist,$tablelist,$aconvertlist,$sconvertlist,$atconvertlist,$db_prefix_bbs,$dbname_bbs;
	$DB->selectdb();
        $cache = $DB->fetch_one_array("SELECT * FROM " . $db_prefix . "cache_bbs
                                                 WHERE name='sort_" . $styleid . "_articlelist".$loadsystem_suffix."'");

        if (!empty($cache) AND $cache['expiry'] == 0) { // 未过期
                $articlelist = unserialize($cache['content']);
        } else {
        	$DB_bbs->selectdb2($dbname_bbs);
                $sorts = $DB_bbs->query("SELECT ".$tablelist['sort']['sortid']." AS sortid,".$tablelist['sort']['title']." AS title FROM " . $db_prefix_bbs . $forumlist['sort']);
                while ($sortinfo = $DB_bbs->fetch_array($sorts)) {
                	$sortdir = get_sortdirs($sortinfo['sortid']);
                	$g_back2path = get_back2path($sortdir)."..";
                        $subsortids = getsubsorts($sortinfo['sortid']);
                        
                        unset($hotarticlelist);
                        unset($hotsortarticlelistbit);
                        $sortinfo['hotarticlenum'] = 10;//intval($sortinfo['hotarticlenum']);
                        if ($sortinfo['hotarticlenum'] > 0) {
                        	$DB_bbs->selectdb2($dbname_bbs);
                                $articles = $DB_bbs->query("SELECT ".$aconvertlist." FROM " . $db_prefix_bbs . $forumlist['article']." AS article
	                                           WHERE ".$tablelist['article']['sortid']." IN (0" . $subsortids . ")
	                                           ORDER BY views DESC LIMIT 10");// AND article.visible=1
	                        $DB->selectdb();
                                while ($article = $DB_bbs->fetch_array($articles)) {
                                        $childdirs = preg_replace("@" . $sortdir . "/@", "", mkdirname($article['sortid'],"",$article['date'],0,0));//get_sortdirs($article['sortid'])."/"
                                        if (empty($childdirs))$childdirs = "./";
                                        $articlehtmllink = $childdirs . rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//date("Y_m", $article['date']) . "/".
                                        $hotsortarticlelistbit .= "";
                                } 
                                $DB_bbs->free_result($articles);
                                $hotarticlelist = "
    <div class='rightblock'>
      <div class='righttitleico'>&nbsp;</div>
      <div class='righttitlename'>&nbsp;&nbsp;热门文章</div>
      <div class='clear'>&nbsp;</div>
      <div class='rightlist rightlistnoad'> <ul>
<!-- BEGIN hotsortarticlelistbit -->
<li><a href='$articlehtmllink' title='$article[title]'>$article[title]</a><!-- $article[views] --></li>
<!-- END hotsortarticlelistbit -->
      </ul> </div>
    </div>
";
                        } 
                        $articlelist['hot'][$sortinfo['sortid']] = $hotarticlelist;

                       /* unset($poparticlelist);
                        unset($popsortarticlelistbit);
                        $sortinfo[ratearticlenum] = intval($sortinfo[ratearticlenum]);
                        if ($sortinfo['ratearticlenum'] > 0) {
                                $articles = $DB_bbs->query("SELECT articleid,article.sortid,date,article.title,views,(totalscore/voters) as averagescore,voters FROM " . $db_prefix . "article AS article
								                                           LEFT JOIN " . $db_prefix . "sort AS sort
								                                             ON article.sortid=sort.sortid
								                                           WHERE voters>0 AND sort.sortid IN (0" . $subsortids . ") AND article.visible=1
								                                           ORDER BY averagescore DESC
								                                           LIMIT $sortinfo[ratearticlenum]");
                                while ($article = $DB_bbs->fetch_array($articles)) {
                                        $childdirs = preg_replace("@" . $sortdir . "/@", "", mkdirname($article['sortid'],"",$article['date'],0,0));//get_sortdirs($article['sortid'])."/"
                                        if (empty($childdirs))$childdirs = "./";
                                        $articlehtmllink = $childdirs . rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//date("Y_m", $article['date']) . "/".
                                        eval("\$popsortarticlelistbit .= \"" . gettemplate('popsortarticlelistbit') . "\";");
                                } 
                                $DB_bbs->free_result($articles);
                                eval("\$poparticlelist = \"" . gettemplate('poparticlelist') . "\";");
                        } 
                        $articlelist['rate'][$sortinfo['sortid']] = $poparticlelist;*/
                }
                $DB->selectdb();
                if (!empty($cache) AND $cache[expiry] == 1) {
                        $DB->query("UPDATE " . $db_prefix . "cache_bbs SET
	                                    content='" . addslashes(serialize($articlelist)) . "',
	                                    expiry=0
	                                    WHERE name='sort_" . $styleid . "_articlelist".$loadsystem_suffix."'");
                } elseif (empty($cache)) {
                        $DB->query("INSERT INTO " . $db_prefix . "cache_bbs (name,content,expiry) VALUES
	                                    ('sort_" . $styleid . "_articlelist".$loadsystem_suffix."','" . addslashes(serialize($articlelist)) . "',0)");
                }
        } 

        return $articlelist;
}
?>

<?
$counter = 0;
function makesortlist() {
        global $phparticleurl,$g_o_back2root,$loadsystem_suffix;
        global $subsort;
        global $counter;
        global $DB, $db_prefix;
        global $styleid;
        global $style;
	$DB->selectdb();
        $cache = $DB->fetch_one_array("SELECT * FROM " . $db_prefix . "cache_bbs
                                                 WHERE name='template_" . $styleid . "_sortlist".$loadsystem_suffix."'");

        if (!empty($cache) AND $cache['expiry'] == 0) { // 未过期
                $sortlist = $cache['content'];
        } else {
                unset($sortlistbit_level1);
                if (is_array($subsort[-1]))
                        foreach ($subsort[-1] as $sort['sortid'] => $sort['title']) {
                        $counter++;
                        unset($sortlistbit_level3);
                        unset($sortlistbit_level2);
                        $sorthtmllink = HTMLDIR . "/" . mkdirname($sort['sortid'],-1,0,0,0) . HTMLEXT; //$sort['sortid'] . "/index."
                        if (isset($subsort[$sort['sortid']])) {
                                foreach ($subsort[$sort['sortid']] as $childsort['sortid'] => $childsort['title']) {
                                        $childsorthtmllink = HTMLDIR ."/". mkdirname($childsort['sortid'],-1,0,0,0) . "index." . HTMLEXT; // $sort['sortid'] . "/" . $childsort['sortid']
                                        $sortlistbit_level3 .= "";
                                } 
                                $sortlistbit_level2 = "";
                                $sort['plusorminus'] = "<img id=\"nav_img_$counter\" src=\"$phparticleurl/$style[imagesfolder]/expand.gif\" align=absmiddle style=\"cursor: hand\" onClick=\"ToggleNode(nav_tr_$counter,nav_img_$counter)\" vspace=\"2\" hspace=\"2\">";
                        } else {
                                $sort['plusorminus'] = "<img src=\"$phparticleurl/$style[imagesfolder]/expand.gif\" align=absmiddle vspace=\"2\" hspace=\"2\">";
                        } 
                        $sortlistbit_level1 .= "";
                }
                $sortlist = "
<!-- BEGIN sortlistbit_level1 -->
    <div class='textad'>
      <div class='textadleft'><a href='./$sorthtmllink'>$sort[title]</a></div>
      <div class='textadright'>
<div id='adlist'>
<ul>
<!-- BEGIN sortlistbit_level2 -->
<!-- BEGIN sortlistbit_level3 -->
<li><a href=$childsorthtmllink rel='external'>$childsort[title]</a></li>
<!-- END sortlistbit_level3 -->
<!-- END sortlistbit_level2 -->
</ul>
</div>
      </div>
      </div>
<div class='mainline'>&nbsp;</div>
<!-- END sortlistbit_level1 -->
";
                if (!empty($cache) AND $cache[expiry] == 1) {
                        $DB->query("UPDATE " . $db_prefix . "cache_bbs SET
                                    content='" . addslashes($sortlist) . "',
                                    expiry=0
                                    WHERE name='template_" . $styleid . "_sortlist".$loadsystem_suffix."'");
                } elseif (empty($cache)) {
                        $DB->query("INSERT INTO " . $db_prefix . "cache_bbs (name,content,expiry) VALUES
                                    ('template_" . $styleid . "_sortlist".$loadsystem_suffix."','" . addslashes($sortlist) . "',0)");
                }
        } 

        return $sortlist;
}
?>