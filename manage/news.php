<?php
error_reporting(7);
require "global.php";


if ($_GET[action]==add OR $_GET[action]==mod) {

    $header = "
<script language=\"JavaScript\">

      function openWindow(theURL,winName,features) { //v2.0
               window.open(theURL,winName,features);
      }
      function ProcessNews(){

               if(document.news.title.value == ''){
                  alert('���������.');
                  document.news.title.focus();
                  return false;
               }

               if(document.news.content.value == ''){
                  alert('����������.');
                  return false;
               }

               return true;
      }
</script>";

}

cpheader($header);


if ($_GET[action]=="add") {

    if (empty($permission[canaddnews])) {
        show_nopermission();
    }
    $cpforms->inithtmlarea();

    $cpforms->formheader(array('title'=>'��������,ʱ���ʽ:(Y-m-d),����ֻ���ڷ���ʱ�������ʱ�����ʱ���ڲŻ���ʾ',
                                'name'=>'news',
                                'extra'=>"onsubmit=\"return ProcessNews()\""));
    $cpforms->makeinput(array('text'=>'���ű���:<br><b>����ʹ��html����</b>',
                               'name'=>'title'));
    $cpforms->makeinput(array('text'=>'����ʱ��:',
                               'name'=>'startdate',
                               'value'=>date("Y-m-d H:i:s")
                               ));
    $cpforms->makeinput(array('text'=>'����ʱ��:',
                               'name'=>'enddate',
                               'value'=>date("Y-m-d H:i:s",mktime(date("H"),date("i"),date("s"),date("m")+1,date("d"),date("Y")))
                               ));

    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'insert'));
    $cpforms->makehtmlarea(array('text'=>"����:",
                                 'name'=>'content'));

    $cpforms->formfooter();

}

if ($_POST[action]==insert) {

    if (empty($permission[canaddnews])) {
        show_nopermission();
    }

    $title = trim($_POST[title]);
    if ($title=="" OR trim($_POST[content])=="") {
        pa_exit("�뷵�ز��������������");
    }
    $DB->query("INSERT INTO ".$db_prefix."news (userid,title,content,startdate,enddate)
                       VALUES ('$pauserinfo[userid]','".addslashes($title)."','".addslashes($_POST[content])."',UNIX_TIMESTAMP('$_POST[startdate]'),UNIX_TIMESTAMP('$_POST[enddate]'))");

    redirect("./news.php?action=edit","�������ѷ���");

}

function validate_newsid($newsid) {

         global $DB,$db_prefix;
         $newsid = intval($newsid);

         $news = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."news WHERE newsid='$newsid'");
         if (empty($news)) {
             pa_exit("�����Ų�����");
         }
         return $news;

}

if ($_GET[action]==mod) {

    if (empty($permission[caneditnews])) {
        show_nopermission();
    }
    $news = validate_newsid($_GET[newsid]);

    $cpforms->inithtmlarea();

    $cpforms->formheader(array('title'=>'�༭����,ʱ���ʽ:(Y-m-d H:i:s),����ֻ���ڷ���ʱ�������ʱ�����ʱ���ڲŻ���ʾ',
                                'name'=>'news',
                                'extra'=>"onsubmit=\"return ProcessNews()\""));

    $cpforms->makeinput(array('text'=>'���ű���:<br><b>����ʹ��html����</b>',
                               'name'=>'title',
                               'value'=>$news[title]));

    $cpforms->makeinput(array('text'=>'����ʱ��:',
                               'name'=>'startdate',
                               'value'=>date("Y-m-d H:i:s",$news[startdate])
                               ));
    $cpforms->makeinput(array('text'=>'����ʱ��:',
                               'name'=>'enddate',
                               'value'=>date("Y-m-d H:i:s",$news[enddate])
                               ));

    $cpforms->makehidden(array('name'=>'newsid',
                                'value'=>$news[newsid]));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'update'));

    $cpforms->makehtmlarea(array('text'=>'����:',
                                 'name'=>'content',
                                 'value'=>$news[content]));

    $cpforms->formfooter();

}


if ($_POST[action]==update) {

    if (empty($permission[caneditnews])) {
        show_nopermission();
    }

    $news = validate_newsid($_POST[newsid]);

    $title = trim($_POST[title]);
    if (empty($title) OR empty($_POST[content])) {
        pa_exit("�뷵�ز��������������");
    }
    $DB->query("UPDATE ".$db_prefix."news SET
                       userid='$pauserinfo[userid]',
                       title='".addslashes($title)."',
                       content='".addslashes($_POST[content])."',
                       startdate=UNIX_TIMESTAMP('$_POST[startdate]'),
                       enddate=UNIX_TIMESTAMP('$_POST[enddate]')
                       WHERE newsid='$_POST[newsid]'
                       ");
    redirect("news.php?action=edit","�������Ѹ���");

}

if ($_GET[action]==kill){

    if (empty($permission[canremovenews])) {
        show_nopermission();
    }

    $news = validate_newsid($_GET[newsid]);

    $cpforms->formheader(array('title'=>'ȷʵҪɾ��������?'));
    $cpforms->makehidden(array('name'=>'newsid',
                                'value'=>$news[newsid]));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'remove'));
    $cpforms->formfooter(array('confirm'=>1));

}


if ($_POST[action]==remove) {

    if (empty($permission[canremovenews])) {
        show_nopermission();
    }

    $news = validate_newsid($_POST[newsid]);

    $DB->query("DELETE FROM ".$db_prefix."news WHERE newsid='$news[newsid]'");
    redirect("news.php?action=edit","��������ɾ��");

}

if ($_GET[action]==edit) {

    if (empty($permission[caneditnews])) {
        show_nopermission();
    }
    
    $results = $DB->query("SELECT news.*,user.username,user.userid FROM ".$db_prefix."news AS news
                                  LEFT JOIN ".$db_prefix."user AS user
                                       ON news.userid=user.userid
                                  ORDER BY startdate DESC,newsid");
    if ($DB->num_rows($results)==0) {
        pa_exit("��û���κ�����");
    }

    $cpforms->tableheader();
    echo "<tr class=\"tbhead\" align=center>
              <td width=\"5%\">id#</td>
              <td width=\"50%\">���ű���</td>
              <td nowrap>������</td>
              <td nowrap>����ʱ��</td>
              <td nowrap>����ʱ��</td>
              <td nowrap>�༭</td>
          </tr>";


    while ($news = $DB->fetch_array($results)) {
           echo "<tr class=".getrowbg().">
                  <td align=center>$news[newsid]</td>
                  <td>".htmlspecialchars($news[title])."</td>
                  <td align=center>".htmlspecialchars($news[username])."</td>
                  <td nowrap>".date("Y-m-d H:i:s",$news[startdate])."</td>
                  <td nowrap>".date("Y-m-d H:i:s",$news[enddate])."</td>
                  <td nowrap>[<a href=\"news.php?action=mod&newsid=$news[newsid]\">�༭</a>] [<a href=\"news.php?action=kill&newsid=$news[newsid]\">ɾ��</a>]</td>
                 </tr>";
    }

    $cpforms->tablefooter();


}

cpfooter();
?>