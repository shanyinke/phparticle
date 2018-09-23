<?php
error_reporting(7);

if (!isset($_POST[action]) AND trim($_POST[action]=="")) {
    $action = $_GET[action];
} else {
    $action = $_POST[action];
}

if (empty($action)) {
    $action = "highlight";
}
require "global.php";
if(!$pauserinfo[cansetcolor] AND !$pauserinfo[isadmin])
{
	show_nopermission();
}
if ($action=="highlight") {

    $templatelist ="myarticle,usercp_navbar,navbar_myarticle,myarticle_highlight";

    if ($pauserinfo[userid]==0) {
        show_nopermission();
    }

    eval("dooutput(\"".gettemplate('myarticle_highlight')."\");");

}

if ($_GET[action]=="dohighlight") {


    if ($pauserinfo[userid]==0) {
        show_nopermission();
    }
	$highlight_style=$_POST['highlight_style'];
	$highlight_color=$_POST['highlight_color'];
	$stylebin = '';
	for($i = 1; $i <= 3; $i++) {
		$stylebin .= empty($highlight_style[$i]) ? '0' : '1';
	}

	$highlight_style = bindec($stylebin);
	$DB->query("UPDATE ".$db_prefix."article SET highlight='$highlight_style$highlight_color' WHERE articleid='".intval($_GET['aid'])."' AND userid='$pauserinfo[userid]'");
    redirect($_POST['referer'],"redirect_success");

}



?>