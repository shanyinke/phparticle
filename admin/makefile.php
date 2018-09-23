<?php
function compile_module_file($modulename='', $savedir='.', $htmldir='.', $srcdir='.')
{
	include_once "template.inc.php";
	$t = new Template($htmldir, $srcdir);// 创建一个名为 $t 的模板对象
	$t->set_file($modulename, $modulename);// 设置 $modulename = 我们的模板文件
	$t->parent_template_filename = $modulename;
	$t->type = "T_TEMPLATE";
	$t->savedir = $savedir;
	$t->parse("MyOutput",$modulename);// 设置模板变量 MyOutput = 分析后的文件
	$t->save("MyOutput");// 存储输出 MyOutput 的值(我们的分析后的数据)
	unset($t);
}
?>