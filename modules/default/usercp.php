<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<?php
if ($pauserinfo[userid]==0) {
    include("modules/default/nopermission.php");
}
unset($bgcolor);
$bgcolor[usercp] = "bgcolor=\"$style[firstalt]\"";

$pauserinfo[joindate] = padate("Y-m-d",$pauserinfo[joindate]);
$articles = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."article WHERE userid='$pauserinfo[userid]'");
$pauserinfo[articles] = $articles[count];
if ($pauserinfo[articles]==0) {
    $pauserinfo[articles] = "�㻹δ�з����κ�����";
}
$comments = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."comment WHERE userid='$pauserinfo[userid]'");
$pauserinfo[comments] = $comments[count];
if ($pauserinfo[comments]==0) {
    $pauserinfo[comments] = "�㻹δ�����۹��κ�����";
}

    $favorites = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."favorite
                                              WHERE userid='$pauserinfo[userid]'");

    $pauserinfo[favorites] = $favorites[count];

    $space[used] = ceil(($pauserinfo[favorites]/$favoritelimit)*100);

    $space[left] = 100-$space[used];
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
<title><?=$phparticletitle?> - ���Ļ�Ա���</title>
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
<div class="maincolumn">
		<div class="pagelist">
		<div class="pagelisttitleico">&nbsp;</div>
		<div class="pagelisttitlebg">
		<div class="pagelisttitlename">
���λ�ã�<a href="<?=$homepage?>/" class="classlinkclass">��ҳ</a>&nbsp;>&nbsp;<a href="<?=$phparticleurl?>" class="classlinkclass"><?=$phparticletitle?></a>&nbsp;>&nbsp;�ҵĿ���̨
		</div>
		</div>

		<div class="clear">&nbsp;</div>
		<div class="pagecontent">

					<div class="title1"> 
<b>����̨����</b> <br/>
<a href="index.php?mod=usercp">�ҵĿ���̨</a><br/>
<a href="index.php?mod=favorite&action=view">�ҵ��ղ�</a><br/>
<a href="index.php?mod=myarticle&action=view">�ҵ�����</a><br/>
<a href="index.php?mod=member&action=modpassword">�޸�����</a><br/>
<a href="index.php?mod=member&action=modprofile">�޸�����</a><br/>
					</div>
					<div class="reg1">

<fieldset><legend><b>��ӭ�������Ļ�Ա�������</b></legend>

<?=$pauserinfo[username]?>,����������Բ鿴�����ղص�����,�޸ĵ�½���뼰�޸���ĸ�������.<br/>
<b>������ͳ��</b><br/>
���ע�� Email:<?=$pauserinfo[email]?><br/>
ע������:<?=$pauserinfo[joindate]?><br/>
�㷢������������:<?=$pauserinfo[articles]?><br/>
�㷢���������:<?=$pauserinfo[comments]?><br/>
�ղؼ�:<br/>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="<?=$style[bordercolor]?>">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="<?=$style[tablecellspacing]?>" cellpadding="4" height="20">
        <tr>
          <td bgcolor="<?=$style[catbgcolor]?>" width="<?=$space[used]?>%"></td>
          <td bgcolor="<?=$style[secondalt]?>" width="<?=$space[left]?>%"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td><span class="normalfont"><?=$space[used]?>%(���ÿռ�)</span></td>
    <td align="right"><span class="normalfont"><?=$space[left]?>%(ʣ��ռ�)</span></td>
  </tr>
</table>
<br/>
      </fieldset>
   
   
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