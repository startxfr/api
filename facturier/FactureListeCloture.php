<?php
/*#########################################################################
#
#   name :       ListeCommande.php
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
loadPlugin(array('ZunoCore','ZView/FactureView'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('facturier');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->headerHTML->initCalendar();
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);

ini_set("memory_limit","20m");
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
aiJeLeDroit('facture', 05, 'web');

$data['status_fact'] = 6;
$datas['from'] = 0;
$datas['limit'] = 30;
$datas['order'] = 'id_fact';
$datas['orderSens'] = 'DESC';
$ordre = 'ORDER BY '.$datas['order'].' '.$datas['orderSens'];
$req = new factureModel();
$total = $req->getDataForSearchWeb('', $datas['from'], 'ALL', '', $data);
$datas['total'] = $total[1][0]['COUNT(*)'];
$result = $req->getDataForSearchWeb('', $datas['from'], $datas['limit'],$ordre, $data);
$datas['data'] = $result[1];
$datas['status'] = $req->getAllStatusFacture();
$view = new factureView();
$mess = ($PC->rcvG['mess']!='') ? $PC->rcvG['mess'] : '';
$sortie = $view->searchResult($datas, $mess);

/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie);
$out->Process();
?>
