<?php
/*
* rss文件
*/
// 加载前台常用函数
require "global.php";
// 数量限制
$limit = intval($_GET['limit']);
if(!$limit || $limit > 100) {
	$limit = 20;
}

$query_add = '';
if (!empty($_GET['sortid'])) {
	$sortid = intval($_GET['sortid']);
	$query_add = "AND sortid='".$sortid."'";
}
// 转换字符
function str_conver($content = "" ) {
	$content = str_replace("&amp;" , "&", $content);
	$content = str_replace("&quot;", "\"", $content);
	$content = str_replace("&#092;", "\\", $content);
	$content = str_replace("&#160;", "\r\n", $content);
	$content = str_replace("&#036;", "\$", $content);
	$content = str_replace("&#33;" , "!", $content);
	$content = str_replace("&#39;" , "'", $content);
	$content = str_replace("&lt;"  , "<", $content);
	$content = str_replace("&gt;"  , ">", $content);
	$content = str_replace("&#124;", '|', $content);
	$content = str_replace("&#58;" , ":", $content);
	$content = str_replace("&#91;" , "[", $content);
	$content = str_replace("&#93;" , "]", $content);
	$content = str_replace("&#064;", '@', $content);
	$content = str_replace("&#60;", '<', $content);
	$content = str_replace("&#62;", '>', $content);
	$content = str_replace("&nbsp;", ' ', $content);
	return $content;
}

$version = in_array($_GET['version'], array('2.0', '0.92')) ? $_GET['version'] : '0.92';
$version = str_replace(".", "", $version);
//================================
//		转换版本标头
//================================
switch(intval($version)) {
	case "20":
		header("Content-Type: text/xml");	
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		echo "<rss version=\"2.0\">\n";
		echo "\t<channel>\n";
		echo "\t\t<title>".str_conver($options['name'])."</title>\n";
		echo "\t\t<link>".$options['url']."</link>\n";
		echo "\t\t<copyright>Copyright (C) 2006 PHPARTICLE All Rights Reserved.</copyright>\n";
		echo "\t\t<generator>SEMIROCK</generator>\n";
	break;
	default:
		header("Content-Type: text/xml");	
		echo "<?xml version=\"1.0\" encoding=\"gb2312\"?>\n";
		echo "<rss version=\"0.92\">\n";
		echo "\t<channel>\n";
		echo "\t\t<title>".str_conver($configuration['phparticletitle'])."</title>\n";
		echo "\t\t<link>".$configuration['phparticleurl']."</link>\n";
		echo "\t\t<copyright>Copyright (C) 2006 phparticle All Rights Reserved.</copyright>\n";
		echo "\t\t<language>zh-CN</language>\n";
	break;
}

//================================
//			文章信息
//================================
$articles = $DB->query("SELECT articleid, title, description, sortid, date, author FROM ".$db_prefix."article WHERE visible='1' ".$query_add." ORDER BY articleid DESC LIMIT 0, ".$limit);

while ($article = $DB->fetch_array($articles)) {

	$sort = $DB->fetch_one_array("SELECT title FROM ".$db_prefix."sort WHERE sortid='$sortid'");

	$articlehtmllink =HTMLDIR."/".get_sortdirs($article['sortid']) . "/" . date("Y_m", $article['date']) . "/".rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;
	$sorthtmllink =HTMLDIR."/".get_sortdirs($article['sortid']);
	switch($version) {
		case "20":
			echo "\t\t<item>\n";
			echo "\t\t\t<guid>".$configuration['phparticleurl']."/".$articlehtmllink."</guid>\n";
			echo "\t\t\t<title>".str_conver(htmlspecialchars($article['title']))."</title>\n";
			echo "\t\t\t<author>".str_conver($article['author'])."</author>\n";
			echo "\t\t\t<description><![CDATA[".str_conver($article['description'])."]]></description>\n";
			echo "\t\t\t<link>".$configuration['phparticleurl']."/".$articlehtmllink."</link>\n";
			echo "\t\t\t<category domain=\"".$configuration['phparticleurl']."/".$sorthtmllink."\">".str_conver($sort['title'])."</category>\n";
			echo "\t\t\t<pubDate>".padate('Y-m-d H:i',$article['date'])."</pubDate>\n";
			echo "\t\t</item>\n";
			break;

		default:
			echo "\t\t<item>\n";
			echo "\t\t\t<title>".str_conver(htmlspecialchars($article['title']))."</title>\n";
			echo "\t\t\t<author>".str_conver($article['author'])."</author>\n";
			echo "\t\t\t<description><![CDATA[".str_conver($article['description'])."]]></description>\n";
			echo "\t\t\t<link>".$configuration['phparticleurl']."/".$articlehtmllink."</link>\n";
			echo "\t\t\t<pubDate>".padate('Y-m-d H:i',$article['date'])."</pubDate>\n";
			echo "\t\t</item>\n";
			break;
	}//end switch
}//end while
echo "\t</channel>\n";
echo " </rss>\n";

?>