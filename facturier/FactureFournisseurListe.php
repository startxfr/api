<?php
/*#########################################################################
#
#   name :       ListeCommande.php
#   desc :       Prospection list for sxprospec
#   categorie :  prospec page
#   ID :  	 $Id: FactureListe.php 3342 2009-11-10 14:20:42Z nm $
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');		// Declare global variables from config files
include ('../inc/core.inc');		// Load core library
loadPlugin(array('ZunoCore','ZView/FactureFournisseurView', 'ZunoRenduHTML'));
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


/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
aiJeLeDroit('factureFournisseur', 05, 'web');
if($PC->rcvP['action'] == 'searchFactFourn') {
    if($PC->rcvP['nom_ent'] != '')
        $data['nom_ent'] = $PC->rcvP['nom_ent'];
    if($PC->rcvP['titre_factfourn'] != '')
        $data['titre_factfourn'] = $PC->rcvP['titre_factfourn'];
    if($PC->rcvP['nom_cont'] != '')
        $data['nom_cont'] = $PC->rcvP['nom_cont'];
    if($PC->rcvP['montantHT_factfourn'] != '')
        $data['montantHT_factfourn'] = $PC->rcvP['montantHT_factfourn'];
    if($PC->rcvP['montantHT_factfourn2'] != '')
        $data['montantHT_factfourn2'] = $PC->rcvP['montantHT_factfourn2'];
    if($PC->rcvP['status_factfourn'] != '')
        $data['status_factfourn'] = $PC->rcvP['status_factfourn'];
    if($PC->rcvP['order'] != '')
	$ordre = 'ORDER BY '.$PC->rcvP['order'].' '.$PC->rcvP['orderSens'];
    if($PC->rcvP['limit'] != '')
         $datas['limit'] = $PC->rcvP['limit'];
    else $datas['limit'] = '30';
    if($PC->rcvP['from'] != '')
         $datas['from'] = $PC->rcvP['from'];
    else $datas['from'] = '0';
    $req = new FactureFournisseurModel();
    $result = $req->getDataForSearch('', $datas['from'], 'ALL', $ordre, $data);
    $datas['total'] = $result[1][0]['counter'];
    if($datas['limit'] == 'ALL') {
        $datas['limit'] = $datas['total'];
        $datas['from'] = 0;
    }
    $datas['order'] = $PC->rcvP['order'];
    $datas['orderSens'] = $PC->rcvP['orderSens'];
    $result = $req->getDataForSearch('', $datas['from'], $datas['limit'], $ordre, $data);
    foreach($result[1] as $k => $v)
        $datas['ren'][$k] = $req->getRenouvellement($v['id_factfourn']);
    $datas['data'] = $result[1];
    $view = new FactureFournisseurView();
    echo $view->searchResult($datas, 'result');
    exit;
}
else {
    if($PC->rcvG['status_factfourn'] != '')
        $data['status_factfourn'] = $PC->rcvG['status_factfourn'];
    $datas['from'] = 0;
    $datas['limit'] = 30;
    $datas['order'] = 'id_factfourn';
    $datas['orderSens'] = 'DESC';
    $ordre = 'ORDER BY '.$datas['order'].' '.$datas['orderSens'];
    $req = new FactureFournisseurModel();
    $total = $req->getDataForSearch('', '0', 'ALL',$ordre, $data);
    $total = $total[1][0]['counter'];
    $datas['total'] = $total;
    $result = $req->getDataForSearch('', $datas['from'], $datas['limit'],$ordre, $data);
    foreach($result[1] as $k => $v)
        $datas['ren'][$k] = $req->getRenouvellement($v['ren_factfourn']);
    $datas['data'] = $result[1];
    $datas['status'] = $req->getStatutFactFourn();
    $datas['from'] = 0;
    $datas['limit'] = 30;
    $view = new FactureFournisseurView();
    $sortie = $view->searchResult($datas, '');
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($message.$sortie);
$out->Process();
?>
