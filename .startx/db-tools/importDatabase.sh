#!/usr/bin/php
<?php
$localRootPath = '/secure/dev/startx/api/.startx/db-dump';
$user = 'dev';
$pass = 'dev';
$db = 'sxapi';
$collections = array(
         'startx.models',
         'startx.resources',
         'sxapi.api',
         'sxapi.application',
         'sxapi.models',
         'sxapi.plugins',
         'sxapi.resources',
         'sxapi.users',
	 'system.indexes'
	);


foreach($collections as $col) {
shell_exec("mongoimport -d $db -c $col -u $user -p $pass $localRootPath/dump_${db}_$col.json");
shell_exec("echo \"dump_${db}_$col.json imported\" >&2");
}
?>
