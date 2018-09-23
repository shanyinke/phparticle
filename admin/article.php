<?php
error_reporting(7);
require "global.php";

$cachesorts = cachesorts();
if ($_GET[action]=="add" OR $_GET[action]=="mod" OR $_GET[action]=="nextpage" OR $_GET[action]=="editcontent"){
?>
<script type="text/javascript">

      function ProcessArticle(){

               if(document.article.title.value == ''){
                  alert('请输入标题.');
                  document.article.title.focus();
                  return false;
               }

               if(document.article.sortid.value == '-1'){
                  alert('请选择分类.');
                  document.article.sortid.focus();
                  return false;
               }


               if(document.article.articletext.value == ''){
                  alert('请输入内容.');
                  return false;
               }

               return true;
      }



      function ProcessNextArticle(){

               if(document.article.subhead.value == ''){
                  alert('请输入小标题.');
                  document.article.subhead.focus();
                  return false;
               }

               if(document.article.articletext.value == ''){
                  alert('请输入内容.');
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
                      //$stitle = htmlspecialchars($stitle);
                      $navbit = " \\ <a href=\"./article.php?action=edit&sortid=$sortid\">$stitle</a>";
                      $navbit = buildsortnav($psid).$navbit;
             }
         }
         return $navbit;

}




function listsort($sortid="-1"){

         global $DB,$db_prefix,$cachesorts;

         if (isset($cachesorts[$sortid])) {

             foreach ($cachesorts[$sortid] as $key => $sort){

                      echo "<ul>";
                      echo "<li>\n<b><a href=../sort.php/$sort[sortid] target=_blank>$sort[title]</a></b>
                             [<a href=article.php?action=edit&sortid=$sort[sortid]>查看文章</a>]
                             [<a href=article.php?action=add&sortid=$sort[sortid]>添加文章</a>]
                             [<a href=article.php?action=massmove&sortid=$sort[sortid]>批量移动</a>]
                             [<a href=article.php?action=massdelete&sortid=$sort[sortid]>批量删除</a>]
                             [<a href=../htmlauto.php?mod=mksort&sortid=$sort[sortid]>静态生成</a>]
                           </li>\n";
                     listsort($sort[sortid]);
                     echo "</ul>\n";

             }

         }

}



if ($_GET[action]=="add"){

    if (!empty($_GET[sortid])) {
        $cpforms->tableheader();
        echo "<tr class=".getrowbg().">
                 <td>导航: <a href=\"./article.php?action=list\">根分类</a>".buildsortnav($_GET[sortid])."</td>
              </tr>";
        $cpforms->tablefooter();
        echo "<br>";
    }

    $cpforms->inithtmlarea();
    $cpforms->formheader(array('title'=>'添加新文章',
                                'name'=>'article',
                                'extra'=>"onSubmit=\"return ProcessArticle()\"",
                                'enctype'=>'multipart/form-data'));

    $cpforms->makehidden(array('name'=>'action',
                          'value'=>'doinsert'));
    $cpforms->makeinput(array('text'=>'标题:',
                               'name'=>'title',
                               'maxlength'=>'100'));

    $cpforms->maketextarea(array('text'=>'文章摘要:<br>可以使用html代码',
                                  'name'=>'articledes'));

    $cpforms->makefile(array('text'=>'缩略图:',
                              'name'=>'image'));

    $cpforms->makeinput(array('text'=>'作者:',
                               'name'=>'author'));

    $cpforms->makeinput(array('text'=>'作者的邮箱:',
                               'name'=>'contact'));

    $cpforms->makeinput(array('text'=>'文章出处或来源:',
                               'name'=>'source'));

    $cpforms->getsortlist(array('text'=>'分类:',
                                 'name'=>'sortid',
                                 'selected'=>$_GET[sortid]));

    $cpforms->makeyesno(array('text'=>'显示?',
                               'name'=>'visible',
                               'selected'=>1));

    $cpforms->maketextarea(array('text'=>'相关链接:<br>每行为一个相关链接,格式: text|||url ,例如 phpArticle|||http://www.phparticle.cn/article',
                               'name'=>'relatedlink',
                               'cols'=>70,
                               'rows'=>7,
                               ));

    $cpforms->makeinput(array('text'=>'关键字:<br>如果超过一个,请使用","分隔',
                               'name'=>'keyword',
                               'maxlength'=>100));

    $cpforms->makecategory("第一页");
    $cpforms->makeinput(array('text'=>'小标题:',
                               'name'=>'subhead'));


    $cpforms->makehtmlarea(array('text'=>'内容:',
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

    if ($title=="") {
        pa_exit("标题不能为空");
    }
    if ($articletext=="") {
        pa_exit("内容不能为空");
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


    $DB->query("INSERT INTO ".$db_prefix."article (sortid,author,title,contact,source,description,date,imageid,editor,visible,keyword,userid)
                       VALUES ('$sortid','".addslashes($author)."','".addslashes($title)."','".addslashes($contact)."','".addslashes($source)."','".addslashes($articledes)."','".time()."','".intval($imageid)."','".addslashes($pauserinfo[username])."','".intval($visible)."','".addslashes(htmlspecialchars(trim($_POST[keyword])))."','$pauserinfo[userid]')");

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

    if ($_POST['nextpage']){
        redirect("./article.php?action=nextpage&articleid=$articleid","继续添加下一页");
    } else {
        redirect("./article.php?action=edit&sortid=$sortid","该文章已添加");
    }
    resetcache();
    resettag($sortid);
}


if ($_GET[action]=="nextpage"){

    $article = validate_articleid($_GET[articleid]);

    if (!empty($article[sortid])) {
        $cpforms->tableheader();
        echo "<tr class=".getrowbg().">
                 <td>导航: <a href=\"./article.php?action=list\">根分类</a>".buildsortnav($article[sortid])." \ <a href=\"article.php?action=mod&articleid=$article[articleid]\">$article[title]</a></td>
              </tr>";
        $cpforms->tablefooter();
        echo "<br>";
    }

    $cpforms->inithtmlarea();
    $cpforms->formheader(array('title'=>'继续添加下一页',
                                'extra'=>"onSubmit=\"return ProcessNextArticle()\"",
                                'name'=>'article'));

    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'insertnextpage'));

    $cpforms->makehidden(array('name'=>'articleid',
                                'value'=>$article[articleid]));
    $cpforms->makeinput(array('text'=>'小标题:',
                               'name'=>'subhead',
                               'maxlength'=>'100'));

    $cpforms->makehtmlarea(array('text'=>'文章内容:',
                                  'name'=>'articletext'));
    $order = $DB->fetch_one_array("SELECT MAX(displayorder) AS maxdisplayorder FROM ".$db_prefix."articletext
                                          WHERE articleid='$article[articleid]'");

    $cpforms->makeorderinput(array('text'=>'排序:',
                               'name'=>'displayorder',
                               'value'=>($order[maxdisplayorder]+1)));
    $cpforms->formfooter(array('nextpage'=>1));

}



if ($_POST[action]=="insertnextpage"){

    $articleid = intval($_POST[articleid]);
    $displayorder = intval($_POST[displayorder]);
    $article = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."article WHERE articleid='$articleid'");

    if (empty($article)) {
        pa_exit("该文章不存在");
    }

    $subhead = htmlspecialchars(trim($_POST[subhead]));
    $articletext = $_POST[articletext];


    if ($subhead=="") {
        pa_exit("小标题不能为空");
    }
    if ($articletext=="") {
        pa_exit("内容不能为空");
    }

    $DB->query("INSERT INTO ".$db_prefix."articletext (articleid,subhead,articletext,displayorder)
                       VALUES ('$articleid','".addslashes($subhead)."','".addslashes($articletext)."','$displayorder')");

    if (isset($_POST[nextpage])){
        redirect("./article.php?action=nextpage&articleid=$article[articleid]","继续添加下一页");
    } else {
        redirect("./article.php?action=mod&articleid=$article[articleid]","该文章已添加");
    }

}


if ($_GET[action]=="mod"){

    $article = validate_articleid($_GET[articleid]);
    $articleid = intval($_GET[articleid]);

    if (!empty($article[sortid])) {
        $cpforms->tableheader();
        echo "<tr class=".getrowbg().">
                 <td>导航: <a href=\"./article.php?action=list\">根分类</a>".buildsortnav($article[sortid])." \ <a href=\"article.php?action=mod&articleid=$article[articleid]\">$article[title]</a></td>
              </tr>";
        $cpforms->tablefooter();
        echo "<br>";
    }

    //formheader("article.php",0);
    $cpforms->formheader(array('title'=>"编辑文章: $article[title]",
                                'enctype'=>'multipart/form-data'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'update'));
    $cpforms->makehidden(array('name'=>'articleid',
                                'value'=>$article[articleid]));
    $cpforms->makeinput(array('text'=>'标题:',
                               'name'=>'title',
                               'value'=>$article[title],
                               'maxlength'=>100));

    $cpforms->maketextarea(array('text'=>'文章摘要:<br>可以使用html代码',
                                  'name'=>'articledes',
                                  'value'=>$article[description],
                                  'html'=>1));

    if (!empty($article[imageid])) {
        $cpforms->makeselect(array('text'=>"缩略图:<img src=\"../showimg.php?iid=$article[imageid]\" border=\"0\" align=\"right\"><br><br>[<a href=\"gallery.php?action=edit&imageid=$article[imageid]\">编辑</a>]",
                                      'name'=>'what',
                                      'option'=>array('keep'=>'保留',
                                                       'upload'=>'上传新的',
                                                       'delete'=>'删除')));
    } else {
        $cpforms->makehidden(array('name'=>'what',
                                    'value'=>'upload'));
    }
    $cpforms->makefile(array('text'=>'上传缩略图:',
                              'name'=>'image'));

    $cpforms->makeinput(array('text'=>'作者:',
                               'name'=>'author',
                               'value'=>$article[author]));
    $cpforms->makeinput(array('text'=>'作者的邮箱:',
                               'name'=>'contact',
                               'value'=>$article[contact]));
    $cpforms->makeinput(array('text'=>'文章出处,来源,网站地址:',
                               'name'=>'source',
                               'value'=>$article[source]));


    $cpforms->getsortlist(array('text'=>'分类:',
                                 'name'=>'sortid',
                                 'selected'=>$article[sortid]));

    $cpforms->makeyesno(array('text'=>'显示?',
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
    $cpforms->maketextarea(array('text'=>'相关链接:<br>每行为一个相关链接,格式: text|||url ,例如 phpArticle|||http://www.phparticle.cn/article',
                                  'name'=>'relatedlink',
                                  'value'=>$links,
                                  'cols'=>70,
                                  'rows'=>7));

    $cpforms->makeinput(array('text'=>'关键字:<br>如果超过一个,请使用","分隔',
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
                  <td align=\"center\">还未有任何内容</td>
              </tr>";
    } else {
        echo "<form action=\"article.php\" method=\"post\">
                     <tr align=\"center\" class=\"tbhead\">
                      <!-- <td>id</td> -->
                      <td nowrap>排序</td>
                      <td width=\"90%\" align=\"left\">小标题</td>
                      <td nowrap>编辑</td>
                      <td nowrap>删除</td>
                     </tr>";

        while($content = $DB->fetch_array($contents)){
              echo "<tr class=".getrowbg().">
                           <!-- <td>$content[id]</td> -->
                           <td align=\"center\"><input class=order type=text value=\"$content[displayorder]\" name=\"displayorder[$content[id]]\" maxlength=\"3\"></td>
                           <td><a href=../article.php?articleid=$content[articleid]&pagenum=$pagenum target=\"_ablank\">第 ".$pagenum." 页</a>: <input type=text name=\"subhead[$content[id]]\" value=\"$content[subhead]\" size=\"70\" maxlength=\"100\"></td>
                           <td nowrap>[<a href=article.php?action=editcontent&id=$content[id]>编辑</a>] [<a href=article.php?articleid=$content[articleid]&action=delcontent&id=$content[id]>删除</a>]</td>
                           <td><input type=\"checkbox\" name=\"articletext[]\" value=\"$content[id]\"></td>
                    </tr>";
              $pagenum++;
        }

        echo "<tr class=\"tbhead\">
                <td align=\"center\" colspan=\"5\">
                <input type=\"hidden\" name=\"action\" value=\"updatecontents\">
                <input type=\"hidden\" name=\"articleid\" value=\"$articleid\">
                <input type=\"submit\" name=\"updatetitleandorder\" value=\"更新标题与排序\" class=\"bginput\"> <input type=\"submit\" name=\"deletecontent\" value=\"删除所有选中的页面\" class=\"bginput\">
                </td>
              </tr>";
        echo "</form>";
    }
    $cpforms->tablefooter();

    echo "<br>";

    $cpforms->inithtmlarea();
    $cpforms->formheader(array('title'=>'继续添加下一页',
                                'name'=>'article',
                                'extra'=>"onSubmit=\"return ProcessNextArticle()\""));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'insertnextpage'));
    $cpforms->makehidden(array('name'=>'articleid',
                                'value'=>$articleid));

    $cpforms->makeinput(array('text'=>'小标题:',
                               'name'=>'subhead',
                               'maxlength'=>'100'));

    $cpforms->makehtmlarea(array('text'=>'文章内容:',
                                  'name'=>'articletext'));
    $order = $DB->fetch_one_array("SELECT MAX(displayorder) AS maxdisplayorder FROM ".$db_prefix."articletext
                                          WHERE articleid='$articleid'");

    $cpforms->makeorderinput(array('text'=>'排序:',
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

    if ($title=="") {
        pa_exit("标题不能为空");
    }

    if (empty($sortid)) {
        pa_exit("还未选中任何分类");
    }

    $article = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."article WHERE articleid='$articleid'");
    if (empty($article)) {
        pa_exit("该文章不存在");
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

    redirect("./article.php?action=edit&sortid=$sortid","该文章已更新");

}


if ($_POST[action]=="updatecontents") {

    $articleid = intval($_POST[articleid]);
    $article = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."article WHERE articleid='$articleid'");
    $subhead = $_POST[subhead];
    $articletext = $_POST[articletext];

    if (empty($article)) {
        pa_exit("该文章不存在");
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
        redirect("./article.php?action=mod&articleid=$articleid","该文章已更新");

    } elseif($_POST[deletecontent]) {

        if (!is_array($articletext)) {
            pa_exit("仍未选中任何要删除的文章内容");
        }
        $cpforms->formheader(array('title'=>'确实要删除所有选中的文章内容?'));
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
        pa_exit("该文章不存在");
    }


    $articletextid = intval($_POST[articletextid]);
    $subhead = htmlspecialchars(trim($_POST[subhead]));
    $displayorder = intval($_POST[displayorder]);
    $articletext = $_POST[articletext];


    if ($subhead=="") {
        pa_exit("小标题不能为空");
    }
    if ($articletext=="") {
        pa_exit("内容不能为空");
    }

    $DB->query("UPDATE ".$db_prefix."articletext SET
                       subhead='".addslashes($subhead)."',
                       articletext='".addslashes($articletext)."',
                       displayorder='$displayorder'
                       WHERE id='$articletextid'");

    if ($nextpage){
        redirect("./article.php?action=next&articleid=$articleid","继续添加下一页");
    } else {
        redirect("./article.php?action=mod&articleid=$articleid","该文章已更新");
    }

}


if ($_GET[action]=="editcontent"){

    $id = intval($_GET[id]);
    $content = $DB->fetch_one_array("SELECT article.title,articletext.* FROM ".$db_prefix."articletext AS articletext
                                              LEFT JOIN ".$db_prefix."article AS article
                                                ON articletext.articleid=article.articleid
                                              WHERE id='$id'");
    if (empty($content)) {
        pa_exit("该文章内容不存在");
    }

    $article = validate_articleid($content[articleid]);

    if (!empty($article[sortid])) {
        $cpforms->tableheader();
        echo "<tr class=".getrowbg().">
                 <td>导航: <a href=\"./article.php?action=list\">根分类</a>".buildsortnav($article[sortid])." \ <a href=\"article.php?action=mod&articleid=$article[articleid]\">$article[title]</a></td>
              </tr>";
        $cpforms->tablefooter();
        echo "<br>";
    }

    $cpforms->inithtmlarea();
    $cpforms->formheader(array('title'=>"编辑文章内容: $content[subhead]",
                                'name'=>'article',
                                'extra'=>"onSubmit=\"return ProcessNextArticle()\""));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'updatecontent'));
    $cpforms->makehidden(array('name'=>'articleid',
                                'value'=>$content[articleid]));

    $cpforms->makehidden(array('name'=>'articletextid',
                                'value'=>$content[id]));

    $cpforms->makeinput(array('text'=>'小标题:',
                               'name'=>'subhead',
                               'value'=>$content[subhead],
                               'maxlength'=>100));

    $cpforms->makehtmlarea(array('text'=>'文章内容:',
                                  'name'=>'articletext',
                                  'value'=>$content[articletext]));
    $cpforms->makeorderinput(array('text'=>'排序:',
                                    'name'=>'displayorder',
                                    'value'=>$content[displayorder]));
    $cpforms->formfooter();

}


if ($_GET[action]=="delcontent"){

    $id = intval($_GET[id]);

    $cpforms->formheader(array('title'=>'确实要删除该页内容?'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'removethis'));
    $cpforms->makehidden(array('name'=>'articletextid',
                                'value'=>$_GET[id]));
    $cpforms->formfooter(array('confirm'=>1));

}



if ($_POST[action]=="removethis"){

    $articletextid = intval($_POST[articletextid]);

    $articletext = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."articletext WHERE id='$articletextid'");
    if (empty($articletext)) {
        pa_exit("该页内容不存在");
    }

    $DB->query("DELETE FROM ".$db_prefix."articletext
                       WHERE id='$articletextid'");

    redirect("./article.php?action=mod&articleid=$articletext[articleid]","该页内容已删除");

}



if ($_POST[action]=="dodeletecontent") {

    if (!is_array($_POST[articletext])) {
        pa_exit("仍未选中任何要删除的文章内容");
    }

    $articleid = intval($_POST[articleid]);
    $article = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."article WHERE articleid='$articleid'");
    if (empty($article)) {
        pa_exit("该文章不存在");
    }

    foreach($_POST[articletext] AS $articletextid) {
            $DB->query("DELETE FROM ".$db_prefix."articletext WHERE id='$articletextid' AND articleid='$articleid'");
    }

    redirect("article.php?action=mod&articleid=$articleid","所有选中的文章内容已删除");

}



if ($_GET[action]=="kill"){

    $article = validate_articleid($_GET[articleid]);

    $cpforms->formheader(array('title'=>'确实要删除该文章?'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'remove'));
    $cpforms->makehidden(array('name'=>'articleid',
                                'value'=>$article[articleid]));

    $cpforms->formfooter(array('confirm'=>1));

}




if ($_POST[action]=="remove"){

    $article = validate_articleid($_POST[articleid]);

    $DB->query("DELETE FROM ".$db_prefix."favorite WHERE articleid='$article[articleid]'");
    $DB->query("DELETE FROM ".$db_prefix."articletext WHERE articleid=$article[articleid]");

    $DB->query("DELETE FROM ".$db_prefix."article WHERE articleid=$articleid");

    deletecomments($articleid);

    $DB->query("UPDATE ".$db_prefix."sort SET articlecount=articlecount-1 WHERE sortid IN (".getparentsorts($article[sortid]).")");
        if ($subdirs = mkdirname($article['sortid'],-1,$article['date'],0,0))//get_sortdirs($article['sortid'])
        {
                $writedir = HTMLDIR . "/" . $subdirs;// . "/" . date("Y_m", $article['date'])
        	$prefilename=mkfilename($filenamemethod,$article['title'],1);
        	$writename = $prefilename . $article['articleid'] . "_1";
		deltree("../".$writedir . $writename . "." . HTMLEXT);
	}
    redirect("./article.php?action=edit&sortid=$article[sortid]","该文章已删除");

}

if ($_GET[action]=="kill2"){
	if(!$_GET['articleids'])pa_exit("该文章不存在");
    $cpforms->formheader(array('title'=>'确实要删除该文章?'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'remove2'));
	foreach($_GET['articleids'] AS $articleid => $value)
    $cpforms->makehidden(array('name'=>"articleids[$articleid]",
                                'value'=>1));

    $cpforms->formfooter(array('confirm'=>1));

}

if ($_POST[action]=="remove2"){
	foreach($_POST[articleids] AS $articleid => $value)
    {
    	$article = validate_articleid($articleid);
    	$articleidlist[]=$articleid;
	}

    $DB->query("DELETE FROM ".$db_prefix."favorite WHERE articleid IN (".join(',',$articleidlist).")");
    $DB->query("DELETE FROM ".$db_prefix."articletext WHERE articleid IN (".join(',',$articleidlist).")");

    $DB->query("DELETE FROM ".$db_prefix."article WHERE articleid IN (".join(',',$articleidlist).")");

    deletecomments2($articleidlist);

    $DB->query("UPDATE ".$db_prefix."sort SET articlecount=articlecount-".count($articleidlist)." WHERE sortid IN (".getparentsorts($article[sortid]).")");

    redirect("./article.php?action=edit&sortid=$article[sortid]","该文章已删除");

}

if ($_GET[action]=="massmove"){

    $cpforms->formheader(array('title'=>'批量移动'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'affirm_massmove'));
    $cpforms->getsortlist(array('text'=>'源分类:',
                                 'name'=>'source_sortid',
                                 'selected'=>$_GET[sortid]));
    $cpforms->getsortlist(array('text'=>'目标分类:',
                                 'name'=>'target_sortid'
                                 ));
    $cpforms->makeyesno(array('text'=>'是否包含子分类?<br>如果是,源分类与源分类的子分类中的所有文章将会移动到目标分类',
                               'name'=>'subsort'));
    $cpforms->formfooter();

}


if ($_POST[action]==affirm_massmove){

    $cpforms->formheader(array('title'=>'确实要批量移动该分类中的文章?'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'domassmove'));
    $cpforms->makehidden(array('name'=>'subsort',
                                'value'=>$_POST[subsort]));
    $cpforms->makehidden(array('name'=>'source_sortid',
                                'value'=>$_POST[source_sortid]));
    $cpforms->makehidden(array('name'=>'target_sortid',
                                'value'=>$_POST[target_sortid]));
    $cpforms->formfooter(array('confirm'=>1));

}


// do mass move articles
if ($_POST[action]==domassmove){

    $target_sortid = intval($_POST[target_sortid]);
    $source_sortid = intval($_POST[source_sortid]);

    $target_sort = $DB->query("SELECT * FROM ".$db_prefix."sort WHERE sortid='$target_sortid'");
    if (empty($target_sort)) {
        pa_exit("目标分类不存在");
    }

    if ($subsort) {
        $sorts = $DB->query("SELECT * FROM ".$db_prefix."sort WHERE sortid='$source_sortid' OR INSTR(parentlist,',$source_sortid,')>0");
    } else {
        $sorts = $DB->query("SELECT * FROM ".$db_prefix."sort WHERE sortid='$source_sortid'");
    }
    while ($sort = $DB->fetch_array($sorts)) {
           $sourcesortids_array[] = $sort[sortid];
    }
    if (is_array($sourcesortids_array)) {
        $sourcesortids = implode(",",$sourcesortids_array);
    } else {
        $sourcesortids = $sourcesortids_array;
        if ($sourcesortids = $target_sortid) {
            redirect("./chapter.php?action=list","所有文章已移动");
            exit;
        }
    }
    //echo $sourcesortids;

    $DB->query("UPDATE ".$db_prefix."article SET sortid='$target_sortid' WHERE sortid IN (0$sourcesortids)");
    redirect("./article.php?action=list","所有文章已移动");


}



if ($_GET[action]=="massdelete"){

    $cpforms->formheader(array('title'=>'批量删除文章'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'affirm_massdelete'));

    $cpforms->getsortlist(array('text'=>'目标分类:',
                                 'name'=>'sortid',
                                 'selected'=>$sortid));

    $cpforms->makeyesno(array('text'=>'是否包含子分类?<br>如果是,该分类与该分类的子分类中的文章均会被删除.',
                               'name'=>'subsort'));
    $cpforms->formfooter();

}

if ($_POST[action]==affirm_massdelete){

    $cpforms->formheader(array('title'=>'确实要删除该分类中的所有文章?'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'domassdelete'));

    $cpforms->makehidden(array('name'=>'sortid',
                                'value'=>$_POST[sortid]));
    $cpforms->makehidden(array('name'=>'subsort',
                                'value'=>$_POST[subsort]));

    $cpforms->formfooter(array('confirm'=>1));

}


if ($_POST[action]==domassdelete){

    $sortid = intval($_POST[sortid]);
    if ($_POST[subsort]) {
        $sorts = $DB->query("SELECT * FROM ".$db_prefix."sort WHERE sortid='$sortid' OR INSTR(parentlist,',$sortid,')>0");
    } else {
        $sorts = $DB->query("SELECT * FROM ".$db_prefix."sort WHERE sortid='$sortid'");
    }
    while ($sort = $DB->fetch_array($sorts)) {
           $sortids_array[] = $sort[sortid];
    }
    if (is_array($sortids_array)) {
        $sortids = implode(",",$sortids_array);
    } else {
        $sortids = $sortids_array;
    }

    $articles = $DB->query("SELECT * FROM ".$db_prefix."article WHERE sortid IN (0$sortids)");
    while ($article = $DB->fetch_array($articles)) {
           $DB->query("DELETE FROM ".$db_prefix."articletext WHERE articleid='$article[articleid]'");
           $DB->query("DELETE FROM ".$db_prefix."favorite WHERE articleid='$article[articleid]'");
           deletecomments($articleid);
           //$DB->query("DELETE FROM ".$db_prefix."comment WHERE articleid='$article[articleid]'");
    }

    $DB->query("DELETE FROM ".$db_prefix."article WHERE sortid IN (0$sortids)");

    redirect("./article.php?action=list","所有文章已删除");

}



if ($_GET[action]=="list"){

    $cpforms->tableheader();
    $cpforms->makecategory("分类列表");
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
    if (empty($sortid)){
       $ssortid="";
    } else {
       $ssortid="WHERE sortid=".$sortid;
       $cpforms->tableheader();
       echo "<tr class=".getrowbg().">
              <td>导航: <a href=\"./article.php?action=list\">根分类</a>".buildsortnav($sortid)."</td>
              <td align=\"right\"><a href=\"./article.php?action=add&sortid=$sortid\">添加文章</a></td>
             </tr>";
       $cpforms->tablefooter();

    }

    $nav = new buildNav;

    $total = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."article AS article $ssortid");

    $nav->total_result = $total[count];

    if (empty($total[count])) {
        pa_exit("还没有任何文章");
    }

    $nav->execute("SELECT * FROM ".$db_prefix."article AS article $ssortid ORDER BY articleid DESC");

    echo $nav->pagenav();

    echo "<table boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\" class=\"tableoutline\">
               <tr align=\"center\" class=\"tbhead\">
                <td nowrap> id# </td>
                <td width=\"60%\"> 标题 </td>
                <td nowrap> 作者 </td>
                <td nowrap> 日期 </td>
                <td nowrap> 点击 </td>
                <td nowrap> 评论 </td>
                <td nowrap> 显示 </td>
                <td nowrap> 编辑 </td>
               </tr>\n";
    while ($article = $DB->fetch_array($nav->sql_result)){
           if (!empty($article[imageid])) {
               $article[img] = "<img src=\"../images/image.gif\" border=\"0\" align=\"absmiddle\" alt=\"附缩略图\">";
           }
           if ($article[visible]) {
               $article[visible] = "显示";
           } else {
               $article[visible] = '<font color="red">隐藏</font>';
           }
           echo "<tr class=".getrowbg().">
                      <td align=\"center\" nowrap>$article[articleid]</td>
                      <td>$article[img] <a target=_blank href=\"article.php?action=view&articleid=$article[articleid]\">$article[title]</a></td>
                      <td align=\"center\" nowrap> $article[author]</td>
                      <td align=\"center\" nowrap>".padate("Y-m-d H:i:s",$article[date])."</td>
                      <td align=\"center\" nowrap>$article[views]</td>
                      <td align=\"center\" nowrap><a href=\"comment.php?action=edit&articleid=$article[articleid]\">$article[comments]</a></td>
                      <td align=\"center\" nowrap>$article[visible]</td>
                      <td align=\"center\" nowrap> [<a href=../htmlauto.php?mod=mkarticle&articleid=$article[articleid]>静态生成</a>] [<a href=\"article.php?action=mod&articleid=$article[articleid]\">编辑</a>] [<a href=\"article.php?action=kill&articleid=$article[articleid]\">删除</a>]</td>
                     </tr>\n";

    }
    echo "</table>\n";

    echo $nav->pagenav();

}

if ($_GET[action]=="search") {

    $cpforms->formheader(array('title'=>'查找文章','method'=>'get'));
    $cpforms->makeinput(array('text'=>'标题:',
                               'name'=>'title'));
    $cpforms->makeinput(array('text'=>'小标题:',
                               'name'=>'subhead'));

    $cpforms->makeinput(array('text'=>'作者:',
                               'name'=>'author'));
    $cpforms->makeinput(array('text'=>'文章摘要:',
                               'name'=>'description'));
    $cpforms->makeinput(array('text'=>'内容:',
                               'name'=>'articletext'));

    $cpforms->makeselect(array('text'=>'匹配:',
                                'name'=>'mode',
                                'option'=>array('OR'=>'部分匹配',
                                                 'AND'=>'全部匹配'
                                                 )));

    $cpforms->getsortlist(array('text'=>'分类:',
                                 'name'=>'sortid',
                                 'extra'=>array('-1'=>'所有分类')));

    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'dosearch'));

    $cpforms->formfooter();

}

if ($_GET[action]=="dosearch") {


    $title = trim($_GET[title]);
    $subhead = trim($_GET[subhead]);
    $author = trim($_GET[author]);
    $description = trim($_GET[description]);
    $sortid = intval($_GET[sortid]);
    $articletext = trim($_GET[articletext]);

    if ($title!="") {
        $condition_array[] = " article.title LIKE '%".addslashes($title)."%' ";
    }
    if ($subhead!="") {
        $condition_array[] = " articletext.subhead LIKE '%".addslashes($subhead)."%' ";
    }
    if ($author!="") {
        $condition_array[] = " article.author LIKE '%".addslashes($author)."%' ";
    }
    if ($description!="") {
        $condition_array[] = " article.description LIKE '%".addslashes($description)."%' ";
    }
    if ($articletext!="") {
        $condition_array[] = " articletext.articletext LIKE '%".addslashes($articletext)."%' ";
    }        

    if (empty($condition_array)) {
        pa_exit("还未输入任何要搜索的关键字");
    }



    $mode = $_GET[mode];

    //print_rr($condition_array);
    if ($mode!="OR") {
        $mode = "AND";
    }
    if (is_array($condition_array)) {
        $conditions = implode(" $mode ",$condition_array);
    } else {
        $conditions = $condition_array;
    }
    if ($sortid!=-1 AND !empty($sortid)) {
        $conditions .= "AND article.sortid='$sortid'";
    }



    $nav = new buildNav;

    $total = $DB->query("SELECT article.articleid FROM ".$db_prefix."articletext AS articletext

                            LEFT JOIN ".$db_prefix."article AS article
                                 ON articletext.articleid=article.articleid
                            LEFT JOIN ".$db_prefix."sort AS sort
                                 ON article.sortid=sort.sortid
                            WHERE $conditions
                            GROUP BY article.articleid");

    $nav->total_result = $DB->num_rows($total);


    $nav->execute("SELECT article.*,sort.title AS sort FROM ".$db_prefix."articletext AS articletext

                            LEFT JOIN ".$db_prefix."article AS article
                                 ON articletext.articleid=article.articleid
                            LEFT JOIN ".$db_prefix."sort AS sort
                                 ON article.sortid=sort.sortid
                            WHERE $conditions
                            GROUP BY article.articleid
                            ");
    $cpforms->tableheader();
    echo "<tr class=".getrowbg().">
           <td>导航: 搜索结果</td>
           <td align=right><a href=\"article.php?action=search\">继续搜索</a></td>
          </tr>\n";
    $cpforms->tablefooter();

    $cpforms->tablefooter();
    if ($nav->total_result>0) {

        echo $nav->pagenav();
        $cpforms->tableheader();
        echo " <tr align=\"center\" class=\"tbhead\">
                <td>id#</td>
                <td width=\"50%\">标题</td>
                <td nowrap>分类</td>
                <td nowrap>作者</td>
                <td nowrap>日期</td>
                <td nowrap>点击</td>
                <td nowrap>显示</td>
                <td nowrap>编辑</td>
               </tr>\n";

        while($article = $DB->fetch_array($nav->sql_result)) {
              if (!empty($article[imageid])) {
                  $article[img] = "<img src=\"../images/image.gif\" border=\"0\" align=\"absmiddle\"> ";
              }
              if ($article[visible]) {
                  $article[visible] = "显示";
              } else {
                  $article[visible] = "<font color=\"red\">隐藏</a>";
              }
              echo "<tr class=".getrowbg().">
                     <td align=\"center\">$article[articleid]</td>
                     <td>$article[img] <a target=_blank href=\"../article.php?articleid=$article[articleid]\">$article[title]</a></td>
                     <td align=\"center\" nowrap><a href=\"article.php?action=edit&sortid=$article[sortid]\">$article[sort]</a></td>
                     <td align=\"center\" nowrap>$article[author]</td>
                     <td align=\"center\" nowrap>".date("Y-m-d H:i:s",$article[date])."</td>
                     <td align=\"center\" nowrap>$article[views]</td>
                     <td align=\"center\" nowrap>$article[visible]</td>
                     <td align=\"center\" nowrap> [<a href=\"article.php?action=mod&articleid=$article[articleid]\">编辑</a>] [<a href=\"article.php?action=kill&articleid=$article[articleid]\">删除</a>]</td>
                    </tr>\n";

        }

        $cpforms->tablefooter();
        echo $nav->pagenav();
    } else {
        pa_exit("找不到任何匹配的文章");
    }

}

if ($_GET[action]=="validate"){

    $cpforms->tableheader();
    echo "<tr class=".getrowbg().">
            <td>导航: <a href=\"./article.php?action=list\">根分类</a>".buildsortnav($sortid)."</td>
            <td align=\"right\"><a href=\"./article.php?action=add&sortid=$sortid\">添加文章</a></td>
          </tr>";
    $cpforms->tablefooter();

    $nav = new buildNav;

    $total = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."article AS article WHERE visible=0");

    $nav->total_result = $total[count];

    if (empty($total[count])) {
        pa_exit("还没有任何文章");
    }

    $nav->execute("SELECT article.*,articletext.articletext FROM ".$db_prefix."article AS article LEFT JOIN ".$db_prefix."articletext AS articletext
    USING (articleid) WHERE visible=0 ORDER BY articleid DESC");

    echo $nav->pagenav();

    echo "<table boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\" class=\"tableoutline\">
    		<form method='get' action='article.php'>
               <tr align=\"center\" class=\"tbhead\">
                <td> id# </td>
                <td width=\"60%\"> 标题/内容 </td>
                <td nowrap> 作者 </td>
                <td nowrap> 日期 </td>
                <td nowrap> 点击 </td>
                <td nowrap> 显示 </td>
                <td nowrap> 编辑 </td>
                <td nowrap> 选择 </td>
               </tr>\n";
    while ($article = $DB->fetch_array($nav->sql_result)){
           if (!empty($article[imageid])) {
               $article[img] = "<img src=\"../images/image.gif\" border=\"0\" align=\"absmiddle\" alt=\"附缩略图\">";
           }
           if ($article[visible]) {
               $article[visible] = "显示";
           } else {
               $article[visible] = "<font color=\"red\">隐藏</a>";
           }
           echo "<tr class=".getrowbg().">
                      <td align=\"center\" nowrap> $article[articleid]</td>
                      <td>$article[img] <a target=_blank href=\"article.php?action=view&articleid=$article[articleid]\">$article[title]</a>/".substr($article[articletext],0,100)."</td>
                      <td align=\"center\" nowrap>$article[author]</td>
                      <td align=\"center\" nowrap>".date("Y-m-d H:i:s",$article[date])."</td>
                      <td align=\"center\" nowrap>$article[views]</td>
                      <td align=\"center\" nowrap>$article[visible]</td>
                      <td align=\"center\" nowrap>
                       [<a href=\"article.php?action=dovalidate&articleids[$article[articleid]]=1\">通过审批</a>]
                       [<a href=\"article.php?action=mod&articleid=$article[articleid]\">编辑</a>]
                       [<a href=\"article.php?action=kill&articleid=$article[articleid]\">删除</a>]
                      </td>
                      <td nowrap align='center'>
                      <input type='checkbox' name='articleids[$article[articleid]]' value='1'>
                      </td>
                     </tr>\n";

    }
    echo "</table><input name=action type=radio value='dovalidate' checked>通过
<input name=action type=radio value='kill2'>删除
    <input type='submit' value=' 提交 ' class='button'></form>\n";

    echo $nav->pagenav();

}

if ($_GET[action]=="dovalidate") {
	$timestamp = time();
	if(!$_GET['articleids'])pa_exit("该文章不存在");
	foreach($_GET['articleids'] AS $articleid => $value)
	{
		$articleid = intval($articleid);
		$articleidlist[]=$articleid;
	}
    $article = validate_articleid($articleid);
    $DB->query("UPDATE ".$db_prefix."article SET
                       visible=1,lastupdate=$timestamp
                       WHERE articleid IN (".join(',',$articleidlist).")");
    redirect("./article.php?action=validate","该文章已通过审批");

}if ($_GET[action]=="view"){
	
	$article = $DB->fetch_one_array("SELECT article.*,articletext.articletext FROM ".$db_prefix."article AS article LEFT JOIN ".$db_prefix."articletext AS articletext
    USING (articleid) WHERE article.articleid = $articleid");
    echo $article[articletext];
}
cpfooter();
?>