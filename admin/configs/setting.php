<?php
/*  settinggroup 基本设置  */
    /*  setting 版本号  */
$configuration[version] = "2.1";
    /*  setting 首页地址  */
$configuration[phparticleurl] = "http://localhost/phparticlexyz";
    /*  setting 网站标题  */
$configuration[phparticletitle] = "phparticle3.0";
    /*  setting 主页地址  */
$configuration[homepage] = "http://localhost";
    /*  setting 管理员Email地址  */
$configuration[webmastermail] = "admin@aksky.com";
/*  settinggroup 模板设置  */
    /*  setting 是否显示模板注释?  */
$configuration[showcomment] = "0";
/*  settinggroup 首页显示设置  */
    /*  setting 分类文章调用数量  */
$configuration[main_article] = "10";
    /*  setting 文章调用标题长度  */
$configuration[main_len] = "20";
    /*  setting 图片文章长度  */
$configuration[img_article_len] = "20";
    /*  setting 图片文章数量  */
$configuration[img_article] = "5";
    /*  setting 是否显示最近更新文章列表(详细)?  */
$configuration[showrecentarticle] = "1";
    /*  setting 显示多少篇最近更新的文章?  */
$configuration[recentarticlenum] = "10";
    /*  setting 分多少列显示最近更新的文章?  */
$configuration[recentarticledivision] = "2";
    /*  setting 是否显示热门文章排行(按评分排行)?  */
$configuration[showratearticle] = "1";
    /*  setting 显示多少篇?  */
$configuration[ratearticlenum] = "10";
    /*  setting 是否显示热门文章列表(按点击排行)?  */
$configuration[showhotarticle] = "1";
    /*  setting 显示多少篇?  */
$configuration[hotarticlenum] = "10";
    /*  setting 是否显示最后更新文章列表(简单)?  */
$configuration[showlastupdate] = "1";
    /*  setting 显示多少篇?  */
$configuration[lastupdatenum] = "10";
/*  settinggroup 文章设置  */
    /*  setting 是否显示评分结果?  */
$configuration[showrating] = "1";
/*  settinggroup 评论设置  */
    /*  setting 评论标题最大长度(单位:字符)?  */
$configuration[comment_title_limit] = "50";
    /*  setting 评论内容最大长度(单位:字符)?  */
$configuration[comment_message_limit] = "400";
/*  settinggroup 会员选项设置  */
    /*  setting 是否允许新会员注册?  */
$configuration[allowregister] = "1";
    /*  setting 是否需要通过email验证会员身份?  */
$configuration[require_activation] = "0";
    /*  setting 会员名的最小长度(单位:字符)?  */
$configuration[username_length_min] = "4";
    /*  setting 会员名的最大长度(单位:字符)?  */
$configuration[username_length_max] = "15";
    /*  setting 密码的最小长度(单位:字符)?  */
$configuration[password_length_min] = "4";
    /*  setting 密码的最大长度(单位:字符)?  */
$configuration[password_length_max] = "15";
    /*  setting 最多可以收藏多少篇文章?  */
$configuration[favoritelimit] = "100";
/*  settinggroup 搜索选项设置  */
    /*  setting 每页显示多少个搜索结果?  */
$configuration[searchperpage] = "10";
/*  settinggroup 服务器设置  */
    /*  setting 是否使用Gzip压缩页面?  */
$configuration[gzipoutput] = "0";
    /*  setting 页面压缩的级别  */
$configuration[gziplevel] = "3";
    /*  setting 服务器所在的时区  */
$configuration[timezone] = "8";
/*  settinggroup 时间显示格式设置  */
    /*  setting 新闻时间格式  */
$configuration[dateformat_news] = "Y-m-d h:i";
    /*  setting 文章日期格式  */
$configuration[dateformat_article] = "Y-m-d";
    /*  setting 文章时间格式  */
$configuration[timeformat_article] = "h:i A";
/*  settinggroup 静态生成设置  */
    /*  setting 静态页存放目录  */
$configuration[htmldir] = "htmldata";
    /*  setting 静态文件后缀名  */
$configuration[htmlfileext] = "html";
    /*  setting 静态文件名生成方法  */
$configuration[filenamemethod] = "2";
    /*  setting 文章静态页默认前缀  */
$configuration[articleprefix] = "article_";
    /*  setting 分类静态页前缀  */
$configuration[sortprefix] = "sort_";
    /*  setting 使用日期作为目录  */
$configuration[usedate] = "1";
    /*  setting 使用分类名字作为目录  */
$configuration[usename] = "1";
    /*  setting 单一子目录  */
$configuration[singledir] = "2";
?>