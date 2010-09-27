<?php
/*#########################################################################
#
#   name :       page.php
#   desc :       Display page content
#   categorie :  page
#   ID :  	 $Id: FactureCreate.php 3354 2009-11-16 13:24:15Z nm $
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');	// Declare global variables from config files
include ('../inc/core.inc');	// Load core library
loadPlugin(array('ZunoCore','ZView/FactureFournisseurView', 'ZunoRenduHTML', 'ZModels/ActualiteModel'));

// Whe get the page context
$PC = new PageContext('facturier');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
aiJeLeDroit('factureFournisseur', 20, 'web');
if($PC->rcvP['action'] == 'addFactFourn') {
    $PC->rcvP['datePaye_factfourn'] = substr($PC->rcvP['datePaye_factfourn'], 6,4).substr($PC->rcvP['datePaye_factfourn'], 3,2).substr($PC->rcvP['datePaye_factfourn'],0,2);

    $PC->rcvP['montantTTC_factfourn'] = prepareNombreTraitement($PC->rcvP['TTC']);
    $PC->rcvP['tauxTVA_factfourn'] = prepareNombreTraitement($PC->rcvP['taux_tva']);


    $model = new FactureFournisseurModel();
    $result = $model->insert($PC->rcvP);
    if($result[0]) {

	$id = $model->getLastId();

	header('Location:FactureFournisseur.php?id_factfourn='.$id);
	exit;
    }
    else {
	$view = new FactureFournisseurView();
	$model = new FactureFournisseurModel();
	$datas['mode'] = $model->getModeReglement();
	$sortie = $view->creer($datas, 'erreur', $result[1]);
    }

}

else {
    $view = new FactureFournisseurView();
    $model = new FactureFournisseurModel();
    $datas['mode'] = $model->getModeReglement();
    $sortie = $view->creer($datas);
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie);
$out->Process();
?>
