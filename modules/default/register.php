<?php
$gzipoutput = 0;
$timestamp=time();
unset($action);
if (isset($_POST[action]) AND trim($_POST[action])!="") {
    $action = $_POST[action];
} elseif (isset($_GET[action]) AND trim($_GET[action])!="") {
    $action = $_GET[action];
}
?>
<?
if ($_POST[action]=="insert") {

    if ($pnuserinfo[userid]!=0) {
        $errormessage="error_registered";
    		include("modules/default/error.php");
    }

    if ($allowregister!=1) {
        $errormessage="error_register_notallow";
    		include("modules/default/error.php");
    }

    //if ($agree!="yes") {
    //    show_errormessage("error_register_notagree");
    //}

    $username = htmlspecialchars(trim($username));
    $password = trim($password);
    $password2 = trim($password2);
    $email = strtolower(trim($email));
    $email2 = strtolower(trim($email2));

    if (empty($username) OR empty($password) OR empty($password2) OR empty($email) OR empty($email2)) {
        $errormessage="error_register_blank";
    		include("modules/default/error.php");
    }

    if (strlen($username)<$username_length_min) {
        $errormessage="error_register_username_tooshort";
    		include("modules/default/error.php");
    }

    if (strlen($username)>$username_length_max) {
        $errormessage="error_register_username_toolong";
    		include("modules/default/error.php");
    }

    if (strlen($password)<$password_length_min) {
        $errormessage="error_register_password_tooshort";
    		include("modules/default/error.php");
    }

    if (strlen($password)>$password_length_max) {
        $errormessage="error_register_password_toolong";
    		include("modules/default/error.php");
    }

    if ($password!=$password2) {
        $errormessage="error_register_password_notmatch";
    		include("modules/default/error.php");
    }

    if (!validate_email($email)) {
        $errormessage="error_register_email_invalid";
    		include("modules/default/error.php");
    }

    if ($email!=$email2) {
        $errormessage="error_register_email_notmatch";
    		include("modules/default/error.php");
    }

    $checkuser = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."user WHERE username='$username'");
    if (!empty($checkuser)) {
        $errormessage="error_register_username_existed";
    		include("modules/default/error.php");
    }

    $checkemail = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."user WHERE email='$email'");
    if (!empty($checkemail)) {
        $errormessage="error_register_email_existed";
    		include("modules/default/error.php");
    }


    if ($require_activation) {
        $usergroupid = 5;// waitting for activation
    } else {
        $usergroupid = 3; // member
    }

    $passwordhash = md5($password);
    $homepage = $_POST[homepage];

    $DB->query("INSERT INTO ".$db_prefix."user (username,usergroupid,password,email,homepage,joindate,sex,address,qq,icq,msn,intro,tel,rememberpw,timezoneoffset,regip)
                       VALUES ('".addslashes($username)."','$usergroupid','$passwordhash','".addslashes($email)."','".addslashes(trim($homepage))."','".$timestamp."','".addslashes(htmlspecialchars($sex))."','".addslashes(htmlspecialchars(trim($address)))."','".addslashes(htmlspecialchars(trim($qq)))."','".addslashes(htmlspecialchars($icq))."','".addslashes(htmlspecialchars(trim($msn)))."','".addslashes(htmlspecialchars(trim($intro)))."','".addslashes(htmlspecialchars(trim($tel)))."','".addslashes($rememberpw)."','".addslashes($timezoneoffset)."','".$_SERVER['REMOTE_ADDR']."')");
    $pauserid = $DB->insert_id();

    if ($rememberpw==1) {
        setcookie("pauserid",$pauserid,$timestamp+3600*24*365);
        setcookie("papasswordhash",$passwordhash,$timestamp+3600*24*365);
    } else {
        setcookie("pauserid",$pauserid);
        setcookie("papasswordhash",$passwordhash);
    }
    if (empty($url) OR strstr($url,"index.php?mod=register")) {
        $url = "index.".HTMLEXT;
    }

    $activationcode = makeradompw();
    if ($require_activation) {
        $DB->query("INSERT INTO ".$db_prefix."useractivation (userid,time,activationcode) VALUES ('$pauserid','".$timestamp."','".addslashes($activationcode)."')");
        $mail_activation_subject = "
�������� $phparticletitle ��ע��Ļ�Ա�ʺ�
";
        $mail_activation_content = "
��л���� $phparticletitle ע��,ֻҪ�����²���,�����Լ������ע����ʺŲ���Ϊ��ʽ��Ա.

���������������ע�ἤ��.
$g_o_back2root/index.php?mod=register&action=activation&userid=$pauserid&activationcode=$activationcode
";
        $mail_recommendtofriend_mailcontent = "
���: $recipients
    $content
Thanks
-------------------------------------------------------------
$phparticletitle ($phparticleurl)
";
        $mailto = $email;
        $recipients = $username;
        mail($mailto,$mail_activation_subject,$mail_recommendtofriend_mailcontent,"From: $webmastermail\r\n");
    }
    if(file_exists("admin/loadsystem/passport_default.php"))
	{
		
include "admin/loadsystem/passport_default.php";
$userinfo = Array();
$userinfo['username'] = $username;
$userinfo['password'] = $passwordhash;
$userinfo['email'] = $email;
$userinfo['joindate'] = $timestamp;
$userinfo['msn'] = $msn;
$userinfo['regip'] = $_SERVER['REMOTE_ADDR'];
$userinfo['time'] = $timestamp;
$member = array();
foreach($membertable AS $pa=>$dz5)
{
	if($userinfo[$pa])
	$member[$dz5] = $userinfo[$pa];
}
$auth = passport_encrypt(passport_encode($member), $passportkey);
$verify = md5("login".$auth.$url.$passportkey);
header('Location: '.$bbsurl.'/api/passport.php?action=login&auth='.rawurlencode($auth).'&forward='.rawurlencode($url).'&verify='.rawurlencode($verify));
exit;
	}else
    if ($require_activation) {
        $information = "information_watingforactivation";
        include("modules/default/information.php");
    } else {
        $redirectmsg="redirect_register_success";
	include("modules/default/redirect.php");
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?
if (!isset($action) OR empty($action)) {

    $url = $_SERVER[HTTP_REFERER];
    if (empty($url)) {
        $url = getenv("HTTP_REFERER");
    }

    if ($pauserinfo[userid]!=0) {
        $errormessage="error_registered";
    		include("modules/default/error.php");
    }
    if ($allowregister!=1) {
        $errormessage="error_register_notallow";
    		include("modules/default/error.php");
    }

    ?><head>
<title><?=$phparticletitle?> - ע��Ϊ��Ա</title>
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

<!-- ͨ�����1 -->
<div class="mainline">&nbsp;</div>



<div class="maincolumn">
   	<form name="" method="post" action="index.php?mod=register">
		<div class="pagelist">
		<div class="pagelisttitleico">&nbsp;</div>
		<div class="pagelisttitlebg">
		<div class="pagelisttitlename">
<?=$phparticletitle?> ע������
		</div>
		</div>

		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
					<div class="enter"> 
<textarea cols="90" rows="25" name="textarea" wrap="VIRTUAL" readonly>��ӭע���Ϊ <?=$phparticletitle?> ��Ա��ע���ʾ����������������

ע��ǰ����ϸ�Ķ�����ֻ�������������������з���������ܼ������룺

1�����������ȷ�Ϻͽ���

��վ���������Ȩ������Ȩ�鱾վ���С����ṩ�ķ�����밴���䷢���Ĺ�˾�³̣���������Ͳ��������ϸ�ִ�С��û�ͨ�����ע����򲢵��һ�¡���ͬ�⡱�İ�ť�����ʾ�û��뱾վ���Э�鲢�������еķ������

2��������

��վ�����Լ��Ĳ���ϵͳͨ�����ʻ�������Ϊ�û��ṩ������񣬶����ַ�������ѵġ��û����룺

��1���ṩ�豸���������˵���һ̨�����ƽ����һ�����䱸����װ�á�

��2������������֧����˷����йصĵ绰���á�

���ǵ���վ��Ʒ�������Ҫ�ԣ��û�ͬ�⣺

��1���ṩ��ʱ���꾡��׼ȷ�ĸ������ϡ�

��2�����ϸ���ע�����ϣ����ϼ�ʱ���꾡׼ȷ��Ҫ������ԭʼ��������Ͻ�����Ϊע�����ϡ�

���⣬�û�����Ȩ��վ�������͸¶��ע�����ϣ�����վ���ܹ����û���������סַ��������ַ���������䡢�ʺš����ǣ���1���û�Ҫ��վ����Ȩĳ��ͨ�������ʼ�����͸¶��Щ��Ϣ��

��2����Ӧ�ķ��ɡ�����Ҫ�󼰳��������Ҫ��վ�ṩ�û��ĸ������ϡ�

����û��ṩ�����ϲ�׼ȷ������ʵ�����Ϸ���Ч����վ���������û�ʹ�ñ�վ��������Ȩ����

�û������ñ�վ��������ͬʱ��ͬ������ṩ�ĸ�����Ϣ����

3������������޸� 

��վ���ڱ�Ҫʱ�޸ķ��������վ��������һ�������䶯����˾�������û�������һ��ʹ��ǰ��ҳ����ʾ�޸����ݡ������ͬ��Ķ�������һ�μ����ͬ�⡱��ť������������ܣ���ʱȡ�������û�ʹ�÷����ʸ�

�û�Ҫ����ʹ�ñ�վ���������Ҫ�������ȷ�ϣ�

��1������ȷ�ϱ�վ���������䶯��

��2��ͬ��������еķ����������ơ�

4�������޶�

��վ������ʱ�޸Ļ��жϷ��������֪ͨ�û���Ȩ�����û����ܱ�վ��ʹ�޸Ļ��жϷ����Ȩ������վ������û������������

5���û���˽�ƶ� 

�����û�������˽�Ǳ�վ��һ��������ߡ����ԣ���Ϊ�����ϵڶ������ע�����Ϸ����Ĳ��䣬��վһ�����ṫ�����༭��͸¶�û���ע�����ϼ������ڱ�վ��������еķǹ������ݣ����Ǳ�վ�ڳ��ŵĻ�������Ϊ͸¶��Щ��Ϣ�����¼�������Ǳ�Ҫ�ģ�

��1�������йط��ɹ涨�������ڹ����йػ��ز�ѯʱ���ṩ�û��ڱ�վ����ҳ�Ϸ�������Ϣ���ݼ��䷢��ʱ�䡢��������ַ����������

��2����ӱ�վ��Ʒ�������

��3������ά����վ���̱�����Ȩ��

��4���ڽ�������½���ά���û����˺������ڵ���˽��ȫ��

��5�����ݵ�11���Ĺ涨���߱�վ��Ϊ��Ҫ����������¡��û��ڴ���Ȩ��վ��������������䷢����ҵ��Ϣ��

6���û����ʺš�����Ͱ�ȫ��

��һ��ע��ɹ���Ϊ�û��������õ�һ��������ʺš������δ���ܺ��Լ����ʺź��������������վ���������ɵ��𺦣�������ȫ�����Ρ����⣬ÿ���û���Ҫ�����ʻ��е����л���¼���ȫ��������ʱ�ı����������ͼ�꣬Ҳ���Խ����ɵ��ʻ��ؿ�һ�����ʻ����û�ͬ���������κηǷ�ʹ���û��ʺŻ�ȫ©�������������ͨ�汾վ��

7���ܾ��ṩ����

�û���ȷͬ������ʹ�����û����˳е����ա������ṩ�ǽ�������ѵĻ����ϡ���վ��ȷ��ʾ���ṩ�κ����͵ĵ�������������ȷ�Ļ������ģ����Ƕ���ҵ�Ե������������ض�Ŀ�ĺͲ�Υ���涨���ʵ��������⡣��վ����������һ���������û���Ҫ��Ҳ���������񲻻����жϣ��Է���ļ�ʱ�ԡ���ȫ�ԡ���������������������վ�ܾ��ṩ�κε�����������Ϣ�ܷ�׼ȷ����ʱ��˳���ش��͡��û���Ⲣ�������ػ�ͨ����վ��Ʒ����ȡ�õ��κ���Ϣ����ȡ�����û��Լ���������е�ϵͳ��������϶�ʧ�����з��պ����Ρ���վ���ڷ������ϵõ����κ���Ʒ���������׽��̣��������������û�����ӱ�վ�յ���ͷ��������������Ϣ����վҲ��������������ȷ������

8����������

��վ��ֱ�ӡ���ӡ�żȻ�����⼰������𺦲������Σ���Щ�����ԣ�������ʹ�ò�Ʒ���������Ϲ�����Ʒ�����Ʒ��������Ͻ��н��ף��Ƿ�ʹ�÷�����û����͵���Ϣ�����䶯����Щ�𺦻ᵼ�±�վ�����������Ա�վ������������𺦵Ŀ����ԡ�

9�����ṩ���ۺ���ҵ�Է���

�û�ʹ�ñ�վ��������Ȩ���Ǹ��˵ġ��û�ֻ����һ�������ĸ����������һ����˾��ʵ�����ҵ����֯���û���ŵ������վͬ�⣬�������ñ�վ�������������ۻ�������ҵ��;��

10����վ������̳��Ϣ�Ĵ��漰����

��վ�����û���������Ϣ��ɾ���򴢴�ʧ�ܸ��𡣱�վû�ж���Ϣ�Ĵ������涨���ޣ����������ж��û�����Ϊ�Ƿ���ϱ�վ������̳���������Ҫ��;���ı���Ȩ��������û�Υ���˷�������Ĺ涨�����ж���������̳������ʺš���������̳�����е����°�Ȩ��ԭ�����ߺͱ�վ��ͬ���У��κ�����Ҫת�����������£���������ԭ�����߻�վ��Ȩ��

11���û�����

�û������е��������ݵ����Ρ��û��Է����ʹ���Ǹ������������ڷ���ĵط����ɡ����ҷ��ɺ͹��ʷ��ɱ�׼�ġ��û���ŵ��

��1���ڱ�վ����ҳ�Ϸ�����Ϣ�������ñ�վ�ķ���ʱ��������й��йط���(���ַ��������¼)�������ڱ�վ����ҳ�ϻ������ñ�վ�ķ������������ơ�����������������Ϣ��

(a) �����ܷ���ȷ���Ļ���ԭ��ģ�

(b) Σ�����Ұ�ȫ��й¶�������ܣ��߸�������Ȩ���ƻ�����ͳһ�ģ�

(c) �𺦹�������������ģ�

(d) ɿ�������ޡ��������ӣ��ƻ������Ž�ģ�

(e) �ƻ������ڽ����ߣ�����а�̺ͷ⽨���ŵģ�

(f) ɢ��ҥ�ԣ�������������ƻ�����ȶ��ģ�

(g) ɢ�����ࡢɫ�顢�Ĳ�����������ɱ���ֲ����߽�������ģ�

(h) ������߷̰����ˣ��ֺ����˺Ϸ�Ȩ��ģ�

(i) ���з��ɡ����������ֹ���������ݵġ�

��2���ڱ�վ����ҳ�Ϸ�����Ϣ�������ñ�վ�ķ���ʱ��������������йع��Һ͵����ķ��ɹ涨�Լ����ʷ����йع涨��

��3�������ñ�վ�ķ���������»��

(a) δ����������������Ϣ�������ʹ�ü������Ϣ������Դ�ģ�

(b) δ�������Լ������Ϣ���繦�ܽ���ɾ�����޸Ļ������ӵģ�

(c) δ�������Խ���������Ϣ�����д洢��������ߴ�������ݺ�Ӧ�ó������ɾ�����޸Ļ������ӵģ�

(d) ��������������������������ƻ��Գ���ģ�

(e) ����Σ���������Ϣ���簲ȫ����Ϊ��

��4�������κη�ʽ���ű�վ�ķ���

��5�����ر�վ�����������涨�ͳ���

�û�����Լ���ʹ�ñ�վ��������е���Ϊ�е��������Ρ��û���⣬�����վ��������վ�������Ϣ���������϶ε�(1)����������֮һ�������й����ɣ���վ����������ֹͣ���䣬�����йؼ�¼��������йػ��ر��棬����ɾ�����и����ݵĵ�ַ��Ŀ¼��رշ��������û�ʹ�ñ�վ���ӹ�����񣬰������Ӳ����ơ����Ӱװ塢������̳�����������Һ����԰���Խ�����ʽΪ�����û��ṩ��Ϣ������������Ϊ��Ҳ�����ر����Ĺ涨�Լ���վ��ר�ŷ����ĵ��ӹ����������϶��������ķ��ɺ���ͷ�������ͬ�������ڵ��ӹ��������û���

���û�����Ϊ�����������ᵽ�ķ��������վ�����������ж�����ȡ���û������ʺš�

12������

�û�ͬ�Ᵽ�Ϻ�ά����վȫ���Ա�����棬����֧�����û�ʹ�ó�������Χ�������ʦ���ã�Υ������������𺦲������ã�������ʹ���û��ĵ��ԡ��ʺź�����֪ʶ��Ȩ��׷���ѡ�

13����������

�û���վ����ʱ����ʵ������жϷ��񡣱�վ������κθ��˻�������������ʱ�жϷ����û��������κη�������Ľ����Ժ����������޸������飬��Ա�վ���������û�ֻ�����µ�׷��Ȩ��

��1������ʹ�ñ�վ����

��2�������û�ʹ�ñ�վ������ʸ�

��3��ͨ�汾վֹͣ���û��ķ���

�����û�������û�ʹ�ñ�վ�����Ȩ��������ֹ������ʱ�𣬱�վ���ٶ��û��е��κ�����

14��ͨ��

���з����û���ͨ�涼��ͨ�������ʼ��򳣹���ż����͡���վ��ͨ���ʼ����񷢱���Ϣ���û����������Ƿ���������޸ġ�����������������Ҫ���顣

15��������߻�

�û��������Ƿ������Ϣ�м����������ϻ������߻����ڱ�վ������ѷ�����չʾ���ǵĲ�Ʒ���κ���������������������������������ҵ�����������������йص�������ֻ������Ӧ���û��͹��������֮�䷢������վ���е��κ����Σ���վû������Ϊ���������۸��κ�һ���ֵ����Ρ�

16���ʼ����ݵ�����Ȩ

�û���������ݰ��������֡��������������Ƭ��¼��ͼ���ڹ����ȫ�����ݣ���վ����ɳ������Ϊ�û��ṩ����ҵ��Ϣ��������Щ���ݾ��ܰ�Ȩ���̱ꡢ��ǩ�������Ʋ�����Ȩ���ɵı��������ԣ��û�ֻ���ڱ�վ�͹������Ȩ�²���ʹ����Щ���ݣ����������Ը��ơ�������Щ���ݡ������������йص�������Ʒ��

17������

�û��ͱ�վһ��ͬ���йر�Э���Լ�ʹ�ñ�վ�ķ�����������齻���ٲý�������Ǳ�վ��Ȩѡ���ȡ���Ϸ�ʽ������Ȩѡ����������ϵ��й�ϽȨ�ķ�Ժ�������κη��������뷨����ִ�������Щ����������ܽӽ��ķ������½����������������򱣳ֶ��û���������Ч����Ӱ�졣

����

ȫ����������᳣��ίԱ�����ά����������ȫ�ľ���

��������Ϣ�������취

���������ӹ���������涨

�л����񹲺͹��������Ϣ������������������й涨

�л����񹲺͹��������Ϣ������������������й涨ʵʩ�취

�л����񹲺͹��������Ϣϵͳ��ȫ��������

�������Ϣ�������������ȫ��������취



��ֻ�������������������з���������ܼ�������

</textarea>


					</div>

					</div>

		</div>
		<div class="tool">
		<input type="hidden" name="url" value="<?=$url?>">
           <input type="hidden" name="action" value="register">
           <input type="submit" class="buttot" name="agree" value=" ͬ�� ">
           <input type="submit" class="buttot" name="disagree" value="��ͬ��">
		</div>

		</div>
		</div>


	</form>

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
if ($_POST[action]=="register") {

    $templatelist ="register_agreement";

    if ($_POST[disagree]) {
        $redirectmsg="redirect_register_disaggree";
		include("modules/default/redirect.php");
    }
    if ($pauserinfo[userid]!=0) {
        $errormessage="error_registered";
    		include("modules/default/error.php");
    }

    if ($allowregister!=1) {
        show_errormessage("error_register_notallow");
    }
}
?>

<?
if ($_GET[action]=="activation") {

    $userid = intval($_GET[userid]);
    $DB->query("DELETE FROM ".$db_prefix."useractivation WHERE time<".($timestamp-60*60*24)."");

    $checkuseractivation = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."useractivation
                                                          WHERE userid='$userid' AND activationcode='".addslashes($_GET[activationcode])."'");
    if (empty($checkuseractivation)) {
        $errormessage="error_invalid_activationcode";
    		include("modules/default/error.php");
    }

    if ($timestamp>($checkuseractivation[time]+60*60*24)) {
        $DB->query("DELETE FROM ".$db_prefix."useractivation WHERE userid='$userid'");

        $errormessage="error_invalid_activationcode_expiry";
    		include("modules/default/error.php");
    } else {
        $DB->query("UPDATE ".$db_prefix."user SET usergroupid='3' WHERE userid='$userid'");
        $DB->query("DELETE FROM ".$db_prefix."useractivation WHERE userid='$userid'");

        //if (empty($url) OR eregi("index.php?mod=register",$url))
        {
            $url = "index.html";
        }
        $redirectmsg="redirect_user_actived";
				include("modules/default/redirect.php");
    }
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
<title><?=$phparticletitle?> - ע��Ϊ��Ա</title>
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

<!-- ͨ�����1 -->

<div class="mainline">&nbsp;</div>

<div class="maincolumn">
		<div class="pagelist">
		<div class="pagelisttitleico">&nbsp;</div>
		<div class="pagelisttitlebg">
		<div class="pagelisttitlename">
���λ�ã�<a href="<?=$homepage?>/" class="classlinkclass">��ҳ</a>&nbsp;>&nbsp;<a href="<?=$phparticleurl?>" class="classlinkclass"><?=$phparticletitle?></a>&nbsp;>&nbsp;ע���Ϊ��Ա
		</div>
		</div>

		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
  <form method="post" name="registerform" action="index.php?mod=register" onSubmit="return process_data(this)">
					<div class="title"> 
<b>����ѡ��</b>,��������������Ϣ,��<span class="reqasterisk">*</span>�ľ�Ϊ����ѡ��,���û�����,��������ѡ���Ϊ����

					</div>

  <fieldset>
                <div> 
                  <label><span class="reqasterisk">*</span>�û���:</label>
                    <input name="username" type="text" maxlength="15">
                    (<?=$username_length_min?>-<?=$username_length_max?> ���ַ�)
                </div>
                <div> 
                  <label> 
                    <span class="reqasterisk">*</span>Email��ַ:
                    </label>
                    <input name="email" type="text" maxlength="50" size="30" onChange="javascript:this.value=this.value.toLowerCase();">
					(50���ַ�����)
                </div>
                <div> 
                  <label><span class="reqasterisk">*</span>ȷ��Email��ַ</label>
                    <input name="email2" type="text" size="30" maxlength="50" onChange="javascript:this.value=this.value.toLowerCase();">
                </div>
                <div> 
                  <label><span class="reqasterisk">*</span>����:</label>
                    <input name="password" type="password" maxlength="15">
                    (<?=$password_length_min?>-<?=$password_length_max?> ���ַ�)
                </div>
                <div> 
                  <label><span class="reqasterisk">*</span>ȷ������</label>
                    <input name="password2" type="password" maxlength="15">
                </div>

  </fieldset>




					<div class="title"> 
<b>ѡ��ѡ��</b>
					</div>
<fieldset>
          <div>
            <label>�����ڵ�ʱ��: </label>
            <select name="timezoneoffset">

                <option value="-12">(GMT-12:00)�������п�,����ֵ�</option>
                <option value="-11">(GMT-11:00)��;��,��Ħ��Ⱥ��</option>
                <option value="-10">(GMT-10:00)������</option>
                <option value="-9">(GMT-09:00)����˹��</option>
                <option value="-8">(GMT-08:00)̫ƽ��ʱ��(�����ͼ��ô�)</option>
                <option value="-7">(GMT-07:00)ɽ��ʱ��(�����ͼ��ô�)</option>
                <option value="-6">(GMT-06:00)ī�����</option>
                <option value="-5">(GMT-05:00)�����,����</option>
                <option value="-4">(GMT-04:00)������ʱ��(���ô�)</option>
                <option value="-3.5">(GMT-03:30)Ŧ����</option>
                <option value="-3">(GMT-03:00)��������</option>
                <option value="-2">(GMT-02:00)�д�����</option>
                <option value="-1">(GMT-01:00)��ý�Ⱥ��</option>
                <option value="0">(GMT)��������ƽʱ:������</option>
                <option value="1">(GMT+01:00)��ķ˹�ص�</option>
                <option value="2">(GMT+02:00)������˹��</option>
                <option value="3">(GMT+03:00)�͸��</option>
                <option value="4">(GMT+04:00)��������</option>
                <option value="4.5">(GMT+04:30)������</option>
                <option value="5">(GMT+05:00)Ҷ�����ձ�</option>
                <option value="5.5">(GMT+05:30)�Ӷ�����</option>
                <option value="5.75">(GMT+05:45)�ӵ�����</option>
                <option value="6">(GMT+06:00)����ľͼ</option>
                <option value="7">(GMT+07:00)����</option>
                <option value="8" selected>(GMT+08)����ʱ��</option>
                <option value="9">(GMT+09:00)����</option>
                <option value="9.5">(GMT+09:30)��������</option>
                <option value="10">(GMT+10:00)����˹��</option>
                <option value="11">(GMT+11:00)��ӵ�</option>
                <option value="12">(GMT+12:00)��ʿ</option>
                <option value="13">(GMT+13:00)Ŭ�Ⱒ�巨</option>
              </select>
          </div>
                <div> 
                  <label>���˼��: </label>
                    <textarea cols="60" rows="5" name="intro"></textarea>
                </div>
                <div> 
                  <label>QQ����:</label>
                    <input type="text" name="qq" maxlength="15">
                </div>
                <div> 
                  <label>Icq����:</label>
                    <input type="text" name="icq" maxlength="15">
                </div>
                <div> 
                  <label>Msn����:</label>
                    <input type="text" name="msn" maxlength="15">
                </div>
                <div> 
                  <label>������ҳ:</label>
                    <input type="text" name="homepage" maxlength="100">
                </div>
                <div> 
                  <label>ͨѸ��ַ:</label>
                    <input type="text" name="address" size="50">
                </div>
                <div> 
                  <label>��ϵ�绰:</label>
                  <input type="text" name="tel" maxlength="15" size="18">
                </div>
                <div> 
                  <label>�Ա�:</label>
                    <select name="sex">
                      <option value="unknow" selected>����</option>
                      <option value="male">��</option>
                      <option value="female">Ů</option>
                    </select>
                </div>
                <div> 
                  <label>��ס����:</label>
                    <input type="checkbox" name="rememberpw" value="1" check checked><br>
                    ��ס�����,ÿ�η���ʱ�Ͳ����ظ���½. 
                </div>
</div>
                <div class="enter tool"> 
                    <script type="text/javascript">
<!--
function process_data(theform) {

                if (theform.username.value=="") {
                        alert("�������û���!");
                        theform.username.focus();
                        return false;
                }

                if (theform.email.value.indexOf("@") == -1) {
                        alert("��������ȷ�� Email ��ַ");
                        theform.email.focus();
                        return false;
                }
                if (theform.email.value!=theform.email2.value) {
                        alert("���������Email����ͬ,��ȷ�ϲ���������!");
                        theform.email.focus();
                        return false;
                }

                var password=theform.password.value;
                if (password.length<<?=$password_length_min?>) {
                        alert("�������������̫��,����ҲҪ���� <?=$password_length_min?> ���ַ�");
                        theform.password.value="";
                        theform.password2.value="";
                        theform.password.focus();
                        return false;
                }
                if (password.length><?=$password_length_max?>) {
                        alert("�������������̫��,���ֻ������ <?=$password_length_max?> ���ַ�");
                        theform.password.value="";
                        theform.password2.value="";
                        theform.password.focus();
                        return false;
                }


                if (theform.password.value=="") {
                        alert("�������½����!");
                        theform.password.focus();
                        return false;
                }
                if (theform.password.value!=theform.password2.value) {
                        alert("������������벻��ͬ,��ȷ�ϲ���������!");
                        theform.password.value="";
                        theform.password2.value="";
                        theform.password.focus();
                        return false;
                }
}

//-->
</script>
                    <input type="hidden" name="url" value="<?=$url?>">
                    <input type="hidden" name="action" value="insert">
                    <input type="submit" value="�ύ����" class="buttot" name="submit">
                    <input type="reset" value="���ñ�" class="buttot" name="reset">
                </div>
</fieldset>

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