<?php
error_reporting(7);
require "global.php";

function validate_messageid(&$messageid) {

         global $DB,$db_prefix;
         $messageid = intval($messageid);
         if (empty($messageid)) {
             pa_exit("该评论不存在");
         }

         $message = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."message WHERE messageid='$messageid'");
         if (empty($message)) {
             pa_exit("该评论不存在");
         }
         return $message;

}

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

cpheader();

if ($_GET[action]=="edit") {

    $commentid = intval($_GET[commentid]);
    $comment = validate_commentid($commentid);

    $articleid = intval($comment[articleid]);

    $article = validate_articleid($articleid);

    if (!empty($article)) {
        $cpforms->tableheader();
        echo "<tr class=".getrowbg().">
                 <td>导航: <a href=\"./article.php?action=list\">根分类</a>".buildsortnav($article[sortid])." \ <a href=\"comment.php?action=edit&articleid=$comment[articleid]\">$article[title]</a> \ <a href=\"message.php?action=edit&commentid=$comment[commentid]\">$comment[title]</a> \ 评论</td>
              </tr>";
        $cpforms->tablefooter();
        echo "<br>";
    }

    $nav = new buildNav;

    $total = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."message WHERE commentid='$commentid'");

    $nav->total_result = $total[count];

    if (empty($total[count])) {
        pa_exit("该评论不存在");
    }

    $nav->execute("SELECT * FROM ".$db_prefix."message WHERE commentid='$commentid' ORDER BY date");

    echo $nav->pagenav();

    echo "<table boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\" class=\"tableoutline\">
           <form action=\"message.php\" method=\"post\">
               <tr align=\"center\" class=\"tbhead\">
                <td nowrap> id# </td>
                <td width=\"40%\"> 标题 </td>
                <td nowrap> 作者 </td>
                <td nowrap> 发表日期 </td>
                <td nowrap> ip </td>
                <td nowrap> 编辑 </td>
                <td nowrap> 删除 </td>
               </tr>\n";
    while ($message = $DB->fetch_array($nav->sql_result)){
           if ($message[removed]) {
               $message[title] = "<b>已标记为删除</b>: <s>$message[title]</s>";
           }
           echo "<tr class=".getrowbg().">
                      <td align=\"center\" nowrap>$message[messageid]</td>
                      <td><a target=_blank href=\"../message.php?action=view&commentid=$message[commentid]&messageid=$message[messageid]\">$message[title]</a></td>
                      <td align=\"center\" nowrap> $message[author]</td>
                      <td align=\"center\" nowrap>".date("Y-m-d H:i:s",$message[date])."</td>
                      <td align=\"center\" nowrap>$message[ipaddress]</td>
                      <td align=\"center\" nowrap>[<a href=\"message.php?action=mod&messageid=$message[messageid]\">编辑</a>]";
           if (!$message[removed]) {
               echo "[<a href=\"message.php?action=kill&tag=1&messageid=$message[messageid]\" title=\"只作删除标记,并非物理删除,以后可以恢复\">作删除标记</a>]";
           }
           echo     "
                      [<a href=\"message.php?action=kill&tag=0&messageid=$message[messageid]\" title=\"物理删除,以后不可恢复\">物理删除</a>]</td>
                      <td align=\"center\"><input type=\"checkbox\" name=\"message[]\" value=\"$message[messageid]\"></td>
                     </tr>\n";
    }
    echo "<tr class=\"tbhead\">
                <td align=\"center\" colspan=\"8\">
                <input type=\"hidden\" name=\"commentid\" value=\"$commentid\">
                <input type=\"hidden\" name=\"action\" value=\"deleteselected\">
                <input type=\"checkbox\" name=\"tag\" checked title=\"只作删除标记,并非真的删除,以后可以恢复\">只作删除标记?
                <input type=\"submit\" name=\"deletemessages\" value=\"删除所有选中的评论/回复\" class=\"bginput\">
                <input type=\"submit\" name=\"deleteallmessages\" value=\"删除本文章的所有评论/回复\" class=\"bginput\">
                </td>
              </tr>";
    echo "</form>";
    echo "</table>\n";


    echo $nav->pagenav();

}


if ($_GET[action]=="mod") {

    $messageid = intval($_GET[messageid]);
    $message = validate_messageid($messageid);
    $cpforms->formheader(array('title'=>"编辑评论: $message[title]"));
    $cpforms->makeinput(array('text'=>'标题:',
                               'name'=>'title',
                               'value'=>$message[title]));
    $cpforms->maketextarea(array('text'=>'内容:',
                                 'name'=>'message',
                                 'value'=>$message[message],
                                 'rows'=>10,
                                 'cols'=>90));
    $cpforms->makeyesno(array('text'=>'作删除标记?<br>标记后,本评论并不显示.',
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
    $DB->query("UPDATE ".$db_prefix."message SET
                       title='".addslashes(htmlspecialchars(trim($_POST[title])))."',
                       message='".addslashes($message)."',
                       removed='".intval($_POST[removed])."'
                       WHERE messageid='$messageid'");

    redirect("./message.php?action=edit&commentid=$messageinfo[commentid]","该评论已更新");

}

if ($_GET[action]=="kill") {

    $messageid = intval($_GET[messageid]);
    $messageinfo = validate_messageid($messageid);
    $cpforms->formheader(array('title'=>'确实要删除该评论?如果该评论没有其它回复,将整个删除!'));
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
    redirect("./message.php?action=edit&commentid=$commentid","该评论已删除");


}


if ($_POST[action]=="deleteselected") {


    $commentid = intval($_POST[commentid]);
    $comment = validate_commentid($commentid);


    if ($_POST[deleteallmessages]) {

        $cpforms->formheader(array('title'=>'确实要删除该文章的所有评论?'));
        $cpforms->makehidden(array('name'=>'commentid',
                                    'value'=>$commentid));
        $cpforms->makehidden(array('name'=>'tag',
                                    'value'=>$_POST[tag]));
        $cpforms->makehidden(array('name'=>'action',
                                    'value'=>'removeallmessages'));
        $cpforms->formfooter();

    } elseif ($_POST[deletemessages]) {

        if (empty($_POST[message]) OR count($_POST[message])==0) {
            pa_exit("仍未选中要删除的评论");
        }
        $cpforms->formheader(array('title'=>'确实要删除该文章的所有选中的评论?'));
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

    if ($_POST[tag]) {
        $DB->query("UPDATE ".$db_prefix."message SET
                             removed=1
                             WHERE commentid='$commentid'");
    } else {
        $DB->query("DELETE FROM ".$db_prefix."message WHERE commentid='$commentid'");
        $DB->query("DELETE FROM ".$db_prefix."comment WHERE commentid='$commentid'");
    }

    updatecomments($comment[articleid]);
    redirect("./message.php?action=edit&commentid=$commentid","该评论已删除");

}


if ($_POST[action]=="removemessages") {

    $commentid = intval($_POST[commentid]);
    $comment = validate_commentid($commentid);

    if (empty($_POST[message]) OR count($_POST[message])==0) {
        pa_exit("仍未选中要删除的评论");
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
    redirect("./message.php?action=edit&commentid=$commentid","该文章的所有选中的评论已删除");

}

cpfooter();
?>
