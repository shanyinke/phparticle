<?php
error_reporting ( 7 );

$charset = "utf-8";

require "./config.php";
require "./class/mysql.php";
require "./adminfunctions.php";
require "./class/pagenav.php";

$DB = new DB_MySQL ();

$DB->servername = $servername;
$DB->dbname = $dbname;
$DB->dbusername = $dbusername;
$DB->dbpassword = $dbpassword;
$DB->mysqlver = $mysqlver;
$DB->dbcharset = $dbcharset;
$DB->connect ();
$DB->selectdb ();

require "./class/forms.php";
$cpforms = new FORMS ();

// session_start();

require "./class/session.php";




//echo $_SESSION ["verification0"];
//echo $_SESSION ["verification"];

if (intval ( str_replace ( ".", "", phpversion () ) ) < 410) {
	cpheader ();
	pa_exit ( "PHP 的版本太低,本程序最低要求是 4.1.0 或以下的版本,当前服务器所安装的版本为 " . phpversion () );
}

if (get_magic_quotes_gpc ()) {
	
	$_GET = stripslashes_array ( $_GET );
	$_POST = stripslashes_array ( $_POST );
	$_COOKIE = stripslashes_array ( $_COOKIE );
}

set_magic_quotes_runtime ( 0 );

if (! ini_get ( "register_globals" )) {
	extract ( $_GET, EXTR_SKIP );
	extract ( $_POST, EXTR_SKIP );
}

require "configs/setting.php";
extract ( $configuration, EXTR_OVERWRITE );
define ( 'HTMLDIR', $htmldir );
define ( 'HTMLEXT', $htmlfileext );
$g_o_back2root = '.';
unset ( $debug );
unset ( $showqueries );

unset ( $pauserinfo );
if ($_POST [action] == "login") {
	if ($_SESSION ["verification"] == md5 ( $_POST ['verification'] )) {
		// echo $_SESSION ["verification"];
		if (getuser_stat ( $_POST [username], md5 ( $_POST [password] ) )) {
			$_SESSION [isadmin] = 1;
			$_SESSION [logined] = 1;
			
			$_SESSION = array_merge ( $_SESSION, $pauserinfo );
			
			cpheader ();
			
			redirect ( "./index.php", "登陆成功,请稍候......" );
			cpfooter ();
		} else {
			loginlog ( $_POST [username], $_POST [password], "Referer: " . getenv ( "HTTP_REFERER" ) );
			echo "ok1";
			// echo $_SESSION ["verification"];
			displaylogin ();
		}
	} else {
		loginlog ( $_POST [username], $_POST [password], "Referer: " . getenv ( "HTTP_REFERER" ) );
		echo "ok2";
		displaylogin ();
	}
}

if (! $_SESSION [isadmin] && (empty ( $_SESSION [ismanager] ) || ! in_array ( $_GET ['mod'], Array (
		"mkarticle",
		"mksort" 
) ))) {
	displaylogin ();
}


$pauserinfo = $_SESSION;
//print_r($pauserinfo);
getlog ();

?>