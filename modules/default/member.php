<?php
$timestamp = time();
if (empty($_POST[action]) AND !empty($_GET[action])) {
    $action = $_GET[action];
} else if (!empty($_POST[action])) {
    $action = $_POST[action];
}

if (empty($action)) {
    $noheader = 1;
    header("Location : $phparticleurl/index.php");
}
?>
<?

if ($_POST[action]=="login") {

    $checkuser = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."user
                                                WHERE (username='".addslashes(htmlspecialchars(trim($_POST[username])))."' AND password='".md5($_POST[password])."')");

    if (!empty($checkuser)) {
    	$checkuser['cookietime'] = 0;
        if ($checkuser[rememberpw] == 1 OR $_POST[rememberpw]==1) {
		$checkuser['cookietime'] = 3600*24*365;
            setcookie("pauserid",$checkuser[userid],$timestamp+3600*24*365);
            setcookie("papasswordhash",$checkuser[password],$timestamp+3600*24*365);
        } else {
            setcookie("pauserid",$checkuser[userid]);
            setcookie("papasswordhash",$checkuser[password]);
        }

        if (pa_isset($_POST[rememberpw])) {
            $DB->query("UPDATE ".$db_prefix."user SET
                               rememberpw='".addslashes($_POST[rememberpw])."'
                               WHERE userid='$checkuser[userid]'");
        }


        if (empty($url)) {
            $url = "./";
        }
	if(file_exists("admin/loadsystem/passport_default.php"))
	{
		
include "admin/loadsystem/passport_default.php";
$member = array();
$checkuser['time']=$timestamp;
foreach($membertable AS $pa=>$dz5)
{
	if($checkuser[$pa])
	$member[$dz5] = $checkuser[$pa];
}
$auth = passport_encrypt(passport_encode($member), $passportkey);
$verify = md5("login".$auth.$url.$passportkey);
header('Location: '.$bbsurl.'/api/passport.php?action=login&auth='.rawurlencode($auth).'&forward='.rawurlencode($url).'&verify='.rawurlencode($verify));
exit;
	}
	else{
	$redirectmsg="redirect_login_success";
    	include("modules/default/redirect.php");
	}
    } else {
    	$errormessage="error_login_fail";
    	include("modules/default/error.php");
    }

}
?>
<?
if ($_POST[action]==updatepassword) {

    if ($pauserinfo[userid]==0) {
        include("modules/default/nopermission.php");
    }

    $oldpassword = $_POST[oldpassword];
    $newpassword1 = $_POST[newpassword1];
    $newpassword2 = $_POST[newpassword2];

    $checkpassword = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."user
                                                    WHERE userid='$pauserinfo[userid]' AND password='".md5($oldpassword)."'");
    if (empty($checkpassword)) {
        $errormessage="error_password_wrong";
    	include("modules/default/error.php");
    }


    if (strlen($newpassword1)<$password_length_min) {
        $errormessage="error_register_password_tooshort";
    	include("modules/default/error.php");
    }

    if (strlen($newpassword2)>$password_length_max) {
        $errormessage="error_register_password_toolong";
    	include("modules/default/error.php");
    }

    if ($newpassword1!=$newpassword2) {
        $errormessage="error_register_password_notmatch";
    	include("modules/default/error.php");
    }

    $DB->query("UPDATE ".$db_prefix."user SET
                       password='".md5($newpassword1)."'
                       WHERE userid='$pauserinfo[userid]'");
    if ($checkpassword[rememberpw] == 1) {
        setcookie("papasswordhash",md5($newpassword1),$timestamp+3600*24*365);
    } else {
        setcookie("papasswordhash",md5($newpassword1));
    }
    $url = "./index.php?mod=usercp";
    $redirectmsg="redirect_password_updated";
    	include("modules/default/redirect.php");

}
?>
<?
if ($action=="logout") {

    setcookie("pauserid","",$timestamp-3600*24*365);
    setcookie("papasswordhash","",$timestamp-3600*24*365);
    session_unset();
    session_destroy();

    $url = $_SERVER[HTTP_REFERER];
    if(file_exists("admin/loadsystem/passport_default.php"))
	{
		
include "admin/loadsystem/passport_default.php";
$pauserinfo['time']=time();
foreach($pauserinfo AS $pa=>$dz5)
{
	if($checkuser[$pa])
	$member[$dz5] = $checkuser[$pa];
}
//$auth = passport_encrypt(passport_encode($member), $passportkey);
$verify = md5("logout".$auth.$url.$passportkey);
header('Location: '.$bbsurl.'/api/passport.php?action=logout&forward='.rawurlencode($url).'&verify='.rawurlencode($verify));
exit;
	}else{
	$redirectmsg="redirect_logout_success";
    	include("modules/default/redirect.php");
	}

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?
if ($_GET[action]=="login") {

    if ($pauserinfo[userid]!=0) {
    	$errormessage="error_logined";
    	include("modules/default/error.php");
    }
	if(file_exists("admin/loadsystem/passport_default.php"))
	{
		
if($_GET['forward'])
$url = $_GET['forward'];
else $url = $_SERVER[HTTP_REFERER];
	}else
	$url = $_SERVER[HTTP_REFERER];

    ?><head>
<title><?=$phparticletitle?> - 会员登陆</title>

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
你的位置：<a href="<?=$homepage?>/" class="classlinkclass">首页</a>&nbsp;>&nbsp;<a href="<?=$phparticleurl?>" class="classlinkclass"><?=$phparticletitle?></a>&nbsp;>&nbsp;会员登陆
		</div>
		</div>
		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
					<div class="title"> 
<b>会员登陆</b>
					</div>
<fieldset>
  <form method="post" action="<?=$g_o_back2root?>/index.php?mod=member">
   <div><label>用户名:</label><input type="text" name="username"></div>
   <div><label>密码:</label><input type="password" name="password"></div>
   <div><label>记住密码?</label>
<input type="radio" name="rememberpw" value="1" checked>是
<input type="radio" name="rememberpw" value="0">否</div>
<div><label>&nbsp;</label>
<a href="<?=$g_o_back2root?>/index.php?mod=member&action=forgetpassword">我忘记了密码?</a></div>
<div><label>&nbsp;</label>
<a href="<?=$g_o_back2root?>/index.php?mod=register">现在就注册成为会员?</a></div>
  <div>
<label>&nbsp;</label>
                          <input type="hidden" name="url" value="<?=$url?>">
                          <input type="hidden" name="action" value="login">
                          <input type="submit" value=" 登陆 " class="buttot">
                          <input type="reset" value=" 重置 " class="buttot">
  </div>
  </form>
</fieldset>

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
</html><?
exit;
}
?>

<?
if ($action=="forgetpassword") {
    ?><head>
<title><?=$phparticletitle?> - 取回密码 </title>
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
你的位置：<a href="<?=$homepage?>/" class="classlinkclass">首页</a>&nbsp;>&nbsp;<a href="<?=$phparticleurl?>" class="classlinkclass"><?=$phparticletitle?></a>&nbsp;>&nbsp;取回密码
		</div>
		</div>

		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
					<div class="title"> 
<b>取回登陆密码</b>
					</div>
<fieldset>
  <form method="post" action="<?=$g_o_back2root?>/index.php?mod=member">
<div><label>请输入您的Email地址:</label><input type="text" name="email">(注册时所用的邮箱地址)</div>
<div>
<label>&nbsp;</label>
                    <input type="hidden" name="action" value="getpassword">
                    <input type="submit" value="取回密码" class="buttot" name="submit">
                    <input type="reset" value="  重    置  "  class="buttot" name="reset">
</div>
  </form> 
   
</fieldset></div>

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
</html><?
    exit;
}
?>

<?
if ($action=="getpassword") {

    $email = htmlspecialchars(trim($email));
    if (!validate_email($email)) {
        $errormessage="error_invalemail";
    	include("modules/default/error.php");
    }
    $checkuser = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."user WHERE email='".addslashes($email)."'");

    if (!empty($checkuser)) {
        $newpw = makeradompw();
        $username = $checkuser[username];
        $password = $newpw;

        $mail_resetpassword="
你好: $pauserinfo[username]

你的密码已经重置.请使用新密码登陆.

你的用户名:$username
你的密码:$password

激活新密码:
$g_o_back2root/index.php?mod=member&action=resetpassword

请点击以下地址登陆:
$g_o_back2root/index.php?mod=member&action=login

如果要修改密码,请点击以下链接修改:
$g_o_back2root/index.php?mod=member&action=modpassword

Thanks
$phparticletitle ($phparticleurl)
";

        $mail_resetpassword_confirm = "
你的密码已重置,请确认!
";
        mail($email,$mail_resetpassword_confirm,$mail_resetpassword);

        $DB->query("UPDATE ".$db_prefix."user SET radompassword='".md5($newpw)."' WHERE userid='$checkuser[userid]'");

        setcookie("pauserid","",$timestamp-3600*24*365);
        setcookie("papasswordhash","",$timestamp-3600*24*365);
        session_unset();
        session_destroy();

        $information = "information_password_send";
        include("modules/default/information.php");

    } else {
        $errormessage="error_invalemail";
    	include("modules/default/error.php");
    }

}else if($action == "resetpassword")
{
	$DB->query("UPDATE ".$db_prefix."user SET password=radompassword,radompassword='' WHERE radompassword='".md5($_GET[rpw])."'");
	$information = "information_password_reseted";
        include("modules/default/information.php");
}
?>

<?
if ($_GET[action]=="modprofile") {

    if ($pauserinfo[userid]==0) {
        include("modules/default/nopermission.php");
    }

    $position = "
修改个人资料
";

    $pauserinfo = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."user WHERE userid='$pauserinfo[userid]'");

//    print_rr($pauserinfo);

    unset($bgcolor);
    $bgcolor[modprofile] = "bgcolor=\"$style[firstalt]\"";

    unset($sexselected);
    $sexselected[$pauserinfo[sex]] = "selected";

    if ($pauserinfo[rememberpw]) {
        $pwchecked = "checked";
    }

    if (empty($pauserinfo[qq])) {
        $pauserinfo[qq] = "";
    }

    if (empty($pauserinfo[icq])) {
        $pauserinfo[icq] = "";
    }

    unset($timezoneselected);
//    settype($pauserinfo[timezoneoffset],"string");
    $timezoneselected["$pauserinfo[timezoneoffset]"] = "selected";
    //print_rr($timezoneselected);

}
?>


<?
if ($_GET[action]==modpassword) {

    if ($pauserinfo[userid]==0) {
        include("modules/default/nopermission.php");
    }

    $position = "
修改密码
";

    unset($bgcolor);
    $bgcolor[modpassword] = "bgcolor=\"$style[firstalt]\"";
}
?>

<?
if ($_POST[action]=="updateprofile") {

    if ($pauserinfo[userid]==0) {
        include("modules/default/nopermission.php");
    }

    $email = strtolower(trim($_POST[email]));

    if (!validate_email($email)) {
        $errormessage="error_invalemail";
    	include("modules/default/error.php");
    }

    $checkuser = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."user WHERE userid!='$pauserinfo[userid]' AND email='".addslashes($email)."'");

    if (!empty($checkuser)) {
        $errormessage="error_email_existed";
    	include("modules/default/error.php");
    }


    $sex = $_POST[sex];
    if ($sex != "male" AND $sex!="female") {
        $sex = "unknow";
    }

    //$_POST[timezoneoffset] = 8;

    $DB->query("UPDATE ".$db_prefix."user SET
                       email='".addslashes($email)."',
                       sex='".addslashes($sex)."',
                       homepage='".addslashes(htmlspecialchars(trim($_POST[homepage])))."',
                       address='".addslashes(htmlspecialchars(trim($_POST[address])))."',
                       qq='".addslashes(htmlspecialchars(trim($_POST[qq])))."',
                       icq='".addslashes(htmlspecialchars(trim($_POST[icq])))."',
                       msn='".addslashes(htmlspecialchars(trim($_POST[msn])))."',
                       intro='".addslashes(htmlspecialchars(trim($_POST[intro])))."',
                       tel='".addslashes(htmlspecialchars(trim($_POST[tel])))."',
                       rememberpw='$rememberpw',
                       timezoneoffset='".addslashes($_POST[timezoneoffset])."'
                       WHERE userid='$pauserinfo[userid]'
                       ");

    $_SESSION[pauserinfo] = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."user WHERE userid='$pauserinfo[userid]'");

    $url = "./index.php?mod=member&action=modprofile";
    $redirectmsg="redirect_modprofile_success";
    	include("modules/default/redirect.php");
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
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
你的位置：<a href="<?=$homepage?>/" class="classlinkclass">首页</a>&nbsp;>&nbsp;<a href="<?=$phparticleurl?>" class="classlinkclass"><?=$phparticletitle?></a>&nbsp;>&nbsp;<?=$position?>
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

<?
if ($_GET[action]==modpassword) {
	?><form method="post" name="form" action="index.php?mod=member">
      <fieldset><legend>修改密码</legend>
		<div>
			<label>旧密码:</label>
			<input name="oldpassword" type="password" maxlength="50" size="30" />
		</div>
		<div>
			<label>新密码:</label>
			<input name="newpassword1" type="password" maxlength="50" size="30" />
		</div>
		<div>
			<label>确认新密码:</label>
			<input name="newpassword2" type="password" maxlength="50" size="30" />
		</div>

            <div class=enter>
              <input type="hidden" name="action" value="updatepassword">
              <input type="submit" class="buttot" value="  提交  ">
              <input type="reset" class="buttot" value="  重置  ">
            </div>


      </fieldset>
</form><?
}else
if ($_GET[action]=="modprofile") {
	?>  <form method="post" name="form" action="index.php?mod=member" onSubmit="return process_data(this)">
<fieldset><legend>资料修改</legend>

           <div> 
              说明：请输入完整的信息,带<span class="reqasterisk">*</span>的均为必填选项,除用户名外,其它所有选项均为保密
          </div>
          <div>
            <label><span class="reqasterisk">*</span>用户名:</label>
            <input type="text" maxlength="15" value="<?=$pauserinfo[username]?>" readonly><br>
              不可修改
          </div>
          <div>
            <label><span class="reqasterisk">*</span>Email地址:</label>
              <input name="email" type="text" maxlength="50" size="30" onChange="javascript:this.value=this.value.toLowerCase();" value="<?=$pauserinfo[email]?>"><br>
              请确认该Email地址有效(50个字符以内)
          </div>
          <div>
            <label><span class="reqasterisk">*</span>性别:</label>
              <select name="sex">
                <option value="unknow" <?=$sexselected[unknow]?>>不明</option>
                <option value="male" <?=$sexselected[male]?>>男</option>
                <option value="female" <?=$sexselected[female]?>>女</option>
              </select>
          </div>
          <div>
            <label><span class="reqasterisk">*</span>记住密码:</label>
              <input type="checkbox" name="rememberpw" value="1" check <?=$pwchecked?>>
          </div>
           <div> 
              选填内容：
          </div>
          <div>
            <label>你所在的时区:</label>
            <select name="timezoneoffset">
                <option value="-12" <?=$timezoneselected[-12]?>>(GMT-12:00)埃尼威托克,夸贾林岛</option>
                <option value="-11" <?=$timezoneselected[-11]?>>(GMT-11:00)中途岛,萨摩亚群岛</option>
                <option value="-10" <?=$timezoneselected[-10]?>>(GMT-10:00)夏威夷</option>
                <option value="-9" <?=$timezoneselected[-9]?>>(GMT-09:00)阿拉斯加</option>
                <option value="-8" <?=$timezoneselected[-8]?>>(GMT-08:00)太平洋时间(美国和加拿大)</option>
                <option value="-7" <?=$timezoneselected[-7]?>>(GMT-07:00)山地时间(美国和加拿大)</option>
                <option value="-6" <?=$timezoneselected[-6]?>>(GMT-06:00)墨西哥城</option>
                <option value="-5" <?=$timezoneselected[-5]?>>(GMT-05:00)波哥大,利马</option>
                <option value="-4" <?=$timezoneselected[-4]?>>(GMT-04:00)大西洋时间(加拿大)</option>
                <option value="-3.5" <?=$timezoneselected[-3.5]?>>(GMT-03:30)纽芬兰</option>
                <option value="-3" <?=$timezoneselected[-3]?>>(GMT-03:00)巴西利亚</option>
                <option value="-2" <?=$timezoneselected[-2]?>>(GMT-02:00)中大西洋</option>
                <option value="-1" <?=$timezoneselected[-1]?>>(GMT-01:00)佛得角群岛</option>
                <option value="0" <?=$timezoneselected[0]?>>(GMT)格林威治平时:都柏林</option>
                <option value="1" <?=$timezoneselected[1]?>>(GMT+01:00)阿姆斯特丹</option>
                <option value="2" <?=$timezoneselected[2]?>>(GMT+02:00)布嘉勒斯特</option>
                <option value="3" <?=$timezoneselected[3]?>>(GMT+03:00)巴格达</option>
                <option value="4" <?=$timezoneselected[4]?>>(GMT+04:00)阿布扎比</option>
                <option value="4.5" <?=$timezoneselected[4.5]?>>(GMT+04:30)喀布尔</option>
                <option value="5" <?=$timezoneselected[5]?>>(GMT+05:00)叶卡捷琳堡</option>
                <option value="5.5" <?=$timezoneselected[5.5]?>>(GMT+05:30)加尔各答</option>
                <option value="5.75" <?=$timezoneselected[5.75]?>>(GMT+05:45)加德满都</option>
                <option value="6" <?=$timezoneselected[6]?>>(GMT+06:00)阿拉木图</option>
                <option value="7" <?=$timezoneselected[7]?>>(GMT+07:00)曼谷</option>
                <option value="8" <?=$timezoneselected[8]?>>(GMT+08)北京时间</option>
                <option value="9" <?=$timezoneselected[9]?>>(GMT+09:00)东京</option>
                <option value="9.5" <?=$timezoneselected[9.5]?>>(GMT+09:30)阿德莱德</option>
                <option value="10" <?=$timezoneselected[10]?>>(GMT+10:00)布里斯班</option>
                <option value="11" <?=$timezoneselected[11]?>>(GMT+11:00)马加丹</option>
                <option value="12" <?=$timezoneselected[12]?>>(GMT+12:00)富士</option>
                <option value="13" <?=$timezoneselected[13]?>>(GMT+13:00)努库阿洛法</option>
              </select>
            </label>
          <div>
            <label>个人简介:</label>
              <textarea cols="50" rows="5" name="intro"><?=$pauserinfo[intro]?></textarea>
          </div>
          <div>
            <label>QQ号码:</label>
              <input type="text" name="qq" maxlength="15" value="<?=$pauserinfo[qq]?>">
          </div>
          <div>
            <label>Icq号码:</label>
              <input type="text" name="icq" maxlength="15" value="<?=$pauserinfo[icq]?>">
          </div>
          <div>
            <label>Msn号码:</label>
              <input type="text" name="msn" maxlength="15" value="<?=$pauserinfo[msn]?>">
          </div>
          <div>
            <label>个人主页:</label>
              <input type="text" name="homepage" maxlength="100" value="<?=$pauserinfo[homepage]?>">
          </div>
          <div>
            <label>通迅地址:</label>
              <input type="text" name="address" size="50" value="<?=$pauserinfo[address]?>">
          </div>
          <div>
            <label>联系电话:</label>
              <input type="text" name="tel" maxlength="15" size="18" value="<?=$pauserinfo[tel]?>">
          </div>
          <div class=enter>
              <input type="hidden" name="url" value="<?=$url?>">
              <input type="hidden" name="action" value="updateprofile">
              <input type="submit" value="提交修改" class="buttot">
              <input type="reset" value="重置表单" class="buttot">
          </div>

</fieldset>
   </form>
<script type="text/javascript">
<!--
function process_data(theform) {

                if (theform.email.value=="") {
                        alert("请输入 Email 地址");
                        theform.email.focus();
                        return false;
                }

}

//-->
</script><?
}
?>
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