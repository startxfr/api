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

foreach($collections as $col)
	shell_exec("mongoexport -d $db -c $col -u $user -p $pass -o $localRootPath/dump_$db_$col.json");

?>
