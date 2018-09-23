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
<!-- 主栏目开始 -->
<div class="maincolumn">
		<div class="pagelist">
		<div class="pagelisttitleico">&nbsp;</div>
		<div class="pagelisttitlebg">
		<div class="pagelisttitlename">
提示信息
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
继续添加下一页
<?
        }else if($redirectmsg == 'redirect_article_submited')
	{
        	?>
你的文章已提交,正等待管理员的审批.
<?
        }else if($redirectmsg == 'redirect_comment_added')
	{
        	?>
你的评论已发表.现即将返回该评论.
<?
        }else if($redirectmsg == 'redirect_email_sended')
	{
        	?>
Email 已成功发送出去,现即将返回该文章.
<?
        }else if($redirectmsg == 'redirect_favorite_deleted')
	{
        	?>
你所选中的文章已成功删除.
<?
        }else if($redirectmsg == 'redirect_favorite_success')
	{
        	?>
该文章已成功添加到收藏夹中,现即将返回该文章.
<?
        }else if($redirectmsg == 'redirect_login_success')
	{
        	?>
你已成功登陆,现即将返回.
<?
        }else if($redirectmsg == 'redirect_logout_success')
	{
        	?>
你已成功退出登陆,现即将返回首页.
<?
        }else if($redirectmsg == 'redirect_message_added')
	{
        	?>
你的回复已发表,现即将返回该评论.
<?
        }else if($redirectmsg == 'redirect_message_edited')
	{
        	?>
该评论已更新,现即将返回.
<?
        }else if($redirectmsg == 'redirect_modprofile_success')
	{
        	?>
你的资料已成功更新.
<?
        }else if($redirectmsg == 'redirect_password_updated')
	{
        	?>
密码已成功更新,请记住新密码.
<?
        }else if($redirectmsg == 'redirect_register_success')
	{
        	?>
注册成功,现即将返回.
<?
        }else if($redirectmsg == 'redirect_password_reseted')
	{
        	?>
密码重置成功，请察看您的email。
<?
        }else if($redirectmsg == 'redirect_vote_success')
	{
        	?>
谢谢你的评分,现即将返回该文章.
<?
        }else if($redirectmsg == 'redirect_add_friendlink_success')
	{
        	?>
申请成功,等待审批中,请留意友链申请状态.
<?
        }else if($redirectmsg == 'redirect_apprize_friendlink_success')
	{
        	?>
已经回复管理员.
<?
        }else if($redirectmsg == 'redirect_edit_friendlink_success')
	{
        	?>
友链编辑成功.
<?
        }else if($redirectmsg == 'redirect_register_disaggree')
	{
        	?>
您不同意注册协议.
<?
        }

?></h2>
					  <h2>
<img src="<?=$g_o_back2root?>/<?=$style[imagesfolder]?>/warning.gif" border="0" align="absmiddle"><b><span class="normalfont"><a href="<?=$url?>">如果你的浏览器没有自动转跳,请点击这里返回.</a> </span></b>
</h2> 
		        </div>
                                 </div>

</div></div>


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