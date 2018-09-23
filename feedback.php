<?php
error_reporting(7);

$templatelist ="redirect_vote_success";

require "global.php";

$articleid = intval($_POST[articleid]);
$article = validate_articleid($articleid);

if (!$pauserinfo[canratearticle] AND !$pauserinfo[isadmin]) {//$pauserinfo[userid]==0
    include("modules/default/nopermission.php");
}

if($pauserinfo[userid]==0)
$rateverify="ip='$pauserinfo[ip]'";
else $rateverify="userid='$pauserinfo[userid]'";
$checkvote = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."articlerate
                                            WHERE articleid='$articleid' AND $rateverify");

if (!empty($checkvote)) {
	$errormessage="error_article_voted";
	include("modules/default/error.php");
}

$vote = intval($_POST[vote]);


if ($vote<1 OR $vote>10) {
	$errormessage="error_invalid_vote";
	include("modules/default/error.php");
}


$DB->query("UPDATE ".$db_prefix."article SET
                   totalscore=totalscore+'$vote',
                   voters=voters+1
                   WHERE articleid='$articleid'");
$DB->query("INSERT INTO ".$db_prefix."articlerate (articleid,userid,vote,date,reason,ip)
                   VALUES ('$articleid','$pauserinfo[userid]','$vote','".time()."','".addslashes(trim($_POST[reason]))."','".$pauserinfo['ip']."')");
if($pauserinfo[userid]!=0)
{
	$url = "$phparticleurl/index.php?mod=article&articleid=$articleid";
	$redirectmsg="redirect_vote_success";
	include("modules/default/redirect.php");
}else{
	$url = $_SERVER["HTTP_REFERER"];
	$redirectmsg="redirect_vote_success";
	include("modules/default/redirect.php");
}
?>