<?php
error_reporting(7);
require "global.php";

cpheader();

if ($_POST[action]=="update"){

    //print_rr($_POST);
    foreach ($_POST[config] AS $k=>$v) {
             $DB->query("UPDATE ".$db_prefix."setting SET
                                  value='".addslashes(trim($v))."'
                                  WHERE name='".addslashes($k)."'");
    }

    $config_filename = "configs/setting.php";
    $fp = fopen($config_filename,w);

    $settinggroups = $DB->query("SELECT * FROM ".$db_prefix."settinggroup ORDER BY displayorder");
    $contents = "<?php\n";
    while ($settinggroup = $DB->fetch_array($settinggroups)) {

           $contents .= "/*  settinggroup $settinggroup[title]  */\n";

           $settings = $DB->query("SELECT * FROM ".$db_prefix."setting WHERE settinggroupid='$settinggroup[settinggroupid]' ORDER BY displayorder");
           while ($setting = $DB->fetch_array($settings)) {
                  $contents .= "    /*  setting $setting[title]  */\n";
                  if ($setting[type]=="boolean") {
                      $contents .= "\$configuration[$setting[name]] = \"".intval($setting[value])."\";\n";
                  } elseif ($setting[type]=="string") {
                      $contents .= "\$configuration[$setting[name]] = \"".addslashes($setting[value])."\";\n";
                  } elseif ($setting[type]=="text") {
                      $contents .= "\$configuration[$setting[name]] = \"".addslashes($setting[value])."\";\n";
                  } elseif ($setting[type]=="integer") {
                      $contents .= "\$configuration[$setting[name]] = \"".intval($setting[value])."\";\n";
                  }
           }
    }
    $contents .= "?>";

    fwrite($fp,$contents);
    fclose($fp);

    resetcache();    

    redirect("./configurate.php","所有设置已更新");

}


if (!pa_isset($_GET[action]) AND !pa_isset($_POST[action])){

    if ($debug) {
        $sql = " WHERE displayorder!=0 ";
    }
    $sql2 = " displayorder!=0 AND ";
    $settinggroups = $DB->query("SELECT * FROM ".$db_prefix."settinggroup $sql ORDER BY displayorder");
    $cpforms->formheader(array('title'=>'phpArticle 选项设置'));
    while ($settinggroup = $DB->fetch_array($settinggroups)) {
           //$cpforms->tableheader();
           $cpforms->makecategory(array('title'=>$settinggroup[title],
                                         'separate'=>0));


           $settings = $DB->query("SELECT * FROM ".$db_prefix."setting WHERE $sql2 settinggroupid='$settinggroup[settinggroupid]' ORDER BY displayorder");
           while ($setting = $DB->fetch_array($settings)) {
                  if ($setting[type]=="boolean") {
                      $cpforms->makeyesno(array('text'=>"<b>".$setting[title]."</b><br>".$setting[description],
                                                 'name'=>"config[$setting[name]]",
                                                 'selected'=>$setting[value]));
                  } elseif ($setting[type]=="string") {
                      $cpforms->makeinput(array('text'=>"<b>".$setting[title]."</b><br>".$setting[description],
                                                 'name'=>"config[$setting[name]]",
                                                 'value'=>$setting[value]
                                                 ));
                  } elseif ($setting[type]=="text") {
                      $cpforms->maketextarea(array('text'=>"<b>".$setting[title]."</b><br>".$setting[description],
                                                 'name'=>"config[$setting[name]]",
                                                 'value'=>$setting[value]
                                                 ));
                  } elseif ($setting[type]=="integer") {
                      $cpforms->makeinput(array('text'=>"<b>".$setting[title]."</b><br>".$setting[description],
                                                 'name'=>"config[$setting[name]]",
                                                 'value'=>$setting[value],
                                                 'maxlength'=>10,
                                                 'size'=>10
                                                 ));
                  }
           }
           //$cpforms->tablefooter();
    }
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'update'));
    $cpforms->formfooter();

}

if ($_GET[action]=="phpinfo") {
    phpinfo();
}

if ($_GET[action]=="addsetting") {

    $cpforms->formheader(array('title'=>'添加选项'));
    $cpforms->getsettinggroups(array('text'=>'请选择选项组:',
                                      'name'=>'settinggroupid',
                                      'selected'=>$_GET[settinggroupid]));
    $cpforms->makeinput(array('text'=>'选项名:',
                               'name'=>'title'));
    $cpforms->makeinput(array('text'=>'变量:',
                               'name'=>'name'));
    $cpforms->maketextarea(array('text'=>'值:',
                                  'name'=>'value'));
    $cpforms->maketextarea(array('text'=>'说明:',
                                  'name'=>'description'));
    $typeoption = array('string'=>'字符',
                         'text'=>'文本',
                         'integer'=>'整数',
                         'boolean'=>'布尔'
                         );
    $cpforms->makeselect(array('text'=>'类型:',
                                'name'=>'type',
                                'option'=>$typeoption
                                ));
    $cpforms->makeorderinput(array('text'=>'排序:',
                                    'name'=>'displayorder'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'insertsetting'));
    $cpforms->formfooter();

}

if ($_POST[action]=="insertsetting") {

    if (trim($_POST[title])=="") {
        pa_exit("选项名不能为空");
    }
    if (trim($_POST[name])=="") {
        pa_exit("变量不能为空");
    }
    $DB->query("INSERT INTO ".$db_prefix."setting (settinggroupid,title,name,value,description,type,displayorder) VALUES
                       ('".intval($_POST[settinggroupid])."','".addslashes(trim($_POST[title]))."','".addslashes(trim($_POST[name]))."','".addslashes($_POST[value])."','".addslashes($_POST[description])."','".addslashes($_POST[type])."','".intval($_POST[displayorder])."')");
    redirect("configurate.php?action=edit","该选项已添加");


}


if ($_GET[action]=="addsettinggroup") {

    $cpforms->formheader(array('title'=>'添加选项组'));
    $cpforms->makeinput(array('text'=>'组名:',
                               'name'=>'title'));
    $cpforms->makeorderinput(array('text'=>'排序:',
                                    'name'=>'displayorder'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'insertsettinggroup'));
    $cpforms->formfooter();

}


if ($_POST[action]=="insertsettinggroup") {

    $title = trim($_POST[title]);
    if (!pa_isset($title)) {
        pa_exit("组名不能为空");
    }
    $DB->query("INSERT INTO ".$db_prefix."settinggroup (title,displayorder) VALUES
                       ('".addslashes($title)."','".intval($_POST[displayorder])."')");
    pa_exit("该组已添加");

}

if ($_GET[action]=="edit") {

    $settinggroups = $DB->query("SELECT * FROM ".$db_prefix."settinggroup ORDER BY displayorder");

    $cpforms->formheader(array('title'=>'编辑选项'));
    while ($settinggroup = $DB->fetch_array($settinggroups)) {
           echo "<tr class=\"tbcat\" id=cat>";
           echo "<td><input class=order type=text value=\"$settinggroup[displayorder]\" name=\"settinggroup_displayorder[$settinggroup[settinggroupid]]\" maxlength=\"3\"> ".htmlspecialchars($settinggroup[title])."</td>";
           echo "<td align=\"center\" width=10% nowrap>[<a href=\"configurate.php?action=editsettinggroup&settinggroupid=$settinggroup[settinggroupid]\">编辑</a>] [<a href=\"configurate.php?action=killsettinggroup&settinggroupid=$settinggroup[settinggroupid]\">删除</a>] [<a href=\"configurate.php?action=addsetting&settinggroupid=$settinggroup[settinggroupid]\">添加选项</a>]</td>";
           echo "</tr>";

           $settings = $DB->query("SELECT * FROM ".$db_prefix."setting WHERE settinggroupid='$settinggroup[settinggroupid]' ORDER BY displayorder");
           while ($setting = $DB->fetch_array($settings)) {
                  echo "<tr class=".getrowbg().">";
                  echo "<td><input class=order type=text value=\"$setting[displayorder]\" name=\"setting_displayorder[$setting[settingid]]\" maxlength=\"3\"> ".htmlspecialchars($setting[title])."</td>";
                  echo "<td align=\"center\" nowrap>[<a href=\"configurate.php?action=editsetting&settingid=$setting[settingid]\">编辑</a>] [<a href=\"configurate.php?action=killsetting&settingid=$setting[settingid]\">删除</a>]</td>";
                  echo "</tr>";
          }
    }
    //$cpforms->formfooter();
    echo "<tr class=tbcat>
            <td colspan=\"4\" align=\"center\">
            <input type=\"hidden\"  name=\"action\" value=\"updatedisplayorder\">
            <input type=\"submit\" class=\"bginput\" value=\"更新排序\">
            </td>
          </tr>";
    $cpforms->tablefooter();
}

if ($_POST[action]=="updatedisplayorder") {

    //print_rr($_POST);
    //exit;
    foreach($_POST[setting_displayorder] as $settingid=>$order){
            $DB->query("UPDATE ".$db_prefix."setting SET
                                 displayorder='$order'
                                 WHERE settingid='$settingid'");
    }
    foreach($_POST[settinggroup_displayorder] as $settinggroupid=>$order){
            $DB->query("UPDATE ".$db_prefix."settinggroup SET
                                 displayorder='$order'
                                 WHERE settinggroupid='$settinggroupid'");
    }

    redirect("configurate.php?action=edit","所有排序已更新");


}

if ($_GET[action]=="editsetting") {

    $settingid = intval($_GET[settingid]);
    $setting = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."setting WHERE settingid='$settingid'");
    $cpforms->formheader(array('title'=>'编辑选项'));
    $cpforms->getsettinggroups(array('text'=>'请选择选项组:',
                                      'name'=>'settinggroupid',
                                      'selected'=>$setting[settinggroupid]));
    $cpforms->makeinput(array('text'=>'选项名:',
                               'name'=>'title',
                               'value'=>$setting[title]));
    $cpforms->makeinput(array('text'=>'变量:',
                               'name'=>'name',
                               'value'=>$setting[name]));
    $cpforms->maketextarea(array('text'=>'值:',
                                  'name'=>'value',
                                  'value'=>$setting[value]));
    $cpforms->maketextarea(array('text'=>'说明:',
                                  'name'=>'description',
                                  'value'=>$setting[description]
                                  ));
    $typeoption = array('string'=>'字符',
                         'text'=>'文本',
                         'integer'=>'整数',
                         'boolean'=>'布尔'
                         );
    $cpforms->makeselect(array('text'=>'类型:',
                                'name'=>'type',
                                'option'=>$typeoption,
                                'selected'=>$setting[type]
                                ));
    $cpforms->makeorderinput(array('text'=>'排序:',
                                    'name'=>'displayorder',
                                  'value'=>$setting[displayorder]));
    $cpforms->makehidden(array('name'=>'settingid','value'=>$setting[settingid]));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'updatesetting'));
    $cpforms->formfooter();

}

if ($_POST[action]=="updatesetting") {

    $settingid = intval($_POST[settingid]);
    $title = trim($_POST[title]);
    if (!pa_isset($title)) {
        pa_exit("组名不能为空");
    }
    $DB->query("UPDATE ".$db_prefix."setting SET
                       settinggroupid='".intval($_POST[settinggroupid])."',
                       title='".addslashes(trim($_POST[title]))."',
                       name='".addslashes(trim($_POST[name]))."',
                       value='".addslashes($_POST[value])."',
                       description='".addslashes($_POST[description])."',
                       type='".addslashes($_POST[type])."',
                       displayorder='".intval($_POST[displayorder])."'
                       WHERE settingid='$settingid'");
    resetcache();
    redirect("configurate.php?action=edit","该选项已更新");

}

if ($_GET[action]=="editsettinggroup") {

    $settinggroupid = intval($_GET[settinggroupid]);
    $settinggroup = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."settinggroup WHERE settinggroupid='$settinggroupid'");
    $cpforms->formheader(array('title'=>'编辑选项组'));
    $cpforms->makeinput(array('text'=>'组名:',
                               'name'=>'title',
                               'value'=>$settinggroup[title]));

    $cpforms->makeorderinput(array('text'=>'排序:',
                                    'name'=>'displayorder',
                                  'value'=>$settinggroup[displayorder]));
    $cpforms->makehidden(array('name'=>'settinggroupid','value'=>$settinggroup[settinggroupid]));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'updatesettinggroup'));
    $cpforms->formfooter();

}


if ($_POST[action]=="updatesettinggroup") {

    $settinggroupid = intval($_POST[settinggroupid]);
    $title = trim($_POST[title]);
    if (!pa_isset($title)) {
        pa_exit("组名不能为空");
    }
    $DB->query("UPDATE ".$db_prefix."settinggroup SET
                       title='".addslashes(trim($_POST[title]))."',
                       displayorder='".intval($_POST[displayorder])."'
                       WHERE settinggroupid='$settinggroupid'");

    resetcache();
    redirect("configurate.php?action=edit","该选项组已更新");

}


cpfooter();
?>