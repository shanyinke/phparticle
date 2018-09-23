<?
require "global.php";
cpheader();
require_once "./makefile.php";
if($_GET['mod'])
{
	compile_module_file($_GET['mod'], "../modules/default/", "../templates/default/html", "../templates/default/src");
}
else
{
	chdir("../templates/default");
	$handle=opendir("html");
	while ($file = readdir($handle)) 
	{
		if (is_file("html/".$file)&&!strchr($file,'_'))//||strstr($file,'_bbs'
		{
			$mod = substr($file,0,strpos($file,"."));
		}else{
			continue;
		}
		echo $mod."<br>";
		compile_module_file($mod, "../../modules/default/", "../../templates/default/html", "../../templates/default/src");
	}
	compile_module_file("mkarticle_bbs", "../../modules/default/", "../../templates/default/html", "../../templates/default/src");
	compile_module_file("functions_bbs", "../../modules/default/", "../../templates/default/html", "../../templates/default/src");
	compile_module_file("mksort_bbs", "../../modules/default/", "../../templates/default/html", "../../templates/default/src");
	compile_module_file("comment_add", "../../modules/default/", "../../templates/default/html", "../../templates/default/src");
	compile_module_file("comment_preview", "../../modules/default/", "../../templates/default/html", "../../templates/default/src");
	closedir($handle);
}
echo "完成";
cpfooter();
?>