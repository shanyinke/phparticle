2.2.0功能改动：
1、分类页号倒排问题解决。
2、phparticle 与 discuz!整合完成。
3、phparticle seo 做了进一步优化。
2.1.0功能改动：
这次更新,主要是更换了编译型模版.从此模版脱离了数据库,速度有了很大程度的提高.
采用了div+css结构的模版。
模版简介：
这个是一个原创的模版。.html后缀的为模版文件。所有的htm修改，在.htm操作就可以了。
html分公共的，和私有的。
.htm模版里面的所有变量格式:
<?=\$parameter?>
模版标签类型：
{T_IMPORT_SRC 引入代码文件名字/}  这个是指示他所在的模版所要引用的程序代码

{T_CODE_BLOCK 代码模块名字/}  这个是程序模块标签

{T_TEMPLATE 模版名字/}  这个就是模版。

{T_CHTML_BLOCK 混合模块名字
<!-- BEGIN 子模版名字 -->
子模版html代码
<!-- END 子模版名字 -->
/}

{T_HTML_BLOCK 子模版名字/} 这里说到的子模版名字，就是“混合模版”里面定义的子模版.对于子模版，里面的变量定义，必须使用 \$variable 方式定义，里面的所有双引号，需要前面加上转义符\
例子代码
<a href=\"http://www.utspeed.com\">\$test</a>

模版特别说明：
T_CHTML_BLOCK 和 T_HTML_BLOCK是配合使用的，前者放在.html模版里面，后者放在.import里面的代码模块里面
.htm文件里面可以放置上面，除了T_HTML_BLOCK以外的所有标签。
.import为模块化后的代码程序,以下面的格式区分每个代码模块.然后，用标签T_CODE_BLOCK 或者 T_CHTML_BLOCK放到.htm文件里面。
<!-- BEGIN 代码模块名字 -->
<?
代码
?>
<! END  代码模块名字 -->

新功能:
删除文章或分类后,数据库和静态文件同步删除
数据库分卷备份与多文件同时恢复.
友情链接
后台评论管理
后台文章审核,可以批量操作.
标签可以使用在文章页,和分类页.

修正的bug:
发表好评论后，返回主题，却出现拉HTTP 404 - 未找到文件
http://bbs.utspeed.com/index.php?mod=readthread&forumid=11&threadid=4008&page=1&#reply15787
MYSQL5有乱码，希望斑竹能否测一下。(这个需要在数据库连接后加一条语句,又需要的可以到论坛来问)
http://bbs.utspeed.com/index.php?mod=readthread&forumid=13&threadid=3855&page=1&#reply16260
支持phpwind有问题。
http://bbs.utspeed.com/index.php?mod=readthread&forumid=11&threadid=4144&page=1&#reply16366
分类文章数统计不正确
http://bbs.utspeed.com/index.php?mod=readthread&forumid=11&threadid=4239
session的bug
http://bbs.utspeed.com/index.php?mod=readthread&forumid=11&threadid=4318

2.0.1 seo 功能改动：
1、支持英文站优化
2、支持yahoo优化
3、支持百度优化

2.0.1 special静态功能简介：
1）支持完全静态生成。包括文章页面、分类页面。
2）静态生成速度经过3次优化，已经接近于最优了。通过4W条笑话的网站的生成测试，速度相当快。
(http://article.utspeed.com)
3）文章统计自动更新。
4）首页文章调用已经整合，可以调用图片和文字文章。
5）静态生成后，会自动记录生成点。添加新文章后，可以重上次生成点继续声称，而不需要全部重新生成。（文章和分类是这样），极大了减少了重复动作。
6）就第5）点而言，一般的静态系统，都很难避免这个问题，而我们解决了，而且保持不同时间生成的文件之间的连贯性与同步。
7）添加文章后，可以方便的对添加文章进行生成。分类也一样。
8）可以后台修改静态生成的


   ┏━━━━━━━━━━━━━━━━━━━━━┓
   ┃             源 码 爱 好 者               ┃
   ┣━━━━━━━━━━━━━━━━━━━━━┫
   ┃                                          ┃
   ┃           提供源码发布与下载             ┃
   ┃                                          ┃
   ┃        http://www.codefans.net           ┃
   ┃                                          ┃
   ┃            互助、分享、提高              ┃
   ┗━━━━━━━━━━━━━━━━━━━━━┛