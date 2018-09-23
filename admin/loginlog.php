<?php
error_reporting(7);
require "global.php";
cpheader();

if ($_GET[action]=="view") {

    $nav = new buildNav;

    $total = $DB->fetch_one_array("SELECT count(*) AS count FROM ".$db_prefix."loginlog");
    $nav->total_result = $total[count];

    if ($total[count]==0) {
        pa_exit("没有任何记录");
    }

    $cpforms->tableheader();
    echo "<tr><td>
           <a href=\"loginlog.php?action=killall\">清空所有记录</a>
          </td></tr>";
    $cpforms->tablefooter();

    $nav->execute("SELECT * FROM ".$db_prefix."loginlog
                            ORDER BY date DESC");


    echo $nav->pagenav();

    echo "<table class=\"tableoutline\" boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\">
               <tr align=\"center\" class=\"tbhead\">
                <td align=center>id#</td>
                <td align=center>ip</td>
                <td width=\"30%\" nowrap>用户名</td>
                <td nowrap width=\"30%\">密码</td>
                <td nowrap>时间</td>
                <td nowrap>其它信息</td>
                <td nowrap>删除</td>
               </tr>";
    while($loginlog=$DB->fetch_array($nav->sql_result)){
          echo "<tr class=".getrowbg().">
                  <td align=\"center\">$loginlog[loginlogid]</td>
                  <td align=\"center\">$loginlog[ipaddress]</td>
                  <td>$loginlog[username]</td>
                  <td>$loginlog[password]</td>
                  <td nowrap>".date("Y-m-d H:i:s a",$loginlog[date])."</td>
                  <td>$loginlog[extra]</td>
                  <td nowrap><a href=\"loginlog.php?action=kill&loginlogid=$loginlog[loginlogid]\">删除</a>

                </td></tr>";
    }
    echo "</table>";

    echo $nav->pagenav();

}

if ($_GET[action]=="kill") {
    $cpforms->formheader(array('title'=>'确实要删除该记录?'));
    $cpforms->makehidden(array('name'=>'loginlogid',
                          'value'=>$_GET[loginlogid]));
    $cpforms->makehidden(array('name'=>'action',
                          'value'=>'remove'));

    $cpforms->formfooter(array('confirm'=>1));
}

if ($_POST[action]=="remove") {
    $DB->query("DELETE FROM ".$db_prefix."loginlog WHERE loginlogid='".intval($_POST[loginlogid])."'");
    redirect("loginlog.php?action=view","该记录已删除");
}

if ($_GET[action]=="killall") {
    $cpforms->formheader(array('title'=>'确实要删除所有记录?'));
    $cpforms->makehidden(array('name'=>'action',
                          'value'=>'removeall'));
    $cpforms->formfooter(array('confirm'=>1));
}


if ($_POST[action]=="removeall") {
    $DB->query("DELETE FROM ".$db_prefix."loginlog");
    redirect("loginlog.php?action=view","所有记录已删除");
}
cpfooter();
?>
