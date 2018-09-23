<?php
require "global.php";

$nogzipoutput = 1;
cpheader();

$cpforms->tableheader();
echo "<td>";
echo "<a href=\"gallery2.php\">刷新</a>";
echo "</td>";
echo "<td align=\"right\">";
echo "<a href=\"gallery.php?action=upload\">上传图片</a>";
echo "</td>";
$cpforms->tablefooter();

   $nav = new buildNav;
   $nav->limit = 10;

   $total = $DB->fetch_one_array("SELECT COUNT(*) AS count FROM ".$db_prefix."gallery");
   $nav->total_result = $total[count];
   if ($total[count]==0) {
       pa_exit("还未有任何图片");
   }
   $nav->execute("SELECT * FROM ".$db_prefix."gallery ORDER BY id DESC");

   echo $nav->pagenav();
   $cpforms->tableheader();

   echo "<tr align=center class=tbhead>
          <td>Id#</td>
          <td>大小</td>
          <td>尺寸(宽x高)px</td>
         </tr>";
   while($image=$DB->fetch_array($nav->sql_result)){

         $imagesize = @getimagesize("../upload/images/$image[filename]");

         $size = get_real_size($image[size]);

         echo "<tr class=".getrowbg()." align=center onmouseover=\"this.style.cursor='hand';\" onClick=\"top.document.forms[0].elements['ImgUrl'].value='$configuration[phparticleurl]/showimg.php?iid=$image[id]';top.document.PREVIEWPIC.src='$configuration[phparticleurl]/showimg.php?iid=$image[id]';\">";
         echo "<td><a href=\"####\" title=\"预览图片: $image[original]\" onClick=\"top.document.forms[0].elements['ImgUrl'].value='$configuration[phparticleurl]/showimg.php?iid=$image[id]';top.document.PREVIEWPIC.src='$configuration[phparticleurl]/showimg.php?iid=$image[id]';\"><img src=\"../showimg.php?iid=$image[id]\" border=\"1\" width=\"50\"></a></td>
               <td>$size</td>
               <td nowrap>$imagesize[0] x $imagesize[1]</td>";
         echo "</tr>";

   }
   $cpforms->tablefooter();
   echo $nav->pagenav();


$cpforms->tableheader();
echo "<td>";
echo "<a href=\"gallery2.php\">刷新</a>";
echo "</td>";
echo "<td align=\"right\">";
echo "<a href=\"gallery.php?action=upload\">上传图片</a>";
echo "</td>";
$cpforms->tablefooter();




cpfooter();
?>