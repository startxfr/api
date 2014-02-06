#!/usr/bin/php
<?php
$localRootPath = '/var/www/virtual/api.startx.fr/v2/api-lib/tmp/';
$user = 'dev';
$pass = 'dev';
$db = 'sxapi2';
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
    echo "mongoimport -d $db -c $col -u $user -p $pass $localRootPath/dump.$db-$col.bson";
shell_exec("mongoimport -d $db -c $col -u $user -p $pass $localRootPath/dump.$db-$col.bson");

?>
