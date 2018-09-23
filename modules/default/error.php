<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<head>
<title><?=$phparticletitle?> - 错误提示</title>
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
错误提示
		</div>
		</div>

		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
		       <div class="title"> 
<img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/warning.gif" border="0" align="absmiddle">
<b>
<?
	if($errormessage == 'error_article_subhead_toolong')
	{
        	?>
你所输入的小标题太长了.最多只可以输入100个字符,请返回重新输入.
<?
        }else if($errormessage == 'error_article_title_toolong')
	{
        	?>
你所输入的文章标题太长了,最多只可以输入100个字符,请返回重新输入.
<?
        }else if($errormessage == 'error_article_voted')
	{
        	?>
在我们的数据记录中,你已对该文章投票.
<?
        }else if($errormessage == 'error_author_toolong')
	{
        	?>
你所输入的作者名太长了,至多可以输入20个字符,即10个汉字.
<?
        }else if($errormessage == 'error_author_tooshort')
	{
        	?>
你所输入的作者名太短了,至少要输入4个字符,即两个汉字或以上.
<?
        }else if($errormessage == 'error_comment_message_blank')
	{
        	?><?
        }else if($errormessage == 'error_comment_message_toolong')
	{
        	?>
评论的内容太长了,最多只能输入 <?=$comment_message_limit?> 个字符.
<?
        }else if($errormessage == 'error_comment_title_blank')
	{
        	?>
评论标题不能为空,请返回重新输入.
<?
        }else if($errormessage == 'error_comment_title_toolong')
	{
        	?>
标题太长了,最多只能输入 <?=$comment_title_limit?> 个字符.
<?
        }else if($errormessage == 'error_email_recipients_blank')
	{
        	?>
收件人名称不能为空,请返回输入.
<?
        }else if($errormessage == 'error_favorite_fulled')
	{
        	?>
你的收藏夹已满,请先清理收藏夹中的文章后再添加.
<?
        }else if($errormessage == 'error_favorite_unselect')
	{
        	?>
仍未选中任何要删除的文章.
<?
        }else if($errormessage == 'error_invalemail')
	{
        	?>
你所输入的Email地址无效,请返回重新输入.
<?
        }else if($errormessage == 'error_invalid_activationcode')
	{
        	?>
无效的激活代码
<?
        }else if($errormessage == 'error_invalid_activationcode_expiry')
	{
        	?>
你的会员激活代码已过期并失效.如果有更多的问题,请联系<a href="mailto:<?=$webmastermail?>">管理员</a>.
<?
        }else if($errormessage == 'error_invalid_articleid')
	{
        	?>
该文章不存在或已被删除.若有任何疑问,请联系<a href="mailto:<?=$webmastermail?>">管理员</a>
<?
        }else if($errormessage == 'error_invalid_commentid')
	{
        	?>
该评论不存在或已被删除.
<?
        }else if($errormessage == 'error_invalid_messageid')
	{
        	?><?
        }else if($errormessage == 'error_invalid_sortid')
	{
        	?>
分类 ID 无效或不存在.若有任何疑问,请通知<a href="mailto:<?=$webmastermail?>">管理员</a>.
<?
        }else if($errormessage == 'error_invalid_vote')
	{
        	?>
你的投票无效.
<?
        }else if($errormessage == 'error_keyword_blank')
	{
        	?>
搜索的关键字与作者名不能同时为空,请返回并输入要搜索的关键字或作者名.
<?
        }else if($errormessage == 'error_keyword_none')
	{
        	?>
<tr>
  <td>
<p align="center">
<img src="<?=$style[imagesfolder]?>/warning.gif" align="absmiddle"><span class="normalfont">还没有输入任何关键字,请返回重新输入.</span>
</p>
  </td>
</tr>
<?
        }else if($errormessage == 'error_keyword_toolong')
	{
        	?>
你所输入的关键太长了,至多可以输入20个字符,即10个汉字.
<?
        }else if($errormessage == 'error_keyword_tooshort')
	{
        	?>
你所输入的关键字太短了,至少要输入4个字符,即两个汉字或以上.
<?
        }else if($errormessage == 'error_logined')
	{
        	?>
你已经登陆,请返回.
<?
        }else if($errormessage == 'error_login_fail')
	{
        	?>
登陆失败,用户名或密码无效,如果你还未注册,请点击<a href="index.php?mod=register">注册链接</a>注册成为会员,方可登陆.
<?
        }else if($errormessage == 'error_message_message_blank')
	{
        	?>
回复内容不能为空.
<?
        }else if($errormessage == 'error_message_title_blank')
	{
        	?>
回复标题不能为空.
<?
        }else if($errormessage == 'error_miss_article_title')
	{
        	?>
标题不能为空,请返回输入.
<?
        }else if($errormessage == 'error_nopermission_loggedout')
	{
        	?>
<table width="100%" border="0" cellspacing="1" cellpadding="3" height="200">
  <tr>
    <td align="center"><span class="normalfont"><img src="<?=$style[imagesfolder]?>/warning.gif" align="absmiddle"><b>操作失败,因为你还未登陆,请返回首页登陆,如果你不是会员,请点击注册链接注册.</b></span></td>
  </tr>
</table>
<?
        }else if($errormessage == 'error_password_wrong')
	{
        	?>
旧密码不正确,请返回重新输入.
<?
        }else if($errormessage == 'error_registered')
	{
        	?>
在我们的记录中,你已经注册,如果你忘记了密码,请点击<a href="index.php?mod=member&action=forgetpassword">这里</a>即回密码.</b>
<?
        }else if($errormessage == 'error_register_blank')
	{
        	?>
请返回并输入完整的信息.
<?
        }else if($errormessage == 'error_register_notagree')
	{
        	?>
请返回并同意思本站的注册条款.
<?
        }else if($errormessage == 'error_register_notallow')
	{
        	?>
管理员已禁止新会员的注册,若有任何疑问,联系<a href="mailto:<?=$webmastermail?>">管理员</a>.
<?
        }else if($errormessage == 'error_register_password_notmatch')
	{
        	?>
两次输入的密码不相同,请返回重新输入.
<?
        }
        else if($errormessage == 'error_register_password_toolong')
	{
        	?>
你所输入的密码过长.本站允许输入的密码长度为 <?=$password_length_min?> 至 <?=$password_length_max?> 字符.
<?
        }
        else if($errormessage == 'error_register_password_tooshort')
	{
        	?>
你所输入的密码过短.本站允许输入的密码长度为 <?=$password_length_min?> 至 <?=$password_length_max?> 字符.
<?
        }
        else if($errormessage == 'error_register_username_existed')
	{
        	?>
该用户名已被他人使用.请返回并使用其它用户名注册.
<?
        }else if($errormessage == 'error_register_email_existed')
	{
        	?>
EMAIL 已经存在
<?
        }
        else if($errormessage == 'error_register_username_toolong')
	{
        	?>
你所注册的用户名过长,本站允许输入的用户名长度为 <?=$username_length_min?> 至 <?=$username_length_max?> 字符.
<?
        }
        else if($errormessage == 'error_register_username_tooshort')
	{
        	?>
你所注册的用户名过短,本站允许输入的用户名长度为 <?=$username_length_min?> 至 <?=$username_length_max?> 字符.
<?
        }
        else if($errormessage == 'error_contribute_articles_exceedlimit')
	{
        	?>
你24小时内只能够投稿<?=$pauserinfo['onedaypostmax']?>篇
<?
        }else if($errormessage == 'error_friendlink_added')
	{
        	?>
对不起,此友链已经添加,无法编辑.要修改请联系管理员.
<?
        }else if($errormessage == 'error_friendlink_reject')
	{
        	?>
对不起,此友链已经被拒绝.
<?
        }else if($errormessage == 'error_interval_tooshort')
	{
        	?>
申请失败,两次申请间隔时间太短.
<?
        }else if($errormessage == 'error_logourl_blank')
	{
        	?>
添加失败.使用logo为'是'的时候,logo地址不能为空.<a href="index.php?mod=friendlink&action=add">重新填写</a>
<?
        }else if($errormessage == 'error_sitename_blank')
	{
        	?>
添加失败.站点名不能为空.<a href="index.php?mod=friendlink&action=add">重新填写</a>
<?
        }else if($errormessage == 'error_siteurl_blank')
	{
        	?>
添加失败.站点地址不能为空.<a href="index.php?mod=friendlink&action=add">重新填写</a>
<?
        }else if($errormessage == 'error_register_email_notmatch')
	{
        	?>
两次EMAIL输入不匹配
<?
        }else if($errormessage == 'error_register_email_invalid')
	{
        	?>
输入EMAIL不正确
<?
        }
?></b>
		        </div>

					<div class="reg">

					</div>
</div>

</div>
</div>
<!-- 通栏广告1 -->


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