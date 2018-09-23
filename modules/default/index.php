<?
cachesorts();
$hot_rate_articlelist = makehot_recommend_articlelist();
$sortlist = makesortlist();
ob_start();
ob_implicit_flush(0);
?>

<?
$cache = $DB->fetch_one_array("SELECT * FROM " . $db_prefix . "cache
			WHERE name='friendlink_" . $styleid . "'");
$friendlink = unserialize($cache['content']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

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
        if (!empty($cache) AND $cache['expiry'] == 0) { // δ����
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
      <div class='righttitlename'>&nbsp;&nbsp;��������</div>
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
      <div class='righttitlename'>&nbsp;&nbsp;�Ƽ�����</div>
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

        if (!empty($cache) AND $cache['expiry'] == 0) { // δ����
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
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
<title><?=$phparticletitle?></title>
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
				<li><a href="<?=$g_o_back2root?>/index.php?mod=usercp">��Ա���</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=member&action=login">��Ա��½</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=register">���ע��</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=search">�߼�����</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=search&action=lastupdate">������</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=contribute">��ҪͶ��</a></li>
				<li> <a href="<?=$g_o_back2root?>/index.php?mod=member&action=logout">�˳���½</a></li>
			</ul>
			</div>
		</div>
	</div>
	<div id="maintop">
		<div id="Logo"><a href="/"><img src="<?=$g_o_back2root?>/images/palogo.gif" alt="��ҳ" /></a></div>
		<div id="TopAds"><a href="http://www.phparticle.net/" target="_blank"><img src="<?=$g_o_back2root?>/images/topbanner.jpg" width="468" height="60" alt="" /></a></div>
		<div id="toprightmenu">
			<ul>
				<li><a href="<?=$phparticleurl?>" onClick="this.style.behavior='url(#default#homepage)';this.setHomePage('<?=$phparticleurl?>');return(false);" style="behavior: url(#default#homepage)">��Ϊ��ҳ</a></li>
				<li><a href=javascript:window.external.AddFavorite('<?=$phparticleurl?>','<?=$phparticletitle?>') title="ϣ����ϲ����վ">�����ղ�</a></li>
				<li><a href="mailto:semi.rock@gmail.com">��ϵ����</a></li>
			</ul>
		</div>
	</div>

	<div class="nav">

		<div class="nav-up-left"><a href="/" class="white">��ҳ</a>��<a href="<?=$g_o_back2root?>/html/2/" class="white">��Ʒ����</a>��<a href="<?=$g_o_back2root?>/cet/2/" class="white">ʹ�ð���</a>��<a href="<?=$g_o_back2root?>/bbshtmldata/1/13/" class="white">��վ����</a>��<a href="<?=$g_o_back2root?>/bbshtmldata/4/6/" class="white">�������</a>��<a href="<?=$g_o_back2root?>/bbshtmldata/1/12/" class="white">VC/C#/���</a>��<a href="<?=$g_o_back2root?>/bbshtmldata/1/9" class="white">UNIX/LINUX����</a>��<a href="<?=$g_o_back2root?>/bbs" class="white">֧����̳</a>��<a href="http://www.utspeed.com" class="white">���ٿƼ�</a>��<a href="http://proxygo.com.ru" class="white">�����Թ�</a>  <a href="http://mp3.utspeed.com" class="white">��������</a></div>

	</div>

	<div class="navline">&nbsp;</div>

	<div class="nav">
		<div class="nav-down-left"><a href="<?=$g_o_back2root?>/bbshtmldata/4/6/" class="white">��������</a>��<a href="http://music.utspeed.com" class="white">��������</a>��<a href="http://4tc.com.ru" class="white">������ַ</a>��<a href="http://article.utspeed.com" class="white">��ĬЦ��</a>��<a href="http://woman.utspeed.com" class="white">Ů������</a>��<a href="http://nuskin.net.ru" class="white">�������Ϲ����̳�</a>  <a href="http://bt.utspeed.com" class="white">��¿/����/emule</a></div>
		<div class="nav-down-right">
		<span>��ǰ����: <b><?=$onlineuser?></b></span></div>
	</div>







</div>

<div class="mainline">&nbsp;</div>
<!-- ����Ŀ��ʼ -->
<div class="maincolumn">
	<div class="mainleft">

		<div class="classnav">
		<div class="sublisttitleico">&nbsp;</div>
		<div class="sublisttitlebg">
		<div class="sublisttitlename"><MARQUEE onmouseover=this.stop() onmouseout=this.start() scrollAmount=2 scrollDelay=10>
		
<?
$nowdate = time();

$newx = $DB->query("SELECT news.*,user.username,user.userid FROM ".$db_prefix."news AS news
                                  LEFT JOIN ".$db_prefix."user AS user
                                       ON news.userid=user.userid
                                  WHERE startdate<'$nowdate' AND enddate>'$nowdate'
                                  ORDER BY startdate DESC,newsid DESC");
if ($DB->num_rows($newx)>0) {
    unset($news);
    unset($home_newsbit);
    if ($news = $DB->fetch_array($newx)) {
        $news[startdate] = padate($dateformat_news,$news['startdate']);
        $home_newsbit .= "";
    }
    $home_newsbit_space = "
		";
    while ($news = $DB->fetch_array($newx)) {
           $news[startdate] = padate($dateformat_news,$news['startdate']);
           $home_newsbit .= $newsbit_space;
           $home_newsbit .= "";
    }
    ?>
<?=
<!-- BEGIN home_newsbit -->
		<a name='#' title='$news[content]'>$news[title]($news[startdate])</a>
<!-- END home_newsbit -->
?>
		<?

}
?></MARQUEE>

</div>
		</div>
		</div>
<div class="mainline">&nbsp;</div>
    <div class="textad">
      <div class="textadleft">�û�����</div>
      <div class="textadright">
		<div class="adlist">




<div class="login_pannel">
		<div  id=login>
			<form action="<?=$g_o_back2root?>/index.php?mod=member" method="post">
				<div class="input">
				<span>�û���:</span>
				<input type="text" name="username" title="������������ע����û���" />
				<span>�ܡ���:</span>
				<input type="password" name="password" title="��������������" />
				<input type="submit" value="��¼" class="button" />
				<input type="button" onclick="location.href='<?=$g_o_back2root?>/index.php?mod=register'" value="ע��" class="button" />
                <input type="hidden" name="url" value="<?=$url?>">
                <input type="hidden" name="action" value="login">
				</div>
			</form>
		</div>





</div>




	<div class="search_pannel">

		<form name="search" method="get" action="<?=$g_o_back2root?>/index.php">
				<input name="keyword" value="" type="text" id="keyboard" class="textbox search_pannel_text" />&nbsp;
				<select name="type" style="width:80px;height:17px;" class="textbox">
					<option value="title" selected>����</option>
					<option value="all">ȫ��</option>
				</select>
				<input type="submit" name="Submit22" value="��������" style="width:60px;height:20px;" class="button" />
<input type="hidden" name="mod" value="search">
            <input type="hidden" name="ordertype" value="date">
            <input type="hidden" name="action" value="result">
			</form>

		</div>







  </div>
      </div>
    </div>
    </div>

	<div class="mainright">
		<div class="rightblock">
		<div class="righttitleico">&nbsp;</div>
		<div class="righttitlename">&nbsp;&nbsp;��������</div>
		<div class="clear">&nbsp;</div>
		<div class="search">





		</div>
	</div>
	</div>
</div>



<div class="mainline">&nbsp;</div>
<div class="maincolumn">
  <div class="mainleft">
    <div class="listleft">
      <div class="indexlist">
        <div class="lefttitleico">&nbsp;</div>
        <div class="lefttitlebg">
          <div class="lefttitlename">��������</div>
          <div class="lefttitlemore"><a href="<?=$g_o_back2root?>/html/2/"><img height="20" src="<?=$g_o_back2root?>/images/more.gif" width="60" border="0" alt="" /></a></div>
        </div>
        <div class="clear">&nbsp;</div>
        <div class="mainlist">
          <div class="list"> <ul>
<?=$tag_articlelist[img][defaultsys][1]?>
</ul> </div>
        </div>
      </div>
      <div class="mainline">&nbsp;</div>
      <div class="subadimg"><a href="#"><img src="<?=$g_o_back2root?>/images/4.gif" width="280" height="60" border="0" alt="" /></a></div>
    </div>
    <div class="listright">
      <div class="indexlist">
        <div class="lefttitleico">&nbsp;</div>
        <div class="lefttitlebg">
          <div class="lefttitlename">ʹ�ð���</div>
          <div class="lefttitlemore"><a href="<?=$g_o_back2root?>/html/1/"><img height="20" src="<?=$g_o_back2root?>/images/more.gif" width="60" border="0" alt="" /></a></div>
        </div>
        <div class="clear">&nbsp;</div>
        <div class="mainlist">
          <div class="list"> <ul>
<?=$tag_articlelist[text][defaultsys][1]?>
</ul> </div>
        </div>
      </div>
      <div class="mainline">&nbsp;</div>
      <div class="subadimg"><a href="#" rel="external"><img src="<?=$g_o_back2root?>/images/b.jpg" width="280" height="60" border="0" alt="������Ƹ" /></a></div>
    </div>
  </div>
  <div class="mainright">

    <div class="rightblock">
      <div class="righttitleico">&nbsp;</div>
      <div class="righttitlename">���¸���</div>
      <div class="clear">&nbsp;</div>
      <div class="rightlist"> <ul>

<?
        $articles = $DB->query("SELECT articleid,article.sortid,article.title,views,date FROM " . $db_prefix . "article AS article
                                           LEFT JOIN " . $db_prefix . "sort AS sort
                                             ON article.sortid=sort.sortid
                                           WHERE sort.showinlast=1 AND article.visible=1
                                           ORDER BY date DESC LIMIT $lastupdatenum");
        while ($article = $DB->fetch_array($articles)) {
                $articlehtmllink = HTMLDIR . "/" . mkdirname($article['sortid'],"",$article['date'],0,0).rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//get_sortdirs($article['sortid']) . "/" . date("Y_m", $article['date']) . "/"
                $article[date] = date("m/d", $article[date]);
                ?>
	          	<li><a href="<?=$g_o_back2root?>/<?=$articlehtmllink?>" title="<?=$article[title]?>"><?=$article[title]?></a></li>
	          	<?
        }
?>

</ul> </div>
    </div>	  

  </div>
</div>

<div class="mainline">&nbsp;</div>
<div class="maincolumn">
  <div class="mainleft">
    <div class="listleft">
      <div class="indexlist">
        <div class="lefttitleico">&nbsp;</div>
        <div class="lefttitlebg">
          <div class="lefttitlename">���¶�̬</div>
          <div class="lefttitlemore"><a href="<?=$g_o_back2root?>/bbshtmldata/18/11/"><img height="20" src="<?=$g_o_back2root?>/images/more.gif" width="60" border="0" alt="" /></a></div>
        </div>
        <div class="clear">&nbsp;</div>
        <div class="mainlist">
          <div class="list"> <ul>
<?=$tag_articlelist[text][_bbs][1]?>
</ul> </div>
        </div>
      </div>
    </div>
    <div class="listright">
      <div class="indexlist">
        <div class="lefttitleico">&nbsp;</div>
        <div class="lefttitlebg">
          <div class="lefttitlename">��վ����</div>
          <div class="lefttitlemore"><a href="<?=$g_o_back2root?>/bbshtmldata/1/13/"><img height="20" src="<?=$g_o_back2root?>/images/more.gif" width="60" border="0" alt="" /></a></div>
        </div>
        <div class="clear">&nbsp;</div>
        <div class="mainlist">
          <div class="list"> <ul>
<?=$tag_articlelist[text][_bbs][13]?>
</ul> </div>
        </div>
      </div>
    </div>
    <div class="mainline">&nbsp;</div>
    <div class="navadimg"><a href="#" rel="external"><img src="./images/2006.jpg" width="569" height="60" border="0" alt="" /></a></div>
  </div>
  <div class="mainright">
    <div class="rightblock">
      <div class="righttitleico">&nbsp;</div>
      <div class="righttitlename">&nbsp;&nbsp;��������</div>
      <div class="clear">&nbsp;</div>
      <div class="rightlist"> <ul>
	          
<?
        $articles = $DB->query("SELECT articleid,article.sortid,date,article.title,views FROM " . $db_prefix . "article AS article
                                           LEFT JOIN " . $db_prefix . "sort AS sort
                                             ON article.sortid=sort.sortid
                                           WHERE sort.showinhot=1 AND article.visible=1
                                           ORDER BY views DESC LIMIT $hotarticlenum");
        while ($article = $DB->fetch_array($articles)) {
                $articlehtmllink = HTMLDIR . "/" . mkdirname($article['sortid'],"",$article['date'],0,0).rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//get_sortdirs($article['sortid']) . "/" . date("Y_m", $article['date']) . "/"
                ?>
	          	<li><a href="<?=$g_o_back2root?>/<?=$articlehtmllink?>" title="<?=$article[title]?>"><?=$article[title]?></a></li>
	          	<?
        } 
?>
	  </ul> </div>
    </div>


</div>
</div>
<div class="mainline">&nbsp;</div>
<div class="maincolumn">
  <div class="mainleft">
    <div class="listleft">
      <div class="indexlist">
        <div class="lefttitleico">&nbsp;</div>
        <div class="lefttitlebg">
          <div class="lefttitlename">VC/C#/���</div>
          <div class="lefttitlemore"><a href="<?=$g_o_back2root?>/bbshtmldata/1/12/"><img height="20" src="<?=$g_o_back2root?>/images/more.gif" width="60" border="0" alt="" /></a></div>
        </div>
        <div class="clear">&nbsp;</div>
        <div class="mainlist">
          <div class="list"> <ul>
<?=$tag_articlelist[text][_bbs][12]?>
</ul> </div>
        </div>
      </div>
    </div>
    <div class="listright">
      <div class="indexlist">
        <div class="lefttitleico">&nbsp;</div>
        <div class="lefttitlebg">
          <div class="lefttitlename">UNIX/LINUX����</div>
          <div class="lefttitlemore"><a href="<?=$g_o_back2root?>/bbshtmldata/1/9/"><img height="20" src="<?=$g_o_back2root?>/images/more.gif" width="60" border="0" alt="" /></a></div>
        </div>
        <div class="clear">&nbsp;</div>
        <div class="mainlist">
          <div class="list"> <ul>
<?=$tag_articlelist[text][_bbs][9]?>
</ul> </div>
        </div>
      </div>
    </div>
  </div>
  <div class="mainright">
    <div class="rightblock">
      <div class="righttitleico">&nbsp;</div>
      <div class="righttitlename">&nbsp;&nbsp;�Ƽ�����</div>
      <div class="clear">&nbsp;</div>
      <div class="rightlist rightlistnoad"> <ul>
      
<?
        $articles = $DB->query("SELECT articleid,article.sortid,article.date,article.title,views,(totalscore/voters) as averagescore,voters FROM " . $db_prefix . "article AS article
                                           LEFT JOIN " . $db_prefix . "sort AS sort
                                             ON article.sortid=sort.sortid
                                           WHERE voters>0 AND sort.showinrate=1 AND article.visible=1
                                           ORDER BY averagescore DESC
                                           LIMIT $ratearticlenum");
        while ($article = $DB->fetch_array($articles)) {
                $articlehtmllink = HTMLDIR . "/" . mkdirname($article['sortid'],"",$article['date'],0,0).rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//get_sortdirs($article['sortid']) . "/" . date("Y_m", $article['date']) . "/"
                ?>
	          	<li><a href="<?=$g_o_back2root?>/<?=$articlehtmllink?>" title="<?=$article[title]?>"><?=$article[title]?></a></li>
	          	<?
        } 
?>
      </ul> </div>
    </div>
  </div>
</div>
<div class="mainline">&nbsp;</div>
<div class="maincolumn">
  <div class="mainleft">
    <div class="listleft">
      <div class="indexlist">
        <div class="lefttitleico">&nbsp;</div>
        <div class="lefttitlebg">
          <div class="lefttitlename">�������</div>
          <div class="lefttitlemore"><a href="<?=$g_o_back2root?>/bbshtmldata/4/6/"><img height="20" src="<?=$g_o_back2root?>/images/more.gif" width="60" border="0" alt="" /></a></div>
        </div>
        <div class="clear">&nbsp;</div>
        <div class="mainlist">
          <div class="list"> <ul>
<?=$tag_articlelist[text][_bbs][6]?>
</ul> </div>
        </div>
      </div>
    </div>
    <div class="listright">
      <div class="indexlist">
        <div class="lefttitleico">&nbsp;</div>
        <div class="lefttitlebg">
          <div class="lefttitlename">��������</div>
          <div class="lefttitlemore"><a href="<?=$g_o_back2root?>/bbshtmldata/4/6/"><img height="20" src="<?=$g_o_back2root?>/images/more.gif" width="60" border="0" alt="" /></a></div>
        </div>
        <div class="clear">&nbsp;</div>
        <div class="mainlist">
          <div class="list"> <ul>
<?=$tag_articlelist[text][_bbs][4]?>
</ul> </div>
        </div>
      </div>
    </div>
    <div class="mainline">&nbsp;</div>
    <div class="navadimg"><a href="#" rel="external"><img src="./images/2006.jpg" width="569" height="60" border="0" alt="" /></a></div>
  </div>
  <div class="mainright">
    <div class="rightblock">
      <div class="righttitleico">&nbsp;</div>
      <div class="righttitlename">&nbsp;&nbsp;��������</div>
      <div class="clear">&nbsp;</div>
      <div class="rightlist"> <ul>
	          
<?
        $articles = $DB->query("SELECT articleid,article.sortid,date,article.title,views FROM " . $db_prefix . "article AS article
                                           LEFT JOIN " . $db_prefix . "sort AS sort
                                             ON article.sortid=sort.sortid
                                           WHERE sort.showinhot=1 AND article.visible=1
                                           ORDER BY views DESC LIMIT $hotarticlenum");
        while ($article = $DB->fetch_array($articles)) {
                $articlehtmllink = HTMLDIR . "/" . mkdirname($article['sortid'],"",$article['date'],0,0).rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//get_sortdirs($article['sortid']) . "/" . date("Y_m", $article['date']) . "/"
                ?>
	          	<li><a href="<?=$g_o_back2root?>/<?=$articlehtmllink?>" title="<?=$article[title]?>"><?=$article[title]?></a></li>
	          	<?
        } 
?>
	  </ul> </div>
    </div>
</div>
</div>
<div class="mainline">&nbsp;</div>
<div class="maincolumn">
  <div class="mainleft">
    <div id="flist">
      <div class="ftitleico">&nbsp;</div>
      <div class="ftitlebg">
        <div class="ftitlename">�������� [<a href="index.php?mod=friendlink&action=add">����<a>]</div>
        <div class="ftitlemore"><a href="/link"><img height="20" src="<?=$g_o_back2root?>/images/more.gif" width="60" border="0" alt="" /></a></div>
      </div>
      <div class="clear">&nbsp;</div>
<?=$friendlink[1]?>
    </div>
  </div>
  <div class="mainright">
    <div class="rightblock">
      <div class="righttitleico">&nbsp;</div>
      <div class="righttitlename">&nbsp;&nbsp;�������</div>
      <div class="clear">&nbsp;</div>
      <div class="hezuo">
        <ul>
          <li><a href="http://www.utspeed.com" rel="external" target=blank>niuboy��ҳ</a></li>
          <li><a href="http://www.phparticle.net/" rel="external" target=blank>PAHtml�ٷ�</a></li>
          <li><a href="#" rel="external"></a></li>
          <li><a href="#" rel="external"></a></li>
          <li><a href="#" rel="external"></a></li>
          <li><a href="#" rel="external"></a></li>
          <li><a href="#" rel="external"></a></li>
          <li><a href="#" rel="external"></a></li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="mainline">&nbsp;</div>
<div id="footer">
	<div id="bottommenu">
	  <a href="#">����վ��</a> - <a href="#">������</a> - <a href="#">��ϵ����</a> - <a href="#">��Ȩ��˽</a> - <a href="#">��������</a> - <a href="http://utspeed.com">�������</a> - <a href="http://phparticle.net" target="_blank">����֧��</a> - <a  href="#">��վ��ͼ</a> - <a href="#top">���ض���</a>
	</div>
	<div class="topline">&nbsp;</div>
	<div id="bottom">��Ȩ���У�<a href="<?=$phparticleurl?>"><?=$phparticletitle?></a> 2006 δ����Ȩ��ֹ���ƻ�������<br />

	  <span>Powered by: <a href="http://www.phparticle.net">phpArticle html</a> Version <?=$version?>.</span><br />
	</div>
	</div>
</div>
</body>
</html>

<?
$outputdata = ob_get_contents();
ob_end_clean();
dooutput($outputdata);
?>