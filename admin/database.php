<?php
error_reporting(7);
require "global.php";

cpheader();

if ($_GET[action]=="optimize" OR $_GET[action]=="repair") {

    if ($_GET[action]=="optimize") {
        $cpforms->formheader(array('title'=>'优化数据库,请选择要优化的表'));
    } else {
        $cpforms->formheader(array('title'=>'修复数据库,请选择要修复的表'));
    }

    $tables = mysql_list_tables($dbname);
    if (!$tables) {
        print "DB Error, could not list tables\n";
        print 'MySQL Error: ' . mysql_error();
        pa_exit();
    }
    $tables = mysql_list_tables($dbname);
    while ($table = $DB->fetch_row($tables)) {
           $cachetables[$table[0]] = $table[0];
           $tableselected[$table[0]] = 1;
    }

    $DB->free_result($tables);
    $cpforms->makeselect(array('text'=>'请选择表:',
                                'name'=>'table[]',
                                'option'=>$cachetables,
                                'selected'=>$tableselected,
                                'multiple'=>1,
                                'size'=>15
                                ));


    $cpforms->makehidden(array('name'=>'action',
                                'value'=>"do$_GET[action]"));
    $cpforms->formfooter();

}

if ($_POST[action]=="dooptimize" OR $_POST[action]=="dorepair") {

    if ($_POST[action]=="dooptimize") {
        $a = "OPTIMIZE";
        $text = "优化";
    } else {
        $a = "REPAIR";
        $text = "修复";
    }
    if (!is_array($table) OR empty($table)) {
        pa_exit("还未选中任何要${text}的表");
    }

    $table = array_flip($_POST[table]);

    foreach ($table AS $name=>$value) {
             if (isset($value)) {
                 echo "正在{$text}表: $name";
                 $result = $DB->query("$a TABLE $name");
                 if ($result) {
                     echo " <b>OK</b>";
                 } else {
                     echo " <font color=\"red\"><b>Failed</b></font>";
                 }
                 echo "<br>\n";
             }
    }

    echo "<p>所有表均已$text.</p>";

}

if ($_GET[action]=="backup") {

    $cpforms->formheader(array('title'=>'备份数据库,请选择要备份的表'));

    $tables = mysql_list_tables($dbname);
    while ($table = $DB->fetch_row($tables)) {
           $cachetables[$table[0]] = $table[0];
           $tableselected[$table[0]] = 1;
    }

    $DB->free_result($tables);
    $cpforms->makeselect(array('text'=>'请选择表:',
                                'name'=>'table[]',
                                'option'=>$cachetables,
                                'selected'=>$tableselected,
                                'multiple'=>1,
                                'size'=>15
                                ));

    $cpforms->makeinput(array('text'=>'备份数据所保存的路径:<br>请确认该文件夹的属性为 0777 ',
                               'name'=>'path',
                               'value'=>"./".date("Y-m-d",time())."-phpArticle.sql"));
    $cpforms->makehidden(array('name'=>'action','value'=>'dobackup'));
    $cpforms->formfooter();

}

if ($_POST[action]=="dobackup") {

    $table = array_flip($_POST[table]);
print_r($table);exit;
    $filehandle = fopen($path,"w");
    $result = $DB->query("SHOW tables");
    while ($currow = $DB->fetch_array($result)) {
           if (isset($table[$currow[0]])) {
               sqldumptable($currow[0], $filehandle);
               fwrite($filehandle,"\n\n\n");
           }
    }
    fclose($filehandle);
    pa_exit("数据库已备份");
}

// data dump functions
function sqldumptable($table, $fp=0) {
         global $DB;

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
                $tabledump .= "   $field[Field] $field[Type]";
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

               if ($kname == "PRIMARY") {
                  // do primary key
                  $tabledump .= "   PRIMARY KEY ($colnames)";
               } else {
                  // do standard key
                  if (substr($kname,0,6) == "UNIQUE") {
                      // key is unique
                      $kname=substr($kname,7);
                  }
                  $tabledump .= "   KEY $kname ($colnames)";
               }
         }

         $tabledump .= "\n);\n\n";
         if ($fp) {
             fwrite($fp,$tabledump);
         } else {
             echo $tabledump;
         }

         // get data
         $rows = $DB->query("SELECT * FROM $table");
//         $numfields=$DB->num_fields($rows);
         $numfields = mysql_num_fields($rows);
         while ($row = $DB->fetch_array($rows)) {
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
                } else {
                    echo $tabledump;
                }
         }
         $DB->free_result($rows);
}

cpfooter();

?>