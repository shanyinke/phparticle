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
<b>�Բ���,��û�����Ȩ��.ԭ���������</b>
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
	          	<li>����δ��½,���������ע��,�����ұ������û����������½,������ǻ�Ա,����<a href="<?=$g_o_back2root?>/index.php?mod=register">ע��</a>����ע���Ϊ��Ա.</li>
	          	<?
        }
        if (!empty($reasons) AND is_array($reasons)) {
                foreach($reasons AS $reason) {
                	if('nopermission_reason_contribute_permission_denied'==$reason)
                	{
                        	?>
	          	<li>��û��Ͷ�����Ȩ��,�����Ҫ��Ȩ��,����ϵ����Ա.</li>
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
        <fieldset><legend>��Ա��½</legend>
<div>�������鿴���޸����ĸ�����Ϣ�����������û����������¼�����޸ġ�</div>

                <div>
                  <label><b>�û���:</b></label>
                    <input type="text" name="username">
                </div>
                <div>
                  <label><b>����:</b></label>
                    <input type="password" name="password">
                </div>
                <div>
                    <label><input type="checkbox" name="rememberpw" value="1"  checked/> <a href="#" title="ѡ���Ƿ��¼������Ϣ">��ס��</a></label>
					<input type="hidden" name="action" value="login">
                    <input type="hidden" name="url" value="<?=$url?>">
                    <input type="submit" class="buttot" value="  ��½  ">

                </div>
                <div>
<label>&nbsp;</label>
                  <a href="<?=$g_o_back2root?>/index.php?mod=member&action=forgetpassword">���������ҵĵ�¼����</a>
                </div>
                <div>
<label>&nbsp;</label>
                  <a href="<?=$g_o_back2root?>/index.php?mod=register">�����ھ�ע��Ϊ <?=$phparticletitle?>
                    ��Ա</a>
                </div>

</fieldset>
        <?
	} else {
		?>
                  <td>


  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td colspan="2"><span class="normalfont">
         <p>�������鿴���޸����ĸ�����Ϣ����������������޸ġ�</p>
        <b>�ѵ�½��Ա</b></span></td>
    </tr>
    <tr>
      <td><span class="normalfont"><b>�û���:</b></span></td>
      <td><span class="normalfont"><?=$pauserinfo[username]?></span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>
        <input type="hidden" name="action" value="logout">
        <input type="hidden" name="url" value="<?=$url?>">
        <input type="submit" class="button" value="�˳���½">
      </td>
    </tr>
    <tr>
      <td colspan="2"><span class="normalfont"><a href="<?=$g_o_back2root?>/index.php?mod=member&action=modpassword">��Ҫ�޸�����</a></span></td>
    </tr>
    <tr>
      <td colspan="2"><span class="normalfont"><a href="<?=$g_o_back2root?>/index.php?mod=member&action=modprofile">��Ҫ�޸ĸ�������</a></span></td>
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

<?exit;?>