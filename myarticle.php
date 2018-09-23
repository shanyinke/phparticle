<?php
error_reporting(7);

if (!isset($_POST[action]) AND trim($_POST[action]=="")) {
    $action = $_GET[action];
} else {
    $action = $_POST[action];
}

if (empty($action)) {
    $action = "view";
}

if ($action=="view") {

    $templatelist ="myarticle,usercp_navbar,navbar_myarticle";
    require "global.php";

    if ($pauserinfo[userid]==0) {
        include("modules/default/nopermission.php");
    }

    eval("\$navbit = \"".gettemplate('navbar_joiner')."\";");
    eval("\$navbit .= \"".gettemplate('navbar_myarticle')."\";");
    eval("\$navbar = \"".gettemplate('navbar')."\";");

    unset($bgcolor);
    $bgcolor[favorite] = "bgcolor=\"$style[firstalt]\"";
    eval("\$usercp_navbar = \"".gettemplate('usercp_navbar')."\";");

    $favorites = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."article
                                              WHERE userid='$pauserinfo[userid]'");

    $pauserinfo[favorites] = $favorites[count];
/*
    $space[used] = ceil(($pauserinfo[favorites]/$favoritelimit)*100);


    $space[left] = 100-$space[used];
    eval("\$status = \"".gettemplate('myarticle_spacestatus')."\";");
*/
    if ($pauserinfo[favorites]>0) {

        $articles = $DB->query("SELECT article.*,sort.sortid,sort.title AS sorttitle FROM ".$db_prefix."article AS article
                                         LEFT JOIN ".$db_prefix."sort AS sort
                                              USING (sortid)
                                         WHERE article.userid='$pauserinfo[userid]'
                                         ORDER BY date DESC");
        while ($article = $DB->fetch_array($articles)){
        	$articlehtmllink = HTMLDIR."/".get_sortdirs($article['sortid'])."/".date("Y_m",$article['date'])."/".rawurlencode(mkfilename($filenamemethod,$article['title'],1)).$article['articleid']."_1.".HTMLEXT;
        	$sorthtmllink = HTMLDIR."/".get_sortdirs($article['sortid'])."/".rawurlencode(mkfilename($filenamemethod,$article['sorttitle'],1)).$article['sortid']."_1.".HTMLEXT;
               $article[date] = padate("Y-m-d",$article[date]);
               $article[adddate] = padate("Y-m-d",$article[adddate]);
               eval("\$articlelistbit .= \"".gettemplate('myarticle_articlelistbit')."\";");
        }
        eval("\$articlelist = \"".gettemplate('myarticle_articlelist')."\";");

    } else {
        eval("\$articlelist = \"".gettemplate('myarticle_articlelist_none')."\";");
    }
    eval("dooutput(\"".gettemplate('myarticle')."\");");

}

if ($_GET[action]=="add") {

    $templatelist ="";
    require "global.php";

    if ($pauserinfo[userid]==0) {
        include("modules/default/nopermission.php");
    }

    $articleid = intval($_GET[articleid]);

    if (empty($articleid)) {
        $nav = $_SERVER["REQUEST_URI"];
        $script = $_SERVER["SCRIPT_NAME"];
        $nav = ereg_replace("^$script","",urldecode($nav));
        $vars = explode("/",$nav);
        $articleid = intval($vars[1]);
        if (!empty($vars[2])) {
            $pagenum = intval($vars[2]);
        }
    }

    $article = validate_articleid($articleid);

    $favorites = $DB->fetch_one_array("SELECT count(articleid) AS count FROM ".$db_prefix."favorite WHERE userid='$userid'");

    if ($favorites[count]>=$favoritelimit) {
	$errormessage="error_favorite_fulled";
	include("modules/default/error.php");
    }

    $checkexist = $DB->query("SELECT articleid FROM ".$db_prefix."favorite
                                               WHERE userid='$pauserinfo[userid]' AND articleid='$articleid'");
    if ($DB->num_rows($checkexist)==0) {
        $DB->query("INSERT INTO ".$db_prefix."favorite (userid,articleid,adddate)
                           VALUES ('$pauserinfo[userid]','$articleid','".time()."')");
    }
    $articles = $DB->query("SELECT articleid,sortid,title,date FROM ".$db_prefix."article WHERE articleid = '$articleid'");
    $article = $DB->fetch_array($articles);
    $pagenum = intval($_GET[pagenum]);
    if (empty($pagenum)) {
        $pagenum = 1;
    }
    $redirectfile = HTMLDIR."/".get_sortdirs($article['sortid'])."/".date("Y_m",$article['date'])."/".rawurlencode(mkfilename($filenamemethod,$article['title'],1)).$article['articleid']."_".$pagenum.".".HTMLEXT;
    redirect("$phparticleurl/$redirectfile","redirect_favorite_success");
}


if ($_POST[action]=="delete") {

    require "global.php";

    if ($pauserinfo[userid]==0) {
        include("modules/default/nopermission.php");
    }

    if (empty($_POST[article]) OR !is_array($_POST[article])) {
	$errormessage="error_favorite_unselect";
	include("modules/default/error.php");
    }
    foreach ($article AS $articleid=>$value) {
             $DB->query("DELETE FROM ".$db_prefix."article WHERE articleid='".intval($articleid)."' AND userid='$pauserinfo[userid]'");
             $DB->query("DELETE FROM ".$db_prefix."favorite WHERE articleid='".intval($articleid)."' AND userid='$pauserinfo[userid]'");
    }
	$url = "$phparticleurl/myarticle.php";
	$redirectmsg="redirect_favorite_deleted";
	include("modules/default/redirect.php");

}



?>