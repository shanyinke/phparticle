<?php
if ($_GET[action]=="view") {

    if (empty($_GET[mode])) {
        $mode = $_COOKIE[mode];
    } else {
        $mode = $_GET[mode];
    }

    $commentid = intval($_GET[commentid]);
    $comment = validate_commentid($commentid);
    $article = validate_articleid($comment[articleid]);

    cachesorts();
    $sortlist = makesortlist();
    $article[tmp_title] = $article[title];

	$articlehtmllink = HTMLDIR."/".mkdirname($article['sortid'],"",$article['date'],0,0).rawurlencode(mkfilename($filenamemethod,$article['title'],1)).$article['articleid']."_1.".HTMLEXT;//get_sortdirs($article['sortid'])."/".date("Y_m",$article['date'])."/"
//	$article[title] = "<a href=\"$articlehtmllink\">$article[title]</a>";
    $navbar = makearticlenavbar2($article);
    $article[title] = $article[tmp_title];
    $article[date] = padate($dateformat_article,$article[date]);
    //$articleid = $article[articleid];
    if (empty($_GET[mode])) {
        $mode = $_COOKIE[mode];
    } else {
        $mode = $_GET[mode];
    }

    if ($mode=="tree") {
        setcookie("mode","tree",time()+3600*24*365);
        $messagemode = "
<a href='index.php?mod=message&action=view&commentid=$commentid&messageid=$messageid&mode=flat'>平板模式</a>　树型模式
";

        $messages = $DB->query("SELECT message.commentid,message.title,message.messageid,message.parentid,message.date,message.removed,user.username FROM ".$db_prefix."message AS message
                                         LEFT JOIN ".$db_prefix."user AS user
                                              ON message.userid=user.userid
                                         WHERE commentid='$commentid'
                                         ORDER BY parentid,messageid");
        unset($cachemessages);
        unset($cachemessageids);
        while ($message = $DB->fetch_array($messages)) {
               $cachemessages[$message[parentid]][] = $message;
               $cachemessageids[] = $message[messageid];
        }
        $DB->free_result($messages);
        unset($message);
        $messageid = $_GET[messageid];
        $key = array_search($messageid,$cachemessageids);
        if (empty($key)) {
            list (,$messageid) = each($cachemessageids);
        }

        $counter = 0;
        function buildthread($parentid=0,$level=1) {

                 global $cachemessages,$counter,$messageid,$style,$phparticleurl,$pauserinfo;
                 if (!isset($cachemessages[$parentid])) {
                     return;
                 }

                 foreach ($cachemessages[$parentid] AS $message) {

                          $message[branch] = str_repeat("<img src=\"$phparticleurl/$style[imagesfolder]/blank.gif\" border=\"0\" align=\"absmiddle\">",$level-1); //<img src=\"{imagesfolder}/branch.gif\" border=\"0\" align=\"absmiddle\">
                          if ($pauserinfo[lastvisit]>$message[date]) {
                              $message[posticon] = "post.gif";
                              //echo $message[posticon];
                          } else {
                              $message[posticon] = "post_n.gif";
                          }

                          $message[date] = padate("Y-m-d H:i:s a",$message[date]);

                          if (++$counter%2==0) {
                              $message[bgcolor] = $style[firstalt];
                          } else {
                              $message[bgcolor] = $style[secondalt];
                          }

                          //echo $branch;
                          if ($message[removed]) {
                              $threadlistbit .= "
        <tr>
          <td >$message[branch]<img src='$phparticleurl/images/post_d.gif' border='0' alt='该评论已被删除' valign='absmiddle'><s>$message[title]</s> - $message[username] - $message[date]</td>
        </tr>
";
                          } else {
                              if ($message[messageid]==$messageid) {
                                  $threadlistbit .= "
        <tr>
          <td>$message[branch]<img src='$g_o_back2root/$style[imagesfolder]/$message[posticon]' border='0' valign='absmiddle'><b>$message[title]</b> - $message[username] - $message[date]</td>
        </tr>
";
                              } else {
                                  $threadlistbit .= "
        <tr>
          <td>$message[branch]<img src='$g_o_back2root/$style[imagesfolder]/$message[posticon]' border='0' valign='absmiddle'><a href='index.php?mod=message&action=view&commentid=$message[commentid]&messageid=$message[messageid]'>$message[title]</a> - $message[username] - $message[date]</td>
        </tr>
";
                              }
                          }

                          $threadlistbit .= buildthread($message[messageid],$level+1);
                 }

                 return $threadlistbit;
        }

        $threadlist = buildthread();
//        echo $threadlist;
        $showmessage_thread = "
      <br>
      <table width='100%' border='0' cellspacing='0' cellpadding='0'>
        <tr>
          <td>
      <table width='100%' border='0' cellpadding='4'>
        <tr>
          <td><b>树型目录</b></td>
        </tr>
        $threadlist
      </table>
          </td>
        </tr>
      </table>
";

        $message = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."message WHERE messageid='$messageid'");
        $message[date] = padate("Y-m-d H:i:s a",$message[date]);

        if ($message[removed]) {
            $message[title] = "<s>$message[title]</s>";
            $message[message] = "该评论已经被删除";
        } else {
            $message[message] = htmlspecialchars($message[message]);
            $message[message] = str_replace("  ","&nbsp;&nbsp;",nl2br($message[message]));
        }


        unset($showmessage_messagelistbit_note);
        if (pa_isset($message[lastupdate])) {
            $message[lastupdate] = padate("Y-m-d H:i:s a",$message[lastupdate]);
            $showmessage_messagelistbit_note = "";
        }

        $showmessage_messagelistbit .= "
      <table width='100%' border='0' cellpadding='4'>
        <tr>
          <td><b>$message[title]</b> - $message[date] - $message[author]</td>
        </tr>
        <tr>
          <td>$message[message]
<!-- BEGIN showmessage_messagelistbit_note -->
<p align='right'><i>最后由 $message[lastupdater] 更新于 $message[lastupdate]<i></p>
<!-- END showmessage_messagelistbit_note -->
          </td>
        </tr>
        <tr>
          <td align='right'><a href='index.php?mod=message&action=reply&messageid=$message[messageid]'>回复</a> <a href='index.php?mod=message&action=edit&messageid=$message[messageid]'>编辑</a>
          </td>
        </tr>
      </table>
";

    } else {

        setcookie("mode","flat",time()+3600*24*365);
        $messagemode = "
平板模式　<a href='index.php?mod=message&action=view&commentid=$commentid&messageid=$messageid&mode=tree'>树型模式</a>
";

        $total = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."message
                                              WHERE commentid='$commentid'");

        $totalresults = $total[count];

        $perpage = 10;
        $totalpages = ceil($total[count]/$perpage);

        if ($pagenum<1 OR empty($pagenum)) {
            $pagenum = 1;
        } elseif ($pagenum>$totalpages) {
            $pagenum = $totalpages;
        }

        $offset = ($pagenum-1)*$perpage;

        if ($totalresults>0) {
            $from = $offset+1;
            if ($pagenum==$totalpages) {
                $to = $totalresults;
            } else {
                $to = $offset+$perpage;
            }
        } else {
            $from = 0;
            $to = 0;
        }

        $messages = $DB->query("SELECT * FROM ".$db_prefix."message
                                         WHERE commentid='$commentid'
                                         ORDER BY date
                                         LIMIT $offset,$perpage");

        while ($message = $DB->fetch_array($messages)) {
               $message[date] = padate("Y-m-d H:i:s a",$message[date]);
               if ($message[removed]) {
                   $message[title] = "<s>$message[title]</s>";
                   $message[message] = "该评论已经被删除";
               } else {
                   $message[message] = htmlspecialchars($message[message]);
                   $message[message] = str_replace("  ","&nbsp;&nbsp;",nl2br($message[message]));
               }
               $showmessage_messagelistbit .= "
      <table width='100%' border='0' cellpadding='4'>
        <tr>
          <td><b>$message[title]</b> - $message[date] - $message[author]</td>
        </tr>
        <tr>
          <td>$message[message]
<!-- BEGIN showmessage_messagelistbit_note -->
<p align='right'><i>最后由 $message[lastupdater] 更新于 $message[lastupdate]<i></p>
<!-- END showmessage_messagelistbit_note -->
          </td>
        </tr>
        <tr>
          <td align='right'><a href='index.php?mod=message&action=reply&messageid=$message[messageid]'>回复</a> <a href='index.php?mod=message&action=edit&messageid=$message[messageid]'>编辑</a>
          </td>
        </tr>
      </table>
";
        }

        $pagelinks = makepagelink2("$phparticleurl/index.php?mod=message&action=view&commentid=$commentid",$pagenum,$totalpages);
        $pagenav = "
<div id='sublistfooter'>
     <div class='left'>共 <b>$totalresults</b> ,显示 <b>$from -
      $to</b></div>
     <div class='right'>$pagelinks</div>
</div>
";
        //eval("\$messagelist = \"".gettemplate('showmessage_messagelist')."\";");
    }

    unset($quickreplybox);
    if ($pauserinfo[userid]!=0) {
        $quickreplybox = "<fieldset><legend>快速回复</legend>
                        <form method='post' action='$g_o_back2root/index.php?mod=message' onSubmit='return process_data(this)'>

                            <div>
                              <label>标题:</label><input type='text' name='title' maxlength='50' size='50' value='回复: $comment[title]'>
                            </div>
                            <div>
                              <label>内容:</label><textarea name='message' cols='70' rows='6'></textarea>
                            </div>
                            <div class=enter>
                              <td colspan='2'>
                                <input type='hidden' name='commentid' value='$commentid'>
                                <input type='hidden' name='messageid' value='$messageid'>
                                <input type='hidden' name='action' value='doinsert'>
                                <input type='submit' value=' 发  表 ' class='buttot'>
                                <input type='submit' name='preview' value=' 预  览 ' class='buttot'>
                                <input type='reset' value=' 重  置 ' class='buttot'>

                            </div>
                        </form>

</fieldset>


<script language='javascript'>
<!--
function process_data(theform) {

                if (theform.title.value=='') {
                        alert('请输入标题!');
                        theform.title.focus();
                        return false;
                }

                if (theform.message.value=='') {
                        alert('请填写内容!');
                        theform.message.focus();
                        return false;
                }

}

//-->
</script>";
    }
    $DB->query("UPDATE ".$db_prefix."comment SET views=views+1 WHERE commentid='$commentid'");

}

if ($_POST[action]=="doinsert") {

    if ($_POST[preview]) {
        

        //print_rr($_POST);

        if ($pauserinfo[userid]==0) {
            include("modules/default/nopermission.php");
        }

        $commentid = intval($_POST[commentid]);
        $comment = validate_commentid($commentid);

        $title = htmlspecialchars(trim($_POST[title]));
        //$message = htmlspecialchars(trim($_POST[message]));
        //$message = str_replace("  ","&nbsp;&nbsp;",nl2br($message));
        $message = htmlspecialchars(trim($_POST[message]));
        $message1 = str_replace("  ","&nbsp;&nbsp;",nl2br($message));


        if (!pa_isset($title)) {
		$errormessage="error_comment_title_blank";
		include("modules/default/error.php");
        }
        if (!pa_isset($message)) {
		$errormessage="error_comment_message_blank";
		include("modules/default/error.php");
        }


        if (strlen($title)>100) {
		$errormessage="error_comment_title_toolong";
		include("modules/default/error.php");
        }
        if (strlen($message)>400) {
		$errormessage="error_comment_message_toolong";
		include("modules/default/error.php");
        }

        //cachesorts();
        //$articleid = intval($_POST[articleid]);
        //$article = validate_articleid($articleid);

        //$article[title] = "<a href=\"index.php?mod=article&articleid=$articleid\">$article[title]</a>";
        //$navbar = makearticlenavbar($article);
        ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
<title><?=$phparticletitle?> - 预览评论</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-CN" />
<link rel="icon" href="<?=$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<link rel="shortcut icon" href="<?$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<meta name="description" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<meta name="keywords" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<script src="<?=$g_o_back2root?>/images/js/chinahtml.js" type="text/javascript"></script>
<link href="<?=$g_o_back2root?>/images/css/css.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
<!--
function process_data(theform) {

                if (theform.title.value=="") {
                        alert("请输入标题!");
                        theform.title.focus();
                        return false;
                }

                if (theform.message.value=="") {
                        alert("请填写内容!");
                        theform.message.focus();
                        return false;
                }

}
//-->
</script>
</head>
<body>
<a name="top"></a>
<div id="header">
    <div id="topmenu">
		<div class="left">
			<div id="topdate"><?=$phparticleurl?></div>
		</div>
		<div class="right">
			<div id="topnavlist">
			<ul>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=usercp">会员面板</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=member&action=login">会员登陆</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=register">免费注册</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=search">高级搜索</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=search&action=lastupdate">最后更新</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=contribute">我要投稿</a></li>
				<li> <a href="<?=$g_o_back2root?>/index.php?mod=member&action=logout">退出登陆</a></li>
			</ul>
			</div>
		</div>
	</div>
	<div id="maintop">
		<div id="Logo"><a href="/"><img src="<?=$g_o_back2root?>/images/palogo.gif" alt="首页" /></a></div>
		<div id="TopAds"><a href="http://www.phparticle.net/" target="_blank"><img src="<?=$g_o_back2root?>/images/topbanner.jpg" width="468" height="60" alt="" /></a></div>
		<div id="toprightmenu">
			<ul>
				<li><a href="<?=$phparticleurl?>" onClick="this.style.behavior='url(#default#homepage)';this.setHomePage('<?=$phparticleurl?>');return(false);" style="behavior: url(#default#homepage)">设为首页</a></li>
				<li><a href=javascript:window.external.AddFavorite('<?=$phparticleurl?>','<?=$phparticletitle?>') title="希望您喜欢本站">加入收藏</a></li>
				<li><a href="mailto:semi.rock@gmail.com">联系我们</a></li>
			</ul>
		</div>
	</div>

	<div class="nav">

		<div class="nav-up-left"><a href="/" class="white">首页</a>　<a href="<?=$g_o_back2root?>/html/2/" class="white">精品文章</a>　<a href="<?=$g_o_back2root?>/cet/2/" class="white">使用帮助</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/1/13/" class="white">网站建设</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/4/6/" class="white">软件共享</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/1/12/" class="white">VC/C#/编成</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/1/9" class="white">UNIX/LINUX资料</a>　<a href="<?=$g_o_back2root?>/bbs" class="white">支持论坛</a>　<a href="http://www.utspeed.com" class="white">极速科技</a>　<a href="http://proxygo.com.ru" class="white">代理猎狗</a>  <a href="http://mp3.utspeed.com" class="white">音乐搜索</a></div>

	</div>

	<div class="navline">&nbsp;</div>

	<div class="nav">
		<div class="nav-down-left"><a href="<?=$g_o_back2root?>/bbshtmldata/4/6/" class="white">休闲娱乐</a>　<a href="http://music.utspeed.com" class="white">极速音乐</a>　<a href="http://4tc.com.ru" class="white">极速网址</a>　<a href="http://article.utspeed.com" class="white">幽默笑话</a>　<a href="http://woman.utspeed.com" class="white">女性美容</a>　<a href="http://nuskin.net.ru" class="white">如新网上购物商城</a>  <a href="http://bt.utspeed.com" class="white">电驴/电骡/emule</a></div>
		<div class="nav-down-right">
		<span>当前在线: <b><?=$onlineuser?></b></span></div>
	</div>







</div>

<div class="mainline">&nbsp;</div>
<div class="maincolumn">
		<div class="pagelist">
		<div class="pagelisttitleico">&nbsp;</div>
		<div class="pagelisttitlebg">
		<div class="pagelisttitlename">
你的位置：<a href="<?=$homepage?>/" class="classlinkclass">首页</a>&nbsp;>&nbsp;<a href="<?=$phparticleurl?>" class="classlinkclass"><?=$phparticletitle?></a>&nbsp;>&nbsp;预览评论
		</div>
		</div>
		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
<fieldset>
              <table width="100%" border="0" cellpadding="4">
                <tr>
                  <td colspan="2"><b>评论预览</b></td>
                </tr>
                <tr>
                  <td nowrap width="10%">标题:</td>
                  <td><b><?=$title?></b></td>
                </tr>
                <tr>
                  <td valign="top">内容:</td>
                  <td><?=$message1?></td>
                </tr>
              </table>
</fieldset>

<fieldset><legend>发表评论</legend>
  <form method="post" action="<?=$g_o_back2root?>/index.php?mod=message" onSubmit="return process_data(this)">

                <div>
                  <label>标题:</label>
                    <input type="text" name="title" size="50" maxlength="50" value="<?=$title?>">
                </div>
                <div>
                  <label>内容:</label>
                    <textarea name="message" cols="70" rows="10"><?=$message?></textarea>
                </div>
                <div class=ad>
                    <input type="hidden" name="action" value="doinsert">
                    <input type="hidden" name="commentid" value="<?=$commentid?>">
                    <input type="hidden" name="messageid" value="<?=$messageid?>">
                    <input type="submit" value=" 回  复 " class="buttot">
                    <input type="submit" name="preview" value=" 预  览 " class="buttot">
                    <input type="reset" value=" 重  置 "  class="buttot">
                </div>

  </form>
</fieldset>

		</div>
		</div>
		</div>


<div class="mainline">&nbsp;</div>
<div id="footer">
	<div id="bottommenu">
	  <a href="#">关于站点</a> - <a href="#">广告服务</a> - <a href="#">联系我们</a> - <a href="#">版权隐私</a> - <a href="#">免责声明</a> - <a href="http://utspeed.com">合作伙伴</a> - <a href="http://phparticle.net" target="_blank">程序支持</a> - <a  href="#">网站地图</a> - <a href="#top">返回顶部</a>
	</div>
	<div class="topline">&nbsp;</div>
	<div id="bottom">版权所有：<a href="<?=$phparticleurl?>"><?=$phparticletitle?></a> 2006 未经授权禁止复制或建立镜像<br />

	  <span>Powered by: <a href="http://www.phparticle.net">phpArticle html</a> Version <?=$version?>.</span><br />
	</div>
	</div>
</div>
</body>
</html><?

    } else {

        

        //print_rr($_POST);

        if ($pauserinfo[userid]==0) {
            include("modules/default/nopermission.php");
        }

        $commentid = intval($_POST[commentid]);
        $comment = validate_commentid($commentid);

        $title = htmlspecialchars(trim($_POST[title]));
        //$message = htmlspecialchars(trim($_POST[message]));
        //$message = str_replace("  ","&nbsp;&nbsp;",nl2br($message));
        $message = trim($_POST[message]);

        if (!pa_isset($title)) {
		$errormessage="error_message_title_blank";
		include("modules/default/error.php");
        }
        if (!pa_isset($message)) {
		$errormessage="error_message_message_blank";
		include("modules/default/error.php");
        }

        if (strlen($title)>$comment_title_limit) {
            //show_errormessage("error_comment_title_toolong");
            $title = substr($title,0,$comment_title_limit);
        }
        if (strlen($message)>$comment_message_limit) {
		$errormessage="error_comment_message_toolong";
		include("modules/default/error.php");
        }

        $messageinfos = $DB->query("SELECT messageid FROM ".$db_prefix."message
                                                     WHERE commentid='$commentid'
                                                     ORDER BY date");
        while ($messageinfo = $DB->fetch_array($messageinfos)) {
               $cachemessage[$messageinfo[messageid]] = $messageinfo[messageid];
        }

        $messageid = $_POST[messageid];
        if (!isset($cachemessage[$messageid])) {
            list($parentid,) = each($cachemessage);
        } else {
            $parentid = $messageid;
        }

        $date = time();
        $DB->query("INSERT INTO ".$db_prefix."message (commentid,userid,author,parentid,title,message,date) VALUES
                           ('$commentid','$pauserinfo[userid]','".addslashes($pauserinfo[username])."','$parentid','".addslashes($title)."','".addslashes($message)."','$date')");
        $messageid = $DB->insert_id();
        $DB->query("UPDATE ".$db_prefix."comment SET
                             replies=replies+1,
                             lastupdate='$date',
                             lastreplier='".addslashes($pauserinfo[username])."'
                             WHERE commentid='$commentid'");
	$url = "$phparticleurl/index.php?mod=message&action=view&commentid=$commentid&messageid=$messageid";
	$redirectmsg="redirect_message_added";
	include("modules/default/redirect.php");
    }

}


if ($_GET[action]=="edit") {

    if ($pauserinfo[userid]==0) {
        include("modules/default/nopermission.php");
    }

    $messageid = $_GET[messageid];
    $message = validate_messageid($messageid);

    $commentid = $message[commentid];
    $comment = validate_commentid($commentid);
    $article = validate_articleid($comment[articleid]);
    //print_rr($message);
    if ($pauserinfo[userid]!=$message[userid]) {
        include("modules/default/nopermission.php");
    }

cachesorts();
    $sortlist = makesortlist();
    $article[tmp_title] = $article[title];

    $navbar = makearticlenavbar2($article);
    $article[title] = $article[tmp_title];

	$g_o_back2root=".";
    ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
<title><?=$phparticletitle?> - 修改评论</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-CN" />
<link rel="icon" href="<?=$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<link rel="shortcut icon" href="<?$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<meta name="description" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<meta name="keywords" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<script src="<?=$g_o_back2root?>/images/js/chinahtml.js" type="text/javascript"></script>
<link href="<?=$g_o_back2root?>/images/css/css.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
<!--
function process_data(theform) {

                if (theform.title.value=="") {
                        alert("请输入标题!");
                        theform.title.focus();
                        return false;
                }

                if (theform.message.value=="") {
                        alert("请填写内容!");
                        theform.message.focus();
                        return false;
                }

}
//-->
</script>
</head>
<?=$style[body]?>
<body>
<a name="top"></a>
<div id="header">
    <div id="topmenu">
		<div class="left">
			<div id="topdate"><?=$phparticleurl?></div>
		</div>
		<div class="right">
			<div id="topnavlist">
			<ul>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=usercp">会员面板</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=member&action=login">会员登陆</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=register">免费注册</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=search">高级搜索</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=search&action=lastupdate">最后更新</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=contribute">我要投稿</a></li>
				<li> <a href="<?=$g_o_back2root?>/index.php?mod=member&action=logout">退出登陆</a></li>
			</ul>
			</div>
		</div>
	</div>
	<div id="maintop">
		<div id="Logo"><a href="/"><img src="<?=$g_o_back2root?>/images/palogo.gif" alt="首页" /></a></div>
		<div id="TopAds"><a href="http://www.phparticle.net/" target="_blank"><img src="<?=$g_o_back2root?>/images/topbanner.jpg" width="468" height="60" alt="" /></a></div>
		<div id="toprightmenu">
			<ul>
				<li><a href="<?=$phparticleurl?>" onClick="this.style.behavior='url(#default#homepage)';this.setHomePage('<?=$phparticleurl?>');return(false);" style="behavior: url(#default#homepage)">设为首页</a></li>
				<li><a href=javascript:window.external.AddFavorite('<?=$phparticleurl?>','<?=$phparticletitle?>') title="希望您喜欢本站">加入收藏</a></li>
				<li><a href="mailto:semi.rock@gmail.com">联系我们</a></li>
			</ul>
		</div>
	</div>

	<div class="nav">

		<div class="nav-up-left"><a href="/" class="white">首页</a>　<a href="<?=$g_o_back2root?>/html/2/" class="white">精品文章</a>　<a href="<?=$g_o_back2root?>/cet/2/" class="white">使用帮助</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/1/13/" class="white">网站建设</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/4/6/" class="white">软件共享</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/1/12/" class="white">VC/C#/编成</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/1/9" class="white">UNIX/LINUX资料</a>　<a href="<?=$g_o_back2root?>/bbs" class="white">支持论坛</a>　<a href="http://www.utspeed.com" class="white">极速科技</a>　<a href="http://proxygo.com.ru" class="white">代理猎狗</a>  <a href="http://mp3.utspeed.com" class="white">音乐搜索</a></div>

	</div>

	<div class="navline">&nbsp;</div>

	<div class="nav">
		<div class="nav-down-left"><a href="<?=$g_o_back2root?>/bbshtmldata/4/6/" class="white">休闲娱乐</a>　<a href="http://music.utspeed.com" class="white">极速音乐</a>　<a href="http://4tc.com.ru" class="white">极速网址</a>　<a href="http://article.utspeed.com" class="white">幽默笑话</a>　<a href="http://woman.utspeed.com" class="white">女性美容</a>　<a href="http://nuskin.net.ru" class="white">如新网上购物商城</a>  <a href="http://bt.utspeed.com" class="white">电驴/电骡/emule</a></div>
		<div class="nav-down-right">
		<span>当前在线: <b><?=$onlineuser?></b></span></div>
	</div>







</div>

<div class="mainline">&nbsp;</div>
<div class="maincolumn">
		<div class="pagelist">
		<div class="pagelisttitleico">&nbsp;</div>
		<div class="pagelisttitlebg">
		<div class="pagelisttitlename">
<?=$navbar?>&nbsp;>&nbsp;<a href="index.php?mod=message&action=view&commentid=<?=$message[commentid]?>"><?=$comment[title]?></a>&nbsp;>&nbsp;修改评论
		</div>
		</div>
		<div class="clear">&nbsp;</div>
		<div class="pagecontent">


<fieldset><legend>修改评论</legend>
  <form method="post" action="<?=$g_o_back2root?>/index.php?mod=message" onSubmit="return process_data(this)">
                <div>
                  <label>标题:</label>
                    <input type="text" name="title" size="50" maxlength="50" value="<?=$message[title]?>">
                </div>
                <div>
                  <label>内容:</label>
                    <textarea name="message" cols="70" rows="10"><?=$message[message]?></textarea>
                </div>
                <div class=enter>
                  <td nowrap align="center" colspan="2">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="messageid" value="<?=$messageid?>">
                    <input type="submit" value=" 更  新 " class="buttot">
                    <input type="submit" name="preview" value=" 预  览 " class="buttot">
                    <input type="reset" value=" 重  置 "  class="buttot">
                  </td>
                </div>
  </form> 
</fieldset>


		</div>
		</div>
		</div>

<div class="mainline">&nbsp;</div>
<div id="footer">
	<div id="bottommenu">
	  <a href="#">关于站点</a> - <a href="#">广告服务</a> - <a href="#">联系我们</a> - <a href="#">版权隐私</a> - <a href="#">免责声明</a> - <a href="http://utspeed.com">合作伙伴</a> - <a href="http://phparticle.net" target="_blank">程序支持</a> - <a  href="#">网站地图</a> - <a href="#top">返回顶部</a>
	</div>
	<div class="topline">&nbsp;</div>
	<div id="bottom">版权所有：<a href="<?=$phparticleurl?>"><?=$phparticletitle?></a> 2006 未经授权禁止复制或建立镜像<br />

	  <span>Powered by: <a href="http://www.phparticle.net">phpArticle html</a> Version <?=$version?>.</span><br />
	</div>
	</div>
</div>
</body>
</html><?
exit;
}


if ($_POST[action]=="update") {

    if ($_POST[preview]) {
        

        //print_rr($_POST);

        if ($pauserinfo[userid]==0) {
            include("modules/default/nopermission.php");
        }


        $title = htmlspecialchars(trim($_POST[title]));
        $message = htmlspecialchars(trim($_POST[message]));
        $message1 = str_replace("  ","&nbsp;&nbsp;",nl2br($message));


        if (!pa_isset($title)) {
		$errormessage="error_comment_title_blank";
		include("modules/default/error.php");
        }
        if (!pa_isset($message)) {
		$errormessage="error_comment_message_blank";
		include("modules/default/error.php");
        }

        if (strlen($title)>$comment_title_limit) {
		$errormessage="error_comment_title_toolong";
		include("modules/default/error.php");
            //$title = substr($title,0,50);
        }
        if (strlen($message)>$comment_message_limit) {
		$errormessage="error_comment_message_toolong";
		include("modules/default/error.php");
        }

        ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
<title><?=$phparticletitle?> - 预览评论</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-CN" />
<link rel="icon" href="<?=$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<link rel="shortcut icon" href="<?$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<meta name="description" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<meta name="keywords" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<script src="<?=$g_o_back2root?>/images/js/chinahtml.js" type="text/javascript"></script>
<link href="<?=$g_o_back2root?>/images/css/css.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
<!--
function process_data(theform) {

                if (theform.title.value=="") {
                        alert("请输入标题!");
                        theform.title.focus();
                        return false;
                }

                if (theform.message.value=="") {
                        alert("请填写内容!");
                        theform.message.focus();
                        return false;
                }

}
//-->
</script>
</head>
<body>
<a name="top"></a>
<div id="header">
    <div id="topmenu">
		<div class="left">
			<div id="topdate"><?=$phparticleurl?></div>
		</div>
		<div class="right">
			<div id="topnavlist">
			<ul>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=usercp">会员面板</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=member&action=login">会员登陆</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=register">免费注册</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=search">高级搜索</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=search&action=lastupdate">最后更新</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=contribute">我要投稿</a></li>
				<li> <a href="<?=$g_o_back2root?>/index.php?mod=member&action=logout">退出登陆</a></li>
			</ul>
			</div>
		</div>
	</div>
	<div id="maintop">
		<div id="Logo"><a href="/"><img src="<?=$g_o_back2root?>/images/palogo.gif" alt="首页" /></a></div>
		<div id="TopAds"><a href="http://www.phparticle.net/" target="_blank"><img src="<?=$g_o_back2root?>/images/topbanner.jpg" width="468" height="60" alt="" /></a></div>
		<div id="toprightmenu">
			<ul>
				<li><a href="<?=$phparticleurl?>" onClick="this.style.behavior='url(#default#homepage)';this.setHomePage('<?=$phparticleurl?>');return(false);" style="behavior: url(#default#homepage)">设为首页</a></li>
				<li><a href=javascript:window.external.AddFavorite('<?=$phparticleurl?>','<?=$phparticletitle?>') title="希望您喜欢本站">加入收藏</a></li>
				<li><a href="mailto:semi.rock@gmail.com">联系我们</a></li>
			</ul>
		</div>
	</div>

	<div class="nav">

		<div class="nav-up-left"><a href="/" class="white">首页</a>　<a href="<?=$g_o_back2root?>/html/2/" class="white">精品文章</a>　<a href="<?=$g_o_back2root?>/cet/2/" class="white">使用帮助</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/1/13/" class="white">网站建设</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/4/6/" class="white">软件共享</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/1/12/" class="white">VC/C#/编成</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/1/9" class="white">UNIX/LINUX资料</a>　<a href="<?=$g_o_back2root?>/bbs" class="white">支持论坛</a>　<a href="http://www.utspeed.com" class="white">极速科技</a>　<a href="http://proxygo.com.ru" class="white">代理猎狗</a>  <a href="http://mp3.utspeed.com" class="white">音乐搜索</a></div>

	</div>

	<div class="navline">&nbsp;</div>

	<div class="nav">
		<div class="nav-down-left"><a href="<?=$g_o_back2root?>/bbshtmldata/4/6/" class="white">休闲娱乐</a>　<a href="http://music.utspeed.com" class="white">极速音乐</a>　<a href="http://4tc.com.ru" class="white">极速网址</a>　<a href="http://article.utspeed.com" class="white">幽默笑话</a>　<a href="http://woman.utspeed.com" class="white">女性美容</a>　<a href="http://nuskin.net.ru" class="white">如新网上购物商城</a>  <a href="http://bt.utspeed.com" class="white">电驴/电骡/emule</a></div>
		<div class="nav-down-right">
		<span>当前在线: <b><?=$onlineuser?></b></span></div>
	</div>







</div>

<div class="mainline">&nbsp;</div>
<div class="maincolumn">
		<div class="pagelist">
		<div class="pagelisttitleico">&nbsp;</div>
		<div class="pagelisttitlebg">
		<div class="pagelisttitlename">
你的位置：<a href="<?=$homepage?>/" class="classlinkclass">首页</a>&nbsp;>&nbsp;<a href="<?=$phparticleurl?>" class="classlinkclass"><?=$phparticletitle?></a>&nbsp;>&nbsp;预览评论
		</div>
		</div>
		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
<fieldset>
              <table width="100%" border="0" cellpadding="4">
                <tr>
                  <td colspan="2"><b>评论预览</b></td>
                </tr>
                <tr>
                  <td nowrap width="10%">标题:</td>
                  <td><b><?=$title?></b></td>
                </tr>
                <tr>
                  <td valign="top">内容:</td>
                  <td><?=$message1?></td>
                </tr>
              </table>
</fieldset>

<fieldset><legend>发表评论</legend>
  <form method="post" action="<?=$g_o_back2root?>/index.php?mod=message" onSubmit="return process_data(this)">

                <div>
                  <label>标题:</label>
                    <input type="text" name="title" size="50" maxlength="50" value="<?=$title?>">
                </div>
                <div>
                  <label>内容:</label>
                    <textarea name="message" cols="70" rows="10"><?=$message?></textarea>
                </div>
                <div class=ad>
                    <input type="hidden" name="action" value="doinsert">
                    <input type="hidden" name="commentid" value="<?=$commentid?>">
                    <input type="hidden" name="messageid" value="<?=$messageid?>">
                    <input type="submit" value=" 回  复 " class="buttot">
                    <input type="submit" name="preview" value=" 预  览 " class="buttot">
                    <input type="reset" value=" 重  置 "  class="buttot">
                </div>

  </form>
</fieldset>

		</div>
		</div>
		</div>


<div class="mainline">&nbsp;</div>
<div id="footer">
	<div id="bottommenu">
	  <a href="#">关于站点</a> - <a href="#">广告服务</a> - <a href="#">联系我们</a> - <a href="#">版权隐私</a> - <a href="#">免责声明</a> - <a href="http://utspeed.com">合作伙伴</a> - <a href="http://phparticle.net" target="_blank">程序支持</a> - <a  href="#">网站地图</a> - <a href="#top">返回顶部</a>
	</div>
	<div class="topline">&nbsp;</div>
	<div id="bottom">版权所有：<a href="<?=$phparticleurl?>"><?=$phparticletitle?></a> 2006 未经授权禁止复制或建立镜像<br />

	  <span>Powered by: <a href="http://www.phparticle.net">phpArticle html</a> Version <?=$version?>.</span><br />
	</div>
	</div>
</div>
</body>
</html><?

    } else {

        

        //print_rr($_POST);

        if ($pauserinfo[userid]==0) {
            include("modules/default/nopermission.php");
        }

        $messageid = intval($_POST[messageid]);
        $messageinfo = validate_messageid($messageid);

        $title = htmlspecialchars(trim($_POST[title]));
        $message = trim($_POST[message]);

        if (!pa_isset($title)) {
		$errormessage="error_message_title_blank";
		include("modules/default/error.php");

        }
        if (!pa_isset($message)) {
		$errormessage="error_message_message_blank";
		include("modules/default/error.php");
        }

        if (strlen($title)>$comment_title_limit) {
		$errormessage="error_comment_title_toolong";
		include("modules/default/error.php");
            //$title = substr($title,0,50);
        }
        if (strlen($message)>$comment_message_limit) {
		$errormessage="error_comment_message_toolong";
		include("modules/default/error.php");
        }

        unset($sql);
        if ((time()-$messageinfo[date])>300) {
            $sql = ",lastupdate=".time().",
                     lastupdater='".addslashes($pauserinfo[username])."'";
        }
        $DB->query("UPDATE ".$db_prefix."message SET
                             title='".addslashes($title)."',
                             message='".addslashes($message)."'
                             $sql
                             WHERE messageid='$messageid'
                             ");
	$url = "$phparticleurl/index.php?mod=message&action=view&commentid=$messageinfo[commentid]&messageid=$messageid";
	$redirectmsg="redirect_message_edited";
	include("modules/default/redirect.php");
    }
}

if ($_GET[action]=="reply") {

    if ($pauserinfo[userid]==0) {
        include("modules/default/nopermission.php");
    }

    $messageid = $_GET[messageid];
    $message = validate_messageid($messageid);

    $commentid = $message[commentid];
    $comment = validate_commentid($commentid);
    $article = validate_articleid($comment[articleid]);
	/*
    if ($pauserinfo[userid]!=$message[userid]) {
        include("modules/default/nopermission.php");
    }*/
    cachesorts();
    $sortlist = makesortlist();
    $article[tmp_title] = $article[title];

    $navbar = makearticlenavbar2($article);
    $article[title] = $article[tmp_title];

	$g_o_back2root=".";

    ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
<title><?=$phparticletitle?> - 回复评论</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-CN" />
<link rel="icon" href="<?=$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<link rel="shortcut icon" href="<?$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<meta name="description" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<meta name="keywords" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<script src="<?=$g_o_back2root?>/images/js/chinahtml.js" type="text/javascript"></script>
<link href="<?=$g_o_back2root?>/images/css/css.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
<!--
function process_data(theform) {

                if (theform.title.value=="") {
                        alert("请输入标题!");
                        theform.title.focus();
                        return false;
                }

                if (theform.message.value=="") {
                        alert("请填写内容!");
                        theform.message.focus();
                        return false;
                }

}

//-->
</script>
</head>
<body>
<a name="top"></a>
<div id="header">
    <div id="topmenu">
		<div class="left">
			<div id="topdate"><?=$phparticleurl?></div>
		</div>
		<div class="right">
			<div id="topnavlist">
			<ul>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=usercp">会员面板</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=member&action=login">会员登陆</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=register">免费注册</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=search">高级搜索</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=search&action=lastupdate">最后更新</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=contribute">我要投稿</a></li>
				<li> <a href="<?=$g_o_back2root?>/index.php?mod=member&action=logout">退出登陆</a></li>
			</ul>
			</div>
		</div>
	</div>
	<div id="maintop">
		<div id="Logo"><a href="/"><img src="<?=$g_o_back2root?>/images/palogo.gif" alt="首页" /></a></div>
		<div id="TopAds"><a href="http://www.phparticle.net/" target="_blank"><img src="<?=$g_o_back2root?>/images/topbanner.jpg" width="468" height="60" alt="" /></a></div>
		<div id="toprightmenu">
			<ul>
				<li><a href="<?=$phparticleurl?>" onClick="this.style.behavior='url(#default#homepage)';this.setHomePage('<?=$phparticleurl?>');return(false);" style="behavior: url(#default#homepage)">设为首页</a></li>
				<li><a href=javascript:window.external.AddFavorite('<?=$phparticleurl?>','<?=$phparticletitle?>') title="希望您喜欢本站">加入收藏</a></li>
				<li><a href="mailto:semi.rock@gmail.com">联系我们</a></li>
			</ul>
		</div>
	</div>

	<div class="nav">

		<div class="nav-up-left"><a href="/" class="white">首页</a>　<a href="<?=$g_o_back2root?>/html/2/" class="white">精品文章</a>　<a href="<?=$g_o_back2root?>/cet/2/" class="white">使用帮助</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/1/13/" class="white">网站建设</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/4/6/" class="white">软件共享</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/1/12/" class="white">VC/C#/编成</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/1/9" class="white">UNIX/LINUX资料</a>　<a href="<?=$g_o_back2root?>/bbs" class="white">支持论坛</a>　<a href="http://www.utspeed.com" class="white">极速科技</a>　<a href="http://proxygo.com.ru" class="white">代理猎狗</a>  <a href="http://mp3.utspeed.com" class="white">音乐搜索</a></div>

	</div>

	<div class="navline">&nbsp;</div>

	<div class="nav">
		<div class="nav-down-left"><a href="<?=$g_o_back2root?>/bbshtmldata/4/6/" class="white">休闲娱乐</a>　<a href="http://music.utspeed.com" class="white">极速音乐</a>　<a href="http://4tc.com.ru" class="white">极速网址</a>　<a href="http://article.utspeed.com" class="white">幽默笑话</a>　<a href="http://woman.utspeed.com" class="white">女性美容</a>　<a href="http://nuskin.net.ru" class="white">如新网上购物商城</a>  <a href="http://bt.utspeed.com" class="white">电驴/电骡/emule</a></div>
		<div class="nav-down-right">
		<span>当前在线: <b><?=$onlineuser?></b></span></div>
	</div>







</div>


<div class="mainline">&nbsp;</div>
<div class="maincolumn">
		<div class="pagelist">
		<div class="pagelisttitleico">&nbsp;</div>
		<div class="pagelisttitlebg">
		<div class="pagelisttitlename">
<?=$navbar?>&nbsp;>&nbsp;<a href="index.php?mod=message&action=view&commentid=<?=$message[commentid]?>"><?=$comment[title]?></a>&nbsp;>&nbsp;回复评论
		</div>
		</div>
		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
<fieldset><legend>回复评论</legend>
  <form method="post" action="<?=$g_o_back2root?>/index.php?mod=message" onSubmit="return process_data(this)">

                <div>
                  <label>标题:</label>
                    <input type="text" name="title" size="50" maxlength="50" value="回复: <?=$message[title]?>">
                </div>
                <div>
                  <label>内容:</label>
                    <textarea name="message" cols="70" rows="10"></textarea>
                </div>
                <div class=enter>

                    <input type="hidden" name="action" value="doinsert">
                    <input type="hidden" name="commentid" value="<?=$commentid?>">
                    <input type="hidden" name="messageid" value="<?=$messageid?>">
                    <input type="submit" value=" 回  复 " class="buttot">
                    <input type="submit" name="preview" value=" 预  览 " class="buttot">
                    <input type="reset" value=" 重  置 "  class="buttot">

                </div>

  </form>
</fieldset>

		</div>
		</div>
		</div>



<div class="mainline">&nbsp;</div>
<div id="footer">
	<div id="bottommenu">
	  <a href="#">关于站点</a> - <a href="#">广告服务</a> - <a href="#">联系我们</a> - <a href="#">版权隐私</a> - <a href="#">免责声明</a> - <a href="http://utspeed.com">合作伙伴</a> - <a href="http://phparticle.net" target="_blank">程序支持</a> - <a  href="#">网站地图</a> - <a href="#top">返回顶部</a>
	</div>
	<div class="topline">&nbsp;</div>
	<div id="bottom">版权所有：<a href="<?=$phparticleurl?>"><?=$phparticletitle?></a> 2006 未经授权禁止复制或建立镜像<br />

	  <span>Powered by: <a href="http://www.phparticle.net">phpArticle html</a> Version <?=$version?>.</span><br />
	</div>
	</div>
</div>
</body>
</html><?
exit;
}
?>


<?
function makesortnavbar($sortid) {
        global $DB, $db_prefix, $phparticletitle, $phparticleurl,$g_o_back2root, $parentsort;

        $navbit .= makesortnavbarbit($sortid, $parentsort);

        $navbar = "
你的位置：<a href='$homepage/' class='classlinkclass'>首页</a>&nbsp;>&nbsp;<a href='$phparticleurl' class='classlinkclass'>$phparticletitle</a>$navbit
";
        return $navbar;
} 

function makesortnavbarbit($sortid, $parentsort, $isarticle = 0) {
        global $writedir, $subsort,$g_o_back2root,$g_depth,$usename,$singledir,$subsort,$usedate;
        static $outdirs;
        if ($sortid != -1) {
        	if($singledir==2)//没有子目录
        	{
        		$sorthtmllink = $subsort["dirname_$sortid"].".".HTMLEXT;
        		//$g_o_back2root="..";//当前对应的根目录相对路径
        		if ($parentsort[$sortid])
	                        foreach ($parentsort[$sortid] as $parentsortid => $title) {
	                        $navbit = "
&nbsp;>&nbsp;
";
	                        $navbit .= "
<a href='$sorthtmllink' class='classlinkclass'>$title</a>
";
	                        $g_depth++;
	                        $navbit = makesortnavbarbit($parentsortid, $parentsort) . $navbit;
	                } 
	                //$writedir = "";
        	}else if($singledir==1)
        	{
        		$g_o_back2root="../".$g_o_back2root;//当前对应的根目录相对路径
        		if ($parentsort[$sortid])
	                        foreach ($parentsort[$sortid] as $parentsortid => $title) {
	                        $sortdirs = ($usename?$subsort["dirname_$sortid"]:$sortid)."/";
	                        $sorthtmllink = $outdirs.$subsort["dirname_$sortid"].".".HTMLEXT;
	                        $navbit = "
&nbsp;>&nbsp;
";
	                        $navbit .= "
<a href='$sorthtmllink' class='classlinkclass'>$title</a>
";
	                        $outdirs = "../";
	                        $g_depth++;	                        
	                        $navbit = makesortnavbarbit($parentsortid, $parentsort) . $navbit;
	                        $outdirs = "";
	                }	
	                $writedir .= $sortdirs."/";
        	}else
        	{
	                if ($isarticle == 1&&$usedate)
	                {
	                	$outdirs .= "../";
	                	$g_o_back2root="../".$g_o_back2root;
	                	$g_depth=1;
	                }
	                if ($parentsort[$sortid])
	                        foreach ($parentsort[$sortid] as $parentsortid => $title) {
	                        if ($sortdirs)
	                                $sortdirs = ($usename?$subsort["dirname_$sortid"]:$sortid) . "/" . $sortdirs."/";
	                        else $sortdirs = ($usename?$subsort["dirname_$sortid"]:$sortid)."/";
	                        $sorthtmllink = $outdirs.$subsort["dirname_$sortid"].".".HTMLEXT; //.rawurlencode(mkfilename($filenamemethod,$sortinfo['title'],2)).$sortid."_".ceil($subsort["total_$sortid"]/$subsort["perpage_$sortid"]).".".HTMLEXT;
	                        $navbit = "
&nbsp;>&nbsp;
";
	                        $navbit .= "
<a href='$sorthtmllink' class='classlinkclass'>$title</a>
";
	                        $outdirs = "../" . $outdirs;
	                        $g_depth++;
	                        $g_o_back2root="../".$g_o_back2root;//当前对应的根目录相对路径
	                        $navbit = makesortnavbarbit($parentsortid, $parentsort) . $navbit;
	                        $outdirs = "";
	                }	
	                $writedir .= $sortdirs."/";
        	}
        } 
        return $navbit;
}

function makearticlenavbar($article = array()) {
        global $DB, $db_prefix, $phparticletitle, $phparticleurl,$g_o_back2root, $parentsort;
        $navbit .= makesortnavbarbit($article['sortid'], $parentsort, 1);
        $navbit .= "
&nbsp;>&nbsp;
";
        $navbit .= "
$article[title]
";

        $navbar = "
你的位置：<a href='$homepage/' class='classlinkclass'>首页</a>&nbsp;>&nbsp;<a href='$phparticleurl' class='classlinkclass'>$phparticletitle</a>$navbit
";
        return $navbar;
} 
?>

<?
function makehot_recommend_articlelist($locate="index") {
        global $phparticleurl,$g_o_back2root,$g_back2path,$filenamemethod;
        global $subsort;
        global $DB, $db_prefix;
        global $styleid,$style,$tag_articlelist;
	$savename = $locate."_" . $styleid . "_articlelist";
        $cachedata = $DB->query("SELECT * FROM " . $db_prefix . "cache
                                                 WHERE name='sort_" . $styleid . "_articlelist' OR name='".$savename."'");
	if($tag_data1=$DB->fetch_array($cachedata))
	{
		if($tag_data1['name']!=$savename)
		{
			$cache=$tag_data1;
		}
		else $tag_articlelist=unserialize($tag_data1['content']);
	}
	if($tag_data2=$DB->fetch_array($cachedata))
	{
		if($tag_data2['name']!=$savename)
		{
			$cache=$tag_data2;
		}
		else $tag_articlelist=unserialize($tag_data2['content']);
	}
        if (!empty($cache) AND $cache['expiry'] == 0) { // 未过期
                $articlelist = unserialize($cache['content']);
        } else {
                $sorts = $DB->query("SELECT sortid,title,hotarticlenum,ratearticlenum FROM " . $db_prefix . "sort");
                while ($sortinfo = $DB->fetch_array($sorts)) {
                	$sortdir = get_sortdirs($sortinfo['sortid']);
                	$g_back2path = get_back2path($sortdir)."..";
                        $subsortids = getsubsorts($sortinfo['sortid']);
                        
                        unset($hotarticlelist);
                        unset($hotsortarticlelistbit);
                        $sortinfo['hotarticlenum'] = intval($sortinfo['hotarticlenum']);
                        if ($sortinfo['hotarticlenum'] > 0) {
                                $articles = $DB->query("SELECT articleid,article.sortid,date,article.title,views FROM " . $db_prefix . "article AS article
								                                           LEFT JOIN " . $db_prefix . "sort AS sort
								                                             ON article.sortid=sort.sortid
								                                           WHERE sort.sortid IN (0" . $subsortids . ") AND article.visible=1
								                                           ORDER BY views DESC LIMIT $sortinfo[hotarticlenum]");
                                while ($article = $DB->fetch_array($articles)) {
                                        $childdirs = preg_replace("@" . $sortdir . "/@", "", mkdirname($article['sortid'],"",$article['date'],0,0));//get_sortdirs($article['sortid'])."/"
                                        if (empty($childdirs))$childdirs = "./";
                                        $articlehtmllink = $childdirs . rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//date("Y_m", $article['date']) . "/".
                                        $hotsortarticlelistbit .= "";
                                }
                                $DB->free_result($articles);
                                $hotarticlelist = "
    <div class='rightblock'>
      <div class='righttitleico'>&nbsp;</div>
      <div class='righttitlename'>&nbsp;&nbsp;热门文章</div>
      <div class='clear'>&nbsp;</div>
      <div class='rightlist rightlistnoad'> <ul>
<!-- BEGIN hotsortarticlelistbit -->
<li><a href='$articlehtmllink' title='$article[title]'>$article[title]</a><!-- $article[views] --></li>
<!-- END hotsortarticlelistbit -->
      </ul> </div>
    </div>
";
                        }
                        $articlelist['hot'][$sortinfo['sortid']] = $hotarticlelist;

                        unset($poparticlelist);
                        unset($popsortarticlelistbit);
                        $sortinfo[ratearticlenum] = intval($sortinfo[ratearticlenum]);
                        if ($sortinfo['ratearticlenum'] > 0) {
                                $articles = $DB->query("SELECT articleid,article.sortid,date,article.title,views,(totalscore/voters) as averagescore,voters FROM " . $db_prefix . "article AS article
								                                           LEFT JOIN " . $db_prefix . "sort AS sort
								                                             ON article.sortid=sort.sortid
								                                           WHERE voters>0 AND sort.sortid IN (0" . $subsortids . ") AND article.visible=1
								                                           ORDER BY averagescore DESC
								                                           LIMIT $sortinfo[ratearticlenum]");
                                while ($article = $DB->fetch_array($articles)) {
                                        $childdirs = preg_replace("@" . $sortdir . "/@", "", mkdirname($article['sortid'],"",$article['date'],0,0));//get_sortdirs($article['sortid'])."/"
                                        if (empty($childdirs))$childdirs = "./";
                                        $articlehtmllink = $childdirs . rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//date("Y_m", $article['date']) . "/".
                                        $popsortarticlelistbit .= "";
                                } 
                                $DB->free_result($articles);
                                $poparticlelist = "
    <div class='rightblock'>
      <div class='righttitleico'>&nbsp;</div>
      <div class='righttitlename'>&nbsp;&nbsp;推荐文章</div>
      <div class='clear'>&nbsp;</div>
      <div class='rightlist rightlistnoad'> <ul>
<!-- BEGIN popsortarticlelistbit -->
<li><a href='$articlehtmllink'>$article[title]</a></li>
<!-- END popsortarticlelistbit -->
        </ul> </div>
    </div>
";
                        }
                        $articlelist['rate'][$sortinfo['sortid']] = $poparticlelist;
                } 
                if (!empty($cache) AND $cache[expiry] == 1) {
                        $DB->query("UPDATE " . $db_prefix . "cache SET
	                                    content='" . addslashes(serialize($articlelist)) . "',
	                                    expiry=0
	                                    WHERE name='sort_" . $styleid . "_articlelist'");
                } elseif (empty($cache)) {
                        $DB->query("INSERT INTO " . $db_prefix . "cache (name,content,expiry) VALUES
	                                    ('sort_" . $styleid . "_articlelist','" . addslashes(serialize($articlelist)) . "',0)");
                } 
        } 

        return $articlelist;
}
?>

<?
$counter = 0;
function makesortlist() {
        global $phparticleurl,$g_o_back2root;
        global $subsort;
        global $counter;
        global $DB, $db_prefix;
        global $styleid;
        global $style;

        $cache = $DB->fetch_one_array("SELECT * FROM " . $db_prefix . "cache
                                                 WHERE name='template_" . $styleid . "_sortlist'");

        if (!empty($cache) AND $cache['expiry'] == 0) { // 未过期
                $sortlist = $cache['content'];
        } else {
                unset($sortlistbit_level1);
                if (is_array($subsort[-1]))
                        foreach ($subsort[-1] as $sort['sortid'] => $sort['title']) {
                        $counter++;
                        unset($sortlistbit_level3);
                        unset($sortlistbit_level2);
                        $sorthtmllink = HTMLDIR . "/" . mkdirname($sort['sortid'],-1,0,0,0)."index." . HTMLEXT;//$sort['sortid']
                        if (isset($subsort[$sort['sortid']])) {
                                foreach ($subsort[$sort['sortid']] as $childsort['sortid'] => $childsort['title']) {
                                        $childsorthtmllink = HTMLDIR . "/" . mkdirname($childsort['sortid'],-1,0,0,0) . "/index." . HTMLEXT;//$sort['sortid'] . "/" . $childsort['sortid']
                                        $sortlistbit_level3 .= "";
                                } 
                                $sortlistbit_level2 = "";
                                $sort['plusorminus'] = "<img id=\"nav_img_$counter\" src=\"$phparticleurl/$style[imagesfolder]/expand.gif\" align=absmiddle style=\"cursor: hand\" onClick=\"ToggleNode(nav_tr_$counter,nav_img_$counter)\" vspace=\"2\" hspace=\"2\">";
                        } else {
                                $sort['plusorminus'] = "<img src=\"$phparticleurl/$style[imagesfolder]/expand.gif\" align=absmiddle vspace=\"2\" hspace=\"2\">";
                        }
                        $sortlistbit_level1 .= "";
                }
                $sortlist = "
<!-- BEGIN sortlistbit_level1 -->
    <div class='textad'>
      <div class='textadleft'><a href='./$sorthtmllink'>$sort[title]</a></div>
      <div class='textadright'>
<div id='adlist'>
<ul>
<!-- BEGIN sortlistbit_level2 -->
<!-- BEGIN sortlistbit_level3 -->
<li><a href=$childsorthtmllink rel='external'>$childsort[title]</a></li>
<!-- END sortlistbit_level3 -->
<!-- END sortlistbit_level2 -->
</ul>
</div>
      </div>
      </div>
<div class='mainline'>&nbsp;</div>
<!-- END sortlistbit_level1 -->
";
                if (!empty($cache) AND $cache[expiry] == 1) {
                        $DB->query("UPDATE " . $db_prefix . "cache SET
                                    content='" . addslashes($sortlist) . "',
                                    expiry=0
                                    WHERE name='template_" . $styleid . "_sortlist'");
                } elseif (empty($cache)) {
                        $DB->query("INSERT INTO " . $db_prefix . "cache (name,content,expiry) VALUES
                                    ('template_" . $styleid . "_sortlist','" . addslashes($sortlist) . "',0)");
                }
        }

        return $sortlist;
}
?>

<?
function makearticlenavbar2($article = array()) {
        global $DB, $db_prefix, $phparticletitle, $phparticleurl,$g_o_back2root, $parentsort;
        $navbit .= makesortnavbarbit2($article['sortid'], $parentsort);
        $navbit .= "
&nbsp;>&nbsp;
";
        $navbit .= "
$article[title]
";

        $navbar = "
你的位置：<a href='$homepage/' class='classlinkclass'>首页</a>&nbsp;>&nbsp;<a href='$phparticleurl' class='classlinkclass'>$phparticletitle</a>$navbit
";
        return $navbar;
}

function makesortnavbarbit2($sortid, $parentsort, $articlesortdir="") {
        global $phparticleurl,$g_o_back2root, $writedir, $subsort,$filenamemethod;
        if ($sortid != -1) {
                foreach ($parentsort[$sortid] as $parentsortid => $title) {
                	if($articlesortdir=="")$articlesortdir=mkdirname($sortid,-1,0,0,0);//get_sortdirs($sortid)."/";
                        $sorthtmllink = HTMLDIR . "/" . $articlesortdir;//.rawurlencode(mkfilename($filenamemethod,$sort['title'],2)). $sortid . "_" . ceil($subsort["total_$sortid"] / $subsort["perpage_$sortid"]) . "." . HTMLEXT;
                        $navbit = "
&nbsp;>&nbsp;
";
                        $navbit .= "
<a href='$sorthtmllink' class='classlinkclass'>$title</a>
";
                        $articlesortdir=str_replace("/".$sortid."/","/",$articlesortdir);
                        $navbit = makesortnavbarbit2($parentsortid, $parentsort,$articlesortdir) . $navbit;
                } 
        } 
        return $navbit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
<title><?=$phparticletitle?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-CN" />
<link rel="icon" href="<?=$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<link rel="shortcut icon" href="<?$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<meta name="description" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<meta name="keywords" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<script src="<?=$g_o_back2root?>/images/js/chinahtml.js" type="text/javascript"></script>
<link href="<?=$g_o_back2root?>/images/css/css.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
<!--
function process_data(theform) {

                if (theform.title.value=="") {
                        alert("请输入标题!");
                        theform.title.focus();
                        return false;
                }

                if (theform.message.value=="") {
                        alert("请填写内容!");
                        theform.message.focus();
                        return false;
                }

}
//-->
</script>
</head>
<body>
<a name="top"></a>
<div id="header">
    <div id="topmenu">
		<div class="left">
			<div id="topdate"><?=$phparticleurl?></div>
		</div>
		<div class="right">
			<div id="topnavlist">
			<ul>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=usercp">会员面板</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=member&action=login">会员登陆</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=register">免费注册</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=search">高级搜索</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=search&action=lastupdate">最后更新</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=contribute">我要投稿</a></li>
				<li> <a href="<?=$g_o_back2root?>/index.php?mod=member&action=logout">退出登陆</a></li>
			</ul>
			</div>
		</div>
	</div>
	<div id="maintop">
		<div id="Logo"><a href="/"><img src="<?=$g_o_back2root?>/images/palogo.gif" alt="首页" /></a></div>
		<div id="TopAds"><a href="http://www.phparticle.net/" target="_blank"><img src="<?=$g_o_back2root?>/images/topbanner.jpg" width="468" height="60" alt="" /></a></div>
		<div id="toprightmenu">
			<ul>
				<li><a href="<?=$phparticleurl?>" onClick="this.style.behavior='url(#default#homepage)';this.setHomePage('<?=$phparticleurl?>');return(false);" style="behavior: url(#default#homepage)">设为首页</a></li>
				<li><a href=javascript:window.external.AddFavorite('<?=$phparticleurl?>','<?=$phparticletitle?>') title="希望您喜欢本站">加入收藏</a></li>
				<li><a href="mailto:semi.rock@gmail.com">联系我们</a></li>
			</ul>
		</div>
	</div>

	<div class="nav">

		<div class="nav-up-left"><a href="/" class="white">首页</a>　<a href="<?=$g_o_back2root?>/html/2/" class="white">精品文章</a>　<a href="<?=$g_o_back2root?>/cet/2/" class="white">使用帮助</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/1/13/" class="white">网站建设</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/4/6/" class="white">软件共享</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/1/12/" class="white">VC/C#/编成</a>　<a href="<?=$g_o_back2root?>/bbshtmldata/1/9" class="white">UNIX/LINUX资料</a>　<a href="<?=$g_o_back2root?>/bbs" class="white">支持论坛</a>　<a href="http://www.utspeed.com" class="white">极速科技</a>　<a href="http://proxygo.com.ru" class="white">代理猎狗</a>  <a href="http://mp3.utspeed.com" class="white">音乐搜索</a></div>

	</div>

	<div class="navline">&nbsp;</div>

	<div class="nav">
		<div class="nav-down-left"><a href="<?=$g_o_back2root?>/bbshtmldata/4/6/" class="white">休闲娱乐</a>　<a href="http://music.utspeed.com" class="white">极速音乐</a>　<a href="http://4tc.com.ru" class="white">极速网址</a>　<a href="http://article.utspeed.com" class="white">幽默笑话</a>　<a href="http://woman.utspeed.com" class="white">女性美容</a>　<a href="http://nuskin.net.ru" class="white">如新网上购物商城</a>  <a href="http://bt.utspeed.com" class="white">电驴/电骡/emule</a></div>
		<div class="nav-down-right">
		<span>当前在线: <b><?=$onlineuser?></b></span></div>
	</div>







</div>

<div class="mainline">&nbsp;</div>
<div class="maincolumn">
		<div class="pagelist">
		<div class="pagelisttitleico">&nbsp;</div>
		<div class="pagelisttitlebg">
		<div class="pagelisttitlename">
<?=$navbar?>
		</div>
		</div>

		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
					<div class="title"> 
					  <h1><a href="<?=$phparticleurl?>/<?=$articlehtmllink?>"><?=$article[title]?></a></h1> 
					  <h2><strong>日期：</strong><?=$article[date]?>&nbsp;&nbsp;<strong>点击：</strong><script src="<?=$phparticleurl?>/count.php"></script>&nbsp;&nbsp;<strong>作者：</strong><?=$article[author]?> &nbsp;&nbsp;<strong>来源：</strong><?=$article[source]?></h2> 
					</div>
					<div class="reg">

                <div><span class="left"><b><?=$messagemode?>　<a href="index.php?mod=comment&action=view&articleid=<?=$article[articleid]?>">查看所有评论</a>　<a href="index.php?mod=comment&action=add&articleid=<?=$article[articleid]?>">发表评论</a></b></span></div>
<div class="clear">&nbsp;</div>

                          <form name="" method="post" action="index.php?mod=message">
<fieldset><legend>评论:<?=$article[title]?></legend>
                                </form>
<?=$showmessage_messagelistbit?>

</fieldset>

     <div class="right"><?=$pagelinks?></div>
<?=$showmessage_thread?>
<?=$quickreplybox?>

           		</table>
		</div>
		</div>

		<div class="clear">&nbsp;</div>
		<div class="pagelistfooter">
			<div id="bklist"><a href="javascript:history.go(-1);"><img src="<?=$phparticleurl?>/images/ar2u.gif" width="5" height="8" /> 返回列表</a></div> 
          <div id="prv">
            <img src="<?=$phparticleurl?>/images/ar2b.gif" width="6" height="7" />&nbsp;
          </div> 
          <div id="next">
            <img src="<?=$phparticleurl?>/images/ar2.gif" width="6" height="7" />&nbsp;下面没有链接了
          </div> 
		</div>

</div>

</div>
<div class="mainline">&nbsp;</div>
<div id="footer">
	<div id="bottommenu">
	  <a href="#">关于站点</a> - <a href="#">广告服务</a> - <a href="#">联系我们</a> - <a href="#">版权隐私</a> - <a href="#">免责声明</a> - <a href="http://utspeed.com">合作伙伴</a> - <a href="http://phparticle.net" target="_blank">程序支持</a> - <a  href="#">网站地图</a> - <a href="#top">返回顶部</a>
	</div>
	<div class="topline">&nbsp;</div>
	<div id="bottom">版权所有：<a href="<?=$phparticleurl?>"><?=$phparticletitle?></a> 2006 未经授权禁止复制或建立镜像<br />

	  <span>Powered by: <a href="http://www.phparticle.net">phpArticle html</a> Version <?=$version?>.</span><br />
	</div>
	</div>
</div>
</body>
</html>