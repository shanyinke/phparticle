<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

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

<div id=wrap>
<div class="maincolumn">
	<div class="mainleft">

		<div class="classnav">
		<div class="sublisttitleico">&nbsp;</div>
		<div class="sublisttitlebg">
		<div class="sublisttitlename">
你的位置：<a href="<?=$homepage?>/" class="classlinkclass">首页</a>&nbsp;>&nbsp;<a href="<?=$phparticleurl?>" class="classlinkclass"><?=$phparticletitle?></a>&nbsp;>&nbsp;搜索结果
</div>

		</div>
		</div>
<div class="mainline">&nbsp;</div>

<?php
if ($_GET[action]=="lastupdate" OR $_GET[action]=="pop" OR $_GET[action]=="hot") {

    cachesorts();

    $sortlist = makesortlist();

    unset($articlelist);

    $perpage = $searchperpage;
                                /*
    $nav = $_SERVER["REQUEST_URI"];
    $script = $_SERVER["SCRIPT_NAME"];
    $nav = ereg_replace("^$script","",$nav);
    $vars = explode("/",$nav);
    $pagenum = intval($vars[1]);
                                */

    $total = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."article");

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

    if ($_GET[action]=="hot") {
        $order = "views";
    } elseif ($_GET[action]=="pop") {
        $order = "totalscore";
    } else {
        $order = "date";
    }
    $articles = $DB->query("SELECT * FROM ".$db_prefix."article
                                   WHERE visible=1
                                   ORDER BY $order DESC
                                   LIMIT $offset,$perpage");

    if ($DB->num_rows($articles)>0) {

        $counter = 0;
        $row = 0;
        //$division = $sortinfo[division_article];
        $division = 1;
        $tablewidth = floor(100/$division);

        while ($article = $DB->fetch_array($articles)){
		$articlehtmllink = HTMLDIR."/".mkdirname($article['sortid'],-1,$article['date'],0,0).rawurlencode(mkfilename($filenamemethod,$article['title'],1)).$article['articleid']."_1.".HTMLEXT;//get_sortdirs($article['sortid'])."/".date("Y_m",$article['date'])."/"
               $article[time] = padate($timeformat_article,$article[date]);
               $article[date] = padate($dateformat_article,$article[date]);
               if (!empty($article[imageid])) {
                   //$articlelistbit_img = "<img src=\"showimg.php?iid=$article[imageid]\" border=\"0\" vspace=\"2\" hspace=\"2\" align=\"left\">";
                   $articlelistbit_img = "
<img src='$g_o_back2root/showimg.php?iid=$article[imageid]' border='0' vspace='2' hspace='2' align='right'>
";
               } else {
                   $articlelistbit_img = "";
               }
               if ($counter==0) {
                   if ($row++%2==0) {
                       $bgcolor = "$style[firstalt]";
                   } else {
                       $bgcolor = "$style[secondalt]";
                   }
                   $articlelistbit .= "<tr bgcolor=\"$bgcolor\" align=\"center\">";
               }
               $articlelistbit .= "<td nowrap valign=\"top\" width=\"$tablewidth%\">";
               $articlelistbit .= "";

               $articlelistbit .= "</td>\n";

               if (++$counter%$division==0) {
                   $articlelistbit .= "</tr>";
                   $counter = 0;
               }

        }
        if ($counter!=0) {
            for (;$counter<$division;$counter++) {
                 $articlelistbit .= "<td></td>\n";
            }
        }


        $pagelinks = makepagelink2("./index.php?mod=search&action=$action",$pagenum,$totalpages);
        $pagenav = "";

        ?>
<div class="sublist">
<div class="onesubnewslist">
<img src="<?=$style[imagesfolder]?>/news.gif" align="absmiddle" vspace="2" hspace="2"><b>搜索结果</b>
	<ul>
<?=
<!-- BEGIN articlelistbit -->
<li><a href='$g_o_back2root/$articlehtmllink' rel='external'>$article[title]</a></li>
<!-- END articlelistbit -->
?>
	</ul>
</div>
<div class="clear">&nbsp;</div>
<?=
<!-- BEGIN pagenav -->
<div id='sublistfooter'>
     <div class='left'>共 <b>$totalresults</b> ,显示 <b>$from -
      <$to</b></div>
     <div class='right'>$pagelinks</div>
</div>
<!-- END pagenav -->
?>
</div>
<?

    } else {
        ?>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td align="center"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/information.gif" border="0" align="absmiddle"><span class="normalfont"><b>找不到任何匹配的结果.</b></span></td>
  </tr>
</table>
<?
    }
}

if ($_GET[action]=="result"){

    /*
    echo $sortidss;
    print_rr($condition);
    echo $conditions;
    */
    $sortlist = makesortlist();

    $sql = "SELECT * FROM ".$db_prefix."article AS article
                     LEFT JOIN ".$db_prefix."articletext AS articletext
                     USING (articleid)
                     WHERE $conditions AND visible=1
                     GROUP BY article.articleid";
    //echo "<pre>$sql</pre>";
    $total = $DB->query($sql);

    $totalresults = $DB->num_rows($total);

    unset($articlelist);

    $perpage = $searchperpage;
    $totalpages = ceil($totalresults/$perpage);

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

    $articles = $DB->query("$sql
                                   ORDER BY binary $orderby $displayorder
                                   LIMIT $offset,$perpage");

    if ($DB->num_rows($articles)>0) {

        $counter = 0;
        $row = 0;
        $division = 1;
        $tablewidth = floor(100/$division);

        while ($article = $DB->fetch_array($articles)){
		$articlehtmllink = HTMLDIR."/".mkdirname($article['sortid'],-1,$article['date'],0,0).rawurlencode(mkfilename($filenamemethod,$article['title'],1)).$article['articleid']."_1.".HTMLEXT;//get_sortdirs($article['sortid'])."/".date("Y_m",$article['date'])."/"
               $article[date] = padate($dateformat_article,$article[date]);
               $article[time] = padate($timeformat_article,$article[date]);
               if (!empty($article[imageid])) {
                   $articlelistbit_img = "
<img src='$g_o_back2root/showimg.php?iid=$article[imageid]' border='0' vspace='2' hspace='2' align='right'>
";
               } else {
                   $articlelistbit_img = "";
               }
               if ($counter==0) {
                   if ($row++%2==0) {
                       $bgcolor = "$style[firstalt]";
                   } else {
                       $bgcolor = "$style[secondalt]";
                   }
                   $articlelistbit .= "<tr bgcolor=\"$bgcolor\" align=\"center\">";
               }
               $articlelistbit .= "<td nowrap valign=\"top\" width=\"$tablewidth%\">";
               $articlelistbit .= "";

               $articlelistbit .= "</td>\n";

               if (++$counter%$division==0) {
                   $articlelistbit .= "</tr>";
                   $counter = 0;
               }

        }
        if ($counter!=0) {
            for (;$counter<$division;$counter++) {
                 $articlelistbit .= "<td></td>\n";
            }
        }

        foreach ($_GET AS $k=>$v) {
                 if ($k!="action" AND $k!="sortids" AND $k!="subsort") {
                     $link .= "&$k=$v";
                 }
        }
        $link .= "&sortidss=$sortidss";
        //echo $link;
        $pagelinks = makepagelink2("./index.php?mod=search&action=result$link",$pagenum,$totalpages);
        $pagenav = "";

        ?>
<div class="sublist">
<div class="onesubnewslist">
<img src="<?=$style[imagesfolder]?>/news.gif" align="absmiddle" vspace="2" hspace="2"><b>搜索结果</b>
	<ul>
<?=
<!-- BEGIN articlelistbit -->
<li><a href='$g_o_back2root/$articlehtmllink' rel='external'>$article[title]</a></li>
<!-- END articlelistbit -->
?>
	</ul>
</div>
<div class="clear">&nbsp;</div>
<?=
<!-- BEGIN pagenav -->
<div id='sublistfooter'>
     <div class='left'>共 <b>$totalresults</b> ,显示 <b>$from -
      <$to</b></div>
     <div class='right'>$pagelinks</div>
</div>
<!-- END pagenav -->
?>
</div>
<?

    } else {
        ?>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td align="center"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/information.gif" border="0" align="absmiddle"><span class="normalfont"><b>找不到任何匹配的结果.</b></span></td>
  </tr>
</table>
<?
        $sortlistoptions = sortsbit();
        ?>                    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="<?=$style[bordercolor]?>">
                                        <form name="" method="get" action="index.php">
                      <tr>
                        <td>

        <table width="100%" border="0" cellspacing="<?=$style[tablecellspacing]?>" cellpadding="4">
          <tr bgcolor="<?=$style[tableheadbgcolor]?>">
            <td colspan="2"><span id="tbh"><b>继续搜索</b></span></td>
          </tr>
          <tr>
            <td bgcolor="<?=$style[catbgcolor]?>"><span id="cattext">按关键字搜索</span></td>
            <td bgcolor="<?=$style[catbgcolor]?>"><span id="cattext">请选择分类</span></td>
          </tr>
          <tr>
            <td width="50%" bgcolor="<?=$style[firstalt]?>">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td rowspan="2" width="50%">
                    <input type="text" name="keyword" size="30" maxlength="20">
                  </td>
                  <td><span class="normalfont">
                    <input type="radio" name="type" value="title">
                    只搜索标题</span></td>
                </tr>
                <tr>
                  <td><span class="normalfont">
                    <input type="radio" name="type" value="all" checked>
                    搜索整篇文章</span></td>
                </tr>
              </table>
            </td>
            <td rowspan="3" bgcolor="<?=$style[firstalt]?>"><span class="normalfont">
              <select name="sortids[]" size="6" multiple>
                <option value="-1" selected>搜索所有分类</option>
                                                                  <?=$sortlistoptions?>

              </select>
              <br>
              <input type="checkbox" name="subsort" value="1" checked>
              搜索子分类</span></td>
          </tr>
          <tr>
            <td bgcolor="<?=$style[catbgcolor]?>"><span id="cattext">按作者搜索</span></td>
          </tr>
          <tr>
            <td bgcolor="<?=$style[firstalt]?>">
              <input type="text" name="author" size="30" maxlength="20">
            </td>
          </tr>
          <tr bgcolor="<?=$style[tableheadbgcolor]?>">
            <td colspan="2" align="center">
            <input type="hidden" name="mod" value="search">
              <input type="hidden" name="action" value="result">
              <input type="submit" value=" 搜  索 " class="button">
              <input type="reset" value=" 重  置 " class="button">
            </td>
          </tr>
        </table>
                        </td>
                      </tr>
                                        </form>
                    </table>
                  <?
    }
}
?>
<div class="mainline">&nbsp;</div>

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










	</div>
</div>		<div class="clear">&nbsp;</div></div>


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