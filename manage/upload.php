<?php
/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
  +------------------------------------------------------------+
  | Filename.......: admin/upload.php                          |
  | Project........: phpArticle                                |
  | Version........: 1.1.0                                     |
  | Last Modified..: 2002-09-09                                |
  +------------------------------------------------------------+
  | Author.........: Hyeo <heizes@21cn.com>                    |
  | Homepage.......: http://www.phparticle.cn                       |
  | Support........: http://www.phparticle.cn/forum                 |
  +------------------------------------------------------------+
  | Copyright (C) 2002 phpArticle Team. All rights reserved.   |
  +------------------------------------------------------------+
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */
error_reporting(7);
require "global.php";


if($action==showgallery){
$header .="
<script language=\"JavaScript\">
function insertImage(ImageId) {
         opener.iView.focus();
         opener.iView.document.execCommand('insertimage', false, '$configuration[phparticleurl]/showimg.php?iid='+ImageId+'\" border=0');
}
</script>";
}

cpheader("$header");

echo "<center>";

/* -=-=-=-=-=-=-=-=-=-=-=-=-
    start do upload
-=-=-=-=-=-=-=-=-=-=-=-=- */
if($_POST[action]==doupload){

   if(isset($attachment)){

      for($key=0;$key<10;$key++){

          $original = $_FILES[attachment][name][$key];
          $filename = md5(uniqid(microtime(),1));

          if(!empty($original)){
              if($attachment_type[$key]=="image/pjpeg" or $attachment_type[$key]=="image/gif"){

                  copy($_FILES['attachment']['tmp_name'][$key], "../upload/images/$filename");
                  $DB->query("INSERT INTO ".$db_prefix."gallery (original,filename,type,size,dateline)
                                 VALUES ('$original','$filename','$attachment_type[$key]','$attachment_size[$key]','".time()."')");

                  echo "上传成功,文件:$original<br>";
              }else{
                  echo "上传失败,类型不正确,文件:".$attachment_name[$key]."<br>";
              }

          } //end  if(!empty($original)){
      } // end for

   }
   echo "<p><a href=./upload.php?action=upload>返回继续上传其它图片</a></p>";
   echo "<p><a href=./gallery.php?action=showgallery>返回查看已上传的图片</a></p>";

}

/* -=-=-=-=-=-=-=-=-=-=-=-=-
       start upload
-=-=-=-=-=-=-=-=-=-=-=-=- */
if($action==upload){

   formheader("upload.php",1);
   tableheader("上传图片,只允许上传类型为\"(gif,jpg,jpeg)\"");
   makehidden(action,doupload);
   for($i=0;$i<10;){
       makeupload("image ".++$i." #");
   }
   formfooter("上传");
   echo "<p><a href=./gallery.php?action=showgallery>返回查看已上传的图片</a></p>";
}
echo "<p>[<a href=\"javascript:self.close()\">关闭这个窗口</a>]</p>";
echo "</center>";
cpfooter();
?>