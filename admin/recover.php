<?php
error_reporting(7);
require_once('./global.php');
if (function_exists("set_time_limit")==1 and get_cfg_var("safe_mode")==0) {
  @set_time_limit(0);
}

//log_admin_action(iif(!empty($table), "Table = $table", ''));
function readfromfile($file_name,$maxsize) {
	global $number,$length,$maxlength,$DB;//$db_name, $db_host, $db_user, $db_password;
	$count=0;

	$table_prefix="serials_";
//	$site_db = new mysqlClass($db_name, $db_host, $db_user, $db_password);
	$site_db = &$DB;
	$filenum=@fopen($file_name,"r");
	flock($filenum,LOCK_SH);
	$fsize=filesize($file_name);
	echo $file_name."文件大小: ".filesize($file_name)."<br>";
	if (function_exists("set_time_limit")==1 and get_cfg_var("safe_mode")==0) {
		@set_time_limit(0);
	}//else echo "set_time_limit() error";
	if($fsize>$maxsize){
		$count=$fsize/$maxsize;
		
		if($number>0){
			echo $file_name."第 ".$number." 部分恢复完成。<br>";			
		
			fseek($filenum,$length);

		}else {
			echo "我们需要把文件分成".$count."多份进行恢复。<br>";
			echo "现在开始恢复数据，数据库1";
			$number=1;
			$length=0;
		}

		if(($fsize-$length)<$maxsize){
			$number=0;
			$length=$fsize-$length;
			$cont=fread($filenum,$length);
			$cont = preg_replace('/sssss_/', $table_prefix, $cont);
			$pieces = split_sql_file(remove_remarks($cont));
			for ($i = 0; $i < sizeof($pieces); $i++) {
				$sql = trim($pieces[$i]);
				if (!empty($sql) and $sql[0] != '#') {
					if (!$site_db->query($sql)) {
						echo $sql;
					}
				}
			}
			echo "<br>".$file_name."数据恢复成功!";
		}else{
			$number++;
			$cont=fread($filenum,$maxsize);
			$cont.=fgets($filenum,1024);

			while(TRUE){
				$string_end  = (strrpos($cont, "\015"))
	                              ? strrpos($cont, "\015")
	                              : strrpos($cont, "\012");
				if(!empty($string_end)){
					if($cont[$string_end-1]==';'){
						$cont=substr($cont,0,$string_end);
						break;
					}else $cont=substr($cont,0,$string_end-1);
				}else $cont.=fgets($filenum,1024);
			}
			$length=$length+$string_end+1;
			$cont = preg_replace('/sssss_/', $table_prefix, $cont);
			$pieces = split_sql_file(remove_remarks($cont));
			for ($i = 0; $i < sizeof($pieces); $i++) {
				$sql = trim($pieces[$i]);
				if (!empty($sql) and $sql[0] != '#') {
					if (!$site_db->query($sql)){
						echo $sql;
					}
				}
			}
			
		}
		
	}else if($fsize>0){
		$number=0;
		$cont=fread($filenum,$fsize);
		$cont = preg_replace('/sssss_/', $table_prefix, $cont);
		$pieces = split_sql_file(remove_remarks($cont));
		for ($i = 0; $i < sizeof($pieces); $i++) {
			$sql = trim($pieces[$i]);
			if (!empty($sql) and $sql[0] != '#') {
				if (!$site_db->query($sql)) {
					echo $sql;
				}
			}
		}
		echo "<br>数据恢复成功!";
	}
	fclose($filenum);	
}
function split_sql_file($sql, $delimiter = ';') {
        $sql               = trim($sql);
        $char              = '';
        $last_char         = '';
        $ret               = array();
        $string_start      = '';
        $in_string         = FALSE;
        $escaped_backslash = FALSE;

        for ($i = 0; $i < strlen($sql); ++$i) {
            $char = $sql[$i];

            // if delimiter found, add the parsed part to the returned array
            if ($char == $delimiter && !$in_string) {
                $ret[]     = substr($sql, 0, $i);
                $sql       = substr($sql, $i + 1);
                $i         = 0;
                $last_char = '';
            }

            if ($in_string) {
                // We are in a string, first check for escaped backslashes
                if ($char == '\\') {
                    if ($last_char != '\\') {
                        $escaped_backslash = FALSE;
                    } else {
                        $escaped_backslash = !$escaped_backslash;
                    }
                }
                // then check for not escaped end of strings except for
                // backquotes than cannot be escaped
                if (($char == $string_start)
                    && ($char == '`' || !(($last_char == '\\') && !$escaped_backslash))) {
                    $in_string    = FALSE;
                    $string_start = '';
                }
            } else {
                // we are not in a string, check for start of strings
                if (($char == '"') || ($char == '\'') || ($char == '`')) {
                    $in_string    = TRUE;
                    $string_start = $char;
                }
            }
            $last_char = $char;
        } // end for

        // add any rest to the returned array
        if (!empty($sql)) {
            $ret[] = $sql;
        }
        return $ret;
    } // end of the 'split_sql_file()' function
    
    
function remove_remarks($sql)
{
$i = 0;

while ($i < strlen($sql)) {
    // Patch FROM Chee Wai
    // (otherwise, if $i == 0 and $sql[$i] == "#", the original order
    // in the second part of the AND bit will fail with illegal index)
    if ($sql[$i] == '#' && ($i == 0 || $sql[$i-1] == "\n")) {
        $j = 1;
        while ($sql[$i+$j] != "\n") {
            $j++;
            if ($j+$i > strlen($sql)) {
                break;
            }
        } // end while
        $sql = substr($sql, 0, $i) . substr($sql, $i+$j);
    } // end if
    $i++;
} // end while

return $sql;
} // end of the 'remove_remarks()' function

//get the backup sql files
function fetch_sql_files($dir = '.')
{
	$folders = array();

	if ($handle = @opendir($dir))
	{
		while ($folder = readdir($handle))
		{
		//	if (is_file($folder))
			{
				if(strtolower(substr($folder,-3)) == 'sql'){
					$folders["$folder"] = $folder;
					
				}
			}
		}
		closedir($handle);
		uksort($folders, 'strnatcasecmp');
		$folders = str_replace('_', ' ', $folders);
	}

	return $folders;
}
	if($_POST['do']){
		globalize($_POST,
			array('maxsize' => INT,
				'length' => INT,
				'do' => INT,
				'db_file' => STR,
				'dstdir2' => STR,
				'filenumber' => INT,
				'filecount' => INT
			)
		);
	}else{
		globalize($_GET,
			array('maxsize' => INT,
				'length' => INT,
				'do' => INT,
				'db_file' => STR,
				'dstdir2' => STR,
				'filenumber' => INT,
				'filecount' => INT,
				'next' => INT
			)
		);
	}
	cpheader();
	if($_POST['db_files']){
		$_GET['db_files'] = &$_POST['db_files'];
	}
	if($_GET['db_files']){
		foreach($_GET['db_files'] AS $dbfile){
			$db_filearray .= "&db_files[]=".$dbfile;
		}
	//	$db_filearray = urlencode($db_filearray);
	}
	if($_POST['dstdir']){
		unset($do);
	}
	else if($_GET['db_files'][$filenumber]){
		if($dstdir2)
		$db_file = $dstdir2."/".$_GET['db_files'][$filenumber];
		else $db_file = $_GET['db_files'][$filenumber];
	}else if($dstdir2)
		$error_log = "请选择数据库文件!";
	else $outputmsg = "数据恢复完成!";

	if(!$maxsize)$maxsize=1024*350;//每次要恢复的数据大小（可以酌情修改）
  	if (empty($error_log)&&($do||!empty($length)||$next)) {
  		if(!$db_file){
  			echo $outputmsg;
  			$number = 0;
  		}
		else readfromfile($db_file,$maxsize);
    }else{
    		echo $error_log;
		$cpforms->formheader(array('title' => '导入服务器数据文件:',
                        'name' => 'recoverForm',
                        'method' => 'post',
                        'action' => 'recover.php'));
$cpforms->makehidden(array('name'=>'do',
                                'value'=>'1'));
		$cpforms->makeinput(array('text'=>'每次读取的最大字节（默认350KB）',
                                    'name'=>'maxsize',
                                    'value'=>$maxsize));
		$cpforms->makehidden(array('name'=>"length",
                                'value'=>$length));
		$cpforms->makeinput(array('text'=>"数据库所在目录<font color='blue'>（填入目录后，按“确定”）默认目录是backup</font>",
                                    'name'=>'dstdir',
                                    'value'=>''));
		if(!$dstdir)$dstdir="../backup";
		$sqlfiles = fetch_sql_files($dstdir);
		$filecount = count($sqlfiles);
		$cpforms->makeselect(array('text' => "数据库文件列表<font color='blue'>（选择文件后，按“确定”，开始导入。支持多选）</font>",
                  'name' => 'db_files[]',
                  'option' => $sqlfiles,
                  'selected' => '',
                  'tabindex'=>"1",
                  'multiple'=>"1"));
		$cpforms->makehidden(array('name'=>"filecount",
                                'value'=>$filecount));
		$cpforms->makehidden(array('name'=>"dstdir2",
                                'value'=>$dstdir));
?>
  <tr class='firstalt'><td colspan='2'><p>如果你在恢复数据库的过程中出现了超时错误，请你改小你的“最大字节”。</p></td></tr>
<?php
		$cpforms->formfooter();
		cpfooter();
	}
if($number!=0){
?>
<script type="text/javascript">//自动刷新的Javascritp语句 
document.location="recover.php?number=<? echo $number."&length=".$length.$db_filearray."&maxsize=".$maxsize."&filecount=".$filecount."&filenumber=".$filenumber."&dstdir2=".$dstdir2;?>";//定位于下一句 
</script>
<?php
}elseif($filenumber == $filecount-1||$filecount==0){
	cpfooter();
}else{
	$filenumber ++;
	?>
<script type="text/javascript">//自动刷新的Javascritp语句 
document.location="recover.php?next=1<? echo "&length=0".$db_filearray."&maxsize=".$maxsize."&filecount=".$filecount."&filenumber=".$filenumber."&dstdir2=".$dstdir2;?>";//定位于下一句 
</script>
<?
}
?>