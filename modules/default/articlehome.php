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

<script language=JavaScript>
function doZoom(size){
document.getElementById('zoom').style.fontSize=size+'px'
}
</script>

<div class="mainline">&nbsp;</div>
<!-- ����Ŀ��ʼ -->
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
					  <h2><strong>���ڣ�</strong><?=$article[date]?> 13:32:12&nbsp;&nbsp;<strong>�����</strong><script src="<?=$phparticleurl?>/count.php?aid=<?=$articleid?>"></script>&nbsp;&nbsp;<strong>���ߣ�</strong><?=$article[author]?>&nbsp;&nbsp;<strong>��Դ��</strong><a href='#' target=_blank><?=$article[source]?></a>  
<br>
<b><a href="<?=$g_o_back2root?>/index.php?mod=comment&articleid=<?=$articleid?>&action=add">��������</a></b> | <b><a href="<?=$g_o_back2root?>/index.php?mod=comment&articleid=<?=$articleid?>&action=view">�鿴����</a></b> | <b><a href="<?=$g_o_back2root?>/index.php?mod=favorite&articleid=<?=$articleid?>&pagenum=<?=$pagenum?>&action=add">�����ղ�</a></b> | <b><a href="<?=$g_o_back2root?>/index.php?mod=recommend&articleid=<?=$articleid?>&pagenum=<?=$pagenum?>">Email������</a></b> | <b><a href="<?=$g_o_back2root?>/index.php?mod=print&articleid=<?=$articleid?>">��ӡ����</a></b> | ���壺[<A href="javascript:doZoom(16)">��</A> <A href="javascript:doZoom(14)">��</A> <A href="javascript:doZoom(12)">С</A>] </h2>
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
                      <td><b>ƽ���÷� <?=$average?>, �� <?=$totalvoters?> ������</b></td>
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
    <td align="left" width="50%"><b>��������
(���� <?=$article[comments]?> ������)</b></td>
    <td width="20%"><b>����ʱ��</b></td>
    <td><b>����</b></td>
    <td><b>�ظ�</b></td>
  </tr>
    
  <tr align="right">
    <td colspan="4"><b><a href="<?=$g_o_back2root?>/index.php?mod=comment&action=view&articleid=<?=$articleid?>">��������...</a></b></td>
  </tr>
</table>
</fieldset>


		</div>
		<div class="tool">
			<span></span>
			<a href="<?=$g_o_back2root?>/index.php?mod=comment&articleid=<?=$articleid?>&action=add" class="button_content" title="�������" target="_self">�������</a>
			<a href="<?=$g_o_back2root?>/index.php?mod=comment&articleid=<?=$articleid?>&action=view" class="button_content" title="�������" target="_self">�������</a>
			<a href="<?=$g_o_back2root?>/index.php?mod=print&articleid=<?=$articleid?>" class="button_content" title="��ӡ����" target="_self">��ӡ����</a>
			<a href="javascript:window.close();" class="button_content">�رմ���</a>
		</div>
		<div class="clear">&nbsp;</div>
		<div class="pagelistfooter">
			<div id="bklist"><a href="javascript:history.go(-1);"><img src="<?=$phparticleurl?>/images/ar2u.gif" width="5" height="8" /> �����б�</a></div> 
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