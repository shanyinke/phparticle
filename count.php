<?
/**********************************
 *使用方法:
 *<script src="$phparticleurl/count.php"></script>
 *在你的articlehome模板</html>前面加这句话。
**********************************/
error_reporting(7);

require "admin/config.php";
require "admin/class/mysql.php";

$DB = new DB_MySQL;

$DB->servername = $servername;
$DB->dbname = $dbname;
$DB->dbusername = $dbusername;
$DB->dbpassword = $dbpassword;

$DB->connect();
$DB->selectdb();
if(intval($_GET['aid'])>0)
	$str[1]=$_GET['aid'];
else if(preg_match("@/[0-9]+_[0-9]+/[^/]*[^0-9]+([0-9]+)_[^/]+$@",$_SERVER["HTTP_REFERER"],$str)){}
//$filename = substr(strrchr($_SERVER["HTTP_REFERER"], "-"), 1);
if($str[1])
{
//$specials = explode("_",$filename);//$specials[1]=1;
//if($specials[0]=='article')
{
	$DB->query("UPDATE ".$db_prefix."article SET views=views+1 WHERE articleid='".$str[1]."'");
	$data = $DB->query("SELECT views FROM ".$db_prefix."article WHERE articleid=".$str[1]);
	$views = $DB->fetch_array($data);
}
}

?>
document.write("<?=$views['views']?>");
<?
exit;
?>