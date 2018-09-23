<?php
/*======================================================================*\
|| #################################################################### ||
|| # utSpeed 1.0 Alpha - Licence Number  ******     # ||
|| # ---------------------------------------------------------------- # ||
|| # Copyright ?000?004 UTSpeed Enterprises Ltd. All Rights Reserved. ||
|| # This file may not be redistributed in whole or significant part. # ||
|| # ---------------- UTSPEED IS A SHARE SOFTWARE ---------------- # ||
|| # http://www.utspeed.com | http://www.utspeed.com/license.html # ||
|| #################################################################### ||
\*======================================================================*/

error_reporting(E_ALL & ~E_NOTICE);

function fetch_table_dump_sql($table, $fp = 0)
{
	global $DB_site;

	$tabledump = "DROP TABLE IF EXISTS $table;\n";
	$tabledump .= "CREATE TABLE $table (\n";

	$firstfield = 1;

	// get columns and spec
	$fields = $DB_site->query("SHOW FIELDS FROM $table");
	while ($field = $DB_site->fetch_array($fields))
	{
		if (!$firstfield)
		{
			$tabledump .= ",\n";
		}
		else
		{
			$firstfield = 0;
		}
		$tabledump .= "	 $field[Field] $field[Type]";
		if (!empty($field['Default']))
		{
			// get default value
			$tabledump .= " DEFAULT '$field[Default]'";
		}
		if ($field['Null'] != 'YES')
		{
			// can field be null
			$tabledump .= ' NOT NULL';
		}
		if (!empty($field['Extra']))
		{
			// any extra info?
			$tabledump .= " $field[Extra]";
		}
	}
	$DB_site->free_result($fields);

	// get keys list
	$keys = $DB_site->query("SHOW KEYS FROM $table");
	while ($key = $DB_site->fetch_array($keys))
	{
		$kname=$key['Key_name'];
		if ($kname != "PRIMARY" AND $key['Non_unique'] == 0)
		{
			$kname = "UNIQUE|$kname";
		}
		if(!is_array($index["$kname"]))
		{
			$index["$kname"] = array();
		}
		$index["$kname"][] = $key['Column_name'];
	}
	$DB_site->free_result($keys);

	// get each key info
	if (is_array($index))
	{
		foreach ($index AS $kname => $columns)
		{
			$tabledump .= ",\n";
			$colnames = implode($columns, ',');

			if($kname == 'PRIMARY'){
				// do primary key
				$tabledump .= "	 PRIMARY KEY ($colnames)";
			}
			else
			{
				// do standard key
				if (substr($kname, 0, 6) == 'UNIQUE')
				{
					// key is unique
					$kname = substr($kname, 7);
				}

				$tabledump .= "	 KEY $kname ($colnames)";
			}
		}
	}

	$tabledump .= "\n);\n\n";
	if ($fp)
	{
		fwrite($fp, $tabledump);
	}
	else
	{
		echo $tabledump;
	}

	// get data
	$rows = $DB_site->query("SELECT * FROM $table");
	$numfields=$DB_site->num_fields($rows);
	while ($row = $DB_site->fetch_array($rows, DBARRAY_NUM))
	{
		$tabledump = "INSERT INTO $table VALUES(";

		$fieldcounter = -1;
		$firstfield = 1;
		// get each field's data
		while (++$fieldcounter < $numfields)
		{
			if (!$firstfield)
			{
				$tabledump .= ', ';
			}
			else
			{
				$firstfield = 0;
			}

			if (!isset($row["$fieldcounter"]))
			{
				$tabledump .= 'NULL';
			}
			else
			{
				$tabledump .= "'" . $DB_site->escape_string($row["$fieldcounter"]) . "'";
			}
		}

		$tabledump .= ");\n";

		if ($fp)
		{
			fwrite($fp, $tabledump);
		}
		else
		{
			echo $tabledump;
		}
	}
	$DB_site->free_result($rows);

	//return $tabledump;
}

function construct_csv_backup($table, $separator, $quotes, $showhead)
{
	global $DB_site;

	// get columns for header row
	if ($showhead)
	{
		$firstfield = 1;
		$fields = $DB_site->query("SHOW FIELDS FROM $table");
		while ($field = $DB_site->fetch_array($fields))
		{
			if (!$firstfield)
			{
				$contents .= $separator;
			}
			else
			{
				$firstfield = 0;
			}
			$contents .= $quotes . $field['Field'] . $quotes;
		}
		$DB_site->free_result($fields);
	}
	$contents .= "\n";


	// get data
	$rows = $DB_site->query("SELECT * FROM $table");
	$numfields = $DB_site->num_fields($rows);
	while ($row = $DB_site->fetch_array($rows, DBARRAY_NUM))
	{

		$fieldcounter = -1;
		$firstfield = 1;
		while (++$fieldcounter < $numfields)
		{
			if (!$firstfield)
			{
				$contents .= $separator;
			}
			else
			{
				$firstfield = 0;
			}

			if (!isset($row["$fieldcounter"]))
			{
				$contents .= 'NULL';
			}
			else
			{
				$contents .= $quotes . addslashes($row["$fieldcounter"]) . $quotes;
			}
		}

		$contents .= "\n";
	}
	$DB_site->free_result($rows);

	return $contents;
}

/*======================================================================*\
|| ####################################################################
|| # Downloaded: 15:37, Fri Feb 6th 2004
|| # CVS: $RCSfile: adminfunctions_backup.php,v $ - $Revision: 1.1.1.1 $
|| ####################################################################
\*======================================================================*/
?>