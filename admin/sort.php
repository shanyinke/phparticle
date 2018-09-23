<?php
error_reporting(7);
require "global.php";

cpheader();

$cachesorts = cachesorts();


function validate_sortid($sortid) {

         global $DB,$db_prefix;

         $sortid = intval($sortid);

         $sort = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."sort WHERE sortid='$sortid'");
         if (empty($sort)) {
             pa_exit("该分类不存在");
         }
         return $sort;

}


function buildsortnav($sortid){

         global $parentsort;
         if ($sortid!=-1){
             foreach ($parentsort[$sortid] as $psid => $stitle) {
                      $navbit = " \\ <a href=\"./sort.php?action=edit&sortid=$sortid\">$stitle</a>";
                      $navbit = buildsortnav($psid).$navbit;
             }
         }
         return $navbit;
}




function listsort($sortid="-1",$level=1){

         global $DB,$db_prefix,$managercache,$cachesorts,$expand;

         if (isset($cachesorts[$sortid])) {

             foreach ($cachesorts[$sortid] as $parentid => $sort){
                      if (empty($cachesorts[$sort[sortid]]) OR $expand==1) {
                          $img = "<img src='../images/minus.gif' border=0 align=absmiddle>";
                      } else {
                          $img = "<a href=sort.php?action=edit&sortid=$sort[sortid]><img src='../images/plus.gif' border=0 align=absmiddle></a>";
                      }

                      echo "<tr class=".getrowbg().">\n<td>";
                      echo str_repeat("<img src='../images/branch.gif' align=absmiddle border=0>",$level-1)."
                           <input class=order type=text value=$sort[displayorder] name=\"displayorder[$sort[sortid]]\" maxlength=\"3\">$img
                           <b><a target=_blank href=../sort.php?sortid=$sort[sortid]>$sort[title]</a></b>\n";

                      echo "</td><td nowrap> [<a href=index2.php?mod=mksort&sortid=$sort[sortid]>静态生成</a>][<a href=sort.php?action=mod&sortid=$sort[sortid]>编辑</a>]
                             [<a href=sort.php?action=kill&sortid=$sort[sortid]>删除</a>]";

                     echo " [<a href=sort.php?action=add&sortid=$sort[sortid]>添加子分类</a>]";

                      echo "<br>
                             [<a href=sort.php?action=addmanager&sortid=$sort[sortid]>添加管理员</a>]
                             [<a href=sort.php?action=killmanager&sortid=$sort[sortid]>删除管理员</a>]
                            </td><td nowrap>\n";

                      if(isset($managercache[$sort[sortid]])){
                         echo "<select>
                                <option selected>管理员</option>
                                <option>---------</option>";
                         foreach($managercache[$sort[sortid]] as $key=>$manager){
                                 echo "<option>$manager</option>\n";
                         }
                         echo "</select>";
                      }


                     echo "</td></tr>\n";
                     if ($expand==1) {
                         listsort($sort[sortid],$level+1);
                     }
             }
         }
}





if ($action=="add")  {

    $cpforms->formheader(array('title'=>'添加分类'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'insert'));

    $cpforms->makeinput(array('text'=>'分类名:',
                               'name'=>'title'));


    $cpforms->makecategory("分类信息");
    $cpforms->makeyesno(array('text'=>'是否显示分类信息?<br>包括: 分类图标与分类介绍,如果分类图标与分类介绍同时为空,分类信息也不会显示',
                               'name'=>'showsoftinfos'
                               ));
    $cpforms->makeinput(array('text'=>'图标:<br>请输入图标图径',
                               'name'=>'img',
                               'maxlength'=>100));

    $cpforms->maketextarea(array('text'=>'分类介绍:',
                                  'name'=>'sortdes'));

    $cpforms->makecategory("分类显示");
    $cpforms->getstyles(array('text'=>'显示风格:',
                               'name'=>'styleid',
                               ));

    $cpforms->makeinput(array('text'=>'分多少列显示子分类?',
                              'name'=>'division_sort',
                              'size'=>3,
                              'value'=>3,
                              'maxlength'=>1
                              ));
    $cpforms->makeinput(array('text'=>'分多少列显示文章列表?',
                              'name'=>'division_article',
                              'size'=>3,
                              'value'=>1,
                              'maxlength'=>1
                              ));

    $cpforms->makeinput(array('text'=>'每页显示多少篇文章?',
                              'name'=>'perpage',
                              'size'=>3,
                              'value'=>10,
                              'maxlength'=>3
                              ));

    $cpforms->makeorderinput(array('text'=>'排序:',
                                    'name'=>'displayorder'));

    $cpforms->getsortlist(array('text'=>'根分类:',
                                 'name'=>'parentid',
                                 'extra'=>array('-1'=>'作为根分类'),
                                 'selected'=>$sortid));


    $cpforms->makecategory("首页文章调用显示设置,以下设置并不继承子分类");
    $cpforms->makeyesno(array('text'=>'是否允许该分类中的文章显示在最近更新(详细)排行中?',
                               'name'=>'showinrecent',
                               'selected'=>1));
    $cpforms->makeyesno(array('text'=>'是否允许该分类中的文章显示在最近更新(简单)排行中?',
                               'name'=>'showinlast',
                               'selected'=>1));
    $cpforms->makeyesno(array('text'=>'是否允许该分类中的文章显示在热门文章(点击)排行中?',
                               'name'=>'showinhot',
                               'selected'=>1));

    $cpforms->makeyesno(array('text'=>'是否允许该分类中的文章显示在热门文章(评分)排行中?',
                               'name'=>'showinrate',
                               'selected'=>1));

    $cpforms->makecategory("分类文章调用显示设置,以下设置并不继承子分类");
    $cpforms->makeinput(array('text'=>'显示多少篇热门文章(按评分排行)?<br>如果设置为 "0",将不显示.',
                               'name'=>'ratearticlenum',
                               'value'=>10,
                               'maxlength'=>3,
                               'size'=>3));
    $cpforms->makeinput(array('text'=>'显示多少篇热门文章(按点击排行)?<br>如果设置为 "0",将不显示.',
                               'name'=>'hotarticlenum',
                               'value'=>10,
                               'maxlength'=>3,
                               'size'=>3));


    $cpforms->formfooter();

}



if ($_POST[action]=="insert"){

    $title = htmlspecialchars(trim($title));
    $img = trim($img);
    if ($title==""){
        pa_exit("分类名不能为空");
    }
    $division_sort = intval($_POST[division_sort]);
    if ($division_sort>9 OR $division_sort<1) {
        $division_sort = 3;
    }
    $division_article = intval($_POST[division_article]);
    if ($division_article>9 OR $division_article<1) {
        $division_article = 3;
    }

    $perpage = intval($_POST[perpage]);
    if ($perpage<1) {
        $perpage = 10;
    }

    $DB->query("INSERT INTO ".$db_prefix."sort (title,img,description,displayorder,parentid,showinrecent,showinhot,showinrate,showinlast,division_sort,division_article,perpage,showsortinfos,styleid,ratearticlenum,hotarticlenum)
                       VALUES ('".addslashes($title)."','".addslashes($img)."','".addslashes($description)."','".intval($displayorder)."','".intval($parentid)."','".intval($showinrecent)."','".intval($showinhot)."','".intval($showinrate)."','".intval($showinlast)."','$division_sort','$division_article','$perpage','".intval($_POST[showsortinfos])."','$_POST[styleid]','".intval($_POST[ratearticlenum])."','".intval($_POST[hotarticlenum])."')");

    $sortid = $DB->insert_id();

    updateparentlist($sortid);
    resetcache();
    redirect("./sort.php?action=edit","该分类已添加");

}



if ($action=="mod")  {

    $sort = validate_sortid($sortid);

    $cpforms->formheader(array('title'=>'编辑分类'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'update'));

    $cpforms->makeinput(array('text'=>'分类名:',
                               'name'=>'title',
                               'value'=>$sort[title]));

    $cpforms->makecategory("分类信息");
    $cpforms->makeyesno(array('text'=>'是否显示分类信息?<br>包括: 分类图标与分类介绍,如果分类图标与分类介绍同时为空,分类信息也不会显示',
                               'name'=>'showsortinfos',
                               'selected'=>$sort[showsortinfos]
                               ));

    $cpforms->makeinput(array('text'=>'图标:<br>请输入图标图径',
                               'name'=>'img',
                               'value'=>$sort[img],
                               'maxlength'=>100));

    $cpforms->maketextarea(array('text'=>'分类介绍:',
                                  'name'=>'sortdes',
                                  'value'=>$sort[description]));

    $cpforms->makecategory("分类显示");
    $cpforms->getstyles(array('text'=>'显示风格:',
                               'name'=>'styleid',
                               'selected'=>$sort[styleid]
                               ));
    $cpforms->makeinput(array('text'=>'分多少列显示子分类?',
                              'name'=>'division_sort',
                              'size'=>3,
                              'value'=>$sort[division_sort],
                              'maxlength'=>1
                              ));
    $cpforms->makeinput(array('text'=>'分多少列显示文章列表?',
                              'name'=>'division_article',
                              'size'=>3,
                              'value'=>$sort[division_article],
                              'maxlength'=>1
                              ));
    $cpforms->makeinput(array('text'=>'每页显示多少篇文章?',
                              'name'=>'perpage',
                              'size'=>3,
                              'value'=>$sort[perpage],
                              'maxlength'=>3
                              ));

    $cpforms->makeorderinput(array('text'=>'排序:',
                                    'name'=>'displayorder',
                                    'value'=>$sort[displayorder]
                                    ));

    $subsorts = getsubsorts($sort[sortid]);
    //$subsort_array = explode(",",$subsorts);
    $subsort_array = array_flip(explode(",",$subsorts));

    //print_rr($subsort_array);

    $cpforms->getsortlist(array('text'=>'根分类:',
                                 'name'=>'parentid',
                                 'extra'=>array('-1'=>'作为根分类'),
                                 'selected'=>$sort[parentid],
                                 'filter'=>$subsort_array));



    $cpforms->makecategory("首页文章调用显示设置,以下设置并不继承子分类");
    $cpforms->makeyesno(array('text'=>'是否允许该分类中的文章显示在最近更新(详细)排行中?',
                               'name'=>'showinrecent',
                               'selected'=>$sort[showinrecent]));
    $cpforms->makeyesno(array('text'=>'是否允许该分类中的文章显示在最近更新(简单)排行中?',
                               'name'=>'showinlast',
                               'selected'=>$sort[showinlast]));
    $cpforms->makeyesno(array('text'=>'是否允许该分类中的文章显示在热门文章(点击)排行中?',
                               'name'=>'showinhot',
                               'selected'=>$sort[showinhot]));

    $cpforms->makeyesno(array('text'=>'是否允许该分类中的文章显示在热门文章(评分)排行中?',
                               'name'=>'showinrate',
                               'selected'=>$sort[showinrate]));
    $cpforms->makehidden(array('name'=>'sortid',
                                'value'=>$sortid));

    $cpforms->makecategory("分类文章调用显示设置,以下设置并不继承子分类");
    $cpforms->makeinput(array('text'=>'显示多少篇热门文章(按评分排行)?<br>如果设置为 "0",将不显示.',
                               'name'=>'ratearticlenum',
                               'value'=>$sort[ratearticlenum],
                               'maxlength'=>3,
                               'size'=>3));
    $cpforms->makeinput(array('text'=>'显示多少篇热门文章(按点击排行)?<br>如果设置为 "0",将不显示.',
                               'name'=>'hotarticlenum',
                               'value'=>$sort[hotarticlenum],
                               'maxlength'=>3,
                               'size'=>3));
    $cpforms->formfooter();

}



if ($_POST[action]=="update"){


    $title = htmlspecialchars(trim($title));
    $img = trim($img);
    if ($title==""){
        pa_exit("分类名不能为空");
    }

    $subsorts = getsubsorts($sortid);
    //$subsort_array = explode(",",$subsorts);
    $subsort_array = array_flip(explode(",",$subsorts));

    //print_rr($subsort_array);
    if (isset($subsort_array[$parentid])) {
        pa_exit("父分类无效");
    }
    //exit;

    $division_sort = intval($_POST[division_sort]);
    if ($division_sort>9 OR $division_sort<1) {
        $division_sort = 3;
    }
    $division_article = intval($_POST[division_article]);
    if ($division_article>9 OR $division_article<1) {
        $division_article = 3;
    }

    $perpage = intval($_POST[perpage]);
    if ($perpage<1) {
        $perpage = 10;
    }

    $DB->query("UPDATE ".$db_prefix."sort SET
                       title='".addslashes($title)."',
                       img='".addslashes($img)."',
                       description='".addslashes($sortdes)."',
                       displayorder='".addslashes($displayorder)."',
                       parentid='".addslashes($parentid)."',
                       showinrecent='$showinrecent',
                       showinhot='$showinhot',
                       showinrate='$showinrate',
                       showinlast='$showinlast',
                       division_sort='$division_sort',
                       division_article='$division_article',
                       perpage='$perpage',
                       showsortinfos='$_POST[showsortinfos]',
                       styleid='$_POST[styleid]',
                       ratearticlenum='".intval($_POST[ratearticlenum])."',
                       hotarticlenum='".intval($_POST[hotarticlenum])."'
                       WHERE sortid='$sortid' ");


    $subsort_array = explode(",",getsubsorts($sortid));
    $subsort_num = count($subsort_array);

    for ($i=0;$i<$subsort_num;$i++) {
         updateparentlist($subsort_array[$i]);
    }

    resetcache();
    redirect("./sort.php?action=edit","该分类已更新");

}


if ($action=="kill"){

    $sort = validate_sortid($sortid);
    $cpforms->formheader(array('title'=>'确实要删除该分类?该分类与该分类的子分类,及该分类与该分类的子分类中的所有文章将均会被删除.'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'remove'));
    $cpforms->makehidden(array('name'=>'sortid',
                                'value'=>$sort[sortid]));

    $cpforms->formfooter(array('confirm'=>1));

}



if ($action=="remove"){

    $sortid = intval($_POST[sortid]);

    $sorts = $DB->query("SELECT * FROM ".$db_prefix."sort WHERE sortid='$sortid' OR INSTR(parentlist,',$sortid,')>0");

    while ($sort = $DB->fetch_array($sorts)) {
           $sortids_array[] = $sort[sortid];
    }
    if (is_array($sortids_array)) {
        $sortids = implode(",",$sortids_array);
    } else {
        $sortids = $sortids_array;
    }
	$subdirs = mkdirname($sortid,-1,0,0,0);//get_sortdirs($sortid);
	if ($subdirs)
        {
                $writedir = HTMLDIR . "/" . $subdirs;
		deltree("../".$writedir);
	}
    $articles = $DB->query("SELECT * FROM ".$db_prefix."article WHERE sortid IN (0$sortids)");
    while ($article = $DB->fetch_array($articles)) {
           $DB->query("DELETE FROM ".$db_prefix."articletext WHERE articleid='$article[articleid]'");
           $DB->query("DELETE FROM ".$db_prefix."articlerate WHERE articleid='$article[articleid]'");
           $DB->query("DELETE FROM ".$db_prefix."relatedlink WHERE articleid='$article[articleid]'");

           $DB->query("DELETE FROM ".$db_prefix."favorite WHERE articleid='$article[articleid]'");
           $comments = $DB->query("SELECT * FROM ".$db_prefix."comment WHERE articleid='$article[articleid]'");
           while ($comment = $DB->fetch_array($comments)) {
                  $DB->query("DELETE FROM ".$db_prefix."message WHERE commentid='$comment[commentid]'");
           }
           $DB->query("DELETE FROM ".$db_prefix."comment WHERE articleid='$article[articleid]'");
    }

    $DB->query("DELETE FROM ".$db_prefix."article WHERE sortid IN (0$sortids)");
    $DB->query("DELETE FROM ".$db_prefix."sort WHERE sortid IN (0$sortids)");

    resetcache();
    redirect("./sort.php?action=edit","所有分类已删除");

}



if ($_GET[action]=="addmanager"){

    $cpforms->formheader(array('title'=>'添加管理员'));
    $cpforms->getsortlist(array('text'=>'分类:',
                              'name'=>'sortid',
                              'selected'=>$_GET[sortid]));

    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'doinsertmanager'));
    $cpforms->makeinput(array('text'=>'用户名:<br>请先确认该用户已存在,<a href="user.php?action=search" target="_blank">查找会员</a>',
                               'name'=>'username'));
    $cpforms->formfooter();

}


if ($_POST[action]=="doinsertmanager") {

    $username = htmlspecialchars(trim($_POST[username]));

    if (!pa_isset($username)) {
        pa_exit("还未输入用户名");
    }
    $checkuser = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."user
                                                WHERE username='".addslashes($username)."'");
    if (empty($checkuser)) {
        pa_exit("该用户不存在,请先添加");
    }

    $sortid = intval($_POST[sortid]);
    $exist = $DB->fetch_one_array("SELECT userid FROM ".$db_prefix."manager
                                        WHERE userid='$checkuser[userid]' AND sortid='$sortid'");
    if ($exist) {
        redirect("sort.php?action=edit","该管理已存在");
        exit;
    }
    $DB->query("INSERT INTO ".$db_prefix."manager (sortid,userid)
                       VALUES ('$sortid','$checkuser[userid]') ");

    redirect("./sort.php?action=edit","该管理员已添加");

}



if ($_GET[action]=="killmanager"){

    $sortid = intval($_GET[sortid]);
    $sort = validate_sortid($sortid);

    $cpforms->formheader(array('title'=>"删除 ".$sort[title]." 分类的管理员"));
    $cpforms->makehidden(array('name'=>'sortid',
                                'value'=>$sort[sortid]));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'removemanager'));

    $cpforms->getmanagers(array('text'=>'请选择要删除的管理员',
                                 'name'=>'managerid',
                                 'sortid'=>$sortid));


    $cpforms->formfooter();

}


if ($_POST[action]=="removemanager"){

    $managerid = intval($_POST[managerid]);
    $DB->query("DELETE FROM ".$db_prefix."manager WHERE managerid='$managerid'");


    redirect("./sort.php?action=edit","该管理员已成功删除");

}



if ($_POST[action]=="updatedisplayorder") {

    foreach($displayorder as $sortid=>$order){
            $DB->query("UPDATE ".$db_prefix."sort SET displayorder='$order' WHERE sortid='$sortid'");
    }

    resetcache();

    redirect("./sort.php?action=edit","所有分类的排序已更新");

}


if ($action=="updateparentlist") {

    if (function_exists("set_time_limit")==1 and get_cfg_var("safe_mode")==0) {
       @set_time_limit(1200);
    }

    //$sortids = getsubsorts($sortid);
    //$sortids_array = explode(",",$sortids);
    $sorts = $DB->query("SELECT sortid FROM ".$db_prefix."sort");
    while ($sort = $DB->fetch_array($sorts)) {
           updateparentlist($sort[sortid]);
    }
    redirect("./sort.php?action=counter","所有分类已更新");

}



if ($action=="edit")  {

    if (empty($cachesorts)) {
        pa_exit("还没有任何分类");
    }

    if (empty($sortid)) {
        $sortid="-1";
    }
    if ($expand==1) {
        $expandorcontact = "<a href=sort.php?action=edit&expand=0>收缩所有</a>";
    } else {
        $expandorcontact = "<a href=sort.php?action=edit&expand=1>展开所有</a>";
    }


    $cpforms->tableheader();
    echo "<tr class=".getrowbg().">
            <td>
               导航: <a href=sort.php?action=edit>首页</a>".buildsortnav($sortid)."
            </td>
            <td align=right>
               $expandorcontact
            </td>
          </tr>";
    $cpforms->tablefooter();
    echo "<br>";

    echo "<table class=\"tableoutline\" cellpadding=\"4\" cellspacing=\"1\" border=\"0\" width=\"100%\" align=\"center\">
           <form action=\"sort.php\" method=\"POST\">
               <tr class=\"tbhead\">
                <td nowrap width=\"70%\"> 排序/分类 </td>
                <td nowrap align=\"center\"> 编辑 </td>
                <td width=5% nowrap align=\"center\"> 管理员 </td>
               </tr>\n";

    $managers = $DB->query("SELECT manager.sortid,user.username,user.userid FROM ".$db_prefix."manager as manager
                                    LEFT JOIN ".$db_prefix."user as user
                                    on manager.userid=user.userid");
    while($manager=$DB->fetch_array($managers)){
          $managercache["$manager[sortid]"][] = $manager[username];
    }

    listsort($sortid);

    echo "<tr class=tbcat>
            <td colspan=\"4\" align=\"center\">
            <input type=\"hidden\"  name=\"action\" value=\"updatedisplayorder\">
            <input type=\"submit\" class=\"bginput\" value=\"更新所有分类的排序\">";
    echo "</td>\n</tr>\n</form>\n</table>\n";

}

cpfooter();
?>