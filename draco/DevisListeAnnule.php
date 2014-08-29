<?php
/*#########################################################################
#
#   name :       ListeDevis.php
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
loadPlugin(array('ZunoCore','ZView/DevisView'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('draco');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);


/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
aiJeLeDroit('devis', 05, 'web');
$req = new devisModel();
$r = $req->getDevisPerdu();
$datas['total'] = $req->getTotalDevisPerdu();
$datas['data'] = $r[1];
$datas['from'] = 0;
$datas['limit'] = 30;
$view = new devisView();
$sortie = $view->searchResult($datas,'result');
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie);
$out->Process();
?>
