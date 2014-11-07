<?php

function exportDB()
{
	global $EP, $DB_DUMP, $USER, $PWD, $DB;

	echo "$EP   dumping databases\n";
	$mongo = new MongoClient();
	$list_col = $mongo->selectDB($DB)->getCollectionNames();
	$ex_col = array(
			 'startx.logs',
			 'logs'
	);
	$collections = array_diff($list_col, $ex_col);

	foreach($collections as $col)
		shell_exec("mongoexport -d $DB -c $col -u $USER -p $PWD -o $DB_DUMP/dump_$col.json --jsonArray");
		shell_exec("echo \"dump_$col.json exported\" >&2");
}

function importDB()
{
	global $EP, $DB_DUMP, $USER, $PWD, $DB;

	echo "$EP   importing databases\n";
	$mongo = new MongoClient();
	$list_col = $mongo->selectDB($DB)->getCollectionNames();
	$ex_col = array(
			 'startx.logs',
			 'logs'
	);
	$collections = array_diff($list_col, $ex_col);

	foreach($collections as $col) {
		shell_exec("mongoimport -d $DB -c $col -u $USER -p $PWD $DB_DUMP/dump_$col.json --jsonArray");
		shell_exec("echo \"dump_$col.json imported\" >&2");
	}
}

?>
