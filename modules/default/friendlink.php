<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<?php
function show_logo($logo){
if($logo==1){
return "��";
}else{
return "��";
}
}
function show_state($state,$id){
switch($state){
case 0:
return "������..";
break;
case 1:
return "��ͨ��";
break;
case 2:
return "���ܾ�<a href=\"index.php?mod=friendlink&action=rejectreason&id=$id\">(�鿴ԭ��)</a>";
break;
case 3:
return "�Ѹ�֪�ܾ�";
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
��������
";

unset($bgcolor);
$bgcolor[friendlink] = "bgcolor=\"$style[firstalt]\"";

$memberbit = "<fieldset>

������������ǰ�������ڹ�վ���ϱ�վ�����ֻ�ͼƬ���ӡ�
����ڹ�վδ���ֱ�վ���ֻ���ͼƬ���ӻ��߹�վ�����ϱ�վҪ���������ӽ�������Ч��<br />
<div>
<label>
<h3>��վͼƬ����</h3>

<a href='http://phparticle.net' target='_blank'>
<img src='$g_o_back2root/boo/logo.gif' alt='phpAricle�ٷ�վ' width='88' height='31' border='0' vspace='4'>
</a></label>

<textarea cols='70' rows='3' onClick='select()'><a href='http://phparticle.net' title:'phpAricle�ٷ�վ' target='_blank'><img src='$g_o_back2root/boo/logo.gif' alt='phpAricle�ٷ�վ' width='88' height='31' border='0' vspace='4'></a></textarea>
</div>

<div>
<label>
<h3>��վ��������</h3>
</label>

<textarea cols='70' rows='3' onClick='select()'><a href='http://phparticle.net' title='phpAricle�ٷ�վ' target='_blank'>PHP����ѧϰ֮��</a></textarea>
</div>
</fieldset>


<fieldset><legend>��������</legend>
  <form method='post' name='form' action='index.php?mod=friendlink'>
<div><a href='index.php?mod=friendlink&action=list'><b>-==�鿴�����������==-</b></a></div>
          
          <div> 
            <label><span class='reqasterisk'>*</span>վ������: (����)</label>
            <input name='sitename' type='text' id='sitename' size='30' maxlength='100'>
          </div>
          <div> 
            <label><span class='reqasterisk'>*</span>վ���ַ: (����)</label>
            <input name='siteurl' type='text' id='siteurl' value='http://' size='30' maxlength='100'>
          </div>
          <div> 
            <label>վ��˵��:  </label>
            <input name='note' type='text' id='note' size='30' maxlength='200'>
            <br />(��ѡ,������������Ϸ�ʱ��ʾ) 
          </div>
          
		   
           <div> 
            <label>�Ƿ�ʹ��logo?</label>
            <select name='isimg' id='isimg'>
              <option value='0'>��</option>
              <option value='1'>��</option>
            </select>
          </div>
          <div> 
            <label>logo��ַ </label>
             <input name='logourl' type='text' id='logourl' value='http://' size='30' maxlength='100'><br /> (ѡ��ʹ��logoʱ����)
          </div>
          <div> 
            <label><span class='reqasterisk'>*</span>��������,˵��:</label>
             <textarea name='request' cols='50' rows='5' id='request'>�𾴵� $phparticletitle վ��:</textarea> 
          </div>
          
          
          <div class=ad> 
            <input type='hidden' name='action' value='insert'> 
             <input type='submit' value='�ύ����' class='buttot'>  
             <input name='reset' type='reset' class='buttot' value='���ñ�'></td>
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
$validtime = 600;//���������������С���ʱ��,��λ����;
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
�����������
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
    <td align='center' width=10%><b>վ����</b></td>
    <td align='center' width=15%><b>վ���ַ</b></td>
    <td align='center' width=40%><b>վ��˵��</b></td>
    <td align='center'><b>logo</b></td>
    <td align='center' width=10%><b>����ʱ��</b></td>
	<td align='center' width=10%><b>״̬</b></td>
	<td align='center' width=5%><b>�༭</b></td>
  </tr>
<!-- BEGIN member_list_friendlinkbit -->
 			<tr>
          <td align='center'>$friendlinklist[sitename]</td>
          <td align='center'>$friendlinklist[siteurl]</td>
          <td align='center'>$friendlinklist[note]</td>
		  <td align='center'>$friendlinklist[logourl]</td>
		  <td align='center'>$friendlinklist[jointime]</td>
		  <td align='center'><font color=red>$friendlinklist[visible]</font></td>
          <td align='center'><a href='index.php?mod=friendlink&action=edit&id=$friendlinklist[id]'>[�༭]</a></td>  
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
     <div class='left'>�� <b>$totalresults</b> ,��ʾ <b>$from -
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
�༭����
";
unset($bgcolor);
$bgcolor[friendlink] = "bgcolor=\"$style[firstalt]\"";
$memberbit = "<fieldset><legend>�༭����</legend>
  <form method='post' name='form' action='index.php?mod=friendlink'>

<div><a href='index.php?mod=friendlink&action=list'><b>-==�鿴�����������==-</b></a></div>

          
          <div> 
            <label><span class='reqasterisk'>*</span>վ������: (����)</label>
            <input name='sitename' type='text' id='sitename' value='$friendlinkinfo[sitename]' size='30' maxlength='100'>
          </div>
          <div> 
            <label><span class='reqasterisk'>*</span>վ���ַ: (����)</label>
            <input name='siteurl' type='text' id='siteurl' value='$friendlinkinfo[siteurl]' size='30' maxlength='100'>
          </div>
		  <div> 
            <label>վ��˵��:</label>
            <input name='note' type='text' id='note' value='$friendlinkinfo[note]' size='30' maxlength='200'><br /> (��ѡ,������������Ϸ�ʱ��ʾ)
          </div>
          
		   
         <div> 
            <label>�Ƿ�ʹ��logo?</label>
            <select name='isimg' id='isimg'>
              <option value='0' $selectedisimg[0] >��</option>
              <option value='1' $selectedisimg[1] >��</option>
            </select>
          </div>
		  <div> 
            <label>logo��ַ</label>
            <input name='logourl' type='text' id='logourl' value='$friendlinkinfo[logourl]' size='30' maxlength='100'><br />(ѡ��ʹ��logoʱ����) 
          </div>
          <div> 
            <label><span class='reqasterisk'>*</span>��������,˵��:</label>
            <textarea name='request' cols='50' rows='5' id='request'>$friendlinkinfo[request]</textarea> 

          </div>
          
          
          <div class=ad> 
<input name='id' type='hidden' id='id' value='$_GET[id]'>            
<input type='hidden' name='action' value='mod'> 
<input type='submit' value='�ύ�޸�' class='buttot'>
<input type='reset' value='���ñ�' class='buttot'>     
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
$friendlinkinfo[request]="û��ԭ��";
}

$position = "
�鿴�ܾ�ԭ��
";
unset($bgcolor);
$bgcolor[friendlink] = "bgcolor=\"$style[firstalt]\"";

$memberbit = "
<table width='100%' border='0' cellspacing='0' cellpadding='0' bgcolor='$style[bordercolor]'>
  <form method='post' name='form' action='index.php?mod=friendlink'>
    <tr>
      <td>
        <table width='100%' border='0'  cellpadding='4'>
		<tr><td><a href='index.php?mod=friendlink&action=list'><span id='tbh'><b>-==�鿴�����������==-</b></span></a></td>
		  </tr>
          <tr> 
            <td><strong>�ܾ�ԭ���֪</strong></td>
          </tr>
          <tr> 
            <td valign='top'><font color=red>$friendlinkinfo[request]</font></td>
          </tr>
          <tr align='center'> 
            <td> <input name='id' type='hidden' id='id' value='$_GET[id]'>              <input type='hidden' name='url' value='$url'> <input type='hidden' name='action' value='apprize'> 
              <input type='submit' value='�յ���֪' class='button'>  
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
				<li><a href="<?=$g_o_back2root?>/index.php?mod=usercp">��Ա���</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=member&action=login">��Ա��½</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=register">���ע��</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=search">�߼�����</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=search&action=lastupdate">������</a></li>
				<li><a href="<?=$g_o_back2root?>/index.php?mod=contribute">��ҪͶ��</a></li>
				<li> <a href="<?=$g_o_back2root?>/index.php?mod=member&action=logout">�˳���½</a></li>
			</ul>
			</div>
		</div>
	</div>
	<div id="maintop">
		<div id="Logo"><a href="/"><img src="<?=$g_o_back2root?>/images/palogo.gif" alt="��ҳ" /></a></div>
		<div id="TopAds"><a href="http://www.phparticle.net/" target="_blank"><img src="<?=$g_o_back2root?>/images/topbanner.jpg" width="468" height="60" alt="" /></a></div>
		<div id="toprightmenu">
			<ul>
				<li><a href="<?=$phparticleurl?>" onClick="this.style.behavior='url(#default#homepage)';this.setHomePage('<?=$phparticleurl?>');return(false);" style="behavior: url(#default#homepage)">��Ϊ��ҳ</a></li>
				<li><a href=javascript:window.external.AddFavorite('<?=$phparticleurl?>','<?=$phparticletitle?>') title="ϣ����ϲ����վ">�����ղ�</a></li>
				<li><a href="mailto:semi.rock@gmail.com">��ϵ����</a></li>
			</ul>
		</div>
	</div>

	<div class="nav">

		<div class="nav-up-left"><a href="/" class="white">��ҳ</a>��<a href="<?=$g_o_back2root?>/html/2/" class="white">��Ʒ����</a>��<a href="<?=$g_o_back2root?>/cet/2/" class="white">ʹ�ð���</a>��<a href="<?=$g_o_back2root?>/bbshtmldata/1/13/" class="white">��վ����</a>��<a href="<?=$g_o_back2root?>/bbshtmldata/4/6/" class="white">�������</a>��<a href="<?=$g_o_back2root?>/bbshtmldata/1/12/" class="white">VC/C#/���</a>��<a href="<?=$g_o_back2root?>/bbshtmldata/1/9" class="white">UNIX/LINUX����</a>��<a href="<?=$g_o_back2root?>/bbs" class="white">֧����̳</a>��<a href="http://www.utspeed.com" class="white">���ٿƼ�</a>��<a href="http://proxygo.com.ru" class="white">�����Թ�</a>  <a href="http://mp3.utspeed.com" class="white">��������</a></div>

	</div>

	<div class="navline">&nbsp;</div>

	<div class="nav">
		<div class="nav-down-left"><a href="<?=$g_o_back2root?>/bbshtmldata/4/6/" class="white">��������</a>��<a href="http://music.utspeed.com" class="white">��������</a>��<a href="http://4tc.com.ru" class="white">������ַ</a>��<a href="http://article.utspeed.com" class="white">��ĬЦ��</a>��<a href="http://woman.utspeed.com" class="white">Ů������</a>��<a href="http://nuskin.net.ru" class="white">�������Ϲ����̳�</a>  <a href="http://bt.utspeed.com" class="white">��¿/����/emule</a></div>
		<div class="nav-down-right">
		<span>��ǰ����: <b><?=$onlineuser?></b></span></div>
	</div>







</div>


<div class="mainline">&nbsp;</div>
<div class="maincolumn">
		<div class="pagelist">
		<div class="pagelisttitleico">&nbsp;</div>
		<div class="pagelisttitlebg">
		<div class="pagelisttitlename">
���λ�ã�<a href="<?=$homepage?>/" class="classlinkclass">��ҳ</a>&nbsp;>&nbsp;<a href="<?=$phparticleurl?>" class="classlinkclass"><?=$phparticletitle?></a>&nbsp;>&nbsp;<?=$position?>
		</div>
		</div>
		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
					<div class="title1"> 
<b>����̨����</b> <br/>
<a href="index.php?mod=usercp">�ҵĿ���̨</a><br/>
<a href="index.php?mod=favorite&action=view">�ҵ��ղ�</a><br/>
<a href="index.php?mod=myarticle&action=view">�ҵ�����</a><br/>
<a href="index.php?mod=member&action=modpassword">�޸�����</a><br/>
<a href="index.php?mod=member&action=modprofile">�޸�����</a><br/>

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
	  <a href="#">����վ��</a> - <a href="#">������</a> - <a href="#">��ϵ����</a> - <a href="#">��Ȩ��˽</a> - <a href="#">��������</a> - <a href="http://utspeed.com">�������</a> - <a href="http://phparticle.net" target="_blank">����֧��</a> - <a  href="#">��վ��ͼ</a> - <a href="#top">���ض���</a>
	</div>
	<div class="topline">&nbsp;</div>
	<div id="bottom">��Ȩ���У�<a href="<?=$phparticleurl?>"><?=$phparticletitle?></a> 2006 δ����Ȩ��ֹ���ƻ�������<br />

	  <span>Powered by: <a href="http://www.phparticle.net">phpArticle html</a> Version <?=$version?>.</span><br />
	</div>
	</div>
</div> 
</body>
</html>