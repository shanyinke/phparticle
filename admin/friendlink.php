<?php

error_reporting(7);
require "global.php";

function validate_friendlink($id) {
global $DB,$db_prefix;
$friendlink = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."friendlink WHERE id='$id'");
if (empty($friendlink)) {
pa_exit("该友情链接不存在");
}
return $friendlink;
}

function show_logo($logo){
if($logo==1){
return "是";
}else{
return "否";
}
}
function show_state($state){
switch($state){
case 0:
return "审批中";
break;
case 1:
return "显示中";
break;
case 2:
return "已拒绝";
break;
case 3:
return "已告知";
break;

}
}
cpheader();


/* -=-=-=-=-=-=-=-=-=-=-=-=-
start add friendlink
-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="add") {

$cpforms->formheader(array('title'=>'添加友情链接'));
$cpforms->makehidden(array('name'=>'action',
'value'=>'insert'));
$cpforms->makeinput(array('text'=>'排序数字:(可选)',
'name'=>'displayorder',
'size'=>3,
'maxlength'=>3));
$cpforms->makeinput(array('text'=>'站点名称:(必填)',
'name'=>'sitename',
'maxlength'=>100));
$cpforms->makeinput(array('text'=>'站点说明:(可选)<br>鼠标移至链接上方时显示',
'name'=>'note',
'maxlength'=>200));
$cpforms->makeinput(array('text'=>'站点地址:(必填)',
'name'=>'siteurl',
'value'=>'http://',
'maxlength'=>100));
$cpforms->makeyesno(array('text'=>'是否用Logo?',
'name'=>'isimg',
'selected'=>0,
));

$cpforms->makeinput(array('text'=>'logo地址:(使用logo时必填)',
'name'=>'logourl',
'value'=>'http://',
'maxlength'=>100));

$cpforms->formfooter();

}
/* -=-=-=-=-=-=-=-=-=-=-=-=-
start insert friendlink
-=-=-=-=-=-=-=-=-=-=-=-=- */
if($_POST[action]=="insert"){
$displayorder = htmlspecialchars(trim($_POST[displayorder]));
$sitename = htmlspecialchars(trim($_POST[sitename]));
$note = htmlspecialchars(trim($_POST[note]));
$siteurl = htmlspecialchars(trim($_POST[siteurl]));
$isimg = $_POST[isimg];
$logourl = htmlspecialchars(trim($_POST[logourl]));

if($sitename==""){
pa_exit("添加失败.站点名不能为空");
}
if($siteurl==""){
pa_exit("添加失败.站点地址不能为空");
}
if($isimg==1 && $logourl==""){
pa_exit("添加失败.使用logo为'是'的时候，logo地址不能为空");
}
$DB->query("INSERT INTO ".$db_prefix."friendlink (displayorder,sitename,note,siteurl,isimg,logourl,visible,editor,jointime)
VALUES ('".intval($displayorder)."','".addslashes($sitename)."','".addslashes($note)."','".addslashes($siteurl)."','$isimg','".addslashes($logourl)."','1','".addslashes($pauserinfo[username])."','".time()."')");
redirect("./friendlink.php?action=cache","友情链接已经更新,刷新缓冲中,请稍后......");
}
/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
start edit(list) friendlink
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="edit") {

$nav = new buildNav;

$total = $DB->fetch_one_array("SELECT count(*) AS count FROM ".$db_prefix."friendlink WHERE visible=1");
$nav->total_result = $total[count];


if ($total[count]==0) {
pa_exit("没有任何友情链接");
}

$nav->execute("SELECT * FROM ".$db_prefix."friendlink WHERE visible=1 ORDER BY isimg DESC,displayorder ASC");


echo $nav->pagenav();

echo "<table class=\"tableoutline\" boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\">
<tr align=\"center\" class=\"tbhead\">
<td align=center>id#</td>
<td align=center width=\"3%\"> 排序 </td>
<td nowrap width=\"15%\"> 站点名称 </td>
<td width=\"20%\"> 站点说明 </td>
<td width=\"20%\"> 站点地址 </td>
<td align=center width=\"3%\"> logo </td>
<td nowrap> logo地址 </td>
<td nowrap width=\"5%\"> 添加人 </td>
<td nowrap width=\"10%\"> 加入时间 </td>
<td nowrap> 编辑 </td>
</tr>";

while ($friendlink = $DB->fetch_array($nav->sql_result)) {
//$isimg = ($user[sex]);
if($friendlink[isimg]==1){
$logourl = "<img src=".$friendlink[logourl]." width=\"88\" height=\"31\">";
}else{
$logourl = "";
}
echo "<tr class=".getrowbg().">
<td>$friendlink[id]</td>
<td>$friendlink[displayorder]</td>
<td>$friendlink[sitename]</td>
<td>$friendlink[note]</td>
<td nowrap>$friendlink[siteurl]</td>
<td nowrap>".show_logo($friendlink[isimg])."</td>
<td align=center>$logourl</td>
<td nowrap align=center>$friendlink[editor]</td>
<td nowrap align=center>".date("Y-m-d",$friendlink[jointime])."</td>
<td nowrap>
[<a href=\"./friendlink.php?action=mod&id=$friendlink[id]\">编辑</a>]
[<a href=\"./friendlink.php?action=kill&id=$friendlink[id]\">删除</a>]
</td>
</tr>";
}
echo "</table>";

echo $nav->pagenav();

}
/* -=-=-=-=-=-=-=-=-=-=-=-=-
start modify friendlink
-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="mod") {
$friendlink = validate_friendlink($_GET[id]);
$cpforms->formheader(array('title'=>'编辑友情链接'));
$cpforms->makehidden(array('name'=>'action',
'value'=>'update'));
$cpforms->makehidden(array('name'=>'id',
'value'=>$friendlink[id]));
$cpforms->makeinput(array('text'=>'排序数字:(可选)',
'name'=>'displayorder',
'value'=>$friendlink[displayorder],
'size'=>3,
'maxlength'=>3));
$cpforms->makeinput(array('text'=>'站点名称:(必填)',
'name'=>'sitename',
'value'=>$friendlink[sitename],
'maxlength'=>100));
$cpforms->makeinput(array('text'=>'站点说明:(可选)<br>鼠标移至链接上方时显示',
'name'=>'note',
'value'=>$friendlink[note],
'maxlength'=>200));
$cpforms->makeinput(array('text'=>'站点地址:(必填)',
'name'=>'siteurl',
'value'=>$friendlink[siteurl],
'maxlength'=>100));
$cpforms->makeyesno(array('text'=>'是否用Logo?',
'name'=>'isimg',
'selected'=>$friendlink[isimg],
));

$cpforms->makeinput(array('text'=>'logo地址:(使用logo时必填)',
'name'=>'logourl',
'value'=>$friendlink[logourl],
'maxlength'=>100));

$cpforms->formfooter();

}
/* -=-=-=-=-=-=-=-=-=-=-=-=-
start update friendlink
-=-=-=-=-=-=-=-=-=-=-=-=- */
if($_POST[action]=="update"){

$displayorder = htmlspecialchars(trim($_POST[displayorder]));
$sitename = htmlspecialchars(trim($_POST[sitename]));
$note = htmlspecialchars(trim($_POST[note]));
$siteurl = htmlspecialchars(trim($_POST[siteurl]));
$isimg = intval($_POST[isimg]);
$logourl = htmlspecialchars(trim($_POST[logourl]));

if($sitename==""){
pa_exit("更新失败.站点名不能为空");
}
if($siteurl==""){
pa_exit("更新失败.站点地址不能为空");
}
if($isimg==1 && $logourl==""){
pa_exit("更新失败.使用logo为'是'的时候，logo地址不能为空");
}
$DB->query("UPDATE ".$db_prefix."friendlink
SET displayorder = '".intval($displayorder)."',
sitename = '".addslashes($sitename)."',
note = '".addslashes($note)."',
siteurl = '".addslashes($siteurl)."',
isimg = '$isimg',
logourl = '".addslashes($logourl)."'
WHERE id=$_POST[id]");
redirect("./friendlink.php?action=cache","友情链接已经更新,刷新缓冲中,请稍后......");
}
/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
start kill friendlink
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="kill"){

$friendlink = validate_friendlink($_GET[id]);

$cpforms->formheader(array('title'=>"确定要删除该友情链接?"));
$cpforms->makehidden(array('name'=>'action',
'value'=>'remove'));
$cpforms->makehidden(array('name'=>'id',
'value'=>$friendlink[id]));

$cpforms->formfooter(array('confirm'=>1));

}
/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
start remove friendlink
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_POST[action]=="remove"){

$friendlink = validate_friendlink($_POST[id]);

$DB->query("DELETE FROM ".$db_prefix."friendlink WHERE id='$friendlink[id]'");

redirect("./friendlink.php?action=cache","友情链接已经删除,刷新缓冲中,请稍后......");

}

/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
start validate friendlink
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="validate") {

$nav = new buildNav;

$total = $DB->fetch_one_array("SELECT count(*) AS count FROM ".$db_prefix."friendlink WHERE visible!=1");
$nav->total_result = $total[count];


if ($total[count]==0) {
pa_exit("没有任何友情链接申请");
}

$nav->execute("SELECT * FROM ".$db_prefix."friendlink WHERE visible!=1 ORDER BY isimg DESC,displayorder ASC");


echo $nav->pagenav();

echo "<table class=\"tableoutline\" boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\">
<tr align=\"center\" class=\"tbhead\">
<td align=center>id#</td>
<td nowrap width=\"15%\"> 站点名称 </td>
<td width=\"20%\"> 站点说明 </td>
<td width=\"20%\"> 站点地址 </td>
<td align=center width=\"3%\"> logo </td>
<td nowrap> logo地址 </td>
<td nowrap width=\"5%\"> 申请者 </td>
<td nowrap width=\"5%\"> 加入时间 </td>
<td nowrap width=\"5%\"> 理由 </td>
<td nowrap width=\"5%\"> 状态 </td>

<td nowrap> 编辑 </td>
</tr>";

while ($friendlink = $DB->fetch_array($nav->sql_result)) {
//$isimg = ($user[sex]);
if($friendlink[isimg]==1){
$logourl = "<img src=".$friendlink[logourl]." width=\"88\" height=\"31\">";
}else{
$logourl = "";
}
echo "<tr class=".getrowbg().">
<td>$friendlink[id]</td>
<td>$friendlink[sitename]</td>
<td>$friendlink[note]</td>
<td nowrap>$friendlink[siteurl]</td>
<td nowrap>".show_logo($friendlink[isimg])."</td>
<td align=center>$logourl</td>
<td nowrap align=center>$friendlink[editor]</td>
<td nowrap align=center>".date("Y-m-d",$friendlink[jointime])."</td>
<td nowrap align=center>
[<a href=\"./friendlink.php?action=viewrequest&id=$friendlink[id]\">查看</a>]
</td>
<td nowrap align=center><font color=red>".show_state($friendlink[visible])."</font></td>
<td nowrap>
[<a href=\"./friendlink.php?action=mod&id=$friendlink[id]\">编辑</a>]
[<a href=\"./friendlink.php?action=kill&id=$friendlink[id]\">删除</a>]
[<a href=\"./friendlink.php?action=pass&id=$friendlink[id]\">通过</a>]
[<a href=\"./friendlink.php?action=deny&id=$friendlink[id]\">拒绝</a>]
</td>
</tr>";
}
echo "</table>";

echo $nav->pagenav();

}
/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
start pass friendlink
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="pass"){

$friendlink = validate_friendlink($_GET[id]);

$cpforms->formheader(array('title'=>"确定要通过该友情链接申请?"));
$cpforms->makehidden(array('name'=>'action',
'value'=>'append'));
$cpforms->makehidden(array('name'=>'id',
'value'=>$friendlink[id]));

$cpforms->formfooter(array('confirm'=>1));

}
/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
start append friendlink
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_POST[action]=="append"){

$friendlink = validate_friendlink($_POST[id]);

$DB->query("UPDATE ".$db_prefix."friendlink
SET visible = '1'
WHERE id='$friendlink[id]'");

redirect("./friendlink.php?action=cache","友情链接一精审核通过,刷新缓冲中,请稍后......");

}

/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
start deny friendlink
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="deny"){

$friendlink = validate_friendlink($_GET[id]);
if($friendlink[visible]!=0){
pa_exit("失败，此友链已经添加，或者已经拒绝。只有审批中的友链才能进行拒绝操作。");
}
$cpforms->formheader(array('title'=>"确定要拒绝该友情链接申请?"));
$cpforms->makehidden(array('name'=>'action',
'value'=>'reject'));
$cpforms->makehidden(array('name'=>'id',
'value'=>$friendlink[id]));
$cpforms->maketextarea(array('text'=>'拒绝原因:此原因将告知申请人，等待其查阅回复。',
'name'=>'reason',
'value'=>'我们很抱歉地通知您，您的友链申请无法通过，我们已经有了更好的选择。',
'cols'=>70,
'rows'=>7
));
$cpforms->formfooter(array('confirm'=>1));

}
/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
start reject friendlink
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_POST[action]=="reject"){
$reason = htmlspecialchars(trim($_POST[reason]));
$friendlink = validate_friendlink($_POST[id]);

$DB->query("UPDATE ".$db_prefix."friendlink
SET visible = '2',
reason = '$reason'
WHERE id='$friendlink[id]'");

redirect("./friendlink.php?action=validate","该友情链接已拒绝");

}

/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
start view request
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="viewrequest"){

$friendlink = validate_friendlink($_GET[id]);
if($friendlink[request]==""){
$request = "没有任何申请理由.";
}else{
$request = $friendlink[request];
}
echo "<table boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\" class=\"tableoutline\">";
echo "<tr class=".getrowbg().">";
echo "<td align=\"center\" nowrap>".$request."</td>";
echo "</tr>\n";
echo "<tr class=".getrowbg().">";
echo "<td align=\"center\" nowrap><a href=\"./friendlink.php?action=validate\">返回审批友链</a></td>";
echo "</tr>\n";
echo "</table>\n";

}
//刷新缓冲
if ($_GET[action]=="cache") {
	$div_friendlink = 5;//分几列显示友情链接,根据需要自行修改;
	//------图片链接--Start----
	$img_links = $DB->query("SELECT * FROM ".$db_prefix."friendlink
	WHERE isimg = 1 AND visible=1
	ORDER BY displayorder");
	if ($DB->num_rows($img_links)>0) {
		$counter = 0;
		$row = 0;
		$tablewidth = floor(100/$div_friendlink);
		while ($img_link = $DB->fetch_array($img_links)){
		if ($counter==0) {
		$img_linkbit .= "<tr bgcolor='$bgcolor' align='center'>";
		}
		$img_linkbit .= "<td nowrap valign='top' width='$tablewidth%'>";
		$img_linkbit .= "{T_HTML_BLOCK img_linkbit/}";
		$img_linkbit .= "</td>n";
			if (++$counter%$div_friendlink==0) {
			$img_linkbit .= "</tr>";
			$counter = 0;
			}
		}
		if ($counter!=0) {
			for (;$counter<$div_friendlink;$counter++) {
			$img_linkbit .= "<td></td>n";
			}
		}
		$img_link = "{T_HTML_BLOCK img_link/}";
	}

	//------图片链接--End----
	//------文字链接--Start----
	$char_links = $DB->query("SELECT * FROM ".$db_prefix."friendlink
	WHERE isimg = 0 AND visible=1
	ORDER BY displayorder");
	if ($DB->num_rows($char_links)>0) {
		$counter = 0;
		$row = 0;
		$tablewidth = floor(100/$div_friendlink);
		while ($char_link = $DB->fetch_array($char_links)){
			if ($counter==0) {
			$char_linkbit .= "<tr bgcolor='$bgcolor' align='center'>";
			}
		$char_linkbit .= "<td nowrap valign='top' width='$tablewidth%'>";
		$char_linkbit .= "{T_HTML_BLOCK char_linkbit/}";
		$char_linkbit .= "</td>n";
			if (++$counter%$div_friendlink==0) {
			$char_linkbit .= "</tr>";
			$counter = 0;
			}
		}
		if ($counter!=0) {
			for (;$counter<$div_friendlink;$counter++) {
				$char_linkbit .= "<td></td>n";
			}
		}
		$char_link = "{T_HTML_BLOCK char_link/}";
	}
	//------文字链接--End----
	$styleid = 1;
        $style = getstyle();
	$friendlink = Array();
	$friendlink[]=$img_link;
	$friendlink[]=$char_link;
	$DB->query("INSERT INTO " . $db_prefix . "cache (name,content,expiry) VALUES
			('friendlink_" . $styleid . "','" . addslashes(serialize($friendlink)) . "',0)");
	redirect("./friendlink.php?action=edit","刷新缓冲完毕!");
}
cpfooter();
?>
