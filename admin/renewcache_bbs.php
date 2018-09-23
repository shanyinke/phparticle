<script type="text/javascript">
function checkpm()
{
		if (confirm("您确定真的要刷新缓冲吗？"))
		{
			window.location.replace("renewcache_bbs.php?action=do");
		}else
		document.write("您已经取消操作.")
	return;
}
</script>
<?php
error_reporting(7);
define('CREATE_HTML_FILE', 1);
require "functions_bbs.php";
require "global_bbs.php";
require "../modules/default/functions_bbs.php";
chdir('./../');
cpheader();
if ($_GET['action'] == "do") {
        $DB->query("UPDATE " . $db_prefix . "cache_bbs SET expiry = 1");
        $styleid = 1;
        $style = getstyle();
        cachesorts();
        makesortlist();
        makehot_recommend_articlelist();
        echo("缓冲已经被刷新！");
} else {

        ?>
	<script>
	checkpm()
	</script>
	<?php
} 
cpfooter();

?>