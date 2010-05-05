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
$sortie = new PageAdminPortlet($_SESSION["language"]);

foreach($GLOBALS['CHANNEL_list'] as $id => $channel)
{
	$DoDisplay = FALSE;
	if ($GLOBALS['CHANNEL_'.$id]['PageManageRequiredRight'] == '')
		$DoDisplay = TRUE;
	else
		foreach(explode(",",$GLOBALS['CHANNEL_'.$id]['PageManageRequiredRight']) as $right)
		if ($_SESSION['user']['right'] == $right)
			$DoDisplay = TRUE;
	
	if ($DoDisplay) {
		$sortie->SetChannel($id);
		if($PC->rcvG['order'] != '')
			$sortie->SetOrder($PC->rcvG['order']);
		$sortie->Type("MANAGE");
		$corps = $sortie->Process();
		if ($id != 'normal')
			 $option = 'close';
		else $option = '';
		$titre 	= 'Gestion des pages du channel '.$channel;
		$content .= generateZBox($titre, $titre, $corps, '', 'Manage'.$id, $option);
		$DoDisplay = FALSE;
	}
}

/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($content);
$out->Process();
?>
