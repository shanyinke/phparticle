<?php
error_reporting(7);

require "global.php";
if(!$_GET['mod']){
	$_GET['mod'] = 'index';
}else if(strpos($_GET['mod'],"/"))
{
	$_GET['mod'] = 'index';
}
if(!include "modules/default/$_GET[mod].php"){
	echo "error:data/modules/default/$_GET[mod].php";exit;
}
?>