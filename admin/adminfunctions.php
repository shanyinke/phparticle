<?php
error_reporting ( 7 );
session_start ();
function displaylogin() {
	global $configuration;
	cpheader ();
	// 检查安装文件是否存在
	if (file_exists ( "./install.php" )) {
		echo ("安全警告: 在使用后台之前, 请删除安装文件 install.php");
	}
	if (! file_exists ( "./configs/install.lock" )) {
		die ( "安全警告: install.lock 不存在, 系统已安装, 需要建立空文件admin/configs/install.lock" );
	}
	
	echo "<script language=\"JavaScript1.2\">\n";
	echo " if(self!=top){top.location=self.location;}\n";
	echo " function changing(){
    document.getElementById('checkpic').src=\"./class/checkcode.php?\"+Math.random();
} ";
	
	echo "</script>\n";
	echo "<br><br><br><br><br><form method='post' action='index.php'>";
	echo "<table width='250' border='0' align='center' cellpadding='4' cellspacing='1' class=tableoutline>";
	echo " <tr class=tbhead>";
	echo "  <td colspan='2' height=25>";
	echo "   <input type='hidden' name=action value=login>";
	echo "    <div align='center'><b>登 陆 入 口</b></div>";
	echo "  </td>";
	echo " </tr>";
	echo " <tr class=firstalt><td width='118'><font class='normalfont'>用户名:</font></td>";
	echo "  <td width='232'><font face='Verdana' size='2'><input class=text type='text' name='username' value=''></font></td></tr>";
	echo " <tr class=secondalt><td width='118'><font class='normalfont'>密码:</font></td>";
	echo "  <td width='232'><font class='normalfont'><input class=text type='password' name='password' value=''></font></td></tr>";
	echo " <tr class=secondalt><td width='118'><font class='normalfont'>验证码:</font></td>";
	echo "  <td width='232'><font class='normalfont'><input class=text type='verification' name='verification' value=''></font><img id=\"checkpic\" onclick=\"changing();\" src='./class/checkcode.php' /></td></tr>";
	echo "<tr class=tbhead><td colspan='2'><div align='center'><input class=bginput type='submit' value='登陆'> <input class=bginput type='reset' value='重置'>";
	echo "</div></td></tr></table></form>";
	cpfooter ();
	exit ();
}
function getuser_stat($user, $pw) {
	global $DB, $db_prefix, $pauserinfo;
	$user = htmlspecialchars ( trim ( $user ) );
	$user = trim ( $user );
	$pauserinfo = $DB->fetch_one_array ( "SELECT * FROM " . $db_prefix . "user AS user
                                     LEFT JOIN " . $db_prefix . "usergroup AS usergroup
                                       ON user.usergroupid=usergroup.usergroupid
                                     WHERE username='" . addslashes ( $user ) . "' AND password='" . addslashes ( $pw ) . "' AND isadmin=1" );
	if (empty ( $pauserinfo )) {
		return false;
	} else {
		return true;
	}
}
function getuser_stat2($user, $pw) {
	global $DB, $db_prefix, $pauserinfo;
	
	$user = htmlspecialchars ( trim ( $user ) );
	$user = trim ( $user );
	$pauserinfo = $DB->fetch_one_array ( "SELECT * FROM " . $db_prefix . "user AS user
                                                    LEFT JOIN " . $db_prefix . "usergroup AS usergroup
                                                         ON user.usergroupid=usergroup.usergroupid
                                                    WHERE user.username='" . addslashes ( $user ) . "'
                                                    AND user.password='$pw'
                                                    AND (isadmin=1 OR ismanager=1)" );
	if (empty ( $pauserinfo )) {
		return false;
	} else {
		return true;
	}
}
function getpermission() {
	global $DB, $db_prefix, $pauserinfo;
	
	if ($pauserinfo [isadmin] == 1) {
		
		$permission [canaddarticle] = 1;
		$permission [caneditarticle] = 1;
		$permission [canremovearticle] = 1;
		
		$permission [canaddnews] = 1;
		$permission [caneditnews] = 1;
		$permission [canremovenews] = 1;
		
		$permission [canaddsort] = 1;
		$permission [caneditsort] = 1;
		$permission [canremovesort] = 1;
	} else {
		$permission = $DB->fetch_one_array ( "SELECT usergroup.* FROM " . $db_prefix . "usergroup AS usergroup
                                                        LEFT JOIN " . $db_prefix . "user AS user
                                                          ON usergroup.usergroupid=user.usergroupid
                                                        WHERE userid='$pauserinfo[userid]'" );
	}
	return $permission;
}

if (CREATE_HTML_FILE != 1) {
	function show_nopermission() {
		global $pauserinfo;
		if ($pauserinfo [isadmin] != 1) {
			pa_exit ( "<b>你无权进入或管理该页面</b>" );
		}
	}
}
function cpheader($extraheader = "", $extraheader1 = "") {
	global $configuration, $nogzipoutput;
	// if (!$nogzipoutput) {
	// @ob_start("ob_gzhandler");
	// }
	echo "
<html>
<head>
<title> $configuration[phparticletitle] Powered By phpArticle $configuration[version] </title>
<meta content=\"text/html; charset=utf-8\" http-equiv=\"Content-Type\">
<meta http-equiv=\"Pragma\" content=\"no-cache\">
<meta http-equiv=\"Cache-Control\" content=\"no-cache\">
<meta http-equiv=\"Expires\" content=\"-1\">
<link rel=\"stylesheet\" href=\"../admin/cp.css\" type=\"text/css\">
" . $extraheader . "
</head>
<body leftmargin=\"10\" topmargin=\"10\" marginwidth=\"10\" marginheight=\"10\" " . $extraheader1 . ">\n";
}
function cpfooter() {
	global $showqueries, $DB, $configuration;
	echo "\n<br>\n<center>Powered by: <a href=\"http://www.phparticle.xyz\" target=\"_blank\">phpArticle</a> Version $configuration[version]</center>\n";
	echo "</body>\n</html>";
	exit ();
}
function getrowbg() {
	global $bgcounter;
	if ($bgcounter ++ % 2 == 0) {
		return "firstalt";
	} else {
		return "secondalt";
	}
}
function makenav($ctitle = "", $hidden = 0, $nav = array()) {
	global $nc; // nav counter
	
	if ($hidden == 1) {
		$display = "style=\"display: none\"";
		// $img = "plus";
		$img = "collapse";
	} else {
		// $img = "minus";
		$img = "expand";
	}
	echo "
  <tr class=tbnav style=\"cursor: hand\" onClick=\"ToggleNode(nav_tr_$nc,nav_img_$nc)\">
    <td><img id=\"nav_img_$nc\" src=../images/$img.gif align=absmiddle>$ctitle</td>
  </tr>
  <tr id=\"nav_tr_$nc\" $display>
    <td>
         <table width=\"100%\" cellpadding=\"0\" cellspacing=\"1\" border=\"0\">";
	foreach ( $nav as $title => $link ) {
		echo "
                     <tr class=firstalt onMouseOver=\"this.className='secondalt'\" onMouseOut=\"this.className='firstalt'\">
                        <td>
                        &nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$link\" target=content>$title</a>
                        </td>
                     </tr>";
	}
	echo "
         </table>
    </td>
  </tr>";
	$nc ++;
}

if (CREATE_HTML_FILE != 1) {
	function redirect($url, $text = "") {
		echo "<p>$text</p>";
		echo "<meta http-equiv=\"refresh\" content=\"1;URL=$url\">";
	}
} else {
	function redirect2($url, $text = "") {
		echo "<p>$text</p>";
		echo "<meta http-equiv=\"refresh\" content=\"1;URL=$url\">";
	}
}
function escape($text) {
	$text = str_replace ( "|P-@phpArticle@-A|", "|P-@php-Article@-A|", $text );
	$text = str_replace ( "|P-@style@-A|", "|P-@s-tyle@-A|", $text );
	$text = str_replace ( "|P-@replacement@-A|", "|P-@replace-ment@-A|", $text );
	return str_replace ( "|P-@template@-A|", "|P-@tem-plate@-A|", $text );
}

if (CREATE_HTML_FILE != 1) {
	unset ( $cachesorts );
	function cachesorts() {
		global $DB, $db_prefix, $parentsort;
		$sorts = $DB->query ( "SELECT * FROM " . $db_prefix . "sort ORDER BY displayorder,binary title,sortid ASC" );
		while ( $sort = $DB->fetch_array ( $sorts ) ) {
			$cachesorts [$sort [parentid]] [$sort [sortid]] = $sort;
			$parentsort [$sort [sortid]] [$sort [parentid]] = $sort [title];
		}
		$DB->free_result ( $sorts );
		return $cachesorts;
	}
}

if (CREATE_HTML_FILE != 1) {
	function subsorts($sortid) {
		global $DB, $db_prefix;
		$sorts = $DB->query ( "SELECT sortid FROM " . $db_prefix . "sort WHERE parentid='$sortid'" );
		if ($DB->num_rows ( $sorts ) > 0) {
			return true;
		} else {
			return false;
		}
	}
}
function subsortlev($sortid) {
	global $DB, $db_prefix, $lev;
	
	if (subsorts ( $sortid )) {
		$lev ++;
		$subsorts = $DB->query ( "SELECT sortid FROM " . $db_prefix . "sort WHERE parentid='$sortid'" );
		while ( $subsort = $DB->fetch_array ( $subsorts ) ) {
			subsortlev ( $subsort [sortid] );
		}
	}
	return $lev;
}

if (CREATE_HTML_FILE != 1) {
	function getsubsorts($sortid) {
		global $DB, $db_prefix, $cachesorts;
		// $sorts = $DB->query("SELECT sortid FROM ".$db_prefix."sort WHERE parentid='$sortid'");
		if (isset ( $cachesorts [$sortid] )) {
			foreach ( $cachesorts [$sortid] as $subsortid => $sort ) {
				$sortid .= "," . getsubsorts ( $subsortid );
			}
		}
		return $sortid;
	}
}

if (CREATE_HTML_FILE != 1) {
	function getparentsorts($sortid) {
		global $DB, $db_prefix;
		$sorts = $DB->query ( "SELECT parentid,title FROM " . $db_prefix . "sort WHERE sortid='$sortid'" );
		while ( $sort = $DB->fetch_array ( $sorts ) ) {
			$sortid .= ",";
			$sortid .= getparentsorts ( $sort [parentid] );
		}
		return $sortid;
	}
}
function get_real_size($size) {
	$kb = 1024; // Kilobyte
	$mb = 1024 * $kb; // Megabyte
	$gb = 1024 * $mb; // Gigabyte
	$tb = 1024 * $gb; // Terabyte
	
	if ($size < $kb) {
		return $size . " B";
	} else if ($size < $mb) {
		return round ( $size / $kb, 2 ) . " KB";
	} else if ($size < $gb) {
		return round ( $size / $mb, 2 ) . " MB";
	} else if ($size < $tb) {
		return round ( $size / $gb, 2 ) . " GB";
	} else {
		return round ( $size / $tb, 2 ) . " TB";
	}
}

if (CREATE_HTML_FILE != 1) {
	function validate_email($address) {
		if (ereg ( '^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+' . '@' . '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.' . '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$', $address, $email )) {
			return true;
		} else {
			return false;
		}
	}
}

if (CREATE_HTML_FILE != 1) {
	function stripslashes_array($array) {
		while ( list ( $k, $v ) = each ( $array ) ) {
			if (is_string ( $v )) {
				$array [$k] = stripslashes ( $v );
			} else if (is_array ( $v )) {
				$array [$k] = stripslashes_array ( $v );
			}
		}
		return $array;
	}
}
if (CREATE_HTML_FILE != 1) {
	function print_rr($array) {
		echo "<pre>";
		print_r ( $array );
		echo "</pre>";
	}
}
function pa_exit($text = "") {
	echo "<p>$text</p>";
	cpfooter ();
	exit ();
}

if (CREATE_HTML_FILE != 1) {
	function getip() {
		if (isset ( $_SERVER )) {
			if (isset ( $_SERVER [HTTP_X_FORWARDED_FOR] )) {
				$realip = $_SERVER [HTTP_X_FORWARDED_FOR];
			} elseif (isset ( $_SERVER [HTTP_CLIENT_IP] )) {
				$realip = $_SERVER [HTTP_CLIENT_IP];
			} else {
				$realip = $_SERVER [REMOTE_ADDR];
			}
		} else {
			if (getenv ( "HTTP_X_FORWARDED_FOR" )) {
				$realip = getenv ( "HTTP_X_FORWARDED_FOR" );
			} elseif (getenv ( "HTTP_CLIENT_IP" )) {
				$realip = getenv ( "HTTP_CLIENT_IP" );
			} else {
				$realip = getenv ( "REMOTE_ADDR" );
			}
		}
		return $realip;
	}
}

if (CREATE_HTML_FILE != 1) {
	function pa_isset($value) {
		if (isset ( $value ) and trim ( $value ) != "") {
			return true;
		} else {
			return false;
		}
	}
}
function loginlog($username = "", $password = "", $extra = "") {
	global $DB, $db_prefix;
	$extra .= "\nScript: http://" . getenv ( "HTTP_HOST" ) . getenv ( "REQUEST_URI" );
	$DB->query ( "INSERT INTO " . $db_prefix . "loginlog (username,password,date,ipaddress,extra) VALUES
                            ('" . addslashes ( $username ) . "','" . addslashes ( $password ) . "','" . time () . "','" . getip () . "','" . addslashes ( htmlspecialchars ( $extra ) ) . "')" );
}
function getlog() {
	global $DB, $db_prefix, $pauserinfo;
	if (pa_isset ( $_POST [action] )) {
		$action = $_POST [action];
	} elseif (pa_isset ( $_GET [action] )) {
		$action = $_GET [action];
	}
	if (pa_isset ( $action )) {
		$script = "http://" . getenv ( "HTTP_HOST" ) . getenv ( "REQUEST_URI" );
		$DB->query ( "INSERT INTO " . $db_prefix . "adminlog (userid,action,script,date,ipaddress) VALUES
                                ('$pauserinfo[userid]','" . addslashes ( htmlspecialchars ( trim ( $action ) ) ) . "','" . addslashes ( htmlspecialchars ( trim ( $script ) ) ) . "','" . time () . "','" . addslashes ( getip () ) . "')" );
	}
}
function cleancache() {
	global $DB, $db_prefix;
	$DB->query ( "DELETE FROM " . $db_prefix . "cache" );
}
function resetcache() {
	global $DB, $db_prefix;
	$DB->query ( "UPDATE " . $db_prefix . "cache SET expiry=1" );
}
function resettag($sortid = 0) {
	global $DB, $db_prefix;
	if ($sortid == 0)
		$DB->query ( "UPDATE " . $db_prefix . "tag SET renew=1 WHERE tagname='defaultsys'" );
	else {
		$sorts = $DB->fetch_one_array ( "SELECT parentlist FROM " . $db_prefix . "sort WHERE sortid='$sortid'" );
		$DB->query ( "UPDATE " . $db_prefix . "tag SET
	                                    renew=0
	                                    WHERE tagname='defaultsys' and sortid IN (" . $sorts ['parentlist'] . ",0)" );
	}
}
function write_replacement($replacementsetid) {
	global $DB, $db_prefix;
	$config_filename = "configs/replacement_$replacementsetid.php";
	$fp = fopen ( $config_filename, w );
	
	$contents = "<?php\n";
	
	$replacements = $DB->query ( "SELECT findword,replaceword FROM " . $db_prefix . "replacement
                                         WHERE replacementsetid='$replacementsetid'
                                         ORDER BY replacementid DESC" );
	
	while ( $replacement = $DB->fetch_array ( $replacements ) ) {
		$contents .= "\$style[$replacement[findword]] = \"" . addslashes ( $replacement [replaceword] ) . "\";\n";
		$custom [$replacement [findword]] = $replacement;
	}
	$replacements = $DB->query ( "SELECT findword,replaceword FROM " . $db_prefix . "replacement
                                         WHERE replacementsetid='-1'
                                         ORDER BY replacementid DESC" );
	while ( $replacement = $DB->fetch_array ( $replacements ) ) {
		if (! isset ( $custom [$replacement [findword]] )) {
			$contents .= "\$style[$replacement[findword]] = \"" . addslashes ( $replacement [replaceword] ) . "\";\n";
		}
	}
	
	$contents .= "?>";
	
	fwrite ( $fp, $contents );
	fclose ( $fp );
}
function set_defaultstyle($styleid) {
	global $DB, $db_prefix;
	$styleid = intval ( $styleid );
	
	$config_filename = "configs/style.php";
	$fp = fopen ( $config_filename, w );
	
	$contents = "<?php\n";
	$contents .= "\$styleid = $styleid;\n";
	$contents .= "?>";
	
	fwrite ( $fp, $contents );
	fclose ( $fp );
}
function getmansorts() {
	global $DB, $db_prefix, $pauserinfo;
	
	$man_sorts = $DB->query ( "SELECT * FROM " . $db_prefix . "manager WHERE userid='$pauserinfo[userid]'" );
	if ($DB->num_rows ( $man_sorts ) == 0 and ! $pauserinfo [isadmin]) {
		pa_exit ( "没有你管理的分类" );
	} else {
		while ( $man_sort = $DB->fetch_array ( $man_sorts ) ) {
			$sort_array [] = getsubsorts ( $man_sort [sortid] );
		}
	}
	
	if (! $pauserinfo [isadmin]) {
		if (! empty ( $sort_array )) {
			$sort_array = implode ( ",", $sort_array );
			// echo $sort_array;
		}
		
		unset ( $un_filter_array );
		unset ( $filter_array );
		
		$un_filter_array = array_flip ( explode ( ",", $sort_array ) );
	}
	
	return $un_filter_array;
}

if (CREATE_HTML_FILE != 1) {
	function padate($format, $timestamp) {
		global $configuration, $pauserinfo;
		return date ( $format, ($timestamp + ($pauserinfo [timezoneoffset] - $configuration [timezone]) * 3600) );
	}
}
function updateparentlist($sortid) {
	global $DB, $db_prefix;
	$DB->query ( "UPDATE " . $db_prefix . "sort SET
                                   parentlist='" . getparentsorts ( $sortid ) . "'
                                   WHERE sortid='$sortid'" );
}

if (CREATE_HTML_FILE != 1) {
	function validate_articleid($articleid) {
		global $DB, $db_prefix;
		$articleid = intval ( $articleid );
		$article = $DB->fetch_one_array ( "SELECT * FROM " . $db_prefix . "article WHERE articleid='$articleid'" );
		if (empty ( $article )) {
			pa_exit ( "该文章不存在" );
		}
		return $article;
	}
}

if (CREATE_HTML_FILE != 1) {
	function validate_commentid(&$commentid) {
		global $DB, $db_prefix;
		$commentid = intval ( $commentid );
		$comment = $DB->fetch_one_array ( "SELECT * FROM " . $db_prefix . "comment WHERE commentid='$commentid'" );
		if (empty ( $comment )) {
			pa_exit ( "该评论不存在" );
		} else {
			return $comment;
		}
	}
}
function updatecomments($articleid) {
	global $DB, $db_prefix;
	$comments = $DB->fetch_one_array ( "SELECT COUNT(*) AS total FROM " . $db_prefix . "comment WHERE articleid='$articleid'" );
	$DB->query ( "UPDATE " . $db_prefix . "article SET comments='$comments[total]'
                             WHERE articleid='$articleid'" );
}
function deletecomments($articleid) {
	global $DB, $db_prefix;
	
	if (empty ( $articleid )) {
		return;
	}
	$comments = $DB->query ( "SELECT * FROM " . $db_prefix . "comment WHERE articleid='" . intval ( $articleid ) . "'" );
	while ( $comment = $DB->fetch_array ( $comments ) ) {
		$comment_array [] = $comment [commentid];
	}
	if (! empty ( $comment_array ) and is_array ( $comment_array )) {
		$commentids = implode ( ",", $comment_array );
	}
	
	$messages = $DB->query ( "SELECT messageid FROM " . $db_prefix . "message WHERE commentid IN (0$commentids)" );
	while ( $message = $DB->fetch_array ( $messages ) ) {
		$message_array [] = $message [messageid];
	}
	if (empty ( $message_array ) or ! is_array ( $message_array ) or count ( $message_array ) == 0) {
		return;
	}
	$messageids = implode ( ",", $message_array );
	$DB->query ( "DELETE FROM " . $db_prefix . "message WHERE messageid IN (0$messageids)" );
	$DB->query ( "DELETE FROM " . $db_prefix . "comment WHERE commentid IN (0$commentids)" );
}
function deletecomments2($articleid = Array()) {
	global $DB, $db_prefix;
	
	if (empty ( $articleid )) {
		return;
	}
	$comments = $DB->query ( "SELECT * FROM " . $db_prefix . "comment WHERE articleid IN (" . join ( ',', $articleid ) . ")" );
	while ( $comment = $DB->fetch_array ( $comments ) ) {
		$comment_array [] = $comment [commentid];
	}
	if (! empty ( $comment_array ) and is_array ( $comment_array )) {
		$commentids = implode ( ",", $comment_array );
	}
	
	$messages = $DB->query ( "SELECT messageid FROM " . $db_prefix . "message WHERE commentid IN (0$commentids)" );
	while ( $message = $DB->fetch_array ( $messages ) ) {
		$message_array [] = $message [messageid];
	}
	if (empty ( $message_array ) or ! is_array ( $message_array ) or count ( $message_array ) == 0) {
		return;
	}
	$messageids = implode ( ",", $message_array );
	$DB->query ( "DELETE FROM " . $db_prefix . "message WHERE messageid IN (0$messageids)" );
	$DB->query ( "DELETE FROM " . $db_prefix . "comment WHERE commentid IN (0$commentids)" );
}
function getsubmessages($messageid) {
	global $DB, $db_prefix, $cachemessages;
	if (isset ( $cachemessages [$messageid] )) {
		foreach ( $cachemessages [$messageid] as $message ) {
			$messageid .= "," . getsubmessages ( $message [messageid] );
		}
	}
	return $messageid;
}

// ###################### Start globalize #######################
function globalize(&$var_array, $var_names) {
	global $_FILES;
	// takes variables from a $_REQUEST, $_POST style array
	// and makes them into global variables
	
	foreach ( $var_names as $varname => $type ) {
		if (is_numeric ( $varname )) // This handles the case where you send a variable in without giving its type, i..e. 'foo' => INT
{
			$varname = $type;
			$type = '';
		}
		if (isset ( $var_array ["$varname"] ) or $type == 'INT' or $type == 'FILE') {
			switch ($type) {
				// integer value - run intval() on data
				case 'INT' :
					$var_array ["$varname"] = intval ( $var_array ["$varname"] );
					break;
				
				// html-safe string - trim and htmlspecialchars data
				case 'STR_NOHTML' :
					$var_array ["$varname"] = htmlspecialchars_uni ( trim ( $var_array ["$varname"] ) );
					break;
				
				// string - trim data
				case 'STR' :
					$var_array ["$varname"] = trim ( $var_array ["$varname"] );
					break;
				
				// file - get data from $_FILES array
				case 'FILE' :
					if (isset ( $_FILES ["$varname"] )) {
						$var_array ["$varname"] = $_FILES ["$varname"];
					}
					break;
				
				// Do nothing, i.e. arrays, etc.
				default :
			}
			$GLOBALS ["$varname"] = &$var_array ["$varname"];
		}
	}
}

if (CREATE_HTML_FILE != 1) {
	function get_sortdirs($sortid = '-1') {
		global $DB, $db_prefix;
		$sorts = $DB->fetch_one_array ( "SELECT parentlist FROM " . $db_prefix . "sort WHERE sortid='$sortid'" );
		$sortbits = explode ( ',', $sorts ['parentlist'] );
		foreach ( $sortbits as $sortid ) {
			if ($sortid != '-1') {
				if ($sortdirs)
					$sortdirs = $sortid . "/" . $sortdirs;
				else
					$sortdirs = $sortid;
			}
		}
		return $sortdirs;
	}
	
	// 删除目录
	function deltree($pathdir) {
		if (is_file ( $pathdir ))
			unlink ( $pathdir );
		else if (is_empty_dir ( $pathdir )) // 如果是空的
{
			rmdir ( $pathdir ); // 直接删除
		} else { // 否则读这个目录，除了.和..外
			$d = dir ( $pathdir );
			while ( $a = $d->read () ) {
				if (is_file ( $pathdir . '/' . $a ) && ($a != '.') && ($a != '..')) {
					unlink ( $pathdir . '/' . $a );
				}
				// 如果是文件就直接删除
				if (is_dir ( $pathdir . '/' . $a ) && ($a != '.') && ($a != '..')) { // 如果是目录
					if (! is_empty_dir ( $pathdir . '/' . $a )) // 是否为空
{ // 如果不是，调用自身，不过是原来的路径+他下级的目录名
						deltree ( $pathdir . '/' . $a );
					}
					if (is_empty_dir ( $pathdir . '/' . $a )) { // 如果是空就直接删除
						rmdir ( $pathdir . '/' . $a );
					}
				}
			}
			$d->close ();
		}
	}
	function is_empty_dir($pathdir) { // 判断目录是否为空，我的方法不是很好吧？只是看除了.和..之外有其他东西不是为空，PHP有没有给出什么函数？
		$d = opendir ( $pathdir );
		$i = 0;
		while ( $a = readdir ( $d ) ) {
			$i ++;
		}
		closedir ( $d );
		if ($i > 2) {
			return false;
		} else
			return true;
	}
	$d = array (
			array (
					"A",
					- 20319 
			),
			array (
					"Ai",
					- 20317 
			),
			array (
					"An",
					- 20304 
			),
			array (
					"Ang",
					- 20295 
			),
			array (
					"Ao",
					- 20292 
			),
			array (
					"Ba",
					- 20283 
			),
			array (
					"Bai",
					- 20265 
			),
			array (
					"Ban",
					- 20257 
			),
			array (
					"Bang",
					- 20242 
			),
			array (
					"Bao",
					- 20230 
			),
			array (
					"Bei",
					- 20161 
			),
			array (
					"Ben",
					- 20036 
			),
			array (
					"Beng",
					- 20032 
			),
			array (
					"Bi",
					- 20026 
			),
			array (
					"Bian",
					- 20002 
			),
			array (
					"Biao",
					- 19990 
			),
			array (
					"Bie",
					- 19986 
			),
			array (
					"Bin",
					- 19982 
			),
			array (
					"Bing",
					- 19976 
			),
			array (
					"Bo",
					- 19805 
			),
			array (
					"Bu",
					- 19784 
			),
			array (
					"Ca",
					- 19775 
			),
			array (
					"Cai",
					- 19774 
			),
			array (
					"Can",
					- 19763 
			),
			array (
					"Cang",
					- 19756 
			),
			array (
					"Cao",
					- 19751 
			),
			array (
					"Ce",
					- 19746 
			),
			array (
					"Ceng",
					- 19741 
			),
			array (
					"Cha",
					- 19739 
			),
			array (
					"Chai",
					- 19728 
			),
			array (
					"Chan",
					- 19725 
			),
			array (
					"Chang",
					- 19715 
			),
			array (
					"Chao",
					- 19540 
			),
			array (
					"Che",
					- 19531 
			),
			array (
					"Chen",
					- 19525 
			),
			array (
					"Cheng",
					- 19515 
			),
			array (
					"Chi",
					- 19500 
			),
			array (
					"Chong",
					- 19484 
			),
			array (
					"Chou",
					- 19479 
			),
			array (
					"Chu",
					- 19467 
			),
			array (
					"Chuai",
					- 19289 
			),
			array (
					"Chuan",
					- 19288 
			),
			array (
					"Chuang",
					- 19281 
			),
			array (
					"Chui",
					- 19275 
			),
			array (
					"Chun",
					- 19270 
			),
			array (
					"Chuo",
					- 19263 
			),
			array (
					"Ci",
					- 19261 
			),
			array (
					"Cong",
					- 19249 
			),
			array (
					"Cou",
					- 19243 
			),
			array (
					"Cu",
					- 19242 
			),
			array (
					"Cuan",
					- 19238 
			),
			array (
					"Cui",
					- 19235 
			),
			array (
					"Cun",
					- 19227 
			),
			array (
					"Cuo",
					- 19224 
			),
			array (
					"Da",
					- 19218 
			),
			array (
					"Dai",
					- 19212 
			),
			array (
					"Dan",
					- 19038 
			),
			array (
					"Dang",
					- 19023 
			),
			array (
					"Dao",
					- 19018 
			),
			array (
					"De",
					- 19006 
			),
			array (
					"Deng",
					- 19003 
			),
			array (
					"Di",
					- 18996 
			),
			array (
					"Dian",
					- 18977 
			),
			array (
					"Diao",
					- 18961 
			),
			array (
					"Die",
					- 18952 
			),
			array (
					"Ding",
					- 18783 
			),
			array (
					"Diu",
					- 18774 
			),
			array (
					"Dong",
					- 18773 
			),
			array (
					"Dou",
					- 18763 
			),
			array (
					"Du",
					- 18756 
			),
			array (
					"Duan",
					- 18741 
			),
			array (
					"Dui",
					- 18735 
			),
			array (
					"Dun",
					- 18731 
			),
			array (
					"Duo",
					- 18722 
			),
			array (
					"E",
					- 18710 
			),
			array (
					"En",
					- 18697 
			),
			array (
					"Er",
					- 18696 
			),
			array (
					"Fa",
					- 18526 
			),
			array (
					"Fan",
					- 18518 
			),
			array (
					"Fang",
					- 18501 
			),
			array (
					"Fei",
					- 18490 
			),
			array (
					"Fen",
					- 18478 
			),
			array (
					"Feng",
					- 18463 
			),
			array (
					"Fo",
					- 18448 
			),
			array (
					"Fou",
					- 18447 
			),
			array (
					"Fu",
					- 18446 
			),
			array (
					"Ga",
					- 18239 
			),
			array (
					"Gai",
					- 18237 
			),
			array (
					"Gan",
					- 18231 
			),
			array (
					"Gang",
					- 18220 
			),
			array (
					"Gao",
					- 18211 
			),
			array (
					"Ge",
					- 18201 
			),
			array (
					"Gei",
					- 18184 
			),
			array (
					"Gen",
					- 18183 
			),
			array (
					"Geng",
					- 18181 
			),
			array (
					"Gong",
					- 18012 
			),
			array (
					"Gou",
					- 17997 
			),
			array (
					"Gu",
					- 17988 
			),
			array (
					"Gua",
					- 17970 
			),
			array (
					"Guai",
					- 17964 
			),
			array (
					"Guan",
					- 17961 
			),
			array (
					"Guang",
					- 17950 
			),
			array (
					"Gui",
					- 17947 
			),
			array (
					"Gun",
					- 17931 
			),
			array (
					"Guo",
					- 17928 
			),
			array (
					"Ha",
					- 17922 
			),
			array (
					"Hai",
					- 17759 
			),
			array (
					"Han",
					- 17752 
			),
			array (
					"Hang",
					- 17733 
			),
			array (
					"Hao",
					- 17730 
			),
			array (
					"He",
					- 17721 
			),
			array (
					"Hei",
					- 17703 
			),
			array (
					"Hen",
					- 17701 
			),
			array (
					"Heng",
					- 17697 
			),
			array (
					"Hong",
					- 17692 
			),
			array (
					"Hou",
					- 17683 
			),
			array (
					"Hu",
					- 17676 
			),
			array (
					"Hua",
					- 17496 
			),
			array (
					"Huai",
					- 17487 
			),
			array (
					"Huan",
					- 17482 
			),
			array (
					"Huang",
					- 17468 
			),
			array (
					"Hui",
					- 17454 
			),
			array (
					"Hun",
					- 17433 
			),
			array (
					"Huo",
					- 17427 
			),
			array (
					"Ji",
					- 17417 
			),
			array (
					"Jia",
					- 17202 
			),
			array (
					"Jian",
					- 17185 
			),
			array (
					"Jiang",
					- 16983 
			),
			array (
					"Jiao",
					- 16970 
			),
			array (
					"Jie",
					- 16942 
			),
			array (
					"Jin",
					- 16915 
			),
			array (
					"Jing",
					- 16733 
			),
			array (
					"Jiong",
					- 16708 
			),
			array (
					"Jiu",
					- 16706 
			),
			array (
					"Ju",
					- 16689 
			),
			array (
					"Juan",
					- 16664 
			),
			array (
					"Jue",
					- 16657 
			),
			array (
					"Jun",
					- 16647 
			),
			array (
					"Ka",
					- 16474 
			),
			array (
					"Kai",
					- 16470 
			),
			array (
					"Kan",
					- 16465 
			),
			array (
					"Kang",
					- 16459 
			),
			array (
					"Kao",
					- 16452 
			),
			array (
					"Ke",
					- 16448 
			),
			array (
					"Ken",
					- 16433 
			),
			array (
					"Keng",
					- 16429 
			),
			array (
					"Kong",
					- 16427 
			),
			array (
					"Kou",
					- 16423 
			),
			array (
					"Ku",
					- 16419 
			),
			array (
					"Kua",
					- 16412 
			),
			array (
					"Kuai",
					- 16407 
			),
			array (
					"Kuan",
					- 16403 
			),
			array (
					"Kuang",
					- 16401 
			),
			array (
					"Kui",
					- 16393 
			),
			array (
					"Kun",
					- 16220 
			),
			array (
					"Kuo",
					- 16216 
			),
			array (
					"La",
					- 16212 
			),
			array (
					"Lai",
					- 16205 
			),
			array (
					"Lan",
					- 16202 
			),
			array (
					"Lang",
					- 16187 
			),
			array (
					"Lao",
					- 16180 
			),
			array (
					"Le",
					- 16171 
			),
			array (
					"Lei",
					- 16169 
			),
			array (
					"Leng",
					- 16158 
			),
			array (
					"Li",
					- 16155 
			),
			array (
					"Lia",
					- 15959 
			),
			array (
					"Lian",
					- 15958 
			),
			array (
					"Liang",
					- 15944 
			),
			array (
					"Liao",
					- 15933 
			),
			array (
					"Lie",
					- 15920 
			),
			array (
					"Lin",
					- 15915 
			),
			array (
					"Ling",
					- 15903 
			),
			array (
					"Liu",
					- 15889 
			),
			array (
					"Long",
					- 15878 
			),
			array (
					"Lou",
					- 15707 
			),
			array (
					"Lu",
					- 15701 
			),
			array (
					"Lv",
					- 15681 
			),
			array (
					"Luan",
					- 15667 
			),
			array (
					"Lue",
					- 15661 
			),
			array (
					"Lun",
					- 15659 
			),
			array (
					"Luo",
					- 15652 
			),
			array (
					"Ma",
					- 15640 
			),
			array (
					"Mai",
					- 15631 
			),
			array (
					"Man",
					- 15625 
			),
			array (
					"Mang",
					- 15454 
			),
			array (
					"Mao",
					- 15448 
			),
			array (
					"Me",
					- 15436 
			),
			array (
					"Mei",
					- 15435 
			),
			array (
					"Men",
					- 15419 
			),
			array (
					"Meng",
					- 15416 
			),
			array (
					"Mi",
					- 15408 
			),
			array (
					"Mian",
					- 15394 
			),
			array (
					"Miao",
					- 15385 
			),
			array (
					"Mie",
					- 15377 
			),
			array (
					"Min",
					- 15375 
			),
			array (
					"Ming",
					- 15369 
			),
			array (
					"Miu",
					- 15363 
			),
			array (
					"Mo",
					- 15362 
			),
			array (
					"Mou",
					- 15183 
			),
			array (
					"Mu",
					- 15180 
			),
			array (
					"Na",
					- 15165 
			),
			array (
					"Nai",
					- 15158 
			),
			array (
					"Nan",
					- 15153 
			),
			array (
					"Nang",
					- 15150 
			),
			array (
					"Nao",
					- 15149 
			),
			array (
					"Ne",
					- 15144 
			),
			array (
					"Nei",
					- 15143 
			),
			array (
					"Nen",
					- 15141 
			),
			array (
					"Neng",
					- 15140 
			),
			array (
					"Ni",
					- 15139 
			),
			array (
					"Nian",
					- 15128 
			),
			array (
					"Niang",
					- 15121 
			),
			array (
					"Niao",
					- 15119 
			),
			array (
					"Nie",
					- 15117 
			),
			array (
					"Nin",
					- 15110 
			),
			array (
					"Ning",
					- 15109 
			),
			array (
					"Niu",
					- 14941 
			),
			array (
					"Nong",
					- 14937 
			),
			array (
					"Nu",
					- 14933 
			),
			array (
					"Nv",
					- 14930 
			),
			array (
					"Nuan",
					- 14929 
			),
			array (
					"Nue",
					- 14928 
			),
			array (
					"Nuo",
					- 14926 
			),
			array (
					"O",
					- 14922 
			),
			array (
					"Ou",
					- 14921 
			),
			array (
					"Pa",
					- 14914 
			),
			array (
					"Pai",
					- 14908 
			),
			array (
					"Pan",
					- 14902 
			),
			array (
					"Pang",
					- 14894 
			),
			array (
					"Pao",
					- 14889 
			),
			array (
					"Pei",
					- 14882 
			),
			array (
					"Pen",
					- 14873 
			),
			array (
					"Peng",
					- 14871 
			),
			array (
					"Pi",
					- 14857 
			),
			array (
					"Pian",
					- 14678 
			),
			array (
					"Piao",
					- 14674 
			),
			array (
					"Pie",
					- 14670 
			),
			array (
					"Pin",
					- 14668 
			),
			array (
					"Ping",
					- 14663 
			),
			array (
					"Po",
					- 14654 
			),
			array (
					"Pu",
					- 14645 
			),
			array (
					"Qi",
					- 14630 
			),
			array (
					"Qia",
					- 14594 
			),
			array (
					"Qian",
					- 14429 
			),
			array (
					"Qiang",
					- 14407 
			),
			array (
					"Qiao",
					- 14399 
			),
			array (
					"Qie",
					- 14384 
			),
			array (
					"Qin",
					- 14379 
			),
			array (
					"Qing",
					- 14368 
			),
			array (
					"Qiong",
					- 14355 
			),
			array (
					"Qiu",
					- 14353 
			),
			array (
					"Qu",
					- 14345 
			),
			array (
					"Quan",
					- 14170 
			),
			array (
					"Que",
					- 14159 
			),
			array (
					"Qun",
					- 14151 
			),
			array (
					"Ran",
					- 14149 
			),
			array (
					"Rang",
					- 14145 
			),
			array (
					"Rao",
					- 14140 
			),
			array (
					"Re",
					- 14137 
			),
			array (
					"Ren",
					- 14135 
			),
			array (
					"Reng",
					- 14125 
			),
			array (
					"Ri",
					- 14123 
			),
			array (
					"Rong",
					- 14122 
			),
			array (
					"Rou",
					- 14112 
			),
			array (
					"Ru",
					- 14109 
			),
			array (
					"Ruan",
					- 14099 
			),
			array (
					"Rui",
					- 14097 
			),
			array (
					"Run",
					- 14094 
			),
			array (
					"Ruo",
					- 14092 
			),
			array (
					"Sa",
					- 14090 
			),
			array (
					"Sai",
					- 14087 
			),
			array (
					"San",
					- 14083 
			),
			array (
					"Sang",
					- 13917 
			),
			array (
					"Sao",
					- 13914 
			),
			array (
					"Se",
					- 13910 
			),
			array (
					"Sen",
					- 13907 
			),
			array (
					"Seng",
					- 13906 
			),
			array (
					"Sha",
					- 13905 
			),
			array (
					"Shai",
					- 13896 
			),
			array (
					"Shan",
					- 13894 
			),
			array (
					"Shang",
					- 13878 
			),
			array (
					"Shao",
					- 13870 
			),
			array (
					"She",
					- 13859 
			),
			array (
					"Shen",
					- 13847 
			),
			array (
					"Sheng",
					- 13831 
			),
			array (
					"Shi",
					- 13658 
			),
			array (
					"Shou",
					- 13611 
			),
			array (
					"Shu",
					- 13601 
			),
			array (
					"Shua",
					- 13406 
			),
			array (
					"Shuai",
					- 13404 
			),
			array (
					"Shuan",
					- 13400 
			),
			array (
					"Shuang",
					- 13398 
			),
			array (
					"Shui",
					- 13395 
			),
			array (
					"Shun",
					- 13391 
			),
			array (
					"Shuo",
					- 13387 
			),
			array (
					"Si",
					- 13383 
			),
			array (
					"Song",
					- 13367 
			),
			array (
					"Sou",
					- 13359 
			),
			array (
					"Su",
					- 13356 
			),
			array (
					"Suan",
					- 13343 
			),
			array (
					"Sui",
					- 13340 
			),
			array (
					"Sun",
					- 13329 
			),
			array (
					"Suo",
					- 13326 
			),
			array (
					"Ta",
					- 13318 
			),
			array (
					"Tai",
					- 13147 
			),
			array (
					"Tan",
					- 13138 
			),
			array (
					"Tang",
					- 13120 
			),
			array (
					"Tao",
					- 13107 
			),
			array (
					"Te",
					- 13096 
			),
			array (
					"Teng",
					- 13095 
			),
			array (
					"Ti",
					- 13091 
			),
			array (
					"Tian",
					- 13076 
			),
			array (
					"Tiao",
					- 13068 
			),
			array (
					"Tie",
					- 13063 
			),
			array (
					"Ting",
					- 13060 
			),
			array (
					"Tong",
					- 12888 
			),
			array (
					"Tou",
					- 12875 
			),
			array (
					"Tu",
					- 12871 
			),
			array (
					"Tuan",
					- 12860 
			),
			array (
					"Tui",
					- 12858 
			),
			array (
					"Tun",
					- 12852 
			),
			array (
					"Tuo",
					- 12849 
			),
			array (
					"Wa",
					- 12838 
			),
			array (
					"Wai",
					- 12831 
			),
			array (
					"Wan",
					- 12829 
			),
			array (
					"Wang",
					- 12812 
			),
			array (
					"Wei",
					- 12802 
			),
			array (
					"Wen",
					- 12607 
			),
			array (
					"Weng",
					- 12597 
			),
			array (
					"Wo",
					- 12594 
			),
			array (
					"Wu",
					- 12585 
			),
			array (
					"Xi",
					- 12556 
			),
			array (
					"Xia",
					- 12359 
			),
			array (
					"Xian",
					- 12346 
			),
			array (
					"Xiang",
					- 12320 
			),
			array (
					"Xiao",
					- 12300 
			),
			array (
					"Xie",
					- 12120 
			),
			array (
					"Xin",
					- 12099 
			),
			array (
					"Xing",
					- 12089 
			),
			array (
					"Xiong",
					- 12074 
			),
			array (
					"Xiu",
					- 12067 
			),
			array (
					"Xu",
					- 12058 
			),
			array (
					"Xuan",
					- 12039 
			),
			array (
					"Xue",
					- 11867 
			),
			array (
					"Xun",
					- 11861 
			),
			array (
					"Ya",
					- 11847 
			),
			array (
					"Yan",
					- 11831 
			),
			array (
					"Yang",
					- 11798 
			),
			array (
					"Yao",
					- 11781 
			),
			array (
					"Ye",
					- 11604 
			),
			array (
					"Yi",
					- 11589 
			),
			array (
					"Yin",
					- 11536 
			),
			array (
					"Ying",
					- 11358 
			),
			array (
					"Yo",
					- 11340 
			),
			array (
					"Yong",
					- 11339 
			),
			array (
					"You",
					- 11324 
			),
			array (
					"Yu",
					- 11303 
			),
			array (
					"Yuan",
					- 11097 
			),
			array (
					"Yue",
					- 11077 
			),
			array (
					"Yun",
					- 11067 
			),
			array (
					"Za",
					- 11055 
			),
			array (
					"Zai",
					- 11052 
			),
			array (
					"Zan",
					- 11045 
			),
			array (
					"Zang",
					- 11041 
			),
			array (
					"Zao",
					- 11038 
			),
			array (
					"Ze",
					- 11024 
			),
			array (
					"Zei",
					- 11020 
			),
			array (
					"Zen",
					- 11019 
			),
			array (
					"Zeng",
					- 11018 
			),
			array (
					"Zha",
					- 11014 
			),
			array (
					"Zhai",
					- 10838 
			),
			array (
					"Zhan",
					- 10832 
			),
			array (
					"Zhang",
					- 10815 
			),
			array (
					"Zhao",
					- 10800 
			),
			array (
					"Zhe",
					- 10790 
			),
			array (
					"Zhen",
					- 10780 
			),
			array (
					"Zheng",
					- 10764 
			),
			array (
					"Zhi",
					- 10587 
			),
			array (
					"Zhong",
					- 10544 
			),
			array (
					"Zhou",
					- 10533 
			),
			array (
					"Zhu",
					- 10519 
			),
			array (
					"Zhua",
					- 10331 
			),
			array (
					"Zhuai",
					- 10329 
			),
			array (
					"Zhuan",
					- 10328 
			),
			array (
					"Zhuang",
					- 10322 
			),
			array (
					"Zhui",
					- 10315 
			),
			array (
					"Zhun",
					- 10309 
			),
			array (
					"Zhuo",
					- 10307 
			),
			array (
					"Zi",
					- 10296 
			),
			array (
					"Zong",
					- 10281 
			),
			array (
					"Zou",
					- 10274 
			),
			array (
					"Zu",
					- 10270 
			),
			array (
					"Zuan",
					- 10262 
			),
			array (
					"Zui",
					- 10260 
			),
			array (
					"Zun",
					- 10256 
			),
			array (
					"Zuo",
					- 10254 
			) 
	);
	function mkfilename($fm = 1, $title, $contenttype) {
		global $articleprefix, $sortprefix;
		switch ($fm) {
			case 1 :
				if ($contenttype == 1)
					$filename = $articleprefix;
				else
					$filename = $sortprefix;
				break;
			case 2 :
				$title = trim ( $title );
				if (! empty ( $title )) {
					$filename = preg_replace ( "/[^" . chr ( 0xa1 ) . "-" . chr ( 0xff ) . "0-9A-Za-z_-]+/", '-', $title );
				}
				break;
			case 3 :
				$title = trim ( $title );
				if (! empty ( $title )) {
					$filename = preg_replace ( "/[^" . chr ( 0xa1 ) . "-" . chr ( 0xff ) . "0-9A-Za-z_-]+/", '-', $title );
					$filename = c ( $filename );
				}
				break;
			default :
				break;
		}
		return $filename;
	}
	function g($num) {
		global $d;
		if ($num > 0 && $num < 160) {
			return chr ( $num );
		} elseif ($num < - 20319 || $num > - 10247) {
			return "";
		} else {
			for($i = count ( $d ) - 1; $i >= 0; $i --) {
				if ($d [$i] [1] <= $num)
					break;
			}
			return $d [$i] [0];
		}
	}
	function c($str) {
		$ret = "";
		for($i = 0; $i < strlen ( $str ); $i ++) {
			$p = ord ( substr ( $str, $i, 1 ) );
			if ($p > 160) {
				$q = ord ( substr ( $str, ++ $i, 1 ) );
				$p = $p * 256 + $q - 65536;
			}
			$ret .= g ( $p );
		}
		return $ret;
	}
}
?>