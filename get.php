<?php 
/*
phparticle 首页调用文件，不同的调用需要不同的get.php文件，四个分类就需要get1.php,get2.php,get4.php,get4.php四个文件！把调用文件上传到 phpArticle 的目录下. 在你需要显示文章的页面的适当的位置加上 
<script src=http://yoursite/phparticle/get.php></script>
代码:
*/
#=========================================== 
# 在不同的页面调用文章 
# 完成时间 2003-05-29 
# http://www.phparticle.cn <heizes@21cn.com> 
#=========================================== 
error_reporting(7); 
#=========================================== 
# 文章调用显示设置 start 
#=========================================== 
$limit = "10"; # 显示多少篇文章 
$sortids = "1"; # 显示哪些分类的文章,如果留空,即显示所有分类,如果要同时显示多个分类,请用 ',' 逗号分隔分类的id 
$ordertype = "date"; # 按什么排序? date: 日期, title 标题 
$order = "DESC"; # 排序的方向,ASC 或留空: 顺序, DESC :倒序 
$dateformat = "m-d"; # 日期显示格式 
$yoursite = "http://www.21view.com"; # 结束请不要加斜杆 "/" 

#=========================================== 
# 文章调用显示设置 end 
#=========================================== 

require "global.php";

$DB = new DB_MySQL; 

$DB->servername = $servername; 
$DB->dbname = $dbname;
$DB->dbusername = $dbusername; 
$DB->dbpassword = $dbpassword; 
$DB->mysqlver=$mysqlver;
$DB->dbcharset=$dbcharset;
$DB->connect(); 
$DB->selectdb(); 

unset($condition); 
if (trim($sortids)!="") { 
   $condition[] = " sortid IN (0$sortids) "; 
} 
$condition[] = "visible=1"; 

$conditions = implode(" AND ",$condition); 
$articles = $DB->query("SELECT * FROM ".$db_prefix."article AS article 
                                WHERE $conditions 
                                ORDER BY $ordertype $order 
                                LIMIT $limit"); 
echo "document.write('"; 
echo "<TABLE border=0 cellPadding=0 cellSpacing=0 width=268><TBODY>"; 
while ($article = $DB->fetch_array($articles)) { 
	$articlehtmllink = get_sortsubdirs($article['sortid'], $sortid) . "/" . date("Y_m", $article['date']) . "/".rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;
      if ($counter++%2==0) { 
          $bgcolor = $bgcolor1; 
      } else { 
          $bgcolor = $bgcolor2; 
      }
      echo "<tr height=28><td width=20>"; 
      echo "<img src=$yoursite/images/dot12.gif></td><td>"; 
      echo "<a href=$yoursite/$articlehtmllink>$article[title]</a>"; 
      echo " - ".date($dateformat,$article[date]); 
      echo "</td></tr><tr><td colspan=2 height=1 background=$yoursite/images/dot_line002.gif>"; 
      echo "</td></tr>"; 
} 
echo "</TBODY></TABLE>"; 
echo "');"; 
?>

