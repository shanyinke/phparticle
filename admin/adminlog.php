<?php
error_reporting(7);
require "global.php";
cpheader();

if ($_GET[action]=="view") {

    $nav = new buildNav;

    $total = $DB->fetch_one_array("SELECT count(*) AS count FROM ".$db_prefix."adminlog");
    $nav->total_result = $total[count];

    if ($total[count]==0) {
        pa_exit("没有任何记录");
    }

    $cpforms->tableheader();
    echo "<tr><td>
           <a href=\"adminlog.php?action=killall\">清空所有记录</a>
          </td></tr>";
    $cpforms->tablefooter();

    $nav->execute("SELECT adminlog.*,user.username FROM ".$db_prefix."adminlog AS adminlog
                          LEFT JOIN ".$db_prefix."user AS user
                               USING (userid)
                          ORDER BY adminlog.date DESC");


    echo $nav->pagenav();

    echo "<table class=\"tableoutline\" boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\">
               <tr align=\"center\" class=\"tbhead\">
                <td align=center>id#</td>
                <td nowrap>用户</td>
                <td nowrap>ip</td>
                <td nowrap>时间</td>
                <td nowrap>文件</td>
                <td nowrap>操作</td>
                <td nowrap>删除</td>
               </tr>";
    while($adminlog=$DB->fetch_array($nav->sql_result)){
          echo "<tr class=".getrowbg().">
                  <td align=\"center\">$adminlog[adminlogid]</td>
                  <td nowrap>$adminlog[username]</td>
                  <td nowrap>$adminlog[ipaddress]</td>
                  <td nowrap>".date("Y-m-d H:i a",$adminlog[date])."</td>
                  <td>$adminlog[script]</td>
                  <td nowrap>$adminlog[action]</td>
                  <td nowrap><a href=\"adminlog.php?action=kill&adminlogid=$adminlog[adminlogid]\">删除</a>

                </td></tr>";
    }
    echo "</table>";

    echo $nav->pagenav();

}
if ($_GET[action]=="kill") {
    $cpforms->formheader(array('title'=>'确实要删除该记录?'));
    $cpforms->makehidden(array('name'=>'adminlogid',
                          'value'=>$_GET[adminlogid]));
    $cpforms->makehidden(array('name'=>'action',
                          'value'=>'remove'));
    $cpforms->formfooter(array('confirm'=>1));
}


if ($_POST[action]=="remove") {
    $DB->query("DELETE FROM ".$db_prefix."adminlog WHERE adminlogid='".intval($_POST[adminlogid])."'");
    redirect("adminlog.php?action=view","该记录已删除");
}


if ($_GET[action]=="killall") {
    $cpforms->formheader(array('title'=>'确实要删除所有记录?'));
    $cpforms->makehidden(array('name'=>'action',
                          'value'=>'removeall'));
    $cpforms->formfooter(array('confirm'=>1));
}

if ($_POST[action]=="removeall") {
    $DB->query("DELETE FROM ".$db_prefix."adminlog");
    redirect("adminlog.php?action=view","所有记录已删除");
}
cpfooter();
?>