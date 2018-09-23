<script type="text/javascript">
function checkpm()
{
		if (confirm("您确定真的要刷新缓冲吗？"))
		{
			window.location.replace("renewcache.php?action=do");
		}else
		document.write("您已经取消操作.")
	return;
}
</script>
<?php
error_reporting(7);
define('CREATE_HTML_FILE', 1);
require "functions.php";
require "global.php";
require "../modules/default/functions.php";
chdir('./../');
cpheader();
if ($_GET['action'] == "do") {
        $DB->query("UPDATE " . $db_prefix . "cache SET expiry = 1");
        $styleid = 1;
        $style = getstyle();
        if (empty($noheader)) {
                if (trim($templatelist) != "") {
                        $templatelist .= ",";
                } 
                $templatelist .= "header,footer,";
        } 

        if (trim($templatelist) != "") {
                $templatelist .= ",";
        } 

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