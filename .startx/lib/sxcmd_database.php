<?php

function exportDB()
{
	global $EP, $config;
        $confDb = $config['datatbases']['nosql'];
	echo "$EP   dumping database ".$confDb['database']."\n";
	$mongo = new MongoClient();
	$list_col = $mongo->selectDB($confDb['database'])->getCollectionNames();
	$ex_col = explode(",",$confDb['exclude_table']);
	$collections = array_diff($list_col, $ex_col);
	foreach($collections as $col)
		shell_exec("mongoexport -d ".$confDb['database']." -c $col -u ".$confDb['user']." -p ".$confDb['pwd']." -o ".$config['project']['path'].'/'.$confDb['dump_dir']."/"."dump_$col.json --jsonArray");
		shell_exec("echo \"dump_$col.json exported\" >&2");
}

function importDB()
{
	global $EP, $config;
        $confDb = $config['datatbases']['nosql'];
	echo "$EP   importing database ".$confDb['database']."\n";
	$mongo = new MongoClient();
	$list_col = $mongo->selectDB($confDb['database'])->getCollectionNames();
	$ex_col = explode(",",$confDb['exclude_table']);
	$collections = array_diff($list_col, $ex_col);
	foreach($collections as $col) {
		shell_exec("mongoimport -d ".$confDb['database']." -c $col -u ".$confDb['user']." -p ".$confDb['pwd']." ".$config['project']['path'].'/'.$confDb['dump_dir']."/"."dump_$col.json --jsonArray");
		shell_exec("echo \"dump_$col.json imported\" >&2");
	}
}

?>
