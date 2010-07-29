<?php
/*#########################################################################
#
#   desc :       include core library
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * Chargement des librairies de fonctions
 */
unset($files);
$dir  = $GLOBALS['REP']['appli'].$GLOBALS['REP']['lib'];
$dh	= opendir($dir);
while (false !== ($filename = readdir($dh)))
    $files[] = $filename;
closedir($dh);
sort($files);

for ($count = 0; $count < count ($files); $count++)
    if ( strstr($files[$count], $GLOBALS['EXT']['lib']) ) {
	include ($GLOBALS['REP']['appli'].$GLOBALS['REP']['lib'].$files[$count]);
	$fil[] = $files[$count];
    }

/**
 * Chargement dynamique des classes
 */
function __autoload($maclasse) {
    require_once $GLOBALS['REP']['appli'].$GLOBALS['REP']['class'].$maclasse.$GLOBALS['EXT']['class'];
}

Logg::loggerInfo('core::loader() ~ Demarrage du script '.$_SERVER['SCRIPT_FILENAME'],$_SERVER['SCRIPT_FILENAME'],__FILE__.'@'.__LINE__);
/**
 * Chargement à la demande des plugins
 */
function loadPlugin($plugins) {
    if(!is_array($plugins)) $plugins = array($plugins);
    if(count($plugins) > 0) {
	foreach ($plugins as $name) {
	    include_once ($GLOBALS['REP']['appli'].$GLOBALS['REP']['plugin'].$name.$GLOBALS['EXT']['plugin']);
	    Logg::loggerNotice('loadPlugin() ~ Chargement du plugin '.$name.$GLOBALS['EXT']['plugin'],'',__FILE__.'@'.__LINE__);
	}

    }
    else Logg::loggerError('loadPlugin() ~ Liste de plugin inconnu',serialize($plugins),__FILE__.'@'.__LINE__);

}
?>
