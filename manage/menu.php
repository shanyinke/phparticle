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

makenav("���Ź���",0,array('��������'=>'news.php?action=add',
                           '�༭����'=>'news.php?action=edit',
                            ));

makenav("���¹���",0,array('�鿴����'=>'article.php?action=list',
                           '�������'=>'article.php?action=add',
                           '��������'=>'article.php?action=validate'
                            ));


makenav("ͼƬ����",0,array('�ϴ�ͼƬ'=>'gallery.php?action=upload',
                           '�鿴ͼƬ'=>'gallery.php?action=showgallery'
                            ));

makenav("��̬ҳ������",0,array('��������'=>'index2.php?mod=mksort',
                           '��������'=>'index2.php?mod=mkarticle'
                            ));

makenav("����Աѡ��",0,array('�˳���½'=>'user.php?action=logout'));

echo "
</table>";
cpfooter();
?>