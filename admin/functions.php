<?php
error_reporting(7);

function stripslashes_array(&$array) {
        while (list($k, $v) = each($array)) {
                if ($k != 'argc' && $k != 'argv' && (strtoupper($k) != $k || '' . intval($k) == "$k")) {
                        if (is_string($v)) {
                                $array[$k] = stripslashes($v);
                        } 
                        if (is_array($v)) {
                                $array[$k] = stripslashes_array($v);
                        } 
                } 
        } 
        return $array;
}

function cachetemplatelist($templateslist) {
        global $templatecache, $DB, $templatesetid, $db_prefix;
        $templateslist = str_replace(',', "','", addslashes($templateslist));
        $temps = $DB->query("SELECT template,title FROM " . $db_prefix . "template
                                     WHERE (title IN ('$templateslist') AND (templatesetid=-1 OR templatesetid='$templatesetid'))
                                     ORDER BY templatesetid");
        while ($temp = $DB->fetch_array($temps)) {
                $templatecache[$temp['title']] = $temp['template'];
        }
        unset($temp);
        $DB->free_result($temps);
} 

function gettemplate($templatename, $comment = 1) {
        global $templatecache, $DB, $templatesetid, $showcomment, $db_prefix, $templateuncache, $templateuncache;

        if (isset($templatecache[$templatename])) {
                $template = $templatecache[$templatename];
        } else {
                $gettemp = $DB->fetch_one_array("SELECT template FROM " . $db_prefix . "template
                                                    WHERE title='" . addslashes($templatename) . "' AND (templatesetid=-1 OR templatesetid='$templatesetid')
                                                    ORDER BY templatesetid DESC
                                                    LIMIT 1");
                $template = $gettemp[template];
                $templatecache[$templatename] = $gettemp[template];
                $templateuncache[] = $templatename;
        } 

        $template = str_replace("\\'", "'", addslashes($template));
        if ($showcomment == 1 AND $comment == 1) {
                return "<!-- BEGIN TEMPLATE: $templatename -->\n$template\n<!-- END TEMPLATE: $templatename -->";
        } else {
                return $template;
        } 
}

function gettemplate_fromfile($templatename, $dir="", $ext, $comment = 1) {
        global $templatecache, $showcomment,  $templateuncache, $templateuncache;

        if (isset($templatecache[$templatename])) {
                $template = $templatecache[$templatename];
        } else {
                $template = implode("", file($dir.$templatename.".".$ext));
                $templatecache[$templatename] = $template;
                $templateuncache[] = $templatename;
        }

        $template = str_replace("\\'", "'", addslashes($template));
        if ($showcomment == 1 AND $comment == 1) {
                return "<!-- BEGIN TEMPLATE: $templatename -->\n$template\n<!-- END TEMPLATE: $templatename -->";
        } else {
                return $template;
        } 
}

function dooutput($text) {
        global $debug, $showqueries, $DB, $templatecache, $gzipoutput, $gziplevel, $writename, $writedir, $pauserinfo, $phparticleurl,$g_o_back2root,$g_o_back2root;
        if ($debug != 1) {
                if ($showqueries == 1) {
                        global $querytime, $script_start_time, $query_count;
                        $start_time = explode(' ', $script_start_time);
                        $script_starttime = $start_time[0] + $start_time[1];

                        $end_time = explode(' ', microtime());
                        $script_endtime = $end_time[0] + $end_time[1];
                        $totaltime = $script_endtime - $script_starttime;
                        $stat = "<!-- <p align=\"center\" class=\"smallfont\">";
                        $stat .= "Page created in $totaltime seconds with $DB->querycount queries.<br>\nspending " . (round($querytime / $totaltime, 4) * 100) . "% doing MySQL queries and " . (round(($totaltime - $querytime) / $totaltime, 4) * 100) . "% doing PHP things.";

                        if ($gzipoutput == 1) {
                                $stat .= "<br>Gzip: ON, Level: $gziplevel";
                        } 

                        $ar_buf = loadavg();

                        for ($i = 0;$i < 3;$i++) {
                                if ($ar_buf[$i] > 2) {
                                        $load_avg .= ' ';
                                } else {
                                        $load_avg .= $ar_buf[$i] . ' ';
                                } 
                        } 
                        $stat .= " Server Load: " . trim($load_avg);
                        $stat .= "</p> -->";
                        $text .= $stat;
                }
               /* if ($pauserinfo['userid'] == 0 || ($pauserinfo[userid] != 0 && empty($writename))) {
                        if ($gzipoutput == 1) {
                                $text = gzip_encode($text, $gziplevel);
                        } 
                        echo $text;
                } else*/if ($writename) {
                        if ($writedir) {
                                make_dir_from_string($writedir);
                                writetofile($writedir . $writename . "." . HTMLEXT, $text);
                                if (CREATE_HTML_FILE != 1)
                                {
                                	echo "<html><META HTTP-EQUIV=Refresh CONTENT='0; URL=".$phparticleurl . "/" . $writedir . urlencode($writename) . "." . HTMLEXT."'>";
                                }
                        } else {
                                writetofile($writename . "." . HTMLEXT, $text);
                                if (CREATE_HTML_FILE != 1&&$writename!="index")
                                {
                                	echo "<html><META HTTP-EQUIV=Refresh CONTENT='0; URL=./" . urlencode($writename) . "." . HTMLEXT."'>";
                            	}
                        }
                        if(!$_GET['auto'])
                        echo $writedir . $writename . "." . HTMLEXT."<br>";
                }
        } else {
                $templatecount = count($templatecache);
                echo "<p><font face=\"verdana, arial, helvetica ,宋体\" style=\"font-size=9px\">";
                echo "Total Templates: $templatecount<br>\n";
                foreach ($templatecache as $key => $val) {
                        echo "$key | \n";
                } 
                echo "<br>";

                global $templateuncache;
                $templateuncachecount = count($templateuncache);
                echo "Uncache Templates: $templateuncachecount<br>";
                if ($templateuncachecount > 0) {
                        foreach ($templateuncache as $key => $val) {
                                echo "$val | \n";
                        } 
                } 
                print_rr(getallheaders());
                echo "</font></p>";
                if ($showqueries == 1) {
                        global $querytime, $script_start_time, $query_count;
                        $start_time = explode(' ', $script_start_time);
                        $script_starttime = $start_time[0] + $start_time[1];

                        $end_time = explode(' ', microtime());
                        $script_endtime = $end_time[0] + $end_time[1];
                        $totaltime = $script_endtime - $script_starttime;
                        $stat = "<center><font face=\"verdana, arial, helvetica ,宋体\" style=\"font-size=7pt\">Page created in $totaltime seconds with $DB->querycount queries.<br>\nspending " . (round($querytime / $totaltime, 4) * 100) . "% doing MySQL queries and " . (round(($totaltime - $querytime) / $totaltime, 4) * 100) . "% doing PHP things.</font>\n</center>";
                        echo $stat;
                } 
        } 
        if (CREATE_HTML_FILE != 1)
                exit;
} 

function writetofile($file_name, $data, $method = "w") {
	$filenum = fopen($file_name, $method);
	$result=flock($filenum, LOCK_EX|LOCK_NB);
	if ($data != "")
		$file_data = fwrite($filenum, $data);
	flock($filenum, LOCK_UN);
	fclose($filenum);
	return $result;
}
/*
function make_dir_from_string($dir_string = '')
{
	$smallimagedirs = explode("/", $dir_string);
	$todir = getcwd ()."/";
	foreach($smallimagedirs AS $creatdir){
		$depth ++;
		if(!@is_dir($todir.$creatdir))
		{
			@mkdir($todir.$creatdir, 0777);
			@chmod($creatdir, 0777);
			rename($creatdir,$todir.$creatdir);
		}
		$todir = $todir.$creatdir."/";
	//	chdir($creatdir);
	}
//	for($i=0;$i<$depth;$i++)chdir("..");//go to pic root directory
}
*/
// Make directorys,each dir should be splited by character '/'
function make_dir_from_string($dir_string = '') {
        $smallimagedirs = explode("/", $dir_string);
        foreach($smallimagedirs AS $creatdir) {
        	if($creatdir){
			$depth ++;
			if (!is_dir($creatdir)) {
				mkdir($creatdir, 0777);
				chmod($creatdir, 0777);
			}
			chdir($creatdir);
        	}
        } 
        for($i = 0;$i < $depth;$i++)chdir(".."); //go to pic root directory
} 
/*
function make_dir_from_string($dir_string = '')
{
	$ftp_server='61.152.251.214';//serverip 
	$conn_id = ftp_connect($ftp_server); 
	
	
	// login with username and password 
	$user="xxxxxxxxxx";//username 
	$passwd="ccccccccccc";//password 
	$login_result = ftp_login($conn_id, $user, $passwd); 
	
	// check connection 
	if ((!$conn_id) || (!$login_result)) { 
	echo "FTP connection has failed!"; 
	echo "Attempted to connect to $ftp_server for user $ftp_user_name"; 
	die; 
	} else { 
	echo "
	Connected to $ftp_server, for user $user
	"; 
	}
	$smallimagedirs = explode("/", $dir_string);
	foreach($smallimagedirs AS $creatdir){
		$depth ++;
	//	if(!is_dir($creatdir))
		{
			$sR = nl2br(@ftp_mkdir($conn_id, $creatdir));
			      if($R) {
			          print($sR);
			        } else {
			          print("There is no output from this command or it failed.\n");
			        }

			ftp_exec($conn_id,"SITE CHMOD 777 $creatdir");
		}
		ftp_chdir($conn_id,$creatdir);
	}
	for($i=0;$i<$depth;$i++)ftp_chdir($conn_id, "..");//go to pic root directory
	ftp_close($conn_id);
}
*/
function get_sortdirs($sortid = '-1') {
        global $DB, $db_prefix,$usename,$singledir,$subsort;
        $sorts = $DB->fetch_one_array("SELECT parentlist FROM " . $db_prefix . "sort WHERE sortid='$sortid'");
        $sortbits = explode(',', $sorts['parentlist']);
        foreach($sortbits AS $sortid) {
                if ($sortid != '-1') {
                	if($singledir==2)
                	{
                		$sortdirs="";
                		break;
                	}else if($singledir==1)
                	{
                		$sortdirs = ($usename?$subsort["dirname_$sortid"]:$sortid);
                		break;
                	}else{
	                        if ($sortdirs)
	                                $sortdirs = ($usename?$subsort["dirname_$sortid"]:$sortid) . "/" . $sortdirs;
	                        else $sortdirs = ($usename?$subsort["dirname_$sortid"]:$sortid);
                	}
                } 
        }
        return $sortdirs;
} 

function get_sortsubdirs($sortid = '-1', $rootdir = '-1',$usename=0) {
        global $DB, $db_prefix,$usename,$singledir,$subsort;
        $sorts = $DB->fetch_one_array("SELECT parentlist FROM " . $db_prefix . "sort WHERE sortid='$sortid'");
        $sortbits = explode(',', $sorts['parentlist']);
        foreach($sortbits AS $sortid) {
                if ($sortid != '-1' && $sortid != $rootdir) {
                	if($singledir==2)
                	{
                		$sortdirs="";
                		break;
                	}else if($singledir==1)
                	{
                		$sortdirs = $usename?$subsort["dirname_$sortid"]:$sortid;
                		break;
                	}else{
	                        if ($sortdirs)
	                                $sortdirs = ($usename?$subsort["dirname_$sortid"]:$sortid) . "/" . $sortdirs;
	                        else $sortdirs = $usename?$subsort["dirname_$sortid"]:$sortid;
                	}
                }else if($sortid == $rootdir)break;
        }
        return $sortdirs;
} 
function mkdirname($sortid,$rootdir,$articledate,$thesame,$insort)
{
	global $usedate,$usename,$singledir,$subsort;
	$returndir = "";

	if($singledir!=2){//=2没有子目录
		if($thesame)
		{
			if($singledir==0)
			if($usedate&&$articledate)$returndir=date("Y_m",$articledate)."/";
		}else if($insort)
		{
			if($singledir)$returndir=($usename?$subsort["dirname_$sortid"]:$sortid)."/";
			else $returndir=get_sortsubdirs($sortid,$rootdir,$usename)."/".(($usedate&&$articledate)?date("Y_m",$articledate)."/":"");
		}else
		{
			$returndir=get_sortdirs($sortid)."/".($usedate&&$articledate&&$singledir==0?date("Y_m",$articledate)."/":"");
		}
	}
	return $returndir;
}
function get_sortdepths($sortid = '-1') {
        global $DB, $db_prefix;
        $sorts = $DB->fetch_one_array("SELECT parentlist FROM " . $db_prefix . "sort WHERE sortid='$sortid'");
        $sortbits = explode(',', $sorts['parentlist']);
        foreach($sortbits AS $sortid) {
                if ($sortid != '-1') {
                        $sortdepths ++;
                } 
        } 
        return $sortdepths;
} 
function gzip_encode($contents, $gziplevel = 3) {
        $gzdata = $contents;
        $encoding = gzip_accepted();
        if (!headers_sent() AND function_exists('gzcompress') AND function_exists('crc32') AND !empty($encoding)) {
                // if (!headers_sent() AND extension_loaded("zlib") AND !empty($encoding)) {
                $gzdata = "\x1f\x8b\x08\x00\x00\x00\x00\x00"; // gzip header
                $size = strlen($contents);
                $crc = crc32($contents);
                $gzdata .= gzcompress($contents, $gziplevel);
                $gzdata = substr($gzdata, 0, strlen($gzdata) - 4); // fix crc bug
                $gzdata .= pack("V", $crc) . pack("V", $size); 
                // $gzdata = gzencode($contents,$gziplevel);
                header("Content-Encoding: " . $encoding);
                header("Vary: Accept-Encoding");
                header("Content-Length: " . strlen($gzdata)); 
                // header("Content-Length: ".strlen($gzdata));
        } 

        return $gzdata;
} 

function gzip_accepted() {
        if (strpos($_SERVER[HTTP_ACCEPT_ENCODING], "gzip") !== false) {
                $encoding = "gzip";
        } else if (strpos($_SERVER[HTTP_ACCEPT_ENCODING], "x-gzip") !== false) {
                $encoding = 'x-gzip';
        } 

        return $encoding;
} 

function loadavg() {
        if (file_exists("/proc/loadavg")) {
                if ($fd = fopen("/proc/loadavg", r)) {
                        $results = split(' ', fgets($fd, 4096));
                        fclose($fd);
                } 
        } else {
                $results = array('N.A.', 'N.A.', 'N.A.');
        } 
        return $results;
} 

unset($style);
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

function validate_email($address) {
        if (ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+' . '@' . '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.' . '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$', $address, $email)) {
                return true;
        } else {
                return false;
        } 
} 

function show_errormessage($templatetitle = "") {
	global $g_o_back2root;
    $errormessage=$templatetitle;
	include($g_o_back2root."/modules/default/error.php");
    exit;
}

function show_information($templatetitle = "") {
        global $style;
        global $header, $headinclude, $footer, $phparticleurl,$g_o_back2root, $phparticletitle, $onlineuser, $webmastermail, $version;

        eval("\$information = \"" . gettemplate("$templatetitle") . "\";");
        eval("dooutput(\"" . gettemplate('information') . "\");");
        exit;
} 

function validate_articleid($articleid) {
        global $DB, $db_prefix;
        $articleid = intval($articleid);
        if (empty($articleid)) {
                show_errormessage("error_invalid_articleid");
        } else {
                $articleinfo = $DB->fetch_one_array("SELECT * FROM " . $db_prefix . "article
                                                           WHERE articleid='$articleid' AND visible=1");
                if (empty($articleinfo)) {
                        show_errormessage("error_invalid_articleid");
                } 
                return $articleinfo;
        } 
} 

function validate_sortid($sortid) {
        global $DB, $db_prefix;
        $sortid = intval($sortid);

        if (empty($sortid)) {
                show_errormessage("error_invalid_sortid");
        } else {
                $sortinfo = $DB->fetch_one_array("SELECT * FROM " . $db_prefix . "sort WHERE sortid='$sortid'");
                if (empty($sortinfo)) {
                        show_errormessage("error_invalid_sortid");
                } 
                return $sortinfo;
        } 
} 

function validate_commentid($commentid) {
        global $DB, $db_prefix;
        $commentid = intval($commentid);

        if (empty($commentid)) {
                show_errormessage("error_invalid_commentid");
        } else {
                $commentinfo = $DB->fetch_one_array("SELECT * FROM " . $db_prefix . "comment WHERE commentid='$commentid'");
                if (empty($commentinfo)) {
                        show_errormessage("error_invalid_commentid");
                } 
                return $commentinfo;
        } 
} 

function validate_messageid($messageid) {
        global $DB, $db_prefix, $messageid;
        $messageid = intval($messageid);

        if (empty($messageid)) {
                show_errormessage("error_invalid_messageid");
        } else {
                $messageinfo = $DB->fetch_one_array("SELECT * FROM " . $db_prefix . "message WHERE messageid='$messageid'");
                if (empty($messageinfo)) {
                        show_errormessage("error_invalid_messageid");
                } 
                return $messageinfo;
        } 
} 

function subsorts($sortid) {
        global $DB, $db_prefix, $sorts;
        $sorts = $DB->query("SELECT sortid FROM " . $db_prefix . "sort WHERE parentid='$sortid'");
        if ($DB->num_rows($sorts) > 0) {
                return true;
        } else {
                return false;
        } 
} 

function getsubsorts($sortid) {
        global $DB, $db_prefix, $subsort; 
        // $sorts = $DB->query("SELECT sortid FROM ".$db_prefix."sort WHERE parentid='$sortid'");
        if (isset($subsort[$sortid])) {
                foreach ($subsort[$sortid] as $subsortid => $stitle) {
                        $sortid .= "," . getsubsorts($subsortid);
                } 
        } 
        return $sortid;
} 

function getsubsorts_first($sortid) {
        global $DB, $db_prefix, $subsort;
        $sorts = $DB->query("SELECT sortid FROM " . $db_prefix . "sort WHERE parentid='$sortid'");
        while ($sortinfo = $DB->fetch_array($sorts)) {
                $sortid .= "," . getsubsorts_first($sortinfo['sortid']);
        } 
        return $sortid;
} 

unset($subsort);
unset($parentsort);
function cachesorts() {
        global $DB, $db_prefix, $subsort, $parentsort;

        $caches = $DB->query("SELECT * FROM " . $db_prefix . "cache
                                        WHERE name IN ('subsort','parentsort')");
        while ($cache = $DB->fetch_array($caches)) {
                $$cache['name'] = unserialize($cache['content']);
                $expiry[$cache['name']] = $cache['expiry'];
                $c[$cache['name']] = 1;
        }
        // print_rr($subsort);
        // print_rr($parentsort);
        // print_rr($expiry);
        // exit;
        if (!$c['subsort'] OR !$c['parentsort'] OR $expiry['subsort'] == 1 OR $expiry['parentsort'] == 1) {
                $sorts = $DB->query("SELECT sortid,title,parentid,perpage,articlecount,dirname FROM " . $db_prefix . "sort ORDER BY displayorder,binary title,sortid");
                unset($subsortids);
                $pgjs = "paginationMax=new Array();";
                while ($sort = $DB->fetch_array($sorts)) {
                        $subsort[intval($sort['parentid'])][intval($sort['sortid'])] = $sort['title'];
                        $subsortids = getsubsorts_first($sort['sortid']);
                        $total = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM " . $db_prefix . "article AS article
					                                      WHERE sortid IN (0" . $subsortids . ") AND visible=1");
                        $subsort["total_$sort[sortid]"] = ($total['count'] < 1)?1:$total['count'];
                        $subsort["count_$sort[sortid]"] = $sort['articlecount'];
                        $subsort["perpage_$sort[sortid]"] = $sort['perpage'];
                        if(!$sort['dirname'])$subsort["dirname_$sort[sortid]"]=mkfilename(3,$sort['title'],0);
                        $parentsort[intval($sort['sortid'])][intval($sort['parentid'])] = $sort['title'];
                        $totalpages = ceil($sort['articlecount'] / $sort['perpage']);
                        $pgjs .= "paginationMax[".$sort['sortid']."]=$totalpages;";
                } 
                // print_rr($parentsort);
                if ($c['subsort']) {
                        if ($expiry['subsort']) {
                                $DB->query("UPDATE " . $db_prefix . "cache SET
                                        content='" . addslashes(serialize($subsort)) . "',
                                        expiry=0
                                        WHERE name='subsort'");
                        }
                } else {
                        $DB->query("INSERT INTO " . $db_prefix . "cache (name,content,expiry) VALUES
                                    ('subsort','" . addslashes(serialize($subsort)) . "',0)");
                }
                if ($c[parentsort]) {
                        if ($expiry[parentsort]) {
                                $DB->query("UPDATE " . $db_prefix . "cache SET
                                        content='" . addslashes(serialize($parentsort)) . "',
                                        expiry=0
                                        WHERE name='parentsort'");
                        }
                } else {
                        $DB->query("INSERT INTO " . $db_prefix . "cache (name,content,expiry) VALUES
                                    ('parentsort','" . addslashes(serialize($parentsort)) . "',0)");
                }
                $fp = fopen("admin/configs/pg.js",w);
                fwrite($fp,$pgjs);
		fclose($fp);
        }
} 

function dosnav($sid) {
        global $DB, $db_prefix, $phparticletitle, $phparticleurl,$g_o_back2root, $parentsort;
        $navbit = buildparent($sid, $parentsort);
        eval("\$nav = \"" . gettemplate('nav') . "\";");
        return $nav;
} 


/* -=-=-=-=-=-=-=-=-=-=-=-=-
    function parse url
-=-=-=-=-=-=-=-=-=-=-=-=- */
function parseurl($text) {
        return ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $text);
} 

/* -=-=-=-=-=-=-=-=-=-=-=-=-
    function redirect
-=-=-=-=-=-=-=-=-=-=-=-=- */
function redirect($url, $template, $image = "information") {
        global $style, $phparticleurl,$g_o_back2root;
        if (!isset($url)) {
                $url = "./index.php";
        }
        eval("\$headinclude = \"" . gettemplate('headinclude') . "\";");
        if ($information == "information") {
                $img = "information.gif";
        } else {
                $img = "warning.gif";
        }
        eval("\$msg = \"" . gettemplate($template) . "\";");
        eval("dooutput(\"" . gettemplate('redirect') . "\");");
        exit;
}

function getuserinfo($userid, $password) {
        global $DB, $db_prefix, $user;
        $userid = intval($userid);
        if (empty($userid)) {
                return;
        } else {
                $pauserinfo = $DB->fetch_one_array("SELECT * FROM " . $db_prefix . "user AS user
                                                          LEFT JOIN " . $db_prefix . "usergroup AS usergroup
                                                               ON user.usergroupid=usergroup.usergroupid
                                                          WHERE user.userid='$userid' AND user.password='" . addslashes($password) . "'");
                return $pauserinfo;
        } 
} 

function padate($format, $timestamp) {
        global $timezone, $pauserinfo;
        $time = $timestamp + ($pauserinfo[timezoneoffset] - $timezone) * 3600;
        if ($time < 0) {
                $time = 0;
        } 
        return date($format, $time);
} 

function makeradompw($length = 8, $list = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ") {
        mt_srand((double)microtime() * 1000000);
        $newstring = "";
        if ($length > 0) {
                while (strlen($newstring) < $length) {
                        $newstring .= $list[mt_rand(0, strlen($list)-1)];
                } 
        } 
        return $newstring;
} 

$disp_pagination_num = 10;
function makepagelink($link, $page, $pages) {
        // echo $link;exit;
        global $disp_pagination_num;
        if (empty($pages)) return "<b>1</b>";
        if (($page + $disp_pagination_num + 1) <= $pages) {
                $pagelink .= " <a href=\"" . $link . "_" . $pages . "." . HTMLEXT . "\" title=\"第一页\">&laquo;</a> <a href=\"" . $link . "_" . ($page + 1) . "." . HTMLEXT . "\">上一页</a>";
        } 

        if (($pages - $page) > $disp_pagination_num + 1) {
                $pagelink .= " <a href=\"" . $link . "_" . ($page + $disp_pagination_num + 1) . "." . HTMLEXT . "\">...</a>";
        } 
        if ($page - $disp_pagination_num < 1) {
                $pagex = 1;
        } else {
                $pagex = $page - $disp_pagination_num;
        } 
        for($i = $page + $disp_pagination_num,$j=1;$i >= $pagex;$i--,$j++) {
                if ($i > $pages) {
                        $i = $pages;
                } 
                if ($i == $page) {
                        $pagelink .= " <b id=\"s$j\">$i</b>";
                } else {
                        $pagelink .= " <a id=\"s$j\" href=\"" . $link . "_$i." . HTMLEXT . "\">$i</a>";
                } 
        } 
        if ($page > $disp_pagination_num + 2) {
                $pagelink .= " <a href=\"" . $link . "_" . ($page - ($disp_pagination_num + 1)) . "." . HTMLEXT . "\">...</a>";
        } 
        if (($page - ($disp_pagination_num + 1)) >= 1) {
                $pagelink .= " <a href=\"" . $link . "_" . ($page-1) . "." . HTMLEXT . "\">下一页</a> <a href=\"" . $link . "_1." . HTMLEXT . "\" title=\"最后一页\">&raquo;</a>";
        } 

        return $pagelink;
} 

function makepagelink2($link, $page, $pages) {
        global $disp_pagination_num;
        if (empty($pages)) return "<b>1</b>";
        if ($page != 1) {
                $pagelink .= " <a href=\"$link&pagenum=1\" title=\"第一页\">&laquo;</a> <a href=\"$link&pagenum=" . ($page-1) . "\">上一页</a>";
        } 
        if ($page >= $disp_pagination_num + 2) {
                $pagelink .= " <a href=\"$link&pagenum=" . ($page - ($disp_pagination_num + 1)) . "\">...</a>";
        } 
        if ($page + $disp_pagination_num >= $pages) {
                $pagex = $pages;
        } else {
                $pagex = $page + $disp_pagination_num;
        } 
        for($i = $page - $disp_pagination_num;$i <= $pagex;$i++) {
                if ($i <= 0) {
                        $i = 1;
                } 
                if ($i == $page) {
                        $pagelink .= " <b>$i</b>";
                } else {
                        $pagelink .= " <a href=\"$link&pagenum=$i\">$i</a>";
                } 
        } 
        if (($pages - $page) >= $disp_pagination_num + 1) {
                $pagelink .= " <a href=\"$link&pagenum=" . ($page + $disp_pagination_num + 1) . "\">...</a>";
        } 
        if ($page != $pages) {
                $pagelink .= " <a href=\"$link&pagenum=" . ($page + 1) . "\">下一页</a> <a href=\"$link&pagenum=" . $pages . "\" title=\"最后一页\">&raquo;</a>";
        } 

        return $pagelink;
} 

//获取给定路径的返回路径
function get_back2path($stringpath)
{
	$points = explode('/',$stringpath);
	foreach($points AS $path)
	{
		$back2path .= "../";
	}
	return $back2path;
}


function make_tag_articlelist($method="get",$locate="index",$contenttype='new',$tagname,$type,$sortid,$maxarticles,$tltlelen,$templatename) {
        global $phparticleurl,$g_o_back2root,$g_back2path,$filenamemethod;
        global $subsort;
        global $DB, $db_prefix;
        global $styleid,$style,$forumlist,$tablelist,$aconvertlist,$sconvertlist,$atconvertlist,$dbname_bbs;
	$g_o_back2root=".";
	$savename = $locate."_" . $styleid . "_articlelist";
        $cache = $DB->fetch_one_array("SELECT * FROM " . $db_prefix . "cache
                                                 WHERE name='".$savename."'");

	$articlelist = unserialize($cache['content']);
        if ((!empty($cache) AND $cache['expiry'] == 0)&&$method=="get") { // 未过期
	//	$articlelist = unserialize($cache['content']);
        } else if($method=="del")
        {
        //	$articlelist = unserialize($cache['content']);
        	foreach($articlelist[$type] AS $namekey => $tagdata1)
        	{
        		if($namekey==$tagname)
	        	foreach($tagdata1 AS $sortname=>$tagdata)
	        	{
	        		if($sortid!=$sortname)
	        		$newarticlelist[$type][$tagname][$sortname]=$tagdata;
	        	}
	        	else $newarticlelist[$type][$namekey] = $tagdata1;
        	}
        	unset($articlelist[$type]);
        	$articlelist[$type]=$newarticlelist;
        	if (!empty($cache)) {
                        $DB->query("UPDATE " . $db_prefix . "cache SET
	                                    content='" . addslashes(serialize($articlelist)) . "',
	                                    expiry=0
	                                    WHERE name='".$savename."'");
                } elseif (empty($cache)) {
                        $DB->query("INSERT INTO " . $db_prefix . "cache (name,content,expiry) VALUES
	                                    ('".$savename."','" . addslashes(serialize($articlelist)) . "',0)");
                }
        }else
        {
        			$subsortids = getsubsorts_first($sortid);
        			if($contenttype=='new')
        			{
        				$selopt="sortid IN (0" . $subsortids . ")";
        				$orderopt="date DESC";
        			}else
        			if($contenttype=='special')
        			{
        				$selopt="sortid=".$sortid;
        				$orderopt="date DESC";
        			}
        			else if($contenttype=='recommend')
        			{
        				$averagescore = ",(totalscore/voters) as averagescore";
        				$selopt="voters>0 AND sortid IN (0" . $subsortids . ")";
        				$orderopt="averagescore DESC";
        			}else
        			{
        				$selopt="sortid IN (0" . $subsortids . ")";
        				$orderopt="views DESC";
        			}
	            if($type=='img')
	            {
	            	$selopt="imageid!=0 AND ".$selopt;
	            }
			        $articles = $DB->query("SELECT * $averagescore FROM " . $db_prefix . "article 
			                             WHERE ".$selopt." AND visible=1
			                            ORDER BY ".$orderopt."
			                            LIMIT $maxarticles");
			        if($type=='img'){
								while ($article = $DB->fetch_array($articles)) {
	                $articlehtmllink = HTMLDIR . "/" . mkdirname($article['sortid'],-1,$article['date'],0,0).rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//get_sortdirs($article['sortid']) . "/" . date("Y_m", $article['date']) . "/"
	                $title = $article['title'];
	                $article['title'] = cnSubStr($article['title'], $img_article_len);
	                $article['description'] = cnSubStr($article['description'], $img_article_len);
	                $article['date'] = padate("m/d", $article['date']);
	                eval("\$content .= \"" . gettemplate_fromfile($templatename,"templates/default/html/","htm") . "\";");
								}
							}else
							{
			        	while ($article = $DB->fetch_array($articles)) {
			                $articlehtmllink = HTMLDIR . "/" . mkdirname($article['sortid'],-1,$article['date'],0,0).rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//get_sortdirs($article['sortid']) . "/" . date("Y_m", $article['date']) . "/"
			                $title = $article['title'];
			                $article['title'] = cnSubStr($article[title], $tltlelen);
			                $article['date'] = padate("m/d", $article[date]);
			                if ($article['date'] == date("m/d")) {
			                        $datefont = "red";
			                } else {
			                        $datefont = "normalfont";
			                }
			                eval("\$content .= \"" . gettemplate_fromfile($templatename,"templates/default/html/","htm") . "\";");
			        	}
			      	}
	                $articlelist[$type][$tagname][$sortid] = $content;
                if (!empty($cache)) {
                        $DB->query("UPDATE " . $db_prefix . "cache SET
	                                    content='" . addslashes(serialize($articlelist)) . "',
	                                    expiry=0
	                                    WHERE name='".$savename."'");
                } elseif (empty($cache)) {
                        $DB->query("INSERT INTO " . $db_prefix . "cache (name,content,expiry) VALUES
	                                    ('".$savename."','" . addslashes(serialize($articlelist)) . "',0)");
                }
                $sorts = $DB->fetch_one_array("SELECT parentlist FROM " . $db_prefix . "sort WHERE sortid='$sortid'");
                $DB->query("UPDATE " . $db_prefix . "tag SET
	                                    renew=0
	                                    WHERE tagname='defaultsys' and sortid IN (".$sorts['parentlist'].",0)");
        }

        return $articlelist;
}

unset($hotarticlelist);
function gethotarticles() {
        global $DB, $db_prefix, $phparticleurl,$g_o_back2root, $hotarticlenum;
        global $style,$filenamemethod;
        $articles = $DB->query("SELECT articleid,article.sortid,date,article.title,views FROM " . $db_prefix . "article AS article
                                           LEFT JOIN " . $db_prefix . "sort AS sort
                                             ON article.sortid=sort.sortid
                                           WHERE sort.showinhot=1 AND article.visible=1
                                           ORDER BY views DESC LIMIT $hotarticlenum");
        while ($article = $DB->fetch_array($articles)) {
                $articlehtmllink = HTMLDIR . "/" . mkdirname($article['sortid'],-1,$article['date'],0,0).rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//get_sortdirs($article['sortid']) . "/" . date("Y_m", $article['date']) . "/"
                eval("\$hotarticlelistbit .= \"" . gettemplate('hotarticlelistbit') . "\";");
        } 
        eval("\$hotarticlelist = \"" . gettemplate('hotarticlelist') . "\";");

        return $hotarticlelist;
} 

function gethotsort_articles($sortid = 0) {
        global $DB, $db_prefix, $phparticleurl,$g_o_back2root, $hotarticlenum;
        global $style,$filenamemethod;
        $articles = $DB->query("SELECT articleid,article.sortid,date,article.title,views FROM " . $db_prefix . "article AS article
                                           LEFT JOIN " . $db_prefix . "sort AS sort
                                             ON article.sortid=sort.sortid
                                           WHERE article.sortid=$sortid AND sort.showinhot=1 AND article.visible=1
                                           ORDER BY views DESC LIMIT $hotarticlenum");
        while ($article = $DB->fetch_array($articles)) {
                $articlehtmllink = "../" . mkdirname($article['sortid'],-1,$article['date'],1,0).rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//date("Y_m", $article['date']) . "/"
                eval("\$hotarticlelistbit .= \"" . gettemplate('hotsortarticlelistbit') . "\";");
        } 
        eval("\$hotarticlelist = \"" . gettemplate('hotarticlelist') . "\";");

        return $hotarticlelist;
} 

unset($newarticlelist);
function getnewarticles() {
        global $DB, $db_prefix, $phparticleurl,$g_o_back2root, $lastupdatenum;
        global $style,$g_o_back2root,$filenamemethod;
        $articles = $DB->query("SELECT articleid,article.sortid,article.title,views,date FROM " . $db_prefix . "article AS article
                                           LEFT JOIN " . $db_prefix . "sort AS sort
                                             ON article.sortid=sort.sortid
                                           WHERE sort.showinlast=1 AND article.visible=1
                                           ORDER BY date DESC LIMIT $lastupdatenum");
        while ($article = $DB->fetch_array($articles)) {
                $articlehtmllink = HTMLDIR . "/" . mkdirname($article['sortid'],-1,$article['date'],0,0).rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//get_sortdirs($article['sortid']) . "/" . date("Y_m", $article['date']) . "/"
                $article[date] = date("m/d", $article[date]);
                eval("\$newarticlelistbit .= \"" . gettemplate('newarticlelistbit') . "\";");
        } 
        eval("\$newarticlelist = \"" . gettemplate('newarticlelist') . "\";");

        return $newarticlelist;
}

unset($poparticlelist);
function getpoparticles() {
        global $DB, $db_prefix, $phparticleurl,$g_o_back2root, $ratearticlenum;
        global $style,$filenamemethod;
        $articles = $DB->query("SELECT articleid,article.sortid,article.date,article.title,views,(totalscore/voters) as averagescore,voters FROM " . $db_prefix . "article AS article
                                           LEFT JOIN " . $db_prefix . "sort AS sort
                                             ON article.sortid=sort.sortid
                                           WHERE voters>0 AND sort.showinrate=1 AND article.visible=1
                                           ORDER BY averagescore DESC
                                           LIMIT $ratearticlenum");
        while ($article = $DB->fetch_array($articles)) {
                $articlehtmllink = HTMLDIR . "/" . mkdirname($article['sortid'],-1,$article['date'],0,0).rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//get_sortdirs($article['sortid']) . "/" . date("Y_m", $article['date']) . "/"
                eval("\$poparticlelistbit .= \"" . gettemplate('poparticlelistbit') . "\";");
        } 
        eval("\$poparticlelist = \"" . gettemplate('poparticlelist') . "\";");

        return $poparticlelist;
} 

/*
unset($hotsortlist);
function gethotsorts() {

         global $DB,$db_prefix,$hotsortnum;
         $sorts = $DB->query("SELECT sortid,title,articlecount FROM ".$db_prefix."sort
                                        ORDER BY articlecount DESC,sortid ASC LIMIT $hotsortnum");
         while ($sort = $DB->fetch_array($sorts)){
                eval("\$hotsortlistbit .= \"".gettemplate('hotsortlistbit')."\";");
         }
         eval("\$hotsortlist = \"".gettemplate('hotsortlist')."\";");

         return $hotsortlist;

}
 */

function print_rr($array = array()) {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
} 

function getip() {
        if (isset($_SERVER)) {
                if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                        $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
                } elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
                        $realip = $_SERVER["HTTP_CLIENT_IP"];
                } else {
                        $realip = $_SERVER["REMOTE_ADDR"];
                } 
        } else {
                if (getenv('HTTP_X_FORWARDED_FOR')) {
                        $realip = getenv('HTTP_X_FORWARDED_FOR');
                } elseif (getenv('HTTP_CLIENT_IP')) {
                        $realip = getenv('HTTP_CLIENT_IP');
                } else {
                        $realip = getenv('REMOTE_ADDR');
                } 
        } 
        return $realip;
} 

function show_nopermission($reasons = array()) {
        global $header, $headinclude, $footer, $pauserinfo, $phparticletitle, $phparticleurl,$g_o_back2root, $url, $style;

        if ($pauserinfo[userid] == 0) {
                eval("\$reasonbit .= \"" . gettemplate("nopermission_reason_notlogin") . "\";");
                eval("\$login = \"" . gettemplate("nopermission_logincode") . "\";");
        } else {
                eval("\$login = \"" . gettemplate("nopermission_logoutcode") . "\";");
        } 
        if (!empty($reasons) AND is_array($reasons)) {
                foreach($reasons AS $reason) {
                        eval("\$reasonbit .= \"" . gettemplate("$reason") . "\";");
                } 
        }

        eval("dooutput(\"" . gettemplate('nopermission') . "\");");
}

function pa_isset($value) {
        $value = trim($value);
        if (isset($value) AND $value != "") {
                return true;
        } else {
                return false;
        } 
} 

function sortsbit($sortid = "-1", $level = 1) {
        global $DB, $db_prefix, $subsort;
        if (isset($subsort[intval($sortid)])) {
                foreach($subsort[intval($sortid)] as $sort['sortid'] => $sort['title']) {
                        $sortlistoptions .= "<OPTION value=\"$sort[sortid]\"$select>" . str_repeat("--", $level-1) . " $sort[title]</OPTION>\n" . sortsbit($sort[sortid], $level + 1);
                } 
        } 
        return $sortlistoptions;
} 

function sortsbit_js($sortid = "-1", $level = 1) {
        global $DB, $db_prefix, $subsort, $selected;
        if (isset($subsort[intval($sortid)])) {
                foreach($subsort[intval($sortid)] as $sort['sortid'] => $sort['title']) {
                        $sortlistoptions .= "<OPTION value=\"$sort[sortid]\"" . $selected[$sort['sortid']] . ">" . str_repeat("--", $level-1) . " $sort[title]</OPTION>\n" . sortsbit_js($sort['sortid'], $level + 1);
                } 
        } 
        return $sortlistoptions;
} 

function getparentsorts($sortid) {
        global $DB, $db_prefix;
        $sorts = $DB->query("SELECT parentid,title FROM " . $db_prefix . "sort WHERE sortid='$sortid'");
        while ($sort = $DB->fetch_array($sorts)) {
                $sortid .= ",";
                $sortid .= getparentsorts($sort[parentid]);
        }
        return $sortid;
}

function mkfilename($fm=1,$title,$contenttype)
{
	global $articleprefix,$sortprefix;
	switch($fm)
	{
		case 1:
			if($contenttype==1)
				$filename=$articleprefix;
			else $filename=$sortprefix;
		break;
		case 2:
			$title=trim($title);
			if(!empty($title))
			{
				$filename=preg_replace("/[^".chr(0xa1)."-".chr(0xff)."0-9A-Za-z_-]+/",'-',$title);
			}
		break;
		case 3:
		$title=trim($title);
			if(!empty($title))
			{
				$filename=preg_replace("/[^".chr(0xa1)."-".chr(0xff)."0-9A-Za-z_-]+/",'-',$title);
				$filename=c($filename);
			}
		break;
		default:
		break;
	}
	return $filename;
}

$d=array( 
		 array("A",-20319), 
		 array("Ai",-20317), 
		 array("An",-20304), 
		 array("Ang",-20295), 
		 array("Ao",-20292), 
		 array("Ba",-20283), 
		 array("Bai",-20265), 
		 array("Ban",-20257), 
		 array("Bang",-20242), 
		 array("Bao",-20230), 
		 array("Bei",-20161), 
		 array("Ben",-20036), 
		 array("Beng",-20032), 
		 array("Bi",-20026), 
		 array("Bian",-20002), 
		 array("Biao",-19990), 
		 array("Bie",-19986), 
		 array("Bin",-19982), 
		 array("Bing",-19976), 
		 array("Bo",-19805), 
		 array("Bu",-19784), 
		 array("Ca",-19775), 
		 array("Cai",-19774), 
		 array("Can",-19763), 
		 array("Cang",-19756), 
		 array("Cao",-19751), 
		 array("Ce",-19746), 
		 array("Ceng",-19741), 
		 array("Cha",-19739), 
		 array("Chai",-19728), 
		 array("Chan",-19725), 
		 array("Chang",-19715), 
		 array("Chao",-19540), 
		 array("Che",-19531), 
		 array("Chen",-19525), 
		 array("Cheng",-19515), 
		 array("Chi",-19500), 
		 array("Chong",-19484), 
		 array("Chou",-19479), 
		 array("Chu",-19467), 
		 array("Chuai",-19289), 
		 array("Chuan",-19288), 
		 array("Chuang",-19281), 
		 array("Chui",-19275), 
		 array("Chun",-19270), 
		 array("Chuo",-19263), 
		 array("Ci",-19261), 
		 array("Cong",-19249), 
		 array("Cou",-19243), 
		 array("Cu",-19242), 
		 array("Cuan",-19238), 
		 array("Cui",-19235), 
		 array("Cun",-19227), 
		 array("Cuo",-19224), 
		 array("Da",-19218), 
		 array("Dai",-19212), 
		 array("Dan",-19038), 
		 array("Dang",-19023), 
		 array("Dao",-19018), 
		 array("De",-19006), 
		 array("Deng",-19003), 
		 array("Di",-18996), 
		 array("Dian",-18977), 
		 array("Diao",-18961), 
		 array("Die",-18952), 
		 array("Ding",-18783), 
		 array("Diu",-18774), 
		 array("Dong",-18773), 
		 array("Dou",-18763), 
		 array("Du",-18756), 
		 array("Duan",-18741), 
		 array("Dui",-18735), 
		 array("Dun",-18731), 
		 array("Duo",-18722), 
		 array("E",-18710), 
		 array("En",-18697), 
		 array("Er",-18696), 
		 array("Fa",-18526), 
		 array("Fan",-18518), 
		 array("Fang",-18501), 
		 array("Fei",-18490), 
		 array("Fen",-18478), 
		 array("Feng",-18463), 
		 array("Fo",-18448), 
		 array("Fou",-18447), 
		 array("Fu",-18446), 
		 array("Ga",-18239), 
		 array("Gai",-18237), 
		 array("Gan",-18231), 
		 array("Gang",-18220), 
		 array("Gao",-18211), 
		 array("Ge",-18201), 
		 array("Gei",-18184), 
		 array("Gen",-18183), 
		 array("Geng",-18181), 
		 array("Gong",-18012), 
		 array("Gou",-17997), 
		 array("Gu",-17988), 
		 array("Gua",-17970), 
		 array("Guai",-17964), 
		 array("Guan",-17961), 
		 array("Guang",-17950), 
		 array("Gui",-17947), 
		 array("Gun",-17931), 
		 array("Guo",-17928), 
		 array("Ha",-17922), 
		 array("Hai",-17759), 
		 array("Han",-17752), 
		 array("Hang",-17733), 
		 array("Hao",-17730), 
		 array("He",-17721), 
		 array("Hei",-17703), 
		 array("Hen",-17701), 
		 array("Heng",-17697), 
		 array("Hong",-17692), 
		 array("Hou",-17683), 
		 array("Hu",-17676), 
		 array("Hua",-17496), 
		 array("Huai",-17487), 
		 array("Huan",-17482), 
		 array("Huang",-17468), 
		 array("Hui",-17454), 
		 array("Hun",-17433), 
		 array("Huo",-17427), 
		 array("Ji",-17417), 
		 array("Jia",-17202), 
		 array("Jian",-17185), 
		 array("Jiang",-16983), 
		 array("Jiao",-16970), 
		 array("Jie",-16942), 
		 array("Jin",-16915), 
		 array("Jing",-16733), 
		 array("Jiong",-16708), 
		 array("Jiu",-16706), 
		 array("Ju",-16689), 
		 array("Juan",-16664), 
		 array("Jue",-16657), 
		 array("Jun",-16647), 
		 array("Ka",-16474), 
		 array("Kai",-16470), 
		 array("Kan",-16465), 
		 array("Kang",-16459), 
		 array("Kao",-16452), 
		 array("Ke",-16448), 
		 array("Ken",-16433), 
		 array("Keng",-16429), 
		 array("Kong",-16427), 
		 array("Kou",-16423), 
		 array("Ku",-16419), 
		 array("Kua",-16412), 
		 array("Kuai",-16407), 
		 array("Kuan",-16403), 
		 array("Kuang",-16401), 
		 array("Kui",-16393), 
		 array("Kun",-16220), 
		 array("Kuo",-16216), 
		 array("La",-16212), 
		 array("Lai",-16205), 
		 array("Lan",-16202), 
		 array("Lang",-16187), 
		 array("Lao",-16180), 
		 array("Le",-16171), 
		 array("Lei",-16169), 
		 array("Leng",-16158), 
		 array("Li",-16155), 
		 array("Lia",-15959), 
		 array("Lian",-15958), 
		 array("Liang",-15944), 
		 array("Liao",-15933), 
		 array("Lie",-15920), 
		 array("Lin",-15915), 
		 array("Ling",-15903), 
		 array("Liu",-15889), 
		 array("Long",-15878), 
		 array("Lou",-15707), 
		 array("Lu",-15701), 
		 array("Lv",-15681), 
		 array("Luan",-15667), 
		 array("Lue",-15661), 
		 array("Lun",-15659), 
		 array("Luo",-15652), 
		 array("Ma",-15640), 
		 array("Mai",-15631), 
		 array("Man",-15625), 
		 array("Mang",-15454), 
		 array("Mao",-15448), 
		 array("Me",-15436), 
		 array("Mei",-15435), 
		 array("Men",-15419), 
		 array("Meng",-15416), 
		 array("Mi",-15408), 
		 array("Mian",-15394), 
		 array("Miao",-15385), 
		 array("Mie",-15377), 
		 array("Min",-15375), 
		 array("Ming",-15369), 
		 array("Miu",-15363), 
		 array("Mo",-15362), 
		 array("Mou",-15183), 
		 array("Mu",-15180), 
		 array("Na",-15165), 
		 array("Nai",-15158), 
		 array("Nan",-15153), 
		 array("Nang",-15150), 
		 array("Nao",-15149), 
		 array("Ne",-15144), 
		 array("Nei",-15143), 
		 array("Nen",-15141), 
		 array("Neng",-15140), 
		 array("Ni",-15139), 
		 array("Nian",-15128), 
		 array("Niang",-15121), 
		 array("Niao",-15119), 
		 array("Nie",-15117), 
		 array("Nin",-15110), 
		 array("Ning",-15109), 
		 array("Niu",-14941), 
		 array("Nong",-14937), 
		 array("Nu",-14933), 
		 array("Nv",-14930), 
		 array("Nuan",-14929), 
		 array("Nue",-14928), 
		 array("Nuo",-14926), 
		 array("O",-14922), 
		 array("Ou",-14921), 
		 array("Pa",-14914), 
		 array("Pai",-14908), 
		 array("Pan",-14902), 
		 array("Pang",-14894), 
		 array("Pao",-14889), 
		 array("Pei",-14882), 
		 array("Pen",-14873), 
		 array("Peng",-14871), 
		 array("Pi",-14857), 
		 array("Pian",-14678), 
		 array("Piao",-14674), 
		 array("Pie",-14670), 
		 array("Pin",-14668), 
		 array("Ping",-14663), 
		 array("Po",-14654), 
		 array("Pu",-14645), 
		 array("Qi",-14630), 
		 array("Qia",-14594), 
		 array("Qian",-14429), 
		 array("Qiang",-14407), 
		 array("Qiao",-14399), 
		 array("Qie",-14384), 
		 array("Qin",-14379), 
		 array("Qing",-14368), 
		 array("Qiong",-14355), 
		 array("Qiu",-14353), 
		 array("Qu",-14345), 
		 array("Quan",-14170), 
		 array("Que",-14159), 
		 array("Qun",-14151), 
		 array("Ran",-14149), 
		 array("Rang",-14145), 
		 array("Rao",-14140), 
		 array("Re",-14137), 
		 array("Ren",-14135), 
		 array("Reng",-14125), 
		 array("Ri",-14123), 
		 array("Rong",-14122), 
		 array("Rou",-14112), 
		 array("Ru",-14109), 
		 array("Ruan",-14099), 
		 array("Rui",-14097), 
		 array("Run",-14094), 
		 array("Ruo",-14092), 
		 array("Sa",-14090), 
		 array("Sai",-14087), 
		 array("San",-14083), 
		 array("Sang",-13917), 
		 array("Sao",-13914), 
		 array("Se",-13910), 
		 array("Sen",-13907), 
		 array("Seng",-13906), 
		 array("Sha",-13905), 
		 array("Shai",-13896), 
		 array("Shan",-13894), 
		 array("Shang",-13878), 
		 array("Shao",-13870), 
		 array("She",-13859), 
		 array("Shen",-13847), 
		 array("Sheng",-13831), 
		 array("Shi",-13658), 
		 array("Shou",-13611), 
		 array("Shu",-13601), 
		 array("Shua",-13406), 
		 array("Shuai",-13404), 
		 array("Shuan",-13400), 
		 array("Shuang",-13398), 
		 array("Shui",-13395), 
		 array("Shun",-13391), 
		 array("Shuo",-13387), 
		 array("Si",-13383), 
		 array("Song",-13367), 
		 array("Sou",-13359), 
		 array("Su",-13356), 
		 array("Suan",-13343), 
		 array("Sui",-13340), 
		 array("Sun",-13329), 
		 array("Suo",-13326), 
		 array("Ta",-13318), 
		 array("Tai",-13147), 
		 array("Tan",-13138), 
		 array("Tang",-13120), 
		 array("Tao",-13107), 
		 array("Te",-13096), 
		 array("Teng",-13095), 
		 array("Ti",-13091), 
		 array("Tian",-13076), 
		 array("Tiao",-13068), 
		 array("Tie",-13063), 
		 array("Ting",-13060), 
		 array("Tong",-12888), 
		 array("Tou",-12875), 
		 array("Tu",-12871), 
		 array("Tuan",-12860), 
		 array("Tui",-12858), 
		 array("Tun",-12852), 
		 array("Tuo",-12849), 
		 array("Wa",-12838), 
		 array("Wai",-12831), 
		 array("Wan",-12829), 
		 array("Wang",-12812), 
		 array("Wei",-12802), 
		 array("Wen",-12607), 
		 array("Weng",-12597), 
		 array("Wo",-12594), 
		 array("Wu",-12585), 
		 array("Xi",-12556), 
		 array("Xia",-12359), 
		 array("Xian",-12346), 
		 array("Xiang",-12320), 
		 array("Xiao",-12300), 
		 array("Xie",-12120), 
		 array("Xin",-12099), 
		 array("Xing",-12089), 
		 array("Xiong",-12074), 
		 array("Xiu",-12067), 
		 array("Xu",-12058), 
		 array("Xuan",-12039), 
		 array("Xue",-11867), 
		 array("Xun",-11861), 
		 array("Ya",-11847), 
		 array("Yan",-11831), 
		 array("Yang",-11798), 
		 array("Yao",-11781), 
		 array("Ye",-11604), 
		 array("Yi",-11589), 
		 array("Yin",-11536), 
		 array("Ying",-11358), 
		 array("Yo",-11340), 
		 array("Yong",-11339), 
		 array("You",-11324), 
		 array("Yu",-11303), 
		 array("Yuan",-11097), 
		 array("Yue",-11077), 
		 array("Yun",-11067), 
		 array("Za",-11055), 
		 array("Zai",-11052), 
		 array("Zan",-11045), 
		 array("Zang",-11041), 
		 array("Zao",-11038), 
		 array("Ze",-11024), 
		 array("Zei",-11020), 
		 array("Zen",-11019), 
		 array("Zeng",-11018), 
		 array("Zha",-11014), 
		 array("Zhai",-10838), 
		 array("Zhan",-10832), 
		 array("Zhang",-10815), 
		 array("Zhao",-10800), 
		 array("Zhe",-10790), 
		 array("Zhen",-10780), 
		 array("Zheng",-10764), 
		 array("Zhi",-10587), 
		 array("Zhong",-10544), 
		 array("Zhou",-10533), 
		 array("Zhu",-10519), 
		 array("Zhua",-10331), 
		 array("Zhuai",-10329), 
		 array("Zhuan",-10328), 
		 array("Zhuang",-10322), 
		 array("Zhui",-10315), 
		 array("Zhun",-10309), 
		 array("Zhuo",-10307), 
		 array("Zi",-10296), 
		 array("Zong",-10281), 
		 array("Zou",-10274), 
		 array("Zu",-10270), 
		 array("Zuan",-10262), 
		 array("Zui",-10260), 
		 array("Zun",-10256), 
		 array("Zuo",-10254) 
		 );
function g($num){
	global $d; 
	if($num>0&&$num<160){ 
		return chr($num); 
	} 
	elseif($num<-20319||$num>-10247){ 
		return ""; 
	}else{
		for($i=count($d)-1;$i>=0;$i--){if($d[$i][1]<=$num)break;} 
		return $d[$i][0];
	}
} 

function c($str){ 
	$ret=""; 
	for($i=0;$i<strlen($str);$i++){ 
		$p=ord(substr($str,$i,1)); 
		if($p>160){ 
			$q=ord(substr($str,++$i,1)); 
			$p=$p*256+$q-65536; 
		}
		$ret.=g($p);
	}
	return $ret; 
}

// ------------------------首页调用 MOD BY aqua19-V1.11Build0508修正版----Start---------
function cnSubStr($string, $sublen) {
        if ($sublen >= strlen($string)) {
                return $string;
        } 
        $s = "";
        for($i = 0;$i < ($sublen-2);$i++) {
                if (ord($string{$i}) > 127) {
                        $s .= $string{$i} . $string{++$i};
                        continue;
                } else {
                        $s .= $string{$i};
                        continue;
                } 
        } 
        $s .= "..";
        return $s;
} // End Function cnSubStr($string,$sublen);中文字符截取,这个函数谁写的忘了，拿来用吧.
function showarticle($sortid, $templatename, $main_article , $main_len) {
        global $DB, $db_prefix, $phparticleurl,$g_o_back2root;
        global $style,$filenamemethod;
        $subsortids = getsubsorts_first($sortid);
        $articles = $DB->query("SELECT articleid,title,date,sortid,author,source,contact,description FROM " . $db_prefix . "article 
                             WHERE sortid IN (0" . $subsortids . ") AND visible=1
                            ORDER BY date DESC
                            LIMIT $main_article");

        while ($article = $DB->fetch_array($articles)) {
                $articlehtmllink = HTMLDIR . "/" . mkdirname($article['sortid'],-1,$article['date'],0,0).rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//get_sortdirs($article['sortid']) . "/" . date("Y_m", $article['date']) . "/"
                $title = $article['title'];
                $article['title'] = cnSubStr($article[title], $main_len);
                $article['date'] = padate("m/d", $article[date]);
                if ($article['date'] == date("m/d")) {
                        $datefont = "red";
                } else {
                        $datefont = "normalfont";
                } 
                eval("\$content .= \"" . gettemplate($templatename) . "\";");
        } 
        return $content;
} //一般文章调用函数
function showimgarticle($sortid, $templatename, $img_article, $img_article_len) {
        global $DB, $db_prefix, $phparticleurl,$g_o_back2root;
        global $style,$filenamemethod;
        $subsortids = getsubsorts_first($sortid);
        if ($sortid == 0) {
                $condition = "";
        } else {
                $condition = "sortid IN (0" . $subsortids . ") AND ";
        } 

        $articles = $DB->query("SELECT articleid,title,description,date,imageid,sortid,author,source,contact FROM 

" . $db_prefix . "article 
                             WHERE $condition visible=1 AND imageid!=0
                            ORDER BY date DESC
                            LIMIT $img_article");

        while ($article = $DB->fetch_array($articles)) {
                $articlehtmllink = HTMLDIR . "/" . mkdirname($article['sortid'],-1,$article['date'],0,0).rawurlencode(mkfilename($filenamemethod,$article['title'],1)) . $article['articleid'] . "_1." . HTMLEXT;//get_sortdirs($article['sortid']) . "/" . date("Y_m", $article['date']) . "/"
                $title = $article['title'];
                $article['title'] = cnSubStr($article['title'], $img_article_len);
                $article['description'] = cnSubStr($article['description'], $img_article_len);
                $article['date'] = padate("m/d", $article['date']);
                eval("\$content .= \"" . gettemplate($templatename) . "\";");
        }
        return $content;
} //图片文章调用函数;当$sortid=0时，调用所有图片文章
// ------------------------首页调用 MOD BY aqua19-V1.11Build0508修正版--End-----------

?>