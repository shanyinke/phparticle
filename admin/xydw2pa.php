<?php
error_reporting(7);

require "global.php";
require "../modules/default/functions.php";

cpheader();
if ($_GET['action'] == "do") {
		$DB->query("UPDATE " . $db_prefix . "sort SET parentlist='-1' WHERE parentid=0 or parentid=-1");
		$parentlist=Array();
		while(true)
		{
			$flagout=1;
	        $result=$DB->query("SELECT sortid,parentid,parentlist FROM " . $db_prefix . "sort WHERE parentlist not like '%,-1'");
	        while($data=$DB->fetch_array($result))
	        {
	        	$flagout=0;
	        	$pl=$data['sortid'];
	        	if($data['parentid']!=-1&&$parentlist[$data['parentid']])
	        	{
	        		$pl=$data['parentlist'].",".$parentlist[$data['parentid']];
	        	}else if($data['parentid']==-1)
	        		$pl.=",".$data['parentid'];
	        	$pl=str_replace(",,",',',$pl);
	        	$DB->query("UPDATE " . $db_prefix . "sort SET parentlist='$pl' WHERE sortid=".$data['sortid']);
	        	$parentlist[$data['sortid']]=$pl;
	        }
	        if($flagout)break;
	    }
        echo("转换成功！");
}
cpfooter();

?>