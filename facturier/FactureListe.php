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


/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
aiJeLeDroit('facture', 05, 'web');
if($PC->rcvP['action'] == 'searchFacture') {
    if($PC->rcvP['entreprise_fact'] != '')
        $data['entreprise_fact'] = $PC->rcvP['entreprise_fact'];
    if($PC->rcvP['titre_fact'] != '')
        $data['titre_fact'] = $PC->rcvP['titre_fact'];
    if($PC->rcvP['affaire_dev'] != '')
        $data['affaire_dev'] = $PC->rcvP['affaire_dev'];
    if($PC->rcvP['status_fact'] != '')
        $data['status_fact'] = $PC->rcvP['status_fact'];
    if($PC->rcvP['commercial_fact'] != '')
        $data['commercial_fact'] = $PC->rcvP['commercial_fact'];
    if($PC->rcvP['cp_ent'] != '')
        $data['cp_ent'] = $PC->rcvP['cp_ent'];
    if($PC->rcvP['sommeHT_fact'] != '')
        $data['sommeHT_fact'] = $PC->rcvP['sommeHT_fact'];
    if($PC->rcvP['sommeHT_fact2'] != '')
        $data['sommeHT_fact2'] = $PC->rcvP['sommeHT_fact2'];
    if($PC->rcvP['type_fact'] != '')
        $data['type_fact'] = $PC->rcvP['type_fact'];
    if($PC->rcvP['order'] != '') {
	$datas['order'] = $PC->rcvP['order'];
	$datas['orderSens'] = $PC->rcvP['orderSens'];
    }
    else {
	$datas['order'] = 'id_fact';
	$datas['orderSens'] = 'DESC';
    }
    $ordre = 'ORDER BY '.$datas['order'].' '.$datas['orderSens'];
    if($PC->rcvP['limit'] != '')
         $datas['limit'] = $PC->rcvP['limit'];
    else $datas['limit'] = '30';
    if($PC->rcvP['from'] != '')
         $datas['from'] = $PC->rcvP['from'];
    else $datas['from'] = '0';
    $req = new factureModel();
    $result = $req->getDataForSearchWeb('', $datas['from'], 'ALL', $ordre, $data);
    $datas['total'] = $result[1][0]['counter'];
    if($datas['limit'] == 'ALL') {
        $datas['limit'] = $datas['total'];
        $datas['from'] = 0;
    }
    $result = $req->getDataForSearchWeb('', $datas['from'], $datas['limit'], $ordre, $data);
    $datas['data'] = $result[1];
    $view = new factureView();
    echo $view->searchResult($datas, 'result');
    exit;
}
elseif($PC->rcvP['action'] == 'exportTableur')
{
	$req = new factureModel();
	$result = $req->getDataForExportTableur($PC->rcvP['select']);
	$gnose = new factureGnose();
	$file = $gnose->FactureExportTableurConverter($result[1],$PC->rcvP['exportType']);
	PushFileToBrowser($file);
	exit;
}
elseif ($PC->rcvP['action'] == 'groupedAction') {
    $bddtmp = new FactureModel();
    $list = $PC->rcvP['select'];
    $action = $PC->rcvP['groupedAction'];

    if (count($list) > 0) {
	$factListString = '';
	foreach ($list as $k => $fact)
	    $factListString .= ', \''.$fact.'\'';
	$factListString = substr($factListString,1);
	$reqAdd = 'AND id_fact IN ('.$factListString.')';
    }

    if($action == 'reinit') {
	$req = "SELECT facture.id_fact FROM facture
		LEFT JOIN commande ON commande.id_cmd = facture.commande_fact
		LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_fact IN (".$factListString.")
		AND (archived_aff = '0' OR archived_aff IS NULL)
		AND (actif_aff = '1' OR actif_aff IS NULL)
		AND status_fact NOT IN (6,7)
		GROUP BY id_fact ORDER BY id_fact ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $fact) {
		factureModel::markReinitFactureInDB($fact['id_fact'],$PC->rcvP);
		$message.= "re-initialisation de la facture ".$fact['id_fact']." \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' factures re-initialisées', $message);
	    $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des factures séléctionnées ne peuvent être re-initialisée</span>";
    }
    elseif($action == 'valide') {
	$req = "SELECT facture.id_fact FROM facture
		LEFT JOIN commande ON commande.id_cmd = facture.commande_fact
		LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_fact IN (".$factListString.")
		AND (archived_aff = '0' OR archived_aff IS NULL)
		AND (actif_aff = '1' OR actif_aff IS NULL)
		AND status_fact IN (1,2,3,4)
		GROUP BY id_fact ORDER BY id_fact ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $fact) {
		factureModel::markValideFactureInDB($fact['id_fact'],$PC->rcvP);
		$message.= "Facture ".$fact['id_fact']." marqué comme validée \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' factures validées', $message);
	    $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des factures séléctionnées ne peuvent être marquée comme validée</span>";
    }
    elseif($action == 'attente') {
	$req = "SELECT facture.id_fact FROM facture
		LEFT JOIN commande ON commande.id_cmd = facture.commande_fact
		LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_fact IN (".$factListString.")
		AND (archived_aff = '0' OR archived_aff IS NULL)
		AND (actif_aff = '1' OR actif_aff IS NULL)
		AND status_fact IN (2,3,4,5)
		GROUP BY id_fact ORDER BY id_fact ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $fact) {
		factureModel::markEnvoyeFactureInDB($fact['id_fact'],$PC->rcvP);
		$message.= "Facture ".$fact['id_fact']." marqué comme en attente de règlement \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' factures  en attente de règlement', $message);
	    $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des factures séléctionnées ne peuvent être marquée comme  en attente de règlement</span>";
    }
    elseif($action == 'regle') {
	$req = "SELECT facture.id_fact FROM facture
		LEFT JOIN commande ON commande.id_cmd = facture.commande_fact
		LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_fact IN (".$factListString.")
		AND (archived_aff = '0' OR archived_aff IS NULL)
		AND (actif_aff = '1' OR actif_aff IS NULL)
		AND status_fact IN (2,3,4,5)
		GROUP BY id_fact ORDER BY id_fact ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $fact) {
		factureModel::markRegleFactureInDB($fact['id_fact'],$PC->rcvP);
		$message.= "Facture ".$fact['id_fact']." réglée \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' factures réglées', $message);
	    $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des factures séléctionnées ne peuvent être marquée comme réglée</span>";
    }
    elseif($action == 'changeAttribute') {
	$req = "SELECT facture.id_fact FROM facture
		LEFT JOIN commande ON commande.id_cmd = facture.commande_fact
		LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_fact IN (".$factListString.")
		AND (archived_aff = '0' OR archived_aff IS NULL)
		AND (actif_aff = '1' OR actif_aff IS NULL)
		AND status_fact NOT IN (7)
		GROUP BY id_fact ORDER BY id_fact ASC";
     $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $fact) {
		factureModel::changeAttributeFactureInDB($fact['id_fact'],$PC->rcvP);
		$message.= "changement des attributs de la facture ".$fact['id_fact']." \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' factures modifiées', $message);
	    $message = "<span class=\"importantblue\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucun des factures séléctionnés ne peuvent être modifié</span>";
    }
    elseif($action == 'archivate') {
	$req = "SELECT facture.id_fact FROM facture
		LEFT JOIN commande ON commande.id_cmd = facture.commande_fact
		LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_fact IN (".$factListString.")
		AND (archived_aff = '0' OR archived_aff IS NULL)
		AND (actif_aff = '1' OR actif_aff IS NULL)
		AND (status_dev IS NULL OR  status_fact IN (1,2,5,6,7))
		AND (status_cmd IS NULL OR  status_cmd IN (9,10))
		AND (status_fact IS NULL OR  status_fact IN (6,7))
		GROUP BY id_fact ORDER BY id_fact ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $fact) {
		factureModel::archivateFactureInDB($fact['id_fact'],$PC->rcvP);
		$message.= "archivage de la facture ".$fact['id_fact']." \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' factures archivées', $message);
	    $message = "<span class=\"importantblue\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des factures séléctionnées ne peuvent être archivée</span>";
    }
    $message = $message.'<script type="text/javascript">setTimeout("window.location.href = \'FactureListe.php\';",1000);</script>';
}
else {
    if($PC->rcvG['status_fact'] != '')
        $data['status_fact'] = $PC->rcvG['status_fact'];
    $datas['from'] = 0;
    $datas['limit'] = 30;
    $datas['order'] = 'id_fact';
    $datas['orderSens'] = 'DESC';
    $ordre = 'ORDER BY '.$datas['order'].' '.$datas['orderSens'];
    $req = new factureModel();
    $total = $req->getDataForSearchWeb('', $datas['from'], 'ALL', $ordre, $data);
    $datas['total'] = $total[1][0]['counter'];
    $result = $req->getDataForSearchWeb('', $datas['from'], $datas['limit'],$ordre, $data);
    $datas['data'] = $result[1];
    $datas['status'] = $req->getAllStatusFacture();
    $view = new factureView();
    $mess = ($PC->rcvG['mess']!='') ? $PC->rcvG['mess'] : '';
    $sortie = $view->searchResult($datas, $mess);
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($message.$sortie);
$out->Process();
?>
