<?php
/*#########################################################################
#
#   name :       FournisseurListe.php
#   desc :       liste des fournisseurs
#   categorie :  produit page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');		// Declare global variables from config files
include ('../inc/core.inc');		// Load core library
loadPlugin(array('ZunoCore','ZView/FournisseurView','ZView/ProduitView'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('produit');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->headerHTML->initCalendar();
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);


/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
aiJeLeDroit('produit', 05, 'web');
if($PC->rcvP['action'] == 'searchFournisseur') {
    if($PC->rcvP['id_fourn'] != '')
	$data['id_fourn'] = $PC->rcvP['id_fourn'];
    if($PC->rcvP['nom_ent'] != '')
	$data['e.nom_ent'] = $PC->rcvP['nom_ent'];
    if($PC->rcvP['cp_ent'] != '')
	$data['e.cp_ent'] = $PC->rcvP['cp_ent'];
    if($PC->rcvP['nom_cont'] != '')
	$data['c.nom_cont'] = $PC->rcvP['nom_cont'];
    if($PC->rcvP['actif'] != '')
	$data['actif'] = $PC->rcvP['actif'];


    if($PC->rcvP['ordre_fourn'] != '')
	$ordre = 'ORDER BY '.$PC->rcvP['ordre_fourn'];
    if($PC->rcvP['limit'] != '')
	$datas['limit'] = $PC->rcvP['limit'];
    else
	$datas['limit'] = '30';
    if($PC->rcvP['from'] != '')
	$datas['from'] = $PC->rcvP['from'];
    else
	$datas['from'] = '0';
    $req = new produitModel();
    $result = $req->getDataForSearchFournisseurWeb('', $datas['from'], 'ALL', $ordre, $data);
    $datas['total'] = $result[1][0]['counter'];
    if($datas['total'] == '')
	$datas['total'] = '0';
    if($datas['limit'] == 'ALL') {
	$datas['limit'] = $datas['total'];
	$datas['from'] = 0;
    }
    $result = $req->getDataForSearchFournisseurWeb('', $datas['from'], $datas['limit'], $ordre, $data);
    $datas['data'] = $result[1];
    $view = new fournisseurView();
    echo $view->searchResult($datas, 'result');
    exit;
}
else {
    $req = new produitModel();
    $total = $req->getDataForSearchFournisseurWeb('','', 'ALL', '', array('actif' => '1'));
    $total = $total[1][0]['counter'];
    $datas['total'] = $total;
    $result = $req->getDataForSearchFournisseurWeb('', '0', '30', 'ORDER BY id_fourn', array('actif' => '1'));
    $result = $result[1];
    $datas['data'] = $result;
    $datas['from'] = 0;
    $datas['limit'] = 30;
    $datas['ordre']['e.nom_ent'] = 'Par nom';
    $datas['ordre']['id_fourn'] = 'Par code fournisseur';
    $datas['ordre']['e.cp_ent'] = 'Par code postal';

    $view = new fournisseurView();
    $sortie = $view->searchResult($datas, '');
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($message.$sortie);
$out->Process();
?>
