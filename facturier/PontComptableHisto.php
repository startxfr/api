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
loadPlugin(array('ZunoCore','ZView/PontComptableView'));
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
aiJeLeDroit('pontComptable', 05, 'web');
if($PC->rcvP['action'] == 'searchPontComptable') {
    if($PC->rcvP['nom_pcth'] != '')
	$data['nom_pcth'] = $PC->rcvP['nom_pcth'];
    if($PC->rcvP['dateStart_pcth'] != '')
	$data['dateStart_pcth'] = DateTimestamp2Univ(DateHuman2Timestamp($PC->rcvP['dateStart_pcth']));
    if($PC->rcvP['dateEnd_pcth'] != '')
	$data['dateEnd_pcth'] = DateTimestamp2Univ(DateHuman2Timestamp($PC->rcvP['dateEnd_pcth'].' 23:59:59'));
    if($PC->rcvP['ordre_pcth'] != '')
	$ordre = 'ORDER BY '.$PC->rcvP['ordre_pcth'];
    if($PC->rcvP['limit'] != '')
	$datas['limit'] = $PC->rcvP['limit'];
    else $datas['limit'] = '30';
    if($PC->rcvP['from'] != '')
	$datas['from'] = $PC->rcvP['from'];
    else $datas['from'] = '0';
    $req = new pontComptableModel();
    $result = $req->getDataForSearchPontComptableHistoWeb('', $datas['from'], 'ALL', $ordre, $data);
    $datas['total'] = $result[1][0]['counter'];
    if($datas['total'] == '')
	$datas['total'] = '0';
    if($datas['limit'] == 'ALL') {
	$datas['limit'] = $datas['total'];
	$datas['from'] = 0;
    }
    $result = $req->getDataForSearchPontComptableHistoWeb('', $datas['from'], $datas['limit'], $ordre, $data);
    $datas['data'] = $result[1];
    $view = new pontComptableView();
    echo $view->searchResult($datas,$PC->rcvP['result']);
    exit;
}
elseif ($PC->rcvP['action'] == 'groupedAction') {
    $bddtmp = new PontComptableModel();
    $list = $PC->rcvP['select'];
    $action = $PC->rcvP['groupedAction'];

    if (count($list) > 0) {
	$prodListString = '';
	foreach ($list as $k => $prod)
	    $prodListString .= ', \''.$prod.'\'';
	$prodListString = substr($prodListString,1);
    }

    if($action == 'delete') {
	$req = "SELECT id_pcth,nom_pcth FROM pontcomptable_histo
		WHERE id_pcth IN (".$prodListString.")
		GROUP BY id_pcth ORDER BY id_pcth ASC";
	$bddtmp->makeRequeteFree($req);
	$res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $prod) {
		pontComptableModel::markDeletePontComptableInDB($prod['id_pcth'],$PC->rcvP);
		$message.= "Fichier d'export comptable ".$prod['id_pcth']." - ".$prod['nom_pcth']." supprimé\n";
	    }
	    //$bddtmp->addActualite('', 'free', 'Lot de '.count($res).' fichiers d\'export comptable supprimés', $message);
	    $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucun des fichiers d'export comptable séléctionnés ne peut être supprimé</span>";
    }
    elseif($action == 'changeAttribute') {
	$req = "SELECT id_pcth,nom_pcth FROM pontcomptable_histo
		WHERE id_pcth IN (".$prodListString.")
		GROUP BY id_pcth ORDER BY id_pcth ASC";
	$bddtmp->makeRequeteFree($req);
	$res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $prod) {
		pontComptableModel::changeAttributePontComptableInDB($prod['id_pcth'],$PC->rcvP);
		$message.= "changement des attributs du fichier d'export comptable ".$prod['id_pcth']." - ".$prod['nom_pcth']."\n";
	    }
	    //$bddtmp->addActualite('', 'free', 'Lot de '.count($res).' fichiers d\'export comptable modifiés', $message);
	    $message = "<span class=\"importantblue\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucun des fichiers d'export comptable séléctionnés ne peut être modifié</span>";
    }
    $message = $message.'<script type="text/javascript">setTimeout("window.location.href = \'PontComptableHisto.php\';",1000);</script>';
}
else {
    $req = new pontComptableModel();
    $total = $req->getDataForSearchPontComptableHistoWeb('', '0', 'ALL', 'ORDER BY nom_pcth', $data);
    $total = $total[1][0]['counter'];
    $datas['total'] = $total;
    $result = $req->getDataForSearchPontComptableHistoWeb('', '0', '30', 'ORDER BY nom_pcth', $data);
    $result = $result[1];
    $datas['data'] = $result;
    $datas['form']['fournisseur_pcth'] = $data['fournisseur_id'];
    $datas['from'] = 0;
    $datas['limit'] = 30;
    $view = new pontComptableView();
    $sortie = $view->searchResult($datas, '');
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($message.$sortie);
$out->Process();
?>
