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
<title><?=$phparticletitle?> - ��Ա��½</title>

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
���λ�ã�<a href="<?=$homepage?>/" class="classlinkclass">��ҳ</a>&nbsp;>&nbsp;<a href="<?=$phparticleurl?>" class="classlinkclass"><?=$phparticletitle?></a>&nbsp;>&nbsp;��Ա��½
		</div>
		</div>
		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
					<div class="title"> 
<b>��Ա��½</b>
					</div>
<fieldset>
  <form method="post" action="<?=$g_o_back2root?>/index.php?mod=member">
   <div><label>�û���:</label><input type="text" name="username"></div>
   <div><label>����:</label><input type="password" name="password"></div>
   <div><label>��ס����?</label>
<input type="radio" name="rememberpw" value="1" checked>��
<input type="radio" name="rememberpw" value="0">��</div>
<div><label>&nbsp;</label>
<a href="<?=$g_o_back2root?>/index.php?mod=member&action=forgetpassword">������������?</a></div>
<div><label>&nbsp;</label>
<a href="<?=$g_o_back2root?>/index.php?mod=register">���ھ�ע���Ϊ��Ա?</a></div>
  <div>
<label>&nbsp;</label>
                          <input type="hidden" name="url" value="<?=$url?>">
                          <input type="hidden" name="action" value="login">
                          <input type="submit" value=" ��½ " class="buttot">
                          <input type="reset" value=" ���� " class="buttot">
  </div>
  </form>
</fieldset>

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
</html><?
exit;
}
?>

<?
if ($action=="forgetpassword") {
    ?><head>
<title><?=$phparticletitle?> - ȡ������ </title>
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
���λ�ã�<a href="<?=$homepage?>/" class="classlinkclass">��ҳ</a>&nbsp;>&nbsp;<a href="<?=$phparticleurl?>" class="classlinkclass"><?=$phparticletitle?></a>&nbsp;>&nbsp;ȡ������
		</div>
		</div>

		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
					<div class="title"> 
<b>ȡ�ص�½����</b>
					</div>
<fieldset>
  <form method="post" action="<?=$g_o_back2root?>/index.php?mod=member">
<div><label>����������Email��ַ:</label><input type="text" name="email">(ע��ʱ���õ������ַ)</div>
<div>
<label>&nbsp;</label>
                    <input type="hidden" name="action" value="getpassword">
                    <input type="submit" value="ȡ������" class="buttot" name="submit">
                    <input type="reset" value="  ��    ��  "  class="buttot" name="reset">
</div>
  </form> 
   
</fieldset></div>

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
���: $pauserinfo[username]

��������Ѿ�����.��ʹ���������½.

����û���:$username
�������:$password

����������:
$g_o_back2root/index.php?mod=member&action=resetpassword

�������µ�ַ��½:
$g_o_back2root/index.php?mod=member&action=login

���Ҫ�޸�����,�������������޸�:
$g_o_back2root/index.php?mod=member&action=modpassword

Thanks
$phparticletitle ($phparticleurl)
";

        $mail_resetpassword_confirm = "
�������������,��ȷ��!
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
�޸ĸ�������
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
�޸�����
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
���λ�ã�<a href="<?=$homepage?>/" class="classlinkclass">��ҳ</a>&nbsp;>&nbsp;<a href="<?=$phparticleurl?>" class="classlinkclass"><?=$phparticletitle?></a>&nbsp;>&nbsp;<?=$position?>
		</div>
		</div>
		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
					<div class="title1"> 
<b>����̨����</b> <br/>
<a href="index.php?mod=usercp">�ҵĿ���̨</a><br/>
<a href="index.php?mod=favorite&action=view">�ҵ��ղ�</a><br/>
<a href="index.php?mod=myarticle&action=view">�ҵ�����</a><br/>
<a href="index.php?mod=member&action=modpassword">�޸�����</a><br/>
<a href="index.php?mod=member&action=modprofile">�޸�����</a><br/>
					</div>
					<div class="reg1">

<?
if ($_GET[action]==modpassword) {
	?><form method="post" name="form" action="index.php?mod=member">
      <fieldset><legend>�޸�����</legend>
		<div>
			<label>������:</label>
			<input name="oldpassword" type="password" maxlength="50" size="30" />
		</div>
		<div>
			<label>������:</label>
			<input name="newpassword1" type="password" maxlength="50" size="30" />
		</div>
		<div>
			<label>ȷ��������:</label>
			<input name="newpassword2" type="password" maxlength="50" size="30" />
		</div>

            <div class=enter>
              <input type="hidden" name="action" value="updatepassword">
              <input type="submit" class="buttot" value="  �ύ  ">
              <input type="reset" class="buttot" value="  ����  ">
            </div>


      </fieldset>
</form><?
}else
if ($_GET[action]=="modprofile") {
	?>  <form method="post" name="form" action="index.php?mod=member" onSubmit="return process_data(this)">
<fieldset><legend>�����޸�</legend>

           <div> 
              ˵������������������Ϣ,��<span class="reqasterisk">*</span>�ľ�Ϊ����ѡ��,���û�����,��������ѡ���Ϊ����
          </div>
          <div>
            <label><span class="reqasterisk">*</span>�û���:</label>
            <input type="text" maxlength="15" value="<?=$pauserinfo[username]?>" readonly><br>
              �����޸�
          </div>
          <div>
            <label><span class="reqasterisk">*</span>Email��ַ:</label>
              <input name="email" type="text" maxlength="50" size="30" onChange="javascript:this.value=this.value.toLowerCase();" value="<?=$pauserinfo[email]?>"><br>
              ��ȷ�ϸ�Email��ַ��Ч(50���ַ�����)
          </div>
          <div>
            <label><span class="reqasterisk">*</span>�Ա�:</label>
              <select name="sex">
                <option value="unknow" <?=$sexselected[unknow]?>>����</option>
                <option value="male" <?=$sexselected[male]?>>��</option>
                <option value="female" <?=$sexselected[female]?>>Ů</option>
              </select>
          </div>
          <div>
            <label><span class="reqasterisk">*</span>��ס����:</label>
              <input type="checkbox" name="rememberpw" value="1" check <?=$pwchecked?>>
          </div>
           <div> 
              ѡ�����ݣ�
          </div>
          <div>
            <label>�����ڵ�ʱ��:</label>
            <select name="timezoneoffset">
                <option value="-12" <?=$timezoneselected[-12]?>>(GMT-12:00)�������п�,����ֵ�</option>
                <option value="-11" <?=$timezoneselected[-11]?>>(GMT-11:00)��;��,��Ħ��Ⱥ��</option>
                <option value="-10" <?=$timezoneselected[-10]?>>(GMT-10:00)������</option>
                <option value="-9" <?=$timezoneselected[-9]?>>(GMT-09:00)����˹��</option>
                <option value="-8" <?=$timezoneselected[-8]?>>(GMT-08:00)̫ƽ��ʱ��(�����ͼ��ô�)</option>
                <option value="-7" <?=$timezoneselected[-7]?>>(GMT-07:00)ɽ��ʱ��(�����ͼ��ô�)</option>
                <option value="-6" <?=$timezoneselected[-6]?>>(GMT-06:00)ī�����</option>
                <option value="-5" <?=$timezoneselected[-5]?>>(GMT-05:00)�����,����</option>
                <option value="-4" <?=$timezoneselected[-4]?>>(GMT-04:00)������ʱ��(���ô�)</option>
                <option value="-3.5" <?=$timezoneselected[-3.5]?>>(GMT-03:30)Ŧ����</option>
                <option value="-3" <?=$timezoneselected[-3]?>>(GMT-03:00)��������</option>
                <option value="-2" <?=$timezoneselected[-2]?>>(GMT-02:00)�д�����</option>
                <option value="-1" <?=$timezoneselected[-1]?>>(GMT-01:00)��ý�Ⱥ��</option>
                <option value="0" <?=$timezoneselected[0]?>>(GMT)��������ƽʱ:������</option>
                <option value="1" <?=$timezoneselected[1]?>>(GMT+01:00)��ķ˹�ص�</option>
                <option value="2" <?=$timezoneselected[2]?>>(GMT+02:00)������˹��</option>
                <option value="3" <?=$timezoneselected[3]?>>(GMT+03:00)�͸��</option>
                <option value="4" <?=$timezoneselected[4]?>>(GMT+04:00)��������</option>
                <option value="4.5" <?=$timezoneselected[4.5]?>>(GMT+04:30)������</option>
                <option value="5" <?=$timezoneselected[5]?>>(GMT+05:00)Ҷ�����ձ�</option>
                <option value="5.5" <?=$timezoneselected[5.5]?>>(GMT+05:30)�Ӷ�����</option>
                <option value="5.75" <?=$timezoneselected[5.75]?>>(GMT+05:45)�ӵ�����</option>
                <option value="6" <?=$timezoneselected[6]?>>(GMT+06:00)����ľͼ</option>
                <option value="7" <?=$timezoneselected[7]?>>(GMT+07:00)����</option>
                <option value="8" <?=$timezoneselected[8]?>>(GMT+08)����ʱ��</option>
                <option value="9" <?=$timezoneselected[9]?>>(GMT+09:00)����</option>
                <option value="9.5" <?=$timezoneselected[9.5]?>>(GMT+09:30)��������</option>
                <option value="10" <?=$timezoneselected[10]?>>(GMT+10:00)����˹��</option>
                <option value="11" <?=$timezoneselected[11]?>>(GMT+11:00)��ӵ�</option>
                <option value="12" <?=$timezoneselected[12]?>>(GMT+12:00)��ʿ</option>
                <option value="13" <?=$timezoneselected[13]?>>(GMT+13:00)Ŭ�Ⱒ�巨</option>
              </select>
            </label>
          <div>
            <label>���˼��:</label>
              <textarea cols="50" rows="5" name="intro"><?=$pauserinfo[intro]?></textarea>
          </div>
          <div>
            <label>QQ����:</label>
              <input type="text" name="qq" maxlength="15" value="<?=$pauserinfo[qq]?>">
          </div>
          <div>
            <label>Icq����:</label>
              <input type="text" name="icq" maxlength="15" value="<?=$pauserinfo[icq]?>">
          </div>
          <div>
            <label>Msn����:</label>
              <input type="text" name="msn" maxlength="15" value="<?=$pauserinfo[msn]?>">
          </div>
          <div>
            <label>������ҳ:</label>
              <input type="text" name="homepage" maxlength="100" value="<?=$pauserinfo[homepage]?>">
          </div>
          <div>
            <label>ͨѸ��ַ:</label>
              <input type="text" name="address" size="50" value="<?=$pauserinfo[address]?>">
          </div>
          <div>
            <label>��ϵ�绰:</label>
              <input type="text" name="tel" maxlength="15" size="18" value="<?=$pauserinfo[tel]?>">
          </div>
          <div class=enter>
              <input type="hidden" name="url" value="<?=$url?>">
              <input type="hidden" name="action" value="updateprofile">
              <input type="submit" value="�ύ�޸�" class="buttot">
              <input type="reset" value="���ñ�" class="buttot">
          </div>

</fieldset>
   </form>
<script type="text/javascript">
<!--
function process_data(theform) {

                if (theform.email.value=="") {
                        alert("������ Email ��ַ");
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