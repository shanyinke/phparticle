<?
//变量名字不能够变
$forumlist=Array(
	'sort'=>'forums',
	'articletext'=>'posts',
	'article'=>'threads'
	);
$tablelist=Array(
	'sort'=>Array(
		'sortid'=>'fid',
		'title'=>'name',
		'displayorder'=>'vieworder',
		'description'=>'descrip',
		'parentid'=>'fup'
		),
	'articletext'=>Array(
		'id'=>'pid',
		'articleid'=>'tid',
		'subhead'=>'subject',
		'articletext'=>'content'
		),
	'article'=>Array(
		'articleid'=>'tid',
		'title'=>'subject',
		'lastupdate'=>'lastpost',
		'sortid'=>'fid',
		'editor'=>'author',
		'userid'=>'authorid',
		'date'=>'postdate',
		'views'=>'hits',
		'attach'=>'ifupload'
		)
	);

function convert_content($content)
{
	return htmlspecialchars($content, ENT_QUOTES);
}