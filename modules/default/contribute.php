<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<?php
if (!isset($_POST[action]) AND trim($_POST[action]=="")) {
    $action = $_GET[action];
} else {
    $action = $_POST[action];
}

if (empty($action)) {
    $action = "submit";
}

if($pauserinfo['onedaypostmax']>0)
{
	$articles = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."article
                                              WHERE userid='$pauserinfo[userid]' AND date>".(time()-86400));
	if($articles['count']>=$pauserinfo['onedaypostmax'])
		$errormessage="error_contribute_articles_exceedlimit";
		include("modules/default/error.php");
}
?>

<?
if ($action=="submit") {
    $gzipoutput = 0;
    $templatelist = "navbar_contribute,contribute_submit";

    if (!$pauserinfo[cancontribute] AND !$pauserinfo[isadmin]) {//$pauserinfo[userid]==0 OR (
        $reasons=array("nopermission_reason_contribute_permission_denied");
        include("modules/default/nopermission.php");
    }

    cachesorts();
    $sortlist = makesortlist();

    $sortlistoptions = sortsbit();
}
?>

<?
if ($action=="doinsert") {

    $gzipoutput = 0;

    if (!$pauserinfo[cancontribute] AND !$pauserinfo[isadmin]) {
        $reasons=array("nopermission_reason_contribute_permission_denied");
        include("modules/default/nopermission.php");
    }

    $title = htmlspecialchars(trim($_POST[title]));

    $description = trim(strip_tags($_POST[description],"<a><b><i><u>"));

    $sortid = intval($_POST[sortid]);
    $author = htmlspecialchars(trim($_POST[author]));
    $contact = htmlspecialchars(trim($_POST[contact]));
    $source = htmlspecialchars(trim($_POST[source]));

    $subhead = htmlspecialchars(trim($_POST[subhead]));
    $articletext = $_POST[articletext];
    $articletext = eregi_replace("javascript","java script",$articletext);
    $articletext = eregi_replace("vbscript","vb script",$articletext);

    if (!pa_isset($title)) {
        $errormessage="error_miss_article_title";
				include("modules/default/error.php");
    }
    if (!pa_isset($articletext)) {
        $errormessage="error_miss_article_articletext";
				include("modules/default/error.php");
    }

    if (!empty($_FILES['image']['tmp_name'])) {
        $original = $_FILES['image']['name'];
        $filename = md5(uniqid(microtime(),1));
        if (($_FILES['image']['type']=="image/pjpeg" OR $_FILES['image']['type']=="image/gif" OR $_FILES['image']['type']=="image/x-png") AND copy($_FILES['image']['tmp_name'], "./upload/images/$filename")) {
           $DB->query("INSERT INTO ".$db_prefix."gallery (original,filename,type,size,dateline,userid)
                              VALUES ('".addslashes(trim($original))."','$filename','".addslashes($_FILES['image']['type'])."','".addslashes($_FILES['image']['size'])."','".time()."','$pauserinfo[userid]')");
           $imageid = $DB->insert_id();
        }
    }

	$postoptions=Array('noneedvalidate'=>1,'canupload'=>2,'cansetcolor'=>4);
	$visible=$postoptions['noneedvalidate']&$pauserinfo['postoptions'];
    $author = $pauserinfo[username];
    $contact = $pauserinfo[email];
    $DB->query("INSERT INTO ".$db_prefix."article (sortid,author,title,contact,source,description,date,imageid,editor,visible,keyword,userid)
                       VALUES ('$sortid','".addslashes($author)."','".addslashes($title)."','".addslashes($contact)."','".addslashes($source)."','".addslashes($description)."','".time()."','$imageid','".addslashes($pauserinfo[username])."','".intval($visible)."','".addslashes(htmlspecialchars(trim($_POST[keyword])))."','$pauserinfo[userid]')");

    $articleid = $DB->insert_id();

    if ($subhead=="") {
        $subhead = $title;
    }
    $DB->query("INSERT INTO ".$db_prefix."articletext (articleid,subhead,articletext)
                       VALUES ('$articleid','".addslashes($subhead)."','".addslashes($articletext)."')");

    $DB->query("UPDATE ".$db_prefix."sort SET articlecount=articlecount+1 WHERE sortid IN (".getparentsorts($sortid).")");


    if (!$nextpage) {
	$url = "$phparticleurl/";
	$redirectmsg="redirect_article_submited";
	include("modules/default/redirect.php");
    } else {
	$url = "$phparticleurl/index.php?mod=contribute&action=nextpage&articleid=$articleid";
	$redirectmsg="redirect_article_nextpage";
	include("modules/default/redirect.php");
    }

}
?>

<?

if ($action=="nextpage") {

    $templatelist = "navbar_contribute,contribute_submit";

    if (!$pauserinfo[cancontribute] AND !$pauserinfo[isadmin]) {
        $reasons=array("nopermission_reason_contribute_permission_denied");
        include("modules/default/nopermission.php");
    }

    $articleid = intval($_GET[articleid]);
    if (empty($articleid)) {
        show_errormessage("error_invalid_articleid");
    }
    $article = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."article WHERE articleid='$articleid' AND userid='$pauserinfo[userid]'");
    if (empty($article)) {
        show_errormessage("error_invalid_articleid");
    }
    //$article = validate_articleid($articleid);

    cachesorts();
    $sortlist = makesortlist();

    $sortlistoptions = sortsbit();

    ?><head>
<title><?=$phparticletitle?> - ��������</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-CN" />
<link rel="icon" href="<?=$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<link rel="shortcut icon" href="<?$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<meta name="description" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<meta name="keywords" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<script src="<?=$g_o_back2root?>/images/js/chinahtml.js" type="text/javascript"></script>
<link href="<?=$g_o_back2root?>/images/css/css.css" rel="stylesheet" type="text/css" />
</head>
<script type="text/javascript">
      function ProcessNextArticle(){

               if(document.article.subhead.value == ''){
                  alert('������С����.');
                  document.article.subhead.focus();
                  return false;
               }

               if(document.article.articletext.value == ''){
                  alert('����������.');
                  return false;
               }

               return true;
      }
</script>
<script type="text/javascript1.2">
<!-- // load htmlarea
_editor_url = "<?=$g_o_back2root?>/htmlarea/";                     // URL to htmlarea files
var win_ie_ver = parseFloat(navigator.appVersion.split("MSIE")[1]);
if (navigator.userAgent.indexOf('Mac')        >= 0) { win_ie_ver = 0; }
if (navigator.userAgent.indexOf('Windows CE') >= 0) { win_ie_ver = 0; }
if (navigator.userAgent.indexOf('Opera')      >= 0) { win_ie_ver = 0; }
if (win_ie_ver >= 5.5) {
 document.write('<scr' + 'ipt src="' +_editor_url+ 'editor.js"');
 document.write(' type="text/javascript1.2"></scr' + 'ipt>');
} else { document.write('<scr'+'ipt>function editor_generate() { return false; }</scr'+'ipt>'); }
// -->
</script>

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
���λ�ã�<a href="<?=$homepage?>/" class="classlinkclass">��ҳ</a>&nbsp;>&nbsp;<a href="<?=$phparticleurl?>" class="classlinkclass"><?=$phparticletitle?></a>&nbsp;>&nbsp;��ҪͶ��
		</div>
		</div>
		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
					<div class="title"> 
                                  <ol>
                    <li>�������ϵ��£�����<a
                    href="http://www.gdca.gov.cn/zcfg/18-10.asp"
                    target=_blank><font
                    color=red>��ȫ���˴�ί�����ά����������ȫ�ľ�����</font></a>��<a
                    href="http://www.gdca.gov.cn/zcfg/50.asp"
                    target=_blank><font
                    color=red>�����������ӹ���������涨��</font></a>���л����񹲺͹����������йط��ɷ��档<br>
                    <li>�Ͻ�����Σ�����Ұ�ȫ���𺦹������桢�ƻ������Žᡢ�ƻ������ڽ����ߡ��ƻ�����ȶ������衢�̰�����������������ݵ���Ʒ
                      ��<br>
                    <li>�û�����Լ���ʹ�����׷�������е���Ϊ�е��������Σ�ֱ�ӻ��ӵ��µģ���<br>
                    <li>����̳������Ȩ������ɾ�����Ͻ��̳�е��������ݡ�<br>
                    <li>���������е����°�Ȩ��ԭ�����ߺ�<?=$phparticletitle?>��ͬ���У��κ�����Ҫת�����������£���������ԭ�����߻�<?=$phparticletitle?>��Ȩ��<br>
                    <li>�����ύ�߷��Դ�������������뱾��վ�����޹� </li>
                  </ol>
					</div>



<fieldset><legend>��������:���������һҳ</legend>
<form method="post" action="<?=$g_o_back2root?>/index.php?mod=contribute" name="article" onSubmit="return ProcessNextArticle()">

                      <div>
                        <label><b>��������:
                          ���������һҳ </b></label>
                      </div>
                      <div>
                        <label>С����:</label>
                        
                          <input type="text" name="subhead" size="50" maxlength="50">
                      </div>
                      <div>
                        <label>����:</label>
                        <span class=text>
                          <textarea type="text" name="articletext" cols="80" rows="20" ></textarea></span>
                      </div>
                      <div class=enter>
                          <input type="hidden" name="articleid" value="<?=$articleid?>">
                          <input type="hidden" name="action" value="doinsertnextpage">
                          <input type="submit" value=" ��  �� " class="buttot" name="submit">
                          <input type="reset" value=" ��  �� "  class="buttot" name="reset">
                          <input type="submit" class="buttot" value="���������һҳ" name="nextpage" >
                          <script type="text/javascript1.2" defer>
var config = new Object(); // create new config object

config.width = "450";
config.height = "250";
config.bodyStyle = 'background-color: #FFFFFF; font-family: "Verdana"; font-size: 12px;';
config.debug = 0;
config.toolbar = [
  ['fontname'],
  ['fontsize'],
  ['linebreak'],
  ['bold','italic','underline','separator'],
  ['strikethrough','subscript','superscript','separator'],
  ['justifyleft','justifycenter','justifyright','separator'],
  ['OrderedList','UnOrderedList','Outdent','Indent','separator'],
  ['forecolor','backcolor','separator'],
  ['HorizontalRule','Createlink','InsertImage2','separator']
];

editor_generate('articletext',config);
</script>
                      </div>

                    </form>

</fieldset>

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
</html><?
exit;
}
?>

<?
if ($_POST[action]=="doinsertnextpage") {


    if (!$pauserinfo[cancontribute] AND !$pauserinfo[isadmin]) {
        $reasons=array("nopermission_reason_contribute_permission_denied");
        include("modules/default/nopermission.php");
    }

    $articleid = intval($_POST[articleid]);
    if (empty($articleid)) {
        show_errormessage("error_invalid_articleid");
    }
    $article = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."article WHERE articleid='$articleid' AND userid='$pauserinfo[userid]'");
    if (empty($article)) {
        show_errormessage("error_invalid_articleid");
    }

    $subhead = htmlspecialchars(trim($_POST[subhead]));

    if (strlen($subhead)>100) {
        show_errormessage("error_article_subhead_toolong");
    }

    $articletext = $_POST[articletext];
    $articletext = eregi_replace("javascript","java script",$articletext);
    $articletext = eregi_replace("vbscript","vb script",$articletext);

    $DB->query("INSERT INTO ".$db_prefix."articletext (articleid,subhead,articletext)
                       VALUES ('$articleid','".addslashes($subhead)."','".addslashes($articletext)."')");

    if (!$nextpage) {
        $url = "$phparticleurl/";
        $redirectmsg="reidrect_article_submited";
				include("modules/default/redirect.php");
    } else {
        $url = "$phparticleurl/index.php?mod=contribute&action=nextpage&articleid=$articleid";
        $redirectmsg="reidrect_article_nextpage";
				include("modules/default/redirect.php");
    }

}
?>


<?
function makesortnavbar($sortid) {
        global $DB, $db_prefix, $phparticletitle, $phparticleurl,$g_o_back2root, $parentsort;

        $navbit .= makesortnavbarbit($sortid, $parentsort);

        $navbar = "
���λ�ã�<a href='$homepage/' class='classlinkclass'>��ҳ</a>&nbsp;>&nbsp;<a href='$phparticleurl' class='classlinkclass'>$phparticletitle</a>$navbit
";
        return $navbar;
} 

function makesortnavbarbit($sortid, $parentsort, $isarticle = 0) {
        global $writedir, $subsort,$g_o_back2root,$g_depth,$usename,$singledir,$subsort,$usedate;
        static $outdirs;
        if ($sortid != -1) {
        	if($singledir==2)//û����Ŀ¼
        	{
        		$sorthtmllink = $subsort["dirname_$sortid"].".".HTMLEXT;
        		//$g_o_back2root="..";//��ǰ��Ӧ�ĸ�Ŀ¼���·��
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
        		$g_o_back2root="../".$g_o_back2root;//��ǰ��Ӧ�ĸ�Ŀ¼���·��
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
	                        $g_o_back2root="../".$g_o_back2root;//��ǰ��Ӧ�ĸ�Ŀ¼���·��
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
���λ�ã�<a href='$homepage/' class='classlinkclass'>��ҳ</a>&nbsp;>&nbsp;<a href='$phparticleurl' class='classlinkclass'>$phparticletitle</a>$navbit
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
        if (!empty($cache) AND $cache['expiry'] == 0) { // δ����
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
      <div class='righttitlename'>&nbsp;&nbsp;��������</div>
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
      <div class='righttitlename'>&nbsp;&nbsp;�Ƽ�����</div>
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

        if (!empty($cache) AND $cache['expiry'] == 0) { // δ����
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
���λ�ã�<a href='$homepage/' class='classlinkclass'>��ҳ</a>&nbsp;>&nbsp;<a href='$phparticleurl' class='classlinkclass'>$phparticletitle</a>$navbit
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

<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
<title><?=$phparticletitle?> - ��������</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-CN" />
<link rel="icon" href="<?=$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<link rel="shortcut icon" href="<?$g_o_back2root?>/images/favicon.ico" type="image/x-icon" media="screen" />
<meta name="description" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<meta name="keywords" content="<?=$phparticletitle?>,<?=$article[keyword]?>,<?=$article[description]?><?=$sortinfo[title]?>,<?=$article[title]?>" />
<script src="<?=$g_o_back2root?>/images/js/chinahtml.js" type="text/javascript"></script>
<link href="<?=$g_o_back2root?>/images/css/css.css" rel="stylesheet" type="text/css" />
</head>
<script type="text/javascript">

      function ProcessArticle(){

               if(document.article.title.value == ''){
                  alert('���������.');
                  document.article.title.focus();
                  return false;
               }

               if(document.article.sortid.value == '-1'){
                  alert('��ѡ�����.');
                  document.article.sortid.focus();
                  return false;
               }


               if(document.article.articletext.value == ''){
                  alert('����������.');
                  return false;
               }

               return true;
      }
      function ProcessNextArticle(){

               if(document.article.subhead.value == ''){
                  alert('������С����.');
                  document.article.subhead.focus();
                  return false;
               }

               if(document.article.articletext.value == ''){
                  alert('����������.');
                  return false;
               }

               return true;
      }
</script>
<script type="text/javascript1.2">
<!-- // load htmlarea
_editor_url = "<?=$g_o_back2root?>/htmlarea/";                     // URL to htmlarea files
var win_ie_ver = parseFloat(navigator.appVersion.split("MSIE")[1]);
if (navigator.userAgent.indexOf('Mac')        >= 0) { win_ie_ver = 0; }
if (navigator.userAgent.indexOf('Windows CE') >= 0) { win_ie_ver = 0; }
if (navigator.userAgent.indexOf('Opera')      >= 0) { win_ie_ver = 0; }
if (win_ie_ver >= 5.5) {
 document.write('<scr' + 'ipt src="' +_editor_url+ 'editor.js"');
 document.write(' type="text/javascript1.2"></scr' + 'ipt>');
} else { document.write('<scr'+'ipt>function editor_generate() { return false; }</scr'+'ipt>'); }
// -->
</script>

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
���λ�ã�<a href="<?=$homepage?>/" class="classlinkclass">��ҳ</a>&nbsp;>&nbsp;<a href="<?=$phparticleurl?>" class="classlinkclass"><?=$phparticletitle?></a>&nbsp;>&nbsp;��ҪͶ��
		</div>
		</div>
		<div class="clear">&nbsp;</div>
		<div class="pagecontent">
					<div class="title"> 
                                  <ol>
                    <li>�������ϵ��£�����<a
                    href="http://www.gdca.gov.cn/zcfg/18-10.asp"
                    target=_blank><font
                    color=red>��ȫ���˴�ί�����ά����������ȫ�ľ�����</font></a>��<a
                    href="http://www.gdca.gov.cn/zcfg/50.asp"
                    target=_blank><font
                    color=red>�����������ӹ���������涨��</font></a>���л����񹲺͹����������йط��ɷ��档<br>
                    <li>�Ͻ�����Σ�����Ұ�ȫ���𺦹������桢�ƻ������Žᡢ�ƻ������ڽ����ߡ��ƻ�����ȶ������衢�̰�����������������ݵ���Ʒ
                      ��<br>
                    <li>�û�����Լ���ʹ��<?=$phparticletitle?>��������е���Ϊ�е��������Σ�ֱ�ӻ��ӵ��µģ���<br>
                    <li>����̳������Ȩ������ɾ�����Ͻ��̳�е��������ݡ�<br>
                    <li>���������е����°�Ȩ��ԭ�����ߺ͹�ͬ���У��κ�����Ҫת�����������£���������ԭ�����߻�<?=$phparticletitle?>��Ȩ��<br>
                    <li>�����ύ�߷��Դ�������������뱾��վ�����޹� </li>
                  </ol>
					</div>


<fieldset><legend>��������</legend>

<form method="post" action="<?=$g_o_back2root?>/index.php?mod=contribute" enctype="multipart/form-data" name="article" onSubmit="return ProcessArticle()">
                      <div>
                        <label>����:</label>
                        
                          <input type="text" name="title" size="50" maxlength="100">
                      </div>
                      <div>
                        <label>����ժҪ:</label>
                          <textarea name="description" cols="50" rows="5" wrap="VIRTUAL"></textarea>
                      </div>
                      <div>
                        <label>����ͼ:</label>
                        
                          <input type="file" name="image">
                        </label>
                      </div>
                      <div>
                        <label>����:</label>
                          <select name="sortid">
				<?=$sortlistoptions?>
                          </select>
                      </div>
                      <div>
                        <label>�ؼ���:</label>
                          <input type="text" name="keyword" size="50" maxlength="50">
                      </div>
                      <div>
                        <label><b>��һҳ</b></label>
                      </div>
                      <div>
                        <label>С����:</label>
                        
                          <input type="text" name="subhead" size="50" maxlength="50">
                      </div>
                      <div>
                        <label>����:</label>
                        <span class=text><textarea type="text" name="articletext" cols="80" rows="20" ></textarea></span>
                      </div>
                      <div class=enter>
                          <input type="hidden" name="action" value="doinsert">
                          <input type="submit" value=" ��  �� " class="buttot" name="submit">
                          <input type="reset" value=" ��  �� "  class="buttot" name="reset">
                          <input type="submit" class="buttot" value="���������һҳ" name="nextpage" >
                          <script type="text/javascript1.2" defer>
var config = new Object(); // create new config object

config.width = "450";
config.height = "250";
config.bodyStyle = 'background-color: #FFFFFF; font-family: "Verdana"; font-size: 12px;';
config.debug = 0;
config.toolbar = [
  ['fontname'],['fontsize'],['linebreak'],['bold','italic','underline','separator'],
  ['strikethrough','subscript','superscript','separator'],
  ['justifyleft','justifycenter','justifyright','separator'],
  ['OrderedList','UnOrderedList','Outdent','Indent','separator'],
  ['forecolor','backcolor','separator'],
  ['HorizontalRule','Createlink','InsertImage2','separator']
];

editor_generate('articletext',config);
</script>
                      </div>

</form>
</fieldset>


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