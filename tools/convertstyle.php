<?
if(!$_GET['inputfile'])
{
	?>
	<form action="convertstyle.php" method="get">
	原风格文件:(<font color=red>请拷贝到相同目录内.</font>)
	<input name="inputfile" type="text" value="phpArticle.style">
	<input type="submit" name="Submit" value="提交">
	</form>
	<?
	exit;
}else
$modify_Table = Array(
	1=>Array(
		'src'=>'article.php/$relatedarticle[articleid]',
		'dst'=>'$relevanthtmllink'
	),
	2=>Array(
		'src'=>'article.php?articleid=$article[articleid]',
		'dst'=>'$articlehtmllink'
	),
	3=>Array(
		'src'=>'article.php/$article[articleid]',
		'dst'=>'$articlehtmllink'
	),
	4=>Array(
		'src'=>'comment.php/$articleid?action=add',
		'dst'=>'comment.php?articleid=$articleid&action=add'
	),
	5=>Array(
		'src'=>'article.php/$articleid',
		'dst'=>'$articlehtmllink'
	),
	6=>Array(
		'src'=>'sort.php/$article[sortid]',
		'dst'=>'$sorthtmllink'
	),
	7=>Array(
		'src'=>'sort.php/$sort[sortid]',
		'dst'=>'$sorthtmllink'
	),
	8=>Array(
		'src'=>'sort.php/$sortid',
		'dst'=>'$sorthtmllink'
	),
	9=>Array(
		'src'=>'sort.php?sortid=$childsort[sortid]',
		'dst'=>'$childsorthtmllink'
	),
	10=>Array(
		'src'=>'article.php/$articleid/$nextpagenum',
		'dst'=>'$nextarticlehtmllink'
	),
	11=>Array(
		'src'=>'sort.php/$childsort[sortid]',
		'dst'=>'$childsorthtmllink'
	),
	12=>Array(
		'src'=>'index.php',
		'dst'=>'index.html'
	),
	13=>Array(
		'src'=>'value="$page"',
		'dst'=>'value="$articlehtmllink"'
	),
	14=>Array(
		'src'=>"article.php/$articleid/'+",
		'dst'=>"'+"
	),
	15=>Array(
		'src'=>'Version $version</title>',
		'dst'=>'HTML 特别版 程序修改：www.utspeed.com</title>'
	),
	16=>Array(
		'src'=>'comment.php/$articleid?action=view',
		'dst'=>'comment.php?articleid=$articleid&action=view'
	),
	17=>Array(
		'src'=>'comment.php/$articleid?action=view',
		'dst'=>'comment.php?articleid=$articleid&action=view'
	),
	18=>Array(
		'src'=>'favorite.php/$articleid/$pagenum?action=add',
		'dst'=>'favorite.php?articleid=$articleid&pagenum=$pagenum&action=add'
	),
	19=>Array(
		'src'=>'recommend.php/$articleid/$pagenum',
		'dst'=>'recommend.php?articleid=$articleid&pagenum=$pagenum'
	),
	20=>Array(
		'src'=>'print.php/$articleid',
		'dst'=>'print.php?articleid=$articleid'
	),
	21=>Array(
		'src'=>'$articlehtmllink/$nextpagenum',
		'dst'=>'$nextarticlehtmllink'
	),
	22=>Array(
		'src'=>'$articlehtmllink/'."'+",
		'dst'=>"'+"
	),
	23=>Array(
		'src'=>'Version $version </span>',
		'dst'=>'HTML 特别版 程序修改：<a href="www.utspeed.com">极速科技</a></title>'
	),
	24=>Array(
		'src'=>'$articlehtmllink'."'+",
		'dst'=>"'+"
	),
	25=>Array(
		'src'=>'article_$article[articleid].html',
		'dst'=>'$articlehtmllink'
	),
	26=>Array(
		'src'=>'article_$article[\'articleid\'].html',
		'dst'=>'$articlehtmllink'
	)
);
$srcdata = readfromfile("phpArticle.style");
foreach($modify_Table AS $data)
{
	$patten = preg_quote($data['src'], "/");
	$pattenend = preg_quote($data['dst']);
	$srcdata = stripslashes(preg_replace("/$patten/",$pattenend,$srcdata));
//	echo $data['src']."777".$patten."88888888".$pattenend."<br>";
	unset($patten);unset($pattenend);
}

writetofile("outputsytle.style",$srcdata);//str_replace(Array('\.','\?'),Array('.','?'),$srcdata)
echo "successful!<br>生成转换后的文件outputsytle.style,<br>请把该文件导入覆盖默认风格";

function readfromfile($file_name) {
 if(file_exists($file_name)==0) {
        return "";
 } else {
  $filenum=fopen($file_name,"r");
  flock($filenum,LOCK_SH);
  $file_data=fread($filenum,filesize($file_name));
  fclose($filenum);
  return $file_data;
 }
}

function writetofile($file_name,$data,$method="w") {
 if ($data!="") {
  $filenum=fopen($file_name,$method);
  flock($filenum,LOCK_EX);
  $file_data=fwrite($filenum,$data);
  fclose($filenum);
  return $file_data;
 }else{
  $fp = fopen($file_name, "w");
  fclose($fp);
 }
}
