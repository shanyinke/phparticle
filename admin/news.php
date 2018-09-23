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
                  alert('请输入标题.');
                  document.news.title.focus();
                  return false;
               }

               if(document.news.content.value == ''){
                  alert('请输入内容.');
                  return false;
               }

               return true;
      }
</script>";

}

cpheader($header);


if ($_GET[action]==add) {

    $cpforms->inithtmlarea();

    $cpforms->formheader(array('title'=>'发布新闻,时间格式:(Y-m-d),新闻只会在发布时间与结束时间这段时间内才会显示',
                                'name'=>'news',
                                'extra'=>"onsubmit=\"return ProcessNews()\""));
    $cpforms->makeinput(array('text'=>'新闻标题:<br><b>可以使用html代码</b>',
                               'name'=>'title'));
    $cpforms->makeinput(array('text'=>'发布时间:',
                               'name'=>'startdate',
                               'value'=>date("Y-m-d H:i:s")
                               ));
    $cpforms->makeinput(array('text'=>'结束时间:',
                               'name'=>'enddate',
                               'value'=>date("Y-m-d H:i:s",mktime(date("H"),date("i"),date("s"),date("m")+1,date("d"),date("Y")))
                               ));

    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'insert'));
    $cpforms->makehtmlarea(array('text'=>"内容:",
                                 'name'=>'content'));

    $cpforms->formfooter();

}

if ($_POST[action]==insert) {

    $title = trim($_POST[title]);
    if ($title=="" OR trim($_POST[content])=="") {
        pa_exit("请返回并输入标题与内容");
    }
    $DB->query("INSERT INTO ".$db_prefix."news (userid,title,content,startdate,enddate)
                       VALUES ('$pauserinfo[userid]','".addslashes($title)."','".addslashes($_POST[content])."',UNIX_TIMESTAMP('$_POST[startdate]'),UNIX_TIMESTAMP('$_POST[enddate]'))");

    redirect("./news.php?action=edit","该新闻已发布");

}

function validate_newsid($newsid) {

         global $DB,$db_prefix;
         $newsid = intval($newsid);

         $news = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."news WHERE newsid='$newsid'");
         if (empty($news)) {
             pa_exit("该新闻不存在");
         }
         return $news;

}
if ($_GET[action]==mod) {


    $news = validate_newsid($_GET[newsid]);

    $cpforms->inithtmlarea();

    $cpforms->formheader(array('title'=>'编辑新闻,时间格式:(Y-m-d H:i:s),新闻只会在发布时间与结束时间这段时间内才会显示',
                                'name'=>'news',
                                'extra'=>"onsubmit=\"return ProcessNews()\""));

    $cpforms->makeinput(array('text'=>'新闻标题:<br><b>可以使用html代码</b>',
                               'name'=>'title',
                               'value'=>$news[title]));

    $cpforms->makeinput(array('text'=>'发布时间:',
                               'name'=>'startdate',
                               'value'=>date("Y-m-d H:i:s",$news[startdate])
                               ));
    $cpforms->makeinput(array('text'=>'结束时间:',
                               'name'=>'enddate',
                               'value'=>date("Y-m-d H:i:s",$news[enddate])
                               ));

    $cpforms->makehidden(array('name'=>'newsid',
                                'value'=>$news[newsid]));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'update'));

    $cpforms->makehtmlarea(array('text'=>'内容:',
                                 'name'=>'content',
                                 'value'=>$news[content]));

    $cpforms->formfooter();

}


if ($_POST[action]==update) {

    $news = validate_newsid($_POST[newsid]);

    $title = trim($_POST[title]);
    if (empty($title) OR empty($_POST[content])) {
        pa_exit("请返回并输入标题与内容");
    }
    $DB->query("UPDATE ".$db_prefix."news SET
                       userid='$pauserinfo[userid]',
                       title='".addslashes($title)."',
                       content='".addslashes($_POST[content])."',
                       startdate=UNIX_TIMESTAMP('$_POST[startdate]'),
                       enddate=UNIX_TIMESTAMP('$_POST[enddate]')
                       WHERE newsid='$_POST[newsid]'
                       ");
    redirect("news.php?action=edit","该新闻已更新");

}

if ($_GET[action]==kill){


    $news = validate_newsid($_GET[newsid]);

    $cpforms->formheader(array('title'=>'确实要删除该新闻?'));
    $cpforms->makehidden(array('name'=>'newsid',
                                'value'=>$news[newsid]));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'remove'));
    $cpforms->formfooter(array('confirm'=>1));

}


if ($_POST[action]==remove) {

    $news = validate_newsid($_POST[newsid]);

    $DB->query("DELETE FROM ".$db_prefix."news WHERE newsid='$news[newsid]'");
    redirect("news.php?action=edit","该新闻已删除");

}

if ($_GET[action]==edit) {

    $results = $DB->query("SELECT news.*,user.username,user.userid FROM ".$db_prefix."news AS news
                                  LEFT JOIN ".$db_prefix."user AS user
                                       ON news.userid=user.userid
                                  ORDER BY startdate DESC,newsid");
    if ($DB->num_rows($results)==0) {
        pa_exit("还没有任何新闻");
    }

    $cpforms->tableheader();
    echo "<tr class=\"tbhead\" align=center>
              <td width=\"5%\">id#</td>
              <td width=\"50%\">新闻标题</td>
              <td nowrap>发布者</td>
              <td nowrap>发布时间</td>
              <td nowrap>结束时间</td>
              <td nowrap>编辑</td>
          </tr>";


    while ($news = $DB->fetch_array($results)) {
           echo "<tr class=".getrowbg().">
                  <td align=center>$news[newsid]</td>
                  <td>".htmlspecialchars($news[title])."</td>
                  <td align=center>".htmlspecialchars($news[username])."</td>
                  <td nowrap>".date("Y-m-d H:i:s",$news[startdate])."</td>
                  <td nowrap>".date("Y-m-d H:i:s",$news[enddate])."</td>
                  <td nowrap>[<a href=\"news.php?action=mod&newsid=$news[newsid]\">编辑</a>] [<a href=\"news.php?action=kill&newsid=$news[newsid]\">删除</a>]</td>
                 </tr>";
    }

    $cpforms->tablefooter();

}

cpfooter();
?>