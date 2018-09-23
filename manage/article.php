<?php
error_reporting(7);
require "global.php";

$cachesorts = cachesorts();
if ($_GET[action]=="add" OR $_GET[action]=="mod" OR $_GET[action]=="nextpage" OR $_GET[action]=="editcontent"){
?>
<script type="text/javascript">

      function ProcessArticle(){

               if(document.article.title.value == ''){
                  alert('���������.');
                  document.article.title.focus();
                  return false;
               }

               if(document.article.sortid.value == '-1'){
                  alert('��ѡ�����.');
                  document.article.sortid.focus();
                  return false;
               }


               if(document.article.articletext.value == ''){
                  alert('����������.');
                  return false;
               }

               return true;
      }



      function ProcessNextArticle(){

               if(document.article.subhead.value == ''){
                  alert('������С����.');
                  document.article.subhead.focus();
                  return false;
               }

               if(document.article.articletext.value == ''){
                  alert('����������.');
                  return false;
               }

               return true;
      }
</script>
<?php
}
cpheader();

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



function listsort($sortid="-1"){

         global $DB,$db_prefix,$cachesorts;
         global $filter_sort_array;

         if (isset($cachesorts[$sortid])) {

             foreach ($cachesorts[$sortid] as $key => $sort){
                      echo "<ul>";
                      //if (!isset($filter_sort_array[$sort[sortid]])) {

                      echo "<li>\n<b><a href=../sort.php/$sort[sortid] target=_blank>$sort[title]</a></b>";
                      if (!isset($filter_sort_array[$sort[sortid]])) {
                           echo "
                                 [<a href=article.php?action=edit&sortid=$sort[sortid]>�鿴����</a>]
                                 [<a href=article.php?action=add&sortid=$sort[sortid]>�������</a>]
                                 [<a href=sort.php?action=add&sortid=$sort[sortid]>����ӷ���</a>]
                                 [<a href=sort.php?action=mod&sortid=$sort[sortid]>�༭����</a>]
                                 [<a href=sort.php?action=kill&sortid=$sort[sortid]>ɾ������</a>]
                                 [<a href=index2.php?mod=mksort&sortid=$sort[sortid]>��̬����</a>]";

                      }
                      echo "</li>\n";

                      listsort($sort[sortid]);
                      echo "</ul>\n";


             }

         }

}

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


if ($_GET[action]=="add"){

    if (pa_isset($_GET[sortid])) {

        if (!isset($un_filter_sort_array[$sortid])) {
            show_nopermission();
        }

        $cpforms->tableheader();
        echo "<tr class=".getrowbg().">
                 <td>����: <a href=\"./article.php?action=list\">������</a>".buildsortnav($_GET[sortid])."</td>
              </tr>";
        $cpforms->tablefooter();
        echo "<br>";
    }

    $cpforms->inithtmlarea();
    $cpforms->formheader(array('title'=>'���������',
                                'name'=>'article',
                                'extra'=>"onSubmit=\"return ProcessArticle()\"",
                                'enctype'=>'multipart/form-data'));

    $cpforms->makehidden(array('name'=>'action',
                          'value'=>'doinsert'));
    $cpforms->makeinput(array('text'=>'����:',
                               'name'=>'title',
                               'maxlength'=>'100'));

    $cpforms->maketextarea(array('text'=>'����ժҪ:<br>����ʹ��html����',
                                  'name'=>'articledes'));

    $cpforms->makefile(array('text'=>'����ͼ:',
                              'name'=>'image'));

    $cpforms->makeinput(array('text'=>'����:',
                               'name'=>'author'));

    $cpforms->makeinput(array('text'=>'���ߵ�����:',
                               'name'=>'contact'));

    $cpforms->makeinput(array('text'=>'���³�������Դ:',
                               'name'=>'source'));

    //$filter_array = array();

    $cpforms->getsortlist(array('text'=>'����:',
                                 'name'=>'sortid',
                                 'selected'=>$_GET[sortid],
                                 'filter'=>$filter_sort_array));


    $cpforms->makeyesno(array('text'=>'��ʾ?',
                               'name'=>'visible',
                               'selected'=>1));

    $cpforms->maketextarea(array('text'=>'�������:<br>ÿ��Ϊһ���������,��ʽ: text|||url ,���� phpArticle|||http://www.phparticle.cn/article',
                               'name'=>'relatedlink',
                               'cols'=>70,
                               'rows'=>7,
                               )); //'value'=>'��ʽ:<b>text|||http://yourlink</b><br>��һ�����������ı�,�ڶ�����������,�м�ʹ��"|||"�������߸���,����ж���������,�뻻��.ÿ��Ϊһ���������'

    $cpforms->makeinput(array('text'=>'�ؼ���:<br>�������һ��,��ʹ��","�ָ�',
                               'name'=>'keyword',
                               'maxlength'=>100));

    $cpforms->makecategory("��һҳ");
    $cpforms->makeinput(array('text'=>'С����:',
                               'name'=>'subhead'));


    $cpforms->makehtmlarea(array('text'=>'����:',
                                  'name'=>'articletext'));


    $cpforms->formfooter(array('nextpage'=>1));

}

// do insert article
if ($_POST[action]=="doinsert"){


    $title = htmlspecialchars(trim($_POST[title]));
    $articledes = $_POST[articledes];
    $sortid = intval($_POST[sortid]);
    $author = htmlspecialchars(trim($_POST[author]));
    $contact = htmlspecialchars(trim($_POST[contact]));
    $source = htmlspecialchars(trim($_POST[source]));

    $subhead = htmlspecialchars(trim($_POST[subhead]));
    $articletext = $_POST[articletext];


    if (!isset($un_filter_sort_array[$sortid])) {
        show_nopermission();
    }

    if ($title=="") {
        pa_exit("���ⲻ��Ϊ��");
    }
    if ($articletext=="") {
        pa_exit("���ݲ���Ϊ��");
    }

    //print_rr($_FILES);
    //exit;

    if (!empty($_FILES['image']['tmp_name'])) {
        $original = $_FILES['image']['name'];
        $filename = md5(uniqid(microtime(),1));
        if (($_FILES['image']['type']=="image/pjpeg" OR $_FILES['image']['type']=="image/gif" OR $_FILES['image']['type']=="image/x-png") AND copy($_FILES['image']['tmp_name'], "../upload/images/$filename")) {
           $DB->query("INSERT INTO ".$db_prefix."gallery (original,filename,type,size,dateline,userid)
                              VALUES ('".addslashes(trim($original))."','$filename','".addslashes($_FILES['image']['type'])."','".addslashes($_FILES['image']['size'])."','".time()."','$pauserinfo[userid]')");
           $imageid = $DB->insert_id();
        }
    }


    $DB->query("INSERT INTO ".$db_prefix."article (sortid,author,title,contact,source,description,date,imageid,editor,visible,keyword)
                       VALUES ('$sortid','".addslashes($author)."','".addslashes($title)."','".addslashes($contact)."','".addslashes($source)."','".addslashes($articledes)."','".time()."','$imageid','".addslashes($pauserinfo[username])."','".intval($visible)."','".addslashes(htmlspecialchars(trim($_POST[keyword])))."')");

    $articleid = $DB->insert_id();

    $relatedlink = trim($_POST[relatedlink]);

    if (pa_isset($relatedlink)) {
        $relatedlinks = explode("\n",$_POST[relatedlink]);
        if (!empty($relatedlinks)) {
            $count  = count($relatedlinks);
            for ($i=0;$i<$count;$i++) {
                 if (!pa_isset($relatedlinks[$i])) {
                     continue;
                 }
                 $links = explode("|||",$relatedlinks[$i]);
                 //print_rr($links);
                 if (pa_isset($links[0]) AND pa_isset($links[1])) {
                     $DB->query("INSERT INTO ".$db_prefix."relatedlink (articleid,text,link) VALUES
                                        ('$articleid','".addslashes(htmlspecialchars(trim($links[0])))."','".addslashes(htmlspecialchars(trim($links[1])))."')");
                 }
            }
        }
    }



    if ($subhead=="") {
        $subhead = $title;
    }
    $DB->query("INSERT INTO ".$db_prefix."articletext (articleid,subhead,articletext)
                       VALUES ('$articleid','".addslashes($subhead)."','".addslashes($articletext)."')");

    $DB->query("UPDATE ".$db_prefix."sort SET articlecount=articlecount+1 WHERE sortid IN (".getparentsorts($sortid).")");

    if ($_POST[nextpage]){
        redirect("./article.php?action=nextpage&articleid=$articleid","���������һҳ");
    } else {
        redirect("./article.php?action=edit&sortid=$sortid","�����������");
    }
}


if ($_GET[action]=="nextpage"){

    $article = validate_articleid($_GET[articleid]);


    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }

    if (!empty($article[sortid])) {
        $cpforms->tableheader();
        echo "<tr class=".getrowbg().">
                 <td>����: <a href=\"./article.php?action=list\">������</a>".buildsortnav($article[sortid])." \ <a href=\"article.php?action=mod&articleid=$article[articleid]\">$article[title]</a></td>
              </tr>";
        $cpforms->tablefooter();
        echo "<br>";
    }

    $cpforms->inithtmlarea();
    $cpforms->formheader(array('title'=>'���������һҳ',
                                'extra'=>"onSubmit=\"return ProcessNextArticle()\"",
                                'name'=>'article'));

    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'insertnextpage'));

    $cpforms->makehidden(array('name'=>'articleid',
                                'value'=>$article[articleid]));
    $cpforms->makeinput(array('text'=>'С����:',
                               'name'=>'subhead',
                               'maxlength'=>'100'));

    $cpforms->makehtmlarea(array('text'=>'��������:',
                                  'name'=>'articletext'));
    $order = $DB->fetch_one_array("SELECT MAX(displayorder) AS maxdisplayorder FROM ".$db_prefix."articletext
                                          WHERE articleid='$article[articleid]'");

    $cpforms->makeorderinput(array('text'=>'����:',
                               'name'=>'displayorder',
                               'value'=>($order[maxdisplayorder]+1)));
    $cpforms->formfooter(array('nextpage'=>1));

}



if ($_POST[action]=="insertnextpage"){

    $articleid = intval($_POST[articleid]);
    $displayorder = intval($_POST[displayorder]);
    $article = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."article WHERE articleid='$articleid'");

    if (empty($article)) {
        pa_exit("�����²�����");
    }

    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }


    $subhead = htmlspecialchars(trim($_POST[subhead]));
    $articletext = $_POST[articletext];


    if ($subhead=="") {
        pa_exit("С���ⲻ��Ϊ��");
    }
    if ($articletext=="") {
        pa_exit("���ݲ���Ϊ��");
    }

    $DB->query("INSERT INTO ".$db_prefix."articletext (articleid,subhead,articletext,displayorder)
                       VALUES ('$articleid','".addslashes($subhead)."','".addslashes($articletext)."','$displayorder')");

    if (isset($_POST[nextpage])){
        redirect("./article.php?action=nextpage&articleid=$article[articleid]","���������һҳ");
    } else {
        redirect("./article.php?action=mod&articleid=$article[articleid]","�����������");
    }

}


if ($_GET[action]=="mod"){

    $article = validate_articleid($_GET[articleid]);
    $articleid = intval($_GET[articleid]);


    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }


    if (!empty($article[sortid])) {
        $cpforms->tableheader();
        echo "<tr class=".getrowbg().">
                 <td>����: <a href=\"./article.php?action=list\">������</a>".buildsortnav($article[sortid])." \ <a href=\"article.php?action=mod&articleid=$article[articleid]\">$article[title]</a></td>
              </tr>";
        $cpforms->tablefooter();
        echo "<br>";
    }

    //formheader("article.php",0);
    $cpforms->formheader(array('title'=>"�༭����: $article[title]",
                                'enctype'=>'multipart/form-data'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'update'));
    $cpforms->makehidden(array('name'=>'articleid',
                                'value'=>$article[articleid]));
    $cpforms->makeinput(array('text'=>'����:',
                               'name'=>'title',
                               'value'=>$article[title],
                               'maxlength'=>100));

    $cpforms->maketextarea(array('text'=>'����ժҪ:<br>����ʹ��html����',
                                  'name'=>'articledes',
                                  'value'=>$article[description],
                                  'html'=>1));

    if (!empty($article[imageid])) {
        $cpforms->makeselect(array('text'=>"����ͼ:<img src=\"../showimg.php?iid=$article[imageid]\" border=\"0\" align=\"right\"><br><br>[<a href=\"gallery.php?action=edit&imageid=$article[imageid]\">�༭</a>]",
                                      'name'=>'what',
                                      'option'=>array('keep'=>'����',
                                                       'upload'=>'�ϴ��µ�',
                                                       'delete'=>'ɾ��')));
    } else {
        $cpforms->makehidden(array('name'=>'what',
                                    'value'=>'upload'));
    }
    $cpforms->makefile(array('text'=>'�ϴ�����ͼ:',
                              'name'=>'image'));

    $cpforms->makeinput(array('text'=>'����:',
                               'name'=>'author',
                               'value'=>$article[author]));
    $cpforms->makeinput(array('text'=>'���ߵ�����:',
                               'name'=>'contact',
                               'value'=>$article[contact]));
    $cpforms->makeinput(array('text'=>'���³���,��Դ,��վ��ַ:',
                               'name'=>'source',
                               'value'=>$article[source]));


    $cpforms->getsortlist(array('text'=>'����:',
                                 'name'=>'sortid',
                                 'selected'=>$article[sortid]));

    $cpforms->makeyesno(array('text'=>'��ʾ?',
                               'name'=>'visible',
                               'selected'=>$article[visible]));

    $relatedlinks = $DB->query("SELECT * FROM ".$db_prefix."relatedlink WHERE articleid='$article[articleid]'");
    unset($links);
    unset($link);
    if ($DB->num_rows($relatedlinks)) {
        while ($relatedlink = $DB->fetch_array($relatedlinks)) {
               $link[] = $relatedlink[text]."|||".$relatedlink[link];
        }
        $links = implode("\n",$link);
    }
    $cpforms->maketextarea(array('text'=>'�������:<br>ÿ��Ϊһ���������,��ʽ: text|||url ,���� phpArticle|||http://www.phparticle.cn/article',
                                  'name'=>'relatedlink',
                                  'value'=>$links,
                                  'cols'=>70,
                                  'rows'=>7));

    $cpforms->makeinput(array('text'=>'�ؼ���:<br>�������һ��,��ʹ��","�ָ�',
                               'name'=>'keyword',
                               'value'=>$article[keyword],
                               'maxlength'=>100));

    $cpforms->formfooter();

    echo "<br>";

    $contents = $DB->query("SELECT * FROM ".$db_prefix."articletext
                                     WHERE articleid='$articleid'
                                     ORDER BY displayorder,id");
    $pagenum = 1;

    $cpforms->tableheader();
    if ($DB->num_rows($contents)==0) {
        echo "<tr class=".getrowbg().">
                  <td align=\"center\">��δ���κ�����</td>
              </tr>";
    } else {
        echo "<form action=\"article.php\" method=\"post\">
                     <tr align=\"center\" class=\"tbhead\">
                      <!-- <td>id</td> -->
                      <td nowrap>����</td>
                      <td width=\"90%\" align=\"left\">С����</td>
                      <td nowrap>�༭</td>
                      <td nowrap>ɾ��</td>
                     </tr>";

        while($content = $DB->fetch_array($contents)){
              echo "<tr class=".getrowbg().">
                           <!-- <td>$content[id]</td> -->
                           <td align=\"center\"><input class=order type=text value=\"$content[displayorder]\" name=\"displayorder[$content[id]]\" maxlength=\"3\"></td>
                           <td><a href=../article.php?articleid=$content[articleid]&pagenum=$pagenum target=\"_ablank\">�� ".$pagenum." ҳ</a>: <input type=text name=\"subhead[$content[id]]\" value=\"$content[subhead]\" size=\"70\" maxlength=\"100\"></td>
                           <td nowrap>[<a href=article.php?action=editcontent&id=$content[id]>�༭</a>] [<a href=article.php?articleid=$content[articleid]&action=delcontent&id=$content[id]>ɾ��</a>]</td>
                           <td><input type=\"checkbox\" name=\"articletext[]\" value=\"$content[id]\"></td>
                    </tr>";
              $pagenum++;
        }

        echo "<tr class=\"tbhead\">
                <td align=\"center\" colspan=\"5\">
                <input type=\"hidden\" name=\"action\" value=\"updatecontents\">
                <input type=\"hidden\" name=\"articleid\" value=\"$articleid\">
                <input type=\"submit\" name=\"updatetitleandorder\" value=\"���±���������\" class=\"bginput\"> <input type=\"submit\" name=\"deletecontent\" value=\"ɾ������ѡ�е�ҳ��\" class=\"bginput\">
                </td>
              </tr>";
        echo "</form>";
    }
    $cpforms->tablefooter();

    echo "<br>";

    $cpforms->inithtmlarea();
    $cpforms->formheader(array('title'=>'���������һҳ',
                                'name'=>'article',
                                'extra'=>"onSubmit=\"return ProcessNextArticle()\""));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'insertnextpage'));
    $cpforms->makehidden(array('name'=>'articleid',
                                'value'=>$articleid));

    $cpforms->makeinput(array('text'=>'С����:',
                               'name'=>'subhead',
                               'maxlength'=>'100'));

    $cpforms->makehtmlarea(array('text'=>'��������:',
                                  'name'=>'articletext'));
    $order = $DB->fetch_one_array("SELECT MAX(displayorder) AS maxdisplayorder FROM ".$db_prefix."articletext
                                          WHERE articleid='$articleid'");

    $cpforms->makeorderinput(array('text'=>'����:',
                               'name'=>'displayorder',
                               'value'=>($order[maxdisplayorder]+1)));
    $cpforms->formfooter(array('nextpage'=>1));

}


if ($_POST[action]=="update"){

    $title = htmlspecialchars(trim($_POST[title]));
    $author = htmlspecialchars(trim($_POST[author]));
    $articleid = intval($_POST[articleid]);
    $sortid = intval($_POST[sortid]);
    $source = htmlspecialchars(trim($_POST[source]));


    if (!isset($un_filter_sort_array[$sortid])) {
        show_nopermission();
    }


    if ($title=="") {
        pa_exit("���ⲻ��Ϊ��");
    }

    if (empty($sortid)) {
        pa_exit("��δѡ���κη���");
    }

    $article = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."article WHERE articleid='$articleid'");
    if (empty($article)) {
        pa_exit("�����²�����");
    }

    if ($_POST[what]=="upload") {
        if (!empty($_FILES['image']['tmp_name'])) {
            $original = $_FILES['image']['name'];
            $filename = md5(uniqid(microtime(),1));
            if (($_FILES['image']['type']=="image/pjpeg" OR $_FILES['image']['type']=="image/gif" OR $_FILES['image']['type']=="image/x-png") AND copy($_FILES['image']['tmp_name'], "../upload/images/$filename")) {
               $DB->query("INSERT INTO ".$db_prefix."gallery (original,filename,type,size,dateline,userid)
                                  VALUES ('".addslashes(trim($original))."','$filename','".addslashes($_FILES['image']['type'])."','".addslashes($_FILES['image']['size'])."','".time()."','$pauserinfo[userid]')");

               $imageid = $DB->insert_id();
               $DB->query("UPDATE ".$db_prefix."article SET
                                  imageid='$imageid'
                                  WHERE articleid='$articleid'");
               if (!empty($article[imageid])) {
                   $image = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."gallery WHERE id='$article[imageid]'");
                   unlink("../upload/images/$image[filename]");
                   $DB->query("DELETE FROM ".$db_prefix."gallery WHERE id='$article[imageid]'");
               }
            }
        }
    } else if ($_POST[what]=="delete") {
               $DB->query("UPDATE ".$db_prefix."article SET
                                  imageid='0'
                                  WHERE articleid='$articleid'");

               $image = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."gallery WHERE id='$article[imageid]'");
               unlink("../upload/images/$image[filename]");
               $DB->query("DELETE FROM ".$db_prefix."gallery WHERE id='$article[imageid]'");
    }

    $DB->query("UPDATE ".$db_prefix."sort SET articlecount=articlecount-1 WHERE sortid IN (".getparentsorts($article[sortid]).")");
    $DB->query("UPDATE ".$db_prefix."article SET
                       sortid='$sortid',
                       author='".trim(addslashes($author))."',
                       title='".addslashes($title)."',
                       contact='".addslashes($contact)."',
                       source='".addslashes($source)."',
                       description='".addslashes($_POST[articledes])."',
                       lastupdate='".time()."',
                       editor='".addslashes($pauserinfo[username])."',
                       visible='".intval($_POST[visible])."',
                       keyword='".addslashes(htmlspecialchars(trim($_POST[keyword])))."'
                       WHERE articleid='$articleid'");

    $relatedlink = trim($_POST[relatedlink]);

    $DB->query("DELETE FROM ".$db_prefix."relatedlink WHERE articleid='$articleid'");
    if (pa_isset($relatedlink)) {
        $relatedlinks = explode("\n",$_POST[relatedlink]);
        if (!empty($relatedlinks)) {
            $count  = count($relatedlinks);
            for ($i=0;$i<$count;$i++) {
                 if (!pa_isset($relatedlinks[$i])) {
                     continue;
                 }
                 $links = explode("|||",$relatedlinks[$i]);
                 //print_rr($links);
                 if (pa_isset($links[0]) AND pa_isset($links[1])) {
                     $DB->query("INSERT INTO ".$db_prefix."relatedlink (articleid,text,link) VALUES
                                        ('$articleid','".addslashes(htmlspecialchars(trim($links[0])))."','".addslashes(htmlspecialchars(trim($links[1])))."')");
                 }
            }
        }
    }

    $DB->query("UPDATE ".$db_prefix."sort SET articlecount=articlecount+1 WHERE sortid IN (".getparentsorts($sortid).")");

    redirect("./article.php?action=edit&sortid=$sortid","�������Ѹ���");

}


if ($_POST[action]=="updatecontents") {

    $articleid = intval($_POST[articleid]);
    $article = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."article WHERE articleid='$articleid'");
    $subhead = $_POST[subhead];
    $articletext = $_POST[articletext];


    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }


    if (empty($article)) {
        pa_exit("�����²�����");
    }
    if ($_POST[updatetitleandorder]) {

        foreach ($_POST[displayorder] AS $articletextid=>$order) {
                 $articletextid = intval($articletextid);
                 $order = intval($order);
                 $subhead[$articletextid] = htmlspecialchars(trim($subhead[$articletextid]));
                 $DB->query("UPDATE ".$db_prefix."articletext SET
                                    displayorder='$order',
                                    subhead='".addslashes($subhead[$articletextid])."'
                                    WHERE id='$articletextid'");
        }
        redirect("./article.php?action=mod&articleid=$articleid","�������Ѹ���");

    } elseif($_POST[deletecontent]) {

        if (!is_array($articletext)) {
            pa_exit("��δѡ���κ�Ҫɾ������������");
        }
        $cpforms->formheader(array('title'=>'ȷʵҪɾ������ѡ�е���������?'));
        $cpforms->makehidden(array('name'=>'action','value'=>'dodeletecontent'));
        $cpforms->makehidden(array('name'=>'articleid','value'=>$articleid));
        foreach ($articletext AS $articletextid) {
                 $cpforms->makehidden(array('name'=>'articletext[]',
                                             'value'=>$articletextid));
        }
        $cpforms->formfooter(array('confirm'=>1));

    }

}


if ($_POST[action]=="updatecontent"){

    $articleid = intval($_POST[articleid]);
    $article = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."article WHERE articleid='$articleid'");

    if (empty($article)) {
        pa_exit("�����²�����");
    }

    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }


    $articletextid = intval($_POST[articletextid]);
    $subhead = htmlspecialchars(trim($_POST[subhead]));
    $displayorder = intval($_POST[displayorder]);
    $articletext = $_POST[articletext];


    if ($subhead=="") {
        pa_exit("С���ⲻ��Ϊ��");
    }
    if ($articletext=="") {
        pa_exit("���ݲ���Ϊ��");
    }

    $DB->query("UPDATE ".$db_prefix."articletext SET
                       subhead='".addslashes($subhead)."',
                       articletext='".addslashes($articletext)."',
                       displayorder='$displayorder'
                       WHERE id='$articletextid'");

    if ($nextpage){
        redirect("./article.php?action=next&articleid=$articleid","���������һҳ");
    } else {
        redirect("./article.php?action=mod&articleid=$articleid","�������Ѹ���");
    }

}


if ($_GET[action]=="editcontent"){

    $id = intval($_GET[id]);
    $content = $DB->fetch_one_array("SELECT article.title,articletext.* FROM ".$db_prefix."articletext AS articletext
                                              LEFT JOIN ".$db_prefix."article AS article
                                                ON articletext.articleid=article.articleid
                                              WHERE id='$id'");
    if (empty($content)) {
        pa_exit("���������ݲ�����");
    }

    $article = validate_articleid($content[articleid]);


    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }

    if (!empty($article[sortid])) {
        $cpforms->tableheader();
        echo "<tr class=".getrowbg().">
                 <td>����: <a href=\"./article.php?action=list\">������</a>".buildsortnav($article[sortid])." \ <a href=\"article.php?action=mod&articleid=$article[articleid]\">$article[title]</a></td>
              </tr>";
        $cpforms->tablefooter();
        echo "<br>";
    }

    $cpforms->inithtmlarea();
    $cpforms->formheader(array('title'=>"�༭��������: $content[subhead]",
                                'name'=>'article',
                                'extra'=>"onSubmit=\"return ProcessNextArticle()\""));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'updatecontent'));
    $cpforms->makehidden(array('name'=>'articleid',
                                'value'=>$content[articleid]));

    $cpforms->makehidden(array('name'=>'articletextid',
                                'value'=>$content[id]));

    $cpforms->makeinput(array('text'=>'С����:',
                               'name'=>'subhead',
                               'value'=>$content[subhead],
                               'maxlength'=>100));

    $cpforms->makehtmlarea(array('text'=>'��������:',
                                  'name'=>'articletext',
                                  'value'=>$content[articletext]));
    $cpforms->makeorderinput(array('text'=>'����:',
                                    'name'=>'displayorder',
                                    'value'=>$content[displayorder]));
    $cpforms->formfooter();

}


if ($_GET[action]=="delcontent"){

    $id = intval($_GET[id]);

    $articletext = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."articletext WHERE id='$id'");
    $article = validate_articleid($articletext[articleid]);
    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }

    $cpforms->formheader(array('title'=>'ȷʵҪɾ����ҳ����?'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'removethis'));
    $cpforms->makehidden(array('name'=>'articletextid',
                                'value'=>$_GET[id]));
    $cpforms->formfooter(array('confirm'=>1));

}



if ($_POST[action]=="removethis"){

    $articletextid = intval($_POST[articletextid]);

    $articletext = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."articletext WHERE id='$articletextid'");

    $article = validate_articleid($articletext[articleid]);
    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }

    if (empty($articletext)) {
        pa_exit("��ҳ���ݲ�����");
    }

    $DB->query("DELETE FROM ".$db_prefix."articletext
                       WHERE id='$articletextid'");

    redirect("./article.php?action=mod&articleid=$articletext[articleid]","��ҳ������ɾ��");

}



if ($_POST[action]=="dodeletecontent") {

    if (!is_array($_POST[articletext])) {
        pa_exit("��δѡ���κ�Ҫɾ������������");
    }

    $articleid = intval($_POST[articleid]);
    $article = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."article WHERE articleid='$articleid'");
    if (empty($article)) {
        pa_exit("�����²�����");
    }

    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }

    foreach($_POST[articletext] AS $articletextid) {
            $DB->query("DELETE FROM ".$db_prefix."articletext WHERE id='$articletextid' AND articleid='$articleid'");
    }

    redirect("article.php?action=mod&articleid=$articleid","����ѡ�е�����������ɾ��");

}


if ($_GET[action]=="kill"){

    $article = validate_articleid($_GET[articleid]);

    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }

    $cpforms->formheader(array('title'=>'ȷʵҪɾ��������?'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'remove'));
    $cpforms->makehidden(array('name'=>'articleid',
                                'value'=>$article[articleid]));

    $cpforms->formfooter(array('confirm'=>1));

}


if ($_POST[action]=="remove"){

    $article = validate_articleid($_POST[articleid]);
    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }

    $DB->query("DELETE FROM ".$db_prefix."favorite WHERE articleid='$article[articleid]'");
    $DB->query("DELETE FROM ".$db_prefix."articletext WHERE articleid=$article[articleid]");
    deletecomments($articleid);

    $DB->query("DELETE FROM ".$db_prefix."article WHERE articleid=$articleid");

    $DB->query("UPDATE ".$db_prefix."sort SET articlecount=articlecount-1 WHERE sortid IN (".getparentsorts($article[sortid]).")");

    redirect("./article.php?action=edit&sortid=$article[sortid]","��������ɾ��");

}


if ($_GET[action]=="list"){

    $cpforms->tableheader();
    $cpforms->makecategory("�����б�");
    echo "<tr class=".getrowbg().">
            <td>";
    echo "<br>";
    listsort();
    echo "  </td>
          </tr>";
    $cpforms->tablefooter();

}


if ($_GET[action]=="edit"){

    $sortid = intval($_GET[sortid]);


    if (!isset($un_filter_sort_array[$sortid])) {
        show_nopermission();
    }


    $ssortid="WHERE sortid=".$sortid;


    $cpforms->tableheader();
    echo "<tr class=".getrowbg().">
              <td>����: <a href=\"./article.php?action=list\">������</a>".buildsortnav($sortid)."</td>
              <td align=\"right\"><a href=\"./article.php?action=add&sortid=$sortid\">�������</a></td>
          </tr>";
    $cpforms->tablefooter();


    $nav = new buildNav;

    $total = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."article AS article $ssortid");

    $nav->total_result = $total[count];

    if (empty($total[count])) {
        pa_exit("��û���κ�����");
    }

    $nav->execute("SELECT * FROM ".$db_prefix."article AS article $ssortid ORDER BY articleid DESC");

    echo $nav->pagenav();

    echo "<table boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\" class=\"tableoutline\">
               <tr align=\"center\" class=\"tbhead\">
                <td> id# </td>
                <td width=\"60%\"> ���� </td>
                <td nowrap> ���� </td>
                <td nowrap> ���� </td>
                <td nowrap> ��� </td>
                <td nowrap> ���� </td>
                <td nowrap> ��ʾ </td>
                <td nowrap> �༭ </td>
               </tr>\n";
    while ($article = $DB->fetch_array($nav->sql_result)){
           if (!empty($article[imageid])) {
               $article[img] = "<img src=\"../images/image.gif\" border=\"0\" align=\"absmiddle\" alt=\"������ͼ\">";
           }
           if ($article[visible]) {
               $article[visible] = "��ʾ";
           } else {
               $article[visible] = "<font color=\"red\">����</a>";
           }
           echo "<tr class=".getrowbg().">
                      <td align=\"center\" nowrap> $article[articleid]</td>
                      <td>$article[img] <a target=_blank href=\"../article.php?articleid=$article[articleid]\">$article[title]</a></td>
                      <td align=\"center\" nowrap>$article[author]</td>
                      <td align=\"center\" nowrap>".date("Y-m-d H:i:s",$article[date])."</td>
                      <td align=\"center\" nowrap>$article[views]</td>
                      <td align=\"center\" nowrap><a href=\"comment.php?action=edit&articleid=$article[articleid]\">$article[comments]</a></td>
                      <td align=\"center\" nowrap>$article[visible]</td>
                      <td align=\"center\" nowrap> [<a href=index2.php?mod=mkarticle&articleid=$article[articleid]>��̬����</a>] [<a href=\"article.php?action=mod&articleid=$article[articleid]\">�༭</a>] [<a href=\"article.php?action=kill&articleid=$article[articleid]\">ɾ��</a>]</td>
                     </tr>\n";

    }
    echo "</table>\n";

    echo $nav->pagenav();

}


if ($_GET[action]=="validate"){

    $cpforms->tableheader();
    echo "<tr class=".getrowbg().">
            <td>����: <a href=\"./article.php?action=list\">������</a>".buildsortnav($sortid)."</td>
            <td align=\"right\"><a href=\"./article.php?action=add&sortid=$sortid\">�������</a></td>
          </tr>";
    $cpforms->tablefooter();

    $nav = new buildNav;

    $man_sortids = implode(",",array_flip($un_filter_sort_array));


    $total = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."article AS article
                                          WHERE visible=0
                                          AND sortid IN (0$man_sortids)");

    $nav->total_result = $total[count];

    if (empty($total[count])) {
        pa_exit("��û���κ�����");
    }

    $nav->execute("SELECT * FROM ".$db_prefix."article AS article 
						WHERE visible=0 
						AND sortid IN (0$man_sortids) 
						ORDER BY articleid DESC"); 

    echo $nav->pagenav();

    echo "<table boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\" class=\"tableoutline\">
               <tr align=\"center\" class=\"tbhead\">
                <td> id# </td>
                <td width=\"60%\"> ���� </td>
                <td nowrap> ���� </td>
                <td nowrap> ���� </td>
                <td nowrap> ��� </td>
                <td nowrap> ��ʾ </td>
                <td nowrap> �༭ </td>
               </tr>\n";
    while ($article = $DB->fetch_array($nav->sql_result)){
           if (!empty($article[imageid])) {
               $article[img] = "<img src=\"../images/image.gif\" border=\"0\" align=\"absmiddle\" alt=\"������ͼ\">";
           }
           if ($article[visible]) {
               $article[visible] = "��ʾ";
           } else {
               $article[visible] = "<font color=\"red\">����</a>";
           }
           echo "<tr class=".getrowbg().">
                      <td align=\"center\" nowrap> $article[articleid]</td>
                      <td>$article[img] <a target=_blank href=\"../article.php?articleid=$article[articleid]\">$article[title]</a></td>
                      <td align=\"center\" nowrap>$article[author]</td>
                      <td align=\"center\" nowrap>".date("Y-m-d H:i:s",$article[date])."</td>
                      <td align=\"center\" nowrap>$article[views]</td>
                      <td align=\"center\" nowrap>$article[visible]</td>
                      <td align=\"center\" nowrap>
                       [<a href=\"article.php?action=dovalidate&articleid=$article[articleid]\">ͨ������</a>]
                       [<a href=\"article.php?action=mod&articleid=$article[articleid]\">�༭</a>]
                       [<a href=\"article.php?action=kill&articleid=$article[articleid]\">ɾ��</a>]
                      </td>
                     </tr>\n";

    }
    echo "</table>\n";

    echo $nav->pagenav();

}

if ($_GET[action]=="dovalidate") {

    $articleid = intval($_GET[articleid]);
    $article = validate_articleid($articleid);

    if (!isset($un_filter_sort_array[$article[sortid]])) {
        show_nopermission();
    }

    $DB->query("UPDATE ".$db_prefix."article SET
                       visible=1
                       WHERE articleid='$articleid'");
    redirect("./article.php?action=validate","��������ͨ������");

}

cpfooter();
?>