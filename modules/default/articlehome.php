<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
<title><?=$phparticletitle?> - <?=$article[title]?> - Powered By phpArticle</title>
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

<script language=JavaScript>
function doZoom(size){
document.getElementById('zoom').style.fontSize=size+'px'
}
</script>

<div class="mainline">&nbsp;</div>
<!-- 主栏目开始 -->
<div class="maincolumn">

		<div class="pagelist">
		<div class="pagelisttitleico">&nbsp;</div>
		<div class="pagelisttitlebg">
		<div class="pagelisttitlename">
<?=$navbar?>
		</div>
		<div class="pagelisttitlemore"><a href="#"><img height="9" src="images/xml.gif" width="29" border="0" alt="" /></a></div>
		</div>

		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
					<div class="title"> 

<span class=rated>


</span>

<span>
					  <h1><?=$article[title]?></h1> 
					  <h2><strong>日期：</strong><?=$article[date]?> 13:32:12&nbsp;&nbsp;<strong>点击：</strong><script src="<?=$phparticleurl?>/count.php?aid=<?=$articleid?>"></script>&nbsp;&nbsp;<strong>作者：</strong><?=$article[author]?>&nbsp;&nbsp;<strong>来源：</strong><a href='#' target=_blank><?=$article[source]?></a>  
<br>
<b><a href="<?=$g_o_back2root?>/index.php?mod=comment&articleid=<?=$articleid?>&action=add">发表评论</a></b> | <b><a href="<?=$g_o_back2root?>/index.php?mod=comment&articleid=<?=$articleid?>&action=view">查看评论</a></b> | <b><a href="<?=$g_o_back2root?>/index.php?mod=favorite&articleid=<?=$articleid?>&pagenum=<?=$pagenum?>&action=add">加入收藏</a></b> | <b><a href="<?=$g_o_back2root?>/index.php?mod=recommend&articleid=<?=$articleid?>&pagenum=<?=$pagenum?>">Email给朋友</a></b> | <b><a href="<?=$g_o_back2root?>/index.php?mod=print&articleid=<?=$articleid?>">打印本文</a></b> | 字体：[<A href="javascript:doZoom(16)">大</A> <A href="javascript:doZoom(14)">中</A> <A href="javascript:doZoom(12)">小</A>] </h2>
</span>


					</div>
					<div class="articlecontent">


<font id=zoom><?=$articletext[articletext]?></font>

<div class="clear">&nbsp;</div>
</div>



<div>

</div>



<fieldset>

                <span class=left></span>
                <span class=right>
                  <table border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td><b>平均得分 <?=$average?>, 共 <?=$totalvoters?> 人评分</b></td>
                    </tr>
                    <tr>
                      <td>
                        <table border="0" cellspacing="1" cellpadding="2">
                          <tr>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[1]?>"></td>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[2]?>"></td>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[3]?>"></td>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[4]?>"></td>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[5]?>"></td>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[6]?>"></td>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[7]?>"></td>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[8]?>"></td>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[9]?>"></td>
                            <td width="10" height="20" valign="bottom"><img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/bar.gif" border="0" width="10" height="<?=$barheight[10]?>"></td>
                          </tr>
                          <tr>
                            <td nowrap align="center">1</td>
                            <td nowrap align="center">2</td>
                            <td nowrap align="center">3</td>
                            <td nowrap align="center">4</td>
                            <td nowrap align="center">5</td>
                            <td nowrap align="center">6</td>
                            <td nowrap align="center">7</td>
                            <td nowrap align="center">8</td>
                            <td nowrap align="center">9</td>
                            <td nowrap align="center">10</td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </span>
</fieldset>



<fieldset>
<table width="100%" border="0" cellpadding="4">
  <tr align="center">
    <td align="left" width="50%"><b>最新评论
(共有 <?=$article[comments]?> 条评论)</b></td>
    <td width="20%"><b>发表时间</b></td>
    <td><b>作者</b></td>
    <td><b>回复</b></td>
  </tr>
    
  <tr align="right">
    <td colspan="4"><b><a href="<?=$g_o_back2root?>/index.php?mod=comment&action=view&articleid=<?=$articleid?>">更多评论...</a></b></td>
  </tr>
</table>
</fieldset>


		</div>
		<div class="tool">
			<span></span>
			<a href="<?=$g_o_back2root?>/index.php?mod=comment&articleid=<?=$articleid?>&action=add" class="button_content" title="添加评论" target="_self">添加评论</a>
			<a href="<?=$g_o_back2root?>/index.php?mod=comment&articleid=<?=$articleid?>&action=view" class="button_content" title="浏览评论" target="_self">浏览评论</a>
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
            
            <img src="<?=$phparticleurl?>/images/ar2.gif" width="6" height="7" />&nbsp;
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