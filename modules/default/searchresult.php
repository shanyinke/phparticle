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

<div id=wrap>
<div class="maincolumn">
	<div class="mainleft">

		<div class="classnav">
		<div class="sublisttitleico">&nbsp;</div>
		<div class="sublisttitlebg">
		<div class="sublisttitlename">
���λ�ã�<a href="<?=$homepage?>/" class="classlinkclass">��ҳ</a>&nbsp;>&nbsp;<a href="<?=$phparticleurl?>" class="classlinkclass"><?=$phparticletitle?></a>&nbsp;>&nbsp;�������
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
<img src="<?=$style[imagesfolder]?>/news.gif" align="absmiddle" vspace="2" hspace="2"><b>�������</b>
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
     <div class='left'>�� <b>$totalresults</b> ,��ʾ <b>$from -
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
    <td align="center"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/information.gif" border="0" align="absmiddle"><span class="normalfont"><b>�Ҳ����κ�ƥ��Ľ��.</b></span></td>
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
<img src="<?=$style[imagesfolder]?>/news.gif" align="absmiddle" vspace="2" hspace="2"><b>�������</b>
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
     <div class='left'>�� <b>$totalresults</b> ,��ʾ <b>$from -
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
    <td align="center"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/information.gif" border="0" align="absmiddle"><span class="normalfont"><b>�Ҳ����κ�ƥ��Ľ��.</b></span></td>
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
            <td colspan="2"><span id="tbh"><b>��������</b></span></td>
          </tr>
          <tr>
            <td bgcolor="<?=$style[catbgcolor]?>"><span id="cattext">���ؼ�������</span></td>
            <td bgcolor="<?=$style[catbgcolor]?>"><span id="cattext">��ѡ�����</span></td>
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
                    ֻ��������</span></td>
                </tr>
                <tr>
                  <td><span class="normalfont">
                    <input type="radio" name="type" value="all" checked>
                    ������ƪ����</span></td>
                </tr>
              </table>
            </td>
            <td rowspan="3" bgcolor="<?=$style[firstalt]?>"><span class="normalfont">
              <select name="sortids[]" size="6" multiple>
                <option value="-1" selected>�������з���</option>
                                                                  <?=$sortlistoptions?>

              </select>
              <br>
              <input type="checkbox" name="subsort" value="1" checked>
              �����ӷ���</span></td>
          </tr>
          <tr>
            <td bgcolor="<?=$style[catbgcolor]?>"><span id="cattext">����������</span></td>
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
              <input type="submit" value=" ��  �� " class="button">
              <input type="reset" value=" ��  �� " class="button">
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
		<div class="righttitlename">&nbsp;&nbsp;��������</div>
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