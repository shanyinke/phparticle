<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<head>
<title><?=$phparticletitle?></title>
<meta http-equiv="Refresh" content="1; URL=<?=$url?>">
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-CN" />
<link rel="icon" href="<?=$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<link rel="shortcut icon" href="<?$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<meta name="description" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<meta name="keywords" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<script src="<?=$g_o_back2root?>/images/js/chinahtml.js" type="text/javascript"></script>
<link href="<?=$g_o_back2root?>/images/css/css.css" rel="stylesheet" type="text/css" />
</head>

<div class="mainline">&nbsp;</div>
<!-- ����Ŀ��ʼ -->
<div class="maincolumn">
		<div class="pagelist">
		<div class="pagelisttitleico">&nbsp;</div>
		<div class="pagelisttitlebg">
		<div class="pagelisttitlename">
��ʾ��Ϣ
		</div>
		</div>
		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
		       <div class="title"> 
<h1>

<?
        if (!isset($url)) {
                $url = "./";
        }
        if ($information == "information") {
                $img = "information.gif";
        } else {
                $img = "warning.gif";
        }
        if($redirectmsg == 'redirect_article_nextpage')
	{
        	?>
���������һҳ
<?
        }else if($redirectmsg == 'redirect_article_submited')
	{
        	?>
����������ύ,���ȴ�����Ա������.
<?
        }else if($redirectmsg == 'redirect_comment_added')
	{
        	?>
��������ѷ���.�ּ������ظ�����.
<?
        }else if($redirectmsg == 'redirect_email_sended')
	{
        	?>
Email �ѳɹ����ͳ�ȥ,�ּ������ظ�����.
<?
        }else if($redirectmsg == 'redirect_favorite_deleted')
	{
        	?>
����ѡ�е������ѳɹ�ɾ��.
<?
        }else if($redirectmsg == 'redirect_favorite_success')
	{
        	?>
�������ѳɹ���ӵ��ղؼ���,�ּ������ظ�����.
<?
        }else if($redirectmsg == 'redirect_login_success')
	{
        	?>
���ѳɹ���½,�ּ�������.
<?
        }else if($redirectmsg == 'redirect_logout_success')
	{
        	?>
���ѳɹ��˳���½,�ּ���������ҳ.
<?
        }else if($redirectmsg == 'redirect_message_added')
	{
        	?>
��Ļظ��ѷ���,�ּ������ظ�����.
<?
        }else if($redirectmsg == 'redirect_message_edited')
	{
        	?>
�������Ѹ���,�ּ�������.
<?
        }else if($redirectmsg == 'redirect_modprofile_success')
	{
        	?>
��������ѳɹ�����.
<?
        }else if($redirectmsg == 'redirect_password_updated')
	{
        	?>
�����ѳɹ�����,���ס������.
<?
        }else if($redirectmsg == 'redirect_register_success')
	{
        	?>
ע��ɹ�,�ּ�������.
<?
        }else if($redirectmsg == 'redirect_password_reseted')
	{
        	?>
�������óɹ�����쿴����email��
<?
        }else if($redirectmsg == 'redirect_vote_success')
	{
        	?>
лл�������,�ּ������ظ�����.
<?
        }else if($redirectmsg == 'redirect_add_friendlink_success')
	{
        	?>
����ɹ�,�ȴ�������,��������������״̬.
<?
        }else if($redirectmsg == 'redirect_apprize_friendlink_success')
	{
        	?>
�Ѿ��ظ�����Ա.
<?
        }else if($redirectmsg == 'redirect_edit_friendlink_success')
	{
        	?>
�����༭�ɹ�.
<?
        }else if($redirectmsg == 'redirect_register_disaggree')
	{
        	?>
����ͬ��ע��Э��.
<?
        }

?></h2>
					  <h2>
<img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/warning.gif" border="0" align="absmiddle"><b><span class="normalfont"><a href="<?=$url?>">�����������û���Զ�ת��,�������ﷵ��.</a> </span></b>
</h2> 
		        </div>
                                 </div>

</div></div>


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