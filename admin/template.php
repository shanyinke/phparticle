<?php
/*
* 模板管理文件-多模版套系管理
*/
error_reporting(7);
require "global.php";

cpheader();
//检查模版id
function validate_templatesetid($templatesetid) {

	global $DB,$db_prefix;

	$templatesetid = intval($templatesetid);

	$templateset = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."templateset WHERE templatesetid='$templatesetid'");
	if (empty($templateset)) {
		pa_exit("该模版套系不存在");
	}
	return $templateset;
}
//删除目录
function remove_directory($dir) {
	if ($handle = opendir("$dir")) {
	while (false !== ($item = readdir($handle))) {
		if ($item != "." && $item != "..") {
			if (is_dir("$dir/$item")) {
				remove_directory("$dir/$item");
			} else {
				unlink("$dir/$item");
				echo " removing $dir/$item<br>\n";
			}
		}
	}
	closedir($handle);
	rmdir($dir);
	echo "removing $dir<br>\n";
	}
}
// add template
if ($action=="add")  {
?>
<script type="text/javascript">
<!--
function displayHTML(form) {
		 var inf = form.template.value;
		 win = window.open(", ", 'popup', 'toolbar = no, status = yes');
		 win.document.write("" + inf + "");
}
-->
</script>
<?php
	$cpforms->formheader(array('title'=>'添加新模板'));

	$cpforms->makehidden(array('name'=>'action',
								'value'=>'insert'));

	$cpforms->makeinput(array('text'=>'模板名:',
								'name'=>'title'));
	$cpforms->gettemplatesets(array('text'=>'模板套系:',
								'name'=>'templatesetid',
								'selected'=>$templatesetid));

	$cpforms->maketextarea(array('text'=>'模板内容:',
								'name'=>'template',
								'cols'=>100,
								'rows'=>30));

	$cpforms->formfooter(array('button'=>array('submit'=>
								array('value'=>'提交'),
									'reset'=> array('value'=>'预览',
									'type'=>'button',
									'extra'=>'onclick="displayHTML(this.form)"'))));

}
//截取字符串指定字符前的字符串
function gettemplategroupname($title = "") {

	$title = trim($title);
	$posit = strpos($title,"_");

	if(empty($posit)) {
		$templategroupname = $title;
	} else {
		$templategroupname = substr($title,0,$posit);
	}
	return $templategroupname;

}
/*
if ($_POST[action]=="insert"){

	$title = trim($_POST[title]);
	$template = $_POST[template];

	if (!pa_isset($title)) {
		pa_exit("模板标题不能为空");
	}
	$checktemplate = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."template WHERE templatesetid='$templatesetid' AND title='".addslashes($title)."'");
	if (empty($checktemplate)) {
		$DB->query("INSERT INTO ".$db_prefix."template (title,templatesetid,template)
							VALUES ('".addslashes($title)."','".addslashes($templatesetid)."','".addslashes($template)."')");
	} else {
		pa_exit("该模板已存在");
	}

	$templategroupname = gettemplategroupname($title);

	cleancache();

	redirect("./template.php?expand=1&templatesetid=$templatesetid&templategroupname=$templategroupname#$templategroupname","该模板已添加!");

}
*/
function readfromfile($file_name) {
	$filenum=@fopen($file_name,"r");
	flock($filenum,LOCK_SH);
	$fsize=filesize($file_name);
	if(!empty($fsize)){
		$content=fread($filenum,$fsize);
	}
	fclose($filenum);
	return $content;
}
function writetofile($file_name, $data, $method = "w") {
		if ($data != "") {
				$filenum = fopen($file_name, $method);
				flock($filenum, LOCK_EX);
				$file_data = fwrite($filenum, $data);
				fclose($filenum);
				return $file_data;
		} else {
				$fp = fopen($file_name, "w");
				fclose($fp);
		} 
}
//================================
//			 修改模版
//================================
if ($action=="mod")  {

	$title = trim($_GET[title]);

	$templateset = validate_templatesetid($_GET[templatesetid]);

	$template['template']=readfromfile("../templates/".$templateset[title]."/html/".$title.".htm");

	if (empty($template)) {
		pa_exit("该模板不存在");
	}

?>
<script type="text/javascript">
<!--
function displayHTML(form) {
		 var inf = form.template.value;
		 win = window.open(", ", 'popup', 'toolbar = no, status = yes');
		 win.document.write("" + inf + "");
}
-->
</script>
<?php

	$cpforms->formheader(array('title'=>'编辑模板'));
	$cpforms->makehidden(array('name'=>'action',
								'value'=>'update'));
	$cpforms->makeinput(array('text'=>'模板名:',
								'name'=>'title',
								'value'=>$title));
	$cpforms->makehidden(array('name'=>'oldtitle',
								'value'=>$title));

	$cpforms->makehidden(array('name'=>'oldtemplatesetid',
								'value'=>$templatesetid));

	$cpforms->gettemplatesets(array('text'=>'模板套系:',
									'name'=>'templatesetid',
									'selected'=>$templatesetid));

	$cpforms->maketextarea(array('text'=>"模板内容:<p><a href=\"template.php?action=viewdefault&templatetitle=$title\" target=\"_blank\">查看默认模板</a></p>",
									'name'=>'template',
									'value'=>$template[template],
									'cols'=>'100',
									'rows'=>'30',
									'html'=>1
								  ));

	//makehidden(templategroupname,$templategroupname);
	$cpforms->formfooter(array('button'=> array('submit'=>
							 array('value'=>'提交'),
							 'reset'=>array('value'=>'预览',
							 'type'=>'button',
							 'extra'=>'onclick="displayHTML(this.form)"'))));

}
//--------------------------------
//		修改和新建升级数据库
//--------------------------------
if ($_POST[action]=="update"||$_POST[action]=="insert"){

	$title = trim($_POST[title]);
	$template = $_POST[template];
	$templateset = validate_templatesetid($_POST[templatesetid]);
	if (!pa_isset($title)) {
		pa_exit("模板标题不能为空");
	}
	//print_rr($_POST);
	//exit;
	###检查文件是否存在(action=insert)###
	if(file_exists("../templates/".$templateset[title]."/html/".$title.".htm"))
	{
		$checktemplate=1;
	}
	clearstatcache();
	if (!empty($checktemplate)&&$_POST[action]=="insert") {
		pa_exit("该模板已存在.");
	}
	if($_POST['oldtitle']) unlink("../templates/".$templateset[title]."/html/".$_POST['oldtitle'].".htm");
	writetofile("../templates/".$templateset[title]."/html/".$title.".htm",$template);
	###目录按名称归类###
	$templategroupname = gettemplategroupname($title);
	if($templategroupname=="sorthome")
		$templategroupname="mksort";
	elseif($templategroupname=="articlehome")
		$templategroupname="mkarticle";
	if($templategroupname=="header"||$templategroupname=="footer")
		$renewtemplatescript = "make.php";
	else $renewtemplatescript = "make.php?mod=".$templategroupname;
	?><iframe style="HEIGHT: 0px; LEFT: 0px;" src="<?=$renewtemplatescript?>"></iframe><?
	resetcache();
	redirect("./template.php?expand=1&templatesetid=$templatesetid&templategroupname=$templategroupname#$templategroupname","该模板正在更新,稍等...");

}

/*================================
//	  恢复默认模版功能
//================================
if ($action=="restore"){
	$cpforms->formheader(array('title'=>'确实要恢复该模板?'));
	$cpforms->makehidden(array('name'=>'action',
								'value'=>'dorestore'));
	$cpforms->makehidden(array('name'=>'templatesetid',
								'value'=>$templatesetid));
	$cpforms->makehidden(array('name'=>'title',
								'value'=>$title));
	$cpforms->formfooter(array('confirm'=>1));
}

if ($_POST[action]=="dorestore") {
	$DB->query("DELETE FROM ".$db_prefix."template WHERE title='".addslashes(trim($title))."' AND templatesetid='".addslashes($templatesetid)."'");
	cleancache();
	redirect("./template.php?expand=1&templatesetid=$templatesetid","该模板已恢复");
}
*/

//================================
//		 删除单独模版
//================================
if ($action=="kill"){

	$cpforms->formheader(array('title'=>'确实要删除该模板?'));
	$cpforms->makehidden(array('name'=>'action',
								'value'=>'remove'));
	$cpforms->makehidden(array('name'=>'templatesetid',
								'value'=>$templatesetid));
	$cpforms->makehidden(array('name'=>'title',
								'value'=>$title));
	$cpforms->formfooter(array('confirm'=>1));

}
//--------------------------------
//		删除单独模版数据
//--------------------------------
if ($action=="remove"){
	$title = trim($_POST[title]);
	$templateset = validate_templatesetid($_POST[templatesetid]);
	if (!pa_isset($title)) {
		pa_exit("模板标题不能为空");
	}
	if(file_exists("../templates/".$templateset[title]."/html/".$title.".htm"))
	{
		$checktemplate=1;
	}
	clearstatcache();
	if (empty($checktemplate)) {
		pa_exit("该模板不存在.");
	}else
		unlink("../templates/".$templateset[title]."/html/".$_POST['title'].".htm");

	//$DB->query("DELETE FROM ".$db_prefix."template WHERE title='".addslashes(trim($title))."' AND templatesetid='".addslashes($templatesetid)."'");
	cleancache();
	redirect("./template.php?expand=1&templatesetid=$templatesetid","该模板已删除");
}
//================================
//		 增加模版套系
//================================
if($action==addset){

	$cpforms->formheader(array('title'=>'添加模板套系'));
	$cpforms->makehidden(array('name'=>'action',
								'value'=>'insertset'));
	$cpforms->makeinput(array('text'=>'模板套系名:',
								'name'=>'title'));
	$cpforms->formfooter();

}
//--------------------------------
//		增加模版套系数据
//--------------------------------
if($_POST[action]=="insertset"){

	$title = trim($title);
	if ($title=="") {
		pa_exit("模板套系名不能为空");
	}
	$DB->query("INSERT INTO ".$db_prefix."templateset (title) VALUES ('".addslashes($title)."')");

	redirect("./template.php?action=edit","该套系已添加");

}
//================================
//		删除模版套系
//================================
if ($action=="killset"){

	$cpforms->formheader(array('title'=>'确实要删除该模板套系?'));
	$cpforms->makehidden(array('name'=>'action',
								'value'=>'removeset'));
	$cpforms->makehidden(array('name'=>'templatesetid',
								'value'=>$templatesetid));

	$cpforms->formfooter(array('confirm'=>1));

}
//--------------------------------
//		删除模版套系数据
//--------------------------------
if ($_POST[action]=="removeset"){

	if ($templatesetid==-1) {
		pa_exit("你不能删除默认模板套系");
	} else {
		//$DB->query("DELETE FROM ".$db_prefix."template WHERE templatesetid='$templatesetid'");

		$templateset = validate_templatesetid($templatesetid);
		remove_directory("../templates/".$templateset[title]);
		$DB->query("DELETE FROM ".$db_prefix."templateset WHERE templatesetid='$templatesetid'");
		cleancache();

		redirect("./template.php?action=edit","该模板套系已删除");
	}

}

//================================
//        模板套系列表
//================================
if ($action=="edit" OR $expand=="0")  {

	$cpforms->tableheader();
	echo "<tr class=\"tbhead\"><td colspan=\"2\">模板套系列表</td></tr>\n";

	if ($debug==1) {
		echo "<tr class=".getrowbg().">
				  <td>Global Templates</td>
				  <td>[<a href=template.php?expand=1&templatesetid=-1>展开</a>] [<a href=template.php?action=add&seltemplatesetid=-1>添加模板</a>]</td>
			  </tr>";
	}
	$templatesetlists = $DB->query("SELECT templatesetid,title FROM ".$db_prefix."templateset ORDER BY templatesetid");
	while ($templatesetlist = $DB->fetch_array($templatesetlists)) {
		echo "<tr class=".getrowbg().">
		<td>$templatesetlist[title]</td>
		<td>[<a href=template.php?expand=1&expand=1&templatesetid=$templatesetlist[templatesetid]>展开</a>] [<a href=template.php?action=add&expand=1&templatesetid=$templatesetlist[templatesetid]>添加自定义模板</a>]";

		if($templatesetlist[templatesetid]!=1){
			echo " [<a href=template.php?action=killset&templatesetid=$templatesetlist[templatesetid]>删除</a>]</td></tr>";
		}
		echo "</td></tr>\n";
	}
	$cpforms->tablefooter();
}

//================================
//		模版展开列表
//================================

if ($expand=="1" and isset($templatesetid)){
?>

<SCRIPT language=JavaScript type=text/javascript>
<!--
function ToggleNode(nodeObject, imgObject){
	if (nodeObject.style.display == '' || nodeObject.style.display == 'inline') {
		nodeObject.style.display = 'none';
		imgObject.src = '../images/collapse.gif';
	} else {
		nodeObject.style.display = 'inline';
		imgObject.src = '../images/expand.gif';
	}
}
-->
</script>

<?php
	echo "<p><b>模板名为<font color=green>绿色</font>:默认模板,<font color=blue>蓝色</font>:编辑过的模板,<font color=orange>橙色</font>:自定义模板.</b></p>";

	if ($templatesetid==-1){
		$templateset[title]="Global Templates";
		$ttemplateset[title]="default";
	}else{
	$ttemplateset = validate_templatesetid($_GET[templatesetid]);}

	echo "<table boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\">
		<tr>
			<td><b>$templateset[title]</b>[<a href=template.php?expand=0>折叠</a>] [<a href=template.php?action=add&templatesetid=$templatesetid>添加自定义模板</a>]</td>
			<td align=\"right\"><a href=\"template.php?expand=1&templatesetid=$templatesetid&expandall=0\">收缩所有模板</a> <a href=\"template.php?expand=1&templatesetid=$templatesetid&expandall=1\">展开所有模板</a></td>
		</tr>
		<tr class=tbcat><td colspan=2>模板列表</td></tr>
	</table>";

	echo "<table boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\" class=\"tableoutline\">";

	unset($cachetemplates);
	chdir("../templates/".$ttemplateset[title]);
	$handle=opendir("html");
	while ($file = readdir($handle))
	{
		if (is_file("html/".$file))
		{
			$mod = substr($file,0,strpos($file,"."));
			$template['title'] = $mod;
			$posit = strpos($mod,"_");
			if(empty($posit)) {
				$cachetemplates[$mod][] = $template;
			} else {
				$cachetemplates[substr($mod,0,$posit)][] = $template;
			}
		}else{
			continue;
		}
	}
	closedir($handle);

	unset($template);
	asort ($cachetemplates);
	reset ($cachetemplates);
	foreach ($cachetemplates AS $key=>$value) {
		$gc = count($cachetemplates[$key]);
		if ($gc>1) {
			if ($expandall) {
				$img = "expand";
			} else {
				$img = "collapse";
			}
			echo "<tr class=tbcat style=\"cursor: hand\" onClick=\"ToggleNode(templategroup_tr_$counter,templategroup_img_$counter)\">
			<td nowrap valign=\"top\"><img src=\"../images/$img.gif\" align=\"absmiddle\" id=\"templategroup_img_$counter\"><b>$key</b></td></tr>";
		}

		if ($gc>1) {
			if ($templategroupname == $key) {
				$display = "inline";
			} else {
				if ($expandall) {
					$display = "inline";
				} else {
					$display = "none";
				}
			}
			echo "<tr class=".getrowbg()." id=\"templategroup_tr_$counter\" style=\"display: $display\"><td><table width=\"97%\"  align=\"right\">";
		} else {
			echo "<tr class=".getrowbg()." id=\"templategroup_tr_$counter\"><td><table width=\"100%\">";
		}
		$counter++;
			//  style=\"display: none\"

		foreach ($cachetemplates[$key] AS $k=>$templatelist){

			if (strpos(" $templatelist[title]","$key")==1) {
				echo "<tr>
				<td><a name=\"$templatelist[title]\"></a>";
				if ($templatelist[title2]=="") { //默认,green

					echo "<font class=normalfont color=green>$templatelist[title]</font>";

					echo "</td><td align=\"right\">";
					echo " [<a href=template.php?action=mod&templatesetid=$templatesetid&title=$templatelist[title]>编辑</a>]";

					//if ($debug==1 AND $templatesetid==-1){
						 echo " [<a href=template.php?action=kill&templatesetid=$templatesetid&title=$templatelist[title]>删除</a>]";
					//}

				} else {//编辑过的,blue
					echo "<font class=normalfont color=blue>$templatelist[title]</font>";
					echo "</td><td align=\"right\">";

					echo " [<a href=template.php?action=mod&templatesetid=$templatesetid&title=$templatelist[title]>编辑</a>]";

					if ($debug==1 AND $templatesetid==-1) {
						 echo " [<a href=template.php?action=kill&templatesetid=$templatesetid&title=$templatelist[title]>删除</a>]";
					} else {
						echo " [<a href=template.php?action=restore&templatesetid=$templatesetid&title=$templatelist[title]>还原</a>]";
					}
					echo " [<a href=template.php?action=viewdefault&templatetitle=$templatelist[title] target=\"_blank\">查看默认</a>]";
					//echo "<td>";
				}
					echo "</td>
						</tr>";
			}
		}
		echo "</td></tr></table>";
		echo "</td></tr>";
	}
	echo "</table>";
} //end if


if ($_GET[action]=="viewdefault") {

	if (empty($templatetitle)) {
		pn_exit("该默认模板不存在");
	}

	$template = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."template
												WHERE title='".addslashes($templatetitle)."' AND templatesetid=-1");
	if (empty($template)) {
		pa_exit("该模板不存在");
	}
	$cpforms->tableheader();
	$cpforms->makecategory(array('title'=>"默认模板: $template[title]"));
	$cpforms->maketextarea(array('text'=>'模板',
								 'value'=>$template[template],
								 'cols'=>100,
								 'rows'=>25,
								 'html'=>1
								  ));
	$cpforms->tablefooter();

}


if ($_GET[action]=="search") {

	$cpforms->formheader(array('title'=>'搜索模板'));
	$cpforms->gettemplatesets(array('text'=>'模板套系:',
									 'name'=>'templatesetid'));
	$cpforms->makeinput(array('text'=>'模板名:',
								'name'=>'title'));
	$cpforms->makeinput(array('text'=>'模板内容:',
								'name'=>'content'));
	$cpforms->makehidden(array('name'=>'action','value'=>'dosearch'));
	$cpforms->formfooter();

}

if ($_POST[action]=="dosearch") {
?>
<SCRIPT language=JavaScript type=text/javascript>
<!--
function ToggleNode(nodeObject, imgObject){
		 if (nodeObject.style.display == '' || nodeObject.style.display == 'inline') {
			 nodeObject.style.display = 'none';
			 imgObject.src = '../images/collapse.gif';
		 } else {
			 nodeObject.style.display = 'inline';
			 imgObject.src = '../images/expand.gif';
		 }
}
-->
</script>
<?php

	$title = trim($_POST[title]);
	$content = trim($_POST[content]);

	if (!pa_isset($title) AND !pa_isset($content)) {
		pa_exit("仍未输入任何要搜索的关键字");
	}
	echo "<p><b>模板名为<font color=green>绿色</font>:默认模板,<font color=blue>蓝色</font>:编辑过的模板,<font color=orange>橙色</font>:自定义模板.</b></p>";

	echo "<table boder=\"0\" width=\"100%\" cellpadding=\"4\" cellspacing=\"1\" class=\"tableoutline\">";

	$seachresults=Array();
	chdir("../templates/default");
	$handle=opendir("html");
	while ($file = readdir($handle)) 
	{
		if (is_file("html/".$file))
		{
			if (pa_isset($title))
				if(strstr($file,$title))$seachresults[]=substr($file,0,strpos($file,"."));
			if (pa_isset($content))
			{
				$filedata = readfromfile("html/".$file);
				if(strstr($filedata,$content))$seachresults[]=substr($file,0,strpos($file,"."));
			}
		}else{
			continue;
		}
	}
	closedir($handle);
	$templatecount=count($seachresults);
	if ($templatecount==0) {
		pa_exit("找不到任何匹配的模板");
	}

	echo "<p>共找到 $templatecount 个匹配的模板</p>";


	unset($cachetemplates);
	foreach($seachresults AS $mod)
	{
		$template['title'] = $mod;
		$posit = strpos($mod,"_");
		if(empty($posit)) {
			$cachetemplates[$mod][] = $template;
		} else {
			$cachetemplates[substr($mod,0,$posit)][] = $template;
		}

	}

	unset($template);


	foreach ($cachetemplates AS $key=>$value) {
			 $gc = count($cachetemplates[$key]);
			 if ($gc>1) {
				 if ($expandall) {
					 $img = "expand";
				 } else {
					 $img = "collapse";
				 }
				 echo "<tr class=tbcat style=\"cursor: hand\" onClick=\"ToggleNode(templategroup_tr_$counter,templategroup_img_$counter)\">
						<td nowrap valign=\"top\"><img src=\"../images/$img.gif\" align=\"absmiddle\" id=\"templategroup_img_$counter\"><b>$key</b></td></tr>";
			 }

			 if ($gc>1) {
				 if ($templategroupname == $key) {
					 $display = "inline";
				 } else {
					 if ($expandall) {
						 $display = "inline";
					 } else {
						 $display = "none";
					 }
				 }
				 echo "<tr class=".getrowbg()." id=\"templategroup_tr_$counter\" style=\"display: $display\"><td><table width=\"97%\"  align=\"right\">";
			 } else {
				 echo "<tr class=".getrowbg()." id=\"templategroup_tr_$counter\"><td><table width=\"100%\">";
			 }
			 $counter++;
					//  style=\"display: none\"

			 foreach ($cachetemplates[$key] AS $k=>$templatelist){

					  if (strpos(" $templatelist[title]",$key)==1) {
							  echo "<tr>
									 <td><a name=\"$templatelist[title]\"></a>";
						  if ($templatelist[title2]=="") { //默认,green

							  echo "<font class=normalfont color=green>$templatelist[title]</font>";

							  echo "</td><td align=\"right\">";
							  echo " [<a href=template.php?action=mod&templatesetid=$templatesetid&title=$templatelist[title]>编辑</a>]";

							  if ($debug==1 AND $templatesetid==-1){
								  echo " [<a href=template.php?action=kill&templatesetid=$templatesetid&title=$templatelist[title]>删除</a>]";
							  }

							} else {//编辑过的,blue
							  echo "<font class=normalfont color=blue>$templatelist[title]</font>";
							  echo "</td><td align=\"right\">";

							  echo " [<a href=template.php?action=mod&templatesetid=$templatesetid&title=$templatelist[title]>编辑</a>]";

							  if ($debug==1 AND $templatesetid==-1) {
								  echo " [<a href=template.php?action=kill&templatesetid=$templatesetid&title=$templatelist[title]>删除</a>]";
							  } else {
								  echo " [<a href=template.php?action=restore&templatesetid=$templatesetid&title=$templatelist[title]>还原</a>]";
							  }
							  //echo "<td>";
						  }
							  echo "</td>
									</tr>";
					  }

			 }
			 echo "</td></tr></table>";
			 echo "</td></tr>";

	}
	echo "</table>";

}

if ($_GET[action]=="replace") {

	$cpforms->formheader(array('title'=>'查找并替换模板内容'));
	$cpforms->gettemplatesets(array('text'=>'模板套系:',
									 'name'=>'templatesetid'));
	$cpforms->makeinput(array('text'=>'查找内容:',
								'name'=>'findword',
								'maxlength'=>100));
	$cpforms->makeinput(array('text'=>'替换为:',
								'name'=>'replaceword',
								'maxlength'=>100));
	$cpforms->makehidden(array('name'=>'action','value'=>'doreplace'));
	$cpforms->formfooter();

}

if ($_POST[action]=="doreplace") {

	//print_rr($_POST);
	$findword = $_POST[findword];
	if (!pa_isset(trim($findword))) {
		pa_exit("查找的内容不能为空");
	}
	$replaceword = $_POST[replaceword];
	$templatesetid = intval($_POST[templatesetid]);

	$seachresults=Array();
	$templatecount=0;
	chdir("../templates/default");
	$handle=opendir("html");
	while ($file = readdir($handle)) 
	{
		if (is_file("html/".$file))
		{
			if (pa_isset($findword))
			{
				$filedata = readfromfile("html/".$file);
				if(strstr($filedata,$findword))
				{
					$tname=substr($file,0,strpos($file,"."));
					writetofile("html/".$file,str_replace($findword,$replaceword,$filedata));
					echo "正在替换: ".$tname."<br>";
					$templatecount++;
				}
			}
		}else{
			continue;
		}
	}
	closedir($handle);
	if ($templatecount<1) {
		pa_exit("找不到任何匹配的模板");
	}
	
	echo "<p>共更新了 $templatecount 个模板</p>";

}

cpfooter();

?>