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
    $pauserinfo[articles] = "你还未有发表任何文章";
}
$comments = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."comment WHERE userid='$pauserinfo[userid]'");
$pauserinfo[comments] = $comments[count];
if ($pauserinfo[comments]==0) {
    $pauserinfo[comments] = "你还未有评论过任何文章";
}

    $favorites = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."favorite
                                              WHERE userid='$pauserinfo[userid]'");

    $pauserinfo[favorites] = $favorites[count];

    $space[used] = ceil(($pauserinfo[favorites]/$favoritelimit)*100);

    $space[left] = 100-$space[used];
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
<title><?=$phparticletitle?> - 您的会员面板</title>
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
<div class="maincolumn">
		<div class="pagelist">
		<div class="pagelisttitleico">&nbsp;</div>
		<div class="pagelisttitlebg">
		<div class="pagelisttitlename">
你的位置：<a href="<?=$homepage?>/" class="classlinkclass">首页</a>&nbsp;>&nbsp;<a href="<?=$phparticleurl?>" class="classlinkclass"><?=$phparticletitle?></a>&nbsp;>&nbsp;我的控制台
		</div>
		</div>

		<div class="clear">&nbsp;</div>
		<div class="pagecontent">

					<div class="title1"> 
<b>控制台导航</b> <br/>
<a href="index.php?mod=usercp">我的控制台</a><br/>
<a href="index.php?mod=favorite&action=view">我的收藏</a><br/>
<a href="index.php?mod=myarticle&action=view">我的文章</a><br/>
<a href="index.php?mod=member&action=modpassword">修改密码</a><br/>
<a href="index.php?mod=member&action=modprofile">修改资料</a><br/>
					</div>
					<div class="reg1">

<fieldset><legend><b>欢迎来到您的会员控制面板</b></legend>

<?=$pauserinfo[username]?>,在这里你可以查看你所收藏的文章,修改登陆密码及修改你的个人资料.<br/>
<b>资料与统计</b><br/>
你的注册 Email:<?=$pauserinfo[email]?><br/>
注册日期:<?=$pauserinfo[joindate]?><br/>
你发布的文章总数:<?=$pauserinfo[articles]?><br/>
你发表的评论数:<?=$pauserinfo[comments]?><br/>
收藏夹:<br/>
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
    <td><span class="normalfont"><?=$space[used]?>%(已用空间)</span></td>
    <td align="right"><span class="normalfont"><?=$space[left]?>%(剩余空间)</span></td>
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