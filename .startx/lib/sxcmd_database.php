<?php

function exportDB()
{
	global $EP, $DB_DUMP, $USER, $PWD, $DB;

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
		shell_exec("mongoexport -d $DB -c $col -u $USER -p $PWD -o $DB_DUMP/dump_${DB}_$col.json --jsonArray");
}

function importDB()
{
	global $EP, $DB_DUMP, $USER, $PWD, $DB;

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
		shell_exec("mongoimport -d $DB -c $col -u $USER -p $PWD $DB_DUMP/dump_${DB}_$col.json --jsonArray");
		shell_exec("echo \"dump_${DB}_$col.json imported\" >&2");
	}
}

?>
