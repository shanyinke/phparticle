<?php
error_reporting ( 7 );

require "global.php";

if (! isset ( $_GET [action] )) {
	$_GET [action] = "frames";
}

if ($_GET [action] == "frames") {
	?>
<html>
<head>
<title>phpArticle 管理面版 Version  <?php echo $configuration[version]; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<frameset rows="24,*" frameborder="NO" border="0" framespacing="0"
	cols="*">
	<frame name="head" scrolling="NO" noresize src="index.php?action=top">
	<frameset cols="160,*" frameborder="NO" border="0" framespacing="0"
		rows="*">
		<frame name="toc" scrolling="YES" noresize src="menu.php">
		<frame name="content" src="index.php?action=main">
	</frameset>
</frameset>
<noframes>
	<body bgcolor="#FFFFFF" text="#000000">
	</body>
</noframes>
</html>
<?php
}

if ($_GET [action] == "top") {
	?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type"
	content="text/html; charset=&lt;?php echo $charset;?&gt;">
<style type="text/css">
<!--
a:link, a:active, a:visited {
	color: #FFFFFF;
	text-decoration: none;
	font-family: Tahoma, MS Shell Dlg, 宋体;
	font-size: 9pt;
}

a:hover {
	color: #FF9900;
}
-->
</style>
</head>

<body bgcolor="#FFFFFF" text="#000000" leftmargin="0" topmargin="0"
	marginwidth="0" marginheight="0">
	<table width="100%" border="0" cellspacing="0" cellpadding="4">
		<tr bgcolor="#505f94">
			<td width="30%"><a href="http://www.phparticle.xyz/" target="_blank">phpArticle <?php echo $configuration[version]; ?></a></td>
			<td align="center"><a href="http://www.phparticle.xyz/member"
				target="_blank">phpArticle 会员区</a></td>
			<td align="right" width="30%"><a href="../" target="_blank">转到首页</a></td>
		</tr>
	</table>
</body>
</html>

<?php
}

if ($_GET [action] == "main") {
	cpheader ();
	
	$usergroup = $DB->fetch_one_array ( "SELECT count(usergroupid) AS count FROM " . $db_prefix . "usergroup" );
	$user = $DB->fetch_one_array ( "SELECT count(userid) AS count FROM " . $db_prefix . "user" );
	
	$sort = $DB->fetch_one_array ( "SELECT count(sortid) AS count FROM " . $db_prefix . "sort" );
	$article = $DB->fetch_one_array ( "SELECT count(articleid) AS count FROM " . $db_prefix . "article" );
	$validate_article = $DB->fetch_one_array ( "SELECT count(articleid) AS count FROM " . $db_prefix . "article WHERE visible=0" );
	
	$article_datasize = 0;
	$tables = $DB->query ( "SHOW TABLE STATUS" );
	while ( $table = $DB->fetch_array ( $tables ) ) {
		$datasize += $table ['Data_length'];
		$indexsize += $table ['Index_length'];
		if ($table ['Name'] == $db_prefix . "articletext" or $table ['Name'] == $db_prefix . "article") {
			$article_datasize += $table ['Data_length'] + $table ['Index_length'];
		}
	}
	
	$onlineusers = $DB->query ( "SELECT * FROM " . $db_prefix . "session WHERE expiry>" . time () . " GROUP BY ipaddress" );
	$onlineuser = $DB->num_rows ( $onlineusers );
	
	if (function_exists ( 'ini_get' )) {
		$onoff = ini_get ( 'register_globals' );
	} else {
		$onoff = get_cfg_var ( 'register_globals' );
	}
	if ($onoff) {
		$onoff = "打开";
	} else {
		$onoff = "关闭";
	}
	if (function_exists ( 'ini_get' )) {
		$upload = ini_get ( 'file_uploads' );
	} else {
		$upload = get_cfg_var ( 'file_uploads' );
	}
	if ($upload) {
		$upload = "可以";
	} else {
		$upload = "不可以";
	}
	
	?>
<h3 align="center">Welcome to phpArticle</h3>

<table width="90%" border="0" cellspacing="0" cellpadding="0"
	align="center">
	<tr>
		<td>


			<table class="tableoutline" cellpadding="4" cellspacing="1"
				width="100%" border="0">
				<tr>
					<td height="20" class="tbhead" colspan="2">快速查找</td>
				</tr>
				<form method="get" action="user.php">
					<tr class="firstalt" nowrap>
						<td width="50%">查找会员:</td>
						<td><input type="text" name="username" size="35" maxlength="50"
							value=""> <input type="submit" value="提交" class="bginput"> <input
							type="hidden" name="action" value="dosearch"></td>
					</tr>
				</form>
				<form method="get" action="article.php">
					<tr class="secondalt" nowrap>
						<td>查找文章:</td>
						<td><input type="text" name="title" size="35" maxlength="50"
							value=""> <input type="submit" value="提交" class="bginput"> <input
							type="hidden" name="action" value="dosearch"></td>
					</tr>
				</form>
			</table> <br> <br>

			<table class="tableoutline" cellpadding="4" cellspacing="1"
				width="100%" border="0">
				<tr>
					<td height="20" class="tbhead" colspan="2">系统信息</td>
				</tr>
				<tr class="firstalt">
					<td width="50%"><font class="normalfont">服务器软件: <?php echo $_SERVER["SERVER_SOFTWARE"];?> </font></td>
					<td width="50%"><font class="normalfont">服务器系统: <?php echo defined('PHP_OS') ? PHP_OS : '未知';?></font></td>
				</tr>
				<tr class="secondalt">
					<td width="50%"><font class="normalfont">PHP 版本: <?php echo phpversion();?> </font></td>
					<td width="50%"><font class="normalfont">MySQL 版本: <?php echo mysql_get_server_info();?></font></td>
				</tr>
				<tr class="firstalt">
					<td width="50%"><font class="normalfont">register_globals: <?php echo $onoff;?></font></td>
					<td width="50%"><font class="normalfont">文件上传: <?php echo $upload; ?> </font></td>
				</tr>
				<tr class="secondalt">
					<td><font class="normalfont">服务器地址: <?php echo $_SERVER[SERVER_ADDR];?></font></td>
					<td width="50%"><font class="normalfont">服务器时区: <?php echo date("T",time()); ?> </font></td>
				</tr>
			</table> <br> <br>
			<table class="tableoutline" cellpadding="4" cellspacing="1"
				width="100%" border="0">
				<tr class="tbhead">
					<td colspan="2">数据统计</td>
				</tr>
				<tr class="secondalt">
					<td width="50%"><font class="normalfont">用户组总数: <?php echo $usergroup[count];?> </font></td>
					<td width="50%"><font class="normalfont">会员总数: <?php echo $user[count];?></font></td>
				</tr>
				<tr class="firstalt">
					<td width="50%"><font class="normalfont">分类总数: <?php echo $sort[count];?></font></td>
					<td width="50%"><font class="normalfont">文章总数: <?php echo $article[count];?> (侍审文章: <?php echo $validate_article[count];?>)</font></td>
				</tr>
				<tr class="secondalt">
					<td width="50%"><font class="normalfont">当前在线 : <?php echo $onlineuser;?></font></td>
					<td width="50%"><font class="normalfont">数据库大小: <?php echo get_real_size($datasize+$indexsize);?></font></td>
				</tr>
				<tr class="firstalt">
					<td colspan="2"><font class="normalfont">文章数据大小: <?php echo get_real_size($article_datasize);?></font></td>
				</tr>
			</table> <br> <br>
			<table class="tableoutline" cellpadding="4" cellspacing="1"
				width="100%" border="0">
				<tr class="tbhead">
					<td colspan="2">程序其它相关信息</td>
				</tr>
				<tr class="firstalt">
					<td width="50%"><font class="normalfont">静态官方主页: <a
							href="http://www.utspeed.com" target="_blank">http://www.phparticle.net</a></font></td>
					<td width="50%"><font class="normalfont">niuboy个人主页: <a
							href="http://www.utspeed.com" target="_blank">http://www.utspeed.com</a></font></td>
				</tr>
				<tr class="secondalt">
					<td width="50%"><font class="normalfont">程序开发: Hyeo ,niuboy</font></td>
					<td width="50%"><font class="normalfont">官方主页: <a
							href="http://www.phparticle.xyz" target="_blank">http://www.phparticle.xyz</a></font></td>
				</tr>
				<tr class="firstalt">
					<td width="50%"><font class="normalfont">Email: heizes@21cn.com</font></td>

					<td width="50%"><font class="normalfont">官方论坛: <a
							href="http://www.phparticle.xyz/forum" target="_blank">http://www.phparticle.xyz/forum</a></font></td>
				</tr>
				<tr class="secondalt">

				</tr>
			</table>
		</td>
	</tr>
</table>
<?php
	cpfooter ();
}
?>