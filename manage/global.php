<?php
error_reporting(7);

//$debug=1;
//$showqueries =1;

require "../admin/config.php";
require "../admin/class/mysql.php";
require "../admin/adminfunctions.php";
require "../admin/class/pagenav.php";


$DB = new DB_MySQL;

$DB->servername=$servername;
$DB->dbname=$dbname;
$DB->dbusername=$dbusername;
$DB->dbpassword=$dbpassword;

$DB->connect();
$DB->selectdb();

require "../admin/class/forms.php";
$cpforms = new FORMS;


require "../admin/class/session.php";


if (intval(str_replace(".","",phpversion()))<410) {
    cpheader();
    pa_exit("PHP 的版本太低,本程序最低要求是 4.1.0 或以下的版本,当前服务器所安装的版本为 ".phpversion());
}

if (get_magic_quotes_gpc()) {

    $_GET = stripslashes_array($_GET);
    $_POST = stripslashes_array($_POST);
    $_COOKIE = stripslashes_array($_COOKIE);

}


set_magic_quotes_runtime(0);

if (!ini_get("register_globals")) {
    extract($_GET,EXTR_SKIP);
    extract($_POST,EXTR_SKIP);
}

require "../admin/configs/setting.php";
extract($configuration,EXTR_OVERWRITE);
define('HTMLDIR',$htmldir);
define('HTMLEXT',$htmlfileext);
unset($debug);
unset($showqueries);


unset($pauserinfo);
if ($_POST[action]=="login") {

    if (getuser_stat2($_POST[username],md5($_POST[password]))) {
        $_SESSION[ismanager] = 1;
        $_SESSION[logined] = 1;
        $_SESSION[pauserinfo] = $pauserinfo;

        cpheader();
        redirect("./index.php","登陆成功,请稍候......");
        cpfooter();
    } else {
        loginlog($_POST[username],$_POST[password],"Referer: ".getenv("HTTP_REFERER"));
        displaylogin();
    }

}


if (empty($_SESSION[isadmin]) AND empty($_SESSION[ismanager])) {
    displaylogin();
}
$pauserinfo = $_SESSION[pauserinfo];

//print_rr($pauserinfo);
getlog();

$permission = getpermission();

//print_rr(getallheaders());
//print_rr($permission);
?>