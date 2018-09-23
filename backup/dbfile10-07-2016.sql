DROP TABLE IF EXISTS pa_adminlog;
CREATE TABLE pa_adminlog (
   `adminlogid` int(10) unsigned NOT NULL auto_increment,
   `userid` int(3) unsigned NOT NULL,
   `action` varchar(50),
   `script` varchar(255) NOT NULL,
   `date` int(10) unsigned NOT NULL,
   `ipaddress` varchar(16),
   PRIMARY KEY (adminlogid),
   KEY `userid` (userid),
   KEY `date` (date)
);

INSERT INTO pa_adminlog VALUES('1', '1', 'top', 'http://localhost/phparticle22/admin/index.php?action=top', '1475768149', '::1');
INSERT INTO pa_adminlog VALUES('2', '1', 'main', 'http://localhost/phparticle22/admin/index.php?action=main', '1475768149', '::1');
INSERT INTO pa_adminlog VALUES('3', '1', 'phpinfo', 'http://localhost/phparticle22/admin/configurate.php?action=phpinfo', '1475768154', '::1');
INSERT INTO pa_adminlog VALUES('4', '1', 'add', 'http://localhost/phparticle22/admin/news.php?action=add', '1475768156', '::1');
INSERT INTO pa_adminlog VALUES('5', '1', 'edit', 'http://localhost/phparticle22/admin/news.php?action=edit', '1475768158', '::1');
INSERT INTO pa_adminlog VALUES('6', '1', 'add', 'http://localhost/phparticle22/admin/article.php?action=add', '1475768161', '::1');
INSERT INTO pa_adminlog VALUES('7', '1', 'list', 'http://localhost/phparticle22/admin/article.php?action=list', '1475768162', '::1');
INSERT INTO pa_adminlog VALUES('8', '1', 'add', 'http://localhost/phparticle22/admin/article.php?action=add', '1475768163', '::1');
INSERT INTO pa_adminlog VALUES('9', '1', 'validate', 'http://localhost/phparticle22/admin/article.php?action=validate', '1475768163', '::1');
INSERT INTO pa_adminlog VALUES('10', '1', 'edit', 'http://localhost/phparticle22/admin/article.php?action=edit', '1475768164', '::1');
INSERT INTO pa_adminlog VALUES('11', '1', 'search', 'http://localhost/phparticle22/admin/article.php?action=search', '1475768165', '::1');
INSERT INTO pa_adminlog VALUES('12', '1', 'massmove', 'http://localhost/phparticle22/admin/article.php?action=massmove', '1475768166', '::1');
INSERT INTO pa_adminlog VALUES('13', '1', 'massdelete', 'http://localhost/phparticle22/admin/article.php?action=massdelete', '1475768167', '::1');
INSERT INTO pa_adminlog VALUES('14', '1', 'add', 'http://localhost/phparticle22/admin/sort.php?action=add', '1475768169', '::1');
INSERT INTO pa_adminlog VALUES('15', '1', 'edit', 'http://localhost/phparticle22/admin/sort.php?action=edit', '1475768170', '::1');
INSERT INTO pa_adminlog VALUES('16', '1', 'upload', 'http://localhost/phparticle22/admin/gallery.php?action=upload', '1475768171', '::1');
INSERT INTO pa_adminlog VALUES('17', '1', 'showgallery', 'http://localhost/phparticle22/admin/gallery.php?action=showgallery', '1475768171', '::1');
INSERT INTO pa_adminlog VALUES('18', '1', 'phpinfo', 'http://localhost/phparticle22/admin/configurate.php?action=phpinfo', '1475768214', '::1');
INSERT INTO pa_adminlog VALUES('19', '1', 'phpinfo', 'http://localhost/phparticle22/admin/configurate.php?action=phpinfo', '1475768217', '::1');
INSERT INTO pa_adminlog VALUES('20', '1', 'phpinfo', 'http://localhost/phparticle22/admin/configurate.php?action=phpinfo', '1475768223', '::1');
INSERT INTO pa_adminlog VALUES('21', '1', 'top', 'http://localhost/phparticle22/admin/index.php?action=top', '1475768942', '::1');
INSERT INTO pa_adminlog VALUES('22', '1', 'main', 'http://localhost/phparticle22/admin/index.php?action=main', '1475768942', '::1');
INSERT INTO pa_adminlog VALUES('23', '1', 'main', 'http://localhost/phparticle22/admin/index.php?action=main', '1475768959', '::1');
INSERT INTO pa_adminlog VALUES('24', '1', 'top', 'http://localhost/phparticle22/admin/index.php?action=top', '1475768959', '::1');
INSERT INTO pa_adminlog VALUES('25', '1', 'top', 'http://localhost/phparticle22/admin/index.php?action=top', '1475768962', '::1');
INSERT INTO pa_adminlog VALUES('26', '1', 'main', 'http://localhost/phparticle22/admin/index.php?action=main', '1475768962', '::1');
INSERT INTO pa_adminlog VALUES('27', '1', 'main', 'http://localhost/phparticle22/admin/index.php?action=main', '1475768972', '::1');
INSERT INTO pa_adminlog VALUES('28', '1', 'top', 'http://localhost/phparticle22/admin/index.php?action=top', '1475768972', '::1');
INSERT INTO pa_adminlog VALUES('29', '1', 'top', 'http://localhost/phparticle22/admin/index.php?action=top', '1475768973', '::1');
INSERT INTO pa_adminlog VALUES('30', '1', 'main', 'http://localhost/phparticle22/admin/index.php?action=main', '1475768973', '::1');
INSERT INTO pa_adminlog VALUES('31', '1', 'top', 'http://localhost/phparticle22/admin/index.php?action=top', '1475768973', '::1');
INSERT INTO pa_adminlog VALUES('32', '1', 'main', 'http://localhost/phparticle22/admin/index.php?action=main', '1475768973', '::1');
INSERT INTO pa_adminlog VALUES('33', '1', 'top', 'http://localhost/phparticle22/admin/index.php?action=top', '1475768976', '::1');
INSERT INTO pa_adminlog VALUES('34', '1', 'main', 'http://localhost/phparticle22/admin/index.php?action=main', '1475768976', '::1');
INSERT INTO pa_adminlog VALUES('35', '1', 'top', 'http://localhost/phparticle22/admin/index.php?action=top', '1475768982', '::1');
INSERT INTO pa_adminlog VALUES('36', '1', 'phpinfo', 'http://localhost/phparticle22/admin/configurate.php?action=phpinfo', '1475768983', '::1');
INSERT INTO pa_adminlog VALUES('37', '1', 'phpinfo', 'http://localhost/phparticle22/admin/configurate.php?action=phpinfo', '1475768983', '::1');
INSERT INTO pa_adminlog VALUES('38', '1', 'showgallery', 'http://localhost/phparticle22/admin/gallery.php?action=showgallery', '1475768984', '::1');
INSERT INTO pa_adminlog VALUES('39', '1', 'upload', 'http://localhost/phparticle22/admin/gallery.php?action=upload', '1475768984', '::1');
INSERT INTO pa_adminlog VALUES('40', '1', 'edit', 'http://localhost/phparticle22/admin/sort.php?action=edit', '1475768984', '::1');
INSERT INTO pa_adminlog VALUES('41', '1', 'add', 'http://localhost/phparticle22/admin/sort.php?action=add', '1475768984', '::1');
INSERT INTO pa_adminlog VALUES('42', '1', 'massdelete', 'http://localhost/phparticle22/admin/article.php?action=massdelete', '1475768984', '::1');
INSERT INTO pa_adminlog VALUES('43', '1', 'massmove', 'http://localhost/phparticle22/admin/article.php?action=massmove', '1475768985', '::1');
INSERT INTO pa_adminlog VALUES('44', '1', 'search', 'http://localhost/phparticle22/admin/article.php?action=search', '1475768985', '::1');
INSERT INTO pa_adminlog VALUES('45', '1', 'edit', 'http://localhost/phparticle22/admin/article.php?action=edit', '1475768985', '::1');
INSERT INTO pa_adminlog VALUES('46', '1', 'validate', 'http://localhost/phparticle22/admin/article.php?action=validate', '1475768985', '::1');
INSERT INTO pa_adminlog VALUES('47', '1', 'add', 'http://localhost/phparticle22/admin/article.php?action=add', '1475768985', '::1');
INSERT INTO pa_adminlog VALUES('48', '1', 'list', 'http://localhost/phparticle22/admin/article.php?action=list', '1475768986', '::1');
INSERT INTO pa_adminlog VALUES('49', '1', 'add', 'http://localhost/phparticle22/admin/article.php?action=add', '1475768986', '::1');
INSERT INTO pa_adminlog VALUES('50', '1', 'edit', 'http://localhost/phparticle22/admin/news.php?action=edit', '1475768986', '::1');
INSERT INTO pa_adminlog VALUES('51', '1', 'phpinfo', 'http://localhost/phparticle22/admin/configurate.php?action=phpinfo', '1475768987', '::1');
INSERT INTO pa_adminlog VALUES('52', '1', 'main', 'http://localhost/phparticle22/admin/index.php?action=main', '1475768987', '::1');
INSERT INTO pa_adminlog VALUES('53', '1', 'main', 'http://localhost/phparticle22/admin/index.php?action=main', '1475768988', '::1');
INSERT INTO pa_adminlog VALUES('54', '1', 'main', 'http://localhost/phparticle22/admin/index.php?action=main', '1475768989', '::1');
INSERT INTO pa_adminlog VALUES('55', '1', 'logout', 'http://localhost/phparticle22/admin/user.php?action=logout', '1475769004', '::1');
INSERT INTO pa_adminlog VALUES('56', '1', 'top', 'http://localhost/phparticle22/admin/index.php?action=top', '1475769015', '::1');
INSERT INTO pa_adminlog VALUES('57', '1', 'main', 'http://localhost/phparticle22/admin/index.php?action=main', '1475769015', '::1');
INSERT INTO pa_adminlog VALUES('58', '1', 'main', 'http://localhost/phparticle22/admin/index.php?action=main', '1475769122', '::1');
INSERT INTO pa_adminlog VALUES('59', '1', 'top', 'http://localhost/phparticle22/admin/index.php?action=top', '1475769122', '::1');
INSERT INTO pa_adminlog VALUES('60', '1', 'logout', 'http://localhost/phparticle22/admin/user.php?action=logout', '1475769125', '::1');
INSERT INTO pa_adminlog VALUES('61', '1', 'top', 'http://localhost/phparticle22/admin/index.php?action=top', '1475769160', '::1');
INSERT INTO pa_adminlog VALUES('62', '1', 'main', 'http://localhost/phparticle22/admin/index.php?action=main', '1475769160', '::1');
INSERT INTO pa_adminlog VALUES('63', '1', 'logout', 'http://localhost/phparticle22/admin/user.php?action=logout', '1475769162', '::1');
INSERT INTO pa_adminlog VALUES('64', '1', 'top', 'http://localhost/phparticle22/admin/index.php?action=top', '1475770228', '::1');
INSERT INTO pa_adminlog VALUES('65', '1', 'main', 'http://localhost/phparticle22/admin/index.php?action=main', '1475770228', '::1');
INSERT INTO pa_adminlog VALUES('66', '1', 'main', 'http://localhost/phparticle22/admin/index.php?action=main', '1475770229', '::1');
INSERT INTO pa_adminlog VALUES('67', '1', 'top', 'http://localhost/phparticle22/admin/index.php?action=top', '1475770229', '::1');
INSERT INTO pa_adminlog VALUES('68', '1', 'top', 'http://localhost/phparticle22/admin/index.php?action=top', '1475770250', '::1');
INSERT INTO pa_adminlog VALUES('69', '1', 'main', 'http://localhost/phparticle22/admin/index.php?action=main', '1475770250', '::1');
INSERT INTO pa_adminlog VALUES('70', '1', 'phpinfo', 'http://localhost/phparticle22/admin/configurate.php?action=phpinfo', '1475770252', '::1');
INSERT INTO pa_adminlog VALUES('71', '1', 'add', 'http://localhost/phparticle22/admin/news.php?action=add', '1475770254', '::1');
INSERT INTO pa_adminlog VALUES('72', '1', 'edit', 'http://localhost/phparticle22/admin/news.php?action=edit', '1475770254', '::1');
INSERT INTO pa_adminlog VALUES('73', '1', 'top', 'http://localhost/phparticle22/admin/index.php?action=top', '1475770271', '::1');
INSERT INTO pa_adminlog VALUES('74', '1', 'main', 'http://localhost/phparticle22/admin/index.php?action=main', '1475770271', '::1');
INSERT INTO pa_adminlog VALUES('75', '1', 'phpinfo', 'http://localhost/phparticle22/admin/configurate.php?action=phpinfo', '1475770304', '::1');
INSERT INTO pa_adminlog VALUES('76', '1', 'phpinfo', 'http://localhost/phparticle22/admin/configurate.php?action=phpinfo', '1475770307', '::1');
INSERT INTO pa_adminlog VALUES('77', '1', 'edit', 'http://localhost/phparticle22/admin/article.php?action=edit', '1475770500', '::1');
INSERT INTO pa_adminlog VALUES('78', '1', 'add', 'http://localhost/phparticle22/admin/article.php?action=add', '1475770501', '::1');
INSERT INTO pa_adminlog VALUES('79', '1', 'list', 'http://localhost/phparticle22/admin/article.php?action=list', '1475770502', '::1');
INSERT INTO pa_adminlog VALUES('80', '1', 'edit', 'http://localhost/phparticle22/admin/news.php?action=edit', '1475770503', '::1');
INSERT INTO pa_adminlog VALUES('81', '1', 'update', 'http://localhost/phparticle22/admin/configurate.php', '1475770519', '::1');
INSERT INTO pa_adminlog VALUES('82', '1', 'list', 'http://localhost/phparticle22/admin/comment.php?action=list', '1475770588', '::1');
INSERT INTO pa_adminlog VALUES('83', '1', 'optimize', 'http://localhost/phparticle22/admin/database.php?action=optimize', '1475770629', '::1');
INSERT INTO pa_adminlog VALUES('84', '1', 'dooptimize', 'http://localhost/phparticle22/admin/database.php?action=optimize', '1475770631', '::1');
INSERT INTO pa_adminlog VALUES('85', '1', 'top', 'http://localhost/phparticle22/admin/index.php?action=top', '1475772507', '::1');
INSERT INTO pa_adminlog VALUES('86', '1', 'main', 'http://localhost/phparticle22/admin/index.php?action=main', '1475772507', '::1');
INSERT INTO pa_adminlog VALUES('87', '1', 'optimize', 'http://localhost/phparticle22/admin/database.php?action=optimize', '1475772519', '::1');
INSERT INTO pa_adminlog VALUES('88', '1', 'dooptimize', 'http://localhost/phparticle22/admin/database.php?action=optimize', '1475772522', '::1');
INSERT INTO pa_adminlog VALUES('89', '1', 'dooptimize', 'http://localhost/phparticle22/admin/database.php?action=optimize', '1475772525', '::1');
INSERT INTO pa_adminlog VALUES('90', '1', 'repair', 'http://localhost/phparticle22/admin/database.php?action=repair', '1475772529', '::1');
INSERT INTO pa_adminlog VALUES('91', '1', 'repair', 'http://localhost/phparticle22/admin/database.php?action=repair', '1475772908', '::1');
INSERT INTO pa_adminlog VALUES('92', '1', 'optimize', 'http://localhost/phparticle22/admin/database.php?action=optimize', '1475772946', '::1');
INSERT INTO pa_adminlog VALUES('93', '1', 'repair', 'http://localhost/phparticle22/admin/database.php?action=repair', '1475772948', '::1');



DROP TABLE IF EXISTS pa_article;
CREATE TABLE pa_article (
   `articleid` int(10) unsigned NOT NULL auto_increment,
   `sortid` int(10) unsigned NOT NULL,
   `title` varchar(100) NOT NULL,
   `source` varchar(50),
   `author` varchar(50),
   `contact` varchar(50),
   `description` text NOT NULL,
   `views` int(10) unsigned NOT NULL,
   `date` int(10) unsigned NOT NULL,
   `totalscore` int(10) unsigned NOT NULL,
   `voters` int(10) unsigned NOT NULL,
   `imageid` int(10) unsigned NOT NULL,
   `lastupdate` int(10),
   `editor` varchar(50),
   `userid` int(10) unsigned NOT NULL,
   `visible` tinyint(1) unsigned DEFAULT '1' NOT NULL,
   `keyword` varchar(100),
   `comments` int(10) unsigned NOT NULL,
   `highlight` tinyint(1) NOT NULL,
   `expiry` int(10) NOT NULL,
   PRIMARY KEY (articleid),
   KEY `date` (date),
   KEY `userid` (userid),
   KEY `title` (title),
   KEY `views` (views),
   KEY `visible` (visible),
   KEY `sortid` (sortid),
   KEY `lastupdate` (lastupdate)
);




DROP TABLE IF EXISTS pa_articlerate;
CREATE TABLE pa_articlerate (
   `articlerateid` int(10) unsigned NOT NULL auto_increment,
   `articleid` int(10) unsigned NOT NULL,
   `userid` int(10) unsigned NOT NULL,
   `date` int(10) unsigned NOT NULL,
   `vote` tinyint(2) unsigned NOT NULL,
   `reason` mediumtext,
   `ip` varchar(255) NOT NULL,
   PRIMARY KEY (articlerateid),
   KEY `userid` (userid),
   KEY `articleid` (articleid),
   KEY `ip` (ip)
);




DROP TABLE IF EXISTS pa_articletext;
CREATE TABLE pa_articletext (
   `id` int(10) unsigned NOT NULL auto_increment,
   `subhead` varchar(100) NOT NULL,
   `articleid` int(10) unsigned NOT NULL,
   `articletext` text NOT NULL,
   `displayorder` tinyint(3) DEFAULT '1' NOT NULL,
   PRIMARY KEY (id),
   KEY `articleid` (articleid),
   KEY `displayorder` (displayorder)
);




DROP TABLE IF EXISTS pa_cache;
CREATE TABLE pa_cache (
   `name` varchar(50) NOT NULL,
   `content` longtext,
   `expiry` tinyint(1) unsigned DEFAULT '1' NOT NULL,
   PRIMARY KEY (name)
);

INSERT INTO pa_cache VALUES('subsort', 'N;', '0');
INSERT INTO pa_cache VALUES('parentsort', 'N;', '0');
INSERT INTO pa_cache VALUES('template_1_sortlist', '\r\n<!-- BEGIN sortlistbit_level1 -->\r\n    <div class=\'textad\'>\r\n      <div class=\'textadleft\'><a href=\'./\'></a></div>\r\n      <div class=\'textadright\'>\r\n<div id=\'adlist\'>\r\n<ul>\r\n<!-- BEGIN sortlistbit_level2 -->\r\n<!-- BEGIN sortlistbit_level3 -->\r\n<li><a href= rel=\'external\'></a></li>\r\n<!-- END sortlistbit_level3 -->\r\n<!-- END sortlistbit_level2 -->\r\n</ul>\r\n</div>\r\n      </div>\r\n      </div>\r\n<div class=\'mainline\'>&nbsp;</div>\r\n<!-- END sortlistbit_level1 -->\r\n', '0');



DROP TABLE IF EXISTS pa_cache_bbs;
CREATE TABLE pa_cache_bbs (
   `name` varchar(50) NOT NULL,
   `content` longtext,
   `expiry` tinyint(1) unsigned DEFAULT '1' NOT NULL,
   PRIMARY KEY (name)
);




DROP TABLE IF EXISTS pa_cjcollects;
CREATE TABLE pa_cjcollects (
   `id` int(10) NOT NULL auto_increment,
   `datarawrule` mediumtext NOT NULL,
   `datarule` mediumtext NOT NULL,
   `siteid` int(10) NOT NULL,
   `pid` int(10) NOT NULL,
   `firstmatchnum` int(4) NOT NULL,
   `filterrule` mediumtext,
   `weblink` tinyint(1) NOT NULL,
   `rtype` int(4) NOT NULL,
   `istitle` tinyint(1) NOT NULL,
   `startfield` int(4) NOT NULL,
   `fields` int(4) NOT NULL,
   `savename` varchar(32) NOT NULL,
   `name` varchar(32) NOT NULL,
   PRIMARY KEY (id),
   KEY `siteid` (siteid),
   KEY `savename` (savename),
   KEY `pid` (pid)
);




DROP TABLE IF EXISTS pa_cjgroups;
CREATE TABLE pa_cjgroups (
   `id` int(10) unsigned NOT NULL auto_increment,
   `name` varchar(32) NOT NULL,
   `website` varchar(255) NOT NULL,
   `valid` smallint(1) NOT NULL,
   PRIMARY KEY (id),
   KEY `name` (name),
   KEY `website` (website),
   KEY `valid` (valid)
);




DROP TABLE IF EXISTS pa_cjpreg;
CREATE TABLE pa_cjpreg (
   `name` varchar(255) NOT NULL,
   `pregstr` varchar(255) NOT NULL,
   `disorder` int(4) NOT NULL,
   KEY `disorder` (disorder)
);

INSERT INTO pa_cjpreg VALUES('#?á??±ê??#', '([^\r\n]*?)', '0');
INSERT INTO pa_cjpreg VALUES('#?á??????#', '(.*?)', '0');
INSERT INTO pa_cjpreg VALUES('#?á????×?#', '([0-9]*?)', '0');
INSERT INTO pa_cjpreg VALUES('#?á??×???#', '([a-zA-Z]*?)', '0');
INSERT INTO pa_cjpreg VALUES('#??×?????#', '[0-9]*?', '0');
INSERT INTO pa_cjpreg VALUES('#×???????#', '[a-zA-Z]*?', '0');
INSERT INTO pa_cjpreg VALUES('#??????×?·?????#', '[s]*?', '0');



DROP TABLE IF EXISTS pa_cjrelations;
CREATE TABLE pa_cjrelations (
   `id` int(10) NOT NULL auto_increment,
   `gid` int(10) NOT NULL,
   `sid` int(10) NOT NULL,
   `tgtable` varchar(32) NOT NULL,
   `fname` varchar(255) NOT NULL,
   `fvalue` varchar(32) NOT NULL,
   `ftype` tinyint(1) NOT NULL,
   `isarraydata` tinyint(2) NOT NULL,
   `istitle` tinyint(1) NOT NULL,
   `reltable` varchar(32) NOT NULL,
   `relequalfield` varchar(32) NOT NULL,
   `relistitle` tinyint(1) NOT NULL,
   `relresultfield` varchar(32) NOT NULL,
   `testdup` tinyint(2) NOT NULL,
   PRIMARY KEY (id),
   KEY `gid` (gid,sid),
   KEY `tgtable` (tgtable),
   KEY `fvalue` (fvalue),
   KEY `testdup` (testdup)
);




DROP TABLE IF EXISTS pa_cjsites;
CREATE TABLE pa_cjsites (
   `id` int(10) NOT NULL auto_increment,
   `name` varchar(255) NOT NULL,
   `rawurlrule` varchar(255) NOT NULL,
   `urlrule` mediumtext NOT NULL,
   `weblink` tinyint(1) NOT NULL,
   `gid` int(10) NOT NULL,
   `pid` int(4) NOT NULL,
   `did` int(10) NOT NULL,
   `fields` int(2) NOT NULL,
   `level` int(2) DEFAULT '-1',
   PRIMARY KEY (id),
   KEY `groupid` (gid),
   KEY `fields` (fields),
   KEY `level` (level)
);




DROP TABLE IF EXISTS pa_cjsnn;
CREATE TABLE pa_cjsnn (
   `id` int(10) NOT NULL auto_increment,
   `nickname` varchar(255) NOT NULL,
   `sortid` int(10) NOT NULL,
   `gid` int(10) NOT NULL,
   PRIMARY KEY (id),
   KEY `nickname` (nickname,sortid),
   KEY `gid` (gid)
);




DROP TABLE IF EXISTS pa_cjstatenn;
CREATE TABLE pa_cjstatenn (
   `id` int(10) NOT NULL auto_increment,
   `nickname` varchar(255) NOT NULL,
   `statevalue` int(10) NOT NULL,
   `gid` int(10) NOT NULL,
   PRIMARY KEY (id),
   KEY `nickname` (nickname,statevalue),
   KEY `gid` (gid)
);




DROP TABLE IF EXISTS pa_comment;
CREATE TABLE pa_comment (
   `commentid` int(10) unsigned NOT NULL auto_increment,
   `articleid` int(10) unsigned NOT NULL,
   `title` varchar(100) NOT NULL,
   `author` varchar(50) NOT NULL,
   `userid` int(10) unsigned NOT NULL,
   `date` int(10) unsigned NOT NULL,
   `views` int(10) NOT NULL,
   `replies` int(10) unsigned NOT NULL,
   `lastupdate` int(10) unsigned NOT NULL,
   `lastreplier` varchar(50) NOT NULL,
   PRIMARY KEY (commentid),
   KEY `articleid` (articleid,userid,date,views,replies,lastupdate)
);




DROP TABLE IF EXISTS pa_favorite;
CREATE TABLE pa_favorite (
   `favoriteid` smallint(5) unsigned NOT NULL auto_increment,
   `userid` int(10) unsigned NOT NULL,
   `articleid` int(10) unsigned NOT NULL,
   `adddate` int(10) unsigned NOT NULL,
   PRIMARY KEY (favoriteid)
);




DROP TABLE IF EXISTS pa_friendlink;
CREATE TABLE pa_friendlink (
   `id` tinyint(3) unsigned NOT NULL auto_increment,
   `displayorder` tinyint(3) NOT NULL,
   `sitename` varchar(100) NOT NULL,
   `note` varchar(200) NOT NULL,
   `siteurl` varchar(100) NOT NULL,
   `isimg` tinyint(1) NOT NULL,
   `logourl` varchar(100) NOT NULL,
   `visible` tinyint(1) unsigned DEFAULT '1' NOT NULL,
   `editor` varchar(50) NOT NULL,
   `request` text NOT NULL,
   `reason` text NOT NULL,
   `jointime` int(10) unsigned NOT NULL,
   PRIMARY KEY (id),
   KEY `displayorder` (displayorder)
);

INSERT INTO pa_friendlink VALUES('1', '0', 'PAHTML官方', 'phpArticle 官方站', 'http://www.phparticle.net', '0', 'http://', '1', 'niuboy', '尊敬的 niuboy 站长:', '', '1150480937');



DROP TABLE IF EXISTS pa_gallery;
CREATE TABLE pa_gallery (
   `id` smallint(5) unsigned NOT NULL auto_increment,
   `original` varchar(100) NOT NULL,
   `filename` varchar(50) NOT NULL,
   `type` varchar(50) NOT NULL,
   `size` smallint(5) unsigned NOT NULL,
   `dateline` varchar(50) NOT NULL,
   `userid` int(10) unsigned NOT NULL,
   PRIMARY KEY (id)
);




DROP TABLE IF EXISTS pa_htmllog;
CREATE TABLE pa_htmllog (
   `htmllogid` int(10) unsigned NOT NULL auto_increment,
   `type` mediumint(5) unsigned NOT NULL,
   `dateline` int(10) unsigned NOT NULL,
   `startid` int(10) unsigned NOT NULL,
   `pagenum` int(10) unsigned NOT NULL,
   PRIMARY KEY (htmllogid),
   KEY `startid` (startid),
   KEY `dateline` (dateline)
);




DROP TABLE IF EXISTS pa_htmllog_bbs;
CREATE TABLE pa_htmllog_bbs (
   `htmllogid` int(10) unsigned NOT NULL auto_increment,
   `type` mediumint(5) unsigned NOT NULL,
   `bbs` varchar(50) NOT NULL,
   `dateline` int(10) unsigned NOT NULL,
   `startid` int(10) unsigned NOT NULL,
   `pagenum` int(10) unsigned NOT NULL,
   PRIMARY KEY (htmllogid),
   KEY `bbs` (bbs),
   KEY `startid` (startid),
   KEY `dateline` (dateline)
);




DROP TABLE IF EXISTS pa_kwords;
CREATE TABLE pa_kwords (
   `word` varchar(255),
   `aid` int(10) NOT NULL,
   `weight` float NOT NULL,
   KEY `word` (word,aid,weight)
);




DROP TABLE IF EXISTS pa_kwordsstat;
CREATE TABLE pa_kwordsstat (
   `word` varchar(255) NOT NULL,
   `cnt` int(10) NOT NULL,
   `isvalid` tinyint(2) DEFAULT '1' NOT NULL,
   PRIMARY KEY (word),
   KEY `isvalid` (isvalid)
);




DROP TABLE IF EXISTS pa_loginlog;
CREATE TABLE pa_loginlog (
   `loginlogid` int(10) unsigned NOT NULL auto_increment,
   `username` varchar(100),
   `password` varchar(100),
   `date` int(10) unsigned NOT NULL,
   `ipaddress` varchar(16) NOT NULL,
   `extra` text,
   PRIMARY KEY (loginlogid)
);

INSERT INTO pa_loginlog VALUES('1', 'admin', 'ADMIN6', '1475772473', '::1', 'Referer: http://localhost/phparticle22/admin/database.php?action=repair\nScript: http://localhost/phparticle22/admin/index.php');
INSERT INTO pa_loginlog VALUES('2', 'admin', 'ADMIN6', '1475772498', '::1', 'Referer: http://localhost/phparticle22/admin/database.php?action=repair\nScript: http://localhost/phparticle22/admin/index.php');
INSERT INTO pa_loginlog VALUES('3', 'admin', 'ADMIN6', '1475772500', '::1', 'Referer: http://localhost/phparticle22/admin/database.php?action=repair\nScript: http://localhost/phparticle22/admin/index.php');



DROP TABLE IF EXISTS pa_manager;
CREATE TABLE pa_manager (
   `managerid` smallint(5) unsigned NOT NULL auto_increment,
   `userid` int(10) unsigned NOT NULL,
   `sortid` int(10) unsigned NOT NULL,
   PRIMARY KEY (managerid)
);




DROP TABLE IF EXISTS pa_message;
CREATE TABLE pa_message (
   `messageid` int(10) unsigned NOT NULL auto_increment,
   `commentid` int(10) unsigned NOT NULL,
   `userid` int(10) unsigned NOT NULL,
   `author` varchar(50) NOT NULL,
   `parentid` int(10) NOT NULL,
   `title` varchar(100) NOT NULL,
   `message` text NOT NULL,
   `date` int(10) unsigned NOT NULL,
   `ipaddress` varchar(16) NOT NULL,
   `removed` tinyint(1) unsigned NOT NULL,
   `lastupdate` int(10) NOT NULL,
   `lastupdater` varchar(255) NOT NULL,
   PRIMARY KEY (messageid),
   KEY `parentid` (parentid),
   KEY `userid` (userid),
   KEY `commentid` (commentid),
   KEY `removed` (removed)
);




DROP TABLE IF EXISTS pa_news;
CREATE TABLE pa_news (
   `newsid` int(10) unsigned NOT NULL auto_increment,
   `userid` int(10) NOT NULL,
   `title` varchar(100) NOT NULL,
   `content` text NOT NULL,
   `startdate` int(10) NOT NULL,
   `enddate` int(10) NOT NULL,
   PRIMARY KEY (newsid),
   KEY `userid` (userid),
   KEY `startdate` (startdate),
   KEY `enddate` (enddate)
);




DROP TABLE IF EXISTS pa_relatedlink;
CREATE TABLE pa_relatedlink (
   `relatedlinkid` int(10) unsigned NOT NULL auto_increment,
   `articleid` int(10) unsigned NOT NULL,
   `text` varchar(100) NOT NULL,
   `link` varchar(255) NOT NULL,
   PRIMARY KEY (relatedlinkid),
   KEY `articleid` (articleid)
);




DROP TABLE IF EXISTS pa_replacement;
CREATE TABLE pa_replacement (
   `replacementid` int(10) unsigned NOT NULL auto_increment,
   `replacementsetid` int(11) NOT NULL,
   `findword` text,
   `replaceword` text,
   `title` varchar(100) NOT NULL,
   `description` text,
   `type` varchar(100) NOT NULL,
   PRIMARY KEY (replacementid)
);




DROP TABLE IF EXISTS pa_replacementset;
CREATE TABLE pa_replacementset (
   `replacementsetid` int(10) unsigned NOT NULL auto_increment,
   `title` char(250) NOT NULL,
   PRIMARY KEY (replacementsetid)
);

INSERT INTO pa_replacementset VALUES('1', 'default');



DROP TABLE IF EXISTS pa_session;
CREATE TABLE pa_session (
   `sessionid` varchar(32) NOT NULL,
   `userid` int(10) unsigned NOT NULL,
   `useragent` varchar(255) NOT NULL,
   `ipaddress` varchar(16) NOT NULL,
   `lastactivity` int(10) unsigned NOT NULL,
   `location` varchar(255) NOT NULL,
   `expiry` int(10) unsigned NOT NULL,
   `value` text,
   PRIMARY KEY (sessionid),
   KEY `expiry` (expiry),
   KEY `ipaddress` (ipaddress)
);

INSERT INTO pa_session VALUES('gtfqijbelto862ue48h7380tb5', '1', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.14 Safari/537.36', '::1', '1475772980', '/phparticle22/admin/backup.php', '1475774420', 'isadmin|s:1:\"1\";logined|i:1;userid|s:1:\"1\";username|s:5:\"admin\";usergroupid|s:1:\"1\";password|s:32:\"c6b853d6a7cc7ec49172937f68f446c8\";radompassword|s:0:\"\";email|s:17:\"shanyinke@163.com\";joindate|s:10:\"1475762014\";homepage|N;sex|s:6:\"unknow\";address|N;qq|N;icq|N;msn|N;intro|N;tel|N;rememberpw|s:1:\"1\";posts|s:1:\"0\";lastvisit|s:10:\"1475770632\";lastactivity|s:10:\"1475770635\";timezoneoffset|s:1:\"8\";regip|s:0:\"\";title|s:10:\"超级管理员\";ismanager|s:1:\"1\";canaddarticle|s:1:\"1\";caneditarticle|s:1:\"1\";canremovearticle|s:1:\"1\";canaddnews|s:1:\"0\";caneditnews|s:1:\"0\";canremovenews|s:1:\"0\";canaddsort|s:1:\"0\";caneditsort|s:1:\"0\";canremovesort|s:1:\"0\";canviewarticle|s:1:\"1\";canratearticle|s:1:\"1\";canviewcomment|s:1:\"1\";cancomment|s:1:\"1\";cancontribute|s:1:\"1\";onedaypostmax|s:1:\"0\";postoptions|s:1:\"0\";');



DROP TABLE IF EXISTS pa_setting;
CREATE TABLE pa_setting (
   `settingid` int(10) unsigned NOT NULL auto_increment,
   `settinggroupid` int(10) unsigned NOT NULL,
   `title` varchar(100) NOT NULL,
   `name` varchar(100) NOT NULL,
   `value` mediumtext,
   `type` varchar(100) NOT NULL,
   `displayorder` int(10) unsigned NOT NULL,
   `description` text,
   PRIMARY KEY (settingid),
   KEY `displayorder` (displayorder)
);

INSERT INTO pa_setting VALUES('1', '1', '主页地址', 'homepage', 'http://localhost', 'string', '3', '');
INSERT INTO pa_setting VALUES('2', '1', '网站标题', 'phparticletitle', 'php', 'string', '2', '');
INSERT INTO pa_setting VALUES('3', '1', '首页地址', 'phparticleurl', 'http://localhost/phparticle22', 'string', '1', '文章系统所安装的地址,结尾不必加斜杆\'/\'.');
INSERT INTO pa_setting VALUES('4', '2', '是否显示模板注释?', 'showcomment', '0', 'boolean', '1', '方便调试界面.');
INSERT INTO pa_setting VALUES('5', '1', '管理员Email地址', 'webmastermail', 'shanyinke@163.com', 'string', '4', '');
INSERT INTO pa_setting VALUES('6', '1', '版本号', 'version', '2.1', 'string', '0', '');
INSERT INTO pa_setting VALUES('7', '3', '是否显示最近更新文章列表(详细)?', 'showrecentarticle', '1', 'boolean', '1', '');
INSERT INTO pa_setting VALUES('8', '3', '显示多少篇最近更新的文章?', 'recentarticlenum', '10', 'integer', '2', '');
INSERT INTO pa_setting VALUES('9', '3', '分多少列显示最近更新的文章?', 'recentarticledivision', '2', 'integer', '3', '');
INSERT INTO pa_setting VALUES('10', '3', '是否显示热门文章排行(按评分排行)?', 'showratearticle', '1', 'boolean', '4', '');
INSERT INTO pa_setting VALUES('11', '3', '显示多少篇?', 'ratearticlenum', '10', 'integer', '5', '');
INSERT INTO pa_setting VALUES('12', '3', '是否显示热门文章列表(按点击排行)?', 'showhotarticle', '1', 'boolean', '6', '');
INSERT INTO pa_setting VALUES('13', '3', '显示多少篇?', 'hotarticlenum', '10', 'integer', '7', '');
INSERT INTO pa_setting VALUES('14', '3', '是否显示最后更新文章列表(简单)?', 'showlastupdate', '1', 'boolean', '8', '');
INSERT INTO pa_setting VALUES('15', '3', '显示多少篇?', 'lastupdatenum', '10', 'integer', '9', '');
INSERT INTO pa_setting VALUES('16', '4', '是否显示评分结果?', 'showrating', '1', 'boolean', '1', '');
INSERT INTO pa_setting VALUES('17', '5', '是否允许新会员注册?', 'allowregister', '1', 'boolean', '1', '');
INSERT INTO pa_setting VALUES('18', '5', '会员名的最小长度(单位:字符)?', 'username_length_min', '4', 'integer', '3', '');
INSERT INTO pa_setting VALUES('19', '5', '会员名的最大长度(单位:字符)?', 'username_length_max', '15', 'integer', '4', '');
INSERT INTO pa_setting VALUES('20', '5', '密码的最小长度(单位:字符)?', 'password_length_min', '4', 'integer', '5', '');
INSERT INTO pa_setting VALUES('21', '5', '密码的最大长度(单位:字符)?', 'password_length_max', '15', 'integer', '6', '');
INSERT INTO pa_setting VALUES('22', '5', '最多可以收藏多少篇文章?', 'favoritelimit', '100', 'integer', '7', '');
INSERT INTO pa_setting VALUES('23', '6', '每页显示多少个搜索结果?', 'searchperpage', '10', 'integer', '1', '');
INSERT INTO pa_setting VALUES('24', '7', '是否使用Gzip压缩页面?', 'gzipoutput', '0', 'boolean', '1', '这样可以加速页面的显示和减少带宽的使用,但同时也会增加服务器的负担.');
INSERT INTO pa_setting VALUES('25', '7', '页面压缩的级别', 'gziplevel', '3', 'integer', '2', 'Max:9,Min:1');
INSERT INTO pa_setting VALUES('26', '7', '服务器所在的时区', 'timezone', '8', 'integer', '3', '');
INSERT INTO pa_setting VALUES('27', '8', '新闻时间格式', 'dateformat_news', 'Y-m-d h:i', 'string', '1', '时间显示格式的及意义,请参看 <a href=\'http://www.php.net/manual/en/function.date.php\' target=\'_blank\'>date()</a>.');
INSERT INTO pa_setting VALUES('28', '8', '文章日期格式', 'dateformat_article', 'Y-m-d', 'string', '2', '');
INSERT INTO pa_setting VALUES('29', '5', '是否需要通过email验证会员身份?', 'require_activation', '0', 'boolean', '2', '如果选择\'是\',游客注册后,系统会自动发发送帐号激活email给他们,提示他们如何激活会员帐号.');
INSERT INTO pa_setting VALUES('30', '8', '文章时间格式', 'timeformat_article', 'h:i A', 'string', '3', '');
INSERT INTO pa_setting VALUES('31', '9', '评论标题最大长度(单位:字符)?', 'comment_title_limit', '50', 'integer', '1', '');
INSERT INTO pa_setting VALUES('32', '9', '评论内容最大长度(单位:字符)?', 'comment_message_limit', '400', 'integer', '2', '');
INSERT INTO pa_setting VALUES('33', '10', '静态页存放目录', 'htmldir', 'htmldata', 'string', '1', '');
INSERT INTO pa_setting VALUES('34', '10', '静态文件后缀名', 'htmlfileext', 'html', 'string', '2', '');
INSERT INTO pa_setting VALUES('35', '10', '静态文件名生成方法', 'filenamemethod', '2', 'integer', '3', '1代表文件名不优化，2代表文件名英文/yahoo搜索引擎优化，3百度优化');
INSERT INTO pa_setting VALUES('36', '10', '文章静态页默认前缀', 'articleprefix', 'article_', 'string', '4', '');
INSERT INTO pa_setting VALUES('37', '10', '分类静态页前缀', 'sortprefix', 'sort_', 'string', '5', '');
INSERT INTO pa_setting VALUES('38', '3', '分类文章调用数量', 'main_article', '10', 'integer', '0', '首页每个分类调用的文章数量');
INSERT INTO pa_setting VALUES('39', '3', '文章调用标题长度', 'main_len', '20', 'integer', '0', '首页每篇文章的标题长度');
INSERT INTO pa_setting VALUES('40', '3', '图片文章长度', 'img_article_len', '20', 'integer', '0', '图片文章调用标题，和摘要长度');
INSERT INTO pa_setting VALUES('41', '3', '图片文章数量', 'img_article', '5', 'integer', '0', '图片文章调用的条数');
INSERT INTO pa_setting VALUES('42', '10', '使用日期作为目录', 'usedate', '1', 'boolean', '6', '是否在静态文件目录后面添加一个日期作为子目录。');
INSERT INTO pa_setting VALUES('43', '10', '使用分类名字作为目录', 'usename', '1', 'boolean', '7', '该分类名可以自己修改，默认为分类名的拼音');
INSERT INTO pa_setting VALUES('44', '10', '单一子目录', 'singledir', '2', 'integer', '8', '=1使用所在分类的分类名作为唯一子目录。=2，没有子目录');



DROP TABLE IF EXISTS pa_settinggroup;
CREATE TABLE pa_settinggroup (
   `settinggroupid` int(10) unsigned NOT NULL auto_increment,
   `title` varchar(100) NOT NULL,
   `displayorder` tinyint(3) unsigned NOT NULL,
   PRIMARY KEY (settinggroupid),
   KEY `displayorder` (displayorder)
);

INSERT INTO pa_settinggroup VALUES('1', '基本设置', '1');
INSERT INTO pa_settinggroup VALUES('2', '模板设置', '2');
INSERT INTO pa_settinggroup VALUES('3', '首页显示设置', '3');
INSERT INTO pa_settinggroup VALUES('4', '文章设置', '4');
INSERT INTO pa_settinggroup VALUES('5', '会员选项设置', '6');
INSERT INTO pa_settinggroup VALUES('6', '搜索选项设置', '7');
INSERT INTO pa_settinggroup VALUES('7', '服务器设置', '8');
INSERT INTO pa_settinggroup VALUES('8', '时间显示格式设置', '9');
INSERT INTO pa_settinggroup VALUES('9', '评论设置', '5');
INSERT INTO pa_settinggroup VALUES('10', '静态生成设置', '10');



DROP TABLE IF EXISTS pa_sort;
CREATE TABLE pa_sort (
   `sortid` int(10) unsigned NOT NULL auto_increment,
   `title` varchar(50) NOT NULL,
   `description` varchar(250) NOT NULL,
   `displayorder` int(10) unsigned DEFAULT '1' NOT NULL,
   `parentid` int(11) DEFAULT '-1' NOT NULL,
   `img` varchar(100),
   `articlecount` int(10) unsigned NOT NULL,
   `showinrecent` tinyint(3) unsigned DEFAULT '1' NOT NULL,
   `showinhot` tinyint(3) unsigned DEFAULT '1' NOT NULL,
   `showinrate` tinyint(3) unsigned DEFAULT '1' NOT NULL,
   `showinlast` tinyint(3) unsigned DEFAULT '1' NOT NULL,
   `parentlist` varchar(255) NOT NULL,
   `division_sort` tinyint(1) unsigned DEFAULT '3' NOT NULL,
   `division_article` tinyint(1) unsigned DEFAULT '1' NOT NULL,
   `perpage` tinyint(3) unsigned DEFAULT '10' NOT NULL,
   `showsortinfos` tinyint(1) unsigned NOT NULL,
   `styleid` int(10) unsigned NOT NULL,
   `ratearticlenum` tinyint(3) unsigned DEFAULT '10' NOT NULL,
   `hotarticlenum` tinyint(3) unsigned DEFAULT '10' NOT NULL,
   `dirname` varchar(255) NOT NULL,
   PRIMARY KEY (sortid),
   KEY `displayorder` (displayorder)
);




DROP TABLE IF EXISTS pa_style;
CREATE TABLE pa_style (
   `styleid` int(10) unsigned NOT NULL auto_increment,
   `replacementsetid` int(10) unsigned NOT NULL,
   `templatesetid` int(10) unsigned NOT NULL,
   `title` char(250) NOT NULL,
   PRIMARY KEY (styleid)
);

INSERT INTO pa_style VALUES('1', '1', '1', 'default');



DROP TABLE IF EXISTS pa_tag;
CREATE TABLE pa_tag (
   `tagid` int(10) NOT NULL auto_increment,
   `tagname` varchar(50) NOT NULL,
   `locate` varchar(50) NOT NULL,
   `contenttype` varchar(50) NOT NULL,
   `type` varchar(50) NOT NULL,
   `sortid` varchar(25) NOT NULL,
   `maxarticles` int(10) NOT NULL,
   `titlelen` int(10) NOT NULL,
   `templatename` varchar(255) NOT NULL,
   `renew` tinyint(1) NOT NULL,
   PRIMARY KEY (tagid),
   KEY `tagname` (tagname),
   KEY `renew` (renew)
);




DROP TABLE IF EXISTS pa_templateset;
CREATE TABLE pa_templateset (
   `templatesetid` int(10) unsigned NOT NULL auto_increment,
   `title` char(250) NOT NULL,
   PRIMARY KEY (templatesetid)
);

INSERT INTO pa_templateset VALUES('1', 'default');



DROP TABLE IF EXISTS pa_user;
CREATE TABLE pa_user (
   `userid` int(10) unsigned NOT NULL auto_increment,
   `username` varchar(50) NOT NULL,
   `usergroupid` int(10) unsigned NOT NULL,
   `password` varchar(50) NOT NULL,
   `radompassword` varchar(32) NOT NULL,
   `email` varchar(100) NOT NULL,
   `joindate` int(10) unsigned NOT NULL,
   `homepage` varchar(100),
   `sex` varchar(10) DEFAULT 'unknow' NOT NULL,
   `address` varchar(250),
   `qq` varchar(16),
   `icq` varchar(16),
   `msn` varchar(50),
   `intro` text,
   `tel` varchar(20),
   `rememberpw` tinyint(1) unsigned DEFAULT '1' NOT NULL,
   `posts` int(10) unsigned NOT NULL,
   `lastvisit` int(10) unsigned NOT NULL,
   `lastactivity` int(10) unsigned NOT NULL,
   `timezoneoffset` float DEFAULT '8' NOT NULL,
   `regip` varchar(15) NOT NULL,
   PRIMARY KEY (userid),
   KEY `usergroupid` (usergroupid)
);

INSERT INTO pa_user VALUES('1', 'admin', '1', 'c6b853d6a7cc7ec49172937f68f446c8', '', 'shanyinke@163.com', '1475762014', NULL, 'unknow', NULL, NULL, NULL, NULL, NULL, NULL, '1', '0', '1475772976', '1475772980', '8', '');



DROP TABLE IF EXISTS pa_useractivation;
CREATE TABLE pa_useractivation (
   `useractivationid` int(10) unsigned NOT NULL auto_increment,
   `userid` int(10) unsigned NOT NULL,
   `time` int(10) unsigned NOT NULL,
   `activationcode` varchar(20) NOT NULL,
   PRIMARY KEY (useractivationid),
   KEY `userid` (userid)
);




DROP TABLE IF EXISTS pa_usergroup;
CREATE TABLE pa_usergroup (
   `usergroupid` smallint(5) NOT NULL auto_increment,
   `title` varchar(50) NOT NULL,
   `isadmin` tinyint(1) unsigned NOT NULL,
   `ismanager` tinyint(1) unsigned NOT NULL,
   `canaddarticle` tinyint(1) unsigned NOT NULL,
   `caneditarticle` tinyint(1) unsigned NOT NULL,
   `canremovearticle` tinyint(1) unsigned NOT NULL,
   `canaddnews` tinyint(1) unsigned NOT NULL,
   `caneditnews` tinyint(1) unsigned NOT NULL,
   `canremovenews` tinyint(1) unsigned NOT NULL,
   `canaddsort` tinyint(1) unsigned NOT NULL,
   `caneditsort` tinyint(1) unsigned NOT NULL,
   `canremovesort` tinyint(1) unsigned NOT NULL,
   `canviewarticle` tinyint(1) unsigned DEFAULT '1' NOT NULL,
   `canratearticle` tinyint(1) unsigned NOT NULL,
   `canviewcomment` tinyint(1) unsigned DEFAULT '1' NOT NULL,
   `cancomment` tinyint(1) unsigned NOT NULL,
   `cancontribute` tinyint(1) unsigned NOT NULL,
   `onedaypostmax` tinyint(3) NOT NULL,
   `postoptions` int(10) NOT NULL,
   PRIMARY KEY (usergroupid)
);

INSERT INTO pa_usergroup VALUES('1', '超级管理员', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0', '0', '1', '1', '1', '1', '1', '0', '0');
INSERT INTO pa_usergroup VALUES('2', '普通管理员', '0', '1', '1', '1', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '1', '1', '0', '0');
INSERT INTO pa_usergroup VALUES('3', '一般会员', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '1', '1', '1', '1', '0', '0');
INSERT INTO pa_usergroup VALUES('4', '游客', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '0', '1', '0', '0', '0', '0');
INSERT INTO pa_usergroup VALUES('5', '等待email激活会员', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '0', '1', '0', '0', '0', '0');



