<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">



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
<div class="maincolumn">
		<div class="pagelist">
		<div class="pagelisttitleico">&nbsp;</div>
		<div class="pagelisttitlebg">
		<div class="pagelisttitlename">
<b>对不起,你没有这个权限.原因可能如下</b>
		</div>
		</div>

		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
<form name="form" method="post" action="<?=$g_o_back2root?>/index.php?mod=member">
					<div class="title"> 
<img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/warning.gif" border="0" align="absmiddle">
<ul>

<?
        if ($pauserinfo[userid] == 0) {
		?>
	          	<li>你仍未登陆,如果你已在注册,请在右边输入用户名与密码登陆,如果不是会员,请点击<a href="<?=$g_o_back2root?>/index.php?mod=register">注册</a>链接注册成为会员.</li>
	          	<?
        }
        if (!empty($reasons) AND is_array($reasons)) {
                foreach($reasons AS $reason) {
                	if('nopermission_reason_contribute_permission_denied'==$reason)
                	{
                        	?>
	          	<li>你没有投稿这个权限,如果需要这权限,请联系管理员.</li>
	          	<?
                        }
                }
        }
?>
</ul>
					</div>
					<div class="reg">
        
<?
	if ($pauserinfo[userid] == 0) {
		?>
        <fieldset><legend>会员登陆</legend>
<div>如果您想查看与修改您的个人信息，请用您的用户名与密码登录后再修改。</div>

                <div>
                  <label><b>用户名:</b></label>
                    <input type="text" name="username">
                </div>
                <div>
                  <label><b>密码:</b></label>
                    <input type="password" name="password">
                </div>
                <div>
                    <label><input type="checkbox" name="rememberpw" value="1"  checked/> <a href="#" title="选择是否记录您的信息">记住我</a></label>
					<input type="hidden" name="action" value="login">
                    <input type="hidden" name="url" value="<?=$url?>">
                    <input type="submit" class="buttot" value="  登陆  ">

                </div>
                <div>
<label>&nbsp;</label>
                  <a href="<?=$g_o_back2root?>/index.php?mod=member&action=forgetpassword">我忘记了我的登录密码</a>
                </div>
                <div>
<label>&nbsp;</label>
                  <a href="<?=$g_o_back2root?>/index.php?mod=register">我现在就注册为 <?=$phparticletitle?>
                    会员</a>
                </div>

</fieldset>
        <?
	} else {
		?>
                  <td>


  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td colspan="2"><span class="normalfont">
         <p>如果您想查看与修改您的个人信息，请点击下面的链接修改。</p>
        <b>已登陆会员</b></span></td>
    </tr>
    <tr>
      <td><span class="normalfont"><b>用户名:</b></span></td>
      <td><span class="normalfont"><?=$pauserinfo[username]?></span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>
        <input type="hidden" name="action" value="logout">
        <input type="hidden" name="url" value="<?=$url?>">
        <input type="submit" class="button" value="退出登陆">
      </td>
    </tr>
    <tr>
      <td colspan="2"><span class="normalfont"><a href="<?=$g_o_back2root?>/index.php?mod=member&action=modpassword">我要修改密码</a></span></td>
    </tr>
    <tr>
      <td colspan="2"><span class="normalfont"><a href="<?=$g_o_back2root?>/index.php?mod=member&action=modprofile">我要修改个人资料</a></span></td>
    </tr>
  </table>

          </td>
        <?
	}
?>
					</div>
</form>
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

<?exit;?>