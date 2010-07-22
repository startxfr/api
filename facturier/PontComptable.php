<?php
/*#########################################################################
#
#   name :       PontComptable.php
#   desc :       Display page content
#   categorie :  pontComptable
#   ID :  	 $Id: PontComptable.php 2814 2009-06-29 14:54:25Z nm $
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');	// Declare global variables from config files
include ('../inc/core.inc');	// Load core library
loadPlugin(array('ZunoCore','ZView/PontComptableView', 'ZunoRenduHTML'));
loadPlugin(array('ZControl/GeneralControl'));
loadPlugin(array('ZControl/DevisControl'));

// Whe get the page context
$PC = new PageContext('facturier');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->headerHTML->initCalendar();
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);
//var_dump($PC);exit;
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/

if($PC->rcvP['action'] == 'modifPontComptable') {
    aiJeLeDroit('pontComptable', 15, 'web');
    $PC->rcvP['id_pcth'] = $PC->rcvP['idPontComptable'];
    if(is_array($PC->rcvP['config_statutFact_pcth'])) {
	foreach($PC->rcvP['config_statutFact_pcth'] as $k)
	    $statutList .= $k.',';
	$PC->rcvP['config_statutFact_pcth'] = substr($statutList, 0, -1);
    }
    if(is_array($PC->rcvP['config_statutFactFourn_pcth'])) {
	foreach($PC->rcvP['config_statutFactFourn_pcth'] as $k)
	    $statutFournList .= $k.',';
	$PC->rcvP['config_statutFactFourn_pcth'] = substr($statutFournList, 0, -1);
    }
    if($PC->rcvP['config_dateDebut_pcth'] != '')
	$PC->rcvP['config_dateDebut_pcth'] = DateTimestamp2Univ(DateHuman2Timestamp($PC->rcvP['config_dateDebut_pcth']));
    if($PC->rcvP['config_dateFin_pcth'] != '')
	$PC->rcvP['config_dateFin_pcth'] = DateTimestamp2Univ(DateHuman2Timestamp($PC->rcvP['config_dateFin_pcth'].' 23:59:59'));
    $sql = new PontComptableModel();
    $rs = $sql->updatePontComptableHisto($PC->rcvP, $PC->rcvP['idPontComptable']);
    echo '<redirection style="display:none">../facturier/PontComptableHisto.php</redirection>';
    exit;
}
elseif($PC->rcvP['action'] == 'addPontComptableHisto') {
    aiJeLeDroit('pontComptable', 20, 'web');
    $sql = new PontComptableModel();

    $PC->rcvP['date_pcth'] = strftime('%F %T');
    if(trim($PC->rcvP['nom_pcth']) == '') {
	$nom = '';
	if($PC->rcvP['config_hasFactureClient_pcth'] == '1' and $PC->rcvP['config_hasFactureFourn_pcth'] == '1')
	    $nom.= 'Factures client et fournisseur ';
	elseif($PC->rcvP['config_hasFactureClient_pcth'] == '1')
	    $nom.= 'Factures client ';
	elseif($PC->rcvP['config_hasFactureFourn_pcth'] == '1')
	    $nom.= 'Factures fournisseur ';
	if($PC->rcvP['config_dateDebut_pcth'] != '' and $PC->rcvP['config_dateFin_pcth'] != '')
	    $nom.= 'du '.$PC->rcvP['config_dateDebut_pcth'].' au '.$PC->rcvP['config_dateFin_pcth'];
	elseif($PC->rcvP['config_dateDebut_pcth'] != '')
	    $nom.= 'depuis le '.$PC->rcvP['config_dateDebut_pcth'];
	elseif($PC->rcvP['config_dateFin_pcth'] != '')
	    $nom.= 'jusqu\'au '.$PC->rcvP['config_dateFin_pcth'];
	$PC->rcvP['nom_pcth'] = $nom;
    }
    if(is_array($PC->rcvP['config_statutFact_pcth'])) {
	foreach($PC->rcvP['config_statutFact_pcth'] as $k)
	    $statutList .= $k.',';
	$PC->rcvP['config_statutFact_pcth'] = substr($statutList, 0, -1);
    }
    if(is_array($PC->rcvP['config_statutFactFourn_pcth'])) {
	foreach($PC->rcvP['config_statutFactFourn_pcth'] as $k)
	    $statutFournList .= $k.',';
	$PC->rcvP['config_statutFactFourn_pcth'] = substr($statutFournList, 0, -1);
    }
    if($PC->rcvP['config_dateDebut_pcth'] != '')
	$PC->rcvP['config_dateDebut_pcth'] = DateTimestamp2Univ(DateHuman2Timestamp($PC->rcvP['config_dateDebut_pcth']));
    if($PC->rcvP['config_dateFin_pcth'] != '')
	$PC->rcvP['config_dateFin_pcth'] = DateTimestamp2Univ(DateHuman2Timestamp($PC->rcvP['config_dateFin_pcth'].' 23:59:59'));
    $rs = $sql->insertPontComptableHisto($PC->rcvP);
    if($rs[0]) {
	echo '<redirection style="display:none">../facturier/PontComptableHisto.php</redirection>';
	exit;
    }
    $view = new PontComptableView();
    echo $view->creer($PC->rcvP);
    exit;
}
elseif($PC->rcvG['action'] == 'get' and $PC->rcvG['id_pcth'] != '') {
    aiJeLeDroit('pontComptable', 10, 'web');
    $sql = new PontComptableModel();
    $data = $sql->getDataFromID($PC->rcvG['id_pcth']);
    $rs = $sql->genererPontComptableHistoTmpFileWithData($data[1][0]);
    PushFileToBrowser($GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$rs,'','application/download');
    exit;
}
elseif($PC->rcvP['action'] == 'generer') {
    aiJeLeDroit('pontComptable', 10, 'web');
    $sql = new PontComptableModel();
    if($PC->rcvP['config_dateDebut_pcth'] != '')
	$PC->rcvP['config_dateDebut_pcth'] = DateTimestamp2Univ(DateHuman2Timestamp($PC->rcvP['config_dateDebut_pcth']));
    if($PC->rcvP['config_dateFin_pcth'] != '')
	$PC->rcvP['config_dateFin_pcth'] = DateTimestamp2Univ(DateHuman2Timestamp($PC->rcvP['config_dateFin_pcth'].' 23:59:59'));
    if(is_array($PC->rcvP['config_statutFact_pcth'])) {
	foreach($PC->rcvP['config_statutFact_pcth'] as $k)
	    $statutList .= $k.',';
	$PC->rcvP['config_statutFact_pcth'] = substr($statutList, 0, -1);
    }
    if(is_array($PC->rcvP['config_statutFactFourn_pcth'])) {
	foreach($PC->rcvP['config_statutFactFourn_pcth'] as $k)
	    $statutFournList .= $k.',';
	$PC->rcvP['config_statutFactFourn_pcth'] = substr($statutFournList, 0, -1);
    }
    $rs = $sql->genererPontComptableHistoTmpFileWithData($PC->rcvP);
    PushFileToBrowser($GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$rs,'','application/download');
    exit;
}
elseif($PC->rcvG['id_pcth'] != '') {
    $sortie = viewFiche($PC->rcvG['id_pcth'], 'pontComptable', '', 'non', 'web');
}
else {
    aiJeLeDroit('pontComptable', 20, 'web');
    $view = new PontComptableView();
    $sortie = $view->creer(array());
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie);
$out->Process();
?>
