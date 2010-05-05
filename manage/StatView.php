<?php
/*#########################################################################
#
#   name :       ActualiteManage.php
#   desc :       Authentification interface
#   categorie :  management page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');		// Declare global variables from config files
include ('../inc/core.inc');		// Load core library
loadPlugin(array('PageAdmin'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('admin');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);

/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
foreach($GLOBALS['CHANNEL_list'] as $key => $val)
{
	if('normal' == $val)
	{
		$disp = "";
	}
	else
	{
		$disp = "none";
	}

	if($GLOBALS['CHANNEL_'.$val]['RequiredRight'] >= $_SESSION['user']['right'])
	{
		$content .= Stat4Channel($val,$disp);
	}
}

/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($content);
$out->Process();
?>
