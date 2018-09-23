<?php
error_reporting(7);
require "global.php";

cpheader();

function validate_usergroupid($usergroupid) {
         global $DB,$db_prefix;
         $usergroupid = intval($usergroupid);
         $usergroup = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."usergroup WHERE usergroupid='$usergroupid'");
         if (empty($usergroup)) {
             pa_exit("该用户组不存在");
         }
         return $usergroup;
}


if ($_GET[action]==add){

    $cpforms->formheader(array('title'=>'添加用户组'));
    $cpforms->makehidden(array('name'=>'action','value'=>'doinsert'));

    $cpforms->makeinput(array('text'=>'组名:',
                               'name'=>'title'));

    $cpforms->makecategory("用户身份");


    $cpforms->makeyesno(array('text'=>'是否作为超级管理员?<br>可以登陆超级管理面版,无权限限制',
                            'name'=>'isadmin'));
    $cpforms->makeyesno(array('text'=>'是否作为普通管理员?<br>可以登陆普通管理面版,受以下设置限制',
                            'name'=>'ismanager'));


    $cpforms->makecategory("新闻管理");
    $cpforms->makeyesno(array('text'=>'可以发布新闻?',
                               'name'=>'canaddnews'));
    $cpforms->makeyesno(array('text'=>'可以编辑新闻?',
                               'name'=>'caneditnews'));
    $cpforms->makeyesno(array('text'=>'可以删除新闻?',
                               'name'=>'canremovenews'));

    $cpforms->makecategory("分类管理");
    $cpforms->makeyesno(array('text'=>'可以添加分类?',
                               'name'=>'canaddsort'));
    $cpforms->makeyesno(array('text'=>'可以编辑分类?',
                               'name'=>'caneditsort'));
    $cpforms->makeyesno(array('text'=>'可以删除分类?',
                               'name'=>'canremovesort'));


    $cpforms->makecategory("文章管理");
    $cpforms->makeyesno(array('text'=>'可以添加文章?',
                               'name'=>'canaddarticle'));
    $cpforms->makeyesno(array('text'=>'可以编辑文章?',
                               'name'=>'caneditarticle'));
    $cpforms->makeyesno(array('text'=>'可以删除文章?',
                               'name'=>'canremovearticle'));

    $cpforms->makecategory("权限设置");
    $cpforms->makeyesno(array('text'=>'可以浏览文章?',
                               'name'=>'canviewarticle',
                               'selected'=>1));
    $cpforms->makeyesno(array('text'=>'可以对文章评分?',
                               'name'=>'canratearticle',
                               'selected'=>1));

    $cpforms->makeyesno(array('text'=>'可以浏览文章评论?',
                               'name'=>'canviewcomment',
                               'selected'=>'1'));
    $cpforms->makeyesno(array('text'=>'可以评论文章?',
                               'name'=>'cancomment',
                               'selected'=>'1'));

    $cpforms->makeyesno(array('text'=>'可以投稿?',
                               'name'=>'cancontribute',
                               'selected'=>'1'));
	$cpforms->makecategory("投稿权限设置");
    $cpforms->makeyesno(array('text'=>'投稿后是否不需要审核?',
                               'name'=>'noneedvalidate',
                               'selected'=>'0'));
    $cpforms->makeyesno(array('text'=>'可以上传图片?',
                               'name'=>'canupload',
                               'selected'=>'0'));

    $cpforms->makeyesno(array('text'=>'可以设置标题颜色?',
                               'name'=>'cansetcolor',
                               'selected'=>'0'));
    $cpforms->makeinput(array('text'=>'每日可发文章条数(0不限制)',
                               'name'=>'onedaypostmax',
                               'maxlength'=>'10',
                               'value'=>'0'));
    $cpforms->formfooter();

}


$postoptions=Array('noneedvalidate'=>1,'canupload'=>2,'cansetcolor'=>4);
if ($_POST[action]=="doinsert"){

    $_POST[title] = htmlspecialchars(trim($_POST[title]));
    if ($_POST[title]=="") {
        pa_exit("组名不能为空");
    }
    $_POST['postoptions']=0;
    if($_POST['noneedvalidate'])
	$_POST['postoptions']+=$postoptions['noneedvalidate'];
    if($_POST['canupload'])
	$_POST['postoptions']+=$postoptions['canupload'];
    if($_POST['cansetcolor'])
	$_POST['postoptions']+=$postoptions['cansetcolor'];
    $DB->query("INSERT INTO ".$db_prefix."usergroup (title,isadmin,ismanager,canaddnews,caneditnews,canremovenews,canaddsort,caneditsort,canremovesort,canaddarticle,caneditarticle,canremovearticle,canviewarticle,canratearticle,canviewcomment,cancomment,cancontribute,onedaypostmax,postoptions)
                          VALUES ('".addslashes($_POST[title])."','$_POST[isadmin]','$_POST[ismanager]','$_POST[canaddnews]','$_POST[caneditnews]','$_POST[canremovenews]','$_POST[canaddsort]','$_POST[caneditsort]','$_POST[canremovesort]','$_POST[canaddarticle]','$_POST[caneditarticle]','$_POST[canremovearticle]','$_POST[canviewarticle]','$_POST[canratearticle]','$_POST[canviewcomment]','$_POST[cancomment]','$_POST[cancontribute]','$_POST[onedaypostmax]','$_POST[postoptions]')");

    redirect("./usergroup.php?action=edit","该组已添加");

}


if ($_GET[action]=="mod"){

    $usergroup = validate_usergroupid($_GET[usergroupid]);

    $cpforms->formheader(array('title'=>'编辑用户组'));
    $cpforms->makehidden(array('name'=>'action','value'=>'update'));
    $cpforms->makehidden(array('name'=>'usergroupid','value'=>$usergroup[usergroupid]));

    $cpforms->makeinput(array('text'=>'组名:',
                               'name'=>'title',
                               'value'=>$usergroup[title]));

    $cpforms->makecategory("用户身份");
    $cpforms->makeyesno(array('text'=>'是否作为超级管理员?<br>可以登陆超级管理面版,无权限限制',
                            'name'=>'isadmin',
                            'selected'=>$usergroup[isadmin]));
    $cpforms->makeyesno(array('text'=>'是否作为普通管理员?<br>可以登陆普通管理面版,受以下设置限制',
                            'name'=>'ismanager',
                            'selected'=>$usergroup[ismanager]));


    $cpforms->makecategory("新闻管理");
    $cpforms->makeyesno(array('text'=>'可以发布新闻?',
                               'name'=>'canaddnews',
                               'selected'=>$usergroup[canaddnews]));
    $cpforms->makeyesno(array('text'=>'可以编辑新闻?',
                               'name'=>'caneditnews',
                               'selected'=>$usergroup[caneditnews]));
    $cpforms->makeyesno(array('text'=>'可以删除新闻?',
                               'name'=>'canremovenews',
                               'selected'=>$usergroup[canremovenews]));

    $cpforms->makecategory("分类管理");
    $cpforms->makeyesno(array('text'=>'可以添加分类?',
                               'name'=>'canaddsort',
                               'selected'=>$usergroup[canaddsort]));
    $cpforms->makeyesno(array('text'=>'可以编辑分类?',
                               'name'=>'caneditsort',
                               'selected'=>$usergroup[caneditsort]));
    $cpforms->makeyesno(array('text'=>'可以删除分类?',
                               'name'=>'canremovesort',
                               'selected'=>$usergroup[canremovesort]));


    $cpforms->makecategory("文章管理");
    $cpforms->makeyesno(array('text'=>'可以添加文章?',
                               'name'=>'canaddarticle',
                               'selected'=>$usergroup[canaddarticle]));
    $cpforms->makeyesno(array('text'=>'可以编辑文章?',
                               'name'=>'caneditarticle',
                               'selected'=>$usergroup[caneditarticle]));
    $cpforms->makeyesno(array('text'=>'可以删除文章?',
                               'name'=>'canremovearticle',
                               'selected'=>$usergroup[canremovearticle]));

    $cpforms->makecategory("权限设置");
    $cpforms->makeyesno(array('text'=>'可以浏览文章?',
                               'name'=>'canviewarticle',
                               'selected'=>$usergroup[canviewarticle]));
    $cpforms->makeyesno(array('text'=>'可以对文章评分?',
                               'name'=>'canratearticle',
                               'selected'=>$usergroup[canratearticle]));

    $cpforms->makeyesno(array('text'=>'可以浏览文章评论?',
                               'name'=>'canviewcomment',
                               'selected'=>$usergroup[canviewcomment]));
    $cpforms->makeyesno(array('text'=>'可以评论文章?',
                               'name'=>'cancomment',
                               'selected'=>$usergroup[cancomment]));

    $cpforms->makeyesno(array('text'=>'可以投稿?',
                               'name'=>'cancontribute',
                               'selected'=>$usergroup[cancontribute]));
	$cpforms->makecategory("投稿权限设置");
    $cpforms->makeyesno(array('text'=>'投稿后是否不需要审核?',
                               'name'=>'noneedvalidate',
                               'selected'=>($postoptions['noneedvalidate']&$usergroup['postoptions'])));
    $cpforms->makeyesno(array('text'=>'可以上传图片?',
                               'name'=>'canupload',
                               'selected'=>($postoptions['canupload']&$usergroup['postoptions'])));

    $cpforms->makeyesno(array('text'=>'可以设置标题颜色?',
                               'name'=>'cansetcolor',
                               'selected'=>($postoptions['cansetcolor']&$usergroup['postoptions'])));
    $cpforms->makeinput(array('text'=>'每日可发文章条数(0不限制)',
                               'name'=>'onedaypostmax',
                               'maxlength'=>'10',
                               'value'=>$usergroup['onedaypostmax']));
    $cpforms->formfooter();

}


if ($_POST[action]=="update"){

    $usergroup = validate_usergroupid($_POST[usergroupid]);
    $_POST[title] = htmlspecialchars(trim($_POST[title]));
    if ($_POST[title]=="") {
        pa_exit("用户组不能为空");
    }
    $_POST['postoptions']=0;
    if($_POST['noneedvalidate'])
	$_POST['postoptions']+=$postoptions['noneedvalidate'];
    if($_POST['canupload'])
	$_POST['postoptions']+=$postoptions['canupload'];
    if($_POST['cansetcolor'])
	$_POST['postoptions']+=$postoptions['cansetcolor'];

    $DB->query("UPDATE ".$db_prefix."usergroup
                             SET title='".addslashes($_POST[title])."',
                             isadmin='$_POST[isadmin]',
                             ismanager='$_POST[ismanager]',


                             canaddnews='$_POST[canaddnews]',
                             caneditnews='$_POST[caneditnews]',
                             canremovenews='$_POST[canremovenews]',

                             canaddsort='$_POST[canaddsort]',
                             caneditsort='$_POST[caneditsort]',
                             canremovesort='$_POST[canremovesort]',

                             canaddarticle='$_POST[canaddarticle]',
                             caneditarticle='$_POST[caneditarticle]',
                             canremovearticle='$_POST[canremovearticle]',

                             canviewarticle='$_POST[canviewarticle]',
                             canratearticle='$_POST[canratearticle]',

                             canviewcomment='$_POST[canviewcomment]',
                             cancomment='$_POST[cancomment]',

                             cancontribute='$_POST[cancontribute]',
                             onedaypostmax='$_POST[onedaypostmax]',
                             postoptions='$_POST[postoptions]'

                             WHERE usergroupid='$usergroup[usergroupid]'");

    redirect("./usergroup.php?action=edit","该用户组已更新");

}


if ($_GET[action]=="kill"){

    $usergroup = validate_usergroupid($_GET[usergroupid]);
    $cpforms->formheader(array('title'=>'确实要删除该用户组?'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'remove'));
    $cpforms->makehidden(array('name'=>'usergroupid',
                                'value'=>$usergroup[usergroupid]));
    $cpforms->formfooter(array('confirm'=>1));
}


if ($_POST[action]=="remove"){

    $usergroup = validate_usergroupid($_POST[usergroupid]);
    $DB->query("DELETE FROM ".$db_prefix."usergroup
                           WHERE usergroupid='$usergroup[usergroupid]'");

    redirect("./usergroup.php?action=edit","该用户组已删除");

}


if ($_GET[action]==edit){

    echo "<table  class=\"tableoutline\" boder=\"0\" width=\"100%\" cellpadding=\"5\" cellspacing=\"1\">
               <tr align=\"center\" class=\"tbhead\">
                <td>id#</td>
                <td nowrap width=\"100%\">用户组</td>
                <td nowrap>会员数</td>
                <td nowrap>编辑</td>
               </tr>";
    $usergroups = $DB->query("SELECT * FROM ".$db_prefix."usergroup");
    while($usergroup=$DB->fetch_array($usergroups)){
          $usersnum=$DB->fetch_one_array("SELECT COUNT(*) as count FROM ".$db_prefix."user WHERE usergroupid=$usergroup[usergroupid]");
          echo "<tr class=".getrowbg().">
                  <td align=\"center\">$usergroup[usergroupid]</td>
                  <td> <a href=\"./user.php?action=edit&usergroupid=$usergroup[usergroupid]\">$usergroup[title]</a></td>
                  <td nowrap align=\"center\">$usersnum[count]</td>
                  <td nowrap>";
          echo "[<a href=\"./usergroup.php?action=mod&usergroupid=$usergroup[usergroupid]\">编辑</a>]";
          if ($usergroup[usergroupid]>5) {
              echo "[<a href=\"./usergroup.php?action=kill&usergroupid=$usergroup[usergroupid]\">删除</a>]";
          }
          echo "[<a href=\"./user.php?action=add&usergroupid=$usergroup[usergroupid]\">添加会员</a>]";
          echo "</td></tr>";
    }
    echo "</table>";


}

cpfooter();
?>