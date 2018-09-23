<?
/**********************************
 *使用方法:
 *<script src="$g_o_back2root/count.php"></script>
 *在你的articlehome模板</html>前面加这句话。
**********************************/
error_reporting(7);

//require "admin/config.php";
if($_GET['sys'])
$loadsystem_suffix=$_GET['sys'];
else{
$loadsystem_suffix='_bbs';
exit;
}
require "admin/loadsystem/config".$loadsystem_suffix.".php";
require "admin/class/mysql.php";
require "admin/loadsystem/dbrelation".$loadsystem_suffix.".php";
$debug=1;
$DB_bbs = new DB_MySQL;

$DB_bbs->servername=$servername_bbs;
$DB_bbs->dbname=$dbname_bbs;
$DB_bbs->dbusername=$dbusername_bbs;
$DB_bbs->dbpassword=$dbpassword_bbs;

$DB_bbs->connect();
$DB_bbs->selectdb();
if(intval($_GET['aid'])>0)
	$str[1]=$_GET['aid'];

else if(preg_match("@/[0-9]+_[0-9]+/[^/]*[^0-9]+([0-9]+)_[^/]+$@",$_SERVER["HTTP_REFERER"],$str)){}
//$filename = substr(strrchr($_SERVER["HTTP_REFERER"], "-"), 1);
if($str[1])
{
//$specials = explode("_",$filename);//$specials[1]=1;
//if($specials[0]=='article')
{
	$DB_bbs->query("UPDATE ".$db_prefix_bbs.$forumlist['article']." SET ".$tablelist['article']['views']."=".$tablelist['article']['views']."+1 WHERE ".$tablelist['article']['articleid']."='".$str[1]."'");
	$data = $DB_bbs->query("SELECT ".$tablelist['article']['views']." FROM ".$db_prefix_bbs.$forumlist['article']." WHERE ".$tablelist['article']['articleid']."=".$str[1]);
	$views = $DB_bbs->fetch_array($data);
}
}

?>
document.write("<?=$views['views']?>");
<?
exit;
?>