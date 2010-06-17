<?php
/*#########################################################################
#
#   desc :       include configuration library
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

$GLOBALS['generate_time_start'] = microtime(true);

//Load general.xml
exportConf2Global(dirname(__FILE__).'/../conf/general.xml');
// Search file in conf/permanent
$GLOBALS['REP']['appli'] = dirname(__FILE__).'/../';
$dir    = $GLOBALS['REP']['appli'].'conf/permanent/';
$dh	= opendir($dir);
while (false !== ($filename = readdir($dh)))
	{$files[] = $filename; }
// and add all config file found in this directory
for ($count = 0; $count < count ($files); $count++)
{
	$pos = strrchr ($files[$count], $GLOBALS['EXT']['conf']);
	if ($pos == $GLOBALS['EXT']['conf'])
		exportConf2Global($GLOBALS['REP']['appli'].'conf/permanent/'.$files[$count]);
}
exportConf2Global($GLOBALS['REP']['appli'].'conf/lang/'.$GLOBALS['LANGUE']['default'].".xml");


// Function to load XML file into $GLOBALS
function exportConf2Global($file)
{
	$ConfigFile	 = simplexml_load_file($file);
	if (!$ConfigFile)
	{
		$explodedName = explode("/",$file);
		$n = count($explodedName);
		echo	"Erreur lors du chargement du fichier de configuration <b>".$explodedName[$n-1].
			"</b><br/> L'application ne peut continuer, merci de prendre contact avec l'administrateur <a href='mailto:dev@startx.fr'>dev@startx.fr</a>";
	}

	foreach ($ConfigFile->group as $group)
	{
		$GROUP = (string) $group['name'];
		foreach ($group->value as $value)
		{
			$id = (string) $value['id'];
			$result = (string) $value;
			if ($result == 'TRUE' or $result == 'true')
				$GLOBALS[$GROUP][$id]= true;
			elseif ($result == 'FALSE' or $result == 'false')
				$GLOBALS[$GROUP][$id]= false;
			else  $GLOBALS[$GROUP][$id]= $result;
		}
	}
}
?>