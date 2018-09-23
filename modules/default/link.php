<?php
require "global.php";
function validate_friendlink($id) {
global $DB,$db_prefix;
$friendlink = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."friendlink WHERE id='$id'");
if (empty($friendlink)) {
pa_exit("���������Ӳ�����");
}
return $friendlink;
}

function show_logo($logo){
if($logo==1){
return "��";
}else{
return "��";
}
}
function show_state($state){
switch($state){
case 0:
return "������";
break;
case 1:
return "��ʾ��";
break;
case 2:
return "�Ѿܾ�";
break;
case 3:
return "�Ѹ�֪";
break;

}
}
cpheader();


/* -=-=-=-=-=-=-=-=-=-=-=-=-
start add friendlink
-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="add") {

$cpforms->formheader(array('title'=>'������������',
'action' => 'index2.php?mod=link',));
$cpforms->makehidden(array('name'=>'action',
'value'=>'insert'));
$cpforms->makeinput(array('text'=>'��������:(��ѡ)',
'name'=>'displayorder',
'size'=>3,
'maxlength'=>3));
$cpforms->makeinput(array('text'=>'վ������:(����)',
'name'=>'sitename',
'maxlength'=>100));
$cpforms->makeinput(array('text'=>'վ��˵��:(��ѡ)<br>������������Ϸ�ʱ��ʾ',
'name'=>'note',
'maxlength'=>200));
$cpforms->makeinput(array('text'=>'վ���ַ:(����)',
'name'=>'siteurl',
'value'=>'http://',
'maxlength'=>100));
$cpforms->makehidden(array('name' => 'mod',
	'value' => "link"));
$cpforms->makeyesno(array('text'=>'�Ƿ���Logo?',
'name'=>'isimg',
'selected'=>0,
));

$cpforms->makeinput(array('text'=>'logo��ַ:(ʹ��logoʱ����)',
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
pa_exit("����ʧ��.վ��������Ϊ��");
}
if($siteurl==""){
pa_exit("����ʧ��.վ���ַ����Ϊ��");
}
if($isimg==1 && $logourl==""){
pa_exit("����ʧ��.ʹ��logoΪ'��'��ʱ��logo��ַ����Ϊ��");
}
$DB->query("INSERT INTO ".$db_prefix."friendlink (displayorder,sitename,note,siteurl,isimg,logourl,visible,editor,jointime)
VALUES ('".intval($displayorder)."','".addslashes($sitename)."','".addslashes($note)."','".addslashes($siteurl)."','$isimg','".addslashes($logourl)."','1','".addslashes($pauserinfo[username])."','".time()."')");
redirect("./index2.php?mod=link&action=cache","���������Ѿ�����,ˢ�»�����,���Ժ�......");
}
/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
start edit(list) friendlink
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="edit") {

$nav = new buildNav;

$total = $DB->fetch_one_array("SELECT count(*) AS count FROM ".$db_prefix."friendlink WHERE visible=1");
$nav->total_result = $total[count];


if ($total[count]==0) {
pa_exit("û���κ���������");
}

$nav->execute("SELECT * FROM ".$db_prefix."friendlink WHERE visible=1 ORDER BY isimg DESC,displayorder ASC");


echo $nav->pagenav();

echo "<table class=\"tableoutline\" boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\">
<tr align=\"center\" class=\"tbhead\">
<td align=center>id#</td>
<td align=center width=\"3%\"> ���� </td>
<td nowrap width=\"15%\"> վ������ </td>
<td width=\"20%\"> վ��˵�� </td>
<td width=\"20%\"> վ���ַ </td>
<td align=center width=\"3%\"> logo </td>
<td nowrap> logo��ַ </td>
<td nowrap width=\"5%\"> ������ </td>
<td nowrap width=\"10%\"> ����ʱ�� </td>
<td nowrap> �༭ </td>
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
[<a href=\"./index2.php?mod=link&action=mod&id=$friendlink[id]\">�༭</a>]
[<a href=\"./index2.php?mod=link&action=kill&id=$friendlink[id]\">ɾ��</a>]
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
$cpforms->formheader(array('title'=>'�༭��������',
'action' => 'index2.php?mod=link',));
$cpforms->makehidden(array('name'=>'action',
'value'=>'update'));
$cpforms->makehidden(array('name'=>'id',
'value'=>$friendlink[id]));
$cpforms->makeinput(array('text'=>'��������:(��ѡ)',
'name'=>'displayorder',
'value'=>$friendlink[displayorder],
'size'=>3,
'maxlength'=>3));
$cpforms->makeinput(array('text'=>'վ������:(����)',
'name'=>'sitename',
'value'=>$friendlink[sitename],
'maxlength'=>100));
$cpforms->makeinput(array('text'=>'վ��˵��:(��ѡ)<br>������������Ϸ�ʱ��ʾ',
'name'=>'note',
'value'=>$friendlink[note],
'maxlength'=>200));
$cpforms->makeinput(array('text'=>'վ���ַ:(����)',
'name'=>'siteurl',
'value'=>$friendlink[siteurl],
'maxlength'=>100));
$cpforms->makehidden(array('name' => 'mod',
	'value' => "link"));
$cpforms->makeyesno(array('text'=>'�Ƿ���Logo?',
'name'=>'isimg',
'selected'=>$friendlink[isimg],
));

$cpforms->makeinput(array('text'=>'logo��ַ:(ʹ��logoʱ����)',
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
pa_exit("����ʧ��.վ��������Ϊ��");
}
if($siteurl==""){
pa_exit("����ʧ��.վ���ַ����Ϊ��");
}
if($isimg==1 && $logourl==""){
pa_exit("����ʧ��.ʹ��logoΪ'��'��ʱ��logo��ַ����Ϊ��");
}
$DB->query("UPDATE ".$db_prefix."friendlink
SET displayorder = '".intval($displayorder)."',
sitename = '".addslashes($sitename)."',
note = '".addslashes($note)."',
siteurl = '".addslashes($siteurl)."',
isimg = '$isimg',
logourl = '".addslashes($logourl)."'
WHERE id=$_POST[id]");
redirect("./index2.php?mod=link&action=cache","���������Ѿ�����,ˢ�»�����,���Ժ�......");
}
/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
start kill friendlink
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="kill"){

$friendlink = validate_friendlink($_GET[id]);

$cpforms->formheader(array('title'=>"ȷ��Ҫɾ������������?",
'action' => 'index2.php?mod=link',));
$cpforms->makehidden(array('name'=>'action',
'value'=>'remove'));
$cpforms->makehidden(array('name'=>'id',
'value'=>$friendlink[id]));
$cpforms->makehidden(array('name' => 'mod',
	'value' => "link"));
$cpforms->formfooter(array('confirm'=>1));

}
/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
start remove friendlink
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_POST[action]=="remove"){

$friendlink = validate_friendlink($_POST[id]);

$DB->query("DELETE FROM ".$db_prefix."friendlink WHERE id='$friendlink[id]'");

redirect("./index2.php?mod=link&action=cache","���������Ѿ�ɾ��,ˢ�»�����,���Ժ�......");

}

/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
start validate friendlink
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="validate") {

$nav = new buildNav;

$total = $DB->fetch_one_array("SELECT count(*) AS count FROM ".$db_prefix."friendlink WHERE visible!=1");
$nav->total_result = $total[count];


if ($total[count]==0) {
pa_exit("û���κ�������������");
}

$nav->execute("SELECT * FROM ".$db_prefix."friendlink WHERE visible!=1 ORDER BY isimg DESC,displayorder ASC");


echo $nav->pagenav();

echo "<table class=\"tableoutline\" boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\">
<tr align=\"center\" class=\"tbhead\">
<td align=center>id#</td>
<td nowrap width=\"15%\"> վ������ </td>
<td width=\"20%\"> վ��˵�� </td>
<td width=\"20%\"> վ���ַ </td>
<td align=center width=\"3%\"> logo </td>
<td nowrap> logo��ַ </td>
<td nowrap width=\"5%\"> ������ </td>
<td nowrap width=\"5%\"> ����ʱ�� </td>
<td nowrap width=\"5%\"> ���� </td>
<td nowrap width=\"5%\"> ״̬ </td>

<td nowrap> �༭ </td>
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
[<a href=\"./index2.php?mod=link&action=viewrequest&id=$friendlink[id]\">�鿴</a>]
</td>
<td nowrap align=center><font color=red>".show_state($friendlink[visible])."</font></td>
<td nowrap>
[<a href=\"./index2.php?mod=link&action=mod&id=$friendlink[id]\">�༭</a>]
[<a href=\"./index2.php?mod=link&action=kill&id=$friendlink[id]\">ɾ��</a>]
[<a href=\"./index2.php?mod=link&action=pass&id=$friendlink[id]\">ͨ��</a>]
[<a href=\"./index2.php?mod=link&action=deny&id=$friendlink[id]\">�ܾ�</a>]
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

$cpforms->formheader(array('title'=>"ȷ��Ҫͨ����������������?",
'action' => 'index2.php?mod=link',));
$cpforms->makehidden(array('name'=>'action',
'value'=>'append'));
$cpforms->makehidden(array('name'=>'id',
'value'=>$friendlink[id]));
$cpforms->makehidden(array('name' => 'mod',
	'value' => "link"));
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

redirect("./index2.php?mod=link&action=cache","��������һ�����ͨ��,ˢ�»�����,���Ժ�......");

}

/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
start deny friendlink
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="deny"){

$friendlink = validate_friendlink($_GET[id]);
if($friendlink[visible]!=0){
pa_exit("ʧ�ܣ��������Ѿ����ӣ������Ѿ��ܾ���ֻ�������е��������ܽ��оܾ�������");
}
$cpforms->formheader(array('title'=>"ȷ��Ҫ�ܾ���������������?",
'action' => 'index2.php?mod=link',));
$cpforms->makehidden(array('name'=>'action',
'value'=>'reject'));
$cpforms->makehidden(array('name'=>'id',
'value'=>$friendlink[id]));
$cpforms->maketextarea(array('text'=>'�ܾ�ԭ��:��ԭ�򽫸�֪�����ˣ��ȴ�����Ļظ���',
'name'=>'reason',
'value'=>'���Ǻܱ�Ǹ��֪ͨ�����������������޷�ͨ���������Ѿ����˸��õ�ѡ��',
'cols'=>70,
'rows'=>7
));
$cpforms->makehidden(array('name' => 'mod',
	'value' => "link"));
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

redirect("./index2.php?mod=link&action=validate","�����������Ѿܾ�");

}

/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
start view request
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */
if ($_GET[action]=="viewrequest"){

$friendlink = validate_friendlink($_GET[id]);
if($friendlink[request]==""){
$request = "û���κ���������.";
}else{
$request = $friendlink[request];
}
echo "<table boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\" class=\"tableoutline\">";
echo "<tr class=".getrowbg().">";
echo "<td align=\"center\" nowrap>".$request."</td>";
echo "</tr>\n";
echo "<tr class=".getrowbg().">";
echo "<td align=\"center\" nowrap><a href=\"./index2.php?mod=link&action=validate\">������������</a></td>";
echo "</tr>\n";
echo "</table>\n";

}
?>

<?
//ˢ�»���
if ($_GET[action]=="cache") {
	$div_friendlink = 5;//�ּ�����ʾ��������,������Ҫ�����޸�;
	//------ͼƬ����--Start----
	$img_links = $DB->query("SELECT * FROM ".$db_prefix."friendlink
	WHERE isimg = 1 AND visible=1
	ORDER BY displayorder");
	if ($DB->num_rows($img_links)>0) {
		$counter = 0;
		$row = 0;
		$tablewidth = floor(100/$div_friendlink);
		while ($img_link = $DB->fetch_array($img_links)){
		if ($counter==0) {
		$img_linkbit .= "<tr bgcolor=\"$bgcolor\" align=\"center\">";
		}
		$img_linkbit .= "<td nowrap valign=\"top\" width=\"$tablewidth%\">";
		$img_linkbit .= "";
		$img_linkbit .= "</td>\n";
			if (++$counter%$div_friendlink==0) {
			$img_linkbit .= "</tr>";
			$counter = 0;
			}
		}
		if ($counter!=0) {
			for (;$counter<$div_friendlink;$counter++) {
			$img_linkbit .= "<td></td>\n";
			}
		}
		$img_link = "
<table width='96%' border='0' cellspacing='0' cellpadding='4'>
   <tr class=showbg> 
    <td colspan='$div_friendlink'>
    </td>
   </tr>
   <tr align='left' class=showbg> 
<!-- BEGIN img_linkbit -->
<td align=left>
<span class='normalfont'>
<a href='$img_link[siteurl]' title='վ������:$img_link[sitename]
վ��˵��:$img_link[note]' target='_blank'>
<img src='$img_link[logourl]' width='88' height='31' border='0'>
</a>
</span>
</td>
<!-- END img_linkbit -->
   </tr>  
</table>
";
	}

	//------ͼƬ����--End----
	//------��������--Start----
	$char_links = $DB->query("SELECT * FROM ".$db_prefix."friendlink
	WHERE isimg = 0 AND visible=1
	ORDER BY displayorder");
	if ($DB->num_rows($char_links)>0) {
		$counter = 0;
		$row = 0;
		$tablewidth = floor(100/$div_friendlink);
		while ($char_link = $DB->fetch_array($char_links)){
			if ($counter==0) {
			$char_linkbit .= "<tr bgcolor=\"$bgcolor\" align=\"center\">";
			}
		$char_linkbit .= "<td nowrap valign=\"top\" width=\"$tablewidth%\">";
		$char_linkbit .= "";
		$char_linkbit .= "</td>\n";
			if (++$counter%$div_friendlink==0) {
			$char_linkbit .= "</tr>";
			$counter = 0;
			}
		}
		if ($counter!=0) {
			for (;$counter<$div_friendlink;$counter++) {
				$char_linkbit .= "<td></td>\n";
			}
		}
		$char_link = "
      <div id='flinks'>
<!-- BEGIN char_linkbit -->
<a href='$char_link[siteurl]' title='վ��˵��:$char_link[note]' target='_blank' rel='external'>$char_link[sitename]</a>
<!-- END char_linkbit -->
	  </div>
";
	}
	//------��������--End----
	$styleid = 1;
	chdir('./../');
        $style = getstyle();
	$friendlink = Array();
	$friendlink[]=$img_link;
	$friendlink[]=$char_link;
	$cache = $DB->fetch_one_array("SELECT * FROM " . $db_prefix . "cache
			WHERE name='friendlink_" . $styleid . "'");
	if (!empty($cache))
		$DB->query("UPDATE " . $db_prefix . "cache SET
			content='" . addslashes(serialize($friendlink)) . "',
			expiry=0
			WHERE name='friendlink_" . $styleid . "'");
	else
		$DB->query("INSERT INTO " . $db_prefix . "cache (name,content,expiry) VALUES
			('friendlink_" . $styleid . "','" . addslashes(serialize($friendlink)) . "',0)");
	redirect("./index2.php?mod=link&action=edit","ˢ�»������!");
}
function getstyle() {
        global $DB, $db_prefix, $configuration, $templatesetid, $styleid,$phparticleurl;

        if (empty($styleid)) {
                if (!empty($_GET['styleid'])) {
                        $styleid = intval($_GET['styleid']);
                        $_SESSION['styleid'] = $styleid;
                } else if (!empty($_SESSION['styleid'])) {
                        $styleid = intval($_SESSION['styleid']);
                } else {
                        require "admin/configs/style.php";
                } 
        }

        $styleinfo = $DB->fetch_one_array("SELECT styleid,replacementsetid,templatesetid FROM " . $db_prefix . "style WHERE styleid='$styleid'");
        if (empty($styleinfo)) {
                $styleinfo = $DB->fetch_one_array("SELECT styleid,replacementsetid,templatesetid FROM " . $db_prefix . "style WHERE styleid='1'");
        } 
        $styleid = $styleinfo['styleid']; 
        // print_rr($styleinfo);
        $templatesetid = $styleinfo['templatesetid'];

        $replacement_file = "admin/configs/replacement_$styleinfo[replacementsetid].php";
        if (!file_exists($replacement_file)) {
                require "admin/configs/replacement_1.php";
        } else {
                require "admin/configs/replacement_$styleinfo[replacementsetid].php";
        } 
        return $style;
}
cpfooter();
?>