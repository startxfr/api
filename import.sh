#!/usr/bin/php
<?php
$rootManager = dirname(__FILE__).'/superbase.zuno.fr';
$srv = 'localhost';
$login='zunosb';
$pass='9MzHq8RejLHW52uX';
$db= 'PROD_zuno_superbase';
echo shell_exec("echo 'DROP DATABASE $db;' | mysql --default-character-set=utf8 -h $srv -u $login -p$pass");
echo shell_exec("echo 'CREATE DATABASE $db;' | mysql --default-character-set=utf8 -h $srv -u $login -p$pass");
echo shell_exec("mysql --default-character-set=utf8 -h $srv -u $login -p$pass $db  < $rootManager/tmp/sqlSchema.sql");
echo shell_exec("mysql --default-character-set=utf8 -h $srv -u $login -p$pass $db  < $rootManager/tmp/sqlData.sql");
echo "Fin de la mise à jour de la base $db\n";




$rootManager = dirname(__FILE__).'/zuno.fr';
$srv = 'localhost';
$login='zunosite';
$pass='secretdemerde';
$db= 'PROD_zuno_siteweb';
echo shell_exec("echo 'DROP DATABASE $db;' | mysql --default-character-set=utf8 -h $srv -u $login -p$pass");
echo shell_exec("echo 'CREATE DATABASE $db;' | mysql --default-character-set=utf8 -h $srv -u $login -p$pass");
echo shell_exec("mysql --default-character-set=utf8 -h $srv -u $login -p$pass $db  < $rootManager/tmp/sqlSchema.sql");
echo shell_exec("mysql --default-character-set=utf8 -h $srv -u $login -p$pass $db  < $rootManager/tmp/sqlData.sql");
echo "Fin de la mise à jour de la base $db\n";



$rootManager = dirname(__FILE__).'/sxa.startx.fr';
$srv = 'localhost';
$login='zunoSxaProd';
$pass='zunoSxaProd';
$db= 'PROD_sxa';
echo shell_exec("echo 'DROP DATABASE $db;' | mysql --default-character-set=utf8 -h $srv -u $login -p$pass");
echo shell_exec("echo 'CREATE DATABASE $db;' | mysql --default-character-set=utf8 -h $srv -u $login -p$pass");
echo shell_exec("mysql --default-character-set=utf8 -h $srv -u $login -p$pass $db  < $rootManager/tmp/sqlSchema.sql");
echo shell_exec("mysql --default-character-set=utf8 -h $srv -u $login -p$pass $db  < $rootManager/tmp/sqlData.sql");
echo "Fin de la mise à jour de la base $db\n";

?>

