<?php
error_reporting(7);
require "global.php";

cpheader();

if ($_GET[action]=="add"){

    $cpforms->formheader(array('title'=>'添加变量'));
    $cpforms->makehidden(array('name'=>'action','value'=>'insert'));
    $cpforms->getreplacementsets(array('text'=>'变量套系:',
                                        'name'=>'replacementsetid',
                                        'selected'=>$_GET[replacementsetid]));

    $cpforms->makeinput(array('text'=>'变量:',
                               'name'=>'findword'));
    $cpforms->maketextarea(array('text'=>'替换为:',
                               'name'=>'replaceword'));

    $cpforms->formfooter();

}


if ($_POST[action]=="insert"){

//    print_rr($_POST);
    $findword = trim($_POST[findword]);
    if (trim($findword)=="") {
        pa_exit("变量不能为为空");
    }

    $checkreplacement = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."replacement
                                                       WHERE replacementsetid='$_POST[replacementsetid]'
                                                       AND findword='".addslashes($_POST[findword])."'");
    if (!empty($checkreplacement)) {
        pa_exit("该变量已存在");
    }
    $DB->query("INSERT INTO ".$db_prefix."replacement (replacementsetid,findword,replaceword)
                       VALUES ('$_POST[replacementsetid]','".addslashes($findword)."','".addslashes($_POST[replaceword])."')");

    write_replacement($_POST[replacementsetid]);
    resetcache();
    redirect("./replacement.php?expand=1&replacementsetid=$replacementsetid","该变量已添加");

}



if ($_GET[action]=="mod")  {

    $replacementsetid = $_GET[replacementsetid];

    $checkreplacement = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."replacement WHERE replacementsetid='$replacementsetid' AND findword='".addslashes($_GET[findword])."'");
    if (empty($checkreplacement)) {
        $replacement = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."replacement WHERE replacementsetid='-1' AND findword='".addslashes($_GET[findword])."'");
    } else {
        $replacement = $checkreplacement;
    }

    //print_rr($replacement);

    $cpforms->formheader(array('title'=>'编辑变量'));
    $cpforms->makehidden(array('name'=>'action','value'=>'update'));
    $cpforms->makehidden(array('name'=>'oldreplacementsetid',
                                'value'=>$_GET[replacementsetid]));


    $cpforms->getreplacementsets(array('text'=>'变量套系:',
                                        'name'=>'replacementsetid',
                                        'selected'=>$_GET[replacementsetid]));
    $cpforms->makehidden(array('name'=>'oldfindword',
                                'value'=>$replacement[findword]));

    $cpforms->makeinput(array('text'=>'变量:',
                               'name'=>'findword',
                               'value'=>$replacement[findword]
                               ));
    $cpforms->maketextarea(array('text'=>'替换为:',
                               'name'=>'replaceword',
                               'value'=>$replacement[replaceword]
                               ));

    $cpforms->formfooter();



}


if ($_POST['action']=="update"){

    $findword = trim($_POST[findword]);
    $replaceword = $_POST[replaceword];

    $replacementsetid = intval($_POST[replacementsetid]);

    if (trim($findword)=="") {
        pa_exit("变量不能为为空");
    }

    $replacementsetid = $_POST[replacementsetid];


    $checkreplacement = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."replacement
                                                       WHERE replacementsetid='$replacementsetid' AND findword='".addslashes($_POST[findword])."'
                                                       ORDER BY findword");


    if (!empty($checkreplacement) AND $_POST[oldreplacementsetid]!=$_POST[replacementsetid]) {
        pa_exit("该变量已存在.");
    }

    $DB->query("DELETE FROM ".$db_prefix."replacement
                       WHERE findword='".addslashes($_POST[oldfindword])."' AND replacementsetid='$_POST[oldreplacementsetid]'");

    $DB->query("INSERT INTO ".$db_prefix."replacement (replacementsetid,findword,replaceword)
                       VALUES ('".addslashes($replacementsetid)."','".addslashes($findword)."','".addslashes($replaceword)."')");

    write_replacement($_POST[replacementsetid]);
    resetcache();
    redirect("./replacement.php?expand=1&replacementsetid=$replacementsetid","该变量已更新");

}


if ($_GET[action]=="kill" OR $_GET[action]=="restore"){

    if ($_GET[action]=="kill") {
        $cpforms->formheader(array('title'=>'确实要删除该变量?'));
    } else {
        $cpforms->formheader(array('title'=>'确实要还原该变量?'));
    }
    $cpforms->makehidden(array('name'=>'action','value'=>'remove'));
    $cpforms->makehidden(array('name'=>'replacementsetid','value'=>$_GET[replacementsetid]));
    $cpforms->makehidden(array('name'=>'findword','value'=>$_GET[findword]));
    $cpforms->formfooter(array('confirm'=>1));
}


if ($_POST[action]=="remove"){

    $DB->query("DELETE FROM ".$db_prefix."replacement
                       WHERE findword='".addslashes($_POST[findword])."'
                       AND replacementsetid='$_POST[replacementsetid]'");

    resetcache();
    write_replacement($_POST[replacementsetid]);
    redirect("./replacement.php?expand=1&replacementsetid=$replacementsetid","该变量已删除");

}


if($_GET[action]=="addset"){

   $cpforms->formheader(array('title'=>'添加变量套系'));
   $cpforms->makehidden(array('name'=>'action','value'=>'insertset'));
   $cpforms->makeinput(array('text'=>'变量套系:','name'=>'title'));
   $cpforms->formfooter();

}



if ($_POST[action]=="insertset") {

    $title = htmlspecialchars(trim($_POST[title]));
    if ($title=="") {
        "变量套系名不能为空";
    }
    $DB->query("INSERT INTO ".$db_prefix."replacementset (title) VALUES ('".addslashes($title)."')");
    $replacementsetid = $DB->insert_id();
    write_replacement($replacementsetid);
    redirect("./replacement.php?action=edit","该套系已添加");

}


if ($_GET[action]=="killset"){

    $cpforms->formheader(array('title'=>'确实要删除该变量套系?'));
    $cpforms->makehidden(array('name'=>'action','value'=>'removeset'));
    $cpforms->makehidden(array('name'=>'replacementsetid','value'=>$replacementsetid));
    $cpforms->formfooter();

}



if ($_POST[action]=="removeset"){

    if ($replacementsetid!=1) {
        $DB->query("DELETE FROM ".$db_prefix."replacement WHERE replacementsetid='$replacementsetid'");
        $DB->query("DELETE FROM ".$db_prefix."replacementset WHERE replacementsetid='$replacementsetid'");
        unlink("configs/replacement_$replacementsetid.php");
        resetcache();
        redirect("./replacement.php?action=edit","该变量套系已删除");
    } else {
        echo "你不能删除默认变量套系";
    }

}


if ($_GET[action]=="edit" OR $expand=="0")  {

    $cpforms->tableheader();
    echo "<tr class=\"tbhead\"><td colspan=\"2\">变量套系列表</td></tr>\n";

    $replacementsets = $DB->query("SELECT * FROM ".$db_prefix."replacementset");
    if ($debug==1) {
        echo "<tr class=".getrowbg().">
                  <td>Global Replacementset</td>
                  <td>[<a href=replacement.php?expand=1&replacementsetid=-1>展开</a>] [<a href=replacement.php?action=add&replacementsetid=-1>添加变量</a>]</td>
              </tr>";
    }
    while ($replacementset=$DB->fetch_array($replacementsets)) {
           if($replacementset[replacementsetid]!=1){
              $l = "[<a href=\"replacement.php?action=killset&replacementsetid=$replacementset[replacementsetid]\">删除</a>]";
           }
           echo "</td></tr>\n";
           echo "<tr class=".getrowbg().">
                     <td>$replacementset[title]</td>
                     <td>[<a href=replacement.php?expand=1&replacementsetid=$replacementset[replacementsetid]>展开</a>] [<a href=replacement.php?action=add&replacementsetid=$replacementset[replacementsetid]>添加自定义变量</a>] $l</td>
                 </tr>";
    }
    $cpforms->tablefooter();

}



if ($_GET[expand]=="1"){

    $replacementsetid = intval($_GET[replacementsetid]);
    $replacementset = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."replacementset
                                                     WHERE replacementsetid='$replacementsetid'");
    if($replacementsetid==-1){
       $replacementset[title]="Global Replacementset";
    }


    echo "<b>$replacementset[title]</b> [<a href=replacement.php?expand=0>折叠</a>]
           [<a href=replacement.php?action=add&replacementsetid=$replacementset[replacementsetid]>添加自定义变量</a>]";

    $cpforms->tableheader();
    echo "<tr id=cat class=tbhead>
           <td>变量</td>
           <td align=center width=5% nowrap>编辑</td>
          </tr>";

    $replacements = $DB->query("SELECT * FROM ".$db_prefix."replacement WHERE replacementsetid=-1");
    while ($replacement = $DB->fetch_array($replacements)) {
           $cachereplacement[$replacement[findword]] = $replacement;
    }
    unset($replacement);
    $DB->free_result($replacements);

    //print_rr($cachereplacement);

    $replacementlists = $DB->query("SELECT DISTINCT r1.* FROM ".$db_prefix."replacement AS r1
                                       LEFT JOIN ".$db_prefix."replacement AS r2 ON (r1.findword=r2.findword AND r2.replacementsetid=-1)
                                       WHERE r1.replacementsetid='$replacementsetid' AND ISNULL(r2.replacementid)
                                       ORDER BY r1.findword");
    while ($replacementlist = $DB->fetch_array($replacementlists)) {

           echo "<tr class=".getrowbg().">
                     <td>$replacementlist[title]</td>
                     <td nowrap>[<a href=replacement.php?action=mod&replacementsetid=$replacementsetid&findword=$replacementlist[findword]>编辑</a>]
                         [<a href=replacement.php?action=kill&replacementsetid=$replacementsetid&findword=$replacementlist[findword]>删除</a>]
                     </td>
                 </tr>";

    }
    $replacementlists = $DB->query("SELECT DISTINCT r1.findword as findword,r1.replacementid as replacementid,r2.findword as findword2
                                       FROM ".$db_prefix."replacement AS r1
                                       LEFT JOIN ".$db_prefix."replacement AS r2 ON (r1.findword=r2.findword AND r2.replacementsetid=$replacementsetid)
                                       WHERE r1.replacementsetid=-1
                                       ORDER BY r1.findword");

    while ($replacementlist = $DB->fetch_array($replacementlists)){
           if ($replacementlist[findword2]==""){ //默认,green

               unset($l);
               if ($debug==1 AND $replacementsetid==-1){
                   $l = "[<a href=\"replacement.php?action=kill&replacementsetid=$replacementsetid&findword=$replacementlist[findword]\">删除</a>]";
               }
               echo "<tr class=".getrowbg().">
                         <td><font color=green>$replacementlist[findword]</font></td>
                         <td nowrap>
                             [<a href=replacement.php?action=mod&replacementsetid=$replacementsetid&findword=$replacementlist[findword]>编辑</a>]
                             $l
                         </td>
                    </tr>";

           } else {//编辑过的,blue

               unset($l);
               if ($debug==1 AND $replacementsetid==-1){
                   $l = "[<a href=\"replacement.php?action=kill&replacementsetid=$replacementsetid&findword=$replacementlist[findword]\">删除</a>]";
               } else {
                   $l = "[<a href=\"replacement.php?action=restore&replacementsetid=$replacementsetid&findword=$replacementlist[findword]\">还原</a>]";
               }
               echo "<tr class=".getrowbg().">
                         <td><font color=blue>$replacementlist[findword]</font></td>
                         <td align=center nowrap>[<a href=replacement.php?action=mod&replacementsetid=$replacementsetid&findword=$replacementlist[findword]>编辑</a>]
                             $l
                         </td>
                    </tr>";

           }
    }

    $cpforms->tablefooter();

} //end if


cpfooter();
?>