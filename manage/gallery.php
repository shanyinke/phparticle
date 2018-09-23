<?php
error_reporting(7);
require "global.php";

cpheader();

function validate_imageid($imageid) {

         global $DB,$db_prefix,$imageid;
         $imageid = intval($imageid);
         $image = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."gallery WHERE id='$imageid'");
         if (empty($image)) {
             pa_exit("��ͼƬ������");
         }
         return $image;

}

if ($action==edit) {

    $image = validate_imageid($imageid);

    if ($image[userid]!=$pauserinfo[userid]) {
        show_nopermission();
    }

    $cpforms->tableheader();
    $cpforms->makecategory("��ͼԤ��");
    echo "<tr class=".getrowbg().">
           <td><img src=\"../showimg.php?iid=$imageid\" border=0></td>
          </tr>";
    $cpforms->tablefooter();
    echo "<br>";

    $cpforms->formheader(array('title'=>"�༭ͼƬ: $image[original]",'enctype'=>'multipart/form-data'));

    $cpforms->makehidden(array('name'=>'imageid','value'=>$imageid));
    $cpforms->makehidden(array('name'=>'action','value'=>'update'));

    $cpforms->makefile(array('text'=>'�ϴ���ͼƬ:','name'=>'newimage'));
    $cpforms->formfooter();

}


if ($_POST[action]==update) {

    $image = validate_imageid($imageid);

    if ($image[userid]!=$pauserinfo[userid]) {
        show_nopermission();
    }

    unlink("../upload/images/$image[filename]");

    $newimage = $_POST['newimage'][tmp_name];
    $filename = md5(uniqid(microtime(),1));
    copy($newimage,"../upload/images/$filename");

    $DB->query("UPDATE ".$db_prefix."gallery SET
                         original='".addslashes($newimage_name)."',
                         filename ='$filename',type='$newimage_type',
                         size='$newimage_size',
                         dateline='".time()."',
                         userid='$pauserinfo[userid]'
                         WHERE id='$imageid'");


    redirect("./gallery.php?action=showgallery","��ͼƬ�Ѹ���");

}


if ($action==kill) {

    $image = validate_imageid($imageid);

    if ($image[userid]!=$pauserinfo[userid]) {
        show_nopermission();
    }

    $cpforms->formheader(array('title'=>'ȷʵҪɾ����ͼƬ?'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'remove'));
    $cpforms->makehidden(array('name'=>'imageid',
                                'value'=>$imageid));

    $cpforms->formfooter(array('confirm'=>1));


}


if($_POST[action]==remove){

   $image = validate_imageid($imageid);

    if ($image[userid]!=$pauserinfo[userid]) {
        show_nopermission();
    }

   unlink("../upload/images/$image[filename]");

   $DB->query("DELETE FROM ".$db_prefix."gallery WHERE id='$imageid'");

   redirect("./gallery.php?action=showgallery","��ͼƬ��ɾ��");

}



if ($action=="seldelete"){


    if (empty($images)) {
        pa_exit("��δѡ���κ�Ҫɾ����ͼƬ");
    }


    $cpforms->formheader(array('title'=>'ȷʵҪɾ������ѡ�е�ͼƬ?'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'doseldelete'));

    if (is_array($images)) {
        foreach($images as $key => $value){
                $cpforms->makehidden(array('name'=>'images[]',
                                            'value'=>$value));
        }
    }
    $cpforms->formfooter(array('confirm'=>1));

}



if ($_POST[action]=="doseldelete") {

    if (empty($images)) {
        pa_exit("��δѡ���κ�Ҫɾ����ͼƬ");
    }
    if (is_array($images)) {
        foreach($images as $key => $value){
                $image = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."gallery WHERE id='$value' AND userid='$pauserinfo[userid]'");
                unlink("../upload/images/$image[filename]");
                $DB->query("DELETE FROM ".$db_prefix."gallery WHERE id='$value' AND userid='$pauserinfo[userid]'");
        }
    }

    redirect("./gallery.php?action=showgallery","��ѡ�е�ͼƬ����ɾ��");

}



if ($action==showgallery OR !isset($action)) {

   $nav = new buildNav;
   $nav->limit = 9;

   $total = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."gallery WHERE userid='$pauserinfo[userid]'");
   $nav->total_result = $total[count];

   if ($total[count]==0) {
       pa_exit("��δ���κ�ͼƬ");
   }
   $nav->execute("SELECT * FROM ".$db_prefix."gallery WHERE userid='$pauserinfo[userid]' ORDER BY ID DESC");

   echo $nav->pagenav();

   $cpforms->formheader(array('title'=>'phpArticle ͼ��',
                               'colspan'=>3));
   $counter = 0;
   while($image = $DB->fetch_array($nav->sql_result)){

         $imagesize = @getimagesize("../upload/images/$image[filename]");

         $size = get_real_size($image[size]);

         if ($counter==0) {
             echo "<tr class=".getrowbg()." align=\"center\">";
         }

         echo "<td valign=\"bottom\">";
         echo "<a href=\"../showimg.php?iid=$image[id]\" target=\"_blank\">";
         if ($imagesize[0]>150) {
             echo "<img src=\"../showimg.php?iid=$image[id]\" alt=\"ȫ�����\" border=1 width=150>";
         } else {
             echo "<img src=\"../showimg.php?iid=$image[id]\" alt=\"ȫ�����\" border=1>";
         }
         echo "</a>";
         echo "<br>��С: $size
               <br>�ߴ�:$imagesize[0] x $imagesize[1]
               <br>
               [<a href=\"gallery.php?action=edit&imageid=$image[id]\" title=\"�ϴ���ͼƬ\">�༭</a>]
               [<a href=\"gallery.php?action=kill&imageid=$image[id]\" title=\"ɾ����ͼƬ\">ɾ��</a>]
               <input type=\"checkbox\" name=\"images[]\" value=\"$image[id]\" title=\"ɾ����ͼƬ\">\n";

         echo "</td>\n";
         $counter++;
         if ($counter%3==0) {
             echo "</tr>";
             $counter = 0;
         }
   }
   if ($counter!=0) {
       for (;$counter<3;$counter++) {
            echo "<td></td>\n";
       }
   }
   echo "</tr>\n";
   $cpforms->makehidden(array('name'=>'action','value'=>'seldelete'));
   $cpforms->formfooter(array('colspan'=>3));
   echo $nav->pagenav();

}



if($_POST[action]==doupload){

    //print_rr($_POST);
    //print_rr($_POST);
    $uploaded = 0;
    $unuploaded = 0;
    for ($key=0;$key<10;$key++) {

         $original = $_POST['image']['name'][$key];
         $filename = md5(uniqid(microtime(),1));

         //echo $_POST['image']['tmp_name'][$key];

         if (!empty($original)) {

                 $result[$key] ="<tr class=".getrowbg().">
                              <td>$original</td>
                              <td>".get_real_size($_POST['image']['size'][$key])."</td>
                              <td>".$_POST['image']['type'][$key]."</td>
                              <td>";
                 if (($_POST['image']['type'][$key]=="image/pjpeg" OR $_POST['image']['type'][$key]=="image/gif" OR $_POST['image']['type'][$key]=="image/x-png") AND copy($_POST['image']['tmp_name'][$key], "../upload/images/$filename")) {

                     $DB->query("INSERT INTO ".$db_prefix."gallery (original,filename,type,size,dateline,userid)
                                        VALUES ('".addslashes(trim($original))."','$filename','".addslashes($_POST['image']['type'][$key])."','".addslashes($_POST['image']['size'][$key])."','".time()."','$pauserinfo[userid]')");

                     $result[$key] .= "�ɹ�";
                     $uploaded++;
                  } else {
                     $result[$key] .= "<font color=red>ʧ��</font>";
                     $unuploaded++;
                  }

                  $result[$key] .= "</td></tr>";


          } //end  if(!empty($original)){

    } // end for
    if (empty($result)) {
       pa_exit("��δѡ���κ�Ҫ�ϴ���ͼƬ");
    }
    $cpforms->tableheader();
    echo "<tr class=tbhead>
                   <td>�ļ���</td>
                   <td>��С</td>
                   <td>����</td>
                   <td>�ϴ����</td>
          </tr>
          ";
    foreach ($result AS $key=>$value) {
             echo "$value";
    }
    echo "<tr class=tbhead>
                   <td colspan=4>���ϴ� ".($uploaded+$unuploaded).", �ɹ�: $uploaded, ʧ��: $unuploaded</td>
          </tr>
          ";
    $cpforms->tablefooter();

    echo "<p align=center>[<a href=\"gallery2.php\">���ز���ͼƬ</a>] [<a href=\"gallery.php\">�鿴ͼ��</a>] [<a href=\"gallery.php?action=upload\">�����ϴ�ͼƬ</a>] </p>";

}



if($action==upload){

   $cpforms->formheader(array('title'=>'�ϴ�ͼƬ,�����ĵ���,����Ҫ�ظ��ύ','enctype'=>'multipart/form-data'));
   $cpforms->makehidden(array('name'=>'action',
                               'value'=>'doupload'));

   for($i=0;$i<10;){
       $cpforms->makefile(array('text'=>"Image ".++$i."#",'name'=>'image[]'));
   }
   $cpforms->formfooter();

}

cpfooter();
?>