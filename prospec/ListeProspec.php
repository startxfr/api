<?php
/*#########################################################################
#
#   name :       ListeProspec.php
#   desc :       Prospection list for sxprospec
#   categorie :  prospec page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');		// Declare global variables from config files
include ('../inc/core.inc');		// Load core library
loadPlugin(array('ZunoCore','ZView/ProspecView','ZView/ContactView'));

/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('prospec');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->headerHTML->initCalendar();
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);


/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
// Si premier arrivée sur le moteur de recherche
aiJeLeDroit('contact', 105, 'web');
if (!isset($_SESSION['ZunoRelanceSearch'])) {
    $_SESSION['ZunoRelanceSearch']['debut'] = DateUniv2Human('','simpleLong');
    $_SESSION['ZunoRelanceSearch']['relactive_app'] = '1';
}
else {
    if (count($PC->rcvP) > 0) {
	$_SESSION['ZunoRelanceSearch'] = array_merge($_SESSION['ZunoRelanceSearch'],$PC->rcvP);
    }
}

$sortie  = contactView::BoxSearchAppelForm($_SESSION['ZunoRelanceSearch']);
$sortie .= contactView::BoxSearchAppelResult($_SESSION['ZunoRelanceSearch'],'');
$_SESSION['ZunoRelanceSearch'] = array();
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie);
$out->Process();
?>
