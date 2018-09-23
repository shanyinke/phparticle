<?php
error_reporting(7);
require "global.php";
cpheader();
$cpforms->inithtmlarea();
$cpforms->formheader(array('title' => '首页静态生成(<font color=yellow>当前的默认首页是index，不需要输入，直接提交即可。</font>)',
                'name' => 'homepage',
                'method' => 'get',
                'action' => '../index.php'));
$cpforms->makeinput(array('text' => '请输入首页面的名字（比如friend是生成friend.html，而且需要在后台自定义friendhome模板，记住了，friendhome，而不是friend）：',
                        'name' => 'pagename',
                        'value' => ""));
$cpforms->formfooter();
$cpforms->tablefooter();
cpfooter();

?>