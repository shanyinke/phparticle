<?php
error_reporting(7);
require "global.php";

if($_GET[action]!=logout){
   cpheader();
}

function validate_userid($userid) {

         global $DB,$db_prefix;
         $user = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."user WHERE userid='$userid'");
         if (empty($user)) {
             pa_exit("该会员不存在");
         }

         return $user;

}

/* -=-=-=-=-=-=-=-=-=-=-=-=-
       start add user
-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]==add){

    if (empty($_GET[usergroupid])) {
        $_GET[usergroupid] = 3;
    }
    $cpforms->formheader(array('title'=>'添加新会员,必填选项'));

    $cpforms->makehidden(array('name'=>'action',
                          'value'=>'doinsert'));

    $cpforms->makeinput(array('text'=>'用户名:',
                               'name'=>'username',
                               ));

    $cpforms->makeinput(array('text'=>'密码:',
                               'type'=>'password',
                               'name'=>'password'));

    $cpforms->makeinput(array('text'=>'E-mail 地址:',
                               'name'=>'email'));
    $cpforms->getusergroups(array('text'=>'用户组:',
                             'name'=>'usergroupid',
                             'selected'=>$_GET[usergroupid],
                             ));
    $cpforms->makecategory(array('title'=>"选填选项",'separate'=>1));
    $cpforms->makesex(array('text'=>'性别:',
                             'name'=>'sex'));
    $cpforms->maketextarea(array('text'=>'个人简介:',
                                  'name'=>'intro'));

    $cpforms->makeinput(array('text'=>'个人主页:',
                               'name'=>'homepage'));
    $cpforms->makeinput(array('text'=>'Qq号码:',
                               'name'=>'qq'));

    $cpforms->makeinput(array('text'=>'Icq号码:',
                               'name'=>'icq'));
	 $cpforms->makeinput(array('text'=>'微信号:',
                               'name'=>'wechat'));						   

    $cpforms->makeinput(array('text'=>'Msn号码:',
                               'name'=>'nsm'));
    $cpforms->makeinput(array('text'=>'Msn号码:',
                               'name'=>'msn:'));
    $cpforms->makeinput(array('text'=>'地址:',
                               'name'=>'address'));
    $cpforms->makeinput(array('text'=>'联系电话:',
                               'name'=>'tel'));

    $cpforms->makeyesno(array('text'=>'是否记住密码?',
                               'name'=>'rememberpw',
                               'selected'=>1));
    $cpforms->formfooter();

}

/* -=-=-=-=-=-=-=-=-=-=-=-=-
       start insert user
-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_POST[action]=="doinsert"){

    $username = htmlspecialchars(trim($_POST[username]));
    $password = trim($_POST[password]);
    $email = htmlspecialchars(strtolower(trim($_POST[email])));
    $usergroupid = $_POST[usergroupid];

    if (trim($username)=="") {
        pa_exit("用户名不能为空");
    }

    if (trim($password)=="") {
        pa_exit("密码不能为空");
    }

    if ($usergroupid==-1) {
        pa_exit("请选择用户组.");
    }

    if (!validate_email($email)){
        pa_exit("E-mail 地址无效");
    }

    $checkuser = $DB->fetch_one_array("SELECT username,email
                                             FROM ".$db_prefix."user
                                             WHERE username='".addslashes($user_name)."' OR email='".addslashes($email)."'");

    if ($checkuser) {
       if ($username==$checkuser[username]) {
           pa_exit("该会员已存在,请使用其它用户名");
       }
       if ($email==trim($checkuser[email])){
           pa_exit("该 E-mail 已被注册,请使用其它 E-mail 地址");
       }
    }


    $DB->query("INSERT INTO ".$db_prefix."user (username,password,email,usergroupid,joindate,homepage,sex,address,qq,wechat,icq,msn,intro,tel,rememberpw)
                       VALUES ('".addslashes($username)."','".md5($password)."','".addslashes($email)."','$usergroupid','".time()."','".addslashes($homepage)."','".addslashes($_POST[sex])."','".addslashes(htmlspecialchars(trim($_POST[address])))."','".addslashes(htmlspecialchars(trim($_POST[qq])))."','".addslashes(htmlspecialchars(trim($_POST[wechat])))."','".addslashes(htmlspecialchars(trim($_POST[icq])))."','".addslashes(htmlspecialchars(trim($_POST[msn])))."','".addslashes(htmlspecialchars(trim($_POST[intro])))."','".addslashes(htmlspecialchars(trim($_POST[tel])))."','".addslashes($_POST[rememberpw])."')");

    redirect("./user.php?action=edit","该会员已添加");

}


if ($_GET[action]=="mod"){

    $user = validate_userid($_GET[userid]);
    $cpforms->formheader(array('title'=>'编辑会员资料,必填选项'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'update'));
    $cpforms->makehidden(array('name'=>'userid',
                                'value'=>$user[userid]));

    $cpforms->makeinput(array('text'=>'用户名:',
                               'name'=>'username',
                               'value'=>$user[username]));
    $cpforms->makeinput(array('text'=>'密码:<br>如果不修改本项,请留空',
                               'name'=>'password',
                               'type'=>'password'));
    $cpforms->makeinput(array('text'=>'E-mail 地址:',
                               'name'=>'email',
                               'value'=>$user[email]));

    $cpforms->getusergroups(array('text'=>'用户组',
                                    'name'=>'usergroupid',
                                    'selected'=>$user[usergroupid]));

    $cpforms->makecategory(array('title'=>'选填选项','separate'=>1));

    $cpforms->makesex(array('text'=>'性别:',
                             'name'=>'sex',
                             'selected'=>$user[sex]));


    $cpforms->makeinput(array('text'=>'注册日期:',
                               'name'=>'joindate',
                               'value'=>date("Y-m-d H:i:s",$user[joindate])
                               ));
    $cpforms->maketextarea(array('text'=>'个人简介:',
                               'name'=>'intro',
                               'value'=>$user[intro]
                               ));

    $cpforms->makeinput(array('text'=>'个人主页:',
                               'name'=>'homepage',
                               'value'=>$user[homepage]));
    $cpforms->makeinput(array('text'=>'Qq号码:',
                               'name'=>'qq',
                               'value'=>$user[qq]));
    $cpforms->makeinput(array('text'=>'微信号码:',
                               'name'=>'wechat',
                               'value'=>$user[wechat]));
    $cpforms->makeinput(array('text'=>'Icq号码:',
                               'name'=>'icq',
                               'value'=>$user[icq]));
    $cpforms->makeinput(array('text'=>'Msn号码:',
                               'name'=>'msn',
                               'value'=>$user[msn]));

    $cpforms->makeinput(array('text'=>'地址:',
                               'name'=>'address',
                               'value'=>$user[address]));
    $cpforms->makeinput(array('text'=>'联系电话:',
                               'name'=>'tel',
                               'value'=>$user[tel]));

    $cpforms->makeyesno(array('text'=>'是否记住密码?',
                               'name'=>'rememberpw',
                               'selected'=>$user[rememberpw]));
    $cpforms->formfooter();

}

/* -=-=-=-=-=-=-=-=-=-=-=-=-
       start update user
-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_POST[action]=="update"){

    $username = htmlspecialchars(trim($_POST[username]));
    $password = trim($_POST[password]);
    $email = htmlspecialchars(strtolower(trim($_POST[email])));
    $usergroupid = $_POST[usergroupid];

    if ($username=="") {
        pa_exit("用户名不能为空");
    }
    if ($usergroupid==-1) {
        pa_exit("请选择用户组.");
    }

    if (!validate_email($email)){
        pa_exit("E-mail 地址无效");
    }

    $userid = $_POST[userid];
    $user = validate_userid($userid);

    $checkuser = $DB->fetch_one_array("SELECT username,email FROM ".$db_prefix."user
                                        WHERE (username='".addslashes($username)."' OR email='".addslashes($email)."') AND userid!='$userid'");
    if (!empty($checkuser)) {
        if ($username==$checkuser[username]) {
            pa_exit("该会员已存在,请使用其它用户名");
       }
       if ($email==$checkuser[email]) {
           pa_exit("该 E-mail 已被他人使用,请使用其它 E-mail");
       }
    }


    $DB->query("UPDATE ".$db_prefix."user
                       SET username='".addslashes($username)."',
                       email='".addslashes($email)."',
                       usergroupid='$_POST[usergroupid]',
                       joindate=UNIX_TIMESTAMP('$_POST[joindate]'),
                       sex='$sex',
                       intro='".addslashes(htmlspecialchars(trim($_POST[intro])))."',
                       homepage='".addslashes(htmlspecialchars(trim($_POST[homepage])))."',
                       qq='".addslashes(htmlspecialchars(trim($_POST[qq])))."',
                       icq='".addslashes(htmlspecialchars(trim($_POST[icq])))."',
					   wechat='".addslashes(htmlspecialchars(trim($_POST[wechat])))."',
                       msn='".addslashes(htmlspecialchars(trim($_POST[msn])))."',
                       address='".addslashes(htmlspecialchars(trim($_POST[address])))."',
                       tel='".addslashes(htmlspecialchars(trim($_POST[tel])))."',
                       rememberpw='$_POST[rememberpw]'

                       WHERE userid='$userid'");

       if ($password!="") {
            $DB->query("UPDATE ".$db_prefix."user
                               SET password='".md5($password)."'
                               WHERE userid='$userid'");
       }
       redirect("./user.php?action=edit","该会员资料已更新");
}

/* -=-=-=-=-=-=-=-=-=-=-=-=-
       start kill user
-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="kill"){

    $user = validate_userid($_GET[userid]);

    $cpforms->formheader(array('title'=>"确定要删除该会员?"));
    $cpforms->makehidden(array('name'=>'action',
                               'value'=>'remove'));
    $cpforms->makehidden(array('name'=>'userid',
                               'value'=>$user[userid]));

    $cpforms->formfooter(array('confirm'=>1));

}

/* -=-=-=-=-=-=-=-=-=-=-=-=-
       start remove user
-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_POST[action]=="remove"){

    $user = validate_userid($_POST[userid]);

    $DB->query("DELETE FROM ".$db_prefix."user WHERE userid='$user[userid]'");
    $DB->query("DELETE FROM ".$db_prefix."favorite WHERE userid='$user[userid]'");

    redirect("./user.php?action=edit","该会员已删除");

}

/* -=-=-=-=-=-=-=-=-=-=-=-=-
       start search user
-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="search"){

    $cpforms->formheader(array('method'=>'get',
                               'title'=>'查找会员'));
    $cpforms->makehidden(array('name'=>'action','value'=>'dosearch'));

    $cpforms->makeinput(array('text'=>'会员名包涵:',
                              'name'=>'username'));
    $cpforms->makeinput(array('text'=>'Email 地址包涵:',
                              'name'=>'email'));

    $cpforms->getusergroups(array('text'=>'用户组:',
                                    'name'=>'usergroupid',
                                    'selected'=>$_GET[usergroupid],
                                    'extra'=>array('-1'=>'请选择用户组')));
    $cpforms->makesex(array('text'=>'性别:',
                             'name'=>'sex'));


    $cpforms->makecategory(array('title'=>"选填选项",'separate'=>1));

    $cpforms->makeinput(array('text'=>'注册日期:',
                                 'name'=>'joindate'));

    $cpforms->maketextarea(array('text'=>'个人简介:',
                                 'name'=>'intro'));

    $cpforms->makeinput(array('text'=>'主页:',
                              'name'=>'homepage'));
    $cpforms->makeinput(array('text'=>'Qq号码:',
                              'name'=>'email'));
    $cpforms->makeinput(array('text'=>'微信号:',
                              'name'=>'wechat'));
    $cpforms->makeinput(array('text'=>'Icq号码:',
                              'name'=>'icq'));
    $cpforms->makeinput(array('text'=>'Msn号码:',
                              'name'=>'msn'));
    $cpforms->makeinput(array('text'=>'地址:',
                              'name'=>'address'));
    $cpforms->makeinput(array('text'=>'联系电话:',
                              'name'=>'tel'));

    $cpforms->formfooter();

}

/* -=-=-=-=-=-=-=-=-=-=-=-=-
    start do search user
-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]==dosearch){


    $username = htmlspecialchars(trim($_GET[username]));
    $email = htmlspecialchars(trim($_GET[email]));
    $homepage = htmlspecialchars(trim($_GET[homepage]));
    $intro = htmlspecialchars(trim($_GET[intro]));
    $joindate = htmlspecialchars(trim($_GET[joindate]));


    if ($username!="") {
        $condition[] = " username LIKE BINARY '%".$username."%' ";
    }
    if (!empty($email)) {
        $condition[] = " email LIKE '%".$email."%' ";
    }
    if (!empty($usergroupid) AND $usergroupid!=-1) {
        $condition[] = " user.usergroupid='$usergroupid' ";
    }
    if ($sex!="unknow" AND !empty($sex)) {

        $condition[] = " sex='$sex' ";
    }
    //if (!empty($joindate)) {
    //    $condition[] = " joindate ";
    //}
    if (!empty($homepage)) {
        $condition[] = " homepage LIKE '%".$homepage."%' ";
    }

    if (!empty($qq)) {
        $condition[] = " qq LIKE '%".$_GET[qq]."%' ";
    }
	if (!empty($wechat)) {
        $condition[] = " wechat LIKE '%".$_GET[wechat]."%' ";
    }

    if (!empty($icq)) {
        $condition[] = " icq LIKE '%".$_GET[icq]."%' ";
    }
    if (!empty($msn)) {
        $condition[] = " msn LIKE '%".$_GET[msn]."%' ";
    }

    if (!empty($address)) {
        $condition[] = " address LIKE '%".$_GET[address]."%' ";
    }
    if (!empty($tel)) {
        $condition[] = " tel LIKE '%".$_GET[tel]."%' ";
    }

    if (is_array($condition))
    $conditions = implode(" AND ",$condition);


    if(empty($conditions)){
       pa_exit("还未输入何等要搜索的关键字,请返回");
    }

    //echo "<pre>$conditions</pre>";
    //exit;
    $nav = new buildNav;

    $total = $DB->fetch_one_array("SELECT count(*) AS count FROM ".$db_prefix."user as user
                                LEFT JOIN ".$db_prefix."usergroup as usergroup
                                ON (user.usergroupid=usergroup.usergroupid)
                                WHERE $conditions
                                ORDER BY user.userid DESC");
    $nav->total_result = $total[count];

    $nav->execute("SELECT user.*,usergroup.title as usergrouptitle FROM ".$db_prefix."user as user
                                LEFT JOIN ".$db_prefix."usergroup as usergroup
                                ON (user.usergroupid=usergroup.usergroupid)
                                WHERE $conditions
                                ORDER BY user.userid DESC");

    //$totalusers =  $DB->num_rows($nav->sql_result);

    if ($total[count]==0) {
        pa_exit("找不到何等任何匹配的会员");
    }

    echo "<table class=\"tableoutline\" boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\">
               <tr align=\"center\" class=\"tbhead\">
                <td align=center>id#</td>
                <td width=\"30%\"> 会员 </td>
                <td nowrap width=\"15%\"> 用户组 </td>
                <td nowrap width=\"40%\"> Email 地址 </td>
                <td nowrap> 注册日期 </td>
                <td nowrap> 编辑 </td>
               </tr>";

    while ($user = $DB->fetch_array($nav->sql_result)) {
           echo "<tr class=".getrowbg().">
                   <td>$user[userid]</td>
                   <td>$user[username]</td>
                   <td nowrap>".htmlspecialchars($user[usergrouptitle])."</td>
                   <td>$user[email]</td>
                   <td nowrap>".date("Y-m-d",$user[joindate])."</td>
                   <td nowrap>
                   [<a href=\"./user.php?action=mod&userid=$user[userid]\">编辑</a>]
                   [<a href=\"./user.php?action=kill&userid=$user[userid]\">删除</a>]
                   </td>
                 </tr>";
    }
    echo "</table>";

    echo $nav->pagenav();
}

/* -=-=-=-=-=-=-=-=-=-=-=-=-
    start edit(list) user
-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="edit"){

    unset($condition);
    $usergroupid = $_GET[usergroupid];
    if (empty($usergroupid) or $usergroupid==-1){
       $condition="";
    } else {
       $condition = " WHERE user.usergroupid='".$usergroupid."' ";
    }

    $nav = new buildNav;

    $total = $DB->fetch_one_array("SELECT count(*) AS count FROM ".$db_prefix."user as user
                                LEFT JOIN ".$db_prefix."usergroup as usergroup
                                ON (user.usergroupid=usergroup.usergroupid)
                                $condition
                                ORDER BY user.userid DESC");
    $nav->total_result = $total[count];


    if ($total[count]==0) {
        pa_exit("没有任何会员");
    }

    $nav->execute("SELECT user.*,usergroup.title as usergrouptitle FROM ".$db_prefix."user as user
                                LEFT JOIN ".$db_prefix."usergroup as usergroup
                                ON (user.usergroupid=usergroup.usergroupid)
                                $condition
                                ORDER BY user.userid DESC");


    echo $nav->pagenav();

    echo "<table class=\"tableoutline\" boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\">
               <tr align=\"center\" class=\"tbhead\">
                <td align=center>id#</td>
                <td width=\"30%\"> 会员 </td>
                <td nowrap width=\"15%\"> 用户组 </td>
                <td nowrap width=\"40%\"> Email 地址 </td>
                <td nowrap> 注册日期 </td>
                <td nowrap> 编辑 </td>
               </tr>";
    while ($user=$DB->fetch_array($nav->sql_result)){
           echo "<tr class=".getrowbg().">
                   <td>$user[userid]</td>
                   <td>$user[username]</td>
                   <td nowrap>$user[usergrouptitle]</td>
                   <td>$user[email]</td>
                   <td nowrap>".date("Y-m-d",$user[joindate])."</td>
                   <td nowrap>
                   [<a href=\"./user.php?action=mod&userid=$user[userid]\">编辑</a>]
                   [<a href=\"./user.php?action=kill&userid=$user[userid]\">删除</a>]
                   </td>
                 </tr>";
    }
    echo "</table>";

    echo $nav->pagenav();
}




if ($_GET[action]=="modpassword"){

    $cpforms->formheader(array('title'=>'修改密码:'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'updatepassword'));

    $cpforms->makeinput(array('text'=>'旧密码:',
                               'name'=>'oldpassword',
                               'type'=>'password'));
    $cpforms->makeinput(array('text'=>'新密码:',
                               'name'=>'newpassword',
                               'type'=>'password'));
    $cpforms->makeinput(array('text'=>'确认新密码:',
                               'name'=>'comfirpassword',
                               'type'=>'password'));
    $cpforms->formfooter();

}


if ($_POST[action]=="updatepassword"){

    if (trim($_POST[oldpassword])=="") {
        pa_exit("密码无效");
    }
    $user = $DB->fetch_one_array("SELECT userid,username,password
                                             FROM ".$db_prefix."user
                                             WHERE username='".addslashes($pauserinfo[username])."'");

    if (empty($user)) {
        pa_exit("该用户不存在");
    }


    if (md5($_POST[oldpassword])!=$user[password]) {
        pa_exit("密码无效");
    }

    $_POST[newpassword] = trim($_POST[newpassword]);
    $_POST[comfirpassword] = trim($_POST[comfirpassword]);

    if (trim($_POST[newpassword])=="") {
        pa_exit("新密码不能为空");
    }
    if ($_POST[newpassword]!=$_POST[comfirpassword]) {
        pa_exit("请确认输入的新密码一致");
    }


    $DB->query("UPDATE ".$db_prefix."user SET
                       password='".addslashes(md5($_POST[newpassword]))."'
                       WHERE userid='$user[userid]'");


    pa_exit("密码已更新,请刷新并重新登陆");

}


if ($_GET[action]==logout){

   session_unset();
   session_destroy();

   setcookie("username","",time()-3600);
   setcookie("password","",time()-3600);

   cpheader();
   redirect("./index.php","<b>你已退出登陆</b>");
   cpfooter();
   exit;

}

/* -=-=-=-=-=-=-=-=-=-=-=-=-
        start email
-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($action=="email") {

    $cpforms->formheader(array('title'=>'给会员发送Email'));
    $cpforms->makehidden(array('name'=>'action','value'=>'sendemail'));
    $cpforms->getusergroups(array('text'=>'选择会员:',
                             'name'=>'usergroupid',
                             'selected'=>"-1",
							'extra'=>array('-1'=>'所有会员')
                             ));
    $cpforms->makeinput(Array('text'=>'标题','name'=>'title'));
    $cpforms->maketextarea(array('text'=>'内容:',
                                  'name'=>'message'));
    $cpforms->formfooter();

}

/* -=-=-=-=-=-=-=-=-=-=-=-=-
     start send email
-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_POST[action]=="sendemail") {

    if (trim($title)=="" OR trim($message)=="") {
        echo "还未完整输入主题与内容,请返回输入完整.";
        exit;
    }
    if ($usergroupid!=-1){
        $condiction = "";
    } else {
        $condiction = " WHERE usergroupid='$usergroupid'";
    }
    $users=$DB->query("SELECT * FROM ".$db_prefix."user$condition");
    while($user=$DB->fetch_array($users)){
          mail($user[email],$title,$message,"From: phpArticle Mailer");
          echo "$user[email]<br>";
    }

}

cpfooter();
?>