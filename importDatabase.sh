#!/usr/bin/php
<?php
$localRootPath = '/var/www/virtual/api.startx.fr/api-lib/tmp/';
$user = 'username';
$pass = 'password';
$db = 'sxapi';
$collections = array(
	'redirect.hit',
	'sxapi.api',
	'sxapi.application',
	'sxapi.logs',
	'sxapi.models',
	'sxapi.ressources',
	'sxapi.session',
	'sxapi.users',
	'system.indexes',
	'system.users',
);
foreach($collections as $col) 
shell_exec("mongoimport -d $db -c $col -u $user -p $pass $localRootPath/dump.$db-$col.bson");

?>
