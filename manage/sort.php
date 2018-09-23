<?php
error_reporting(7);
require "global.php";

cpheader();

$cachesorts = cachesorts();


function valid_sortid($sortid) {

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
                      //$stitle = htmlspecialchars($stitle);
                      $navbit = " \\ <a href=\"./sort.php?action=edit&sortid=$sortid\">$stitle</a>";
                      $navbit = buildsortnav($psid).$navbit;
             }
         }
         return $navbit;
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


if ($action=="add")  {

    if (empty($permission[canaddsort])) {
        show_nopermission();
    }

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
                                 'selected'=>$sortid,
                                 'filter'=>$filter_sort_array)); //                                  'extra'=>array('-1'=>'作为根分类'),



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

    if (empty($permission[canaddsort])) {
        show_nopermission();
    }

    $parentid = intval($_POST[parentid]);
    if (!isset($un_filter_sort_array[$parentid])) {
        show_nopermission();
    }

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
                       VALUES ('".addslashes($title)."','".addslashes($img)."','".addslashes($description)."','".intval($displayorder)."','".intval($parentid)."','".intval($showinrecent)."','".intval($showinhot)."','".intval($showinrate)."','".intval($showinlast)."','$division_sort','$division_article','$perpage','$_POST[showsortinfos]','$_POST[styleid]','".intval($_POST[ratearticlenum])."','".intval($_POST[hotarticlenum])."')");
    $sortid = $DB->insert_id();

    updateparentlist($sortid);
    resetcache();
    redirect("./sort.php?action=edit","该分类已添加");

}



if ($action=="mod")  {

    $sort = valid_sortid($sortid);

    if (empty($permission[caneditsort])) {
        show_nopermission();
    }


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

    foreach ($subsort_array AS $k=>$v) {
             if (!isset($filter_sort_array[$k])) {
                 $filter_sort_array[$k] = $v;
             }
    }
    unset($filter_sort_array[$sort[parentid]]);


    //print_rr($subsort_array);
    //print_rr($filter_sort_array);

    $cpforms->getsortlist(array('text'=>'根分类:',
                                 'name'=>'parentid',
                                 'extra'=>array('-1'=>'作为根分类'),
                                 'selected'=>$sort[parentid],
                                 'filter'=>$filter_sort_array));



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


    if (empty($permission[caneditsort])) {
        show_nopermission();
    }

    $title = htmlspecialchars(trim($title));
    $img = trim($img);
    if ($title==""){
        pa_exit("分类名不能为空");
    }

    $subsorts = getsubsorts($sortid);
    //$subsort_array = explode(",",$subsorts);
    $subsort_array = array_flip(explode(",",$subsorts));

    //print_rr($subsort_array);

    $parentid = intval($_POST[parentid]);
    if (isset($subsort_array[$parentid])) {
        pa_exit("父分类无效");
    }
    //exit;

    foreach ($subsort_array AS $k=>$v) {
             if (!isset($filter_sort_array[$k])) {
                 $filter_sort_array[$k] = $v;
             }
    }

    $sort = valid_sortid($sortid);
    $un_filter_sort_array["$sort[parentid]"] = 1;
    //print_rr($un_filter_sort_array);
    if (!isset($un_filter_sort_array["$parentid"])) {
        pa_exit("父分类无效");
    }

    if (!isset($un_filter_sort_array[$sortid])) {
        show_nopermission();
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
    redirect("./article.php?action=list","该分类已更新");

}



if ($action=="kill"){


    if (empty($permission[canremovesort])) {
        show_nopermission();
    }

    $sort = valid_sortid($sortid);
    $cpforms->formheader(array('title'=>'确实要删除该分类?该分类与该分类的子分类,及该分类与该分类的子分类中的所有文章将均会被删除.'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'remove'));
    $cpforms->makehidden(array('name'=>'sortid',
                                'value'=>$sort[sortid]));

    $cpforms->formfooter(array('confirm'=>1));

}


if ($action=="remove"){

    if (empty($permission[canremovesort])) {
        show_nopermission();
    }


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

    $articles = $DB->query("SELECT * FROM ".$db_prefix."article WHERE sortid IN (0$sortids)");
    while ($article = $DB->fetch_array($articles)) {
           $DB->query("DELETE FROM ".$db_prefix."articletext WHERE articleid='$article[articleid]'");
           $DB->query("DELETE FROM ".$db_prefix."articlerate WHERE articleid='$article[articleid]'");
           $DB->query("DELETE FROM ".$db_prefix."relatedlink WHERE articleid='$article[articleid]'");

           $DB->query("DELETE FROM ".$db_prefix."favorite WHERE articleid='$article[articleid]'");
           $comments = $DB->query("SELECT * FROM ".$db_prefix."comment WHERE article='$article[articleid]'");
           while ($comment = $DB->fetch_array($comments)) {
                  $DB->query("DELETE FROM ".$db_prefix."message WHERE commentid='$comment[commentid]'");
           }
           $DB->query("DELETE FROM ".$db_prefix."comment WHERE articleid='$article[articleid]'");
    }

    $DB->query("DELETE FROM ".$db_prefix."article WHERE sortid IN (0$sortids)");
    $DB->query("DELETE FROM ".$db_prefix."sort WHERE sortid IN (0$sortids)");

    resetcache();

    redirect("./article.php?action=list","所有分类已删除");

}



cpfooter();
?>