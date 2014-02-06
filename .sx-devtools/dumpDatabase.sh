#!/usr/bin/php
<?php
$localRootPath = '/var/www/virtual/dev.startx.fr/api/api-lib/tmp/';
$user = 'dev';
$pass = 'dev';
$db = 'sxapi';
$collections = array(
	'redirect.hit',
	'sxapi.api',
	'sxapi.application',
	'sxapi.logs',
	'sxapi.models',
	'sxapi.plugins',
	'sxapi.resources',
	'sxapi.session',
	'sxapi.users',
	'system.indexes',
	'system.users',
);
foreach($collections as $col)
shell_exec("mongoexport -d $db -c $col -u $user -p $pass -o $localRootPath/dump.$db-$col.bson");

?>
