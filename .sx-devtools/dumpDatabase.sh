#!/usr/bin/php
<?php
// conf du shell
$shell_user = trim(shell_exec('whoami'));
$shell_path = trim(shell_exec('pwd'));
$localRootPath = '/secure/dev/sxa/';
// conf de la base
$mysqlHost = '127.0.0.1';
$mysqlUser = 'dev';
$mysqlPass = 'dev';


// PERSONNALISATION
$mysqlDatabase = 'sxa';
$projectPath = 'tmp/';


// lancement du dump
$fileSchema = $projectPath.'sqlSchema.sql';
$fileData = $projectPath.'sqlData.sql';
fwrite(STDOUT, "Exportation du schema $mysqlDatabase : ");
shell_exec("mysqldump -d --lock-all-tables --host $mysqlHost --user $mysqlUser -p$mysqlPass $mysqlDatabase > $localRootPath$fileSchema ; ");
fwrite(STDOUT, "$fileSchema OK \n");
fwrite(STDOUT, "Exportation des données $mysqlDatabase : ");
shell_exec("mysqldump  --skip-opt  --no-create-info --lock-all-tables --host $mysqlHost --user $mysqlUser -p$mysqlPass $mysqlDatabase > $localRootPath$fileData ; ");
fwrite(STDOUT, "$fileData OK \n");
?>
