<?php
require "global.php";
cpheader();
?>
<script language=JavaScript type=text/javascript>
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
echo "
<table width=\"100%\" border=\"0\" cellspacing=\"4\" cellpadding=\"1\">
 <tr>
  <td align=\"center\">
   <b><a href=\"index.php?action=main\" target=content>phpArticle<br>Control Panel</a></b>
  </td>
 </tr>
</table>
<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\">";

makenav("新闻管理",0,array('发布新闻'=>'news.php?action=add',
                           '编辑新闻'=>'news.php?action=edit',
                            ));

makenav("文章管理",0,array('查看分类'=>'article.php?action=list',
                           '添加文章'=>'article.php?action=add',
                           '审批文章'=>'article.php?action=validate'
                            ));


makenav("图片管理",0,array('上传图片'=>'gallery.php?action=upload',
                           '查看图片'=>'gallery.php?action=showgallery'
                            ));

makenav("静态页面生成",0,array('分类生成'=>'index2.php?mod=mksort',
                           '文章生成'=>'index2.php?mod=mkarticle'
                            ));

makenav("管理员选项",0,array('退出登陆'=>'user.php?action=logout'));

echo "
</table>";
cpfooter();
?>