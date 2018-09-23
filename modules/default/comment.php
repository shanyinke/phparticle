<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">



<?php

if ($_GET[action]=="add") {

    if (!$pauserinfo[cancomment] AND !$pauserinfo[isadmin]) {
        include("modules/default/nopermission.php");
    }


    cachesorts();
    $articleid = intval($_GET[articleid]);
    if (empty($articleid)) {
        $nav = $_SERVER["REQUEST_URI"];
        $script = $_SERVER["SCRIPT_NAME"];
        $nav = ereg_replace("^$script","",urldecode($nav));
        $vars = explode("/",$nav);
        $articleid = intval($vars[1]);
    }

    $article = validate_articleid($articleid);
	$articlehtmllink = HTMLDIR."/".mkdirname($article['sortid'],"",$article['date'],0,0).rawurlencode(mkfilename($filenamemethod,$article['title'],1)).$article['articleid']."_1.".HTMLEXT;//get_sortdirs($article['sortid'])."/".date("Y_m",$article['date'])."/"
	$article[date] = padate($dateformat_article,$article[date]);
//	$article[title] = "<a href=\"$phparticleurl/$articlehtmllink\">$article[title]</a>".gettemplate('navbar_joiner')."发表评论";
    $navbar = makearticlenavbar2($article);

    include("modules/default/comment_add.php");
    exit;
}

if ($_POST[action]=="doinsert") {

    if ($_POST[preview]) {

        if (!$pauserinfo[cancomment] AND !$pauserinfo[isadmin]) {
            include("modules/default/nopermission.php");
        }


        $title = htmlspecialchars(trim($_POST[title]));
        $message = htmlspecialchars(trim($_POST[message]));
        $message1 = str_replace("  ","&nbsp;&nbsp;",nl2br($message));

        if (!pa_isset($title)) {
		$errormessage="error_comment_title_blank";
		include("modules/default/error.php");
        }
        if (!pa_isset($message)) {
		$errormessage="error_comment_message_blank";
		include("modules/default/error.php");
        }

        if (strlen($title)>$comment_title_limit) {
		$errormessage="error_comment_title_toolong";
		include("modules/default/error.php");
        }
        if (strlen($message)>$comment_message_limit) {
		$errormessage="error_comment_message_toolong";
		include("modules/default/error.php");
        }

        cachesorts();
        $articleid = intval($_POST[articleid]);
        $article = validate_articleid($articleid);
	$articlehtmllink = HTMLDIR."/".mkdirname($article['sortid'],"",$article['date'],0,0).rawurlencode(mkfilename($filenamemethod,$article['title'],1)).$article['articleid']."_1.".HTMLEXT;//get_sortdirs($article['sortid'])."/".date("Y_m",$article['date'])."/"
//	$article[title] = "<a href=\"$articlehtmllink\">$article[title]</a>".gettemplate('navbar_joiner')."发表评论";
        $navbar = makearticlenavbar2($article);
        include("modules/default/comment_preview.php");

    } else {

        

        if (!$pauserinfo[cancomment] AND !$pauserinfo[isadmin]) {
            include("modules/default/nopermission.php");
        }


        $title = htmlspecialchars(trim($_POST[title]));
        $message = trim($_POST[message]);

        if (!pa_isset($title)) {
		$errormessage="error_comment_title_blank";
		include("modules/default/error.php");
        }
        if (!pa_isset($message)) {
		$errormessage="error_comment_message_blank";
		include("modules/default/error.php");
        }


        if (strlen($title)>$comment_title_limit) {
            //show_errormessage("error_comment_title_toolong");
            $title = substr($title,0,$comment_title_limit);
        }
        if (strlen($message)>$comment_message_limit) {
		$errormessage="error_comment_message_toolong";
		include("modules/default/error.php");
        }

        $articleid = intval($_POST[articleid]);
        $article = validate_articleid($articleid);

        $date = time();
        $DB->query("INSERT INTO ".$db_prefix."comment (articleid,title,author,userid,date,lastupdate) VALUES
                           ('$articleid','".addslashes($title)."','".addslashes($pauserinfo[username])."','".addslashes($pauserinfo[userid])."','$date','$date')");
        $commentid = $DB->insert_id();
        $DB->query("INSERT INTO ".$db_prefix."message (commentid,userid,author,title,message,date,ipaddress) VALUES
                           ('$commentid','$pauserinfo[userid]','".addslashes($pauserinfo[username])."','".addslashes($title)."','".addslashes($message)."','$date','".getip()."')");

        $DB->query("UPDATE ".$db_prefix."article SET
                             comments=comments+1
                             WHERE articleid='$articleid'");
	if($pauserinfo[userid]!=0)
	{
		$url = "$phparticleurl/index.php?mod=article&articleid=$articleid";
		$redirectmsg="redirect_comment_added";
		include("modules/default/redirect.php");
	}else{
		$url = "$phparticleurl/index.php?mod=message&action=view&commentid=$commentid";
		$redirectmsg="redirect_comment_added";
		include("modules/default/redirect.php");
	}
    }

}
?>

<?
if ($_GET[action]=="view" OR (empty($_GET[action]) AND empty($_POST[action]) )) {
    $articleid = intval($_GET[articleid]);
    if (empty($articleid)) {
        $nav = $_SERVER["REQUEST_URI"];
        $script = $_SERVER["SCRIPT_NAME"];
        $nav = ereg_replace("^$script","",urldecode($nav));
        $vars = explode("/",$nav);
        $articleid = intval($vars[1]);
    }

    $article = validate_articleid($articleid);
/*//静态，无效
    if (!$pauserinfo[canviewcomment] AND !$pauserinfo[isadmin]) {
        include("modules/default/nopermission.php");
    }
*/
    cachesorts();
    $sortlist = makesortlist();
    $article[tmp_title] = $article[title];
	$articlehtmllink = HTMLDIR."/".mkdirname($article['sortid'],"",$article['date'],0,0).rawurlencode(mkfilename($filenamemethod,$article['title'],1)).$article['articleid']."_1.".HTMLEXT;//get_sortdirs($article['sortid'])."/".date("Y_m",$article['date'])."/"
//	$article[title] = "<a href=\"$phparticleurl/$articlehtmllink\">$article[title]</a>";
    $navbar = makearticlenavbar2($article);
    $article[title] = $article[tmp_title];

    $article[time] = padate($timeformat_article,$article[date]);
    $article[date] = padate($dateformat_article,$article[date]);

    if (!empty($article[imageid])) {
         $articlelistbit_img = "
<img src='$g_o_back2root/showimg.php?iid=$article[imageid]' border='0' vspace='2' hspace='2' align='right'>
";
    } else {
         $articlelistbit_img = "";
    }
    $article[description] = str_replace("  ","&nbsp;&nbsp;",nl2br($article[description]));
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
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
<title><?=$phparticletitle?>-<?=$article[title]?> - Powered by phpArticle</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-CN" />
<link rel="icon" href="<?=$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<link rel="shortcut icon" href="<?$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<meta name="description" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<meta name="keywords" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<script src="<?=$g_o_back2root?>/images/js/chinahtml.js" type="text/javascript"></script>
<link href="<?=$g_o_back2root?>/images/css/css.css" rel="stylesheet" type="text/css" />
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


<div class="mainline">&nbsp;</div>
<div class="maincolumn">
		<div class="pagelist">
		<div class="pagelisttitleico">&nbsp;</div>
		<div class="pagelisttitlebg">
		<div class="pagelisttitlename">
<?=$navbar?>
		</div>
		</div>
		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
					<div class="title"> 
					  <h1><a href="<?=$phparticleurl?>/<?=$articlehtmllink?>"><?=$article[title]?></a></h1> 
					  <h2><strong>日期：</strong><?=$article[date]?>&nbsp;&nbsp;<strong>点击：</strong><script src="<?=$phparticleurl?>/count.php"></script>&nbsp;&nbsp;<strong>作者：</strong><?=$article[author]?> &nbsp;&nbsp;<strong>来源：</strong><?=$article[source]?></h2> 
					</div>
					<div class="reg">

<?

    $perpage = 20;

    $total = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."comment
                                          WHERE articleid='$articleid'");

    $totalresults = $total[count];

    $totalpages = ceil($total[count]/$perpage);

    if ($pagenum<1 OR empty($pagenum)) {
        $pagenum = 1;
    } elseif ($pagenum>$totalpages) {
        $pagenum = $totalpages;
    }

    $offset = ($pagenum-1)*$perpage;

    if ($totalresults>0) {
        $from = $offset+1;
        if ($pagenum==$totalpages) {
            $to = $totalresults;
        } else {
            $to = $offset+$perpage;
        }
    } else {
        $from = 0;
        $to = 0;
    }
    $comments = $DB->query("SELECT * FROM ".$db_prefix."comment
                                     WHERE articleid='$articleid'
                                     ORDER BY lastupdate DESC
                                     LIMIT $offset,$perpage");
    if ($DB->num_rows($comments)>0) {
        while ($comment = $DB->fetch_array($comments)) {
               $comment[date] = padate("Y-m-d H:i:s a",$comment[date]);
               $showcomment_commentlistbit .="";
        }

        $pagelinks = makepagelink("$phparticleurl/index.php?mod=comment&action=view&articleid=$articleid",$pagenum,$totalpages);
        $pagenav = "
<div id='sublistfooter'>
     <div class='left'>共 <b>$totalresults</b> ,显示 <b>$from -
      $to</b></div>
     <div class='right'>$pagelinks</div>
</div>
";
        ?>
<fieldset>
     <table width="100%" border="0" cellpadding="4">
        <tr>
          <td colspan="4"><b>评论: <?=$article[title]?></b></td>
        </tr>
        <tr>
          <td align="left" width="50%">标题</td>
          <td align="center" width="15%">作者</td>
          <td align="center" width="15%">人气</td>
          <td align="center" width="20%">发表时间</td>
        </tr>
<?=
<!-- BEGIN showcomment_commentlistbit -->
        <tr>
          <td><div class=title><NOBR><a href='$g_o_back2root/index.php?mod=message&action=view&commentid=$comment[commentid]'>$comment[title]</a></NOBR></div></td>
          <td>$comment[author]</td>
          <td>$comment[views]</td>
          <td>$comment[date]</td>
        </tr>
<!-- END showcomment_commentlistbit -->
?>
      </table>
     <table width="100%" border="0" cellpadding="4">
     <span class=left>共 <b><?=$totalresults?></b> ,显示 <b><?=$from?> -
      <?=$to?></span><span class=right><?=$pagelinks?></span>
      </table>
</fieldset>
<?
    } else {
        ?>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td align="center"><img src="<?=$phparticleurl?>/images/information.gif" border="0" align="absmiddle"><span class="normalfont"><b>该文章还未有任何评论</b></span></td>
  </tr>
</table>
<?
    }
        if ($pauserinfo[userid]!=0) {
        ?><form method="post" action="<?=$g_o_back2root?>/index.php?mod=comment" onSubmit="return process_data(this)">
<fieldset><legend>快速发表评论</legend>

  <div>
    <label>标题:</label>
      <input type="text" name="title" maxlength="50" size="50">
  </div>
  <div>
    <label>内容:</label>
      <textarea name="message" cols="70" rows="6"></textarea>
    </label>
  </div>
  <div class=enter>
      <input type="hidden" name="articleid" value="<?=$articleid?>">
      <input type="hidden" name="action" value="doinsert">
      <input type="submit" value=" 发  表 " class="buttot">
      <input type="submit" name="preview" value=" 预  览 " class="buttot">
      <input type="reset" value=" 重  置 " class="buttot">
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
</script><?
    }
?>
					</div>
		</div>


		<div class="tool">
			<span></span>
			<a href="<?=$phparticleurl?>/index.php?mod=comment&articleid=<?=$articleid?>&action=add" class="button_content" title="添加评论" target="_self">添加评论</a>
			<a href="<?=$phparticleurl?>/index.php?mod=comment&articleid=<?=$articleid?>&action=view" class="button_content" title="浏览评论" target="_self">浏览评论</a>
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
</html>