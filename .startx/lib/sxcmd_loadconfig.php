<?php

function exportConf( $conf )
{
	foreach ($conf as $key => $value) {
		$GLOBALS[$key] = $value;
	}
}

function loadConf()
{
	$sxcmdpath = dirname(array_shift(get_included_files()));
	$confpath = $sxcmdpath."/sxcmd.json";
	$projectpath = dirname($sxcmdpath);

	$GLOBALS['EP'] = "[sxcmd-api]";
	$GLOBALS['PROJECT_DIR'] = $projectpath;
	$GLOBALS['DB_DUMP'] = $projectpath. "/.startx/db-dump";

	if ( file_exists($confpath) && ($str = file_get_contents($confpath)) !== false ) 
	{
		$CONFIG = json_decode($str, true);
		exportConf($CONFIG);
		return true;
	}
	return false;
}

?>
