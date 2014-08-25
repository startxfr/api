#!/usr/bin/php
<?php
$localRootPath = '/secure/dev/startx/api/api-lib/tmp/';
$user = 'dev';
$pass = 'dev';
$db = 'sxapi';
$collections = array(
	'manager.resources',
        'startx.logs',
        'startx.models',
        'startx.resources',
        'sxapi.api',
        'sxapi.application',
        'sxapi.plugins',
        'sxapi.session',
        'sxapi.users',
        'system.indexes',
        'system.users'
);
foreach($collections as $col)
shell_exec("mongoexport -d $db -c $col -u $user -p $pass -o $localRootPath/dump.$db-$col.bson");

?>
