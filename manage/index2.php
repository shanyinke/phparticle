<?php
error_reporting(7);
if(!$_GET['mod']){
	exit;
}
chdir("../admin/");
if(!include "../modules/default/$_GET[mod].php"){
	echo "error:modules/default/$_GET[mod].php";exit;
}
?>