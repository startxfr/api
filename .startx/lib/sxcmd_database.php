<?php

$PROJECT_DIR = "/var/www/html/startx/api";
$DB_DUMP = "$PROJECT_DIR/.startx/db-dump2";

$user = 'admin';
$pwd = 'admin';
$db = 'sxapi';

function exportDB()
{
	global $EP, $DB_DUMP, $user, $pwd, $db;

	echo "$EP   dumping databases\n";
	$collections = array(
			 'startx.resources',
			 'sxapi.api',
			 'sxapi.application',
			 'sxapi.plugins',
			 'sxapi.resources',
			 'sxapi.users',
		 	 'system.indexes'
	);
	foreach($collections as $col)
		shell_exec("mongoexport -d $db -c $col -u $user -p $pwd -o $DB_DUMP/dump_${db}_$col.json --jsonArray");
}

function importDB()
{
	global $EP, $DB_DUMP, $user, $pwd, $db;

	echo "$EP   importing databases\n";
	$collections = array(
			 'startx.resources',
			 'sxapi.api',
			 'sxapi.application',
			 'sxapi.plugins',
			 'sxapi.resources',
			 'sxapi.users',
		 	 'system.indexes'
	);

	foreach($collections as $col) {
		shell_exec("mongoimport -d $db -c $col -u $user -p $pwd $DB_DUMP/dump_${db}_$col.json");
		shell_exec("echo \"dump_${db}_$col.json imported\" >&2");
	}
}

?>
