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
loadPlugin(array('ZunoCore','ZView/CommandeView'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('pegase');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->headerHTML->initCalendar();
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);


/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
aiJeLeDroit('commande', 05, 'web');
if($PC->rcvP['action'] == 'searchCmd') {
    if($PC->rcvP['entreprise_cmd'] != '')
	$data['entreprise_cmd'] = $PC->rcvP['entreprise_cmd'];
    if($PC->rcvP['titre_cmd'] != '')
	$data['titre_cmd'] = $PC->rcvP['titre_cmd'];
    if($PC->rcvP['id_aff'] != '')
	$data['id_aff'] = $PC->rcvP['id_aff'];
    if($PC->rcvP['status_cmd'] != '')
	$data['status_cmd'] = $PC->rcvP['status_cmd'];
    if($PC->rcvP['commercial_cmd'] != '')
	$data['commercial_cmd'] = $PC->rcvP['commercial_cmd'];
    if($PC->rcvP['cp_ent'] != '')
	$data['cp_ent'] = $PC->rcvP['cp_ent'];
    if($PC->rcvP['sommeFHT_cmd'] != '')
	$data['sommeFHT_cmd'] = $PC->rcvP['sommeFHT_cmd'];
    if($PC->rcvP['sommeFHT_cmd2'] != '')
	$data['sommeFHT_cmd2'] = $PC->rcvP['sommeFHT_cmd2'];
    if($PC->rcvP['order'] != '') {
	$datas['order'] = $PC->rcvP['order'];
	$datas['orderSens'] = $PC->rcvP['orderSens'];
    }
    else {
	$datas['order'] = 'id_cmd';
	$datas['orderSens'] = 'DESC';
    }
    $ordre = 'ORDER BY '.$datas['order'].' '.$datas['orderSens'];
    if($PC->rcvP['limit'] != '')
	 $datas['limit'] = $PC->rcvP['limit'];
    else $datas['limit'] = '30';
    if($PC->rcvP['from'] != '')
	 $datas['from'] = $PC->rcvP['from'];
    else $datas['from'] = '0';
    $req = new commandeModel();
    $result = $req->getDataForSearchWeb('', $datas['from'], 'ALL', $ordre, $data);
    $datas['total'] = $result[1][0]['counter'];
    if($datas['limit'] == 'ALL') {
	$datas['limit'] = $datas['total'];
	$datas['from'] = 0;
    }
    $result = $req->getDataForSearchWeb('', $datas['from'], $datas['limit'], $ordre, $data);
    $datas['data'] = $result[1];
    $view = new commandeView();
    echo $view->searchResult($datas, 'result');
    exit;
}
elseif($PC->rcvP['action'] == 'exportTableur')
{
	$req = new commandeModel();
	$result = $req->getDataForExportTableur($PC->rcvP['select']);
	$gnose = new commandeGnose();
	$file = $gnose->CommandeExportTableurConverter($result[1],$PC->rcvP['exportType']);
	PushFileToBrowser($file);
	exit;
}
elseif ($PC->rcvP['action'] == 'groupedAction') {
    $bddtmp = new CommandeModel();
    $list = $PC->rcvP['select'];
    $action = $PC->rcvP['groupedAction'];

    if (count($list) > 0) {
	$cmdListString = '';
	foreach ($list as $k => $cmd)
	    $cmdListString .= ', \''.$cmd.'\'';
	$cmdListString = substr($cmdListString,1);
	$reqAdd = 'AND id_cmd IN ('.$cmdListString.')';
    }

    if($action == 'reinit') {
	$req = "SELECT commande.id_cmd FROM commande
		LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_cmd IN (".$cmdListString.")
		AND archived_aff = '0'
		AND actif_aff = '1'
		AND status_cmd NOT IN (1,9,10)
		GROUP BY id_cmd ORDER BY id_cmd ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $cmd) {
		commandeModel::markReinitCommandeInDB($cmd['id_cmd'],$PC->rcvP);
		$message.= "re-initialisation de la commande ".$cmd['id_cmd']." \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' commandes re-initialisées', $message);
	    $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des commandes séléctionnées ne peuvent être re-initialisée</span>";
    }
    elseif($action == 'recu') {
	$req = "SELECT commande.id_cmd FROM commande
		LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_cmd IN (".$cmdListString.")
		AND archived_aff = '0'
		AND actif_aff = '1'
		AND status_cmd NOT IN (9,10)
		GROUP BY id_cmd ORDER BY id_cmd ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $cmd) {
		commandeModel::markRecuCommandeInDB($cmd['id_cmd'],$PC->rcvP);
		$message.= "Bon de commande client n°".$cmd['id_cmd']." reçu \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' commandes recues', $message);
	    $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des commandes séléctionnées ne peuvent être marquées comme reçue</span>";
    }
    elseif($action == 'bdcfenvoye') {
	$req = "SELECT commande.id_cmd FROM commande
		LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_cmd IN (".$cmdListString.")
		AND archived_aff = '0'
		AND actif_aff = '1'
		AND status_cmd NOT IN (1,2,9,10)
		GROUP BY id_cmd ORDER BY id_cmd ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $cmd) {
		commandeModel::markBDCFEnvoyeCommandeInDB($cmd['id_cmd'],$PC->rcvP);
		$message.= "Bon de commande fournisseur ".$cmd['id_cmd']." envoyé \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' commandes fournisseurs envoyées', $message);
	    $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des commandes séléctionnées ne peuvent être marquées comme envoyées aux fournisseurs</span>";
    }
    elseif($action == 'bdcfrecu') {
	$req = "SELECT commande.id_cmd FROM commande
		LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_cmd IN (".$cmdListString.")
		AND archived_aff = '0'
		AND actif_aff = '1'
		AND status_cmd NOT IN (1,2,9,10)
		GROUP BY id_cmd ORDER BY id_cmd ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $cmd) {
		commandeModel::markBDCFRecuCommandeInDB($cmd['id_cmd'],$PC->rcvP);
		$message.= "Bon de commande fournisseur ".$cmd['id_cmd']." reçu \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' commandes récéptionnées par les fournisseurs partenaires', $message);
	    $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des commandes séléctionnées ne peuvent être marquées comme reçues par les fournisseurs</span>";
    }
    elseif($action == 'bdcfvalid') {
	$req = "SELECT commande.id_cmd FROM commande
		LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_cmd IN (".$cmdListString.")
		AND archived_aff = '0'
		AND actif_aff = '1'
		AND status_cmd NOT IN (1,2,9,10)
		GROUP BY id_cmd ORDER BY id_cmd ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $cmd) {
		commandeModel::markBDCFValidCommandeInDB($cmd['id_cmd'],$PC->rcvP);
		$message.= "Bon de commande fournisseur ".$cmd['id_cmd']." validé \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' commandes validées par les fournisseurs partenaires', $message);
	    $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des commandes séléctionnées ne peuvent être marquées comme validées par les fournisseurs</span>";
    }
    elseif($action == 'expedie') {
	$req = "SELECT commande.id_cmd FROM commande
		LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_cmd IN (".$cmdListString.")
		AND archived_aff = '0'
		AND actif_aff = '1'
		AND status_cmd NOT IN (1,2,9,10)
		GROUP BY id_cmd ORDER BY id_cmd ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $cmd) {
		commandeModel::markExpedieCommandeInDB($cmd['id_cmd'],$PC->rcvP);
		$message.= "Commande client n°".$cmd['id_cmd']." expédiée \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' commandes expédiées par les fournisseurs partenaires', $message);
	    $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des commandes séléctionnées ne peuvent être marquées comme expédiées</span>";
    }
    elseif($action == 'receptionne') {
	$req = "SELECT commande.id_cmd FROM commande
		LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_cmd IN (".$cmdListString.")
		AND archived_aff = '0'
		AND actif_aff = '1'
		AND status_cmd NOT IN (1,2,9,10)
		GROUP BY id_cmd ORDER BY id_cmd ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $cmd) {
		commandeModel::markReceptionneCommandeInDB($cmd['id_cmd'],$PC->rcvP);
		$message.= "Commande client n°".$cmd['id_cmd']." réceptionnée \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' commandes récéptionnées par les clients', $message);
	    $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des commandes séléctionnées ne peuvent être marquées comme réceptionnées</span>";
    }
    elseif($action == 'termine') {
	$req = "SELECT commande.id_cmd FROM commande
		LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_cmd IN (".$cmdListString.")
		AND archived_aff = '0'
		AND actif_aff = '1'
		AND status_cmd NOT IN (1,2,9,10)
		GROUP BY id_cmd ORDER BY id_cmd ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $cmd) {
		commandeModel::markTermineCommandeInDB($cmd['id_cmd'],$PC->rcvP);
		$message.= "Commande client n°".$cmd['id_cmd']." terminée \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' commandes terminées', $message);
	    $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des commandes séléctionnées ne peuvent être marquées comme terminées</span>";
    }
    elseif($action == 'changeAttribute') {
	$req = "SELECT commande.id_cmd FROM commande
		LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_cmd IN (".$cmdListString.")
		AND archived_aff = '0'
		AND actif_aff = '1'
		AND status_cmd NOT IN (10)
		GROUP BY id_cmd ORDER BY id_cmd ASC";
     $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $cmd) {
		commandeModel::changeAttributeCommandeInDB($cmd['id_cmd'],$PC->rcvP);
		$message.= "changement des attributs de la commande n°".$cmd['id_cmd']." \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' commandes modifiées', $message);
	    $message = "<span class=\"importantblue\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucun des commandes séléctionnées ne peuvent être modifiées</span>";
    }
    elseif($action == 'archivate') {
	$req = "SELECT commande.id_cmd FROM commande
		LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		LEFT JOIN facture ON commande.id_cmd = facture.commande_fact
		WHERE id_cmd IN (".$cmdListString.")
		AND archived_aff = '0'
		AND actif_aff = '1'
		AND (status_dev IS NULL OR  status_dev IN (1,2,5,6,7))
		AND (status_cmd IS NULL OR  status_cmd IN (8,9))
		AND (status_fact IS NULL OR  status_fact IN (6,7))
		GROUP BY id_cmd ORDER BY id_cmd ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $cmd) {
		commandeModel::archivateCommandeInDB($cmd['id_cmd'],$PC->rcvP);
		$message.= "archivage de la commande ".$cmd['id_cmd']." \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' commandes archivées', $message);
	    $message = "<span class=\"importantblue\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des commandes séléctionnées ne peuvent être archivées</span>";
    }
    $message = $message.'<script type="text/javascript">setTimeout("window.location.href = \'CommandeListe.php\';",1000);</script>';
}
else {
    if($PC->rcvG['status_cmd'] != '')
	$data['status_cmd'] = $PC->rcvG['status_cmd'];
    $datas['from'] = 0;
    $datas['limit'] = 30;
    $datas['order'] = 'id_cmd';
    $datas['orderSens'] = 'DESC';
    $req = new commandeModel();
    $total = $req->getDataForSearchWeb('',$datas['from'], 'ALL','ORDER BY id_cmd DESC',$data);
    $datas['total'] = $total[1][0]['counter'];
    $result = $req->getDataForSearchWeb('',$datas['from'],$datas['limit'],'ORDER BY id_cmd DESC',$data);
    $datas['data'] = $result[1];
    $datas['status'] = $req->getAllStatusCommande();
    $view = new commandeView();
    $sortie = $view->searchResult($datas, '');
}

/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($message.$sortie);
$out->Process();
?>