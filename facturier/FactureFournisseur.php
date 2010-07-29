<?php
/*#########################################################################
#
#   name :       Facture.php
#   desc :       Display page content
#   categorie :  page
#   ID :  	 $Id: Facture.php 3341 2009-11-10 13:54:45Z nm $
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');	// Declare global variables from config files
include ('../inc/core.inc');	// Load core library
loadPlugin(array('ZunoCore','ZView/FactureFournisseurView', 'ZunoRenduHTML', 'ZModels/ActualiteModel', 'ZModels/RenouvellementModel'));
loadPlugin(array('ZControl/GeneralControl'));

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
$info = new FactureFournisseurModel();
if($PC->rcvG['id_factfourn'] != '') {
    $sortie = viewFiche($PC->rcvG['id_factfourn'], 'factureFournisseur', '', 'non', 'web', true);
}
elseif(isset($PC->rcvG['newFichier']) and isset($_SESSION['temp']['upload'])) {
    $data['fichier_factfourn'] = $_SESSION['temp']['upload']['name'];
    unset($_SESSION['temp']['upload']);
    if($data['fichier_factfourn'] != "")
	$result = $info->update($PC->rcvG['newFichier'], $data);
    $sortie = viewFiche($PC->rcvG['newFichier'], 'factureFournisseur', '', 'non', 'web', true);
}

elseif($PC->rcvP['action'] == 'modifFactFourn' or $PC->rcvP['action'] == 'journalise') {
    aiJeLeDroit('factureFournisseur', 15, 'web');

    if($PC->rcvP['dateReglement_factfourn'] != '')
	$PC->rcvP['dateReglement_factfourn'] = substr($PC->rcvP['dateReglement_factfourn'], 6,4).substr($PC->rcvP['dateReglement_factfourn'], 3,2).substr($PC->rcvP['dateReglement_factfourn'],0,2);
    else
	unset($PC->rcvP['dateReglement_factfourn']);
    $PC->rcvP['datePaye_factfourn'] = substr($PC->rcvP['datePaye_factfourn'], 6,4).substr($PC->rcvP['datePaye_factfourn'], 3,2).substr($PC->rcvP['datePaye_factfourn'],0,2);
    if($PC->rcvP['actif_ren'] == 1) {
	if($PC->rcvP['ren_factfourn'] == "") {
	    $ren['type_ren'] = 'factureFournisseur';
	    $ren['idChamp_ren'] = $PC->rcvP['id_factfourn'];
	    $ren['actif_ren'] = 1;
	    $ren['periode_ren'] = $PC->rcvP['periode_ren'];
	    $ren['mail_ren'] = $PC->rcvP['mail_ren'];
	    $ren['statusChamp_ren'] = $PC->rcvP['statusChamp_ren'];
	    $ren['fin_ren'] = substr($PC->rcvP['fin_ren'], 6,4).substr($PC->rcvP['fin_ren'], 3,2).substr($PC->rcvP['fin_ren'],0,2);
	    $renModel = new RenouvellementModel();
	    $renModel->insert($ren);
	    $PC->rcvP['ren_factfourn'] = $renModel->getLastId();
	}
	else {
	    $ren['type_ren'] = 'factureFournisseur';
	    $ren['idChamp_ren'] = $PC->rcvP['id_factfourn'];
	    $ren['actif_ren'] = 1;
	    $ren['periode_ren'] = $PC->rcvP['periode_ren'];
	    $ren['mail_ren'] = $PC->rcvP['mail_ren'];
	    $ren['statusChamp_ren'] = $PC->rcvP['statusChamp_ren'];
	    $ren['fin_ren'] = substr($PC->rcvP['fin_ren'], 6,4).substr($PC->rcvP['fin_ren'], 3,2).substr($PC->rcvP['fin_ren'],0,2);
	    $renModel = new RenouvellementModel();
	    $renModel->update($PC->rcvP['ren_factfourn'], $ren);
	}
    }
    else {
	if($PC->rcvP['ren_factfourn'] != "") {
	    $renModel = new RenouvellementModel();
	    $renModel->desactiver($PC->rcvP['ren_factfourn']);
	}
    }
    if($PC->rcvP['action'] == 'journalise' and $PC->rcvP['status_factfourn'] < 4)
	$PC->rcvP['status_factfourn'] = '4';
    $result = $info->update($PC->rcvP['id_factfourn'], $PC->rcvP);
    if($result[0] and $PC->rcvP['action'] == 'journalise') {
	loadPlugin('ZModels/JournalBanqueModel');
	$jb = new journalBanqueModel();
	$jb->insertFromFactureFournisseur($PC->rcvP['id_factfourn']);
    }
    if($PC->rcvP['status_factfourn'] == 4) {
	$info= new actualiteModel();
	$data['type'] = 'factureFournisseur';
	$data['id_factfourn'] = $PC->rcvP['id_factfourn'];
	$data['status_factfourn'] = 4;
	$data['titre'] = "Payement de la facture fournisseur ".$PC->rcvP['id_factfourn'];
	$data['desc'] = "La facture ".$PC->rcvP['id_factfourn']." a été réglée.";
	$info->insert($data);
    }
    echo viewFiche($PC->rcvP['id_factfourn'], 'factureFournisseur', 'interneInfos', 'non', 'web', true, 'Sauvegardé');
    exit;
}
elseif ($PC->rcvG['action'] == 'voir') {
    $Doc = $info->getFichier($PC->rcvG['id']);
    PushFileToBrowser($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoFacture']['dir.factureFournisseur'].$Doc, $Doc);
    exit;
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie);
$out->Process();
?>
