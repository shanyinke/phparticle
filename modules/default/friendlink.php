<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<?php
function show_logo($logo){
if($logo==1){
return "是";
}else{
return "否";
}
}
function show_state($state,$id){
switch($state){
case 0:
return "审批中..";
break;
case 1:
return "已通过";
break;
case 2:
return "被拒绝<a href=\"index.php?mod=friendlink&action=rejectreason&id=$id\">(查看原因)</a>";
break;
case 3:
return "已告知拒绝";
break;

}
}
function valid_friendlink($id){
global $DB,$db_prefix;
$friendlinkinfo = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."friendlink WHERE id='$id'");
if (empty($friendlinkinfo)) {
$errormessage="error_friendlink_not_exist";
include("modules/default/error.php");
}

return $friendlinkinfo;
}

/* -=-=-=-=-=-=-=-=-=-=-=-=-
start add friendlink
-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="add") {
if ($pauserinfo[userid]==0) {
include("modules/default/nopermission.php");
}

$position = "
申请友链
";

unset($bgcolor);
$bgcolor[friendlink] = "bgcolor=\"$style[firstalt]\"";

$memberbit = "<fieldset>

申请友情链接前，请先在贵站做上本站的文字或图片链接。
如果在贵站未发现本站文字或者图片链接或者贵站不符合本站要求，友情链接将不会生效。<br />
<div>
<label>
<h3>本站图片链接</h3>

<a href='http://phparticle.net' target='_blank'>
<img src='$g_o_back2root/boo/logo.gif' alt='phpAricle官方站' width='88' height='31' border='0' vspace='4'>
</a></label>

<textarea cols='70' rows='3' onClick='select()'><a href='http://phparticle.net' title:'phpAricle官方站' target='_blank'><img src='$g_o_back2root/boo/logo.gif' alt='phpAricle官方站' width='88' height='31' border='0' vspace='4'></a></textarea>
</div>

<div>
<label>
<h3>本站文字链接</h3>
</label>

<textarea cols='70' rows='3' onClick='select()'><a href='http://phparticle.net' title='phpAricle官方站' target='_blank'>PHP文章学习之家</a></textarea>
</div>
</fieldset>


<fieldset><legend>申请友链</legend>
  <form method='post' name='form' action='index.php?mod=friendlink'>
<div><a href='index.php?mod=friendlink&action=list'><b>-==查看已申请的友链==-</b></a></div>
          
          <div> 
            <label><span class='reqasterisk'>*</span>站点名称: (必填)</label>
            <input name='sitename' type='text' id='sitename' size='30' maxlength='100'>
          </div>
          <div> 
            <label><span class='reqasterisk'>*</span>站点地址: (必填)</label>
            <input name='siteurl' type='text' id='siteurl' value='http://' size='30' maxlength='100'>
          </div>
          <div> 
            <label>站点说明:  </label>
            <input name='note' type='text' id='note' size='30' maxlength='200'>
            <br />(可选,鼠标移至链接上方时显示) 
          </div>
          
		   
           <div> 
            <label>是否使用logo?</label>
            <select name='isimg' id='isimg'>
              <option value='0'>否</option>
              <option value='1'>是</option>
            </select>
          </div>
          <div> 
            <label>logo地址 </label>
             <input name='logourl' type='text' id='logourl' value='http://' size='30' maxlength='100'><br /> (选择使用logo时必填)
          </div>
          <div> 
            <label><span class='reqasterisk'>*</span>申请理由,说明:</label>
             <textarea name='request' cols='50' rows='5' id='request'>尊敬的 $phparticletitle 站长:</textarea> 
          </div>
          
          
          <div class=ad> 
            <input type='hidden' name='action' value='insert'> 
             <input type='submit' value='提交申请' class='buttot'>  
             <input name='reset' type='reset' class='buttot' value='重置表单'></td>
          </div>

   </form>
  
</fieldset>";

}
/* -=-=-=-=-=-=-=-=-=-=-=-=-
start insert friendlink
-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_POST[action]=="insert") {

if ($pauserinfo[userid]==0) {
include("modules/default/nopermission.php");
}
$validtime = 600;//允许连续申请的最小间隔时间,单位：秒;
$result = $DB->fetch_one_array("SELECT MAX(jointime) AS lastjointime FROM ".$db_prefix."friendlink WHERE editor='$pauserinfo[username]'");
if((time()-$result[lastjointime])< $validtime){
$errormessage="error_interval_tooshort";
include("modules/default/error.php");
}

$sitename = htmlspecialchars(trim($_POST[sitename]));
$note = htmlspecialchars(trim($_POST[note]));
$siteurl = htmlspecialchars(trim($_POST[siteurl]));
$isimg = htmlspecialchars(trim($_POST[isimg]));
$logourl = htmlspecialchars(trim($_POST[logourl]));
$request = htmlspecialchars(trim($_POST[request]));
if($sitename==""){
$errormessage="error_sitename_blank";
include("modules/default/error.php");
}
if($siteurl==""){
$errormessage="error_siteurl_blank";
include("modules/default/error.php");
}
if($isimg==1 && $logourl==""){
$errormessage="error_logourl_blank";
include("modules/default/error.php");
}
$DB->query("INSERT INTO ".$db_prefix."friendlink (sitename,note,siteurl,isimg,logourl,visible,editor,jointime,request)
VALUES ('".addslashes($sitename)."','".addslashes($note)."','".addslashes($siteurl)."','$isimg','".addslashes($logourl)."','0','".addslashes($pauserinfo[username])."','".time()."','".addslashes($request)."')");

$url = "./index.php?mod=friendlink&action=list";
$redirectmsg="redirect_add_friendlink_success";
include("modules/default/redirect.php");
}
/* -=-=-=-=-=-=-=-=-=-=-=-=-
start list friendlink
-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="list") {

if ($pauserinfo[userid]==0) {
include("modules/default/nopermission.php");
}

$position = "
已申请的友链
";

unset($bgcolor);
$bgcolor[friendlink] = "bgcolor=\"$style[firstalt]\"";

$totals = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."friendlink WHERE editor='$pauserinfo[username]'");
//-------------------------------pagenav_start----------------------------
$perpage = 10;
$totalresults = $totals[count];

$totalpages = ceil($totals[count]/ $perpage);

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
$pagelinks = makepagelink2("$phparticleurl/index.php?mod=friendlink&action=list",$pagenum,$totalpages);
$pagenav = "";
//-------------------------------pagenav_end----------------------------
if($totals[count]>0){
$friendlinklists = $DB->query("SELECT * FROM ".$db_prefix."friendlink AS friendlink
WHERE editor='$pauserinfo[username]'
ORDER BY visible,id DESC
LIMIT $offset,$perpage");
while($friendlinklist = $DB->fetch_array($friendlinklists)){
if($friendlinklist[isimg]==1){
$friendlinklist[logourl] = "<img src=".$friendlinklist[logourl]." width=\"88\" height=\"31\">";
}else{
$friendlinklist[logourl] = "";
}
$friendlinklist[isimg] = show_logo($friendlinklist[isimg]);
$friendlinklist[visible] = show_state($friendlinklist[visible],$friendlinklist[id]);
$friendlinklist[jointime] = date("Y-m-d",$friendlink[jointime]);
$member_list_friendlinkbit .= "";
}
}
$memberbit = "
<table width='100%'  border='0' cellpadding='3'>
  <tr>
    <td align='center' width=10%><b>站点名</b></td>
    <td align='center' width=15%><b>站点地址</b></td>
    <td align='center' width=40%><b>站点说明</b></td>
    <td align='center'><b>logo</b></td>
    <td align='center' width=10%><b>申请时间</b></td>
	<td align='center' width=10%><b>状态</b></td>
	<td align='center' width=5%><b>编辑</b></td>
  </tr>
<!-- BEGIN member_list_friendlinkbit -->
 			<tr>
          <td align='center'>$friendlinklist[sitename]</td>
          <td align='center'>$friendlinklist[siteurl]</td>
          <td align='center'>$friendlinklist[note]</td>
		  <td align='center'>$friendlinklist[logourl]</td>
		  <td align='center'>$friendlinklist[jointime]</td>
		  <td align='center'><font color=red>$friendlinklist[visible]</font></td>
          <td align='center'><a href='index.php?mod=friendlink&action=edit&id=$friendlinklist[id]'>[编辑]</a></td>  
			</tr>
<!-- END member_list_friendlinkbit -->
</table>
<table width='100%' border='0' cellpadding='3'>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<!-- BEGIN pagenav -->
<div id='sublistfooter'>
     <div class='left'>共 <b>$totalresults</b> ,显示 <b>$from -
      $to</b></div>
     <div class='right'>$pagelinks</div>
</div>
<!-- END pagenav -->
";

}
/* -=-=-=-=-=-=-=-=-=-=-=-=-
start edit friendlink
-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="edit") {

if ($pauserinfo[userid]==0) {
include("modules/default/nopermission.php");
}
$friendlinkinfo = valid_friendlink($_GET[id]);
if($friendlinkinfo[editor]!=$pauserinfo[username]){
$errormessage="error_friendlink_editor_not_match";
include("modules/default/error.php");
}
if($friendlinkinfo[visible] == 1){
$errormessage="error_friendlink_added";
include("modules/default/error.php");
}
if(($friendlinkinfo[visible] == 2)||($friendlinkinfo[visible] == 3)){
$errormessage="error_friendlink_reject";
include("modules/default/error.php");
}

$friendlinkinfo[sitename] = stripslashes($friendlinkinfo[sitename]);
$friendlinkinfo[siteurl] = stripslashes($friendlinkinfo[siteurl]);
$friendlinkinfo[note] = stripslashes($friendlinkinfo[note]);
$selectedisimg[$friendlinkinfo[isimg]] = "selected";
$friendlinkinfo[logourl] = stripslashes($friendlinkinfo[logourl]);
$friendlinkinfo[request] = stripslashes($friendlinkinfo[request]);

$position = "
编辑友链
";
unset($bgcolor);
$bgcolor[friendlink] = "bgcolor=\"$style[firstalt]\"";
$memberbit = "<fieldset><legend>编辑友链</legend>
  <form method='post' name='form' action='index.php?mod=friendlink'>

<div><a href='index.php?mod=friendlink&action=list'><b>-==查看已申请的友链==-</b></a></div>

          
          <div> 
            <label><span class='reqasterisk'>*</span>站点名称: (必填)</label>
            <input name='sitename' type='text' id='sitename' value='$friendlinkinfo[sitename]' size='30' maxlength='100'>
          </div>
          <div> 
            <label><span class='reqasterisk'>*</span>站点地址: (必填)</label>
            <input name='siteurl' type='text' id='siteurl' value='$friendlinkinfo[siteurl]' size='30' maxlength='100'>
          </div>
		  <div> 
            <label>站点说明:</label>
            <input name='note' type='text' id='note' value='$friendlinkinfo[note]' size='30' maxlength='200'><br /> (可选,鼠标移至链接上方时显示)
          </div>
          
		   
         <div> 
            <label>是否使用logo?</label>
            <select name='isimg' id='isimg'>
              <option value='0' $selectedisimg[0] >否</option>
              <option value='1' $selectedisimg[1] >是</option>
            </select>
          </div>
		  <div> 
            <label>logo地址</label>
            <input name='logourl' type='text' id='logourl' value='$friendlinkinfo[logourl]' size='30' maxlength='100'><br />(选择使用logo时必填) 
          </div>
          <div> 
            <label><span class='reqasterisk'>*</span>申请理由,说明:</label>
            <textarea name='request' cols='50' rows='5' id='request'>$friendlinkinfo[request]</textarea> 

          </div>
          
          
          <div class=ad> 
<input name='id' type='hidden' id='id' value='$_GET[id]'>            
<input type='hidden' name='action' value='mod'> 
<input type='submit' value='提交修改' class='buttot'>
<input type='reset' value='重置表单' class='buttot'>     
          </div>
   </form>
  
</fieldset>";

}
/* -=-=-=-=-=-=-=-=-=-=-=-=-
start modify friendlink
-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_POST[action]=="mod") {

if ($pauserinfo[userid]==0) {
include("modules/default/nopermission.php");
}
$sitename = htmlspecialchars(trim($_POST[sitename]));
$note = htmlspecialchars(trim($_POST[note]));
$siteurl = htmlspecialchars(trim($_POST[siteurl]));
$isimg = htmlspecialchars(trim($_POST[isimg]));
$logourl = htmlspecialchars(trim($_POST[logourl]));
$request = htmlspecialchars(trim($_POST[request]));
if($sitename==""){
$errormessage="error_sitename_blank";
include("modules/default/error.php");
}
if($siteurl==""){
$errormessage="error_siteurl_blank";
include("modules/default/error.php");
}
if($isimg==1 && $logourl==""){
$errormessage="error_logourl_blank";
include("modules/default/error.php");
}
$DB->query("UPDATE ".$db_prefix."friendlink
SET sitename = '$sitename',
siteurl = '$siteurl',
note = '$note',
isimg = '$isimg',
logourl = '$logourl',
request = '$request',
jointime = '".time()."'
WHERE id = '$_POST[id]'"
);
$url = "./index.php?mod=friendlink&action=list";
$redirectmsg="redirect_edit_friendlink_success";
include("modules/default/redirect.php");
}
/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
start view_reject_reason friendlink
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="rejectreason") {

if ($pauserinfo[userid]==0) {
include("modules/default/nopermission.php");
}
$friendlinkinfo = valid_friendlink($_GET[id]);
if($friendlinkinfo[editor]!=$pauserinfo[username]){
$errormessage="error_friendlink_editor_not_match";
include("modules/default/error.php");
}

$friendlinkinfo[request] = stripslashes($friendlinkinfo[request]);
if($friendlinkinfo[request]==""){
$friendlinkinfo[request]="没有原因";
}

$position = "
查看拒绝原因
";
unset($bgcolor);
$bgcolor[friendlink] = "bgcolor=\"$style[firstalt]\"";

$memberbit = "
<table width='100%' border='0' cellspacing='0' cellpadding='0' bgcolor='$style[bordercolor]'>
  <form method='post' name='form' action='index.php?mod=friendlink'>
    <tr>
      <td>
        <table width='100%' border='0'  cellpadding='4'>
		<tr><td><a href='index.php?mod=friendlink&action=list'><span id='tbh'><b>-==查看已申请的友链==-</b></span></a></td>
		  </tr>
          <tr> 
            <td><strong>拒绝原因告知</strong></td>
          </tr>
          <tr> 
            <td valign='top'><font color=red>$friendlinkinfo[request]</font></td>
          </tr>
          <tr align='center'> 
            <td> <input name='id' type='hidden' id='id' value='$_GET[id]'>              <input type='hidden' name='url' value='$url'> <input type='hidden' name='action' value='apprize'> 
              <input type='submit' value='收到告知' class='button'>  
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </form>
  </table>
";

}

/* -=-=-=-=-=-=-=-=-=-=-=-=-
start apprize friendlink
-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_POST[action]=="apprize") {

if ($pauserinfo[userid]==0) {
include("modules/default/nopermission.php");
}

$DB->query("UPDATE ".$db_prefix."friendlink
SET visible = 3
WHERE id = '$_POST[id]'"
);
$url = "./index.php?mod=friendlink&action=list";
$redirectmsg="redirect_apprize_friendlink_success";
include("modules/default/redirect.php");
}
?>
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
你的位置：<a href="<?=$homepage?>/" class="classlinkclass">首页</a>&nbsp;>&nbsp;<a href="<?=$phparticleurl?>" class="classlinkclass"><?=$phparticletitle?></a>&nbsp;>&nbsp;<?=$position?>
		</div>
		</div>
		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
					<div class="title1"> 
<b>控制台导航</b> <br/>
<a href="index.php?mod=usercp">我的控制台</a><br/>
<a href="index.php?mod=favorite&action=view">我的收藏</a><br/>
<a href="index.php?mod=myarticle&action=view">我的文章</a><br/>
<a href="index.php?mod=member&action=modpassword">修改密码</a><br/>
<a href="index.php?mod=member&action=modprofile">修改资料</a><br/>

					</div>
					<div class="reg1">
<?=$memberbit?>
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