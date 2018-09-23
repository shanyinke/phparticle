<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<?php

$cpforms->cpheader();
if (!$_GET['createlimit']) {
	if(!$_GET['articleid'])
        $datainfo = $DB->fetch_one_array("SELECT * FROM " . $db_prefix . "htmllog WHERE type = 1");
	else
	{
	$datainfo['startid']=$_GET['articleid'];
	}
	
	$cpforms->formheader(array('title' => '文章静态生成',
	            'name' => 'form1',
	            'method' => 'get',
	            'action' => 'htmlauto.php'));
	$cpforms->makeinput(array('text' => '需要生成的文章开始ID',
	            'name' => 'startaid',
	            'value' => $datainfo['startid']));
	$cpforms->makeinput(array('text' => '需要生成的文章结束ID',
	            'name' => 'endid',
	            'value' => $_GET['articleid']));
	$cpforms->makeinput(array('text' => '每次生成文章静态页个数',
	            'name' => 'createlimit',
	            'value' => 200));
	$cpforms->makehidden(array('name' => 'mod',
	            'value' => "mkarticle"));
	$cpforms->formfooter();
	$cpforms->cpfooter();
    exit;
}else if($_GET['auto']&&!$_GET['startaid'])
{
	$datainfo = $DB->fetch_one_array("SELECT startid FROM " . $db_prefix . "htmllog WHERE type = 1");
	$_GET['startaid']=$datainfo['startid'];
}

$styleid = 1;
$style = getstyle();

cachesorts();
$hot_rate_articlelist = makehot_recommend_articlelist();
$tag_articlelist_backup=$tag_articlelist;
//$tag_inde=make_tag_articlelist("get","index","new","bbs",2,10,30,'home_myart');

// $sortlist = makesortlist();
if (!$_GET['createlimit']) $_GET['createlimit'] = 1000;
if (!$_GET['startaid'] || $_GET['startaid'] < 0) $_GET['startaid'] = 0;
if (!intval($_GET['endid'])) $sqlopt = "";
else $sqlopt = "AND articleid <= $_GET[endid]";
if($datainfo[dateline])
	$timelimit = "or lastupdate >$datainfo[dateline]";
$articles = $DB->query("SELECT * FROM " . $db_prefix . "article WHERE (articleid > $_GET[startaid] $timelimit) AND visible=1 $sqlopt ORDER BY articleid LIMIT $_GET[createlimit]");
while ($article = $DB->fetch_array($articles)) {
        if ($createcount >= $_GET['createlimit']) {
                break;
        }
        $articleid = &$article['articleid'];
        $g_o_back2root="..";
        $navbar = makearticlenavbar($article);
        if($article[sortid]!=$sortid&&isset($sortid))
	{
		$sort = $DB->fetch_one_array("SELECT styleid,sortid FROM " . $db_prefix . "sort WHERE sortid='$article[sortid]'");
	        $sortid=$article[sortid];
	        if (!empty($sort['styleid']) && $sort['styleid'] != $styleid) { // 复位风格
	                $styleid = $sort['styleid'];
	                $style = getstyle();

	                if (empty($noheader)||$dirdepth!=$g_depth) {
			//	eval("\$header = \"" . gettemplate('header') . "\";");
			//	eval("\$footer = \"" . gettemplate('footer') . "\";");
	                }
	                cachesorts();
	                // $sortlist = makesortlist();
	                $hot_rate_articlelist = makehot_recommend_articlelist();
	        }
	}

        if ($subdirs = mkdirname($article['sortid'],-1,$article['date'],0,0))//get_sortdirs($article['sortid'])
		$writedir = HTMLDIR . "/" . $subdirs;// . "/" . date("Y_m", $article['date']);
        else $writedir = HTMLDIR."/";//continue;
        $article[description] = htmlspecialchars(trim($article[description]));
        $articledate = $article[date];
        $article[date] = padate($dateformat_article, $article[date]);

        $pages = $DB->query("SELECT id,subhead FROM " . $db_prefix . "articletext WHERE articleid='$articleid' ORDER BY displayorder,id");

        $totalpages = $DB->num_rows($pages);
        unset($pagejump);
        unset($articlehome_pagejumpbit);
        if (!$_GET['pagenum']) $_GET['pagenum'] = 1;
        for($pagenum = $_GET['pagenum']; $pagenum <= $totalpages; $pagenum ++) {
                $offset = $pagenum-1;
                // $DB->query("UPDATE ".$db_prefix."article SET views=views+1 WHERE articleid='$articleid'");
                $articletext = $DB->fetch_one_array("SELECT subhead,articletext
                                            FROM " . $db_prefix . "articletext
                                            WHERE articleid='$articleid'
                                            ORDER BY displayorder,id LIMIT $offset,1");
		$prefilename=mkfilename($filenamemethod,$article['title'],1);
                $writename = $prefilename . $article['articleid'] . "_" . $pagenum;

			if($dirdepth!=$g_depth)
			{
                // sort hotarticles
                if ($showhotarticle == "1") {
                       // $hotarticlelist = gethotsort_articles($article['sortid']);
                       $g_back2path = get_back2path($subdirs)."..";
                        $hotarticlelist = preg_replace(Array("@\"(".$g_back2path."|)/@","@\"./@"),Array("\"".$g_back2path."/../","\"../"),$hot_rate_articlelist['hot'][$article['sortid']]);
                }
                if(!empty($tag_articlelist_backup[text][defaultsys]))
				{
	        	if(!$g_back2path)
	        		$g_back2path = get_back2path($subdirs)."..";
	        	foreach($tag_articlelist_backup[text][defaultsys] AS $key=>$tagtmp)
				$tag_articlelist[text][defaultsys][$key] = preg_replace("@\"./@","\"".$g_back2path."../",$tagtmp);
				}
			}
                //需要把全局模板放在最后，保证所有变量都已经生成。 
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
					  <h2><strong>日期：</strong><?=$article[date]?> 13:32:12&nbsp;&nbsp;<strong>点击：</strong><script src="<?=$phparticleurl?>/count.php?aid=<?=$articleid?>"></script>&nbsp;&nbsp;<strong>作者：</strong><?=$article[author]?>&nbsp;&nbsp;<strong>来源：</strong><a href='#' target=_blank><?=$article[source]?></a>  
<br>
<b><a href="<?=$g_o_back2root?>/index.php?mod=comment&articleid=<?=$articleid?>&action=add">发表评论</a></b> | <b><a href="<?=$g_o_back2root?>/index.php?mod=comment&articleid=<?=$articleid?>&action=view">查看评论</a></b> | <b><a href="<?=$g_o_back2root?>/index.php?mod=favorite&articleid=<?=$articleid?>&pagenum=<?=$pagenum?>&action=add">加入收藏</a></b> | <b><a href="<?=$g_o_back2root?>/index.php?mod=recommend&articleid=<?=$articleid?>&pagenum=<?=$pagenum?>">Email给朋友</a></b> | <b><a href="<?=$g_o_back2root?>/index.php?mod=print&articleid=<?=$articleid?>">打印本文</a></b> | 字体：[<A href="javascript:doZoom(16)">大</A> <A href="javascript:doZoom(14)">中</A> <A href="javascript:doZoom(12)">小</A>] </h2>
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



<fieldset>

                <span class=left>
<?
$votes = $DB->query("SELECT sum(vote) AS total,count(vote) as voters,vote
                             FROM " . $db_prefix . "articlerate
                             WHERE articleid='$articleid'
                             GROUP BY vote");
                if ($DB->num_rows($votes) > 0) {
                        while ($vote = $DB->fetch_array($votes)) {
                                $voter[$vote[vote]] = $vote[voters];
                                $scores[$vote[vote]] = $vote[total];
                        } 
                        $totalvoters = array_sum($voter);
                        $totalscores = array_sum($scores);
                } else {
                        $totalvoters = 0;
                        $totalscores = 0;
                } 
                $maxheight = 30;
                for ($i = 1;$i <= 10;$i++) {
                        if ($totalvoters == 0) {
                                $barheight[$i] = 1;
                        } else {
                                $barheight[$i] = floor($voter[$i] / $totalvoters * $maxheight);
                        } 
                } 

                if ($totalvoters > 0) {
                        $average = number_format($totalscores / $totalvoters, 2, '.', '');
                } else {
                        $average = 0;
                } 

                unset($rate);
                if ($pauserinfo[userid] != 0) {
                        $checkvote = $DB->fetch_one_array("SELECT * FROM " . $db_prefix . "articlerate
                                                WHERE articleid='$articleid' AND userid='$pauserinfo[userid]'");
                        /*if (empty($checkvote)) {
        if (!$pauserinfo[canratearticle] AND !$pauserinfo[isadmin]) {
            eval("\$rate = \"".gettemplate('articlehome_rate_nopermission')."\";");
        } else {
            eval("\$rate = \"".gettemplate('articlehome_rate')."\";");
        }
    } else {
        $checkvote[date] = padate("Y-m-d H:i:s A",$checkvote[date]);
        eval("\$rate = \"".gettemplate('articlehome_rated')."\";");
    }*/?>
                <form name="" method="post" action="<?=$g_o_back2root?>/feedback.php">
  <table width="0%" border="0" cellpadding="0">
    <tr>
      <td colspan="12"><b>给该文章评分</b></td>
    </tr>
    <tr>
      <td nowrap>差</td>
      <td nowrap align="center" valign="bottom">1</td>
      <td nowrap align="center" valign="bottom">2</td>
      <td nowrap align="center" valign="bottom">3</td>
      <td nowrap align="center" valign="bottom">4</td>
      <td nowrap align="center" valign="bottom">5</td>
      <td nowrap align="center" valign="bottom">6</td>
      <td nowrap align="center" valign="bottom">7</td>
      <td nowrap align="center" valign="bottom">8</td>
      <td nowrap align="center" valign="bottom">9</td>
      <td nowrap align="center" valign="bottom">10 </td>
      <td nowrap>好</td>
    </tr>
    <tr>
      <td nowrap></td>
      <td nowrap align="center">
        <input type="radio" name="vote" value="1">
      </td>
      <td nowrap align="center">
        <input type="radio" name="vote" value="2">
      </td>
      <td nowrap align="center">
        <input type="radio" name="vote" value="3">
      </td>
      <td nowrap align="center">
        <input type="radio" name="vote" value="4">
      </td>
      <td nowrap align="center">
        <input type="radio" name="vote" value="5">
      </td>
      <td nowrap align="center">
        <input type="radio" name="vote" value="6">
      </td>
      <td nowrap align="center">
        <input type="radio" name="vote" value="7">
      </td>
      <td nowrap align="center">
        <input type="radio" name="vote" value="8">
      </td>
      <td nowrap align="center">
        <input type="radio" name="vote" value="9">
      </td>
      <td nowrap align="center">
        <input type="radio" name="vote" value="10">
      </td>
      <td nowrap align="center">
        <input type="hidden" name="articleid" value="<?=$articleid?>">
        <input type="submit"  value="GO" class="button">
      </td>
    </tr>
  </table>
</form>
<?
                } else {
                        ?>
                <span class="normalfont"><b>如果你想对该文章评分, 请先<a href="<?=$g_o_back2root?>/index.php?mod=member&action=login">登陆</a>, 如果你仍未注册,请点击<a href="<?=$g_o_back2root?>/index.php?mod=register">注册链接</a>注册成为本站会员.</b></span>
                <?
                }
			$DB->free_result($votes);
?></span>
                <span class=right>
                  <table border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td><b>平均得分 <?=$average?>, 共 <?=$totalvoters?> 人评分</b></td>
                    </tr>
                    <tr>
                      <td>
                        <table border="0" cellspacing="1" cellpadding="2">
                          <tr>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[1]?>"></td>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[2]?>"></td>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[3]?>"></td>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[4]?>"></td>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[5]?>"></td>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[6]?>"></td>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[7]?>"></td>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[8]?>"></td>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[9]?>"></td>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[10]?>"></td>
                          </tr>
                          <tr>
                            <td nowrap align="center">1</td>
                            <td nowrap align="center">2</td>
                            <td nowrap align="center">3</td>
                            <td nowrap align="center">4</td>
                            <td nowrap align="center">5</td>
                            <td nowrap align="center">6</td>
                            <td nowrap align="center">7</td>
                            <td nowrap align="center">8</td>
                            <td nowrap align="center">9</td>
                            <td nowrap align="center">10</td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </span>
</fieldset>



<fieldset>
<table width="100%" border="0" cellpadding="4">
  <tr align="center">
    <td align="left" width="50%"><b>最新评论
(共有 <?=$article[comments]?> 条评论)</b></td>
    <td width="20%"><b>发表时间</b></td>
    <td><b>作者</b></td>
    <td><b>回复</b></td>
  </tr>
    
<?
unset($commentbits);
                if ($pauserinfo[canviewcomment] OR $pauserinfo[isadmin]) {
                        $comments = $DB->query("SELECT * FROM " . $db_prefix . "comment
                                     WHERE articleid='$articleid'
                                     ORDER BY lastupdate DESC
                                     LIMIT 5");

                        if ($DB->num_rows($comments) > 0) {
                                unset($commentbit);
                                while ($comment = $DB->fetch_array($comments)) {
                                        $comment[date] = padate("Y-m-d H:i a", $comment[date]);
                                        ?>
        <tr>
          <td>
          <div class=title><NOBR><a href="<?=$g_o_back2root?>/index.php?mod=message&action=view&commentid=<?=$comment[commentid]?>"><?=$comment[title]?></a></NOBR></div>
          </td>
          <td align="right" nowrap>
          <span><?=$comment[date]?></span>
          </td>
          <td align="center" nowrap>
          <span><?=$comment[author]?></span>
          </td>
          <td align="center" nowrap>
          <span><?=$comment[replies]?></span>
          </td>
        </tr>
<?
                                }
                        } 
                        $DB->free_result($comments);
                }
?>
  <tr align="right">
    <td colspan="4"><b><a href="<?=$g_o_back2root?>/index.php?mod=comment&action=view&articleid=<?=$articleid?>">更多评论...</a></b></td>
  </tr>
</table>
</fieldset>

<?
$relatedlinks = $DB->query("SELECT * FROM " . $db_prefix . "relatedlink WHERE articleid='$articleid'");

                if ($DB->num_rows($relatedlinks) > 0) {
                        unset($articlehome_relatedlinkbit);
                        unset($relatedlink);
                        while ($relatedlink = $DB->fetch_array($relatedlinks)) {
                                $articlehome_relatedlinkbit .= "";
                        } 
                        ?>
      <div>&nbsp;&nbsp;相关链接
      <div> <ul>
<!-- BEGIN articlehome_relatedlinkbit -->
<li>&nbsp;&nbsp;<a href='$relatedlink[link]' title='$relatedlink[text]' target='_blank'>$relatedlink[text]</a></li>
<!-- END articlehome_relatedlinkbit -->
      </ul> </div>
      </div>
<?
                }
                $DB->free_result($relatedlinks);
                /*
if (pa_isset($article['keyword'])) {
    $keywords = explode(",",$article['keyword']);
    $keywordcounter = count($keywords);
    unset($kc);
    for ($i=0;$i<$keywordcounter;$i++) {
         $kc[] = "title LIKE '%".addslashes(htmlspecialchars($keywords[$i]))."%'";
    }

    $kcsql = implode(" OR ",$kc);
    $relatedarticles = $DB->query("SELECT * FROM ".$db_prefix."article WHERE
                                            ($kcsql) AND articleid!='$articleid'
                                            AND sortid IN (0".getsubsorts($article[sortid]).")
                                            AND visible=1
                                            ORDER BY date DESC
                                            LIMIT 10");
	unset($relatedarticlebits);
    if ($DB->num_rows($relatedarticles)>0) {
			//	unset($relatedarticle);
				unset($relatedarticlebit);
        while ($relatedarticle = $DB->fetch_array($relatedarticles)) {
        	   $relevanthtmllink = "../../".$relatedarticle['sortid']."/".date("Y_m",$relatedarticle['date'])."/".rawurlencode(mkfilename($filenamemethod,$relatedarticle['title'],1)).$relatedarticle['articleid']."_1.".HTMLEXT;
               $relatedarticle[date] = padate("m-d h:i a",$relatedarticle[date]);
               eval("\$relatedarticlebit .= \"".gettemplate('articlehome_relatedarticlebit')."\";");

        }
        eval("\$relatedarticlebits = \"".gettemplate('articlehome_relatedarticle')."\";");
    }
    $DB->free_result($relatedarticles);
}*/
?>

<?
unset($commentbox);
                if ($pauserinfo['cancomment'] OR $pauserinfo['isadmin']) {
                        if ($pauserinfo['userid'] != 0) {
                                ?>
<form method="post" action="<?=$g_o_back2root?>/index.php?mod=comment" onSubmit="return process_data(this)">

<fieldset><legend>发表评论</legend>
      <div>

		<label>您的标题:</label>
        <input type="text" name="title" maxlength="50" size="50">
      </div>
      <div>
		<label>您的内容:</label>


        <textarea name="message" cols="70" rows="6"></textarea>
      </div>
	  <div class=enter>		
        <input type="hidden" name="articleid" value="<?=$articleid?>">
        <input type="hidden" name="action" value="doinsert">
        <input type="submit" value=" 发  表 " class="buttot" name="submit">
        <input type="submit" name="preview" value=" 预  览 " class="buttot">
        <input type="reset" value=" 重  置 " class="buttot" name="reset">
      </div>
</fieldset>
</form>

<script type="text/javascript">
<!--
function process_data(theform) {

                if (theform.title.value=="") {
    alert("请输入标题!");
    theform.title.focus();
    return false;
                }

                if (theform.message.value=="") {
    alert("请填写内容!");
    theform.message.focus();
    return false;
                }

}

//-->
</script>
<?
                        } 
                }
?>
		</div>
		<div class="tool">
			<span></span>
			<a href="<?=$g_o_back2root?>/index.php?mod=comment&articleid=<?=$articleid?>&action=add" class="button_content" title="添加评论" target="_self">添加评论</a>
			<a href="<?=$g_o_back2root?>/index.php?mod=comment&articleid=<?=$articleid?>&action=view" class="button_content" title="浏览评论" target="_self">浏览评论</a>
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
            
<?
unset($creat_jumpopts);
                if ($totalpages > 1 && !$creat_jumpopts) {
                        $page = 1;
                        while ($pg = $DB->fetch_array($pages)) {
                                $articlehtmllink = "../" . mkdirname($article['sortid'],1,$articledate,1,0).rawurlencode($prefilename) . $article['articleid'] . "_$page." . HTMLEXT;//date("Y_m", $articledate) . "/"
                                $subhead[$page] = $pg[subhead];
                                $articlehome_pagejumpbit .= "";
                                $page++;
                        } 

                        if ($pagenum >= $totalpages) {
                                $articlehome_nextpage = "";
                        } else {
                                $nextpagenum = $pagenum + 1;
                                $nextarticlehtmllink = "../" . date("Y_m", $articledate) . "/".rawurlencode($prefilename) . $article['articleid'] . "_$nextpagenum." . HTMLEXT;
                                $nextsubhead = $subhead[$nextpagenum];
                                $articlehome_nextpage = "
            <span class='normalfont'><a href='$nextarticlehtmllink' title=$nextsubhead><b>下一页</b></a> <b><span class='arrow'>&raquo;</span></b></span>
            ";
                        }

                        ?>
              <form action="index.php?mod=article" method="get">
        <input type="hidden" name="articleid" value="<?=$article[articleid]?>">
        <select name="pagenum" onChange="window.location=(this.options[this.selectedIndex].value)">
          <option value="<?=$articlehtmllink?>" <?=$defaultselected?>>--《<?=$article[title]?>》章节列表--</option>
          <option value="#">--------------------</option>
            <?=<!-- BEGIN articlehome_pagejumpbit -->
            <option value='$articlehtmllink'> $page - $pg[subhead] </option>
            <!-- END articlehome_pagejumpbit -->
            ?>
        </select>
        <input type="submit" value="GO" class="button">
  </form>
  <?
                        $creat_jumpopts = 1; //flag
                        
                }
?>
            <img src="<?=$phparticleurl?>/images/ar2.gif" width="6" height="7" />&nbsp;
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
        $DB->free_result($pages);
}
$DB->free_result($articles);
if ($createcount == 0 || ($_GET['endid'] && $articleid > $_GET['endid'])) {
//	unlink(HTMLDIR."/updateing.txt");
		?>
		<script>
		if(top!=undefined)
		{
			var url = ""+top.document.location;
			if(url.indexOf("/admin/index.php")==-1)
			{				
			location="htmlauto.php?mod=mksort&auto=1&createlimit=200";
			}
		}
		</script>
		<?
		//echo "<meta http-equiv=\"refresh\" content=\"0;url=htmlauto.php?mod=mksort&auto=1&createlimit=200\">";
        echo "生成完毕!";
        $htmllogdata = $DB->query("SELECT htmllogid,startid FROM " . $db_prefix . "htmllog WHERE type = 1");
        if ($htmlloginfo = $DB->fetch_array($htmllogdata))
        {
        	if($_GET['startaid']>$htmlloginfo['startid'])
                $DB->query("UPDATE " . $db_prefix . "htmllog SET startid='" . $_GET['startaid'] . "',dateline=" . time() . " WHERE type=1");
        }
        else $DB->query("INSERT INTO " . $db_prefix . "htmllog (`type`,`startid`,`dateline`) VALUES ('1','" . $_GET['startaid'] . "','" . time() . "')");
        $cpforms->cpfooter();
} else {
	//	unlink(HTMLDIR."/updateing.txt");
        echo "<meta http-equiv=\"refresh\" content=\"1;url=htmlauto.php?mod=mkarticle&startaid=" . $articleid . "&endid=$_GET[endid]&createlimit=$_GET[createlimit]&pagenum=$pagenum&auto=$_GET[auto]\">";
        $cpforms->cpfooter();
}

?>


<?
function makesortnavbar($sortid) {
        global $DB, $db_prefix, $phparticletitle, $phparticleurl,$g_o_back2root, $parentsort;

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
        	}else if($singledir==1)
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
        	}else
        	{
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
        global $DB, $db_prefix, $phparticletitle, $phparticleurl,$g_o_back2root, $parentsort;
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
function makehot_recommend_articlelist($locate="index") {
        global $phparticleurl,$g_o_back2root,$g_back2path,$filenamemethod;
        global $subsort;
        global $DB, $db_prefix;
        global $styleid,$style,$tag_articlelist;
	$savename = $locate."_" . $styleid . "_articlelist";
        $cachedata = $DB->query("SELECT * FROM " . $db_prefix . "cache
                                                 WHERE name='sort_" . $styleid . "_articlelist' OR name='".$savename."'");
	if($tag_data1=$DB->fetch_array($cachedata))
	{
		if($tag_data1['name']!=$savename)
		{
			$cache=$tag_data1;
		}
		else $tag_articlelist=unserialize($tag_data1['content']);
	}
	if($tag_data2=$DB->fetch_array($cachedata))
	{
		if($tag_data2['name']!=$savename)
		{
			$cache=$tag_data2;
		}
		else $tag_articlelist=unserialize($tag_data2['content']);
	}
        if (!empty($cache) AND $cache['expiry'] == 0) { // 未过期
                $articlelist = unserialize($cache['content']);
        } else {
                $sorts = $DB->query("SELECT sortid,title,hotarticlenum,ratearticlenum FROM " . $db_prefix . "sort");
                while ($sortinfo = $DB->fetch_array($sorts)) {
                	$sortdir = get_sortdirs($sortinfo['sortid']);
                	$g_back2path = get_back2path($sortdir)."..";
                        $subsortids = getsubsorts($sortinfo['sortid']);
                        
                        unset($hotarticlelist);
                        unset($hotsortarticlelistbit);
                        $sortinfo['hotarticlenum'] = intval($sortinfo['hotarticlenum']);
                        if ($sortinfo['hotarticlenum'] > 0) {
                                $articles = $DB->query("SELECT articleid,article.sortid,date,article.title,views FROM " . $db_prefix . "article AS article
								                                           LEFT JOIN " . $db_prefix . "sort AS sort
								                                             ON article.sortid=sort.sortid
								                                           WHERE sort.sortid IN (0" . $subsortids . ") AND article.visible=1
								                                           ORDER BY views DESC LIMIT $sortinfo[hotarticlenum]");
                                while ($article = $DB->fetch_array($articles)) {
                                        $childdirs = preg_replace("@" . $sortdir . "/@", "", mkdirname($article['sortid'],"",$article['date'],0,0));//get_sortdirs($article['sortid'])."/"
                                        if (empty($childdirs))$childdirs = "./";
                                        $articlehtmllink = $childdirs . rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//date("Y_m", $article['date']) . "/".
                                        $hotsortarticlelistbit .= "";
                                }
                                $DB->free_result($articles);
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

                        unset($poparticlelist);
                        unset($popsortarticlelistbit);
                        $sortinfo[ratearticlenum] = intval($sortinfo[ratearticlenum]);
                        if ($sortinfo['ratearticlenum'] > 0) {
                                $articles = $DB->query("SELECT articleid,article.sortid,date,article.title,views,(totalscore/voters) as averagescore,voters FROM " . $db_prefix . "article AS article
								                                           LEFT JOIN " . $db_prefix . "sort AS sort
								                                             ON article.sortid=sort.sortid
								                                           WHERE voters>0 AND sort.sortid IN (0" . $subsortids . ") AND article.visible=1
								                                           ORDER BY averagescore DESC
								                                           LIMIT $sortinfo[ratearticlenum]");
                                while ($article = $DB->fetch_array($articles)) {
                                        $childdirs = preg_replace("@" . $sortdir . "/@", "", mkdirname($article['sortid'],"",$article['date'],0,0));//get_sortdirs($article['sortid'])."/"
                                        if (empty($childdirs))$childdirs = "./";
                                        $articlehtmllink = $childdirs . rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//date("Y_m", $article['date']) . "/".
                                        $popsortarticlelistbit .= "";
                                } 
                                $DB->free_result($articles);
                                $poparticlelist = "
    <div class='rightblock'>
      <div class='righttitleico'>&nbsp;</div>
      <div class='righttitlename'>&nbsp;&nbsp;推荐文章</div>
      <div class='clear'>&nbsp;</div>
      <div class='rightlist rightlistnoad'> <ul>
<!-- BEGIN popsortarticlelistbit -->
<li><a href='$articlehtmllink'>$article[title]</a></li>
<!-- END popsortarticlelistbit -->
        </ul> </div>
    </div>
";
                        }
                        $articlelist['rate'][$sortinfo['sortid']] = $poparticlelist;
                } 
                if (!empty($cache) AND $cache[expiry] == 1) {
                        $DB->query("UPDATE " . $db_prefix . "cache SET
	                                    content='" . addslashes(serialize($articlelist)) . "',
	                                    expiry=0
	                                    WHERE name='sort_" . $styleid . "_articlelist'");
                } elseif (empty($cache)) {
                        $DB->query("INSERT INTO " . $db_prefix . "cache (name,content,expiry) VALUES
	                                    ('sort_" . $styleid . "_articlelist','" . addslashes(serialize($articlelist)) . "',0)");
                } 
        } 

        return $articlelist;
}
?>

<?
$counter = 0;
function makesortlist() {
        global $phparticleurl,$g_o_back2root;
        global $subsort;
        global $counter;
        global $DB, $db_prefix;
        global $styleid;
        global $style;

        $cache = $DB->fetch_one_array("SELECT * FROM " . $db_prefix . "cache
                                                 WHERE name='template_" . $styleid . "_sortlist'");

        if (!empty($cache) AND $cache['expiry'] == 0) { // 未过期
                $sortlist = $cache['content'];
        } else {
                unset($sortlistbit_level1);
                if (is_array($subsort[-1]))
                        foreach ($subsort[-1] as $sort['sortid'] => $sort['title']) {
                        $counter++;
                        unset($sortlistbit_level3);
                        unset($sortlistbit_level2);
                        $sorthtmllink = HTMLDIR . "/" . mkdirname($sort['sortid'],-1,0,0,0)."index." . HTMLEXT;//$sort['sortid']
                        if (isset($subsort[$sort['sortid']])) {
                                foreach ($subsort[$sort['sortid']] as $childsort['sortid'] => $childsort['title']) {
                                        $childsorthtmllink = HTMLDIR . "/" . mkdirname($childsort['sortid'],-1,0,0,0) . "/index." . HTMLEXT;//$sort['sortid'] . "/" . $childsort['sortid']
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
                        $DB->query("UPDATE " . $db_prefix . "cache SET
                                    content='" . addslashes($sortlist) . "',
                                    expiry=0
                                    WHERE name='template_" . $styleid . "_sortlist'");
                } elseif (empty($cache)) {
                        $DB->query("INSERT INTO " . $db_prefix . "cache (name,content,expiry) VALUES
                                    ('template_" . $styleid . "_sortlist','" . addslashes($sortlist) . "',0)");
                }
        }

        return $sortlist;
}
?>

<?
function makearticlenavbar2($article = array()) {
        global $DB, $db_prefix, $phparticletitle, $phparticleurl,$g_o_back2root, $parentsort;
        $navbit .= makesortnavbarbit2($article['sortid'], $parentsort);
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

function makesortnavbarbit2($sortid, $parentsort, $articlesortdir="") {
        global $phparticleurl,$g_o_back2root, $writedir, $subsort,$filenamemethod;
        if ($sortid != -1) {
                foreach ($parentsort[$sortid] as $parentsortid => $title) {
                	if($articlesortdir=="")$articlesortdir=mkdirname($sortid,-1,0,0,0);//get_sortdirs($sortid)."/";
                        $sorthtmllink = HTMLDIR . "/" . $articlesortdir;//.rawurlencode(mkfilename($filenamemethod,$sort['title'],2)). $sortid . "_" . ceil($subsort["total_$sortid"] / $subsort["perpage_$sortid"]) . "." . HTMLEXT;
                        $navbit = "
&nbsp;>&nbsp;
";
                        $navbit .= "
<a href='$sorthtmllink' class='classlinkclass'>$title</a>
";
                        $articlesortdir=str_replace("/".$sortid."/","/",$articlesortdir);
                        $navbit = makesortnavbarbit2($parentsortid, $parentsort,$articlesortdir) . $navbit;
                } 
        } 
        return $navbit;
}
?>