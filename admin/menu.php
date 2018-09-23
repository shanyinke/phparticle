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

function userConfirm()
{
		if (confirm("您确定真的执行操作吗？"))
		{
			return true;
		}else	return false;
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

makenav("系统设置",0,array('基本设置'=>'configurate.php',
                           'PHP 资料'=>'configurate.php?action=phpinfo'
                            ));

//makenav("系统设置",0,array('添加设置'=>'configurate.php?action=addsetting',
//                            '编辑设置'=>'configurate.php?action=edit',
//                           '添加设置组'=>'configurate.php?action=addsettinggroup'
//                            ));


makenav("新闻管理",0,array('发布新闻'=>'news.php?action=add',
                           '编辑新闻'=>'news.php?action=edit',
                            ));

makenav("文章管理",0,array('查看分类'=>'article.php?action=list',
                           '添加文章'=>'article.php?action=add',
                           '审批文章'=>'article.php?action=validate',
                           '最后更新'=>'article.php?action=edit',
                           '查找文章'=>'article.php?action=search',
                           '批量移动'=>'article.php?action=massmove',
                           '批量删除'=>'article.php?action=massdelete'
                            ));

makenav("分类管理",0,array('添加分类'=>'sort.php?action=add',
                           '编辑分类'=>'sort.php?action=edit'
                            ));

makenav("图片管理",0,array('上传图片'=>'gallery.php?action=upload',
                           '查看图片'=>'gallery.php?action=showgallery'
                            ));

makenav("用户管理",1,array('添加新会员'=>'user.php?action=add',
                           '会员列表'=>'user.php?action=edit',
                           '查找会员'=>'user.php?action=search'
                            ));

makenav("用户组管理",1,array('添加组'=>'usergroup.php?action=add',
                           '编辑组'=>'usergroup.php?action=edit'
                            ));
makenav("管理日志",1,array('操作记录'=>'adminlog.php?action=view',
                            '非法登陆记录'=>'loginlog.php?action=view'));
/*
makenav("风格管理",1,array('编辑风格'=>'style.php?action=view',
                           '添加风格'=>'style.php?action=add',
                           '下载风格'=>'style.php?action=download',
                           '上传风格'=>'style.php?action=upload'
                            ));

makenav("变量管理",1,array('添加变量'=>'replacement.php?action=add',
                           '编辑变量'=>'replacement.php?action=edit',
                           '添加变量套系'=>'replacement.php?action=addset'
                            ));

*/
makenav("模板管理",1,array('添加模板'=>'template.php?action=add',
                           '编辑模板'=>'template.php?action=edit',
                           '添加模板套系'=>'template.php?action=addset',
                           '查找模板'=>'template.php?action=search',
                           '替换模板'=>'template.php?action=replace',
                           '模板缓冲'=>'make.php" onclick="return userConfirm()'
                            ));
makenav("数据库选项",1,array('优化数据'=>'database.php?action=optimize',
                           '修复数据'=>'database.php?action=repair',
                           '分卷备份'=>'backup.php',
                           '导入数据'=>'recover.php'
                            ));

makenav("静态页面生成",0,array('分类生成'=>'../htmlauto.php?mod=mksort',
                           '文章生成'=>'../htmlauto.php?mod=mkarticle',
                           '首页生成'=>'renewhomepage.php',
                           '刷新统计'=>'renewcount.php?action=do" onclick="return userConfirm()',
                           '刷新缓冲'=>'renewcache.php?action=do" onclick="return userConfirm()'
                            ));

makenav("论坛静态管理",1,array('系统配置'=>'selectsystem.php',
                           '分类生成'=>'../htmlauto.php?mod=mksort_bbs&type=2',
                           '文章生成'=>'../htmlauto.php?mod=mkarticle_bbs&type=2',
                           '首页生成'=>'renewhomepage.php',
                           '刷新缓冲'=>'renewcache_bbs.php?action=do" onclick="return userConfirm()',
                            ));

makenav("标签管理",1,array('添加删除'=>'maketag.php?tagname=defaultsys',
				'刷新标签'=>'renewtag.php'
                            ));
makenav("友链管理",1,array('添加友链'=>'index2.php?mod=link&action=add',
'编辑友链'=>'index2.php?mod=link&action=edit',
'审批友链'=>'index2.php?mod=link&action=validate"'
));
makenav("评论管理",1,array('评论管理'=>'comment.php?action=list'
));
makenav("其他工具",0,array('自动生成'=>'../autotest.html" target="_blank',
				'论坛整合'=>'passport.php'
                            ));
makenav("管理员选项",1,array('修改密码'=>'user.php?action=modpassword',
                           '退出登陆'=>'user.php?action=logout'
                            ));

echo "
</table>";
cpfooter();
?>