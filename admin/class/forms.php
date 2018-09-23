<?php
error_reporting(7);

class FORMS {

      function formheader($arguments=array()) {

               global $HTTP_SERVER_VARS;
               if ($arguments[enctype]){
                   $enctype="enctype=\"$arguments[enctype]\"";
               } else {
                   $enctype="";
               }
               if (!isset($arguments[method])) {
                   $arguments[method] = "post";
               }
               if (!isset($arguments[action])) {
                   $arguments[action] = $HTTP_SERVER_VARS[PHP_SELF];
               }

               if (!$arguments[colspan]) {
                   $arguments[colspan] = 2;
               }


               echo "<table width=\"100%\" align=\"center\" border=\"0\" cellspacing=\"1\" cellpadding=\"4\" class=\"tableoutline\">\n";
               echo "<form action=\"$arguments[action]\" $enctype method=\"$arguments[method]\" name=\"$arguments[name]\" $arguments[extra]>\n";
               if ($arguments[title]!="") {
                   echo "<tr id=\"cat\">
                          <td class=\"tbhead\" colspan=\"$arguments[colspan]\">
                          <b> $arguments[title] </b>
                          </td>
                         </tr>\n";
               }

      }

      function formfooter($arguments=array()){

               echo "<tr class=\"tbhead\">\n";

               if ($arguments[confirm]==1) {

                   //$arguments[colspan] = 1;

                   $arguments[button][submit][type] = "submit";
                   $arguments[button][submit][name] = "submit";
                   $arguments[button][submit][value] = "确认";
                   $arguments[button][submit][accesskey] = "y";

                   $arguments[button][back][type] = "button";
                   $arguments[button][back][value] = "取消";
                   $arguments[button][back][accesskey] = "r";
                   $arguments[button][back][extra] = " onclick=\"history.back(1)\" ";

               } elseif (empty($arguments[button])) {

                   $arguments[button][submit][type] = "submit";
                   $arguments[button][submit][name] = "submit";
                   $arguments[button][submit][value] = "提交";
                   $arguments[button][submit][accesskey] = "y";

                   $arguments[button][reset][type] = "reset";
                   $arguments[button][reset][value] = "重置";
                   $arguments[button][reset][accesskey] = "r";

               }

               if ($arguments[nextpage]==1) {

                   $arguments[button][nextpage][type] = "submit";
                   $arguments[button][nextpage][name] = "nextpage";
                   $arguments[button][nextpage][value] = "继续添加下一页";
                   $arguments[button][nextpage][accesskey] = "n";

               }


               if (empty($arguments[colspan])) {
                   $arguments[colspan] = 2;
               }


               //echo "<pre>";
               //print_r($arguments);
               //echo "</pre>";
               echo "<td colspan=\"$arguments[colspan]\" align=\"center\">\n";
               if (isset($arguments) AND is_array($arguments)) {
                   foreach ($arguments[button] AS $k=>$button) {
                            if (empty($button[type])) {
                                $button[type] = "submit";
                            }
                            echo " <input class=\"bginput\" accesskey=\"$button[accesskey]\" type=\"$button[type]\" name=\"$button[name]\" value=\" $button[value] \" $button[extra]> \n";
                   }
               }
               echo "</td>
                     </tr>\n";
               echo "</form>\n";
               echo "</table>\n";

      }

      function tableheader() {
               echo "<table width=\"100%\" align=\"center\" border=\"0\" cellspacing=\"1\" cellpadding=\"4\" class=\"tableoutline\">\n";
      }

      function tablefooter() {
               echo "</table>\n";
      }

      function tableseparate() {

               $this->tablefooter();
               echo "<br>\n";
               $this->tableheader();

      }

      function makecategory($arguments = array()) {

               if (!is_array($arguments)) {
                   $title = $arguments;
               } else {
                   $title = $arguments[title];
               }
               if (is_array($arguments) && $arguments[separate]==1  ) {
                   $this->tableseparate();
               }

               echo "<tr class=\"tbcat\" id=\"cat\">
                       <td colspan=\"2\">".htmlspecialchars($title)."</td>
                     </tr>\n";

      }

      function maketd($arguments = array()) {

               echo "<tr ".$this->getrowbg()." nowrap>";
               foreach ($arguments AS $k=>$v) {
                        echo "<td>$v</td>";
               }
               echo "</tr>\n";

      }

      function makeinput($arguments = array()) {

               if (empty($arguments[size])) {
                   $arguments[size] = 35;
               }
               if (empty($arguments[maxlength])) {
                   $arguments[maxlength] = 50;
               }
               if ($arguments[html]) {
                   $arguments[value] = htmlspecialchars($arguments[value]);
               }
               if (!empty($arguments[css])) {
                   $class = "class=\"$arguments[css]\"";
               }

               if (empty($arguments[type])) {
                   $arguments[type] = "text";
               }
               echo "<tr ".$this->getrowbg()." nowrap>
                      <td width=\"50%\">$arguments[text]</td>
                       <td>
                         <input $class type=\"$arguments[type]\" name=\"$arguments[name]\" size=\"$arguments[size]\" maxlength=\"$arguments[maxlength]\" value=\"$arguments[value]\" $arguments[extra]>\n
                       </td>
                     </tr>\n";

      }

      function makecolorinput($arguments = array()) {

               if (empty($arguments[size])) {
                   $arguments[size] = 35;
               }
               if (empty($arguments[maxlength])) {
                   $arguments[maxlength] = 50;
               }
               if (empty($arguments[nohtml])) {
                   $arguments[value] = htmlspecialchars($arguments[value]);
               }
               if (!empty($arguments[css])) {
                   $class = "class=\"$arguments[css]\"";
               }
               $arguments[size] = "7";
               $arguments[maxlength] = "7";
                                                                    //this.form.$arguments[name].style.backgroundColor=this.value;
               echo "<tr ".$this->getrowbg()." nowrap>
                      <td width=\"50%\">$arguments[text]</td>
                       <td>
                         <input onchange=\"this.style.backgroundColor=this.value;\" $class type=\"text\" name=\"$arguments[name]\" size=\"$arguments[size]\" maxlength=\"$arguments[maxlength]\" value=\"$arguments[value]\">\n
                         <input style=\"background-color:$arguments[value];\" name=\"$arguments[name]\" type=\"button\"  value=\"          \" disable>
                       </td>
                     </tr>\n";

      }

      function makefile($arguments = array()) {

               echo "<tr ".$this->getrowbg()." nowrap>
                      <td width=\"50%\">$arguments[text]</td>
                       <td>
                         <input type=\"file\" name=\"$arguments[name]\" $arguments[extra]>\n
                       </td>
                     </tr>\n";

      }

      function maketextarea($arguments = array()){

               // $text,$name,$value="",$cols=40,$rows=7,$extra=""
               if (empty($arguments[cols])) {
                   $arguments[cols] = 40;
               }
               if (empty($arguments[rows])) {
                   $arguments[rows] = 7;
               }
               if (!empty($arguments[html])) {
                   $arguments[value] = htmlspecialchars($arguments[value]);
               }

               echo "<tr ".$this->getrowbg()." nowrap>
                     <td width=\"50%\" valign=\"top\">$arguments[text]</td>
                     <td>
                       <textarea type=\"text\" name=\"$arguments[name]\" cols=\"$arguments[cols]\" rows=\"$arguments[rows]\" $arguments[extra]>$arguments[value]</textarea>
                     </td>
                   </tr>\n";

      }

      function inithtmlarea() {
?>
<script language="Javascript1.2">
<!-- // load htmlarea
_editor_url = "../htmlarea/";                     // URL to htmlarea files
var win_ie_ver = parseFloat(navigator.appVersion.split("MSIE")[1]);
if (navigator.userAgent.indexOf('Mac')        >= 0) { win_ie_ver = 0; }
if (navigator.userAgent.indexOf('Windows CE') >= 0) { win_ie_ver = 0; }
if (navigator.userAgent.indexOf('Opera')      >= 0) { win_ie_ver = 0; }
if (win_ie_ver >= 5.5) {
 document.write('<scr' + 'ipt src="' +_editor_url+ 'editor.js"');
 document.write(' language="Javascript1.2"></scr' + 'ipt>');
} else { document.write('<scr'+'ipt>function editor_generate() { return false; }</scr'+'ipt>'); }
// -->
</script>
<?php
      }
      function makehtmlarea($arguments = array()) {

               $this->maketextarea(array('text'=>$arguments[text],
                                         'name'=>$arguments[name],
                                         'value'=>$arguments[value],
                                         'html'=>1,
                                         'cols'=>100,
                                         'rows'=>20
                                         ));
               if (empty($arguments[width])) {
                   $arguments[width] = 500;
               }
               if (empty($arguments[height])) {
                   $arguments[height] = 250;
               }

?>
<script language="JavaScript1.2" defer>
var config = new Object(); // create new config object

config.width = "<?php echo $arguments[width];?>";
config.height = "<?php echo $arguments[height];?>";

config.debug = 0;

editor_generate('<?php echo $arguments[name];?>',config);

</script>
<?php

      }

      function makeorderinput($arguments = array()) {

               if (empty($arguments[text])) {
                   $arguments[text] = "排序:";
               }
               if (empty($arguments[name])) {
                   $arguments[name] = "displayorder";
               }

               $this->makeinput(array('text'=>$arguments[text],
                                      'name'=>$arguments[name],
                                      'value'=>$arguments[value],
                                      'size'=>3,
                                      'maxlength'=>3
                                      ));

      }

      function makeselect($arguments = array()){

               if ($arguments[html]==1) {
                   $value = htmlspecialchars($value);
               }
               if ($arguments[multiple]==1) {
                   $multiple = " multiple";
                   if ($arguments[size]>0) {
                       $size = "size=$arguments[size]";
                   }
               }
               if($arguments['onchange'])
               {
               	$onchange = "onchange=\"".$arguments['onchange']."\"";
        	}

               echo "<tr ".$this->getrowbg().">
                      <td width=\"50%\" valign=\"top\">$arguments[text]</td>
                      <td>
                      <select name=\"$arguments[name]\" $onchange $multiple $size>\n";
               if (is_array($arguments[option])) {

                   foreach ($arguments[option] AS $key=>$value) {
                            if (!is_array($arguments[selected])) {
                                if ($arguments[selected]==$key) {
                                    echo "<option value=\"$key\" selected class=\"{$arguments[css][$key]}\">$value</option>\n";
                                } else {
                                    echo "<option value=\"$key\" class=\"{$arguments[css][$key]}\">$value</option>\n";
                                }

                            } elseif (is_array($arguments[selected])) {

                                if ($arguments[selected]["$key"]==1) {
                                    echo "<option value=\"$key\" selected class=\"{$arguments[css][$key]}\">$value</option>\n";
                                } else {
                                    echo "<option value=\"$key\" class=\"{$arguments[css][$key]}\">$value</option>\n";
                                }
                            }
                   }
               }

               echo "</select>\n";
               echo "</td>
                     </tr>\n";

      }

      function makeyesno($arguments = array()) {

               $arguments[option] = array('1'=>'是','0'=>'否');
               $this->makeselect($arguments);

      }
      function makesex($arguments = array()) {

               $arguments[option] = array('unknow'=>'不明','male'=>'男','female'=>'女');
               $this->makeselect($arguments);

      }


      function getstyles($arguments = array()) {

               global $DB,$db_prefix,$debug;

               if ($debug) {
                   $option[-1] = "Global Style";
               }

               $styles = $DB->query("SELECT * FROM ".$db_prefix."style ORDER BY styleid");
               while ($style = $DB->fetch_array($styles)) {
                      $option[$style[styleid]] = $style[title];
               }
               $this->makeselect(array('text'=>$arguments[text],
                                       'name'=>$arguments[name],
                                       'selected'=>$arguments[selected],
                                       'option'=>$option));

      }

      function getstylefiles($arguments = array()) {
               if ($handle = opendir("./style")) {
                   while (false !== ($file = readdir($handle))) {
                          if ($file != "." && $file != ".." && ereg(".style",$file)) {
                              $option[$file] = $file;
                          }
                   }
               closedir($handle);
               }
               $this->makeselect(array('text'=>$arguments[text],
                                       'name'=>$arguments[name],
                                       'selected'=>$arguments[selected],
                                       'option'=>$option));
      }

      function gettemplatesets($arguments = array()) {

               global $DB,$db_prefix,$debug;
               if ($debug) {
                   $option[-1] = "Global Templateset";
               }

               $templatesets = $DB->query("SELECT * FROM ".$db_prefix."templateset ORDER BY templatesetid");
               while ($templateset = $DB->fetch_array($templatesets)) {
                      $option[$templateset[templatesetid]] = $templateset[title];
               }
               $this->makeselect(array('text'=>$arguments[text],
                                       'name'=>$arguments[name],
                                       'selected'=>$arguments[selected],
                                       'option'=>$option));
      }

      function getreplacementsets($arguments = array()) {

               global $DB,$db_prefix,$debug;
               if ($debug) {
                   $option[-1] = "Global Replacementset";
               }
               $replacements = $DB->query("SELECT * FROM ".$db_prefix."replacementset ORDER BY replacementsetid");
               while ($replacement = $DB->fetch_array($replacements)){
                      $option[$replacement[replacementsetid]] = $replacement[title];
               }
               $this->makeselect(array('text'=>$arguments[text],
                                       'name'=>$arguments[name],
                                       'selected'=>$arguments[selected],
                                       'option'=>$option));

      }

      function getsettinggroups($arguments = array()) {

               global $DB,$db_prefix;
               $settinggroups = $DB->query("SELECT * FROM ".$db_prefix."settinggroup ORDER BY displayorder");
               while ($settinggroup = $DB->fetch_array($settinggroups)){
                      $option[$settinggroup[settinggroupid]] = $settinggroup[title];
               }
               $this->makeselect(array('text'=>$arguments[text],
                                       'name'=>$arguments[name],
                                       'selected'=>$arguments[selected],
                                       'option'=>$option));

      }

      function makehidden($arguments = array()){

               echo "<input type=\"hidden\" name=\"$arguments[name]\" value=\"$arguments[value]\">\n";

      }

      function makelink($arguments = array()) {

               if (empty($arguments[target])) {
                   $target = "";
               } else {
                   $target = " target=\"$arguments[target]\" ";
               }
               echo "<a href=\"$arguments[url]\"$target$argument[extra]>$text</a>\n";

      }

      function getrowbg() {

               global $bgcounter;
               if ($bgcounter++%2==0) {
                   return "class=\"firstalt\"";
               } else {
                   return "class=\"secondalt\"";
               }

      }

      function getusergroups($arguments = array()) {

               global $DB,$db_prefix;

               if (!empty($arguments[extra])) {
                   foreach ($arguments[extra] AS $key=>$value) {
                            $option[$key] = $value;
                   }
               }
               $usergroups = $DB->query("SELECT * FROM ".$db_prefix."usergroup ORDER BY usergroupid,binary title");
               while ($usergroup = $DB->fetch_array($usergroups)) {
                      $option[$usergroup[usergroupid]] = $usergroup[title];
               }

               $this->makeselect(array('text'=>$arguments[text],
                                       'name'=>$arguments[name],
                                       'selected'=>$arguments[selected],
                                       'option'=>$option));

      }
      /*
      function getsorts($arguments = array()) {

               global $DB,$db_prefix;

               $sorts = $DB->query("SELECT * FROM ".$db_prefix."sort WHERE status=0 AND parentid=-1 ORDER BY displayorder,binary title");
               while ($sort = $DB->fetch_array($sorts)) {
                      $option[$sort[sortid]] = $sort[title];
               }

               $this->makeselect(array('text'=>$arguments[text],
                                       'name'=>$arguments[name],
                                       'selected'=>$arguments[selected],
                                       'option'=>$option));

      }
      */
      var $cachesorts = array();

      function cachesorts() {

               global $DB,$db_prefix;
               $sorts = $DB->query("SELECT * FROM ".$db_prefix."sort ORDER BY displayorder,binary title,sortid ASC");
               while ($sort = $DB->fetch_array($sorts)) {
                      $this->cachesorts[$sort[parentid]][$sort[sortid]] = $sort;
               }
               $DB->free_result($sorts);
               return true;

      }


      var $option = array();
      var $css = array();

      function getsortlistbit($sortid="-1",$level=1) {

               if (isset($this->cachesorts[$sortid])) {
                   foreach($this->cachesorts[$sortid] AS $key => $sort){
                           if ($level==1) {
                               $this->css[$sort[sortid]] = "option_sort";
                           }
                           if (!isset($this->filter[$key])) {
                               $this->option[$sort[sortid]] = str_repeat("--",$level-1)." $sort[title]";
                           }
                           $this->getsortlistbit($sort[sortid],$level+1);
                   }
               }

      }


      var $filter = array();
      function getsortlist($arguments = array()) {

               if (empty($this->cachesorts)) {
                   $this->cachesorts();
               }

               if (!empty($arguments[extra])) {
                   foreach ($arguments[extra] AS $key=>$value) {
                            $this->option[$key] = $value;
                   }
               }
               if (!empty($arguments[filter])) {
                   $this->filter = $arguments[filter];
               }


               $this->getsortlistbit();
               //echo "<pre>";
               //print_r($this->option);
               //echo "</pre>";

               $this->makeselect(array('text'=>$arguments[text],
                                       'name'=>$arguments[name],
                                       'selected'=>$arguments[selected],
                                       'option'=>$this->option,
                                       'css'=>$this->css));


      }

      function getmanagers($arguments = array()) {

               global $DB,$db_prefix;
               $managers = $DB->query("SELECT manager.managerid,user.username,user.userid FROM ".$db_prefix."manager as manager
                                          LEFT JOIN ".$db_prefix."user as user
                                          on manager.userid=user.userid
                                          WHERE manager.sortid='$arguments[sortid]'");
               if ($DB->num_rows($managers)==0) {
                   $option[0] = "该分类还没有任何管理员";
               } else {
                   while ($manager = $DB->fetch_array($managers)) {
                          $option[$manager[managerid]] = $manager[username];
                   }
               }

               $this->makeselect(array('text'=>$arguments[text],
                                       'name'=>$arguments[name],
                                       'selected'=>$arguments[selected],
                                       'option'=>$option));
      }
      function cpheader($extraheader="",$extraheader1=""){
         global $configuration,$nogzipoutput;
         //if (!$nogzipoutput) {
         //    @ob_start("ob_gzhandler");
         //}
         echo "
<html>
<head>
<title> $configuration[phparticletitle] Powered By phpArticle $configuration[version] </title>
<meta content=\"text/html; charset=utf-8\" http-equiv=\"Content-Type\">
<meta http-equiv=\"Pragma\" content=\"no-cache\">
<meta http-equiv=\"Cache-Control\" content=\"no-cache\">
<meta http-equiv=\"Expires\" content=\"-1\">
<link rel=\"stylesheet\" href=\"admin/cp.css\" type=\"text/css\">
".$extraheader."
</head>
<body leftmargin=\"10\" topmargin=\"10\" marginwidth=\"10\" marginheight=\"10\" ".$extraheader1.">\n";

}


function cpfooter(){

         global $showqueries,$DB,$configuration;
         echo "\n<br>\n<center>Powered by: <a href=\"http://www.phparticle.cn\" target=\"_blank\">phpArticle</a> Version $configuration[version]</center>\n";
         echo "</body>\n</html>";
	exit;
}
}

?>