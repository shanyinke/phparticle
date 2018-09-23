<?php
error_reporting(7);
require "global.php";


function buildsortnav($sortid){

         global $parentsort;
         if (empty($sortid)) return;
         if ($sortid!=-1){
             foreach ($parentsort[$sortid] as $psid => $stitle) {
                      $navbit = " \\ <a href=\"./article.php?action=edit&sortid=$sortid\">$stitle</a>";
                      $navbit = buildsortnav($psid).$navbit;
             }
         }
         return $navbit;

}



$cachesorts = cachesorts();


if ($pauserinfo[isadmin]==1) {

    $filter_sort_array = array();

} else {

    $un_filter_sort_array = getmansorts();

    $sorts = $DB->query("SELECT * FROM ".$db_prefix."sort");
    while ($sort = $DB->fetch_array($sorts)) {
           if (!isset($un_filter_sort_array[$sort[sortid]])) {
               $filter_sort_array[$sort[sortid]] = 1;
           }
    }

}

if (empty($un_filter_sort_array)) {
    $un_filter_sort_array = array();
}


cpheader();

if ($_GET[action]=="edit") {

    $articleid = intval($_GET[articleid]);

    $article = validate_articleid($articleid);

    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }


    if (!empty($article)) {
        $cpforms->tableheader();
        echo "<tr class=".getrowbg().">
                 <td>����: <a href=\"./article.php?action=list\">������</a>".buildsortnav($article[sortid])." \ <a href=\"comment.php?action=edit&articleid=$article[articleid]\">$article[title]</a> \ ����</td>
              </tr>";
        $cpforms->tablefooter();
        echo "<br>";
    }

    $nav = new buildNav;

    $total = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."comment WHERE articleid='$articleid'");

    $nav->total_result = $total[count];

    if (empty($total[count])) {
        pa_exit("�����»�û���κ�����");
    }

    $nav->execute("SELECT * FROM ".$db_prefix."comment WHERE articleid='$articleid' ORDER BY lastupdate DESC");

    echo $nav->pagenav();

    echo "<table boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\" class=\"tableoutline\">
           <form action=\"comment.php\" method=\"post\">
               <tr align=\"center\" class=\"tbhead\">
                <td nowrap> id# </td>
                <td width=\"60%\"> ���� </td>
                <td nowrap> ���� </td>
                <td nowrap> ���� </td>
                <td nowrap> ��� </td>
                <td nowrap> �ظ� </td>
                <td nowrap> �༭ </td>
                <td nowrap> ɾ�� </td>
               </tr>\n";
    while ($comment = $DB->fetch_array($nav->sql_result)){
           echo "<tr class=".getrowbg().">
                      <td align=\"center\" nowrap>$comment[commentid]</td>
                      <td><a target=_blank href=\"../message.php?action=view&commentid=$comment[commentid]\">$comment[title]</a></td>
                      <td align=\"center\" nowrap> $comment[author]</td>
                      <td align=\"center\" nowrap>".date("Y-m-d H:i:s",$comment[date])."</td>
                      <td align=\"center\" nowrap>$comment[views]</td>
                      <td align=\"center\" nowrap><a href=\"message.php?action=edit&commentid=$comment[commentid]\">$comment[replies]</a></td>
                      <td align=\"center\" nowrap> [<a href=\"comment.php?action=mod&commentid=$comment[commentid]\">�༭</a>] [<a href=\"comment.php?action=kill&commentid=$comment[commentid]\">ɾ��</a>]</td>
                      <td align=\"center\"><input type=\"checkbox\" name=\"comment[]\" value=\"$comment[commentid]\"></td>
                     </tr>\n";

    }
    echo "<tr class=\"tbhead\">
                <td align=\"center\" colspan=\"8\">
                <input type=\"hidden\" name=\"articleid\" value=\"$articleid\">
                <input type=\"hidden\" name=\"action\" value=\"deleteselected\">
                <input type=\"submit\" name=\"deletecomments\" value=\"ɾ������ѡ�е�����\" class=\"bginput\">
                <input type=\"submit\" name=\"deleteallcomments\" value=\"ɾ�������µ���������\" class=\"bginput\">
                </td>
              </tr>";
    echo "</form>";
    echo "</table>\n";

    echo $nav->pagenav();

}

if ($_GET[action]=="mod") {

    $commentid = intval($_GET[commentid]);
    $comment = validate_commentid($commentid);
    $article = validate_articleid($comment[articleid]);

    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }

    $cpforms->formheader(array('title'=>"�༭����: $comment[title]"));
    $cpforms->makeinput(array('text'=>'���۱���:',
                               'name'=>'title',
                               'value'=>$comment[title]));
    $cpforms->makehidden(array('name'=>'commentid',
                                'value'=>$commentid));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'update'));
    $cpforms->formfooter();
}

if ($_POST[action]=="update") {

    $commentid = intval($_POST[commentid]);
    $comment = validate_commentid($commentid);

    $article = validate_articleid($comment[articleid]);

    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }

    $DB->query("UPDATE ".$db_prefix."comment SET
                       title='".addslashes(htmlspecialchars(trim($_POST[title])))."'
                       WHERE commentid='$commentid'");

    redirect("./comment.php?action=edit&articleid=$comment[articleid]","�������Ѹ���");

}

if ($_GET[action]=="kill") {

    $commentid = intval($_GET[commentid]);
    $comment = validate_commentid($commentid);

    $article = validate_articleid($comment[articleid]);

    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }

    $cpforms->formheader(array('title'=>'ȷʵҪɾ��������?��������ظ����ᱻɾ��!'));
    $cpforms->makehidden(array('name'=>'commentid',
                                'value'=>$commentid));

    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'remove'));
    $cpforms->formfooter();

}

if ($_POST[action]=="remove") {

    $commentid = intval($_POST[commentid]);
    $comment = validate_commentid($commentid);

    $article = validate_articleid($comment[articleid]);

    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }

    $DB->query("DELETE FROM ".$db_prefix."message WHERE commentid='$commentid'");
    $DB->query("DELETE FROM ".$db_prefix."comment WHERE commentid='$commentid'");

    updatecomments($comment[articleid]);
    redirect("./comment.php?action=edit&articleid=$comment[articleid]","��������ɾ��");

}

if ($_POST[action]=="deleteselected") {


    $articleid = intval($_POST[articleid]);
    $article = validate_articleid($articleid);

    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }
    if ($_POST[deleteallcomments]) {

        $cpforms->formheader(array('title'=>'ȷʵҪɾ�������µ���������?'));
        $cpforms->makehidden(array('name'=>'articleid',
                                    'value'=>$articleid));
        $cpforms->makehidden(array('name'=>'action',
                                    'value'=>'removeallcomments'));
        $cpforms->formfooter();

    } elseif ($_POST[deletecomments]) {
        //print_rr($_POST[comment]);
        if (empty($_POST[comment]) OR count($_POST[comment])==0) {
            pa_exit("��δѡ��Ҫɾ��������");
        }
        $cpforms->formheader(array('title'=>'ȷʵҪɾ�������µ�����ѡ�е�����?'));
        $cpforms->makehidden(array('name'=>'articleid',
                                    'value'=>$articleid));

        foreach ($_POST[comment] AS $k=>$v) {
                 $cpforms->makehidden(array('name'=>'comment[]',
                                             'value'=>$v));
        }
        $cpforms->makehidden(array('name'=>'action',
                                    'value'=>'removecomments'));
        $cpforms->formfooter();

    }

}

if ($_POST[action]=="removeallcomments") {

    $articleid = intval($_POST[articleid]);
    $article = validate_articleid($articleid);
    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }
    $comments = $DB->query("SELECT commentid FROM ".$db_prefix."comment WHERE articleid='$articleid'");
    while ($comment = $DB->fetch_array($comments)) {
           $cachecomment[] = $comment[commentid];
    }
    if (!empty($cachecomment) AND is_array($cachecomment)) {
        $commentids = implode(",",$cachecomment);
    }
    unset($cachecomment);

    $DB->query("DELETE FROM ".$db_prefix."message WHERE commentid IN (0$commentids)");
    $DB->query("DELETE FROM ".$db_prefix."comment WHERE articleid='$articleid'");

    updatecomments($articleid);
    redirect("./comment.php?action=edit&articleid=$articleid","�����µ�����������ɾ��");

}


if ($_POST[action]=="removecomments") {

    $articleid = intval($_POST[articleid]);
    $article = validate_articleid($articleid);
    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }

    if (empty($_POST[comment]) OR count($_POST[comment])==0) {
        pa_exit("��δѡ��Ҫɾ��������");
    }

    if (is_array($_POST[comment])) {
        $commentids = implode(",",$_POST[comment]);
    }

    $DB->query("DELETE FROM ".$db_prefix."message WHERE commentid IN (0$commentids)");

    $DB->query("DELETE FROM ".$db_prefix."comment WHERE
                       articleid='$articleid'
                       AND commentid IN (0$commentids)");

    updatecomments($articleid);
    redirect("./comment.php?action=edit&articleid=$articleid","�����µ�����ѡ�е�������ɾ��");

}


cpfooter();
?>
