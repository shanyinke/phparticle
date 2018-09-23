<?php
error_reporting(7);
require "global.php";



if ($_POST[action]=="dodownload") {

    if (function_exists("set_time_limit")==1 and get_cfg_var("safe_mode")==0) {
       @set_time_limit(1200);
    }

    $styleid = intval($_POST[styleid]);
    if ($styleid==-1) {
        $style[title]="Global phpArticle Style";
        $style[replacementsetid]=-1;
        $style[templatesetid]=-1;
        $replacementset[title]="NULL";
        $templateset[title]="NULL";
    } else {
        $style=$DB->fetch_one_array("SELECT * FROM ".$db_prefix."style WHERE styleid='$styleid'");
        $replacementset=$DB->fetch_one_array("SELECT * FROM ".$db_prefix."replacementset WHERE replacementsetid=$style[replacementsetid]");
        $templateset=$DB->fetch_one_array("SELECT * FROM ".$db_prefix."templateset WHERE templatesetid=$style[templatesetid]");
    }

    $templates=$DB->query("SELECT * FROM ".$db_prefix."template WHERE templatesetid='$style[templatesetid]'");
    $replacements=$DB->query("SELECT * FROM ".$db_prefix."replacement WHERE replacementsetid='$style[replacementsetid]'");

    $stylecontents=$configuration[version]."|P-@phpArticle@-A|".escape($style[title])."|P-@style@-A|".escape($replacementset[title])."|P-@style@-A|".escape($templateset[title])."|P-@style@-A|".$DB->num_rows($replacements)."|P-@style@-A|".$DB->num_rows($templates)."|P-@phpArticle@-A|";

    while ($replacement=$DB->fetch_array($replacements)) {
    //	$findwordlist[]="'".$replacement['findword']."'";
           $stylecontents.=escape($replacement[findword])."|P-@replacement@-A|".escape($replacement[replaceword])."|P-@replacement@-A|";
    }
  //  $DB->query("DELETE FROM ".$db_prefix."replacement WHERE replacementsetid='-1' AND findword IN (".join(',',$findwordlist).")");
   /* $replacements=$DB->query("SELECT * FROM ".$db_prefix."replacement WHERE replacementsetid='-1' AND findword NOT IN (".join(',',$findwordlist).")");
    while ($replacement=$DB->fetch_array($replacements)) {
           $stylecontents.=escape($replacement[findword])."|P-@replacement@-A|".escape($replacement[replaceword])."|P-@replacement@-A|";
    }*/

    $stylecontents.="|P-@phpArticle@-A|";
    while ($template=$DB->fetch_array($templates)) {
    //	$titlelist[]="'".$template[title]."'";
           $stylecontents.=escape($template[title])."|P-@template@-A|".escape($template[template])."|P-@template@-A|";
    }
  //  $DB->query("DELETE FROM ".$db_prefix."template WHERE templatesetid='-1' AND title IN (".join(',',$titlelist).")");
  /*  $templates2=$DB->query("SELECT * FROM ".$db_prefix."template WHERE templatesetid='-1' AND title NOT IN (".join(',',$titlelist).")");
    $stylecontents.="|P-@phpArticle@-A|";
    while ($template=$DB->fetch_array($templates2)) {
           $stylecontents.=escape($template[title])."|P-@template@-A|".escape($template[template])."|P-@template@-A|";
    }*/

    header("Content-disposition: filename=".date("Y-m-d.",time())."phpArticle.style");
    header("Content-Length: ".strlen($stylecontents));
    header("Content-type: unknown/unknown");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $stylecontents;
    exit;

}

cpheader();


if ($_GET[action]==download) {

    $cpforms->formheader(array('title'=>'下载风格文件'));
    $cpforms->getstyles(array('text'=>'风格:',
                               'name'=>'styleid',
                               'selected'=>$_GET[styleid]));
    $cpforms->makehidden(array('name'=>'action','value'=>'dodownload'));
    $cpforms->formfooter();

}



if ($_GET[action]==upload) {

    $cpforms->formheader(array('title'=>'从本地上传并导入风格','enctype'=>'multipart/form-data'));
    $cpforms->makefile(array('text'=>'请选择要上传风格文件:','name'=>'style'));
    $cpforms->makeyesno(array('text'=>'覆盖风格?<br>如果"否",则会新建一个风格',
                               'name'=>'over'));
    $cpforms->getstyles(array('text'=>'被覆盖的风格:<br>如果要覆盖旧的风格,请先将以上覆盖选项设置为"是"',
                               'name'=>'styleid',
                               ));

    $cpforms->makehidden(array('name'=>'action','value'=>'doupload'));
    $cpforms->formfooter();


    echo "<br>";

    $cpforms->formheader(array('title'=>'从服务器导入风格'));

    $cpforms->getstylefiles(array('text'=>'请选择要导入的风格文件:<br>如果本项为空,请先上传风格文件到 admin/style 目录下,并确认该文件名为 *.style',
                                   'name'=>'stylefile'));

    $cpforms->makeyesno(array('text'=>'覆盖风格?<br>如果"否",则会新建一个风格',
                               'name'=>'over'));
    $cpforms->getstyles(array('text'=>'被覆盖的风格:<br>如果要覆盖旧的风格,请先将以上覆盖选项设置为"是"',
                               'name'=>'styleid',
                               ));

    $cpforms->makehidden(array('name'=>'action','value'=>'doimport'));
    $cpforms->formfooter();

}


if ($_POST[action]=="doupload" OR $_POST[action]=="doimport") {


   if ((isset($_FILES[style]) AND ereg(".style",$_FILES[style][name])) OR (isset($_POST[stylefile]) AND trim($_POST[stylefile])!="" AND ereg(".style",$_POST[stylefile]))) {
       /*

       print_rr($_FILES);
       print_rr($_POST);
       exit;
       */
       $styleid = intval($_POST[styleid]);
       if ($_POST[action]=="doimport") {
           $style = "./style/$_POST[stylefile]";
       } elseif ($_POST[action]=="doupload") {
           $style = $_FILES[style][tmp_name];
       }



       $stylecontents = implode("",file($style));

       //echo $stylecontents;
       //exit;
       $stylecontent = explode("|P-@phpArticle@-A|",$stylecontents);

       list($key,$styleversion) = each($stylecontent);
       // check version
       if ($styleversion!="$configuration[version]") {
           pa_exit("风格的版本与当前系统版本不符,风格版本: $styleversion, 系统版本: $configuration[version]");
       }

       list($key,$styleinfos) = each($stylecontent);
       // check style title
       $styleinfo = explode("|P-@style@-A|",$styleinfos);

       if ($styleinfo[0]=="Global phpArticle Style") {
           $replacementsetid = -1;
           $templatesetid = -1;
       } else {

           // get replacement title
           $replacement[title] = htmlspecialchars(trim($styleinfo[1]));
           // get template title
           $template[title] = htmlspecialchars(trim($styleinfo[2]));
           // over old style
           if ($over==1){
               if (isset($styleid) AND $styleid!=-1) {
                   $style = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."style WHERE styleid='$styleid'");
                   $replacementsetid = $style[replacementsetid];
                   $templatesetid = $style[templatesetid];
                   $DB->query("UPDATE ".$db_prefix."style SET title='".addslashes($styleinfo[0])."' WHERE styleid='$styleid'");
                   $DB->query("UPDATE ".$db_prefix."replacementset SET title='".addslashes($replacement[title])."' WHERE replacementsetid='$replacementsetid'");
                   $DB->query("UPDATE ".$db_prefix."templateset SET title='".addslashes($template[title])."' WHERE templatesetid='$templatesetid'");
               }
           } else { // new style
               $DB->query("INSERT INTO ".$db_prefix."replacementset (title) VALUES ('".addslashes($replacement[title])."')");
               $replacementsetid = $DB->insert_id();
               $DB->query("INSERT INTO ".$db_prefix."templateset (title) VALUES ('".addslashes($template[title])."')");
               $templatesetid = $DB->insert_id();
               $DB->query("INSERT INTO ".$db_prefix."style (replacementsetid,templatesetid,title) VALUES ('$replacementsetid','$templatesetid','".addslashes(htmlspecialchars(trim($styleinfo[0])))."')");
               $styleid = $DB->insert_id();
           }
       }

       list($key,$replacements) = each($stylecontent);
       $replacement = explode("|P-@replacement@-A|",$replacements);

       $DB->query("DELETE FROM ".$db_prefix."replacement WHERE replacementsetid='$replacementsetid'");

       for ($i=0;$i<$styleinfo[3];$i++) {
            list($key,$findword)=each($replacement);
            list($key,$replaceword)=each($replacement);
            if ($findword!="") {
                $DB->query("INSERT INTO ".$db_prefix."replacement (replacementsetid,findword,replaceword)
                                   VALUES ($replacementsetid,'".addslashes($findword)."','".addslashes($replaceword)."')");
            }
       }

       list($key,$templates) = each($stylecontent);
       $temp = explode("|P-@template@-A|",$templates);

       $DB->query("DELETE FROM ".$db_prefix."template WHERE templatesetid='$templatesetid'");

       for ($i=0;$i<$styleinfo[4];$i++) {
            list($key,$title)=each($temp);
            list($key,$template)=each($temp);
            $DB->query("INSERT INTO ".$db_prefix."template (templatesetid,title,template)
                               VALUES ($templatesetid,'".addslashes(htmlspecialchars(trim($title)))."','".addslashes($template)."')");
       }

       write_replacement($replacementsetid);
       resetcache();
       redirect("./style.php?action=view","该风格已导入");
   } else {
       echo "风格文件无效,请确认上传或导入的文件名为 *.style";
   }

}


if ($_GET[action]=="add") {

    $cpforms->formheader(array('title'=>'添加新风格'));
    $cpforms->makehidden(array('name'=>'action','value'=>'insert'));
    $cpforms->makeinput(array('text'=>'风格名:','name'=>'title'));

    $cpforms->formfooter();
}


if ($_POST[action]=="insert") {

    $_POST[title] = htmlspecialchars(trim($_POST[title]));
    if ($_POST[title]=="") {
        pa_exit("风格名不能为空");
    }

    $DB->query("INSERT INTO ".$db_prefix."replacementset (title)
                       VALUES ('".addslashes($_POST[title])."')");
    $replacementsetid = $DB->insert_id();
    write_replacement($replacementsetid);

    $DB->query("INSERT INTO ".$db_prefix."templateset (title)
                       VALUES ('".addslashes($_POST[title])."')");
    $templatesetid = $DB->insert_id();
    $DB->query("INSERT INTO ".$db_prefix."style (replacementsetid,templatesetid,title)
                       VALUES ($replacementsetid,$templatesetid,'".addslashes($_POST[title])."')");
    $styleid = $DB->insert_id();
    resetcache();
    redirect("./style.php?action=view","该风格已添加");

}

function validate_styleid(&$styleid) {

         global $DB,$db_prefix;
         $styleid = intval($styleid);
         $style = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."style WHERE styleid='$styleid'");
         if (empty($style)) {
             pa_exit("该风格不存在");
         }
         return $style;

}

if ($_GET[action]=="kill") {

    if ($_GET[styleid]==1) {
        pa_exit("你不能删除默认风格");
    }
    $style = validate_styleid($_GET[styleid]);

    $cpforms->formheader(array('title'=>'确实要删除该风格?'));
    $cpforms->makehidden(array('name'=>'action','value'=>'remove'));
    $cpforms->makehidden(array('name'=>'styleid','value'=>$style[styleid]));
    $cpforms->formfooter(array('confirm'=>1));

}


if($_POST[action]=="remove"){

    if ($_POST[styleid]==1) {
        pa_exit("你不能删除默认风格");
    }
    $style = validate_styleid($_POST[styleid]);

    $DB->query("DELETE FROM ".$db_prefix."style WHERE styleid='$_POST[styleid]'");
    resetcache();
    redirect("./style.php?action=view","该风格已删除");


}


if ($_GET[action]=="view")  {

    $cpforms->tableheader();
    echo "<tr class=\"tbhead\"><td colspan=\"2\">风格列表</td></tr>\n";

    require "configs/style.php";

    $styles = $DB->query("SELECT styleid,replacementsetid,templatesetid,title FROM ".$db_prefix."style ORDER BY styleid");
    while($style = $DB->fetch_array($styles)){
           echo "<tr class=".getrowbg()."><td><b>$style[title]</b></td>";
           echo "<td width=50%>";
           echo "[<a href=\"style.php?action=download&styleid=$style[styleid]\">下载</a>]";
           echo "[<a href=\"style.php?action=properties&styleid=$style[styleid]\">属性</a>]";
           echo "[<a href=\"style.php?action=edit&styleid=$style[styleid]\">编辑</a>]";

           $cpforms->makelink(array('text'=>'编辑',
                                    'url'=>"style.php?action=edit&styleid=$style[styleid]"
                                    ));
           if ($style[styleid]!=1) {
               echo "[<a href=\"style.php?action=kill&styleid=$style[styleid]\">删除</a>]";
           }
           if ($style[styleid]!=$styleid) {
               echo "[<a href=\"style.php?action=setdefault&styleid=$style[styleid]\">设为默认</a>]";
           }
           echo "</td></tr>";
    }
    $cpforms->tablefooter();

}



if ($_GET[action]=="properties"){

    //$style=$DB->fetch_one_array("SELECT * FROM ".$db_prefix."style WHERE styleid='$styleid'");
    $style = validate_styleid($_GET[styleid]);
    $cpforms->formheader(array('title'=>"风格属性: $style[title]"));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'update'));
    $cpforms->makehidden(array('name'=>'styleid',
                                'value'=>$style[styleid]));
    $cpforms->makeinput(array('text'=>'风格:',
                               'name'=>'title',
                               'value'=>$style[title]));
    $cpforms->getreplacementsets(array('text'=>'变量套系:',
                                        'name'=>'replacementsetid',
                                        'selected'=>$style[replacementsetid]));
    $cpforms->gettemplatesets(array('text'=>'模板套系:',
                                        'name'=>'templatesetid',
                                        'selected'=>$style[templatesetid]));

    $cpforms->formfooter();

}


if ($_POST[action]=="update"){

    if(trim($title)==""){
       echo "风格名不能为空";
    }else{
       if($replacementsetid==-1){
          $replacementsetid=1;
       }
       if($templatesetid==-1){
          $templatesetid=1;
       }
       $DB->query("UPDATE ".$db_prefix."style SET
                            title='".addslashes($title)."',
                            replacementsetid='$replacementsetid',
                            templatesetid='$templatesetid'
                            WHERE styleid='$styleid'");

       resetcache();
       redirect("./style.php?action=view","该风格已更新");
    }

}


$findwordlist = "charset,outtablecellspacing,roottextcolor,firstalt,secondalt,bordercolor,imagesfolder,tableheadbgcolor,tableheadtextcolor,catbgcolor,cattextcolor,body,bodywidth,tablecellspacing,linecolor,linewidth,timecolor";
$findwordlist = str_replace(',', "','", addslashes($findwordlist));


if ($_GET[action]==edit) {
?>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td>
      <p><b>风格说明:</b><br>
       以下模板,颜色,字体等为<font class=custom>该颜色</font>的是已编辑过或自定义的.
      </p>
      </td>
  </tr>
</table>
<?php

    $styleid = intval($_GET[styleid]);
    $style = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."style WHERE styleid='$styleid'");

    //print_r($style);
    $templatesetid = $style[templatesetid];
    $templateslist = "headinclude,header,footer";
    $templateslist = str_replace(',', "','", addslashes($templateslist));


    $templates = $DB->query("SELECT template,title FROM ".$db_prefix."template
                                     WHERE (title IN ('$templateslist') AND (templatesetid=-1 OR templatesetid='$templatesetid'))
                                     ORDER BY templatesetid");

    while ($template = $DB->fetch_array($templates)) {
           $cachetemplates[$template[title]] = $template[template];
    }

    // get custom templates
    $templates2 = $DB->query("SELECT template,title FROM ".$db_prefix."template
                                     WHERE (title IN ('$templateslist') AND templatesetid='$templatesetid')
                                     ");

    while ($template2 = $DB->fetch_array($templates2)) {
           $class[$template2[title]] = "custom";
           //$cachetemplates2[$template2[title]] = $template2[template];
    }

    $findword_array[charset][title] = "字体编码";
    $findword_array[charset][type] = "string";


    $findword_array[body][title] = "body 标记";
    $findword_array[body][type] = "string";

    $findword_array[bodywidth][title] = "页面 body 宽度";
    $findword_array[bodywidth][type] = "integer";

    $findword_array[imagesfolder][title] = "图片路径";
    $findword_array[imagesfolder][type] = "string";

    $findword_array[outtablecellspacing][title] = "外表格间隔";
    $findword_array[outtablecellspacing][type] = "integer";

    $findword_array[tablecellspacing][title] = "内表格间隔";
    $findword_array[tablecellspacing][type] = "integer";



    $findword_array[bordercolor][title] = "表格边框颜色";
    $findword_array[bordercolor][type] = "color";


    $findword_array[catbgcolor][title] = "分类背景色";
    $findword_array[catbgcolor][type] = "color";

    $findword_array[cattextcolor][title] = "分类字体颜色";
    $findword_array[cattextcolor][type] = "color";


    $findword_array[firstalt][title] = "第一分栏颜色";
    $findword_array[firstalt][type] = "color";

    $findword_array[secondalt][title] = "第二分栏颜色";
    $findword_array[secondalt][type] = "color";

    $findword_array[tableheadbgcolor][title] = "表头背景色";
    $findword_array[tableheadbgcolor][type] = "color";

    $findword_array[tableheadtextcolor][title] = "表头字体颜色";
    $findword_array[tableheadtextcolor][type] = "color";


    $findword_array[roottextcolor][title] = "根分类字体颜色";
    $findword_array[roottextcolor][type] = "color";


    $findword_array[linewidth][title] = "分隔线宽度";
    $findword_array[linewidth][type] = "integer";

    $findword_array[linecolor][title] = "分隔线颜色";
    $findword_array[linecolor][type] = "color";

    $findword_array[timecolor][title] = "时间颜色";
    $findword_array[timecolor][type] = "color";


    //print_rr($findword_array);
    //echo count($findword_array);

    $replacementsetid = $style[replacementsetid];


    $replacements = $DB->query("SELECT * FROM ".$db_prefix."replacement
                                     WHERE (findword IN ('$findwordlist') AND (replacementsetid=-1))
                                     ORDER BY replacementsetid");                                   // OR replacementsetid='$replacementsetid'

    while ($replacement = $DB->fetch_array($replacements)) {
           $cachereplacements[$replacement[findword]] = $replacement; // default
           //$findword_array[$replacement[findword]] = $replacement;
    }

    $replacements = $DB->query("SELECT * FROM ".$db_prefix."replacement
                                         WHERE (findword IN ('$findwordlist') AND replacementsetid='$replacementsetid')");

    while ($replacement = $DB->fetch_array($replacements)) {
           $class2[$replacement[findword]] = "custom";
           $cachereplacements2[$replacement[findword]] = $replacement; // custom
    }

    //echo "<pre>";
    //print_r($cachereplacements);


    echo "
        <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">
          <tr>
            <td>风格: $style[title]</td>
          </tr>
        </table>
    ";

    $cpforms->formheader(array('title'=>"模板",
                               'name'=>'style'
                                ));

    $cpforms->maketextarea(array('text'=>"模板: headinclude<p>在这个模板里可以定义 CSS 样式表</p><p><a href=\"template.php?action=viewdefault&templatetitle=headinclude\" target=\"_blank\">查看默认模板</a></p>",
                                'html'=>'1',
                                'cols'=>'90',
                                'rows'=>'10',
                                'extra'=>"class =\"$class[headinclude]\" onchange=\"this.className='custom';this.form.template_headinclude.value=this.value\"",
                                'value'=>"$cachetemplates[headinclude]"
                                ));
    $cpforms->makehidden(array('name'=>'template_headinclude'));

    $cpforms->maketextarea(array('text'=>"模板: header<p>该模板为页头,在这个模板里可以定义 LOGO,常用链接等</p><p><a href=\"template.php?action=viewdefault&templatetitle=header\" target=\"_blank\">查看默认模板</a></p>",
                                'html'=>'1',
                                'cols'=>'90',
                                'rows'=>'10',
                                'extra'=>"class =\"$class[header]\" onchange=\"this.className='custom';this.form.template_header.value=this.value\"",
                                'value'=>"$cachetemplates[header]"
                                ));
    $cpforms->makehidden(array('name'=>'template_header'));

    $cpforms->maketextarea(array('text'=>"模板: footer<p>该模板为页尾,在这个模板里可以定义版权信息之类的</p><p><a href=\"template.php?action=viewdefault&templatetitle=footer\" target=\"_blank\">查看默认模板</a></p>",
                                'html'=>'1',
                                'cols'=>'90',
                                'rows'=>'10',
                                'extra'=>"class =\"$class[footer]\" onchange=\"this.className='custom';this.form.template_footer.value=this.value\"",
                                'value'=>"$cachetemplates[footer]"
                                ));
    $cpforms->makehidden(array('name'=>'template_footer'));

    $cpforms->makecategory(array('title'=>"页面,表格,颜色,字体等",
                                  'separate'=>1));

                                  //print_rr($cachereplacements);
                                  //print_rr($cachereplacements2);
    foreach ($findword_array AS $k=>$findword) {
    //foreach ($cachereplacements AS $k=>$replacement) {
           if (isset($cachereplacements2[$k])) {
               $replacement = $cachereplacements2[$k];
           } else {
               $replacement = $cachereplacements[$k];
           }

           if ($findword[type]=="color") {
               $cpforms->makecolorinput(array('text'=>$findword[title].":",
                                              'name'=>"replacement[$replacement[findword]]",
                                              'css'=>$class2[$replacement[findword]],
                                              'value'=>htmlspecialchars($replacement[replaceword]),
                                              ));
           } elseif ($findword[type]=="string") {
               $cpforms->makeinput(array('text'=>$findword[title].":",
                                         'name'=>"replacement[$replacement[findword]]",
                                         'css'=>$class2[$replacement[findword]],
                                         'value'=>htmlspecialchars($replacement[replaceword]),
                                         'size'=>50,
                                         'maxlength'=>100,
                                         'extra'=>'onchange="this.className=\'custom\';"'
                                         )); //'extra'=>"onchange=\"this.className='custom';this.form.replacement.$replacement[findword].value=this.value\"",
           } else {
               $cpforms->makeinput(array('text'=>$findword[title].":",
                                         'name'=>"replacement[$replacement[findword]]",
                                         'css'=>$class2[$replacement[findword]],
                                         'value'=>htmlspecialchars($replacement[replaceword]),
                                         'size'=>10,
                                         'maxlength'=>10,
                                         'extra'=>'onchange="this.className=\'custom\';"'
                                         )); //'extra'=>"onchange=\"this.className='custom';this.form.replacement.$replacement[findword].value=this.value\"",

           }
    }



    $cpforms->makehidden(array('name'=>'styleid',
                           'value'=>$styleid));
    $cpforms->makehidden(array('name'=>'action',
                           'value'=>'updatestyle'));

    $cpforms->formfooter();

}

if ($_POST[action]==updatestyle) {
    /*
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    $showqueries=1;
    exit;
    */
    $styleid = intval($_POST[styleid]);
    $style = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."style WHERE styleid='$styleid'");

    if (empty($style)) {
        pn_exit("风格无效");
    }

    // templates
    $template_headinclude = $_POST[template_headinclude];
    if (pa_isset($template_headinclude)) {
        $checktemplate = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."template WHERE title='headinclude' AND templatesetid='$style[templatesetid]'");
        if (empty($checktemplate)) {
            $DB->query("INSERT INTO ".$db_prefix."template (title,template,templatesetid)
                                                            VALUES ('headinclude','".addslashes($template_headinclude)."','$style[templatesetid]')");
        } else {
            $DB->query("UPDATE ".$db_prefix."template SET
                               template='".addslashes($template_headinclude)."'
                               WHERE templatesetid='$style[templatesetid]' AND title='headinclude'");
        }
    }

    $template_header = $_POST[template_header];
    if (pa_isset($template_header)) {
        $checktemplate = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."template WHERE title='header' AND templatesetid='$style[templatesetid]'");
        if (empty($checktemplate)) {
            $DB->query("INSERT INTO ".$db_prefix."template (title,template,templatesetid)
                                                            VALUES ('header','".addslashes($template_header)."','$style[templatesetid]')");
        } else {
            $DB->query("UPDATE ".$db_prefix."template SET
                               template='".addslashes($template_header)."'
                               WHERE templatesetid='$style[templatesetid]' AND title='header'");
        }
    }

    $template_footer = $_POST[template_footer];
    if (pa_isset($template_footer)) {
        $checktemplate = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."template WHERE title='footer' AND templatesetid='$style[templatesetid]'");
        if (empty($checktemplate)) {
            $DB->query("INSERT INTO ".$db_prefix."template (title,template,templatesetid)
                                                            VALUES ('footer','".addslashes($template_footer)."','$style[templatesetid]')");
        } else {
            $DB->query("UPDATE ".$db_prefix."template SET
                               template='".addslashes($template_footer)."'
                               WHERE templatesetid='$style[templatesetid]' AND title='footer'");
        }
    }

    $replacements = $DB->query("SELECT * FROM ".$db_prefix."replacement
                                         WHERE findword IN ('$findwordlist')
                                         AND replacementsetid=-1");
    while ($replacement = $DB->fetch_array($replacements)) {
           $cachereplacements[$replacement[findword]] = $replacement[replaceword];
    }
    //$DB->query("DELETE FROM ".$db_prefix."replacement WHERE findword IN ('$findwordlist') AND replacementsetid='$style[replacementsetid]'");
    foreach ($_POST[replacement] AS $k=>$v) {

             $checkreplacement = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."replacement
                                                                WHERE findword='".addslashes($k)."'
                                                                AND replacementsetid='$style[replacementsetid]'");
             if (empty($checkreplacement) AND $cachereplacements[$k]!=$v) {
                 $DB->query("INSERT INTO ".$db_prefix."replacement (findword,replaceword,replacementsetid)
                                    VALUES ('".addslashes($k)."','".addslashes($v)."','$style[replacementsetid]')");
             } else if(!empty($checkreplacement))  {
                 $DB->query("UPDATE ".$db_prefix."replacement SET
                                    replaceword='".addslashes($v)."'
                                    WHERE replacementsetid='$style[replacementsetid]'
                                    AND findword='".addslashes($k)."'");
             }

    }
//    print_rr($_POST);
//    exit;
    write_replacement($style[replacementsetid]);
    resetcache();
    redirect("./style.php?action=edit&styleid=$styleid","该风格已更新");

}



if ($_GET[action]=="setdefault") {

    set_defaultstyle($_GET[styleid]);
    redirect("./style.php?action=view","该风格已设成默认");

}

cpfooter();
?>