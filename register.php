<?php
require "global.php";
$gzipoutput = 0;
$timestamp = time ();
unset ( $action );
if (isset ( $_POST [action] ) and trim ( $_POST [action] ) != "") {
	$action = $_POST [action];
} elseif (isset ( $_GET [action] ) and trim ( $_GET [action] ) != "") {
	$action = $_GET [action];
}
?>
<?

if ($_POST [action] == "insert") {
	
	if ($pnuserinfo [userid] != 0) {
		$errormessage = "error_registered";
		include ("modules/default/error.php");
	}
	
	if ($allowregister != 1) {
		$errormessage = "error_register_notallow";
		include ("modules/default/error.php");
	}
	
	// if ($agree!="yes") {
	// show_errormessage("error_register_notagree");
	// }
	
	$username = htmlspecialchars ( trim ( $username ) );
	$password = trim ( $password );
	$password2 = trim ( $password2 );
	$email = strtolower ( trim ( $email ) );
	$email2 = strtolower ( trim ( $email2 ) );
	
	if (empty ( $username ) or empty ( $password ) or empty ( $password2 ) or empty ( $email ) or empty ( $email2 )) {
		$errormessage = "error_register_blank";
		include ("modules/default/error.php");
	}
	
	if (strlen ( $username ) < $username_length_min) {
		$errormessage = "error_register_username_tooshort";
		include ("modules/default/error.php");
	}
	
	if (strlen ( $username ) > $username_length_max) {
		$errormessage = "error_register_username_toolong";
		include ("modules/default/error.php");
	}
	
	if (strlen ( $password ) < $password_length_min) {
		$errormessage = "error_register_password_tooshort";
		include ("modules/default/error.php");
	}
	
	if (strlen ( $password ) > $password_length_max) {
		$errormessage = "error_register_password_toolong";
		include ("modules/default/error.php");
	}
	
	if ($password != $password2) {
		$errormessage = "error_register_password_notmatch";
		include ("modules/default/error.php");
	}
	
	if (! validate_email ( $email )) {
		$errormessage = "error_register_email_invalid";
		include ("modules/default/error.php");
	}
	
	if ($email != $email2) {
		$errormessage = "error_register_email_notmatch";
		include ("modules/default/error.php");
	}
	
	$checkuser = $DB->fetch_one_array ( "SELECT * FROM " . $db_prefix . "user WHERE username='$username'" );
	if (! empty ( $checkuser )) {
		$errormessage = "error_register_username_existed";
		include ("modules/default/error.php");
	}
	
	$checkemail = $DB->fetch_one_array ( "SELECT * FROM " . $db_prefix . "user WHERE email='$email'" );
	if (! empty ( $checkemail )) {
		$errormessage = "error_register_email_existed";
		include ("modules/default/error.php");
	}
	
	if ($require_activation) {
		$usergroupid = 5; // waitting for activation
	} else {
		$usergroupid = 3; // member
	}
	
	$passwordhash = md5 ( $password );
	$homepage = $_POST [homepage];
	
	$DB->query ( "INSERT INTO " . $db_prefix . "user (username,usergroupid,password,email,homepage,joindate,sex,address,qq,icq,msn,intro,tel,rememberpw,timezoneoffset,regip)
                       VALUES ('" . addslashes ( $username ) . "','$usergroupid','$passwordhash','" . addslashes ( $email ) . "','" . addslashes ( trim ( $homepage ) ) . "','" . $timestamp . "','" . addslashes ( htmlspecialchars ( $sex ) ) . "','" . addslashes ( htmlspecialchars ( trim ( $address ) ) ) . "','" . addslashes ( htmlspecialchars ( trim ( $qq ) ) ) . "','" . addslashes ( htmlspecialchars ( $icq ) ) . "','" . addslashes ( htmlspecialchars ( trim ( $msn ) ) ) . "','" . addslashes ( htmlspecialchars ( trim ( $intro ) ) ) . "','" . addslashes ( htmlspecialchars ( trim ( $tel ) ) ) . "','" . addslashes ( $rememberpw ) . "','" . addslashes ( $timezoneoffset ) . "','" . $_SERVER ['REMOTE_ADDR'] . "')" );
	$pauserid = $DB->insert_id ();
	
	if ($rememberpw == 1) {
		setcookie ( "pauserid", $pauserid, $timestamp + 3600 * 24 * 365 );
		setcookie ( "papasswordhash", $passwordhash, $timestamp + 3600 * 24 * 365 );
	} else {
		setcookie ( "pauserid", $pauserid );
		setcookie ( "papasswordhash", $passwordhash );
	}
	if (empty ( $url ) or strstr ( $url, "index.php?mod=register" )) {
		$url = "index." . HTMLEXT;
	}
	
	$activationcode = makeradompw ();
	if ($require_activation) {
		$DB->query ( "INSERT INTO " . $db_prefix . "useractivation (userid,time,activationcode) VALUES ('$pauserid','" . $timestamp . "','" . addslashes ( $activationcode ) . "')" );
		$mail_activation_subject = "
激活你在 $phparticletitle 所注册的会员帐号
";
		$mail_activation_content = "
感谢你在 $phparticletitle 注册,只要完以下步骤,即可以激活你刚注册的帐号并成为正式会员.

请点击下以连接完成注册激活.
$g_o_back2root/index.php?mod=register&action=activation&userid=$pauserid&activationcode=$activationcode
";
		$mail_recommendtofriend_mailcontent = "
你好: $recipients
    $content
Thanks
-------------------------------------------------------------
$phparticletitle ($phparticleurl)
";
		$mailto = $email;
		$recipients = $username;
		mail ( $mailto, $mail_activation_subject, $mail_recommendtofriend_mailcontent, "From: $webmastermail\r\n" );
	}
	if (file_exists ( "admin/loadsystem/passport_default.php" )) {
		
		include "admin/loadsystem/passport_default.php";
		$userinfo = Array ();
		$userinfo ['username'] = $username;
		$userinfo ['password'] = $passwordhash;
		$userinfo ['email'] = $email;
		$userinfo ['joindate'] = $timestamp;
		$userinfo ['msn'] = $msn;
		$userinfo ['regip'] = $_SERVER ['REMOTE_ADDR'];
		$userinfo ['time'] = $timestamp;
		$member = array ();
		foreach ( $membertable as $pa => $dz5 ) {
			if ($userinfo [$pa])
				$member [$dz5] = $userinfo [$pa];
		}
		$auth = passport_encrypt ( passport_encode ( $member ), $passportkey );
		$verify = md5 ( "login" . $auth . $url . $passportkey );
		header ( 'Location: ' . $bbsurl . '/api/passport.php?action=login&auth=' . rawurlencode ( $auth ) . '&forward=' . rawurlencode ( $url ) . '&verify=' . rawurlencode ( $verify ) );
		exit ();
	} else if ($require_activation) {
		$information = "information_watingforactivation";
		include ("modules/default/information.php");
	} else {
		$redirectmsg = "redirect_register_success";
		include ("modules/default/redirect.php");
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?
if (! isset ( $action ) or empty ( $action )) {
	
	$url = $_SERVER [HTTP_REFERER];
	if (empty ( $url )) {
		$url = getenv ( "HTTP_REFERER" );
	}
	
	if ($pauserinfo [userid] != 0) {
		$errormessage = "error_registered";
		include ("modules/default/error.php");
	}
	if ($allowregister != 1) {
		$errormessage = "error_register_notallow";
		include ("modules/default/error.php");
	}
	
	?><head>
<title><?=$phparticletitle?> - 注册为会员</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-CN" />
<link rel="icon" href="<?=$g_o_back2root?>/images/favicon.ico"
	type="image/x-icon" media="screen" />
<link rel="shortcut icon" href="<?$g_o_back2root?>/images/favicon.ico"
	type="image/x-icon" media="screen" />
<meta name="description"
	content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<meta name="keywords"
	content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<script src="<?=$g_o_back2root?>/images/js/chinahtml.js"
	type="text/javascript"></script>
<link href="<?=$g_o_back2root?>/images/css/css.css" rel="stylesheet"
	type="text/css" />
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
						<li><a
							href="<?=$g_o_back2root?>/index.php?mod=member&action=login">会员登陆</a></li>
						<li><a href="<?=$g_o_back2root?>/index.php?mod=register">免费注册</a></li>
						<li><a href="<?=$g_o_back2root?>/index.php?mod=search">高级搜索</a></li>
						<li><a
							href="<?=$g_o_back2root?>/index.php?mod=search&action=lastupdate">最后更新</a></li>
						<li><a href="<?=$g_o_back2root?>/index.php?mod=contribute">我要投稿</a></li>
						<li><a
							href="<?=$g_o_back2root?>/index.php?mod=member&action=logout">退出登陆</a></li>
					</ul>
				</div>
			</div>
		</div>
		<div id="maintop">
			<div id="Logo">
				<a href="/"><img src="<?=$g_o_back2root?>/images/palogo.gif"
					alt="首页" /></a>
			</div>
			<div id="TopAds">
				<a href="http://www.phparticle.xyz/" target="_blank"><img
					src="<?=$g_o_back2root?>/images/topbanner.jpg" width="468"
					height="60" alt="" /></a>
			</div>
			<div id="toprightmenu">
				<ul>
					<li><a href="<?=$phparticleurl?>"
						onClick="this.style.behavior='url(#default#homepage)';this.setHomePage('<?=$phparticleurl?>');return(false);"
						style="behavior: url(#default#homepage)">设为首页</a></li>
					<li><a href=javascript:window.external.AddFavorite(
						'<?=$phparticleurl?>','<?=$phparticletitle?>') title="希望您喜欢本站">加入收藏</a></li>
					<li><a href="mailto:semi.rock@gmail.com">联系我们</a></li>
				</ul>
			</div>
		</div>

		<div class="nav">

			<div class="nav-up-left">
				<a href="/" class="white">首页</a> <a
					href="<?=$g_o_back2root?>/html/2/" class="white">精品文章</a> <a
					href="<?=$g_o_back2root?>/cet/2/" class="white">使用帮助</a> <a
					href="<?=$g_o_back2root?>/bbshtmldata/1/13/" class="white">网站建设</a>
				<a href="<?=$g_o_back2root?>/bbshtmldata/4/6/" class="white">软件共享</a>
				<a href="<?=$g_o_back2root?>/bbshtmldata/1/12/" class="white">VC/C#/编成</a>
				<a href="<?=$g_o_back2root?>/bbshtmldata/1/9" class="white">UNIX/LINUX资料</a>
				<a href="<?=$g_o_back2root?>/bbs" class="white">支持论坛</a> <a
					href="http://www.utspeed.com" class="white">极速科技</a> <a
					href="http://proxygo.com.ru" class="white">代理猎狗</a> <a
					href="http://mp3.utspeed.com" class="white">音乐搜索</a>
			</div>

		</div>

		<div class="navline">&nbsp;</div>

		<div class="nav">
			<div class="nav-down-left">
				<a href="<?=$g_o_back2root?>/bbshtmldata/4/6/" class="white">休闲娱乐</a>
				<a href="http://music.utspeed.com" class="white">极速音乐</a> <a
					href="http://4tc.com.ru" class="white">极速网址</a> <a
					href="http://article.utspeed.com" class="white">幽默笑话</a> <a
					href="http://woman.utspeed.com" class="white">女性美容</a> <a
					href="http://nuskin.net.ru" class="white">如新网上购物商城</a> <a
					href="http://bt.utspeed.com" class="white">电驴/电骡/emule</a>
			</div>
			<div class="nav-down-right">
				<span>当前在线: <b><?=$onlineuser?></b></span>
			</div>
		</div>







	</div>

	<!-- 通栏广告1 -->
	<div class="mainline">&nbsp;</div>



	<div class="maincolumn">
		<form name="" method="post" action="index.php?mod=register">
			<div class="pagelist">
				<div class="pagelisttitleico">&nbsp;</div>
				<div class="pagelisttitlebg">
					<div class="pagelisttitlename">
<?=$phparticletitle?> 注册条款
		</div>
				</div>

				<div class="clear">&nbsp;</div>
				<div class="pagecontent">
					<div class="enter">
						<textarea cols="90" rows="25" name="textarea" wrap="VIRTUAL"
							readonly>欢迎注册成为 <?=$phparticletitle?> 会员，注册表示您必须接受以下条款：

注册前请仔细阅读，您只有无条件接受以下所有服务条款，才能继续申请：

1、服务条款的确认和接纳

本站服务的所有权和运作权归本站所有。所提供的服务必须按照其发布的公司章程，服务条款和操作规则严格执行。用户通过完成注册程序并点击一下“我同意”的按钮，这表示用户与本站达成协议并接受所有的服务条款。

2、服务简介

本站运用自己的操作系统通过国际互联网络为用户提供各项服务，而这种服务是免费的。用户必须：

（1）提供设备，包括个人电脑一台、调制解调器一个及配备上网装置。

（2）个人上网和支付与此服务有关的电话费用。

考虑到本站产品服务的重要性，用户同意：

（1）提供及时、详尽及准确的个人资料。

（2）不断更新注册资料，符合及时、详尽准确的要求。所有原始键入的资料将引用为注册资料。

另外，用户可授权本站向第三方透露其注册资料，否则本站不能公开用户的姓名、住址、出件地址、电子邮箱、帐号。除非：（1）用户要求本站或授权某人通过电子邮件服务透露这些信息。

（2）相应的法律、法规要求及程序服务需要本站提供用户的个人资料。

如果用户提供的资料不准确，不真实，不合法有效，本站保留结束用户使用本站各项服务的权利。

用户在享用本站各项服务的同时，同意接受提供的各类信息服务。

3、服务条款的修改 

本站会在必要时修改服务条款，本站服务条款一旦发生变动，公司将会在用户进入下一步使用前的页面提示修改内容。如果您同意改动，则再一次激活“我同意”按钮。如果您不接受，则及时取消您的用户使用服务资格。

用户要继续使用本站各项服务需要两方面的确认：

（1）首先确认本站服务条款及其变动。

（2）同意接受所有的服务条款限制。

4、服务修订

本站保留随时修改或中断服务而不需通知用户的权利。用户接受本站行使修改或中断服务的权利，本站不需对用户或第三方负责。

5、用户隐私制度 

尊重用户个人隐私是本站的一项基本政策。所以，作为对以上第二点个人注册资料分析的补充，本站一定不会公开、编辑或透露用户的注册资料及保存在本站各项服务中的非公开内容，除非本站在诚信的基础上认为透露这些信息在以下几种情况是必要的：

（1）遵守有关法律规定，包括在国家有关机关查询时，提供用户在本站的网页上发布的信息内容及其发布时间、互联网地址或者域名。

（2）遵从本站产品服务程序。

（3）保持维护本站的商标所有权。

（4）在紧急情况下竭力维护用户个人和社会大众的隐私安全。

（5）根据第11条的规定或者本站认为必要的其他情况下。用户在此授权本站可以向其电子邮箱发送商业信息。

6、用户的帐号、密码和安全性

您一旦注册成功成为用户，您将得到一个密码和帐号。如果您未保管好自己的帐号和密码而对您、本站或第三方造成的损害，您将负全部责任。另外，每个用户都要对其帐户中的所有活动和事件负全责。您可随时改变您的密码和图标，也可以结束旧的帐户重开一个新帐户。用户同意若发现任何非法使用用户帐号或安全漏洞的情况，立即通告本站。

7、拒绝提供担保

用户明确同意服务的使用由用户个人承担风险。服务提供是建立在免费的基础上。本站明确表示不提供任何类型的担保，不论是明确的或隐含的，但是对商业性的隐含担保，特定目的和不违反规定的适当担保除外。本站不担保服务一定能满足用户的要求，也不担保服务不会受中断，对服务的及时性、安全性、出错发生都不作担保。本站拒绝提供任何担保，包括信息能否准确、及时、顺利地传送。用户理解并接受下载或通过本站产品服务取得的任何信息资料取决于用户自己，并由其承担系统受损或资料丢失的所有风险和责任。本站对在服务网上得到的任何商品购物服务或交易进程，都不作担保。用户不会从本站收到口头或书面的意见或信息，本站也不会在这里作明确担保。

8、有限责任

本站对直接、间接、偶然、特殊及继起的损害不负责任，这些损害来自：不正当使用产品服务，在网上购买商品或类似服务，在网上进行交易，非法使用服务或用户传送的信息有所变动。这些损害会导致本站形象受损，所以本站早已提出这种损害的可能性。

9、不提供零售和商业性服务

用户使用本站各项服务的权利是个人的。用户只能是一个单独的个体而不能是一个公司或实体的商业性组织。用户承诺不经本站同意，不能利用本站各项服务进行销售或其他商业用途。

10、本站网友论坛信息的储存及限制

本站不对用户所发布信息的删除或储存失败负责。本站没有对信息的传输量规定上限，但是它有判定用户的行为是否符合本站网友论坛服务条款的要求和精神的保留权利，如果用户违背了服务条款的规定，则中断其网友论坛服务的帐号。本网友论坛内所有的文章版权归原文作者和本站共同所有，任何人需要转载社区内文章，必须征得原文作者或本站授权。

11、用户管理

用户单独承担发布内容的责任。用户对服务的使用是根据所有适用于服务的地方法律、国家法律和国际法律标准的。用户承诺：

（1）在本站的网页上发布信息或者利用本站的服务时必须符合中国有关法规(部分法规请见附录)，不得在本站的网页上或者利用本站的服务制作、复制、发布、传播以下信息：

(a) 反对宪法所确定的基本原则的；

(b) 危害国家安全，泄露国家秘密，颠覆国家政权，破坏国家统一的；

(c) 损害国家荣誉和利益的；

(d) 煽动民族仇恨、民族歧视，破坏民族团结的；

(e) 破坏国家宗教政策，宣扬邪教和封建迷信的；

(f) 散布谣言，扰乱社会秩序，破坏社会稳定的；

(g) 散布淫秽、色情、赌博、暴力、凶杀、恐怖或者教唆犯罪的；

(h) 侮辱或者诽谤他人，侵害他人合法权益的；

(i) 含有法律、行政法规禁止的其他内容的。

（2）在本站的网页上发布信息或者利用本站的服务时还必须符合其他有关国家和地区的法律规定以及国际法的有关规定。

（3）不利用本站的服务从事以下活动：

(a) 未经允许，进入计算机信息网络或者使用计算机信息网络资源的；

(b) 未经允许，对计算机信息网络功能进行删除、修改或者增加的；

(c) 未经允许，对进入计算机信息网络中存储、处理或者传输的数据和应用程序进行删除、修改或者增加的；

(d) 故意制作、传播计算机病毒等破坏性程序的；

(e) 其他危害计算机信息网络安全的行为。

（4）不以任何方式干扰本站的服务。

（5）遵守本站的所有其他规定和程序。

用户需对自己在使用本站服务过程中的行为承担法律责任。用户理解，如果本站发现其网站传输的信息明显属于上段第(1)条所列内容之一，依据中国法律，本站有义务立即停止传输，保存有关记录，向国家有关机关报告，并且删除含有该内容的地址、目录或关闭服务器。用户使用本站电子公告服务，包括电子布告牌、电子白板、电子论坛、网络聊天室和留言板等以交互形式为上网用户提供信息发布条件的行为，也须遵守本条的规定以及本站将专门发布的电子公告服务规则，上段中描述的法律后果和法律责任同样适用于电子公告服务的用户。

若用户的行为不符合以上提到的服务条款，本站将作出独立判断立即取消用户服务帐号。

12、保障

用户同意保障和维护本站全体成员的利益，负责支付由用户使用超出服务范围引起的律师费用，违反服务条款的损害补偿费用，其它人使用用户的电脑、帐号和其它知识产权的追索费。

13、结束服务

用户或本站可随时根据实际情况中断服务。本站不需对任何个人或第三方负责而随时中断服务。用户若反对任何服务条款的建议或对后来的条款修改有异议，或对本站服务不满，用户只有以下的追索权：

（1）不再使用本站服务。

（2）结束用户使用本站服务的资格。

（3）通告本站停止该用户的服务。

结束用户服务后，用户使用本站服务的权利马上中止。从那时起，本站不再对用户承担任何义务。

14、通告

所有发给用户的通告都可通过电子邮件或常规的信件传送。本站会通过邮件服务发报消息给用户，告诉他们服务条款的修改、服务变更、或其它重要事情。

15、参与广告策划

用户可在他们发表的信息中加入宣传资料或参与广告策划，在本站各项免费服务上展示他们的产品。任何这类促销方法，包括运输货物、付款、服务、商业条件、担保及与广告有关的描述都只是在相应的用户和广告销售商之间发生。本站不承担任何责任，本站没有义务为这类广告销售负任何一部分的责任。

16、邮件内容的所有权

用户定义的内容包括：文字、软件、声音、相片、录象、图表；在广告中全部内容；本站网友沙龙服务为用户提供的商业信息。所有这些内容均受版权、商标、标签和其它财产所有权法律的保护。所以，用户只能在本站和广告商授权下才能使用这些内容，而不能擅自复制、再造这些内容、或创造与内容有关的派生产品。

17、法律

用户和本站一致同意有关本协议以及使用本站的服务产生的争议交由仲裁解决，但是本站有权选择采取诉讼方式，并有权选择受理该诉讼的有管辖权的法院。若有任何服务条款与法律相抵触，那这些条款将按尽可能接近的方法重新解析，而其它条款则保持对用户产生法律效力和影响。

附：

全国人民代表大会常务委员会关于维护互联网安全的决定

互联网信息服务管理办法

互联网电子公告服务管理规定

中华人民共和国计算机信息网络国际联网管理暂行规定

中华人民共和国计算机信息网络国际联网管理暂行规定实施办法

中华人民共和国计算机信息系统安全保护条例

计算机信息网络国际联网安全保护管理办法



您只有无条件接受以上所有服务条款，才能继续申请

</textarea>


					</div>

				</div>

			</div>
			<div class="tool">
				<input type="hidden" name="url" value="<?=$url?>"> <input
					type="hidden" name="action" value="register"> <input type="submit"
						class="buttot" name="agree" value=" 同意 "> <input type="submit"
							class="buttot" name="disagree" value="不同意">
			
			</div>
	
	</div>
	</div>


	</form>

	</div>
	<div class="mainline">&nbsp;</div>
	<div id="footer">
		<div id="bottommenu">
			<a href="#">关于站点</a> - <a href="#">网站地图</a> - <a href="#top">返回顶部</a>
		</div>
		<div class="topline">&nbsp;</div>
		<div id="bottom">
			版权所有：<a href="<?=$phparticleurl?>"><?=$phparticletitle?></a> 2006
			未经授权禁止复制或建立镜像<br /> <span>Powered by: <a
				href="http://www.phparticle.xyz">phpArticle </a> Version <?=$version?>.</span><br />
		</div>
	</div>
	</div>
</body>
</html><?
	exit ();
}
?>

<?
if ($_POST [action] == "register") {
	
	$templatelist = "register_agreement";
	
	if ($_POST [disagree]) {
		$redirectmsg = "redirect_register_disaggree";
		include ("modules/default/redirect.php");
	}
	if ($pauserinfo [userid] != 0) {
		$errormessage = "error_registered";
		include ("modules/default/error.php");
	}
	
	if ($allowregister != 1) {
		show_errormessage ( "error_register_notallow" );
	}
}
?>

<?
if ($_GET [action] == "activation") {
	
	$userid = intval ( $_GET [userid] );
	$DB->query ( "DELETE FROM " . $db_prefix . "useractivation WHERE time<" . ($timestamp - 60 * 60 * 24) . "" );
	
	$checkuseractivation = $DB->fetch_one_array ( "SELECT * FROM " . $db_prefix . "useractivation
                                                          WHERE userid='$userid' AND activationcode='" . addslashes ( $_GET [activationcode] ) . "'" );
	if (empty ( $checkuseractivation )) {
		$errormessage = "error_invalid_activationcode";
		include ("modules/default/error.php");
	}
	
	if ($timestamp > ($checkuseractivation [time] + 60 * 60 * 24)) {
		$DB->query ( "DELETE FROM " . $db_prefix . "useractivation WHERE userid='$userid'" );
		
		$errormessage = "error_invalid_activationcode_expiry";
		include ("modules/default/error.php");
	} else {
		$DB->query ( "UPDATE " . $db_prefix . "user SET usergroupid='3' WHERE userid='$userid'" );
		$DB->query ( "DELETE FROM " . $db_prefix . "useractivation WHERE userid='$userid'" );
		
		// if (empty($url) OR eregi("index.php?mod=register",$url))
		{
			$url = "index.html";
		}
		$redirectmsg = "redirect_user_actived";
		include ("modules/default/redirect.php");
	}
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
<title><?=$phparticletitle?> - 注册为会员</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-CN" />
<link rel="icon" href="<?=$g_o_back2root?>/images/favicon.ico"
	type="image/x-icon" media="screen" />
<link rel="shortcut icon" href="<?$g_o_back2root?>/images/favicon.ico"
	type="image/x-icon" media="screen" />
<meta name="description"
	content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<meta name="keywords"
	content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<script src="<?=$g_o_back2root?>/images/js/chinahtml.js"
	type="text/javascript"></script>
<link href="<?=$g_o_back2root?>/images/css/css.css" rel="stylesheet"
	type="text/css" />
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
						<li><a
							href="<?=$g_o_back2root?>/index.php?mod=member&action=login">会员登陆</a></li>
						<li><a href="<?=$g_o_back2root?>/index.php?mod=register">免费注册</a></li>
						<li><a href="<?=$g_o_back2root?>/index.php?mod=search">高级搜索</a></li>
						<li><a
							href="<?=$g_o_back2root?>/index.php?mod=search&action=lastupdate">最后更新</a></li>
						<li><a href="<?=$g_o_back2root?>/index.php?mod=contribute">我要投稿</a></li>
						<li><a
							href="<?=$g_o_back2root?>/index.php?mod=member&action=logout">退出登陆</a></li>
					</ul>
				</div>
			</div>
		</div>
		<div id="maintop">
			<div id="Logo">
				<a href="/"><img src="<?=$g_o_back2root?>/images/palogo.gif"
					alt="首页" /></a>
			</div>
			<div id="TopAds">
				<a href="http://www.phparticle.xyz/" target="_blank"><img
					src="<?=$g_o_back2root?>/images/topbanner.jpg" width="468"
					height="60" alt="" /></a>
			</div>
			<div id="toprightmenu">
				<ul>
					<li><a href="<?=$phparticleurl?>"
						onClick="this.style.behavior='url(#default#homepage)';this.setHomePage('<?=$phparticleurl?>');return(false);"
						style="behavior: url(#default#homepage)">设为首页</a></li>
					<li><a href=javascript:window.external.AddFavorite(
						'<?=$phparticleurl?>','<?=$phparticletitle?>') title="希望您喜欢本站">加入收藏</a></li>
					<li><a href="mailto:semi.rock@gmail.com">联系我们</a></li>
				</ul>
			</div>
		</div>

		<div class="nav">

			<div class="nav-up-left">
				<a href="/" class="white">首页</a>
			</div>

		</div>

		<div class="navline">&nbsp;</div>

		<div class="nav">
			<div class="nav-down-left">
				<a href="<?=$g_o_back2root?>/bbshtmldata/4/6/" class="white">休闲娱乐</a>
				
			</div>
			<div class="nav-down-right">
				<span>当前在线: <b><?=$onlineuser?></b></span>
			</div>
		</div>







	</div>

	<!-- 通栏广告1 -->

	<div class="mainline">&nbsp;</div>

	<div class="maincolumn">
		<div class="pagelist">
			<div class="pagelisttitleico">&nbsp;</div>
			<div class="pagelisttitlebg">
				<div class="pagelisttitlename">
					你的位置：<a href="<?=$homepage?>/" class="classlinkclass">首页</a>&nbsp;>&nbsp;<a
						href="<?=$phparticleurl?>" class="classlinkclass"><?=$phparticletitle?></a>&nbsp;>&nbsp;注册成为会员
				</div>
			</div>

			<div class="clear">&nbsp;</div>
			<div class="pagecontent">
				<form method="post" name="registerform"
					action="index.php?mod=register"
					onSubmit="return process_data(this)">
					<div class="title">
						<b>必填选项</b>,请输入完整的信息,带<span class="reqasterisk">*</span>的均为必填选项,除用户名外,其它所有选项均为保密

					</div>

					<fieldset>
						<div>
							<label><span class="reqasterisk">*</span>用户名:</label> <input
								name="username" type="text" maxlength="15">
                    (<?=$username_length_min?>-<?=$username_length_max?> 个字符)
                
						
						</div>
						<div>
							<label> <span class="reqasterisk">*</span>Email地址:
							</label> <input name="email" type="text" maxlength="50" size="30"
								onChange="javascript:this.value=this.value.toLowerCase();">
								(50个字符以内) 
						
						</div>
						<div>
							<label><span class="reqasterisk">*</span>确认Email地址</label> <input
								name="email2" type="text" size="30" maxlength="50"
								onChange="javascript:this.value=this.value.toLowerCase();">
						
						</div>
						<div>
							<label><span class="reqasterisk">*</span>密码:</label> <input
								name="password" type="password" maxlength="15">
                    (<?=$password_length_min?>-<?=$password_length_max?> 个字符)
                
						
						</div>
						<div>
							<label><span class="reqasterisk">*</span>确认密码</label> <input
								name="password2" type="password" maxlength="15">
						
						</div>

					</fieldset>




					<div class="title">
						<b>选填选项</b>
					</div>
					<fieldset>
						<div>
							<label>你所在的时区: </label> <select name="timezoneoffset">

								<option value="-12">(GMT-12:00)埃尼威托克,夸贾林岛</option>
								<option value="-11">(GMT-11:00)中途岛,萨摩亚群岛</option>
								<option value="-10">(GMT-10:00)夏威夷</option>
								<option value="-9">(GMT-09:00)阿拉斯加</option>
								<option value="-8">(GMT-08:00)太平洋时间(美国和加拿大)</option>
								<option value="-7">(GMT-07:00)山地时间(美国和加拿大)</option>
								<option value="-6">(GMT-06:00)墨西哥城</option>
								<option value="-5">(GMT-05:00)波哥大,利马</option>
								<option value="-4">(GMT-04:00)大西洋时间(加拿大)</option>
								<option value="-3.5">(GMT-03:30)纽芬兰</option>
								<option value="-3">(GMT-03:00)巴西利亚</option>
								<option value="-2">(GMT-02:00)中大西洋</option>
								<option value="-1">(GMT-01:00)佛得角群岛</option>
								<option value="0">(GMT)格林威治平时:都柏林</option>
								<option value="1">(GMT+01:00)阿姆斯特丹</option>
								<option value="2">(GMT+02:00)布嘉勒斯特</option>
								<option value="3">(GMT+03:00)巴格达</option>
								<option value="4">(GMT+04:00)阿布扎比</option>
								<option value="4.5">(GMT+04:30)喀布尔</option>
								<option value="5">(GMT+05:00)叶卡捷琳堡</option>
								<option value="5.5">(GMT+05:30)加尔各答</option>
								<option value="5.75">(GMT+05:45)加德满都</option>
								<option value="6">(GMT+06:00)阿拉木图</option>
								<option value="7">(GMT+07:00)曼谷</option>
								<option value="8" selected>(GMT+08)北京时间</option>
								<option value="9">(GMT+09:00)东京</option>
								<option value="9.5">(GMT+09:30)阿德莱德</option>
								<option value="10">(GMT+10:00)布里斯班</option>
								<option value="11">(GMT+11:00)马加丹</option>
								<option value="12">(GMT+12:00)富士</option>
								<option value="13">(GMT+13:00)努库阿洛法</option>
							</select>
						</div>
						<div>
							<label>个人简介: </label>
							<textarea cols="60" rows="5" name="intro"></textarea>
						</div>
						<div>
							<label>QQ号码:</label> <input type="text" name="qq" maxlength="15">
						
						</div>
						<div>
							<label>Icq号码:</label> <input type="text" name="icq"
								maxlength="15">
						
						</div>
						<div>
							<label>Msn号码:</label> <input type="text" name="msn"
								maxlength="15">
						
						</div>
						<div>
							<label>个人主页:</label> <input type="text" name="homepage"
								maxlength="100">
						
						</div>
						<div>
							<label>通迅地址:</label> <input type="text" name="address" size="50">
						
						</div>
						<div>
							<label>联系电话:</label> <input type="text" name="tel" maxlength="15"
								size="18">
						
						</div>
						<div>
							<label>性别:</label> <select name="sex">
								<option value="unknow" selected>保密</option>
								<option value="male">男</option>
								<option value="female">女</option>
							</select>
						</div>
						<div>
							<label>记住密码:</label> <input type="checkbox" name="rememberpw"
								value="1" check checked><br> 记住密码后,每次访问时就不必重复登陆. 
						
						</div>
			
			</div>
			<div class="enter tool">
				<script language="javascript">
<!--
function process_data(theform) {

                if (theform.username.value=="") {
                        alert("请输入用户名!");
                        theform.username.focus();
                        return false;
                }

                if (theform.email.value.indexOf("@") == -1) {
                        alert("请输入正确的 Email 地址");
                        theform.email.focus();
                        return false;
                }
                if (theform.email.value!=theform.email2.value) {
                        alert("两次输入的Email不相同,请确认并重新输入!");
                        theform.email.focus();
                        return false;
                }

                var password=theform.password.value;
                if (password.length<<?=$password_length_min?>) {
                        alert("你所输入的密码太短,至少也要输入 <?=$password_length_min?> 个字符");
                        theform.password.value="";
                        theform.password2.value="";
                        theform.password.focus();
                        return false;
                }
                if (password.length><?=$password_length_max?>) {
                        alert("你所输入的密码太长,最多只能输入 <?=$password_length_max?> 个字符");
                        theform.password.value="";
                        theform.password2.value="";
                        theform.password.focus();
                        return false;
                }


                if (theform.password.value=="") {
                        alert("请输入登陆密码!");
                        theform.password.focus();
                        return false;
                }
                if (theform.password.value!=theform.password2.value) {
                        alert("两次输入的密码不相同,请确认并重新输入!");
                        theform.password.value="";
                        theform.password2.value="";
                        theform.password.focus();
                        return false;
                }
}

//-->
</script>
				<input type="hidden" name="url" value="<?=$url?>"> <input
					type="hidden" name="action" value="insert"> <input type="submit"
						value="提交申请" class="buttot" name="submit"> <input type="reset"
							value="重置表单" class="buttot" name="reset">
			
			</div>
			</fieldset>

			</form>


		</div>

	</div>

	</div>

	<div class="mainline">&nbsp;</div>
	<div id="footer">
		<div id="bottommenu">
			<a href="#">关于站点</a> - <a href="#">广告服务</a> - <a href="#">联系我们</a> -
			<a href="#">版权隐私</a> - <a href="#">免责声明</a> - <a
				href="http://utspeed.com">合作伙伴</a> - <a href="http://phparticle.net"
				target="_blank">程序支持</a> - <a href="#">网站地图</a> - <a href="#top">返回顶部</a>
		</div>
		<div class="topline">&nbsp;</div>
		<div id="bottom">
			版权所有：<a href="<?=$phparticleurl?>"><?=$phparticletitle?></a> 2006
			未经授权禁止复制或建立镜像<br /> <span>Powered by: <a
				href="http://www.phparticle.xyz">phpArticle </a> Version <?=$version?>.</span><br />
		</div>
	</div>
	</div>
</body>
</html>