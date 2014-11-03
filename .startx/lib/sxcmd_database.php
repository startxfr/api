<?php

$PROJECT_DIR = "/var/www/html/startx/api";
$DBTOOLS = "$PROJECT_DIR/.startx/db-tools";

function exportDB()
{
	global $EP, $DBTOOLS;

	echo "$EP   dumping databases\n";
	`. $DBTOOLS/dumpDatabase.sh`;
}

function importDB()
{
	global $EP, $DBTOOLS;

	echo "$EP   importing databases\n";
	`. $DBTOOLS/importDatabase.sh`;
}

?>
