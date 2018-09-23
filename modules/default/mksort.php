<?php

$cpforms->cpheader();
if (!$_GET['createlimit']) {
        $cpforms->formfooter(Array('button'=>Array('submit'=>array('type'=>'hidden'))));
        $cpforms->formheader(array('title' => '分类静态生成',
                        'name' => 'sortForm',
                        'method' => 'get',
                        'action' => 'htmlauto.php'));
	$cpforms->maketd(Array("<input type='checkbox' name='rnn' ".($_GET[rnn]==1?"checked":"")." value=1/>更新所有？",""));

        /*$cpforms->makeinput(array('text' => '需要生成的分类开始ID(默认为0)',
                        'name' => 'startsortid',
                        'value' => $_GET[sortid]));
        $cpforms->makeinput(array('text' => '需要生成的分类结束ID(默认为分类id的最大数)',
                        'name' => 'endid',
                        'value' => $_GET[sortid]));
        $cpforms->makeinput(array('text' => '分类开始的分页号',
                        'name' => 'pagenum',
                        'value' => $datainfo['pagenum']));*/

        $cpforms->makeinput(array('text' => '每次生成的静态页的个数',
                        'name' => 'createlimit',
                        'value' => 200));
        $cpforms->makehidden(array('name' => 'mod',
                        'value' => "mksort"));
        $cpforms->formfooter();
        $cpforms->cpfooter();
        exit;
}

$styleid = 1;
$style = getstyle();

$colorarray = array('', 'red', 'orange', 'yellow', 'green', 'cyan', 'blue', 'purple', 'gray');

cachesorts();

$sortlist1 = makesortlist();
$hot_rate_articlelist = makehot_recommend_articlelist();
$tag_articlelist_backup=$tag_articlelist;
$createcount = 0;
if (!intval($_GET['createlimit'])) $_GET['createlimit'] = 200;
if (!intval($_GET['startsortid']) || intval($_GET['startsortid']) < 0) $_GET['startsortid'] = 0;
if (!intval($_GET['endid'])) $sqlopt = "";
else $sqlopt = "AND sortid <= $_GET[endid]";
if($_GET[rnn])
$sorts = $DB->query("SELECT * FROM " . $db_prefix . "sort WHERE sortid >= $_GET[startsortid] $sqlopt ORDER BY sortid LIMIT " . $_GET['createlimit']);
else
{
	$lcount=1;
	if(!$_GET['startsortid'])
	{
		$logcount = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM " . $db_prefix . "htmllog WHERE type = 2");
		$lcount=$logcount['count'];
		
	}
	if($lcount==0)
	{
		$_GET[rnn] = 1;
		$sorts = $DB->query("SELECT * FROM " . $db_prefix . "sort WHERE sortid >= $_GET[startsortid] $sqlopt ORDER BY sortid LIMIT " . $_GET['createlimit']);
	}
	else
	{
		$sorts = $DB->query("SELECT * FROM " . $db_prefix . "sort s left join " . $db_prefix . "htmllog l on s.sortid=l.startid WHERE s.sortid >= $_GET[startsortid] and l.type=2 ORDER BY sortid LIMIT " . $_GET['createlimit']);
	}
}
while ($sortinfo = $DB->fetch_array($sorts)) {
        if ($createcount >= $_GET['createlimit']) {
                break;
        }
        $sortid = $sortinfo['sortid'];
        $g_o_back2root="..";
        $navbar = makesortnavbar($sortid);//放到最前面
        if (!empty($sortinfo['styleid']) && $sortinfo['styleid'] != $styleid) { // 复位风格
                $styleid = $sortinfo['styleid'];
                $style = getstyle();

                if (empty($noheader)||$dirdepth!=$g_depth) {
				//	eval("\$header = \"" . gettemplate('header') . "\";");
				//	eval("\$footer = \"" . gettemplate('footer') . "\";");
                } 
                cachesorts();
                $sortlist1 = makesortlist();
                $hot_rate_articlelist = makehot_recommend_articlelist();
        }
        $sortinfo['description'] = str_replace("  ", "&nbsp;&nbsp;", nl2br($sortinfo[description]));
        $sortdir = mkdirname($sortinfo['sortid'],-1,0,0,0);//get_sortdirs($sortinfo['sortid'])
        $writedir = HTMLDIR . "/" . $sortdir;
        $dirs = explode("/", $sortdir);
        $sort_depths = get_sortdepths($sortid);
        if($dirdepth!=$g_depth)
        {
			$sortlist = preg_replace("@(\.|\.\.|)/" . HTMLDIR . "/" . $dirs[0] . "/@", str_repeat("../", $sort_depths-1), $sortlist1);
			$sortlist = preg_replace("@(\.|\.\.|)/" . HTMLDIR . "/@", str_repeat("../", $sort_depths), $sortlist);
			if(!empty($tag_articlelist_backup[text][defaultsys]))
			{
				$g_back2path = get_back2path($sortdir);
				foreach($tag_articlelist_backup[text][defaultsys] AS $key=>$tagtmp)
				$tag_articlelist[text][defaultsys][$key] = preg_replace("@\"./@","\"".$g_back2path."../",$tagtmp);
			}
		}

        unset($articlelist);

        $perpage = $sortinfo['perpage'];

        $subsortids = getsubsorts($sortid);
        /*
$total = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."article AS article
                                      WHERE sortid IN (0".$subsortids.") AND visible=1");

$totalresults = $total['count'];
*/
        $totalresults = $subsort["count_$sortid"];

        $totalpages = ceil($totalresults / $perpage);
        if (!$totalpages) $totalpages = 1;
        // hot rate article list
        $hotarticlelist = $hot_rate_articlelist['hot'][$sortid];
        $poparticlelist = $hot_rate_articlelist['rate'][$sortid];
        if($sortinfo['pagenum'])
        {
    		if($sortinfo['pagenum']>$_GET['pagenum']){
    			$_GET['pagenum'] = $sortinfo['pagenum'];
    		}
    	}
    	if (!intval($_GET['pagenum'])) $_GET['pagenum'] = 1;
        for($pagenum = $_GET['pagenum']; $pagenum <= $totalpages; $pagenum ++) {
        	$prefilename=mkfilename($filenamemethod,$sortinfo['title'],2);
                $writename = $prefilename . $sortinfo['sortid'] . "_" . intval($pagenum);
                
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
<title><?=$phparticletitle?> - <?=$sortinfo[title]?> - Powered by phpArticle</title>
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

<script src="<?=$g_o_back2root?>/admin/configs/pg.js"></script>
<div class="mainline">&nbsp;</div>

<?
// mainsortlist
        unset($subsortlist);
        unset($subsortlistbit_level1);
        if (isset($subsort[$sortid])) {
                $division = $sortinfo['division_sort'];
                $counter = 0;
                $tablewidth = floor(100 / $division);

                foreach ($subsort[$sortid] as $sort['sortid'] => $sort['title']) {
                        $sorthtmllink = mkdirname($sort['sortid'],-1,0,0,0).rawurlencode(mkfilename($filenamemethod,$sort['title'],2)) . $sort['sortid'] . "_" . ceil($subsort["total_$sort[sortid]"] / $subsort["perpage_$sort[sortid]"]) . "." . HTMLEXT;//$sort['sortid'] . "/"
                        unset($subsortlistbit_level2);
                        if ($counter == 0) {
                                $subsortlistbit_level1 .= "<tr bgcolor=\"$style[firstalt]\"\">";
                        } 
                        $subsortlistbit_level1 .= "<td nowrap valign=\"top\" width=\"$tablewidth%\">";
                        if (isset($subsort[$sort['sortid']])) {
                                unset($subsortlistbit_level3);
                                foreach($subsort[$sort['sortid']] as $childsort['sortid'] => $childsort['title']) {
                                        // 相对地址
                                        $childsorthtmllink = mkdirname($childsort['sortid'],-1,0,0,0).rawurlencode(mkfilename($filenamemethod,$childsort['title'],2)) . $childsort['sortid'] . "_" . ceil($subsort["total_$childsort[sortid]"] / $subsort["perpage_$childsort[sortid]"]) . "." . HTMLEXT;//$sort['sortid'] . "/" . $childsort['sortid'] . "/"
                                        $subsortlistbit_level3 .="";
                                }
                                $subsortlistbit_level2 = "
<li>
<!-- BEGIN subsortlistbit_level3 -->
<a href=$childsorthtmllink rel='external'>$childsort[title]</a>
<!-- END subsortlistbit_level3 -->
</li>
";
                        }
                        $subsortlistbit_level1 .= "";
                        $subsortlistbit_level1 .= "</td>\n";

                        if (++$counter % $division == 0) {
                                $subsortlistbit_level1 .= "</tr>";
                                $counter = 0;
                        } 
                }
                if ($counter != 0) {
                        for (;$counter < $division;$counter++) {
                                $subsortlistbit_level1 .= "<td></td>\n";
                        } 
                } 
                ?>
<div class=subsort>
	<div class='nav'>
		<div id='nav-sort-left'>
		<span><b>当前子分类</b></span></div>
		<div id='nav-sort-right'>
<?=
<!-- BEGIN subsortlistbit_level1 -->
<a href='./$sorthtmllink'  class='grey'>$sort[title]</a>
<!-- END subsortlistbit_level1 -->
?>
		</div>
	</div>
</div>
<div class='mainline'>&nbsp;</div>
<?
        }
        ?>

<!-- 主栏目开始 -->
<div id="wrap">
<div class="maincolumn">
	<div class="mainleft">

		<div class="classnav">
		<div class="sublisttitleico">&nbsp;</div>
		<div class="sublisttitlebg">
		<div class="sublisttitlename"><?=$navbar?></div>
		</div>
		</div>


<div class="mainline">&nbsp;</div>


<?
$offset = ($pagenum-1) * $perpage;
                unset($sorthome_articlelistbit);
                if ($totalresults > 0) {
                        $from = $offset + 1;
                        if ($pagenum == $totalpages) {
                                $to = $totalresults;
                                $offset -= $perpage-($totalresults-$offset);
                                if($offset<0)$offset=0;
                        } else {
                                $to = $offset + $perpage;
                        }
                } else {
                        $from = 0;
                        $to = 0;
                }
                if ($totalpages == 1) {
                	$total = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."article AS article
                                      WHERE sortid IN (0".$subsortids.") AND visible=1");
					$totalresults = $total['count'];
					$offset=$totalresults-$perpage;
					if($offset<0)$offset=0;
					$articles = $DB->query("SELECT * FROM " . $db_prefix . "article AS article
                                     WHERE sortid IN (0" . $subsortids . ") AND visible=1
                                     ORDER BY `date` ASC
                                     LIMIT $offset,$perpage");
                } else
                        $articles = $DB->query("SELECT * FROM " . $db_prefix . "article AS article
                                     WHERE sortid =$sortid AND visible=1
                                     ORDER BY `date` ASC
                                     LIMIT $offset,$perpage");

                if ($DB->num_rows($articles) > 0) {
                        $counter = 0;
                        $row = 0;
                        $division = $sortinfo[division_article];
                        $tablewidth = floor(100 / $division);

                        while ($article = $DB->fetch_array($articles)) {
                        	if($article['highlight']) {
					$string = sprintf('%02d', $article['highlight']);
					$stylestr = sprintf('%03b', $string[0]);

					$article['highlight'] = 'style="';
					$article['highlight'] .= $stylestr[0] ? 'font-weight: bold;' : '';
					$article['highlight'] .= $stylestr[1] ? 'font-style: italic;' : '';
					$article['highlight'] .= $stylestr[2] ? 'text-decoration: underline;' : '';
					$article['highlight'] .= $string[1] ? 'color: '.$colorarray[$string[1]] : '';
					$article['highlight'] .= '"';
				} else {
					$article['highlight'] = '';
				}
                                if ($article['sortid'] == $sortid)
                                        $articlehtmllink = mkdirname($article['sortid'],-1,$article['date'],1,0).rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//date("Y_m", $article['date']) . "/"
                                else $articlehtmllink = mkdirname($article['sortid'],$sortid,$article['date'],0,1).rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//get_sortsubdirs($article['sortid'], $sortid) . "/" . date("Y_m", $article['date']) . "/"
                                $article['time'] = padate($timeformat_article, $article['date']);
                                $article['date'] = padate($dateformat_article, $article['date']);

                                if (!empty($article['imageid'])) {
                                        // $sorthome_articlelistbit_img = "<img src=\"showimg.php?iid=$article[imageid]\" border=\"0\" vspace=\"2\" hspace=\"2\" align=\"left\">";
                                        $sorthome_articlelistbit_img = "
<img src='$g_o_back2root/showimg.php?iid=$article[imageid]' border='0' vspace='2' hspace='2' align='left'>
";
                                } else {
                                        $sorthome_articlelistbit_img = "";
                                } 
                                if ($counter == 0) {
                                        if ($row++ % 2 == 1) {
                                                $bgcolor = "$style[firstalt]";
                                        } else {
                                                $bgcolor = "$style[secondalt]";
                                        } 
                                        $articlelistbit1 .= "<tr bgcolor=\"$bgcolor\" align=\"center\">";
                                }
                                $article[description] = str_replace("  ", "&nbsp;&nbsp;", nl2br($article[description]));
                                $articlelistbit1 .= "<td nowrap valign=\"top\" width=\"$tablewidth%\">";
                                $articlelistbit1 .= "";
                                $articlelistbit1 .= "</td>\n";

                                if (++$counter % $division == 0) {
                                        $articlelistbit1 .= "</tr>";
                                        $counter = 0;
                                }
                                $sorthome_articlelistbit = $articlelistbit1 . $sorthome_articlelistbit;
                                unset($articlelistbit1);
                        }
                        if ($counter != 0) {
                                for (;$counter < $division;$counter++) {
                                        $sorthome_articlelistbit .= "<td></td>\n";
                                }
                        }

                        $sorthtmlpagelink = rawurlencode($prefilename) . $sortid;
                        $pagelinks = makepagelink($sorthtmlpagelink, $pagenum, $totalpages);
                        $pagenav = "";

                        ?>
<div class="sublist">
<div class="onesubnewslist">
	<ul>
<?=
<!-- BEGIN sorthome_articlelistbit -->
<li><span>($article[date]  $article[time])</span><a title='$article[title]' href='$articlehtmllink'>$article[title]</a></li>
<!-- END sorthome_articlelistbit -->
?>
	</ul>
</div>
<div class="clear">&nbsp;</div>
<?=
<!-- BEGIN pagenav -->
<div id='sublistfooter'>
     <div class='left'>共 <b>$totalresults</b> ,显示 <b>$from -
      $to</b></div>
     <div class='right'>$pagelinks</div>
</div>
<!-- END pagenav -->
?>
</div>
<?
                } else {
                        ?>
<table class=border width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/information.gif" border="0" align="absmiddle"><span class="normalfont"><b>当前分类仍未有任何文章</b></span></td>
  </tr>
</table>

<?
                }
?>


  </div>

	
	<div class="mainright">

		<div class="rightblock">
		<div class="righttitleico">&nbsp;</div>
		<div class="righttitlename">&nbsp;&nbsp;搜索工具</div>
		<div class="clear">&nbsp;</div>
		<div class="search">




		</div>
	</div>

<div class="mainline">&nbsp;</div>
<?=$hotarticlelist?>

    <div class="rightblock">
      <div class="righttitleico">&nbsp;</div>
      <div class="righttitlename">&nbsp;&nbsp;相关内容</div>
      <div class="clear">&nbsp;</div>
      <div class="rightlist rightlistad"> 

      </div>
    </div>






	</div>
</div>
<div class="clear">&nbsp;</div>
</div>
<script>
sortid = <?=$sortid?>;
paginationNum = <?=$disp_pagination_num?>;
startPageNum = parseInt(document.all.s1.innerText);
for(i=1;i<=paginationNum;i++)
{
	eval("linkid=document.all.s"+i);
	if(linkid != undefined)
	{
		id = linkid.innerText;
		linkid.innerText = paginationMax[sortid]-parseInt(id)+1;
	}else break;
}
</script>
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
                if ($pagenum == $totalpages) {
                        $writename = $subsort["dirname_".$sortinfo['sortid']];//"index";
                        dooutput($outputdata);
                }
                $createcount ++;
                $DB->free_result($articles);
                if ($createcount >= $_GET['createlimit'])break;
        }
        if ($createcount < $_GET['createlimit'])save_htmllog($sortid,$pagenum,$_GET['s']);
        $_GET['pagenum'] = 0;
}
$DB->free_result($sorts);
if ($createcount == 0 || ($_GET['endid'] && $sortid > $_GET['endid'])) {
	//	unlink(HTMLDIR."/updateing.txt");
		?>
		<script>
		if(top!=undefined)
		{
			var url = ""+top.document.location;
			if(url.indexOf("/admin/index.php")==-1)
			location="htmlauto.php?mod=mkarticle_bbs&auto=1&createlimit=200&type=2";
		}
		</script>
		<?
        echo "生成完毕!";
        $cpforms->cpfooter();
        exit;
} else {
       /* $htmlloginfo = $DB->fetch_one_array("SELECT htmllogid FROM " . $db_prefix . "htmllog WHERE startid=$sortid AND type = 2");
        if (!empty($htmlloginfo))
                $DB->query("UPDATE " . $db_prefix . "htmllog SET startid='$sortid',pagenum='" . ($pagenum-1) . "',dateline=" . time() . " WHERE startid=$sortid AND type = 2");
        else $DB->query("INSERT INTO " . $db_prefix . "htmllog (`type`,`startid`,`dateline`, `pagenum`) VALUES ('2','$sortid','" . time() . "','" . ($pagenum-1) . "')");
        */
	//	unlink(HTMLDIR."/updateing.txt");
        echo "<meta http-equiv=\"refresh\" content=\"1;url=htmlauto.php?mod=mksort&startsortid=$sortid&endid=$_GET[endid]&createlimit=$_GET[createlimit]&pagenum=$pagenum&auto=$_GET[auto]&rnn=$_GET[rnn]\">";
        $cpforms->cpfooter();
}

function save_htmllog($sortid,$pagenum){
	global $db_prefix,$DB;
	$htmlloginfo = $DB->fetch_one_array("SELECT htmllogid FROM " . $db_prefix . "htmllog WHERE startid=$sortid AND type = 2");
    if (!empty($htmlloginfo))
            $DB->query("UPDATE " . $db_prefix . "htmllog SET startid='$sortid',pagenum='" . ($pagenum-1) . "',dateline=" . time() . " WHERE startid=$sortid AND type = 2");
    else $DB->query("INSERT INTO " . $db_prefix . "htmllog (`type`,`startid`,`dateline`, `pagenum`) VALUES ('2','$sortid','" . time() . "','" . ($pagenum-1) . "')");
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