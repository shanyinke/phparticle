<?php
error_reporting(7);

if ($_POST['do']) {
} else if ($_GET['do']) {
	$_POST['do'] = $_GET['do'];
}

if (function_exists("set_time_limit")==1 and get_cfg_var("safe_mode")==0) {
  @set_time_limit(0);
}

if (isset($_POST['do']) and ($_POST['do']=="csvtable" or $_POST['do']=="sqltable")) {
  $noheader=1;
}

//suppress gzipping
$nozip=1;

require_once('./global.php');
require_once('./adminfunctions_backup.php');

chdir("../backup");
// data dump functions
function sqldumptable($table, $fp=0) {
  global $DB,$dumpSize,$eachfilesize,$dump_flag,$datalinenum;
if(empty($datalinenum)||!isset($datalinenum)){
  $datalinenum=0;//init
  $tabledump = "DROP TABLE IF EXISTS $table;\n";
  $tabledump .= "CREATE TABLE $table (\n";

  $firstfield=1;

  // get columns and spec
  $fields = $DB->query("SHOW FIELDS FROM $table");
  while ($field = $DB->fetch_array($fields)) {
    if (!$firstfield) {
      $tabledump .= ",\n";
    } else {
      $firstfield=0;
    }
    $tabledump .= "   `$field[Field]` $field[Type]";
    if (!empty($field["Default"])) {
      // get default value
      $tabledump .= " DEFAULT '$field[Default]'";
    }
		if ($field['Null'] != "YES") {
      // can field be null
      $tabledump .= " NOT NULL";
    }
    if ($field['Extra'] != "") {
      // any extra info?
      $tabledump .= " $field[Extra]";
    }
  }
  $DB->free_result($fields);

  // get keys list
  $keys = $DB->query("SHOW KEYS FROM $table");
  while ($key = $DB->fetch_array($keys)) {
    $kname=$key['Key_name'];
    if ($kname != "PRIMARY" and $key['Non_unique'] == 0) {
      $kname="UNIQUE|$kname";
    }
    if(!is_array($index[$kname])) {
      $index[$kname] = array();
    }
    $index[$kname][] = $key['Column_name'];
  }
  $DB->free_result($keys);

  // get each key info
  while(list($kname, $columns) = @each($index)){
    $tabledump .= ",\n";
    $colnames=implode($columns,",");

    if($kname == "PRIMARY"){
      // do primary key
      $tabledump .= "   PRIMARY KEY ($colnames)";
    } else {
      // do standard key
      if (substr($kname,0,6) == "UNIQUE") {
        // key is unique
        $kname=substr($kname,7);
      }

      $tabledump .= "   KEY `$kname` ($colnames)";

    }
  }

  $tabledump .= "\n);\n\n";
  if ($fp) {
    fwrite($fp,$tabledump);
    $dumpSize+=strlen($tabledump);
  } else {
    echo $tabledump;
  }
}
  // get data

  $rows = $DB->query("SELECT * FROM $table");
  if($datalinenum>0 && $datalinenum < mysql_num_rows($rows))
    mysql_data_seek($rows,$datalinenum);
  elseif($datalinenum > 0){
    mysql_data_seek($rows,$datalinenum-1);
    $DB->fetch_array($rows, DBARRAY_NUM);
  }
  $numfields=$DB->num_fields($rows);
  while ($row = $DB->fetch_array($rows, DBARRAY_NUM)) {
    $tabledump = "INSERT INTO $table VALUES(";

    $fieldcounter=-1;
    $firstfield=1;
    // get each field's data
    while (++$fieldcounter<$numfields) {
      if (!$firstfield) {
        $tabledump.=", ";
      } else {
        $firstfield=0;
      }

      if (!isset($row[$fieldcounter])) {
        $tabledump .= "NULL";
      } else {
        $tabledump .= "'".mysql_real_escape_string($row[$fieldcounter])."'";
      }
    }

    $tabledump .= ");\n";
    
    if ($fp) {
      fwrite($fp,$tabledump);
      $dumpSize+=strlen($tabledump);
    } else {
      echo $tabledump;
    }
    $datalinenum++;
    if($dumpSize>=$eachfilesize){
      $dump_flag=1;
      break;
    }else{
      $dump_flag=0;
    }
  }
  if($dump_flag==0){
     $datalinenum="";
     unset($datalinenum);
  }

  $DB->free_result($rows);
  //return $tabledump;
}

function csvdumptable($table,$separator,$quotes,$showhead) {
  global $DB;

  // get columns for header row
  if ($showhead) {
    $firstfield=1;
    $fields = $DB->query("SHOW FIELDS FROM $table");
    while ($field = $DB->fetch_array($fields, DBARRAY_NUM)) {
      if (!$firstfield) {
        $contents.=$separator;
      } else {
        $firstfield=0;
      }
      $contents.=$quotes.$field['Field'].$quotes;
    }
    $DB->free_result($fields);
  }
  $contents.="\n";


  // get data
  $rows = $DB->query("SELECT * FROM $table");
  $numfields=$DB->num_fields($rows);
  while ($row = $DB->fetch_array($rows, DBARRAY_NUM)) {

    $fieldcounter=-1;
    $firstfield=1;
    while (++$fieldcounter<$numfields) {
      if (!$firstfield) {
        $contents.=$separator;
      } else {
        $firstfield=0;
      }

      if (!isset($row[$fieldcounter])) {
        $contents .= "NULL";
      } else {
        $contents .= $quotes.addslashes($row[$fieldcounter]).$quotes;
      }
    }

    $contents .= "\n";
  }
  $DB->free_result($rows);

  return $contents;
}

if ($_POST['do']=="csvtable") {
  header("Content-disposition: filename=".$table.".csv");
  header("Content-type: unknown/unknown");

  echo csvdumptable($table,$separator,$quotes,$showhead);

  exit;

}

if ($_POST['do']=="sqltable") {
	header("Content-disposition: filename=vbulletin.sql");
	header("Content-type: unknown/unknown");

	$result=$DB->query("SHOW tables");
	while (list($key,$val)=each($table)) {
		if ($val==1) {
		  sqldumptable($key);
		  echo "\n\n\n";
		}
  }

  exit;

}

cpheader();

if (isset($_POST['do'])==0) {
  $_POST['do']="choose";
}

if ($_POST['do']=="choose") {

  ?>
  <p>你可以在这里备份你的论坛数据库。</p>

  <P><b>SQL 数据导出:</b></p>

  <?php
/*
  print_form_header("backup","sqltable");
  print_table_header("在备份文件中所要包含的数据表");

  $result=$DB->query("SHOW tables");
  while ($currow=$DB->fetch_array($result, DBARRAY_NUM)) {
    print_yes_no_row(TABLE_PREFIX . $currow[0],"table[" . TABLE_PREFIX .$currow[0]."]",1);
  }

  print_submit_row("创建备份");
*/

  $cpforms->formheader(array('title' => '在服务器上分卷保存数据文件:',
                        'name' => 'backupForm',
                        'method' => 'post',
                        'action' => 'backup.php'));
$cpforms->makehidden(array('name'=>'do',
                                'value'=>'sqlfile'));
  if(empty($filename))$filename="dbfile".date("m-d-Y",time()).".sql";
  if(empty($eachfilesize))$eachfilesize=10000000;
  $cpforms->makeinput(array('text'=>'在服务器上的路径和文件名',
                                    'name'=>'filename',
                                    'value'=>$filename));
  $cpforms->makeinput(array('text'=>'单个文件大小（byte）',
                                    'name'=>'eachfilesize',
                                    'value'=>$eachfilesize));
  $cpforms->makehidden(array('name'=>"linenum",
                                'value'=>$linenum));
  $cpforms->makehidden(array('name'=>"filenum",
                                'value'=>$filenum));
  $cpforms->makehidden(array('name'=>"datalinenum",
                                'value'=>$datalinenum));
  echo "<tr class='firstalt'><td colspan='2'><p><b>PHP必须在此目录中有写入权限</b>(默认目录为系统的backup/目录，需要设置此目录的属性为 0777，推荐指定目录，比如：<br>“backup/dbfile06-24-2004.sql”，请不要在目录前面添加‘/’线。用ftp设置“backup”目录的属性为0777)</p></td></tr>\n";
  echo "<tr class='firstalt'><td colspan='2'><p><b>警告:</b> 请不要将备份文件放置到互联网上不安全的目录内。如果可能的话请将它放在论坛根目录以外的目录！</p></td></tr>\n";
  $cpforms->formfooter();
/*
  print_form_header("backup","csvtable");
  print_table_header("CSV方式导出数据:");

  echo "<tr class='".getrowbg()."'>\n<td><p>选择数据表:</p></td>\n<td><p>";
  echo "<select name=\"table\" size=\"1\">\n";

  $result=$DB->query("SHOW tables");
  while ($currow=$DB->fetch_array($result, DBARRAY_NUM)) {
    echo "<option value=\"$currow[0]\">$currow[0]</option>\n";
  }

  echo "</select></p></td></tr>\n\n";

  print_input_row("分隔","separator",",");
  print_input_row("引用","quotes","'");
  print_yes_no_row("显示字段名","showhead",1);

  print_submit_row("创建");
*/
}

if ($_POST['do']=="sqlfile") {
  $filehandle=fopen($filename,"w");
  $result=$DB->query("SHOW tables");
  if($linenum<0||empty($linenum))$linenum=0;
  if(mysql_data_seek($result,$linenum)){
  	$filenum++;
	  if($string_end=strrpos($filename,"_")){
	  	$filename=substr($filename,0,$string_end);
	  	$filename=$filename."_".$filenum.".sql";
	  }elseif($string_end=strrpos($filename,".")) {
	  	$filename=substr($filename,0,$string_end);
	  	$filename=$filename."_".$filenum.".sql";
	  }else $filename=$filename."_".$filenum.".sql";
  while ($currow=$DB->fetch_array($result, DBARRAY_NUM)) {
		sqldumptable($currow[0], $filehandle);
		
		echo "<p>正在导出 $currow[0]</p>";
		if($dump_flag==1)break;
		fwrite($filehandle, "\n\n\n");
		$linenum++;
  }
  if($dumpSize<$eachfilesize)
      $dumpok=1;
}else $dumpok=1;
  fclose($filehandle);
if($dumpok==1)
  echo "<p>数据已成功导出！</p>";
else{
  $cpforms->formheader(array('title' => '在服务器上分卷保存数据文件:',
                        'name' => 'backupForm',
                        'method' => 'post',
                        'action' => 'backup.php'));
$cpforms->makehidden(array('name'=>'do',
                                'value'=>'sqlfile'));
//  print_table_header("在服务器上保存数据文件:");
  if(empty($filename))$filename="dbfile".date("m-d-Y",time()).".sql";
  if(empty($eachfilesize))$eachfilesize=10000000;
  $cpforms->makehidden(array('name'=>"filename",
                                'value'=>$filename));
  $cpforms->makehidden(array('name'=>"eachfilesize",
                                'value'=>$eachfilesize));
  $cpforms->makehidden(array('name'=>"linenum",
                                'value'=>$linenum));
  $cpforms->makehidden(array('name'=>"filenum",
                                'value'=>$filenum));
  $cpforms->makehidden(array('name'=>"datalinenum",
                                'value'=>$datalinenum));
  $dispnum=$filenum+1;
  echo"保存文件（".$dispnum."）";
  $cpforms->formfooter();
}
cpfooter();
}
exit;
?>
//
<script type="text/javascript">//自动刷新的Javascritp语句 
document.location="./backup.php?filename=<? echo $filename."&linenum=".$linenum."&eachfilesize=".$eachfilesize."&filenum=".$filenum."&datalinenum=".$datalinenum;?>";//定位于下一句 
</script>
