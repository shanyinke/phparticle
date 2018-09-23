<script type="text/javascript">
function checkpm()
{
		if (confirm("您确定真的要刷新统计？"))
		{
			window.location.replace("renewcount.php?action=do");
		}
		else
		document.write("您已经取消操作.")
	return;
}
</script>
<?php
error_reporting(7);
require "global.php";
cpheader();
if ($_GET['action'] == "do") {
        $catedata = $DB->query("SELECT sortid FROM " . $db_prefix . "sort WHERE 1");
        while ($cateinfo = $DB->fetch_array($catedata)) {
                $result = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM " . $db_prefix . "article WHERE sortid = " . $cateinfo['sortid'] . " AND visible=1");
                if ($result['count']) {
                        $allcount += $result['count'];
                        $DB->query("UPDATE " . $db_prefix . "sort SET articlecount='" . $result['count'] . "' WHERE sortid=$cateinfo[sortid]");
                } 
        } 
        echo("统计已经被刷新！共有" . $allcount . "篇文章");
} else {

        ?>
	<script>
	checkpm()
	</script>
	<?php
} 
cpfooter();

?>