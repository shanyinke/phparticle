<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<head>
<title><?=$phparticletitle?> - ������ʾ</title>
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
������ʾ
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
���������С����̫����.���ֻ��������100���ַ�,�뷵����������.
<?
        }else if($errormessage == 'error_article_title_toolong')
	{
        	?>
������������±���̫����,���ֻ��������100���ַ�,�뷵����������.
<?
        }else if($errormessage == 'error_article_voted')
	{
        	?>
�����ǵ����ݼ�¼��,���ѶԸ�����ͶƱ.
<?
        }else if($errormessage == 'error_author_toolong')
	{
        	?>
���������������̫����,�����������20���ַ�,��10������.
<?
        }else if($errormessage == 'error_author_tooshort')
	{
        	?>
���������������̫����,����Ҫ����4���ַ�,���������ֻ�����.
<?
        }else if($errormessage == 'error_comment_message_blank')
	{
        	?><?
        }else if($errormessage == 'error_comment_message_toolong')
	{
        	?>
���۵�����̫����,���ֻ������ <?=$comment_message_limit?> ���ַ�.
<?
        }else if($errormessage == 'error_comment_title_blank')
	{
        	?>
���۱��ⲻ��Ϊ��,�뷵����������.
<?
        }else if($errormessage == 'error_comment_title_toolong')
	{
        	?>
����̫����,���ֻ������ <?=$comment_title_limit?> ���ַ�.
<?
        }else if($errormessage == 'error_email_recipients_blank')
	{
        	?>
�ռ������Ʋ���Ϊ��,�뷵������.
<?
        }else if($errormessage == 'error_favorite_fulled')
	{
        	?>
����ղؼ�����,���������ղؼ��е����º������.
<?
        }else if($errormessage == 'error_favorite_unselect')
	{
        	?>
��δѡ���κ�Ҫɾ��������.
<?
        }else if($errormessage == 'error_invalemail')
	{
        	?>
���������Email��ַ��Ч,�뷵����������.
<?
        }else if($errormessage == 'error_invalid_activationcode')
	{
        	?>
��Ч�ļ������
<?
        }else if($errormessage == 'error_invalid_activationcode_expiry')
	{
        	?>
��Ļ�Ա��������ѹ��ڲ�ʧЧ.����и��������,����ϵ<a href="mailto:<?=$webmastermail?>">����Ա</a>.
<?
        }else if($errormessage == 'error_invalid_articleid')
	{
        	?>
�����²����ڻ��ѱ�ɾ��.�����κ�����,����ϵ<a href="mailto:<?=$webmastermail?>">����Ա</a>
<?
        }else if($errormessage == 'error_invalid_commentid')
	{
        	?>
�����۲����ڻ��ѱ�ɾ��.
<?
        }else if($errormessage == 'error_invalid_messageid')
	{
        	?><?
        }else if($errormessage == 'error_invalid_sortid')
	{
        	?>
���� ID ��Ч�򲻴���.�����κ�����,��֪ͨ<a href="mailto:<?=$webmastermail?>">����Ա</a>.
<?
        }else if($errormessage == 'error_invalid_vote')
	{
        	?>
���ͶƱ��Ч.
<?
        }else if($errormessage == 'error_keyword_blank')
	{
        	?>
�����Ĺؼ���������������ͬʱΪ��,�뷵�ز�����Ҫ�����Ĺؼ��ֻ�������.
<?
        }else if($errormessage == 'error_keyword_none')
	{
        	?>
<tr>
  <td>
<p align="center">
<img src="<?=$style[imagesfolder]?>/warning.gif" align="absmiddle"><span class="normalfont">��û�������κιؼ���,�뷵����������.</span>
</p>
  </td>
</tr>
<?
        }else if($errormessage == 'error_keyword_toolong')
	{
        	?>
��������Ĺؼ�̫����,�����������20���ַ�,��10������.
<?
        }else if($errormessage == 'error_keyword_tooshort')
	{
        	?>
��������Ĺؼ���̫����,����Ҫ����4���ַ�,���������ֻ�����.
<?
        }else if($errormessage == 'error_logined')
	{
        	?>
���Ѿ���½,�뷵��.
<?
        }else if($errormessage == 'error_login_fail')
	{
        	?>
��½ʧ��,�û�����������Ч,����㻹δע��,����<a href="index.php?mod=register">ע������</a>ע���Ϊ��Ա,���ɵ�½.
<?
        }else if($errormessage == 'error_message_message_blank')
	{
        	?>
�ظ����ݲ���Ϊ��.
<?
        }else if($errormessage == 'error_message_title_blank')
	{
        	?>
�ظ����ⲻ��Ϊ��.
<?
        }else if($errormessage == 'error_miss_article_title')
	{
        	?>
���ⲻ��Ϊ��,�뷵������.
<?
        }else if($errormessage == 'error_nopermission_loggedout')
	{
        	?>
<table width="100%" border="0" cellspacing="1" cellpadding="3" height="200">
  <tr>
    <td align="center"><span class="normalfont"><img src="<?=$style[imagesfolder]?>/warning.gif" align="absmiddle"><b>����ʧ��,��Ϊ�㻹δ��½,�뷵����ҳ��½,����㲻�ǻ�Ա,����ע������ע��.</b></span></td>
  </tr>
</table>
<?
        }else if($errormessage == 'error_password_wrong')
	{
        	?>
�����벻��ȷ,�뷵����������.
<?
        }else if($errormessage == 'error_registered')
	{
        	?>
�����ǵļ�¼��,���Ѿ�ע��,���������������,����<a href="index.php?mod=member&action=forgetpassword">����</a>��������.</b>
<?
        }else if($errormessage == 'error_register_blank')
	{
        	?>
�뷵�ز�������������Ϣ.
<?
        }else if($errormessage == 'error_register_notagree')
	{
        	?>
�뷵�ز�ͬ��˼��վ��ע������.
<?
        }else if($errormessage == 'error_register_notallow')
	{
        	?>
����Ա�ѽ�ֹ�»�Ա��ע��,�����κ�����,��ϵ<a href="mailto:<?=$webmastermail?>">����Ա</a>.
<?
        }else if($errormessage == 'error_register_password_notmatch')
	{
        	?>
������������벻��ͬ,�뷵����������.
<?
        }
        else if($errormessage == 'error_register_password_toolong')
	{
        	?>
����������������.��վ������������볤��Ϊ <?=$password_length_min?> �� <?=$password_length_max?> �ַ�.
<?
        }
        else if($errormessage == 'error_register_password_tooshort')
	{
        	?>
����������������.��վ������������볤��Ϊ <?=$password_length_min?> �� <?=$password_length_max?> �ַ�.
<?
        }
        else if($errormessage == 'error_register_username_existed')
	{
        	?>
���û����ѱ�����ʹ��.�뷵�ز�ʹ�������û���ע��.
<?
        }else if($errormessage == 'error_register_email_existed')
	{
        	?>
EMAIL �Ѿ�����
<?
        }
        else if($errormessage == 'error_register_username_toolong')
	{
        	?>
����ע����û�������,��վ����������û�������Ϊ <?=$username_length_min?> �� <?=$username_length_max?> �ַ�.
<?
        }
        else if($errormessage == 'error_register_username_tooshort')
	{
        	?>
����ע����û�������,��վ����������û�������Ϊ <?=$username_length_min?> �� <?=$username_length_max?> �ַ�.
<?
        }
        else if($errormessage == 'error_contribute_articles_exceedlimit')
	{
        	?>
��24Сʱ��ֻ�ܹ�Ͷ��<?=$pauserinfo['onedaypostmax']?>ƪ
<?
        }else if($errormessage == 'error_friendlink_added')
	{
        	?>
�Բ���,�������Ѿ����,�޷��༭.Ҫ�޸�����ϵ����Ա.
<?
        }else if($errormessage == 'error_friendlink_reject')
	{
        	?>
�Բ���,�������Ѿ����ܾ�.
<?
        }else if($errormessage == 'error_interval_tooshort')
	{
        	?>
����ʧ��,����������ʱ��̫��.
<?
        }else if($errormessage == 'error_logourl_blank')
	{
        	?>
���ʧ��.ʹ��logoΪ'��'��ʱ��,logo��ַ����Ϊ��.<a href="index.php?mod=friendlink&action=add">������д</a>
<?
        }else if($errormessage == 'error_sitename_blank')
	{
        	?>
���ʧ��.վ��������Ϊ��.<a href="index.php?mod=friendlink&action=add">������д</a>
<?
        }else if($errormessage == 'error_siteurl_blank')
	{
        	?>
���ʧ��.վ���ַ����Ϊ��.<a href="index.php?mod=friendlink&action=add">������д</a>
<?
        }else if($errormessage == 'error_register_email_notmatch')
	{
        	?>
����EMAIL���벻ƥ��
<?
        }else if($errormessage == 'error_register_email_invalid')
	{
        	?>
����EMAIL����ȷ
<?
        }
?></b>
		        </div>

					<div class="reg">

					</div>
</div>

</div>
</div>
<!-- ͨ�����1 -->


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