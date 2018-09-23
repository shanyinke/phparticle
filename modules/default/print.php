<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<?php
error_reporting(7);
$noheader = 1;
$articleid = intval($_GET[articleid]);
if (empty($articleid)) {
    $nav = $_SERVER["REQUEST_URI"];
    $script = $_SERVER["SCRIPT_NAME"];
    $nav = ereg_replace("^$script","",urldecode($nav));
    $vars = explode("/",$nav);
    $articleid = intval($vars[1]);
}


$article = validate_articleid($articleid);


if (!$pauserinfo[isadmin] AND !$pauserinfo[canviewarticle]) {
    include("modules/default/nopermission.php");
}

$articlehtmllink = HTMLDIR."/".mkdirname($article['sortid'],-1,$article['date'],0,1).rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//get_sortsubdirs($article['sortid']) . "/" .date("Y_m", $article['date']) . "/"
$article[date] = padate($dateformat_article,$article[date]);
?>
<html>
<head>
<title><?=$phparticletitle?> <?=$article[title]?> Powered By phpArticle</title>
<?=$headinclude?>
</head> 
<body bgcolor="#FFFFFF" text="#000000">
<table width="778" border="0" cellspacing="0" cellpadding="10" bgcolor=#FFFFFF>
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="4">
        <tr>
          <td><font class=bigfont><b><?=$article[title]?></b></font></td>
        </tr>
        <tr>
          <td><span class="normalfont"><?=$article[date]?> &nbsp;&nbsp; <?=$article[author]?> &nbsp;&nbsp;
            <?=$article[source]?></span></td>
        </tr>
        <tr>
          <td><span class="normalfont">打印自: <a href="<?=$phparticleurl?>"><?=$phparticletitle?></a><br>
            地址: <a href="<?=$g_o_back2root?>/<?=$articlehtmllink?>"><?=$g_o_back2root?>/index.php?mod=article&articleid=<?=$articleid?></a></span></td>
        </tr>
        <tr>
          <td>
          
<?
$contents = $DB->query("SELECT subhead,articletext
                               FROM ".$db_prefix."articletext
                               WHERE articleid='".addslashes($articleid)."' ORDER BY displayorder,id");

while ($articletext = $DB->fetch_array($contents)){
       ?>
          <table width="100%" border="0" cellspacing="1" cellpadding="4">
  <tr>
    <td valign="top">
      <div class="subhead"><b><?=$articletext[subhead]?></b></div>
    </td>
  </tr>
  <tr>
    <td valign="top">
      <div class="content"><?=$articletext[articletext]?></div>
    </td>
  </tr>
</table>
          <?
}

if (pa_isset($article[editor])) {
    $print_editor = "
                   <tr>
                      <td valign='top' align='right'><span class='normalfont'><b>责任编辑:</b>
                        $article[editor]</span></td>
                    </tr>
          ";
}
?></td>
        </tr>
        <tr>
          <td align="right"><?=$editor?></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<script language=JavaScript>
        window.print();
</script>
</body>
</html>