<?php
error_reporting(7);
require "global.php";

function validate_messageid(&$messageid) {

         global $DB,$db_prefix;
         $messageid = intval($messageid);
         if (empty($messageid)) {
             pa_exit("�����۲�����");
         }

         $message = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."message WHERE messageid='$messageid'");
         if (empty($message)) {
             pa_exit("�����۲�����");
         }
         return $message;

}

function buildsortnav($sortid){

         global $parentsort;
         if (empty($sortid)) return;
         if ($sortid!=-1){
             foreach ($parentsort[$sortid] as $psid => $stitle) {
                      //$stitle = htmlspecialchars($stitle);
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

    $commentid = intval($_GET[commentid]);
    $comment = validate_commentid($commentid);

    $articleid = intval($comment[articleid]);

    $article = validate_articleid($articleid);

    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }


    if (!empty($article)) {
        $cpforms->tableheader();
        echo "<tr class=".getrowbg().">
                 <td>����: <a href=\"./article.php?action=list\">������</a>".buildsortnav($article[sortid])." \ <a href=\"comment.php?action=edit&articleid=$comment[articleid]\">$article[title]</a> \ <a href=\"message.php?action=edit&commentid=$comment[commentid]\">$comment[title]</a> \ ����</td>
              </tr>";
        $cpforms->tablefooter();
        echo "<br>";
    }

    $nav = new buildNav;

    $total = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."message WHERE commentid='$commentid'");

    $nav->total_result = $total[count];

    if (empty($total[count])) {
        pa_exit("�����۲�����");
    }

    $nav->execute("SELECT * FROM ".$db_prefix."message WHERE commentid='$commentid' ORDER BY date");

    echo $nav->pagenav();

    echo "<table boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\" class=\"tableoutline\">
           <form action=\"comment.php\" method=\"post\">
               <tr align=\"center\" class=\"tbhead\">
                <td nowrap> id# </td>
                <td width=\"40%\"> ���� </td>
                <td nowrap> ���� </td>
                <td nowrap> �������� </td>
                <td nowrap> ip </td>
                <td nowrap> �༭ </td>
                <td nowrap> ɾ�� </td>
               </tr>\n";
    while ($message = $DB->fetch_array($nav->sql_result)){
           if ($message[removed]) {
               $message[title] = "<b>�ѱ��Ϊɾ��</b>: <s>$message[title]</s>";
           }
           echo "<tr class=".getrowbg().">
                      <td align=\"center\" nowrap>$message[messageid]</td>
                      <td><a target=_blank href=\"../message.php?action=view&commentid=$message[commentid]&messageid=$message[messageid]\">$message[title]</a></td>
                      <td align=\"center\" nowrap> $message[author]</td>
                      <td align=\"center\" nowrap>".date("Y-m-d H:i:s",$message[date])."</td>
                      <td align=\"center\" nowrap>$message[ipaddress]</td>
                      <td align=\"center\" nowrap>[<a href=\"message.php?action=mod&messageid=$message[messageid]\">�༭</a>]";
           if (!$message[removed]) {
               echo "[<a href=\"message.php?action=kill&tag=1&messageid=$message[messageid]\" title=\"ֻ��ɾ�����,��������ɾ��,�Ժ���Իָ�\">��ɾ�����</a>]";
           }
           echo     "
                      [<a href=\"message.php?action=kill&tag=0&messageid=$message[messageid]\" title=\"����ɾ��,�Ժ󲻿ɻָ�\">����ɾ��</a>]</td>
                      <td align=\"center\"><input type=\"checkbox\" name=\"message[]\" value=\"$message[messageid]\"></td>
                     </tr>\n";
    }
    echo "<tr class=\"tbhead\">
                <td align=\"center\" colspan=\"8\">
                <input type=\"hidden\" name=\"articleid\" value=\"$articleid\">
                <input type=\"hidden\" name=\"action\" value=\"deleteselected\">
                <input type=\"checkbox\" name=\"tag\" checked title=\"ֻ��ɾ�����,�������ɾ��,�Ժ���Իָ�\">ֻ��ɾ�����?
                <input type=\"submit\" name=\"deletemessages\" value=\"ɾ������ѡ�е�����/�ظ�\" class=\"bginput\">
                <input type=\"submit\" name=\"deleteallmessages\" value=\"ɾ�������µ���������/�ظ�\" class=\"bginput\">
                </td>
              </tr>";
    echo "</form>";
    echo "</table>\n";


    echo $nav->pagenav();

}


if ($_GET[action]=="mod") {

    $messageid = intval($_GET[messageid]);
    $message = validate_messageid($messageid);
    $comment = validate_commentid($message[commentid]);

    $article = validate_articleid($comment[articleid]);

    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }

    $cpforms->formheader(array('title'=>"�༭����: $message[title]"));
    $cpforms->makeinput(array('text'=>'����:',
                               'name'=>'title',
                               'value'=>$message[title]));
    $cpforms->maketextarea(array('text'=>'����:',
                                 'name'=>'message',
                                 'value'=>$message[message],
                                 'rows'=>10,
                                 'cols'=>90));
    $cpforms->makeyesno(array('text'=>'��ɾ�����?<br>��Ǻ�,�����۲�����ʾ.',
                               'name'=>'removed',
                               'selected'=>$message[removed]));
    $cpforms->makehidden(array('name'=>'messageid',
                                'value'=>$messageid));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'update'));
    $cpforms->formfooter();
}


if ($_POST[action]=="update") {

    $messageid = intval($_POST[messageid]);

    $messageinfo = validate_messageid($messageid);
    $message = trim($_POST[message]);

    $comment = validate_commentid($message[commentid]);

    $article = validate_articleid($comment[articleid]);

    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }

    $DB->query("UPDATE ".$db_prefix."message SET
                       title='".addslashes(htmlspecialchars(trim($_POST[title])))."',
                       message='".addslashes($message)."',
                       removed='".intval($_POST[removed])."'
                       WHERE messageid='$messageid'");

    redirect("./message.php?action=edit&commentid=$messageinfo[commentid]","�������Ѹ���");

}

if ($_GET[action]=="kill") {

    $messageid = intval($_GET[messageid]);
    $messageinfo = validate_messageid($messageid);

    $comment = validate_commentid($messageinfo[commentid]);

    $article = validate_articleid($comment[articleid]);

    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }

    $cpforms->formheader(array('title'=>'ȷʵҪɾ��������?���������û�������ظ�,������ɾ��!'));
    $cpforms->makehidden(array('name'=>'messageid',
                                'value'=>$messageid));
    $cpforms->makehidden(array('name'=>'tag',
                                'value'=>$_GET[tag]));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'remove'));
    $cpforms->formfooter();


}


if ($_POST[action]=="remove") {

    $messageid = intval($_POST[messageid]);
    $message = validate_messageid($messageid);

    $comment = validate_commentid($message[commentid]);

    $article = validate_articleid($comment[articleid]);

    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }

    $commentid = $message[commentid];
    if ($_POST[tag]) {
        $DB->query("UPDATE ".$db_prefix."message SET removed=1
                             WHERE messageid='$messageid'");
    } else {

        $messages = $DB->query("SELECT * FROM ".$db_prefix."message WHERE commentid='$commentid'");

        while ($message = $DB->fetch_array($messages)) {
               $cachemessages[$message[parentid]][] = $message;
        }
        $DB->free_result($messages);
        unset($message);



        $DB->query("DELETE FROM ".$db_prefix."message WHERE messageid IN (0".getsubmessages($messageid).") AND commentid='$commentid'");


        $messages = $DB->fetch_one_array("SELECT COUNT(*) AS total FROM ".$db_prefix."message WHERE commentid='$commentid'");
        $DB->query("UPDATE ".$db_prefix."comment SET replies='$messages[total]' WHERE commentid='$commentid'");
        if ($messages[total]==0) {
            $DB->query("DELETE FROM ".$db_prefix."message WHERE commentid='$commentid'");
            $comment = validate_commentid($commentid);
            updatecomments($comment[articleid]);
        }

    }
    redirect("./message.php?action=edit&commentid=$commentid","��������ɾ��");


}


if ($_POST[action]=="deleteselected") {


    $commentid = intval($_POST[commentid]);
    $comment = validate_commentid($commentid);

    $article = validate_articleid($comment[articleid]);

    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }

    if ($_POST[deleteallmessages]) {

        $cpforms->formheader(array('title'=>'ȷʵҪɾ�������µ���������?'));
        $cpforms->makehidden(array('name'=>'commentid',
                                    'value'=>$commentid));
        $cpforms->makehidden(array('name'=>'tag',
                                    'value'=>$_POST[tag]));
        $cpforms->makehidden(array('name'=>'action',
                                    'value'=>'removeallmessages'));
        $cpforms->formfooter();

    } elseif ($_POST[deletemessages]) {

        if (empty($_POST[message]) OR count($_POST[message])==0) {
            pa_exit("��δѡ��Ҫɾ��������");
        }
        $cpforms->formheader(array('title'=>'ȷʵҪɾ�������µ�����ѡ�е�����?'));
        $cpforms->makehidden(array('name'=>'commentid',
                                    'value'=>$commentid));

        foreach ($_POST[message] AS $k=>$v) {
                 $cpforms->makehidden(array('name'=>'message[]',
                                             'value'=>$v));
        }
        $cpforms->makehidden(array('name'=>'tag',
                                    'value'=>$_POST[tag]));
        $cpforms->makehidden(array('name'=>'action',
                                    'value'=>'removemessages'));
        $cpforms->formfooter();

    }

}

if ($_POST[action]=="removeallmessages") {


    $commentid = intval($_POST[commentid]);
    $comment = validate_commentid($commentid);

    $article = validate_articleid($comment[articleid]);

    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }

    if ($_POST[tag]) {
        $DB->query("UPDATE ".$db_prefix."message SET
                             removed=1
                             WHERE commentid='$commentid'");
    } else {
        $DB->query("DELETE FROM ".$db_prefix."message WHERE commentid='$commentid'");
        $DB->query("DELETE FROM ".$db_prefix."comment WHERE commentid='$commentid'");
    }

    updatecomments($comment[articleid]);
    redirect("./message.php?action=edit&commentid=$commentid","��������ɾ��");

}


if ($_POST[action]=="removemessages") {

    $commentid = intval($_POST[commentid]);
    $comment = validate_commentid($commentid);

    $article = validate_articleid($comment[articleid]);

    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }


    if (empty($_POST[message]) OR count($_POST[message])==0) {
        pa_exit("��δѡ��Ҫɾ��������");
    }

    if ($_POST[tag]) {
        $messageids = implode(",",$_POST[message]);
        $DB->query("UPDATE ".$db_prefix."message SET
                           removed=1
                           WHERE messageid IN (0$messageids)
                           AND commentid='$commentid'");
    } else {

        $messages = $DB->query("SELECT * FROM ".$db_prefix."message WHERE commentid='$commentid'");

        while ($message = $DB->fetch_array($messages)) {
               $cachemessages[$message[parentid]][] = $message;
        }
        $DB->free_result($messages);
        unset($message);

        foreach ($_POST[message] AS $messageid) {
                 $DB->query("DELETE FROM ".$db_prefix."message
                                    WHERE messageid IN (0".getsubmessages($messageid).")
                                    AND commentid='$commentid'");
        }

        $messages = $DB->fetch_one_array("SELECT COUNT(*) AS total FROM ".$db_prefix."message WHERE commentid='$commentid'");
        $DB->query("UPDATE ".$db_prefix."comment SET replies='$messages[total]' WHERE commentid='$commentid'");
        if ($messages[total]==0) {
            $DB->query("DELETE FROM ".$db_prefix."message WHERE commentid='$commentid'");
            $comment = validate_commentid($commentid);
            updatecomments($comment[articleid]);
        }

    }
    redirect("./message.php?action=edit&commentid=$commentid","�����µ�����ѡ�е�������ɾ��");

}

cpfooter();
?>
